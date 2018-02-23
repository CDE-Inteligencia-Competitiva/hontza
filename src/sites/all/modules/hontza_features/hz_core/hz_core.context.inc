<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_core_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'contendio_og';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'node' => array(
      'values' => array(
        'grupo' => 'grupo',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'dashboard' => 'dashboard',
        'dashboard*' => 'dashboard*',
        'estadisticas' => 'estadisticas',
        'estadisticas/*' => 'estadisticas/*',
        'stat/etiquetas' => 'stat/etiquetas',
        'stat/etiquetas/*' => 'stat/etiquetas/*',
        'stat/item' => 'stat/item',
        'stat/item/*' => 'stat/item/*',
        'analisis' => 'analisis',
        'usuarios' => 'usuarios',
        'usuarios/*' => 'usuarios/*',
      ),
    ),
    'views' => array(
      'values' => array(
        'contenidos_del_grupo' => 'contenidos_del_grupo',
        'servicios' => 'servicios',
        'servicios:page_1' => 'servicios:page_1',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
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

  $export['contendio_og'] = $context;
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'menu_general';
  $context->description = 'Context menu that contains the top';
  $context->tag = '';
  $context->conditions = array(
    'path' => array(
      'values' => array(
        '*' => '*',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-2' => array(
          'module' => 'hontza',
          'delta' => 2,
          'region' => 'menuuser',
          'weight' => 0,
        ),
        'hontza-3' => array(
          'module' => 'hontza',
          'delta' => 3,
          'region' => 'menuuser',
          'weight' => 1,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('Context menu that contains the top');

  $export['menu_general'] = $context;
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'mis_contenidos';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'views' => array(
      'values' => array(
        'mis_contenidos_v1' => 'mis_contenidos_v1',
        'mis_contenidos_v1:page_1' => 'mis_contenidos_v1:page_1',
        'mis_contenidos_v1:page_2' => 'mis_contenidos_v1:page_2',
        'mis_contenidos_v1:page_3' => 'mis_contenidos_v1:page_3',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-26' => array(
          'module' => 'hontza',
          'delta' => 26,
          'region' => 'menutop',
          'weight' => 0,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  $export['mis_contenidos'] = $context;
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'usuarios_og';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'views' => array(
      'values' => array(
        'og_usuarios' => 'og_usuarios',
        'og_usuarios:page_1' => 'og_usuarios:page_1',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-1' => array(
          'module' => 'hontza',
          'delta' => 1,
          'region' => 'left',
          'weight' => 0,
        ),
        'views-og_usuarios-block_1' => array(
          'module' => 'views',
          'delta' => 'og_usuarios-block_1',
          'region' => 'left',
          'weight' => 1,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  $export['usuarios_og'] = $context;
  return $export;
}
