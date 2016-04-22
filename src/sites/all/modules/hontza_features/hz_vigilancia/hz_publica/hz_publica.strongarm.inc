<?php

/**
 * Implementation of hook_strongarm().
 */
function hz_publica_strongarm() {
  $export = array();
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_noticias_portada';
  $strongarm->value = 0;

  $export['comment_anonymous_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_rss_feed';
  $strongarm->value = 0;

  $export['comment_anonymous_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_rss_feed_importador';
  $strongarm->value = 0;

  $export['comment_anonymous_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_noticias_portada';
  $strongarm->value = '3';

  $export['comment_controls_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_rss_feed';
  $strongarm->value = '3';

  $export['comment_controls_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_rss_feed_importador';
  $strongarm->value = '3';

  $export['comment_controls_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_noticias_portada';
  $strongarm->value = '4';

  $export['comment_default_mode_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_rss_feed';
  $strongarm->value = '4';

  $export['comment_default_mode_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_rss_feed_importador';
  $strongarm->value = '4';

  $export['comment_default_mode_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_noticias_portada';
  $strongarm->value = '1';

  $export['comment_default_order_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_rss_feed';
  $strongarm->value = '1';

  $export['comment_default_order_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_rss_feed_importador';
  $strongarm->value = '1';

  $export['comment_default_order_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_noticias_portada';
  $strongarm->value = '50';

  $export['comment_default_per_page_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_rss_feed';
  $strongarm->value = '50';

  $export['comment_default_per_page_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_rss_feed_importador';
  $strongarm->value = '50';

  $export['comment_default_per_page_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_noticias_portada';
  $strongarm->value = '0';

  $export['comment_form_location_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_rss_feed';
  $strongarm->value = '0';

  $export['comment_form_location_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_rss_feed_importador';
  $strongarm->value = '0';

  $export['comment_form_location_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_noticias_portada';
  $strongarm->value = '0';

  $export['comment_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_noticias_portada';
  $strongarm->value = '1';

  $export['comment_preview_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_rss_feed';
  $strongarm->value = '1';

  $export['comment_preview_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_rss_feed_importador';
  $strongarm->value = '1';

  $export['comment_preview_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_rss_feed';
  $strongarm->value = '0';

  $export['comment_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_rss_feed_importador';
  $strongarm->value = '0';

  $export['comment_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_noticias_portada';
  $strongarm->value = '1';

  $export['comment_subject_field_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_rss_feed';
  $strongarm->value = '1';

  $export['comment_subject_field_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_rss_feed_importador';
  $strongarm->value = '1';

  $export['comment_subject_field_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_images_noticias_portada';
  $strongarm->value = 'none';

  $export['comment_upload_images_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_images_rss_feed';
  $strongarm->value = 'none';

  $export['comment_upload_images_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_images_rss_feed_importador';
  $strongarm->value = 'none';

  $export['comment_upload_images_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_noticias_portada';
  $strongarm->value = '0';

  $export['comment_upload_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_rss_feed';
  $strongarm->value = '0';

  $export['comment_upload_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_rss_feed_importador';
  $strongarm->value = '0';

  $export['comment_upload_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'community_tags_display_noticias_portada';
  $strongarm->value = '1';

  $export['community_tags_display_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'community_tags_display_rss_feed';
  $strongarm->value = '1';

  $export['community_tags_display_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'community_tags_display_rss_feed_importador';
  $strongarm->value = '1';

  $export['community_tags_display_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'content_extra_weights_noticias_portada';
  $strongarm->value = array(
    'title' => '-5',
    'revision_information' => '-2',
    'author' => '-1',
    'options' => '0',
    'comment_settings' => '3',
    'menu' => '-3',
    'path' => '2',
    'attachments' => '1',
  );

  $export['content_extra_weights_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'content_extra_weights_rss_feed';
  $strongarm->value = array(
    'title' => '-5',
    'revision_information' => '1',
    'author' => '2',
    'options' => '3',
    'comment_settings' => '6',
    'menu' => '0',
    'path' => '5',
    'attachments' => '4',
  );

  $export['content_extra_weights_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'form_build_id_noticias_portada';
  $strongarm->value = 'form-9a215024532a8b903f75328e726d1854';

  $export['form_build_id_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'form_build_id_rss_feed';
  $strongarm->value = 'form-a9ed9cb24338bfb8bf31e3bfa51d5d37';

  $export['form_build_id_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'form_build_id_rss_feed_importador';
  $strongarm->value = 'form-a9d8027b6ba0b5f0f4be8043dfef16cb';

  $export['form_build_id_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_noticias_portada';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_rss_feed';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_rss_feed_importador';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_rss_feed_importador'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_noticias_portada_pattern';
  $strongarm->value = 'noticias/home/[title-raw]';

  $export['pathauto_node_noticias_portada_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_rss_feed_importador_pattern';
  $strongarm->value = '';

  $export['pathauto_node_rss_feed_importador_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_rss_feed_pattern';
  $strongarm->value = 'noticias-aveq/[title-raw]';

  $export['pathauto_node_rss_feed_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'show_preview_changes_rss_feed';
  $strongarm->value = 1;

  $export['show_preview_changes_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'spaces_dashboard_blocks';
  $strongarm->value = array(
    'block-3' => 1,
    'block-4' => 1,
    'book-0' => 0,
    'comment-0' => 0,
    'community_tags-0' => 0,
    'context_ui-devel' => 0,
    'devel-0' => 0,
    'devel-2' => 0,
    'faq-0' => 0,
    'faq-1' => 0,
    'faq-2' => 0,
    'fivestar-0' => 1,
    'forum-0' => 0,
    'forum-1' => 0,
    'locale-0' => 0,
    'menu-admin' => 0,
    'menu-devel' => 0,
    'menu-features' => 0,
    'menu-menu-anadir-pagina-libro' => 0,
    'menu-menu-area-debate' => 0,
    'menu-menu-canales' => 0,
    'menu-menu-crear-noticia' => 0,
    'menu-menu-fuentes' => 0,
    'menu-menu-mis-contenidos' => 0,
    'menu-primary-links' => 0,
    'menu-secondary-links' => 0,
    'node-0' => 0,
    'og-0' => 0,
    'og-3' => 0,
    'og_forum-0' => 0,
    'og_forum-1' => 0,
    'og_views-0' => 0,
    'og_views-1' => 0,
    'profile-0' => 0,
    'search-0' => 0,
    'spaces_dashboard-editor' => 1,
    'stored_views-save' => 0,
    'system-0' => 0,
    'tagadelic-0' => 0,
    'tagadelic-1' => 0,
    'tagadelic-3' => 0,
    'tagadelic-4' => 0,
    'tagadelic-5' => 0,
    'tagadelic-6' => 0,
    'user-0' => 0,
    'user-1' => 0,
    'user-2' => 0,
    'user-3' => 0,
    'views-backlinks-block' => 0,
    'views-c7ca0b2ddabaa89f4f33dec32ab7adf5' => 1,
    'views-lista_canales_busqueda-block_1' => 0,
    'views-lista_items-block_1' => 0,
    'views-og_area_trabajo-block_1' => 0,
    'views-og_canales-block_1' => 0,
    'views-og_files-block_1' => 0,
    'views-og_home_ultimasnoticias-block_1' => 1,
    'views-og_members_block-block' => 0,
    'views-og_members_block-block_1' => 0,
    'views-og_my-block_1' => 0,
  );

  $export['spaces_dashboard_blocks'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'spaces_dashboard_layouts';
  $strongarm->value = array(
    'default' => 1,
  );

  $export['spaces_dashboard_layouts'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'spaces_dashboard_regions';
  $strongarm->value = array(
    'default' => array(
      'left' => 1,
      'right' => 0,
      'menuuser' => 0,
      'menutop' => 0,
      'nombredelgrupo' => 1,
      'busquedagrupo' => 0,
      'contenttop' => 1,
      'contentbottom' => 1,
      'contentfooter' => 1,
    ),
  );

  $export['spaces_dashboard_regions'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_noticias_portada';
  $strongarm->value = '1';

  $export['upload_noticias_portada'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_rss_feed';
  $strongarm->value = '1';

  $export['upload_rss_feed'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_rss_feed_importador';
  $strongarm->value = '1';

  $export['upload_rss_feed_importador'] = $strongarm;
  return $export;
}
