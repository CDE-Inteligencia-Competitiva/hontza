<?php
function red_funciones_is_perfil_usuario(){
    if(strcmp(arg(0),'user')==0){
        $param=arg(1);
        if(!empty($param)){
            if(is_numeric($param)){            
                return 1;
            }    
        }
    }else if(strcmp(arg(0),'users')==0){
        $param=arg(1);
        if(!empty($param)){
            return 1;
        }
    }
    return 0;
}
function red_funciones_get_user_menu_icono(){
    global $base_url;
    global $user;
    $popup=red_funciones_get_user_roles_popup();
    $html='<img src="'.$base_url.'/sites/all/themes/buho/images/user.png" alt="'.$popup.'"/>';
    $html=l($html,'user/'.$user->uid,array('html'=>TRUE,'attributes'=>array('target'=>'_blank','title'=>$popup)));
    return $html;
}
function red_funciones_define_user_menu_net_resources_input_select_html(){
    $html='';
    if(hontza_is_sareko_id_red()){
        $icono=red_funciones_get_user_menu_network_auth_icono($url_auth);
        if(hontza_is_mostrar_recursos_compartidos_del_servidor_red()){
            $input_select=red_funciones_define_user_menu_net_resources_input_select_select($url_auth);
            if(!empty($input_select)){
                $html='<li style="float:left;padding-left:0px;">'.$icono.$input_select.'</li>';
            }
        }else{
           $input_select=red_funciones_define_user_menu_net_resources_input_select_select($url_auth,1);
            if(!empty($input_select)){
                $html='<li style="float:left;padding-left:0px;">'.$icono.$input_select.'</li>';
            }       
        }
    }
    return $html;
}
function red_funciones_define_user_menu_net_resources_input_select_select($url_auth,$is_solo_link_servidor_in=0){
    global $base_url;
    global $user;
    if(red_funciones_is_desplegables_comprimidos()){
        return red_funciones_define_user_menu_net_resources_input_select_select_comprimidos($url_auth,$is_solo_link_servidor_in);
    }
    $result=array();
    $is_solo_link_servidor=$is_solo_link_servidor_in;
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }else{
        $is_solo_link_servidor=1;
    }
    /*if($is_solo_link_servidor){
        return '';
    }*/
    //
    $result[]='<select id="menu_user_net_resources_select" name="menu_user_net_resources_select" style="clear:both;">';
    $result[]='<option value="none">'.t('Net Resources').'</option>';
    if(!$is_solo_link_servidor){
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_fuentes_servidor').'">'.t('Download Sources').'</option>';
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_canales_servidor').'">'.t('Download Channels').'</option>';
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_facilitadores_servidor').'">'.t('Download facilitators').'</option>';
        $result[]='<option value="'.url('red/fuentes-pipes/todas').'">'.t('Share Sources').'</option>';
        $result[]='<option value="'.url('red/canales/todas').'">'.t('Share Channels').'</option>';
        $result[]='<option value="'.url('red/facilitadores/todas').'">'.t('Share Facilitators').'</option>';
    }
    $result[]='<option value="'.$url_auth.'">'.t('Shared Groups').'</option>';
    if(!$is_solo_link_servidor){
        if(hontza_is_sareko_id_red()){
          if(!hontza_is_user_anonimo()){
            if(hontza_user_access_red_local()){ 
               $result[]='<option value="'.url('red_local/fuentes_local').'"'.$selected_fuentes_local.'>'.t('Shared Resources').'</option>';        
            }
          }
        }
    }    
    $result[]='</select>';
    //
    $js='
        $(document).ready(function()
        {
            $("#menu_user_net_resources_select option")[0].selected=true;
            $("#menu_user_net_resources_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_net_resources_select option")[0].selected=true;
                }
                //alert(my_menu_user_url);                
            });            
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function red_funciones_get_user_roles_popup($uid_in='',$is_array=0){
    global $user;
    if(empty($uid_in)){
        $my_user=clone $user;
    }else{
        $my_user=user_load($uid_in);
    }
    $result=array();
    if($my_user->uid==1){
        $result[]=t('Root');
    }
    //
        if(isset($my_user->roles) && !empty($my_user->roles)){
            $rol_order_array=red_funciones_define_rol_order_array();
            foreach($rol_order_array as $key=>$label){
                if(!empty($key) && $key=='chief_editor'){
                    $chief_editor_uid=hontza_grupos_get_chief_editor_uid();
                    if(!empty($chief_editor_uid) && $my_user->uid==$chief_editor_uid){
                        $result[]=$label;
                    }
                }else if(!empty($key) && $key=='group_owner'){
                    $owner_uid=hontza_grupos_mi_grupo_get_creador_grupo_uid();
                    if(!empty($owner_uid) && $my_user->uid==$owner_uid){
                        $result[]=$label;
                    }
                }else{
                    if(isset($my_user->roles[$key]) && !empty($my_user->roles[$key])){
                        $result[]=$label;    
                    }
                }    
            }        
        }
    if($is_array){
        return $result;
    }    
    return implode("\n",$result);
}
function red_funciones_define_rol_order_array(){    
    $rol_order_array=array();    
    $rol_order_array[3]=t('Administrator');
    $rol_order_array[10]=t('Developer');
    $rol_order_array[4]=t('Groups Creator');
    $rol_order_array['group_owner']=t('Group Owner');
    $rol_order_array['chief_editor']=t('Editor in chief');
    $rol_order_array[6]=t('Groups Administrator');
    $rol_order_array[9]=t('Expert');
    $rol_order_array[8]=t('CI Consultant');
    $rol_order_array[2]=t('User');
    $rol_order_array[5]=t('Guest');            
    $rol_order_array[7]=t('Demo');    
    $rol_order_array[1]=t('Anonymous');           
    //intelsat-2015
    //$rol_order_array[12]=t('Reader');    
    $reader_role_id=_role_id('Lector');
    if(!empty($reader_role_id)){
        $rol_order_array[$reader_role_id]=t('Reader');
    }
    $basico_role_id=_role_id('Basico');
    if(!empty($basico_role_id)){
        $rol_order_array[$basico_role_id]=t('Basic Reader');
    }
    $traductores_role_id=_role_id('Traductores');
    if(!empty($traductores_role_id)){
        $rol_order_array[$traductores_role_id]=t('Translator');
    }            
    return $rol_order_array;
}
function red_funciones_get_user_menu_network_auth_icono(&$my_url){
   global $language,$base_url;
   if(hontza_is_user_anonimo()){
       return '';
   }
   $my_lang='';
   if($language->language!='en'){
    $my_lang=$language->language.'/';
   }
   $html='';
    //if(hontza_is_red_hoja()){
    if(hontza_is_sareko_id_red()){
        //intelsat-2015
        //if(hontza_is_sareko_user()){
        $is_sareko_user=hontza_is_sareko_user();
        $alguna_vez_has_estado_conectato=red_funciones_alguna_vez_has_estado_conectato();
        if($is_sareko_user || $alguna_vez_has_estado_conectato){
            $subdominio='';
            $user_login_enviar=hontza_define_user_login_enviar();
            $my_url=red_compartir_define_redalerta_servidor_url().'/';
            $my_action='red_servidor/authenticate_red_alerta';
            $login_red_alerta_url=$my_url.$my_lang.$my_action.'/myprofile';
            $my_url=$login_red_alerta_url.'?user_login_enviar_get='.base64_encode($user_login_enviar).'&red_idioma='.trim($my_lang,'/');
            $id_a=$subdominio.'login_red_alerta_myprofile';
            //
            $html=array();
            //$html[]='<div style="padding-top:20px;float:right;">';
            //$html[]=l(t('Network'),$my_url,array('absolute'=>TRUE,'attributes'=>array('id'=>$id_a,'class'=>'a_login_red_alerta_class')));
            $label_network=t('Network');
            
            $icono_name='network_auth_menu';
            $grupo_local_icon_name='';
            $my_icon=red_compartir_grupo_get_icono_red_alerta('',0,1,$grupo_local_icon_name);
            if($grupo_local_icon_name=='grupo_local_no_conectado'){
                $icono_name='icons/grupo_local_no_conectado';
            }else{
                if(!hontza_is_sareko_user() && red_funciones_alguna_vez_has_estado_conectato()){
                    $icono_name='icons/grupo_local_no_conectado';
                }
            }
            //
            $src=$base_url.'/sites/all/themes/buho/images/'.$icono_name.'.png';
            $img_network='<img style="margin-top:2px;" src="'.$src.'" alt="'.$label_network.'" title="'.$label_network.'"/>';
                                    
            $html[]=l($img_network,$my_url,array('html'=>true,'absolute'=>TRUE,'attributes'=>array('id'=>$id_a,'class'=>'a_login_red_alerta_class')));
            //$html[]='</div>';
            //$is_target_blank=0;
            $is_target_blank=1;
            hontza_login_red_alerta_formulario($login_red_alerta_url.'&red_idioma='.trim($my_lang,'/'),'myprofile',1,'',$user_login_enviar,$is_target_blank);
            return implode('',$html);
       }
       //intelsat-2016
       /*$my_url='http://network.hontza.es';
       if(red_is_network_sareko_id()){
            $my_url='http://network.hontza.es';
       }else{*/
            $my_url=red_get_servidor_central_url();            
       //} 
   }       
    return $html;
}
function red_funciones_set_menutop_active_trail($menutop_in){
    $menutop=$menutop_in;
    $sep='<li class="leaf';
    $my_array=explode($sep,$menutop);
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if($i>0){
                $pos=strpos($v,'class="active"');
                if($pos===FALSE){
                    //
                }else{
                    $pos2=strpos($v,'active-trail');
                    if($pos2===FALSE){
                        $my_array[$i]=' active-trail '.$v;
                    }
                }
            }
        }
    }   
    $menutop=implode($sep,$my_array);
    return $menutop;
}
function red_funciones_guia_usuario_callback(){
    //gemini-2014-network
    $file_name='users_guide';
    //if(red_is_network_sareko_id()){
    //intelsat-2015
    //if(!red_funciones_is_sareko_id_alerta()){
    //intelsat-2015
    //if(!red_is_subdominio_red_alerta()){
    //intelsat-2016
    if(!red_is_subdominio_red_alerta() && !red_crear_usuario_is_crear_usuario_net()){
        $file_name='users_network_guide';
    }
    //
    red_funciones_print_guia($file_name);
    exit();
}
function red_funciones_guia_administrador_callback(){    
    //gemini-2014-network
    $file_name='admins_guide';
    //if(red_is_network_sareko_id()){
    //intelsat-2015
    //if(!red_funciones_is_sareko_id_alerta()){
    //intelsat-2015
    //if(!red_is_subdominio_red_alerta()){
    //intelsat-2016
    if(!red_is_subdominio_red_alerta() && !red_crear_usuario_is_crear_usuario_net()){
        $file_name='admins_network_guide';
    }
    //
    red_funciones_print_guia($file_name);
    exit();
}
function red_funciones_print_guia($file_name){
    //intelsat-2016
    /*if(hontza_registrar_is_registrar_activado()){
        red_registrar_hontza_file_get_contents(0,'');    
    }*/
    //intelsat-2016
    if(red_crear_usuario_is_crear_usuario_net()){
        $filepath='sites/default/files/'.$file_name.'.pdf';
        //$filepath=panel_admins_guias_get_file_url($file_name);
    }else{
        //$filepath='/home/hontza3_files/'.$file_name.'.pdf';
        $filepath=panel_admin_guias_get_filepath($file_name);
    }
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false); // required for certain browsers
    header('Content-type: application/pdf');
    //header("Content-Disposition: attachment; filename=\"" . basename($filepath) . "\";");
    header("Content-Transfer-Encoding: binary");
    /*if(!red_crear_usuario_is_crear_usuario_net()){
        header("Content-Length: " . filesize($filepath));
    }*/
    //intelsat-2015
    if(red_is_guia_internet()){
        $content=red_get_guia_internet_content($file_name);
        print $content;
        exit();
    }else{
        readfile($filepath);
    }
    exit();
}
function red_funciones_unset_shared_group($my_list){
    global $user;
    if(hontza_is_servidor_red_alerta()){
        $grupo_shared=red_servidor_get_grupo_shared();
        if(isset($grupo_shared->nid) && !empty($grupo_shared->nid)){
            $result=array();
            foreach($my_list as $i=>$row){
                if($user->uid==1){
                    $result[]=$row;
                }else{
                    if($grupo_shared->nid!=$row->nid){
                        $result[]=$row;
                    }
                }
            }
            return $result;
        }        
    }
    return $my_list;
}
function red_funciones_repase_access_grupo_shared($is_return){
    global $user;
    if(hontza_is_servidor_red_alerta()){
        if(isset($user->uid) && !empty($user->uid)){
            if(red_servidor_is_grupo_shared()){
                if(hontza_is_node_view()){
                    return 1;    
                }else if(hontza_is_node_edit()){
                    $node=my_get_node();
                    if(in_array($node->type,array('supercanal','fuentedapper'))){
                        if($user->uid==$node->uid){
                            return 1;
                        }
                    }
                }
                //if(!red_funciones_is_logout()){
                if(hontza_is_repase_access_grupo_shared()){    
                    if($user->uid==1){
                        if($is_return){
                            return 1;
                        }
                    }else{
                        if($is_return){
                            return 0;
                        }else{
                            drupal_access_denied();
                            exit();
                        }
                    }
                }
            }
        }    
    }    
    if($is_return){
        return 1;
    }
}
function red_funciones_is_logout(){
    $param0=arg(0);
    if($param0=='logout'){
        return 1;
    }
    return 0;
}
function red_funciones_get_preguntas_clave_inicio(){
  $html=array();
  $content=my_get_grupo_reto_content(1); 
  if(empty($content)){
      return '';
  }  
  $html[]=$content;  
  return implode("",$html);
}
function red_funciones_get_nube_de_etiquetas_inicio($is_publico=0){
  $block=tagadelic_block('view',3);
  if(isset($block['content']) && !empty($block['content'])){
      $html=array();            
      $help_block=help_popup_block(2954);
      if($is_publico){
          $help_block='';
      }
        $html[]='<div class="block block-tagadelic block-even region-odd clearfix " id="block-tagadelic-3">';
        //intelsat-2015
        $title=t('Tag Cloud');
        $icono=my_get_icono_action('tag_left',$title,'tag_left').'&nbsp;';
        //
        $html[]='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
        $html[]='<div class="content">';
        $html[]=$block['content'];      
        $html[]='</div>';
        $html[]='</div>';      
      return implode('',$html);
  }
  return '';
}
function red_funciones_get_noticias_validadas_inicio(){
    $content='';
    $my_view_name = 'og_home_noticiasvalidadas_dash';
    $my_display_name = 'block_1';
    $my_view = views_get_view($my_view_name);
    if ( is_object($my_view) ) {
      $my_view->set_display($my_display_name);
      $my_view->pre_execute();
      $content=$my_view->render($my_display_name);
    }
    if(!empty($content) && !red_funciones_is_view_content_empty($content)){
        $html=array();            
        $help_block=help_popup_block(2951);
        $html[]='<div class="block block-views block-odd region-odd clearfix " id="block-views-d640a64f46f55241f1a9ed29d2536ef2">';
        //intelsat-2015
        $title=t('Monitoring - Validated News');
        $icono=my_get_icono_action('canal',$title,'canal').'&nbsp;';
        //
        $html[]='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
        $html[]='<div class="content">';
        $html[]=$content;      
        $html[]='</div>';
        $html[]='</div>';      
      return implode('',$html);
    }
    return '';
}
function red_funciones_get_respuestas_inicio(){
  $html=array();
  $html[]=hontza_get_response_del_grupo_region(1);  
  return implode("",$html);
}
function red_funciones_get_wikis_inicio(){
    $content='';
    $my_view_name = 'og_home_areadetrabajo';
    $my_display_name = 'block_1';
    $my_view = views_get_view($my_view_name);
    if ( is_object($my_view) ) {
      $my_view->set_display($my_display_name);
      $my_view->pre_execute();
      $content=$my_view->render($my_display_name);
    }
    if(!empty($content) && !red_funciones_is_view_content_empty($content)){
        $html=array();            
        $help_block=help_popup_block(2956);
        $html[]='<div class="block block-views block-even region-odd clearfix " id="block-views-og-home-areadetrabajo-block-1">';
        //intelsat-2015
        $icono=my_get_icono_action('trabajo_left',t('Collaboration')).'&nbsp;';
        //
        $html[]='<h3 class="title">'.$icono.t('Collaboration').$help_block.'</h3>';
        $html[]='<div class="content">';
        $html[]=$content;      
        $html[]='</div>';
        $html[]='</div>';      
      return implode('',$html);
    }
    return '';
}
function red_funciones_get_debates_inicio(){
    $content='';
    $my_view_name = 'og_home_areadebate';
    $my_display_name = 'block_1';
    $my_view = views_get_view($my_view_name);
    if ( is_object($my_view) ) {
      $my_view->set_display($my_display_name);
      $my_view->pre_execute();
      $content=$my_view->render($my_display_name);
    }
    if(!empty($content) && !red_funciones_is_view_content_empty($content)){
        $html=array();            
        $help_block=help_popup_block(2955);
        $html[]='<div class="block block-views block-even region-odd clearfix " id="block-views-og-home-areadebate-block-1">';
        //intelsat-2015
        $icono=my_get_icono_action('debate_left',t('Discussion')).'&nbsp;';
        //
        $html[]='<h3 class="title">'.$icono.t('Discussion').$help_block.'</h3>';
        $html[]='<div class="content">';
        $html[]=$content;      
        $html[]='</div>';
        $html[]='</div>';      
      return implode('',$html);
    }
    return '';
}
function red_funciones_is_view_content_empty($content){
    if(empty($content)){
        return 1;
    }
    $find='<div class="view-empty">';
    $pos=strpos($content,$find);
    if($pos===FALSE){
        //if(hontza_is_servidor_red_alerta()){
            $result=trim(strip_tags($content));            
            if(empty($result)){
                return 1;
            }else{
                $result=str_replace(t('Edit'),'',$result);
                $result=str_replace(t('Export'),'',$result);
                $result=str_replace(t('Clone'),'',$result);
                $result=trim(strip_tags($result));
                if(empty($result)){
                    return 1;
                }
            }
        //}
        return 0;
    }else{
        $result=substr($content,$pos+strlen($find));        
        $pos_end=strpos($result,'</div>');
        if($pos_end===FALSE){
            return 0;
        }else{
            $result=substr($result,0,$pos_end);
            $result=trim(strip_tags($result));            
            if(empty($result) || $result==t('No contents')){
                return 1;
            }
        }
    }   
    return 0;
}
function red_funciones_og_usuarios_block_pre_execute(&$view){
    hontza_empty_view_pre_execute($view);
}
function red_funciones_set_node_link_servidor_alerta($param){
    global $user;    
    if(hontza_is_servidor_red_alerta()){
        if(hontza_is_grupo_shared){
            $node=my_get_node();
            if($user->uid==1){
                return '';
            }else{
                if(in_array($node->type,array('canal_de_supercanal','canal_de_yql','servicio'))){
                    $result='<li class="active">'.l(t('View'),'node/'.$node->nid,array('attributes'=>array('title'=>t('View'))));                
                    return $result;
                }    
            }
        }
    }
    return '';
}
function red_funciones_is_show_shared_block_menu_left(){
    if(hontza_is_servidor_red_alerta()){    
        if(is_super_admin()){
            return 1;
        }
        if(red_servidor_is_grupo_shared()){
            return 0;
        }
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            return 0;
        }
    }
    return 1;
}
function red_funciones_is_show_language_in_options($code){
    //if(hontza_is_sareko_id_red() || hontza_canal_rss_is_visualizador_activado()){
    if(hontza_is_sareko_id_red()){
        //intelsat-2015
        //if(!red_is_network_sareko_id()){
        if(!red_is_network_sareko_id()){
            if(in_array($code,array('eu'))){
                return 0;
            }
        }
    }
    return 1;
}
function red_funciones_is_administrador_grupo(){
    global $user;
    if(is_administrador_grupo(1)){
        return 1;
    }
    $chief_editor_uid=hontza_grupos_get_chief_editor_uid();
    if(!empty($chief_editor_uid) && $user->uid==$chief_editor_uid){
        return 1;
    }
    $owner_uid=hontza_grupos_mi_grupo_get_creador_grupo_uid();
    if(!empty($owner_uid) && $user->uid==$owner_uid){
        return 1;
    }
    /*if(isset($user->roles[ADMINISTRADOR_DE_GRUPO])){
        return 1;
    }
    if(isset($user->roles[CREADOR])){
        return 1;
    }*/
    return 0;
}
function hontza_is_repase_access_grupo_shared(){
    if(red_funciones_is_logout()){
       return 0;        
    }
    $param0=arg(0);
    if(!empty($param0)){
        if(in_array($param0,array('cambiar_idioma'))){
            return 0;
        }
    }
    return 1;
}
function red_funciones_og_categorias_fuentes_block_pre_execute(&$view){
    if(!red_funciones_is_show_shared_block_menu_left()){
        hontza_empty_view_pre_execute($view);
    }
}
function red_funciones_define_menu_primary_links(){
    return red_funciones_define_menu_navigation_links(variable_get('menu_primary_links_source', 'primary-links'));
}
function red_funciones_define_menu_navigation_links($menu_name, $level = 0) {
  // Don't even bother querying the menu table if no menu is specified.
    
  if (empty($menu_name)) {
    return array();
  }
  // Get the menu hierarchy for the current page.
  $tree = menu_tree_page_data($menu_name);
  
  // Go down the active trail until the right level is reached.
  while ($level-- > 0 && $tree) {
    // Loop through the current level's items until we find one that is in trail.
    while ($item = array_shift($tree)) {
      if ($item['link']['in_active_trail']) {
        // If the item is in the active trail, we continue in the subtree.
        $tree = empty($item['below']) ? array() : $item['below'];
        break;
      }
    }
  }

  // Create a single level of links.
  $links = array();
  foreach ($tree as $item) {
    if (!$item['link']['hidden']) {
      $class = '';
      $l = $item['link']['localized_options'];
      $l['href'] = $item['link']['href'];
      //gemini-2014
      //$l['title'] = $item['link']['title'];
      $l['title'] = $item['link']['link_title'];
      if ($item['link']['in_active_trail']) {
        $class = ' active-trail';
      }
      // Keyed with the unique mlid to generate classes in theme_links().
      $links['menu-'. $item['link']['mlid'] . $class] = $l;
    }
  }
  return $links;
}
function red_funciones_is_desplegables_comprimidos(){
    return 1;
}
function red_funciones_define_user_menu_input_select_comprimidos(){
    global $base_url;
    global $user;
    $result=array();
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }
    //
    $result[]='<select id="menu_user_select" name="menu_user_select" style="clear:both;padding-left:0px;">';
    $selected=' selected="selected"';
    $result[]='<option value="none"'.$selected.'>'.$user->name.'</option>';
    //intelsat-2015
    $profile_url=red_crear_usuario_get_profile_url();
    $result[]='<option value="'.$profile_url.'">'.t('---------------- My').'</option>';
    $result[]='<option value="'.$profile_url.'">'.t('My Profile').'</option>';
    //intelsat-2015
    //$result[]='<option value="'.url('mis-contenidos').'">'.t('Contents').'</option>';
    //
    //intelsat-2015
    if(!hontza_canal_rss_is_usuario_basico()){
        $result[]='<option value="'.url('contenidos').'">'.t('My Contents').'</option>';
    }    
    $result[]='<option value="'.url('mis-grupos').'">'.t('My Groups').'</option>';
    $result[]='<option value="'.url('alerta_user/'.$user->uid.'/my_list').'">'.t('My Alerts').'</option>';
    if(hontza_social_is_activado()){
        if(hontza_social_is_grupo_semantico_social()){
            $result[]='<option value="'.url('social_learning/collections').'">'.t('Collections').'</option>';
        }    
    }
    //intelsat-2015
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        $result[]='<option value="'.url('oferta_demanda').'">'.t('Offer-Request').'</option>';
    }
    //
    /*if(hontza_solr_funciones_is_bookmark_activado()){
        $result[]='<option value="'.url('hontza_solr/bookmarks').'">'.t('Bookmarks').'</option>';
    }*/
    //
    if(hontza_is_sareko_id_red()){
      if(!hontza_is_user_anonimo()){
        if(hontza_user_access_recursos_red_alerta()){ 
           $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
           $result[]='<option value="'.url('red_red/fuentes').'">'.t('Network').'</option>';           
        }        
      }
    }
    //intelsat-2015
    //se ha comentado
    /*if(hontza_is_mostrar_recursos_compartidos_del_servidor_red()){
        if(!is_super_admin()){
            $result[]='<option value="'.url('red_compartir/borrarme_del_servidor').'">'.t('Delete my account').'</option>';
        }    
    }*/
    //
    $result[]='<option value="delete">'.t('---------- Delete').'</option>';
    $result[]='<option value="'.url('user/'.$user->uid.'/delete').'">'.t('Delete User').'</option>';        
    $result[]='<option value="logout">'.t('-------------- Exit').'</option>';
    $result[]='<option value="logout">'.t('Logout').'</option>';
    $result[]='</select>';
    if(!empty($purl)){
        $purl=$purl.'/';
    }
    //
    //intelsat-2015
    $url_logout=url('logout');
    if(red_crear_usuario_is_activado()){
        $my_lang='/es';
        $url_logout=url($base_url.$my_lang.'/logout');
    }
    $js='
        $(document).ready(function()
        {
            $("#menu_user_select option")[0].selected=true;
            $("#menu_user_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url=="logout"){
                    location.href="'.$url_logout.'";
                }else if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_select option")[0].selected=true;
                }
                //alert(my_menu_user_url);                
            });            
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function red_funciones_define_user_menu_net_resources_input_select_select_comprimidos($url_auth,$is_solo_link_servidor_in=0){
    global $base_url;
    global $user;
    $result=array();
    $is_solo_link_servidor=$is_solo_link_servidor_in;
    //intelsat-2016
    $servidor_central_url=red_get_servidor_central_url();        
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }else{
        $is_solo_link_servidor=1;
    }
    //
    /*
    //gemini
    $is_sareko_user=hontza_is_sareko_user();
    if(!$is_sareko_user){
        return '';
    }*/
    //intelsat-2015
    if(hontza_canal_rss_is_usuario_basico()){
        return '';
    }
    $result[]='<select id="menu_user_net_resources_select" name="menu_user_net_resources_select" style="clear:both;">';
    $result[]='<option value="none">'.t('Net Resources').'</option>';
    if(!$is_solo_link_servidor){
        //$result[]='<option value="'.url('red_compartir/red_compartir_copiar_fuentes_servidor').'">'.t('-------- Download').'</option>';
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_canales_servidor').'">'.t('-------- Download').'</option>';
        //$result[]='<option value="'.url('red_compartir/red_compartir_copiar_fuentes_servidor').'">'.t('Sources').'</option>';
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_canales_servidor').'">'.t('Channels').'</option>';
        $result[]='<option value="'.url('red_compartir/red_compartir_copiar_facilitadores_servidor').'">'.t('Experts').'</option>';
        //$result[]='<option value="'.url('red/fuentes-pipes/todas').'">'.t('----------- Share').'</option>';
        $result[]='<option value="'.url('red/canales/todas').'">'.t('----------- Share').'</option>';
        //$result[]='<option value="'.url('red/fuentes-pipes/todas').'">'.t('Sources').'</option>';
        $result[]='<option value="'.url('red/canales/todas').'">'.t('Channels').'</option>';
        $result[]='<option value="'.url('red/facilitadores/todas').'">'.t('Experts').'</option>';
    }
    //gemini    
    if(!empty($url_auth)){
        $result[]='<option value="'.$url_auth.'">'.t('---------- Shared').'</option>';    
        //$result[]='<option value="'.$url_auth.'">'.t('Groups').'</option>';
        $result[]='<option value="'.$url_auth.'">'.t('Central Server').'</option>';
    }
    if(red_facilitador_user_access() && hontza_canal_rss_is_facilitador_activado()){
        $label_servicios=t('Experts');
        $url_servicios=$servidor_central_url.'/facilitador/facilitadores_publicados';
        $selected_gestion_servicios='';
        $result[]='<option value="'.$url_servicios.'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';
    }
    //intelsat-2016
    hontza_registrar_add_registrados_central_server_link($result);
    if(!$is_solo_link_servidor){
        if(hontza_is_sareko_id_red()){
          if(!hontza_is_user_anonimo()){
            if(hontza_user_access_red_local()){ 
               $result[]='<option value="'.url('red_local/fuentes_local').'"'.$selected_fuentes_local.'>'.t('Resources').'</option>';        
            }
          }
        }
    }    
    $result[]='</select>';
    //
    $js='
        $(document).ready(function()
        {
            $("#menu_user_net_resources_select option")[0].selected=true;
            $("#menu_user_net_resources_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_net_resources_select option")[0].selected=true;
                }
                //alert(my_menu_user_url);                
            });            
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function red_funciones_define_user_menu_management_input_select_comprimidos(){
    global $base_url;
    global $user;
    $result=array();
    $gestion_array=array();
    //intelsat-2016
    $servidor_central_url=red_get_servidor_central_url();      
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }
    //
    $result[]='<select id="menu_user_gestion_select" name="menu_user_gestion_select" "style="clear:both;">';
    $selected='';
    $selected_panel_de_gestion='';
    $selected_panel_gestion_grupos='';
    $selected_panel_gestion_usuarios='';
    $selected_translate_interface='';
    $selected_network='';
    $selected_fuentes_local='';
    $selected_gestion_faq='';
    $selected_gestion_estadisticas='';
    $selected_gestion_post_form='';
    $selected_gestion_claves='';
    $selected_gestion_fuentes='';
    $selected_gestion_canales='';
    $selected_gestion_respuesta='';
    $selected_gestion_wiki='';
    $selected_gestion_debate='';
    $selected_gestion_servicios='';
    $selected_gestion_noticias_publicas='';
    $selected_gestion_ayuda_popup='';
    //
    $result[]='<option value="none"'.$selected.'>'.t('Help/Management').'</option>';
    //if(hontza_is_sareko_id_red()){
    //if(hontza_is_sareko_id_red()){
    //intelsat-2015
    $url_guia_usuario=url('red/guia_usuario');
    $url_guia_administrador=url('red/guia_administrador');
        $result[]='<option value="'.$url_guia_usuario.'">'.t("------------- Guides").'</option>';
        $result[]='<option value="'.$url_guia_usuario.'">'.t("For Users").'</option>';
        $result[]='<option value="'.$url_guia_administrador.'">'.t("For Admins").'</option>';
    if($user->uid==1 || red_is_administrador()){
        //intelsat-2016
        /*$gestion_array[]='<option value="'.url('panel_admin/ayuda').'"'.$selected_gestion_faq.'>'.t('---------------- Help').'</option>';
        $label_faq=t('Faq');
        $gestion_array[]='<option value="'.url('panel_admin/ayuda').'"'.$selected_gestion_faq.'>'.$label_faq.'</option>';*/
        $gestion_array[]='<option value="'.url('panel_admin/ayuda_popup').'"'.$selected_gestion_faq.'>'.t('---------------- Help').'</option>';        
    }/*else{
        $result[]='<option value="'.url('faq').'"'.$selected_ayuda.'>'.t('Help').'</option>';    
    }*/
    if($user->uid==1 || red_is_administrador()){
        $label_ayuda_popup=t('Popup');
        $gestion_array[]='<option value="'.url('panel_admin/ayuda_popup').'"'.$selected_gestion_ayuda_popup.'>'.$label_ayuda_popup.'</option>';        
    }
    $is_gestion_comprimido=0;
    //if ($user->roles[ADMINISTRADOR] or $user->uid == 1) {
    if(hontza_canal_rss_panel_admin_access()){        
        $is_gestion_comprimido=1;
        //intelsat-2015
        //$gestion_array[]='<option value="'.url('gestion').'"'.$selected_panel_de_gestion.'>'.t('---- Management').'</option>';
        //
        $gestion_array[]='<option value="'.url('panel_admin').'"'.$selected_panel_de_gestion.'>'.t('---- Management').'</option>';
        /*$label_panel=t('Panel');
        //intelsat-2015
        //$gestion_array[]='<option value="'.url('gestion').'"'.$selected_panel_de_gestion.'>'.$label_panel.'</option>';
        $gestion_array[]='<option value="'.url('panel_admin').'"'.$selected_panel_de_gestion.'>'.$label_panel.'</option>';
        //
        */
    }
    //intelsat-2015
    //if ($user->roles[CREADOR] || $user->uid == 1) {
    if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR] || $user->uid == 1) {    
    //if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR_DE_GRUPO] || $user->uid == 1) {
        if(hontza_user_access_gestion_usuarios()){
            if(!$is_gestion_comprimido){
                $is_gestion_comprimido=1;
                $gestion_array[]='<option value="'.url('gestion/usuarios').'"'.$selected_panel_gestion_usuarios.'>'.t('---- Management').'</option>';
            }
            $label_users=t('Users');
            $gestion_array[]='<option value="'.url('gestion/usuarios').'"'.$selected_panel_gestion_usuarios.'>'.$label_users.'</option>';
        }
        if(!$is_gestion_comprimido){
            $is_gestion_comprimido=1;
             $gestion_array[]='<option value="'.url('user-gestion/grupos/propios').'"'.$selected_panel_gestion_grupos.'>'.t('---- Management').'</option>';
        }        
        $label_groups=t('Groups');
        $gestion_array[]='<option value="'.url('user-gestion/grupos/propios').'"'.$selected_panel_gestion_grupos.'>'.$label_groups.'</option>';       
    }
    //if ($user->roles[ADMINISTRADOR] or $user->uid == 1) {
    $label_servicios=t('Experts');        
    if($user->uid == 1 || red_is_administrador()){
        if(!$is_gestion_comprimido){
            $is_gestion_comprimido=1;
            $gestion_array[]='<option value="'.url('admin/content/taxonomy/1').'"'.$selected_gestion_fuentes.'>'.t('---- Management').'</option>'; 
        }    
        $label_fuentes=t('Types of Sources');
        $gestion_array[]='<option value="'.url('admin/content/taxonomy/1').'"'.$selected_gestion_fuentes.'>'.$label_fuentes.'</option>';        
        $label_channels=t('Channels');
        $gestion_array[]='<option value="'.url('gestion/gestion_canales').'"'.$selected_gestion_canales.'>'.$label_channels.'</option>';        
        $label_channels=t('News');
        $gestion_array[]='<option value="'.url('panel_admin/items').'">'.$label_channels.'</option>';
        $label_channels=str_replace('_',' ',t('User_News'));
        $gestion_array[]='<option value="'.url('panel_admin/noticias').'">'.$label_channels.'</option>';
        $label_debate=t('Discussion');
        $gestion_array[]='<option value="'.url('panel_admin/debate').'"'.$selected_gestion_debate.'>'.$label_debate.'</option>';        
        //$label_wiki=t('Management - WIKI');
        $label_wiki=t('Collaboration');
        $gestion_array[]='<option value="'.url('panel_admin/collaboration').'"'.$selected_gestion_wiki.'>'.$label_wiki.'</option>';        
        /*$label_respuesta=t('Proposals');
        $gestion_array[]='<option value="'.url('admin/content/hontza/my_idea_settings').'"'.$selected_gestion_respuesta.'>'.$label_respuesta.'</option>';*/
        //$label_servicios=t('FACILITATORS');
        //intelsat-2016
        if(red_facilitador_user_access()){
            if(hontza_canal_rss_is_facilitador_activado()){
                $url_servicios=$servidor_central_url.'/facilitador/facilitadores';
                $gestion_array[]='<option value="'.$url_servicios.'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';        
            }else{
                $gestion_array[]='<option value="'.url('panel_admin/servicios').'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';        
            }
        }
        if(!hontza_is_sareko_id_red()){
            $label_noticias_publicas=t('Public News');
            $gestion_array[]='<option value="'.url('gestion/my_noticias_publicas').'"'.$selected_gestion_noticias_publicas.'>'.$label_noticias_publicas.'</option>';
        }
        //$url_analitycs=url('analytics');
        $url_analitycs=url($base_url.'/analytics',array('absolute'=>TRUE));
        $gestion_array[]='<option value="'.$url_analitycs.'"'.$selected_gestion_estadisticas.'>'.t('----------- Various').'</option>';
        $label_estadisticas=t('Statistics');
        $gestion_array[]='<option value="'.$url_analitycs.'"'.$selected_gestion_estadisticas.'>'.$label_estadisticas.'</option>';
        //$label_post_form=t('MESSAGES');        
        $label_post_form=t('Forms');
        $gestion_array[]='<option value="'.url('frases_post_formulario').'"'.$selected_gestion_post_form.'>'.$label_post_form.'</option>';        
        //$label_claves=t('PASSWORDS');
        $label_claves=t('Passwords');
        $gestion_array[]='<option value="'.url('gestion/claves').'"'.$selected_gestion_claves.'>'.$label_claves.'</option>';
        //intelsat-2015
        //intelsat-2016
        //if(hontza_canal_rss_is_facilitador_activado()){
        /*if(red_facilitador_user_access() && hontza_canal_rss_is_facilitador_activado()){
            $result[]='<option value="'.url('facilitador/facilitadores_publicados').'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';        
        }*/
        //  
    }else{
        //if($user->roles[CREADOR]){
        if(red_despacho_is_gestionar_tipos_fuente()){
            $label_fuentes=t('Sources');
            $gestion_array[]='<option value="'.url('admin/content/taxonomy/1').'"'.$selected_gestion_fuentes.'>'.$label_fuentes.'</option>';        
        }        
        $url_servicios=url('servicios');
        //intelsat-2015
        //intelsat-2016
        //if(hontza_canal_rss_is_facilitador_activado()){
        /*if(red_facilitador_user_access() && hontza_canal_rss_is_facilitador_activado()){
            $url_servicios=url('facilitador/facilitadores_publicados');
        //
        $result[]='<option value="'.$url_servicios.'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';
        */        
    }                             
    if ($user->roles[TRADUCTORES]) {
        $gestion_array[]='<option value="'.url('admin/build/translate').'"'.$selected_translate_interface.'>'.t('Translate interface').'</option>';
    } 
    /*if(hontza_is_sareko_id_red()){
      if(!hontza_is_user_anonimo()){
        if(hontza_user_access_recursos_red_alerta()){ 
           $result[]='<option value="'.url('red_red/fuentes').'"'.$selected_network.'>'.t('Network').'</option>';
        }
        if(hontza_user_access_red_local()){ 
           $result[]='<option value="'.url('red_local/fuentes_local').'"'.$selected_fuentes_local.'>'.t('Shared').'</option>';        
        }
      }
    }*/
    /*if(empty($gestion_array)){
        return '';
    }*/
    $result[]=implode('',$gestion_array);
    $result[]='</select>';
    if(!empty($purl)){
        $purl=$purl.'/';
    }
    //
    $js='
        $(document).ready(function()
        {
            $("#menu_user_gestion_select option")[0].selected = true;
            $("#menu_user_gestion_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_gestion_select option")[0].selected = true;
                }
                //alert(my_menu_user_url);                
            });
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function red_funciones_get_languages_ordenados($languages){
    $result=array();
    $my_array=array();
    if(!empty($languages)){
        foreach($languages as $code=>$lang){
            $my_array[]=$code;
        }
    }
    sort($my_array);
    if(!empty($my_array)){
        foreach($my_array as $i=>$my_code){
            if(isset($languages[$my_code])){
                $result[$my_code]=$languages[$my_code];
            }    
        }
    }
    return $result;
}
//intelsat-2015
//function red_funciones_alerta_financiado_por_html(){
function red_funciones_alerta_financiado_por_html($is_link=0,$is_alerta_custom_css=0){    
    global $base_url;
    $img_src=$base_url.'/sites/default/files/my_images/fse_logo.png';
    $html=array();
    //intelsat-2015
    $background_color=red_alerta_financiado_por_background_color();
    $margin_top='';
    $margin_bottom='';
    $padding_top='';
    if(!hontza_canal_rss_is_visualizador_activado()){
        //$margin_top='margin-top:50px;';
        $margin_top='margin-top:60px;';
        $margin_bottom='margin-bottom:-25px;';
        if(!hontza_is_user_anonimo()){
            //$margin_top='margin-top:40px;';
            $margin_top='margin-top:50px;';
        }
        if(red_is_subdominio_alerta()){
            $padding_top='padding-top:10px;';
            if($is_alerta_custom_css || red_movil_is_activado()){    
                $background_color='';
                $margin_top='';
                $margin_bottom='';
                $padding_top='';                    
            }            
        }    
    }
    $html[]='<div class="rt-block div_financiado_por" style="'.$margin_top.$margin_bottom.$padding_top.$background_color.'">';
    //intelsat-2015
    $label=t('Financed by');
    if($is_link){
        $label=l($label,'http://ec.europa.eu/esf/home.jsp?langId=es',array('attributes'=>array('target'=>'_blank'),'absolute'=>true));
    }
    $span_style='';
    if(!hontza_canal_rss_is_visualizador_activado()){
        $span_style=' style="font-size:12px;color:#FFFFFF;"';
    }    
    $html[]='<div class="main-title module-title"><div class="module-title2"><div class="module-title3"><h2 class="title" style="visibility: visible;"><span'.$span_style.'">'.$label.'</span></h2></div></div></div>';
    $html[]='<div class="module-content">';
    $html[]='<div class="customtitle2">';
    $html[]='<p><img width="198" height="46" border="0" alt="Fondo Social Europeo" src="'.$img_src.'" title="'.t('Financed by').'" smartload="6"></p></div>';
    $html[]='<div class="clear"></div>';
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);
}
function red_funciones_get_language_menu_icono(){
    global $base_url;
    global $user;
    $popup=t('Select language');
    $language_label=red_funciones_get_select_language_label();
    if(!empty($language_label)){
        $popup=$language_label;
    }
    $html='<img src="'.$base_url.'/sites/all/themes/buho/images/language.png" alt="'.$popup.'" title="'.$popup.'" style="float:left;"/>';
    //$html=l($html,'user/'.$user->uid,array('html'=>TRUE,'attributes'=>array('target'=>'_blank','title'=>$popup,'style'=>'float:left;')));
    return $html;
}
function red_funciones_get_gestion_menu_icono(){
    global $base_url;
    global $user;
    $popup=t('Help/Management');
    $html='<img src="'.$base_url.'/sites/all/themes/buho/images/gestion.png" alt="'.$popup.'" title="'.$popup.'" style="float:left;"/>';
    $html=l($html,'gestion',array('html'=>TRUE,'attributes'=>array('target'=>'_blank','title'=>$popup,'style'=>'float:left;')));
    return $html;
}
function red_funciones_get_user_roles_li($uid){
    $html=array();
    $roles_array=red_funciones_get_user_roles_popup($uid,1);
    if(!empty($roles_array)){
        foreach($roles_array as $i=>$role_name){
            $html[]=$role_name.'<BR>';
        }
    }
    return implode('',$html);
}
function red_funciones_get_grupo_by_add_user(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='og'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='users'){
            $param2=arg(2);
            if(is_numeric($param2)){
                $grupo=node_load($param2);
                return $grupo;
            }
        }
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_funciones_is_show_menu_ideas_by_page_tpl(){
    //gemini-2014
    //AVISO::::para no enseñar el menú en las fichas
    return 0;
    //
    $my_array=array('idea','oportunidad','proyecto');
    $node=my_get_node();
    if(isset($node->type) && !empty($node->type) && in_array($node->type,$my_array)){
        return 1;
    }
    return 0;
}
//gemini-2014
function red_funciones_flag_get_validador_node_uid($content_id){
    $validador_node_row=red_funciones_flag_get_validador_node_row($content_id);
    if(isset($validador_node_row->uid) && !empty($validador_node_row->uid)){
        return $validador_node_row->uid;
    }
    return '';
}
//gemini-2014
function red_funciones_flag_save_validador_node($content_id, $fid,$uid_in=''){
    global $user;
    $uid=$user->uid;
    if(!empty($uid_in)){
        $uid=$uid_in;
    }
    //fid=2 validado, fid=3 rechazado
    if($fid==2){
        $validador_node_row=red_funciones_flag_get_validador_node_row($content_id);
        if(!(isset($validador_node_row->id) && !empty($validador_node_row->id))){            
            db_query("INSERT INTO {validador_node} (content_id, uid, timestamp) VALUES (%d, %d, %d)",$content_id, $uid, time());
        //intelsat-2016                        
        }else{
            db_query("UPDATE {validador_node} SET uid=%d,timestamp=%d WHERE content_id=%d",$uid, time(),$content_id);        
        }
    }    
}
//gemini-2014
function red_funciones_flag_get_validador_node_row($content_id){
    $sql='SELECT * FROM {validador_node} WHERE content_id='.$content_id;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_funciones_is_sareko_id_alerta(){
    if(hontza_is_sareko_id_red(1,1)){
        return 1;
    }
    return 0;
}
function red_funciones_get_grupo_menu_max($my_list,$max){
    if(hontza_canal_rss_is_custom_menu_activado()){
        if(count($my_list)==1){
            return 0;
        }else{
            return 1;
        }
    }
    $kont=0;
    $letras=0;
    $letra_max=80;
    if(!empty($my_list)){
        foreach($my_list as $i=>$row){
            $len=strlen($row->title);
            $letras+=$len;
            if($letras>$letra_max){
                break;
            }
            $kont++;
        }
    }
    //
    if($kont>$max){
        return $max;
    }
    return $kont;
}
function red_funciones_get_subdominio_activado_menu_icono($row='',$with_icono_activado=0,$is_mis_grupos=0,$with_congelado_value=0,$is_congelado=0){
    global $base_url;
    global $user;
    $html='';            
    $is_desactivado=0;    
    $style='style="float:left;padding-right:5px;"';
    if($is_mis_grupos){
        $style='';
        //$html.='<div style="text-align:center">';
    }
    if($with_congelado_value){
        $is_desactivado=$is_congelado;
    }else{
        if(red_funciones_is_subdominio_congelado() || hontza_is_grupo_congelado($row,1)){
            $is_desactivado=1;
        }    
    }    
    if($is_desactivado){
        $popup=t('Channels deactivated');
        $icono_name='canales-off';
        if($is_mis_grupos){
            $icono_name='canal_off';
        }
        $html.='<img src="'.$base_url.'/sites/all/themes/buho/images/'.$icono_name.'.png" alt="'.$popup.'" title="'.$popup.'"'.$style.'/>';
        /*if($is_mis_grupos){
            $html.='</div>';
        }*/
        return $html;
    }else{
        //intelsat-2015
        if($with_icono_activado){
            $popup=t('Channels activated');
            $icono_name='canales-on';
            if($is_mis_grupos){
                $icono_name='canal_on';                
            }
            $html.='<img src="'.$base_url.'/sites/all/themes/buho/images/'.$icono_name.'.png" alt="'.$popup.'" title="'.$popup.'"'.$style.'/>';
            /*if($is_mis_grupos){
                $html.='</div>';
            }*/
            return $html;
        }
    }
    return '';
}
function red_funciones_is_subdominio_congelado(){
    //if(hontza_is_congelar_canal_sareko_id()){
        $active_refresh=variable_get('active_refresh_subdomain',0);
        if(empty($active_refresh)){
            return 1;
        }
    //}
    return 0;
}
function red_funciones_statistics_user_tracker_count($uid,$my_grupo){
  $where_time=hontza_get_usuarios_acceso_where_time();
  $kont=0;  
  if ($account = user_load(array('uid' => $uid))) {      
    $res = db_query('SELECT aid, timestamp, path, title,url FROM {accesslog} WHERE uid = %d AND %s',$account->uid,$where_time);
    $rows = array();
    while ($log = db_fetch_object($res)) {
       $url=purl_language_strip($log->url);
       //print $url.'<br>';              
       $kont++;
    }
  }   
  return $kont;  
}
function red_funciones_get_tipos_de_fuentes_options($options){
    return red_funciones_get_tipos_de_fuentes_by_lang($options);
}
function red_funciones_get_tipos_de_fuentes_by_lang($options){
    $result=array();
    if(!empty($options)){
        foreach($options as $tid=>$v){
           if(is_numeric($tid)){ 
            $term_name=taxonomy_get_term_name_by_language($tid,$v);
            $result[$tid]=$term_name;
           }
        }
    }
    $all['All']='<'.t('Any').'>';
    asort($result);
    $result=array_replace($all,$result);
    return $result;
}
function red_funciones_get_left($left){
    if(is_alerta_user()){
        return alerta_get_sidebar_left();
    }
    return $left;
}
function red_funciones_is_body_attributes_left($left){
    if(!empty($left) && (is_term_edit_orig('Categoría Servicios') || is_area_debate() || is_alerta_user())){
	return 1;
    }
    return 0;
}
function red_funciones_set_og_canales_busqueda_header(){
    //intelsat-2015
    $my_grupo=og_get_group_context();
    $my_grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;        
    }
    $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);    
    //
    $html=array();
    $html[]="<div class=\"mas create-separacion\">";
    $is_solr_activado=hontza_solr_is_solr_activado();
    if($is_solr_activado){
        $url='hontza_solr/busqueda_avanzada_solr';
    }else{
        $url='busqueda';
    }        
    //intelsat-2015
    $html[]="<?php print red_funciones_get_create_link(t('Create Search'),'".$url."',array('query'=>'".$query_busqueda_avanzada_solr."','attributes'=>array('id'=>'sin_flecha')));?>";
    $html[]="</div>";
    return implode('',$html); 
}
function red_funciones_set_view_options(&$view,$options){
    $result=$options;
    //intelsat-2015
    $icono='';
    if($view->name=='og_canales_busqueda'){
        $result['header']=red_funciones_set_og_canales_busqueda_header();
        //intelsat-2015
        //$style=' style="float:left;"';
        $icono=my_get_icono_action('save_current_search',t('Searches'),'','').'&nbsp;';
        //
        $result['title']=$icono.t('Searches');
    }else if(in_array($view->name,array('og_area_debate_my_block','og_area_debate_by_node'))){        
        $title=t('Discussion');
        if($view->name=='og_area_debate_my_block'){
            $icono=my_get_icono_action('debate_left',$title,'','').'&nbsp;';
        }/*else{
            $icono=my_get_icono_action('debate32',$title,'','').'&nbsp;';            
        }*/
        $result['title']=$icono.$title;
    }else{
        $result=hontza_canal_rss_set_view_options($view,$options);
    }
    return $result;
}
function red_funciones_add_create_wiki_by_block($vars){
    $html=array();
    $result='';
    if(!hontza_solr_search_is_usuario_lector()){
        //intelsat-2015
        $html[]=red_funciones_get_create_link(t('Create Wiki Document'), 'node/add/wiki',array('attributes'=>array('class'=>'a_create_user_news')));
        //intelsat-2016
        /*if(red_copiar_is_copiar_activado()){
            if(!red_copiar_is_grupo_conectado()){
                $html[]=compartir_documentos_get_importar_wiki_link();
                $html[]=compartir_documentos_get_importar_my_report_link();
            }    
        }*/        
        $result=implode('<br>',$html);    
        $result='<div class="views-summary views-summary-unformatted create-separacion">'.$result.'</div>';    
    }else{
        if(empty($vars['rows'])){
            return t('There are no contents');
        }
    }
    $result=$result.$vars['rows'];
    return $result;
}
function red_funciones_set_og_area_debate_my_block($vars){
    $html=array();
    //intelsat-2015
    $result='';
    if(!hontza_solr_search_is_usuario_lector()){
    //    
        //intelsat-2015
        $html[]=  red_funciones_get_create_link(t('Create Discussion'),'node/add/debate',array('attributes'=>array('class'=>'a_create_user_news')));
        $result=implode('<br>',$html);
        $result='<div class="views-summary views-summary-unformatted create-separacion">'.$result.'</div>';                
    }else{
        if(empty($vars['rows'])){
            return t('There are no contents');
        }
    }
    $result=$result.$vars['rows'];
    return $result;
}
function red_funciones_get_title_report_simbolo_img(){  
    $html=array();
    $html[]='<img src="http://'.$_SERVER['HTTP_HOST'].base_path().'sites/all/themes/buho/images/icons/reports32.png"/>';
    return implode('',$html);
}
function red_funciones_report_tag_link($node){
    return hontza_item_tag_link($node);
}
function red_funciones_report_comment_link($node){
    return hontza_item_comment_link($node);
}
function red_funciones_report_edit_link($node){
    return hontza_item_edit_link($node);
}
function red_funciones_report_delete_link($node){
    return hontza_item_delete_link($node);
}
function red_funciones_get_report_img(){  
    $html=array();
    $html[]='<img src="http://'.$_SERVER['HTTP_HOST'].base_path().'sites/all/themes/buho/images/icons/reports.png"/>';
    return implode('',$html);
}
function red_funciones_get_select_language_label(){
    global $language;
    /*if(isset($language->name) && !empty($language->name)){
        return $language->name;
    }*/
    if(isset($language->native) && !empty($language->native)){
        return $language->native;
    }
    return '';
}
function red_funciones_is_node_access($op,$node,$is_access=0){
    global $user;
    //if(in_array($op,array('update'))){
        if(is_super_admin()){
            if($is_access){
                return 1;
            }else{
                return 0;
            }    
        }
        
        if(hontza_is_user_anonimo()){        
            if(hontza_canal_rss_is_visualizador_activado()){
               if(is_node_add('noticia')){
                   return 1;
               }
               /*if(publico_is_vigilancia_node_view()){
                   return 1;
               }*/
            }   
        }
        if($user->uid==$node->uid){
            if($is_access){
                return 1;
            }else{
                return 0;
            }
        }
        
        $modo_estrategia=1;
        if(is_administrador_grupo($modo_estrategia)){
            if(isset($node->nid) && !empty($node->nid)){
                if(isset($node->type) && !empty($node->type)){
                    if(in_array($node->type,array('supercanal','fuentedapper','fuentehtml','debate','wiki','boletin_grupo_introduccion','boletin-grupo-despedida'))){
                        return 1;
                    }    
                }
            }else{
                if(red_funciones_add_node_in_array()){
                    return 1;
                }    
            }    
        }
    //}
    return 0;
}
function red_funciones_node_access($node){
    global $user;
    if(isset($user->og_groups) && !empty($user->og_groups)){
        if(isset($node->og_groups) && !empty($node->og_groups)){
            $key_array=array_keys($node->og_groups);
            foreach($user->og_groups as $grupo_nid=>$row){
                if(in_array($grupo_nid,$key_array)){
                    return TRUE;
                }
            }
        }
    }
    if(red_funciones_add_node_in_array()){
        return TRUE;
    }  
    return FALSE;
}
function red_funciones_add_node_in_array(){
    $node_type_array=array('boletin-grupo-introduccion','boletin-grupo-despedida');
    if(hontza_canal_rss_is_visualizador_activado()){
        $node_type_array[]='noticia';
    }
    if(!empty($node_type_array)){
        foreach($node_type_array as $i=>$node_type){
            if(is_node_add($node_type)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_funciones_view_set_mis_contenidos($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            if(isset($row->node_title) && !empty($row->node_title)){
                $result[$i]->node_title=red_funciones_resumir_titulo($row->node_title);
            }
        }
    }
    return $result;
}
function red_funciones_resumir_titulo($title,$max=54){
    //$max=54;
    $my_array=explode(' ',$title);
    foreach($my_array as $i=>$v){        
        $len=strlen($v);
        if($len>$max){
            $result=substr($title,0,$max).' ...';
            return $result;
        }
        
    }
    return $title;
}
function red_funciones_is_taxonomy_term(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='taxonomy'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='term'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                $param3=arg(3);
                if(empty($param3)){
                    return 1;                    
                }
            }
        }
    }
    return 0;
}
function red_funciones_unset_de_la_botonera_gris_del_node($result_in){
    //intelsat-2015
    //if(!is_super_admin()){
    //if(!red_is_administrador()){    
        /*$my_array=explode('</a>',$result_in);
        echo print_r($my_array,1);
        exit();*/
        return '';
    //}
    return $result_in;
}
function red_funciones_get_grupo_privacidad_name($row='',$tid_in=''){
    $tid=$tid_in;
    if(isset($row->type_of_group_tid) && !empty($row->type_of_group_tid)){
        $tid=$row->type_of_group_tid;
    }
    if(!empty($tid)){
        $term=taxonomy_get_term_by_language($tid);
        if(isset($term->name) && !empty($term->name)){
            return $term->name;
        }
        $term=taxonomy_get_term($tid);
        if(isset($term->name) && !empty($term->name)){
            return $term->name;
        }
    }
    return '';        
}
function red_funciones_get_filtro_por_categoria(){
    $tid=arg(2);
    if(red_despacho_is_activado()){
        if(red_functiones_is_canales_por_my_categorias()){
            return $tid;
        }
    }else{    
        if(red_functiones_is_canales_por_my_categorias()){
            $_SESSION['filtro_por_categoria']=$tid;
        }
        if(isset($_SESSION['filtro_por_categoria']) && !empty($_SESSION['filtro_por_categoria'])){
            $result=$_SESSION['filtro_por_categoria'];
            if(!red_despacho_is_types_of_sources_term($result)){
                return $result;
            }else{
                $_SESSION['filtro_por_categoria']='';
            }
        }
    }    
    return '';
}
function red_functiones_is_canales_por_my_categorias(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canales'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='my_categorias'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                return 1;
            }
        }
    }
    return 0;
}
function red_funciones_cambiar_consulta_canales_my_categorias_block_callback(){
    if(isset($_SESSION['filtro_por_categoria']) && !empty($_SESSION['filtro_por_categoria'])){
        unset($_SESSION['filtro_por_categoria']);
    }
    drupal_goto('vigilancia/validados');
    exit();
}
function red_funciones_limpiar_cache_subdominios_callback(){
    return 'Funcion desactivada';
    $sareko_id_array=array('LOKALA','ROOT');
    if(hontza_in_sareko_id($sareko_id_array)){
        $conexiones=diccionario_define_subdominios_conexiones();
        if(!empty($conexiones)){
            foreach($conexiones as $key=>$value){
                db_set_active($value);
                //cache_clear_all();
                //cache_clear_all('','cache_menu');
                red_funciones_cache_menu_clear();
                db_set_active();
                print $value.'<BR>';                        
            }    
        }
    }
}
function red_funciones_cache_menu_clear(){
    $sql='DELETE FROM {cache_menu} WHERE 1';
    db_query($sql);
    $sql='DELETE FROM {cache} WHERE 1';
    db_query($sql);
    menu_rebuild();
}
function red_funciones_user_profile_add_vista_compacta_field(&$form,&$form_state,$form_id){
   if(strcmp($form_id,'user_profile_form')==0){
       $uid='';
       if(isset($form['#uid']) && !empty($form['#uid'])){
           $uid=$form['#uid'];
       }
       //
       $is_vista_compacta=red_funciones_get_user_vista_compacta($uid);
       $form['vista_compacta_fs']=array(
           '#type'=>'fieldset',
           '#title'=>t('Compact display'),
           //intelsat-2015
           '#prefix'=>'<div style="clear:both;">',
           '#suffix'=>'</div>',
           //
       );       
       $form['vista_compacta_fs']['is_vista_compacta']=array(
           '#title'=>t('Compact display'),
           '#type'=>'checkbox',           
       );
       if(!empty($is_vista_compacta)){
           $form['vista_compacta_fs']['is_vista_compacta']['#attributes']['checked']='checked';
       }
   }
}
function red_funciones_get_user_vista_compacta($uid){
   if(empty($uid)){
       return 0;
   } 
   $row=red_funciones_get_users_vista_compacta_row($uid);
   if(isset($row->is_vista_compacta) && !empty($row->is_vista_compacta)){
    return 1;
   }
   return 0;   
}
function red_funciones_get_users_vista_compacta_row($uid){
   $sql='SELECT * FROM {users_vista_compacta} WHERE uid='.$uid;
   $res=db_query($sql);
   while($row=db_fetch_object($res)){
       return $row;
   }
   $my_result=new stdClass();
   return $my_result;
}
function red_funciones_save_user_is_vista_compacta($my_user){
    //if(isset($my_user->is_vista_compacta) && !empty($my_user->is_vista_compacta)){
    if(isset($my_user->is_vista_compacta)){
        $is_vista_compacta=$my_user->is_vista_compacta;
        if(empty($is_vista_compacta)){
            $is_vista_compacta=0;
        }
        $row=red_funciones_get_users_vista_compacta_row($my_user->uid);
        if(isset($row->id) && !empty($row->id)){
            $sql='UPDATE {users_vista_compacta} SET is_vista_compacta='.$is_vista_compacta.' WHERE uid='.$my_user->uid;
        }else{
            $sql='INSERT INTO {users_vista_compacta}(uid,is_vista_compacta) VALUES ('.$my_user->uid.','.$is_vista_compacta.')';
        }
        db_query($sql);
    }
}
function red_funciones_is_vista_compacta(){
    //return 0;
    global $user;
    $row=red_funciones_get_users_vista_compacta_row($user->uid);
    if(isset($row->is_vista_compacta) && !empty($row->is_vista_compacta)){
        return 1;
    }
    return 0;
}
function red_funciones_get_node_vista_compacta_description($node){
    $result=hontza_content_resumen($node,0,0);
    return $result;
}
function red_funciones_cortar_node_title($title,$max_in=70){
    global $theme;
    //return $title.'='.strlen($title).'<br>';
    $max=$max_in;
    if($max==70){
        //if($theme=='fluid-buho'){
        if(red_funciones_is_tema_fluid_buho()){
            $max=140;
        }
    }
    $result=$title;
    //$len=strlen($result);
    $len=mb_strlen($result);
    if($len>$max){
        //$result=substr($result,0,$max).' ...';
        $result=mb_substr($result,0,$max).' ...';
        if(empty($result)){
            return $title;
        }
    }
    return $result;
}
function red_funciones_cortar_grupo_title($node_title){
    //return $node_title;
    return red_funciones_cortar_node_title($node_title,20);
}
//intelsat-2016
//function red_funciones_get_node_description($node){
function red_funciones_get_node_description($node,$is_crm_exportar_item=0){    
    if(isset($node->nid) && !empty($node->nid) && isset($node->vid) && !empty($node->vid)){
        $sql='SELECT * FROM {node_revisions} WHERE nid='.$node->nid.' AND vid='.$node->vid;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            //intelsat-2016
            if($is_crm_exportar_item){
                if(isset($row->body) && !empty($row->body)){
                    return $row->body;
                }else{
                    return $row->teaser;
                }    
            }
            $result=strip_tags($row->body);
            $result=html_entity_decode($result);
            return $result;
        }
    }
    //
    //intelsat-2016
    /*$my_result=new stdClass();
    return $my_result;*/
    return '';
}
function red_funciones_get_filtro_por_canal(){   
    if(hontza_is_canales() || hontza_is_canal_usuarios()){
        //$_SESSION['filtro_por_canal']=arg(1);
        return arg(1);
    }
    /*if(isset($_SESSION['filtro_por_canal']) && !empty($_SESSION['filtro_por_canal'])){
        return $_SESSION['filtro_por_canal'];
    }*/    
    return '';
}
function red_funciones_set_active_canal_link($rows_in,$selected_canal_nid){
    if(hontza_is_canales() || hontza_is_canal_usuarios()){
        $sep='href="';
        $my_array=explode($sep,$rows_in);
        if(!empty($my_array)){
            foreach($my_array as $i=>$v){
                if($i>0){
                    $find='/canales/';
                    if(hontza_is_canal_usuarios()){
                        $find='/canal-usuarios/';
                    }
                    $pos=strpos($v,$find);
                    if($pos===FALSE){
                        $result[]=$sep.$v;
                    }else{
                        $pos=$pos+strlen($find);
                        $s=substr($v,$pos);
                        $pos_end=strpos($s,'">');
                        if($pos_end===FALSE){
                            $result[]=$sep.$v;
                        }else{                            
                            $s=substr($s,0,$pos_end);
                            if($s==$selected_canal_nid){
                                $result[$i-1]=str_replace('<div class="views-summary views-summary-unformatted">','<div class="views-summary views-summary-unformatted" style="color:black;">',$result[$i-1]);
                                //$result[$i-1]=str_replace('<div class="views-summary views-summary-unformatted">','<li class="nivel0">',$result[$i-1]);
                                $v=hontza_solr_funciones_add_solr_filter_icon($v);
                                $result[$i]=' class="active" style="color:black;" '.$sep.$v;
                                //$result[$i]=str_replace('</div>','</li>',$result[$i]);
                            }else{
                                $result[]=$sep.$v;
                            }
                        }
                    }
                }else{
                    $result[]=$v;
                }
            }
            return implode('',$result);
        }
    }
    return $rows_in;
}
function red_funciones_get_filter_activated_style(){
    return 'style="background-color: #2E5753;color:#FFFFFF;float:left;font-weight:bold;"';
}
function red_funciones_is_tema($tema){
    global $theme;
    if($tema=='fluid-buho'){
        //intelsat-2016
        $theme_array=array('fluid-buho');
        $sareko_id=strtolower(_SAREKO_ID);
        $theme_array[]=$sareko_id;
        if(in_array($theme,$theme_array)){
            return 1;
        }
    }else{    
        if($theme==$tema){
            return 1;
        }
    }    
    return 0;
}
function red_funciones_is_tema_fluid_buho(){
    return red_funciones_is_tema('fluid-buho');
}
function red_funciones_get_fluid_buho_templates_dir(){
    return 'sites/all/themes/buho/templates/overrides/fluid-buho/';
}
function red_funciones_get_responsable($nid,$is_secundario=0){ 
        $where=array();
	$where[]='1';
	if(!empty($nid)){
		$where[]='r.nid='.$nid;
	}
        //gemini-2014
        $table='content_field_responsable_uid';
        $field='field_responsable_uid_uid';
        if($is_secundario){
            $table='content_field_responsable_uid2';
            $field='field_responsable_uid2_uid';
        }
	$sql='SELECT r.* FROM '.$table.' r WHERE '.implode(' AND ',$where);
	
	$result = db_query($sql);
	 
	  while ($row = db_fetch_object($result)) {		
		if(!empty($row->$field)){
			return user_load(array('uid'=>$row->$field));
	  	}
	  }
	
	$my_result=(object) array();
			
	return $my_result;
}
function red_funciones_get_canal_pipe_id($node){
    $feeds_source=hontza_get_feeds_source($node->nid);
    if(isset($feeds_source->source) && !empty($feeds_source->source)){
        return hontza_get_pipe_id_by_canal_feeds_source($feeds_source->source);    
    }
    return '';
}
function red_funciones_set_canal_pipe_id_by_request($pipe_id,&$node){
    if(isset($_REQUEST['my_pipe_id']) && !empty($_REQUEST['my_pipe_id'])){
        $my_pipe_id=$_REQUEST['my_pipe_id'];
        if($my_pipe_id!=$pipe_id){
            $url_rss=$node->feeds['FeedsHTTPFetcher']['source'];
            $url_new=str_replace("?_id=".$pipe_id."&","?_id=".$my_pipe_id."&",$url_rss);
            $node->feeds['FeedsHTTPFetcher']['source']=$url_new;            
        }
    }    
}
function red_funciones_update_canal_pipe_id(&$node){
    if($node->type=='supercanal'){
       $my_pipe_id=$node->field_supercanal_fuente[0]['value'];
       $content_field_nid_fuente_canal=hontza_get_content_field_nid_fuente_canal($node->nid);
       if(!empty($content_field_nid_fuente_canal)){
           foreach($content_field_nid_fuente_canal as $i=>$row){
               $canal_nid=$row->nid;
               $feeds_source=get_feeds_source($canal_nid);
               if(isset($feeds_source->feed_nid) && !empty($feeds_source->feed_nid)){
                   $pipe_id=hontza_get_pipe_id_by_canal_feeds_source($feeds_source->source);
                   if($my_pipe_id!=$pipe_id){
                      $url_rss=$feeds_source->source;
                      $url_new=str_replace("?_id=".$pipe_id."&","?_id=".$my_pipe_id."&",$url_rss);
                      $source=$url_new;
                      $config=unserialize($feeds_source->config);
                      if(isset($config['FeedsHTTPFetcher']) && isset($config['FeedsHTTPFetcher']['source'])){
                          $url_rss=$config['FeedsHTTPFetcher']['source'];
                          $url_new=str_replace("?_id=".$pipe_id."&","?_id=".$my_pipe_id."&",$url_rss);
                          $config['FeedsHTTPFetcher']['source']=$url_new;
                          $new_config=serialize($config);
                          db_query('UPDATE {feeds_source} SET source="%s",config="%s" WHERE feed_nid=%d',$source,$new_config,$feeds_source->feed_nid);
                      }                      
                   }
               }
           }
       }
    }
}
function red_funciones_is_change_pipe_id($node){
    if(!red_funciones_validar_cambio_fuente_pipe_id_access()){
        return 0;
    }
    if($node->type=='supercanal'){
        if(isset($_POST['op']) && !empty($_POST['op']) && $_POST['op']==t('Save')){
            $my_pipe_id=$node->field_supercanal_fuente[0]['value'];
            /*$content_field_nid_fuente_canal=hontza_get_content_field_nid_fuente_canal($node->nid);
            if(!empty($content_field_nid_fuente_canal)){
                foreach($content_field_nid_fuente_canal as $i=>$row){
                    $canal_nid=$row->nid;
                    $feeds_source=get_feeds_source($canal_nid);
                    if(isset($feeds_source->feed_nid) && !empty($feeds_source->feed_nid)){
                        $pipe_id=hontza_get_pipe_id_by_canal_feeds_source($feeds_source->source);
                        if($my_pipe_id!=$pipe_id){
                            return 1;
                        }
                    }
                }
            }*/
            if(isset($node->bakup_fuente_pipe_id) && !empty($node->bakup_fuente_pipe_id)){
                if($my_pipe_id!=$node->bakup_fuente_pipe_id){
                    return 1;
                }
            }
        }     
    }
    return 0;
}
function red_funciones_validar_cambio_fuente_pipe_id_callback(){
    drupal_set_title(t('Source'));
    if(!red_funciones_validar_cambio_fuente_pipe_id_access()){
        drupal_access_denied();
        exit();
    }
    $tiene_canales=0;
    $fuente_nid=arg(2);
    $html=array();
    $html[]='<p><i>'.t('Yahoo! Pipes ID has been modified. Please click on "Validate" to pass the new ID to linked channels').':</i></p>';
    $content_field_nid_fuente_canal=hontza_get_content_field_nid_fuente_canal($fuente_nid);
    if(!empty($content_field_nid_fuente_canal)){
        $html[]='<ul>';
        foreach($content_field_nid_fuente_canal as $i=>$row){
            $canal=node_load($row->nid);
            if(isset($canal->nid) && !empty($canal->nid)){    
                $html[]='<li>'.$canal->title.'</li>';
                $tiene_canales=1;
            }    
        }
        $html[]='</ul>';
    }
    if($tiene_canales){
        $html[]=drupal_get_form('red_funciones_validar_cambio_fuente_pipe_id_form');
    }else{
        drupal_goto('node/'.$fuente_nid);
    }
    return implode('',$html);
}
function red_funciones_validar_cambio_fuente_pipe_id_form(){
    $form=array();
    $fuente_nid=arg(2);
    $form['fuente_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$fuente_nid,
    );
    $form['validate_btn']=array(
        '#type'=>'submit',    
        '#default_value'=>t('Validate'),
        '#prefix'=>'<div style="padding-top:5px">',
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'node/'.$fuente_nid),
        '#suffix'=>'</div>',
    );
    return $form;
}
function red_funciones_validar_cambio_fuente_pipe_id_form_submit($form,&$form_state){
    if(isset($form_state['values']['fuente_nid']) && !empty($form_state['values']['fuente_nid'])){
        $fuente_nid=$form_state['values']['fuente_nid'];
        $fuente=node_load($fuente_nid);
        if(isset($fuente->nid) && !empty($fuente->nid)){
            red_funciones_update_canal_pipe_id($fuente);
        }    
    }
    drupal_goto('node/'.$fuente_nid);
}
function red_funciones_is_validate_change_pipe_id(){
    $param0=arg(0);
    //
    if(!empty($param0) && $param0=='hontza'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='validar_cambio_fuente_pipe_id'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                return 1;
            }
        }
    }
}
function red_funciones_get_name_of_source_by_nid_fuente_canal($node,$is_enlace=1,$default_value=''){
    if(isset($node->field_nid_fuente_canal[0]['value']) && !empty($node->field_nid_fuente_canal[0]['value'])){
        $fuente_nid=$node->field_nid_fuente_canal[0]['value'];
        $fuente=node_load($fuente_nid);
        if(isset($fuente->nid) && !empty($fuente->nid)){
            if($is_enlace){
                return l($fuente->title,'node/'.$fuente->nid);
            }else{
                return $fuente->title;
            }    
        }
    }
    if(!empty($default_value)){
        return $default_value;
    }
    return '';
}
function red_funciones_validar_cambio_fuente_pipe_id_access(){
    if(is_administrador_grupo(1)){
        return 1;
    }
    $fuente_nid=arg(2);
    $fuente=node_load($fuente_nid);
    if(isset($fuente->nid) && !empty($fuente->nid)){
        if(isset($fuente->og_groups) && !empty($fuente->og_groups)){
            $group_nid_array=array_keys($fuente->og_groups);
            if(isset($group_nid_array[0]) && !empty($group_nid_array[0])){
                if(is_administrador_grupo(1,$group_nid_array[0])){
                    return 1;
                }
            }
        }
    }
    return 0;
}
//intelsat-2015
function red_funciones_get_inicio_login_on_access_denied($result_in){
    global $user,$base_url,$language;    
    if(isset($user->uid) && !empty($user->uid)){
        return $result_in;
    }
    //
    $result=$result_in.'<BR>';
    $text=t('Your session has expired. Please');
    //intelsat-2015
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url=request_uri();
        $url=hontza_solr_search_prepare_redirect_url($url);
        $url_info=parse_url($url);
        parse_str($url_info['query'],$query_array);
        if(isset($query_array['destination'])){
            unset($query_array['destination']);
        }
        $query=http_build_query($query_array,'','&');
        //$query=implode('&',$query_array);
        $url=url($url_info['path'],array('query'=>$query));
        $url=hontza_solr_search_prepare_redirect_url($url);
        $url=urlencode($url);
        $destination='destination='.$url;        
    }else{
        $destination=drupal_get_destination();
    }    
    //
    /*if($language->language=='es'){
        $text='Tu sesión ha expirado. Por favor,';
        $result.='<div style="padding-top:10px;">'.$text.' '.l('Inicia sesión','user',array('query'=>$destination)).'</div>';
    }else{*/
        $result.='<div style="padding-top:10px;">'.$text.', '.l('<b><u>'.t('Login here').'</u><b>','user',array('query'=>$destination,'html'=>true)).'</div>';
    //}
    return $result;
}
//intelsat-2015
function red_funciones_alguna_vez_has_estado_conectato(){
    if(hontza_is_sareko_user(0,'',1)){
        return 1;
    }
    return 0;
}
//intelsat-2015
function red_funciones_get_actualizacion_activado_menu_icono(){
    global $base_url;
    //return '';
    $html='';            
    if(is_super_admin()){    
        $is_activado=red_funciones_is_actualizacion_activado($row);
        $style='style="float:left;padding-right:5px;"';
        if($is_activado){
            $popup=t('Cron activated');
            $icono_name='actualizacion_activado';
            $html.='<img src="'.$base_url.'/sites/all/themes/buho/images/'.$icono_name.'.png" alt="'.$popup.'" title="'.$popup.'"'.$style.'/>';
            return $html;
        }else{
            $popup=t('Cron deactivated');
            $icono_name='actualizacion_desactivado';
            $html.='<img src="'.$base_url.'/sites/all/themes/buho/images/'.$icono_name.'.png" alt="'.$popup.'" title="'.$popup.'"'.$style.'/>';
            return $html;
        }
    }    
    return '';
}
function red_funciones_is_actualizacion_activado(&$result){
    $row='';
    $sql = 'SELECT w.wid, w.uid, w.severity, w.type, w.timestamp, w.message, w.variables, w.link, u.name FROM {watchdog} w INNER JOIN {users} u ON w.uid = u.uid WHERE w.type="cron" ORDER BY w.timestamp DESC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result=$row;
        return 1;
    }
    return 0;
}
//intelsat-2015
function red_funciones_set_gestion_servicios_titles($content){
    $result=$content;
    $my_array=array();
    $my_array[]=array('find'=>'<th class="views-field views-field-title">','title'=>t('Organisation'));
    $my_array[]=array('find'=>'<th class="views-field views-field-field-categoria-servicios-value">','title'=>t('Services'));
    foreach($my_array as $i=>$row){
        $result=red_funciones_set_gestion_servicios_title($result,$row['find'],$row['title']);
    }
    return $result;
}
//intelsat-2015
function red_funciones_set_gestion_servicios_title($content,$find,$title){
    $result=$content;
    $pos=strpos($result,$find);
    if($pos===FALSE){
        return $result;
    }
    $s=substr($result,$pos+strlen($find));
    $find2='</th>';
    $pos2=strpos($s,$find2);
    if($pos2===FALSE){
        return $result;
    }
    $s=substr($s,$pos2);
    $inicio=substr($result,0,$pos+strlen($find));
    $result=$inicio.$title.$s;
    return $result;
}
//intelsat-2015
function red_funciones_get_create_link($title,$url,$options_in=array()){
    $options=$options_in;
    $options['html']=TRUE;
    $icono=my_get_icono_action('add_left',$title).'&nbsp;';
    $result=l($icono.$title,$url,$options);
    return $result;        
}
//intelsat-2015
function red_funciones_set_title_help_icon($title_in){
    $html=array();
    $title=$title_in;
    if(is_fuentes_pipes_todas()){
        $title=t('Sources');
    }
    $html[]=$title;
    $html[]='<div style="float:right;">';
    if(hontza_solr_is_resultados_pantalla()){
        $html[]=hontza_solr_search_get_busqueda_avanzada_title_links();
        $html[]=my_show_help_icon();
    //intelsat-2015        
    }else if(hontza_canal_rss_is_carpeta_dinamica_guardada()){
        $html[]=hontza_canal_rss_get_busqueda_title_links();
        $html[]='<div style="float:right;padding-left:5px;">'.my_show_help_icon().'</div>';
    }    
    $html[]='</div>';
    return implode('',$html);                            
}