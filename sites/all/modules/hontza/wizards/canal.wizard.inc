<?php 

/**
 * Llamada para crear el formulario multistep
 */
function canal_wizard() {
  //intesal-2015
  hontza_solr_search_is_usuario_lector_access_denied();  
  //  
  $step = arg(2);
  ctools_include('wizard');
  ctools_include('object-cache');
  $form_info = array(
    'id' => 'supercanal_basic',
    'path' => 'crear/canal-supercanal/%step',
    'show trail' => TRUE,
    'show back' => TRUE,
    'show cancel' => TRUE,
    'show return' => FALSE,
    'next text' => t('Next step'),
    'next callback' =>  'canal_next',
    'finish callback' => 'canal_finish',
    'cancel callback' => 'canal_cancel',
    'order' => array(
      'datos_canal' => t('Channel data'),
      'elegir_fuente' => t('Choose Font'),
      'valor_args' => t('Value'),
      'previsualizar' => t("Preview")
    ),
    'forms' => array(
      'datos_canal' => array(
        'form id' => 'datos_canal_form',
      ),
      'elegir_fuente' => array(
        'form id' => 'elegir_fuente_form',
      ),
      'valor_args' => array(
        'form id' => 'valor_args_form'
      ),
      'previsualizar' => array(
        'form id' => 'previsualizar_form'
      ),
    ),
  );
  $form_state = array(
    'cache name' => NULL,
  );
  $canal = ctools_object_cache_get('canal_obj', NULL);
  if (!$canal) {
    global $user;
    $canal = new stdClass();
    $canal->type = 'canal_de_supercanal';
    $canal->uid = $user->uid;
    $canal->status = 1;	
    ctools_object_cache_set('canal_obj', NULL, $canal);
    $step = current(array_keys($form_info['order']));
  }
  //gemini-2014  
  $canal->field_canal_active_refresh[0]['value']=1;
  
  $form_state['canal_obj'] = $canal;
  $output = ctools_wizard_multistep_form($form_info, $step, $form_state);
  return $output;
}

/**
 * Datos generales del canal
 */
function datos_canal_form(&$form, &$form_state) {

  $form['titulo'] = array(
    '#type' => 'textfield',
    '#title' => t('Name of Channel'),
    '#required' => TRUE,
  );

  //Ahora se podran crear dos tipo de super canal dapper, pipe
  $form['tipo'] = array(
    //gemini-2014
    //'#title' => t('Type of source (dapper or yahoo pipes)'),
    '#title' => t('Type of Source'),
    '#type' => 'select',
    '#required' => TRUE,
    //gemini-2014
    //'#options' => array('yahoo pipes', 'dapper'),
    '#options' => array('yahoo pipes'),
    '#default_value' => 2,
  );
  //intelsat-2015-kimonolabs
  if(hontza_canal_rss_is_kimonolabs_activado()){
     $form['tipo']['#options']=array('yahoo pipes','kimonolabs'); 
  }
  //gemini
  $form['responsable_uid']= array(
	   '#title' => t('Main Validator'),
	  '#type' => 'textfield',
	  '#required' => TRUE,	 
	  '#maxlength' => 60,
	  //'#autocomplete_path' => 'user/autocomplete',
	  '#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  '#default_value' => '',
	  //'#weight' => -1,
	);
  $form['responsable_uid2']= array(
	   '#title' => t('Second Validator'),
	  '#type' => 'textfield',
	  //'#required' => TRUE,	 
	  '#maxlength' => 60,
	  //'#autocomplete_path' => 'user/autocomplete',
	  '#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  '#default_value' => '',
	  //'#weight' => -1,
	);
  //$form['apply_open_calais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
  //$form['apply_alchemy']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply Alchemy').'</b>');
  //gemini-2013
  $form['is_validacion_automatica']=array('#type'=>'checkbox','#title'=>'<b>'.t('Automatic validation').'</b>');
  if(hontza_canal_rss_is_visualizador_activado()){
      $form['is_canal_opencalais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
  }
  //intelsat-2015
  red_despacho_vigilancia_add_import_canal_duplicate_news_form_field($form);
  //
  $cat_form_field=create_categorias_tematicas_fieldset(my_get_description_categorias());
  if(!empty($cat_form_field)){
    $form['cat']=$cat_form_field;
  }
  //intelsat-2015
  //if(hontza_grupos_is_activo_pestana('estrategia')){
  if(estrategia_inc_is_show_reto_al_que_responde_html()){
    $form['reto_al_que_responde_id']=array(
      //'#title' => t('Associated Challenge'),
      '#value'=>get_reto_al_que_responde_html(),
   );
  }
  /*if (og_get_group_context()) {
    //Obtener el ID del grupo en el que se esta
    $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s", og_get_group_context()->nid));
    
    //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
    $categorias=taxonomy_get_tree($id_categoria);
	
	//simulatzeko
	//$categorias=array();
	//if(empty($categorias)){  
  	if(!my_hay_categorias(1,$categorias)){	 
	 unset($form['buttons']['next']);
	 form_set_error('categorias', t('There are no categories'));
    }
	//
	
    $form['cat'] = array(
      '#type' => 'fieldset',
      '#title' => t('Categories'),
      //gemini
	  //'#description' => t('<u>Please select at least ONE category</u>'),
      '#description' => t(my_get_description_categorias()),
	  //
	);
	
	//gemini
	$indefinida_tid=my_get_term_data_tid('Categoria indefinida');
	//print 'indefinida_tid='.$indefinida_tid.'<BR>';
	//
	
    foreach ($categorias as $id => $contenido) {
	  //gemini
	  //AVISO::::117 categoria indefinida, solo para importaciones simples, opml, y se asigna por defecto
	  if($contenido->tid==$indefinida_tid){
	  	continue;
      }
	  //
	  //echo print_r($contenido,1);
	  profundidad($contenido->tid);
      $pro=variable_get('profundidad_valor', 0);
      $form['cat'][$contenido->tid] = array(
        '#required' => TRUE,
        '#type' => 'checkbox',
        '#prefix' => '<div class=taxo'. $pro .'>',
        '#suffix' => '</div>', 
        '#title' => $contenido->name,
      );
    }
  }*/
}

function datos_canal_form_validate(&$form, &$form_state) {
  //gemini	  
  my_validate_responsable($form,$form_state);
  //

  //Validar categorias
  $categoria=0;
  foreach ($form_state['values'] as $tid => $cont) {
    //gemini
    //if ($cont!=0 and $tid!='numero_rss') {
    if ($cont!=0 && is_numeric($tid)) {
    //
      $categoria=1;
    }
  }
  //gemini-2014
  //AVISO::::se ha quitado required  
  /*
  if ($categoria==0) {
    form_error($form['cat'], t('You have to select at least one category'));
  }*/
  //gemini-2013
  hontza_canal_validate_reto_al_que_responde($form,$form_state);
}



function datos_canal_form_submit(&$form, &$form_state) {
  $fuente = $form_state['canal_obj'];
  //intelsat-2015-kimonolabs  
  $is_kimonolabs=0;
  if(hontza_canal_rss_is_kimonolabs_activado()){
    //variable fuente en este caso es canal, por eso es el segundo parametro
    if(kimonolabs_is_fuente_kimonolabs('',$fuente)){  
     $is_kimonolabs=1;
    }        
    //$form_state['values']['tipo']=kimonolabs_valor_args_form_set_tipo($form,$form_state);
  }
  $fuente->title = $form_state['values']['titulo'];
  $fuente->tipo = $form_state['values']['tipo'];
  //gemini	  
  $fuente->field_responsable_uid[0]['uid']=my_get_responsable_uid($form_state['values']['responsable_uid']);
  $fuente->field_responsable_uid2[0]['uid']=my_get_responsable_uid($form_state['values']['responsable_uid2']);
  // 
  //alamacenar tipo de fuente en el campo fuente_canal del nodo
  if ($form_state['values']['tipo']==0) {
    $fuente->field_fuente_canal[0]['value']='PIPE';        
  }
  elseif ($form_state['values']['tipo']==1) {
    //intelsat-2015-kimonolabs    
    if(!$is_kimonolabs){
        $fuente->field_fuente_canal[0]['value']='Dapper';
    }else{
        $fuente->field_fuente_canal[0]['value']='Kimonolabs';
    }
  }


   //Almacenar taxonomias seleccionadas
  //echo print_r($form_state['values'],1);exit();
  foreach ($form_state['values'] as $tid => $cont) {
    //if ($cont!=0 && !in_array($tid,array('numero_rss','apply_open_calais','form_token'))) {
    if ($cont!=0 && is_numeric($tid)) {
      //print 'tid='.$tid.'<BR>';
      $fuente->taxonomy[$tid] = taxonomy_get_term($tid);
    }
  }
  //gemini
  $fuente->field_apply_open_calais=array();
  $fuente->field_apply_open_calais[0]['value']=0;
  if(isset($form_state['values']['apply_open_calais']) && !empty($form_state['values']['apply_open_calais'])){
      $fuente->field_apply_open_calais[0]['value']=1;
  }
  $fuente->field_apply_alchemy=array();
  $fuente->field_apply_alchemy[0]['value']=0;
  if(isset($form_state['values']['apply_alchemy']) && !empty($form_state['values']['apply_alchemy'])){
      $fuente->field_apply_alchemy[0]['value']=1;
  }
  $fuente->taxonomy=unset_no_tid($fuente->taxonomy);
  //gemini-2013
  $fuente->field_is_validacion_automatica=array();
  $fuente->field_is_validacion_automatica[0]['value']=0;
  if(isset($form_state['values']['is_validacion_automatica']) && !empty($form_state['values']['is_validacion_automatica'])){
      $fuente->field_is_validacion_automatica[0]['value']=1;
  }
  //gemini-2013
  $fuente->estrategia_responde_array=hontza_canal_get_request_responde_array($is_form_save);
  //gemini
  if(hontza_canal_rss_is_visualizador_activado()){
    $fuente->field_is_canal_opencalais=array();
    $fuente->field_is_canal_opencalais[0]['value']=0;
    if(isset($form_state['values']['is_canal_opencalais']) && !empty($form_state['values']['is_canal_opencalais'])){
        $fuente->field_is_canal_opencalais[0]['value']=1;
    }
  }  
  //
  //intelsat-2015
  red_despacho_vigilancia_add_import_canal_duplicate_news_form_state_values($form_state,$fuente);
  //$form_state['yql_obj'] = $fuente;
  $form_state['canal_obj'] = $fuente;
  //
}

/**
 * Formulario para elegir la fuente del canal
 */
function elegir_fuente_form(&$form, &$form_state) {
  //obtenemos el tipo de supernacal 
  $tip = $form_state['canal_obj']->tipo;
  //si es 0 es yahoopipes si es uno es dapper y lo asociamos a su tipo de contenidos
  //intelsat-2015-kimonolabs
  $where_is_kimonolabs='1';
  if(!hontza_canal_rss_is_kimonolabs_activado()){
     $tipo = ($tip == 0) ? 'supercanal' : 'fuentedapper';
     $where_is_kimonolabs='1';
  }else{
    $tipo='supercanal';
    if($tip==1){
        $where_is_kimonolabs='content_type_supercanal.field_is_kimonolabs_value=1';
    }  
  }
  $grupo = og_get_group_context();
  //Obtenemos las últimas fuentes parametrizables y construímos un array con las opciones teniendo en cuenta el tipo n
  //$order_by='n.created DESC';
  $order_by='n.title ASC';
  
  //intelsat-2015-kimonolabs
  //$result = db_query("SELECT n.nid, title FROM {node} n INNER JOIN {og_ancestry} oa ON n.nid = oa.nid WHERE n.type = '". $tipo ."' AND oa.group_nid = %d ORDER BY ".$order_by, $grupo->nid);
  $result = db_query($sql=sprintf("SELECT n.nid, title FROM {node} n LEFT JOIN {og_ancestry} oa ON n.nid = oa.nid LEFT JOIN {content_type_supercanal} ON n.vid=content_type_supercanal.vid WHERE n.type = '". $tipo ."' AND oa.group_nid = %d AND ".$where_is_kimonolabs." ORDER BY ".$order_by, $grupo->nid));  
  //
  while ($row = db_fetch_object($result)) {
    $opciones[$row->nid] = $row->title;
  }
  $form['super'] = array(
    '#title' => t('Available sources'),
    '#type' => 'fieldset',
  );
  if (!$opciones) {
    $form['super']['fuente'] = array(
      '#type' => 'item',
      '#value' => t('There are no sources'),
    );
    drupal_set_message('No existen fuentes creadas.', 'status');
    drupal_goto('vigilancia');
  }
  else{
  $form['super']['fuente'] = array(
    '#type' => 'select',
    '#title' => t('Choose a source'),
    '#options' => $opciones,
  );
  }
}

function elegir_fuente_form_submit(&$form, &$form_state) {
  $form_state['canal_obj']->supercanal_nid = $form_state['values']['fuente'];      //'#required' => TRUE,
  
  
  //Almacenar el titulo de la fuente en el campo de fuentetitulo_canal del nodo
  $nod=node_load($form_state['values']['fuente']);
  $form_state['canal_obj'] ->field_nombrefuente_canal[0]['value']=$nod->title;
  //gemini-2013
  $form_state['canal_obj'] ->field_nid_fuente_canal[0]['value']=$nod->nid;
  //
 
}

/**
 * Formulario para pasarle un valor a los parámetros
 */
function valor_args_form(&$form, &$form_state) {
  //obtenemos el tipo de supernacal
  $typ = $form_state['canal_obj']->tipo;
  //intelsat-2015-kimonolabs
  if(hontza_canal_rss_is_kimonolabs_activado()){
    $typ=kimonolabs_valor_args_form_set_tipo($form,$form_state);
  }
  //
  if ($typ == 0 ) { 
    $supercanal = node_load($form_state['canal_obj']->supercanal_nid);
    $form_state['canal_obj']->supercanal = $supercanal;
    $form['filtros'] = array(
      '#type' => 'fieldset',
      '#title' => t('Value of Parameters'),
    ); 
    foreach ($supercanal->field_supercanal_args as $id => $arg) {
       //intelsat-2015-kimonolabs
       if(hontza_canal_rss_is_kimonolabs_activado()){
        $arg['value']=kimonolabs_set_arg_value($arg['value'],$supercanal);
       }
       $form['filtros'][$arg['value']] = array(
        '#type' => 'textfield',
        //gemini-2013  
        /*
        '#title' => t('Value of @val Parameter', array('@val' => $arg['value'])),
        '#description' => $supercanal->field_supercanal_args_desc[$id]['value'],*/
        //'#title' => $supercanal->field_supercanal_args_desc[$id]['value'],
        '#title'=>hontza_set_supercanal_argument_title($supercanal,$id),  
      );
    }
  }
  
  //Supercanal Dapper
  else{
    $supercanal = node_load($form_state['canal_obj']->supercanal_nid);
    $form_state['canal_obj']->supercanal = $supercanal;
    
    if ($supercanal->field_fuentedapper_args) {
      //Filtros de caja de texto
      $form['filtros_texto'] = array(
        '#type' => 'fieldset',
        '#title' => t('Text Box Filters'),
      ); 
      foreach ($supercanal->field_fuentedapper_args as $id => $arg) {
        $form['filtros_texto']['filtros'][$arg['value']] = array(
          '#type' => 'textfield',
          '#title' => t('Value of @val Argument', array('@val' => $arg['value'])),
          '#description' => $supercanal->field_fuentedapper_args_desc[$id]['value'],
        );
      }
    }
    if ($supercanal->field_fuentedapper_filtrosnombre) {
      //Filtros de listas de valores
      $form['filtros_lista'] = array(
        '#type' => 'fieldset',
        '#title' => t('Value selection Filters'),
      );
      foreach ($supercanal->field_fuentedapper_filtrosnombre as $id => $arg) {
        //Crear array con las opciones de la lista
        $lista = array();
        $elementos = $supercanal->field_fuentedapper_desplegable[$id]['value'];
        $lineas = explode("\n", $elementos);
        for ( $i=0; $i < count($lineas); $i++ ) {
          $palabras = explode("/", $lineas[$i]);
          $lista[$palabras[0]]= $palabras[1]; 
        }
        $form['filtros_lista']['filtros'][$arg['value']] = array(
          '#type' => 'select',
          '#title' => t('Value of @val Argument', array('@val' => $arg['value'])),
          '#options' => $lista,
          '#description' => $supercanal->field_fuentedapper_filtrosdesc[$id]['value'],
        );
      }
    }
  } 
}

/**
 * Parece ser que el unserialize falla como una escopeta de feria y la peña ofrece esta solución (Mael NO USO)
 */
function mb_unserialize($serial_str) { 
  $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str ); 
  return unserialize($out); 
}


/**
 *
 */
function valor_args_form_submit(&$form, &$form_state) {
  //Supercanal pipe
  //intelsat-2015-kimonolabs
  $supercanal = node_load($form_state['canal_obj']->supercanal_nid);  
  if(hontza_canal_rss_is_kimonolabs_activado()){
    $form_state['canal_obj']->tipo=kimonolabs_valor_args_form_set_tipo($form,$form_state);
  }
  
  if ($form_state['canal_obj']->tipo == 0) {
        foreach ($form_state['canal_obj']->supercanal->field_supercanal_args as $arg) {
           //intelsat-2015-kimonolabs
           $bakup_arg_value=$arg['value']; 
            if(hontza_canal_rss_is_kimonolabs_activado()){
             $arg['value']=kimonolabs_set_arg_value($arg['value'],$supercanal);
            }            
           //gemini-2014    
           if(!empty($arg['value'])){
               //intelsat-2015-kimonolabs
               //$form_state['canal_obj']->$arg['value'] = urlencode($form_state['values'][$arg['value']]);
               $form_state['canal_obj']->$bakup_arg_value = urlencode($form_state['values'][$arg['value']]);
           }     
        }
    
    /*
    //gemini-2013
    if(is_super_admin()){
        echo print_r($form_state['canal_obj'],1);
        exit();
    }
    //
     * 
     */
  }
  //Supercanal Dapper
  else{
    foreach ($form_state['values'] as $id=>$contenido){
      if($id!='previous' and $id!='op' and $id!='next' and $id!='cancel' and $id!='form_build_id' and$id!='form_token' and $id!='form_id' ){
      //$filtros.='&v_'.$id.'='.$contenido;
        $filtros.='&v_'.$id.'='.urlencode($contenido);

      }
    }
    /*
    //gemini-2013
    if(is_super_admin()){
        print 'filtros===='.$filtros;exit();
    }
     * 
     */
    $form_state['canal_obj']->filtros = $filtros;
    
  }
}

/**
 * Previsualización del canal.
 */
function previsualizar_form(&$form, &$form_state){
  //gemini-2013
  $_SESSION['url_show_validar_canal']=array();
  //
  $nodo = $form_state['canal_obj']->supercanal;
  //intelsat-2015
  if(hontza_canal_rss_is_kimonolabs_activado()){
    $form_state['canal_obj']->tipo=kimonolabs_valor_args_form_set_tipo($form,$form_state);
  }
  //
  //Supercanal Pipe  
  if($form_state['canal_obj']->tipo == 0){
    //intelsat-2015-kimonolabs
    $is_kimonolabs=0;
    if(hontza_canal_rss_is_kimonolabs_activado()){
        if(kimonolabs_is_fuente_kimonolabs($nodo)){
            $is_kimonolabs=1;
        }        
    }
    if(!$is_kimonolabs){
    //    
        $pipe_id = $nodo->field_supercanal_fuente[0]['value'];
        $pipe_params = '';
        foreach ($nodo->field_supercanal_args as $arg){
          //gemini-2013  
          //$pipe_params .= '"'.$arg['value'].'":"'.$form_state['canal_obj']->$arg['value'].'",';
          //gemini-2014    
          if(!empty($arg['value'])){    
            $pipe_params .= '"'.$arg['value'].'":"'.set_pipe_param($form_state['canal_obj']->$arg['value']).'",';
          }
          //
        }
        //
        $my_url=get_url_rss_yahoo_pipes($nodo,$form_state['canal_obj']);
        $_SESSION['url_show_validar_canal']=array();
        $_SESSION['url_show_validar_canal']['url']=$my_url;
        $_SESSION['url_show_validar_canal']['fuente_nid']=$nodo->nid;
       /*
        //gemini-2013
        //simulando
        if(is_super_admin()){
            print $pipe_id.'===='.$pipe_params.'<BR>';exit();
        }
        //
        * 
        */
        $output .='<script src="http://l.yimg.com/a/i/us/pps/listbadge_1.3.js">{"pipe_id":"'.$pipe_id.'","_btype":"list","pipe_params":{'.$pipe_params.'}}</script>';
        //gemini-2013
        /*if(strpos($pipe_params,'+')){
          $form['mensaje'] = array(
          '#type' => 'item',
          '#value' => 'La previsualizaci&oacute;n no puede mostrarse, pero el PIPE puede ser alamacenado correctamente.',
        );
        }
        else{
          $form['valor_param'] = array(
          '#type' => 'item',
          '#value' => $output
        );
        }*/
    }else{
        //intelsat-2015-kimonolabs
        if(hontza_canal_rss_is_kimonolabs_activado()){
            $output=kimonolabs_get_previsualizacion_canal_html($nodo,$form,$form_state);
        }            
    }
    $form['valor_param'] = array(
      '#type' => 'item',
      '#value' => $output
    );
    
    //
    
    
  }
  //Supercanal Dapper
  else{
    /*
    //gemini-2013  
    if(is_super_admin()){  
        print 'filtros==1234=='.$form_state['canal_obj']->filtros;exit();  
    }
    //
     * 
     */        
    $output ='http://open.dapper.net/transform.php?dappName='.$nodo->title.'&transformer='.$nodo->field_fuentedapper_fuente[0]['value'].$nodo->field_fuentedapper_extraargs[0]['value'].$form_state['canal_obj']->filtros;
    //gemini-2013
    //$my_url=get_url_rss_yahoo_pipes($nodo,$form_state['canal_obj']);
    $_SESSION['url_show_validar_canal']=array();
    $_SESSION['url_show_validar_canal']['url']=$output;
    $_SESSION['url_show_validar_canal']['fuente_nid']=$nodo->nid;
    //
    $enlace="<A HREF=".$output." TARGET='_blank'>Previsualizar en ventana nueva</A>";
    $form_state['canal_obj']->auxenlace=$output;
    $form['valor_param'] = array(
      '#type' => 'item',
      '#value' => $enlace,
      '#prefix' => '<div class="rss-dapper">',
      '#suffix' => '</div>',

    ); 
    
  }
  //gemini-2013
  /*if(is_super_admin()){
    $form['buttons']['url_show_validar_canal']['#value']=l(t('Show RSS'),'show_validar_canal', array('attributes' => array('target' => '_blank')));
  }*/
  //
}


function previsualizar_form_validate(&$form, &$form_state){
  
  //Supercanal Pipe
  $nodo = $form_state['canal_obj']->supercanal;
  
  //Supercanal Pipe
  if($form_state['canal_obj']->tipo == 0){
    foreach ($nodo->field_supercanal_args as $arg){
        if(!empty($form_state['canal_obj']->$arg['value'])){                
            $params .= $arg['value'].'='.$form_state['canal_obj']->$arg['value'].'&';
            //$params .= $arg['value'].'='.urlencode($form_state['canal_obj']->$arg['value']).'&'; 
        }             
    }
        
    $params = substr($params, 0, strlen($params)-1);
        
    $pipe_id = $nodo->field_supercanal_fuente[0]['value']; 


    $url ='http://pipes.yahoo.com/pipes/pipe.run?_id='.$pipe_id.'&_render=rss&'.$params;
    
    /*
    //gemini-2013
    if(is_super_admin()){
        print 'url validate='.$url;exit();
    }
     * 
     */
     
    //intelsat-2015-kimonolabs
    $is_kimonolabs=0;
    if(hontza_canal_rss_is_kimonolabs_activado()){
        if(kimonolabs_is_fuente_kimonolabs($nodo)){
            $is_kimonolabs=1;
        }
    }    
    //intelsat-2015-kimonolabs
    if(!$is_kimonolabs){
        $dxml = file_get_contents($url);
        $dxml1 = file_get_contents($url);

        $data = simplexml_load_string($dxml);

        $sets = $data->channel->item;
        $all = sizeof($sets);

        $data1 = simplexml_load_string($dxml1);

        $sets1 = $data1->channel->item;
        $all1 = sizeof($sets1);


        for($i=0;$i<$all;$i++){
          if($i+2==$all){
            if(md5($sets[$i]->link)==md5($sets1[$i]->link)){
            }
            else{

              drupal_set_message('No se ha podido crear el canal: '.$form_state['canal_obj']->title.'. La fuente PIPE no se ha podido procesar.', 'error');
              drupal_goto('vigilancia');
            }

          }
        } 
    }
  }  
}

/**
 *
 */
function previsualizar_form_submit(&$form, &$form_state){
  $nodo = $form_state['canal_obj']->supercanal;
  
  //Supercanal Pipe
  //intelsat-2015
  if(hontza_canal_rss_is_kimonolabs_activado()){
    $form_state['canal_obj']->tipo=kimonolabs_valor_args_form_set_tipo($form,$form_state);
  }
  //
  if($form_state['canal_obj']->tipo == 0){
    foreach ($nodo->field_supercanal_args as $arg){
        if(!empty($form_state['canal_obj']->$arg['value'])){
            $params .= $arg['value'].'='.$form_state['canal_obj']->$arg['value'].'&';
            //$params .= $arg['value'].'='.urlencode($form_state['canal_obj']->$arg['value']).'&'; 
        }
        //
    }
    $params = substr($params, 0, strlen($params)-1);
    /*
    //gemini-2013
    //$params=urlencode($params);
    //
     * 
     */
    
    $pipe_id = $nodo->field_supercanal_fuente[0]['value']; 
    
    /*
    //gemini-2013
    if(is_super_admin()){
        print 'http://pipes.yahoo.com/pipes/pipe.run?_id='.$pipe_id.'&_render=rss&'.$params;exit();
    }
     * 
     */
    
    //intelsat-2015-kimonolab    
    $is_kimonolabs=0;
    if(hontza_canal_rss_is_kimonolabs_activado()){
        if(kimonolabs_is_fuente_kimonolabs($nodo)){
            $is_kimonolabs=1;
        }        
    }
    if(!$is_kimonolabs){        
    	$form_state['canal_obj']->feeds = array('FeedsHTTPFetcher' => array('source' => 'http://pipes.yahoo.com/pipes/pipe.run?_id='.$pipe_id.'&_render=rss&'.$params));
    }else{
        kimonolabs_add_canal_feeds($nodo,$params,$form_state);
    }    
  }
  //Supercanal Dapper
  else{
    $form_state['canal_obj']->feeds = array('FeedsHTTPFetcher' => array('source' =>  $form_state['canal_obj']->auxenlace));
  }
}

/**
 *
 */
function canal_next(&$form_state){
  $canal = $form_state['canal_obj'];
  $cache = ctools_object_cache_set('canal_obj', $form_state['cache name'], $canal);
}

/**
 *
 */
function canal_finish(&$form_state){
  //echo print_r($form_state['canal_obj'],1);exit();
  if(is_add_canal_taxonomy_no_selected($form_state['canal_obj']->taxonomy)){
  	$indefinida_row=my_get_term_data('Categoria indefinida');
    //echo print_r($indefinida_row,1);exit();
    if(isset($indefinida_row->tid) && !empty($indefinida_row->tid)){
		$form_state['canal_obj']->taxonomy[$indefinida_row->tid]=$indefinida_row;		
	}
  }
  //
  /*if(user_access('root')){
    echo print_r($form_state['canal_obj'],1);exit();
  }*/
  
  $estrategia_responde_array=array();
  if(isset($form_state['canal_obj']->estrategia_responde_array)){
      $estrategia_responde_array=$form_state['canal_obj']->estrategia_responde_array;
  }
  //gemini-2014  
  $form_state['canal_obj']->field_canal_active_refresh[0]['value']=1;
  //intelsat-2014
  if(hontza_solr_is_solr_activado()){
    $form_state['canal_obj']->field_canal_source_type_updated[0]['value']=1;
  }              
  //
  node_save($form_state['canal_obj']);  
  
  $canal = $form_state['canal_obj'];
  $canal->estrategia_responde_array=$estrategia_responde_array;
  
  hontza_canal_save_reto_al_que_responde($canal);
  
  //intelsat-2014
  hontza_solr_save_canal_source_type($canal);
  //
  
  /*if(user_access('root')){
      $canal_prueba=node_load($canal->nid);
      echo print_r($canal_prueba,1);
      exit();
  }*/
  
  //
  //gemini
  //$form_state['redirect'] = 'node/'.$canal->nid;
  $form_state['redirect'] = 'node/'.$canal->nid.'/import';
  //
  ctools_object_cache_clear('canal_obj', $form_state['cache name']); 
}

/**
 *
 */
function canal_cancel(&$canal){
  //$form_state['redirect'] = 'node';
  //drupal_goto('vigilancia');
  ctools_object_cache_clear('canal_obj', $form_state['cache name']);
  hontza_goto_vigilancia_default();
}
//gemini-2013
function get_url_rss_yahoo_pipes($nodo,$canal_obj){
    $params='';
    if(isset($nodo->field_supercanal_args) && !empty($nodo->field_supercanal_args)){
        foreach ($nodo->field_supercanal_args as $arg){
           //gemini-2014    
           if(!empty($arg['value'])){   
            $params .= $arg['value'].'='.$canal_obj->$arg['value'].'&';
           }
        }
    }
    $params = substr($params, 0, strlen($params)-1);
    $pipe_id = $nodo->field_supercanal_fuente[0]['value'];


    $url ='http://pipes.yahoo.com/pipes/pipe.run?_id='.$pipe_id.'&_render=rss&'.$params;
    //
    return $url;
}
//gemini-2013
function set_pipe_param($value_in){
    $value=addslashes(urldecode($value_in));    
    return $value;
}