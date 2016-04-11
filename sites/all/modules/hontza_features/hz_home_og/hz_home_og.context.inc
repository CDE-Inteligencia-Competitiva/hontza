<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_home_og_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'spaces_dashboard-custom-1';
  $context->description = 'Dashboard';
  $context->tag = 'Dashboard';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'dashboard' => 'dashboard',
        'dashboard/*' => 'dashboard/*',
      ),
    ),
    'user' => array(
      'values' => array(
        'Administrador' => 'Administrador',
        'authenticated user' => 'authenticated user',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'views-d640a64f46f55241f1a9ed29d2536ef2' => array(
          'module' => 'views',
          'delta' => 'd640a64f46f55241f1a9ed29d2536ef2',
          'region' => 'homecenter',
          'weight' => 0,
        ),
        'views-273bd4ba9d1b68191046f2e902e83370' => array(
          'module' => 'views',
          'delta' => '273bd4ba9d1b68191046f2e902e83370',
          'region' => 'homecenter',
          'weight' => 1,
        ),
        'views-og_home_faces-block_1' => array(
          'module' => 'views',
          'delta' => 'og_home_faces-block_1',
          'region' => 'homecenter',
          'weight' => 2,
        ),
        'views-og_canales_dash-block_1' => array(
          'module' => 'views',
          'delta' => 'og_canales_dash-block_1',
          'region' => 'homeleft',
          'weight' => 0,
        ),
        'views-ca8c74ef9b29fefa6b202cd5cbf47fbb' => array(
          'module' => 'views',
          'delta' => 'ca8c74ef9b29fefa6b202cd5cbf47fbb',
          'region' => 'homeleft',
          'weight' => 1,
        ),
        'views-og_home_areadebate-block_1' => array(
          'module' => 'views',
          'delta' => 'og_home_areadebate-block_1',
          'region' => 'homeright',
          'weight' => 0,
        ),
        'views-og_home_areadetrabajo-block_1' => array(
          'module' => 'views',
          'delta' => 'og_home_areadetrabajo-block_1',
          'region' => 'homeright',
          'weight' => 1,
        ),
      ),
      'layout' => 'columns',
    ),
  );
  $context->condition_mode = 1;

  // Translatables
  // Included for use with string extractors like potx.
  t('Dashboard');

  $export['spaces_dashboard-custom-1'] = $context;
  return $export;
}
