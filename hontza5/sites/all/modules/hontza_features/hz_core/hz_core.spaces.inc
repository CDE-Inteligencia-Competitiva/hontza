<?php

/**
 * Implementation of hook_spaces_presets().
 */
function hz_core_spaces_presets() {
  $export = array();
  $spaces_presets = new stdClass;
  $spaces_presets->disabled = FALSE; /* Edit this to true to make a default spaces_presets disabled initially */
  $spaces_presets->api_version = 3;
  $spaces_presets->name = 'grupos_hontza';
  //intelsat-2015
  //$spaces_presets->title = 'Grupos ';
  $spaces_presets->title = 'Groups';
  $spaces_presets->description = '';
  $spaces_presets->space_type = 'og';
  $spaces_presets->value = array(
    'variable' => array(
      'spaces_og_selective' => '3',
      'spaces_og_register' => 0,
      'spaces_og_directory' => 0,
      'spaces_og_private' => 1,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  //t('Hontza Groups');
  t('Groups');
  
  $export['grupos_hontza'] = $spaces_presets;
  return $export;
}
