<?php
// $Id: FeedsNodeProcessor.inc,v 1.51.2.2 2010/10/25 22:43:03 alexb Exp $

/**
 * @file
 * Class definition of FeedsNodeProcessor.
 */

// Create or delete FEEDS_NODE_BATCH_SIZE at a time.
define('FEEDS_NODE_BATCH_SIZE', 50);

// Deprecated. Use FEEDS_SKIPE_EXISTING, FEEDS_REPLACE_EXISTNG,
// FEEDS_UPDATE_EXISTING instead.
define('FEEDS_NODE_SKIP_EXISTING', 0);
define('FEEDS_NODE_REPLACE_EXISTING', 1);
define('FEEDS_NODE_UPDATE_EXISTING', 2);

/**
 * Creates nodes from feed items.
 */
class FeedsNodeProcessor extends FeedsProcessor {

  /**
   * Implementation of FeedsProcessor::process().
   */
  public function process(FeedsImportBatch $batch, FeedsSource $source) {
    // Keep track of processed items in this pass, set total number of items.
    $processed = 0;
    if (!$batch->getTotal(FEEDS_PROCESSING)) {
      $batch->setTotal(FEEDS_PROCESSING, count($batch->items));
    }
 
    //intelsat-2014
    if(hontza_solr_is_solr_activado()){
        hontza_solr_actualizar_canal_source_type($source->feed_nid);
        //hontza_solr_clear_cache_content($source->feed_nid);        
    }
    //
    //gemini
    $is_open_calais=is_open_calais_by_source($source);
    //if(hontza_canal_rss_is_visualizador_activado()){
    if(red_canal_is_canal_opencalais_activado()){
        $is_open_calais=hontza_canal_rss_is_canal_open_calais($source->feed_nid);
    }
    $is_alchemy=is_alchemy_by_source($source);
    
    
    //gemini-2013
    $is_validacion_automatica=hontza_is_validacion_automatica_by_feeds_source($source);    
    $last_import_time=hontza_save_canal_last_import_time_by_feeds_source($source);
    //echo 'source===='.print_r($source,1);
    //intelsat-2015
    $config=$source->getConfig();  
    $url=$config['FeedsHTTPFetcher']['source'];
    //intelsat-2016
    $grupo_source=hontza_get_grupo_by_feed_nid($source);
    //
    
    while ($item = $batch->shiftItem()) {      
      /*
      //intelsat-2015-kimonolabs
      if(hontza_canal_rss_is_kimonolabs_activado()){
          $item=kimonolabs_set_item_parse($source,$item);          
      }
      //
      */
      // Create/update if item does not exist or update existing is enabled.
      if (!($nid = $this->existingItemId($batch, $source,$item,'',$url)) || ($this->config['update_existing'] != FEEDS_SKIP_EXISTING)) {
        // Only proceed if item has actually changed.
        
        $hash = $this->hash($item);
        if (!empty($nid) && $hash == $this->getHash($nid)) {
            continue;
        }
        //gemini-2014
        if(!empty($nid)){
            continue;
        }
        if(hontza_is_item_duplicado($item,$source,'',$url)){
            continue;
        }
        //
        
        $node = $this->buildNode($nid, $source->feed_nid);
        $node->feeds_node_item->hash = $hash;
        /*
        //gemini-2014
        if(isset($item['field_uniq']) && !empty($item['field_uniq'])){
            $node->field_uniq[0]['value']=$item['field_uniq'];
        }else{
            if(hontza_is_sareko_id_desduplicados()){
                $node->field_uniq[0]['value']=hontza_get_elemento_uniq($item);
            }
        }
        //
        */ 

        // Map and save node. If errors occur don't stop but report them.
        try {
          $this->map($batch, $node);
          //intelsat-2014
          hontza_social_set_feed_node_item_url($source,$node);
          hontza_social_set_item_servidor_fields($item,$node);
          //
          //intelsat-2015
          if(hontza_solr_search_is_canal_correo($source,'',$url)){
              hontza_solr_search_set_feed_node_item_guid($source,$node,$item);
          }
          //
          //gemini
          $vid=3;
          //echo print_r($entity_array,1);
          $vids=array();
          $vids[0]=$vid;
          $my_tags=_community_tags_get_node_tags($node->nid,$vids);
          //
          if($is_alchemy){
              $node->body=apply_alchemy_api_by_feeds_node_item($node->body,$node->feeds_node_item,$grupo_source);
              //intelsat-2016
              //$node->teaser=$node->body;
              $node->teaser=node_teaser($node->body);
          }
          //gemini-2014
          //$begiratu=0;
          if(isset($node->nid) && !empty($node->nid)){
              if(!isset($node->created) || empty($node->created)){
                  $node->created=hontza_get_node_field($node->nid,'created');
                  //$begiratu=1;
              }
              
              //$old_created=$node->created;
              
              //$begiratu=1;
          }
          //intelsat-2014
          if(hontza_solr_is_solr_activado()){
            $node->field_item_solr_updated[0]['value']=1;
          }
          //
          //intelsat-2015
          if(red_despacho_is_activado()){
            red_despacho_unset_hound_id_item_title($node,$source->feed_nid);
          }
          node_save($node);
          //print $node->body;exit();

          //intelsat-2014
          hontza_solr_save_item($node);
          hontza_social_insert_item_interest_hontza_by_feed_item($node,$item);
          //
          
          /*if($begiratu){
              //if($old_created!=$node->created){
                  print 'old_created='.$old_created;
                  print 'created='.$node->created;
                  print 'node='.print_r($node,1);
                  exit();
              //}
          }*/
          //intelsat-2015
          if(hontza_canal_rss_is_visualizador_activado()){
              publico_vigilancia_save_item_tags_comments($node,$item);
          }
          
          //gemini
          if($is_open_calais){
                //intelsat-2016
                //canal_open_calais_term_save($node,$my_tags);
                canal_open_calais_term_save($node,$my_tags,$grupo_source);
          }
          //gemini-2013
          if($is_validacion_automatica){
              hontza_import_validacion_automatica($node);
          }
          //
          //intelsat-2015
          red_despacho_on_node_save('', $node);
          if (!empty($nid)) {
            $batch->updated++;
          }
          else {
            $batch->created++;
          }
        }
        catch (Exception $e) {
          drupal_set_message($e->getMessage(), 'warning');
          watchdog('feeds', $e->getMessage(), array(), WATCHDOG_WARNING);
        }
      }
      $processed++;
      if ($processed >= variable_get('feeds_node_batch_size', FEEDS_NODE_BATCH_SIZE)) {
        $batch->setProgress(FEEDS_PROCESSING, $batch->created + $batch->updated);
        return;
      }
    }

    // Set messages.
    if ($batch->created) {
      drupal_set_message(format_plural($batch->created, 'Created @number @type node.', 'Created @number @type nodes.', array('@number' => $batch->created, '@type' => node_get_types('name', $this->config['content_type']))));
    }
    elseif ($batch->updated) {
      drupal_set_message(format_plural($batch->updated, 'Updated @number @type node.', 'Updated @number @type nodes.', array('@number' => $batch->updated, '@type' => node_get_types('name', $this->config['content_type']))));
    }
    else {
      drupal_set_message(t('There is no new content.'));
    }
    $batch->setProgress(FEEDS_PROCESSING, FEEDS_BATCH_COMPLETE);
  }

  /**
   * Implementation of FeedsProcessor::clear().
   */
  public function clear(FeedsBatch $batch, FeedsSource $source) {
    if (!$batch->getTotal(FEEDS_CLEARING)) {
      $total = db_result(db_query("SELECT COUNT(nid) FROM {feeds_node_item} WHERE id = '%s' AND feed_nid = %d", $source->id, $source->feed_nid));
      $batch->setTotal(FEEDS_CLEARING, $total);
    }
    $result = db_query_range("SELECT nid FROM {feeds_node_item} WHERE id = '%s' AND feed_nid = %d", $source->id, $source->feed_nid, 0, variable_get('feeds_node_batch_size', FEEDS_NODE_BATCH_SIZE));
    while ($node = db_fetch_object($result)) {
      _feeds_node_delete($node->nid);
      $batch->deleted++;
    }
    if (db_result(db_query_range("SELECT nid FROM {feeds_node_item} WHERE id = '%s' AND feed_nid = %d", $source->id, $source->feed_nid, 0, 1))) {
      $batch->setProgress(FEEDS_CLEARING, $batch->deleted);
      return;
    }

    // Set message.
    drupal_get_messages('status');
    if ($batch->deleted) {
      drupal_set_message(format_plural($batch->deleted, 'Deleted @number node.', 'Deleted @number nodes.', array('@number' => $batch->deleted)));
    }
    else {
      drupal_set_message(t('There is no content to be deleted.'));
    }
    $batch->setProgress(FEEDS_CLEARING, FEEDS_BATCH_COMPLETE);
  }

  /**
   * Implement expire().
   */
  public function expire($time = NULL) {
    if ($time === NULL) {
      $time = $this->expiryTime();
    }
    if ($time == FEEDS_EXPIRE_NEVER) {
      return;
    }
    $result = db_query_range("SELECT n.nid FROM {node} n JOIN {feeds_node_item} fni ON n.nid = fni.nid WHERE fni.id = '%s' AND n.created < %d", $this->id, FEEDS_REQUEST_TIME - $time, 0, variable_get('feeds_node_batch_size', FEEDS_NODE_BATCH_SIZE));
    while ($node = db_fetch_object($result)) {
      _feeds_node_delete($node->nid);
    }
    if (db_result(db_query_range("SELECT n.nid FROM {node} n JOIN {feeds_node_item} fni ON n.nid = fni.nid WHERE fni.id = '%s' AND n.created < %d", $this->id, FEEDS_REQUEST_TIME - $time, 0, 1))) {
      return FEEDS_BATCH_ACTIVE;
    }
    return FEEDS_BATCH_COMPLETE;
  }

  /**
   * Return expiry time.
   */
  public function expiryTime() {
    return $this->config['expire'];
  }

  /**
   * Override parent::configDefaults().
   */
  public function configDefaults() {
    $types = node_get_types('names');
    $type = isset($types['story']) ? 'story' : key($types);
    return array(
      'content_type' => $type,
      'input_format' => FILTER_FORMAT_DEFAULT,
      'update_existing' => FEEDS_SKIP_EXISTING,
      'expire' => FEEDS_EXPIRE_NEVER,
      'mappings' => array(),
      'author' => 0,
    );
  }

  /**
   * Override parent::configForm().
   */
  public function configForm(&$form_state) {
    $types = node_get_types('names');
    $form = array();
    $form['content_type'] = array(
      '#type' => 'select',
      '#title' => t('Content type'),
      '#description' => t('Select the content type for the nodes to be created. <strong>Note:</strong> Users with "import !feed_id feeds" permissions will be able to <strong>import</strong> nodes of the content type selected here regardless of the node level permissions. Further, users with "clear !feed_id permissions" will be able to <strong>delete</strong> imported nodes regardless of their node level permissions.', array('!feed_id' => $this->id)),
      '#options' => $types,
      '#default_value' => $this->config['content_type'],
    );
    $format_options = array(FILTER_FORMAT_DEFAULT => t('Default format'));
    $formats = filter_formats();
      foreach ($formats as $format) {
        $format_options[$format->format] = $format->name;
      }
    $form['input_format'] = array(
      '#type' => 'select',
      '#title' => t('Input format'),
      '#description' => t('Select the input format for the body field of the nodes to be created.'),
      '#options' => $format_options,
      '#default_value' => $this->config['input_format'],
    );
    $author = user_load(array('uid' => $this->config['author']));
    $form['author'] = array(
      '#type' => 'textfield',
      '#title' => t('Author'),
      '#description' => t('Select the author of the nodes to be created - leave empty to assign "anonymous".'),
      '#autocomplete_path' => 'user/autocomplete',
      '#default_value' => empty($author->name) ?  'anonymous' : check_plain($author->name),
    );
    $period = drupal_map_assoc(array(FEEDS_EXPIRE_NEVER, 3600, 10800, 21600, 43200, 86400, 259200, 604800, 604800 * 4, 604800 * 12, 604800 * 24, 31536000), 'feeds_format_expire');
    $form['expire'] = array(
      '#type' => 'select',
      '#title' => t('Expire nodes'),
      '#options' => $period,
      '#description' => t('Select after how much time nodes should be deleted. The node\'s published date will be used for determining the node\'s age, see Mapping settings.'),
      '#default_value' => $this->config['expire'],
    );
    $form['update_existing'] = array(
      '#type' => 'radios',
      '#title' => t('Update existing nodes'),
      '#description' => t('Select how existing nodes should be updated. Existing nodes will be determined using mappings that are a "unique target".'),
      '#options' => array(
        FEEDS_SKIP_EXISTING => 'Do not update existing nodes',
        FEEDS_REPLACE_EXISTING => 'Replace existing nodes',
        FEEDS_UPDATE_EXISTING => 'Update existing nodes (slower than replacing them)',
      ),
      '#default_value' => $this->config['update_existing'],
    );
    return $form;
  }

  /**
   * Override parent::configFormValidate().
   */
  public function configFormValidate(&$values) {
    if ($author = user_load(array('name' => $values['author']))) {
      $values['author'] = $author->uid;
    }
    else {
      $values['author'] = 0;
    }
  }

  /**
   * Reschedule if expiry time changes.
   */
  public function configFormSubmit(&$values) {
    if ($this->config['expire'] != $values['expire']) {
      feeds_reschedule($this->id);
    }
    parent::configFormSubmit($values);
  }

  /**
   * Override setTargetElement to operate on a target item that is a node.
   */
  public function setTargetElement($target_node, $target_element, $value) {
    if (in_array($target_element, array('url', 'guid'))) {
      $target_node->feeds_node_item->$target_element = $value;
    }
    elseif ($target_element == 'body') {
      $target_node->teaser = node_teaser($value);
      $target_node->body = $value;
    }
    elseif (in_array($target_element, array('title', 'status', 'created', 'nid', 'uid'))) {
      $target_node->$target_element = $value;
    }
  }

  /**
   * Return available mapping targets.
   */
  public function getMappingTargets() {
    $targets = array(
      'title' => array(
        'name' => t('Title'),
        'description' => t('The title of the node.'),
       ),
     );
    // Include body field only if available.
    $type = node_get_types('type',  $this->config['content_type']);
    if ($type->has_body) {
      // Using 'teaser' instead of 'body' forces entire content above the break.
      $targets['body'] = array(
        'name' => t('Body'),
        'description' => t('The body of the node. The teaser will be the same as the entire body.'),
      );
    }
    $targets += array(
      'nid' => array(
        'name' => t('Node ID'),
        'description' => t('The nid of the node. NOTE: use this feature with care, node ids are usually assigned by Drupal.'),
        'optional_unique' => TRUE,
      ),
      'uid' => array(
        'name' => t('User ID'),
        'description' => t('The Drupal user ID of the node author.'),
      ),
      'status' => array(
        'name' => t('Published status'),
        'description' => t('Whether a node is published or not. 1 stands for published, 0 for not published.'),
      ),
      'created' => array(
        'name' => t('Published date'),
        'description' => t('The UNIX time when a node has been published.'),
      ),
      'url' => array(
        'name' => t('URL'),
        'description' => t('The external URL of the node. E. g. the feed item URL in the case of a syndication feed. May be unique.'),
        'optional_unique' => TRUE,
      ),
      'guid' => array(
        'name' => t('GUID'),
        'description' => t('The external GUID of the node. E. g. the feed item GUID in the case of a syndication feed. May be unique.'),
        'optional_unique' => TRUE,
      ),
    );

    // Let other modules expose mapping targets.
    self::loadMappers();
    drupal_alter('feeds_node_processor_targets', $targets, $this->config['content_type']);

    return $targets;
  }

  /**
   * Get nid of an existing feed item node if available.
   */
  protected function existingItemId(FeedsImportBatch $batch, FeedsSource $source,$item='',$canal_in='',$url_rss_in='') {
    //intelsat-2015
    if(red_despacho_is_canal_duplicate_news($source->feed_nid)){
        return 0;
    }
    // Iterate through all unique targets and test whether they do already
    // exist in the database.
    foreach ($this->uniqueTargets($batch) as $target => $my_value) {
      //intelsat-2015      
      $value=$my_value;
      //intelsat-2015-kimonolabs
      /*
      //intelsat-2015-kimonolabs
      $is_kimonolabs_json=0;
      if(hontza_canal_rss_is_kimonolabs_activado()){
        $is_kimonolabs_json=kimonolabs_get_is_kimonolabs_json_feedsSource($batch,$source);
        if($target=='url' && empty($value)){
            $value=hontza_solr_search_create_guid($item);
        }
      }*/
      //intelsat-2015            
      $is_canal_correo=0;
      if(hontza_solr_search_is_canal_correo($source,$canal_in,$url_rss_in)){
          $is_canal_correo=1;
          if($target=='guid' && empty($value)){
              $value=hontza_solr_search_create_guid($item);
          }
      }
      //
      switch ($target) {
        case 'nid':
          $nid = db_result(db_query("SELECT nid FROM {node} WHERE nid = %d", $value));
          break;
        case 'url':
          $nid = db_result(db_query("SELECT nid FROM {feeds_node_item} WHERE feed_nid = %d AND id = '%s' AND url = '%s'", $source->feed_nid, $source->id, $value));
          break;
        case 'guid':
          //intelsat-2015  
          if($is_canal_correo){
            $nid=hontza_solr_search_existingItemId($source,$value);  
          }else{    
            $nid = db_result(db_query("SELECT nid FROM {feeds_node_item} WHERE feed_nid = %d AND id = '%s' AND guid = '%s'", $source->feed_nid, $source->id, $value));
          }
          break;
      }
      if ($nid) {         
        // Return with the first nid found.
        return $nid;
      }
    }
      
    return 0;
  }

  /**
   * Creates a new node object in memory and returns it.
   */
  protected function buildNode($nid, $feed_nid) {
    $node = new stdClass();
    if (empty($nid)) {
      $node->created = FEEDS_REQUEST_TIME;
      $populate = TRUE;
    }
    else {
      if ($this->config['update_existing'] == FEEDS_UPDATE_EXISTING) {
        $node = node_load($nid, NULL, TRUE);
      }
      else {
        $node->nid = $nid;
        $node->vid = db_result(db_query("SELECT vid FROM {node} WHERE nid = %d", $nid));
        $populate = TRUE;
      }
    }
    if ($populate) {
      $node->type = $this->config['content_type'];
      $node->changed = FEEDS_REQUEST_TIME;
      $node->format = $this->config['input_format'];
      $node->feeds_node_item = new stdClass();
      $node->feeds_node_item->id = $this->id;
      $node->feeds_node_item->imported = FEEDS_REQUEST_TIME;
      $node->feeds_node_item->feed_nid = $feed_nid;
      $node->feeds_node_item->url = '';
      $node->feeds_node_item->guid = '';
    }

    static $included;
    if (!$included) {
      module_load_include('inc', 'node', 'node.pages');
      $included = TRUE;
    }
    node_object_prepare($node);

    // Populate properties that are set by node_object_prepare().
    $node->log = 'Created/updated by FeedsNodeProcessor';
    if ($populate) {
      $node->uid = $this->config['author'];
    }
    return $node;
  }

  /**
   * Create MD5 hash of item and mappings array.
   *
   * Include mappings as a change in mappings may have an affect on the item
   * produced.
   *
   * @return Always returns a hash, even with empty, NULL, FALSE:
   *  Empty arrays return 40cd750bba9870f18aada2478b24840a
   *  Empty/NULL/FALSE strings return d41d8cd98f00b204e9800998ecf8427e
   */
  protected function hash($item) {
    static $serialized_mappings;
    if (!$serialized_mappings) {
        //gemini
        //$serialized_mappings = serialize($this->config['mappings']);
        $new_mappings=my_fix_feeds_serialize($this->config['mappings']);
        $serialized_mappings = @serialize($new_mappings);
        //
    }
    
    //gemini
    //return hash('md5', serialize($item) . $serialized_mappings);
    //return hash('md5', @serialize($item) . $serialized_mappings);
    $new_item=my_fix_feeds_serialize($item);
    $s=@serialize($new_item) . $serialized_mappings;
    return hash('md5',$s);
    //
  }

  /**
   * Retrieve MD5 hash of $nid from DB.
   * @return Empty string if no item is found, hash otherwise.
   */
  protected function getHash($nid) {
    $hash = db_result(db_query("SELECT hash FROM {feeds_node_item} WHERE nid = %d", $nid));
    if ($hash) {
      // Return with the hash.
      return $hash;
    }
    return '';
  }
}

/**
 * Copy of node_delete() that circumvents node_access().
 *
 * Used for batch deletion.
 */
function _feeds_node_delete($nid) {

  $node = node_load($nid);

  db_query("DELETE FROM {node} WHERE nid = %d", $node->nid);
  db_query("DELETE FROM {node_revisions} WHERE nid = %d", $node->nid);

  // Call the node-specific callback (if any):
  node_invoke($node, 'delete');
  node_invoke_nodeapi($node, 'delete');

  // Clear the page and block caches.
  cache_clear_all();

  // Remove this node from the search index if needed.
  if (function_exists('search_wipe')) {
    search_wipe($node->nid, 'node');
  }
  watchdog('content', '@type: deleted %title.', array('@type' => $node->type, '%title' => $node->title));
  drupal_set_message(t('@type %title has been deleted.', array('@type' => node_get_types('name', $node), '%title' => $node->title)));
}
//gemini
function my_fix_feeds_serialize($item){
    $result=array();
    if(!empty($item)){
        foreach($item as $my_name=>$row){
            $result[$my_name]=(array) $row;
            //gemini-2013
            if(!empty($result[$my_name])){
                foreach($result[$my_name] as $k=>$v){
                    $result[$my_name][$k]=(array) $v;
                }
            }
        }
    }
    //echo print_r($result,1);exit();
    return $result;
}