<?php
function hontza_solr_simulatu_facet_callback(){
    return 'Funcion desactivada';
    /*$types=array('item');
    $nodes=get_all_nodes($types);
    foreach($nodes as $i=>$row){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){    
           $canal_nid=$node->field_item_canal_reference[0]['nid'];
           db_query('UPDATE {content_type_item} SET field_canal2_nid=%d WHERE nid=%d AND vid=%d',$canal_nid,$node->nid,$node->vid);
        }    
    }
    return date('Y-m-d H:i:s');*/
}
function hontza_solr_get_reference($nid){
    if(!empty($nid)){
       $node=node_load($nid);
       echo print_r($node,1);
       exit();
    }
    return '';
}
function hontza_solr_perm($result_in){
    $result=$result_in;
    $result[]='access custom solr';
    return $result;
}
function hontza_solr_menu_items($items_in){
    $items=$items_in;
    $items['hontza_solr/busqueda_avanzada']=array(
    'title'=>t('Advanced Search'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_solr_busqueda_avanzada_form'),  
    //'access arguments' => array('access custom solr'),
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/canal/autocomplete']=array(
    'title'=>t('Channel'),
    'page callback' => 'hontza_solr_canal_autocomplete_callback',    
    //'access arguments' => array('access custom solr'),
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_social/simular_social_add_recurso']=array(
    'title'=>t('Channel'),
    'page callback' => 'hontza_social_simular_add_recurso_callback',    
    'access callback' => TRUE,    
    );
    $items['hontza_solr/search_saved']=array(
    'title'=>t('Search'),
    'page callback' => 'hontza_solr_search_saved_callback',    
    //'access arguments' => array('access custom solr'),
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/busqueda_simple']=array(
    'title'=>t('Simple Search'),
    'page callback' => 'hontza_solr_busqueda_simple_callback',    
    //'access arguments' => array('access custom solr'),
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_social/simular_social_auth']=array(
    'title'=>t('Authentication'),
    'page callback' => 'hontza_social_simular_social_auth_callback',    
    'access arguments' => array('root'),   
    );
    $items['hontza_solr/simular_hontza_solr_actualizar_item_canal_category_tid']=array(
    'title'=>t('Update'),
    'page callback' => 'hontza_solr_simular_hontza_solr_actualizar_item_canal_category_tid_callback',    
    'access arguments' => array('root'),    
    );
    $items['hontza_social/simular_get_resource_id']=array(
    'title'=>t('Search'),
    'page callback' => 'hontza_social_simular_get_resource_id_callback',    
    'access arguments' => array('root'),   
    );   
    $items['hontza_solr/filename/autocomplete']=array(
    'title'=>t('Channel'),
    'page callback' => 'hontza_solr_filename_autocomplete_callback',    
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/busqueda_avanzada_solr']=array(
    'title'=>t('Advanced Search'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_solr_busqueda_avanzada_form'),  
    //'access arguments' => array('access custom solr'),
    'access callback' => 'hontza_solr_my_access',    
    );
    $items=hontza_solr_funciones_menu_items($items);
    return $items;
}
function hontza_solr_busqueda_avanzada_form(){
    //AVISO::::si dá error unsoported operand types es que falta el permiso "Search content" en los usuarios que no son "admin" 
    hontza_solr_search_unset_bookmarks_rows();
    drupal_set_title(t('Advanced Search'));
    $form=array();
    //intelsat-2016
    $form['index_status_html']=array(
        '#value'=>red_solr_inc_get_index_status_html(),
        '#prefix'=>'<div style="padding-right:10px;">',
        '#suffix'=>'</div>',        
    );
    //intelsat-2016
    //$width_main='45%';
    $width_main='48%';
    $prefix_main='<div style="float:left;width:100%;"><div style="float:left;width:'.$width_main.';padding-left:5px;">';
    $suffix_main='</div>';
    $prefix_right='<div style="float:left;width:'.$width_main.';padding-left:10px;">';
    $suffix_right='</div></div>';
    $prefix_right2='<div style="float:left;width:15%;padding-left:10px;">';
    $suffix_right2='</div>';
    $prefix_right3='<div style="float:left;width:28%;padding-left:10px;">';
    $suffix_right3='</div></div>';
    //
    //intelsat-2016-noticias-usuario
    $div_tag='<div style="float:left;width:48%;">';
    $div_tipo_noticia='<div style="float:left;width:48%;padding-left:10px">';
    //intelsat-2015
    $prefix_main_canal_linea='<div style="float:left;width:100%;"><div style="float:left;width:45%;padding-left:5px;">';        
    if(!red_solr_inc_is_status_activado()){
        $prefix_right2_canal_linea='<div style="float:left;width:15%;padding-left:10px;">';            
        $prefix_right3_canal_linea='<div style="float:left;width:33%;padding-left:20px;">';
    }else{
        $prefix_right2_canal_linea='<div style="float:left;width:25%;padding-left:10px;">';            
        $prefix_right3_canal_linea='<div style="float:left;width:23%;padding-left:20px;">';
    }
    //$fecha_attributes=array('style'=>'padding-top:5px;');
    $prefix_fecha='<div style="float:left;padding-top:5px;">';
    $suffix_fecha='</div>';
    
    $form['in_any_field']=array(
        '#type'=>'textfield',
        '#title'=>t('In any field'),
        //'#prefix'=>'<div style="float:left;width:100%;"><div style="float:left;width:45%;padding-left:5px;">',
        '#prefix'=>$prefix_main,
        //'#attributes'=>array('style'=>'float:left;width:50%'),
        //'#suffix'=>'</div>',
        '#suffix'=>$suffix_main,
    );
    $form['tag']= array(
	  '#title' => t('Tag'),
	  '#type' => 'textfield',
	  '#autocomplete_path' => 'taxonomy/autocomplete/is_busqueda',
	  '#default_value' => '',
          '#prefix'=>$prefix_right.$div_tag,
          '#suffix'=>'</div>',  
    );
    //intelsat-2016-noticias-usuario
    $form['tipo_noticia'] = array(
        '#type' => 'select',
        '#title'=>t('News type'),  
        '#options' => red_solr_inc_get_tipo_noticia_options(),
        //'#prefix'=>'<div class="solr_busqueda_avanzada_buttons"><div style="float:left;margin-top:-20px;">',
        //'#suffix'=>'</div>',
        '#prefix'=>$div_tipo_noticia,
        '#suffix'=>'</div>'.$suffix_right,  
        //'#attributes'=>array('style'=>'float:left;'), 
    );
    $form['in_the_title']=array(
        '#type'=>'textfield',
        '#title'=>t('In the title'),
        '#prefix'=>$prefix_main,
         '#suffix'=>$suffix_main,
    );
    $form['in_the_description']=array(
        '#type'=>'textfield',
        '#title'=>t('In the description'),
        /*'#prefix'=>$prefix_main,
        '#suffix'=>$suffix_main,*/
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
    );
    /*    
    $form['not_in_the_title']=array(
        '#type'=>'textfield',
        '#title'=>t('Not in the title'),
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
    );        
    $form['not_in_the_description']=array(
        '#type'=>'textfield',
        '#title'=>t('Not in the description'),
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
    );
    */
    //intelsat-2015
    //hontza_solr_funciones_add_tipo_categoria_validate_status_form_field($form);
    hontza_solr_funciones_add_categoria_tipo_scoring_form_field($form);
        
    $form['canal_title']= array(
	  '#title' => t('Channel'),
	  '#type' => 'textfield',
	  '#autocomplete_path' =>'hontza_solr/canal/autocomplete',
	  '#default_value' => '',
          '#prefix'=>$prefix_main_canal_linea,
          '#suffix'=>$suffix_main,
    );
      
    $form['item_validador']= array(
	  '#title' => t('Validator'),
	  '#type' => 'textfield',
	  '#maxlength' => 60,
	  '#autocomplete_path' =>'userreference/autocomplete',
	  '#default_value' => '',
	  /*'#prefix'=>$prefix_right3,
          '#suffix'=>$suffix_right3,*/
          '#prefix'=>$prefix_right2_canal_linea,
          '#suffix'=>$suffix_right2,  
	); 
    
    //intelsat-2015
    if(!red_solr_inc_is_status_activado()){
        $validate_status_array=hontza_solr_funciones_get_status_options();
        $form['validate_status']= array(
              '#title' => t('Status'),
              '#type' => 'select',
              '#options' => $validate_status_array,
              '#default_value' =>0,
              '#prefix'=>$prefix_right3_canal_linea,
              '#suffix'=>$suffix_right3,   
        );
    }else{    
        //intelsat-2015
        red_solr_inc_add_multiple_validate_status_form_field($prefix_right3_canal_linea,$suffix_right3,$form);
    } 
    if(hontza_solr_is_filename_activado()){
        //intelsat-2015
        $fid_style=red_solr_inc_get_fid_style();
        $fid_div_style=red_solr_inc_get_fid_div_style();
        $form['item_fid']= array(
              '#title' => t('Name of Attachment'),
              '#type' => 'textfield',
              '#autocomplete_path' => 'hontza_solr/filename/autocomplete',
              '#default_value' => '',
              //intelsat-2015
              '#attributes'=>$fid_style,
              //intelsat-2015  
              '#prefix'=>$fid_div_style,
              '#suffix'=>'</div>',    
        );
    }         
     
    $fecha_inicio='';
    $fecha_fin='';
    $form['solr_busqueda_fecha_inicio']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('Start date'),                          
			'#default_value'=>  $fecha_inicio);
    $form['solr_busqueda_fecha_fin']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('End date'),                         
			'#default_value'=>$fecha_fin);

    //intelsat-2016
    if(red_solr_inc_is_actualizar_noticias_usuario()){        
        $form['my_order'] = array(
        '#type' => 'select',
        '#title'=>t('Order'),  
        '#options' => red_solr_inc_get_my_order_options(),
        //'#prefix'=>'<div style="float:left;margin-top:-20px;padding-left:20px;">',
        //'#suffix'=>'</div></div>',
        '#prefix'=>'<div class="solr_busqueda_avanzada_buttons" style="padding-bottom:40px;">',
        //'#prefix'=>'<div class="solr_busqueda_avanzada_buttons" style="display:none;">',
        '#suffix'=>'</div>',    
        '#attributes'=>array('style'=>'float:left;'), 
        );
    }
    
    $form['search_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Search'),
        '#prefix'=>'<div class="solr_busqueda_avanzada_buttons">',        
    );
    $destination='vigilancia/pendientes';
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),$destination),
        '#suffix'=>'</div>',  
    );
    return $form;
}
function hontza_solr_busqueda_avanzada_form_submit($form,$form_state){    
    $query='';
    $url='my_solr/my_search';        
    $my_grupo=og_get_group_context();
    $my_array=array();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $i=count($my_array);
        $my_array[]='f['.$i.']=im_og_gid:'.$my_grupo->nid;
    }
    //
    $i=count($my_array);
    //
    //intelsat-2016-noticias-usuario
    /*
    if(!red_solr_inc_is_actualizar_noticias_usuario()){
        hontza_solr_funciones_add_validate_status_filter($my_array,$form_state['values']['validate_status']);
    }*/
    $noticias_usuario_search_array=array();
    //
    //intelsat-2015
    //intelsat-2016-noticias-usuario
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        if(red_solr_inc_is_status_activado()){
            red_solr_inc_add_multiple_validate_status_filter($noticias_usuario_search_array,$form_state);
        }else{
            hontza_solr_funciones_add_validate_status_filter($noticias_usuario_search_array,$form_state['values']['validate_status']);
        }
    }else{
        if(red_solr_inc_is_status_activado()){
            red_solr_inc_add_multiple_validate_status_filter($my_array,$form_state);
        }else{
            hontza_solr_funciones_add_validate_status_filter($my_array,$form_state['values']['validate_status']);
        }    
    }
    $search_array=array();
    $in_any_field=$form_state['values']['in_any_field'];
    if(!empty($in_any_field)){
        //$search_array[]='(label:'.$in_any_field.' OR '.'content:'.$in_any_field.')';
        $search_array[]=$in_any_field;
    }
    $in_the_title=$form_state['values']['in_the_title'];
    if(!empty($in_the_title)){
        $search_array[]='(label:'.$in_the_title.')';
    }
    $in_the_description=$form_state['values']['in_the_description'];
    if(!empty($in_the_description)){
        $search_array[]='(content:'.$in_the_description.')';
    }
    $not_in_the_title=$form_state['values']['not_in_the_title'];
    if(!empty($not_in_the_title)){
        $search_array[]='(-label:'.$not_in_the_title.')';
    }
    $not_in_the_description=$form_state['values']['not_in_the_description'];
    if(!empty($not_in_the_description)){
        $search_array[]='(-content:'.$not_in_the_description.')';
    }
    $canal_nid=hontza_solr_get_canal_nid_by_title($form_state['values']['canal_title']);
    if(!empty($canal_nid)){
        $i=count($my_array);
        $my_array[]='f['.$i.']=im_field_item_canal_reference:'.$canal_nid;
    }/*else{
        if(!empty($canal_nid_array)){
            $my_array=hontza_solr_set_query_canal_nid_array($canal_nid_array,$my_array);       
        }
    }*/
    $tid=hontza_solr_get_tid_by_term_name($form_state['values']['tag']);
    if(!empty($tid)){
        $i=count($my_array);
        $my_array[]='f['.$i.']=im_taxonomy_vid_3:'.$tid;
    }
    //intelsat-2015
    $commented=red_solr_inc_get_commented($form_state);
    if(!empty($commented)){
        $s='is_comment_count:[1 TO *]';
        if($commented==2){
            $s='-'.$s;
        }
        $search_array[]='('.$s.')';
    }    
    //
    //intelsat-2015
    //intelsat-2016-noticias-usuario-solr
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        red_solr_inc_add_rated_filter($noticias_usuario_search_array,$form_state);
    }else{
        red_solr_inc_add_rated_filter($my_array,$form_state);
    }
    $fuente_tipo=$form_state['values']['fuente_tipo'];
    if(!empty($fuente_tipo)){
        $i=count($my_array);
        $my_array[]='f['.$i.']=itm_field_item_source_tid:'.$fuente_tipo;
    }
    //intelsat-2016-noticias-usuario-solr
    red_solr_inc_add_tipo_noticia_query($form_state,$noticias_usuario_search_array);
        
    $categorias_canal=$form_state['values']['categorias_canal'];
    if(!empty($categorias_canal)){
        $i=count($my_array);
         $my_array[]='f['.$i.']=itm_field_item_canal_category_ti:'.$categorias_canal;
    }
    //
    $uid=hontza_solr_get_uid_by_username($form_state['values']['item_validador']);
    if(!empty($uid)){
        $i=count($my_array);
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            //$my_array[]='f['.$i.']=(im_field_item_validador_uid:'.$uid.' OR im_field_noticia_validador_uid:'.$uid.')';
            $noticias_usuario_search_array[]='(im_field_item_validador_uid:'.$uid.' OR im_field_noticia_validador_uid:'.$uid.')';
        }else{
            $my_array[]='f['.$i.']=im_field_item_validador_uid:'.$uid;
        }    
    }
    //
    $search_fechas=hontza_solr_get_search_fechas($form_state['values']['solr_busqueda_fecha_inicio'],$form_state['values']['solr_busqueda_fecha_fin']);
    if(!empty($search_fechas)){
        $search_array[]='('.$search_fechas.')';
    }
    //
    if(hontza_solr_is_filename_activado()){
        $fid=hontza_solr_get_fid_by_filename($form_state['values']['item_fid']);
        if(!empty($fid)){
            $i=count($my_array);
            $my_array[]='f['.$i.']=itm_field_item_fid:'.$fid;
        }
    }
    //
    //intelsat-2016-noticias-usuario
    $search_array=red_solr_inc_busqueda_avanzada_form_submit_search_array($search_array,$noticias_usuario_search_array);
    if(!empty($search_array)){        
        $url=$url.'/'.implode(' AND ',$search_array);
    }
    $query=implode('&',$my_array);
    if(!empty($query)){
        //intelsat-2016
        //$query.='&solrsort=ds_created desc';
        $query.=red_solr_inc_get_query_solrsort($form_state);
    }
    /*if(!empty($query)){
        //$url=urlencode($url.'?'.$query);
        //$url=url($url);
        //$url=$url.drupal_encode_path('?'.$query);
        //$url=$url.'?'.$query;
    }*/
    //AVISO:::: mirar esto https://www.drupal.org/node/1688150
    //print $url;exit();
    $_SESSION['solr_is_show_save_search']=1;
    //intelsat-2016
    //red_solr_inc_set_my_order($form_state['values']['my_order']);
    drupal_goto($url,$query);
}
function hontza_solr_canal_autocomplete_callback($string){
    $result=array();
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    $my_array=array();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    $where[]='node.type IN("canal_de_yql","canal_de_supercanal")';
    //$where[]='LOWER(node.title) LIKE LOWER("'.$string.'%")';
    $where[]='LOWER(node.title) LIKE "%%%s%%"';
    $sql='SELECT * FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid WHERE '.implode(' AND ',$where);
    $res=db_query($sql,strtolower($string));
    while ($row = db_fetch_object($res)) {
        $title=check_plain($row->title);
        $result[$row->title] = $title;
    }
    drupal_json($result);
}
function hontza_solr_get_canal_nid_by_title($canal_title){
    if(!empty($canal_title)){
        $where=array();
        $where[]='1';
        $my_grupo=og_get_group_context();
        $my_array=array();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        }
        $where[]='node.type IN("canal_de_yql","canal_de_supercanal")';
        //$where[]='node.title="%s"';
        $where[]='node.title="'.$canal_title.'"';
        $sql='SELECT * FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid WHERE '.implode(' AND ',$where);
        //print $sql;exit();
        $res=db_query($sql);
        while ($row = db_fetch_object($res)) {
            return $row->nid;
        }
    }
    return '';
}
function hontza_solr_get_tid_by_term_name($tag,$vid=3){
    if(!empty($tag)){
        $my_grupo=og_get_group_context();
        $grupo_nid=0;
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid=$my_grupo->nid;
        }
        $result = db_query($sql=sprintf("SELECT t.tid, t.name 
        FROM {term_data} t 
        LEFT JOIN {term_node} tn ON t.tid=tn.tid 
        LEFT JOIN {og_ancestry} og_ancestry ON tn.nid=og_ancestry.nid 
        WHERE og_ancestry.group_nid=%d AND t.vid = %d AND t.name='%s' 
        GROUP BY t.tid 
        ORDER BY t.name ASC",$grupo_nid,$vid, $tag));
        $res=db_query($sql);
        while ($row = db_fetch_object($res)) {
            return $row->tid;
        }
    }    
    return '';
}
function hontza_solr_get_search_fechas($ini,$end){
    if(empty($ini) && empty($end)){
        return '';
    }
    $solr_ini='*';
    if(!empty($ini)){
        $time_ini=strtotime($ini);
        $solr_ini=date('Y-m-d\TH:i:s\Z',$time_ini);
    }
    $solr_end='*';
    if(!empty($end)){
        $time_end=strtotime($end);
        $time_end=$time_end+(24*60*60)-1;    
        $solr_end=date('Y-m-d\TH:i:s\Z',$time_end);
    }
    return 'ds_created:['.$solr_ini.' TO '.$solr_end.']';
}
function hontza_solr_is_solr_activado(){
    $sareko_id_array=array('ROOT');
    //simulando
    $sareko_id_array[]='LOKALA';
    //
    if(in_array(_SAREKO_ID,$sareko_id_array)){
        return 1;    
    }
    if(hontza_solr_search_is_solr_activado_admin()){
        return 1;
    }
    if(defined('_IS_SOLR')){
        if(_IS_SOLR){
            return 1;
        }
    }
    if(red_dashboard_is_activado()){
        return 1;
    }
    return 0;
}
function hontza_solr_is_busqueda_avanzada_pantalla(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='hontza_solr'){
        $param1=arg(1);
        //if(!empty($param1) && $param1=='busqueda_avanzada'){
        if(!empty($param1) && $param1=='busqueda_avanzada_solr'){
            return 1;
        }                
    }    
    return 0;
}
function hontza_solr_get_pre_canal_nid_array($tid,$canal_nid_array_in,$fuente_tid){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'", $my_grupo->nid));
        hontza_get_canales_por_categoria($id_categoria, $tid);    
        $result=hontza_get_canales_por_categoria($id_categoria, $tid);
    }
    if(!empty($fuente_tid) && !empty($canal_nid_array_in)){
        if(empty($result)){
            return $canal_nid_array_in;
        }
        $result=array_intersect($canal_nid_array_in,$result);
    }
    return $result;
}
function hontza_solr_set_query_canal_nid_array($canal_nid_array,$my_array){
    $result=$my_array;
    if(!empty($canal_nid_array)){
        foreach($canal_nid_array as $a=>$canal_nid){
            $i=count($result);
            $result[]='f['.$i.']=im_field_item_canal_reference:'.$canal_nid;     
        }
    }
    return $result;
}
function hontza_solr_get_pre_fuente_tipo_canal_nid_array($tid){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $fuente_nid_array=hontza_solr_get_pre_fuente_tipo_nid_array($tid);
        if(!empty($fuente_nid_array)){    
            $where=array();
            $where[]='1';
            $where[]='node.type IN("canal_de_supercanal","canal_de_yql")';
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
            $where[]='content_field_nid_fuente_canal.field_nid_fuente_canal_value IN('.implode(',',$fuente_nid_array).')';
            $sql='SELECT * 
            FROM {node} node 
            LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid 
            LEFT JOIN {content_field_nid_fuente_canal} content_field_nid_fuente_canal ON node.vid=content_field_nid_fuente_canal.vid 
            WHERE '.implode(' AND ',$where);
            $res=db_query($sql);
            while($row=db_fetch_object($res)){
                $result[]=$row->nid;
            }
        }    
    }    
    return $result;    
}
function hontza_solr_get_pre_fuente_tipo_nid_array($tid){    
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $vid=1;
        $where=array();
        $where[]='1';
        $where[]='node.type IN("supercanal","fuentedapper","fuentehtml")';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $where[]='term_node.tid='.$tid;
        $where[]='term_data.vid='.$vid;        
        $sql='SELECT * 
        FROM {node} node 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid 
        LEFT JOIN {term_node} term_node ON node.vid=term_node.vid 
        LEFT JOIN {term_data} term_data ON term_node.tid=term_data.tid 
        WHERE '.implode(' AND ',$where);
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row->nid;
        }
    }    
    return $result;
}
function hontza_solr_actualizar_items(){
    //intelsat-2015
    red_solr_inc_actualizar_items();
}
function hontza_solr_actualizar_validador(){
    hontza_solr_reset_item_validador();
    //cache_clear_all();
    //
    $flag_content_array=hontza_solr_get_flag_content_array();
    if(!empty($flag_content_array)){
        foreach($flag_content_array as $i=>$row){
            hontza_canal_rss_solr_update_validador($row);
        }
    }
}
function hontza_solr_reset_item_validador(){
    $sql='UPDATE {content_type_item} SET field_item_validador_uid_uid=NULL';
    $res=db_query($sql);    
}
function hontza_solr_get_flag_content_array(){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='flag_content.fid=2';
    $where[]='flag_content.content_type="node"';
    $sql='SELECT * 
    FROM {flag_content} flag_content
    WHERE '.implode(' AND ',$where).'
    ORDER BY flag_content.fcid ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_solr_is_empty_userreference($user_array){
    if(!empty($user_array)){
        foreach($user_array as $row){
            if(!empty($row)){
                if(!empty($row['uid'])){
                   if(!isset($row['safe'])){
                       return 1;
                   }
                   return 0;
                }
            }
        }
    }
    return 1;
}
function hontza_solr_define_entity_field_name_item_validador_uid($entity_field_name_array,$entity){
    /*$result=array();
    $user_array=hontza_solr_get_users();
    if(!empty($user_array)){
        foreach($user_array as $i=>$row){
            $my_array=array('uid'=>$row->uid,'safe'=>array());
            $result[]=$my_array;
        }
    }
    return $result;*/
    $row=my_get_content_type_item($entity->nid,$entity->vid);
    //if(!empty($row->field_item_validador_uid_uid) || is_numeric($row->field_item_validador_uid_uid)){
    //if(!empty($row->field_item_validador_uid_uid) || $row->field_item_validador_uid_uid===0){
    if(!empty($row->field_item_validador_uid_uid)){
        $result=array();
        $my_array=array('uid'=>$row->field_item_validador_uid_uid,'safe'=>array());
        $result[0]=$my_array;
        return $result;
    }
    return $entity_field_name_array;
}
function hontza_solr_get_users(){
    $result=array();
    $res=db_query('SELECT * FROM {users} users WHERE 1');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_solr_get_uid_by_username($username){
    $my_user=user_load(array('name'=>$username));
    if(isset($my_user->uid) && !empty($my_user->uid)){
        return $my_user->uid;
    }
    return '';
}
function hontza_solr_get_busqueda($my_is_array=0,$is_rss=0){
  $result=array();
  if(isset($_GET['solr_busqueda_value']) && !empty($_GET['solr_busqueda_value'])){
      return base64_decode($_GET['solr_busqueda_value']);
  }
  //
  $query = array();
  foreach ($_GET as $k => $v) {    
	if ($k != 'q') {
      //gemini
	  //if(in_array($k,array('is_my_submit','is_carpeta_noticia_publica','is_carpeta_noticia_destacada'))){
          if(in_array($k,array('is_my_submit','is_select_bookmark_all','is_my_all_results'))){
            continue;
          }
          //
          if($k=='f'){
            if(!empty($v) && is_array($v)){  
                foreach($v as $i=>$b){  
                    /*if(in_array($k,array('fecha_inicio','fecha_fin'))){
                          //$query[] = "$k=".get_fecha_filter($k,0);
                          if(is_array($_GET[$k])){
                                  foreach($_GET[$k] as $name=>$value){			
                                          //print $k."[".$name."]=".$value."<BR>";
                                          $query[] = $k."[".$name."]=".$value;
                                  }
                          }
                    }else{*/
                    //	
                          $kont=count($query);  
                          $query[] ='f['.$kont.']='.$b; 
                    //}
                }
            }    
          }  
	}
  }
  if($is_rss){
      $query[]='is_my_all_results=1';
  }
  if($my_is_array){
    $result['q']=$_GET['q'];
    $result['query']=implode('&', $query);
    return $result;
  }else{
    return $_GET['q'].'?'.implode('&', $query);
  }  
}
function hontza_solr_save_search_form(&$form_state,$is_rss=0){
  //intelsat-2015
  $my_grupo=og_get_group_context();
  $my_grupo_nid='';
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $my_grupo_nid=$my_grupo->nid;        
  }
  $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
  //  
  $form = array();
  $form['#action'] = url('hontza_solr/search_saved');
  //$form['description']['#value'] = '<h1 class="title">'. t('Save Search') .'</h1>';
  /*if($is_rss){
      $form['#attributes']=array('target'=>'_blank');
  }
  $form['is_rss'] = array(
    '#type' => 'hidden',
    '#default_value'=>$is_rss,  
  );*/
      
  $form['nombre'] = array(
    '#type' => 'textfield',
    //'#title' => t('Name'),
    '#title' => t('Save Search'),  
    //'#size' => 15,
    //'#attributes'=>array('style'=>'width:70%;float:left;'),  
    '#required' => TRUE,  
  );
  
  if(isset($_REQUEST['my_grupo_nid']) && !empty($_REQUEST['my_grupo_nid'])){
      $form['my_grupo_nid']=array(
          '#type'=>'hidden',
          '#default_value'=>$_REQUEST['my_grupo_nid'],
      );
  }
  
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Add'),
  );
  
  $form['cancel_btn'] = array(
    //intelsat-2015    
    //'#value' => l(t('Cancel'),'hontza_solr/busqueda_avanzada_solr',array('query'=>$query_busqueda_avanzada_solr,'attributes'=>array('id'=>'id_a_close_window','class'=>'jqmClose','onclick'=>"self.close();")))
    '#value' => l(t('Cancel'),'hontza_solr/busqueda_avanzada_solr',array('query'=>$query_busqueda_avanzada_solr,'attributes'=>array('id'=>'id_a_close_window','class'=>'jqmClose')))    
  );   
  
  $form['solr_busqueda_value']=array(
     '#type'=>'hidden',
     '#value'=>hontza_solr_get_busqueda(),     
  );
  //$form['#validate'][] = 'my_stored_views_validate';
  if($is_rss){
      $form['my_javascript']=array('#value'=>hontza_solr_funciones_add_rss_js());
  }
  return $form;
}
function hontza_solr_save_search_form_submit($is_validate=1){
  global $user,$base_url;
  $grupo='';
  //$busqueda = hontza_solr_get_busqueda();
  $busqueda=$_POST['solr_busqueda_value'];
  //
  $node = new stdClass();
  $node->type = 'canal_busqueda';
  $node->title = $_POST['nombre'];
  $node->uid = $user->uid;
  $node->field_canal_busqueda_busqueda[0]['value'] = $busqueda;  
  $node->field_fuente_canal[0]['value']= 'Search';
  $my_grupo_nid='';
  if(isset($_POST['my_grupo_nid']) && !empty($_POST['my_grupo_nid'])){
      $my_grupo_nid=$_POST['my_grupo_nid'];
      $node->og_groups=array();
      $node->og_groups[$my_grupo_nid]=$my_grupo_nid;
  }
  $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
  if($is_validate){
    node_save($node);
  }
  /*if(hontza_solr_funciones_is_post_rss()){
    if(!empty($my_grupo_nid)){
      $grupo=node_load($my_grupo_nid);
      $url=$base_url.'/'.$grupo->purl.'/'.$busqueda.'&is_my_all_results=1';
      drupal_goto($url);
    }else{
      drupal_goto($busqueda.'&is_my_all_results=1');
    }  
  }else{*/
    if(!empty($my_grupo_nid)){
      if($is_validate){
        drupal_set_message(t('Search saved').': <b>'.$node->title.'</b>');
        $busqueda.='&canal_busqueda_nid='.$node->nid;
      }
      $grupo=node_load($my_grupo_nid);
      //$url=$base_url.'/'.$grupo->purl.'/hontza_solr/busqueda_avanzada_solr';
      //drupal_goto($url,$query_busqueda_avanzada_solr);
      $url=$base_url.'/'.$grupo->purl.'/'.$busqueda;
      drupal_goto($url);
    }else{
      drupal_goto('hontza_solr/busqueda_avanzada');
    }
  //}
  //hontza_solr_funciones_save_resultados($node,$busqueda,$grupo);
}
function hontza_solr_search_saved_callback(){
  //intelsat-2015
  $my_grupo=og_get_group_context();
  $my_grupo_nid='';
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $my_grupo_nid=$my_grupo->nid;        
  }else if(isset($_REQUEST['my_grupo_nid']) && !empty($_REQUEST['my_grupo_nid'])){
    $my_grupo_nid=$_REQUEST['my_grupo_nid'];  
  }
  $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);  
  //  
  $is_validate=hontza_solr_save_search_validate();  
  hontza_solr_save_search_form_submit($is_validate);
  //drupal_goto('hontza_solr/busqueda_avanzada_solr',$query_busqueda_avanzada_solr);
}
function hontza_solr_save_search_validate(){
	$nombre=$_POST['nombre'];
	if(!empty($nombre)){
		$node=node_load(array('type'=>'canal_busqueda','title'=>$nombre));
		if(isset($node->nid) && !empty($node->nid)){
			drupal_set_message(hontza_solr_search_get_nombre_busqueda_repetido_mensaje(),'error');
                        return 0;
		}
	}
        return 1;
}
function hontza_solr_item_node_form_alter(&$form,&$form_state, $form_id){
    if($form_id=='item_node_form'){
        $nid=$form['nid']['#value'];
        if(isset($form['field_item_validador_uid'])){
            unset($form['field_item_validador_uid']);
        }
        if(isset($form['field_item_solr_updated'])){
            $form['field_item_solr_updated'][0]['#default_value']['value']=1;
            $form['field_item_solr_updated'][0]['#prefix']='<div style="display:none">';
            $form['field_item_solr_updated'][0]['#suffix']='</div>';
        }        
        //intelsat-2015
        //red_solr_inc_field_item_source_tid_form_alter($form,$form_state, $form_id,$nid);
        if(isset($form['field_item_canal_category_tid'])){
            /*$form['field_item_canal_category_tid']['#title']=t('Category');
            $form['field_item_canal_category_tid']['#default_value']=hontza_solr_set_form_field_item_canal_category_tid_default_value($form['field_item_canal_category_tid']['#default_value'],$nid);
            $form['field_item_canal_category_tid']['#pre_render']=array('hontza_solr_field_item_categoria_tid_pre_render');
            $form['field_item_canal_category_tid']['#attributes']=  hontza_solr_set_select_multiple_style();
            $form['#field_info']['field_item_canal_category_tid']['allowed_values']=hontza_solr_get_field_item_canal_category_tid_allowed_values();*/            
            $form['field_item_canal_category_tid']=create_categorias_tematicas_fieldset('',1,$nid,'item');            
        }
        if(isset($form['field_item_validate_status'])){
            unset($form['field_item_validate_status']);
        }
        if(isset($form['field_item_fid'])){
            unset($form['field_item_fid']);
        }
        //intelsat-2015
        red_solr_inc_item_node_form_alter($form,$form_state, $form_id);
    }
}
function hontza_solr_busqueda_simple_callback(){
    $param0=arg(0);
    $param1=arg(1);
    $param2=arg(2);
    $url='my_solr/my_search/'.$param2;
    $query_array=array();
    if(isset($_REQUEST['f']) && !empty($_REQUEST['f'])){
        foreach($_REQUEST['f'] as $i=>$v){
            $query_array[]='f['.$i.']='.$v;
        }
    }
    if(!empty($query_array)){
        $query=implode('&',$query_array);
        $_SESSION['solr_is_show_save_search']=1;
        if(!empty($query)){
            $query.='&solrsort=ds_created desc';
        }
        drupal_goto($url,$query);
    }else{
        drupal_goto($url);
    }
}
function hontza_solr_is_resultados_pantalla(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='my_solr'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='my_search'){
           return 1;     
        }
    }
    return 0;
}
function hontza_solr_get_delete_filters_content(){
    global $user;
    if(!isset($user->uid) || empty($user->uid)){
        return '';
    }
    if(isset($_REQUEST['f']) && !empty($_REQUEST['f'])){
        $result=array();
        $html=array();
        $category_array=hontza_solr_add_delete_category_filtros($_REQUEST['f']);
        if(!empty($category_array)){
            $result[]='<b><i>'.t('Category').'</i></b>';
            $result=array_merge($result,$category_array);
        }
        $type_array=hontza_solr_add_delete_type_filtros($_REQUEST['f']);
        if(!empty($type_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Type').'</i></b>';            
            $result=array_merge($result,$type_array);
        }
        $term_array=hontza_solr_add_delete_term_filtros($_REQUEST['f']);
        if(!empty($term_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Tag').'</i></b>';  
            $result=array_merge($result,$term_array);
        }
        
        $validador_array=hontza_solr_add_delete_validador_filtros($_REQUEST['f']);
        if(!empty($validador_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Validator').'</i></b>';
            $result=array_merge($result,$validador_array);
        }        
        $canal_array=hontza_solr_add_delete_canal_filtros($_REQUEST['f']);
        if(!empty($canal_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Channel').'</i></b>';
            $result=array_merge($result,$canal_array);
        }       
        $file_array=hontza_solr_funciones_add_delete_file_filtros($_REQUEST['f']);
        if(!empty($file_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Filename').'</i></b>';
            $result=array_merge($result,$file_array);
        }
        hontza_solr_funciones_add_beste_delete_filters_content($result);
        if(!empty($result)){
            $html[]='<div class="item-list">';
            $html[]='<ul id="facetapi-facet-apachesolr@solr-block-delete-filters" class="facetapi-facetapi_links facetapi-facet-delete-filters">';
            $num=count($result);
            $is_title=1;
            foreach($result as $i=>$v){
                if(empty($v)){
                    continue;
                }
                $class='leaf';
                if($i>1){
                    if($i==($num-1)){
                        $class="leaf last";
                    }
                }else{
                    $class="leaf first";
                }
                $konp=hontza_solr_funciones_get_linea_separacion($result);
                if($v==$konp){
                    $class.=' my_separacion';
                    $html[]='<li class="'.$class.'" style="margin-top:5px;margin-bottom:5px;"></li>';
                    $is_title=1;
                }else if($is_title){    
                    $html[]='<li class="'.$class.'" style="margin-bottom:5px;">'.$v.'</li>';
                    $is_title=0;
                }else{
                    $html[]='<li class="'.$class.'">'.$v.'</li>';
                }    
            }
            $html[]='</ul>';
            $html[]='</div>';
        }
        return implode('',$html);
    }
    return '';
}
function hontza_solr_add_delete_canal_filtros($my_array){
    $result=array();
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='im_field_item_canal_reference'){
                    $canal=node_load($value_array[1]);
                    if(isset($canal->nid) && !empty($canal->nid)){
                        $icono_link=hontza_solr_get_canal_icono_link($canal->nid,'im_field_item_canal_reference',$my_array);
                        $result[]=$icono_link.$canal->title;
                    }            
                }
            }
        }
    }    
    return $result;
}
function hontza_solr_add_delete_term_filtros($my_array){
    $result=array();
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='im_taxonomy_vid_3'){
                    $term_name=taxonomy_get_term_name($value_array[1]);                
                    if(!empty($term_name)){
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'im_taxonomy_vid_3',$my_array);
                        $result[]=$icono_link.$term_name;
                    }
                }
            }
        }
    }    
    return $result;
}
function hontza_solr_add_delete_validador_filtros($my_array){
    $result=array();
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=red_solr_inc_add_delete_validador_filtros();
    }else{    
        if(!empty($my_array) && is_array($my_array)){
            foreach($my_array as $i=>$value){
                $value_array=explode(':',$value);
                if(count($value_array)>1){
                    $field=$value_array[0];
                    if($field=='im_field_item_validador_uid'){
                        //intelsat-2016-noticias-usuario
                        /*$username=hontza_get_username($value_array[1]);              
                        if(!empty($username)){
                            $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'im_field_item_validador_uid',$my_array);
                            $result[]=$icono_link.$username;
                        }*/
                        $is_noticias_usuario=0;
                        red_solr_inc_add_add_delete_im_field_item_validador_uid_filtros($value_array[1],$result,$my_array,$is_noticias_usuario);
                    }
                }
            }
        }
    }
    return $result;
}
function hontza_solr_get_canal_icono_link($canal_nid,$field_konp,$my_array){
    $result=array();
    foreach($my_array as $i=>$value){
        $value_array=explode(':',$value);
        if(count($value_array)>1){
            $field=$value_array[0];
            if($field==$field_konp){
                if($value_array[1]!=$canal_nid){
                    $result[]=$value;
                }
            }else{
                $result[]=$value;
            }
        }    
    }
    $url=arg(0).'/'.arg(1).'/'.arg(2);
    $url=rtrim($url,'/');
    if(!empty($result)){
        $query_array=array();
        $query='';
        foreach($result as $i=>$v){
            $query_array[]='f['.$i.']='.$v;
        }
        if(!empty($query_array)){
            $query=implode('&',$query_array);
            //$url.='?'.$query;
            //$url=url($url,array('query'=>$query));
        }
        if(!empty($query)){
            $query.='&solrsort=ds_created desc';
        }
        return l(my_get_icono_action('delete_solr_filter', t('Delete filter')),$url,array('html'=>TRUE,'query'=>$query)).'&nbsp;';
    }
    return '';
}
function hontza_solr_actualizar_item_source_tid(){
    //hontza_solr_reset_item_source_tid();
    //
    $item_array=hontza_get_all_nodes(array('item'));
    //simulando
    //$item_array=hontza_get_all_nodes(array('item'),'','',200);
    //
    if(count($item_array)>0){
        foreach($item_array as $i=>$node){
            if(isset($node->nid) && !empty($node->nid)){
                //hontza_solr_delete_term_item_source_tid($node);
                //
                if(hontza_solr_is_item_actualizado($node)){
                    continue;
                }
                $canal_nid=$node->field_item_canal_reference[0]['nid'];                
                /*
                if($canal_nid!=187722){
                    continue;
                }*/
                hontza_solr_reset_item_source_tid($node);
                $canal_nid=$node->field_item_canal_reference[0]['nid'];                                                
                $canal=node_load($canal_nid);
                hontza_solr_set_canal_source_info($canal);
                //
                if(isset($canal->nid) && !empty($canal->nid)){
                    if($canal->type=='canal_de_supercanal'){
                        if(hontza_solr_is_canal_source_type_updated($canal)){
                            hontza_solr_save_item($node,$canal,1,0);
                        }else{
                            $fuente_nid=$canal->field_nid_fuente_canal[0]['value'];
                            $fuente=node_load($fuente_nid);
                            if(isset($fuente->nid) && !empty($fuente->nid)){
                                if(isset($fuente->taxonomy) && !empty($fuente->taxonomy)){
                                    $delta=0;
                                    foreach($fuente->taxonomy as $tid=>$term){
                                        if($term->vid==1){
                                            if($delta>0){
                                                $res=db_query('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$term->tid,$node->nid,$node->vid,$delta);                                        
                                            }else{
                                                $res=db_query('UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$term->tid,$node->nid,$node->vid);
                                            }    
                                            $delta++;
                                            //hontza_solr_insert_term_node_item_source($node,$tid);
                                        }                                    
                                    }
                                }
                            }
                        }    
                    }else if($canal->type=='canal_de_yql'){
                       //$res=db_query('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(NULL,%d,%d,0)',$node->nid,$node->vid);           
                       hontza_solr_search_actualizar_item_source_tid_by_canal_de_yql($canal,$node); 
                    }                            
                }
            }
        }    
    }
    return $item_array;
}
function hontza_solr_reset_item_source_tid($node=''){
    if(isset($node->nid) && !empty($node->nid)){
        $sql='DELETE FROM {content_field_item_source_tid} WHERE delta>0 AND nid='.$node->nid.' AND vid='.$node->vid;
        $res=db_query($sql);
        $sql='UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=NULL WHERE nid='.$node->nid.' AND vid='.$node->vid;
        $res=db_query($sql); 
    }else{
        $sql='DELETE FROM {content_field_item_source_tid} WHERE delta>0';
        $res=db_query($sql);
        $sql='UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=NULL';
        $res=db_query($sql); 
    }
}
function hontza_solr_actualizar_item_canal_category_tid($item_array){
    //hontza_solr_reset_item_canal_category_tid();
    //
    if(count($item_array)>0){
        foreach($item_array as $i=>$node){
            if(isset($node->nid) && !empty($node->nid)){
                if(hontza_solr_is_item_actualizado($node)){
                    continue;
                }
                hontza_solr_reset_item_canal_category_tid($node);
                $canal_nid=$node->field_item_canal_reference[0]['nid'];
                $canal=node_load($canal_nid);
                if(isset($canal->nid) && !empty($canal->nid)){
                    $tid_array=hontza_solr_canal_categoria_tid_array($canal);
                    if(!empty($tid_array)){
                        $delta=0;
                        foreach($tid_array as $k=>$tid){
                            if($delta>0){
                                if(!hontza_canal_rss_existe_content_field_item_canal_category_tid_vid_delta($node->vid,$delta)){
                                    $res=db_query('INSERT INTO {content_field_item_canal_category_tid}(field_item_canal_category_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
                                }else{
                                    $res=db_query('UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=%d WHERE nid=%d AND vid=%d AND delta=%d',$tid,$node->nid,$node->vid,$delta);                            
                                }                                    
                            }else{
                                $res=db_query('UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$tid,$node->nid,$node->vid);
                            }    
                            $delta++;
                        }
                    }
                }
                /*
                //intelsat-2015
                $updated=0;
                */
                hontza_solr_set_item_solr_updated($node,$updated);                
            }
        }
    }    
}
function hontza_solr_simular_hontza_solr_actualizar_item_canal_category_tid_callback(){
    //return 'Funcion desactivada';
    //$item_array=hontza_solr_actualizar_item_source_tid();
    $is_limit=0;
    //$is_limit=1;
    //$item_array=hontza_get_all_nodes(array('item'),'','',$is_limit);
    //hontza_solr_actualizar_item_canal_category_tid($item_array);
    //hontza_solr_actualizar_canal_source_type();
    //hontza_solr_actualizar_validate_status($item_array);
    //hontza_solr_actualizar_item_ficheros($item_array);
    //hontza_solr_actualizar_items();
    return date('Y-m-d H:i:s');
}
function hontza_solr_reset_item_canal_category_tid($node=''){
    if(isset($node->nid) && !empty($node->nid)){
        $sql='DELETE FROM {content_field_item_canal_category_tid} WHERE delta>0 AND nid='.$node->nid.' AND vid='.$node->vid;
        $res=db_query($sql);
        $sql='UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=NULL WHERE nid='.$node->nid.' AND vid='.$node->vid;
        $res=db_query($sql);
    }else{
        $sql='DELETE FROM {content_field_item_canal_category_tid} WHERE delta>0';
        $res=db_query($sql);
        $sql='UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=NULL';
        $res=db_query($sql);        
    }    
}
function hontza_solr_get_id_categoria_by_canal($canal){
    if(isset($canal->og_groups) && !empty($canal->og_groups) && is_array($canal->og_groups)){
        $grupo_nid_array=array_keys($canal->og_groups);
        if(isset($grupo_nid_array[0]) && !empty($grupo_nid_array[0])){
            $grupo_nid=$grupo_nid_array[0];
            $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$grupo_nid));
            return $id_categoria;
        }
    }
    return '';
}
function hontza_solr_canal_categoria_tid_array($canal){
    $result=array();
    $id_categoria=hontza_solr_get_id_categoria_by_canal($canal);
    if(isset($canal->taxonomy) && !empty($canal->taxonomy)){
        $delta=0;
        foreach($canal->taxonomy as $tid=>$term){
            if($term->vid==$id_categoria){
                $result[]=$term->tid;
            }
        }
    }
    return $result;
}
function hontza_solr_delete_term_item_source_tid($node){
    $vid=1;
    $tree=taxonomy_get_tree($vid);
    $tid_array=hontza_solr_get_tree_tid_array($tree);
    $sql='DELETE FROM {term_node} WHERE nid='.$node->nid.' AND vid='.$node->vid.' AND tid IN('.implode(',',$tid_array).')';
    db_query($sql);
}
function hontza_solr_get_tree_tid_array($tree){
    $result=array();
    if(!empty($tree)){
        foreach($tree as $i=>$row){
            $result[]=$row->tid;
        }
    }
    return $result;
}
function hontza_solr_insert_term_node_item_source($node,$tid){
    $sql='INSERT {term_node}(nid,vid,tid) VALUES('.$node->nid.','.$node->vid.','.$tid.')';
    db_query($sql);
}
function hontza_solr_is_empty_numeric_reference($user_array){
    if(!empty($user_array)){
        foreach($user_array as $row){
            if(!empty($row)){
                if(!empty($row['value'])){                   
                   return 0;
                }
            }
        }
    }
    return 1;
}
function hontza_solr_define_entity_field_name_item_numeric_reference($entity_field_name_array,$entity){
    $content_field_item_source_tid_array=hontza_solr_get_content_field_item_source_tid_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$content_field_item_source_tid_array);
}
function hontza_solr_get_content_field_item_source_tid_array($nid,$vid,$is_row=0){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_item_source_tid} WHERE nid=%d AND vid=%d AND field_item_source_tid_value IS NOT NULL',$nid,$vid));
    while($row=db_fetch_object($res)){
        //intelsat-2016
        //if(red_solr_inc_existe_term($row->field_item_source_tid_value)){
            if($is_row){
                $result[]=$row;
            }else{
                $result[]=$row->field_item_source_tid_value;
            }
        //}
    }
    return $result;
}
function hontza_solr_set_item_source_label(&$element,$field_alias=''){    
    hontza_solr_funciones_set_item_source_label($element,$field_alias);
}
function hontza_solr_add_delete_type_filtros($my_array){
    global $language;
    $result=array();
    $with_orig=0;
    if($language->language=='es'){
        $with_orig=1;
    }
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='itm_field_item_source_tid'){
                    $term_name=taxonomy_get_term_name_by_language($value_array[1],'',$with_orig); 
                    if(!empty($term_name)){
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_source_tid',$my_array);
                        $result[]=$icono_link.$term_name;
                    }
                }
            }
        }
    }    
    return $result;
}
function hontza_solr_define_entity_field_name_item_canal_category_tid($entity_field_name_array,$entity){
    $content_field_item_canal_category_tid_array=hontza_solr_get_content_field_item_canal_category_tid($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$content_field_item_canal_category_tid_array);
}
function hontza_solr_set_indexing_numeric_value($entity_field_name_array,$tid_array){
    if(!empty($tid_array)){
        $result=array();                
        foreach($tid_array as $i=>$tid){
           if(!empty($tid)){
                //$my_array=array('uid'=>$row->field_item_validador_uid_uid,'safe'=>array());
                $my_array=array('value'=>$tid);
                $result[]=$my_array;                
            }     
        }
        return $result;
    }    
    return $entity_field_name_array;
}
function hontza_solr_get_content_field_item_canal_category_tid($nid,$vid,$is_row=0){
    $result=array();
    $res=db_query('SELECT * FROM {content_field_item_canal_category_tid} WHERE nid=%d AND vid=%d AND field_item_canal_category_tid_value IS NOT NULL ORDER BY delta ASC',$nid,$vid);
    while($row=db_fetch_object($res)){
        if($is_row){
            $result[]=$row;
        }else{
            $result[]=$row->field_item_canal_category_tid_value;
        }    
    }
    return $result;
}
function hontza_solr_add_delete_category_filtros($my_array){
    global $language;
    $with_orig=0;
    if($language->language=='es'){
        $with_orig=1;
    }                
    $result=array();
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='itm_field_item_canal_category_ti'){
                    $term_name=taxonomy_get_term_name_by_language($value_array[1],'',$with_orig); 
                    if(!empty($term_name)){
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_canal_category_ti',$my_array);
                        $result[]=$icono_link.$term_name;
                    }
                }
            }
        }
    }
    return $result;
}
function hontza_solr_set_facetapi_by_tree($element,$my_tree){
    if(!empty($my_tree)){
        $result=array();
        foreach($my_tree as $i=>$row){
            if(isset($element[$row->tid])){
                $my_array=taxonomy_get_parents($row->tid);
                if(!empty($my_array)){
                    foreach($my_array as $k=>$r){
                        if(!isset($result[$r->tid])){
                            $result[$r->tid]=array();
                            $result[$r->tid]['#value']=$r->name;
                        }
                    }
                }
                $result[$row->tid]=$element[$row->tid];
            }
        }
        echo print_r($result,1);
        return $result;
    }   
    return $element;
}
function hontza_solr_save_canal_source_type($canal){
    if(hontza_solr_is_solr_activado()){
        $delta=0;       
        if(isset($canal->supercanal)){
            $fuente=$canal->supercanal;
            if(isset($fuente->taxonomy) && !empty($fuente->taxonomy)){
                hontza_solr_delete_canal_source_type($canal->vid,$canal->nid);
                foreach($fuente->taxonomy as $tid=>$row){
                    if($row->vid==1){                        
                        hontza_solr_insert_canal_source_type($canal->vid,$canal->nid,$row->tid,$delta);
                        $delta++;
                    }
                }
            }
        }
    }
}
function hontza_solr_insert_canal_source_type($vid,$nid,$tid,$delta){
    db_query($sql=sprintf('INSERT INTO {content_field_canal_source_type}(vid,nid,field_canal_source_type_value,delta) VALUES(%d,%d,%d,%d)',$vid,$nid,$tid,$delta));    
}
function hontza_solr_delete_canal_source_type($vid,$nid){
    db_query('DELETE FROM {content_field_canal_source_type} WHERE vid=%d AND nid=%d',$vid,$nid);
}
function hontza_solr_canal_de_supercanal_form_alter(&$form,&$form_state,$form_id){
    red_solr_canal_source_type_form_alter($form,$form_state,$form_id);
}
function hontza_solr_source_type_options(){
    $result=array();
    $tree=taxonomy_get_tree(1);
    if(!empty($tree)){
        foreach($tree as $tid=>$term){
            $term_name=taxonomy_get_term_name_by_language($term->tid);
            $result[$term->tid]=$term_name;
            //$result[$term_name]=$term->tid;
        }
    }
    return $result;
}
function hontza_solr_field_canal_source_type_pre_render($element){
    $options=hontza_solr_source_type_options();
    $element['value']['#options'] = $options;
    $element['value']['#default_value']=$element['#default_value'];
    $element['value']['#attributes']=hontza_solr_set_select_multiple_style();
    return $element;
}
function hontza_solr_get_field_canal_source_type_allowed_values(){
    $options=hontza_solr_source_type_options();
    $result=array_keys($options);
    return implode("\n",$result);
}
function hontza_solr_actualizar_canal_source_type($canal_nid_in=''){
    if(hontza_solr_is_solr_activado()){
        $canal_array=hontza_get_all_nodes(array('canal_de_supercanal'),'','',0,$canal_nid_in);
        if(!empty($canal_array)){
            foreach($canal_array as $i=>$canal){
                if(!hontza_solr_is_canal_source_type_updated($canal)){
                  $fuente_nid=$canal->field_nid_fuente_canal[0]['value'];             
                  $fuente=node_load($fuente_nid);
                  if(isset($fuente->nid) && !empty($fuente->nid)){
                     hontza_solr_delete_canal_source_type($canal->vid,$canal->nid);
                     $delta=0;                    
                     foreach($fuente->taxonomy as $tid=>$row){
                             if($row->vid==1){                        

                             hontza_solr_insert_canal_source_type($canal->vid,$canal->nid,$row->tid,$delta);
                             $delta++;
                         }
                     }
                  }
                  hontza_solr_set_canal_source_type_updated($canal,1);
               }
            }
        }
    }    
}
function hontza_solr_is_canal_source_type_updated($canal){
   $updated=hontza_solr_get_canal_source_type_updated_value($canal);
   if(!empty($updated)){
       return 1;
   }
   return 0;
}
function hontza_solr_get_canal_source_type_updated_value($canal){
    $row=hontza_solr_get_canal_source_type_updated_row($canal);
    if(isset($row->field_canal_source_type_updated_value) && !empty($row->field_canal_source_type_updated_value)){
        return $row->field_canal_source_type_updated_value;
    }
    return ''; 
}
function hontza_solr_get_canal_source_type_updated_row($canal){
    $res=db_query('SELECT * FROM {content_field_canal_source_type_updated} WHERE nid=%d AND vid=%d',$canal->nid,$canal->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function hontza_solr_set_canal_source_type_updated($canal,$updated){
    $res=db_query('UPDATE {content_field_canal_source_type_updated} SET field_canal_source_type_updated_value=%d WHERE nid=%d AND vid=%d',$updated,$canal->nid,$canal->vid);
}
function  hontza_solr_canal_de_yql_form_alter(&$form,&$form_state, $form_id){
    //intelsat-2015
    //if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
    if(hontza_canal_rss_is_item_categorias()){
        hontza_solr_canal_de_supercanal_form_alter($form,$form_state,$form_id);
    }else{
        if(isset($form['field_canal_source_type'])){
            unset($form['field_canal_source_type']);
        }        
        if(isset($form['field_canal_source_type_updated'])){
            unset($form['field_canal_source_type_updated']);
        }
    }
    //intelsat-2016
    $unset_array=array('field_my_opml','field_url_html','field_is_validacion_automatica','field_is_canal_correo');
    red_movil_unset_form_field_form_alter($form,$form_state,$form_id,$unset_array);
}
function hontza_solr_save_item($node,$canal_in='',$is_source_type=1,$is_category=1){
    //intelsat-2015
    //if(hontza_solr_is_solr_activado()){
    if(hontza_canal_rss_is_item_categorias()){    
        $canal_nid=$node->field_item_canal_reference[0]['nid'];
        if(!empty($canal_in)){
            $canal=$canal_in;
        }else{
            $canal=node_load($canal_nid);
            hontza_solr_set_canal_source_info($canal);
        }
        //
        if(isset($canal->nid) && !empty($canal->nid)){
            if($is_source_type){
                $sql='DELETE FROM {content_field_item_source_tid} WHERE nid='.$node->nid.' AND vid='.$node->vid;
                $res=db_query($sql);
                if(isset($canal->field_canal_source_type) && !empty($canal->field_canal_source_type)){
                    $delta=0;
                    foreach($canal->field_canal_source_type as $i=>$row){
                        $tid=$row['value'];
                        $res=db_query($sql=sprintf('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta));                                                                                
                        $delta++;
                    }
                }
            }
            if($is_category){
                $tid_array=hontza_solr_canal_categoria_tid_array($canal);
                $sql='DELETE FROM {content_field_item_canal_category_tid} WHERE nid='.$node->nid.' AND vid='.$node->vid;
                $res=db_query($sql);
                if(!empty($tid_array)){
                        $delta=0;
                        foreach($tid_array as $k=>$tid){
                            //if($delta>0){
                                $res=db_query('INSERT INTO {content_field_item_canal_category_tid}(field_item_canal_category_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
                            /*}else{
                                $res=db_query('UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$tid,$node->nid,$node->vid);
                            }*/    
                            $delta++;
                        }
                }
            }
        }
    }    
}
function hontza_solr_is_item_actualizado($node){
    $row=my_get_content_type_item($node->nid,$node->vid);
    if(isset($row->field_item_solr_updated_value) && !empty($row->field_item_solr_updated_value)){
        return 1;
    }    
    return 0;
}
function hontza_solr_set_item_solr_updated($node,$updated){
    $res=db_query('UPDATE {content_type_item} SET field_item_solr_updated_value=%d WHERE nid=%d AND vid=%d',$updated,$node->nid,$node->vid);
    //intelsat-2015
    hontza_solr_search_clear_cache_content($node);
}
function hontza_solr_get_item_source_tid_array($nid,$node_in=''){
    $result=array();
    if(!empty($node_in)){
        $node=$node_in;
    }else{
        $node=node_load($nid);
    }
    $res=db_query('SELECT * FROM {content_field_item_source_tid} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row->field_item_source_tid_value;
    }
    return $result;
}
function hontza_solr_field_item_source_type_pre_render($element){
    $options=hontza_solr_source_type_options();
    $element['value']['#options'] = $options;
    $element['value']['#default_value']=$element['#default_value'];
    $element['value']['#attributes']=  hontza_solr_set_select_multiple_style();
    return $element;
}
function hontza_solr_get_field_item_source_type_allowed_values(){
    return hontza_solr_get_field_canal_source_type_allowed_values();
}
function hontza_solr_set_form_field_item_source_tid_default_value($default_value,$nid,$form_id='',$node_in=''){
    if(isset($node_in->nid) && !empty($node_in->nid)){
        $node=$node_in;
    }else{
        $node=node_load($nid);
    }
    if(empty($default_value) && !hontza_solr_is_item_actualizado($node)){
        $canal_nid=$node->field_item_canal_reference[0]['nid'];
        $canal=node_load($canal_nid);
        if(hontza_solr_is_canal_source_type_updated($canal)){
            $value_array=hontza_solr_get_content_field_canal_source_type_value_array($canal);
            return $value_array;
        }else{
           $type_array=hontza_solr_get_source_value_array($canal);
           return $type_array;
        }
    }else{
        //intelsat-2015
        return red_solr_inc_get_item_source_value_array($node,$form_id);
    }
    return $default_value;
}
function hontza_solr_get_content_field_canal_source_type_value_array($canal){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_canal_source_type} WHERE nid=%d AND vid=%d',$canal->nid,$canal->vid));
    while($row=db_fetch_object($res)){
        $result[]['value']=$row->field_canal_source_type_value;
    }
    return $result;
}
function hontza_solr_get_item_source_value_array($node){
    $result=array();
    $tid_array=hontza_solr_get_item_source_tid_array('',$node);
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            $result[]['value']=$tid;
        }
    }
    return $result;
}
function hontza_solr_set_form_field_item_canal_category_tid_default_value($default_value,$nid){
    $node=node_load($nid);
    if(empty($default_value) && !hontza_solr_is_item_actualizado($node)){
        $result=array();
        $canal_nid=$node->field_item_canal_reference[0]['nid'];
        $canal=node_load($canal_nid);
        $tid_array=hontza_solr_canal_categoria_tid_array($canal);
        if(!empty($tid_array)){
            foreach($tid_array as $i=>$tid){
                $result[]['value']=$tid;
            }
        }
        return $result;
    }else{
       $item_value_array=hontza_solr_get_item_categoria_value_array($node);
       return $item_value_array;
    }
    return $default_value;
}
function hontza_solr_get_item_categoria_value_array($node){
    $result=array();
    $tid_array=hontza_solr_get_item_categoria_tid_array($node);
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            $result[]['value']=$tid;
        }
    }
    return $result;
}
function hontza_solr_get_item_categoria_tid_array($node){
    $tid_array=hontza_solr_get_content_field_item_canal_category_tid($node->nid,$node->vid);
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            $result[]=$tid;
        }
    }
    return $result;
}
function hontza_solr_get_field_item_canal_category_tid_allowed_values(){
    $result=array();
    /*$my_array=my_get_categorias_canal();
    if(!empty($my_array)){
        foreach($my_array as $key=>$value_array){
            if($key===0 || is_numeric($key)){
                $result[]=$key;
            }else{
                $key_array=array_keys($value_array);
                $result=array_merge($result,$key_array);
            }
        }
    }*/
    $options=hontza_solr_get_categorias_canal();
    if(!empty($options)){
        foreach($options as $tid=>$term_name){
            $result[]=$tid;
        }
    }    
    return implode("\n",$result);
}
function hontza_solr_field_item_categoria_tid_pre_render($element){
    $options=hontza_solr_get_categorias_canal();
    $element['value']['#options'] = $options;
    $element['value']['#default_value']=$element['#default_value'];
    $element['value']['#attributes']=  hontza_solr_set_select_multiple_style();
    return $element;
}
function hontza_solr_get_canal_source_type_default_value($default_value,$nid,$form_id='',$canal_in=''){
    if(isset($canal_in->nid) && !empty($canal_in->nid)){
        $canal=$canal_in;
    }else{
        $canal=node_load($nid);
    }
    if(hontza_solr_is_canal_source_type_updated($canal)){
        $value_array=hontza_solr_get_content_field_canal_source_type_value_array($canal);
        return $value_array;
    }else{
        $type_array=hontza_solr_get_source_value_array($canal);
        return $type_array;
    }
    return $default_value;    
}
function hontza_solr_get_source_value_array($canal){
     $type_array=array();
            $fuente_nid=$canal->field_nid_fuente_canal[0]['value'];
            $fuente=node_load($fuente_nid);
             if(isset($fuente->nid) && !empty($fuente->nid)){
                hontza_solr_delete_canal_source_type($canal->vid,$canal->nid);
                $delta=0;
                foreach($fuente->taxonomy as $tid=>$row){
                    if($row->vid==1){                        
                        $type_array[]['value']=$row->tid;
                    }
                }
             }
             return $type_array;
}
function hontza_solr_set_select_multiple_style(){
    $result=array('style'=>'height:200px;');
    return $result;
}
function hontza_solr_get_categorias_canal(){
    $result[0]= t('Any');
    $tree=my_get_categorias_canal(0);
    if(!empty($tree)){
        foreach($tree as $i=>$term){
            $result[$term->tid]=$term->name;
        }
    }
    return $result;
}
function hontza_solr_clear_cache_content($nid,$is_clear_cache_content=0){
    //intelsat-2015
    //if(hontza_solr_is_solr_activado()){
    //intelsat-2016
    if(hontza_canal_rss_is_item_categorias() || $is_clear_cache_content){
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $sql = 'delete from cache_content where cid = "content:'.$node->nid.':'.$node->vid.'"';
            db_query($sql);            
        }
    }
}
function hontza_solr_set_canal_source_info(&$canal){    
    if(hontza_solr_is_solr_activado()){
        if(isset($canal->nid) && !empty($canal->nid)){              
            $value_array=hontza_solr_get_content_field_canal_source_type_value_array($canal);
            $canal->field_canal_source_type=$value_array;
            //
            $updated=hontza_solr_get_canal_source_type_updated_value($canal);
            $canal->field_canal_source_type_updated[0]['value']=$updated;
        }
    }
}
function hontza_solr_set_enter_terms_display_none(&$form){
  if(!user_access('root')){  
    if(hontza_solr_is_resultados_pantalla()){  
      $form['#attributes']=array('style'=>'display:none');
    }    
  }  
}
function hontza_solr_is_all_results(){
  if(isset($_REQUEST['is_my_all_results']) && !empty($_REQUEST['is_my_all_results'])){
      return 1;
  }
  if(hontza_solr_funciones_is_select_bookmark_all()){
      return 1;
  }
  return 0;
}
function hontza_solr_print_rss($results){
  $nid_array=hontza_solr_get_results_nid_array($results);
  if(isset($_REQUEST['is_enviar_mail']) && !empty($_REQUEST['is_enviar_mail']) && $_REQUEST['is_enviar_mail']==1){
      $nid_array=busqueda_rss_get_nid_array_destacados($nid_array);
      busqueda_rss_node_feed($nid_array,array(),$_REQUEST['red_exportar_rss_canal_nid']);
  }else{
    //node_feed($nid_array);  
    hontza_solr_funciones_node_feed($nid_array);
  }  
  exit();
}
function hontza_solr_get_results_nid_array($results){
    $result=array();
    foreach($results as $i=>$row){
      $result[]=$row['node']->entity_id;
    }
    return $result;
}
function hontza_solr_get_help_busqueda_avanzada(){
    if(hontza_solr_is_solr_activado()){
        return help_popup_window(309971, 'help',my_get_help_link_object(),0,0,0,0,1);
    }
    return '';
}
function hontza_solr_my_access(){
    global $user;
    if(isset($user->uid) && !empty($user->uid)){
        if(user_access('access content') && user_access('search content')){
            return TRUE;
        }
    }
    return FALSE;
}
function hontza_solr_help_close_window_link(){      
    //intelsat-2016
    return red_copiar_get_cancel_link('hontza_solr/help_close_window',1);
}
function hontza_solr_canal_source_term_array($canal_nid){
    $result=array();
    $default_value=array();
    $value_array=hontza_solr_get_canal_source_type_default_value($default_value,$canal_nid);
    if(!empty($value_array)){
        foreach($value_array as $i=>$row){
            if(isset($row['value']) && !empty($row['value'])){
                $result[]=hontza_solr_get_taxonomy_term($row['value']);
            }
        }
    }
    return $result;
}
function hontza_solr_add_help_close_window_js(){
     $js='$(document).ready(function()
     {
         $(document).keypress(function(e) {
            var is_close_window=false;
            if(e.keyCode==13){
                //enter
                is_close_window=true;
            }else if(e.keyCode==27){
                //esc
                is_close_window=true;
            }
            if(is_close_window){
                $("#id_a_close_window").click();
            }
         });         
     });';     
     $result='<script type="text/javascript">'.$js.'</script>';
     return $result;        
}
function hontza_solr_get_taxonomy_term($tid){
    $row=taxonomy_get_term_by_language($tid);
    if(isset($row->tid) && !empty($row->tid)){
        return $row;
    }else{
        $result=taxonomy_get_term($tid);
        return $result;
    }
}
function hontza_solr_set_canal_source_type_terms_ul($source_array){
    $result=array();
    if(!empty($source_array)){
        $result[]='<ul>';
        foreach($source_array as $i=>$row){
            $term_name=taxonomy_get_term_name_by_language($row->tid);
            $result[]='<li>'.l($term_name,'taxonomy/term/'.$row->tid).'</li>';
        }
        $result[]='</ul>';
    }
    return implode('',$result);
}
function hontza_solr_define_search_result_buttons(){
   return hontza_solr_funciones_define_search_result_buttons();
}
function hontza_solr_add_save_current_search_js(){    
 $my_grupo=og_get_group_context();
 $my_grupo_nid='';
 if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
     $my_grupo_nid=$my_grupo->nid;
 }
 //
 $solr_busqueda_value=hontza_solr_get_busqueda();
 $solr_busqueda_value=base64_encode($solr_busqueda_value);
 //intelsat-2016
 $my_base_path=hontza_canal_rss_get_base_path_help_popup();
 //   
 $js="$(document).ready(function()
     {
         $('#exsave_current_search').jqm({ajax: '".$my_base_path."help_popup.php?nid=save_current_search&w=500&h=400&my_grupo_nid=".$my_grupo_nid."&solr_busqueda_value=".$solr_busqueda_value."', trigger: 'a.jqm-trigger-save_current_search',modal:true, toTop: true, overlay: 0});
         
     });";
     drupal_add_js($js,'inline');
}
function hontza_solr_actualizar_validate_status($item_array){
   if(!empty($item_array)){
       foreach($item_array as $i=>$node){
           if(isset($node->nid) && !empty($node->nid)){
               //intelsat-2015
               hontza_solr_update_node_validate_status($node);
           }
       }
   } 
}
function hontza_solr_update_item_validate_status($node,$validate_status){   
   db_query('UPDATE {content_type_item} SET field_item_validate_status_value=%d WHERE nid=%d AND vid=%d',$validate_status,$node->nid,$node->vid); 
}
function hontza_solr_define_entity_field_name_item_validate_status($entity_field_name_array,$entity){
    $validate_status_array=hontza_solr_get_content_field_item_validate_status_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$validate_status_array);
}
function hontza_solr_get_content_field_item_validate_status_array($nid,$vid){
    $result=array();
    //sin validar=1
    //validado=2
    //rechazado=3
    $result[0]=1;
    $row=my_get_content_type_item($nid,$vid);
    if(isset($row->field_item_validate_status_value) && !empty($row->field_item_validate_status_value)){
        $result[0]=$row->field_item_validate_status_value;
    }
    return $result;
}
function hontza_solr_get_busqueda_simple_solr_param_js(){
        $my_grupo=og_get_group_context();        
        $solr_param='';
        $my_array=array();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $i=count($my_array); 
            //$solr_param='?f[0]=im_og_gid:'.$my_grupo->nid;
            $my_array[]='f['.$i.']=im_og_gid:'.$my_grupo->nid;
        }
        $sort='solrsort=ds_created desc';
        $my_array[]=$sort;
        return '?'.implode('&',$my_array);
}
function hontza_solr_actualizar_item_ficheros($item_array){
    if(hontza_solr_is_filename_activado()){
        $fid_array=array();
        if(!empty($item_array)){
           foreach($item_array as $i=>$node){
               if(isset($node->nid) && !empty($node->nid)){
                   hontza_solr_reset_item_fid($node);        
                   $delta=0;
                   $files=array();
                   if(isset($node->files) && !empty($node->files)){
                       $files=$node->files;
                   }    
                   $comments_files=hontza_solr_get_comments_files($node->nid);
                   if(!empty($comments_files)){
                        $files=array_merge($files,$comments_files);                       
                   }
                   if(!empty($files)){
                       $files=hontza_solr_set_files_key($files);                   
                       foreach($files as $fid=>$file_row){
                           if(!in_array($fid,$fid_array)){
                                if($delta>0){
                                     hontza_solr_insert_item_fid($node,$delta,$fid);   
                                }else{
                                     hontza_solr_update_item_fid($node,$delta,$fid);
                                }
                                $fid_array[]=$fid;
                                $delta++;
                           } 
                       }
                   }
               }
           }
        }
    }
}
function hontza_solr_reset_item_fid($node){
    db_query('UPDATE {content_field_item_fid} SET field_item_fid_value=NULL WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    db_query('DELETE FROM {content_field_item_fid} WHERE nid=%d AND vid=%d AND delta>0',$node->nid,$node->vid);
}
function hontza_solr_is_filename_activado(){
    return 1;   
}
function hontza_solr_insert_item_fid($node,$delta,$fid){
    db_query('INSERT INTO {content_field_item_fid}(nid,vid,delta,field_item_fid_value) VALUES(%d,%d,%d,%d)',$node->nid,$node->vid,$delta,$fid);
}
function hontza_solr_update_item_fid($node,$delta,$fid){
    db_query('UPDATE {content_field_item_fid} SET field_item_fid_value=%d WHERE nid=%d AND vid=%d',$fid,$node->nid,$node->vid);
}
function hontza_solr_is_empty_text($my_array){
    if(!empty($my_array)){
        foreach($my_array as $row){
            if(!empty($row)){
                if(!empty($row['value'])){
                   if(!isset($row['safe'])){
                       return 1;
                   }
                   return 0;
                }
            }
        }
    }
    return 1;
}
function hontza_solr_define_entity_field_name_item_fid($entity_field_name_array,$entity){
    $content_field_item_fid_array=hontza_solr_get_content_field_item_fid_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$content_field_item_fid_array);
}
function hontza_solr_get_content_field_item_fid_array($nid,$vid){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_item_fid} WHERE nid=%d AND vid=%d AND field_item_fid_value IS NOT NULL',$nid,$vid));
    while($row=db_fetch_object($res)){
        $result[]=$row->field_item_fid_value;
    }
    return $result;
}
function hontza_solr_filename_autocomplete_callback($string){
    $result=array();
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    $my_array=array();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //intelsat-2016-noticias-usuario
    $node_type_array=array('"item"');
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $node_type_array[]='"noticia"';
    }
    $where[]='node.type IN('.implode(',',$node_type_array).')';    
    $where[]='LOWER(files.filename) LIKE "%%s%%"';
    $sql='SELECT files.* 
    FROM {node} node 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
    LEFT JOIN {content_field_item_fid} item_fid ON node.vid=item_fid.vid
    LEFT JOIN {files} files ON item_fid.field_item_fid_value=files.fid
    WHERE '.implode(' AND ',$where);
    //print $sql;exit();
    $res=db_query($query=sprintf($sql,strtolower($string)));
    while ($row = db_fetch_object($res)) {
        if(!empty($row->filename)){
            $title=check_plain($row->filename);
            $result[$row->filename] = $title;
        }
    }
    drupal_json($result);
}
function hontza_solr_get_fid_by_filename($filename){
    if(!empty($filename)){
        $files_array=hontza_get_files_array($filename);
        if(!empty($files_array)){
           return $files_array[0]->fid;
        }
    }
    return '';
}
function hontza_get_files_array($filename){
    $result=array();
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    $my_array=array();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //intelsat-2016-noticias-usuario
    $node_type_array=array('"item"');
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $node_type_array[]='"noticia"';
    }
    $where[]='node.type IN('.implode(',',$node_type_array).')';
    $where[]='files.filename="'.$filename.'"';
    $sql='SELECT files.* 
    FROM {node} node 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
    LEFT JOIN {content_field_item_fid} item_fid ON node.vid=item_fid.vid
    LEFT JOIN {files} files ON item_fid.field_item_fid_value=files.fid
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while ($row = db_fetch_object($res)) {
        $result[]=$row;
    }
    return $result;
}
function hontza_solr_get_comments_files($nid){
    $result=array();
    $where=array();
    $where[]='u.nid='.$nid;
    $sql='SELECT u.* FROM {comment_upload} u WHERE '.implode(' AND ',$where);    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[$row->fid]=$row;
    }
    return $result;
}
function hontza_solr_set_files_key($files){
    $result=array();
    if(!empty($files)){
        foreach($files as $i=>$row){
            $result[$row->fid]=$row;
        }
    }
    return $result;
}
function hontza_solr_update_node_validate_status($node){
    //intelsat-2015
    $validate_status=red_despacho_get_validate_status($node);
    hontza_solr_update_item_validate_status($node,$validate_status);
}               