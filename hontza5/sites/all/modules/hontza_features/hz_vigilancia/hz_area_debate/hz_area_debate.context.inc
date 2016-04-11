<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_area_debate_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'area_debate';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'node' => array(
      'values' => array(
        'debate' => 'debate',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'node/add/debate' => 'node/add/debate',
        'debate' => 'debate',
        'debate/*' => 'debate/*',
      ),
    ),
    'views' => array(
      'values' => array(
        'og_area_debate' => 'og_area_debate',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'menu-menu-area-debate' => array(
          'module' => 'menu',
          'delta' => 'menu-area-debate',
          'region' => 'left',
          'weight' => 0,
        ),
        'hontza-11' => array(
          'module' => 'hontza',
          'delta' => 11,
          'region' => 'left',
          'weight' => 1,
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

  $export['area_debate'] = $context;
  return $export;
}
