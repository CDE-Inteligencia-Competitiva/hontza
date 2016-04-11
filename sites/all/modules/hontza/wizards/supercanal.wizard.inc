<?php

/**
 * Formulario multistep para que añadan una fuente parametrizable (algunos le llaman supercanal)
 */
function supercanal_wizard(){
  //intesal-2015
  hontza_solr_search_is_usuario_lector_access_denied();  
  //  
  $step = arg(2);
  ctools_include('wizard');
  ctools_include('object-cache');
  
  $form_info = array(
    'id' => 'supercanal_wizard_form',
    'path' => "crear/fuente-supercanal/%step",
    'show trail' => TRUE,
    'show back' => TRUE,
    'show cancel' => true,
    'show return' =>false,
    'next text' => t('Next step'),
    'next callback' =>  'supercanal_wizard_next',
    'finish callback' => 'supercanal_wizard_finish',
    'cancel callback' => 'supercanal_wizard_cancel',
    'order' => array(
      'supercanal_wizard_nombre' => t('Step 1: Name and ID of Pipe'),
      'supercanal_wizard_num_args' => t('Step 2: Number of Parameters'),
      'supercanal_wizard_args' => t('Step 3: Name of Parameters'),
    ),
    'forms' => array(
      'supercanal_wizard_nombre' => array(
        'form id' => 'supercanal_wizard_nombre_form',
      ),
      'supercanal_wizard_num_args' => array(
        'form id' => 'supercanal_wizard_num_args_form',
      ),
      'supercanal_wizard_args' => array(
        'form id' => 'supercanal_wizard_args_form',
      ),
    ),
  );
  $form_state = array(
    'cache name' => NULL,
  );
  // no matter the step, you will load your values from the callback page
  $fuente = ctools_object_cache_get('supercanal_obj', NULL);
  if (!$fuente) {
    global $user;
    // set form to first step -- we have no data
    $step = current(array_keys($form_info['order']));
    $fuente = new stdClass();
    $fuente->type = 'supercanal';
    $fuente->uid = $user->uid;
    $fuente->status = 1;
    // ** set the storage object so its ready for whatever comes next
    ctools_object_cache_set('supercanal_obj', $form_state['cache name'], $fuente);
  }
  //intelsat-2015-kimonolabs
  if(hontza_canal_rss_is_kimonolabs_activado()){
    kimonolabs_set_fuente_is_kimonolabs($fuente);
    kimonolabs_set_supercanal_wizard_title_labels($form_info,$fuente);
  }
  //    
  //THIS IS WHERE WILL STORE ALL FORM DATA
  $form_state['supercanal_obj'] = $fuente;
    
  $output = ctools_wizard_multistep_form($form_info, $step, $form_state);
  return $output;
}




function supercanal_wizard_next(&$form_state) {
  $fuente = $form_state['supercanal_obj'];
  $cache = ctools_object_cache_set('supercanal_obj', $form_state['cache name'], $fuente);
}

function supercanal_wizard_finish(&$form_state){    
  node_save($form_state['supercanal_obj']);
  //$node = node_load(array('title' => $form_state['supercanal_obj']->title));
  $node = $form_state['supercanal_obj'];
  //drupal_set_message(t('Se ha creado la fuente !name', array('!name' => l($node->title, 'node/'.$node->nid))));
  ctools_object_cache_clear('supercanal_obj', $form_state['cache name']);
  //$form_state['redirect'] = 'node/'.$node->nid;
  //gemini
  //$form_state['redirect'] = 'fuentes';
  $form_state['redirect'] = 'fuentes-pipes/todas';
  //
}
function supercanal_wizard_cancel(&$form_state){
  ctools_object_cache_clear('supercanal_obj', $form_state['cache name']);
  //$form_state['redirect'] = 'node';
  //gemini
  //drupal_goto('fuentes/todas');
  drupal_goto('fuentes-pipes/todas');
  //
}


/**
 * Formulario para el primer paso. Aquí introducimos los campos de título,
 * categoría y valoración
 */
function supercanal_wizard_nombre_form(&$form, &$form_state){
  $fuente = $form_state['supercanal_obj'];
  $form['titulo'] = array(
    '#title' => t('Name of Pipe source'),
    '#type' => 'textfield',
    '#required' => 1,
  );
  
  //$form['grupos'] = array(
  //  '#title' => t('Groups'),
  //  '#type' => 'checkboxes',
  //  '#required' => TRUE,
  //  '#options' => get_grupos(),
  //);
  
  //intelsat-2015-kimonolabs
  if(hontza_canal_rss_is_kimonolabs_activado()){
      kimonolabs_supercanal_wizard_nombre_form_add_kimonolabs_api_key_form_field($fuente,$form,$form_state);  
  }
  //
    
  $form['pipe_id'] = array(
    '#title' => t('Pipe ID'),
    '#type' => 'textfield',
    '#size' => 30,
    '#required' => TRUE,
  );
  
  //intelsat-2015-kimonolabs
  if(hontza_canal_rss_is_kimonolabs_activado()){
      kimonolabs_supercanal_wizard_nombre_form_set_kimonolabs_titles($fuente,$form,$form_state);
  }
  //
  
  /*$padres = taxonomy_get_tree(1, 0, -1, 1);
  $taxo = array();
  foreach ($padres as $padre){
    $taxo[$padre->tid] = $padre->name;
    $hijos = taxonomy_get_children($padre->tid);
    if (!empty($hijos)){
      foreach ($hijos as $hijo){
        $taxo[$hijo->tid] = '--'.$hijo->name;
      }
    }
  }*/
  /*$taxo=hontza_define_clasificaciones_fuente();
 
  //gemini
  //
  //simulatzeko
  //$taxo=array();
  if(empty($taxo)){
    unset($form['buttons']['next']);  
  	form_set_error('asignar_clasif', t('There are no classifications'));
  }
  //if(!my_hay_categorias()){
  //unset($form['buttons']['next']);
  //form_set_error('categorias', t('There are no categories. You have to create at least one category before continuing.'));
  //}
  // 
  $form['taxonomia'] = array(
    '#title' => t('Assign one or more classifications'),
    '#type' => 'checkboxes',
    '#multiple' => TRUE,
    '#required' => TRUE,
    '#options' => $taxo,
  );*/
  //intelsat-2015
  hontza_solr_search_add_source_type_form_field($form);
  
  $form['valoracion'] = array(
    '#title' => t('Rating of source'),
    '#type' => 'fieldset',
  );
  $form['valoracion']['calidad'] = array(
    '#title' => t('Information Quality'),
    '#type' => 'select',
    '#required' => TRUE,
    '#options' => get_valoracion_options(),
    '#default_value' => 2,
  );
  $form['valoracion']['exhaustividad'] = array(
    '#title' => t('Coverage'),
    '#type' => 'select',
    '#required' => TRUE,
    '#options' => get_valoracion_options(),
    '#default_value' => 2,
  );
  $form['valoracion']['actualizacion'] = array(
    '#title' => t('Update'),
    '#type' => 'select',
    '#required' => TRUE,
    '#options' => get_valoracion_options(),
    '#default_value' => 2,
  );
  /*
  $form['publica'] = array(
    '#title' => t('Fuente p&uacute;blica'),
    '#type' => 'fieldset',
  );
  $form['publica']['check']= array(
    '#title' => t('Hacer p&uacute;blica a todos los grupos esta fuente Dapper'),
    '#type' => 'checkbox',
  );
  */
  
  //gemini-2013
  $form['apply_opencalais']= array(
    '#title' => t('Apply OpenCalais'),
    '#type' => 'checkbox',
  );
  $form['apply_alchemy']= array(
    '#title' => t('Apply Alchemy'),
    '#type' => 'checkbox',
  );
  $form['apply_fulltextrss']= array(
    '#title' => t('Apply FulltextRSSFeed'),
    '#type' => 'checkbox',
  );
  //
  
  $form_state['no buttons'] = TRUE;
  
}
//intelsat-2015
function supercanal_wizard_nombre_form_validate(&$form, &$form_state){ 
  hontza_solr_search_taxonomia_validate($form,$form_state);
}  
function supercanal_wizard_nombre_form_submit(&$form, &$form_state){
  $fuente = $form_state['supercanal_obj'];
  $fuente->title = $form_state['values']['titulo'];
  $vid = db_result(db_query("SELECT vid FROM {term_data} WHERE tid = %d", key($form_state['values']['taxonomia'])));
  //intelsat-2015
  //foreach($form_state['values']['taxonomia'] as $tid){
  foreach($form_state['values']['taxonomia'] as $tid=>$value){
      //intelsat-2015
      if(!empty($value)){
        $fuente->taxonomy[$vid][$tid] = $tid;
      }  
  }
  //$fuente->field_pipe_args[0]['value'] = array('value' => 'http://pipes.yahoo.com/pipes/pipe.run?_id='.$form_state['values']['pipe_id'].'&_render=rss');
  $fuente->field_supercanal_fuente[0] = array('value' => $form_state['values']['pipe_id']);
  
  $fuente->field_supercanal_calidad[0]['rating'] = ($form_state['values']['calidad'] + 1) * 20;
  $fuente->field_supercanal_calidad[0]['target'] = 0;
  
  $fuente->field_supercanal_exhaustividad[0]['rating'] = ($form_state['values']['exhaustividad'] + 1) * 20;
  $fuente->field_supercanal_exhaustividad[0]['target'] = 0;
  
  $fuente->field_supercanal_actualizacion[0]['rating'] = ($form_state['values']['actualizacion'] + 1) * 20;
  $fuente->field_supercanal_actualizacion[0]['target'] = 0;
  
  //gemini-2013
  //if($form_state['values']['check']==1){
  if(isset($form_state['values']['check']) && !empty($form_state['values']['check']) && $form_state['values']['check']==1){
    $fuente->field_tematica_gupos[0]['value'] = 'publico';
  }
  else{
    $fuente->field_tematica_gupos[0]['value'] = 'privado';
  }
  
  //gemini-2013
  $fuente->field_apply_source_opencalais[0]['value']=0; 
  if(isset($form_state['values']['apply_opencalais']) && !empty($form_state['values']['apply_opencalais'])){
    $fuente->field_apply_source_opencalais[0]['value']=1;  
  }
  $fuente->field_apply_source_alchemy[0]['value']=0;
  if(isset($form_state['values']['apply_alchemy']) && !empty($form_state['values']['apply_alchemy'])){
    $fuente->field_apply_source_alchemy[0]['value']=1;  
  }
  $fuente->field_apply_full_text_rss[0]['value']=0;
  if(isset($form_state['values']['apply_fulltextrss']) && !empty($form_state['values']['apply_fulltextrss'])){
    $fuente->field_apply_full_text_rss[0]['value']=1;  
  }
  //intelsat-2015
  if(hontza_canal_rss_is_kimonolabs_activado()){
    kimonolabs_supercanal_wizard_nombre_form_submit_add_fuente_kimonolabs_api_key($fuente,$form,$form_state);
  }
  $form_state['supercanal_obj'] = $fuente;
}
  

/**
 * Formulario para que introduzcan el número de argumentos que tendrá la fuente
 */
function supercanal_wizard_num_args_form(&$form, &$form_state){
  $form['num_args'] = array(
  '#title' => t('Number of Parameters'),
  '#type' => 'textfield',
  '#required' => 1,
  );
  $form_state['no buttons'] = TRUE; 
}
function fuente_parametrizable_num_args_form_validate(&$form, &$form_state){
  if (!is_numeric($form_state['values']['num_args'])){
    form_set_error('num_args', t('The value of this field must be numeric.'));
  }
}

function supercanal_wizard_num_args_form_submit(&$form, &$form_state){
  $form_state['supercanal_obj']->num_args = $form_state['values']['num_args'];
}


/**
 *Formulario para que introduzcan los nombres de los argumentos.
 **/
function supercanal_wizard_args_form(&$form, &$form_state){
  //dprint_r($form_state);
  $num_args = $form_state['supercanal_obj']->num_args;
  for($i = 0; $i < $num_args; $i++){
    $arg = 'args'.$i;
    $form[$arg] = array(
    '#title' => t('Name of parameter @num', array('@num' => $i + 1)),
    '#type' => 'fieldset',
    );
    $form[$arg]['arg_name'.$i] = array(
      //intelsat-2015
      //'#title' => t("Name of parameter"),
      '#title' => t("Name"),  
      '#type' => 'textfield',
      '#size' => 30,
      '#required' => TRUE,
    );
    $form[$arg]['arg_desc'.$i] = array(
      //intelsat-2015  
      //'#title' => t("Description of parameters"),
      '#title' => t('Prompt'),  
      '#type' => 'textfield',
      '#size' => 30,
    );
  }
  $form_state['no buttons'] = TRUE; 
}

function supercanal_wizard_args_form_submit(&$form, &$form_state){
  $num_args = $form_state['supercanal_obj']->num_args;
  if ($num_args > 0){
    for($i = 0; $i < $num_args; $i++){
      $form_state['supercanal_obj']->field_supercanal_args[]['value'] = $form_state['values']['arg_name'.$i];
      $form_state['supercanal_obj']->field_supercanal_args_desc[]['value'] = $form_state['values']['arg_desc'.$i].' ';
    }
  }
}