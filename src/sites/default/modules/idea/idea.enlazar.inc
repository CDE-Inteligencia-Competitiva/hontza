<?php
function idea_enlazar_hontza_perm($result_in){
    $result=$result_in;
    $result[]='Enlazar idea';
    return $result;
}
function idea_enlazar_menu_items($items_in){
    $items=$items_in;    
  $items['node/%/enlazar_idea'] = array(
    'title'=>t('Idea'),
    'page callback' => 'idea_node_enlazar_callback',
    'access arguments' => array('Enlazar idea'),
  );
  $items['node/%/confirm_enlazar_idea/%']=array(
    'title'=>t('Idea'),
    //'page callback' => 'drupal_get_form',    
    //'page arguments'   => array('idea_confirm_enlazar_form'),
    'page callback' => 'idea_enlazar_confirm_enlazar_idea_callback', 
    'access arguments' => array('Enlazar idea'),
    );
  $items['node/%/enlaces_idea'] = array(
    'title'=>t('Idea'),
    'page callback' => 'idea_enlaces_callback',
    'access arguments' => array('Enlazar idea'),
  );
    $items['node/%/origenes_idea'] = array(
    'title'=>t('Idea'),
    'page callback' => 'idea_origenes_callback',
    'access arguments' => array('Enlazar idea'),
  );
   $items['ideas/no_existe_enlace_origen_idea'] = array(
    'title' => t('Origin of Idea'),
    'page callback' => 'no_existe_enlace_origen_idea_callback',
    'access arguments' => array('Enlazar idea'),
  );
   $items['node/%/unlink_idea/%']=array(
    'title'=>t('Delete idea link'),
    'page callback' => 'idea_unlink_idea_callback',
    'access arguments' => array('Enlazar idea'),
    );
   $items['ideas/adherirse_ajax'] = array(
    'title' => t('Support the idea'),
    'page callback' => 'ideas_adherirse_ajax_callback',
    //'access callback' => TRUE,
    'access arguments' => array('Ver ideas_adherirse'),
  );
   //gemini-2014
   $items['ideas/criterios_de_ponderacion'] = array(
    'title' => t('Promotion Criteria'),
    'page callback' => 'drupal_get_form',
    'page arguments'=>array('idea_criterios_de_ponderacion_form'),   
    'access callback' => 'idea_criterios_de_ponderacion_access',
  );
  return $items;
}  
function idea_node_add_link($node){
    //$url='node/add/idea/'.$node->nid;
    $url='node/'.$node->nid.'/enlazar_idea';
    //return l(t('Idea'),$url);
    $label='';
    //$label=t('Idea');    
    //return l($label,$url,array('attributes'=>array('title'=>t('Idea'),'alt'=>t('Idea'))));
    return l($label,$url,array('attributes'=>array('target'=>'_blank','title'=>t('Ideate'),'alt'=>t('Ideate'))));        
}
function idea_node_enlazar_callback(){
   drupal_set_title(t('Create a new idea or link to an idea')); 
   $output='';
   //intelsat-2015
   if(!hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
    $nid=arg(1);
    $node=node_load($nid);
    $output.=node_view($node);
   } 
   //
    $my_limit=100;    
    $sort='asc';
    $field='node_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
        }else if($order==t('Creator')){
            $field='username';
        }else if($order==t('Creation date')){
            $field='node_created';
        }
    }
    $grupo_seleccionado=0;
    $sql=idea_define_list_sql($field,$sort,$grupo_seleccionado);
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[1]=array('data'=>t('Creator'),'field'=>'username');
    $headers[2]=array('data'=>t('Creation date'),'field'=>'node_created');
    $headers[3]=t('Actions');

    $rows=array();
    //$output.=my_create_boton_boletin_volver();
    //$output.=my_create_boton_boletin_cron();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $is_activado=0;
      $icono_activado=idea_enlazar_asociado_icono_activado($r,$nid,$is_activado);
      //intelsat-2015
      $icono_activado='';
      //
      $rows[$kont][0]=$icono_activado.'&nbsp;'.estrategia_set_title_max_len($r->node_title);
      $rows[$kont][1]=$r->username;
      $rows[$kont][2]='';
      if(!empty($r->node_created)){
        $rows[$kont][2]=date('Y-m-d',$r->node_created);
      }
      $rows[$kont][3]=idea_node_enlazar_define_acciones($r,$nid,$is_activado);
      $kont++;
    }
    //
    $new_rows=idea_define_new_rows($nid);
    $rows=array_merge($new_rows,$rows);
    $rows=my_set_estrategia_pager($rows, $my_limit);

  $output .='<div style="float:left;width:100%;">';
       
  if (count($rows)>0) {
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
  $output .='</div>';
  
  return $output;
}
function idea_define_list_sql($field_in='',$sort_in='',&$grupo_seleccionado){
  global $user;  
  $where=array();
  //
  $where[]='n.promote = 1';
  $where[]='n.status = 1';
  $where[]='n.type="idea"'; 
  //
  $search=my_get_request_value('search');
  if(!empty($search)){
    $where[]='(n.title LIKE "%%'.$search.'%%" OR nr.body LIKE "%%'.$search.'%%")';
  }
  //
  $my_grupo=og_get_group_context();
  $grupo_seleccionado=0;
  if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]='idea.grupo_nid='.$my_grupo->nid;
        $grupo_seleccionado=1;
  }
  if(is_user_invitado()){
    $where[]='ii.uid='.$user->uid;
  }else{
    if($user->uid!=1){
        $where[]='(idea.is_todo_subgrupo=1 OR s.uid='.$user->uid.')';
    }
  }
  //
  if(empty($field_in) && empty($sort_in)){ 
    $order_fields='n.sticky DESC, n.created DESC';  
  }else{
    $order_fields=$field_in.' '.$sort_in;
  }
    
  if(is_idea('mas_comentadas')){
      //$order_by=' ORDER BY comment_num DESC, n.sticky DESC, n.created DESC';
      $order_fields='comment_num DESC,'.$order_fields;
  }else if(is_idea('mas_valoradas')){
      //$order_by=' ORDER BY votingapi_cache_node_average_value DESC, n.sticky DESC, n.created DESC';
      $order_fields='votingapi_cache_node_average_value DESC,'.$order_fields;
  }
  $order_by=' ORDER BY '.$order_fields;
  //
  $sql='SELECT n.nid, n.sticky, n.created,COUNT(c.cid) as comment_num,
  votingapi_cache_node_average.value AS votingapi_cache_node_average_value,
  n.title AS node_title,n.created AS node_created,
  u.name AS username
  FROM {node} n
  LEFT JOIN {idea} idea ON n.nid=idea.nid
  LEFT JOIN {idea_invitados} ii ON idea.nid=ii.idea_nid
  LEFT JOIN {idea_subgrupo} s ON idea.nid=s.idea_nid
  LEFT JOIN {comments} c ON n.nid=c.nid
  LEFT JOIN votingapi_cache votingapi_cache_node_average ON n.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = "node" AND votingapi_cache_node_average.function = "average")
  LEFT JOIN {node_revisions} nr ON (n.nid=nr.nid AND n.vid=nr.vid)
  LEFT JOIN {users} u ON n.uid=u.uid 
  WHERE '.implode(' AND ',$where).'
  GROUP BY n.nid '.$order_by;
  /*
  if(user_access('root') && hontza_is_sareko_id('ROOT')){  
    print $sql;
    //exit();
  }
  */  
  return $sql;
}
function idea_node_enlazar_define_acciones($r,$item_nid,$is_activado){
    $html=array();
    //intelsat-2015
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
        $url='node/'.$_REQUEST['node_id_array'].'/confirm_enlazar_idea/'.$r->nid; 
    //                
    }else{            
        $url='node/'.$item_nid.'/confirm_enlazar_idea/'.$r->nid;        
    }
    if($is_activado){
        //$url='node/'.$r->nid;
        //$url='comment/reply/'.$r->nid;
        $url='node/'.$r->nid.'/edit';
        $html[]=l(my_get_icono_action('idea', t('Link to this idea')),$url,array('html'=>true));
        $html[]=l(my_get_icono_action('delete', t('Delete idea link')),'node/'.$item_nid.'/unlink_idea/'.$r->nid,array('html'=>true));
    }else{    
        $html[]=l(my_get_icono_action('idea', t('Link to this idea')),$url,array('html'=>true));    
    }
    return implode('&nbsp;',$html);
}
function idea_enlazar_asociado_icono_activado($r,$nid,&$is_activado){
    return hontza_enlazar_debate_asociado_icono_activado($r,$nid,2,$is_activado);
}
function idea_get_enlazar_row($idea_nid,$item_nid){
    $enlazar_idea_array=idea_get_enlazar_array($idea_nid,$item_nid);
    if(count($enlazar_idea_array)>0){
        return $enlazar_idea_array[0];
    }
    $content_type_idea_array=idea_get_content_type_array($idea_nid,$item_nid);
    if(count($content_type_idea_array)>0){
        return $content_type_idea_array[0];
    }  
    $result=new stdClass();
    return $result;
}
function idea_define_new_rows($nid){
    return hontza_define_debate_new_rows($nid,2);
}
function idea_get_enlazar_array($idea_nid,$item_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($idea_nid)){
        $where[]='idea_nid='.$idea_nid;
    }
    if(!empty($item_nid)){
        $where[]='item_nid='.$item_nid;
    }
    //
    $res=db_query('SELECT * FROM {enlazar_idea} WHERE '.implode(' AND ',$where));
    while($row=db_fetch_object($res)){
        $item_node=node_load($row->item_nid);
        $idea_node=node_load($row->idea_nid);
        if(isset($item_node->nid) && !empty($item_node->nid) && isset($idea_node->nid) && !empty($idea_node->nid)){
            $result[]=$row;
        }    
    }
    //
    return $result;
}
function idea_get_content_type_array($idea_nid,$item_nid){
    $result=array();
    $content_type_idea_array=idea_get_content_type($item_nid,$idea_nid);
    if(!empty($content_type_idea_array)){
        foreach($content_type_idea_array as $i=>$row){
            $r=new stdClass();
            $r->id=-444;
            $r->idea_nid=$row->nid;
            $r->item_nid=$row->noticia_nid;
            $item_node=node_load($r->item_nid);
            $idea_node=node_load($r->idea_nid);
            if(isset($item_node->nid) && !empty($item_node->nid) && isset($idea_node->nid) && !empty($idea_node->nid)){
                $result[]=$r;
            }
        }    
    }
    return $result;
}
function idea_get_content_type($item_nid,$nid){
    $where=array();
    $where[]="1";
	if(!empty($item_nid)){
            $where[]="i.noticia_nid=".$item_nid;
	}
        if(!empty($nid)){
            $where[]="i.nid=".$nid;
	}
        
	$sql="SELECT i.* FROM {idea} i WHERE ".implode(" AND ",$where);
        /*if($nid==159015){
            print $sql.'<BR>';
        }*/
	$items = db_query($sql);        
		while ($item = db_fetch_object($items)) {				  
		  $result[]=$item;
		}
	return $result;
}
function idea_confirm_enlazar_form(){
    $form=array();
    $nid=arg(1);
    $item_title='';
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        $item_title=$node->title;
    }
    $idea_title='';
    $idea_nid=arg(3);
    $idea=node_load($idea_nid);
    if(isset($node->nid) && !empty($node->nid)){
        $idea_title=$idea->title;
    }
    //
    $form['item_title']=array(
        '#type'=>'textfield',
        '#title'=>t('News'),
        '#default_value'=>$item_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
     $form['idea_title']=array(
        '#type'=>'textfield',
        '#title'=>t('Idea'),
        '#default_value'=>$idea_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
    //
    $form['item_nid']=array('#type'=>'hidden','#value'=>$nid);
    $form['idea_nid']=array('#type'=>'hidden','#value'=>$idea_nid);        
    $form['confirm_btn']=array('#type'=>'submit','#name'=>'confirm_btn','#value'=>t('Confirm'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'node/'.$nid.'/enlazar_idea'));     
    return $form;
}
function idea_confirm_enlazar_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='confirm_btn'){
            $item_nid=$form_state['values']['item_nid'];
            $idea_nid=$form_state['values']['idea_nid'];
            //intelsat-2015
            /*idea_delete_enlazar($idea_nid,$item_nid);
            idea_insert_enlazar($idea_nid,$item_nid);
            //
            //drupal_goto('comment/reply/'.$idea_nid);
            drupal_goto('node/'.$idea_nid.'/edit');*/
            idea_enlazar_confirm_enlazar_idea($item_nid,$idea_nid);
            //
        }                
    }
   drupal_goto('vigilancia/pendientes');    
}
function idea_delete_enlazar($idea_nid,$item_nid){
    db_query('DELETE FROM {enlazar_idea} WHERE idea_nid=%d AND item_nid=%d',$idea_nid,$item_nid);
}
function idea_insert_enlazar($idea_nid,$item_nid){
    global $user;
    db_query('INSERT INTO {enlazar_idea}(idea_nid,item_nid,enlazar_uid,enlazar_created) VALUES(%d,%d,%d,%d)',$idea_nid,$item_nid,$user->uid,time());
    //gemini-2014
    hontza_validar_con_accion($item_nid);
}
function idea_is_origenes(){
    return hontza_in_pantallas_enlace_debate(4);
}
function idea_in_pantallas_enlace(){
    return hontza_in_pantallas_enlace_debate(5);
}
function idea_enlazar_on_save($op,$node){
    if($node->type=='idea'){
        if($op=='insert'){
            if(isset($node->nid) && !empty($node->nid)){
                if(isset($node->noticia_nid) && !empty($node->noticia_nid)){
                    //intelsat-2015
                    $noticia_nid_array=explode(',',$node->noticia_nid);
                    $noticia_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($noticia_nid_array);                   
                    if(count($noticia_nid_array)>1){
                        foreach($noticia_nid_array as $i=>$noticia_nid){
                            idea_delete_enlazar($node->nid,$noticia_nid);
                            idea_insert_enlazar($node->nid,$noticia_nid);
                        }
                    //    
                    }else{    
                        idea_delete_enlazar($node->nid,$node->noticia_nid);
                        idea_insert_enlazar($node->nid,$node->noticia_nid);
                    }    
                }
            }
        }    
    }
}
function idea_enlaces_callback(){
    $output='';
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();
    $item_nid=arg(1);
    $my_limit=10;
    $enlazar_idea_array=idea_get_all_enlazar_array('',$item_nid);
    $rows=array();
    $kont=0;
    if(count($enlazar_idea_array)>0){
        foreach($enlazar_idea_array as $i=>$row){
            $rows[$kont]=new stdClass();            
            $node=node_load($row->idea_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]->view=node_view($node);
                $kont++;
            }  
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
   
  return $output;
}
function idea_get_all_enlazar_array($idea_nid,$item_nid){
    $enlazar_idea_array=idea_get_enlazar_array($idea_nid,$item_nid);
    $content_array=idea_get_content_type_array($idea_nid, $item_nid);
    if(!empty($content_array)){
        foreach($content_array as $i=>$row){
            if(!hontza_in_array($row->idea_nid,'idea_nid',$enlazar_idea_array)){
                $enlazar_idea_array[]=$row;
            }
        }
    }   
    return $enlazar_idea_array;
}
function idea_origin_link($node){
    $enlazar_idea_array=idea_get_all_enlazar_array($node->nid,'');
    if(count($enlazar_idea_array)>0){
        $url='node/'.$node->nid.'/origenes_idea';
    }else{                        
        $url='ideas/no_existe_enlace_origen_idea';		
    }
    return l(t('Origin'),$url);	
}
function idea_origenes_callback(){
    drupal_set_title(t('Origin of Idea'));
    $output='';
    $idea_nid=arg(1);
    $my_limit=10;
    $enlazar_idea_array=idea_get_all_enlazar_array($idea_nid,'');
    $rows=array();
    $kont=0;
    if(count($enlazar_idea_array)>0){
        foreach($enlazar_idea_array as $i=>$row){
            $node=node_load($row->item_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]=new stdClass();
                $rows[$kont]->view=node_view($node);
                $kont++;
            }    
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
   
  return $output;
}
function idea_get_invidato_field_label($field){
    $invitado_fields_labels=idea_define_invidato_fields_labels();
    if(isset($invitado_fields_labels[$field])){
        return $invitado_fields_labels[$field];
    }
    return $field;
}
function idea_define_invidato_fields_labels(){
    $result=array();
    $result['invitado_name']=t('Username');
    $result['invitado_mail']=t('E-mail address');
    $result['pass']=t('Password');
    $result['profile_nombre']=t('Name');
    $result['profile_apellidos']=t('Surname');
    $result['profile_empresa']=t('Organisation');
    $result['responsable']=t('Email of responsible person');        
    return $result;
}
function idea_node_delete_confirm_form_alter(&$form,&$form_state,$form_id){
    $form['#redirect']='ideas';
}
function idea_get_status_color($node){
    return estrategia_get_status_color($node,'plazo_del_reto');
}
function idea_convertir_data_row($arbol){
    return estrategia_convertir_data_row($arbol);
}
function idea_add_div_actions($content){
    return estrategia_add_div_actions($content);
}
function idea_is_arbol(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='ideas'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='arbol'){
            return 1;
        }
    }    
    return 0;
}
function no_existe_enlace_origen_idea_callback(){
    drupal_set_title(t('Origin of Idea'));
    $html='<p>'.t('There are no contents').'</p>';
    return $html;
}
function idea_delete_link($node){
    return hontza_item_delete_link($node);    
}
function idea_tag_link($node){
    return hontza_item_tag_link($node);    
}
function idea_active_tabs_access(){
    return hontza_grupos_active_access_tab('idea');
}
function idea_limpiar_db(){
    boletin_report_no_group_selected_denied();
    $response_nid_array=idea_get_response_all_nid_array();
    $result=get_all_nodes(array('idea','oportunidad','proyecto'));
    if(!empty($result)){
        foreach($result as $i=>$row){
            if(isset($row->nid) && !empty($row->nid)){
                if(in_array($row->nid,$response_nid_array)){
                    //
                    //print 'nid(response ok)='.$row->nid.'<BR>';
                }else{
                    node_delete($row->nid);
                    print 'nid(response delete)='.$row->nid.'<BR>';
                }
            }
        }
    }
}
function idea_get_response_all_nid_array(){
    $result=array();
    $rows=get_idea_arbol_rows();
    if(!empty($rows)){
        foreach($rows as $i=>$r){
            if(isset($r[2])){
                $nid=estrategia_get_nid_by_div_actions($r[2]);
                if(!empty($nid)){
                    $result[]=$nid;
                }
            }
        }
    }
    return $result;
}
function idea_unlink_idea_callback(){
    //intelsat-2015
    hontza_solr_search_is_usuario_lector_access_denied();
    $nid=arg(1);
    $idea_nid=arg(3);
    idea_delete_enlazar($idea_nid,$nid);
    drupal_goto('node/'.$nid.'/enlazar_idea');
    exit();
}
function idea_comment_link($node){
    return hontza_item_comment_link($node);
}
function idea_edit_link($node){
    return hontza_item_edit_link($node);
}
function idea_add_adherirse_ajax($id_a,$aurrizkia,$idea_nid){
   global $base_url;
   $purl='';
   $my_grupo=og_get_group_context(); 
   if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
   }
   $js='$(document).ready(function()
   {
                
            function call_adherirse_ajax_'.$id_a.'(id_a){
                var my_array=id_a.split("_");
                var idea_nid=my_array[3];
                var uid=my_array[4];
                jQuery.ajax({
				type: "POST",
                                url: "'.$base_url.'/'.$purl.'/ideas/adherirse_ajax?my_time="+new Date().getTime(),
				dataType:"json",
                                data:{idea_nid:idea_nid,uid:uid,aurrizkia:"'.$aurrizkia.'"},
				success: function(my_result){
                                    set_adherir_item_ajax_on_success_'.$id_a.'(my_result,idea_nid);
                                    $("#id_supported_by_'.$idea_nid.'").text(my_result.supported_by);
				}
			});
            }
            function set_adherir_item_ajax_on_success_'.$id_a.'(my_result,idea_nid){
                var my_parent=$("#'.$id_a.'").parent();
                var my_class=my_parent.attr("class");
                my_parent.attr("class",set_adherir_item_class_ajax_'.$id_a.'(my_class));
                my_parent.html(my_result.a);
            }
            function set_adherir_item_class_ajax_'.$id_a.'(my_class,idea_nid){                
                if(my_class=="n-item-adherir"){
                    $("#id_add_oportunidad_'.$idea_nid.'").css("display","");
                    return "n-item-no-adherir";
                }
                if(my_class=="n-item-no-adherir"){
                    $("#id_add_oportunidad_'.$idea_nid.'").css("display","none");
                    return "n-item-adherir";
                }
                if(my_class=="item-adherir"){
                    $("#id_add_oportunidad_'.$idea_nid.'").css("display","");
                    return "item-no-adherir";
                }
                if(my_class=="item-no-adherir"){
                    $("#id_add_oportunidad_'.$idea_nid.'").css("display","none");
                    return "item-adherir";
                }    
            }
                $("#'.$id_a.'").click(function(){
                    call_adherirse_ajax_'.$id_a.'($(this).attr("id"));
                    return false;    
                });
    });';
    drupal_add_js($js,'inline');    
}
function ideas_adherirse_ajax_callback(){
    $idea_nid=$_POST['idea_nid'];
    $uid=$_POST['uid'];
    $my_array=get_idea_adheridas_array($idea_nid,$uid);
    if(empty($my_array)){
        save_idea_adherida($idea_nid,$uid);
        $my_class='item-no-adherir';
    }else{
        save_idea_no_adherida($idea_nid,$uid);
        $my_class='item-adherir';
    }
    $result=array();
    $result['ok']=1;
    $result['my_class']=$_POST['aurrizkia'].$my_class;
    $node=node_load($idea_nid);    
    $result['supported_by']=get_idea_adheridas_string_list($node);
    print json_encode($result);
    exit();
}
function idea_set_proyectos_arbol($arbol){
    $result=array();
    $kont=0;
    if(!empty($arbol)){
        foreach($arbol as $i=>$row){
            $pos=strpos($row[0],'proyecto.png');
            if($pos===FALSE){
                continue;
            }else{
                $result[$kont]=$row;
                $result[$kont][0]=str_replace('<div style="padding-left:100px">','<div style="padding-left:0px">',$row[0]);
                $kont++;
            }
        }
    }
    return $result;
}
//gemini-2014
function idea_criterios_de_ponderacion_access(){
    if(is_administrador_grupo(1)){
        return TRUE;
    }
    return FALSE;
}
//gemini-2014
function idea_criterios_de_ponderacion_form(){
  drupal_set_title(t('Promotion Criteria'));
  
  idea_active_tabs_access();
  hontza_grupo_shared_active_tabs_access();
  $grupo_nid='';
  $my_grupo=og_get_group_context();
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $grupo_nid=$my_grupo->nid;
  }
  $row=idea_create_criterios_de_ponderacion($grupo_nid);
  
  $form['promocionar_idea_min'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum number of supporters to Promote Idea to Opportunity'),
    '#default_value' => $row->promocionar_idea_min,
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
  );

  $form['promocionar_oportunidad_min'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum number of supporters to Promote Opportunity to Project'),
    '#default_value' => $row->promocionar_oportunidad_min,
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
  );
  $form['save_btn']=array(
      '#type'=>'submit',
      '#name'=>'save_btn',
      '#value'=>t('Save'),
  );
  return $form;  
}
function idea_create_criterios_de_ponderacion($grupo_nid=''){
    $result=idea_get_criterios_de_ponderacion($grupo_nid);
    if(isset($result->id) && !empty($result->id)){
        return $result;
    }
    $result->promocionar_idea_min=variable_get('promocionar_idea_min',2);
    $result->promocionar_oportunidad_min=variable_get('promocionar_oportunidad_min',3);        
    return $result;
}
function idea_get_criterios_de_ponderacion($grupo_nid_in=''){
    $result=array();
    //
    $grupo_nid=$grupo_nid_in;
    if(empty($grupo_nid)){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid=$my_grupo->nid;
        }    
    }        
    if(!empty($grupo_nid)){
        $where=array();
        $where[]="1";
        $where[]="(cp.group_nid = ".$grupo_nid.")";    
        //
        $sql="SELECT * FROM {criterios_de_ponderacion} cp WHERE ".implode(" AND ",$where);    
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            return $row;
        }
    }    
    //
    $my_result=new stdClass();
    return $my_result;
}
function idea_criterios_de_ponderacion_form_validate(&$form, &$form_state){
  $fields=array('promocionar_idea_min'=>'Minimum number of supporters to Promote Idea to Opportunity','promocionar_oportunidad_min'=>'Minimum number of supporters to Promote Opportunity to Project');
 //
  idea_validate_numeric_fields($form, $form_state,$fields);
}
function idea_validate_numeric_fields(&$form, &$form_state,$fields){
  $numeric_fields=array_keys($fields);
  if(count($numeric_fields)>0){
      foreach($numeric_fields as $i=>$f){
        $v=$form_state['values'][$f];
        if(!is_numeric($v)){
            form_error($form[$f], t($fields[$f]).' '.t('must be a numeric value'));
        }
      }
  }
}
function idea_criterios_de_ponderacion_form_submit(&$form, &$form_state) {
    $promocionar_idea_min=$form_state['values']['promocionar_idea_min'];
    $promocionar_oportunidad_min =$form_state['values']['promocionar_oportunidad_min'];
    
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $group_nid=$my_grupo->nid;
        $row=idea_get_criterios_de_ponderacion();
         if(!empty($row) && isset($row->id) && !empty($row->id)){
             $sql=sprintf("UPDATE criterios_de_ponderacion SET group_nid=%d,promocionar_idea_min=%d,promocionar_oportunidad_min=%d WHERE id=%d",$group_nid,
$promocionar_idea_min,
$promocionar_oportunidad_min,$row->id);
         }else{
             $sql=sprintf("INSERT INTO criterios_de_ponderacion(group_nid,
promocionar_idea_min,
promocionar_oportunidad_min) VALUES (%d,%d,%d)",$group_nid,
$promocionar_idea_min,
$promocionar_oportunidad_min);
         }
         db_query($sql);
         drupal_set_message(t('Promotion criteria saved'));
    }
    //gemini-2014
    drupal_goto('proyectos/todas');
}
//gemini-2014
function idea_get_num_ideas_creadas($uid,$where_time="1"){
    $result=array();
    $where=array();
    $where[]=$where_time;
    $where[]="n.uid=".$uid;
    $where[]="n.type='idea'";
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='(og_ancestry.group_nid='.$my_grupo->nid.' OR idea.grupo_nid='.$my_grupo->nid.')';
    }
    //
    $sql="SELECT n.* 
    FROM {node} n 
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid
    LEFT JOIN {idea} ON n.vid=idea.vid  
    WHERE ".implode(" AND ",$where)." ORDER BY n.created ASC";
    //print $sql.'<BR>';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function idea_get_simbolo_style($is_taula_header=0){
    $style='';
    if($is_taula_header){
        $style=' style="vertical-align:middle;margin-right:5px;"';
    }
    return $style;
}
//intelsat-2015
function idea_enlazar_confirm_enlazar_idea($item_nid,$idea_nid){
    $item_nid_array=explode(',',$item_nid);
    $item_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($item_nid_array);
    //intelsat-2015
    $num=count($item_nid_array);
    if($num>1){
        foreach($item_nid_array as $i=>$my_item_nid){
            idea_delete_enlazar($idea_nid,$my_item_nid);
            idea_insert_enlazar($idea_nid,$my_item_nid);
        }
    //    
    }else{
        idea_delete_enlazar($idea_nid,$item_nid);
        idea_insert_enlazar($idea_nid,$item_nid);
    }
    //
    //drupal_goto('comment/reply/'.$idea_nid);
    //intelsat-2015
    red_set_bulk_command_executed_message($num);
    drupal_goto('node/'.$idea_nid.'/edit');
}
//intelsat-2015
function idea_enlazar_confirm_enlazar_idea_callback(){
    $item_nid=arg(1);
    $idea_nid=arg(3);
    idea_enlazar_confirm_enlazar_idea($item_nid,$idea_nid);
}
//intelsat-2015
function idea_enlazar_is_ficha_tabla(){
    return hontza_canal_rss_is_fuente_ficha_tabla();
}