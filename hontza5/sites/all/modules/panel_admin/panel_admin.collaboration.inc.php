<?php
function panel_admin_collaboration_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('Author');
    $headers[2]=t('Wiki Documents');
    $headers[3]=t('Group');
    //$headers[4]=t('Creation Date');
    $headers[4]=t('Last update');
    //$headers[6]=t('Published');
    $headers[6]=t('Actions');
    
    $my_limit=30;
    
    $filter_fields=panel_admin_collaboration_define_gestion_collaboration_filter_fields();
   
    $where=array();
    $where[]='1';
    $where[]='node.type in ("wiki")';

    if(!empty($filter_fields)){
        foreach($filter_fields as $k=>$f){
            $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_collaboration_filtro');
            if(!empty($v)){
                 switch($f){
                     case 'grupo_nid':
                         $where[]='og_ancestry.group_nid='.$v;
                         break;
                     case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                 }
            } 
        }
    }
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_collaboration_filtro','fecha_inicio','fecha_fin');
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    users.name AS users_name,
    users.uid AS users_uid,
    og_ancestry.nid AS og_ancestry_nid,
    node.created AS node_created,
    node.changed AS node_changed,
    node.status AS node_status,
    node.uid AS node_uid,
    node.type AS node_type,
    node_revisions.format AS node_revisions_format,
    og_ancestry.group_nid AS og_ancestry_group_nid
    FROM {node} node INNER JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC';
    
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=$r->users_uid;      
      $rows[$kont][2]=$r->node_title;
      $rows[$kont][3]=$r->og_ancestry_group_nid;
      /*$rows[$kont][4]=date('Y-m-d H:i',$r->node_created);
      $rows[$kont][5]=date('Y-m-d H:i',$r->node_changed);*/
      //$rows[$kont][4]=date('Y-m-d',$r->node_created);
      $rows[$kont][4]=date('Y-m-d',$r->node_changed);
      //$rows[$kont][6]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][5]=$r;            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=panel_admin_collaboration_set_fields($rows);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_collaboration'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Management of wiki documents'));
    //
    return panel_admin_collaboration_define_gestion_collaboration_header().drupal_get_form('panel_admin_collaboration_gestion_collaboration_bulk_form',array($output));
}
function panel_admin_collaboration_define_gestion_collaboration_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_collaboration_define_gestion_collaboration_header(){
    my_add_buscar_js();
    return drupal_get_form('panel_admin_collaboration_filtro_form');
}
function panel_admin_collaboration_filtro_form(){
    $fs_title=t('Search');
    if(!panel_admin_collaboration_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));    
    /*$form['file_buscar_fs']['grupo_nid']=array(
        '#type'=>'select',
        '#title'=>t('Filter by group'),
        '#options'=>panel_admin_collaboration_define_filtro_grupo_options(),
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('grupo_nid','panel_admin_collaboration_filtro'),
    );*/
    contenidos_add_filter_form_fields('panel_admin_collaboration_filtro',$form);
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
function panel_admin_collaboration_define_filtro_grupo_options(){
    return panel_admin_items_define_filtro_grupo_options();
}
function panel_admin_collaboration_gestion_collaboration_bulk_form(){
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
function panel_admin_collaboration_set_fields($rows_in){
    $result=$rows_in;
    $faktore=50;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $uid=$row[1];            
            $user_image=hontza_grupos_mi_grupo_get_user_img($uid,$faktore);
            $result[$i][1]=$user_image;            
            $node_title=$row[2];
            $grupo_nid=$row[3];
            $my_grupo=node_load($grupo_nid);
            $nid=$row[5]->nid;
            $result[$i][2]=l($node_title,'node/'.$nid);
            //$result[$i][2]=hontza_get_username($uid);
            $result[$i][3]=l($my_grupo->title,'node/'.$grupo_nid);
            $result[$i][5]=array('data'=>panel_admin_collaboration_define_acciones($result[$i][5]),'style'=>'white-space:nowrap;');
        }
    }
    return $result;
}
function panel_admin_collaboration_gestion_collaboration_bulk_form_submit($form, &$form_state) {
    panel_admin_collaboration_gestion_collaboration_bulk_submit($form,$form_state,'panel_admin_collaboration');
}
function panel_admin_collaboration_delete_collaboration_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_collaboration_nid_array','panel_admin/collaboration');
    return $form;
}
function panel_admin_collaboration_delete_collaboration_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/collaboration';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_collaboration_publish_post_collaboration_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_collaboration_nid_array','panel_admin/collaboration');
    return $form;
}
function panel_admin_collaboration_unpublish_post_collaboration_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_collaboration_nid_array','panel_admin/collaboration');
    return $form;
}
function panel_admin_collaboration_publish_post_collaboration_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/collaboration';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_collaboration_unpublish_post_collaboration_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/collaboration';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_collaboration_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_collaboration_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_collaboration_define_gestion_collaboration_filter_fields();
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
function panel_admin_collaboration_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/collaboration';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_collaboration_define_accion_publish($r, $destination);
    return implode('&nbsp;',$html);
}
function panel_admin_collaboration_is_filter_activated(){
    $key='panel_admin_collaboration_filtro';
            $fields=panel_admin_collaboration_define_gestion_collaboration_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if(isset($_SESSION[$key]['filter'][$f]) && !empty($_SESSION[$key]['filter'][$f])){
                        return 1;
                    }
                }
            }
            return 0;
}
function panel_admin_collaboration_block_content(){
    if(panel_admin_admin_access()){
        return l(t('List of wiki pages'), 'panel_admin/collaboration');
    }
    return '';
}
function panel_admin_collaboration_define_accion_publish($r,$destination){
    return panel_admin_items_define_accion_publish($r,$destination);
}
function panel_admin_collaboration_gestion_collaboration_bulk_submit($form, &$form_state,$type=''){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_collaboration_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                if($type=='contenidos_collaboration'){
                    drupal_goto('contenidos/collaboration/delete_collaboration_confirm');
                }else{
                    drupal_goto('panel_admin/delete_collaboration_confirm');
                }            
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                if($type=='contenidos_collaboration'){
                    drupal_goto('contenidos/collaboration/publish_post_collaboration_confirm');
                }else{
                    drupal_goto('panel_admin/publish_post_collaboration_confirm');
                }            
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                if($type=='contenidos_collaboration'){
                    drupal_goto('contenidos/collaboration/unpublish_post_collaboration_confirm');
                }else{
                    drupal_goto('panel_admin/unpublish_post_collaboration_confirm');
                }    
            }
        }
    }
}    