<?php

/**
 * Implementation of hook_feeds_importer_default().
 */
function hz_publica_feeds_importer_default() {
  $export = array();
  $feeds_importer = new stdClass;
  $feeds_importer->disabled = FALSE; /* Edit this to true to make a default feeds_importer disabled initially */
  $feeds_importer->api_version = 1;
  $feeds_importer->id = 'rss_feed_aveq';
  $feeds_importer->config = array(
    'name' => 'rss_feed_aveq',
    'description' => '',
    'fetcher' => array(
      'plugin_key' => 'FeedsHTTPFetcher',
      'config' => array(
        'auto_detect_feeds' => FALSE,
        'use_pubsubhubbub' => FALSE,
        'designated_hub' => '',
      ),
    ),
    'parser' => array(
      'plugin_key' => 'FeedsSyndicationParser',
      'config' => array(),
    ),
    'processor' => array(
      'plugin_key' => 'FeedsNodeProcessor',
      'config' => array(
        'content_type' => 'rss_feed',
        'input_format' => '0',
        'update_existing' => '1',
        'expire' => '-1',
        'mappings' => array(
          0 => array(
            'source' => 'title',
            'target' => 'title',
            'unique' => FALSE,
          ),
          1 => array(
            'source' => 'description',
            'target' => 'field_rss_descripcion',
            'unique' => FALSE,
          ),
          2 => array(
            'source' => 'guid',
            'target' => 'guid',
            'unique' => 1,
          ),
          3 => array(
            'source' => 'url',
            'target' => 'field_rss_link:url',
            'unique' => FALSE,
          ),
          4 => array(
            'source' => 'title',
            'target' => 'field_rss_link:title',
            'unique' => FALSE,
          ),
          5 => array(
            'source' => 'timestamp',
            'target' => 'created',
            'unique' => FALSE,
          ),
          6 => array(
            'source' => 'author_name',
            'target' => 'field_rss_autor',
            'unique' => FALSE,
          ),
          7 => array(
            'source' => 'tags',
            'target' => 'field_rss_categorias',
            'unique' => FALSE,
          ),
        ),
        'author' => 0,
      ),
    ),
    'content_type' => 'rss_feed_importador',
    'update' => 0,
    'import_period' => '0',
    'expire_period' => 3600,
    'import_on_create' => 1,
  );

  $export['rss_feed_aveq'] = $feeds_importer;
  return $export;
}
