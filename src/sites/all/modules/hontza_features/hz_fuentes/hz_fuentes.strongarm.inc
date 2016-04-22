<?php

/**
 * Implementation of hook_strongarm().
 */
function hz_fuentes_strongarm() {
  $export = array();
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_fuentedapper';
  $strongarm->value = 0;

  $export['comment_anonymous_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_supercanal';
  $strongarm->value = 0;

  $export['comment_anonymous_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_fuentedapper';
  $strongarm->value = '3';

  $export['comment_controls_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_supercanal';
  $strongarm->value = '3';

  $export['comment_controls_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_fuentedapper';
  $strongarm->value = '4';

  $export['comment_default_mode_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_supercanal';
  $strongarm->value = '4';

  $export['comment_default_mode_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_fuentedapper';
  $strongarm->value = '1';

  $export['comment_default_order_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_supercanal';
  $strongarm->value = '1';

  $export['comment_default_order_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_fuentedapper';
  $strongarm->value = '50';

  $export['comment_default_per_page_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_supercanal';
  $strongarm->value = '50';

  $export['comment_default_per_page_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_fuentedapper';
  $strongarm->value = '0';

  $export['comment_form_location_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_supercanal';
  $strongarm->value = '0';

  $export['comment_form_location_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_fuentedapper';
  $strongarm->value = '2';

  $export['comment_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_fuentedapper';
  $strongarm->value = '1';

  $export['comment_preview_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_supercanal';
  $strongarm->value = '1';

  $export['comment_preview_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_fuentedapper';
  $strongarm->value = '1';

  $export['comment_subject_field_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_supercanal';
  $strongarm->value = '1';

  $export['comment_subject_field_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_supercanal';
  $strongarm->value = '2';

  $export['comment_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_fuentedapper';
  $strongarm->value = '0';

  $export['comment_upload_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_images_fuentedapper';
  $strongarm->value = 'none';

  $export['comment_upload_images_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_images_supercanal';
  $strongarm->value = 'none';

  $export['comment_upload_images_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_upload_supercanal';
  $strongarm->value = 0;

  $export['comment_upload_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'community_tags_display_fuentedapper';
  $strongarm->value = '1';

  $export['community_tags_display_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'community_tags_display_supercanal';
  $strongarm->value = '1';

  $export['community_tags_display_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'content_extra_weights_fuentedapper';
  $strongarm->value = array(
    'title' => '-5',
    'revision_information' => '6',
    'comment_settings' => '10',
    'menu' => '8',
    'taxonomy' => '-3',
    'path' => '11',
    'og_nodeapi' => '9',
  );

  $export['content_extra_weights_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'content_extra_weights_supercanal';
  $strongarm->value = array(
    'title' => '-5',
    'body_field' => '-4',
    'revision_information' => '2',
    'comment_settings' => '6',
    'menu' => '4',
    'taxonomy' => '3',
    'path' => '7',
    'og_nodeapi' => '5',
  );

  $export['content_extra_weights_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'fivestar_labels_supercanal';
  $strongarm->value = array(
    0 => 'Cancel rating',
    1 => 'Poor',
    2 => 'Okay',
    3 => 'Good',
    4 => 'Great',
    5 => 'Awesome',
    6 => 'Give it @star/@count',
    7 => 'Give it @star/@count',
    8 => 'Give it @star/@count',
    9 => 'Give it @star/@count',
    10 => 'Give it @star/@count',
  );

  $export['fivestar_labels_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'form_build_id_fuentedapper';
  $strongarm->value = 'form-8bc255f439f578013efb77a291d0890d';

  $export['form_build_id_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'form_build_id_supercanal';
  $strongarm->value = 'form-cd3d3a22d2337a03480c1d3f16232df8';

  $export['form_build_id_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_fuentedapper';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_supercanal';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'og_content_type_usage_fuentedapper';
  $strongarm->value = 'group_post_standard';

  $export['og_content_type_usage_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'og_content_type_usage_supercanal';
  $strongarm->value = 'group_post_standard';

  $export['og_content_type_usage_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'og_max_groups_fuentedapper';
  $strongarm->value = '';

  $export['og_max_groups_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'og_max_groups_supercanal';
  $strongarm->value = '';

  $export['og_max_groups_supercanal'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_fuentedapper_pattern';
  $strongarm->value = 'dapper/[title-raw]';

  $export['pathauto_node_fuentedapper_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_supercanal_pattern';
  $strongarm->value = 'pipe/[title-raw]';

  $export['pathauto_node_supercanal_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_fuentedapper';
  $strongarm->value = '0';

  $export['upload_fuentedapper'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_supercanal';
  $strongarm->value = '0';

  $export['upload_supercanal'] = $strongarm;
  return $export;
}
