<?php
function panel_admin_ayuda_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('FAQ');
    //$headers[2]=t('Published');
    $headers[2]=t('Actions');
    
    $my_limit=30;
    
    
    $filter_fields=panel_admin_ayuda_define_gestion_ayuda_filter_fields();
   
    $where=array();
    $where[]='node.type in ("faq")';
    
    if(!empty($filter_fields)){
        foreach($filter_fields as $k=>$f){
            $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_ayuda_filtro');
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
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_ayuda_filtro','fecha_inicio','fecha_fin');
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    node.status AS node_status,
    node.uid AS node_uid, node.type AS node_type,
    node_revisions.format AS node_revisions_format,
    node.created AS node_created
    FROM {node} node 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC ';
    
    $res=db_query($sql);        
    
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=l($r->node_title,'node/'.$r->nid);
      //$rows[$kont][2]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][2]=array('data'=>panel_admin_ayuda_define_acciones($r),'style'=>'white-space:nowrap;');            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_ayuda'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Management of FAQs'));
    //
    return panel_admin_ayuda_define_gestion_ayuda_header().drupal_get_form('panel_admin_ayuda_gestion_ayuda_bulk_form',array($output));
}
function panel_admin_ayuda_define_gestion_ayuda_header(){
    my_add_buscar_js();
    return drupal_get_form('panel_admin_ayuda_filtro_form');
}
function panel_admin_ayuda_filtro_form(){    
    $fs_title=t('Search');
    if(!panel_admin_ayuda_is_filter_activated()){
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
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('title','panel_admin_ayuda_filtro'),
    );*/
    contenidos_add_filter_form_fields('panel_admin_ayuda_filtro',$form,0);
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
function panel_admin_ayuda_gestion_ayuda_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function panel_admin_ayuda_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/ayuda';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('viewmag',t('View')),'node/'.$r->nid,array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_ayuda_define_accion_publish($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_ayuda_gestion_ayuda_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_ayuda_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                drupal_goto('panel_admin/delete_ayuda_confirm');
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                drupal_goto('panel_admin/publish_post_ayuda_confirm');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                drupal_goto('panel_admin/unpublish_post_ayuda_confirm');
            }
        }
    }
}
function panel_admin_ayuda_delete_ayuda_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_ayuda_nid_array','panel_admin/ayuda');
    return $form;
}
function panel_admin_ayuda_delete_ayuda_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/ayuda';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_ayuda_publish_post_ayuda_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_ayuda_nid_array','panel_admin/ayuda');
    return $form;
}
function panel_admin_ayuda_unpublish_post_ayuda_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_ayuda_nid_array','panel_admin/ayuda');
    return $form;
}
function panel_admin_ayuda_publish_post_ayuda_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/ayuda';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_ayuda_unpublish_post_ayuda_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/ayuda';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_ayuda_block_content(){
    return '';
}        
function panel_admin_ayuda_define_accion_publish($r,$destination){
    return panel_admin_items_define_accion_publish($r,$destination);
}
function panel_admin_ayuda_define_gestion_ayuda_filter_fields(){
    $result=array('text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_ayuda_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_ayuda_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_ayuda_define_gestion_ayuda_filter_fields();
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
function panel_admin_ayuda_is_filter_activated(){
    $key='panel_admin_ayuda_filtro';
            $fields=panel_admin_ayuda_define_gestion_ayuda_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if(isset($_SESSION[$key]['filter'][$f]) && !empty($_SESSION[$key]['filter'][$f])){
                        return 1;
                    }
                }
            }
            return 0;
}