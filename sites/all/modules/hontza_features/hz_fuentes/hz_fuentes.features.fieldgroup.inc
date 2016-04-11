<?php

/**
 * Implementation of hook_fieldgroup_default_groups().
 */
function hz_fuentes_fieldgroup_default_groups() {
  $groups = array();

  // Exported group: group_fuentedapper_valoracion
  $groups['fuentedapper-group_fuentedapper_valoracion'] = array(
    'group_type' => 'standard',
    'type_name' => 'fuentedapper',
    'group_name' => 'group_fuentedapper_valoracion',
    'label' => 'Valoración de la fuente',
    'settings' => array(
      'form' => array(
        'style' => 'fieldset_collapsible',
        'description' => '',
      ),
      'display' => array(
        'description' => '',
        'teaser' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'full' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '4' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '2' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '3' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'token' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'label' => 'above',
      ),
    ),
    'weight' => '-1',
    'fields' => array(
      '0' => 'field_fuentedapper_calidad',
      '1' => 'field_fuentedapper_exhaustividad',
      '2' => 'field_fuentedapper_actualizacion',
    ),
  );

  // Exported group: group_supercanal_valoracion
  $groups['supercanal-group_supercanal_valoracion'] = array(
    'group_type' => 'standard',
    'type_name' => 'supercanal',
    'group_name' => 'group_supercanal_valoracion',
    'label' => 'Valoración de la fuente',
    'settings' => array(
      'form' => array(
        'style' => 'fieldset_collapsible',
        'description' => '',
      ),
      'display' => array(
        'description' => '',
        'teaser' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'full' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '4' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '2' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        '3' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'token' => array(
          'format' => 'fieldset',
          'exclude' => 0,
        ),
        'label' => 'above',
      ),
    ),
    'weight' => '0',
    'fields' => array(
      '0' => 'field_supercanal_calidad',
      '1' => 'field_supercanal_exhaustividad',
      '2' => 'field_supercanal_actualizacion',
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Rating of source');

  return $groups;
}
