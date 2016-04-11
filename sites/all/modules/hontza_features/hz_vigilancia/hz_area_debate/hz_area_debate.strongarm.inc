<?php

/**
 * Implementation of hook_strongarm().
 */
function hz_area_debate_strongarm() {
  $export = array();
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_anonymous_debate';
  $strongarm->value = 0;

  $export['comment_anonymous_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_controls_debate';
  $strongarm->value = '3';

  $export['comment_controls_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_debate';
  $strongarm->value = '2';

  $export['comment_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_mode_debate';
  $strongarm->value = '4';

  $export['comment_default_mode_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_order_debate';
  $strongarm->value = '1';

  $export['comment_default_order_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_default_per_page_debate';
  $strongarm->value = '50';

  $export['comment_default_per_page_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_form_location_debate';
  $strongarm->value = '0';

  $export['comment_form_location_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_preview_debate';
  $strongarm->value = '0';

  $export['comment_preview_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_subject_field_debate';
  $strongarm->value = '1';

  $export['comment_subject_field_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'content_extra_weights_debate';
  $strongarm->value = array(
    'title' => '-5',
    'body_field' => '-3',
    'revision_information' => '1',
    'comment_settings' => '3',
    'menu' => '-4',
    'book' => '0',
    'path' => '4',
    'attachments' => '2',
    'og_nodeapi' => '-1',
  );

  $export['content_extra_weights_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_debate';
  $strongarm->value = array(
    0 => 'status',
    1 => 'promote',
  );

  $export['node_options_debate'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'pathauto_node_debate_pattern';
  $strongarm->value = 'debate/[title-raw]';

  $export['pathauto_node_debate_pattern'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'quant_height';
  $strongarm->value = '200';

  $export['quant_height'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'quant_palette';
  $strongarm->value = array(
    0 => 'FF8000',
    1 => 'FFC080',
    2 => 'FFDFBF',
    3 => 'FFC080',
    4 => 'FFCC00',
    5 => 'FFE500',
    6 => 'FFF9BF',
    7 => '78C0E9',
    8 => '179CE8',
    9 => '30769E',
    10 => 'C8E9FC',
    11 => 'ECF8FF',
    12 => '00CCFF',
    13 => '4086AA',
    14 => '91C3DC',
    15 => '87907D',
    16 => 'AAB6A2',
    17 => '555555',
    18 => '666666',
    19 => '21B6A8',
    20 => '177F75',
    21 => 'B6212D',
    22 => '7F171F',
    23 => 'B67721',
    24 => '7F5417',
  );

  $export['quant_palette'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'quant_visible';
  $strongarm->value = array(
    'group_creation1' => 'group_creation1',
    'content_creation1' => 'content_creation1',
    'comment_creation1' => 'comment_creation1',
    'node_creation_by_type1' => 'node_creation_by_type1',
    'user_creation1' => 'user_creation1',
    'group_creation' => 0,
    'group_joins' => 0,
    'content_creation' => 0,
    'comment_creation' => 0,
    'node_creation_by_type' => 0,
    'node_creation_aggregate_by_type' => 0,
    'user_creation' => 0,
  );

  $export['quant_visible'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'quant_width';
  $strongarm->value = '500';

  $export['quant_width'] = $strongarm;
  $strongarm = new stdClass;
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'upload_debate';
  $strongarm->value = '0';

  $export['upload_debate'] = $strongarm;
  return $export;
}
