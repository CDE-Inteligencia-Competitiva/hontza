<?php
function contenidos_ideas_custom_access(){
    $groups_array=get_usuario_grupos_options();
    $modo_estrategia=1;        
    if(!empty($groups_array)){
        foreach($groups_array as $grupo_nid=>$grupo_title){
            if(is_administrador_grupo($modo_estrategia,$grupo_nid)){
                return TRUE;
            }
        }            
    }
    return FALSE;
}
function contenidos_ideas_callback(){
    global $user;
    //
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[2]=array('data'=>t('Group'),'field'=>'grupo_nid');
    $headers[3]=array('data'=>t(t('Last Update')),'field'=>'node_changed');
    $headers[4]=t('Actions');
    $my_limit=30;
    
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
        }else if($order==t('Group')){
            $field='og_ancestry_group_nid';
        }else if($order==t('Date')){
            $field='node_changed';
        }
    }
    
    $filter_fields=contenidos_ideas_define_filter_fields();
    
   $where=array();
   $where[]='1';
   $where[]='users.uid = '.$user->uid;
   $where[]='node.type in ("idea")';  
   
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f,'contenidos_ideas_filtro');
           if(!empty($v)){
                switch($f){
                    case 'grupo_nid':
                        $where[]='idea.grupo_nid='.$v;
                        break;
                    case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                }
           } 
       }
   }
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','contenidos_ideas_filtro','fecha_inicio','fecha_fin');
   
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    node.type AS node_type,
    og_ancestry.nid AS og_ancestry_nid,
    node.created AS node_created,
    node.status AS node_status,
    node.uid AS node_uid,
    node.changed AS node_changed,
    idea.grupo_nid AS grupo_nid 
    FROM {node} node 
    LEFT JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN {idea} idea ON node.vid=idea.vid
    LEFT JOIN {node_revisions} node_revisions ON node.vid=node_revisions.vid 
    WHERE '.implode(' AND ',$where).' 
    GROUP BY idea.nid     
    ORDER BY '.$field.' '.$sort;
    //
    $res=db_query($sql);            
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $node=node_load($r->nid);
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->uid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';
      $rows[$kont][1]=l(red_funciones_resumir_titulo($r->node_title),'node/'.$r->nid);
      $grupo_nid=$r->grupo_nid;
      $rows[$kont][2]=contenidos_get_grupo_link($grupo_nid,$grupo_title);
      $rows[$kont][3]=date('Y-m-d H:i',$r->node_changed);
      $rows[$kont][4]=array('data'=>contenidos_ideas_define_acciones($r),'style'=>'white-space:nowrap;');
      $rows[$kont]['grupo_title']=$grupo_title;
      $kont++;
    }
    //
    if($order==t('Group')){
        $rows=array_ordenatu($rows,'grupo_title', $sort,0);        
    }
    $rows=hontza_unset_array($rows,array('grupo_title'));
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_grupos'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('My Ideas'));
    /*$output=contenidos_my_menu().$output;
    return $output;*/
    return contenidos_ideas_define_header().drupal_get_form('contenidos_ideas_bulk_form',array($output));
}
function contenidos_ideas_define_acciones($row){
    return contenidos_define_acciones($row,drupal_get_destination());
}
function contenidos_ideas_define_header(){
    my_add_buscar_js();
    return drupal_get_form('contenidos_ideas_filtro_form');
}
function contenidos_ideas_bulk_form(){
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
function contenidos_ideas_bulk_form_submit($form, &$form_state) {
    contenidos_ideas_bulk_submit($form,$form_state,'contenidos_ideas');
}
function contenidos_ideas_delete_ideas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','contenidos_ideas_nid_array','contenidos/ideas');
    return $form;
}
function contenidos_ideas_delete_ideas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/contenidos/ideas';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function contenidos_ideas_publish_post_ideas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','contenidos_ideas_nid_array','contenidos/ideas');
    return $form;
}
function contenidos_ideas_unpublish_post_ideas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','contenidos_ideas_nid_array','contenidos/ideas');
    return $form;
}
function contenidos_ideas_publish_post_ideas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='contenidos/ideas';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function contenidos_ideas_unpublish_post_ideas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='contenidos/ideas';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function contenidos_ideas_filtro_form(){
    $fs_title=t('Search');
    if(contenidos_ideas_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    /*$form['file_buscar_fs']['node_type']=array(
        '#type'=>'select',
        '#title'=>t('Select the type of Source'),
        '#options'=>contenidos_ideas_define_node_type_options(),
        '#default_value'=>contenidos_ideas_get_filter_value('node_type'),
    );*/
    contenidos_add_filter_form_fields('contenidos_ideas_filtro',$form);
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
function contenidos_ideas_bulk_submit($form,$form_state,$type=''){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['contenidos_ideas_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                if($type=='contenidos_ideas'){
                    drupal_goto('contenidos/ideas/delete_ideas_confirm');
                }                   
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                if($type=='contenidos_ideas'){
                    drupal_goto('contenidos/ideas/publish_post_ideas_confirm');
                }              
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                if($type=='contenidos_ideas'){
                    drupal_goto('contenidos/ideas/unpublish_post_ideas_confirm');
                }             
            }
        }
    }
}
function contenidos_ideas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='contenidos_ideas_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=contenidos_ideas_define_filter_fields();
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
function contenidos_ideas_is_filter_activated(){
    $fields=contenidos_fuentes_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['contenidos_ideas_filtro']['filter'][$f]) && !empty($_SESSION['contenidos_ideas_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function contenidos_ideas_define_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}