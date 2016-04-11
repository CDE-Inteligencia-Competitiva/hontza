<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function hz_publica_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "context" && $api == "context") {
    return array("version" => 3);
  }
  elseif ($module == "feeds" && $api == "feeds_importer_default") {
    return array("version" => 1);
  }
  elseif ($module == "strongarm" && $api == "strongarm") {
    return array("version" => 1);
  }
}

/**
 * Implementation of hook_node_info().
 */
function hz_publica_node_info() {
  $items = array(
    'noticias_portada' => array(
      'name' => t('News Cover'),
      'module' => 'features',
      'description' => t('Contents to create public access news'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '0',
      'body_label' => '',
      'min_word_count' => '0',
      'help' => '',
    ),
    'rss_feed' => array(
      'name' => t('Public Rss home'),
      'module' => 'features',
      'description' => t('Contents for the import of a Rss'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '0',
      'body_label' => '',
      'min_word_count' => '0',
      'help' => '',
    ),
    'rss_feed_importador' => array(
      'name' => t('Rss importer'),
      'module' => 'features',
      'description' => '',
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '0',
      'body_label' => '',
      'min_word_count' => '0',
      'help' => '',
    ),
  );
  return $items;
}

/**
 * Implementation of hook_views_api().
 */
function hz_publica_views_api() {
  return array(
    'api' => '2',
  );
}
