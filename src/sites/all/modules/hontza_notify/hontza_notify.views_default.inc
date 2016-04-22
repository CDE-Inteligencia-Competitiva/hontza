<?php

/**
 * Implementation of hook_views_default_views().
 */
function hontza_notify_views_default_views() {
  /*
   * View de items validados de la última semana
   */
  $view = new view;
  $view->name = 'items_validos_notify';
  $view->description = 'Items validados para enviar a los usuarios suscritos al canal';
  $view->tag = 'notify';
  $view->view_php = '';
  $view->base_table = 'node';
  $view->is_cacheable = FALSE;
  $view->api_version = 2;
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */
  $handler = $view->new_display('default', 'Defaults', 'default');
  $handler->override_option('relationships', array(
    'flag_content_rel' => array(
      'label' => 'flag',
      'required' => 1,
      'flag' => 'leido_interesante',
      'user_scope' => 'any',
      'id' => 'flag_content_rel',
      'table' => 'node',
      'field' => 'flag_content_rel',
      'relationship' => 'none',
    ),
    'votingapi_cache' => array(
      'label' => 'Vote results',
      'required' => 0,
      'votingapi' => array(
        'value_type' => 'percent',
        'tag' => '',
        'function' => 'average',
      ),
      'id' => 'votingapi_cache',
      'table' => 'node',
      'field' => 'votingapi_cache',
      'relationship' => 'none',
    ),
  ));
  $handler->override_option('fields', array(
    'title' => array(
      'label' => 'Title',
      'alter' => array(
        'alter_text' => 0,
        'text' => '',
        'make_link' => 0,
        'path' => '',
        'link_class' => '',
        'alt' => '',
        'prefix' => '',
        'suffix' => '',
        'target' => '',
        'help' => '',
        'trim' => 0,
        'max_length' => '',
        'word_boundary' => 1,
        'ellipsis' => 1,
        'html' => 0,
        'strip_tags' => 0,
      ),
      'empty' => '',
      'hide_empty' => 0,
      'empty_zero' => 0,
      'link_to_node' => 0,
      'spaces' => array(
        'frontpage' => 0,
        'type' => 'spaces_og',
      ),
      'exclude' => 0,
      'id' => 'title',
      'table' => 'node',
      'field' => 'title',
      'relationship' => 'none',
    ),
  ));
  $handler->override_option('sorts', array(
    'imported' => array(
      'order' => 'DESC',
      'granularity' => 'second',
      'id' => 'imported',
      'table' => 'feeds_node_item',
      'field' => 'imported',
      'relationship' => 'none',
    ),
    'value' => array(
      'order' => 'DESC',
      'id' => 'value',
      'table' => 'votingapi_cache',
      'field' => 'value',
      'relationship' => 'votingapi_cache',
    ),
  ));
  $handler->override_option('arguments', array(
    'field_item_canal_reference_nid' => array(
      'default_action' => 'not found',
      'style_plugin' => 'default_summary',
      'style_options' => array(),
      'wildcard' => 'all',
      'wildcard_substitution' => 'All',
      'title' => '',
      'breadcrumb' => '',
      'default_argument_type' => 'fixed',
      'default_argument' => '',
      'validate_type' => 'none',
      'validate_fail' => 'not found',
      'break_phrase' => 0,
      'not' => 0,
      'id' => 'field_item_canal_reference_nid',
      'table' => 'node_data_field_item_canal_reference',
      'field' => 'field_item_canal_reference_nid',
      'validate_user_argument_type' => 'uid',
      'validate_user_roles' => array(
        '2' => 0,
        '5' => 0,
        '6' => 0,
      ),
      'relationship' => 'none',
      'default_options_div_prefix' => '',
      'default_argument_fixed' => '',
      'default_argument_user' => 0,
      'default_argument_php' => '',
      'validate_argument_node_type' => array(
        'faq' => 0,
        'feed' => 0,
        'feed_item' => 0,
        'book' => 0,
        'canal_busqueda' => 0,
        'canal_de_supercanal' => 0,
        'canal_de_yql' => 0,
        'debate' => 0,
        'fuentedapper' => 0,
        'grupo' => 0,
        'item' => 0,
        'item_clonado' => 0,
        'noticia' => 0,
        'noticias_portada' => 0,
        'page' => 0,
        'rss_feed' => 0,
        'rss_feed_importador' => 0,
        'servicio' => 0,
        'supercanal' => 0,
        'wiki' => 0,
      ),
      'validate_argument_node_access' => 0,
      'validate_argument_nid_type' => 'nid',
      'validate_argument_vocabulary' => array(
        '38' => 0,
        '26' => 0,
        '31' => 0,
        '33' => 0,
        '35' => 0,
        '32' => 0,
        '34' => 0,
        '28' => 0,
        '29' => 0,
        '30' => 0,
        '12' => 0,
        '46' => 0,
        '8' => 0,
        '1' => 0,
        '4' => 0,
        '7' => 0,
        '43' => 0,
        '37' => 0,
        '39' => 0,
        '44' => 0,
        '45' => 0,
        '3' => 0,
        '47' => 0,
        '36' => 0,
        '40' => 0,
        '19' => 0,
        '42' => 0,
        '13' => 0,
        '18' => 0,
        '14' => 0,
        '27' => 0,
        '6' => 0,
      ),
      'validate_argument_type' => 'tid',
      'validate_argument_transform' => 0,
      'validate_user_restrict_roles' => 0,
      'validate_argument_node_flag_name' => '*relationship*',
      'validate_argument_node_flag_test' => 'flaggable',
      'validate_argument_node_flag_id_type' => 'id',
      'validate_argument_user_flag_name' => '*relationship*',
      'validate_argument_user_flag_test' => 'flaggable',
      'validate_argument_user_flag_id_type' => 'id',
      'validate_argument_is_member' => 'OG_VIEWS_DO_NOT_VALIDATE_MEMBERSHIP',
      'validate_argument_group_node_type' => array(
        'grupo' => 0,
      ),
      'validate_argument_php' => '',
    ),
  ));
  $handler->override_option('filters', array(
    'type' => array(
      'operator' => 'in',
      'value' => array(
        'item' => 'item',
      ),
      'group' => '0',
      'exposed' => FALSE,
      'expose' => array(
        'operator' => FALSE,
        'label' => '',
      ),
      'id' => 'type',
      'table' => 'node',
      'field' => 'type',
      'relationship' => 'none',
    ),
    'imported' => array(
      'operator' => '>',
      'value' => array(
        'type' => 'date',
        'value' => '-7 day',
        'min' => '',
        'max' => '',
      ),
      'group' => '0',
      'exposed' => FALSE,
      'expose' => array(
        'operator' => FALSE,
        'label' => '',
      ),
      'id' => 'imported',
      'table' => 'feeds_node_item',
      'field' => 'imported',
      'relationship' => 'none',
    ),
  ));
  $handler->override_option('access', array(
    'type' => 'none',
  ));
  $handler->override_option('cache', array(
    'type' => 'none',
  ));
  $handler->override_option('items_per_page', 0);
  $handler = $view->new_display('block', 'Block', 'block_1');
  $handler->override_option('access', array(
    'type' => 'role',
    'role' => array(
      '1' => 1,
    ),
  ));
  $handler->override_option('items_per_page', 2);
  $handler->override_option('block_description', '');
  $handler->override_option('block_caching', -1);

  $views[$view->name] = $view;

  return $views;
}
