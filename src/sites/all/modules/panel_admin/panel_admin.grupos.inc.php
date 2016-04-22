<?php
function panel_admin_grupos_callback(){
    $output='';
    
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('Name');
    $headers[2]=t('Description');
    //$headers[3]=t('Subject Area');
    //$headers[4]=t('Type of group');
    $headers[3]=t('Users');
    //$headers[6]=t('Published');
    $headers[4]=t('Actions');
    
    $my_limit=20;
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    og.og_description AS og_og_description,
    node_data_field_tematica.field_tematica_value AS node_data_field_tematica_field_tematica_value, 
    node.type AS node_type,
    node.vid AS node_vid,
    term_data.name AS term_data_name,
    term_data.vid AS term_data_vid,
    term_data.tid AS term_data_tid,
    (SELECT COUNT(*) FROM {og_uid} ou INNER JOIN {users} u ON ou.uid = u.uid WHERE ou.nid = og.nid AND u.status > 0 AND ou.is_active >= 1 AND ou.is_admin >= 0 ) AS member_count,
    node.status AS node_status,
    node.uid AS node_uid,
    node_revisions.format AS node_revisions_format,
    node.created AS node_created 
    FROM {node} node LEFT JOIN {og} og ON node.nid = og.nid 
    LEFT JOIN {content_type_grupo} node_data_field_tematica ON node.vid = node_data_field_tematica.vid 
    LEFT JOIN {term_node} term_node ON node.vid = term_node.vid 
    LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE node.type in ("grupo") ORDER BY node_created DESC'; 
    
    $res=db_query($sql);        
    
    $rows=array();
    $kont=0;
    $max=custom_menu_define_grupo_title_max_len();
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      //$rows[$kont][1]=$r->node_title;
      $icono_red_alerta=hontza_get_icono_grupo_red_alerta($r);
      $type_of_group=panel_admin_grupos_get_type_of_group($r->term_data_tid);
      $icono_privacidad=panel_admin_get_tipo_grupo_icono($r,$icono_red_alerta,0,$title_popup);            
      $grupo_title=$r->node_title;
      $len=strlen($grupo_title);      
      if($len>$max){
        $grupo_title=substr($grupo_title,0,$max)."...";
      }
      $grupo_title=l($grupo_title,'node/'.$r->nid,array('attributes'=>array('title'=>$title_popup)));      
      $rows[$kont][1]='<div style="white-space:nowrap;">'.$icono_congelado.$icono_privacidad.$icono_red_alerta.$grupo_title.'</div>'; 
      $rows[$kont][2]=array('data'=>$r->og_og_description,'class'=>'panel_admin_td_description');
      //$rows[$kont][3]=$r->node_data_field_tematica_field_tematica_value;
      //$rows[$kont][4]=$type_of_group;
      //$rows[$kont][4]=$icono_privacidad;
      $rows[$kont][3]=l($r->member_count,'og/users/'.$r->nid.'/faces');
      //$rows[$kont][6]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][4]=array('data'=>panel_admin_grupos_define_acciones($r),'style'=>'white-space:nowrap;');            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_grupos'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //
    return panel_admin_grupos_define_gestion_grupos_header().drupal_get_form('panel_admin_grupos_gestion_grupos_bulk_form',array($output));
}
function panel_admin_grupos_define_gestion_grupos_header(){
    /*$html=array();
    $html[]='<div class="view-header">';
    $html[]=l(t('Create new Group'),'node/add/grupo',array('query'=>'destination=panel_admin/grupos','attributes'=>array('class'=>'add')));
    $html[]='<div>';
    return implode('',$html);*/
    return '';
}
function panel_admin_grupos_gestion_grupos_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form,1,'delete_node_btn',1);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    //$form['#submit'][]='my_noticias_publicas_form_submit';
    return $form;
}
function panel_admin_grupos_get_type_of_group($tid){
    $term=taxonomy_get_term_by_language($tid);
    if(isset($term->name) && !empty($term->name)){
        return $term->name;
    }
    return '';
}
function panel_admin_grupos_get_published_label($status){
    if($status){
        return t('Yes');
    }
    return t('No');
}
function panel_admin_grupos_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/grupos';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $url_add_remove_users='og/users/'.$r->nid;
    $url_add_users='og/users/'.$r->nid.'/add_user';
    $url_remove_users=$url_add_remove_users;
    if(!empty($url_add_users)){
        $icono=my_get_icono_action('add',t('Add users'));
        $html[]=l($icono,$url_add_users,array('query'=>$destination,'html'=>true));
    }
    if(!empty($url_remove_users)){
        $icono=my_get_icono_action('edit_remove',t('Remove users'),'remove_users');
        $html[]=l($icono,$url_remove_users,array('query'=>$destination,'html'=>true));
    }
    $html[]=panel_admin_grupos_define_accion_publish($r,$destination);
    //intelsat-2015
    if(hontza_canal_rss_is_usuario_basico_activado()){
        $html[]=usuario_basico_panel_admin_grupos_get_link_inicio($r,$destination);
    }
    //
    return implode('&nbsp;',$html);
}
function panel_admin_grupos_gestion_grupos_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn','add_users_btn','remove_users_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_grupos_nid_array']=$nid_array;                
            if(strcmp($button_name,'add_users_btn')==0){
                drupal_goto('panel_admin/add_users_grupos_confirm',drupal_get_destination());
            }else if(strcmp($button_name,'remove_users_btn')==0){
                drupal_goto('panel_admin/remove_users_grupos_confirm',drupal_get_destination());
            }else if(strcmp($button_name,'delete_node_btn')==0){
                drupal_goto('panel_admin/delete_grupos_confirm');
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                drupal_goto('panel_admin/publish_post_grupos_confirm');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                drupal_goto('panel_admin/unpublish_post_grupos_confirm');
            }
        }
    }
}
function panel_admin_grupos_delete_grupos_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node');
    return $form;
}
function panel_admin_grupos_get_grupos_confirm($my_type,$field='block_panel_admin_grupos_nid_array',$url_cancel='panel_admin/grupos'){
    $form=array();
    $esaldi='';
    if($my_type=='delete_node'){
        $esaldi='Delete node';
    }else if($my_type=='publish_post'){
        $esaldi='Publish';
    }else if($my_type=='unpublish_post'){
        $esaldi='Unpublish';
    }else if($my_type=='activate_channel'){
        $esaldi='Activate';
    }else if($my_type=='deactivate_channel'){
        $esaldi='Deactivate';
    }

    drupal_set_title(t('Are you sure you want to perform '.$esaldi.' on selected rows?'));
    //
    $nid_array_string=my_get_session_default_value($field);
    $form['nid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid_array_string,
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    //
    $form['my_content']['#value']=create_content_confirm_delete_publicas($field);
    //
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),$url_cancel);
    //    
    return $form;
}
function panel_admin_grupos_delete_grupos_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/grupos';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_grupos_publish_post_grupos_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post');
    return $form;
}
function panel_admin_grupos_unpublish_post_grupos_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post');
    return $form;
}
function panel_admin_grupos_publish_post_grupos_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/grupos';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_grupos_unpublish_post_grupos_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/grupos';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_grupos_grupos_access(){
    global $user;
    //intelsat-2015
    //if ($user->roles[CREADOR] || $user->uid == 1) 
    if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR] || $user->uid == 1) {
        return TRUE;
    }
    return FALSE;
}
function panel_admin_grupos_define_accion_publish($r,$destination){
    return panel_admin_items_define_accion_publish($r,$destination);
}
function panel_admin_grupos_add_users_grupos_confirm_form(){
  $user_options=hontza_grupos_mi_grupo_usuarios_de_mis_grupos_options();
  $usuarios_del_grupo=array();
  if(isset($_SESSION['block_panel_admin_grupos_nid_array'])){
    $usuarios_del_grupo=hontza_grupos_mi_grupo_get_usuarios_grupos($_SESSION['block_panel_admin_grupos_nid_array']);
  }
    $form['group_nid_array'] = array('#type' => 'value', '#value' => $_SESSION['block_panel_admin_grupos_nid_array']);  
    $form['selected_grupos_html']=array(
      '#value'=>panel_admin_grupos_selected_grupos_html($_SESSION['block_panel_admin_grupos_nid_array']),
  );    
      $form['og_my_users']=array(
          '#type'=>'select',
          '#title'=>t('Select known users'),
          '#options'=>$user_options,
          '#multiple'=>TRUE,
          '#default_value'=>$usuarios_del_grupo,
          '#size'=>10,
      );
  $form['og_names'] = array(
    '#type' => 'textarea',
    //intelsat-2014  
    //'#title' => t('List of Users'),
    '#title' => t('Select other usernames'),  
    '#rows' => 5,
    '#cols' => 70,
    // No autocomplete b/c user_autocomplete can't handle commas like taxonomy. pls improve core.
    // '#autocomplete_path' => 'user/autocomplete',
    '#description' => t('Add one or more usernames in order to associate users with this group. Multiple usernames should be separated by a comma.'),
    '#element_validate' => array('og_add_users_og_names_validate'),
  );
  $form['add_users_btn'] = array('#type' => 'submit', '#value' => t('Add users'));
  $url_cancel='panel_admin/grupos';
  if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
      $url_cancel=$_REQUEST['destination'];
  }
  $form['my_cancel']['#value']=l(t('Cancel'),$url_cancel);
  return $form;
}
function panel_admin_grupos_add_users_grupos_confirm_form_submit($form,&$form_state){
    panel_admin_grupos_add_users_grupos_og_names($form,$form_state);
    panel_admin_grupos_add_users_grupos_select_known_users($form,$form_state);    
}
function panel_admin_grupos_add_users_grupos_select_known_users($form,&$form_state){
    if(isset($form_state['values']['og_my_users']) && !empty($form_state['values']['og_my_users'])){
        if(isset($form_state['values']['group_nid_array']) && !empty($form_state['values']['group_nid_array'])){
            foreach($form_state['values']['group_nid_array'] as $i=>$grupo_nid){
                $accounts=array();
                foreach($form_state['values']['og_my_users'] as $uid=>$uid_value){
                    $my_user=user_load($uid);                
                    if(isset($my_user->uid) && !empty($my_user->uid)){
                        if(!in_user_groups($grupo_nid,$my_user)){
                            $accounts[]=$my_user;
                            //my_insert_user_grupo($uid,$grupo_nid);                                                                        
                        }
                    }
                }
                hontza_grupos_mi_grupo_og_save_subscription_grupo_nid($accounts,$grupo_nid);
            }
        }    
    }
}
function panel_admin_grupos_add_users_grupos_og_names($form,&$form_state){
  $names = explode(',', $form_state['values']['og_names']);
  $accounts=array();
  foreach ($names as $name) {
    $account = user_load(array('name' => trim($name)));
    if ($account->uid) {
      $accounts[] = $account;
    }
  }
  if(!empty($accounts)){
    if(isset($form_state['values']['group_nid_array']) && !empty($form_state['values']['group_nid_array'])){
        foreach($form_state['values']['group_nid_array'] as $i=>$grupo_nid){
            hontza_grupos_mi_grupo_og_save_subscription_grupo_nid($accounts,$grupo_nid);
        }    
    }
  }
}
function panel_admin_grupos_user_gestion_grupos_add_users_grupos_confirm_form(){
  return panel_admin_grupos_add_users_grupos_confirm_form();  
}
function panel_admin_grupos_is_user_gestion_grupos_propios_add_users_grupos($konp='add_users_grupos_confirm'){
    $param0=arg(0);
    if($param0=='user-gestion'){
        $param1=arg(1);
        if($param1=='grupos'){
            $param2=arg(2);
            if($param2==$konp){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_grupos_user_gestion_grupos_add_users_grupos_confirm_form_submit($form,&$form_state){
    panel_admin_grupos_add_users_grupos_confirm_form_submit($form,$form_state);
}
function panel_admin_grupos_selected_grupos_html($grupo_nid_array){
    $html=array();
    if(!empty($grupo_nid_array)){
        $html[]='<div id="edit-selected-groups" class="form-item form-item-select">';
        $html[]='<label for="edit-selected-groups">'.t('Selected Groups').': </label>';
        $html[]='<ul style="padding-left:20px;">';
        foreach($grupo_nid_array as $i=>$grupo_nid){
            $grupo_node=node_load($grupo_nid);
            if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
                $html[]='<li>'.$grupo_node->title.'</li>';
            }
        }
        $html[]='</ul>';
        $html[]='</div>';
    }
    return implode('',$html);
}
function panel_admin_grupos_remove_users_grupos_confirm_form(){
  $user_options=hontza_grupos_mi_grupo_usuarios_de_mis_grupos_options();
  $usuarios_del_grupo=array();
  /*if(isset($_SESSION['block_panel_admin_grupos_nid_array'])){
    $usuarios_del_grupo=hontza_grupos_mi_grupo_get_usuarios_grupos($_SESSION['block_panel_admin_grupos_nid_array']);
  }*/
    $form['group_nid_array'] = array('#type' => 'value', '#value' => $_SESSION['block_panel_admin_grupos_nid_array']);  
    $form['selected_grupos_html']=array(
      '#value'=>panel_admin_grupos_selected_grupos_html($_SESSION['block_panel_admin_grupos_nid_array']),
  );    
      $form['og_my_users']=array(
          '#type'=>'select',
          '#title'=>t('Select known users'),
          '#options'=>$user_options,
          '#multiple'=>TRUE,
          //'#default_value'=>$usuarios_del_grupo,
          '#size'=>10,
      );
  $form['og_names'] = array(
    '#type' => 'textarea',
    //intelsat-2014  
    //'#title' => t('List of Users'),
    '#title' => t('Select other usernames'),  
    '#rows' => 5,
    '#cols' => 70,
    // No autocomplete b/c user_autocomplete can't handle commas like taxonomy. pls improve core.
    // '#autocomplete_path' => 'user/autocomplete',
    //'#description' => t('Add one or more usernames in order to associate users with this group. Multiple usernames should be separated by a comma.'),
    '#element_validate' => array('og_add_users_og_names_validate'),
  );
  $form['add_users_btn'] = array('#type' => 'submit', '#value' => t('Remove users'));
  $url_cancel='panel_admin/grupos';
  if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
      $url_cancel=$_REQUEST['destination'];
  }
  $form['my_cancel']['#value']=l(t('Cancel'),$url_cancel);
  return $form;
}
function panel_admin_grupos_remove_users_grupos_confirm_form_submit($form,&$form_state){
    panel_admin_grupos_remove_users_grupos_og_names($form,$form_state);
    panel_admin_grupos_remove_users_grupos_select_known_users($form,$form_state);    
}
function panel_admin_grupos_remove_users_grupos_select_known_users($form,&$form_state){
    if(isset($form_state['values']['og_my_users']) && !empty($form_state['values']['og_my_users'])){
        if(isset($form_state['values']['group_nid_array']) && !empty($form_state['values']['group_nid_array'])){
            foreach($form_state['values']['group_nid_array'] as $i=>$grupo_nid){
                foreach($form_state['values']['og_my_users'] as $uid=>$uid_value){
                    $my_user=user_load($uid);                
                    if(isset($my_user->uid) && !empty($my_user->uid)){
                        if(in_user_groups($grupo_nid,$my_user)){
                            og_delete_subscription($grupo_nid,$uid);                                                                   
                        }
                    }
                }                
            }
        }    
    }
}
function panel_admin_grupos_remove_users_grupos_og_names($form,&$form_state){
  $names = explode(',', $form_state['values']['og_names']);
  $accounts=array();
  foreach ($names as $name) {
    $account = user_load(array('name' => trim($name)));
    if ($account->uid) {
      $accounts[] = $account;
    }
  }
  if(!empty($accounts)){
    if(isset($form_state['values']['group_nid_array']) && !empty($form_state['values']['group_nid_array'])){
        foreach($form_state['values']['group_nid_array'] as $i=>$grupo_nid){
            foreach($accounts as $i=>$account){
                og_delete_subscription($grupo_nid,$account->uid);
            }    
        }    
    }
  }
}
function panel_admin_grupos_user_gestion_grupos_remove_users_grupos_confirm_form(){
  return panel_admin_grupos_remove_users_grupos_confirm_form();  
}
function panel_admin_grupos_is_user_gestion_grupos_propios_remove_users_grupos(){
  return panel_admin_grupos_is_user_gestion_grupos_propios_add_users_grupos('remove_users_grupos_confirm');  
}
function panel_admin_grupos_user_gestion_grupos_remove_users_grupos_confirm_form_submit($form,&$form_state){
  panel_admin_grupos_remove_users_grupos_confirm_form_submit($form,$form_state);  
}