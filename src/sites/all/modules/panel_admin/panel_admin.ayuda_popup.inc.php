<?php
function panel_admin_ayuda_popup_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('Title');
    $headers[2]=t('Actions');
    
    $my_limit=30;
    
    $filter_fields=panel_admin_ayuda_popup_define_gestion_ayuda_popup_filter_fields();
   
    $where=array();
    
    if(!empty($filter_fields)){
        foreach($filter_fields as $k=>$f){
            $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_ayuda_popup_filtro');
            if(!empty($v)){
                 switch($f){
                     case 'title':
                         $where[]='node.title LIKE "%'.$v.'%"';
                         break;
                     case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                 }
            } 
        }
    }
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_ayuda_popup_filtro','fecha_inicio','fecha_fin');
    
    $sql=panel_admin_ayuda_popup_get_gestion_ayuda_popup_sql($where);
    
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=$r->node_title;
      $rows[$kont][2]=array('data'=>panel_admin_ayuda_popup_define_acciones($r),'style'=>'white-space:nowrap;');      
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //$rows=panel_admin_ayuda_popup_set_fields($rows);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_ayuda_popup'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //
    return panel_admin_ayuda_popup_define_gestion_ayuda_popup_header().drupal_get_form('panel_admin_ayuda_popup_gestion_ayuda_popup_bulk_form',array($output));
}
function panel_admin_ayuda_popup_define_gestion_ayuda_popup_filter_fields(){
    $result=array('text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_ayuda_popup_define_gestion_ayuda_popup_header(){
    my_add_buscar_js();
    return drupal_get_form('panel_admin_ayuda_popup_filtro_form');
}
function panel_admin_ayuda_popup_filtro_form(){    
    $fs_title=t('Search');
    if(!panel_admin_ayuda_popup_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));        
    /*$form['file_buscar_fs']['title']=array(
        '#type'=>'textfield',
        '#title'=>t('Title'),
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('title','panel_admin_ayuda_popup_filtro'),
    );*/
    contenidos_add_filter_form_fields('panel_admin_ayuda_popup_filtro',$form,0);
    $form['file_buscar_fs']['my_submit']=array(
        '#type'=>'submit',
        '#value'=>t('Apply'),
    );
    $form['file_buscar_fs']['limpiar']=array(
        '#type'=>'submit',
        '#name'=>'limpiar',
        '#value'=>t('Reset'),
    );
    return $form;
}
function panel_admin_ayuda_popup_define_filtro_grupo_options(){
    return panel_admin_items_define_filtro_grupo_options();
}
function panel_admin_ayuda_popup_gestion_ayuda_popup_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form,0);
    /*$form['my_bulk_operations_fs']['publish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Publish'),
      '#name'=>'publish_post_btn',
    );
    $form['my_bulk_operations_fs']['unpublish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Unpublish'),
      '#name'=>'unpublish_post_btn',
    );*/
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function panel_admin_ayuda_popup_set_fields($rows_in){
    $result=$rows_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $node_title=$row[1];
            //$uid=$row[2];
            $grupo_nid=$row[3];
            $my_grupo=node_load($grupo_nid);
            $nid=$row[7]->nid;
            $result[$i][1]=l($node_title,'node/'.$nid);
            //$result[$i][2]=hontza_get_username($uid);
            $result[$i][3]=l($my_grupo->title,'node/'.$grupo_nid);
            $result[$i][7]=panel_admin_ayuda_popup_define_acciones($result[$i][7]);
        }
    }
    return $result;
}
function panel_admin_ayuda_popup_gestion_ayuda_popup_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_ayuda_popup_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                drupal_goto('panel_admin/delete_ayuda_popup_confirm');
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                drupal_goto('panel_admin/publish_post_ayuda_popup_confirm');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                drupal_goto('panel_admin/unpublish_post_ayuda_popup_confirm');
            }
        }
    }
}
function panel_admin_ayuda_popup_delete_ayuda_popup_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_ayuda_popup_nid_array','panel_admin/ayuda_popup');
    return $form;
}
function panel_admin_ayuda_popup_delete_ayuda_popup_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/ayuda_popup';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_ayuda_popup_publish_post_ayuda_popup_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_ayuda_popup_nid_array','panel_admin/ayuda_popup');
    return $form;
}
function panel_admin_ayuda_popup_unpublish_post_ayuda_popup_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_ayuda_popup_nid_array','panel_admin/ayuda_popup');
    return $form;
}
function panel_admin_ayuda_popup_publish_post_ayuda_popup_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/ayuda_popup';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_ayuda_popup_unpublish_post_ayuda_popup_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/ayuda_popup';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_ayuda_popup_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_ayuda_popup_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_ayuda_popup_define_gestion_ayuda_popup_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];
                    if(!empty($v)){
                        $_SESSION[$key]['filter'][$f]=$v;
                    }
                }
            }            
        }
    } 
}
function panel_admin_ayuda_popup_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/ayuda_popup';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function panel_admin_ayuda_popup_get_gestion_ayuda_popup_sql($where_in=array()){
    $where=$where_in;
    $where[]='node.type="my_help"'; 

    if(isset($_REQUEST['title']) && !empty($_REQUEST['title'])){
            $where[]='node.title LIKE ("%'.$_REQUEST['title'].'%")';
    }

    $result='SELECT node.nid AS nid,
       node.title AS node_title,
       node.type AS node_type,
       node.uid AS node_uid,
       node_revisions.format AS node_revisions_format
     FROM node node 
     LEFT JOIN node_revisions node_revisions ON node.vid = node_revisions.vid
     WHERE '.implode(' AND ',$where);
    return $result;
}
function panel_admin_ayuda_popup_is_filter_activated(){
    $key='panel_admin_ayuda_popup_filtro';
            $fields=panel_admin_ayuda_popup_define_gestion_ayuda_popup_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if(isset($_SESSION[$key]['filter'][$f]) && !empty($_SESSION[$key]['filter'][$f])){
                        return 1;
                    }
                }
            }
            return 0;
}
function panel_admin_ayuda_popup_get_help_popup_node_callback(){
    $result='';
    $nid=arg(3);
    $my_lang=arg(4);
    //print $nid;exit();
    if(panel_admin_ayuda_popup_is_node_type_my_help($nid)){
        if(empty($my_lang) || $my_lang=='en'){
            $node=node_load($nid);
        }else{
            $node = node_load(array('tnid' => $nid, 'language' => $my_lang));
            if (!(isset($node->nid) && !empty($node->nid))){
                $node=node_load($nid);
            }
        }
        
        /*echo print_r($node,1);
        exit();*/
    }else{
        $node=new stdClass();
    }
    $result=json_encode($node);
    print $result;
    exit();
}
function panel_admin_ayuda_popup_is_node_type_my_help($nid){
    $where=array();
    $where[]='node.type="my_help"';
    $where[]='node.nid='.$nid;
    $sql='SELECT node.nid,node.type FROM {node} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return 1;
    }
    return 0;
}