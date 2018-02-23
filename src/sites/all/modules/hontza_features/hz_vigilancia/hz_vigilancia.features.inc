<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function hz_vigilancia_ctools_plugin_api() {
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
function hz_vigilancia_node_info() {
  $items = array(
    'canal_busqueda' => array(
      'name' => t('Search Channel'),
      'module' => 'features',
      'description' => t('Saves local searches'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '1',
      'body_label' => t('Body'),
      'min_word_count' => '0',
      'help' => '',
    ),
    'canal_de_supercanal' => array(
      'name' => t('Source Channel'),
      'module' => 'features',
      'description' => t('Created from Pipes Sources'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '1',
      'body_label' => t('Body'),
      'min_word_count' => '0',
      'help' => '',
    ),
    'canal_de_yql' => array(
      'name' => t('Channel filtering several RSS'),
      'module' => 'features',
      'description' => t('Channel from several RSS feeds, concatenated with YQL.'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '0',
      'body_label' => '',
      'min_word_count' => '0',
      'help' => '',
    ),
    'item' => array(
      'name' => t('Item'),
      'module' => 'features',
      'description' => t('Node Imported using feeds'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '1',
      'body_label' => t('Body'),
      'min_word_count' => '0',
      'help' => '',
    ),
    'noticia' => array(
      'name' => t('User News'),
      'module' => 'features',
      'description' => '',
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '1',
      'body_label' => t('Body'),
      'min_word_count' => '0',
      'help' => '',
    ),
  );
  return $items;
}

/**
 * Implementation of hook_views_api().
 */
function hz_vigilancia_views_api() {
  return array(
    'api' => '2',
  );
}
