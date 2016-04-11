<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_publica_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'home_publica';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        'node' => 'node',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'views-10d88f55258a6e919c60aa69b14a35d7' => array(
          'module' => 'views',
          'delta' => '10d88f55258a6e919c60aa69b14a35d7',
          'region' => 'right',
          'weight' => 0,
        ),
        'views-noticias_home_publica-block_1' => array(
          'module' => 'views',
          'delta' => 'noticias_home_publica-block_1',
          'region' => 'contenttop',
          'weight' => 0,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  $export['home_publica'] = $context;
  return $export;
}
