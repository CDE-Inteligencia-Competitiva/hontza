<?php

/**
 * Implementation of hook_content_default_fields().
 */
function hz_fuentes_content_default_fields() {
  $fields = array();

  // Exported field: field_fuentedapper_actualizacion
  $fields['fuentedapper-field_fuentedapper_actualizacion'] = array(
    'field_name' => 'field_fuentedapper_actualizacion',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => '8',
      'parent' => 'group_fuentedapper_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Update',
      'weight' => '8',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_fuentedapper_args
  $fields['fuentedapper-field_fuentedapper_args'] = array(
    'field_name' => 'field_fuentedapper_args',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => '-1',
      'parent' => '',
      'label' => array(
        'format' => 'inline',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_args][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Parameters',
      'weight' => '1',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_args_desc
  $fields['fuentedapper-field_fuentedapper_args_desc'] = array(
    'field_name' => 'field_fuentedapper_args_desc',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => '31',
      'parent' => '',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_args_desc][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Description of Parameters',
      'weight' => '2',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_calidad
  $fields['fuentedapper-field_fuentedapper_calidad'] = array(
    'field_name' => 'field_fuentedapper_calidad',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => '6',
      'parent' => 'group_fuentedapper_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Information Quality',
      'weight' => '6',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_fuentedapper_desplegable
  $fields['fuentedapper-field_fuentedapper_desplegable'] = array(
    'field_name' => 'field_fuentedapper_desplegable',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => '5',
      'size' => 60,
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_desplegable][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Filtros (lista)',
      'weight' => '5',
      'description' => '',
      'type' => 'text_textarea',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_exhaustividad
  $fields['fuentedapper-field_fuentedapper_exhaustividad'] = array(
    'field_name' => 'field_fuentedapper_exhaustividad',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => '7',
      'parent' => 'group_fuentedapper_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      //gemini-2014
      //'label' => 'Exhaustividad',
      'label'=>'Coverage',
      'weight' => '7',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_fuentedapper_extraargs
  $fields['fuentedapper-field_fuentedapper_extraargs'] = array(
    'field_name' => 'field_fuentedapper_extraargs',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '0',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_extraargs][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'extraArgs',
      'weight' => 0,
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_filtrosdesc
  $fields['fuentedapper-field_fuentedapper_filtrosdesc'] = array(
    'field_name' => 'field_fuentedapper_filtrosdesc',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '100',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_filtrosdesc][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Filtros (descripción)',
      'weight' => '4',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_filtrosnombre
  $fields['fuentedapper-field_fuentedapper_filtrosnombre'] = array(
    'field_name' => 'field_fuentedapper_filtrosnombre',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_filtrosnombre][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Filtros (nombre)',
      'weight' => '3',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_fuentedapper_fuente
  $fields['fuentedapper-field_fuentedapper_fuente'] = array(
    'field_name' => 'field_fuentedapper_fuente',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'weight' => 0,
      'parent' => '',
      'label' => array(
        'format' => 'inline',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '1',
    'multiple' => '0',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_fuentedapper_fuente][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Fuente',
      'weight' => '-2',
      'description' => 'Para el Dapper es RSS',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_tematica_gupos
  $fields['fuentedapper-field_tematica_gupos'] = array(
    'field_name' => 'field_tematica_gupos',
    'type_name' => 'fuentedapper',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '0',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => 'privado|privado
publico|publico
',
    'allowed_values_php' => '',
    'widget' => array(
      'default_value' => array(
        '0' => array(
          'value' => 'publico',
        ),
      ),
      'default_value_php' => NULL,
      //gemini-2014  
      //'label' => 'Grupos',
      'label' => 'Groups',  
      'weight' => '7',
      'description' => 'Verifica la casilla, para que esta fuente este disponible en todos los grupos.',
      'type' => 'optionwidgets_onoff',
      'module' => 'optionwidgets',
    ),
  );

  // Exported field: field_supercanal_actualizacion
  $fields['supercanal-field_supercanal_actualizacion'] = array(
    'field_name' => 'field_supercanal_actualizacion',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => '8',
      'parent' => 'group_supercanal_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Update',
      'weight' => '8',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_supercanal_args
  $fields['supercanal-field_supercanal_args'] = array(
    'field_name' => 'field_supercanal_args',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => '-1',
      'parent' => '',
      'label' => array(
        'format' => 'inline',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_supercanal_args][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Parameters',
      'weight' => '-2',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_supercanal_args_desc
  $fields['supercanal-field_supercanal_args_desc'] = array(
    'field_name' => 'field_supercanal_args_desc',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => '31',
      'parent' => '',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '1',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_supercanal_args_desc][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Description of Parameters',
      'weight' => '-1',
      'description' => '',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_supercanal_calidad
  $fields['supercanal-field_supercanal_calidad'] = array(
    'field_name' => 'field_supercanal_calidad',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => '6',
      'parent' => 'group_supercanal_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Information Quality',
      'weight' => '6',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_supercanal_exhaustividad
  $fields['supercanal-field_supercanal_exhaustividad'] = array(
    'field_name' => 'field_supercanal_exhaustividad',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => '7',
      'parent' => 'group_supercanal_valoracion',
      'label' => array(
        'format' => 'above',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'fivestar',
    'required' => '1',
    'multiple' => '0',
    'module' => 'fivestar',
    'active' => '1',
    'stars' => '5',
    'dynamic_target' => '',
    'php_target' => '',
    'axis' => '',
    'widget' => array(
      'allow_clear' => 1,
      'default_value' => array(
        '0' => array(
          'rating' => '-',
          'target' => NULL,
          'axis' => '',
        ),
      ),
      'default_value_php' => NULL,
      //gemini-2014
      //'label' => 'Exhaustividad',
      'label'=>'Coverage',
      'weight' => '7',
      'description' => '',
      'type' => 'stars',
      'module' => 'fivestar',
    ),
  );

  // Exported field: field_supercanal_fuente
  $fields['supercanal-field_supercanal_fuente'] = array(
    'field_name' => 'field_supercanal_fuente',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'weight' => 0,
      'parent' => '',
      'label' => array(
        'format' => 'inline',
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '1',
    'multiple' => '0',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => '',
    'allowed_values_php' => '',
    'widget' => array(
      'rows' => 5,
      'size' => '60',
      'default_value' => array(
        '0' => array(
          'value' => '',
          '_error_element' => 'default_value_widget][field_supercanal_fuente][0][value',
        ),
      ),
      'default_value_php' => NULL,
      'label' => 'Fuente',
      'weight' => '-3',
      'description' => 'El ID de las pipes',
      'type' => 'text_textfield',
      'module' => 'text',
    ),
  );

  // Exported field: field_tematica_gupos
  $fields['supercanal-field_tematica_gupos'] = array(
    'field_name' => 'field_tematica_gupos',
    'type_name' => 'supercanal',
    'display_settings' => array(
      'label' => array(
        'format' => 'above',
        'exclude' => 0,
      ),
      'teaser' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'full' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '4' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '2' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      '3' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
      'token' => array(
        'format' => 'default',
        'exclude' => 0,
      ),
    ),
    'widget_active' => '1',
    'type' => 'text',
    'required' => '0',
    'multiple' => '0',
    'module' => 'text',
    'active' => '1',
    'text_processing' => '0',
    'max_length' => '',
    'allowed_values' => 'privado|privado
publico|publico
',
    'allowed_values_php' => '',
    'widget' => array(
      'default_value' => array(
        '0' => array(
          'value' => NULL,
        ),
      ),
      'default_value_php' => NULL,
      //gemini-2014  
      //'label' => 'Grupos',
      'label' => 'Groups',  
      'weight' => '1',
      'description' => 'Verifica la casilla, para que esta fuente este disponible en todos los grupos.',
      'type' => 'optionwidgets_onoff',
      'module' => 'optionwidgets',
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Update');
  t('Parameters');
  t('Information Quality');
  t('Description of Parameters');
  t('Coverage');
  t('Filters (description)');
  t('Filters (list)');
  t('Filters (name)');
  t('Source');
  t('Groups');
  t('extraArgs');

  return $fields;
}
