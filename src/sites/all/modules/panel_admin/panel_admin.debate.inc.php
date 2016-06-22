<?php
function panel_admin_debate_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('Author');
    $headers[2]=t('Discussion');
    //$headers[2]=t('Author');
    $headers[3]=t('Group');
    //$headers[4]=t('Comments');
    //$headers[4]=t('Creation date');
    $headers[4]=t('Last update');    
    //$headers[5]=t('Published');
    $headers[5]=t('Actions');
    
    $my_limit=30;
    
    $filter_fields=panel_admin_debate_define_gestion_debate_filter_fields();
   
    $where=array();
    $where[]='1';
    $where[]='node.type in ("debate")';

    if(!empty($filter_fields)){
        foreach($filter_fields as $k=>$f){
            $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_debate_filtro');
            if(!empty($v)){
                 switch($f){
                     case 'grupo_nid':
                         //intelsat-2016
                         //$where[]='og_ancestry.group_nid='.$v;
                         $grupo_nid_array=array_keys($v);
                         $where[]='og_ancestry.group_nid IN('.implode(',',$grupo_nid_array).')'; 
                         break;
                     case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                 }
            } 
        }
    }
    
     $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_debate_filtro','fecha_inicio','fecha_fin');
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    users.name AS users_name,
    og_ancestry.nid AS og_ancestry_nid,
    node_comment_statistics.comment_count AS node_comment_statistics_comment_count,
    node.created AS node_created,
    node.status AS node_status,
    node.uid AS node_uid,
    node.type AS node_type,
    node_revisions.format AS node_revisions_format,
    og_ancestry.group_nid AS og_ancestry_group_nid,
    node.changed AS node_changed
    FROM {node} node INNER JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC'; 
    
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=$r->users_name;
      $rows[$kont][2]=$r->node_title;      
      $rows[$kont][3]=$r->og_ancestry_group_nid;
      //$rows[$kont][4]=$r->node_comment_statistics_comment_count;
      //$rows[$kont][4]=date('Y-m-d H:i',$r->node_created);
      //$rows[$kont][5]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][4]=date('Y-m-d H:i',$r->node_changed);
      $rows[$kont][5]=$r;            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=panel_admin_debate_set_fields($rows);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_debate'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //
    return panel_admin_debate_define_gestion_debate_header().drupal_get_form('panel_admin_debate_gestion_debate_bulk_form',array($output));
}
function panel_admin_debate_define_gestion_debate_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_debate_define_gestion_debate_header(){
    my_add_buscar_js();
    return drupal_get_form('panel_admin_debate_filtro_form');
}
function panel_admin_debate_filtro_form(){
    $fs_title=t('Search');
    if(panel_admin_debate_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    /*$form['file_buscar_fs']['grupo_nid']=array(
        '#type'=>'select',
        '#title'=>t('Filter by group'),
        '#options'=>panel_admin_debate_define_filtro_grupo_options(),
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('grupo_nid','panel_admin_debate_filtro'),
    );*/
    contenidos_add_filter_form_fields('panel_admin_debate_filtro',$form);
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
function panel_admin_debate_define_filtro_grupo_options(){
    return panel_admin_items_define_filtro_grupo_options();
}
function panel_admin_debate_gestion_debate_bulk_form(){
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
function panel_admin_debate_set_fields($rows_in){
    $result=$rows_in;
    $faktore=50;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $uid=$row[5]->node_uid;
            $user_image=hontza_grupos_mi_grupo_get_user_img($uid,$faktore);
            $result[$i][1]=$user_image;            
            $node_title=$row[2];
            //$uid=$row[2];
            $grupo_nid=$row[3];
            $my_grupo=node_load($grupo_nid);
            $nid=$row[5]->nid;            
            //$result[$i][1]='<div style="float:left;white-space:nowrap;"><div style="float:left;">'.l($node_title,'node/'.$nid).'</div>';
            $result[$i][2]='<div style="float:left;"><div style="float:left;">'.l($node_title,'node/'.$nid).'</div>';
            //intelsat-2015
            //if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
            $result[$i][2].=panel_admin_debate_get_numero_comentarios_icono($row,$nid);        
            //}
            //
            $result[$i][2].='</div>';
            //$result[$i][2]=hontza_get_username($uid);
            $result[$i][3]=l($my_grupo->title,'node/'.$grupo_nid);
            
            $result[$i][5]=array('data'=>panel_admin_debate_define_acciones($result[$i][5]),'style'=>'white-space:nowrap;');
        }
    }
    return $result;
}
function panel_admin_debate_gestion_debate_bulk_form_submit($form, &$form_state) {
    panel_admin_debate_gestion_debates_bulk_submit($form,$form_state,'panel_admin_debate');
}
function panel_admin_debate_delete_debate_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_debate_nid_array','panel_admin/debate');
    return $form;
}
function panel_admin_debate_delete_debate_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/debate';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_debate_publish_post_debate_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_debate_nid_array','panel_admin/debate');
    return $form;
}
function panel_admin_debate_unpublish_post_debate_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_debate_nid_array','panel_admin/debate');
    return $form;
}
function panel_admin_debate_publish_post_debate_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/debate';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_debate_unpublish_post_debate_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/debate';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_debate_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_debate_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_debate_define_gestion_debate_filter_fields();
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
function panel_admin_debate_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/debate';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_debate_define_accion_publish($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_debate_get_numero_comentarios_icono($row,$nid){
    if($row[6]->node_comment_statistics_comment_count>0){
        $html=array();
        $html[]='<div class="items-coments" style="float:left;">';
        $html[]='<div class="div_icono_con_numero">';
        $html[]='<span class="span_icono_con_numero">';
        $html[]=$row[6]->node_comment_statistics_comment_count;
        $html[]='</span>';
        $html[]=l(my_get_icono_action("comment-star",$label_comment_count),'node/'.$nid,array('html'=>true,'fragment'=>'comments'));
        $html[]='</div>';
        $html[]='</div>';
        return ' '.implode('',$html);
    }
    return '';
}
function panel_admin_debate_block_content(){
    if(panel_admin_admin_access()){
        return l(t('List of discussions'), 'panel_admin/debate');
    }
    return '';
}
function panel_admin_debate_define_accion_publish($r,$destination){
    return panel_admin_items_define_accion_publish($r,$destination);
}
function panel_admin_debate_gestion_debates_bulk_submit($form,&$form_state,$type=''){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_debate_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                if($type=='contenidos_debates'){
                    drupal_goto('contenidos/debates/delete_debates_confirm');
                }else{
                    drupal_goto('panel_admin/delete_debate_confirm');
                }                    
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                if($type=='contenidos_debates'){
                    drupal_goto('contenidos/debates/publish_post_debates_confirm');
                }else{
                    drupal_goto('panel_admin/publish_post_debate_confirm');
                }    
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                if($type=='contenidos_debates'){
                    drupal_goto('contenidos/debates/unpublish_post_debates_confirm');
                }else{
                    drupal_goto('panel_admin/unpublish_post_debate_confirm');
                }    
            }
        }
    }
    
}
function panel_admin_debate_is_filter_activated(){
    $fields=panel_admin_debate_define_gestion_debate_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_debate_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_debate_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}