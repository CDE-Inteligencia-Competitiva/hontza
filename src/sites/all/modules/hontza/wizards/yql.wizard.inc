<?php

/**
 * Llamada para crear el formulario multistep
 */
function yql_wizard(){
  //intesal-2015
  hontza_solr_search_is_usuario_lector_access_denied();  
  //   
  $step = arg(2);
  ctools_include('wizard');
  ctools_include('object-cache');
  $form_info = array(
    'id' => 'yql_basic',
    'path' => 'crear/canal-yql/%step',
    'show trail' => TRUE,
    'show back' => TRUE,
    'show cancel' => true,
    'show return' =>false,
    'next text' => t('Next step'),
    'next callback' =>  'yql_next',
    'finish callback' => 'yql_finish',
    'cancel callback' => 'yql_cancel',
    'order' => array(
      'yql_canal' => t('Channel data'),
      'agregar_fuente' => t('Add source'),
      'filtros' => t('Filters'),
      'previsualizar' => t("Preview")
    ),
    'forms' => array(
      'yql_canal' => array(
        'form id' => 'yql_canal_form',
      ),
      'agregar_fuente' => array(
        'form id' => 'agregar_fuente_form',
      ),
      'filtros' => array(
        'form id' => 'filtros_form'
      ),
      'previsualizar' => array(
        'form id' => 'previsualizar_form'
      ),
    ),
  );
  $form_state = array(
    'cache name' => NULL,
  );
  $canal = ctools_object_cache_get('yql_obj', NULL);
  if (!$canal){
    global $user;
    $canal = new stdClass();
    $canal->type = 'canal_de_yql';
    $canal->uid = $user->uid;
    $canal->status = 1;
    $canal->field_fuente_canal[0]['value']='YQL';	
    ctools_object_cache_set('yql_obj', NULL, $canal);
    $step = current(array_keys($form_info['order']));
  }
 
  //$form_state['yql_obj'] = $canal;
  
  //gemini
  if(my_is_opml($form_state)){  	
      $canal->field_fuente_canal[0]['value']='OPML';
  }else if(my_is_simple($form_state)){
      //drupal_set_title(t('Create a channel from an RSS'));
      drupal_set_title(t('Import RSS'));
      $canal->field_fuente_canal[0]['value']='RSS';
      $form_info['show trail']=FALSE;
      $form_info['next text']=t('Finish');
  }
  $row_html=get_import_html_row($_REQUEST['my_id']);
  if(isset($row_html->id)){
      $canal->field_url_html[0]['value']=$row_html->my_url;
      $canal->field_fuentehtml_nid[0]['value']=$row_html->fuentehtml_nid;
      $canal->my_id=$_REQUEST['my_id'];
      $canal->field_apply_open_calais_yql[0]['value']=$row_html->apply_open_calais;
      $canal->field_apply_alchemy_yql[0]['value']=$row_html->apply_alchemy;
      //ctools_object_cache_set('yql_obj', NULL, $canal);      
      $_SESSION['yql_my_id']=$_REQUEST['my_id'];

  }
  //gemini-2014  
  $canal->field_canal_active_refresh[0]['value']=1;
  //intelsat-2016
  yql_wizard_add_mail_from_field($canal);
  yql_wizard_add_field_is_imported($canal);
  $form_state['yql_obj'] = $canal;
  //
  $output = ctools_wizard_multistep_form($form_info, $step, $form_state);
  //intelsat-2014
  if(hontza_social_is_activado()){
      $output.=social_learning_collections_get_import_table_html();
  }
  //
  return $output;
}

/**
 * Datos generales del canal
 */
function yql_canal_form(&$form, &$form_state){
  //gemini-2013
  if(!yql_hound_access()){
      drupal_access_denied();
      exit();
  }
  //intelsat-2016
  yql_wizard_all_hide($form,$form_state);
  //gemini  
  set_title_yql_forms($form_state);
  $row_html=get_import_html_row($_REQUEST['my_id']);
  $nombre_canal='';
  if(isset($row_html->id)){
    $nombre_canal=$row_html->nombre_canal;
    //echo print_r($form_state,1);
    $form['my_id']=array(
        '#type' => 'hidden',
        '#value' => $row_html->id,
    );
    $form['my_url']=array(
        '#type' => 'hidden',
        '#value' => $row_html->my_url,
    );
  }  
  //intelsat-2016
  $nombre_canal=red_copiar_get_nombre_canal_importar_rss($form_state,$nombre_canal);
  //
  $form['titulo'] = array(
    '#type' => 'textfield',
    '#title' => t('Name of Channel'),
    '#required' => TRUE,
    //gemini
    '#default_value' => $nombre_canal,
    //
  );
  //gemini-2013
  $form['is_validacion_automatica']=array('#type'=>'checkbox','#title'=>'<b>'.t('Automatic validation').'</b>');
  //intelsat-2015
  if(my_is_simple($form_state)){
    yql_wizard_add_mail_channel_form_field($form);
  }
  //
  
  
  //gemini
  //if(hontza_canal_rss_is_visualizador_activado()){
  if(red_canal_is_canal_opencalais_activado()){
      $form['is_canal_opencalais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
      //intelsat-2016
      red_canal_add_is_canal_opencalais_description($form,'is_canal_opencalais');
  }
  
  //gemini
  /*if(!isset($form_state['yql_obj']->field_apply_open_calais_yql[0]['value'])){
        $form['apply_open_calais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
  }*/
  //if(!isset($form_state['yql_obj']->field_apply_alchemy_yql[0]['value'])){
        $form['apply_alchemy']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply Alchemy').'</b>');
        red_canal_add_apply_alchemy_description($form,'apply_alchemy');
  //}
  
  //intelsat-2015
  red_despacho_vigilancia_add_import_canal_duplicate_news_form_field($form);
  
  $form['numero_rss'] = array(
    '#title' => t('Number of RSS.'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#description' => t('Number of RSS to filter'),
    '#default_value' => 1,
    '#size' => 10,
  );
  
  //gemini-2013
  if(yql_is_crear_hound()){
    $form['numero_rss']['#prefix']='<div style="display:none">';
    $form['numero_rss']['#suffix']='</div>';
  }
  
  //gemini
  $is_required=TRUE;
  if(my_is_simple($form_state) || my_is_opml($form_state)){
  //if(my_is_simple($form_state) || my_is_opml($form_state) || !red_copiar_is_yql_wizard_responsable_uid_required($form_state)){	
    $is_required=FALSE;
  }
  //
  $form['responsable_uid']= array(
	  '#title' => t('Main Validator'),
	  '#type' => 'textfield',
	  '#required' => $is_required,	 
	  //'#maxlength' => 60,
          '#maxlength' => 255,
	  //'#autocomplete_path' => 'user/autocomplete',
	  '#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  '#default_value' => '',
	  //'#weight' => -1,
	);
  $form['responsable_uid2']= array(
	  '#title' => t('Second Validator'),
	  '#type' => 'textfield',
	  '#required' => FALSE,	 
	  //'#maxlength' => 60,
          '#maxlength' => 255,
	  //'#autocomplete_path' => 'user/autocomplete',
	  '#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  '#default_value' => '',
	  //'#weight' => -1,
	);
  // 
  //gemini
  prepare_simple_or_opml($form,$form_state);
  //	
  // 
  /*if(og_get_group_context()){
    //Obtener el ID del grupo en el que se esta
    $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",og_get_group_context()->nid));
    
    //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
    $categorias=taxonomy_get_tree($id_categoria);
	
	//geminiif(!$is_canal_correo){
      echo print_r($fuente,1);
      exit();
  }
  
	
	//simulatzeko
	//$categorias=array();
	//if(empty($categorias)){
	if(!my_hay_categorias(1,$categorias)){	 
	 //unset($form['buttons']['next']);
  	 //form_set_error('categorias', t('There are no categories'));
    }
	    	
    //$salida = array();
    $form['cat'] = array(
      '#type' => 'fieldset',
      '#title' => t('Categories'),
      //'#description' => '<u>'.t('Please select at least ONE category').'</u>',
	  //gemini
	  '#attributes'=>array('id'=>'my_categorias_yql'),
	  //
    );
    foreach ($categorias as $id => $contenido){
      //gemini-2014
      $pro=profundidad($contenido->tid);
      //$pro=variable_get('profundidad_valor',0);
      $form['cat'][$contenido->tid] = array(
        //gemini-2014
        //AVISO::::se ha quitado required  
        //'#required' => TRUE,
        '#type' => 'checkbox',
        '#prefix' => '<div class=taxo'.$pro.'>',
        '#suffix' => '</div>', 
        '#title' => $contenido->name,
      );
    }
  }*/
  //intelsat-2015
  if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
    //if(!my_is_simple($form_state)){  
    hontza_solr_search_add_source_type_form_field($form,1,0,1,'',$form_state);
    //}    
  }  
  $id_categoria=0;
  hontza_solr_search_add_categorias_form_field($form,$id_categoria,'',$form_state);
  //
  //intelsat-2015
  //if(hontza_grupos_is_activo_pestana('estrategia')){
  if(estrategia_inc_is_show_reto_al_que_responde_html()){
    //gemini-2013
    $form['reto_al_que_responde_id']=array(
      //'#title' => t('Associated Challenge'),
      '#value'=>get_reto_al_que_responde_html(),
   );
  }
  $form_state['no buttons'] = TRUE;
  //intelsat-2016
  yql_wizard_add_compartir_documentos_importar_rss_js($form_state);
}

function yql_canal_form_validate(&$form, &$form_state){
  //gemini-2013  
  $my_grupo=og_get_group_context();
  if(!isset($my_grupo->nid) || empty($my_grupo->nid)){
      form_error($form['numero_rss'],t('You have to enter in a group'));
      return '';
  }
  //      
  if(!is_numeric($form_state['values']['numero_rss'])){
    form_error($form['numero_rss'],t('The value must be numeric'));
  }
  if($form_state['values']['numero_rss']<1 or $form_state['values']['numero_rss']>100){
    form_error($form['numero_rss'],t('The value must be between 1-100'));
  }
  //gemini	  
  if(!my_is_simple($form_state) && !my_is_opml($form_state)){
      //if(red_copiar_is_yql_wizard_responsable_uid_required($form_state)){
        my_validate_responsable($form,$form_state);
      //}  
  }
  //  
  //Validar categorias
  $categoria=0;
  foreach($form_state['values'] as $tid => $cont){
    //gemini
    //if($cont!=0 and $tid!='numero_rss'){
    if($cont!=0 && is_numeric($tid)){
      $categoria=1;
    }
  }
  //gemini-2014
  //AVISO::::se ha quitado required 
  /*if($categoria==0){
    form_error($form['cat'],t('You have to select at least one category'));
  }*/
  //gemini-2013
  hontza_canal_validate_reto_al_que_responde($form,$form_state);
  //intelsat-2015
  hontza_solr_search_taxonomia_validate($form,$form_state);
}

function yql_canal_form_submit(&$form, &$form_state){
  $fuente = $form_state['yql_obj'];  
  $fuente->title = $form_state['values']['titulo'];
  $fuente->numero_rss = $form_state['values']['numero_rss'];
  //gemini
  //$fuente->field_responsable_uid=my_get_responsable_uid($form_state['values']['responsable_uid']);  
  $fuente->field_responsable_uid[0]['uid']=my_get_responsable_uid($form_state['values']['responsable_uid']);
  $fuente->field_responsable_uid2[0]['uid']=my_get_responsable_uid($form_state['values']['responsable_uid2']);
  //echo print_r($fuente->field_responsable_uid,1);exit();
  // 
  //Almacenar taxonomias seleccionadas
  foreach($form_state['values'] as $tid => $cont){
    if($cont!=0 && $tid!='numero_rss' && $tid!='simple' && $tid!='my_id' && is_numeric($tid)){
      $fuente->taxonomy[$tid] = taxonomy_get_term($tid);
    }
  }
  //intelsat-2015
  if(yql_wizard_is_empty_categorias_tematicas($fuente)){
      yql_wizard_add_categoria_indefinida($fuente);
  }
  //
   //gemini
   $fuente->is_simple=0;
  if(my_is_simple($form_state)){
  	$fuente->is_simple=1;
  }
   $fuente->is_opml=0;
  if(my_is_opml($form_state)){
  	$fuente->is_opml=1;
  }
  //  
  //gemini  
  if(my_is_opml($form_state)){
  	$my_info=my_upload_opml_xml();
	if(!empty($my_info)){
		$fuente->field_my_opml[0]['value']=$my_info['name'];
		$fuente->xml_list=$my_info['xml_list'];
		$fuente->numero_rss=count($fuente->xml_list);
	}  
  }
  if(!isset($fuente->field_apply_open_calais_yql[0]['value'])){
      $fuente->field_apply_open_calais_yql=array();
      $fuente->field_apply_open_calais_yql[0]['value']=0;
      if(isset($form_state['values']['apply_open_calais']) && !empty($form_state['values']['apply_open_calais'])){
          $fuente->field_apply_open_calais_yql[0]['value']=1;
      }
  }

  if(!isset($fuente->field_apply_alchemy_yql[0]['value'])){
      $fuente->field_apply_alchemy_yql=array();
      $fuente->field_apply_alchemy_yql[0]['value']=0;
      if(isset($form_state['values']['apply_alchemy']) && !empty($form_state['values']['apply_alchemy'])){
          $fuente->field_apply_alchemy_yql[0]['value']=1;
      }
  }
  
  if(!isset($fuente->field_is_validacion_automatica[0]['value'])){
    $fuente->field_is_validacion_automatica=array();
    $fuente->field_is_validacion_automatica[0]['value']=0;
    if(isset($form_state['values']['is_validacion_automatica']) && !empty($form_state['values']['is_validacion_automatica'])){
        $fuente->field_is_validacion_automatica[0]['value']=1;
    }
  }
  
  //intelsat-2015
  $is_canal_correo=0;
  if(is_super_admin()){
    if(!isset($fuente->field_is_canal_correo[0]['value'])){
      if(isset($form_state['values']['is_canal_correo']) && !empty($form_state['values']['is_canal_correo'])){
          $fuente->field_is_canal_correo=array();
          //$fuente->field_is_canal_correo[0]['value']=0;      
          $fuente->field_is_canal_correo[0]['value']=1;
          $is_canal_correo=1;
      }
    }
  }
  //
  
  //gemini
  //if(hontza_canal_rss_is_visualizador_activado()){
  if(red_canal_is_canal_opencalais_activado()){
    $fuente->field_is_canal_opencalais=array();
    $fuente->field_is_canal_opencalais[0]['value']=0;
    if(isset($form_state['values']['is_canal_opencalais']) && !empty($form_state['values']['is_canal_opencalais'])){
        $fuente->field_is_canal_opencalais[0]['value']=1;
    }
  }  

  $fuente->taxonomy=unset_no_tid($fuente->taxonomy);
  //
  //gemini-2013
  $fuente->estrategia_responde_array=hontza_canal_get_request_responde_array($is_form_save);	
  $fuente->hound1_search='';
  if(isset($form_state['values']['hound1_search']) && !empty($form_state['values']['hound1_search'])){
    $fuente->hound1_search=$form_state['values']['hound1_search'];
  }
  //gemini-2014
  if(hontza_is_hound_text_input()){
      hound_add_canal_hound_parametros($form_state,$fuente);      
  }
  //intelsat-2015
  if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
    $fuente->field_canal_source_type=yql_wizard_set_field_canal_source_type($form_state);
  }
  //
  //intelsat-2015
  red_despacho_vigilancia_add_import_canal_duplicate_news_form_state_values($form_state,$fuente);  
  $form_state['yql_obj'] = $fuente;

  //gemini
  if(my_is_simple($form_state)){
  	set_rss_simple($form,$form_state);
  }else if(my_is_opml($form_state)){
  	set_rss_opml($form,$form_state);
  }
  //intelsat-2015
  if(!$is_canal_correo){
      $campos_rss=array();
      if(isset($form_state['yql_obj']->campos_rss) && !empty($form_state['yql_obj']->campos_rss)){
        $campos_rss=$form_state['yql_obj']->campos_rss;
      }
      $my_rss = implode(', ',$campos_rss);
      if(hontza_solr_search_is_canal_correo_by_rss($my_rss)){
          $fuente->field_is_canal_correo=array();
          $fuente->field_is_canal_correo[0]['value']=1;
      }
  }
  //intelsat-2016
  if(isset($form_state['values']['is_url_json']) && !empty($form_state['values']['is_url_json'])){     
     $fuente->is_url_json=1;
     $fuente->field_json_fields[0]['value']=$form_state['values']['json_fields'];
     $form_state['yql_obj']->is_url_json=1;
     $fuente->field_json_fields[0]['value']=$form_state['values']['json_fields'];
  }    
  //
}

/**
 * Formulario donde a�adimos las fuentes RSS
 */
function agregar_fuente_form(&$form, &$form_state){
  //gemini 
  set_title_yql_forms($form_state);
  	$value='';
	if(my_is_simple($form_state)){
		$value=$form_state['yql_obj']->campos_rss[1];
		/*unset($form_state['yql_obj']->campos_rss);
		unset($form_state['yql_obj']->field_nombrefuente_canal);*/
	}
  //	

  //obtenemos cantidad de rss
  $num_rss = $form_state['yql_obj']->numero_rss;
  
  $form['fuentes'] = array(
    '#title' => t('Add RSS feed'),
    '#type' => 'fieldset',
	//gemini
	'#attributes'=>array('id'=>'fuentes_simple'),
  	//
  );
      
  //gemini-2013
  $is_hound=hontza_is_hound_by_yql_obj($form_state['yql_obj']);
  
  for($i=1;$i<=$num_rss;$i++){
    $form['fuentes'][$i] = array(
      '#title' => t('RSS '.$i),
      '#type' => 'textfield',
      '#description' => t('Url of RSS'),
      '#size' => 50,
      '#maxlength'=> 500,  
	);
	//gemini
	if(my_is_simple($form_state)){		
	  $form['fuentes'][$i]['#value']=$value;          
	}else if(my_is_opml($form_state)){
	  $form['fuentes'][$i]['#value']=$form_state['yql_obj']->xml_list[$i-1];
	}
	//
            if($is_hound){
                //intelsat-2016-hound-filter
                if(my_is_simple($form_state)){	
                    $form['fuentes'][$i]['#default_value']=hontza_get_hound_rss1();
                    yql_add_filter_hound_js($i);
                    $form['fuentes'][$i]['#prefix']='<div style="display:none">';
                    $form['fuentes'][$i]['#suffix']='</div>';
                    if(hontza_is_hound_text_input()){
                                    hound_add_select_text_input($form);                              
                    }else{
                                    //gemini-2013
                                    $hound_options=hontza_define_hound_options();
                                    $form['fuentes']['hound'.$i] = array(
                                            '#title' => t('Hound '.$i),
                                            '#type' => 'select',
                                            '#options'=>$hound_options,
                                            '#required'=>TRUE,
                                          );                            
                                    if(hontza_is_hound_search($hound_options)){
                                        $form['fuentes']['hound'.$i.'_search'] = array(
                                                '#title' => t('Hound search'),
                                                //'#type' => 'textfield',
                                                '#type' => 'textarea',
                                                '#maxlength'=>1024,
                                        );                                                            
                                    }
                    }
                }else{
                    $url=yql_get_hound_url_by_data('',$form_state['yql_obj']);
                    $form['fuentes'][$i]['#default_value']=$url;                    
                }  
            }
        
        
  }
  
  	//gemini
	if(my_is_simple($form_state) || my_is_opml($form_state)){
		$js='$(document).ready(function()
			{
				$("#fuentes_simple").hide();
				'.my_js_cmd().'
				$("#edit-next").click(); 
				
			});';
		drupal_add_js($js,'inline');
	}
  //
}

function agregar_fuente_form_submit(&$form, &$form_state){
  
  foreach ($form_state['values'] as $campo=>$valor){
    if (is_numeric($campo) and $valor){
      $rss[$campo].=$valor;
    }
  }
  
  $form_state['yql_obj']->campos_rss= $rss;
  $form_state['yql_obj']->field_nombrefuente_canal[0]['value'] = implode(', ',$form_state['yql_obj']->campos_rss);
  //gemini-2013
  if(is_import_fuente_html($form_state['yql_obj'])){
    $form_state['yql_obj']->field_nid_fuente_canal[0]['value']=$form_state['yql_obj']->field_fuentehtml_nid[0]['value'];
  }
  if(!isset($form_state['yql_obj']->hound1_search)){
    $form_state['yql_obj']->hound1_search='';
  }
  if(isset($form_state['values']['hound1_search']) && !empty($form_state['values']['hound1_search'])){
    $form_state['yql_obj']->hound1_search=$form_state['values']['hound1_search'];
  }
  //gemini-2014
  if(hontza_is_hound_text_input()){
      hound_add_canal_hound_parametros($form_state,$form_state['yql_obj']);
  }
  //
}


/**
 * Formulario donde incluir los filtros.
 */
function filtros_form(&$form, &$form_state){
  //gemini
  set_title_yql_forms($form_state);
  //gemini-2013
  $is_hound=hontza_is_hound_by_yql_obj($form_state['yql_obj']);
  
  if($form_state['yql_obj']->campos_rss){
    //gemini
	if(!my_is_simple($form_state) && !my_is_opml($form_state)){
	//
	$form['info'] = array(
      '#type' => 'item',
      '#value' => t("<b>Please fill ONLY ONE of the avaliable filters.<br>To get all the contents, just don't fill any filter.</b>"),
        );
	}
    /*$form['filtros1'] = array(
      '#title' => t('Apply filter 1 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros1'),
	  //
    );
    $form['filtros1']['todos'] = array(
      '#title' => t('General Search'),
      '#type' => 'textfield',
      '#size' => 20,
      '#description' => t('There will be shown only items containing the term in the title or description'),
    );
    $form['filtros2'] = array(
      '#title' => t('Apply filter 2 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros2'),
	  //
    );
    $form['filtros2']['titulo'] = array(
      '#title' => t('Contains this word in the title'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros2']['cbox1'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros2']['descripcion'] = array(
      '#title' => t('Contains this word in the description'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros3'] = array(
      '#title' => t('Apply filter 3 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	   //gemini
	  '#attributes'=>array('id'=>'my_filtros3'),
	  //
    );
    $form['filtros3']['no_titulo'] = array(
      '#title' => t('It does not contain this word in the title'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros3']['cbox2'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros3']['no_descripcion'] = array(
      '#title' => t('It does not contain this word in the description'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4'] = array(
      '#title' => t('Apply filter 4 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros4'),
	  //
    );
    $form['filtros4']['contiene'] = array(
      '#title' => t('Contains this word in the'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4']['select1'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('Title'), t('Description')),
    );
    $form['filtros4']['con'] = array(
      //'#title' => t('Anidación de filtros'),
      '#title' => t('Filter nesting'),  
      '#type' => 'fieldset',	   
    );
    $form['filtros4']['con']['cbox3'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros4']['no_contiene'] = array(
      '#title' => t('It does not contain the word'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4']['select2'] = array(
      '#type' => 'radios',
      '#default_value' => 1,
      '#options' => array(t('Title'), t('Description')),
    );
    
    //textarea entrada manual de filtros
    $form['filtros5'] = array(
      '#title' => t('Apply filter 5 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t("By enabling filter 5 all the other filters will be disabled").'<br>
      <A HREF="http://developer.yahoo.com/yql/guide/index.html" target="_blank"><b>Manual YQL</b></A>' ,
	   //gemini
	  '#attributes'=>array('id'=>'my_filtros5'),
	  //
    );
    $form['filtros5']['checkarea'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable to create filters manually'),
      '#prefix' => '<div class="filtro-manual">',
      '#suffix' => '</div>',
    );
    $form['filtros5']['area'] = array(
      '#title' => t('Code to create the filter manually'),
      '#type' => 'textarea',
      '#default_value' => 'and channel.item.title like "%Android%"',
      '#disabled' => false,
      '#prefix' => '<div class="area-filtro-manual">',
      '#suffix' => '</div>',
      '#weight' => 5,
      '#description' =>t('Ej: and (channel.item.title like "%Android%" and channel.item.description not like "%HTC%"'),
    );
    
    //gemini-2013
    if($is_hound){
        $form['filtros5']['#prefix']='<div style="display:none;">';
        $form['filtros5']['#suffix']='</div>';        
    }
    */
    hontza_set_form_filtros_yql($is_hound,0,$form,$form_state);    
  }
  else{
    $form['text'] = array(
      '#title' => t('There are no RSS'),
      '#type' => 'item',
      '#size' => 20,
    );
    drupal_set_message('No ha agregado ningun RSS sobre el que aplicar filtros.','status');
  }
  
  	//gemini
	if(my_is_simple($form_state) || my_is_opml($form_state)){
		$js='$(document).ready(function()
			{
				for(i=1;i<=5;i++){
					$("#my_filtros"+i).hide();
				}
				'.my_js_cmd().'
				//				
				$("#edit-next").click(); 				 
			});';
		drupal_add_js($js,'inline');
	}
  //
}

function filtros_form_validate(&$form, &$form_state){
  if($form_state['values']['todos'] and
      ($form_state['values']['titulo'] or $form_state['values']['descripcion']
      or $form_state['values']['no_titulo'] or $form_state['values']['no_descripcion']
      or $form_state['values']['contiene'] or $form_state['values']['no_contiene'])){
    form_error($form,t('Filter too long, please reduce RSS or filtering terms')); 
  }
  elseif(($form_state['values']['titulo'] or $form_state['values']['descripcion']) and
      ($form_state['values']['no_titulo'] or $form_state['values']['no_descripcion'] or $form_state['values']['todos']
       or $form_state['values']['contiene'] or $form_state['values']['no_contiene'])){
    form_error($form,t('Filter too long, please reduce RSS or filtering terms'));
  }
  elseif(($form_state['values']['no_titulo'] or $form_state['values']['no_descripcion']) and
      ($form_state['values']['titulo'] or $form_state['values']['descripcion'] or $form_state['values']['todos']
       or $form_state['values']['contiene'] or $form_state['values']['no_contiene'])){  
    form_error($form,t('Filter too long, please reduce RSS or filtering terms'));
  }
  elseif(($form_state['values']['contiene'] or $form_state['values']['no_contiene']) and
      ($form_state['values']['titulo'] or $form_state['values']['descripcion'] or $form_state['values']['todos']
       or $form_state['values']['no_titulo'] or $form_state['values']['no_descripcion'])){  
    form_error($form,t('Filter too long, please reduce RSS or filtering terms'));
  }
}
function filtros_form_submit(&$form, &$form_state){
  $filtros=array();  
  
  if($form_state['values']['checkarea']){
    $filtros['area'].=$form_state['values']['area'];
  }
  else{   
    //Filtro 1
    if($form_state['values']['todos']){
      $filtros['todos'].=$form_state['values']['todos'];
    }
    //Filtro 2
    if($form_state['values']['titulo']){
      $filtros['titulo'].=$form_state['values']['titulo'];
    }
    if($form_state['values']['descripcion']){
      $filtros['descripcion'].=$form_state['values']['descripcion'];
    }
    //Filtro 3
    if($form_state['values']['no_titulo']){
      $filtros['no_titulo'].=$form_state['values']['no_titulo'];
    }
    if($form_state['values']['no_descripcion']){
      $filtros['no_descripcion'].=$form_state['values']['no_descripcion'];
    }
    //Filtro 4
    if($form_state['values']['contiene']){
      $filtros['contiene'].=$form_state['values']['contiene'];
    }
    if($form_state['values']['no_contiene']){
      $filtros['no_contiene'].=$form_state['values']['no_contiene'];
    }
    //Filtro 2
    $form_state['yql_obj']->filtrosSI = $form_state['values']['cbox1'];
    //Filtro 3
    $form_state['yql_obj']->filtrosNO = $form_state['values']['cbox2'];
    //Filtro 4
    $form_state['yql_obj']->campo_contiene = $form_state['values']['select1'];
    $form_state['yql_obj']->campo_no_contiene = $form_state['values']['select2'];
    $form_state['yql_obj']->conjuncion = $form_state['values']['cbox3'];
  }
  
  $form_state['yql_obj']->filtros= $filtros; 
}

/**
 * Previsualizaci�n del canal YQL.
 */
function previsualizar_form(&$form, &$form_state){
  //gemini
  set_title_yql_forms($form_state);
  //gemini
  $filter='';
  //
  /*
  //Filtro manual
  if($form_state['yql_obj']->filtros['area']){
    $filter = $form_state['yql_obj']->filtros['area'];
    
  }
  //Resto de filtros
  else{
    //Filtro 1 todos
    if($form_state['yql_obj']->filtros['todos']){
    
        
     
        
        
        //gemini
        //if(!empty($filter)){
        $filter.=' and ';
        //}
        //$filter=' and (channel.item.title like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.description like "%'.$form_state['yql_obj']->filtros['todos'].'%")';
        $filter.='(channel.item.title like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.description like "%'.$form_state['yql_obj']->filtros['todos'].'%")';
        //

    }
    
    //Filtro 2 
    elseif($form_state['yql_obj']->filtros['titulo'] or $form_state['yql_obj']->filtros['descripcion']){
      if($form_state['yql_obj']->filtros['titulo'] and $form_state['yql_obj']->filtros['descripcion']){
        if($form_state['yql_obj']->filtrosSI==0){
          $f='or';
        }
        else{
          $f='and';
        }
        $filter=' and (channel.item.title like "%'.$form_state['yql_obj']->filtros['titulo'].'%" '.$f.' channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%")';
      }
      elseif($form_state['yql_obj']->filtros['titulo']){
        $filter=' and channel.item.title like "%'.$form_state['yql_obj']->filtros['titulo'].'%"';
      }
      elseif($form_state['yql_obj']->filtros['descripcion']){
        $filter=' and channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%"';
      }
    }
    
    //Filtro 3
    elseif($form_state['yql_obj']->filtros['no_titulo'] or $form_state['yql_obj']->filtros['no_descripcion']){
      if($form_state['yql_obj']->filtros['no_titulo'] and $form_state['yql_obj']->filtros['no_descripcion']){
        if($form_state['yql_obj']->filtrosNO==0){
          $f='or';
        }
        else{
          $f='and';
        }
        $filter=' and (channel.item.title not like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%" '.$f.' channel.item.description not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%")';
      }
      elseif($form_state['yql_obj']->filtros['no_titulo']){
        $filter=' and channel.item.title like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%"';
      }
      elseif($form_state['yql_obj']->filtros['no_descripcion']){
        $filter=' and channel.item.description like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%"';
      }
    }
    //Filtro 4
    elseif($form_state['yql_obj']->filtros['contiene'] or $form_state['yql_obj']->filtros['no_contiene']){
      //Tipo del contiene
      if($form_state['yql_obj']->campo_contiene==0){
          $name1='title';
        }
        else{
          $name1='description';
        }
        //Tipo del no_contiene
      if($form_state['yql_obj']->campo_no_contiene==0){
          $name2='title';
        }
        else{
          $name2='description';
        }
      if($form_state['yql_obj']->filtros['contiene'] and $form_state['yql_obj']->filtros['no_contiene']){
        if($form_state['yql_obj']->conjuncion==0){
          $f='or';
        }
        else{
          $f='and';
        }
        $filter=' and (channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" '.$f.' channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%")';
      }
      elseif($form_state['yql_obj']->filtros['contiene']){
        $filter=' and channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%"';
      }
      elseif($form_state['yql_obj']->filtros['no_contiene']){
        $filter=' and channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%"';
      }
    }Reino Unido-Norwich
  }*/
  //gemini-2013
  //if(is_super_admin()){
  $filter=my_encode_yql_filter($form,$form_state);
 
  //}
  //print $filter;exit();
  //Obtener fuentes RSS
  $data=$form_state['yql_obj']->campos_rss;
    
  //$url ='http://query.yahooapis.com/v1/public/yql?q=';
  /*if(is_super_admin()){
      print implode("', '",$data);exit();
  }*/
  //gemini
  //AVISO::::se han añadido los unique-s
  //$query = "select channel.title, channel.link, channel.item.title, channel.item.link, channel.item.description, channel.item.guid from xml where url in('".implode("', '",$data)."')";
  //$query = "select channel.title, channel.link, channel.item.title, channel.item.link, channel.item.description, channel.item.guid from xml where url in('".implode("', '",$data)."')";
  //$post_query=" | unique(field='channel.item.title') | unique(field='channel.item.link') | unique(field='channel.item.description')";
  //$query = "select * from xml where url in('".implode("', '",$data)."')";
  
  //gemini
  //$url.=urlencode($query.$filter.'LIMIT 20');
  //gemini-2013
  /*
  $query=addslashes($query);
  $filter=addslashes($filter);
  $post_query=addslashes($post_query);
   * 
   */
  //gemini-2013  
  //$url.=urlencode($query.$filter.$post_query);
  if(my_is_simple($form_state)){
    $url=$data[1];    
  }else{
    $url=yql_wizard_define_url($data,$filter,$form_state['yql_obj']);
  }  
  //$url=$query.$filter.$post_query;
  //$url.=urlencode($query);
  //simulando
  //$url='http://192.168.110.202/hound/houndRss.php';
  //intelsat-2014
  if(!hontza_social_is_json_url($url)){
  //
    if(hontza_is_hound_rss_by_data($data,$form_state['yql_obj'])){
        $url=yql_get_hound_url_by_data($data,$form_state['yql_obj']);
        /*if(user_access('root')){
            print $url;exit;
        }*/
        /*if(!my_is_simple($form_state)){
            $url=yql_add_request_params_filtros_hound($url,$form,$form_state);
        }*/
    }
  }
  
  
  $form['consulta'] = array(
    '#title' => t('YQL requests with filters'),
    '#type' => 'fieldset',
	 //gemini
	  '#attributes'=>array('id'=>'my_con_filtros'),
	  //
  );
  $form['consulta']['url'] = array(
    '#title' => t('YQL query'),
    '#type' => 'item',
    '#value' => $url,
  );
  
  
  //Almacenar la url en una variable del objeto para poder utilizarla despues
  $form_state['yql_obj']->urlsearch = $url;
  //intelsat-2015
  $is_kimonolabs_json_url=hontza_canal_rss_is_kimonolabs_json_url($url);
  $is_url_json=yql_wizar_is_url_json($form_state['yql_obj']);  
  $dxml='';
  if(!$is_kimonolabs_json_url && !$is_url_json){
    $dxml = file_get_contents($url);    
  }
  //intelsat-2016  
  yql_wizard_add_from_canal_title($form_state['yql_obj'],$dxml);
  
  //intelsat-2014
  //if(hontza_social_is_json_url($url)){
  //intelsat-2016
  //if(hontza_social_is_json_url($url) || $is_kimonolabs_json_url){
  if(hontza_social_is_json_url($url) || $is_kimonolabs_json_url || $is_url_json){
      if($is_url_json){
        $form_state['yql_obj']->field_fuente_canal[0]['value']=yql_wizard_get_field_fuente_canal_value($form_state['yql_obj']);  
      }else{
        $form_state['yql_obj']->field_fuente_canal[0]['value']='JSON';
      }    
  //    
  }else if(!strpos($dxml,'yahoo:count="0"')){
          $data='';
          //intelsat-2015
          $output='';
          if(red_is_xml($dxml)){
            $data = simplexml_load_string($dxml);    
            if(isset($data->results->rss)){
                $sets = $data->results->rss;
                $all = sizeof($sets);
                //gemini-2014
                $all=hontza_hound_limit_all($all);
                //
                $output='';
                for($i=0;$i<$all;$i++){
                  $r = $sets[$i];
                  $title = $r->channel->title.'';
                  if($title != $oldtitle){
                    $output.='<div><h3><a href="'.($r->channel->link.'').'">'.($r->channel->title.'').'</a></h3><ul>';
                  }
                    $output.='<li><a href="'.($r->channel->item->link.'').'">'.($r->channel->item->title.'').'</a></li>';
                  if($title != $sets[$i+1]->channel->title.''){
                    $output.='</ul></div>';
                  }
                  $oldtitle = $r->channel->title.'';
                }        
            }else{
                //intelsat-2016
                yql_wizard_is_canal_usuario_exportado_add($form_state['yql_obj'],$data);
                $sets = $data->channel->item;
                $all=0;
                if(isset($data->channel) && isset($data->channel->item)){
                    $all = sizeof($sets);
                }
                $all=hontza_hound_limit_all($all);
                $output='';
                  $title = (string) $data->channel->title;
                  $link=(string) $data->channel->link;
                  $output.='<div><h3><a href="'.($link.'').'">'.($title.'').'</a></h3><ul>';


                for($i=0;$i<$all;$i++){
                  $r = $sets[$i];
                  $item_title = (string) $r->title;
                  $item_link=(string) $r->link;
                  //
                  $output.='<li><a href="'.($item_link.'').'">'.($item_title.'').'</a></li>';          
                }
                $output.='</ul></div>';
            }
          }    
    
      $form['datos'] = array(
      '#title' => t('Result of request'),
      '#type' => 'fieldset',
	  //gemini
	  '#attributes'=>array('id'=>'my_resultado'),
	  //
    );
      $form['datos']['content'] = array(
      '#type' => 'item',
      '#value' => $output,
    );    
  }else{
     $form['datos'] = array(
          '#title' => t('Result of request'),
          '#type' => 'fieldset',
               //gemini
              '#attributes'=>array('id'=>'my_resultado'),
              //
        );  
      
    //gemini-2014
    $yql_entry=yql_is_rss_entry($form_state['yql_obj'],$url);
    if($yql_entry['ok']){
        $form_state['yql_obj']->urlsearch = $yql_entry['url'];
        $form_state['yql_obj']->field_is_atom[0]['value']=1;
        $output=yql_entry_previsualizar($yql_entry['data']);
        $form['datos']['content'] = array(
          '#type' => 'item',
          '#value' => $output,
        );
    }else{       
        $form['datos']['content'] = array(
          '#type' => 'item',
          '#value' => t('There are no results with those filters'),
        );
    }    
  }
  
  	//gemini
	if(my_is_simple($form_state) || my_is_opml($form_state)){
		$js='$(document).ready(function()
			{
				$("#my_resultado").hide();
				$("#my_con_filtros").hide();
				'.my_js_cmd('edit-return').'
				$("#edit-return").click();
			});';
		drupal_add_js($js,'inline');
	}
  //
}


function get($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}

 
function previsualizar_form_submit(&$form, &$form_state){
  $form_state['yql_obj']->feeds = array('FeedsHTTPFetcher' => array('source' =>  $form_state['yql_obj']->urlsearch));
}


function yql_next(&$form_state){
  $canal = $form_state['yql_obj'];
  $cache = ctools_object_cache_set('yql_obj', $form_state['cache name'], $canal);
}


function yql_finish(&$form_state){
  global $user;	  
  if(my_is_simple($form_state) || my_is_opml($form_state)){  	
    if(!isset($form_state['yql_obj']->field_responsable_uid[0]['uid']) || empty($form_state['yql_obj']->field_responsable_uid[0]['uid'])){ 
       $form_state['yql_obj']->field_responsable_uid[0]['uid']=$user->uid;
    }
    if(!isset($form_state['yql_obj']->field_responsable_uid2[0]['uid']) || empty($form_state['yql_obj']->field_responsable_uid2[0]['uid'])){ 
       $form_state['yql_obj']->field_responsable_uid2[0]['uid']=$user->uid;
    }
  }
  //gemini
  $my_id=0;
  $form_state['yql_obj']->field_import_html_source_id[0]['value']=0;
  if(isset($form_state['yql_obj']->my_id) && !empty($form_state['yql_obj']->my_id)){
    $my_id=$form_state['yql_obj']->my_id;
    $form_state['yql_obj']->field_import_html_source_id[0]['value']=$my_id;
    unset($form_state['yql_obj']->my_id);
  }
  //
  $estrategia_responde_array=array();
  if(isset($form_state['yql_obj']->estrategia_responde_array)){
      $estrategia_responde_array=$form_state['yql_obj']->estrategia_responde_array;
  }
  //gemini-2014  
  $form_state['yql_obj']->field_canal_active_refresh[0]['value']=1;
  
  //intelsat-2014
  if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
    $form_state['yql_obj']->field_canal_source_type_updated[0]['value']=1;
  }
  //intelsat-2016
  $mail_from='';
  if(isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
      $mail_from=$form_state['yql_obj']->mail_from;
  }
  //
  node_save($form_state['yql_obj']);
  $canal = $form_state['yql_obj'];  
  //intelsat-2016
  if(!empty($mail_from)){
    $compartir_documentos_from_canal_title=$canal->title;  
    if(isset($canal->compartir_documentos_from_canal_title) && !empty($canal->compartir_documentos_from_canal_title)){
        $compartir_documentos_from_canal_title=$canal->compartir_documentos_from_canal_title;
        red_copiar_importar_enviar_mail_from($mail_from,$compartir_documentos_from_canal_title,$canal);
    }    
  }
  $canal->estrategia_responde_array=$estrategia_responde_array;
  //gemini-2013
  hontza_canal_save_reto_al_que_responde($canal);
  
  //gemini
  save_fuentehtml_nid($my_id,$canal->nid);
  //gemini-2013
  yql_save_canal_yql_parametros($canal);
  //gemini-2014
  //intelsat-2016
  if(!yql_wizar_is_url_json($canal)){
    if(hontza_is_hound_text_input()){
        //hound_delete_canal_hound_parametros($canal);
        hound_save_canal_hound_parametros($canal);
        hound_api_save_parametros($canal);
    }
  }
  //gemini
  //$form_state['redirect'] = 'node/'.$canal->nid;
  $form_state['redirect'] = 'node/'.$canal->nid.'/import';
  //
  ctools_object_cache_clear('yql_obj', $form_state['cache name']);
}


function yql_cancel(&$canal){
  //$form_state['redirect'] = 'node';
  //drupal_goto('vigilancia');
  ctools_object_cache_clear('yql_obj', $form_state['cache name']);
  hontza_goto_vigilancia_default();  
}
//gemini
function prepare_simple_or_opml(&$form,&$form_state){
    //gemini
      $is_import_html=0;
      $rss1='';
      $row_html=get_import_html_row($_REQUEST['my_id']);
      if(isset($row_html->id)){
        $rss1=$row_html->rssgen;
        //echo print_r($form_state,1);
        $is_import_html=1;
      }
    //gemini-2013
    $is_hound=0;
    $form_state['yql_obj']->field_is_hound[0]['value']=0;  
    if(yql_is_crear_hound()){
        $is_hound=1;
        $form['is_hound']=array(
            '#type' => 'hidden',
            '#value' => 1,			  
	);
        $rss1=hontza_get_hound_rss1();
        $form_state['yql_obj']->field_is_hound[0]['value']=1;    
    }
    //
    //if($is_hound){
        if(red_copiar_is_yql_wizard_responsable_uid_required($form_state)){
            $form['responsable_uid']['#required']=TRUE;
        }
    //}        
	$indefinida_tid=my_get_term_data_tid('Categoria indefinida');	
	//intelsat-2016-hound-filter
        //if(my_is_simple($form_state) || my_is_opml($form_state)){
        if(my_is_simple($form_state) || my_is_opml($form_state) || $is_hound){
		$js='var is_import_html='.$is_import_html.';
                var is_hound='.$is_hound.';
                var hound_url="'.hontza_define_hound_url().'"    
                var is_simple_by_solr='.hontza_solr_search_is_simple_import_rss().';
                $(document).ready(function()
		{
		   $("#edit-numero-rss-wrapper").hide();
                   /*if(is_hound==0){
                    $("#edit-responsable-uid-wrapper").hide();
                    $("#edit-responsable-uid2-wrapper").hide();
                   }*/
                   if(is_simple_by_solr){
                        $("#edit-'.$indefinida_tid.'").attr("checked",true);
                        $("#my_categorias_yql").hide();    
                   }    		   
                   if(is_import_html==1){
                        $("#edit-next").hide();
                        $("#fuentes_simple").hide();
                        $("#edit-titulo-wrapper").hide();
                        $("#edit-cancel").hide();
                        $("#edit-next").click();
                   }
                   if(is_hound){
                    $(("#edit-hound1")).change(function(){
                        var hound_title=$("#edit-hound1 option:selected" ).text();
                        var new_hound_url=hound_url+"?title="+hound_title;
                        $("#edit-1").val(new_hound_url);
                    });
                   }
                });';
		//
                //intelsat-2016-hound-filter
		//if(my_is_simple($form_state)){
                if(my_is_simple($form_state) || $is_hound){
                    //intelsat-2014
                    if(hontza_social_is_activado()){
                        if(social_learning_collections_is_import_table_html()){
                            $url_json=social_learning_collections_create_filtro_url('json');
                            $rss1=$url_json;
                        }    
                    }
                    //intelsat-2016
                    $rss1=red_copiar_get_canal_import_rss_by_arg($rss1);
                    //
			$form['fuentes'] = array(
				'#title' => t('Add RSS feed'),
				'#type' => 'fieldset',
				//gemini
				'#attributes'=>array('id'=>'fuentes_simple'),
				//
			);
                        //intelsat-2016
                        if(yql_wizard_is_url_json()){
                          yql_wizard_add_url_json_form_field($form);  
                        }else{
			  $form['fuentes'][1] = array(
				  '#title' => t('RSS 1'),
				  '#type' => 'textfield',
				  '#description' => t('Url of RSS'),
				  '#size' => 50,
                                  //gemini-2013
                                  '#maxlength'=>500,
                                  //gemini
                                  '#default_value' => $rss1,
                                  //
				);
                        }
                          if($is_hound){
                            $form['fuentes'][1]['#prefix']='<div style="display:none">';
                            $form['fuentes'][1]['#suffix']='</div>';
                            //gemini-2013
                            if(hontza_is_hound_text_input()){
                                hound_add_select_text_input($form);                              
                            }else{    
                                $hound_options=hontza_define_hound_options();
                                $form['fuentes']['hound1'] = array(
                                        '#title' => t('Hound'),
                                        '#type' => 'select',
                                        '#options'=>$hound_options,
                                        '#required'=>TRUE,
                                      );
                                if(hontza_is_hound_search($hound_options)){
                                    $form['fuentes']['hound1_search'] = array(
                                            '#title' => t('Hound search'),
                                            //'#type' => 'textfield',
                                            '#type' => 'textarea',
                                            '#maxlength'=>1024,
                                    );                                                            
                                }
                            }    
                          }
			//intelsat-2016-hound-filter
                        if(my_is_simple($form_state)){	
                            $form['simple']=array(
                                      '#type' => 'hidden',
                                      '#value' => 1,			  
                            );
                        }    
		}else{
			$form['#attributes'] = array('enctype' => "multipart/form-data");
			$form['fuentes'] = array(
				'#title' => t('Add OPML'),
				'#type' => 'fieldset',				
				'#attributes'=>array('id'=>'fuentes_opml'),
			);
			  $form['fuentes']['field_my_opml_0'] = array(
				  '#title' => t('OPML File'),
				  '#type' => 'file',
				);
				
			$form['is_opml']=array(
				  '#type' => 'hidden',
				  '#value' => 1,			  
			);
		}			 
	}else{
		$js='$(document).ready(function()
		{
		   $("#edit-'.$indefinida_tid.'-wrapper").hide();		  
		});';
	}        
	drupal_add_js($js,'inline');
}
//gemini
function set_rss_simple(&$form,&$form_state){	 
  $indefinida_tid=my_get_term_data_tid('Categoria indefinida');	
  foreach ($form_state['values'] as $campo=>$valor){  	
    if (is_numeric($campo) && $valor && $campo!=$indefinida_tid){
      $rss[$campo].=$valor;
    }
  }
  $form_state['yql_obj']->campos_rss= $rss;
  $form_state['yql_obj']->field_nombrefuente_canal[0]['value'] = implode(', ',$form_state['yql_obj']->campos_rss);   
  //gemini-2013
  if(is_import_fuente_html($form_state['yql_obj'])){
    $form_state['yql_obj']->field_nid_fuente_canal[0]['value']=$form_state['yql_obj']->field_fuentehtml_nid[0]['value'];
  }
  //
}
//gemini
function my_js_cmd($submit_btn_in=''){
	$submit_btn='edit-next';
	if(!empty($submit_btn_in)){
		$submit_btn=$submit_btn_in;
	}
	//
				$result='
				var submit_btn="'.$submit_btn.'"
				var my_title="'.t('Importing').'";
				$(".inner").hide();
				$("#main").css("width","100%");  
				$("#main").css("background-color","#F4F4F4");
				$("#main").css("padding-left","0px");
				$("#edit-previous").hide();
				$("#'.$submit_btn.'").hide();
				$("#edit-cancel").hide();
				$(".title").html(my_title);
				$(".wizard-trail").hide();
				$("#block-hontza-5").hide();
				$("#navigation").hide();
				$("#block-menu-primary-links").hide();';				
return $result;
}
//gemini
function my_upload_opml_xml(){		
	foreach ($_FILES["files"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$tmp_name = $_FILES["files"]["tmp_name"][$key];
                        //$name = $_FILES["files"]["name"][$key];
			$name=time().".xml";
			$xml_path="sites/default/files/my_opml/$name";
			move_uploaded_file($tmp_name, $xml_path);
			if (file_exists($xml_path)) {
				$xml = simplexml_load_file($xml_path);			 
				$num=count($xml->body->outline->outline);
				$result=array();
				$info=array();
				if($num>0){
					for($i=0;$i<$num;$i++){	
						$result[$i]=(string )$xml->body->outline->outline[$i]->attributes()->xmlUrl;													
					}																				
				}else{
					$num=count($xml->body->outline);
					for($i=0;$i<$num;$i++){	
						$result[$i]=(string )$xml->body->outline[$i]->attributes()->xmlUrl;													
					}	
				}
				$info['name']=$name;
				$info['xml_list']=$result;
				return $info;				
			} /*else {
				exit('Error abriendo test.xml.');
			}*/
		}
	}
	return '';	
}
//gemini
function set_rss_opml(&$form,&$form_state){	   
  $form_state['yql_obj']->campos_rss= $form_state['yql_obj']->xml_list;
  $form_state['yql_obj']->field_nombrefuente_canal[0]['value'] = implode(', ',$form_state['yql_obj']->campos_rss);   
  //gemini-2013
  if(is_import_fuente_html($form_state['yql_obj'])){
    $form_state['yql_obj']->field_nid_fuente_canal[0]['value']=$form_state['yql_obj']->field_fuentehtml_nid[0]['value'];
  }
  //
}
//gemini
function set_title_yql_forms(&$form_state){
    //intelsat-2016
    if(yql_wizard_is_url_json()){
      $is_csv=hontza_canal_json_is_csv($_REQUEST['url_json']);
      if($is_csv){
        drupal_set_title(t('Import Csv'));  
      }else{                      
        drupal_set_title(t('Import Json'));
      }
    }else if(my_is_simple($form_state)){
      drupal_set_title(t('Import RSS'));
    }else{
      drupal_set_title(t('Create Channel RSS Filter'));
    }
}
//gemini
function save_fuentehtml_nid($my_id,$canal_nid){
    if(!empty($my_id) && !empty($canal_nid)){
        $sql='UPDATE import_html_source SET fuentehtml_nid='.$canal_nid.' WHERE fuentehtml_nid=0 AND id='.$my_id;    
        db_query($sql);
    }
}
//gemini-2013
function is_import_fuente_html($my_obj){
    if(isset($my_obj->field_fuentehtml_nid) && isset($my_obj->field_fuentehtml_nid[0]) && isset($my_obj->field_fuentehtml_nid[0]['value'])){
        if(!empty($my_obj->field_fuentehtml_nid[0]['value'])){
            return 1;
        }
    }
    return 0;
}
//gemini-2013
function yql_save_canal_yql_parametros($canal){
    hontza_yql_save_canal_yql_parametros($canal);     
}
//gemini-2013
function yql_is_crear_hound(){
    $v=my_get_request('is_hound');
    if(!empty($v)){
        return 1;
    }
    return 0;
}
//gemini-2013
function yql_get_hound_url_by_data($data,$yql_obj=''){
    if(hontza_is_hound_text_input()){
        $hound_id=$yql_obj->canal_hound_parametros->hound_id;
        //intelsat-2016
        $url=hound_enlazar_inc_get_canal_rss_url($hound_id);
        //$hound_search=hontza_limpiar_returns($yql_obj->hound1_search);
        //$url.='&hound_search='.$hound_search;
        return $url;
    }else{
        if(!empty($data)){
            $url_values=array_values($data);
            $num=count($url_values);
            if($num==1){
                /*if(user_access('root')){
                    echo print_r($yql_obj,1);exit();
                }*/
                $url=$url_values[0];
                /*$find='/hound/houndRss.php';
                $pos=strpos($url,$find);
                if($pos===FALSE){
                    return '';
                }else{*/
                $result=$url.'&from=0';
                if(!empty($yql_obj) && isset($yql_obj->hound1_search) && !empty($yql_obj->hound1_search)){
                    $hound_search=hontza_limpiar_returns($yql_obj->hound1_search);
                    $result.='&hound_search='.$hound_search;
                }
                return $result;
                //}    
            }
        }
    }    
    return '';
}
//gemini-2013
function yql_add_filter_hound_js($i){
$js='$(document).ready(function()
			{
                                var hound_url="'.hontza_define_hound_url().'"
				$(("#edit-hound'.$i.'")).change(function(){
                                    var hound_title=$("#edit-hound1 option:selected" ).text();
                                    var new_hound_url=hound_url+"?title="+hound_title;
                                    $("#edit-'.$i.'").val(new_hound_url);
                                }); 
				
			});';
		drupal_add_js($js,'inline');
}
//gemini-2013
function yql_add_request_params_filtros_hound($url_in,&$form,&$form_state){
    $url=$url_in;
    if(isset($form_state['yql_obj']->filtros)){
        $filtros=$form_state['yql_obj']->filtros;
        if(isset($filtros['area']) && !empty($filtros['area'])){
            return $url;
        }else{
            if(yql_hay_filtros_hound($filtros)){
               //Filtro 1 todos
               if(isset($filtros['todos']) && !empty($filtros['todos'])){
                   $url.='&filtros[todos]='.$filtros['todos'];
               }elseif(yql_is_filtros2($filtros)){
                   //Filtro 2
                   $titulo=yql_get_filtros_value($filtros,'titulo');
                   $descripcion=yql_get_filtros_value($filtros,'descripcion');
                   if(!empty($titulo) && !empty($descripcion)){
                       $url.='&filtros[titulo]='.$titulo.'&filtros[descripcion]='.$descripcion.'&filtro[op]='.yql_get_filtros_hound_op($form_state['yql_obj'],'filtrosSI');
                   }else if(!empty($titulo)){
                       $url.='&filtros[titulo]='.$filtros['titulo'];
                   }else if(!empty($descripcion)){
                       $url.='&filtros[descripcion]='.$filtros['descripcion'];
                   }
               }elseif(yql_is_filtros3($filtros)){
                   //Filtro 3
                   $no_titulo=yql_get_filtros_value($filtros,'no_titulo');
                   $no_descripcion=yql_get_filtros_value($filtros,'no_descripcion');
                   if(!empty($no_titulo) && !empty($no_descripcion)){
                       $url.='&filtros[no_titulo]='.$no_titulo.'&filtros[no_descripcion]='.$no_descripcion.'&filtro[op]='.yql_get_filtros_hound_op($form_state['yql_obj'],'filtrosNO');
                   }else if(!empty($no_titulo)){
                       $url.='&filtros[no_titulo]='.$filtros['no_titulo'];
                   }else if(!empty($no_descripcion)){
                       $url.='&filtros[no_descripcion]='.$filtros['no_descripcion'];
                   }
               }elseif(yql_is_filtros4($filtros)){
                   $name1=yql_filtros4_name($form_state['yql_obj'],'campo_contiene');
                   $name2=yql_filtros4_name($form_state['yql_obj'],'campo_no_contiene');
                   $contiene=yql_get_filtros_value($filtros,'contiene');
                   $no_contiene=yql_get_filtros_value($filtros,'no_contiene');
                   if(!empty($contiene) && !empty($no_contiene)){
                       $url.='&filtros['.$name1.']='.$contiene.'&filtros['.$name2.']='.$no_contiene.'&filtro[op]='.yql_get_filtros_hound_op($form_state['yql_obj'],'conjuncion');
                   }else if(!empty($contiene)){
                       $url.='&filtros['.$name1.']='.$contiene;
                   }else if(!empty($descripcion)){
                       $url.='&filtros['.$name2.']='.$no_contiene;
                   }
               } 
            }             
        }        
    }
    /*if(user_access('root')){
        print $url;exit();
    }*/
    return $url;
}
function yql_hay_filtros_hound($filtros){
    $my_array=array('todos','titulo','descripcion','no_titulo','no_descripcion','contiene','no_contiene');
    if(!empty($my_array)){
        foreach($my_array as $i=>$f){
            if(isset($filtros[$f]) && !empty($filtros[$f])){
                return 1;
            }
        }
    }
    return 0;
}
function yql_is_filtros2($filtros){
    if((isset($filtros['titulo']) && !empty($filtros['titulo'])) || (isset($filtros['descripcion']) && !empty($filtros['descripcion']))){
        return 1;
    }
    return 0;
}
function yql_get_filtros_value($filtros,$field){
    if(isset($filtros[$field]) && !empty($filtros[$field])){
        return $filtros[$field];
    }
    return '';
}
function yql_get_filtros_hound_op($yql_obj,$field){
    if(isset($yql_obj->$field) && !empty($yql_obj->$field)){
        return 'and';
    }else{        
        return 'or';
    }
}
function yql_is_filtros3($filtros){
    $no_titulo=yql_get_filtros_value($filtros,'no_titulo');
    $no_descripcion=yql_get_filtros_value($filtros,'no_descripcion');
    if(!empty($no_titulo) || !empty($no_descripcion)){
        return 1;
    }
    return 0;
}
function yql_is_filtros4($filtros){
    $contiene=yql_get_filtros_value($filtros,'contiene');
    $no_contiene=yql_get_filtros_value($filtros,'no_contiene');
    if(!empty($contiene) || !empty($no_contiene)){
        return 1;
    }
    return 0;
}
function yql_filtros4_name($yql_obj,$field){
    if($field=='campo_contiene'){
        if(isset($yql_obj->$field) && !empty($yql_obj->$field)){
            return 'descripcion';
        }else{
            return 'titulo';            
        }
    }else if($field=='campo_no_contiene'){
        if(isset($yql_obj->$field) && !empty($yql_obj->$field)){        
            return 'no_descripcion';
        }else{
            return 'no_titulo';
        }
    }
}
function yql_hound_access(){
    if(yql_is_crear_hound()){
        if(hontza_is_hound_actions()){
            return 1;
        }
        return 0;
    }
    return 1;
}
function yql_wizard_define_url($data,$filter,$yql_obj=''){
  return hontza_yql_wizard_define_url($data,$filter,$yql_obj);
}
//gemini-2014
function yql_is_rss_entry($yql_obj,$yql_url){
    $result=array();
    $result['ok']=0;
    $result['url']='';
    $result['data']='';
    $result['is_json']=0;
    $url='';
    if(isset($yql_obj->field_nombrefuente_canal[0]['value'])){
        $url=$yql_obj->field_nombrefuente_canal[0]['value'];
        //intelsat-2014
        if(hontza_social_is_activado()){    
            if(hontza_social_is_json_url($url)){
                $result['is_json']=1;
                return $result;
            }
        }
        //
        //intelsat-2015
        $is_kimonolabs_json_url=hontza_canal_rss_is_kimonolabs_json_url($url);
        $is_url_json=yql_wizar_is_url_json($yql_obj); 
        $dxml='';
        if(!$is_kimonolabs_json_url && !$is_url_json){
            $dxml = file_get_contents($url);
        }else{
            if($is_kimonolabs_json_url){
                $result['is_json']=1;
                $result['is_kimonolabs_json_url']=$is_kimonolabs_json_url;
            }else if($is_url_json){
                $result['is_json']=1;
                $result['is_url_json']=1;
            }    
            return $result;
        }
        if(!empty($dxml)){
            $data = simplexml_load_string($dxml);
            if(isset($data->entry->content)){
                $result['ok']=1;
                $result['url']=$url;
                $result['data']=$data;                
                return $result;
            }
        }    
    }
    return $result;
}
function yql_entry_previsualizar($data){
        $sets = $data->entry;
        $all=0;
        $all = sizeof($sets);        
        $all=hontza_hound_limit_all($all);
          $output='';
          $title = (string) $data->title;
          $link=yql_get_link_entry($data);
          
          $output.='<div><h3><a href="'.($link.'').'">'.($title.'').'</a></h3><ul>';
          
        for($i=0;$i<$all;$i++){
          $r = $sets[$i];
          $item_title = (string) $r->title;
          $item_link=yql_get_link_entry($r);
          //
          $output.='<li><a href="'.($item_link.'').'">'.($item_title.'').'</a></li>';          
        }
        $output.='</ul></div>';
        return $output;
}
function yql_get_link_entry($row){
    $sets=$row->link;
    $num = sizeof($sets);
    $f='@attributes';
    for($i=0;$i<$num;$i++){
        $r = $sets[$i];
        $att = $r->attributes();
        if($att['rel']=='alternate'){
            return (string) $att['href'];
        }
    }
    return '';
}
//intelsat-2015
function yql_wizard_set_field_canal_source_type($form_state){
    $result=array();
    if(isset($form_state['values']['taxonomia']) && !empty($form_state['values']['taxonomia'])){
        foreach($form_state['values']['taxonomia'] as $tid=>$v){
            if(!empty($v)){
                $row=array();
                $row['value']=$tid;
                $result[]=$row;
            }
        }
    }
    return $result;
}
//intelsat-2015
function yql_wizard_add_mail_channel_form_field(&$form){
    if(is_super_admin()){
        $form['is_canal_correo']=array('#type'=>'checkbox','#title'=>'<b>'.hontza_solr_search_define_mail_channel_label().'</b>');
    }
}
function yql_wizard_is_empty_categorias_tematicas($node){
    if(!isset($node->taxonomy)){
        return 1;
    }
    if(empty($node->taxonomy)){
        return 1;
    }
    return 0;
}
//intelsat-2015
function yql_wizard_add_categoria_indefinida($node){
    $indefinida_tid=my_get_term_data_tid('Categoria indefinida');
    if(!empty($indefinida_tid)){
        $node->taxonomy[$indefinida_tid] = taxonomy_get_term($indefinida_tid);
    }    
}
//intelsat-2016
function yql_wizard_add_mail_from_field(&$canal){
    if(isset($_REQUEST['mail_from']) && !empty($_REQUEST['mail_from'])){
        $canal->mail_from=$_REQUEST['mail_from'];
    }
}
//intelsat-2016
function yql_wizard_add_from_canal_title(&$canal,$dxml){
    if(isset($canal->mail_from) && !empty($canal->mail_from)){
        if(!isset($canal->compartir_documentos_from_canal_title)){
            red_copiar_get_canal_title_importar_rss($canal,$dxml);
        }
    }
}
//intelsat-2016
function yql_wizard_add_compartir_documentos_importar_rss_js($form_state){
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        $js='$(document).ready(function()
			{
                                $("#edit-next").click();                                
			});';
		drupal_add_js($js,'inline');
    }
}
//intelsat-2016
function yql_wizard_all_hide(&$form,$form_state){
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        $form['#attributes']=array('style'=>'display:none;');
    }
}
//intelsat-2016
function yql_wizard_add_field_is_imported(&$canal){
    if(red_copiar_is_copiar_activado()){    
        if(isset($canal->mail_from) && !empty($canal->mail_from)){
            compartir_documentos_add_field_is_imported($canal);
        }
    }    
}
//intelsat-2016
function yql_wizard_is_url_json(){
    if(isset($_REQUEST['url_json']) && !empty($_REQUEST['url_json'])){
        return 1;
    }
    return 0;
}
//intelsat-2016
function yql_wizard_add_url_json_form_field(&$form){
    $json_fields=json_decode(base64_decode($_REQUEST['url_json']));
    $url_json=$json_fields->url_json;
                          $form['fuentes'][1] = array(
				  '#title' => t('Url of Json'),
				  '#type' => 'textarea',
				  //'#description' => t('Url of Json'),
                                  '#default_value'=>$url_json,
                                  '#required'=>TRUE,
				);
                          $form['is_url_json']=array(
                              '#type'=>'hidden',
                              '#default_value'=>1,
                          );
                          $form['json_fields']=array(
                              '#type'=>'hidden',
                              '#default_value'=>$_REQUEST['url_json'],
                          );
                          $form['fuentes']['#prefix']='<div style="display:none">';
                          $form['fuentes']['#suffix']='</div>';                            
}
//intelsat-2016
function yql_wizar_is_url_json($canal){
    if(isset($canal->is_url_json) && !empty($canal->is_url_json)){
        return 1;
    }
    return 0;
}
//intelsat-2016
function yql_wizard_get_field_fuente_canal_value($canal){
    if(isset($canal->field_json_fields) && isset($canal->field_json_fields[0]) && isset($canal->field_json_fields[0]['value'])){
        if(!empty($canal->field_json_fields[0]['value'])){
             $json_fields=hontza_canal_json_decode_json_fields($canal->field_json_fields[0]['value']);
             if(isset($json_fields->url_type) && !empty($json_fields->url_type)){
                 if($json_fields->url_type=='csv'){
                     return 'CSV';
                 }
             }
        }
    }
    return 'JSON';
}
//intelsat-2016
function yql_wizard_is_canal_usuario_exportado_add(&$canal,$data){
    red_copiar_is_canal_usuario_exportado_add($canal,$data);
}