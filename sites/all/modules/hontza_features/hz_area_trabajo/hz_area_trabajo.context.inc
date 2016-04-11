<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_area_trabajo_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'area_trabajo';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'menu' => array(
      'values' => array(
        'area-trabajo' => 'area-trabajo',
      ),
    ),
    'node' => array(
      'values' => array(
        'wiki' => 'wiki',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'node/add/wiki*' => 'node/add/wiki*',
        'wiki/*' => 'wiki/*',
        'wiki' => 'wiki',
      ),
    ),
    'views' => array(
      'values' => array(
        'og_area_trabajo' => 'og_area_trabajo',
        'og_area_trabajo:page_1' => 'og_area_trabajo:page_1',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'menu-menu-anadir-pagina-libro' => array(
          'module' => 'menu',
          'delta' => 'menu-anadir-pagina-libro',
          'region' => 'left',
          'weight' => 0,
        ),
        'hontza-13' => array(
          'module' => 'hontza',
          'delta' => 13,
          'region' => 'left',
          'weight' => 1,
        ),
        'views-og_area_trabajo-block_1' => array(
          'module' => 'views',
          'delta' => 'og_area_trabajo-block_1',
          'region' => 'left',
          'weight' => 2,
        ),
        'menu-primary-links' => array(
          'module' => 'menu',
          'delta' => 'primary-links',
          'region' => 'menutop',
          'weight' => 0,
        ),
        'hontza-5' => array(
          'module' => 'hontza',
          'delta' => 5,
          'region' => 'nombredelgrupo',
          'weight' => 0,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  $export['area_trabajo'] = $context;
  return $export;
}
