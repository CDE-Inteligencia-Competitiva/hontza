<?php

/**
 * @file
 * Functions related to Apache Solr indexing operations.
 */

/**
 * Processes all index queues associated with the passed environment.
 *
 * An environment usually indexes one or more entity types. Each entity type
 * stores its queue in a database table that is defined in the entity type's
 * info array. This function processes N number of items in each queue table,
 * where N is the limit passed as the second argument.
 *
 * The indexing routine allows developers to selectively bypass indexing on a
 * per-entity basis by implementing the following hooks:
 * - hook_apachesolr_exclude()
 * - hook_apachesolr_ENTITY_TYPE_exclude()
 *
 * @param string $env_id
 *   The machine name of the environment.
 * @param int $limit
 *   The number of items to process per queue table. For example, if there are
 *   two entities that are being indexed in this environment and they each have
 *   their own queue table, setting a limit of 50 will send a maximum number of
 *   100 documents to the Apache Solr server.
 *
 * @return int
 *   The total numer of documents sent to the Apache Solr server for indexing.
 *
 * @see apachesolr_index_get_entities_to_index()
 * @see apachesolr_index_entity_to_documents()
 * @see apachesolr_index_send_to_solr()
 */
function apachesolr_index_entities($env_id, $limit) {
  $documents_submitted = 0;
  $entity_type = 'node';
  // With each pass through the callback, retrieve the next group of nids.
  $rows = apachesolr_index_get_entities_to_index($env_id, $entity_type, $limit);
  $documents = array();
  foreach ($rows as $row) {
    $row_documents = apachesolr_index_entities_document($row, $entity_type, $env_id);
    //gemini-2014
    if(!is_array($row_documents)){
        $row_documents=array();
    }
    //
    $documents = array_merge($documents, $row_documents);
  }
  $indexed = apachesolr_index_send_to_solr($env_id, $documents);
  if ($indexed !== FALSE) {
    $documents_submitted += count($documents);
    $index_position = apachesolr_get_last_index_position($env_id, $entity_type);
      $max_changed = $index_position['last_changed'];
      $max_entity_id = $index_position['last_entity_id'];
      foreach ($rows as $row) {
        if (!empty($row->status)) {
          if ($row->changed > $max_changed) {
            $max_changed = $row->changed;
          }
          if ($row->entity_id > $max_entity_id) {
            $max_entity_id = $row->entity_id;
          }
        }
      }
      apachesolr_set_last_index_position($env_id, $entity_type, $max_changed, $max_entity_id);
      apachesolr_set_last_index_updated($env_id, APACHESOLR_REQUEST_TIME);
  }
  //intelsat-2015
  $updated=1;
  red_solr_inc_set_entity_id_array_solr_updated($rows,$updated);
  return $documents_submitted;
}

function apachesolr_index_entities_document($row, $entity_type, $env_id) {
  $documents = array();
  if (!empty($row->status)) {
    // Let any module exclude this entity from the index.
    $build_document = TRUE;
    foreach (module_implements('apachesolr_exclude') as $module) {
      $exclude = module_invoke($module, 'apachesolr_exclude', $row->entity_id, $entity_type, $row, $env_id);
      // If the hook returns TRUE we should exclude the entity
      if (!empty($exclude)) {
        $build_document = FALSE;
      }
    }
    foreach (module_implements('apachesolr_' . $entity_type . '_exclude') as $module) {
      $exclude = module_invoke($module, 'apachesolr_' . $entity_type . '_exclude', $row->entity_id, $row, $env_id);
      // If the hook returns TRUE we should exclude the entity
      if (!empty($exclude)) {
        $build_document = FALSE;
      }
    }
    if ($build_document) {
      //gemini-2014
      $my_array=apachesolr_index_entity_to_documents($row, $env_id);
      if(!is_array($my_array)){
          $my_array=array();
      }
      //  
      $documents = array_merge($documents,$my_array);
    }
  }
  else {
    // Delete the entity from our index if the status callback returned 0
    apachesolr_remove_entity($env_id, $row->entity_type, $row->entity_id);
  }
  return $documents;
}

/**
 * Returns the total number of documents that are able to be indexed and the
 * number of documents left to be indexed.
 *
 * This is a helper function for modules that implement hook_search_status().
 *
 * @param string $env_id
 *   The machine name of the environment.
 *
 * @return array
 *   An associative array with the key-value pairs:
 *   - remaining: The number of items left to index.
 *   - total: The total number of items to index.
 *
 * @see hook_search_status()
 */
function apachesolr_index_status($env_id) {
  $remaining = 0;
  $total = 0;
  $entity_type = 'node';
  $bundles = apachesolr_get_index_bundles($env_id, $entity_type);
  $table = apachesolr_get_indexer_table($entity_type);

  if (!empty($bundles)) {
    $query = "SELECT count(*)
      FROM {{$table}} asn
      WHERE (asn.status = 1) AND (asn.bundle IN (" . db_placeholders($bundles, 'varchar') . "))";
    $total += db_result(db_query($query, $bundles));

    // Get $last_entity_id and $last_change.
    extract(apachesolr_get_last_index_position($env_id, $entity_type));

    // Find the next batch of entities to index for this entity type.  Note that
    // for ordering we're grabbing the oldest first and then ordering by ID so
    // that we get a definitive order.
    $query = "SELECT count(*)
     FROM {{$table}} aie
     WHERE (aie.bundle IN (" . db_placeholders($bundles, 'varchar') . "))
     AND (aie.status = 1)
     AND ((aie.changed > %d) OR ((aie.changed <= %d) AND (aie.entity_id > %d)))";
    $arguments = array_merge($bundles, array($last_changed, $last_changed, $last_entity_id));
    if ($table == 'apachesolr_index_entities') {
      // Other, entity-specific tables don't need this condition.
      $query .= " AND aie.entity_type = '%s'";
      $arguments[] = $entity_type;
    }
    $query .= " ORDER BY aie.entity_id ASC";
    $remaining += db_result(db_query($query, $arguments));

  }

  return array('remaining' => $remaining, 'total' => $total);
}

/**
 * Worker callback for apachesolr_index_entities().
 *
 * Loads and proccesses the entity queued for indexing and converts into one or
 * more documents that are sent to the Apache Solr server for indexing.
 *
 * The entity is loaded as the user specified in the "apachesolr_index_user"
 * system variable in order to prevent sentive data from being indexed and
 * displayed to underprivileged users in search results. The index user defaults
 * to a user ID of "0", which is the anonymous user.
 *
 * After the entity is loaded, it is converted to an array via the callback
 * specified in the entity type's info array. The array that the entity is
 * converted to is the model of the document sent to the Apache Solr server for
 * indexing. This function allows develoeprs to modify the document by
 * implementing the following hooks:
 * - apachesolr_index_document_build()
 * - apachesolr_index_document_build_ENTITY_TYPE()
 * - apachesolr_index_documents_alter()
 *
 * @param stdClass $item
 *   The data returned by the queue table containing:
 *   - entity_id: An integer containing the unique identifier of the entity, for
 *     example a node ID or comment ID.
 *   - entity_type: The unique identifier for the entity, i.e. "node", "file".
 *   - bundle: The machine-readable name of the bundle the passed entity is
 *     associated with.
 *   - status: The "published" status of the entity. The status will also be set
 *     to "0" when entity is deleted but the Apache Solr server is unavailable.
 *   - changed: A timestamp flagging when the entity was last modified.
 * @param string $env_id
 *   The machine name of the environment.
 *
 * @return array
 *   An associative array of documents that are sent to the Apache Solr server
 *   for indexing.
 *
 * @see apachesolr_index_nodes() for the old-skool version.
 */
function apachesolr_index_entity_to_documents($item, $env_id) {

  // Always build the content for the index as an anonynmous user to avoid
  // exposing restricted fields and such.
  // @todo Uncomment these lines when we're done debugging, since they break dpm().

  global $user;
  session_save_session(FALSE);
  $saved_user = $user;
  // Should indexing take place using anon ( default )
  // or as another user
  $uid = variable_get('apachesolr_index_user', 0);
  if($uid == 0) {
    $user = drupal_anonymous_user();
  }
  else {
    $user = user_load($uid);
  }

  // Pull out all of our pertinent data.
  $entity_type = $item->entity_type;
  $id = $item->entity_id;
  $bundle = $item->bundle;

  // TRUE on reset to bypass static caching and not blow out our memory limit.
  $entity = node_load($item->entity_id, NULL, TRUE);

  if (empty($entity)) {
    // If the object failed to load, just stop.
    return FALSE;
  }

  // Create a new document, and do the bare minimum on it.
  $document = _apachesolr_index_process_entity_get_document($entity, $entity_type);

  //Get the callback array to add stuff to the document
  $callbacks = apachesolr_entity_get_callback($entity_type, 'document callback', $bundle);
  $documents = array();
  foreach ($callbacks as $callback) {
    // Call a type-specific callback to add stuff to the document.
    $documents = array_merge($documents, $callback($document, $entity, $entity_type, $env_id));
  }

  //do this for all possible documents that were returned by the callbacks
  foreach ($documents as $document) {
    // Call an all-entity hook to add stuff to the document.
    module_invoke_all('apachesolr_index_document_build', $document, $entity, $entity_type, $env_id);

    // Call a type-specific hook to add stuff to the document.
    module_invoke_all('apachesolr_index_document_build_' . $entity_type, $document, $entity, $env_id);

    // Final processing to ensure that the document is properly structured.
    // All records must have a label field, which is used for user-friendly labeling.
    if (empty($document->label)) {
      $document->label = '';
    }

    // All records must have a "content" field, which is used for fulltext indexing.
    // If we don't have one, enter an empty value.  This does mean that the entity
    // will not be fulltext searchable.
    if (empty($document->content)) {
      $document->content = '';
    }

    // All records must have a "teaser" field, which is used for abbreviated
    // displays when no highlighted text is available.
    if (empty($document->teaser)) {
      $document->teaser = truncate_utf8($document->content, 300, TRUE);
    }

    // Add additional indexing based on the body of each record.
    apachesolr_index_add_tags_to_document($document, $document->content);
  }

  // Now allow modules to alter each other's additions for maximum flexibility.

  // Hook to allow modifications of the retrieved results
  foreach (module_implements('apachesolr_index_documents_alter') as $module) {
    $function = $module . '_apachesolr_index_documents_alter';
    $function($documents, $entity, $entity_type, $env_id);
  }

  // Restore the user.
  $user = $saved_user;
  session_save_session(TRUE);
  return $documents;

}

/**
 * Index an array of documents to solr.
 *
 * @return number indexed, or FALSE on failure.
 */
function apachesolr_index_send_to_solr($env_id, $documents) {
  try {
    // Get the $solr object
    $solr = apachesolr_get_solr($env_id);
    // If there is no server available, don't continue.
    if (!$solr->ping(variable_get('apachesolr_ping_timeout', 4))) {
      throw new Exception(t('No Solr instance available during indexing.'));
    }
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
    return FALSE;
  }
  // Do not index when we do not have any documents to send
  // Send TRUE because this is not an error
  if (empty($documents)) {
    return TRUE;
  }
  // Send the document off to Solr.
  watchdog('Apache Solr', 'Adding @count documents.', array('@count' => count($documents)));
  try {
    $docs_chunk = array_chunk($documents, 20);
    foreach ($docs_chunk as $docs) {
      $solr->addDocuments($docs);
    }
    watchdog('Apache Solr', 'Indexing succeeded on @count documents', array(
      '@count' => count($documents),
    ), WATCHDOG_INFO);
    return count($documents);
  }
  catch (Exception $e) {
    if (!empty($docs)) {
      foreach ($docs as $doc) {
        $eids[] = $doc->entity_type . '/' . $doc->entity_id;
      }
    }
    watchdog('Apache Solr', 'Indexing failed on one of the following entity ids: @eids <br /> !message', array(
      '@eids' => implode(', ', $eids),
      '!message' => nl2br(strip_tags($e->getMessage())),
    ), WATCHDOG_ERROR);
    return FALSE;
  }
}

/**
 * Extract HTML tag contents from $text and add to boost fields.
 *
 * $text must be stripped of control characters before hand.
 */
function apachesolr_index_add_tags_to_document($document, $text) {
  $tags_to_index = variable_get('apachesolr_tags_to_index', array(
    'h1' => 'tags_h1',
    'h2' => 'tags_h2_h3',
    'h3' => 'tags_h2_h3',
    'h4' => 'tags_h4_h5_h6',
    'h5' => 'tags_h4_h5_h6',
    'h6' => 'tags_h4_h5_h6',
    'u' => 'tags_inline',
    'b' => 'tags_inline',
    'i' => 'tags_inline',
    'strong' => 'tags_inline',
    'em' => 'tags_inline',
    'a' => 'tags_a'
  ));

  // Strip off all ignored tags.
  $text = strip_tags($text, '<' . implode('><', array_keys($tags_to_index)) . '>');

  preg_match_all('@<(' . implode('|', array_keys($tags_to_index)) . ')[^>]*>(.*)</\1>@Ui', $text, $matches);
  foreach ($matches[1] as $key => $tag) {
    $tag = drupal_strtolower($tag);
    // We don't want to index links auto-generated by the url filter.
    if ($tag != 'a' || !preg_match('@(?:http://|https://|ftp://|mailto:|smb://|afp://|file://|gopher://|news://|ssl://|sslv2://|sslv3://|tls://|tcp://|udp://|www\.)[a-zA-Z0-9]+@', $matches[2][$key])) {
      if (!isset($document->{$tags_to_index[$tag]})) {
        $document->{$tags_to_index[$tag]} = '';
      }
      $document->{$tags_to_index[$tag]} .= ' ' . apachesolr_clean_text($matches[2][$key]);
    }
  }
}

/**
 * Returns a generic Solr document object for this entity.
 *
 * This function will do the basic processing for the document that is common
 * to all entities, but virtually all entities will need their own additional
 * processing.
 *
 * @param stdClass $entity
 *   The entity for which we want a document.
 * @param string $entity_type
 *   The type of entity we're processing.
 * @return ApacheSolrDocument
 */
function _apachesolr_index_process_entity_get_document($entity, $entity_type) {
  module_load_include('php', 'apachesolr', 'Apache_Solr_Document');

  $entity_id = $entity->nid;
  $bundle = $entity->type;

  $document = new ApacheSolrDocument();

  // Define our url options in advance. This differs depending on the
  // language
  $languages = language_list();
  $url_options = array('absolute' => TRUE);
  if (isset($entity->language) && isset($languages[$entity->language])) {
    $url_options['language'] = $languages[$entity->language];
  }

  $document->id = apachesolr_document_id($entity_id, $entity_type);
  $document->site = url(NULL, $url_options);
  $document->hash = apachesolr_site_hash();

  $document->entity_id = $entity_id;
  $document->entity_type = $entity_type;
  $document->bundle = $bundle;
  $document->bundle_name = entity_bundle_label($entity_type, $bundle);

  if (empty($entity->language)) {
    // 'und' is the language-neutral code in Drupal 7.
    $document->ss_language = 'und';
    $path_language = NULL;
  }
  else {
    $document->ss_language = $entity->language;
    $path_language = $entity->language;
  }

  // Hardcoded drupal 6 node path
  $path = 'node/' . $entity->nid;
  // A path is not a requirement of an entity
  if (!empty($path)) {
    $document->path = $path;
    $document->url = url($path, $url_options);
    // Path aliases can have important information about the content.
    // Add them to the index as well.
    if (function_exists('drupal_get_path_alias')) {
      // Add any path alias to the index, looking first for language specific
      // aliases but using language neutral aliases otherwise.
      $output = drupal_get_path_alias($document->path, $path_language);
      if ($output && $output != $document->path) {
        $document->path_alias = $output;
      }
    }
  }
  return $document;
}

/**
 * Returns an array of rows from a query based on an indexing environment.
 * @todo Remove the read only because it is not environment specific
 */
function apachesolr_index_get_entities_to_index($env_id, $entity_type, $limit) {
  $rows = array();
  if (variable_get('apachesolr_read_only', 0)) {
    return $rows;
  }
  $bundles = apachesolr_get_index_bundles($env_id, $entity_type);
  if (empty($bundles)) {
    return $rows;
  }
  // Drupal 6 specifically only supports nodes
  $type = 'node';
  if (($type != $entity_type)) {
    return $rows;
  }

  $bundles = apachesolr_get_index_bundles($env_id, $type);
  $table = apachesolr_get_indexer_table($entity_type);

  // Get $last_entity_id and $last_change.
  extract(apachesolr_get_last_index_position($env_id, $entity_type));

  // Find the next batch of entities to index for this entity type.  Note that
  // for ordering we're grabbing the oldest first and then ordering by ID so
  // that we get a definitive order.
  //intelsat-2015
  $or='';
  if(red_solr_inc_is_sended()){
      $or='((aie.changed <='.$last_changed.') AND aie.my_sended=0) OR ';
  }
  $query = "SELECT * FROM {{$table}} aie
   WHERE (aie.bundle IN (" . db_placeholders($bundles, 'varchar') . "))
   AND (aie.entity_type = '%s')
   AND (".$or."(aie.changed > %d) OR ((aie.changed <= %d) AND (aie.entity_id > %d)))";
  $arguments = array_merge($bundles, array($type, $last_changed, $last_changed, $last_entity_id));
  if ($table == 'apachesolr_index_entities') {
    // Other, entity-specific tables don't need this condition.
    $query .= " AND aie.entity_type = '%s'";
    $arguments[] = $type;
  }
  $query .= " ORDER BY aie.entity_id ASC";
  $result = db_query_range($query, $arguments, 0, $limit);

  $status_callbacks = apachesolr_entity_get_callback($entity_type, 'status callback');
  while ($record = db_fetch_object($result)) {
    //intelsat-2015
    if(red_solr_inc_is_sended()){
        red_solr_inc_update_sended($record->entity_id,1);
    }  
    // Check status and status callbacks before sending to the index
    if (is_array($status_callbacks)) {
      foreach($status_callbacks as $status_callback) {
        if (is_callable($status_callback)) {
          // by placing $status in front we prevent calling any other callback
          // after one status callback returned false
          $record->status = $record->status && $status_callback($record->entity_id, $record->entity_type);
        }
      }
    }
    $rows[] = $record;
  }
  return $rows;
}

/**
 * Delete the whole index for an environment.
 *
 * @param string $env_id
 *   The solr environment indentifier.
 * @param string $entity_type
 *   (optional) specify to remove just this entity_type from the index.
 * @param string $bundle
 *   (optional) also specify a bundle to remove just the bundle from
 *   the index.
 */
function apachesolr_index_delete_index($env_id, $entity_type = NULL, $bundle = NULL) {
  // Instantiate a new Solr object.
  try {
    $solr = apachesolr_get_solr($env_id);
    $query = '*:*';

    if (!empty($entity_type) && !empty($bundle)) {
      $query = "(bundle:$bundle AND entity_type:$entity_type) OR sm_parent_entity_bundle:{$entity_type}-{$bundle}";
    }
    elseif (!empty($bundle)) {
      $query = "(bundle:$bundle)";
    }

    // Allow other modules to modify the delete query.
    // For example, use the site hash so that you only delete this site's
    // content:  $query = 'hash:' . apachesolr_site_hash()
    drupal_alter('apachesolr_delete_by_query', $query);
    $solr->deleteByQuery($query);
    $solr->commit();

    // Log the query used for deletion.
    watchdog('Apache Solr', 'Deleted documents from index with query @query', array('@query' => $query), WATCHDOG_INFO);

    if (!empty($entity_type)) {
      $rebuild_callback = apachesolr_entity_get_callback($entity_type, 'reindex callback');
      if (is_callable($rebuild_callback)) {
        $rebuild_callback($env_id, $bundle);
      }
    }
    else {
      apachesolr_index_mark_for_reindex($env_id);
    }

    apachesolr_set_last_index_updated($env_id, APACHESOLR_REQUEST_TIME);
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
  }
}

/**
 * Delete from the index documents with the entity type and any of the excluded bundles.
 *
 * Also deletes all documents that have the entity type and bundle as a parent.
 *
 * @param string $env_id
 * @param string $entity_type
 * @param array $excluded_bundles
 *
 * @return TRUE on success, FALSE on failure.
 */
function apachesolr_index_delete_bundles($env_id, $entity_type, array $excluded_bundles) {
  // Remove newly omitted bundles.
  try {
    $solr = apachesolr_get_solr($env_id);
    foreach ($excluded_bundles as $bundle) {
      $query = "(bundle:$bundle AND entity_type:$entity_type) OR sm_parent_entity_bundle:{$entity_type}-{$bundle}";

      // Allow other modules to modify the delete query.
      // For example, use the site hash so that you only delete this site's
      // content:  $query = 'hash:' . apachesolr_site_hash()
      drupal_alter('apachesolr_delete_by_query', $query);
      $solr->deleteByQuery($query);

      // Log the query used for deletion.
      watchdog('Apache Solr', 'Deleted documents from index with query @query', array('@query' => $query), WATCHDOG_INFO);      
    }
    if ($excluded_bundles) {
      $solr->commit();
    }
    return TRUE;
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
    return FALSE;
  }
}

/**
 * Delete an entity from the index.
 *
 * Also deletes all documents that have the deleted document as a parent.
 *
 * @param string $env_id
 * @param string $entity_type
 * @param string $entity_id
 *
 * @return TRUE on success, FALSE on failure.
 */
function apachesolr_index_delete_entity_from_index($env_id, $entity_type, $entity_id) {
  static $failed = FALSE;
  if ($failed) {
    return FALSE;
  }
  if (apachesolr_environment_variable_get($env_id, 'apachesolr_read_only', APACHESOLR_READ_WRITE) == APACHESOLR_READ_ONLY) {
    return FALSE;
  }
  try {
    $solr = apachesolr_get_solr($env_id);
    $document_id = apachesolr_document_id($entity_id, $entity_type);
    $query = "id:$document_id OR sm_parent_document_id:$document_id";
    //intelsat-2015
    $solr->deleteByQuery($query);
    //$solr->deleteById($document_id);
    //
    apachesolr_set_last_index_updated($env_id, APACHESOLR_REQUEST_TIME);
    // Log the query used for deletion.
    watchdog('Apache Solr', 'Deleted documents from index with query @query', array('@query' => $query), WATCHDOG_INFO); 
    return TRUE;
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
    // Don't keep trying queries if they are failing.
    $failed = TRUE;
    return FALSE;
  }
}

/**
 *
 * @param $entity_type
 *
 * @throws Exception
 */
function apachesolr_index_mark_for_reindex($env_id, $entity_type = 'node') {
  foreach (content_types() as $bundle => $entity_info) {
    if (!empty($entity_info['extra']['apachesolr']['index'])) {
      $reindex_callback = apachesolr_entity_get_callback($entity_type, 'reindex callback');
    }
    if (!empty($reindex_callback)) {
      call_user_func($reindex_callback, $env_id, $bundle);
    }
  }
  apachesolr_clear_last_index_position($env_id, $entity_type);
  cache_clear_all('*', 'cache_apachesolr', TRUE);
}


/**
 * Sets what bundles on the specified entity type should be indexed.
 *
 * @param string $env_id
 *   The Solr core for which to index entities.
 * @param string $entity_type
 *   The entity type to index.
 * @param array $bundles
 *   The machine names of the bundles to index.
 *
 * @throws Exception
 */
function apachesolr_index_set_bundles($env_id, $entity_type, array $bundles) {
  // @todo - need a lock?
  $query = "DELETE FROM {apachesolr_index_bundles} WHERE env_id = '%s' AND entity_type = '%s'";
  db_query($query, array($env_id, $entity_type));
  if ($bundles) {
    foreach ($bundles as $bundle) {
      $query = "INSERT INTO {apachesolr_index_bundles} (env_id, entity_type, bundle) VALUES ('%s', '%s', '%s')";
      db_query($query, array($env_id, $entity_type, $bundle));
    }
  }
}


// This really should be in core, but it isn't yet.  When it gets added to core,
// we can remove this version.
// @see http://drupal.org/node/969180
if (!function_exists('entity_bundle_label')) {

/**
 * Returns the label of a bundle.
 *
 * @param $entity_type
 *   The entity type; e.g. 'node' or 'user'.
 * @param $entity
 *   The entity for which we want the human-readable label of its bundle.
 *
 * @return
 *   A string with the human-readable name of the bundle, or FALSE if not specified.
 */
function entity_bundle_label($entity_type, $bundle_name) {
  static $labels = array();
  if (empty($labels)) {
    foreach (content_types() as $bundle => $bundle_info) {
        $labels['node'][$bundle] = !empty($bundle_info['name']) ? $bundle_info['name'] : FALSE;
    }
  }
  // Backport only supports node
  return $labels['node'][$bundle_name];
}

}


/************************
 * The NODE entity indexing part
 ************************/

/**
 * Builds the node-specific information for a Solr document.
 *
 * @param ApacheSolrDocument $document
 *   The Solr document we are building up.
 * @param stdClass $entity
 *   The entity we are indexing.
 * @param string $entity_type
 *   The type of entity we're dealing with.
 */
function apachesolr_index_node_solr_document(ApacheSolrDocument $document, $node, $entity_type, $env_id) {
  // None of these get added unless they are explicitly in our schema.xml
  $document->label = apachesolr_clean_text($node->title);

  $node->build_mode = NODE_BUILD_SEARCH_INDEX;
  $node = node_build_content($node, FALSE, FALSE);
  $node->body = drupal_render($node->content);

  $document->content = apachesolr_clean_text($node->body);

  // Adding the teaser
  if (isset($node->teaser)) {
    $document->teaser = apachesolr_clean_text($node->teaser);
  }
  else {
    $document->teaser = truncate_utf8($document->content, 300, TRUE);
  }

  // Author information
  if ($node->uid == 0 || strlen($node->name) == 0) {
    // @see user_validate_name(). !'0' === TRUE.
    $document->ss_name = '0';
  }
  else {
    $document->ss_name = $node->name;
    // We want the name to be searchable for keywords.
    $document->tos_name = $node->name;
  }
  // Index formatted username so it can be searched and sorted on.
  $account = (object) array('uid' => $node->uid, 'name' => $node->name);

  $username = check_plain($account->name);

  $document->ss_name_formatted = $username;
  $document->tos_name_formatted = $username;
  $document->is_uid = $node->uid;
  $document->bs_status = $node->status;
  $document->bs_sticky = $node->sticky;
  $document->bs_promote = $node->promote;
  $document->is_tnid = $node->tnid;
  $document->bs_translate = $node->translate;

  // Timestamp of the node
  $document->ds_created = apachesolr_date_iso($node->created);
  $document->ds_changed = apachesolr_date_iso($node->changed);

  // Comment counts + time
  if (isset($node->last_comment_timestamp) && !empty($node->comment_count)) {
    $document->ds_last_comment_timestamp = apachesolr_date_iso($node->last_comment_timestamp);
    $document->ds_last_comment_or_change = apachesolr_date_iso(max($node->last_comment_timestamp, $node->changed));
    $document->is_comment_count = $node->comment_count;
  }
  else {
    $document->ds_last_comment_or_change = apachesolr_date_iso($node->changed);
  }

  // Fetch extra data normally not visible, including comments.
  // We do this manually (with module_implements instead of node_invoke_nodeapi)
  // because we want a keyed array to come back. Only in this way can we decide
  // whether to index comments or not.
  $extra = array();
  $excludes = variable_get('apachesolr_exclude_nodeapi_types', array());
  $exclude_nodeapi = isset($excludes[$node->type]) ? $excludes[$node->type] : array();
  foreach (module_implements('node_update_index') as $module) {
    // Invoke nodeapi if this module has not been excluded, for example,
    // exclude 'comment' for a type to skip indexing its comments.
    if (empty($exclude_nodeapi[$module])) {
      $function = $module . '_node_update_index';
      if ($output = $function($node)) {
        $extra[$module] = $output;
      }
    }
  }
  // Adding the text of the comments
  if (isset($extra['comment'])) {
    $comments = $extra['comment'];
    // Remove comments from the extra fields
    unset($extra['comment']);
    $document->ts_comments = apachesolr_clean_text($comments);
    // @todo: do we want to reproduce apachesolr_add_tags_to_document() for comments?
  }

  // If there are other extra fields, add them to the document
  if (!empty($extra)) {
    // Use an omit-norms text field since this is generally going to be short; not
    // really a full-text field.
    $document->tos_content_extra = apachesolr_clean_text(implode(' ', $extra));
  }
  //  Generic usecase for future reference. Callbacks can
  //  allow you to send back multiple documents
  $documents = array();
  $documents[] = $document;
  return $documents;
}

function apachesolr_index_node_bundles_changed($env_id, $existing_bundles, $new_bundles) {
  // Nothing to do for now.
}

/**
 * Reindexing callback for ApacheSolr, for nodes.
 *
 * @param string $env_id
 *   The solr environment
 * @param string|null $bundle
 *   (optional) The bundle type to reindex. If not used
 *   all bundles will be reindexed.
 *
 * @throws Exception
 */
function apachesolr_index_node_solr_reindex($env_id, $bundle = NULL) {
  $indexer_table = apachesolr_get_indexer_table('node');
  try {
    $indexable_bundles = apachesolr_get_index_bundles($env_id, 'node');
    if ($bundle && !empty($indexable_bundles) && !in_array($bundle, $indexable_bundles)) {
      // The bundle specified is not in the indexable bundles list.
      return;
    }

    // in the 6.x-3.x version we are not very respective to what bundles we
    // keep or remove in contrary to the 7.x-1.x version. db layer makes our
    // life complicated
    if ($bundle) {
      // Leave status 0 rows - those need to be
      // removed from the index later.
      db_query("DELETE FROM {{$indexer_table}} WHERE entity_type = 'node' AND bundle = '%s' AND status = 1", array($bundle));
      // Mark all nodes of the specified content type for reindexing.
      $query = "INSERT INTO {{$indexer_table}} (entity_id, bundle, status, entity_type, changed) (
        SELECT n.nid AS entity_id, n.type AS bundle, n.status AS status, 'node' AS entity_type, %d AS changed
        FROM {node} n WHERE n.type = '%s' AND status = 1)";
      db_query($query, array(APACHESOLR_REQUEST_TIME, $bundle));
    }
    else {
      // Leave status 0 rows - those need to be
      // removed from the index later.
      db_query("DELETE FROM {{$indexer_table}} WHERE entity_type = 'node' AND status = 1");
      $query = "INSERT INTO {{$indexer_table}} (entity_id, bundle, status, entity_type, changed) (
        SELECT n.nid AS entity_id, n.type AS bundle, n.status AS status, 'node' AS entity_type, %d AS changed
        FROM {node} n WHERE status = 1)";
      db_query($query, array(APACHESOLR_REQUEST_TIME));
    }




  }
  catch (Exception $e) {
    throw $e;
  }
}

/**
 * Status callback for ApacheSolr, for nodes.
 */
function apachesolr_index_node_status_callback($entity_id, $entity_type) {
  // Make sure we have a boolean value.
  // Anything different from 1 becomes zero
  $node_status = db_result(db_query("SELECT n.status FROM {node} n WHERE nid = %d", array($entity_id)));
  $status = ($node_status == 1 ? 1 : 0);
  return $status;
}

/**
 * Callback that converts term field into an array
 */
function apachesolr_term_indexing_callback($node, $field_name, $index_key, $field_info) {
  // Keep ancestors cached
  static $ancestors = array();
    
  $fields = array();
  $vocab_names = array();
  $field_terms = array();

  $vid = $field_info['field']['vid'];
  foreach($node->taxonomy as $tid => $term) {
    if($term->vid == $vid) {
      $field_terms[$tid] = $term;
    }
  }

  if (!empty($field_terms) && function_exists('taxonomy_get_parents_all')) {
    foreach ($field_terms as $term) {
      // Triple indexing of tids lets us do effecient searches (on tid)
      // and do accurate per field or per-vocabulary faceting.

      // By including the ancestors to a term in the index we make
      // sure that searches for general categories match specific
      // categories, e.g. Fruit -> apple, a search for fruit will find
      // content categorized with apple.
      if (!isset($ancestors[$term->tid])) {
        $ancestors[$term->tid] = taxonomy_get_parents_all($term->tid);
      }
      foreach ($ancestors[$term->tid] as $ancestor) {
        // Index parent term against the field. Note that this happens
        // regardless of whether the facet is set to show as a hierarchy or not.
        // We would need a separate field if we were to index terms without any
        // hierarchy at all.
        $fields[] = array(
          'key' => $index_key,
          'value' => $ancestor->tid,
        );
        $fields[] = array(
          'key' => 'tid',
          'value' => $ancestor->tid,
        );
        $fields[] = array(
          'key' => 'im_vid_' . $ancestor->vid,
          'value' => $ancestor->tid,
        );
        $name = apachesolr_clean_text($ancestor->name);
        $vocab_names[$ancestor->vid][] = $name;
        // We index each name as a string for cross-site faceting
        // using the vocab name rather than vid in field construction .
        $fields[] = array(
          'key' => 'sm_vid_' . apachesolr_vocab_name($ancestor->vid),
          'value' => $name,
        );
      }
    }
    // Index the term names into a text field for MLT queries and keyword searching.
    foreach ($vocab_names as $vid => $names) {
      $fields[] = array(
        'key' => 'tm_vid_' . $vid . '_names',
        'value' => implode(' ', $names),
      );
    }
  }
  return $fields;
}

/**
 * Helper function - return a safe (PHP identifier) vocabulary name.
 */
function apachesolr_vocab_name($vid) {
  static $names = array();

  if (!isset($names[$vid])) {
    $vocab_name = db_result(db_query("SELECT v.name FROM {vocabulary} v WHERE v.vid = '%s'", array($vid)));
    $names[$vid] = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/', '_', $vocab_name);
    // Fallback for names ending up all as '_'.
    $check = rtrim($names[$vid], '_');
    if (!$check) {
      $names[$vid] = '_' . $vid . '_';
    }
  }
  return $names[$vid];
}

/**
 * Callback that converts list module field into an array
 * For every multivalued value we also add a single value to be able to
 * use the stats
 */
function apachesolr_fields_default_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  $numeric = TRUE;
  if (!empty($entity->{$field_name})) {
    $field = $entity->$field_name;
    list($lang, $values) = each($field);
    switch ($field_info['index_type']) {
      case 'integer':
      case 'half-int':
      case 'sint':
      case 'tint':
      case 'thalf-int':
      case 'boolean':
        $function = 'intval';
        break;
      case 'float':
      case 'double':
      case 'sfloat':
      case 'sdouble':
      case 'tfloat':
      case 'tdouble':
        $function = 'apachesolr_floatval';
        break;
      default:
        $numeric = FALSE;
        $function = 'apachesolr_clean_text';
    }
    for ($i = 0; $i < count($values); $i++) {
      $fields[] = array(
        'key' => $index_key,
        'value' => $function($values[$i]['value']),
      );
    }
    // Also store the first value of the field in a singular index for multi value fields
    if ($field_info['multiple'] && $numeric && !empty($values[0])) {
      $singular_field_info = $field_info;
      $singular_field_info['multiple'] = FALSE;
      $single_key = apachesolr_index_key($singular_field_info);
      $fields[] = array(
        'key' => $single_key,
        'value' => $function($values[0]['value']),
      );
    }
  }  
  return $fields;
}

function apachesolr_index_content_text_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  if (isset($entity->{$field_name})) {
    $index_key = apachesolr_index_key($field_info);
    /*
    //intelsat-2014
    if(hontza_solr_is_filename_activado()){
        if($field_name=='field_item_filename'){
            if(hontza_solr_is_empty_text($entity->$field_name)){
                $entity->$field_name=hontza_solr_define_entity_field_name_item_filename($entity->$field_name,$entity);
            }
        }    
    }
    // 
     */
    foreach ($entity->$field_name as $field) {
      if ($index_value = (isset($field['safe']) && strlen($field['safe'])) ? $field['safe'] : FALSE) {
        $fields[] = array(
          'key' => $index_key,
          'value' => $index_value,
        );
      }
    }
  }
  return $fields;
}

function apachesolr_index_content_numeric_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  if (isset($entity->{$field_name})) {
    //intelsat-2014
    if($field_name=='field_item_source_tid'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
           $entity->$field_name=hontza_solr_define_entity_field_name_item_numeric_reference($entity->$field_name,$entity);
        }
        //intelsat-2016
        //if(red_despacho_is_activado()){
        $entity->$field_name=red_solr_inc_get_existe_term_tid_entity_field_name_array($entity->$field_name);
        //}
    }else if($field_name=='field_item_canal_category_tid'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
           $entity->$field_name=hontza_solr_define_entity_field_name_item_canal_category_tid($entity->$field_name,$entity);
        }
        //intelsat-2016
        //if(red_despacho_is_activado()){
        //$entity->$field_name=red_solr_inc_get_existe_term_tid_entity_field_name_array($entity->$field_name);
        //}
    }else if($field_name=='field_item_validate_status'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){             
            $entity->$field_name=hontza_solr_define_entity_field_name_item_validate_status($entity->$field_name,$entity);
        }
    }else if($field_name=='field_item_fid'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
           $entity->$field_name=hontza_solr_define_entity_field_name_item_fid($entity->$field_name,$entity);
        }
    //intelsat-2015    
    }else if($field_name=='field_item_seleccionado_boletin'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
            $entity->$field_name=red_solr_inc_define_entity_field_name_item_seleccionado_boletin($entity->$field_name,$entity);
        }
    }else if($field_name=='field_item_bookmark'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
            $entity->$field_name=red_solr_inc_define_entity_field_name_item_bookmark($entity->$field_name,$entity);
        }
    }else if($field_name=='field_is_carpeta_noticia_destaca'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){
            $entity->$field_name=red_solr_inc_define_entity_field_is_carpeta_noticia_destaca($entity->$field_name,$entity);
        }
    }else if($field_name=='field_item_rated'){
        if(hontza_solr_is_empty_numeric_reference($entity->$field_name)){             
            $entity->$field_name=red_solr_inc_define_entity_field_name_item_rated($entity->$field_name,$entity);
        }
    }
    //
    $index_key = apachesolr_index_key($field_info);
    foreach ($entity->$field_name as $field) {
      if ($index_value = isset($field['value']) ? (int) $field['value'] : FALSE) {
        $fields[] = array(
          'key' => $index_key,
          'value' => $index_value,
        );          
      }
    }
  }
  return $fields;
}

/**
 * This function is used during indexing to normalize the DATE and DATETIME
 * fields into the appropriate format for Apache Solr.
 */
function apachesolr_date_default_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  if (!empty($entity->{$field_name})) {
    $field = $entity->$field_name;
    foreach ($field as $value) {
      // Construct a Solr-ready date string in UTC time zone based on the field's date string and time zone.
      $tz = new DateTimeZone(isset($value['timezone']) ? $value['timezone'] : 'UTC');

      // $fields may end up having two values; one for the start date
      // and one for the end date.
      if ($date = date_create($value['value'], $tz)) {
        $index_value = apachesolr_date_iso($date->format('U'));
        $fields[] = array(
          'key' => $index_key,
          'value' => $index_value,
        );

        if (isset($value['value2'])) {
          if ($date = date_create($value['value2'], $tz)) {
            $index_value = apachesolr_date_iso($date->format('U'));
            $fields[] = array(
              // The value2 element is the end date. Therefore it gets indexed
              // into its own Solr field.
              'key' => $index_key . '_end',
              'value' => $index_value,
            );
          }
        }
      }
    }
  }
  return $fields;
}

/**
 * This function is used during indexing to normalize the DATESTAMP fields
 * into the appropriate format for Apache Solr.
 */
function apachesolr_datestamp_default_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  if (!empty($entity->{$field_name})) {
    // $fields may end up having two values; one for the start date
    // and one for the end date.
    $field = $entity->$field_name;
    list($lang, $values) = each($field);

    foreach ($values as $value) {
      if (isset($value['value']) && $value['value'] != 0) {
        $index_value = apachesolr_date_iso($value['value']);
        $fields[] = array(
          'key' => $index_key,
          'value' => $index_value,
        );
      }
      if (isset($value['value2']) && $value['value'] != 0) {
        $index_value = apachesolr_date_iso($value['value2']);
        $fields[] = array(
          // The value2 element is the end date. Therefore it gets indexed
          // into its own Solr field.
          'key' => $index_key . '_end',
          'value' => $index_value,
        );
      }
    }
  }
  return $fields;
}

function apachesolr_floatval($value) {
  return sprintf('%0.20f', $value);
}

/**
 *  Indexing callback for the node_reference module
 *  by the references module
 */
function apachesolr_nodereference_indexing_callback($entity, $field_name, $index_key, $field_info) {
  $fields = array();
  if (!empty($entity->{$field_name})) {
    $index_key = apachesolr_index_key($field_info);
    foreach ($entity->$field_name as $field_references) {
      foreach ($field_references as $reference) {
        // Validate that reference is an array
        // If not, skip this element
        if(!is_array($reference)){
          continue;
        }
        if ($index_value = (!empty($reference['nid'])) ? $reference['nid'] : FALSE) {
          $fields[] = array(
            'key' => $index_key,
            'value' => $index_value,
          );
        }else{
            //intelsat-2004
            if($field_name=='field_item_canal_reference'){
                //echo print_r($entity->$field_name,1);exit();
                //$my_reference=hontza_solr_get_reference($field_references['nid']);
                if(isset($field_references['nid']) && !empty($field_references['nid'])){ 
                    $fields[] = array(
                    'key' => $index_key,
                    'value' => $field_references['nid'],                
                  );
                }
            }
            //
        }
      }
    }
  }
  return $fields;
}

/**
 *  Indexing callback for the user_reference module
 *  by the references module
 */
function apachesolr_userreference_indexing_callback($entity, $field_name, $index_key, $field_info) {
  
  $fields = array();
  if (!empty($entity->$field_name)) {
    $index_key = apachesolr_index_key($field_info);
     //intelsat-2014
    if($field_name=='field_item_validador_uid'){
        if(hontza_solr_is_empty_userreference($entity->$field_name)){
           $entity->$field_name=hontza_solr_define_entity_field_name_item_validador_uid($entity->$field_name,$entity);
        }
    }
    //
    foreach ($entity->$field_name as $field_references) {
      foreach ($field_references as $reference) {
        // Validate that reference is an array
        // If not, skip this element
        if(!is_array($reference)){
          continue;
        }

        if ($index_value = (isset($reference['uid']) && strlen($reference['uid'])) ? $reference['uid'] : FALSE) {
          $fields[] = array(
            'key' => $index_key,
            'value' => $index_value,
          );
        }else{
            //intelsat-2014
            if($field_name=='field_item_validador_uid'){
                if(isset($field_references['uid']) && !empty($field_references['uid'])){
                //if(isset($field_references['uid']) && (!empty($field_references['uid']) || $field_references['uid']===0)){                
                    $fields[] = array(
                    'key' => $index_key,
                    'value' => $field_references['uid'],                
                  );                   
                }              
            }
            //
        }
      }
    }
  }
  return $fields;
}

/**
 * Extract HTML tag contents from $text and add to boost fields.
 *
 * $text must be stripped of control characters before hand.
 */
function apachesolr_add_tags_to_document($document, $text) {
  $tags_to_index = variable_get('apachesolr_tags_to_index', array(
    'h1' => 'tags_h1',
    'h2' => 'tags_h2_h3',
    'h3' => 'tags_h2_h3',
    'h4' => 'tags_h4_h5_h6',
    'h5' => 'tags_h4_h5_h6',
    'h6' => 'tags_h4_h5_h6',
    'u' => 'tags_inline',
    'b' => 'tags_inline',
    'i' => 'tags_inline',
    'strong' => 'tags_inline',
    'em' => 'tags_inline',
    'a' => 'tags_a'
  ));

  // Strip off all ignored tags.
  $text = strip_tags($text, '<' . implode('><', array_keys($tags_to_index)) . '>');

  preg_match_all('@<(' . implode('|', array_keys($tags_to_index)) . ')[^>]*>(.*)</\1>@Ui', $text, $matches);
  foreach ($matches[1] as $key => $tag) {
    $tag = strtolower($tag);
    // We don't want to index links auto-generated by the url filter.
    if ($tag != 'a' || !preg_match('@(?:http://|https://|ftp://|mailto:|smb://|afp://|file://|gopher://|news://|ssl://|sslv2://|sslv3://|tls://|tcp://|udp://|www\.)[a-zA-Z0-9]+@', $matches[2][$key])) {
      if (!isset($document->{$tags_to_index[$tag]})) {
        $document->{$tags_to_index[$tag]} = '';
      }
      $document->{$tags_to_index[$tag]} .= ' ' . apachesolr_clean_text($matches[2][$key]);
    }
  }
}

/**
 * Additional index utility functions
 */

/**
 * hook_cron() helper to try to make the index table consistent with their respective entity table.
 */
function apachesolr_index_node_check_table() {
  // Check for unpublished content that wasn't deleted from the index.
  $table = apachesolr_get_indexer_table('node');
  // We do not check more nodes than double the cron limit per time
  // Update or delete at most this many in each Solr query.
  $limit = variable_get('apachesolr_cron_mass_limit', 500);
  $result = db_query("SELECT n.nid, n.status FROM {{$table}} aien INNER JOIN {node} n ON n.nid = aien.entity_id WHERE aien.status <> n.status LIMIT 0, %d", array($limit*2));
  $nodes = array();
  while($record = db_fetch_array($result)) {
    $nodes[$record['nid']] = $record;
  }

  $node_lists = array_chunk($nodes, $limit, TRUE);

  foreach ($node_lists as $nodes) {
    watchdog('Apache Solr', 'On cron running apachesolr_nodeapi_mass_update() on nids @nids', array('@nids' => implode(',', array_keys($nodes))), WATCHDOG_NOTICE);
    if (!apachesolr_index_nodeapi_mass_update($nodes, $table)) {
      // Solr query failed - so stop trying.
      break;
    }
  }

  // Check for deleted content that wasn't deleted from the index.
  $result = db_query("SELECT aien.entity_id FROM {{$table}} aien LEFT JOIN {node} n ON n.nid = aien.entity_id WHERE n.nid = NULL LIMIT 0, %d", array($limit*2));
  $nodes = array();
  while($record = db_fetch_array($result)) {
    $nodes[$record['nid']] = $record;
  }
  $node_lists = array_chunk($nodes, $limit, TRUE);

  foreach ($node_lists as $nodes) {
    watchdog('Apache Solr', 'On cron running apachesolr_nodeapi_mass_delete() on nids @nids', array('@nids' => implode(',', array_keys($nodes))), WATCHDOG_WARNING);
    if (!apachesolr_index_nodeapi_mass_delete($nodes, $table)) {
      // Solr query failed - so stop trying.
      break;
    }
  }
}

/**
 * Mass Update nodes from the solr indexer table
 *
 * @param type $nodes
 * @param type $table
 * @return type
 */
function apachesolr_index_nodeapi_mass_update($nodes, $table = NULL) {
  if (empty($nodes)) {
    return TRUE;
  }
  if (empty($table)) {
    $table = apachesolr_get_indexer_table('node');
  }

  if (apachesolr_environment_variable_get(apachesolr_default_environment(), 'apachesolr_read_only', APACHESOLR_READ_WRITE) == APACHESOLR_READ_ONLY) {
    return TRUE;
  }

  $published_ids = array();
  $unpublished_ids = array();
  foreach ($nodes as $node) {
    if ($node->status) {
      $published_ids[$node->nid] = apachesolr_document_id($node->nid);
    }
    else {
      $unpublished_ids[$node->nid] = apachesolr_document_id($node->nid);
    }
  }
  try {
    $env_id = apachesolr_default_environment();
    $solr = apachesolr_get_solr($env_id);
    $solr->deleteByMultipleIds($unpublished_ids);
    apachesolr_set_last_index_updated($env_id, APACHESOLR_REQUEST_TIME);

    // There was no exception, so update the table.
    if (count($published_ids)) {
      $query = "UPDATE {{$table}} asn SET asn.changed = '%s' WHERE asn.entity_id IN (" . db_placeholders($published_ids) . ")";
      db_query($query, array_merge(array(APACHESOLR_REQUEST_TIME), $published_ids));
    }
    if (count($unpublished_ids)) {
      $query = "UPDATE {{$table}} asn SET asn.changed = '%s', asn.status = 0 WHERE asn.entity_id IN (" . db_placeholders($unpublished_ids) . ")";
      db_query($query, array_merge(array(APACHESOLR_REQUEST_TIME), $unpublished_ids));
    }
    return TRUE;
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
    return FALSE;
  }
}

/**
 * Mass delete nodes form the solr indexer tables.
 *
 * @param type $nodes
 * @param type $table
 * @return type
 */
function apachesolr_index_nodeapi_mass_delete($nodes, $table = NULL) {
  if (empty($nodes)) {
    return TRUE;
  }
  if (empty($table)) {
    $table = apachesolr_get_indexer_table('node');
  }

  if (apachesolr_environment_variable_get(apachesolr_default_environment(), 'apachesolr_read_only', APACHESOLR_READ_WRITE) == APACHESOLR_READ_ONLY) {
    return TRUE;
  }

  $ids = array();
  $nids = array();
  foreach ($nodes as $node) {
    $ids[] = apachesolr_document_id($node->nid);
    $nids[] = $node->nid;
  }
  try {
    $env_id = apachesolr_default_environment();
    $solr = apachesolr_get_solr($env_id);
    $solr->deleteByMultipleIds($ids);
    apachesolr_set_last_index_updated($env_id, APACHESOLR_REQUEST_TIME);    // There was no exception, so update the table.
    db_query("DELETE FROM {{$table}} WHERE entity_id IN " . db_placeholders($nids), $nids);
    return TRUE;
  }
  catch (Exception $e) {
    watchdog('Apache Solr', nl2br(check_plain($e->getMessage())), NULL, WATCHDOG_ERROR);
    return FALSE;
  }
}
