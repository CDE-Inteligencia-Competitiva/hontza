<?php

/**
 * Implementation of hook_context_default_contexts().
 */

function hz_fuentes_context_default_contexts() {
  $export = array();
  $context = new stdClass;
  $context->disabled = FALSE; /* Edit this to true to make a default context disabled initially */
  $context->api_version = 3;
  $context->name = 'fuentes';
  $context->description = 'Fuentes';
  $context->tag = '';
  $context->conditions = array(
    'node' => array(
      'values' => array(
        'fuentedapper' => 'fuentedapper',
        'supercanal' => 'supercanal',
      ),
      'options' => array(
        'node_form' => '1',
      ),
    ),
    'path' => array(
      'values' => array(
        'crear/fuente-supercanal*' => 'crear/fuente-supercanal*',
        'crear/fuente-dapper*' => 'crear/fuente-dapper*',
        'dapper' => 'dapper',
        'dapper/*' => 'dapper/*',
        'pipe' => 'pipe',
        'pipe/*' => 'pipe/*',
        'fuentes-pipes' => 'fuentes-pipes',
        'fuentes-pipes/*' => 'fuentes-pipes/*',
        'fuentes-dapper' => 'fuentes-dapper',
        'fuentes-dapper/*' => 'fuentes-dapper/*',
      ),
    ),
    'views' => array(
      'values' => array(
        'fuentes_referencia' => 'fuentes_referencia',
        'gestion_fuentes' => 'gestion_fuentes',
        'gestion_fuentes:page_1' => 'gestion_fuentes:page_1',
        'og_categorias_fuentes' => 'og_categorias_fuentes',
        'og_categorias_fuentes:page_1' => 'og_categorias_fuentes:page_1',
        'og_fuentes' => 'og_fuentes',
        'og_fuentes:page_1' => 'og_fuentes:page_1',
        'og_fuentes_actualizacion' => 'og_fuentes_actualizacion',
        'og_fuentes_actualizacion:page_1' => 'og_fuentes_actualizacion:page_1',
        'og_fuentes_actualizacion_dapper' => 'og_fuentes_actualizacion_dapper',
        'og_fuentes_actualizacion_dapper:page_1' => 'og_fuentes_actualizacion_dapper:page_1',
        'og_fuentes_calidad' => 'og_fuentes_calidad',
        'og_fuentes_calidad:page_1' => 'og_fuentes_calidad:page_1',
        'og_fuentes_calidad_dapper' => 'og_fuentes_calidad_dapper',
        'og_fuentes_calidad_dapper:page_1' => 'og_fuentes_calidad_dapper:page_1',
        'og_fuentes_dapper' => 'og_fuentes_dapper',
        'og_fuentes_dapper:page_1' => 'og_fuentes_dapper:page_1',
        'og_fuentes_exahustivo' => 'og_fuentes_exahustivo',
        'og_fuentes_exahustivo:page_1' => 'og_fuentes_exahustivo:page_1',
        'og_fuentes_exahustivo_dapper' => 'og_fuentes_exahustivo_dapper',
        'og_fuentes_exahustivo_dapper:page_1' => 'og_fuentes_exahustivo_dapper:page_1',
        'og_fuentes_pipes' => 'og_fuentes_pipes',
        'og_fuentes_pipes:page_1' => 'og_fuentes_pipes:page_1',
      ),
    ),
  );
  $context->reactions = array(
    'block' => array(
      'blocks' => array(
        'hontza-4' => array(
          'module' => 'hontza',
          'delta' => 4,
          'region' => 'left',
          'weight' => 0,
        ),
        'menu-menu-ver-fuentes' => array(
          'module' => 'menu',
          'delta' => 'menu-ver-fuentes',
          'region' => 'left',
          'weight' => 1,
        ),
        'hontza-28' => array(
          'module' => 'hontza',
          'delta' => 28,
          'region' => 'left',
          'weight' => 2,
        ),
        'views-og_categorias_fuentes-block_1' => array(
          'module' => 'views',
          'delta' => 'og_categorias_fuentes-block_1',
          'region' => 'left',
          'weight' => 3,
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

  // Translatables
  // Included for use with string extractors like potx.
  t('Sources');

  $export['fuentes'] = $context;
  return $export;
}
