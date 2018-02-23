<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_gestion_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'gestion';
  $context->description = '';
  $context->tag = 'gestión panel de control';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'gestion' => 'gestion',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-7' => array(
          'module' => 'hontza',
          'delta' => 7,
          'region' => 'homecenter',
          'weight' => 0,
        ),
        'hontza-17' => array(
          'module' => 'hontza',
          'delta' => 17,
          'region' => 'homecenter',
          'weight' => 1,
        ),
        'hontza-18' => array(
          'module' => 'hontza',
          'delta' => 18,
          'region' => 'homecenter',
          'weight' => 2,
        ),
        'hontza-22' => array(
          'module' => 'hontza',
          'delta' => 22,
          'region' => 'homecenter',
          'weight' => 3,
        ),
        'hontza-21' => array(
          'module' => 'hontza',
          'delta' => 21,
          'region' => 'homeleft',
          'weight' => 0,
        ),
        'hontza-25' => array(
          'module' => 'hontza',
          'delta' => 25,
          'region' => 'homeleft',
          'weight' => 1,
        ),
        'hontza-16' => array(
          'module' => 'hontza',
          'delta' => 16,
          'region' => 'homeleft',
          'weight' => 2,
        ),
        'hontza-20' => array(
          'module' => 'hontza',
          'delta' => 20,
          'region' => 'homeleft',
          'weight' => 3,
        ),
        'hontza-15' => array(
          'module' => 'hontza',
          'delta' => 15,
          'region' => 'homeright',
          'weight' => 0,
        ),
        'hontza-19' => array(
          'module' => 'hontza',
          'delta' => 19,
          'region' => 'homeright',
          'weight' => 1,
        ),
        'hontza-24' => array(
          'module' => 'hontza',
          'delta' => 24,
          'region' => 'homeright',
          'weight' => 2,
        ),
        'hontza-23' => array(
          'module' => 'hontza',
          'delta' => 23,
          'region' => 'homeright',
          'weight' => 3,
        ),
        'hontza-9' => array(
          'module' => 'hontza',
          'delta' => 9,
          'region' => 'nombredelgrupo',
          'weight' => 0,
        ),
		//gemini
		'hontza-30' => array(
          'module' => 'hontza',
          'delta' => 30,
          'region' => 'homecenter',
          'weight' => 4,
        ),
		//gemini
		'hontza-31' => array(
          'module' => 'hontza',
          'delta' => 31,
          'region' => 'homeright',
          'weight' => 4,
        ),
		//
      ),
      'layout' => 'columns',
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('management control panel');

  $export['gestion'] = $context;
  return $export;
}
