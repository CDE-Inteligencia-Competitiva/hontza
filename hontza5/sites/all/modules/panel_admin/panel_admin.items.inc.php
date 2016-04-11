<?php
function panel_admin_items_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('News');
    $headers[2]=t('Channel');
    $headers[3]=t('Group');
    $headers[4]=t('Date');
    //$headers[5]=t('Type');
    //$headers[5]=t('Published');
    $headers[5]=t('Actions');
    
    $my_limit=30;
    
    
    $filter_fields=panel_admin_items_define_gestion_items_filter_fields();
    
   $where=array();
   $where[]='1';
   $where[]='node.type in ("item")';
   
  if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_items_filtro');
           if(!empty($v)){
                switch($f){
                    case 'grupo_nid':
                        $where[]='og_ancestry.group_nid='.$v;
                        break;
                    case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                    /*case 'fecha_ini':
                    case 'fecha_fin':    
                        $where[]=hontza_get_usuarios_acceso_where_time('node.changed','contenidos_filtro','fecha_ini','fecha_fin');
                        break;*/
                }
           } 
       }
   }
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_items_filtro','fecha_inicio','fecha_fin');
    
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    og_ancestry.nid AS og_ancestry_nid,
    node.created AS node_created,
    node.type AS node_type,
    node.status AS node_status,
    node.uid AS node_uid,
    node_revisions.format AS node_revisions_format,
    og_ancestry.group_nid AS og_ancestry_group_nid, 
    node.changed AS node_changed 
    FROM {node} node 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC';
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=$r->node_title;
      $rows[$kont][2]=$r->nid;
      $rows[$kont][3]=$r->og_ancestry_group_nid;
      $rows[$kont][4]=date('Y-m-d H:i',$r->node_changed);
      //$rows[$kont][5]=ucfirst($r->node_type);
      //$rows[$kont][5]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][5]=$r;            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=panel_admin_items_set_fields($rows);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Management of News'));
    //
    return panel_admin_items_define_gestion_items_header().drupal_get_form('panel_admin_items_gestion_items_bulk_form',array($output));
}
function panel_admin_items_define_gestion_items_header(){
    my_add_buscar_js();
    return drupal_get_form('panel_admin_items_filtro_form');
}
function panel_admin_items_gestion_items_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form,1);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function panel_admin_items_set_fields($rows_in){
    $result=$rows_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $node_title=$row[1];
            $nid=$row[2];
            $grupo_nid=$row[3];
            $my_grupo=node_load($grupo_nid);
            $r=new stdClass();
            $r->nid=$nid;
            $result[$i][1]=l($node_title,'node/'.$nid);
            $result[$i][2]=my_gestion_items_canal($r);
            $result[$i][3]=l($my_grupo->title,'node/'.$grupo_nid);
            $result[$i][5]=array('data'=>panel_admin_items_define_acciones($result[$i][5]),'style'=>'white-space:nowrap;');
        }
    }
    return $result;
}
function panel_admin_items_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/items';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_items_define_accion_publish($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_items_gestion_items_bulk_form_submit($form, &$form_state) {
    panel_admin_items_gestion_items_bulk_submit($form,$form_state,'panel_admin_items');
}
function panel_admin_items_delete_items_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_items_nid_array','panel_admin/items');
    return $form;
}
function panel_admin_items_delete_items_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/items';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_items_publish_post_items_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_items_nid_array','panel_admin/items');
    return $form;
}
function panel_admin_items_unpublish_post_items_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_items_nid_array','panel_admin/items');
    return $form;
}
function panel_admin_items_publish_post_items_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/items';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_items_unpublish_post_items_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/items';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_items_filtro_form(){
    $fs_title=t('Search');
    if(!panel_admin_items_is_filter_activated()){
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
        '#options'=>panel_admin_items_define_filtro_grupo_options(),
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('grupo_nid','panel_admin_items_filtro'),
    );
    */
    
    contenidos_add_filter_form_fields('panel_admin_items_filtro',$form);
    
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
function panel_admin_items_define_filtro_grupo_options(){
    $result=array();
    $result[0]='<'.t('Any').'>';
    $grupo_array=panel_admin_items_get_grupo_array();
    $max=custom_menu_define_grupo_title_max_len();
    if(!empty($grupo_array)){
        foreach($grupo_array as $i=>$row){
            $len=strlen($row->title);
            if($len>$max){
                $result[$row->nid]=substr($row->title,0,$max)."...";
            }else{
                $result[$row->nid]=$row->title;
            }    
        }
    }
    return $result;
}
function panel_admin_items_get_grupo_array(){
    $result=array();
    $sql='SELECT * FROM {node} node WHERE node.type="grupo" ORDER BY node.title ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function panel_admin_items_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_items_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_items_define_gestion_items_filter_fields();
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
function panel_admin_items_define_gestion_items_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_items_block_content(){
    if(panel_admin_admin_access()){
        $html=array();
        $html[]=l(t('List of news'), 'panel_admin/items');
        $html[]=l(t('List of user news'), 'panel_admin/noticias');
        return implode('<br>',$html);
    }
    return '';
}
function panel_admin_items_define_accion_publish($r,$destination){
    $param0=arg(0);
    if($param0!='contenidos'){
        $param0='panel_admin';
    }    
    if(isset($r->node_status) && !empty($r->node_status)){
        return $html[]=l(my_get_icono_action('unpublish_post',t('Unpublish')),$param0.'/items/unpublish_post/'.$r->nid,array('query'=>$destination,'html'=>true));
    }
    return $html[]=l(my_get_icono_action('publish_post',t('Publish')),$param0.'/items/publish_post/'.$r->nid,array('query'=>$destination,'html'=>true));
}
function panel_admin_items_publish_post_callback(){
    $nid=arg(3);
    update_node_status($nid,1);
    drupal_goto($_REQUEST['destination']);
}
function panel_admin_items_unpublish_post_callback(){
    $nid=arg(3);
    update_node_status($nid,0);
    drupal_goto($_REQUEST['destination']);
}
function panel_admin_items_add_bulk_operations_form_fields(&$form,$is_publish_post=1,$delete_btn_name='delete_node_btn',$with_add_users=0){
    if($with_add_users){
      $form['my_bulk_operations_fs']['add_users_btn']=array(
        '#type'=>'image_button',
        '#name'=>'add_users_btn',
        '#src'=>'sites/all/themes/buho/images/icons/edit_add.png',
        '#attributes'=>array('alt'=>t('Add users to groups'),'title'=>t('Add users to groups')),
      );
      $form['my_bulk_operations_fs']['remove_users_btn']=array(
        '#type'=>'image_button',
        '#name'=>'remove_users_btn',
        '#src'=>'sites/all/themes/buho/images/icons/edit_remove.png',
        '#attributes'=>array('alt'=>t('Remove users to groups'),'title'=>t('Remove users to groups')),
      );
    }
    $form['my_bulk_operations_fs'][$delete_btn_name]=array(
      //'#type'=>'submit',
      '#type'=>'image_button',
      //'#value'=>t('Delete node'),
      '#name'=>$delete_btn_name,
      '#src'=>'sites/all/themes/buho/images/icons/delete.png',
      '#attributes'=>array('alt'=>t('Delete node'),'title'=>t('Delete')),
    );
    if($is_publish_post){
        $form['my_bulk_operations_fs']['publish_post_btn']=array(
          //'#type'=>'submit',
          '#type'=>'image_button',  
          //'#value'=>t('Publish'),
          '#name'=>'publish_post_btn',
          '#src'=>'sites/all/themes/buho/images/icons/publish_post.png',
          '#attributes'=>array('alt'=>t('Publish'),'title'=>t('Publish')),  
        );
        $form['my_bulk_operations_fs']['unpublish_post_btn']=array(
          //'#type'=>'submit',
          '#type'=>'image_button',   
          //'#value'=>t('Unpublish'),
          '#name'=>'unpublish_post_btn',
          '#src'=>'sites/all/themes/buho/images/icons/unpublish_post.png',
          '#attributes'=>array('alt'=>t('Unpublish'),'title'=>t('Unpublish')),    
        );
    }
}
function panel_admin_items_gestion_items_bulk_submit($form, &$form_state,$type='') {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            if($type=='contenidos_fuentes'){
                $_SESSION['block_panel_admin_fuentes_nid_array']=$nid_array;
            }else if($type=='panel_admin_noticias_destacadas'){    
                $_SESSION['block_panel_admin_noticias_destacadas_nid_array']=$nid_array;
            }else{
                $_SESSION['block_panel_admin_items_nid_array']=$nid_array;
            }
            if(strcmp($button_name,'delete_node_btn')==0){
                if($type=='contenidos_items'){
                    drupal_goto('contenidos/items/delete_items_confirm');
                }else if($type=='contenidos_fuentes'){
                    drupal_goto('contenidos/fuentes/delete_fuentes_confirm');
                }else if($type=='panel_admin_noticias_destacadas'){    
                    drupal_goto('panel_admin/delete_noticias_destacadas_confirm');
                }else{
                    drupal_goto('panel_admin/delete_items_confirm');
                }                
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                if($type=='contenidos_items'){
                    drupal_goto('contenidos/items/publish_post_items_confirm');
                }else if($type=='contenidos_fuentes'){
                    drupal_goto('contenidos/fuentes/publish_post_fuentes_confirm');
                }else if($type=='panel_admin_noticias_destacadas'){    
                    drupal_goto('panel_admin/publish_post_noticias_destacadas_confirm');
                }else{
                    drupal_goto('panel_admin/publish_post_items_confirm');
                }    
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                if($type=='contenidos_items'){
                    drupal_goto('contenidos/items/unpublish_post_items_confirm');
                }else if($type=='contenidos_fuentes'){
                    drupal_goto('contenidos/fuentes/unpublish_post_fuentes_confirm');
                }else if($type=='panel_admin_noticias_destacadas'){    
                    drupal_goto('panel_admin/unpublish_post_noticias_destacadas_confirm');
                }else{
                    drupal_goto('panel_admin/unpublish_post_items_confirm');
                }    
            }
        }
    }
}
function panel_admin_items_is_filter_activated(){
    $fields=panel_admin_items_define_gestion_items_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_items_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_items_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}