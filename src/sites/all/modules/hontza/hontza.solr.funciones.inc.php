<?php
function hontza_solr_funciones_menu_items($items_in){
    $items=$items_in;
    $items['hontza_solr/delete_canal_busqueda/%']=array(
    'title'=>t('Channel'),
    'page callback' => 'drupal_get_form',
    'page arguments'=>array('hontza_solr_delete_canal_busqueda_form'),    
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/search_result_saved']=array(
    'title'=>'Save search result',
    'page callback' => 'hontza_solr_funciones_search_result_saved_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/resultado_busqueda_solr']=array(
    'title'=>'Save Search Results',
    'page callback' => 'hontza_solr_funciones_resultado_busqueda_solr_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/results/%']=array(
    'title'=>'Results',
    'page callback' => 'hontza_solr_funciones_results_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/delete_resultado_busqueda_solr/%']=array(
    'title'=>t('Delete'),
    'page callback' => 'drupal_get_form',
    'page arguments'=>array('hontza_solr_funciones_delete_resultado_busqueda_solr_form'),    
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/%/bookmark_ajax'] = array(
    'title' => 'Bookmark',
    'page callback' => 'hontza_solr_funciones_bookmark_ajax_callback',
    'access arguments' => array('access content'),
    );
    $items['hontza_solr/%/no_bookmark_ajax'] = array(
    'title' => 'Bookmark',
    'page callback' => 'hontza_solr_funciones_no_bookmark_ajax_callback',
    'access arguments' => array('access content'),
    );
    $items['hontza_solr/bookmarks']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_bookmarks_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['vigilancia/bookmarks']=array(
    'title'=>'Bookmarked',
    'page callback' => 'hontza_solr_funciones_vigilancia_bookmarks_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/bookmark_multiple']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_bookmark_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/bookmark_multiple_mode']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_bookmark_multiple_mode_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['canales/%/bookmarks']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_canales_bookmarks_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/destacar_bookmark/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_destacar_bookmark_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/unmark_multiple/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_unmark_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/delete_multiple/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_delete_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/mark_multiple/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_funciones_mark_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
     $items['hontza_solr/validar_multiple/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_search_validar_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
      $items['hontza_solr/rechazar_multiple/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_solr_search_rechazar_multiple_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items=hontza_canal_comodin_menu_items($items);
    $items=hontza_solr_search_menu_items($items);
    return $items;
}
function hontza_solr_funciones_set_og_canales_busqueda_result($result_in){
    if(hontza_solr_is_solr_activado()){
        $result=$result_in;
        foreach($result as $i=>$row){
            if(isset($row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value) && !empty($row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value)){
                if(hontza_solr_is_busqueda_solr($row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value)){
                    //intelsat-2015
                    //se ha añadido solrsort=ds_created desc
                    $value=$row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value.'&canal_busqueda_nid='.$row->nid.'&solrsort=ds_created desc';
                    $result[$i]->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value=$value;
                }
            }
        }
        return $result;
    }
    return $result_in;
}
function hontza_solr_is_busqueda_solr($canal_busqueda_busqueda_value){
    $pos=strpos($canal_busqueda_busqueda_value,'my_solr/my_search');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_solr_delete_canal_busqueda_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    
    $form['canal_busqueda_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $url_info=parse_url($_REQUEST['destination']);
    $url_cancel=$url_info['path'];
    $form['cancel_btn']['#value']=l(t('Cancel'),$url_cancel,array('query'=>$url_info['query']));        
    return $form;
}
function hontza_solr_delete_canal_busqueda_form_submit($form,&$form_state){
    $my_grupo=og_get_group_context();
    $my_grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;        
    }
    $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
    $nid='';
    if(isset($form_state['values']['canal_busqueda_nid'])){
       $nid=$form_state['values']['canal_busqueda_nid'];
    }
    //
    if(!empty($nid)){
       node_delete($nid);
    }
    $_REQUEST['destination']='';
    if(!empty($my_grupo_nid)){
        drupal_goto('hontza_solr/busqueda_avanzada_solr',$query_busqueda_avanzada_solr);
    }else{
        drupal_goto('hontza_solr/busqueda_avanzada');
    }    
}
function hontza_solr_funciones_add_guardar_resultado_solr_js(){
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
         $('#exguardar_resultado_solr').jqm({ajax: '".$my_base_path."help_popup.php?nid=guardar_resultado_solr&w=500&h=400&my_grupo_nid=".$my_grupo_nid."&solr_busqueda_value=".$solr_busqueda_value."', trigger: 'a.jqm-trigger-guardar_resultado_solr',modal:true, toTop: true, overlay: 0});
         
     });";
     drupal_add_js($js,'inline');
}
function hontza_solr_funciones_define_search_result_buttons(){
    $html=array();
    $canal_busqueda_nid=$_REQUEST['canal_busqueda_nid'];
    if(!empty($canal_busqueda_nid)){
        $html[]=l(my_get_icono_action('delete', t('Delete current search')),'hontza_solr/delete_canal_busqueda/'.$canal_busqueda_nid,array('html'=>TRUE,'query'=>  drupal_get_destination()));    
        //intelsat-2015
        if(alerta_solr_inc_is_busqueda_rss_activado()){
            $html[]=alerta_solr_inc_get_busqueda_rss_solr_link($canal_busqueda_nid);
        }else{
            //intelsat-2016
            $rss_url_array=hontza_solr_get_busqueda(1,1);        
            $html[]=l(my_get_icono_action('solr_results_rss', t('Generate RSS')),$rss_url_array['q'],array('html'=>TRUE,'query'=>$rss_url_array['query'],'attributes'=>array('target'=>'_blank','style'=>'padding-left:10px;')));            
        }
    }else{
        hontza_solr_add_save_current_search_js();
        $html[]=l(my_get_icono_action('save_current_search', t('Save current search')),'hontza_solr/save_current_search',array('html'=>TRUE,'attributes'=>array('class'=>'jqm-trigger-save_current_search','style'=>'padding-left:5px;')));
        $html[]='<div id="exsave_current_search" class="jqmWindow jqmID2000"></div>';
        //
        //intelsat-2016
        if(alerta_solr_inc_is_busqueda_rss_activado()){
            hontza_solr_funciones_add_save_current_rss_js();
            $html[]='<div id="exsave_current_rss" class="jqmWindow jqmID2000"></div>';
            //intelsat-2016
            //$html[]=l(my_get_icono_action('solr_results_rss', t('Generate RSS')),'hontza_solr/save_current_rss',array('html'=>TRUE,'attributes'=>array('class'=>'jqm-trigger-save_current_rss')));
            $html[]=l(my_get_icono_action('publico_solr_results_rss', t('Generate RSS')),'hontza_solr/save_current_rss',array('html'=>TRUE,'attributes'=>array('class'=>'jqm-trigger-save_current_rss')));          
        }
    }
    /*if(hontza_solr_funciones_is_guardar_resultado_de_la_busqueda_activado()){
        hontza_solr_funciones_add_guardar_resultado_solr_js();
        $html[]=l(my_get_icono_action('guardar_resultado_solr','Save search result'),'hontza_solr/guardar_resultado_solr',array('html'=>TRUE,'attributes'=>array('class'=>'jqm-trigger-guardar_resultado_solr','style'=>'padding-left:10px;')));    
        $html[]='<div id="exguardar_resultado_solr" class="jqmWindow jqmID2000"></div>';
    }*/
    if(hontza_solr_funciones_is_select_bookmark_all()){
        $html[]='<b><label id="id_label_selected_news" style="padding-left:10px;">'.t('@selected news selected',array('@selected'=>$_SESSION['my_solr_total'])).'</label></b>';
    }
    return implode('&nbsp;',$html);    
}
function hontza_solr_funciones_guardar_resultado_solr_form(){
  //intelsat-2015
  $my_grupo=og_get_group_context();
  $my_grupo_nid='';
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $my_grupo_nid=$my_grupo->nid;        
  }
  $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
  //  
  $form = array();
  $form['#action'] = url('hontza_solr/search_result_saved');
  $form['nombre'] = array(
    '#type' => 'textfield',
    '#title' => 'Save search result',  
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
    '#value' => l(t('Cancel'),'hontza_solr/busqueda_avanzada_solr',array('query'=>$query_busqueda_avanzada_solr,'attributes'=>array('id'=>'id_a_close_window','class'=>'jqmClose','onclick'=>"self.close();")))
  );   
  $form['solr_busqueda_value']=array(
     '#type'=>'hidden',
     '#value'=>hontza_solr_get_busqueda(),     
  );
  return $form;
}
function hontza_solr_funciones_search_result_saved_callback(){
  //intelsat-2015
  $my_grupo=og_get_group_context();
  $my_grupo_nid='';
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $my_grupo_nid=$my_grupo->nid;        
  }
  //
  if(hontza_solr_funciones_is_guardar_resultado_de_la_busqueda_activado()){  
    if(hontza_solr_funciones_save_search_result_validate()){  
      hontza_solr_funciones_save_search_result_form_submit();    
    }
  }
  if(!empty($my_grupo_nid)){
    drupal_goto('hontza_solr/busqueda_avanzada_solr','f[0]=im_og_gid:'.$my_grupo_nid);      
  }else{
    drupal_goto('hontza_solr/busqueda_avanzada');
  }  
}
function hontza_solr_funciones_save_search_result_validate(){
    $nombre=$_POST['nombre'];
	if(!empty($nombre)){
		$node=node_load(array('type'=>'resultado_busqueda_solr','title'=>$nombre));
		if(isset($node->nid) && !empty($node->nid)){
			drupal_set_message('El nombre del resultado de la búsqueda ya existe','error');
                        return 0;
		}
	}
        return 1;
}
function hontza_solr_funciones_save_search_result_form_submit(){
  global $user,$base_url;
  //$busqueda = hontza_solr_get_busqueda();
  $busqueda=$_POST['solr_busqueda_value'];
  //
  $node = new stdClass();
  $node->type = 'resultado_busqueda_solr';
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
  node_save($node);
  $resultado_busqueda_solr_nid=$node->nid;
  $url_params='&is_my_all_results=1&is_guardar_resultado_solr=1&resultado_busqueda_solr_nid='.$resultado_busqueda_solr_nid;
  if(!empty($my_grupo_nid)){
    $grupo=node_load($my_grupo_nid);
    //$url=$base_url.'/'.$grupo->purl.'/hontza_solr/busqueda_avanzada';    
    //hontza_solr_funciones_save_resultados($node,$busqueda,$grupo);  
    //drupal_goto($url);
    $url=$base_url.'/'.$grupo->purl.'/'.$busqueda.$url_params;
  }else{
    //drupal_goto('hontza_solr/busqueda_avanzada');
    $url=$base_url.$busqueda.$url_params;
    print $url;exit();  
  }
  drupal_goto($url);
}
function hontza_solr_funciones_is_guardar_resultado_de_la_busqueda_activado(){
  if(hontza_solr_is_solr_activado()){
    if(defined('_IS_SOLR_GUARDAR_RESULTADO_BUSQUEDA') && _IS_SOLR_GUARDAR_RESULTADO_BUSQUEDA==1){
        return 1;
    }
    return 0;
  }  
  return 0;
}
function hontza_solr_funciones_save_resultados($resultado_busqueda_solr_nid,$results){
    if(!empty($resultado_busqueda_solr_nid)){
        hontza_solr_funciones_delete_resultado_busqueda_solr($resultado_busqueda_solr_nid);
        $nid_array=hontza_solr_get_results_nid_array($results);
        if(!empty($nid_array)){
            foreach($nid_array as $i=>$nid){
                hontza_solr_funciones_insert_resultado_busqueda_solr($nid,$resultado_busqueda_solr_nid);
            }
        }
    }
    drupal_goto('hontza_solr/resultado_busqueda_solr');
}
function hontza_solr_funciones_is_guardar_resultado_solr(){
  if(isset($_REQUEST['is_guardar_resultado_solr']) && !empty($_REQUEST['is_guardar_resultado_solr'])){
      return 1;
  }
  return 0;
}
function hontza_solr_funciones_delete_resultado_busqueda_solr($resultado_busqueda_solr_nid){
  db_query('DELETE FROM {resultado_busqueda_apache_solr} WHERE resultado_busqueda_solr_nid=%d',$resultado_busqueda_solr_nid);  
}
function hontza_solr_funciones_insert_resultado_busqueda_solr($nid,$resultado_busqueda_solr_nid){
  db_query('INSERT INTO {resultado_busqueda_apache_solr}(nid,resultado_busqueda_solr_nid) VALUES(%d,%d)',$nid,$resultado_busqueda_solr_nid);    
}
function hontza_solr_funciones_resultado_busqueda_solr_callback($mode=''){
    global $user;
    $headers=array();
    $my_grupo=og_get_group_context();
    $headers[]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[]='';
    //
    $my_limit=20;
    //
    $sort='asc';
    $field='node_title';
    $is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
            $is_numeric=0;
        }      
    }
    //
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        //$where[]='n.status=1';
        $where[]='node.uid='.$user->uid;
        $where[]='node.type="resultado_busqueda_solr"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $sql='SELECT node.*,node.title AS node_title 
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){  
                if($mode=='nodes'){
                    $rows[]=$node;
                }else{
                    $r=array();
                    //
                    $r[0]=$node->title;
                    $r[1]=array('data'=>hontza_solr_funciones_resultado_busqueda_solr_define_acciones($row->nid),'class'=>'td_nowrap');
                    $rows[]=$r;
                }
                $num_rows=TRUE;
            }
        }
        //
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //
    if($mode=='nodes'){
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    }
    //
    if ($num_rows) {
        if($mode!='nodes'){
            $output .= theme('table',$headers,$rows);
        }
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function hontza_solr_funciones_resultado_busqueda_solr_define_acciones($nid){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$nid.'/edit',array('html'=>TRUE,'query'=>'destination=hontza_solr/resultado_busqueda_solr'));         
    //$html[]=l(my_get_icono_action('viewmag',t('View Resource')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/resources_collection/'.$collection_nid));             
    $html[]=l(my_get_icono_action('delete',t('Delete')),'hontza_solr/delete_resultado_busqueda_solr/'.$nid,array('html'=>TRUE,'query'=>'destination=hontza_solr/resultado_busqueda_solr'));                     
    $html[]=l(my_get_icono_action('boletin_historico',t('Results')),'hontza_solr/results/'.$nid,array('html'=>TRUE));     
    return implode('&nbsp;',$html);     
}
function hontza_solr_funciones_results_callback(){
    $resultado_busqueda_solr_nid=arg(2);
    $my_limit=20;
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        $where[]='resultado_busqueda_apache_solr.resultado_busqueda_solr_nid='.$resultado_busqueda_solr_nid;
        $sql='SELECT resultado_busqueda_apache_solr.*
        FROM {resultado_busqueda_apache_solr} resultado_busqueda_apache_solr 
        WHERE '.implode(' AND ',$where).'
        ORDER BY resultado_busqueda_apache_solr.nid ASC';
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){  
                $rows[]=$node;
                $num_rows=TRUE;
            }
        }
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    //
    if ($num_rows) {
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function hontza_solr_funciones_in_pantalla_resultados_guardados(){
    if(hontza_solr_funciones_is_guardar_resultado_de_la_busqueda_activado()){
        $param0=arg(0);
        if(!empty($param0) && $param0=='hontza_solr'){
            $param1=arg(1);
            if(!empty($param1) && in_array($param1,array('resultado_busqueda_solr','results'))){
                return 1;
            }
        }
    }    
    return 0;
}
function hontza_solr_funciones_delete_resultado_busqueda_solr_form(){
    $form=array();
    $form['my_text']['#value']='Hay que implementar esta pantalla';
    return $form;
}
function hontza_solr_funciones_is_bookmark_activado(){
    $sareko_id_array=array('ROOT');
    if(in_array(_SAREKO_ID,$sareko_id_array)){
        return 1;
    }
    if(defined('_IS_BOOKMARK') && _IS_BOOKMARK==1){
        return 1;
    }
    if(hontza_solr_search_is_bookmark_activado_admin()){
        return 1;
    }
    if(defined('_IS_SOLR')){
        if(_IS_SOLR){
            return 1;
        }
    }
    return 0;
}
function hontza_solr_funciones_bookmark_action_class($node,$is_n=1){
    global $user;
    $result='item-bookmark';
    if(isset($node->nid) && !empty($node->nid)){
        $row=hontza_solr_funciones_get_bookmark_row($node->nid,$node->vid,$user->uid);
        if(!empty($row) && isset($row->nid) && !empty($row->nid)){
            $result='item-no-bookmark';    
        }else{
            $result='item-bookmark';
        }    
    }
    if($is_n){
        $result='n-'.$result;
    }
    return $result;
}
function hontza_solr_funciones_get_bookmark_row($nid,$vid,$uid){
    $where=array();
    //$res=db_query('SELECT * FROM {hontza_bookmark} WHERE nid=%d AND vid=%d AND uid=%d',$nid,$vid,$uid);
    $where[]='1';
    $where[]='hontza_bookmark.nid='.$nid;
    //intelsat-2015
    if(!empty($uid)){
        $where[]='hontza_bookmark.uid='.$uid;
    }
    $where[]=hontza_get_vigilancia_where_filter();    
    $sql='SELECT * 
    FROM {hontza_bookmark} hontza_bookmark
    LEFT JOIN {node} node ON hontza_bookmark.vid=node.vid
    WHERE '.implode(' AND ',$where);    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function hontza_solr_funciones_bookmark_link($node,$is_ajax=0){
    global $user;
    if(isset($node->nid) && !empty($node->nid)){
        $row=hontza_solr_funciones_get_bookmark_row($node->nid,$node->vid,$user->uid);
        if(!empty($row) && isset($row->nid) && !empty($row->nid)){
                $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Unmark'),'alt'=>t('Unmark'),'id'=>'id_bookmark_ajax_'.$node->nid,'class'=>'a_no_bookmark_ajax'));
                if($is_ajax){
                    unset($params['query']);
                }
                $label='';
                //$label=t('Selected');
                return l('','hontza_solr/'.$node->nid.'/no_bookmark',$params);
        }else{
            //return l(t('Select for the Bulletin'),'boletin_report/'.$node->nid.'/report',array('query'=>drupal_get_destination()));
            $label='';
            //$label=t('Select for the Bulletin');
            $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Mark'),'alt'=>t('Mark'),'id'=>'id_bookmark_ajax_'.$node->nid,'class'=>'a_bookmark_ajax'));
            if($is_ajax){
                unset($params['query']);
            }
            return l($label,'hontza_solr/'.$node->nid.'/bookmark',$params);
        }    
    }
    return '';
}
function hontza_solr_funciones_add_bookmark_ajax(){
   global $base_url;
   $purl='';
   $my_grupo=og_get_group_context(); 
   if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
   }
   $js='$(document).ready(function()
   {
            create_call_bookmark_ajax_functions();
            function call_bookmark_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/hontza_solr/"+nid+"/bookmark_ajax?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_bookmark_item_ajax_on_success(my_result);
				}
			});
            }
            function call_no_bookmark_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/hontza_solr/"+nid+"/no_bookmark_ajax?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_bookmark_item_ajax_on_success(my_result);                                    
				}
			});
            }
            function create_call_bookmark_ajax_functions(){
                $("a.a_bookmark_ajax").unbind( "click" );
                $("a.a_bookmark_ajax").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_bookmark_ajax_","");
                    call_bookmark_ajax(nid);
                    return false;
                });
                $("a.a_no_bookmark_ajax").unbind( "click" );
                $("a.a_no_bookmark_ajax").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_bookmark_ajax_","");
                    call_no_bookmark_ajax(nid);
                    return false;
                });
            }
            function set_bookmark_item_ajax_on_success(my_result){
                var my_parent=$("#id_bookmark_ajax_"+my_result.nid).parent();
                var my_class=my_parent.attr("class");
                my_parent.attr("class",set_bookmark_item_class_ajax(my_class));
                my_parent.html(my_result.a);
                create_call_bookmark_ajax_functions();
            }
            function set_bookmark_item_class_ajax(my_class){
                /*if(my_class=="n-item-bookmark"){
                    return "n-item-no-bookmark";
                }
                if(my_class=="n-item-no-bookmark"){
                    return "n-item-bookmark";
                }
                if(my_class=="item-bookmark"){
                    return "item-no-bookmark";
                }
                if(my_class=="item-no-bookmark"){
                    return "item-bookmark";
                }*/
                if(my_class=="n-item-bookmark"){
                    return "n-item-no-bookmark";
                }
                if(my_class=="n-item-no-bookmark"){
                    return "n-item-bookmark";
                }
                if(my_class=="item-bookmark"){
                    return "item-no-bookmark";
                }
                if(my_class=="item-no-bookmark"){
                    return "item-bookmark";
                }    
            }
   });';
   drupal_add_js($js,'inline');
}
function hontza_solr_funciones_bookmark_ajax_callback(){
    global $user;
    $result=array();
    $nid=arg(1);
    $node=node_load($nid);
    hontza_solr_funciones_bookmark_insert_yes($node->nid,$node->vid,$user->uid);
    //intelsat-2015
    red_solr_inc_update_node_bookmark($node);
    $result['nid']=$nid;
    //$node=node_load($nid);
    //gemini-2014
    //hontza_validar_con_accion($nid);
    $result['a']=hontza_solr_funciones_bookmark_link($node,1);
    print json_encode($result);
    exit(); 
}
function hontza_solr_funciones_bookmark_insert_yes($nid,$vid,$uid){
    db_query($sql=sprintf('INSERT INTO {hontza_bookmark}(nid,vid,uid) VALUES(%d,%d,%d)',$nid,$vid,$uid));
}
function hontza_solr_funciones_no_bookmark_ajax_callback(){
    global $user;
    $result=array();
    $nid=arg(1);
    $node=node_load($nid);    
    hontza_solr_funciones_delete_bookmark_row($node->nid,$node->vid,$user->uid);
    //intelsat-2015
    red_solr_inc_update_node_bookmark($node);    
    $result['nid']=$nid;
    $result['a']=hontza_solr_funciones_bookmark_link($node,1);
    print json_encode($result);
    exit();
}
function hontza_solr_funciones_delete_bookmark_row($nid,$vid,$uid){
    db_query('DELETE FROM {hontza_bookmark} WHERE nid=%d AND uid=%d',$nid,$uid);
}
function hontza_solr_funciones_bookmarks_callback($bookmark_type=''){
    global $user;
    hontza_solr_funciones_delete_bookmark_node_temporal();
    //$my_limit=20;
    $my_limit=red_despacho_get_nodes_limit(20);
    //print $my_limit.'<br>';
    $rows=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if($bookmark_type=='canales'){
            $canal_nid=arg(1);
            $sql=hontza_canales_default_sql($my_grupo,$canal_nid,'bookmarks',0);
        }else{
            //intelsat-2015
            $sql=red_solr_inc_get_bookmarked_sql($my_grupo);
        }
        //        
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){  
                $rows[]=$node;
                $num_rows=TRUE;
            }
        }
    }
    $_SESSION['bookmarks_rows']=$rows;
    
    if(hontza_solr_funciones_is_select_bookmark_all()){
        $rows=array_slice($rows,0,$my_limit);      
    }
        
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    //
    if ($num_rows) {
        $bookmark_form_ini=hontza_solr_funciones_get_bookmark_ini(0);
        $output=$bookmark_form_ini.$output;
        //$output.='</form>';
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function hontza_solr_funciones_vigilancia_bookmarks_callback(){
    drupal_set_title(t('Monitoring').' - '.t('Bookmarked News'));
    hontza_solr_funciones_unset_my_results_solr();
    $html=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $html[]=hontza_vigilancia_menu();
        $html[]=hontza_solr_funciones_bookmarks_callback();
    }
    return implode('',$html);
}
function hontza_solr_funciones_canales_bookmarks_callback(){
    drupal_set_title(t('Bookmarked'));
    $html=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $html[]=hontza_canales_menu();
        $html[]=hontza_solr_funciones_bookmarks_callback('canales');
    }    
    return implode('',$html);
}
function hontza_solr_funciones_get_node_bookmark_checkbox_html($node){
    //return '';
    //if(hontza_is_vigilancia('bookmarks')){
    if(hontza_solr_funciones_in_pantalla_bookmarks() || hontza_solr_is_resultados_pantalla()){    
        $checked='';
        if(hontza_solr_funciones_is_select_bookmark_all()){
            $checked=' checked="checked"';
        }else{
            $checked=hontza_solr_search_get_checked($node->nid);
        }
        return '<input type="checkbox" id="node_'.$node->nid.'_bookmark_txek" name="node_bookmark_txek['.$node->nid.']" class="node_bookmark_txek_class" value="1"'.$checked.'/>';
    }else{
        return '';
    }
}
function hontza_solr_funciones_get_bookmark_ini($is_solr){
    if(hontza_solr_search_is_usuario_lector()){
        return '';
    }
    $style='float:left;padding-left:20px;';
    $html=array();
    //intelsat-2016
    if(hontza_solr_is_resultados_pantalla()){
        red_solr_inc_add_remaining_html($html);
    }            
    //$html[]='<form id="bookmarked_form" method="POST" action="'.url('hontza_solr/bookmark_multiple').'">';
    $html[]='<fieldset>';
    $html[]='<legend>'.t('Bulk Actions').'</legend>';    
    /*$html[]='<div class="fieldset-wrapper" style="float:left;">';
    $html[]='<select id="id_bookmark_select">'.hontza_solr_funciones_get_bookmark_select_options().'<select/>';
    $html[]='<input id="edit-send-node-btn" class="form-submit" type="button" value="'.t('Send').'" name="send_node_btn">';
    $html[]='</div>';*/
    $html[]='<div style="float:left;padding-left:20px">';
    $checked='';
    $title_check=t('Select all');
    if(hontza_solr_funciones_is_select_bookmark_all()){
        $checked=' checked="checked"';
        $title_check=t('Deselect all');
        hontza_solr_search_unset_selected_node_id_array();
    }
    $html[]='<input type="checkbox" id="select_bookmark_all_txek" name="select_bookmark_all_txek"'.$checked.' title="'.$title_check.'"/>';
    $html[]='</div>';
    $html[]=hontza_solr_search_get_validar_link($style);
    $html[]=hontza_solr_search_get_rechazar_link($style);    
    $html[]='<div style="'.$style.'">';        
    $html[]=hontza_solr_search_get_tag_popup_link();
    $html[]='</div>';    
    $html[]='<div style="'.$style.'">';    
    $html[]=l(my_get_icono_action('fivestar', t('Rate'),''),'hontza_solr/fivestar_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_fivestar_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));
    $html[]='</div>';
    //intelsat-2015
    if(hontza_grupos_is_activo_pestana('debate')){
        $html[]='<div style="'.$style.'">';
        $html[]=l(my_get_icono_action('debatir', t('Discuss'),''),'hontza_solr/discuss_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_discuss_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));
        $html[]='</div>';
    }
    //intelsat-2015
    if(hontza_grupos_is_activo_pestana('wiki')){
        $html[]='<div style="'.$style.'">';
        $html[]=l(my_get_icono_action('trabajo', t('Collaborate'),''),'hontza_solr/collaborate_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_collaborate_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));        
        $html[]='</div>';
    }
    //intelsat-2015
    if(hontza_grupos_is_activo_pestana('idea')){
        $html[]='<div style="'.$style.'">';
        $html[]=l(my_get_icono_action('idea', t('Ideate'),''),'hontza_solr/idea_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_idea_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
        $html[]='</div>';
    }    
    if(is_show_destacar_link()){
        $html[]='<div style="'.$style.'">';
        $html[]=l(my_get_icono_action('destacar', t('Highlight'),''),'hontza_solr/destacar_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_destacar_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
        $html[]='</div>';    
    }
    //intelsat-2015
    if(red_despacho_is_show_boletin_report_link()){
        //intelsat-2016
        $html[]=red_solr_inc_get_boletin_report_bulk_actions_links($style);            
    }
    if(!hontza_is_canal_usuarios('bookmarks')){
        if(hontza_canal_comodin_backup_access()){
            $html[]='<div style="'.$style.'">';
            $html[]=l(my_get_icono_action('backup_channel', t('Move'),''),'hontza_solr/backup_channel_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_backup_channel_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
            $html[]='</div>';
        }
    }
    //intelsat-2016
    $html[]=red_solr_inc_get_mark_bulk_actions_links($is_solr,$style);
    //intelsat-2016
    if(red_solr_inc_is_show_bookmark_reclasificar_tipo_fuente_link()){        
        $html[]=red_solr_inc_get_bookmark_reclasificar_tipo_fuente_link($style);
    }    
    if(hontza_solr_search_modificar_taxonomia_access()){
        $html[]=hontza_solr_search_get_modificar_taxonomia_link($style);        
    }
    if(is_super_admin()){
      $html[]='<div style="'.$style.'">';
      $html[]=l(my_get_icono_action('delete', t('Delete'),''),'hontza_solr/delete_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_delete_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
      $html[]='</div>';
    }  
    $html[]='</fieldset>';
    hontza_solr_funciones_add_bookmark_multiple_js();
    return implode('',$html);
}
function hontza_solr_funciones_bookmark_multiple_callback(){
    echo print_r($_POST,1);
    exit();
}
function hontza_solr_funciones_add_bookmark_multiple_js(){
   hontza_solr_search_get_js_variables($bookmark_type,$canal_nid,$tid,$is_solr,$destination,$current_path_all_selected,$current_path,$select_msg);
   $js='$(document).ready(function()
   {
    modificar_en_grupo_link();
    $("#edit-send-node-btn").click(function() {
        var mode=$("#id_bookmark_select").attr("value");
        bookmark_multiple_post_ajax(mode);
        return false;
    });
    $(".a_class_bookmark_multiple_mode").click(function() {
        var mode=hontza_solr_funciones_get_bookmark_mode($(this).attr("id"));
        bookmark_multiple_post_ajax(mode);
        return false;
    });
    $("#select_bookmark_all_txek").change(function() {
        var is_selected=$(this).attr("checked");
        //select_bookmark_all(is_selected);
        if(is_selected){
            location.href="'.$current_path_all_selected.'";
        }else{
            location.href="'.$current_path.'";
        }
        return false;
    });
    function bookmark_multiple_post_ajax(mode){
        var my_array=new Array();
        var my_id="";
        var is_all_selected=$("#select_bookmark_all_txek").attr("checked");
        if(is_all_selected){
            node_id_array="is_all_selected";
        }else{
            $(".node_bookmark_txek_class").each(function() {
                var is_txeked=$(this).attr("checked");
                if(is_txeked){    
                    my_id=$(this).attr("id");
                    my_id=my_id.replace("node_","");
                    my_id=my_id.replace("_bookmark_txek","");
                    my_array.push(my_id);
                }
            });        
            node_id_array=my_array.join(",");
        }
        if(node_id_array.length==0){
            alert("'.$select_msg.'");
        }else{
            my_url="'.url('hontza_solr/bookmark_multiple_mode').'?mode="+mode+"&node_id_array="+node_id_array+"&bookmark_type='.$bookmark_type.'&canal_nid='.$canal_nid.'&tid='.$tid.'&is_solr='.$is_solr.$destination.'";
            if(mode=="tag" || mode=="fivestar"){
                HontzaPopupCenter_solr(my_url,"mywindow",350,250);
            }else{
                location.href=my_url;
            }
            //alert(my_url);
        }    
    }
    '.hontza_solr_search_get_bookmark_mode_js().hontza_solr_search_get_otro_tab_navegador_js().hontza_solr_search_on_item_checkbox_click_js($is_solr).'
    function select_bookmark_all(is_selected){
        $(".node_bookmark_txek_class").each(function() {
            $(this).attr("checked",is_selected);
        });
    }
    '.hontza_solr_search_get_popup_center_js().'
   });';
   drupal_add_js($js,'inline');
}
function hontza_solr_funciones_bookmark_multiple_mode_callback(){
   hontza_solr_funciones_delete_bookmark_node_temporal();
   hontza_solr_search_is_usuario_lector_access_denied();
   $mode=$_REQUEST['mode'];
   $_SESSION['selected_node_id_array']=$_REQUEST['node_id_array'];
   $node_id_array=hontza_solr_funciones_get_request_node_id_array();
   $html='';
   $is_return=1;
   $mode_array=array('backup_channel','tag','fivestar','debate','area_trabajo','idea','destacar','report','unmark','delete','mark','modificar_taxonomia'
   ,'validar','rechazar','reclasificar_tipo_fuente','unselect_report');
   if(in_array($mode,$mode_array)){
    $url_return='vigilancia/bookmarks';
    $url_return=hontza_solr_search_define_url_return($url_return);
    $is_solr=0;
    if(hontza_solr_funciones_is_request_solr()){
        $is_solr=1;
        $my_destination=$_REQUEST['destination'];
        if(empty($my_destination)){
             $my_destination=$_REQUEST['my_destination'];
        }
        $url_return=base64_decode($my_destination);
    }else{
        if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode_canales()){
            $canal_nid='';
            if(isset($_REQUEST['canal_nid']) && !empty($_REQUEST['canal_nid'])){
                $canal_nid=$_REQUEST['canal_nid'];
                $url_return='canales/'.$canal_nid.'/bookmarks';
            }
        }else if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode_categorias()){
            $tid='';
            if(isset($_REQUEST['tid']) && !empty($_REQUEST['tid'])){
                $tid=$_REQUEST['tid'];
                $url_return='canales/my_categorias/'.$tid.'/bookmarks';
            }
        }
    }
    $url_return=hontza_solr_search_add_url_return_selected_node_id_array($url_return);
   // 
   if(!empty($node_id_array)){
        $node_id_array=explode(',',$node_id_array);     
        if(in_array($mode,array('tag','fivestar'))){
         //return drupal_get_form('community_tags_form');
         $node=hontza_solr_search_bookmark_temporal_node_save($node_id_array);
        }
        if($mode=='tag'){
         $title=t('Tag');   
         $html=community_tags_node_view($node);
         //$html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
         $is_return=0;
        }else if($mode=='fivestar'){
         $title=t('Rate');      
         $html=fivestar_widget_form($node);
         //$html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
         $is_return=0;
        }else if($mode=='debate'){
         $title=t('Discuss');   
         $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
         $html.=hontza_node_enlazar_debate_callback();
        }else if($mode=='area_trabajo'){
         $title=t('Collaborate');   
         $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
         $html.=hontza_node_enlazar_wiki_callback();
        }else if($mode=='idea'){
         $title=t('Ideate');     
         $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
         $html.=idea_node_enlazar_callback();
        }else if($mode=='backup_channel'){
         $title=t('Move');   
            if(hontza_canal_comodin_is_canal_comodin_activado()){
               if(!empty($node_id_array)){
                   $is_return=0;
                   $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                   $html.=hontza_canal_comodin_backup_channel_html($node_id_array,$url_return,$is_solr);              
               }       
            }
       }else if($mode=='destacar'){
            $title=t('Highlight');
            if(!empty($node_id_array)){
                $is_return=0;
                $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                $html.=hontza_solr_funciones_destacar_html($node_id_array,$url_return,$is_solr);      
            }    
        }else if($mode=='report'){
            $title=t('Select for the Bulletin');
            if(!empty($node_id_array)){
                $is_return=0;
                $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                $html.=hontza_solr_funciones_report_html($node_id_array,$url_return,$is_solr);    
            }    
        }else if($mode=='unmark'){
            $title=t('Unmark');
            if(!empty($node_id_array)){
                $is_return=0;
                $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                //intelsat-2016
                //$html.=hontza_solr_funciones_unmark_html($node_id_array,$url_return);
                $html.=hontza_solr_funciones_unmark_html($node_id_array,$url_return,$is_solr);
            }    
        }else if($mode=='delete'){
            $title=t('Delete');
            if(!empty($node_id_array)){
                $is_return=0;
                $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                $html.=hontza_solr_funciones_delete_html($node_id_array,$url_return,$is_solr);      
            }    
        }else if($mode=='mark'){
            $title=t('Mark');
            if(!empty($node_id_array)){
                $is_return=0;
                $html.=hontza_solr_funciones_get_selected_node_titles($node_id_array);
                $html.=hontza_solr_funciones_mark_html($node_id_array,$url_return,$is_solr);      
            }    
        }else if($mode=='modificar_taxonomia'){
            $html.=hontza_solr_search_modificar_taxonomia_titles_html($node_id_array,$url_return,$is_return,$title,$is_solr);
        }else if($mode=='validar'){
            $html.=hontza_solr_search_validar_titles_html($node_id_array,$url_return,$is_return,$title,$is_solr);
        }else if($mode=='rechazar'){
            $html.=hontza_solr_search_rechazar_titles_html($node_id_array,$url_return,$is_return,$title,$is_solr);
        //intelsat-2016            
        }else if($mode=='reclasificar_tipo_fuente'){            
            $html.=red_solr_inc_reclasificar_tipo_fuente_titles_html($node_id_array,$url_return,$is_return,$title,$is_solr);
        }else if($mode=='unselect_report'){
            $html.=red_solr_inc_unselect_report_titles_html($node_id_array,$url_return,$is_return,$title,$is_solr);
        }
   }
        if($is_return){
            //if($is_solr){
            $url_info=parse_url($url_return);
            $html.=l(t('Return'),$url_info['path'],array('query'=>$url_info['query']));
            /*}else{
                $html.=l(t('Return'),$url_return);
            }*/    
        }else{
            $html.=hontza_solr_search_get_bookmark_close_button($mode);
        }
   } 
   drupal_set_title($title);
   return $html;
}
function hontza_solr_funciones_in_pantalla_bookmarks(){
   if(hontza_is_vigilancia('bookmarks')){
       return 1;
   }else if(hontza_is_canales('bookmarks')){
       return 1;
   }else if(hontza_is_canales_categorias('bookmarks')){
       return 1;
   }else if(hontza_is_canal_usuarios('bookmarks')){
       return 1;
   }
   return 0;
}
function hontza_solr_funciones_is_pantalla_bookmark_multiple_mode($mode=''){
    if(hontza_solr_funciones_is_bookmark_activado()){    
        $param0=arg(0);
        if(!empty($param0) && $param0=='hontza_solr'){
            $param1=arg(1);
            if(!empty($param1) && $param1=='bookmark_multiple_mode'){
                if(empty($mode)){
                    return 1;
                }else{
                    $konp=$_REQUEST['mode'];
                    if($konp==$mode){
                        return 1;
                    }
                }    
            }
        }
        if(hontza_solr_search_is_popup()){
            return 1;
        }
    }
    return 0;
}
function hontza_solr_funciones_is_bookmark_tag_save($node){
    $mode=$_REQUEST['mode'];
    if($mode=='tag'){
        if($node->type=='bookmark_temporal'){
            $node_id_array=$_REQUEST['node_id_array'];
            if(!empty($node_id_array)){
                return 1;
            }
        }    
    }
    return 0;
}
function hontza_solr_funciones_bookmark_node_tag_save($node,$removed_tags){
    global $user;
    $node_id_array=hontza_solr_funciones_get_request_node_id_array();
    $node_id_array=explode(',',$node_id_array);
    //intelsat-2015
    $kont=0;
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $bookmark_node=node_load($nid);
            if(isset($bookmark_node->nid) && !empty($bookmark_node->nid)){
                $taxonomy=$node->taxonomy;
                if(!empty($taxonomy)){
                    foreach($taxonomy as $tid=>$term){
                        if(!isset($bookmark_node->taxonomy[$tid])){
                            $bookmark_node->taxonomy[$tid]=$term;
                        }
                    }
                }
                node_save($bookmark_node);
                community_tags_hontza_remove_tag($removed_tags,$bookmark_node);
                //hontza_solr_funciones_delete_bookmark_row($bookmark_node->nid,$bookmark_node->vid,$user->uid);
                hontza_canal_rss_solr_on_bookmark_node_tag_save($nid);                
                //intelsat-2015
                $kont++;
            }    
        }
    }
    //intelsat-2015
    red_set_bulk_command_executed_message($kont);
}
function hontza_solr_funciones_delete_bookmark_node_temporal(){
   if(isset($_SESSION['bookmark_nid_temporal_array']) && !empty($_SESSION['bookmark_nid_temporal_array'])){
       foreach($_SESSION['bookmark_nid_temporal_array'] as $i=>$nid){
           node_delete($nid);
       }
   }
}
function hontza_solr_funciones_get_bookmark_select_options(){
   $html=array();
   $html[]='<option value="backup_channel">'.t('Backup channel').'</option>';
   $html[]='<option value="area_trabajo">'.t('Collaboration').'</option>';      
   $html[]='<option value="debate">'.t('Discussion').'</option>';
   $html[]='<option value="idea">'.t('Ideate').'</option>';
   $html[]='<option value="fivestar">Fivestar</option>';   
   $html[]='<option value="tag">'.t('Tag').'</option>';
   return implode('',$html);
}
function hontza_solr_funciones_save_bookmark_multiple_vote($node){
   global $user; 
   if(hontza_solr_funciones_is_bookmark_activado()){ 
        if($node->type=='bookmark_temporal'){
            $node_id_array=$node->field_node_id_array[0]['value'];
            if(!empty($node_id_array)){
                $node_id_array=unserialize($node_id_array);
                if(!empty($node_id_array)){
                    $value=hontza_get_node_puntuacion_media_para_txt($node->nid,1);
                    foreach($node_id_array as $i=>$nid){
                        $node_vote=node_load($nid);
                        hontza_solr_funciones_insert_votingapi($node_vote,$value);
                        //intelsat-2016
                        red_solr_inc_update_node_rated($node_vote);
                        //hontza_solr_funciones_delete_bookmark_row($node_vote->nid,$node_vote->vid,$user->uid);
                        //intelsat-2015
                        hontza_canal_rss_solr_on_bookmark_multiple_vote($nid);
                    }    
                }    
            }
        }
   }
}
function hontza_solr_funciones_insert_votingapi($node,$value){
    global $user;
    hontza_solr_funciones_delete_votingapi_vote($node->nid,$user->uid);    
    $votes=array();
    $vote['content_type']='node';
    $vote['content_id']=$node->nid;
    $vote['value_type']='percent';
    $vote['value']=$value;
    $vote['tag']='vote';
    $vote['uid']=$user->uid;
    $vote['vote_source']=ip_address();
    $vote['timestamp']=time();
    $votes[0]=$vote;
    votingapi_add_votes($votes);
    votingapi_recalculate_results('node',$node->nid,TRUE);
}
function hontza_solr_funciones_get_item_categorias_tematicas($node,$is_ficha_completa=0,$is_js=0){
    $html=array();
    $ul_id=red_despacho_get_item_canal_category_tid_ul_id($node->nid);
    $html[]='<ul id="'.$ul_id.'">';
    if(in_array($node->type,array('my_report','wiki','debate','canal_usuario'))){
        $content=hontza_canal_rss_get_node_categorias_tematicas($node,$is_ficha_completa,$is_js);
    }else{
        $content=hontza_solr_search_get_item_categorias_tematicas($node,$is_ficha_completa,$is_js);
    }
    if($is_js){
        return $content;
    }
    if(!empty($content)){
        $html[]=$content;
    }
    $html[]='</ul>';
    return implode('',$html);
}
function hontza_solr_funciones_get_item_source_types($node,$is_ficha_completa=0){
    $html=array();
    $ul_id=red_despacho_get_item_source_tid_ul_id($node->nid);
    $html[]='<ul id="'.$ul_id.'">';
    $content=hontza_solr_search_get_item_source_types($node,$is_ficha_completa);
    if(!empty($content)){
        $html[]=$content;
    }
    $html[]='</ul>';
    //red_despacho_add_item_source_tid_replace_popup_js($ul_id);
    return implode('',$html);
}
function hontza_solr_funciones_get_tid_by_term_class($tid,$s){
    $find='<li class="taxonomy_term_';
    $pos=strpos($s,$find);
    if($pos===FALSE){
        return $tid;
    }else{
        $result=substr($s,$pos+strlen($find));
        $pos_end=strpos($result,' ');
        if($pos_end===FALSE){
            $pos_end=strpos($result,'"');
            if($pos_end===FALSE){
                return $tid;
            }else{
                $result=substr($result,0,$pos_end);
                return $result;
            }
        }else{
            $result=substr($result,0,$pos_end);
            return $result;
        }
        return $result;        
    }
}
function hontza_solr_funciones_add_solr_filter_icon($v){
    //if(hontza_solr_is_solr_activado()){
        $img=my_get_icono_action('delete_solr_filter', t('Delete filter'));
        $pos=strpos($v,'>');
        if($pos===FALSE){
            return $v;
        }else{
            $s=substr($v,0,$pos+1);
            $end=substr($v,$pos+1);
            return $s.$img.$end;
        }
    //}
    return $v;
}
function hontza_solr_funciones_get_active_filters_block_content(){
    global $user;
    //if(hontza_solr_is_solr_activado()){
        if(isset($user->uid) && !empty($user->uid)){
            $my_selected_categoria=red_funciones_get_filtro_por_categoria();
            //intelsat-2015
            $my_selected_tipos_fuente=red_despacho_get_selected_tipos_fuente();            
            $selected_canal_nid=red_funciones_get_filtro_por_canal();
            //intelsat-2015
            //if(!empty($my_selected_categoria) || !empty($selected_canal_nid)){
            if(!empty($my_selected_categoria) || !empty($selected_canal_nid) || !empty($my_selected_tipos_fuente)){
                $html=array();
                //intelsat-2015
                if(!empty($my_selected_tipos_fuente)){
                    red_despacho_add_filter_block_term($html,$my_selected_tipos_fuente);
                }
                if(!empty($my_selected_categoria)){
                    red_despacho_add_filter_block_term($html,$my_selected_categoria);
                }                
                if(!empty($selected_canal_nid)){
                    $label='';
                    if(hontza_solr_funciones_is_canal_usuarios()){
                        $my_user=user_load($selected_canal_nid);
                        if(isset($my_user->uid) && !empty($my_user->uid)){
                            $label=$my_user->name;
                        }
                    }else{
                        $canal=node_load($selected_canal_nid);
                        if(isset($canal->nid) && !empty($canal->nid)){
                            $label=$canal->title;
                        }    
                    }
                    //
                    if(!empty($label)){
                        $img=my_get_icono_action('delete_solr_filter', t('Delete filter'));
                        if(!empty($my_selected_categoria)){
                            $html[]='<li'.red_despacho_get_li_style().'>'.l($img.$label,'canales/my_categorias/'.$my_selected_categoria.'/validados',array('html'=>true,'attributes'=>array('class'=>'active'))).'</li>';
                        }else{
                            $html[]='<li'.red_despacho_get_li_style().'>'.l($img.$label,'vigilancia/validados',array('html'=>true,'attributes'=>array('class'=>'active'))).'</li>';
                        }
                    }    
                }
                if(!empty($html)){
                    return '<ul>'.implode('',$html).'</ul>';
                }    
            }
        }    
    //}
    return '';
}
function hontza_solr_funciones_is_canal_usuarios(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canal-usuarios'){
        return 1;
    }
    return 0;
}
function hontza_solr_funciones_delete_votingapi_vote($nid,$uid){
    db_query('DELETE FROM {votingapi_vote} WHERE content_id=%d AND uid=%d',$nid,$uid);
}
function hontza_solr_funciones_get_selected_node_titles($node_id_array){
    return '';
    /*$html=array();
    $html[]='<div><b>'.t('Selected News').':</b></div>';
    $html[]='<ul>';
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            $html[]='<li>'.$node->title.'</li>';
        }    
    }
    $html[]='</ul>';
    return implode('',$html);*/
}
function hontza_solr_funciones_add_delete_file_filtros($my_array){
    $result=array();
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='itm_field_item_fid'){
                    $file=my_get_file($value_array[1]);
                    if(isset($file->filename) && !empty($file->filename)){
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_fid',$my_array);
                        $result[]=$icono_link.$file->filename;
                    }
                }
            }
        }
    }    
    return $result;
}
function hontza_solr_funciones_is_pantalla_bookmark_multiple_mode_canales($type='canales'){
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
        if(isset($_REQUEST['bookmark_type']) && !empty($_REQUEST['bookmark_type']) && $_REQUEST['bookmark_type']==$type){
            return 1;
        }        
    }
    return 0;
}
function hontza_solr_funciones_is_pantalla_bookmark_multiple_mode_categorias(){
    return hontza_solr_funciones_is_pantalla_bookmark_multiple_mode_canales('categorias');
}
function hontza_solr_funciones_node_feed($nids = FALSE, $channel = array()) {
  global $base_url, $language;

  if ($nids === FALSE) {
    $nids = array();
    $result = db_query_range(db_rewrite_sql('SELECT n.nid, n.created FROM {node} n WHERE n.promote = 1 AND n.status = 1 ORDER BY n.created DESC'), 0, variable_get('feed_default_items', 10));
    while ($row = db_fetch_object($result)) {
      $nids[] = $row->nid;
    }
  }
  //$item_length = variable_get('feed_item_length', 'teaser');
  //intelsat-2015
  $item_length ='my_content';
  //
  $namespaces = array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/');
  $items = '';
  foreach ($nids as $nid) {
    // Load the specified node:
    $item = node_load($nid);
    if(!(isset($item->nid) && !empty($item->nid))){
      continue;
    }
    $item->build_mode = NODE_BUILD_RSS;
    $item->link = url("node/$nid", array('absolute' => TRUE));

    if ($item_length != 'title') {
      $teaser = ($item_length == 'teaser') ? TRUE : FALSE;

      // Filter and prepare node teaser
      if (node_hook($item, 'view')) {
        $item = node_invoke($item, 'view', $teaser, FALSE);
      }
      else {
        $item = node_prepare($item, $teaser);
      }
      // Allow modules to change $node->content before the node is rendered.
      node_invoke_nodeapi($item, 'view', $teaser, FALSE);
      // Set the proper node property, then unset unused $node property so that a
      // bad theme can not open a security hole.
      $content = drupal_render($item->content);
      if ($teaser) {
        $item->teaser = $content;
        unset($item->body);
      }
      else {
        $item->body = $content;
        unset($item->teaser);
      }
    
      // Allow modules to modify the fully-built node.
      node_invoke_nodeapi($item, 'alter', $teaser, FALSE);
    }

    // Allow modules to add additional item fields and/or modify $item
    $extra = node_invoke_nodeapi($item, 'rss item');
    $extra = array_merge($extra, array(array('key' => 'pubDate', 'value' => gmdate('r', $item->created)), array('key' => 'dc:creator', 'value' => $item->name), array('key' => 'guid', 'value' => $item->nid .' at '. $base_url, 'attributes' => array('isPermaLink' => 'false'))));
    foreach ($extra as $element) {
      if (isset($element['namespace'])) {
        $namespaces = array_merge($namespaces, $element['namespace']);
      }
    }

    // Prepare the item description
    switch ($item_length) {
      case 'fulltext':
        $item_text = $item->body;
        break;
      case 'teaser':
        $item_text = $item->teaser;
        if (!empty($item->readmore)) {
          $item_text .= '<p>'. l(t('read more'), 'node/'. $item->nid, array('absolute' => TRUE, 'attributes' => array('target' => '_blank'))) .'</p>';
        }
        break;
      case 'title':
        $item_text = '';
        break;
      case 'my_content':
        $item_text=hontza_content_resumen($item,0,0);  
        break;  
    }

    $items .= hontza_solr_funciones_format_rss_item($item->title, $item->link, $item_text, $extra,$item);
  }

  $channel_defaults = array(
    'version'     => '2.0',
    'title'       => variable_get('site_name', 'Drupal'),
    'link'        => $base_url,
    'description' => variable_get('site_mission', ''),
    'language'    => $language->language
  );
  $channel = array_merge($channel_defaults, $channel);

  $output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
  $output .= "<rss version=\"". $channel["version"] ."\" xml:base=\"". $base_url ."\" ". drupal_attributes($namespaces) .">\n";
  $output .= format_rss_channel($channel['title'], $channel['link'], $channel['description'], $items, $channel['language']);
  $output .= "</rss>\n";

  drupal_set_header('Content-Type: application/rss+xml; charset=utf-8');
  print $output;
}
function hontza_solr_funciones_format_rss_item($title, $link, $description, $args = array(),$item='') {
  $output = "<item>\n";
  $output .= ' <title>'. check_plain($title) ."</title>\n";
  $output .= ' <link>'. check_url($link) ."</link>\n";
  $output .= ' <description>'. check_plain($description) ."</description>\n";
  $output .= format_xml_elements($args);
  $output .='<created>' . date("Y-m-d H:i:s",$node->created) . '</created>';
  $output .= "</item>\n";

  return $output;
}
function hontza_solr_funciones_get_item_canal_title($node){
    if(isset($node->field_item_canal_reference) && isset($node->field_item_canal_reference[0]) && isset($node->field_item_canal_reference[0]['nid'])){
        $canal=node_load($node->field_item_canal_reference[0]['nid']);
        if(isset($canal->nid) && !empty($canal->nid)){
            return $canal->title;
        }
    }        
    return '';
}
function hontza_solr_funciones_get_title_volver_simbolo_img(){
    $html=array();
    $url='vigilancia/validados';
    $html[]=l(my_get_icono_action('back32', t('Back')),$url,array('html'=>TRUE));
    return implode('',$html);
}
function hontza_solr_funciones_destacar_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);
    drupal_goto('hontza_solr/destacar_bookmark/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);
    /*$html[]='<div>'.l(t('Highlight'),'hontza_solr/destacar_bookmark/'.$node_id_array_string,array('query'=>'destination='.$url_return.'&is_solr='.$is_solr)).'</div>';
    if($is_solr){
        $url_info=parse_url($url_return_in);
        $html[]='<div>'.l(t('Return'),$url_info['path'],array('query'=>$url_info['query'])).'</div>';
    }else{
        $html[]='<div>'.l(t('Return'),$url_return).'</div>';
    }    
    return implode('&nbsp;',$html);*/
}
function hontza_solr_funciones_destacar_bookmark_callback(){
    $node_id_array=explode(',',arg(2));
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg($node_id_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            if($node->type=='noticia'){
                update_noticia_destacada($nid,1);
            }else{
                update_item_carpeta_destacada($nid,1);
            }
            $updated=0;
            hontza_solr_set_item_solr_updated($node,$updated);
            //if(red_solr_inc_is_rated_clear_node_index($seleccionado_boletin)){
                hontza_canal_rss_solr_clear_node_index($node,$node->nid);
            //}           
        }    
    }
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);
    hontza_solr_funciones_redirect();    
}
function hontza_solr_funciones_report_html($node_id_array,$url_return_in,$is_solr=0,$type='report_bookmark'){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);
    drupal_goto('boletin_report/'.$type.'/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);
    /*$html[]='<div>'.l(t('Select for the Bulletin'),'boletin_report/report_bookmark/'.$node_id_array_string,array('query'=>'destination='.$url_return.'&is_solr='.$is_solr)).'</div>';
    if($is_solr){
        $url_info=parse_url($url_return_in);
        $html[]='<div>'.l(t('Return'),$url_info['path'],array('query'=>$url_info['query'])).'</div>';
    }else{
        $html[]='<div>'.l(t('Return'),$url_return).'</div>';
    }  
    return implode('&nbsp;',$html);*/
}
function hontza_solr_funciones_unmark_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    //intelsat-2016
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }    
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);        
    drupal_goto('hontza_solr/unmark_multiple/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);
    /*$html=array();
    $html[]='<div>'.l(t('Unmark'),'hontza_solr/unmark_multiple/'.implode(',',$node_id_array),array('query'=>'destination='.$url_return)).'</div>';
    $html[]='<div>'.l(t('Return'),$url_return).'</div>';
    return implode('&nbsp;',$html);*/
}
function hontza_solr_funciones_unmark_multiple_callback(){
    global $user;
    $node_id_array=explode(',',arg(2));
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg($node_id_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            hontza_solr_funciones_delete_bookmark_row($nid,'',$user->uid);
            //intelsat-2016-noticias-usuario
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                red_solr_inc_update_node_bookmark($node);
                hontza_canal_rss_solr_clear_node_index($node,$nid);
            }
        }    
    }
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);    
    hontza_solr_funciones_redirect();
}
function hontza_solr_funciones_delete_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);
    $html[]='<div>'.l(t('Delete'),'hontza_solr/delete_multiple/'.$node_id_array_string,array('query'=>'destination='.$url_return.'&is_solr='.$is_solr)).'</div>';
    //if($is_solr){
        $url_info=parse_url($url_return_in);    
        $html[]='<div>'.l(t('Return'),$url_info['path'],array('query'=>$url_info['query'])).'</div>';
    /*}else{
        $html[]='<div>'.l(t('Return'),$url_return).'</div>';       
    }*/    
    return implode('&nbsp;',$html);
}
function hontza_solr_funciones_delete_multiple_callback(){
    global $user;
    $node_id_array=explode(',',arg(2));
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg($node_id_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            /*$node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $env_id = apachesolr_default_environment();
                require_once('sites/all/modules/apachesolr/apachesolr.index.inc');                
                apachesolr_index_delete_entity_from_index($env_id,$node->type,$nid);
                apachesolr_remove_entity($env_id,$node->type,$nid);                
            }*/
            node_delete($nid);
            //hontza_solr_search_clear_cache_content($my_node);            
        }    
    }
    //cache_clear_all('*', 'cache_apachesolr', TRUE);
    //cache_clear_all();
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);        
    hontza_solr_funciones_redirect('delete');
}
//intelsat-2015
//function hontza_solr_funciones_get_status_options(){
function hontza_solr_funciones_get_status_options($is_multiple=0){
    //intelsat-2015
    return red_solr_inc_get_get_status_options($is_multiple);
}
function hontza_solr_funciones_add_validate_status_filter(&$my_array,$validate_status){
    red_solr_inc_add_validate_status_filter($my_array,$validate_status);
}
//intelsat-2015
//function hontza_solr_funciones_add_tipo_categoria_validate_status_form_field(&$form){
function hontza_solr_funciones_add_categoria_tipo_scoring_form_field(&$form){
    //intelsat-2015
    $categorias=my_get_categorias_canal(1,1);
    $is_long=red_solr_inc_is_long_categorias_canal($categorias);
    if($is_long){
        $prefix_right='<div style="float:left;width:55%;padding-left:5px;">';
        $suffix_right='</div></div>';
        $prefix_right2='<div style="float:left;width:20%;padding-left:10px;">';
        $suffix_right2='</div>';
        //$prefix_right3='<div style="float:left;width:14%;padding-left:20px;">';
        $prefix_right3='<div style="float:left;width:14%;padding-left:60px;">';
        $suffix_right3='</div></div>';
    }else{
        $prefix_right='<div style="float:left;width:45%;padding-left:5px;">';
        //intelsat-2016
        //if(red_despacho_is_activado()){
            //$prefix_right='<div style="float:left;width:25%;padding-left:5px;">';
            $prefix_right='<div style="float:left;padding-left:5px;">';
        //}
        $suffix_right='</div></div>';
        //$prefix_right2='<div style="float:left;width:35%;padding-left:10px;">';
        $prefix_right2='<div style="float:left;width:20%;padding-left:10px;">';
        //intelsat-2016
        //if(red_despacho_is_activado()){
            //$prefix_right2='<div style="float:left;width:40%;padding-left:10px;">';
            $prefix_right2='<div style="float:left;padding-left:10px;">';
        //}
        $suffix_right2='</div>';
        //$prefix_right3='<div style="float:left;width:14%;padding-left:20px;">';
        //intelsat-2016
        $prefix_right3='<div style="float:left;width:14%;padding-left:100px;">';
        //intelsat-2016
        //if(red_despacho_is_activado()){
            //$prefix_right3='<div style="float:right;width:14%;">';
            $prefix_right3='<div style="float:left;padding-left:10px;">';            
        //}
        $suffix_right3='</div></div>';
    }    
    //
        
     $form['categorias_canal']= array(
	  '#title' => t('Category'),
	  '#type' => 'select',
	  '#options' => $categorias,
	  //'#default_value' => '',
          //'#prefix'=>$prefix_right,
          //'#suffix'=>$suffix_right,
          '#prefix'=>$prefix_right,
          '#suffix'=>$suffix_right,
    );
     
     $form['fuente_tipo']= array(
	  '#title' => t('Type'),
	  '#type' => 'select',
	  '#options' => my_get_tipo_options(1),
	  //'#default_value' => '',
          '#prefix'=>$prefix_right2,
          '#suffix'=>$suffix_right2,  
    );
    
    //intelsat-2015 
    /*$form['validate_status']= array(
	  '#title' => t('Status'),
	  '#type' => 'select',
	  '#options' => hontza_solr_funciones_get_status_options(),
	  '#default_value' =>0,
          '#prefix'=>$prefix_right3,
          '#suffix'=>$suffix_right3,   
    );*/
          
    //intelsat-2015
    red_solr_inc_add_busqueda_avanzada_rated_form_field($form,$prefix_right3,$suffix_right3);
      
}
function hontza_solr_funciones_set_item_source_label(&$element,$field_alias=''){
    global $language;
    $key_array=array_keys($element);
    if(!empty($element)){
        foreach($element as $key=>$row){
            if($field_alias=='itm_field_item_fid'){
                $fid=$element[$key]['#indexed_value'];
                $file=my_get_file($fid);
                if(isset($file->filename) && !empty($file->filename)){
                    $element[$key]['#value']=$file->filename;
                }
            }else if($field_alias=='itm_field_item_validate_status'){    
                $validate_status=$element[$key]['#indexed_value'];
                $validate_status_label=hontza_solr_funciones_get_validate_status_label($validate_status);
                if(!empty($validate_status_label)){
                    $element[$key]['#value']=$validate_status_label;
                }
            }else{
                $tid=$element[$key]['#indexed_value'];
                $default_name='';
                $with_orig=0;
                if(in_array($field_alias,array('itm_field_item_source_tid','itm_field_item_canal_category_ti'))){
                    if($language->language=='es'){
                        $with_orig=1;
                    }
                }
                $term_name=taxonomy_get_term_name_by_language($tid,$default_name,$with_orig);                
                if(!empty($term_name)){
                    $element[$key]['#value']=$term_name;
                }
            }    
        }    
    }
}
function hontza_solr_funciones_get_validate_status_label($validate_status){
    $result=hontza_solr_funciones_get_status_options();
    if(isset($result[$validate_status]) && !empty($result[$validate_status])){
        return $result[$validate_status];
    }
    return $validate_status;
}
function hontza_solr_funciones_add_beste_delete_filters_content(&$result){
    $validate_status_array=hontza_solr_funciones_add_delete_validate_status_filtros($_REQUEST['f']);
    if(!empty($validate_status_array)){
        $result[]=hontza_solr_funciones_get_linea_separacion($result);
        $result[]='<b><i>'.t('Status').'</i></b>';
        $result=array_merge($result,$validate_status_array);
    }
    //intelsat-2015
    red_solr_inc_add_beste_delete_filters_content($result);
}
function hontza_solr_funciones_add_delete_validate_status_filtros($my_array){
    //intelsat-2015
    return red_solr_inc_add_delete_validate_status_filtros($my_array);
}
function hontza_solr_funciones_get_linea_separacion($result){
    if(!empty($result)){
        return 'linea_separacion';
    }
    return '';
}
function hontza_solr_funciones_get_enter_terms_default_filter_array(){
    $my_array=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_array[]='im_og_gid:'.$my_grupo->nid;
    }
    return $my_array;
}
function hontza_solr_funciones_get_result_message($total,$start,$end){
    $_SESSION['my_solr_total']=$total;    
    return t('Showing @start - @end of @total', array(
      '@start' => $start,
      '@end' => $end,  
      '@total' => $total,
    ));
}
function hontza_solr_funciones_add_save_current_rss_js(){    
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
         $('#exsave_current_rss').jqm({ajax: '".$my_base_path."help_popup.php?nid=save_current_rss&w=500&h=400&my_grupo_nid=".$my_grupo_nid."&solr_busqueda_value=".$solr_busqueda_value."', trigger: 'a.jqm-trigger-save_current_rss',modal:true, toTop: true, overlay: 0});
         
     });";
     drupal_add_js($js,'inline');
}
function hontza_solr_funciones_save_rss_form(){
    $form_state=array();
    return hontza_solr_save_search_form($form_state,1);
}
function hontza_solr_funciones_is_post_rss(){
    if(isset($_POST['is_rss']) && !empty($_POST['is_rss'])){
        return 1;
    }
    return 0;
}
function hontza_solr_funciones_add_rss_js(){
    $js='<script>';
    $js.="$(document).ready(function()
     {
        $('#edit-submit').click(function()
        {
            $('#id_a_close_window').click();
        }); 
     });";
    $js.="</script>";
     //drupal_add_js($js,'inline');
    return $js;
}
function hontza_solr_funciones_get_en_grupo_ini(){
    return hontza_solr_funciones_get_bookmark_ini(1);
}
function hontza_solr_funciones_is_request_solr(){
    if(isset($_REQUEST['is_solr']) && !empty($_REQUEST['is_solr'])){
        return 1;
    }
    return 0;
}
function hontza_solr_funciones_redirect($mode=''){
    global $base_root;            
    $url=$_REQUEST['destination'];
    $mirar_base64=0;
    if(empty($url)){
        $mirar_base64=1;
        $url=$_REQUEST['my_destination'];
    }
    $url_in=$url;
    $is_solr=hontza_solr_funciones_is_request_solr();
    if($is_solr){
        $url=base64_decode($url);
        if(empty($url)){
            $url=$url_in;
        }
        $url=hontza_solr_search_add_solrsort($url);
        $url_info=parse_url($url);
        //if($mode!='delete'){
            $selected_node_id_array=hontza_solr_search_get_temp_selected_node_id_array();
            if(!empty($selected_node_id_array)){
                $url_info['query']=hontza_solr_search_add_url_query_selected_node_id_array($url_info['query'],$selected_node_id_array);
                //$url_info['query']=hontza_solr_search_add_url_query_my_var($url_info['query'],$mode);
            }
        /*}else{
            $url_info['query']=hontza_solr_search_unset_query_selected_node_id_array($url_info['query'],'','selected_node_id_array',$url_info['query']);
        }*/
        $_REQUEST['destination']='';
        //drupal_goto($url_info['path'],$url_info['query']);
        $my_url=hontza_solr_search_prepare_redirect_url($url_info['path']);
        drupal_goto($my_url,$url_info['query']);
    }else{
        if($mirar_base64){
            if(!hontza_solr_search_in_url_base64_decode($url)){
                $url=base64_decode($url);
            }
        }
        $selected_node_id_array=hontza_solr_search_get_temp_selected_node_id_array();
        if(!empty($selected_node_id_array)){
            $url_info=parse_url($url);
            $url_info['query']=hontza_solr_search_add_url_query_selected_node_id_array($url_info['query'],$selected_node_id_array);
            //$url_info['query']=hontza_solr_search_add_url_query_my_var($url_info['query'],$mode);
            $my_url=hontza_solr_search_prepare_redirect_url($url_info['path']);
            drupal_goto($my_url,$url_info['query']);
        }else{
            drupal_goto($url);
        }    
    }
}
function hontza_solr_funciones_mark_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);
    drupal_goto('hontza_solr/mark_multiple/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);    
}
function hontza_solr_funciones_mark_multiple_callback(){
    global $user;
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg(explode(',',arg(2)));    
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                hontza_solr_funciones_bookmark_insert_yes($node->nid,$node->vid,$user->uid);
                //intelsat-2016-noticias-usuario
                red_solr_inc_update_node_bookmark($node);                
                hontza_canal_rss_solr_clear_node_index($node,$nid);
            }    
        }    
    }
    //intelsat-2016
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);        
    hontza_solr_funciones_redirect();
}
function hontza_solr_funciones_is_select_bookmark_all(){
    if(isset($_REQUEST['is_select_bookmark_all']) && !empty($_REQUEST['is_select_bookmark_all'])){
        return 1;
    }else{
        //if($_SESSION['selected_node_id_array']=='is_all_selected'){
        if(isset($_REQUEST['selected_node_id_array']) && !empty($_REQUEST['selected_node_id_array']) && $_REQUEST['selected_node_id_array']=='is_all_selected'){
            return 1;
        }
    }
    return 0;
}
function hontza_solr_funciones_get_current_path($is_all=1){
    global $base_root;
    $result=$base_root.request_uri();
    $result=hontza_solr_search_unset_query_selected_node_id_array($result);
    if(!$is_all){
       $result=hontza_solr_search_unset_all_variables($result);
       return $result;
    }
    $url_info=parse_url($result);
    parse_str($url_info['query'],$query);
    $question=0;
    if(!isset($query['is_select_bookmark_all']) || empty($query['is_select_bookmark_all'])){
        if(empty($url_info['query'])){
            $result=hontza_solr_search_add_question($result);
            $question=1;
        }else{
            $result.='&';
        }
        $result.='is_select_bookmark_all=1';
    }
    if(!isset($query['is_my_all_results']) || empty($query['is_my_all_results'])){
        if($question){
            $result.='&';
        }else{
            if(empty($url_info['query'])){
                $result=hontza_solr_search_add_question($result);
                $question=1;
            }else{
                $result.='&';
            }
        }
        $result.='is_my_all_results=1';
    }
    //intelsat-2016-noticias-usuario
    if($is_all){
        $result=red_solr_inc_unset_page($result,$query);
    }
    return $result;
}
function hontza_solr_funciones_get_request_node_id_array(){
    if(isset($_REQUEST['node_id_array']) && !empty($_REQUEST['node_id_array'])){
        if($_REQUEST['node_id_array']=='is_all_selected'){
            return hontza_solr_funciones_get_result_node_id_array_string();
        }
        return $_REQUEST['node_id_array'];
    }
    return '';
}
function hontza_solr_funciones_get_result_node_id_array(){
    $result=array();
    if(isset($_SESSION['my_results_solr']) && !empty($_SESSION['my_results_solr'])){
        foreach($_SESSION['my_results_solr'] as $i=>$row){
            $result[]=$row['node']->entity_id;
        }
    }else{
        return hontza_solr_search_get_result_node_id_array();
    }
    return $result;
}
function hontza_solr_funciones_get_result_node_id_array_string(){
    $result=hontza_solr_funciones_get_result_node_id_array();
    return implode(',',$result);
}
function hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array){
    if(isset($_REQUEST['node_id_array']) && !empty($_REQUEST['node_id_array'])){
        if($_REQUEST['node_id_array']=='is_all_selected'){
            return 'is_all_selected';
        }
    }
    return implode(',',$node_id_array);
}
function hontza_solr_funciones_get_node_id_array_by_arg($node_id_array){
    if(isset($node_id_array[0]) && !empty($node_id_array[0]) && $node_id_array[0]=='is_all_selected'){
        $result=hontza_solr_funciones_get_result_node_id_array();
        return $result;
    }
    return $node_id_array;
}
function hontza_solr_funciones_get_node_id_array_by_arg_string($node_id_array){
    if($node_id_array=='is_all_selected'){
        $result=hontza_solr_funciones_get_result_node_id_array();
        return implode(',',$result);
    }
    return $node_id_array;
}
function hontza_solr_funciones_set_page_id($attributes_in){
    $attributes=str_replace('id="page-hontza-solr-busqueda-avanzada-solr','id="page-hontza-solr-busqueda-avanzada',$attributes_in);
    $find='id="page-my-solr-my-search';
    $pos=strpos($attributes,$find);
    if($pos===FALSE){
        return $attributes;
    }else{
        $len=$pos+strlen($find);
        $s=substr($attributes,$len);
        $pos_end=strpos($s,'"');
        if($pos_end===FALSE){
            return $attributes;
        }else{
            $result=substr($attributes,0,$len).substr($s,$pos_end);
            return $result;
        }
    }    
}
function hontza_solr_funciones_set_enter_terms_fecha_filter($get,$form_state,$redirect){
    $result=$redirect;
    $search_fechas=hontza_solr_get_search_fechas($form_state['values']['solr_busqueda_fecha_inicio'],$form_state['values']['solr_busqueda_fecha_fin']);
    if(!empty($search_fechas)){
        $result=hontza_solr_funciones_unset_fechas($result);
        if($result=='my_solr/my_search'){
            $result.='/';
        }else if(!empty($result)){
            $result.=' AND ';
        }else{
            $result.='my_solr/my_search/';
        }
        $result.='('.$search_fechas.')';
    }
    return $result;
}
function hontza_solr_funciones_unset_fechas($redirect){
    $result=array();
    $my_array=explode(' AND ',$redirect);
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if(hontza_solr_funciones_is_ds_created($v)){
                continue;
            }
            //
            $result[]=$v;
        }
    }
    return implode(' AND ',$result);
}
function hontza_solr_funciones_is_ds_created($v){
    $pos=strpos($v,'ds_created:');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_solr_funciones_get_info_fecha(){
    $result=array();
    $result['fecha_inicio']='';
    $result['fecha_fin']='';                    
    $search=arg(2);
    $my_array=explode(' AND ',$search);
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if(hontza_solr_funciones_is_ds_created($v)){
                $fecha_array=explode(' TO ',$v);
                if(!empty($fecha_array)){
                    $fecha_ini=str_replace('(ds_created:[','',$fecha_array[0]);                        
                    if($fecha_ini!='*'){
                        $result['fecha_inicio']=$fecha_ini;
                    }
                    $fecha_end=str_replace('])','',$fecha_array[1]);
                    if($fecha_end!='*'){
                        $result['fecha_fin']=$fecha_end;
                    }
                    return $result;
                }
            }
        }
    }
    return $result;
}
function hontza_solr_funciones_create_fecha_array($fecha,$is_start=1){
    return $fecha;    
}
function hontza_solr_funciones_unset_my_results_solr(){
    if(isset($_SESSION['my_results_solr'])){
        unset($_SESSION['my_results_solr']);
    }
}