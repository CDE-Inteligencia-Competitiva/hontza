<?php
function hontza_fix_hontzafeeds_elemento($item_in){
    $result=$item_in;
    if(!empty($result)){
        foreach($result as $key=>$value){
            $result->$key=(string) $value;
        }
    }
    return $result;
}
function hontza_gestion_grupos_propios_access(){
    global $user;
    if(user_access('root') || hontza_is_creador_de_grupo()){
        return 1;
    }    
    //gemini-2014
    //if(is_administrador_grupo(1)){
    /*if(isset($user->roles[ADMINISTRADOR_DE_GRUPO])){    
        return 1;
    }*/
    return 0;
}
function hontza_gestion_grupos_propios_callback(){
   if(!hontza_gestion_grupos_propios_access()){ 
       drupal_access_denied();
       exit();
   }
    
    
    $my_limit=20;
    
    
    $sort='asc';
    $field='node_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Name')){
            $field='node_title';
        }else if($order==t('Description')){
            $field='og_og_description';
        }else if($order==t('Subject Area')){
            $field='node_data_field_tematica_field_tematica_value';
        }else if($order==t('Type of group')){
            $field='term_data_name';
        }else if($order==t('Users')){
            $field='member_count';
        }           
    }
    
    $sql=hontza_define_gestion_grupos_propios_sql($field,$sort,1);
    
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=array('data'=>t('Owner'),'field'=>'node_uid');
    $headers[2]=array('data'=>t('Editor in chief'),'field'=>'group_administrator_uid');
    $headers[3]=array('data'=>t('Name'),'field'=>'node_title');
    //$headers[2]=array('data'=>t('Description'),'field'=>'og_og_description');
    $headers[4]=array('data'=>t('Subject Area'),'field'=>'node_data_field_tematica_field_tematica_value');    
    //$headers[3]=array('data'=>t('Type of group'),'field'=>'term_data_name');
    $headers[5]=array('data'=>t('Users'),'field'=>'member_count');        
    //$headers[5]=array('data'=>t('Published'),'field'=>'node_status');
    //gemini-2014
    //$headers[8]=array('data'=>t('Group Administrator'),'field'=>'group_administrator_uid');    
    $headers[6]=t('Actions');    
    //$headers[9]='';
    
    
    $rows=array();
    $kont=0;
    $hay_congelado=0;
    //intelsat-2015
    $faktore=50;
    //
    while ($r = db_fetch_object($res)) {
      $my_data=clone $r;
      /*
      //gemini-2014
      if(!is_administrador_grupo(1,$my_data->nid)){
          continue;
      }*/
      $my_data->is_grupo_local=1;
      $rows[$kont]=array();
      $icono_congelado=hontza_get_icono_grupo_congelado($my_data,1,$is_congelado);
      if($is_congelado){
          $hay_congelado=1;
      }
      $icono_red_alerta=hontza_get_icono_grupo_red_alerta($my_data);
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';
      $username=hontza_get_username($r->node_uid);
      //intelsat-2015
      //$rows[$kont][7]=$username;
      $user_image=hontza_grupos_mi_grupo_get_user_img($r->node_uid,$faktore);
      $rows[$kont][1]=$user_image;
      //
      $group_administrator_username=hontza_get_username($r->group_administrator_uid);
      //intelsat-2015
      //$rows[$kont][8]=$group_administrator_username;
      $group_administrator_user_image=hontza_grupos_mi_grupo_get_user_img($r->group_administrator_uid,$faktore);
      $rows[$kont][2]=$group_administrator_user_image;      
      //intelsat-2015
      //$rows[$kont][1]='<div style="white-space:nowrap;">'.$icono_congelado.$icono_red_alerta.$r->node_title.'</div>';
      $icono_privacidad=panel_admin_get_tipo_grupo_icono($r,$icono_red_alerta,0,$title_popup);
      //
      $grupo_title=red_funciones_cortar_grupo_title($r->node_title);
      $grupo_title=l($grupo_title,'node/'.$r->nid,array('attributes'=>array('title'=>$r->og_og_description)));
      //$rows[$kont][3]='<div style="white-space:nowrap;">'.$icono_congelado.$icono_privacidad.$icono_red_alerta.$grupo_title.'</div>';      
      $rows[$kont][3]='<div style="white-space:nowrap;">'.$icono_congelado.$icono_privacidad.$grupo_title.'</div>';            
      //
      //$rows[$kont][2]=$r->og_og_description;
      $rows[$kont][4]=$r->node_data_field_tematica_field_tematica_value;
      $term_name=taxonomy_get_term_name_by_language($r->term_data_tid,$r->term_data_name);
      $term_data_name=$term_name; 
      //intelsat-2015
      //$rows[$kont][3]=$term_data_name;
      //$rows[$kont][3]=$icono_privacidad;
      //
      $rows[$kont][5]=l($r->member_count,'og/users/'.$r->nid.'/faces');
      //$node_status_string=hontza_get_active_string($r->node_status);
      //$rows[$kont][5]=$node_status_string;
      //
      $rows[$kont][6]=array('data'=>hontza_gestion_grupos_propios_acciones($r->nid,$r),'style'=>'white-space:nowrap;');
      $rows[$kont]['term_data_name_lang']=$term_data_name;
      $rows[$kont]['node_status_string']=$node_status_string;
      $rows[$kont]['username']=$username;
      $rows[$kont]['group_administrator_username']=$group_administrator_username;
      $rows[$kont]['is_congelado']=$is_congelado;
      $rows[$kont]['title_temp']='<div style="white-space:nowrap;">'.$rows[$kont][3].'</div>';
      $kont++;
    }
    //
    if($order==t('Type of group')){
        $rows=array_ordenatu($rows,'term_data_name_lang', $sort,0);        
    }else if($order==t('Published')){
        $rows=array_ordenatu($rows,'node_status_string', $sort,0);        
    //gemini-2014
    //}else if($order==t('Creator')){
    }else if($order==t('Group Owner')){    
        $rows=array_ordenatu($rows,'username', $sort,0);        
    }else if($order==t(t('Group Administrator'))){
        $rows=array_ordenatu($rows,'group_administrator_username', $sort,0);        
    }
    //
    $unset_array=array('term_data_name_lang','node_status_string','username','group_administrator_username');
    if($hay_congelado){
        $unset_array=array_merge($unset_array,array('is_congelado','title_temp'));
    }
    //
    $rows=hontza_unset_array($rows,$unset_array);
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if(!$hay_congelado){
        $rows=hontza_set_icono_congelado_by_pagina($rows);
    }    
    
 if (count($rows)>0) {
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
    return hontza_define_gestion_grupos_propios_header().drupal_get_form('hontza_gestion_grupos_propios_bulk_form',array($output));
}
function hontza_is_og_users_add_user(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='og'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='users'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                $param3=arg(3);
                if(!empty($param3) && $param3=='add_user'){
                    return 1;
                }
            }
        }
    }
}
function hontza_define_gestion_grupos_propios_sql($field,$sort,$is_admin_grupo_rol=0){
    global $user;
        $where_user="users.uid=".$user->uid;
        if($user->uid==1){
        //if($user->uid==1 || $is_admin_grupo_rol){
            $where_user="1";
            $creador_de_grupo_uid=hontza_get_gestion_grupos_propios_filter_value('creador_de_grupo_uid');
            if(!empty($creador_de_grupo_uid)){
                $where_user="users.uid=".$creador_de_grupo_uid;
            }
        }
        //$order_by=hontza_gestion_grupos_propios_order_by();
        $order_by=' ORDER BY '.$field.' '.strtoupper($sort);
        $sql="SELECT node.nid AS nid,
        node.title AS node_title,
        node.language AS node_language,
        og.og_description AS og_og_description,
        node_data_field_tematica.field_tematica_value AS node_data_field_tematica_field_tematica_value,
        node.type AS node_type,
        node.vid AS node_vid,
        term_data.name AS term_data_name,
        term_data.vid AS term_data_vid,
        term_data.tid AS term_data_tid,
        (SELECT COUNT(*) 
        FROM {og_uid} ou INNER JOIN {users} u ON ou.uid = u.uid 
        WHERE ou.nid = og.nid AND u.status > 0 
        AND ou.is_active >= 1 AND ou.is_admin >= 0 ) AS member_count, 
        node.status AS node_status, 
        node.uid AS node_uid, 
        node_revisions.format AS node_revisions_format,
        node_data_field_tematica.field_admin_grupo_uid_value AS group_administrator_uid 
        FROM {node} node INNER JOIN {users} users ON node.uid = users.uid 
        LEFT JOIN {og} og ON node.nid = og.nid 
        LEFT JOIN {content_type_grupo} node_data_field_tematica ON node.vid = node_data_field_tematica.vid 
        LEFT JOIN {term_node} term_node ON node.vid = term_node.vid 
        LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid 
        LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
        WHERE (node.type in ('grupo')) AND ".$where_user.$order_by;
       
    return $sql;    
}
function hontza_gestion_grupos_propios_acciones($nid,$r){
    global $base_url;
    $html=array();
    $destination='destination=user-gestion/grupos/propios';
    $html[]='<div class="div_gestion_grupos_actions">';
    $html[]=l(my_get_icono_action('edit', t('Edit')),'node/'.$nid.'/edit',array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Edit'),'alt'=>t('Edit'))));
    $html[]=l(my_get_icono_action('delete', t('Delete')),'node/'.$nid.'/delete',array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Delete'),'alt'=>t('Delete'))));     
    /*if(estrategia_user_access_importar_strategia($nid)){
        //$html[]=l(my_get_icono_action('import_strategy', t('Import Strategy')),'estrategia/'.$nid.'/importar_estrategia',array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Import Strategy'),'alt'=>t('Import Strategy'))));         
        $grupo=node_load($nid);
        if(isset($grupo->nid) && !empty($grupo->nid)){    
            $url_importar_estrategia=$base_url.'/'.$grupo->purl.'/estrategia/importar_estrategia';        
            $html[]=l(my_get_icono_action('import_strategy', t('Import Strategy')),$url_importar_estrategia,array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Import Strategy'),'alt'=>t('Import Strategy'))));                     
        }                
    }*/
    //gemini-2014
    $html[]=l(my_get_icono_action('add', t('Add users to groups')),'og/users/'.$nid.'/add_user',array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Add users to groups'),'alt'=>t('Add users to groups'))));
    $html[]=l(my_get_icono_action('edit_remove', t('Remove users to groups')),'user-gestion/borrar_usuario_grupo/'.$nid,array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Remove users to groups'),'alt'=>t('Remove users to groups'))));    
    //
    if(hontza_is_sareko_id_red()){
        if(hontza_is_red_hoja()){
            //intelsat-2016
            if(!hontza_grupo_mi_grupo_is_grupo_publico_colaborativo($nid)){
                //gemini-2014
                if(red_compartir_grupo_is_grupo_red_alerta($nid)){
                    $url_no_network='red_compartir/no_compartir_grupo_hoja/'.$nid;
                    $html[]=l(my_get_icono_action('no_network', t('Disconnect from Network')),$url_no_network,array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Disconnect from Network'),'alt'=>t('Disconnect from Network'))));
                }else{
                    //intelsat-2016
                    //$url_sign_network='red_compartir/compartir_grupo_hoja/'.$nid;
                    $url_sign_network=hontza_registrar_get_url_sign_network($nid);
                    /*if(red_compartir_grupo_is_grupo_con_datos_rellenados($nid)){
                        $url_sign_network='node/'.$nid.'/edit';
                    }*/
                    $html[]=l(my_get_icono_action('network', t('Connect to Network')),$url_sign_network,array('html'=>TRUE,'query'=>$destination),array('attributes'=>array('title'=>t('Connect to Network'),'alt'=>t('Connect to Network'))));
                }
            }    
        }      
    }
    //intelsat-2015
    $html[]=panel_admin_ayuda_define_accion_publish($r,$destination);    
    //
    //intelsat-2015
    if(hontza_canal_rss_is_usuario_basico_activado()){
        $html[]=usuario_basico_panel_admin_grupos_get_link_inicio($r,$destination);
    }
    $html[]='</div>';
    return implode('&nbsp;',$html);
}
function hontza_define_gestion_grupos_propios_header(){
    //intelsat-2015
    return '';
    //
    $html=array();
    //$html[]='<div class="view-header">';
    $html[]=l(t('Create new Group'),'node/add/grupo',array('query'=>'destination=user-gestion/grupos/propios','attributes'=>array('class'=>'add')));
    //$html[]=l(t('Add users to groups'),'user-gestion/usuarios-grupos',array('query'=>'destination=user-gestion/grupos/propios','attributes'=>array('class'=>'add')));
    //$html[]=l(t('Remove users to groups'),'user-gestion/borrar-usuarios-grupos',array('query'=>'destination=user-gestion/grupos/propios','attributes'=>array('class'=>'my_remove')));
    //gemini-2014
    //AVISO::::se ha comentado 'Active channels'
    /*if(is_super_admin()){
        $html[]=l(t('Active channels'),'activar_actualizacion_subdominio',array('query'=>'destination=user-gestion/grupos/propios'));        
    }*/
    $html[]=hontza_gestion_grupos_propios_filtro();
    //$html[]='<div>';
    
    return implode('<BR>',$html);
}
function hontza_gestion_grupos_propios_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    /*$form['my_bulk_operations_fs']['delete_node_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete node'),
      '#name'=>'delete_node_btn',
    ); 
    $form['my_bulk_operations_fs']['publish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Publish'),
      '#name'=>'publish_post_btn',
    );       
    $form['my_bulk_operations_fs']['unpublish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Unpublish'),
      '#name'=>'unpublish_post_btn',
    );*/
    panel_admin_items_add_bulk_operations_form_fields($form,1,$delete_btn_name='delete_node_btn',1);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function hontza_gestion_grupos_propios_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('publish_post_btn','unpublish_post_btn','delete_node_btn','add_users_btn','remove_users_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            //intelsat-2015
            if(strcmp($button_name,'add_users_btn')==0){
                $_SESSION['block_panel_admin_grupos_nid_array']=$nid_array;
                drupal_goto('user-gestion/grupos/add_users_grupos_confirm',drupal_get_destination());
            }else if(strcmp($button_name,'remove_users_btn')==0){
                $_SESSION['block_panel_admin_grupos_nid_array']=$nid_array;
                drupal_goto('user-gestion/grupos/remove_users_grupos_confirm',drupal_get_destination());
            //    
            }else if(strcmp($button_name,'publish_post_btn')==0){
                $_SESSION['publish_grupo_nid_array']=$nid_array;                
                drupal_goto('user-gestion/grupos/hontza_publish_grupo_confirm');
            }else if(strcmp($button_name,'delete_node_btn')==0){                
                $_SESSION['delete_grupo_nid_array']=$nid_array;
                drupal_goto('user-gestion/grupos/hontza_delete_grupo_confirm');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                $_SESSION['unpublish_grupo_nid_array']=$nid_array;
                drupal_goto('user-gestion/grupos/hontza_unpublish_grupo_confirm');
            }
        }
    }
}
function hontza_publish_grupo_confirm_form(){
    $form=array();
    $nid_array=array();
    if(isset($_SESSION['publish_grupo_nid_array'])){
        $nid_array=$_SESSION['publish_grupo_nid_array'];
    }
    $form['nid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('publish_grupo_nid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_publish_grupo_confirm_content($nid_array);
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'user-gestion/grupos/propios');
    
    return $form;
}
function hontza_publish_grupo_confirm_content($nid_array){
    //
    $html=array();
    $html[]='<div class="item-list">';
    //intelsat-2015
    $html[]='<h3>'.hontza_canal_rss_get_selected_rows_message(count($nid_array)).':</h3>';
    //
    $html[]='<ul>';
    if(count($nid_array)>0){
        foreach($nid_array as $i=>$nid){
            $node=node_load($nid);
            $html[]='<li>'.$node->title.'</li>';
        }
    }
    $html[]='</ul>';
    $html[]='</div>';
    return implode('',$html);
}
function hontza_publish_grupo_confirm_form_submit($form, &$form_state) {
    hontza_update_publish_grupo($form,$form_state,1,'publish_grupo');
}
function hontza_unpublish_grupo_confirm_form_submit($form, &$form_state) {
    hontza_update_publish_grupo($form,$form_state,0,'unpublish_grupo');
}
function hontza_update_publish_grupo($form,&$form_state,$status,$type){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if($button_name=='my_confirm_btn'){
        $nid_string=$form_state['values']['nid_array'];
        if(!empty($nid_string)){
            $nid_array=explode(',',$nid_string);
            foreach($nid_array as $i=>$nid){
                if($type=='delete_grupo'){
                    node_delete($nid);
                }else{
                    update_node_status($nid,$status);
                }    
            }
        }    
    }
    //intelsat-2016
    hontza_grupos_mi_grupo_bulk_actions_drupal_goto($type);     
}
function hontza_unpublish_grupo_confirm_form(){
    $form=array();
    $nid_array=array();
    if(isset($_SESSION['unpublish_grupo_nid_array'])){
        $nid_array=$_SESSION['unpublish_grupo_nid_array'];
    }
    $form['nid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('unpublish_grupo_nid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_publish_grupo_confirm_content($nid_array);
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'user-gestion/grupos/propios');
    
    return $form;
}
function hontza_delete_grupo_confirm_form(){
    $form=array();
    $nid_array=array();
    if(isset($_SESSION['delete_grupo_nid_array'])){
        $nid_array=$_SESSION['delete_grupo_nid_array'];
    }
    $form['nid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('delete_grupo_nid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_publish_grupo_confirm_content($nid_array);
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'user-gestion/grupos/propios');
    
    return $form;
}
function hontza_delete_grupo_confirm_form_submit($form, &$form_state) {
    hontza_update_publish_grupo($form,$form_state,0,'delete_grupo');
}
function hontza_unset_array($rows,$my_array){
    $result=array();
    if(!empty($rows)){
        foreach($rows as $i=>$r){
            foreach($r as $key=>$value){
                if(is_numeric($key) || !in_array($key,$my_array)){
                   $result[$i][$key]=$value; 
                }
            }
        }
    }
    return $result;
}
function hontza_og_vigilancia_validados_pre_execute(&$view){        
   $my_grupo=og_get_group_context();
   if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){ 
        $where=array();
        $where[]="1";
        $where[]="node.status <> 0";
        $where[]="og_ancestry.group_nid = ".$my_grupo->nid;;
        $where[]="node.type in ('item', 'noticia')";
        //$where[]="flag_content_node.fid=2";
        $where[]=hontza_get_vigilancia_where_filter();
        //
        //$order_by="node_comment_statistics_comment_count DESC, flag_content_node_timestamp DESC";
        $order_by="node_created DESC";
        $sql="SELECT DISTINCT(node.nid) AS nid,
        node_comment_statistics.comment_count AS node_comment_statistics_comment_count,
        flag_content_node.timestamp AS flag_content_node_timestamp,
        node.created AS node_created 
        FROM {node} node 
        INNER JOIN {flag_content} flag_content_node ON node.nid = flag_content_node.content_id AND flag_content_node.fid = 2 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid 
        WHERE ".implode(" AND ",$where)." 
        GROUP BY nid 
        ORDER BY ".$order_by;
        /*$sql="SELECT DISTINCT(node.nid) AS nid,
        flag_content_node.timestamp AS flag_content_node_timestamp,
        node.created AS node_created 
        FROM {node} node 
        LEFT JOIN {flag_content} flag_content_node ON node.nid = flag_content_node.content_id
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        WHERE ".implode(" AND ",$where)." 
        GROUP BY nid 
        ORDER BY ".$order_by;*/
        //print $sql;
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;
   }  
}
function hontza_gestion_usuarios_filtro(){
    my_add_buscar_js();
    return drupal_get_form('hontza_gestion_usuarios_filtro_form');    
}
function hontza_gestion_usuarios_filtro_form(){
    $form=array();
    //intelsat-2015
    $fs_title=t('Search');
    if(!panel_admin_is_gestion_usuarios_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    //
    $form['file_buscar_fs']['users_name']=
        array('#type'=>'textfield',
        '#title'=>t('Username'),
        "#default_value"=>hontza_get_gestion_usuarios_filter_value('users_name'));
    $form['file_buscar_fs']['mail']=
        array('#type'=>'textfield',
        '#title'=>t('E-mail address'),
        "#default_value"=>hontza_get_gestion_usuarios_filter_value('mail'));
    $form['file_buscar_fs']['profile_values_profile_nombre_value']=
        array('#type'=>'textfield',
        '#title'=>t('Name'),
        "#default_value"=>hontza_get_gestion_usuarios_filter_value('profile_values_profile_nombre_value'));
    $form['file_buscar_fs']['profile_values_profile_apellidos_value']=
        array('#type'=>'textfield',
        '#title'=>t('Surname'),
        "#default_value"=>hontza_get_gestion_usuarios_filter_value('profile_values_profile_apellidos_value'));
    $form['file_buscar_fs']['profile_values_profile_empresa_value']=
        array('#type'=>'textfield',
        '#title'=>t('Organisation'),
        "#default_value"=>hontza_get_gestion_usuarios_filter_value('profile_values_profile_empresa_value'));
    //intelsat-2016
    $form['file_buscar_fs']['grupo_nid']=array(
            '#type'=>'select',
            '#title'=>t('Filter by group'),
            '#options'=>panel_admin_items_define_filtro_grupo_options(),
            '#multiple'=>TRUE,
            '#attributes'=>contenidos_get_grupo_multiple_attributes(),
            '#default_value'=>hontza_get_gestion_usuarios_filter_value('grupo_nid'),
    );
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar');
    return $form;
}
function hontza_get_gestion_usuarios_filter_value($f,$key='gestion_usuarios'){
    if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
        if(isset($_SESSION[$key]['filter'][$f]) && !empty($_SESSION[$key]['filter'][$f])){
            return $_SESSION[$key]['filter'][$f];
        }
    }
    return '';
}
function hontza_gestion_usuarios_filtro_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $name=$form['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['gestion_usuarios']['filter']) && !empty($_SESSION['gestion_usuarios']['filter'])){
                unset($_SESSION['gestion_usuarios']['filter']);
            }
        }else{
            $_SESSION['gestion_usuarios']['filter']=array();
            $fields=hontza_define_gestion_usuarios_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form['values'][$f];
                    if(!empty($v)){
                        $_SESSION['gestion_usuarios']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function hontza_define_gestion_usuarios_filter_fields(){
    $filter_fields=array('users_name','profile_values_profile_nombre_value','profile_values_profile_apellidos_value','profile_values_profile_empresa_value');
    //intelsat-2016
    $filter_fields[]='grupo_nid';
    $filter_fields[]='mail';
    return $filter_fields;
}
function hontza_is_batch(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='batch'){
        return 1;
    }
    return 0;
}
function hontza_canales_add_popup_en_indices($v,$nid){
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        $pos=strpos($v,'"');
        $a_title=strip_tags($node->body);
        $s=substr($v,0,$pos+1);
        $end=substr($v,$pos+1);
        $result=$s.' title="'.$a_title.'"'.$end;
        return $result;            
    }
    return $v;
}
function hontza_fuentes_pipes_add_popup_en_indices($vars){
    $sep=hontza_define_fuentes_pipes_sep($vars['rows']);
    if(!empty($sep)){
        $my_array=explode($sep,$vars['rows']);
        if(!empty($my_array)){
            foreach($my_array as $i=>$v){
                if($i>0){
                    if(isset($vars['view']->result[$i-1])){
                        $nid=$vars['view']->result[$i-1]->nid;
                        $pos=strpos($v,'</td>');
                        if($pos===FALSE){
                            $glue_array[]=$v;
                        }else{
                            $fuente=node_load($nid);                        
                            $glue_array[]=l($vars['view']->result[$i-1]->node_title,'node/'.$nid,array('attributes'=>array('title'=>$fuente->body))).substr($v,$pos);
                        }
                    }else{
                        $glue_array[]=$v;
                    }
                }else{
                    $glue_array[]=$v;
                }
            }
            return implode($sep,$glue_array);
        }
    }    
    return $vars['rows'];
}
function hontza_define_fuentes_pipes_sep($rows_in){
    $sep='<td class="views-field views-field-title">';
    $pos=strpos($rows_in,$sep);
    if($pos===FALSE){
        $sep='<td class="views-field views-field-title active">';
        $pos_active=strpos($rows_in,$sep);
        if($pos_active===FALSE){            
            return '';
        }  
    }
    return $sep;
}
function hontza_parametros_del_canal($node,$is_form=0){
    if($node->type=='canal_de_supercanal'){
        $canal_params=hontza_get_canal_params($node);
        if(!empty($canal_params)){
            $html=array();
            $html[]='<div class="div_parametros_del_canal">';
            $html[]='<fieldset>';
            $html[]='<legend>'.t('Value of Parameters').'</legend>';
            $html[]='<div class="fieldset-wrapper">';
            foreach($canal_params as $i=>$params){
                $html[]='<div id="edit-'.$params['name'].'-wrapper" class="form-item form-item-textfield">';
                /*$html[]='<label for="edit-'.$params['name'].'">'.t('Value of @val Parameter', array('@val' =>$params['name'])).'</label>';
                $html[]='<input type="text" class="form-text" value="'.$params['value'].'" size="60" id="edit-'.$params['name'].'" name="'.$params['name'].'" maxlength="255" readonly="readonly">';
                $html[]='<div class="description">'.$params['description'].'</div>';*/
                $readonly=' readonly="readonly"';
                if($is_form){
                    $readonly='';
                }
                $decode_value=htmlentities(urldecode($params['value']));
                //$decode_value=$params['value'];
                $description=trim($params['description']);
                if(empty($description)){
                    $description=$params['name'];
                }
                $html[]='<label for="edit-'.$params['name'].'">'.$description.'</label>';
                $html[]='<input type="text" class="form-text" value="'.$decode_value.'" size="60" id="edit-'.$params['name'].'" name="filtro_parametros['.$params['name'].']" maxlength="255"'.$readonly.'>';
                $html[]='</div>';
            }
            $html[]='</div>';
            $html[]='</fieldset>';
            $html[]='</div>';
            return implode('',$html);        
        }    
    }else if($node->type=='canal_de_yql'){
        return hontza_parametros_del_canal_yql($node);
    }
    return '';
}
function hontza_get_feeds_source($nid){
    $res=db_query('SELECT fs.* FROM {feeds_source} fs WHERE feed_nid=%d',$nid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=new stdClass();
    return $result;
}
function hontza_get_canal_params($node){
    $result=array();
    $feeds_source=hontza_get_feeds_source($node->nid);
    if(isset($feeds_source->source) && !empty($feeds_source->source)){
        $source=$feeds_source->source;
        $my_array=explode('&',$source);
        //
        $fuente=hontza_get_fuente($node);
        if(isset($fuente->nid) && !empty($fuente->nid)){
            if($fuente->type=='supercanal'){
                if(isset($fuente->field_supercanal_args) && !empty($fuente->field_supercanal_args)){
                    foreach($fuente->field_supercanal_args as $i=>$args){
                        if(!empty($args)){
                            foreach($args as $k=>$v){
                                $result[$i]=array();
                                $result[$i]['name']=$v;
                                $result[$i]['description']=$fuente->field_supercanal_args_desc[$i][$k];
                                $result[$i]['value']=hontza_get_canal_params_value($my_array,$v,$fuente->type);
                            }
                        }
                    }
                }
            }else if($fuente->type=='fuentedapper'){
                 if(isset($fuente->field_fuentedapper_args) && !empty($fuente->field_fuentedapper_args)){
                    foreach($fuente->field_fuentedapper_args as $i=>$args){
                        if(!empty($args)){
                            foreach($args as $k=>$v){
                                $result[$i]=array();
                                $result[$i]['name']=$v;
                                $result[$i]['description']=$fuente->field_fuentedapper_args_desc[$i][$k];
                                $result[$i]['value']=hontza_get_canal_params_value($my_array,$v,$fuente->type);
                            }
                        }
                    }
                }
            }    
        }
    }
    return $result;
}
function hontza_get_fuente($canal){
    $fuente=get_fuente_by_canal($canal->nid);
    if(isset($fuente->nid) && !empty($fuente->nid)){
        return $fuente;
    }
    if(isset($canal->field_nombrefuente_canal) && isset($canal->field_nombrefuente_canal[0]) && isset($canal->field_nombrefuente_canal[0]['value']) && !empty($canal->field_nombrefuente_canal[0]['value'])){
        $fuente=hontza_get_fuente_by_title($canal->field_nombrefuente_canal[0]['value']);
        if(isset($fuente->nid) && !empty($fuente->nid)){
            return $fuente;
        }
    }
    //
    $result=new stdClass();
    return $result;
}
function hontza_get_canal_params_value($my_array,$konp_in,$fuente_type){
    $konp=$konp_in;
    if($fuente_type=='fuentedapper'){
        $konp='v_'.$konp;
    }
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            $pos=strpos($value,'=');
            if($pos===FALSE){
                //
            }else{
                $s=substr($value,0,$pos);
                $v=substr($value,$pos+1);
                if($s==$konp){
                    return $v;
                }
            }
        }
    }
    return '';
}
function hontza_og_access($nid){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $node=node_load($nid);
        if(isset($node->og_groups) && !empty($node->og_groups)){
            $group_nid_array=array_values($node->og_groups);
            if(!in_array($my_grupo->nid,$group_nid_array)){
                drupal_access_denied();
            }
        }else{
            drupal_access_denied();
        }
    }else{
        drupal_access_denied();
    }    
}
function hontza_get_grupo_purl_by_node($node){
    if(isset($node->og_groups) && !empty($node->og_groups)){
        $grupo_nid_array=array_values($node->og_groups);
        if(isset($grupo_nid_array[0]) && !empty($grupo_nid_array[0])){
            $grupo=node_load($grupo_nid_array[0]);
            if(isset($grupo->purl) && !empty($grupo->purl)){
                return $grupo->purl;
            }
        }
    }
    return '';
}
function hontza_get_canal_yql_parametros_row($vid,$nid){
    $res=db_query('SELECT * FROM {canal_yql_parametros} WHERE vid=%d AND nid=%d',$vid,$nid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $result=new stdClass();
    return $result;
}
function hontza_delete_canal_yql_parametros($canal){
    db_query('DELETE FROM {canal_yql_parametros} WHERE vid=%d AND nid=%d',$canal->vid,$canal->nid);
}
function hontza_parametros_del_canal_yql($node){
        if($node->type=='canal_de_yql'){
            $canal_params=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            if(!empty($canal_params)){
                $html=array();
                $html[]='<div class="div_parametros_del_canal">';
                $html[]='<fieldset>';
                $html[]='<legend>'.t('Value of Parameters').'</legend>';
                $html[]='<div class="fieldset-wrapper">';
                if(hontza_is_canal_params_filtro1($canal_params)){
                    $html[]=hontza_get_canal_params_filtro1_html($canal_params);            
                }else if(hontza_is_canal_params_filtro2($canal_params)){
                    $html[]=hontza_get_canal_params_filtro2_html($canal_params);
                }else if(hontza_is_canal_params_filtro3($canal_params)){
                    $html[]=hontza_get_canal_params_filtro3_html($canal_params);
                }else if(hontza_is_canal_params_filtro4($canal_params)){
                    $html[]=hontza_get_canal_params_filtro4_html($canal_params);
                }else if(hontza_is_canal_params_filtro5($canal_params)){
                    $html[]=hontza_get_canal_params_filtro5_html($canal_params);
                }else{
                    return '';
                }
                $html[]='</div>';
                $html[]='</fieldset>';
                $html[]='</div>';
                return implode('',$html);        
            }   
        }
    return '';
}
function hontza_is_canal_params_filtro1($canal_params){
    if(!empty($canal_params)){
        if(!empty($canal_params->todos) && !empty($canal_params->todos)){
            return 1;
        }
    }
    return 0;
}
function hontza_is_canal_params_filtro2($canal_params){
    if(!empty($canal_params)){
        if(!empty($canal_params->titulo) && !empty($canal_params->descripcion)){
            return 1;
        }
    }
    return 0;
}
function hontza_is_canal_params_filtro3($canal_params){
    if(!empty($canal_params)){
        if(!empty($canal_params->no_titulo) && !empty($canal_params->no_descripcion)){
            return 1;
        }
    }
    return 0;
}
function hontza_is_canal_params_filtro4($canal_params){
    if(!empty($canal_params)){
        if(!empty($canal_params->contiene) && !empty($canal_params->no_contiene)){
            return 1;
        }
    }
    return 0;
}
function hontza_is_canal_params_filtro5($canal_params){
    if(!empty($canal_params)){
        if(!empty($canal_params->area) && !empty($canal_params->area)){
            return 1;
        }
    }
    return 0;
}
function hontza_get_canal_params_filtro1_html($canal_params){
    $html=array();
    $html[]='<fieldset id="my_filtros1">';
    $html[]='<legend>'.t('Apply filter 1 to RSS feeds').'</legend>';
    $html[]='<div class="fieldset-wrapper">';
    $html[]='<div id="edit-todos-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-todos">'.t('General Search').': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->todos.'" size="20" id="edit-todos" name="todos" maxlength="128" readonly="readonly">';
    $html[]='</div>';
    $html[]='</div>';
    $html[]='</fieldset>';
    return implode('',$html);    
}
function hontza_get_canal_params_filtro2_html($canal_params){
    $html=array();
    //
    $or_checked=' checked="checked"';
    $and_checked='';
    $yql_op='OR';
    if(!empty($canal_params->filtrosSI)){
        $or_checked='';
        $and_checked=' checked="checked"';
        $yql_op='AND';
    }
    //
    $html[]='<fieldset id="my_filtros2">';
    $html[]='<legend>'.t('Apply filter 2 to RSS feeds').'</legend>';
    $html[]='<div class="fieldset-wrapper">';
    $html[]='<div id="edit-titulo-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-titulo">'.t('Contains this word in the title').': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->titulo.'" size="20" id="edit-titulo" name="titulo" maxlength="128" readonly="readonly">';
    $html[]='</div>';    
    $html[]='<div class="form-radios">';
    /*$html[]='<div id="edit-cbox1-0-wrapper" class="form-item form-item-radio">';
    $html[]='<label for="edit-cbox1-0" class="option">';
    $html[]='<input type="radio" class="form-radio" value="0" name="cbox1" id="edit-cbox1-0"'.$or_checked.'> '.t('OR').'</label>';
    $html[]='</div>';
    $html[]='<div id="edit-cbox1-1-wrapper" class="form-item form-item-radio">';
    $html[]='<label for="edit-cbox1-1" class="option"><input type="radio" class="form-radio" value="1" name="cbox1" id="edit-cbox1-1"'.$and_checked.'> '.t('AND').'</label>';
    $html[]='</div>';*/
    $html[]='<input type="text" class="form-text" value="'.$yql_op.'" id="my_yql_op" name="my_yql_op" maxlength="10" readonly="readonly" style="width:40px;">';    
    $html[]='</div>';
    $html[]='<div id="edit-descripcion-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-descripcion">'.t('Contains this word in the description').': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->descripcion.'" size="20" id="edit-descripcion" name="descripcion" maxlength="128" readonly="readonly">';
    $html[]='</div>';
    $html[]='</div>'; 
    $html[]='</fieldset>';
    return implode('',$html);
}
function hontza_goto_vigilancia_default(){
    /*$url='vigilancia/validados';
    if(hontza_is_show_vigilancia_pendientes_tab()){*/
        $url='vigilancia/pendientes';
    //}
    drupal_goto($url);
}
function hontza_get_canal_params_filtro3_html($canal_params){
    $html=array();
    //
    $yql_op='OR';
    if(!empty($canal_params->filtrosNO)){
        $yql_op='AND';
    }
    //
    $html[]='<fieldset id="my_filtros3">';
    $html[]='<legend>'.t('Apply filter 3 to RSS feeds').'</legend>';
    $html[]='<div class="fieldset-wrapper">';
    $html[]='<div id="edit-titulo-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-titulo">'.t('It does not contain this word in the title').': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->no_titulo.'" size="20" id="edit-titulo" name="titulo" maxlength="128" readonly="readonly">';
    $html[]='</div>';    
    $html[]='<div class="form-radios">';
    $html[]='<input type="text" class="form-text" value="'.$yql_op.'" id="my_yql_op" name="my_yql_op" maxlength="10" readonly="readonly" style="width:40px;">';    
    $html[]='</div>';
    $html[]='<div id="edit-descripcion-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-descripcion">'.t('It does not contain this word in the description').': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->no_descripcion.'" size="20" id="edit-descripcion" name="descripcion" maxlength="128" readonly="readonly">';
    $html[]='</div>';
    $html[]='</div>'; 
    $html[]='</fieldset>';
    return implode('',$html);
}
function hontza_get_canal_params_filtro4_html($canal_params){
    $html=array();
    $contiene_field='title';
    if(!empty($canal_params->campo_contiene)){
        $contiene_field='description';
    }
    //
    $no_contiene_field='title';
    if(!empty($canal_params->campo_no_contiene)){
        $no_contiene_field='description';
    }
    //
    $yql_op='OR';
    if(!empty($canal_params->conjuncion)){
        $yql_op='AND';
    }
    //
    $html[]='<fieldset id="my_filtros4">';
    $html[]='<legend>'.t('Apply filter 4 to RSS feeds').'</legend>';        
    $html[]='<div class="fieldset-wrapper">';
    $html[]='<div id="edit-contiene-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-contiene">'.t('Contains this word in the '.$contiene_field).': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->contiene.'" size="20" id="edit-contiene" name="contiene" maxlength="128" readonly="readonly">';
    $html[]='</div>';
    $html[]='<div class="form-radios">';
    $html[]='<input type="text" class="form-text" value="'.$yql_op.'" id="my_yql_op" name="my_yql_op" maxlength="10" readonly="readonly" style="width:40px;">';    
    $html[]='</div>';
    $html[]='<div id="edit-no-contiene-wrapper" class="form-item form-item-textfield">';
    $html[]='<label for="edit-contiene">'.t('It does not contain this word in the '.$no_contiene_field).': </label>';
    $html[]='<input type="text" class="form-text" value="'.$canal_params->no_contiene.'" size="20" id="edit-contiene" name="no_contiene" maxlength="128" readonly="readonly">';
    $html[]='</div>';
    $html[]='</div>';
    $html[]='</fieldset>';
    return implode('',$html);
}
function hontza_get_canal_params_filtro5_html($canal_params){
    $html=array();
    $html[]='<fieldset id="my_filtros5">';
    $html[]='<legend>'.t('Apply filter 5 to RSS feeds').'</legend>';
    $html[]='<div class="fieldset-wrapper">';
    $html[]='<label for="edit-area">'.t('Code to create the filter manually').': </label>';
    $html[]='<textarea name="area" rows="5" cols="50" readonly="readonly">'.$canal_params->area.'</textarea>';
    $html[]='</div>';
    $html[]='</fieldset>';
    return implode('',$html);        
}
function hontza_og_canales_pre_execute(&$view){
   //intelsat-2015
   if(hontza_canal_rss_is_canal_con_canal_sin_resultados()){
    hontza_canal_rss_og_canales_pre_execute($view);  
   }else{
    global $user;
    //gemini-2014
    if(!red_funciones_is_show_shared_block_menu_left()){
        hontza_empty_view_pre_execute($view);
        return 1;
    }
    //
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){ 
         $where=array();
         $where[]="1";
         $where[]="node.status <> 0";
         $where[]="og_ancestry.group_nid = ".$my_grupo->nid;
         $where[]="node.type in ('item')";
         //$num_records="COUNT(node.nid) AS num_records";
         $num_records="SUM(IF(flag_content_node.uid IS NULL,1,0)) AS num_records";
         if(hontza_is_mis_canales_block()){
             $where[]='(node_data_field_responsable_uid.field_responsable_uid_uid = '.$user->uid.' OR node_data_field_responsable_uid2.field_responsable_uid2_uid = '.$user->uid.')';
             //$where[]='(flag_content_node.uid IS NULL)';
             //$num_records="SUM(IF(flag_content_node.uid IS NULL,1,0)) AS num_records";            
         }
         $left_join_content_field_canal_active_refresh='';
         if(hontza_is_congelar_canal_sareko_id()){
             if(hontza_canal_comodin_is_canal_comodin_activado()){
                 $left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_canal_active_refresh.nid ';
                 $left_join_content_field_canal_active_refresh.=' LEFT JOIN {content_field_is_canal_comodin} content_field_is_canal_comodin ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_is_canal_comodin.nid ';
                 $where[]="(content_field_canal_active_refresh.field_canal_active_refresh_value=1 OR content_field_is_canal_comodin.field_is_canal_comodin_value=1)";                
             }else{
                 $left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_canal_active_refresh.nid ';
                 $where[]="content_field_canal_active_refresh.field_canal_active_refresh_value=1";
             }
         }
         $left_join_categoria='';
         $categoria=red_funciones_get_filtro_por_categoria();
         if(!empty($categoria)){
             $left_join_categoria=" LEFT JOIN {term_node} term_node ON node_node_data_field_item_canal_reference.nid=term_node.nid ";
             $where[]="term_node.tid=".$categoria;
         }
         //
         $sql="SELECT node_data_field_item_canal_reference.
         field_item_canal_reference_nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid, 
         node_node_data_field_item_canal_reference.title AS node_node_data_field_item_canal_reference_title, 
         ".$num_records." 
         FROM {node} node 
         LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
         LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
         LEFT JOIN {node} node_node_data_field_item_canal_reference ON node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid 
         LEFT JOIN content_field_responsable_uid node_data_field_responsable_uid ON node_node_data_field_item_canal_reference.nid = node_data_field_responsable_uid.nid
         LEFT JOIN content_field_responsable_uid2 node_data_field_responsable_uid2 ON node_node_data_field_item_canal_reference.nid = node_data_field_responsable_uid2.nid        
         LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id
         ".$left_join_content_field_canal_active_refresh.$left_join_categoria."
         WHERE ".implode(" AND ",$where)."
         GROUP BY node_node_data_field_item_canal_reference_title, node_data_field_item_canal_reference_field_item_canal_reference_nid 
         ORDER BY node_node_data_field_item_canal_reference_title ASC";

         //print $sql;exit();
         //
         $view->build_info['query']=$sql;
         $view->build_info['count_query']=$sql;
    }//else{
    //     hontza_repasar_canal_sin_valor($view->build_info['query']);
    //}
   }
}
//intelsat-2015
//function hontza_noticia_usuario_web_link($node){
function hontza_noticia_usuario_web_link($node,$is_url=0,$is_crm_exportar=0){
    global $base_url;
    $url='no_existe_enlace_origen_noticia_usuario';
    //intelsat-2015
    //if(red_movil_is_activado()){
    //intelsat-2016
    if($is_crm_exportar){
        $url=$base_url.'/node/'.$node->nid;
    }else{
        $url='node/'.$node->nid;    
    }    
    //}
    if(isset($node->field_enlace_noticia) && !empty($node->field_enlace_noticia)){
        if(isset($node->field_enlace_noticia[0]['url']) && !empty($node->field_enlace_noticia[0]['url'])){
            $url=$node->field_enlace_noticia[0]['url'];            
        }    
    }
    //intelsat-2015
    if($is_url){
        return $url;
    }
    $label='';
    //$label=t('Web');
    return l($label,$url,array('attributes'=>array('target'=>'_blank','title'=>t('Web'),'alt'=>t('Web'))));	
}
function hontza_is_hound_rss_by_data($data,$yql_obj){
    //intelsat-2016
    return hound_enlazar_inc_is_hound_rss_by_data($data,$yql_obj);
}
function hontza_node_has_body($node){
    if(isset($node->content) && isset($node->content['body']) && isset($node->content['body']['#value'])){
        $value=$node->content['body']['#value'];
        if(!empty($value)){
            return 1;
        }
    }
    return 0;
}
function hontza_content_resumen($node,$len_in=150,$is_cortar=1){    
    //$len=150;
    $result='';
    //intelsat-2015
    $len=red_despacho_get_content_resumen_lenght($len_in);
    if(hontza_node_has_body($node)){
        $value=$node->content['body']['#value'];
        $result=strip_tags($value);
        //$result=str_replace("\t"," ",$result);
        //$result=preg_replace('/ +/',' ', $result);
        $result=red_despacho_unset_content_resumen_caracteres($result);
        $result=trim($result);
        if(empty($result)){
            $result=red_funciones_get_node_description($node);
        }        
        if($is_cortar){
            if(strlen($result)>$len){
                $result=drupal_substr($result, 0, $len); 
                $result.='...';
                return red_solr_inc_resaltar_termino_busqueda($result);
            }/*else{
                return $value;
            }*/
        }       
    }else{
        //intelsat-2014
        if(isset($node->body)){
            $result=$node->body;
        }else{
            $result=red_funciones_get_node_description($node);
        }    
        //
            $result=strip_tags($result);
            $result=html_entity_decode($result);
            //$result=str_replace("\t"," ",$result);
            //$result=preg_replace('/ +/',' ', $result);
            $result=red_despacho_unset_content_resumen_caracteres($result);
            if($is_cortar){
                if(strlen($result)>$len){
                    $result=drupal_substr($result, 0, $len); 
                    $result.='...';
                    return red_solr_inc_resaltar_termino_busqueda($result);
                }
            }        
        //
    }
    return red_solr_inc_resaltar_termino_busqueda($result);
}
function hontza_my_report_resumen($node){
   return hontza_content_resumen($node,250);
}
function hontza_is_mis_canales_block(){
   $value=variable_get('my_canales_list_block','mis_canales');
   if($value=='todos'){
       return 0;
   }
   return 1;
}
function hontza_mis_canales_block_link(){
   /* 
   $label=t('My Channels');
   if(hontza_is_mis_canales_block()){ 
       $label=t('All channels');
   }   
   return l($label,'cambiar_consulta_canales_block',array('query'=>drupal_get_destination()));*/
   return ''; 
   /*$result =l(t('Create User News'), 'node/add/noticia') .'<br>'.
                              l(t('View all'), 'canal-usuarios');
   return $result;*/
}
function hontza_cambiar_consulta_canales_block_callback(){
    $url=$_REQUEST['destination'];    
    if(hontza_is_mis_canales_block()){
        variable_set('my_canales_list_block','todos');
    }else{
        variable_set('my_canales_list_block','mis_canales');
    }
    drupal_goto($url);
    exit();
}
function hontza_quitar_signo_t($value,$signo){
    $result=ltrim($value,$signo);
    return $result;
}
function hontza_unset_seconds_by_date($date){
    if(!empty($date)){
        $my_array=explode(':',$date);
        $num=count($my_array);
        if($num>=3){
            $my_array=array_slice($my_array,0,$num-1);
            return implode(':',$my_array);
        }
    }
    return $date;
}
function hontza_get_block_content_anadir_canal(){
    if(!repase_organic_group_access()){
        return '';
    }
    //intelsat-2015
    if(hontza_solr_search_is_usuario_lector()){
        return '';
    }
	$result=array();
        //intelsat-2016
        /*
        //intelsat-2015
        if(!hontza_canal_rss_is_visualizador_activado()){
            $result[]=l(t('From a Source'), 'crear/canal-supercanal');
        }*/
	//$result[]=l(t('RSS Filter'), 'crear/canal-yql');
	$result[]=l(t('RSS Filter'), 'crear/canal-yql',array('query'=>array('simple'=>0)));
	$result[]=l(t('Import RSS'), 'crear/canal-yql',array('query'=>array('simple'=>1)));
	//$result[]=l(t('Importar OPML'), 'crear/canal-yql',array('query'=>array('is_opml'=>1)));
	//intelsat-2015
        /*if(is_super_admin()){
            //intelsat-2015
            if(!hontza_canal_rss_is_visualizador_activado()){
                $result[]=l(t('(Beta) Import HTML page'),'importar_pagina_html');
                $result[]=l(t('(Beta) Import HTML source'),'importar_fuente_html');
            }
        }*/
        //intelsat-2015
        //if(!hontza_canal_rss_is_visualizador_activado()){
            //if(hontza_is_sareko_id('ROOT')){
            if(hontza_is_hound_actions()){                
                if(module_exists('hound')){
                  if(hound_is_hound_filter_activado()){
                    $result[]=l(t('Hound Filter'), 'crear/canal-yql',array('query'=>array('simple'=>0,'is_hound'=>1)));
                  }
                }
                $result[]=l(t('Import Hound'), 'crear/canal-yql',array('query'=>array('simple'=>1,'is_hound'=>1)));               
            }
            //intelsat-2016
            if(hontza_canal_json_is_activado()){
                $result[]=l(t('Import Json'), 'canal_json/crear');
                $result[]=l(t('Import Csv'), 'canal_json/crear_csv');
            }
            if(hound_settings_access()){
                //$result[]=l(t('Hound settings'), 'hound/settings',array('query'=>drupal_get_destination()));
                $result[]=l(t('Hound settings'), 'hound/settings');
            }
        //}    
        return implode("<BR>",$result);
}
function hontza_define_hound_options(){
    $result=array();
    if(red_is_rojo()){
        $result[1]='boamp';
        //$result[2]='boe';
        $result[2]='ted';
    //}else if(hontza_is_sareko_id('GESTION_CLAVES')){
    }else if(red_is_claves_activado()){    
        $result[4]='espacenet';
        $result[5]='sciencedirect';
        $result[6]='scholar';
        $result[7]='mendeley';
    }else{
        $result[1]='boamp';
        $result[2]='boe';
        $result[3]='ted';
    }
    return $result;
}
function hontza_get_hound_rss1(){
    $options=hontza_define_hound_options();
    $values=array_values($options);
    if(count($values)>0){
        $url=hontza_define_hound_url();
        $url.='?title='.$values[0];
        return $url;
    }
    return '';
}
//intelsat-2015
//function hontza_define_hound_url(){
function hontza_define_hound_url($is_ip=0){
    global $base_url;
        //intelsat-2015
        if(hound_is_new_server_hound()){
            $ip_hound='217.70.191.147:8080';
            //$ip_hound='hound2.hontza.es';
        }else{
            $ip_hound='80.32.72.239:8000';
            /*if(red_is_rojo()){
                $ip_hound='46.4.177.210';
            }*/
            if(in_array($base_url,array('http://localhost/hontza3','http://192.168.110.210/hontza3'))){
                $ip_hound='192.168.110.211';            
            }
        }
    
    //intelsat-2015
    //$ip_hound='217.70.191.147:8080';
    $ip_hound='hound.hontza.es';    
    //intelsat-2016
    if(hound_enlazar_inc_is_activado()){
        $ip_hound=hound_enlazar_inc_get_base_url(0);
    }//$ip_hound='hound2.hontza.es';
    if($is_ip){
        return $ip_hound;
    }
    $url='http://'.$ip_hound.'/hound/houndRss.php';
    //intelsat-2016
    if(hound_enlazar_inc_is_activado()){
        $url=$ip_hound;
    }
    return $url;
}
function hontza_is_hound_by_yql_obj($yql_obj){
    if(isset($yql_obj->field_is_hound) && isset($yql_obj->field_is_hound[0]) && isset($yql_obj->field_is_hound[0]['value']) && !empty($yql_obj->field_is_hound[0]['value'])){
        return 1;
    }    
    return 0;
}
function hontza_update_hound_feeds_source($nid,$last_import_time){
    //if(user_access('root')){
        if(!empty($nid)){
            if(hontza_is_hound_canal($nid)){
                $source=hontza_get_feeds_source($nid);
                if(isset($source->feed_nid) && !empty($source->feed_nid)){
                    $url=hontza_set_hound_url_from_time($source->source,$last_import_time);
                    
                    //db_query($sql=sprintf('UPDATE {feeds_source} SET source="%s" WHERE feed_nid=%d',$url,$source->feed_nid));
                    
                }
            }
        }    
    //}
}
function hontza_is_hound_canal($nid,$canal_in=''){
    if(!empty($canal_in)){
        $canal=$canal_in;
    }else{
        if(empty($nid)){
            return 0;
        }
        $canal=node_load($nid);
    }
    //
    if(isset($canal->nid) && !empty($canal->nid)){
        if($canal->type=='canal_de_yql'){
            if(isset($canal->field_is_hound) && isset($canal->field_is_hound[0]) && isset($canal->field_is_hound[0]['value']) && !empty($canal->field_is_hound[0]['value'])){
                return 1;
            }else{
              //intelsat
              if(module_exists('hound')){
                if(hound_canal_field_is_hound_value($canal)){
                  return 1;
                }
              }  
            }
        }
    }
    return 0;
}
function hontza_set_hound_url_from_time($url_in,$last_import_time){
   //intelsat-2016 
   return hound_enlazar_inc_set_hound_url_from_time($url_in,$last_import_time);
}
function hontza_get_hound_feeds_source_url($nid,$last_import_time,$url_in){
    //if(user_access('root')){
        if(!empty($nid)){
            if(hontza_is_hound_canal($nid)){
                //$source=hontza_get_feeds_source($nid);
                //if(isset($source->feed_nid) && !empty($source->feed_nid)){
                    $url=hontza_set_hound_url_from_time($url_in,$last_import_time);
                    return $url;                    
                //}
            }
        }    
    //}
    return $url_in;
}
//intelsat-2015
//function hontza_get_hound_url_by_nid($nid){
function hontza_get_hound_url_by_nid($nid,$is_view_canal=0){
    //if(user_access('root')){
        $source=hontza_get_feeds_source($nid);
        if(isset($source->feed_nid) && !empty($source->feed_nid)){
            $last_import_time='';
            $canal=node_load($nid);
            if(isset($canal->nid) && !empty($canal->nid)){
                if(isset($canal->field_last_import_time) && isset($canal->field_last_import_time[0]) && isset($canal->field_last_import_time[0]['value']) && !empty($canal->field_last_import_time[0]['value'])){
                    $last_import_time=$canal->field_last_import_time[0]['value'];
                    $url=hontza_set_hound_url_from_time($source->source,$last_import_time);
                    return $url;
                }else{
                    //intelsat-2015
                    if($is_view_canal){
                        $url=hontza_set_hound_url_from_time($source->source,0);
                        return $url;
                    }
                }
            }    
        }    
    //}
    return '';
}
function hontza_convertir_fecha_doble_digito($fecha_in){
   $fecha=$fecha_in;
   if(isset($fecha['month'])){
       $fecha['month']=hontza_convertir_numero_doble_digito($fecha['month']);
   }
   if(isset($fecha['day'])){
       $fecha['day']=hontza_convertir_numero_doble_digito($fecha['day']);
   }
   return array_values($fecha);
}
function hontza_convertir_numero_doble_digito($value){
   $result=$value;
   $s=(string) $value;
   $len=strlen($s);
   if($len<2){
       $s='0'.$s;
       return $s;
   }
   return $result;
}
function hontza_hound_limit_all($all_in){
    $all=$all_in;
    /*if($all>0){
        $max_hound_items=variable_get('max_hound_items',50);
        if($all>$max_hound_items){
            return $max_hound_items;
        }
    }*/
    return $all;
}
function hontza_item_web_link($node,$is_url=0,$in_vista_compacta=0){
    $url='';
    if(isset($node->feeds_node_item->url) && !empty($node->feeds_node_item->url)){
        $url=$node->feeds_node_item->url;
    }else if(isset($node->feeds_node_item->guid) && !empty($node->feeds_node_item->guid)){
        $url=$node->feeds_node_item->guid;
        if(!red_canal_is_url_item_guid($url)){
          if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $url='';
          }        
        }
        //print 'guid='.$url;exit();
        
    }
    //print 'url='.$url;exit();
    //
    //intelsat-2016
    $field_item_web=red_fields_inc_get_field_item_web_name();
    if(empty($url)){
        /*if(isset($node->$field_item_web) && isset($node->$field_item_web[0]) && isset($node->$field_item_web[0]['value'])){           
            $url=$node->$field_item_web[0]['value'];
        }*/
        if(isset($node->$field_item_web)){
            $row_item_web=$node->$field_item_web;
            if(isset($row_item_web[0]) && isset($row_item_web[0]['value'])){
                $url=$row_item_web[0]['value'];
            }    
        }
    }
    if(empty($url) || hontza_is_nolink($url)){
        //intelsat-2016
        //$url='no_existe_enlace_origen';
        $url='no_existe_enlace_origen/item';
        //intelsat-2015
        if(red_movil_is_activado()){
            $url='node/'.$node->nid;
        }
    }    
    if($is_url){
        return $url;
    }
    //intelsat-2015
    $url=red_solr_set_url_sin_redireccionamiento($url);
    $label='';
    //$label=t('Web');
    $attributes=array('target'=>'_blank','title'=>t('Web'),'alt'=>t('Web'));
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    return l($label,$url,array('attributes'=>$attributes));
}
//intelsat-2015
//function hontza_item_tag_link($node){
function hontza_item_tag_link($node,$is_url=0){
    $label='';    
    //$label=t('Tag');
    //return l($label,'node/'.$node->nid.'/tag',array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Tag'),'alt'=>t('Tag'))));
    //return l($label,'node/'.$node->nid.'/tag',array('query'=>drupal_get_destination(),'attributes'=>array('target'=>'_blank','title'=>t('Tag'),'alt'=>t('Tag'))));
    $url='node/'.$node->nid.'/tag';
    //intelsat-2015
    if($is_url){
        return $url;
    }
    hontza_add_tag_window_open_js($node->nid,$url);
    return l($label,$url,array('query'=>drupal_get_destination(),'attributes'=>array('id'=>$node->nid.'_tag_link','target'=>'_blank','title'=>t('Tag'),'alt'=>t('Tag'))));
}
//intelsat-2015
//function hontza_item_comment_link($node){
function hontza_item_comment_link($node,$is_url=0){
    //intelsat-2015
    if(red_despacho_is_show_comment_link()){
        $label='';
        //$label=t('Comment');
        //return l($label,'comment/reply/'.$node->nid,array('fragment'=>'comment-form','query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Comment'),'alt'=>t('Comment'))));
        $url='comment/reply/'.$node->nid;
        $url=comment_publico_item_comment_link($node,$url);
        //intelsat-2015
        if($is_url){
            return $url;
        }
        return l($label,$url,array('fragment'=>'comment-form','query'=>drupal_get_destination(),'attributes'=>array('target'=>'_blank','title'=>t('Comment'),'alt'=>t('Comment'))));    
    }    
    return '';    
}
function hontza_item_delete_link($node,$title_in=''){
    //intelsat-2015
    $title=t('Delete');
    if(!empty($title_in)){
        $title=$title_in;
    }
    //
    $label=$label_in;
    //$label=t('Delete');
    $attributes=array('title'=>$title,'alt'=>$title);
    //$attributes=array('target'=>'_blank','title'=>t('Delete'),'alt'=>t('Delete'));
    //$label_confirm=t('Are you sure you want to delete @title?', array('@title' => $node->title));    
    //$attributes['onclick']='"if(confirm(\''.$label_confirm.'\')){return true;}return false;)"';
    $destination=hontza_define_delete_destination($node);
    return l($label,'node/'.$node->nid.'/delete',array('query'=>$destination,'attributes'=>$attributes));    
}
function hontza_add_destacar_ajax(){
    global $base_url;
    $purl='';
   $my_grupo=og_get_group_context(); 
   if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
   }
    $js='$(document).ready(function()
   {
            create_call_destacar_ajax_functions();
            function call_destacar_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/destacar_ajax/"+nid+"?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_destacar_item_ajax_on_success(my_result);
				}
			});
            }
            function call_no_destacar_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/no_destacar_ajax/"+nid+"?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_destacar_item_ajax_on_success(my_result);                                    
				}
			});
            }
            function create_call_destacar_ajax_functions(){
                $("a.a_destacar_ajax").unbind( "click" );
                $("a.a_destacar_ajax").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_destacar_","");
                    call_destacar_ajax(nid);
                    return false;
                });
                $("a.a_no_destacar_ajax").unbind( "click" );
                $("a.a_no_destacar_ajax").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_destacar_","");
                    call_no_destacar_ajax(nid);
                    return false;
                });
            }
            function set_destacar_item_ajax_on_success(my_result){
                var my_parent=$("#id_destacar_"+my_result.nid).parent();
                var my_class=my_parent.attr("class");
                my_parent.attr("class",set_destacar_item_class_ajax(my_class));
                my_parent.html(my_result.a);
                create_call_destacar_ajax_functions();
            }
            function set_destacar_item_class_ajax(my_class){
                if(my_class=="n-item-destacar"){
                    return "n-item-no-destacar";
                }
                if(my_class=="n-item-no-destacar"){
                    return "n-item-destacar";
                }
                if(my_class=="item-destacar"){
                    return "item-no-destacar";
                }
                if(my_class=="item-no-destacar"){
                    return "item-destacar";
                }                
            }
   });';
   drupal_add_js($js,'inline');
}
function destacar_ajax_callback(){
    $result=array();
    $nid=arg(1);
    update_item_carpeta_destacada($nid,1);
    $result['nid']=$nid;
    $node=node_load($nid);
    //intelsat-2015
    hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    $result['a']=get_destacar_link($node,1);
    print json_encode($result);
    exit();
}
function no_destacar_ajax_callback(){
    $result=array();
    $nid=arg(1);
    update_item_carpeta_destacada($nid,0);
    $result['nid']=$nid;
    $node=node_load($nid);
    //intelsat-2015
    hontza_canal_rss_solr_clear_node_index($node,$node->nid);    
    $result['a']=get_destacar_link($node,1);
    print json_encode($result);
    exit();
}
function hontza_add_noticia_usuario_destacar_ajax(){
    global $base_url;
    $purl='';
   $my_grupo=og_get_group_context(); 
   if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
   }
    $js='$(document).ready(function()
   {
            create_call_noticia_usuario_ajax_functions();
            function call_noticia_usuario_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/destacar_noticia_usuario_ajax/"+nid+"?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_noticia_usuario_destacar_ajax_on_success(my_result);
				}
			});
            }
            function call_no_noticia_usuario_ajax(nid){
             jQuery.ajax({
				//type: "POST",
                                type:"GET",
				url: "'.$base_url.'/'.$purl.'/no_destacar_noticia_usuario_ajax/"+nid+"?my_time="+new Date().getTime(),
				dataType:"json",
				success: function(my_result){
                                    set_noticia_usuario_destacar_ajax_on_success(my_result);                                    
				}
			});
            }
            function create_call_noticia_usuario_ajax_functions(){
                $("a.a_destacar_noticia_usuario").unbind( "click" );
                $("a.a_destacar_noticia_usuario").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_destacar_noticia_usuario_","");
                    call_noticia_usuario_ajax(nid);
                    return false;
                });
                $("a.a_no_destacar_noticia_usuario").unbind( "click" );
                $("a.a_no_destacar_noticia_usuario").click(function(){
                    var a_id=$(this).attr("id");
                    var nid=a_id.replace("id_destacar_noticia_usuario_","");
                    call_no_noticia_usuario_ajax(nid);
                    return false;
                });
            }
            function set_noticia_usuario_destacar_ajax_on_success(my_result){
                var my_parent=$("#id_destacar_noticia_usuario_"+my_result.nid).parent();
                var my_class=my_parent.attr("class");
                my_parent.attr("class",set_noticia_usuario_destacar_class_ajax(my_class));
                my_parent.html(my_result.a);
                create_call_noticia_usuario_ajax_functions();
            }
            function set_noticia_usuario_destacar_class_ajax(my_class){
                if(my_class=="n-item-destacar"){
                    return "n-item-no-destacar";
                }
                if(my_class=="n-item-no-destacar"){
                    return "n-item-destacar";
                }
                if(my_class=="item-destacar"){
                    return "item-no-destacar";
                }
                if(my_class=="item-no-destacar"){
                    return "item-destacar";
                }                
            }
   });';
   drupal_add_js($js,'inline');
}
function destacar_noticia_usuario_ajax_callback(){
    hontza_destacar_access_denied();
    $result=array();
    $nid=arg(1);
    update_noticia_destacada($nid, 1);
    $result['nid']=$nid;
    $node=node_load($nid);
    //intelsat-2016
    if(red_solr_inc_is_rated_clear_node_index(1)){
        hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    }    
    $result['a']=get_noticia_destacar_link($node,1);
    print json_encode($result);
    exit();
}
function no_destacar_noticia_usuario_ajax_callback(){
    hontza_destacar_access_denied();
    $result=array();
    $nid=arg(1);
    update_noticia_destacada($nid, 0);
    $result['nid']=$nid;
    $node=node_load($nid);
    //intelsat-2016
    if(red_solr_inc_is_rated_clear_node_index(1)){
        hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    }    
    $result['a']=get_noticia_destacar_link($node,1);
    print json_encode($result);
    exit();
}
function hontza_destacar_action_class($node,$is_n=1){
    $result='item-destacar';
    $row=my_get_content_type_item($node->nid,$node->vid);
    if(!empty($row) && isset($node->nid) && !empty($node->nid) && is_item_noticia_destacada($row)){
        $result='item-no-destacar';
    }
    if($is_n){
        $result='n-'.$result;
    }
    return $result;
}
function hontza_noticia_destacar_action_class($node,$is_n=1){
    $result='item-destacar';
    $row=my_get_content_type_noticia($node->nid,$node->vid);
    if(!empty($row) && isset($node->nid) && !empty($node->nid) && is_noticia_usuario_destacada($row)){
        $result='item-no-destacar';
    }
    if($is_n){
        $result='n-'.$result;
    }
    return $result;
}
function hontza_is_hound_actions(){
    global $user;
    //intelsat-2015
    if(red_crear_usuario_is_rol_administrador_creador_grupo()){
        $my_array=hontza_define_hound_sareko_id_array();
        //simulando
        //$my_array=array_merge(hontza_simular_hound_sareko_id_array());
        //
        if(!empty($my_array)){
            foreach($my_array as $i=>$v){
                if(hontza_is_sareko_id($v)){
                    //if($user->uid==1){
                        return 1;
                    //}
                }
            }
        }
        $hound_is_active=variable_get('hound_is_active',0);
        if($hound_is_active){
            return 1;
        }
        //
        //intelsat-2015
        if(defined('_IS_HOUND')){
            if(_IS_HOUND){
                return 1;
            }
        }
    }    
    return 0;    
}
function hontza_debate_comment_link($node){
    return hontza_item_comment_link($node);
}
function hontza_item_edit_link($node,$destination_in=''){
    //$label=t('Edit');
    $label='';
    $options=array('attributes'=>array('title'=>t('Edit'),'alt'=>t('Edit')));
    if(!empty($destination_in)){
        $options['query']=$destination_in;
    }
    return l($label,"node/".$node->nid.'/edit',$options);   
}
function hontza_debate_edit_link($node){
    return hontza_item_edit_link($node);
}
function hontza_debate_tag_link($node){
    return hontza_item_tag_link($node);
}
function hontza_wiki_edit_link($node){
    return hontza_item_edit_link($node);
}
function hontza_wiki_comment_link($node){
    return hontza_item_comment_link($node);
}
function hontza_wiki_tag_link($node){
    return hontza_item_tag_link($node);
}
function hontza_no_existe_enlace_origen_wiki_callback(){
    drupal_set_title(t('Origin of Collaborative Document'));
    $html='<p>'.t('There are no contents').'</p>';
    return $html;
}
function hontza_no_existe_enlace_origen_noticia_usuario_callback(){
    drupal_set_title(t('Links of User News'));
    $html='<p>'.t('There are no contents').'</p>';
    return $html;
}
function hontza_get_username($uid){
      $username='';
      $my_user=user_load($uid);
      if(isset($uid) && !empty($uid)){
         $username=$my_user->name;
      }
      return $username;
}
function hontza_define_delete_destination($node){
    $destination=drupal_get_destination();
    if(urldecode($destination)=='destination=node/'.$node->nid){
        $url='';
        if($node->type=='noticia'){
            $url='canal-usuarios';        
        }else if($node->type=='item'){
            $url='vigilancia/validados';        
        }else if($node->type=='my_report'){
            $url='boletin_report/report_view_list';
        }else if($node->type=='estrategia'){
            $url='estrategias';
        }else if($node->type=='despliegue'){
            $url='despliegues/todas';
        }else if($node->type=='decision'){
            $url='decisiones/todas';
        }else if($node->type=='informacion'){
            $url='informaciones/todas';
        }else if($node->type=='idea'){
            $url='ideas';
        }else if($node->type=='oportunidad'){
            $url='oportunidades/todas';
        }else if($node->type=='proyecto'){
            $url='proyectos/todas';
        }else if($node->type=='debate'){
            $url='area-debate';        
        }else if($node->type=='wiki'){
            $url='area-trabajo';
        //gemini-2014
        }else if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
            $url='vigilancia/validados';        
        }else if(in_array($node->type,array('supercanal','fuentedapper'))){
            $url='fuentes-pipes/todas';        
        }
        //
        if(!empty($url)){
            return 'destination='.$url;
        }    
    }    
    return $destination;
}
function hontza_set_node_delete_confirm(&$form, &$form_state, $form_id) {
	if($form_id=='node_delete_confirm'){
                $nid=my_get_nid_by_form($form);	
		if(!empty($nid)){
                    	$my_node=node_load($nid);
			if(!empty($my_node)){
                                //drupal_set_title(estrategia_set_title_max_len($my_node->title));		
				estrategia_node_delete_confirm_form_alter($form,$form_state,$form_id,$my_node);				
                                if(strcmp($my_node->type,'my_help')==0){
					$s=urldecode(drupal_get_destination());
					//$s=str_replace('?','&',$s);		
					//$my_array=explode('?title=',$s);
					$my_array=explode('?',$s);
					if(isset($my_array[1]) && !empty($my_array[1])){
						//$s='title='.$my_array[1];
						$s=$my_array[1];
						//print $s.'<BR>';
						$form['actions']['cancel'] = array('#value' => l(t('Cancel'),'panel_admin/ayuda_popup',array('query'=>$s)));
					}else{
						$form['actions']['cancel'] = array('#value' => l(t('Cancel'),'panel_admin/ayuda_popup'));
					}
				}else if(in_array($my_node->type,array('supercanal','fuentedapper'))){
					//echo print_r($form,1);					
					$my_description=$form['description']['#value'];
					$my_description.='<BR>';
					$my_description.='<b>'.t('If the source is deleted also the following channels will be deleted').':</b>';
					$canales=my_get_canales_by_fuente($my_node->title);
					if(!empty($canales)){
						$my_description.=my_get_html_list($canales);
					}
					$form['description']=array('#value'=>$my_description);
					$form['#redirect']='fuentes-pipes/todas';
				}else if(strcmp($my_node->type,'noticia')==0){
                                    hontza_node_delete_noticia_form_alter($form,$form_state,$form_id);
				}else if(in_array($my_node->type,array('canal_de_supercanal','canal_de_yql'))){
					//intelsat-2015
                                        //$form['#redirect']='vigilancia/pendientes';
					hontza_canal_rss_canal_node_delete_confirm_form_alter($form, $form_state, $form_id);
                                        //print $form['#redirect'];
				}else if(strcmp($my_node->type,'wiki')==0){
					$form['#redirect']='area-trabajo';
				}else if(strcmp($my_node->type,'debate')==0){
					$form['#redirect']='area-debate';
                                //gemini-2013        
				}else if(strcmp($my_node->type,'grupo')==0){                                    
                                    node_delete_group_form_alter($form,$form_state,$form_id);
                                }else if(strcmp($my_node->type,'idea')==0){    
                                    idea_node_delete_confirm_form_alter($form,$form_state,$form_id);   
                                }else if(strcmp($my_node->type,'oportunidad')==0){    
                                    oportunidad_node_delete_confirm_form_alter($form,$form_state,$form_id);   
                                }else if(strcmp($my_node->type,'proyecto')==0){    
                                    proyecto_node_delete_confirm_form_alter($form,$form_state,$form_id);   
                                }else if(strcmp($my_node->type,'my_report')==0){    
                                    boletin_report_node_delete_confirm_form_alter($form,$form_state,$form_id);   
                                }else if(strcmp($my_node->type,'item')==0){
                                    hontza_node_delete_item_form_alter($form,$form_state,$form_id);
				}else if(strcmp($my_node->type,'despliegue')==0){
                                    despliegue_node_delete_confirm_form_alter($form,$form_state,$form_id);
				}else if(strcmp($my_node->type,'decision')==0){
                                    decision_node_delete_confirm_form_alter($form,$form_state,$form_id);
				}else if(strcmp($my_node->type,'informacion')==0){
                                    informacion_node_delete_confirm_form_alter($form,$form_state,$form_id);
				}                               
			}	
		}						
	}
}
function hontza_node_delete_noticia_form_alter(&$form,&$form_state,$form_id){
    $form['#redirect']='canal-usuarios';
}
function hontza_node_delete_item_form_alter(&$form,&$form_state,$form_id){
    $form['#redirect']='vigilancia/validados';
}
function hontza_define_usuarios_acceso_form_filter(){
    my_add_buscar_js();
    return drupal_get_form('hontza_usuarios_acceso_filter_form');    
}
function hontza_usuarios_acceso_filter_form(){
    $form=array();
    $fecha_inicio=hontza_get_usuarios_acceso_filter_value('usuarios_acceso_fecha_inicio');
    $fecha_fin=hontza_get_usuarios_acceso_filter_value('usuarios_acceso_fecha_fin');
    //gemini-2014
    $fs_title=t('Search');
    //intelsat-2016
    //if(empty($fecha_inicio) && empty($fecha_fin)){
    if(!panel_admin_usuarios_acceso_filter_activated($fecha_inicio,$fecha_fin)){    
        $fs_title=t('Filter by Date');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter by Date Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    //intelsat-2016
    panel_admin_usuarios_acceso_add_filter_form($form);        
    //$fecha_inicio=array('Y'=>2013,'n'=>10,'j'=>21);
    $form['file_buscar_fs']['usuarios_acceso_fecha_inicio']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('From'),
			'#default_value'=>  $fecha_inicio);
    $form['file_buscar_fs']['usuarios_acceso_fecha_fin']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('To'),
			'#default_value'=>$fecha_fin);
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div class="vigilancia_filter_buttons">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form; 
}
function hontza_get_usuarios_acceso_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'usuarios_acceso');
}
function hontza_usuarios_acceso_filter_form_submit($form,&$form_state){
    $key='usuarios_acceso';
    //intelsat-2016
    $panel_admin_key=panel_admin_usuarios_acceso_get_session_key();
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
            //intelsat-2016
            if(isset($_SESSION[$panel_admin_key]['filter']) && !empty($_SESSION[$panel_admin_key]['filter'])){
                unset($_SESSION[$panel_admin_key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            //intelsat-2016
            $_SESSION[$panel_admin_key]['filter']=array();
            $fields=hontza_define_usuarios_acceso_filter_fields();
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
    //intelsat-2016
    if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
        $_SESSION[$panel_admin_key]['filter']=$_SESSION[$key]['filter'];
    }
}
function hontza_define_usuarios_acceso_filter_fields(){
    $result=array('usuarios_acceso_fecha_inicio','usuarios_acceso_fecha_fin');
    //intelsat-2016
    if(panel_admin_usuarios_acceso_is_estadisticas()){
        $result[]='grupo_nid';
    }
    return $result;
}
function hontza_define_usuarios_acceso_table_limit(){
    $my_limit=variable_get('default_nodes_main', 100);
    $my_limit=20;
    return $my_limit;
}
function hontza_define_not_tipo_informacion_options(){
    $result=array('alerta','boletin_grupo','boletin_grupo_despedida','boletin_grupo_introduccion','canal_busqueda','canal_de_supercanal','canal_de_yql','chat','chatroom','faq','fuentedapper','grupo','my_help','page','rss_feed_importador','supercanal');
    return $result;
}
//intelsat-2015
//function hontza_define_tipo_informacion_options($with_empty=1){
function hontza_define_tipo_informacion_options($with_empty=1,$is_quant=0){
    $options=array();
    if($with_empty){
        $options['']='';
    }
    $options['estrategia']=t('Strategy').'-'.t('Challenge');
    $options['despliegue']=t('Strategy').'-'.t('SubChallenge');
    $options['decision']=t('Strategy').'-'.t('Decision');
    $options['informacion']=t('Strategy').'-'.t('Key Question');
    $options['item']=t('Monitoring').'-'.t('News');
    $options['noticia']=t('Monitoring').'-'.t('User News');
    $options['debate']=t('Discussion').'-'.t('Discussion');
    $options['wiki']=t('Collaboration').'-'.t('Wiki');
    $options['my_report']=t('Collaboration').'-'.t('Report');
    $options['idea']=t('Proposals').'-'.t('Idea');
    $options['oportunidad']=t('Proposals').'-'.t('Opportunity');
    $options['proyecto']=t('Proposals').'-'.t('Project');
    $options['fuentehtml']=t('Sources').'-'.t('HTML');
    //intelsat-2015
    if($is_quant){
        $canales_options=contenidos_canales_define_node_type_options();
        $options=array_merge($options,$canales_options);
        red_add_tipo_tipo_informacion_options($options);
    }
    return $options;
}
function hontza_where_node_type_docs(){
    $keys=array_keys(hontza_define_tipo_informacion_options());
    if(isset($keys[0]) && empty($keys[0])){
        $keys=array_slice($keys,1);
    }
    $types=implode('","',$keys);
    $or=array();
    $or[]='n.type IN("'.$types.'")';
    $or[]='nc.type IN("'.$types.'")';
    $or[]='ns.type IN("'.$types.'")';
    return '('.implode(' OR ',$or).')';
}
//intelsat-2016
//function hontza_get_usuarios_acceso_where_time($field='timestamp',$key_filter='usuarios_acceso',$ini_field='usuarios_acceso_fecha_inicio',$end_field='usuarios_acceso_fecha_fin'){
function hontza_get_usuarios_acceso_where_time($field='timestamp',$key_filter='usuarios_acceso',$ini_field='usuarios_acceso_fecha_inicio',$end_field='usuarios_acceso_fecha_fin',$is_crm_exportar=0,$fecha_ini='',$fecha_end=''){    
    $where=array();
    $where[]='1';
    $time_ini='';
    $time_end='';
    //intelsat-2016
    if($is_crm_exportar){
        $ini=$fecha_ini;
        $end=$fecha_end;
    }else{
        $ini=hontza_get_gestion_usuarios_filter_value($ini_field,$key_filter);
        $end=hontza_get_gestion_usuarios_filter_value($end_field,$key_filter);
    }
    if(!empty($ini)){
        $time_ini=strtotime($ini);
        $where[]=$field.'>='.$time_ini;
    }    
    if(!empty($end)){    
        $time_end=strtotime($end);
        //23:59:59 hora
        $time_end=$time_end+(24*60*60)-1;    
        $where[]=$field.'<='.$time_end;
    }
    //
    return '('.implode(' AND ',$where).')';
}
function hontza_debate_delete_link($node){
    return hontza_item_delete_link($node);
}
function hontza_wiki_delete_link($node){
    return hontza_item_delete_link($node);
}
function hontza_repase_access_debate(){
    if(is_area_debate()){
        hontza_grupos_active_access_tab('debate');
    }
}
function hontza_repase_access_wiki(){
    if(is_area_trabajo()){
        hontza_grupos_active_access_tab('wiki');
    }
}
function hontza_on_debate_presave(&$node){
    //hontza_on_node_categoria_save($node);
    //intelsat-2015
    hontza_canal_rss_on_node_categoria_tematica_presave($node); 
}
function hontza_on_wiki_presave(&$node){
    //hontza_on_node_categoria_save($node);
    //intelsat-2015
    hontza_canal_rss_on_node_categoria_tematica_presave($node); 
}
function hontza_debate_comment_form_alter(&$form,&$form_state, $form_id){
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='debate'){
            if(hontza_is_comment_reply()){
                $item_nid=my_get_request('item_nid');
                if(!empty($item_nid)){
                    //intelsat-2015
                    $item_nid_array=explode(',',$item_nid);
                    $item_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($item_nid_array);
                    if(count($item_nid_array)>1){
                        $link_array=array();
                        foreach($item_nid_array as $i=>$my_item_nid){
                            $node_item=node_load($my_item_nid);
                            $url=hontza_get_item_url_enlace($my_item_nid,$node_item);
                            $form['enlace_nid']=array(
                                '#type'=>'hidden',
                                '#default_value'=>$item_nid,
                            );
                            $link=l($node_item->title,$url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
                            $link_array[]=t('Origin').': '.$link;
                        }
                        $form['comment_filter']['comment']['#default_value']=implode('<br>',$link_array);                        
                    //    
                    }else{
                        $node_item=node_load($item_nid);
                        $url=hontza_get_item_url_enlace($item_nid,$node_item);
                        $form['enlace_nid']=array(
                            '#type'=>'hidden',
                            '#default_value'=>$item_nid,
                        );
                        $link=l($node_item->title,$url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
                        $form['comment_filter']['comment']['#default_value']=t('Origin').': '.$link;
                    }
                }
            }    
        }
    }
}
function hontza_get_node_by_form($form){
    $nid=hontza_get_nid_by_form($form);
    if(!empty($nid)){
        $node=node_load($nid);
        return $node;
    }    
}
//intelsat-2015
//function hontza_get_item_url_enlace($item_nid,$node_in=''){
function hontza_get_item_url_enlace($item_nid,$node_in='',$is_fix=1){
    global $base_url;
    if(empty($node_in)){        
        $node=node_load($item_nid);
    }else{
        $node=$node_in;
    }
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='item'){
            if(isset($node->feeds_node_item)){
                if(isset($node->feeds_node_item->url) && !empty($node->feeds_node_item->url)){
                    return hontza_fix_url_absolute($node->feeds_node_item->url,$is_fix);
                }
                //intelsat-2014
                if(isset($node->feeds_node_item->link) && !empty($node->feeds_node_item->link)){
                    return hontza_fix_url_absolute($node->feeds_node_item->link,$is_fix);
                }
                //
                if(isset($node->feeds_node_item->guid) && !empty($node->feeds_node_item->guid)){
                    return hontza_fix_url_absolute($node->feeds_node_item->guid,$is_fix);
                }                
            }
        }else if($node->type=='noticia'){
            if(isset($node->field_enlace_noticia) && isset($node->field_enlace_noticia[0]) && isset($node->field_enlace_noticia[0]['url']) && !empty($node->field_enlace_noticia[0]['url'])){
                return hontza_fix_url_absolute($node->field_enlace_noticia[0]['url'],$is_fix);
            }else{
                //gemini
                return $base_url.'/node/'.$node->nid;
            }
        //gemini-2014    
        }else if($node->type=='wiki'){
            return $base_url.'/node/'.$node->nid;
        }else if($node->type=='debate'){
            return $base_url.'/node/'.$node->nid;
        }
        //
    }    
}
function hontza_comment_enlace_origen_save($cid,$enlace_nid){
    //intelsat-2015
    $enlace_nid_array=explode(',',$enlace_nid);
    if(count($enlace_nid_array)>1){
        foreach($enlace_nid_array as $i=>$new_enlace_nid){
            if(!hontza_exist_enlace_comentario($cid,$new_enlace_nid)){
                db_query('INSERT INTO {enlace_comentario}(cid,enlace_nid) VALUES(%d,%d)',$cid,$new_enlace_nid);
            }
        }
    }else{
    //    
        if(!hontza_exist_enlace_comentario($cid,$enlace_nid)){
            db_query('INSERT INTO {enlace_comentario}(cid,enlace_nid) VALUES(%d,%d)',$cid,$enlace_nid);
        }
    }    
}
function hontza_is_comment_enlace_nid_post(){
    if(isset($_POST['enlace_nid']) && !empty($_POST['enlace_nid'])){
        return 1;
    }
    return 0;
}
function hontza_exist_enlace_comentario($cid,$enlace_nid){
    $result=hontza_get_enlace_comentario($cid,$enlace_nid);
    if(count($result)>0){
        return 1;
    }
    return 0;
}
function hontza_get_enlace_comentario($cid,$enlace_nid){
    $result=array();
    $res=db_query('SELECT * FROM {enlace_comentario} WHERE cid=%d AND enlace_nid=%d',$cid,$enlace_nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
//intelsat-2015
//function hontza_fix_url_absolute($url_in){
function hontza_fix_url_absolute($url_in,$is_fix=1){    
    $url=$url_in;
    //intelsat-2015
    if(!$is_fix){
        return $url;
    }
    $info=parse_url($url_in);
    if ( !isset($info["scheme"]) )
    {
       $url = "http://".$url;
    }
    return $url;
}
function hontza_get_enlaces_view_html($node,$is_edit=0,$con_fecha=0){
    $content=boletin_report_get_enlaces_derecha_html($node,1,$con_fecha);
    if($is_edit){
        if(!empty($content)){
            $html=array();
            $html[]='<div>';
            $html[]='<h3>'.t('Links').'</h3>';
            $html[]=$content;
            $html[]='</div>';
            return implode('',$html);
        }
    }
    return $content;
}
function hontza_set_form_buttons($url_cancel_in,&$form){
   $url_cancel=$url_cancel_in;
   if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
       $url_cancel=$_REQUEST['destination'];
   }
   boletin_report_unset_buttons(array('preview','preview_changes'),$form);
   $form['buttons']['cancel_btn']=array(
       '#value'=>l(t('Cancel'),$url_cancel),
       '#weight'=>1001);   
}
function hontza_is_private_download($picture,$is_picture=0,$with_filepath=1){
    $private_downloads=variable_get('file_downloads',1);
    if($private_downloads){
        if($private_downloads==2){
            if(!$with_filepath){
                return 1;
            }
            $file_directory_path=variable_get('file_directory_path','');
            $add='/';
            if($is_picture){
                $add='/pictures/';
            }    
            $konp=$file_directory_path.$add.basename($picture);            
            if($picture==$konp){    
                return 1;
            }    
        }
    }
    return 0;
}
//function hontza_get_url_file($filepath){
function hontza_get_url_file($filepath,$is_urlencode=0){    
    global $base_root,$base_path;
    /*$purl='';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
    }*/
    $my_filepath='';    
    if(hontza_is_private_download($filepath)){
        //$url=$base_root.$base_path.'system/files/'.basename($filepath);
        $my_filepath=basename($filepath);
        if($is_urlencode){
          $my_filepath=urlencode($my_filepath);
        }
        $url=$base_root.$base_path.'system/files/'.$my_filepath;
        //$url=$base_root.$base_path.$purl.'/system/files/'.basename($filepath);
    }else{
        //$url=$base_root.$base_path.$filepath;
        $my_filepath=$filepath;
        if($is_urlencode){
          $my_filepath=urlencode($filepath);
        }
        $url=$base_root.$base_path.$my_filepath;        
        //$url=$base_root.$base_path.$purl.'/'.$filepath;
    }
    return $url;
}
function hontza_get_fuente_stars_view($node,$field,$is_view=1){
    $v=0;
    if(isset($node->$field)){
       $my_array=$node->$field;     
       if(isset($my_array[0]) && isset($my_array[0]['rating'])){
        if(!empty($my_array[0]['rating'])){
            $v=$my_array[0]['rating'];
        }
       } 
    }
    $value=(int) $v/20;
    if($is_view){
        return my_create_stars_view($value,0,'',1);
    }else{
        return $value;
    }    
}
function hontza_is_hound_search($hound_options){
    /*
    $values=array_values($hound_options);
    if(!empty($values) && in_array($values[0],array('espacenet'))){
        return 1;
    }
    return 0;*/
    //if(hontza_is_sareko_id('GESTION_CLAVES')){
    if(red_is_claves_activado()){    
        return 1;
    }    
    if(hontza_is_sareko_id('LOKALA')){
        return 1;
    }
    return 0;
}
function hontza_trim_base_root($base_root){
    $result=$base_root;
    $result=str_replace('http://www.','',$result);
    $result=str_replace('https://www.','',$result);
    $result=str_replace('http://','',$result);
    $result=str_replace('https://','',$result);
    $result=trim($result,'/');
    if($result=='hontza.es'){
        return 'hontza3';
    }
    return $result;
}
function hontza_define_vigilancia_form_filter(){
    my_add_buscar_js();
    //gemini-2014
    $output='';
    $output.=hontza_define_page_actions();
    /*if(hontza_is_validar_page_header($name)){ 
        $output.=validar_page_header();
    }*/
    $output.=drupal_get_form('hontza_vigilancia_filter_form');       
    return $output;
}
function hontza_vigilancia_filter_form(){
    $form=array();
    $fecha_inicio=hontza_get_vigilancia_filter_value('vigilancia_fecha_inicio');
    $fecha_fin=hontza_get_vigilancia_filter_value('vigilancia_fecha_fin');
    $fs_title=t('Search');
    if(empty($fecha_inicio) && empty($fecha_fin)){
        //$fs_title=t('Filter by Date');
        $fs_title=t('Filter by date of download');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        //$fs_title=t('Filter by Date Activated');
        $fs_title=t('Filter by date of download activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));        
    //$fecha_inicio=array('Y'=>2013,'n'=>10,'j'=>21);
    $form['file_buscar_fs']['vigilancia_fecha_inicio']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			//'#title'=>t('Start date'),
                        '#title'=>t('From'),    
			'#default_value'=>  $fecha_inicio);
    $form['file_buscar_fs']['vigilancia_fecha_fin']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			//'#title'=>t('End date'),
                        '#title'=>t('To'),    
			'#default_value'=>$fecha_fin);
    
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div class="vigilancia_filter_buttons">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form; 
}
function hontza_get_vigilancia_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'vigilancia_filter');
}
function hontza_vigilancia_filter_form_submit($form,&$form_state){
    $key='vigilancia_filter';
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=hontza_define_vigilancia_filter_fields();
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
function hontza_define_vigilancia_filter_fields(){
    $result=array('vigilancia_fecha_inicio','vigilancia_fecha_fin');
    return $result;
}
function hontza_get_vigilancia_where_filter(){
    return hontza_get_usuarios_acceso_where_time('node.created','vigilancia_filter','vigilancia_fecha_inicio','vigilancia_fecha_fin');    
}
function hontza_og_vigilancia_rechazados_pre_execute(&$view){        
   $my_grupo=og_get_group_context();
   if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){ 
        $where=array();
        $where[]="1";
        $where[]="node.status <> 0";
        $where[]="og_ancestry.group_nid = ".$my_grupo->nid;;
        $where[]="node.type in ('item', 'noticia')";
        $where[]=hontza_get_vigilancia_where_filter();
        //
        //$order_by="node_comment_statistics_comment_count DESC, flag_content_node_timestamp DESC";
        $order_by="node_created DESC";
        $sql="SELECT DISTINCT(node.nid) AS nid,
        node_comment_statistics.comment_count AS node_comment_statistics_comment_count,
        flag_content_node.timestamp AS flag_content_node_timestamp,
        node.created AS node_created
        FROM {node} node 
        INNER JOIN {flag_content} flag_content_node ON node.nid = flag_content_node.content_id AND flag_content_node.fid = 3 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid 
        WHERE ".implode(" AND ",$where)." 
        GROUP BY nid 
        ORDER BY ".$order_by;
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;
   }  
}
function hontza_is_sareko_id_red($with_lokala=1,$is_alerta=0){
    //return 0;
    //intelsat-2016
    if(hontza_registrar_is_sareko_id_red_desactivado()){
        return 0;
    }
    $sareko_id_array=hontza_define_red_sareko_id_array($with_lokala);
    if(!empty($sareko_id_array)){
        foreach($sareko_id_array as $i=>$sareko_id){
            if(hontza_is_sareko_id($sareko_id)){
                return 1;
            }
        }
    }
    //gemini-2014
    if(!$is_alerta){
        if(red_is_network_sareko_id()){
            return 1;
        }
    }    
    //
    return 0;
}
function hontza_user_access_gestion_red(){
    global $user;
    if(isset($user->uid) && !empty($user->uid) && $user->uid==1){
        return 1;
    }
    return 0;
}
function hontza_is_empty_feeds_source($job){
    if($job['callback']=='feeds_source_import'){
        $feed_nid=$job['id'];
        $row=hontza_get_feeds_source($feed_nid);
        if(isset($row->source) && !empty($row->source)){            
            return 0;
        }
        return 1;
    }
    return 0;
}
function hontza_define_job_param($nid){
    $result=array();
    $result['callback']='feeds_source_import';
    $result['id']=$nid;
    return $result;
}
function hontza_no_existe_feed_source_callback(){
    $html=array();
    $html[]='<p>'.t('Feed url is empty').'</p>';
    $html[]='<div>'.l(t('Return'),$_REQUEST['destination']).'</div>';
    return implode('',$html);
}