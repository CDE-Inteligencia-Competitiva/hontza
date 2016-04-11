<?php

/**
 * Implementation of hook_context_default_contexts().
 */
function hz_vigilancia_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'canal_busqueda';
  $context->description = 'To display the save searches block when we are in search view';
  $context->tag = 'search';
  $context->conditions = array(
    'node' => array(
      'values' => array(
        'canal_busqueda' => 'canal_busqueda',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'busqueda' => 'busqueda',
        'busqueda*' => 'busqueda*',
        'busqueda/*' => 'busqueda/*',
        'canales/busqueda' => 'canales/busqueda',
        'canales/busqueda*' => 'canales/busqueda*',
      ),
    ),
    'views' => array(
      'values' => array(
        'busqueda_en_canales' => 'busqueda_en_canales',
        'busqueda_en_canales:page_1' => 'busqueda_en_canales:page_1',
        'canal_busqueda' => 'canal_busqueda',
        'canal_busqueda:page_1' => 'canal_busqueda:page_1',
        'og_canales_busqueda' => 'og_canales_busqueda',
        'og_canales_busqueda:page_1' => 'og_canales_busqueda:page_1',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-0' => array(
          'module' => 'hontza',
          'delta' => 0,
          'region' => 'left',
          'weight' => 0,
        ),
        'views-og_canales-block_1' => array(
          'module' => 'views',
          'delta' => 'og_canales-block_1',
          'region' => 'left',
          'weight' => 1,
        ),
        'views-og_canales_busqueda-block_1' => array(
          'module' => 'views',
          'delta' => 'og_canales_busqueda-block_1',
          'region' => 'left',
          'weight' => 2,
        ),
        'menu-menu-busquedas' => array(
          'module' => 'menu',
          'delta' => 'menu-busquedas',
          'region' => 'left',
          'weight' => 3,
        ),
        'menu-menu-crear-noticia' => array(
          'module' => 'menu',
          'delta' => 'menu-crear-noticia',
          'region' => 'left',
          'weight' => 4,
        ),
        'menu-menu-canales' => array(
          'module' => 'menu',
          'delta' => 'menu-canales',
          'region' => 'left',
          'weight' => 5,
        ),
        'menu-menu-categorias-canales' => array(
          'module' => 'menu',
          'delta' => 'menu-categorias-canales',
          'region' => 'left',
          'weight' => 6,
        ),
        'block-9' => array(
          'module' => 'block',
          'delta' => 9,
          'region' => 'left',
          'weight' => 7,
        ),
        'menu-primary-links' => array(
          'module' => 'menu',
          'delta' => 'primary-links',
          'region' => 'menutop',
          'weight' => 0,
        ),
        'stored_views-save' => array(
          'module' => 'stored_views',
          'delta' => 'save',
          'region' => 'contenttop',
          'weight' => 0,
        ),
      ),
    ),
  );
  $context->condition_mode = 0;

  // Translatables
  // Included for use with string extractors like potx.
  t('To display the save searches block when we are in search view');
  t('search');

  $export['canal_busqueda'] = $context;
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'vigilancia';
  $context->description = '';
  $context->tag = '';
  $context->conditions = array(
    'node' => array(
      'values' => array(
        'canal_de_supercanal' => 'canal_de_supercanal',
        'canal_de_yql' => 'canal_de_yql',
        'item' => 'item',
        'noticia' => 'noticia',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'canales/*' => 'canales/*',
        'item/*' => 'item/*',
        'node/add/item*' => 'node/add/item*',
        'crear/canal-supercanal/*' => 'crear/canal-supercanal/*',
        'crear/canal-supercanal' => 'crear/canal-supercanal',
        'crear/canal-tematico' => 'crear/canal-tematico',
        'node/add/canal-tematico*' => 'node/add/canal-tematico*',
        'crear/canal-yql/*' => 'crear/canal-yql/*',
        'crear/canal-yql' => 'crear/canal-yql',
        'supercanal/*' => 'supercanal/*',
        'supercanal' => 'supercanal',
        'admin/content/taxonomy/' => 'admin/content/taxonomy/',
        'node/add/noticia' => 'node/add/noticia',
      ),
    ),
    'views' => array(
      'values' => array(
        'lista_items' => 'lista_items',
        'lista_items:page_1' => 'lista_items:page_1',
        'og_canal_aportaciones_usuarios' => 'og_canal_aportaciones_usuarios',
        'og_canal_aportaciones_usuarios:page_1' => 'og_canal_aportaciones_usuarios:page_1',
        'og_canales' => 'og_canales',
        'og_canales:page_1' => 'og_canales:page_1',
        'og_vigilancia_lo_mas_valorado' => 'og_vigilancia_lo_mas_valorado',
        'og_vigilancia_mascomentadas' => 'og_vigilancia_mascomentadas',
        'og_vigilancia_ultimas' => 'og_vigilancia_ultimas',
        'og_vigilancia_validados' => 'og_vigilancia_validados',
		//gemini
		'og_vigilancia_rechazados' => 'og_vigilancia_rechazados',
		'og_vigilancia_pendientes' => 'og_vigilancia_pendientes',
		//
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-0' => array(
          'module' => 'hontza',
          'delta' => 0,
          'region' => 'left',
          'weight' => 0,
        ),
        'views-og_canales-block_1' => array(
          'module' => 'views',
          'delta' => 'og_canales-block_1',
          'region' => 'left',
          'weight' => 1,
        ),
        'views-og_canales_busqueda-block_1' => array(
          'module' => 'views',
          'delta' => 'og_canales_busqueda-block_1',
          'region' => 'left',
          'weight' => 2,
        ),
        'hontza-14' => array(
          'module' => 'hontza',
          'delta' => 14,
          'region' => 'left',
          'weight' => 3,
        ),
        'menu-menu-crear-noticia' => array(
          'module' => 'menu',
          'delta' => 'menu-crear-noticia',
          'region' => 'left',
          'weight' => 4,
        ),
        'hontza-27' => array(
          'module' => 'hontza',
          'delta' => 27,
          'region' => 'left',
          'weight' => 5,
        ),
        'hontza-12' => array(
          'module' => 'hontza',
          'delta' => 12,
          'region' => 'left',
          'weight' => 6,
        ),
        'hontza-8' => array(
          'module' => 'hontza',
          'delta' => 8,
          'region' => 'left',
          'weight' => 7,
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
    'menu' => 'vigilancia',
  );
  $context->condition_mode = 0;

  $export['vigilancia'] = $context;
  return $export;
}
