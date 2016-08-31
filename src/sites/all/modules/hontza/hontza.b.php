<?php
function beste_hontza_perm($result_in){
    $result=$result_in;
    $result[]='Ver repasar_canal_fuente_nid';
    $result[]='Ver show_validar_canal';
    $result[]='Ver probar_full_text_rss';
    $result[]='Ver vaciar_taxonomy';
    $result[]='Ver show_validar_fuente_rss';
    $result[]='Ver copiar_fuentes_servidor';
    $result[]='Ver usuarios_acceso';
    $result[]='Enlazar debate';
    $result[]='Enlazar wiki';
    $result[]='Cambiar consulta canales';
    $result[]='access activar_actualizacion_canal';
    //intelsat-2014     
    //return $result;
    return hontza_solr_perm($result);
}
function beste_menu_items($items_in){
    $items=$items_in;
    
    $items['repasar_canal_fuente_nid'] = array(
    'title' => 'repasar_canal_fuente_nid',
    'page callback' => 'repasar_canal_fuente_nid_callback',
    'access arguments' => array('Ver repasar_canal_fuente_nid'),
  );

     $items['show_validar_canal'] = array(
    'title' => t('Show RSS'),
    'page callback' => 'show_validar_canal_callback',
    'access arguments' => array('Ver show_validar_canal'),
  );

     $items['probar_full_text_rss'] = array(
    'title' => 'probar_full_text_rss',
    'page callback' => 'probar_full_text_rss_callback',
    'access arguments' => array('Ver probar_full_text_rss'),
  );

      $items['vaciar_taxonomy'] = array(
    'title' => 'vaciar_taxonomy',
    'page callback' => 'vaciar_taxonomy_callback',
    'access arguments' => array('Ver vaciar_taxonomy'),
  );
      
      $items['show_validar_fuente_rss/%'] = array(
    'title' => t('Show RSS'),
    'page callback' => 'show_validar_fuente_rss_callback',
    'access arguments' => array('Ver show_validar_fuente_rss'),
  );

  //gemini-2013    
   $items['copiar_fuentes_servidor'] = array(
    'title'=>'Copy server sources',
    'page callback' => 'copiar_fuentes_servidor_callback',
    //'access callback' => TRUE,
    'access arguments' => array('Ver copiar_fuentes_servidor'),   
  );
    $items['descargar_fuentes_servidor'] = array(
    'title'=>t('Download Server Sources'),
    'page callback' => 'descargar_fuentes_servidor_callback',
    'access callback' => TRUE,
    //'access arguments' => array('Ver descargar_fuentes_servidor'),   
  );
    $items['fuentes_servidor_copiadas']=array(
    'title'=>t('Sources Copied to Server'),
    'page callback' => 'fuentes_servidor_copiadas_callback',
    'access callback' => TRUE,    
    );
    $items['registrar_red_hontza']=array(
    'title'=>t('Register'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('registrar_red_hontza_form'),
    //'type' => MENU_CALLBACK,    
    'access callback' => TRUE,    
    );
    $items['save_registrar_red_hontza']=array(
    'title'=>t('Register'),
    'page callback' => 'save_registrar_red_hontza_callback',
    'access callback' => TRUE,  
    );
    $items['esta_registrado_by_key/%']=array(
    'title'=>t('Is registered'),
    'page callback' => 'esta_registrado_by_key_callback',
    'access callback' => TRUE,  
    );
     $items['user-gestion/borrar-usuarios-grupos']=array(
    'title'=>t('Remove users to groups'),
    'page callback' => 'borrar_usuarios_grupos_callback',
    'access callback' =>'gestion_grupos_access',  
    );
    $items['user-gestion/borrar_usuario_grupo/%']=array(
    'title'=>t('Remove user to group'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('borrar_usuario_grupo_form'),    
    'access callback' =>'gestion_grupos_access',  
    );
    $items['user/%/edit/my_groups']=array(
    'title'=>t('Groups'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('user_my_groups_form'),    
    'access callback' =>'user_my_groups_access',  
    );
     $items['node/%/validar_canal']=array(
    'title'=>t('Validate'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_validar_canal_form'),    
    'access arguments' => array('Validar canal'), 
    );
    $items['usuarios_acceso/%'] = array(
    'page callback' => 'usuarios_acceso_callback',
    'page arguments' => array(1),
    'access arguments' => array('Ver usuarios_acceso'),
  );
   $items['fix_eval_options'] = array(
    'title'=>'Fix Eval Options',
    'page callback' => 'hontza_fix_eval_options_callback',
    'access arguments' => array('root'),   
  );
  $items['node/%/enlazar_debate'] = array(
    'title'=>t('Discuss'),
    'page callback' => 'hontza_node_enlazar_debate_callback',
    'access arguments' => array('Enlazar debate'),
  );
  $items['node/%/confirm_enlazar_debate/%']=array(
    'title'=>t('Discuss'),
    //'page callback' => 'drupal_get_form',    
    //'page arguments'   => array('hontza_confirm_enlazar_debate_form'),
    'page callback' => 'hontza_confirm_enlazar_debate_callback',  
    'access arguments' => array('Enlazar debate'),
    );
  $items['node/%/enlaces_debate'] = array(
    'title'=>t('Discuss'),
    'page callback' => 'hontza_enlaces_debate_callback',
    'access arguments' => array('Enlazar debate'),
  );
  $items['node/%/origenes_debate'] = array(
    'title'=>t('Discuss'),
    'page callback' => 'hontza_origenes_debate_callback',
    'access arguments' => array('Enlazar debate'),
  );
  $items['node/%/enlazar_wiki'] = array(
    'title'=>t('Collaborate'),
    'page callback' => 'hontza_node_enlazar_wiki_callback',
    'access arguments' => array('Enlazar wiki'),
  );
  $items['node/%/confirm_enlazar_wiki/%']=array(
    'title'=>t('Collaborate'),
    //'page callback' => 'drupal_get_form',    
    //'page arguments'   => array('hontza_confirm_enlazar_wiki_form'),
    'page callback' => 'hontza_confirm_enlazar_wiki_callback',  
    'access arguments' => array('Enlazar wiki'),
    );
  $items['node/%/enlaces_wiki'] = array(
    'title'=>t('Collaborate'),
    'page callback' => 'hontza_enlaces_wiki_callback',
    'access arguments' => array('Enlazar wiki'),
  );
  $items['node/%/origenes_wiki'] = array(
    'title'=>t('Collaborate'),
    'page callback' => 'hontza_origenes_wiki_callback',
    'access arguments' => array('Enlazar wiki'),
  );
  $items['hontza_user_login_custom'] = array(
    'title'=>t('Users'),
    'page callback' => 'hontza_user_login_custom',  
    'access callback' => TRUE,
  );
  $items['gestion/usuarios'] = array(
    'title'=>t('USERS'),
    'page callback' => 'gestion_usuarios_callback',  
    'access arguments' => array('administer users'),
  );
  $items['gestion/hontza_block_user_confirm'] = array(
    'title'=>t('Are you sure you want to Block selected users on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_block_user_confirm_form'),  
    'access arguments' => array('administer users'),
  );
  $items['gestion/hontza_unblock_user_confirm'] = array(
    'title'=>t('Are you sure you want to Unblock selected users on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_unblock_user_confirm_form'),  
    'access arguments' => array('administer users'),
  );  
  $items['gestion/hontza_modify_user_roles_confirm'] = array(
    'title'=>t('Are you sure you want to Modify user roles on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_modify_user_roles_confirm_form'),  
    'access arguments' => array('administer users'),
  );  
  $items['user-gestion/grupos/propios'] = array(
    'title'=>t('Management of Groups'),
    'page callback' => 'hontza_gestion_grupos_propios_callback',  
    'access callback' => 'hontza_gestion_grupos_propios_access',
  );
  $items['user-gestion/grupos/hontza_publish_grupo_confirm'] = array(
    'title'=>t('Are you sure you want to Publish posts on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_publish_grupo_confirm_form'),  
    'access arguments' => array('hontza_gestion_grupos_propios_access'),
  );
  $items['user-gestion/grupos/hontza_unpublish_grupo_confirm'] = array(
    'title'=>t('Are you sure you want to Unpublish posts on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_unpublish_grupo_confirm_form'),  
    'access arguments' => array('hontza_gestion_grupos_propios_access'),
  );
  $items['user-gestion/grupos/hontza_delete_grupo_confirm'] = array(
    'title'=>t('Are you sure you want to Delete groups on selected rows?'),
    'page callback' => 'drupal_get_form',    
    'page arguments'   => array('hontza_delete_grupo_confirm_form'),  
    'access callback' => 'hontza_gestion_grupos_propios_access',
  );
  $items['cambiar_consulta_canales_block'] = array(
    'title'=>'Change channels query',
    'page callback' => 'hontza_cambiar_consulta_canales_block_callback',
    'access arguments' => array('Cambiar consulta canales'),
  );
  $items['destacar_ajax/%'] = array(
    'title' => t('Events'),
    'page callback' => 'destacar_ajax_callback',
    'access arguments' => array('Ver destacar_item'),  
  );
  $items['no_destacar_ajax/%'] = array(
    'title' => t('Events'),
    'page callback' => 'no_destacar_ajax_callback',
    'access arguments' => array('Ver no_destacar_item'),  
  );
  $items['destacar_noticia_usuario_ajax/%'] = array(
    'title' => t('Events'),
    'page callback' => 'destacar_noticia_usuario_ajax_callback',
    'access arguments' => array('Ver destacar_noticia_usuario'),  
  );
  $items['no_destacar_noticia_usuario_ajax/%'] = array(
    'title' => t('Events'),
    'page callback' => 'no_destacar_noticia_usuario_ajax_callback',
    'access arguments' => array('Ver no_destacar_noticia_usuario'), 
  );
   $items['no_existe_enlace_origen_wiki'] = array(
    'title'=>t('Origin of Collaborative Document'),
    'page callback' => 'hontza_no_existe_enlace_origen_wiki_callback',
    'access arguments' => array('Enlazar wiki'),
  );
   $items['no_existe_enlace_origen_noticia_usuario'] = array(
    'title'=>t('Links of User News'),
    'page callback' => 'hontza_no_existe_enlace_origen_noticia_usuario_callback',
    'access arguments' => array('access content'),
  );
   $items['no_existe_feed_source'] = array(
    'title'=>t('Update channel'),
    'page callback' => 'hontza_no_existe_feed_source_callback',
    'access arguments' => array('access content'),
  );
   $items['download_blanco'] = array(
    'title'=>'Download blanco',
    'page callback' => 'hontza_download_blanco_callback',
    'access arguments' => array('root'),
  );
   $items['download_sources_txt'] = array(
    'title'=>'Download Sources txt',
    'page callback' => 'hontza_download_sources_txt_callback',
    'access arguments' => array('root'),
  );
    $items['download_channels_txt'] = array(
    'title'=>'Download Channels txt',
    'page callback' => 'hontza_download_channels_txt_callback',
    'access arguments' => array('root'),
  );
    $items['node/%/unlink_wiki/%']=array(
    'title'=>t('Delete collaboration link'),
    'page callback' => 'hontza_unlink_wiki_callback',  
    'access arguments' => array('Enlazar wiki'),
    );
    $items['node/%/unlink_debate/%']=array(
    'title'=>t('Delete discussion link'),
    'page callback' => 'hontza_unlink_debate_callback',  
    'access arguments' => array('Enlazar debate'),
    );
    $items['activar_actualizacion_subdominio']=array(
    'title'=>t('Active channels'),
    'page callback' => 'drupal_get_form',
    'page arguments'   => array('hontza_activar_actualizacion_subdominio_form'),    
    'access arguments' => array('root'),
    );
    $items['fix_uniq_item']=array(
    'title'=>t('Script-Unique ID'),
    'page callback' => 'hontza_fix_uniq_item_callback',  
    'access arguments' => array('root'),
    );
    $items['activar_actualizacion_canal']=array(
    'title'=>t('Activated channel'),
    /*'page callback' => 'drupal_get_form',
    'page arguments'   => array('hontza_activar_actualizacion_canal_form'),*/
    'page callback' => 'hontza_activar_actualizacion_canal_callback',    
    'access arguments' => array('access activar_actualizacion_canal'),
    );
    $items['fix_canal_active_refresh']=array(
    'title'=>t('Script-Active channel'),
    'page callback' => 'hontza_fix_canal_active_refresh_callback',  
    'access arguments' => array('root'),
    );
    $items['hay_repetidos']=array(
    'title'=>t('Repeat'),
    'page callback' => 'hontza_hay_repetidos_callback',  
    'access arguments' => array('root'),
    );
    $items['vigilancia/validar_pagina_ajax']=array(
    'title'=>t("Validate this page's news"),
    'page callback' => 'hontza_validar_pagina_ajax_callback',  
    'access arguments' => array('access content'),
    );
    $items['vigilancia/rechazar_pagina_ajax']=array(
    'title'=>t("Reject this page's news"),
    'page callback' => 'hontza_rechazar_pagina_ajax_callback',  
    'access arguments' => array('access content'),
    );
    $items['vigilancia/activar_actualizacion_canal_ajax']=array(                
    'title'=>t('Activated channel'),
    'page callback' => 'hontza_activar_actualizacion_canal_ajax_callback',  
    'access arguments' => array('access content'),
    );
    $items['canal-usuarios-title/%/edit']=array(                
    'title'=>t('Edit Channel Title'),
    'page callback' => 'drupal_get_form',
    'page arguments'=>array('hontza_canal_usuarios_title_form'),    
    'access callback' => 'hontza_canal_usuarios_title_access',
    );
    $items['hontza_solr_simulatu_facet']=array(                
    'title'=>t('Edit Channel Title'),
    'page callback' => 'hontza_solr_simulatu_facet_callback',
    'access arguments' => array('root'),
    );
     $items['hontza_simular_repasar_canal_pipe_id']=array(                
    'title'=>'Channel',
    'page callback' => 'hontza_simular_repasar_canal_pipe_id_callback',
    'access arguments' => array('root'),
    );
     $items['hontza/validar_cambio_fuente_pipe_id']=array(
         'title'=>'Channel',
         'page callback' => 'red_funciones_validar_cambio_fuente_pipe_id_callback',
         'access arguments' => array('access content'),
     );
    //intelsat-2014 
    //return $items;
    return hontza_solr_menu_items($items);
}
function repasar_canal_fuente_nid_callback(){
    $result=array();
    $sql='SELECT vid,nid,field_nombrefuente_canal_value
    FROM content_field_nombrefuente_canal
    WHERE 1
    ORDER BY vid,nid ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    if(count($result)>0){
        foreach($result as $i=>$row){
            $node_list=get_node_list_by_title($row->field_nombrefuente_canal_value);
            $num=count($node_list);
            if($num>0){
                if($num==1){
                    //save_canal_fuente_nid_by_row($row,$node_list[0]);
                    //print 'num===='.$num.'===='.$row->field_nombrefuente_canal_value.'<BR>';
                }else if($num>1){
                    $canal=node_load($row->nid);
                    $beste_list=mirar_grupos_canal_fuente($canal,$node_list);
                    $num_beste=count($beste_list);
                    if($num_beste>0){
                        if($num_beste==1){
                            //print 'nid='.$row->nid.'<BR>';
                            //print 'num_beste===='.$num_beste.'===='.$row->field_nombrefuente_canal_value.'<BR>';
                            //save_canal_fuente_nid_by_row($row,$beste_list[0]);
                        }else{
                            print 'num_beste===='.$num_beste.'===='.$row->field_nombrefuente_canal_value.'<BR>';
                        }
                    }else{
                        print 'num_beste=zero da<BR>';
                    }
                }
            }else{
                //print 'num===='.$num.'===='.$row->field_nombrefuente_canal_value.'<BR>';
            }
        }
    }
    return 'OK';
}
function get_node_list_by_title($title){
    $where=array();
    $where[]='n.title="'.$title.'"';
    $where[]='n.type IN("supercanal","fuentedapper","fuentehtml")';
    //
    $result=array();
    $sql='SELECT n.* FROM {node} n WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $result[]=$row;
        }
    }
    return $result;
}
function save_canal_fuente_nid_by_row($row,$fuente){
    $my_list=get_canal_fuente_nid_list($row);
    //
    if(count($my_list)>0){
        foreach($my_list as $i=>$r){
            $where=array();
            $where[]='nid='.$row->nid;
            $where[]='vid='.$row->vid;
            $sql='UPDATE content_field_nid_fuente_canal SET field_nid_fuente_canal_value='.$fuente->nid .' WHERE '.implode(' AND ',$where);
            db_query($sql);
            print $sql.'<BR>';
        }
    }else{
        $sql='INSERT INTO content_field_nid_fuente_canal(vid,nid,field_nid_fuente_canal_value) VALUES('.$row->vid.','.$row->nid.','.$fuente->nid.')';
        db_query($sql);
        print $sql.'<BR>';
    }
}
function get_canal_fuente_nid_list($row){
    $result=array();
    $where=array();
    $where[]='nid='.$row->nid;
    $where[]='vid='.$row->vid;
    //
    $sql='SELECT * FROM content_field_nid_fuente_canal WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function mirar_grupos_canal_fuente($canal,$node_list){
    $result=array();
    if(count($node_list)>0){
        foreach($node_list as $i=>$row){
            $node=node_load($row->nid);
            $og_groups=$canal->og_groups;
            if(!empty($og_groups)){
               $my_array=array_values($og_groups);
               $group_nid_array=array_values($node->og_groups);
               if(count($my_array)>0){
                   foreach($my_array as $i=>$group_nid){
                       if(in_array($group_nid,$group_nid_array)){
                            $result[]=$row;
                       }
                   }
               }
            }
        }
    }
    return $result;
}
function show_validar_canal_callback(){
    return '';
    $html=array();
    $api_form=create_canal_api_validate_form();
    $api_form='';
    $html[]=$api_form;
    $html[]='<div class="ybr">';
    if(isset($_SESSION['url_show_validar_canal']) && !empty($_SESSION['url_show_validar_canal'])){
       if(isset($_SESSION['url_show_validar_canal']['url']) && !empty($_SESSION['url_show_validar_canal']['url'])){
            $is_alchemy=is_alchemy_by_yahoo($_SESSION['url_show_validar_canal']['fuente_nid']);
            $is_opencalais=is_opencalais_by_yahoo($_SESSION['url_show_validar_canal']['fuente_nid']);
            $is_full_text_rss=is_full_text_rss_by_yahoo($_SESSION['url_show_validar_canal']['fuente_nid']);
            //
            //print $_SESSION['url_show_validar_canal']['url'];exit();
            if($is_full_text_rss){
                $my_url=get_url_full_text_rss($_SESSION['url_show_validar_canal']['url']);
                $content=file_get_contents($my_url);                
            }else{
                $content=file_get_contents($_SESSION['url_show_validar_canal']['url']);
            }
            //
              $xml = new SimpleXMLElement($content);
              $sets = $xml->channel;
              $all = sizeof($sets);
              for ($i=0; $i<$all; $i++) {
                $r = $sets[$i];
                  foreach ($r->item as $elemento) {
                    //if(is_filtrar_numero_palabras($elemento->title,$is_import_html,$my_row,$source)){
                        $html[]=mostrar_rss_html($elemento,$is_alchemy,$is_opencalais,$is_full_text_rss);
                    //}
                  }
              }
        }
    }
    $html[]='</div>';
    $html[]=$api_form;
    return implode('',$html);
}
function copiar_fuentes_servidor_callback(){
    //return 'function desactivada';
    global $user;
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $url='http://online.hontza.es/descargar_fuentes_servidor?my_param='.time();
        $content=file_get_contents($url);
        $result=unserialize($content);
        db_query('DELETE FROM fuentes_servidor WHERE 1');
        if(count($result)>0){            
            foreach($result as $i=>$row){
                /*$node=$row;
                unset($node->nid);
                unset($node->vid);
                $node->uid=$user->uid;
                if(isset($node->og_groups_both)){
                    unset($node->og_groups_both);
                }
                if(isset($node->og_groups)){
                    unset($node->og_groups);
                }
                $node->og_groups=array();
                $node->og_groups[$my_grupo->nid]=$my_grupo->nid;
                node_save($node);
                break;*/
                $fuentes_servidor_row=get_fuentes_servidor_row($row->nid);
                if(empty($fuentes_servidor_row)){
                    db_query('INSERT INTO fuentes_servidor(title,nid,vid,value) VALUES("%s",%d,%d,"%s")',$row->title,$row->nid,$row->vid,serialize($row));
                }else{
                    db_query('UPDATE fuentes_servidor SET title="%s",value="%s",vid=%d WHERE nid=%d',$row->title,serialize($row),$row->vid,$row->nid);
                }    
            }
            return drupal_get_form('copiar_fuentes_servidor_form');
        }
    }else{
        return t('No group selected');
    }    
    //return 'OK';
    return t('There are no public sources');
}
function descargar_fuentes_servidor_callback(){
    //return 'function desactivada';
    $sql='SELECT * 
    FROM {node} n 
    LEFT JOIN {content_field_tematica_gupos} p ON (n.nid=p.nid AND n.vid=p.vid)
    WHERE n.type="supercanal" AND p.field_tematica_gupos_value="publico"';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        $result[]=$node;        
    }
    print serialize($result);
    exit();
}
function copiar_fuentes_servidor_form(){
    $form=array();
    $fuente_list=get_fuentes_servidor_list();        
    if(count($fuente_list)>0){
        $form['copiar_fuente']['#tree']=true;
        foreach($fuente_list as $i=>$row){
            //$form['copiar_fuente_'.$row->id]=array(
            $form['copiar_fuente'][$row->nid]=array(
                '#type'=>'checkbox',
                '#title'=>$row->title,
                //'#name'=>'copiar_fuente['.$row->nid.']',
                //'#return_value'=>$row->nid,
                //'#default_value'=>1,
                //'#required'=>TRUE,
                );
        }
        $form['my_submit']=array('#type'=>'submit','#value'=>t('Save'));
    }
    return $form;
}
function get_fuentes_servidor_list($id='',$nid='',$vid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($id)){
        $where[]='id='.$id;
    }
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    if(!empty($vid)){
        $where[]='vid='.$vid;
    }
    $sql='SELECT * FROM fuentes_servidor WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function copiar_fuentes_servidor_form_submit($form,&$form_state){
    $result=0;
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if(isset($form_state['values']['copiar_fuente']) && !empty($form_state['values']['copiar_fuente'])){
            foreach($form_state['values']['copiar_fuente'] as $nid=>$value){
                if(!empty($value)){
                    $row=get_fuentes_servidor_row($nid);
                    if(isset($row->nid) && !empty($row->nid)){
                        $node=unserialize($row->value);
                        node_save_fuente_servidor($node);
                        $result=1;                              
                    }                                      
                }
            }            
        }
    }
    if(!empty($result)){
        drupal_goto('fuentes_servidor_copiadas');
    }
}
function get_fuentes_servidor_row($nid){
    $result=get_fuentes_servidor_list('', $nid,'');
    if(count($result)>0){
        return $result[0];
    }
    return $result;
}
function node_save_fuente_servidor($node_in){
                $node=$node_in;
                //if(!existe_fuente_by_title($node->title)){
                    unset($node->nid);
                    unset($node->vid);
                    $node->uid=$user->uid;
                    if(isset($node->og_groups_both)){
                        unset($node->og_groups_both);
                    }
                    if(isset($node->og_groups)){
                        unset($node->og_groups);
                    }
                    $node->og_groups=array();
                    $node->og_groups[$my_grupo->nid]=$my_grupo->nid;
                    node_save($node);
                //}        
}
function fuentes_servidor_copiadas_callback(){
    $html=array();
    $html[]='<p>'.t('Sources Copied to Server').'</p>';
    $html[]=l(t('Return'),'copiar_fuentes_servidor');
    return implode('',$html);
}
function existe_fuente_by_title($title){
    $res=db_query('SELECT * FROM {node} n WHERE title="%s"',$title);
    while($row=db_fetch_object($res)){
        return true;
    }
    return false;
}
function registrar_red_hontza_form(){
    /*
    Organización /Organisation
Sitio web /Website
Persona de Contacto /Contact person
Correo electrónico /email
Pais /Country

Logo? (duda ... en la pagina de http://www.hontza.es/es/node/82 hay logos)*/
    
    //echo print_r($_SERVER,1);
    
    $texto_ingles='<p>Free Register in Hontza Network to automatically download
and synchronize Hontza\'s official information sources! Your organisation will appear in <a href="http://www.hontza.es/es/red_hontza" target="_blank">http://www.hontza.es/es/red_hontza</a></p>';

    
    
    $form=array();
    $registrados=  get_registrados_by_key(arg(1));
    if(count($registrados)>0){
        return $form;
    }
    //
    $form['#attributes'] = array('enctype' => "multipart/form-data");
    $form['#action']='http://online.hontza.es/save_registrar_red_hontza';
    $form['registrado_key']=array('#type'=>'hidden',
        '#value'=>arg(1));
    $form['texto']=array('#value'=>'<p>Inscríbete gratis en la Red Hontza, para que se descarguen y se
sincronicen automáticamente las fuentes de información oficiales de Hontza! Tras la aprobación del administrador de Hontza Online,
tu organización aparecerá listada en <a href="http://www.hontza.es/es/red_hontza"" target="_blank">http://www.hontza.es/es/node/red_hontza</a></p>'.$texto_ingles);    
    $form['organizacion']=array('#type'=>'textfield',
        '#title'=>get_red_hontza_title('organizacion'));
    $form['sitio_web']=array('#type'=>'textfield',
        '#title'=>get_red_hontza_title('sitio_web'));
    $form['persona_contacto']=array('#type'=>'textfield',
        '#title'=>get_red_hontza_title('persona_contacto'));
    $form['email']=array('#type'=>'textfield',
        '#title'=>get_red_hontza_title('email'));
    $form['pais']=array('#type'=>'textfield',
        '#title'=>get_red_hontza_title('pais'));
    $form['logo']=array('#type'=>'file',
        '#title'=>get_red_hontza_title('logo'));    
    $form['actions']['submit']=array('#type'=>'submit','#value'=>'Enviar/Send','#name'=>'enviar_registrar_red_hontza_submit');
    
    return $form;
}
function save_registrar_red_hontza_callback(){
    //$fields=array('organizacion','sitio_web','persona_contacto','email','pais');
    $error_fields=array();
    if(validate_registrar_red_hontza($error_fields)){
        save_registrar_red_hontza_row();
        $html[]='OK';
        $html[]='<p><a href="http://www.hontza.es/es/red_hontza" target="_blank">http://www.hontza.es/es/red_hontza</a></p>';
    }else{
        if(count($error_fields)>0){
            foreach($error_fields as $i=>$f){
                $html[]='<p style="color:red;">'.get_red_hontza_title($f,0).' es requerido'.' / '.get_red_hontza_title($f,1).' is required</p>';
            }
        }
        $html[]=l('Volver/Return','registrar_red_hontza');
    }    
    return implode('',$html);
}
function define_registrar_red_hontza_field_titles(){
    $result=array();
    $result['organizacion']='Organización/Organisation';
    $result['sitio_web']='Sitio web/Website';
    $result['persona_contacto']='Persona de Contacto/Contact person';
    $result['email']='Correo electrónico/Email';
    $result['pais']='Pais/Country';
    $result['logo']='Logo';
    //
    return $result;
}
function get_red_hontza_title($field,$ind=-1){
    $fields_array=define_registrar_red_hontza_field_titles();
    if(isset($fields_array[$field])){
        if($ind>=0){
            $my_array=explode('/',$fields_array[$field]);
            if(isset($my_array[$ind])){
                return $my_array[$ind];
            }
        }else{
            return $fields_array[$field];
        }    
    }
    return '';
}
function validate_registrar_red_hontza(&$error_fields){
    $result=1;
    $fields=array('organizacion','sitio_web','persona_contacto','email','pais','logo');
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if($f=='logo'){
                if(!is_logo_received()){
                    $result=0;
                    $error_fields[]=$f;
                }
            }else{
                if(isset($_REQUEST[$f]) && !empty($_REQUEST[$f])){
                    //
                }else{
                    $result=0;
                    $error_fields[]=$f;
                }
            }
        }
    }
    return $result;
}
function save_registrar_red_hontza_row(){
    $organizacion=$_REQUEST['organizacion'];
    $sitio_web=$_REQUEST['sitio_web'];
    $persona_contacto=$_REQUEST['persona_contacto'];
    $email=$_REQUEST['email'];
    $pais=$_REQUEST['pais'];
    $fecha=date('Y-m-d H:i:s');
    //$registrado_key=leer_desde_registrado_txt($path);
    $registrado_key=$_REQUEST['registrado_key'];
    //
    $registrados=get_registrados_by_key($registrado_key);
    //print $registrado_key;exit();
    if(count($registrados)>0){
        //
    }else{
        $sql='INSERT INTO registrados_red_hontza(organizacion,sitio_web,persona_contacto,email,pais,fecha,registrado_key)  
        VALUES("'.$organizacion.'","'.$sitio_web.'","'.$persona_contacto.'","'.$email.'","'.$pais.'","'.$fecha.'","'.$registrado_key.'")';
        db_query($sql);
        $id=db_last_insert_id('registrados_red_hontza','id');
        red_logo_save($id);
    }    
}
function esta_registrado_red_hontza($is_file_get_contents=0){
    //
    $root_dir='./sites/default/files/';
    if(is_dir($root_dir.'registrado')){
        
    }else{
        mkdir($root_dir.'registrado');        
    }
    //
    $registrado_key='';
    if(is_file($root_dir.'registrado/registrado.txt')){        
        $registrado_key=leer_desde_registrado_txt($root_dir.'registrado/registrado.txt');        
    }else{
        $f=fopen($root_dir.'registrado/registrado.txt','w');
        if($f){
            $registrado_key=create_registrado_key();
            fputs($f,$registrado_key);
            fclose($f);
        }
    }
    //
    //intelsat-2016
    //return 1;
    if($is_file_get_contents){
        $content=file_get_contents('http://online.hontza.es/esta_registrado_by_key/'.$registrado_key);
        $content=trim($content);
        if(!empty($content)){
            //intelsat-2016
            //return 1;
            return $registrado_key;
        }
    }else{
        $registrados=get_registrados_by_key($registrado_key);
        if(count($registrados)>0){
            //echo print_r($registrados,1);
            //return 1;
            return $registrado_key;
        }
    }
    //
    return 0;
}
function create_registrado_key(){
    $result=time();    
    return $result;
}
function leer_desde_registrado_txt($path_in=''){
    $root_dir='./sites/default/files/';
    if(empty($path_in)){            
        $path=$root_dir.'registrado/registrado.txt';
    }else{
        $path=$path_in;
    }
    //
    $result='';
    $handle = @fopen($path, "r");
    if ($handle) {        
        while (($buffer = fgets($handle, 4096)) !== false) {
            $result=trim($buffer);
            break;
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    return $result;
}
function get_registrados_by_key($registrado_key){
    $result=array();
    
    if(!empty($registrado_key)){
        //
        $where=array();
        $where[]='1';
        $where[]='rh.registrado_key="'.$registrado_key.'"';
        //
        $sql='SELECT rh.* FROM registrados_red_hontza rh WHERE '.implode(' AND ',$where);
        //print $sql;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            //echo print_r($row,1);
            $result[]=$row;
        }
    }    
    //
    return $result;
}
function red_logo_save($id){
    if(isset($_FILES) && isset($_FILES['files'])){
        if(!file_exists($_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/logos_red')){
            mkdir($_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/logos_red');            
        }
        //
        foreach ($_FILES["files"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["files"]["tmp_name"][$key];
                $name = $id.my_get_extension_red_logo($_FILES["files"]["name"][$key]);
                move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/logos_red/'.$name);
                $sql='UPDATE registrados_red_hontza SET logo="'.$name.'" WHERE id='.$id;
                db_query($sql);
                break;                
            }
        } 
    }   
}
function my_get_extension_red_logo($name){
    $my_array=explode('.',$name);
    $num=count($my_array);
    if($num>0){
        return '.'.$my_array[$num-1];
    }
    return '';
}
function get_register_red_hontza_content(){
    global $user;
    global $base_url;
    //gemini-2014-registrado
    return '';
    $is_registrarse=esta_registrado_red_hontza(1);  
    //
    //$my_server_url='http://www.hontza.es/hontza3';
    //$my_server_url_array=array('http://www.hontza.es/hontza3','http://hontza.es/hontza3');
    $my_server_url_array=array('http://online.hontza.es');
    //if($base_url==$my_server_url){
    if(in_array($base_url,$my_server_url_array)){
        $is_registrarse=1;    
    }
    //
    if(!$is_registrarse){
        if(isset($user->uid) && !empty($user->uid)){
            $is_registrarse=1;
        }
    }
    //
    $registrado_key='';
    if(empty($is_registrarse)){
       $root_dir='./sites/default/files/';
       if(is_file($root_dir.'registrado/registrado.txt')){        
            $registrado_key=leer_desde_registrado_txt($root_dir.'registrado/registrado.txt'); 
       }                 
    }
    //simulando
    //$is_registrarse=0;
    //
    if(!$is_registrarse){
        $output.='<p>Inscríbete gratis en la Red Hontza, para que se descarguen y se
sincronicen automáticamente las fuentes de información oficiales de Hontza! Tras la aprobación del administrador de Hontza Online, 
tu organización aparecerá listada en <a href="http://www.hontza.es/es/red_hontza" target="_blank">http://www.hontza.es/es/red_hontza</a></p>';
        $output.='<p>Free Register in Hontza Network to automatically download
and synchronize Hontza\'s official information sources! After approval from Hontza Online administrator, your organisation will appear in <a href="http://www.hontza.es/es/red_hontza" target="_blank">http://www.hontza.es/es/red_hontza</a></p>';

        $output.=l('Registrarse/Register','http://online.hontza.es/registrar_red_hontza/'.$registrado_key,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
        return $output;
    }
    return '';
}
function esta_registrado_by_key_callback(){
    $registrado_key=trim(arg(1));
    if(!empty($registrado_key)){
        $registrados=get_registrados_by_key($registrado_key);
        if(count($registrados)>0){
            print 'yes';
            exit();
        }
    }
    print '';
    exit();
}
function repase_http($s){
    $pos=strpos($s,'href="');
    if($pos===FALSE){
        return $s;
    }
    $s_temp=substr($s,$pos+strlen('href="'));
    $pos=strpos($s_temp,'"');
    if($pos===FALSE){
        return $s;
    }
    $url=substr($s_temp,0,$pos);
    $url=str_replace(' ','',$url);
    //
    $pos=strpos($s,'http://');
    if($pos===FALSE){
        return '<a href="http://'.$url.'" target="_blank">'.$url.'</a>';
    }else{
        return $s;
    }
    return $value;
}
function add_canales_sin_items($result_in){
    //global $user;
    $result=$result_in;
    
    /*if($user->uid!=1){
        return $result;
    }*/
    
    if(empty($result)){
        $html=array();
        $canal_list=get_canal_list_sin_items();
        if(count($canal_list)>0){
            foreach($canal_list as $i=>$canal){
                $html[]='<div class="views-summary views-summary-unformatted">';
                $html[]=l('aaaa','bbbb');
                $html[]='</div>';
            }
        }
        $result=implode('',$html);        
        return $result;
    }
    
    return $result;
}
function get_canal_list_sin_items(){
    $result=array();
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        $where[]='n.type IN ("canal_de_supercanal","canal_de_yql")';
        $where[]='og.group_nid='.$my_grupo->nid;
        //
        $sql='SELECT n.nid AS canal_nid,n.title AS canal_title
        FROM {node} n
        LEFT JOIN {og_ancestry} og ON n.nid=og.nid
        WHERE '.implode(' AND ',$where);
        //
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
    }
    return $result;
}
function my_og_canales_dash_pre_execute(&$view){
    /*global $user;
    if($user->uid==1){*/
        $sql=$view->build_info['query'];
        $result=get_query_result($sql);
        if(empty($result)){            
            $my_grupo=og_get_group_context();
            //print $my_grupo->nid;
            if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $where=array();
                $where[]='1';
                $where[]='n.type IN ("canal_de_supercanal","canal_de_yql")';
                $where[]='og.group_nid='.$my_grupo->nid;
                //
                //$sql='SELECT n.nid AS canal_nid,n.title AS canal_title
                $sql='SELECT n.nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid,
                n.title AS node_node_data_field_item_canal_reference_title,
                0 AS num_records
                FROM {node} n
                LEFT JOIN {og_ancestry} og ON n.nid=og.nid
                WHERE '.implode(' AND ',$where);
                //print $sql;
                $view->build_info['query']=$sql;
                $view->build_info['count_query']=$sql;
                //print $view->build_info['query'];                
            }
        }
         
    //}
}
function get_query_result($sql_in){
    $sql=$sql_in;
    $result=array();    
    //print $sql;exit();
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        //$sql=str_replace('***CURRENT_GID***',$my_grupo->nid,$sql);
        $sql='SELECT node_data_field_item_canal_reference.field_item_canal_reference_nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid, node_node_data_field_item_canal_reference.title AS node_node_data_field_item_canal_reference_title, COUNT(node.nid) AS num_records 
        FROM {node} node 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
        LEFT JOIN {node} node_node_data_field_item_canal_reference ON node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid 
        WHERE (node.type in ("item")) AND (og_ancestry.group_nid = '.$my_grupo->nid.') 
        GROUP BY node_node_data_field_item_canal_reference_title, node_data_field_item_canal_reference_field_item_canal_reference_nid ORDER BY node_node_data_field_item_canal_reference_title ASC';
    }
    //print $sql;exit();
    //
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
    
    return $result;
}
function is_user_node_administrador_de_grupo($node){
    global $user;
    //echo print_r($user,1);
    
    if($user->uid==1){
        return 1;
    }
    
    if(is_user_administrador_de_grupo()){
        if(is_user_in_node_grupo($node)){
            return 1;
        }
        return 0;
    }
    
    return 1;
}
function is_user_in_node_grupo($node){
    global $user;
    if(isset($node->og_groups) && !empty($node->og_groups)){
        $my_user=user_load($user->uid);
        //echo print_r($my_user,1);
        foreach($node->og_groups as $grupo_nid=>$v){
            if(in_user_groups($grupo_nid,$my_user)){
                return 1;
            }    
        }
    }
    return 0;
}
function repase_access_canal($node,$is_return=0){
    $is_access=0;
    //
    if(is_user_node_administrador_de_grupo($node)){
       $is_access=1;
    }
    /*
    //gemini-2014
    if(hontza_is_servidor_red_alerta()){
        if(red_servidor_is_grupo_shared()){
            $is_access=1;
        }
    }*/
    //
    if(!$is_access){
        if(!$is_return){
            drupal_access_denied();
            //drupal_goto('my_access_denied');
            exit();
        }
    }
    return $is_access;
}
function is_logo_received(){
    if(isset($_FILES) && isset($_FILES['files'])){
        foreach ($_FILES["files"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["files"]["tmp_name"][$key];
                /*$name = $id.my_get_extension_red_logo($_FILES["files"]["name"][$key]);
                move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/logos_red/'.$name);
                $sql='UPDATE registrados_red_hontza SET logo="'.$name.'" WHERE id='.$id;
                db_query($sql);*/
                if(file_exists($tmp_name)){
                    return 1;
                }
                break;                
            }
        } 
    }
    return 0;
}
function is_show_og_canales_busqueda_block(){
    //return 0;
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(is_vigilancia_left()){
            return 1;
        }
    }    
    return 0;
}
function get_grupo_name_by_node($nid){
    if(!empty($nid)){    
        $grupo=my_get_grupo($nid);
        if(isset($grupo->nid) && !empty($grupo->nid)){
            return my_get_purl_value($grupo->nid);
        }
    }
    return '';
}
function my_get_purl_row($nid){
    $where=array();
    $where[]='1';
    $where[]='p.provider="spaces_og"';
    $where[]='p.id='.$nid;
    $sql='SELECT p.* FROM {purl} p WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function my_get_purl_value($nid){
    $row=my_get_purl_row($nid);
    if(isset($row->value)){
        return $row->value;
    }
    return '';
}
function get_grupo_name_by_tid($tid){
    if(!empty($tid)){
        $grupo=get_grupo_by_tid($tid);
        if(isset($grupo->nid) && !empty($grupo->nid)){
            return my_get_purl_value($grupo->nid);
        }
    }
    return '';
}
function get_usuarios_creadores_y_administradores_grupo_options($with_admin_grupo=1,$with_creador_grupo=1,$with_empty=0,$group_nid='',$with_all_roles=0){
    global $user;
    $result=array();
    if($with_empty){
       $result[0]='';
    }
    $result[1]='admin';
    $grupo_nid_array=array();
    if(empty($group_nid)){
        $grupo_nid_array=get_usuario_grupo_nid_array();
    }else{
        $grupo_nid_array[]=$group_nid;
    }
    //
    $role_id_array=array();
    if($with_admin_grupo){
        $role_id_array[]=ADMINISTRADOR_DE_GRUPO;
    }    
    if($with_creador_grupo){
        $role_id_array[]=CREADOR;
        //intelsat-2015
        $role_id_array[]=ADMINISTRADOR;
    }
    if(empty($role_id_array) && !$with_all_roles){
        return $result;
    }
    //
    $where=array();
    $where[]='1';
    //gemini-2014
    $where[]='u.status=1';
    if(!$with_all_roles){
        $where[]='r.rid IN('.implode(',',$role_id_array).')';
    }    
    //
    /*AVISO::::$group_nid no es empty por ahora cuando lo llamanos desde la ficha del grupo,
    en el formulario de añadir grupo es empty y el admin puede ver los usuarios de todos los grupos en el input select
     *      */
     if($user->uid!=1 || !empty($group_nid)){
        if(count($grupo_nid_array)>0){
            $where[]='ou.nid IN('.implode(',',$grupo_nid_array).')';
        }else{
            return $result;
        }
    }
    //    
    $sql='SELECT u.* 
    FROM {users} u 
    LEFT JOIN {users_roles} r ON u.uid=r.uid 
    LEFT JOIN {og_uid} ou ON u.uid=ou.uid
    WHERE '.implode(' AND ',$where).' ORDER BY u.name ASC';
    //print $sql;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[$row->uid]=$row->name;
    }    
    return $result;
}
function on_repasar_node_uid_save(&$node){
    global $user;
    //if($node->type=='grupo'){
        //if(!isset($node->nid) || empty($node->nid)){
            if(!isset($node->uid) || empty($node->uid)){
                if(isset($user->uid) && !empty($user->uid)){
                    $node->uid=$user->uid;
                }
            }
        //}
    //}
}
function add_usuario_grupo($nid,$uid){
    if(!empty($nid) && !empty($uid)){
        $my_list=get_og_uid_list($nid,$uid);
        if(count($my_list)>0){
            //
        }else{
            //print $uid.'===='.$nid;exit();
            my_insert_user_grupo($uid, $nid);
        }
    }
}
function get_og_uid_list($nid='',$uid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($nid)){
        $where[]='o.nid='.$nid;
    }
    //
    if(!empty($uid)){
        $where[]='o.uid='.$uid;
    }
    //
    $sql='SELECT o.* FROM {og_uid} o WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function on_save_grupo($op,&$node){
  global $user;  
  if($node->type=='grupo'){
      if($op=='insert'){
        add_usuario_grupo($node->nid,$node->field_admin_grupo_uid[0]['value']);
        //gemini-2014
        add_usuario_grupo($node->nid,$user->uid);        
      }      
  }  
}
function get_usuario_grupo_nid_array(){
    global $user;
    $result=array();
    $my_list=get_og_uid_list('',$user->uid);
    if(count($my_list)>0){
        foreach($my_list as $i=>$row){
            $result[]=$row->nid;
        }
    }
    return $result;
}
function node_delete_group_form_alter(&$form,&$form_state,$form_id){
    //intelsat-2016
    hontza_grupos_mi_grupo_node_delete_group_form_alter($form,$form_state,$form_id);
}
function get_usuario_grupos_options($uid_in=''){
    global $user;
    $uid=$uid_in;
    if(empty($uid)){
        $uid=$user->uid;
    }
    $result=array();
    $my_list=get_og_uid_list('',$uid);
    if(count($my_list)>0){
        foreach($my_list as $i=>$row){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){
                $result[$node->nid]=$node->title;
            }                    
        }
    }
    return $result;
}
function gestion_grupos_access(){
    global $user;
    if($user->uid == 1){
        return 1;
    }
    //intelsat-2015
    //if ($user->roles[CREADOR]) {        
    if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR]) {
        if(arg(1)=='borrar_usuario_grupo'){
            $grupo_nid=arg(2);
            //print $grupo_nid;
            if(in_user_groups($grupo_nid)){
                return 1;
            }
        }else{
            return 1;
        }    
    }
    return 0;
}
function borrar_usuarios_grupos_callback(){
  global $user;  
  $headers=array();
  $headers[]=array('data'=>t('Groups'),'field'=>'node_title');
  $headers[]=array('data'=>t('Creation Date'),'field'=>'created');
  $headers[]='';
  //
  $sql='SELECT node.nid AS nid,
   node.title AS node_title,
   node.created AS node_created
 FROM node node 
 INNER JOIN users users ON node.uid = users.uid
 WHERE (node.type in ("grupo")) AND (users.uid = '.$user->uid.')
   ORDER BY node_created DESC';
  //print $sql;exit();

  $my_limit=20;
  //
  $result = pager_query($sql, $my_limit);

  $output = '';
  $num_rows = FALSE;
  $rows=array();
  $kont=0;
  //$node_types=array();
  while ($row = db_fetch_object($result)) {
    $rows[$kont]=array();
    $rows[$kont][0]=$row->node_title;
    $rows[$kont][1]=date('Y-m-d H:i',$row->node_created);
    $rows[$kont][2]=define_borrar_usuarios_grupos($row);
    $num_rows = TRUE;
    $kont++;
  }

  $output.=define_borrar_usuarios_grupos_header_actions();
  //$output .= drupal_get_form('hontza_search_file_form');

  if ($num_rows) {
    /*$feed_url = url('oportunidad_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$headers=array(t('fid'),t('User'),t('Name'),t('Ruta'),t('Date'),t('nid'),t('cid'),t('c_nid'),t('Type'));
    //$headers=array_slice($headers,1);
    //    
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output.= '<div id="first-time">' .t('There are no groups'). '</div>';
  }
  drupal_set_title(t('Remove users to group'));

  return $output;
}
function define_borrar_usuarios_grupos($row){
    $html=array();
    $html[]=l(t('Remove users'),'user-gestion/borrar_usuario_grupo/'.$row->nid);
    return implode('&nbsp;',$html);
}
function define_borrar_usuarios_grupos_header_actions(){
    $html=array();
    $html[]=l(t('Create new Group'),'node/add/grupo',array('attributes'=>array('class'=>'add')));
    $html[]=l(t('Add users to groups'),'user-gestion/usuarios-grupos',array('attributes'=>array('class'=>'add')));
    $html[]=l(t('Remove users to groups'),'user-gestion/borrar-usuarios-grupos',array('attributes'=>array('class'=>'my_remove')));
    return implode('<br>',$html);
}
function borrar_usuario_grupo_form(){
    $form=array();
    $grupo_nid=arg(2);
    $grupo=node_load($grupo_nid);
    if(isset($grupo->nid) && !empty($grupo->nid)){
        drupal_set_title(t('Remove users to group: @grupo_title',array('@grupo_title'=>$grupo->title)));
    }
    //
    $form['grupo_nid']=array('#type'=>'hidden','#value'=>$grupo_nid);
    //
    $options=get_usuarios_grupo_options($grupo_nid);
   
     $form['users_delete_txek'] = array(
    '#title' => t('Remove users to group'),
    '#type' => 'checkboxes',
    '#options' => $options,
  );
    
    $form['borrar_btn']=array('#type'=>'submit','#name'=>'borrar_btn','#value'=>t('Delete'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'user-gestion/borrar-usuarios-grupos')); 
    
    return $form;
}
function borrar_usuario_grupo_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='borrar_btn'){
            if(isset($form_state['values']['users_delete_txek']) && !empty($form_state['values']['users_delete_txek'])){
                $txek_list=$form_state['values']['users_delete_txek'];
                if(!empty($txek_list)){
                    foreach($txek_list as $uid=>$v){
                        if(!empty($v)){
                            my_user_group_unsubscribe($form_state['values']['grupo_nid'],$uid);
                        }
                    }
                }
            }
        }                
    }
   //
   drupal_goto('user-gestion/borrar-usuarios-grupos');    
}
function my_user_group_unsubscribe($grupo_nid,$uid) {
  global $user;
  $group_node = node_load($grupo_nid);
  $account = user_load($uid);
  if(!empty($grupo_nid) && !empty($uid)){
    og_delete_subscription($group_node->nid, $account->uid);
    // If needed, reload user object to reflect unsubscribed group.
    if ($user->uid == $account->uid) {
      og_get_subscriptions($account->uid, 1, TRUE); // Clear cache.
      $user = user_load(array('uid' => $user->uid));
      $message = t('You left the group %group.', array('%group' => $group_node->title));
    } 
    else {
      $message = t('%user removed from %group.', array('%user' => $account->name, '%group' => $group_node->title));
    }
    //intelsat-2016
    custom_menu_red_rebuild_custom_menu_red_row($account);
    drupal_set_message($message);      
  } 
}
function user_my_groups_access(){
    global $user;
    if($user->uid==1){
        return 1;        
    }
    //
    $uid=arg(1);
    if($user->uid==$uid){
        return 1;
    }
    return 0;
}
function my_og_add_users_form_alter(&$form,&$form_state,$form_id){
    drupal_set_title(t('Add members'));
}
function is_og_users_faces(){
    $zero=arg(0);
    if($zero=='og'){
        $bat=arg(1);
        if($bat=='users'){
            $bi=arg(2);
            if(!empty($bi) && is_numeric($bi)){
                $hiru=arg(3);
                if($hiru=='faces'){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function my_og_vigilancia_lo_mas_valorado(&$view){
    $where=array();
    $where[]="1";
    $where[]="(node.type in ('item', 'noticia'))";
    $where[]="node.status <> 0";
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }
    $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
    $where[]='votingapi_cache_node_average.value>0';
    $where[]=hontza_get_vigilancia_where_filter();
    
    $sql='SELECT DISTINCT(node.nid) AS nid,
    votingapi_cache_node_average.value AS votingapi_cache_node_average_value,
    DATE(FROM_UNIXTIME(node.created)) node_created_date 
    FROM {node} node LEFT JOIN {votingapi_cache} votingapi_cache_node_average ON node.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = "node" AND votingapi_cache_node_average.function = "average") 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id     
    WHERE '.implode(' AND ',$where).'           
    GROUP BY nid 
    ORDER BY votingapi_cache_node_average_value DESC,node_created_date DESC,node.title ASC';
    //ORDER BY votingapi_cache_node_average_value DESC    
    $view->build_info['query']=$sql;
    $view->build_info['count_query']=$sql;
}
function my_og_vigilancia_mascomentadas(&$view){
    $where=array();
    $where[]="1";
    $where[]="(node.type in ('item', 'noticia'))";
    $where[]="node.status <> 0";
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }
    $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
    $where[]=hontza_get_vigilancia_where_filter();
    //intelsat-2015
    $where[]='node_comment_statistics.comment_count>0';
    
    $sql='SELECT DISTINCT(node.nid) AS nid, node_comment_statistics.comment_count AS node_comment_statistics_comment_count
    FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid
    LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id
    WHERE '.implode(' AND ',$where).'
    GROUP BY nid 
    ORDER BY node_comment_statistics_comment_count DESC,node.created DESC'; 
    //print $sql;
    $view->build_info['query']=$sql;
    $view->build_info['count_query']=$sql;
}
function my_estadisticas_modal($js = FALSE,$is_modal=1,$nid_in=0) {
  $nid=$nid_in;
  if(empty($nid)){
      $nid=arg(2);
  }
  
  $salida='';
    
  if($is_modal){
    $salida = '<p>'.t('Number of new items in the channel').': '. node_load($nid)->title.'</p>';
  }/*else{
    $salida = '<p><b>'.t('Analysis').'</b></p>';  
  }*/
  
  $title = t('Statistics from last month.');
  if ($js) {
    ctools_include('ajax');
    ctools_include('modal');
    
    $period = strtotime('-1 month');
    $quant = new stdClass;
    $quant->id = 'canales_chart';    
    $quant->label = t('Number of items');  // The title of the chart
    $quant->labelsum = TRUE; // Show the total amount of items in the chart title
    $quant->query = 'SELECT node.created FROM {content_type_item} content_type_item INNER JOIN {node} node ON node.vid = content_type_item.vid
                    WHERE node.created >= %d AND field_item_canal_reference_nid = '.$nid.'
                    ORDER BY node.created DESC'; // We can provide a custom query instead
    $quant->table = 'og';
    $quant->field = 'created';
    $quant->dataType = 'single';
    $quant->chartType = 'line';
    $quants[] = $quant;
  
    $salida .= '<p>'. quant_process($quant, $period) .'</p>';
    
    //gemini-2013
    if($is_modal){
      ctools_modal_render($title, $salida);
    }else{
        return $salida;
    }
  }
  else {
    drupal_set_title($titulo);
    return $salida;
  }    
}
function is_user_creador_de_grupo_del_grupo($user,$grupo){
    if(is_user_creador_de_grupo($user) && $grupo->uid==$user->uid){
        return 1;
    }
    return 0;
}
function is_user_creador_de_grupo($my_user_in=''){
    $my_user=my_set_global_user($my_user_in);
    //echo print_r($my_user,1);
    if(isset($my_user->roles) && !empty($my_user->roles)){
        $roles_keys=array_keys($my_user->roles);
        //intelsat-2015
        //return in_array(CREADOR,$roles_keys);
        if(in_array(CREADOR,$roles_keys)){
            return 1;
        }
        if(in_array(ADMINISTRADOR,$roles_keys)){
            return 1;
        }
    }
    return 0;
}
function link_validar_canal($nid,$is_solo_paginas=0,$is_a_la_par_del_menu=0){
    if(hontza_solr_search_is_usuario_lector()){
        return '';
    }
    global $user,$base_url;    
    $html=array();
    if(hontza_is_vigilancia_pantalla('pendientes')){
        hontza_add_rechazar_pagina_js();
        $paginas=hontza_link_rechazar_pagina($is_a_la_par_del_menu);        
        $paginas.=hontza_link_validar_pagina($is_a_la_par_del_menu);
        if($is_solo_paginas){
            $html[]=$paginas;
            return implode('',$html);
        }
    }
    //
    $node=node_load($nid);
    //if(user_access('root')){    
    if(repase_access_canal($node,1)){                
        $label=t('Automatic Validation');
        $html[]= '<span class="link_validar_canal">';        
        if(isset($node->nid) && !empty($node->nid)){
            //$html[]=  l(t('Validate'), 'node/'. $nid .'/validar_canal', array('query'=>drupal_get_destination()));
            
            //$html[]=  l('&nbsp', 'node/'. $nid .'/validar_canal', array('html'=>TRUE,'query'=>drupal_get_destination()));
            $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/validar_canal.png';
            $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';        
            $html[]=l($img,'node/'. $nid .'/validar_canal',array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));

        }else{
            return '';
        }
        $html[]= '</span>';        
    }
    $html[]=$paginas;
    return implode('',$html);
}
function hontza_validar_canal_form(){
    //drupal_access_denied();
    $form=array();
    $nid=arg(1);
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        //drupal_set_title(t('Automatic Validation: @canal_title',array('@canal_title'=>$node->title)));
        drupal_set_title(t('Automatic Validation').': '.$node->title);
    }
    //
    $form['canal_nid']=array('#type'=>'hidden','#value'=>$nid);
        
    $form['validar_canal_btn']=array('#type'=>'submit','#name'=>'validar_canal_btn','#value'=>t('Validate'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'canales/'.$nid)); 
    
    return $form;
}
function hontza_validar_canal_form_submit($form,&$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if(isset($form_state['clicked_button']['#name']) && $form_state['clicked_button']['#name']=='validar_canal_btn'){
            $canal_nid=$form_state['values']['canal_nid'];
            $node=node_load($canal_nid);
            if(isset($node->nid) && !empty($node->nid)){
                hontza_validar_canal($canal_nid,$node);
                drupal_set_message(t('@canal_title validated',array('@canal_title'=>$node->title)));
            }
        }
    }   
}
function view_set_variable_canal_sql($view_name,$query){
        if($view_name=='og_canales_dash'){
            variable_set('my_og_canales_dash',$query);
        }
}
function hontza_validar_canal($canal_nid){
    //intelsat-2015
    $my_grupo=og_get_group_context();
    if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
        $source=new stdClass();
        $source->feed_nid=$canal_nid;
        $my_grupo=hontza_get_grupo_by_feed_nid($source);        
    }
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
    //    
        //$query=variable_get('my_og_canales_dash','');
        $query='SELECT DISTINCT node.nid AS nid, node.created AS node_created 
        FROM {node} node 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
        WHERE (node.type in ("%s")) AND (og_ancestry.group_nid = '.$my_grupo->nid.') AND (node_data_field_item_canal_reference.field_item_canal_reference_nid = %d) ORDER BY node_created DESC ';
        $node_type=array('item');
        $res=db_query($query,implode(',',$node_type),$canal_nid);
        while($row=db_fetch_object($res)){
            hontza_delete_flag_content($row);
            $flag_result = flag('flag','leido_interesante',$row->nid);
            if (!$flag_result) {
                $error = t('You are not allowed to flag, or unflag, this content.');
                drupal_set_message($error,'error');
            }
        }
    }
}
function hontza_delete_flag_content($row){
    $sql='DELETE flag_content.* FROM {flag_content} flag_content WHERE flag_content.content_id='.$row->nid;
    db_query($sql);    
}
function hontza_is_validacion_automatica_by_feeds_source($source){
    //if(user_access('root')){
        $canal=node_load($source->feed_nid);
        if(isset($canal->nid) && !empty($canal->nid)){            
            if(isset($canal->field_is_validacion_automatica) && isset($canal->field_is_validacion_automatica[0]) && isset($canal->field_is_validacion_automatica[0]['value'])){
                if(!empty($canal->field_is_validacion_automatica[0]['value'])){
                    return 1;
                }
            }
        }        
    //}
    return 0;
}
function hontza_import_validacion_automatica($node){
    $flag_result = flag('flag','leido_interesante',$node->nid);
}
function hontza_get_num_paginas_visitadas_by_usuario($uid){
    $my_user=user_load($uid);
    //if(user_access('root')){
        /*$my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $kont=red_funciones_statistics_user_tracker_count($uid,$my_grupo);
        }else{*/
            $kont=statistics_user_tracker_count($uid);
        //}    
        //print $uid.'='.$kont.'<BR>';
        return $kont;
    //}
    //return '';
}
function statistics_user_tracker_count($uid) {
  $where_time=hontza_get_usuarios_acceso_where_time();
  $kont=0;  
  if ($account = user_load(array('uid' => $uid))) {      
    $res = db_query('SELECT aid, timestamp, path, title FROM {accesslog} WHERE uid = %d AND %s',$account->uid,$where_time);
    $rows = array();
    while ($log = db_fetch_object($res)) {
      $kont++;
    }
  }   
  return $kont;  
}
function usuarios_acceso_callback(){
  //gemini-2014
  hontza_grupo_shared_active_tabs_access();
  $output='';  
  drupal_set_title(t('Access').get_la_empresa());
  $user_list=get_estadisticas_user_list('usuarios_acceso');  
  $output=get_usuarios_acceso_html($user_list);
  return $output; 
}
function get_usuarios_acceso_html($user_list){
    $output='';
    if(!hontza_grupos_mi_grupo_is_usuarios_mas_contacto()){
        $output.=create_usuarios_estadisticas_menu();
        $output.=hontza_define_usuarios_acceso_form_filter();    
    }
    if(count($user_list)){
        //
        $headers=array();
        //gemini-2014
        if(hontza_grupos_mi_grupo_is_usuarios_mas_contacto()){
            /*$headers[0]=t('Photo');
            $headers[1]=get_header_usuarios('usuario',t('User'));
            $headers[2]=get_header_usuarios('roles',t('Roles'));
            $headers[3]=t('Contact');*/
            $headers[0]=get_header_usuarios('usuario',t('User'));
            $headers[1]=get_header_usuarios('roles',t('Roles'));
            $headers[2]=t('Contact');
        }else{
            /*$headers[0]=t('Photo');
            $headers[1]=get_header_usuarios('usuario',t('User'));
            $headers[2]=get_header_usuarios('num_paginas_visitadas',t('Visited pages'));
            $headers[3]=get_header_usuarios('roles',t('Roles'));*/
            $headers[0]=get_header_usuarios('usuario',t('User'));
            $headers[1]=get_header_usuarios('num_paginas_visitadas',t('Visited pages'));
            $headers[2]=get_header_usuarios('roles',t('Roles')); 
        }        
        //
        $rows=array();
        $kont=0;
        $my_limit=hontza_define_usuarios_acceso_table_limit();        
        //        
        $my_list=prepare_user_list_to_order($user_list);
        $my_list=call_array_ordenatu($my_list);
        //intelsat-2015
        $faktore='';
        if(hontza_grupos_mi_grupo_is_usuarios_mas_contacto()){
            $faktore=75;
        }
        foreach($my_list as $i=>$capta){
            $rows[$kont]=array();
            //gemini-2014
            //$user_link=l($capta->usuario,'user/'.$capta->uid,array('query'=>drupal_get_destination()));
            $user_link=l($capta->usuario,'hontza_grupos/'.$capta->uid.'/user_view',array('query'=>drupal_get_destination()));
            $roles_li=hontza_get_user_roles_li($capta->uid);
            //intelsat-2015
            //gemini-2014            
            $user_image=hontza_grupos_mi_grupo_get_user_img($capta->uid,$faktore);
            $user_image=$user_link.'<div>'.$user_image.'</div>';
            //
            $rows[$kont][0]=$user_image;              
            if(hontza_grupos_mi_grupo_is_usuarios_mas_contacto()){
                //$rows[$kont][1]=$user_link;
                $rows[$kont][1]=$roles_li;
                $rows[$kont][2]=hontza_grupos_mi_grupo_define_usuarios_mas_contacto_acciones($capta);                
            }else{
                //$rows[$kont][1]=$user_link;
                //$rows[$kont][1]=$capta->num_paginas_visitadas;
                $num_paginas_visitadas_html=hontza_grupos_mi_grupo_num_paginas_visitadas_modal(TRUE,0,$capta->uid);
                $num_paginas_visitadas_html=str_replace('<p>','',$num_paginas_visitadas_html);
                $num_paginas_visitadas_html=str_replace('</p>','',$num_paginas_visitadas_html);
                //print $num_paginas_visitadas_html;
                //exit();
                $rows[$kont][1]=$num_paginas_visitadas_html;
                $rows[$kont][2]=$roles_li;                
            }
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output.= theme('table',$headers,$rows);
        $output.= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no users'). '</div>';
    }
    return $output;
}
function get_usuarios_acceso_by_user($uid){
    $result=array();
    $result['num_paginas_visitadas']=hontza_get_num_paginas_visitadas_by_usuario($uid);
    return $result;
}
function hontza_og_canales_dash_pre_execute(&$view){
    global $user;
    //if(user_access('root')){
        if(hontza_is_canales()){
            $canal_nid=arg(1);
            $arg_type=arg(2);
            $my_grupo=og_get_group_context();
            if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
                if($arg_type=='lo-mas-valorado'){
                    $sql=hontza_canales_lo_mas_valorado_sql($my_grupo,$canal_nid);
                }else if($arg_type=='lo-mas-comentado'){
                    $sql=hontza_canales_lo_mas_comentado_sql($my_grupo,$canal_nid);    
                }else{
                    $sql=hontza_canales_default_sql($my_grupo,$canal_nid,$arg_type);
                }
                $view->build_info['query']=$sql;
                $view->build_info['count_query']=$sql;
            }    
        }
    //}
}
function hontza_is_canales($arg_type=''){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canales'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            if(empty($arg_type)){
                return 1;
            }else{
                $param2=arg(2);
                if(!empty($param2) && $param2==$arg_type){
                    return 1;
                }else{
                    if(empty($param2) && $arg_type=='pendientes'){
                        return 1;
                    }
                }
            }    
        }
    }
    return 0;
}
function hontza_canales_default_sql($my_grupo,$canal_nid,$arg_type,$nid=0){
    global $user;
    $where=array();
    //intelsat-2014
    $left_join='';
    //
    $where[]="node.status <> 0";    
                    //intelsat-2015
                    //$where[]='node.type in ("item")';
                    $where[]='node.type in ("item","noticia")';
                    $where[]='og_ancestry.group_nid = '.$my_grupo->nid;
                    if(!empty($nid)){
                        $where[]='node.nid = '.$nid;
                    }
                    if(!empty($canal_nid)){
                        $where[]='node_data_field_item_canal_reference.field_item_canal_reference_nid = '.$canal_nid;
                    }
                    if(empty($arg_type) || $arg_type=='pendientes'){
                        $where[]='flag_content_node.uid IS NULL';
                        $where[]='(node_data_field_responsable_uid.field_responsable_uid_uid = '.$user->uid.' OR node_data_field_responsable_uid2.field_responsable_uid2_uid = '.$user->uid.')';
                    }else if($arg_type=='ultimas'){
                        //$where[]='flag_content_node.uid IS NULL';
                    }else if($arg_type=='validados'){
                        $where[]='flag_content_node.fid = 2';
                    }else if($arg_type=='rechazados'){
                        $where[]='flag_content_node.fid = 3';
                    }else if($arg_type=='bookmarks'){
                        //intelsat-2014
                        if(hontza_solr_funciones_is_bookmark_activado()){
                            $left_join=' LEFT JOIN {hontza_bookmark} hontza_bookmark ON node.nid=hontza_bookmark.nid ';
                            $where[]='NOT hontza_bookmark.nid IS NULL';
                            $where[]='hontza_bookmark.uid='.$user->uid;
                        }    
                    }
                    if(!is_dashboard()){
                        $where[]=hontza_get_vigilancia_where_filter();
                    }
                    //$order_by='node_created DESC';
                    $order_by='node.nid DESC';
                    $sql='SELECT node.nid AS nid, node.created AS node_created 
                    FROM {node} node 
                    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
                    LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.nid = node_data_field_item_canal_reference.nid 
                    LEFT JOIN {node} my_canal ON node_data_field_item_canal_reference.field_item_canal_reference_nid=my_canal.nid  
                    LEFT JOIN content_field_responsable_uid node_data_field_responsable_uid ON my_canal.nid = node_data_field_responsable_uid.nid
                    LEFT JOIN content_field_responsable_uid2 node_data_field_responsable_uid2 ON my_canal.nid = node_data_field_responsable_uid2.nid
                    LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id 
                    '.$left_join.'
                    WHERE '.implode(' AND ',$where).'
                    ORDER BY '.$order_by;
                    /*if(user_access('root')){
                        print $sql;
                    }*/    
    return $sql;                
}
function hontza_canales_lo_mas_valorado_sql($my_grupo,$canal_nid,$nid=0){
    $where=array();
    $where[]="1";
    //intelsat-2015
    //$where[]="(node.type in ('item'))";
    $where[]="(node.type in ('item', 'noticia'))";    
    $where[]="node.status <> 0";
    $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    if(!empty($nid)){
        $where[]='node.nid = '.$nid;
    }
    $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
    $where[]='votingapi_cache_node_average.value>0';
    if(!empty($canal_nid)){
        $where[]='node_data_field_item_canal_reference.field_item_canal_reference_nid = '.$canal_nid;
    }
    $where[]=hontza_get_vigilancia_where_filter();
    $sql='SELECT DISTINCT(node.nid) AS nid,
    votingapi_cache_node_average.value AS votingapi_cache_node_average_value,
    DATE(FROM_UNIXTIME(node.created)) node_created_date,
    node.title AS node_title 
    FROM {node} node LEFT JOIN {votingapi_cache} votingapi_cache_node_average ON node.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = "node" AND votingapi_cache_node_average.function = "average") 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id     
    LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.nid = node_data_field_item_canal_reference.nid     
    WHERE '.implode(' AND ',$where).'           
    ORDER BY votingapi_cache_node_average_value DESC,node_created_date DESC,node.title ASC';
    //ORDER BY votingapi_cache_node_average_value DESC
    return $sql;      
}
function hontza_canales_lo_mas_comentado_sql($my_grupo,$canal_nid,$nid=0){
    $where=array();
    $where[]="1";
    if(!empty($nid)){
        $where[]='node.nid = '.$nid;
    }
    //intelsat-2015
    //$where[]="(node.type in ('item'))";
    $where[]="(node.type in ('item', 'noticia'))";    
    $where[]="node.status <> 0";
    $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
    if(!empty($canal_nid)){
        $where[]='node_data_field_item_canal_reference.field_item_canal_reference_nid = '.$canal_nid;
    }
    $where[]=hontza_get_vigilancia_where_filter();
    $sql='SELECT DISTINCT(node.nid) AS nid, node_comment_statistics.comment_count AS node_comment_statistics_comment_count 
    FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid
    LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id
    LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.nid = node_data_field_item_canal_reference.nid
    WHERE '.implode(' AND ',$where).'
    GROUP BY nid 
    ORDER BY node_comment_statistics_comment_count DESC';
    return $sql;
}
function hontza_canales_menu(){
    $canal_nid=arg(1);
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    //if(hontza_is_show_canales_pendientes_tab($canal_nid)){
        //intelsat-2015
        if(!hontza_canal_rss_is_usuario_basico()){
            $html[]='<li'.hontza_canales_menu_class('pendientes').'>'.l(t('Pending'),'canales/'.$canal_nid.'/pendientes').'</li>';
        }    
    //}
    //intelsat-2015
    if(hontza_canal_rss_is_visualizador_activado()){
        $html[]='<li'.hontza_canales_menu_class('destacados').'>'.l(t('.Highlighted'),'canales/'.$canal_nid.'/destacados').'</li>';
    }    
    $html[]='<li'.hontza_canales_menu_class('validados').'>'.l(t('Validated'),'canales/'.$canal_nid.'/validados').'</li>';
    //intelsat-2015
    if(red_despacho_is_show_lo_mas_valorado()){
        $html[]='<li'.hontza_canales_menu_class('lo-mas-valorado').'>'.l(t('Top Rated'),'canales/'.$canal_nid.'/lo-mas-valorado').'</li>';
    }    
    //intelsat-2015
    if(red_despacho_is_show_lo_mas_comentado()){
        $html[]='<li'.hontza_canales_menu_class('lo-mas-comentado').'>'.l(t('Most commented'),'canales/'.$canal_nid.'/lo-mas-comentado').'</li>';
    }
    $html[]='<li'.hontza_canales_menu_class('ultimas').'>'.l(t('.Last'),'canales/'.$canal_nid.'/ultimas').'</li>';
    //intelsat-2015
    if(!hontza_canal_rss_is_usuario_basico()){
        $html[]='<li'.hontza_canales_menu_class('rechazados').'>'.l(t('Rejected'),'canales/'.$canal_nid.'/rechazados').'</li>';
    }
    //intelsat-2014
    if(hontza_solr_funciones_is_bookmark_activado()){
        $html[]='<li'.hontza_canales_menu_class('bookmarks').'>'.l(t('Bookmarked'),'canales/'.$canal_nid.'/bookmarks').'</li>';
    }
    //
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    $output=implode('',$html);
    $output.=hontza_define_vigilancia_form_filter();
    return $output;
}
function hontza_canales_menu_class($arg_type,$param_in=''){
    $result=0;
    if(empty($param_in)){
        $param=arg(2);
    }else{
        $param=$param_in;
    }
    //
    if(empty($param)){
        if($arg_type=='pendientes'){
            $result=1;
        }
    }else{
        if($param==$arg_type){
            $result=1;
        }
    }    
    if($result){
        return ' class="active"';
    }
    return '';
}
//intelsat-2015
//function hontza_canales_por_categorias($item_list){
function hontza_canales_por_categorias($item_list,$arg_type_in=''){
    global $user;
    $result=array();
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $arg_type=arg(3);
        //intelsat-2015
        if(!empty($arg_type_in)){
            $arg_type=$arg_type_in;
        }
        if(!empty($item_list)){
            foreach($item_list as $i=>$nid){
                //intelsat-2015
                //$node=node_load($nid);
                if(empty($arg_type) || in_array($arg_type,array('pendientes','ultimas','validados','rechazados'))){
                    $row=hontza_responsable_row($my_grupo,$nid,$arg_type);
                    if(isset($row->nid) && !empty($row->nid)){
                        $result[]=$nid;
                    }/*else{
                        //intelsat-2015
                        if($node->type=='noticia'){
                            $result[]=$nid;
                        }
                    }*/
                }else if($arg_type=='lo-mas-valorado'){    
                    $row=hontza_lo_mas_valorado_row($my_grupo,$nid);
                    if(isset($row->nid) && !empty($row->nid)){
                        if(empty($row->votingapi_cache_node_average_value)){
                            $row->votingapi_cache_node_average_value=0;
                        }                        
                        $result[]=$row;
                    }
                }else if($arg_type=='lo-mas-comentado'){    
                    $row=hontza_lo_mas_comentado_row($my_grupo,$nid);
                    if(isset($row->nid) && !empty($row->nid)){                        
                        if(empty($row->node_comment_statistics_comment_count)){
                            $row->node_comment_statistics_comment_count=0;
                        } 
                        if(hontza_is_comentada_by_row($row)){
                            $result[]=$row;
                        }
                    }
                }else if($arg_type=='bookmarks'){
                    //intelsat-2015
                    $row=hontza_solr_funciones_get_bookmark_row($nid,'', $user->uid);
                    if(isset($row->nid) && !empty($row->nid)){    
                        $result[]=$nid;
                    }    
                    //
                }else{
                    $result[]=$nid;
                }
            }
        }
    }
    if($arg_type=='lo-mas-valorado'){    
        //$result=array_ordenatu($result,'votingapi_cache_node_average_value','desc',1);
        $result=hontza_array_ordenatu_categorias_lo_mas_valorado($result);
        $result=hontza_nid_array($result);
    }else if($arg_type=='lo-mas-comentado'){    
        $result=array_ordenatu($result,'node_comment_statistics_comment_count','desc',1);
        $result=hontza_nid_array($result);
    }
    return $result;
}
function hontza_responsable_row($my_grupo,$nid,$arg_type){
    $canal_nid=0;
    $sql=hontza_canales_default_sql($my_grupo, $canal_nid, $arg_type,$nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=new stdClass();
    return $result;
}
function hontza_lo_mas_valorado_row($my_grupo,$nid){
    $canal_nid=0;
    $sql=hontza_canales_lo_mas_valorado_sql($my_grupo, $canal_nid,$nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=new stdClass();
    return $result;
}
function hontza_nid_array($my_array){
    $result=array();
    if(!empty($my_array)){
        foreach($my_array as $i=>$row){
            $result[]=$row->nid;
        }
    }
    return $result;
}
function hontza_lo_mas_comentado_row($my_grupo,$nid){
    $canal_nid=0;
    $sql=hontza_canales_lo_mas_comentado_sql($my_grupo, $canal_nid,$nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=new stdClass();
    return $result;
}
//intelsat-2015
//function hontza_canales_por_categorias_menu(){
function hontza_canales_por_categorias_menu($type_in='',$tid_in='',$arg_type_in=''){    
    $tid=arg(2);
    $arg_type=arg(3);
    if(empty($arg_type)){
        $arg_type='pendientes';
    }
    //intelsat-2015
    if($type_in=='canales_tipos_fuente'){
        $tid=$tid_in;
        $arg_type=$arg_type_in;
    }        
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    //if(hontza_is_show_canales_por_categorias_pendientes_tab($tid)){
        //intelsat-2015
        if(!hontza_canal_rss_is_usuario_basico()){
            $url_pendientes=red_despacho_get_canales_por_categorias_url_tab($tid,$type_in,'pendientes');
            $html[]='<li'.hontza_canales_menu_class('pendientes',$arg_type).'>'.l(t('Pending'),$url_pendientes).'</li>';
        }    
    //}
    //intelsat-2015
    //$url_validados='canales/my_categorias/'.$tid.'/validados';    
    $url_validados=red_despacho_get_canales_por_categorias_url_tab($tid,$type_in,'validados');    
    $html[]='<li'.hontza_canales_menu_class('validados',$arg_type).'>'.l(t('Validated'),$url_validados).'</li>';
     //intelsat-2015
    if(red_despacho_is_show_lo_mas_valorado()){
        $html[]='<li'.hontza_canales_menu_class('lo-mas-valorado',$arg_type).'>'.l(t('Top Rated'),'canales/my_categorias/'.$tid.'/lo-mas-valorado').'</li>';
    }    
    //intelsat-2015
    if(red_despacho_is_show_lo_mas_comentado()){
        $html[]='<li'.hontza_canales_menu_class('lo-mas-comentado',$arg_type).'>'.l(t('Most commented'),'canales/my_categorias/'.$tid.'/lo-mas-comentado').'</li>';        
    }
    //intelsat-2015
    //$url_ultimas='canales/my_categorias/'.$tid.'/ultimas';
    $url_ultimas=red_despacho_get_canales_por_categorias_url_tab($tid,$type_in,'ultimas');        
    $html[]='<li'.hontza_canales_menu_class('ultimas',$arg_type).'>'.l(t('.Last'),$url_ultimas).'</li>';
    //intelsat-2015
    if(!hontza_canal_rss_is_usuario_basico()){
        //intelsat-2015
        //$url_rechazados='canales/my_categorias/'.$tid.'/rechazados';
        $url_rechazados=red_despacho_get_canales_por_categorias_url_tab($tid,$type_in,'rechazados');            
        $html[]='<li'.hontza_canales_menu_class('rechazados',$arg_type).'>'.l(t('Rejected'),$url_rechazados).'</li>';
    }    
    //intelsat-2015
    if(hontza_solr_funciones_is_bookmark_activado()){
        //intelsat-2015
        $url_bookmarks='canales/my_categorias/'.$tid.'/bookmarks';
        $url_bookmarks=red_despacho_get_canales_por_categorias_url_tab($tid,$type_in,'bookmarks');                    
        $html[]='<li'.hontza_canales_menu_class('bookmarks',$arg_type).'>'.l(t('Bookmarked'),$url_bookmarks).'</li>';     
    }
    //
    $html[]='</ul>';
    $html[]='</div>';
    //gemini-2014
    $html[]='<div class="view-header">';
    $html[]=link_validar_canal('',1,1);
    $html[]='</div>';
    //
    $html[]='</div>';
    
    return implode('',$html);
}
function hontza_get_node_created($nid){
    $sql='SELECT * FROM {node} n WHERE nid='.$nid;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row->created;
    }
    return 1;
}
function hontza_gestion_grupos_propios(&$view){
    global $user;
    if($user->uid==1){
        $where_user="users.uid=".$user->uid;
        if($user->uid==1){
            $where_user="1";
            $creador_de_grupo_uid=hontza_get_gestion_grupos_propios_filter_value('creador_de_grupo_uid');
            if(!empty($creador_de_grupo_uid)){
                $where_user="users.uid=".$creador_de_grupo_uid;
            }
        }
        $order_by=hontza_gestion_grupos_propios_order_by();
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
        node_revisions.format AS node_revisions_format
        FROM {node} node INNER JOIN {users} users ON node.uid = users.uid 
        LEFT JOIN {og} og ON node.nid = og.nid 
        LEFT JOIN {content_type_grupo} node_data_field_tematica ON node.vid = node_data_field_tematica.vid 
        LEFT JOIN {term_node} term_node ON node.vid = term_node.vid 
        LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid 
        LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
        WHERE (node.type in ('%s')) AND ".$where_user.$order_by;
        //print $sql;
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;
    }
    
    
    //print $view->build_info['query'];
}
function hontza_gestion_grupos_propios_filtro(){
    global $user;
    if($user->uid==1){
        my_add_buscar_js();
        return drupal_get_form('hontza_gestion_grupos_propios_filtro_form');
    }
    return '';
}
function hontza_gestion_grupos_propios_filtro_form(){    
    $fs_title=t('Search');
    if(!panel_admin_gestion_grupos_propios_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));            
    $form['file_buscar_fs']['creador_de_grupo_uid']=array('#type'=>'select','#title'=>t('Creator'),"#options"=>hontza_creador_de_grupo_options(),"#default_value"=>hontza_get_gestion_grupos_propios_filter_value('creador_de_grupo_uid'));    
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar');
    return $form;
}
function hontza_creador_de_grupo_options(){
    $with_admin_grupo=0;
    $with_creador_grupo=1;
    $with_empty=1;
    $result=get_usuarios_creadores_y_administradores_grupo_options($with_admin_grupo,$with_creador_grupo,$with_empty);
    return $result;
}
function hontza_get_gestion_grupos_propios_filter_value($f){
    if(isset($_SESSION['gestion_grupos_propios']['filter']) && !empty($_SESSION['gestion_grupos_propios']['filter'])){
        if(isset($_SESSION['gestion_grupos_propios']['filter'][$f]) && !empty($_SESSION['gestion_grupos_propios']['filter'][$f])){
            return $_SESSION['gestion_grupos_propios']['filter'][$f];
        }
    }
    return '';
}
function hontza_gestion_grupos_propios_filtro_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $name=$form['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['gestion_grupos_propios']['filter']) && !empty($_SESSION['gestion_grupos_propios']['filter'])){
                unset($_SESSION['gestion_grupos_propios']['filter']);
            }
        }else{
            $_SESSION['gestion_grupos_propios']['filter']=array();
            $fields=array('creador_de_grupo_uid');
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form['values'][$f];
                    if(!empty($v)){
                        $_SESSION['gestion_grupos_propios']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function hontza_gestion_grupos_propios_order_by(){
    $sort='asc';
    $field='node_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order=='title'){
            $field='node_title';
        }else if($order=='field_tematica_value'){
            $field='node_data_field_tematica_field_tematica_value';
        }else if($order=='name'){
            $field='term_data_name';
        }else if($order=='member_count'){
            $field='member_count';
        }else if($order=='status'){
            $field='node_status';
        }
    }
    $result=' ORDER BY '.$field.' '.strtoupper($sort);
    return $result;
}
function hontza_limpiar_taxonomy(){
    $v_array=taxonomy_get_vocabularies();
    if(count($v_array)>0){
        foreach($v_array as $vid=>$v){
            //AVISO::::116 es de un subdominio concreto
            if(in_array($vid,array(1,12,11,6,10,3,4,116))){
                if(in_array($vid,array(3,10))){
                    //$term_list=taxonomy_get_tree($vid);
                    $term_list=hontza_get_term_data_array($vid);
                    if(!empty($term_list)){
                        foreach($term_list as $i=>$row){
                            taxonomy_del_term($row->tid);
                        }
                    }                      
                }
            }else{
                taxonomy_del_vocabulary($vid);
            }
        }
    }
}
function hontza_get_term_data_array($vid){
    $result=array();
    $sql='SELECT * FROM {term_data} td WHERE td.vid='.$vid;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_faq_node_form_alter(&$form,&$form_state,$form_id){
    $form['title']['#title']=t('Question');
    $form['body_field']['body']['#title']=t('Answer');
}
function hontza_node_load_by_lang($nid_orig){
    global $language;
    //
    if(!empty($nid_orig)){
        $node_orig=node_load($nid_orig);
        if(isset($node_orig->nid) && !empty($node_orig->nid)){
            if(strcmp($node_orig->language,$language->language)==0){
                return $node_orig;
            }else{
                 $param=array('tnid'=>$nid_orig,'language'=>$language->language);
                 $node=node_load($param);
                 if(isset($node->nid) && !empty($node->nid)){
                    return $node;
                 }
            }
            return $node_orig;
        }
    }
    //
    $my_result=new StdClass();
    return $my_result;
}
function hontza_vigilancia_menu(){
    if(hontza_is_vigilancia_menu()){
        $arg_type=arg(1);
        //intelsat-2015
        if(hontza_canal_rss_is_publico_activado()){
            if(!hontza_is_vigilancia('destacados')){
                $arg_type=publico_vigilancia_get_hontza_vigilancia_menu_arg_type();
            }
        }    
        if(empty($arg_type)){
            $arg_type='pendientes';
        }
        $html=array();
        if(!empty($arg_type) && $arg_type=='pendientes'){
            $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
        }else{
            $html[]='<div class="tab-wrapper clearfix primary-only">';
        }
        $html[]='<div id="tabs-primary" class="tabs primary">';
        $html[]='<ul>';
        $attributes=array();
       // $attributes=array('attributes'=>array('style'=>'font-size:12px;'));
        //if(hontza_is_show_vigilancia_pendientes_tab()){
            //intelsat-2015
            $is_show_pendientes=1;
            $url_validados='vigilancia/validados';
            $url_ultimos='vigilancia/ultimas';
            $param_ultimos='ultimas';
            $url_valorado='vigilancia/lo-mas-valorado';
            $url_comentado='vigilancia/lo-mas-comentado';
            if(hontza_canal_rss_is_publico_activado()){
                if(publico_is_pantalla_publico('vigilancia')){
                    $is_show_pendientes=0;
                    $url_validados='publico/vigilancia/validados';
                    $url_ultimos='publico/vigilancia/ultimos';
                    $param_ultimos='ultimos';
                    $url_valorado='publico/vigilancia/lo-mas-valorado';
                    $url_comentado='publico/vigilancia/lo-mas-comentado';
                }
            }
            //intelsat-2015
            if(hontza_canal_rss_is_usuario_basico()){
                $is_show_pendientes=0;
            }
            if($is_show_pendientes){
                $html[]='<li'.hontza_canales_menu_class('pendientes',$arg_type).'>'.l(t('Pending'),'vigilancia/pendientes',$attributes).'</li>';
            }
        //}
        //intelsat-2015
        if(hontza_canal_rss_is_visualizador_activado()){
            $html[]='<li'.hontza_canales_menu_class('destacados',$arg_type).'>'.l(t('.Highlighted'),'vigilancia/destacados').'</li>';
        }            
        $html[]='<li'.hontza_canales_menu_class('validados',$arg_type).'>'.l(t('Validated'),$url_validados).'</li>';
        //intelsat-2015
        if(red_despacho_is_show_lo_mas_valorado()){
            $html[]='<li'.hontza_canales_menu_class('lo-mas-valorado',$arg_type).'>'.l(t('Top Rated'),$url_valorado).'</li>';
        }    
        //intelsat-2015
        if(red_despacho_is_show_lo_mas_comentado()){
            $html[]='<li'.hontza_canales_menu_class('lo-mas-comentado',$arg_type).'>'.l(t('Most commented'),$url_comentado).'</li>';
        }    
        //$html[]='<li'.hontza_canales_menu_class('ultimas',$arg_type).'>'.l(t('All'),'vigilancia/ultimas').'</li>';
        $html[]='<li'.hontza_canales_menu_class($param_ultimos,$arg_type).'>'.l(t('.Last'),$url_ultimos).'</li>';
        //intelsat-2015
        if(!hontza_canal_rss_is_usuario_basico()){
            $html[]='<li'.hontza_canales_menu_class('rechazados',$arg_type).'>'.l(t('Rejected'),'vigilancia/rechazados').'</li>';
        }
        if(hontza_solr_funciones_is_bookmark_activado()){
            $html[]='<li'.hontza_canales_menu_class('bookmarks',$arg_type).'>'.l(t('Bookmarked'),'vigilancia/bookmarks').'</li>';
        }
        $html[]='</ul>';
        $html[]='</div>';
        //gemini-2014
        if(hontza_is_validar_page_header($name)){ 
            $html[]=validar_page_header(1);    
        }
        $html[]='</div>';                
        $output=implode('',$html);
        $output.=hontza_define_vigilancia_form_filter();
        return $output;
    }
    return '';
}
function hontza_is_vigilancia_menu(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='vigilancia'){
        $param1=arg(1);
        if(empty($param1)){
            return 1;
        }else{
            $keys=hontza_define_vigilancia_menu_keys();
            if(in_array($param1,$keys)){
                return 1;
            }
        }
    }
    //intelsat-2015
    if(hontza_canal_rss_is_publico_activado()){
        if(publico_is_pantalla_publico('vigilancia')){
            return 1;
        }
    }
    return 0;
}
function hontza_define_vigilancia_menu_keys(){
    $result=array('pendientes','ultimas','lo-mas-valorado','lo-mas-comentado','validados','rechazados');
    if(hontza_solr_funciones_is_bookmark_activado()){
        $result[]='bookmarks';
    }
    //intelsat-2015
    if(hontza_canal_rss_is_visualizador_activado()){
        $result[]='destacados';
    }    
    return $result;
}            
?>