<?php
function hontza_grupos_mi_grupo_callback(){
    boletin_report_no_group_selected_denied();    
    $my_grupo=og_get_group_context();
    drupal_set_title(t('Group').': '.$my_grupo->title);
    $output=node_view($my_grupo);
    return $output;
}
function hontza_grupos_mi_grupo_get_type_of_group_string($node){
    $result='';
    $tid=0;
    if(isset($node->taxonomy) && !empty($node->taxonomy)){
        foreach($node->taxonomy as $key=>$term_row){
            if($term_row->vid==6){
                $tid=$key;
                $result=$term_row->name;
                break;
            }
        }
    }
    //
    if(!empty($tid)){
        $term=taxonomy_get_term_by_language($tid);
        if(isset($term->name) && !empty($term->name)){
            $result=$term->name;
        }
    }
    if($result=='Collaboration'){
        $result='Collaborative';
    }
    return $result;
}
function hontza_grupos_mi_grupo_get_group_language_string($node){
    if(isset($node->og_language) && !empty($node->og_language)){
        $lang_array=language_list();
        if(!empty($lang_array)){
            if(isset($lang_array[$node->og_language])){
                return $lang_array[$node->og_language]->native;
            }
        }
    }
    return t('Language neutral');
}
function hontza_grupos_get_chief_editor_username($my_grupo){
        $result='admin';
        if(isset($my_grupo->field_admin_grupo_uid[0]['value'])){
            $result=hontza_get_username($my_grupo->field_admin_grupo_uid[0]['value']);        
        }
    return $result;    
}
function hontza_grupos_get_administrators_html($node){
    $with_admin_grupo=1;
    $with_creador_grupo=0;
    $with_empty=0;
    $administrador_array=get_usuarios_creadores_y_administradores_grupo_options($with_admin_grupo,$with_creador_grupo,$with_empty,$node->nid);
    if(!empty($administrador_array)){
        $html=array();
        $html[]='<ul>';
        foreach($administrador_array as $uid=>$username){
            $html[]='<li>'.$username.'</li>';
        }
        $html[]='</ul>';
        return implode('',$html);
    }
    return '';
}
function hontza_grupos_get_active_tabs_html($node){
    if(isset($node->field_group_active_tabs) && !empty($node->field_group_active_tabs)){
        $html=array();
        $html[]='<ul>';
        foreach($node->field_group_active_tabs as $i=>$row){
            $html[]='<li>'.$row['view'].'</li>';
        }
        $html[]='</ul>';
        return implode('',$html);
    }    
    return '';    
}
function hontza_grupos_get_activate_channels_html($node){
    if(isset($node->field_group_active_refresh) && isset($node->field_group_active_refresh[0]) && isset($node->field_group_active_refresh[0]['value'])){
            $v=$node->field_group_active_refresh[0]['value'];
            if(!empty($v)){
                return t('Yes');
            }
    }
    return t('No');
}
function hontza_grupos_get_network_connected_html($node){
    if(hontza_is_sareko_id_red()){
        if(red_compartir_grupo_is_grupo_red_alerta($node->nid)){
            return t('Yes');
        }
    }
    return t('No');
}
function hontza_grupos_mi_grupo_get_manage_group_content(){
    global $base_url;
    $html=array();
    //intelsat-2015
    if(hontza_is_user_anonimo()){
        return '';
    }
    //
    $url_mi_grupo='mi-grupo';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $url_mi_grupo=$base_url.'/'.$my_grupo->purl.'/mi-grupo';
        $html[]=l(t('Group Settings'),$url_mi_grupo,array('absolute'=>TRUE));
    }else{
        $html[]=l(t('Group Settings'),$url_mi_grupo);
    }
    if(is_administrador_grupo()){
        
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $nid=$my_grupo->nid;
            $html[]=l(t('Edit Group Settings'),'node/'.$my_grupo->nid.'/edit');
            //
            if(hontza_is_sareko_id_red()){
                if(hontza_is_red_hoja()){
                    //intelsat-2016
                    if(!hontza_grupo_mi_grupo_is_grupo_publico_colaborativo('',$my_grupo)){
                        //gemini-2014
                        if(red_compartir_grupo_is_grupo_red_alerta($nid)){
                            $options=array('html'=>TRUE,'query'=>drupal_get_destination(),array('attributes'=>array('title'=>t('Disconnect from Network'),'alt'=>t('Disconnect from Network'))));
                            $url_no_network='red_compartir/no_compartir_grupo_hoja/'.$nid;
                            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                                $url_no_network=$base_url.'/'.$my_grupo->purl.'/'.$url_no_network;
                                $options['absolute']=TRUE;
                            }                        
                            //$label=my_get_icono_action('no_network', t('Disconnect from Network')).t('Disconnect from Network');
                            $label=t('Disconnect from Network');
                            $html[]=l($label,$url_no_network,$options);
                        }else{
                            $options=array('html'=>TRUE,'query'=>drupal_get_destination(),array('attributes'=>array('title'=>t('Connect to Network'),'alt'=>t('Connect to Network'))));                    
                            //intelsat-2016
                            //$url_sign_network='red_compartir/compartir_grupo_hoja/'.$nid;
                            $url_sign_network=hontza_registrar_get_url_sign_network($nid);
                            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                                $url_sign_network=$base_url.'/'.$my_grupo->purl.'/'.$url_sign_network;
                                $options['absolute']=TRUE;
                            }                        
                            //$label=my_get_icono_action('network', t('Connect to Network')).t('Connect to Network');
                            $label=t('Connect to Network');
                            $html[]=l($label,$url_sign_network,$options);
                        }
                    }
                }      
            }
            //intelsat-2015
            //AVISO::::se ha comentado esto
            //$html[]=l(t('Analysis of Group'),'analytics');
        }        
    }
    //intelsat-2015
    if(quant_hontza_analytics_access()){
        $html[]=l(t('Analysis of Group'),'analytics');
    }
    //
    return implode('<br>',$html);
}
function hontza_grupos_mi_grupo_get_users_content(){
    global $user,$base_url;
    $html=array();
    //intelsat-2015
    if(hontza_is_user_anonimo()){
        return '';
    }
    //
    $my_grupo=og_get_group_context();
    $url_add_remove_users='';
    //intelsat-2015
    $url_add_users='';
    $url_remove_users='';
    $remove_from_group='';
    //
    $label_list_of_users=t('List of Users');
    //    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $html[]=l($label_list_of_users,$base_url.'/'.$my_grupo->purl.'/hontza_grupos/usuarios_mas_contacto',array('absolute'=>TRUE));
        //$url_add_remove_users='og/users/'.$my_grupo->nid.'/add_user';
        $url_add_remove_users='og/users/'.$my_grupo->nid;
        //intelsat-2015
        $url_add_users='og/users/'.$my_grupo->nid.'/add_user';
        $url_remove_users=$url_add_remove_users;        
        //
        //intelsat-2015
        $icono=my_get_icono_action('edit_remove',t('Remove myself from group'),'remove_from_group','',' style="float:left;padding-top:2px;"').'&nbsp;';            
        $remove_from_group=$icono.l(t('Remove myself from group'),'og/unsubscribe/'.$my_grupo->nid.'/'.$user->uid,array('query'=>'destination=user'));
    }else{
        $remove_from_group=l($label_list_of_users,'hontza_grupos/usuarios_mas_contacto');
    }            
    if(is_administrador_grupo(1)){
        $label_analysis_of_users=t('Analysis of Users');
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $html[]=l($label_analysis_of_users,$base_url.'/'.$my_grupo->purl.'/usuarios_acceso/todos',array('absolute'=>TRUE));
        }else{
            $html[]=l($label_analysis_of_users,'usuarios_acceso/todos');        
        }
        //        
        /*if(!empty($url_add_remove_users)){
            $html[]=l(t('Add/Remove Users'),$url_add_remove_users);
        }*/
        //intelsat-2015
        if(!empty($url_add_users)){
            //intelsat-2015
            $icono=my_get_icono_action('add_left',t('Add users')).'&nbsp;';
            $html[]=$icono.l(t('Add users'),$url_add_users);
        }
        if(!empty($url_remove_users)){
            //intelsat-2015
            $icono=my_get_icono_action('edit_remove',t('Remove users'),'remove_users','',' style="float:left;padding-top:2px;"').'&nbsp;';
            $html[]=$icono.l(t('Remove users'),$url_remove_users);
        }
        //
    }
    //intelsat-2015
    if(!empty($remove_from_group)){
        $html[]=$remove_from_group;
    }
    return implode('<br>',$html);
}
function hontza_grupos_mi_grupo_define_usuarios_acceso_acciones($capta){
    global $base_url;
    //simulando
    return '';
    $html=array();
    $my_user=user_load($capta->uid);
    if(isset($my_user->uid) && !empty($my_user->uid)){
        $my_grupo=og_get_group_context();
        //$query='';
        $query='contact_grupo='.$my_grupo->purl.'&'.drupal_get_destination();        
        //$html[]=l(my_get_icono_action('email', t('Send message')),'user/'.$capta->uid.'/contact',array('query'=>$query,'attributes'=>array('title'=>t('Send message'),'alt'=>t('Send message')),'html'=>true));                
        $html[]=l(my_get_icono_action('email', t('Send message')),'hontza_grupos/'.$capta->uid.'/contact',array('query'=>$query,'attributes'=>array('title'=>t('Send message'),'alt'=>t('Send message')),'html'=>true));                                   
    }
    return implode('&nbsp;',$html);
}
function hontza_grupos_mi_grupo_contact_mail_user_form_alter(&$form, &$form_state, $form_id){
    //global $base_url;
    /*$url_volver='usuarios_acceso/todos';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_volver=$_REQUEST['destination'];
    }
    $form['volver_btn']=array(
        '#value'=>l(t('Return'),$url_volver,array('attributes'=>array('class'=>'back_left'))),
    );*/
    /*$url_redirect=$url_volver;
    if(isset($_REQUEST['contact_grupo']) && !empty($_REQUEST['contact_grupo'])){
        $url_redirect=$base_url.$_REQUEST['contact_grupo'].$url_volver;
    }
    $form_state['is_my_contact']=1;
    $form_state['redirect']=$url_redirect;*/
}
function hontza_grupos_mi_grupo_contact_form_callback(){
    $account=user_load(arg(1));
    //intelsat-2015
    //drupal_set_title($account->name);
    drupal_set_title(t('Send message'));
    //
    //return 'Funcion desactivada';    
    return drupal_get_form('hontza_grupos_mi_grupo_contact_form');
}
function hontza_grupos_mi_grupo_contact_form($form_state,$uid_in='',$destination_send_message_popup=''){
    require_once('modules/contact/contact.pages.inc');
    //intelsat-2015    
    if(!empty($uid_in)){
        $uid=$uid_in;
    }else{
        $uid=arg(1);
    }
    //
    $account=user_load($uid);
    $form=contact_mail_user($form_state,$account);
    //intelsat-2016
    hontza_grupos_mi_grupo_contact_form_multiple($form,$uid);
    //intelsat-2015
    $form['destination_send_message_popup']=array(
        '#type'=>'hidden',
        '#value'=>$destination_send_message_popup,
    );
    if(!empty($uid_in)){
        $form['titulo_form']=array(
            '#value'=>'<h1>'.t('Send message').'</h1>',
            '#weight'=>-99,
        );
    }
    $form['message']['#rows']=12;
    $form['from']['#prefix']='<div style="float:left;clear:both,width:500px;"><div style="float:left;width:250px;">';
    $form['from']['#suffix']='</div>';
    $form['to']['#prefix']='<div style="float:left;width:250px;">';
    $form['to']['#suffix']='</div></div>';
    $form['subject']['#prefix']='<div style="clear:both;">';
    $form['subject']['#suffix']='</div>';
    $js='';
    //$js=hontza_grupos_mi_grupo_add_send_message_popup_return_js();
    $return_url='hontza_grupos/usuarios_mas_contacto';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $return_url=$_REQUEST['destination'];
    }
    //$link_return=$js.l(t('Return'),$return_url,array('attributes'=>array('class'=>'back_left jqmClose','id'=>'send_message_popup_return','onclick'=>hontza_grupos_mi_grupo_contact_close_js($uid))));
    $link_return=$js.l(t('Return'),$return_url,array('attributes'=>array('class'=>'back_left jqmClose','id'=>'send_message_popup_return')));    
    //    
    $form['volver_btn']=array(
        '#value'=>$link_return
    );
    return $form;
}
function hontza_grupos_mi_grupo_contact_form_submit($form, &$form_state) {
  global $user, $language;
  //intelsat-2016
  //$account = $form_state['values']['recipient'];
  $account_array=hontza_grupos_mi_grupo_contact_get_account_array($form_state);
  if(!empty($account_array)){
      foreach($account_array as $i=>$account){
        //intelsat-2015
        $destination_send_message_popup=$form_state['values']['destination_send_message_popup'];
        $destination_send_message_popup=base64_decode($destination_send_message_popup);
        //

        // Send from the current user to the requested user.
        $to = $account->mail;
        $from = $user->mail;

        // Save both users and all form values for email composition.
        $values = $form_state['values'];
        $values['account'] = $account;
        $values['user'] = $user;
        //intelsat-2015
        hontza_grupos_mi_grupo_insert_users_contact($user,$account);
        //
        // Send the e-mail in the requested user language.
        drupal_mail('contact', 'user_mail', $to, user_preferred_language($account), $values, $from);

        // Send a copy if requested, using current page language.
        if ($form_state['values']['copy']) {
          drupal_mail('contact', 'user_copy', $from, $language, $values, $from);
        }

        flood_register_event('contact');
        watchdog('mail', '%name-from sent %name-to an e-mail.', array('%name-from' => $user->name, '%name-to' => $account->name));
        //drupal_set_message(t('The message has been sent.'));
        //gemini-2014
        // Back to the requested users profile page.
        //$form_state['redirect'] = "user/$account->uid";
      }   
  }
  //intelsat-2016
  drupal_set_message(t('The message has been sent.'));
  drupal_goto($destination_send_message_popup);
}
function hontza_grupos_mi_grupo_usuarios_mas_contacto_callback(){
  drupal_set_title(t('List of Users'));
  $user_list=get_estadisticas_user_list('usuarios_acceso');  
  $output=get_usuarios_acceso_html($user_list);
  return $output; 
}
function hontza_grupos_mi_grupo_define_usuarios_mas_contacto_acciones($capta){
    global $base_url;
    //simulando
    //return '';
    $html=array();
    $my_user=user_load($capta->uid);
    if(isset($my_user->uid) && !empty($my_user->uid)){
        $my_grupo=og_get_group_context();
        //$query='';
        $query='contact_grupo='.$my_grupo->purl.'&'.drupal_get_destination();        
        //$html[]=l(my_get_icono_action('email', t('Send message')),'user/'.$capta->uid.'/contact',array('query'=>$query,'attributes'=>array('title'=>t('Send message'),'alt'=>t('Send message')),'html'=>true));                
        //intelsat-2015        
        //$icono=hontza_grupos_mi_grupo_get_send_message_icono($my_user->uid);        
        //$html[]=l($icono,'hontza_grupos/'.$capta->uid.'/contact',array('query'=>$query,'attributes'=>array('title'=>t('Send message'),'alt'=>t('Send message')),'html'=>true));                                    
        $html[]=hontza_grupos_mi_grupo_get_send_message_link_popup($my_user->uid); 
        //
    }
    return implode('&nbsp;',$html);  
}
function hontza_grupos_mi_grupo_is_usuarios_mas_contacto(){
    return hontza_grupos_mi_grupo_is_pantalla('usuarios_mas_contacto');
}
function hontza_grupos_mi_grupo_is_pantalla($konp=''){
    $param0=arg(0);
    if(!empty($param0) && $param0=='hontza_grupos'){
        if(empty($konp)){
            return 1;
        }else{
            $param1=arg(1);
            if(!empty($param1) && $param1==$konp){
                return 1;
            }
        }    
    }
    return 0;
}
function hontza_grupos_mi_grupo_is_mi_grupo(){
    $param0=arg(0);
    if(!empty($param0)){
        $param1=arg(1);
        $param2=arg(2);
        if(in_array($param0,array('mi-grupo','usuarios_acceso','usuarios_captacion_informacion','usuarios_aportacion_valor','usuarios_generacion_ideas'))){        
            return 1;
        }else if($param0=='hontza_grupos'){
            if(!empty($param1)){
                if($param1=='usuarios_mas_contacto'){
                    return 1;
                }else if(is_numeric($param1)){
                    if(!empty($param2)){
                        if(in_array($param2,array('contact','user_view'))){
                            return 1;
                        }
                    }
                }
            }                    
        }else if($param0=='node'){
            if(hontza_is_og_vocab_terms()){
                return 0;
            }
            $node=my_get_node();            
            if(isset($node->type) && !empty($node->type) && $node->type=='grupo'){
                if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
                    if($_REQUEST['destination']=='user-gestion/grupos/propios'){
                        return 0;
                    }
                }
                return 1;
            }
            
        }else if($param0=='og'){
            if(!empty($param1)){
                if($param1=='users'){
                    if(!empty($param2)){
                        return 1;
                    }
                }else if($param1=='unsubscribe'){
                    return 1;
                }
            }    
        }else if($param0=='red_compartir'){
            if(!empty($param1)){
                if(in_array($param1,array('compartir_grupo_hoja','no_compartir_grupo_hoja','compartir_grupo_hoja_registrar'))){
                    return 1;
                }
            }
        }else if($param0=='analytics'){
            $my_grupo=og_get_group_context();
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                return 1;
            }            
        }
    }
    /*if(red_funciones_is_perfil_usuario()){
        return 1;
    }*/
    return 0;
}
function hontza_grupos_mi_grupo_user_view_callback(){
    //intelsat-2015
    $uid=arg(1);
    //
    $my_user=user_load($uid);
    if(isset($my_user->uid) && !empty($my_user->uid)){
        drupal_set_title($my_user->name);
        require_once('sites/all/modules/user/user.pages.inc');
        $output=user_view($my_user);
        //intelsat-2015
        //$output.=hontza_grupos_mi_grupo_num_paginas_visitadas_modal(TRUE,0,$uid);
        //
        return $output;
    }else{
        drupal_set_title(t('User'));
    }
    return '';
}
function hontza_grupos_mi_grupo_get_rows_og_members($vars_in){
    $vars=$vars_in;
    $sep='<td class="views-field views-field-name">';
    $my_array=explode($sep,$vars['rows']);
    $kont=0;
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if($i>0){
                if(isset($vars['view']->result[$kont])){
                    $pos=strpos($v,'</a>');
                    if($pos===FALSE){
                        //
                    }else{
                        $row=$vars['view']->result[$kont];
                        $my_array[$i]=l($row->users_name,'hontza_grupos/'.$row->uid.'/user_view',array('attributes'=>array('title'=>t('View user profile.'))));
                        $my_array[$i].=substr($v,$pos+strlen('</a>'));
                    }
                }
                $kont++;
            }
        }
    }    
    $vars['rows']=implode($sep,$my_array);
    return $vars['rows'];
}
function hontza_grupos_mi_grupo_get_rows_og_members_faces($vars_in){
    global $base_url;
    $vars=$vars_in;    
    $sep='<div class="picture">';
    $my_array=explode($sep,$vars['rows']);
    $kont=0;
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if($i>0){
                if(isset($vars['view']->result[$kont])){
                    $pos=strpos($v,'</a>');
                    if($pos===FALSE){
                        //
                    }else{
                        $row=$vars['view']->result[$kont];
                        $account=user_load($row->uid);
                        //$img='<img src="'.$base_url.'/'.$row->users_picture.'" alt="'.$row->users_name.'" title="'.$row->users_name.'"/>';
                        if($account->picture){
                            $img=my_get_user_img_src('',$account->picture,$account->name,$account->uid,0,'',1);
                        }else{
                            $img=my_get_user_img_src('','',$account->name,$account->uid,0,'',1);
                        }
                        $my_array[$i]=l($img,'hontza_grupos/'.$row->uid.'/user_view',array('html'=>TRUE,'attributes'=>array('title'=>t('View user profile.'))));
                        $my_array[$i].=substr($v,$pos+strlen('</a>'));
                    }
                }
                $kont++;
            }
        }
    }    
    $vars['rows']=implode($sep,$my_array);
    $vars['rows']=hontza_grupos_mi_grupo_get_rows_og_members_faces_field_name($vars);
    return $vars['rows'];
}
function hontza_grupos_mi_grupo_get_rows_og_members_faces_field_name($vars_in){
    $vars=$vars_in;
    $sep='<span class="views-field-name">';
    $my_array=explode($sep,$vars['rows']);
    $kont=0;
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if($i>0){
                if(isset($vars['view']->result[$kont])){
                    $pos=strpos($v,'</a>');
                    if($pos===FALSE){
                        //
                    }else{
                        $row=$vars['view']->result[$kont];
                        $my_array[$i]='<span class="field-content">'.l($row->users_name,'hontza_grupos/'.$row->uid.'/user_view',array('attributes'=>array('title'=>t('View user profile.'))));
                        $my_array[$i].=substr($v,$pos+strlen('</a>'));
                    }
                }
                $kont++;
            }
        }
    }    
    $vars['rows']=implode($sep,$my_array);
    return $vars['rows'];
}
function hontza_grupos_get_chief_editor_uid($my_grupo_in=''){
    $my_grupo=$my_grupo_in;
    $result=0;
    if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
        $my_grupo=og_get_group_context();
    }
    //
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $result=1;
        if(isset($my_grupo->field_admin_grupo_uid[0]['value']) && !empty($my_grupo->field_admin_grupo_uid[0]['value'])){
            $result=$my_grupo->field_admin_grupo_uid[0]['value'];        
        }
    }    
    return $result;    
}
function hontza_grupos_mi_grupo_get_creador_grupo_uid(){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        return $my_grupo->uid;
    }
    return 0;
}
function hontza_grupos_mi_grupo_is_propietario_del_grupo($node){
    global $user;
    if(isset($node->nid) && !empty($node->nid)){
        if($node->uid==$user->uid){
            return 1;
        }
    }
    return 0;
}
function hontza_grupos_mi_grupo_get_user_img($uid,$factor=''){
    $my_user=user_load($uid);
    $result=my_get_user_img_src('',$my_user->picture,$my_user->name,$uid,0,'',0,$factor);
    return $result;
}
function hontza_grupos_mi_grupo_define_editar_crear_categorias_primary(){
    if(hontza_is_og_vocab_terms(1)){
        if(is_show_modificar_vocab()){
            $my_grupo=og_get_group_context();
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $vocab_list=my_get_og_vocab_list(array('nid'=>$my_grupo->nid));
                if(count($vocab_list)==1){
                    $vid=$vocab_list[0]->vid;
                    $html=array();
                    $html[]='<li'.hontza_grupos_mi_grupo_get_editar_crear_categorias_li_class('').'>'.l(t('List'),'node/'.$my_grupo->nid.'/og/vocab/terms/'.$vid).'</li>';
                    $html[]='<li'.hontza_grupos_mi_grupo_get_editar_crear_categorias_li_class('add_term').'>'.l(t('Add category'),'node/'.$my_grupo->nid.'/og/vocab/terms/'.$vid.'/add_term').'</li>';
                    return implode('',$html);
                }
            }
        }
    }    
    return '';
}
function hontza_grupos_mi_grupo_get_editar_crear_categorias_li_class($konp=''){
    $is_active=0;
    $param=arg(6);        
    if(empty($konp)){
        if(empty($param)){
            $is_active=1;
        }
    }else{
        if($param==$konp){
            $is_active=1;
        }
    }
    if($is_active){
        return ' class="active"';
    }
    return '';
}
//intelsat-2014
function hontza_grupos_mi_grupo_usuarios_de_mis_grupos_options(){
  $result=get_usuarios_creadores_y_administradores_grupo_options(0,0,0,'',1);
  if(isset($result[1])){
      unset($result[1]);
  }
  return $result;
}
//intelsat-2014
function hontza_grupos_mi_grupo_og_add_users_submit($form,&$form_state,&$accounts){
  if(isset($form_state['values']['og_my_users']) && !empty($form_state['values']['og_my_users'])){
      $accounts=array();            
      $uid_array=array_keys($form_state['values']['og_my_users']);
      if(!empty($uid_array)){
            //
            foreach ($uid_array as $i=>$uid) {
              $account = user_load($uid);
              if ($account->uid) {
                if(!hontza_grupos_mi_grupo_user_in_group($form_state['values']['gid'],$account)){
                    $accounts[] = $account;                
                }
              }
            }
      }
      hontza_grupos_mi_grupo_og_save_subscription($accounts,$form_state);
      if(!empty($accounts)){
        drupal_set_message(format_plural(count($accounts), '1 user added to the group.', '@count users added to the group.'));
      }
  }
}
function hontza_grupos_mi_grupo_user_in_group($group_nid,$account){
  if(isset($account->og_groups) && !empty($account->og_groups)){
      foreach($account->og_groups as $key=>$group){
          if($group['nid']==$group_nid){
              return 1;
          }
      }      
  }
  return 0;
}
function hontza_grupos_mi_grupo_get_usuarios_del_grupo($group_nid){
    $result=array();
    $user_options=boletin_report_get_usuarios_options($group_nid);
    if(isset($user_options[1])){
        unset($user_options[1]);
    }
    if(!empty($user_options)){
        $result=array_keys($user_options);
    }
    return $result;
}
//intelsat-2014
function hontza_grupos_mi_grupo_og_save_subscription($accounts,&$form_state){
    hontza_grupos_mi_grupo_og_save_subscription_grupo_nid($accounts,$form_state['values']['gid']);
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_title_grupo_simbolo_img(){
   $title=t('Group');
   $result=my_get_icono_action('grupo32', $title,'grupo32');
   return $result;  
}
//intelsat-2015
function hontza_grupos_mi_grupo_entidades_left_title(){    
    $title=t('Organisations');
    $icono=my_get_icono_action('entidad',$title,'entidad').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_is_remove_from_group_owner(&$my_group){
    $my_group='';
    $param0=arg(0);
    if(!empty($param0) && $param0=='og'){
        $param1=arg(1);        
        if(!empty($param1) && $param1=='unsubscribe'){
            $group_nid=arg(2);
            if(!empty($group_nid) && is_numeric($group_nid)){
                $my_group=node_load($group_nid);
                if(isset($my_group->nid) && !empty($my_group->nid)){
                    $uid=arg(3);
                    if(!empty($uid) && is_numeric($uid) && $my_group->uid == $uid) {
                        return 1;
                    }
                }
            }
        }
    }
    return 0;
}
function hontza_grupos_mi_grupo_add_quants($quants_in){
    $quants=$quants_in;
    //Number of visited pages
    $quant = new stdClass;	
    $quant->id = 'number_of_visited_page_group';
    $quant->label = t('Number of visited pages');
    $quant->labelsum = TRUE;
    $quant->table = 'accesslog';
    $quant->field = 'timestamp';
    $quant->dataType = 'single';
    $quant->chartType = 'line';
    $quants[] = $quant;
    return $quants;
}
function hontza_grupos_mi_grupo_num_paginas_visitadas_modal($js = FALSE,$is_modal=1,$uid_in=0) {
  $uid=$uid_in;
  $salida='';
  if($is_modal){
    $salida = '<p>'.t('Number of visited pages').'</p>';
  }
  
  $grupo_nid=0;
  $my_grupo=og_get_group_context();
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
      $grupo_nid=$my_grupo->nid;
  }
  
  //$title = t('Statistics from last month.');
  $title = t('Number of visited pages');
  if ($js) {
    ctools_include('ajax');
    ctools_include('modal');
    $period = strtotime('-1 month');
    $fecha_end='';
    $fecha_end_where='1';
    //$where_time=hontza_get_usuarios_acceso_where_time();
    $where_time=quant_get_usuarios_acceso_where_time_array();
    if(quant_is_fecha_mayor($where_time,$fecha_ini)){
        $period=$fecha_ini;
    }else if(quant_is_fecha_menor($where_time,$fecha_end)){
        $fecha_end_where="accesslog.timestamp <= ".$fecha_end;
        $period=strtotime('-1 month',$fecha_end);             
    //}else if($where_time!='(1)'){
    }else if(quant_is_dos_fechas($where_time)){    
        quant_get_fechas($where_time,$fecha_ini,$fecha_end);        
        $period=$fecha_ini;
        $fecha_end_where="accesslog.timestamp <= ".$fecha_end;
    }
    //
    $sql="SELECT accesslog.timestamp 
           FROM {accesslog}
           LEFT JOIN {og_uid} ON accesslog.uid=og_uid.uid 
           WHERE accesslog.timestamp >= %d AND ".$fecha_end_where." AND og_uid.nid=".$grupo_nid." AND accesslog.uid=".$uid." 
           ORDER BY accesslog.timestamp DESC";
    $quant = new stdClass;
    $quant->id = 'number_of_visited_page_user_'.$uid;    
    //$quant->label = t('Number of visited pages');  // The title of the chart
    $quant->label = '';  // The title of the chart
    $quant->labelsum = TRUE; // Show the total amount of items in the chart title
    $quant->query =$sql; 
    $quant->table = 'accesslog';
    $quant->field = 'timestamp';
    $quant->dataType = 'single';
    $quant->chartType = 'line';
    //$quants[] = $quant;
  
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
//intelsat-2015
function hontza_grupos_mi_grupo_is_custom_quant_size(&$result,&$chart){
    $result=array();
    //if($chart['#chart_id']=='number_of_visited_page_user'){
    if(hontza_grupos_mi_grupo_is_number_of_visited_page_user($chart['#chart_id'])){
        $result['quant_width']=300; 
    $result['quant_height']=100;
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_grupos_mi_grupo_is_number_of_visited_page_user($chart_id){
    $pos=strpos($chart_id,'number_of_visited_page_user_');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_user_img_factor($title,$alt,$src,$faktore_in){
    $size='';
    $faktore=$faktore_in;
    $faktore=$faktore_in/100;
    if(!empty($faktore)){
        $path=$src;
        $info_size=@getimagesize($path);
				if(isset($info_size[0]) && isset($info_size[1])){
					$wa=$info_size[0];
					$ha=$info_size[1];
				}else{
                                    $basename=basename($path);
                                    $dir=variable_get('file_directory_path','');
                                    if(!empty($dir)){
                                        $path=$dir.'/'.$basename;
                                    }
                                    $info_size=@getimagesize($path);
                                    if(isset($info_size[0]) && isset($info_size[1])){
                                            $wa=$info_size[0];
                                            $ha=$info_size[1];
                                    }
                                }
        if(!empty($wa) && !empty($ha)){
            				
            $wa=$wa*$faktore;
            
						$ha=$ha*$faktore;
					
				
            $size=' width="'.$wa.'" height="'.$ha.'"';
        }    
    }
    $result='<img title="'.$title.'" alt="'.$alt.'" src="'.$src.'"'.$size.'>';
    return $result;    
}
function hontza_grupos_mi_grupo_is_ficha_tabla(){
    return hontza_canal_rss_is_fuente_ficha_tabla();
}
function hontza_grupos_mi_grupo_get_tipo_grupo_icono($data,$tid,&$title_popup){
    $icono_red_alerta=hontza_get_icono_grupo_red_alerta($data,1);    
    if(empty($icono_red_alerta)){
        if(is_array($data)){    
            if(isset($data[0])){
                $icono_red_alerta=$data[0];
            }
        }    
    }
    return panel_admin_get_tipo_grupo_icono($data,$icono_red_alerta,$tid,$title_popup);
}
//intelsat-2015
function hontza_grupos_mi_grupo_unset_fields($rows,$bakup_rows){
    $result=$rows;
    $is_este_servidor=0;
    if(hontza_grupos_mi_grupo_is_este_servidor()){
        $is_este_servidor=1;
    }
    if(!empty($result)){
        foreach($result as $i=>$r){
            $result[$i]=$r;
            $icono=$result[$i][3];
            //$result[$i][0]=$icono.$result[$i][0];
            unset($result[$i][5]);
            unset($result[$i][3]);
            $result[$i][3]=$result[$i][4];
            unset($result[$i][4]);
            $my_array=$result[$i];
            $result[$i]=array();
            $result[$i][0]=$icono;
            $result[$i][1]=$bakup_rows[$i]['server_link'];
            if($is_este_servidor){
                $result[$i]['tags']=$bakup_rows[$i]['tags'];
                $result[$i]['tags_geograficos']=$bakup_rows[$i]['tags_geograficos'];
            }
            $result[$i]=array_merge($result[$i],$my_array);
        }
    }            
    return $result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_insert_users_contact($user,$account){
    if(db_table_exists('users_contact')){
        $timestamp=time();
        db_query('INSERT INTO {users_contact}(uid,timestamp,uid_sender) VALUES(%d,%d,%d)',$account->uid,$timestamp,$user->uid);
    }    
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_send_message_icono($uid){
    $result='';
    /*if(hontza_grupos_mi_grupo_is_message_sent($uid)){
        $result=my_get_icono_action('sent', t('Send message'));
    }else{*/
    $result=my_get_icono_action('email', t('Send message'));
    //}
    return $result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_is_message_sent($uid){
    $users_contact_row=hontza_grupos_mi_grupo_get_last_users_contact_row($uid);
    if(isset($users_contact_row->id) && !empty($users_contact_row->id)){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_last_users_contact_row($uid){
    if(db_table_exists('users_contact')){
        $res=db_query('SELECT * FROM {users_contact} WHERE uid=%d ORDER BY timestamp DESC',$uid);
        while($row=db_fetch_object($res)){
            return $row;
        }
    }    
    $my_result=new stdClass();
    return $my_result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_send_message_link($my_user){
    echo print_r($my_user,1);
    exit();
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_send_message_link_popup($uid){
    hontza_grupos_mi_grupo_add_send_message_popup_js($uid);
    $html=array();
    $html[]='<div id="exsend_message_popup_'.$uid.'" class="jqmWindow jqmID2000"></div>';
    $html[]=l(my_get_icono_action('email', t('Send message')),'hontza_grupos/'.$uid.'/contact',array('html'=>TRUE,'attributes'=>array('class'=>'jqm-trigger-send_message_popup_'.$uid),'query'=>  drupal_get_destination()));       
    return implode('',$html);
}
//intelsat-2015
function hontza_grupos_mi_grupo_add_send_message_popup_js($uid){    
 global $base_root; 
 $destination_send_message_popup=$base_root.request_uri();
 $destination_send_message_popup=base64_encode($destination_send_message_popup);
 //intelsat-2016
 $my_base_path=hontza_canal_rss_get_base_path_help_popup();
 $js="$(document).ready(function()
     {
         $('#exsend_message_popup_".$uid."').jqm({ajax: '".$my_base_path."help_popup.php?nid=send_message_popup&w=500&h=400&uid=".$uid."&destination_send_message_popup=".$destination_send_message_popup."', trigger: 'a.jqm-trigger-send_message_popup_".$uid."',modal:true, toTop: true, overlay: 0});
         
     });";
     drupal_add_js($js,'inline');
}
function hontza_grupos_mi_grupo_add_send_message_popup_return_js(){
   $js='$(document).ready(function(){
        $("#send_message_popup_return").click(function(){
            self.close();
            return false;
        });
   });';
   $result='<script type="text/javascript">'.$js.'</script>';
   return $result;
}
function hontza_grupos_mi_grupo_contact_close_js($uid){
    return "self.close();";
    //return "$('#exsend_message_popup_".$uid."').hide();";
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_usuarios_grupos($group_nid_array){
    $result=array();
    if(!empty($group_nid_array)){
        foreach($group_nid_array as $i=>$group_nid){
            $users=hontza_grupos_mi_grupo_get_usuarios_del_grupo($group_nid);
            if(empty($result)){
                $result=$users;
            }else{
                $result=array_intersect($result,$users);
            }
        }    
    }
    return $result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_og_save_subscription_grupo_nid($accounts,$grupo_nid){
    if(!empty($accounts)){
      //    
        foreach ($accounts as $account) {
          //print $grupo_nid.'='.$account->uid.'<BR>';  
          og_save_subscription($grupo_nid, $account->uid, array('is_active' => 1));
          //intelsat-2015
          red_crear_usuario_send_mail_added_usuario_grupo($account,$grupo_nid);
          //gemini-2014
          if(hontza_is_sareko_id_red()){
            if(isset($account->uid) && !empty($account->uid)){
              red_compartir_grupo_on_add_user_group($account,$grupo_nid);
            }  
          }
          //
        }      
    }
}
//intelsat-2015
function hontza_grupos_mi_grupo_set_tabs_activados_remoto($grupo_node,$row,&$result_row){
    $result_row->activo_tabs=array();
    $type_array=array('estrategia','debate','wiki','idea');
    if(!empty($type_array)){
        foreach($type_array as $i=>$type){
            $result_row->activo_tabs[$type]=0;
            if(hontza_grupos_is_activo_pestana($type,'',$grupo_node)){
                $result_row->activo_tabs[$type]=1;
            }
        }
    }
}
//intelsat-2015
function hontza_grupos_mi_grupo_is_grupo_local($row){
    if(isset($row->is_grupo_local) && !empty($row->is_grupo_local)){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_html($node){
    if(isset($node->field_delete_rejected_news_time) && isset($node->field_delete_rejected_news_time[0])){
        if(isset($node->field_delete_rejected_news_time[0]['value']) && !empty($node->field_delete_rejected_news_time[0]['value'])){
            return hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_label($node->field_delete_rejected_news_time[0]['value']);
        }
    }
    return t('None');
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_label($value){
    $label_array=hontza_grupos_mi_grupo_define_field_delete_rejected_news_time_label_assoc();
    if(isset($label_array[$value])){
        return $label_array[$value];
    }
    return t('None');
}
//intelsat-2015
function hontza_grupos_mi_grupo_define_field_delete_rejected_news_time_label_assoc(){
    $result=array();
    $result[1]='1 '.t('Month');
    $result[2]='2 '.t('Months');
    $result[3]='3 '.t('Months');
    $result[4]='4 '.t('Months');
    $result[5]='5 '.t('Months');
    $result[6]='6 '.t('Months');
    return $result;
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_field_delete_unread_news_time_html($node){
    if(isset($node->field_delete_unread_news_time) && isset($node->field_delete_unread_news_time[0])){
        if(isset($node->field_delete_unread_news_time[0]['value']) && !empty($node->field_delete_unread_news_time[0]['value'])){
            return hontza_grupos_mi_grupo_get_field_delete_unread_news_time_label($node->field_delete_unread_news_time[0]['value']);
        }
    }
    return t('None');
}
//intelsat-2015
function hontza_grupos_mi_grupo_get_field_delete_unread_news_time_label($value){
    return hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_label($value);
}
//intelsat-2015
function hontza_grupos_mis_grupos_script_access(){
    if(alerta_solr_is_cron()){
        return 1;
    }
    if(custom_menu_red_is_red_grupos_subdominios_script()){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_grupos_mi_grupo_set_active_menutop($menutop_in,$my_replace){
    $sep='<li class="';
    $is_last=0;
    $my_array=explode($sep,$menutop_in);
    if(!empty($my_array)){
        $num=count($my_array);
        foreach($my_array as $i=>$value){
            if($i<1){
                $html[]=$value;
                continue;
            }
            $href_value=hontza_grupos_mi_grupo_get_menutop_li_class_href_value($value);
            $pos=strpos($href_value,'"');
            if($pos===FALSE){
                $html[]=$value;
            }else{                                
                $url=substr($href_value,0,$pos);
                $s=str_replace($my_replace,'',$url);
                //print $my_replace.'<BR>';
                //print $s.'<BR>';
                /*if(!hontza_grupos_is_url_menutop_active($s)){
                    if($i==($num-1)){
                        $is_last=1;
                        $value=hontza_grupos_unset_last_menu($value);
                        $html[]=$value;
                        continue;
                    }else{
                        continue;
                    }    
                }
                $html[]=$sep.$value;*/
                if(hontza_grupos_is_url_menutop_active($s)){
                    $html[]=$sep.$value;
                }
            }
        }
        /*
        if($is_last){
            $len=count($html);
            $penultimo=$len-2;
            if($penultimo>=0){
                $html[$penultimo]=hontza_grupos_unset_penultimo_menu($html[$penultimo]);
            }
        }*/
        
       // return implode($sep,$html);       
       //$result=implode($sep,$html);
       $result=implode('',$html); 
       //print $menutop_in.'====#########################';
       return $result;
    }
    return $menutop_in;
}
function hontza_grupos_mi_grupo_get_menutop_li_class_href_value($value){
    $sep='<a href="';
    $pos=strpos($value,$sep);
    if($pos===FALSE){
        return $value;
    }else{
        $result=substr($value,$pos+strlen($sep));
    }
    return $result;
}
function hontza_grupos_mi_grupo_prepare_fields($rows){
    //$result=$rows;
    $kont=0;
    $result=array();
    //intelsat-2016
    $is_este_servidor=0;
    if(hontza_grupos_mi_grupo_is_este_servidor()){
        $is_este_servidor=1;
    }
    if(!empty($rows)){
        foreach($rows as $i=>$my_row){
            $row=$my_row;
            if($is_este_servidor){
                $row=hontza_grupos_mi_grupo_get_tags_fields($row);
            }                
            if(hontza_grupos_mi_grupos_is_grupo_local_by_subdominio($row)){
                continue;
            }
            $result[$kont]=$row;
            $doc = new DOMDocument();
            $doc->loadHTML($row[3]);
            $xpath = new DOMXPath($doc);
            $title = $xpath->evaluate("string(//img/@title)");
            $result[$kont]['type_of_group']=$title;
            $info_server=hontza_grupos_mi_grupo_get_server($row);
            $result[$kont]['server']=$info_server['server'];
            $result[$kont]['server_link']=$info_server['server_link'];
            $kont++;
        }
    }
    return $result;
}
function hontza_grupos_mi_grupo_get_server($row){
    global $base_url;
    $result=array();
    $result['server']='';
    $result['server_link']='';
    if(isset($row['login_red_alerta_url'])){
        if(!empty($row['login_red_alerta_url'])){
            $url=$row['login_red_alerta_url'];
        }else{
            $url=$base_url;             
        }
        $url=hontza_grupos_mi_grupo_get_grupo_url_by_alias($url);
        $link=custom_menu_get_href($row[0]);
        $link=hontza_grupos_mi_grupo_get_grupo_link_by_alias($link);
        $info_url=parse_url($url);
        $result['server']=$info_url['host'];
        $result['server_link']=l($info_url['host'],$link,array('attributes'=>array('target'=>'_blank'),'absolute'=>true));
        return $result;
    }
    return $result;
}
function hontza_grupos_mi_grupo_get_grupo_url_by_alias($url){
    $result=$url;
    if(in_array($url,array('http://www.hontza.es/hontza3'))){
        $result='http://online.hontza.es';
    }
    return $result;
}
function hontza_grupos_mi_grupo_get_grupo_link_by_alias($link){
    $result=$link;
    if(_SAREKO_ID=='ROOT'){
        $result=str_replace('http://www.hontza.es/hontza3','http://online.hontza.es',$link);
    }
    return $result;
}
function hontza_grupos_mi_grupos_is_grupo_local_by_subdominio($row){
    if(_SAREKO_ID=='ROOT'){
        if($row['subdominio']=='online_hontza_es_'){
            return 1;
        }
    }    
    return 0;
}
//intelsat-2016
function hontza_grupos_mi_grupos_is_user_admistrador_grupo_propietario($user,$grupo){
    if(isset($user->roles[ADMINISTRADOR_DE_GRUPO]) && $grupo->uid==$user->uid){
        return 1;
    }
    return 0;
}
//intelsat-2016
function hontza_grupos_mi_grupo_is_creador_administrador_grupo($user){
    if(isset($user->roles[CREADOR]) && isset($user->roles[ADMINISTRADOR_DE_GRUPO])){
        return 1;
    }
    return 0;
}
//intelsat-2016
function hontza_grupos_mi_grupo_registrar_add_grupo_node_form_js($node){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_add_grupo_node_form_js($node);
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_registrar_grupo_node_form_alter(&$form,&$form_state,$form_id,$node){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_grupo_node_form_alter($form,$form_state,$form_id,$node);
    }
    if(!hontza_is_red_hoja()){
        unset($form['field_is_private_collabo_network']);
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_registrar_on_grupo_node_save(&$node,$op,$nid='',$is_exec=0){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_on_grupo_node_save($node,$op,$nid,$is_exec);
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_grupo_node_registrar_submit($form,&$form_state){
  if(hontza_registrar_is_registrar_activado()){
    $_REQUEST['destination']='';
    $grupo_nid=hontza_get_nid_by_form($form);
    if(empty($grupo_nid)){
        if(isset($_SESSION['red_registrar_grupo_nid_saved'])){
            $grupo_nid=$_SESSION['red_registrar_grupo_nid_saved'];
            unset($_SESSION['red_registrar_grupo_nid_saved']);
        }
    }
    $_REQUEST['destination']='red_registrar/'.$grupo_nid.'/on_grupo_node_saved';
  }          
}
//intelsat-2016
function hontza_grupos_mi_grupo_add_grupo_node_registrar_submit(&$form){
    if(hontza_registrar_is_registrar_activado()){
        $form['buttons']['submit']['#submit'][]='hontza_grupos_mi_grupo_grupo_node_registrar_submit';
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_registrar_set_session_grupo_nid_saved(&$node,$op){
    if(hontza_registrar_is_registrar_activado()){
        //echo print_r($node,1);exit();
        red_registrar_set_session_grupo_nid_saved($node,$op);
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_is_este_servidor(){
    $type=arg(1);
    if(!empty($type) && $type=='este_servidor'){
        return 1;
    }
    return 0;
}
//intelsat-2016
function hontza_grupos_mis_grupos_get_header($header){
    $result=$header;
    if(hontza_grupos_mi_grupo_is_este_servidor()){
        $result = array(
            array('data'=>t('Type'),'field'=>'group_type'),
            array('data'=>t('Server'),'field'=>'server'),    
            array('data' => t('Name'), 'field' => 'node_title'),
            array('data' => t('Thematic Tags'), 'field' => 'tags'),    
            array('data' => t('Geographic Tags'), 'field' => 'tags_geograficos'),
            array('data' => t('Users'), 'field' => 'member_count'),
        );
    }
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_get_rows_tags($rows){
    $result=$rows;
    if(hontza_grupos_mi_grupo_is_este_servidor()){
        if(!empty($result)){
            foreach($result as $i=>$row){
                $r=$row;
                $r[0]=$r[0].$r[3];
                $r[3]=$r['tags'];
                $r[4]=$r['tags_geograficos'];
                unset($r['tags']);
                unset($r['tags_geograficos']);
                $result[$i]=$r;
            }
        }
    }
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_get_tags_fields($row){
    $result=$row;
    $grupo_node=node_load($result['my_grupo_nid']);
    $result['tags']='';
    if(isset($grupo_node->field_grupo_regis_tags[0]['value']) && !empty($grupo_node->field_grupo_regis_tags[0]['value'])){
        $result['tags']=$grupo_node->field_grupo_regis_tags[0]['value'];
    }
    $result['tags_geograficos']='';
    if(isset($grupo_node->field_grupo_regis_tags_geo[0]['value']) && !empty($grupo_node->field_grupo_regis_tags_geo[0]['value'])){
         $result['tags_geograficos']=$grupo_node->field_grupo_regis_tags_geo[0]['value'];                
    }    
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_contact_form_multiple(&$form,$uid_in){
    if($uid_in=='usuarios'){
        $gestion_usuarios_send_message_uid_array=arg(3);
        $gestion_usuarios_send_message_uid_array=base64_decode($gestion_usuarios_send_message_uid_array);
        $gestion_usuarios_send_message_uid_array=unserialize($gestion_usuarios_send_message_uid_array);
        if(!empty($gestion_usuarios_send_message_uid_array)){
            $result=array();
            $my_user_array=array();
            foreach($gestion_usuarios_send_message_uid_array as $i=>$uid){
                $my_user=user_load($uid);
                if(isset($my_user->uid) && !empty($my_user->uid)){
                    $result[]=$my_user->name;
                    $my_user_array[]=$my_user;
                }
            }
            $form['to']['#value']=implode(',',$result);
            $form['recipient']['#value']=$my_user_array;
            $form['is_multiple']=array(
                '#type'=>'hidden',
                '#value'=>1,
            );
        }
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_contact_get_account_array($form_state){
    $result=array();
    if(isset($form_state['values']['is_multiple']) && !empty($form_state['values']['is_multiple'])){
        $result=$form_state['values']['recipient'];            
    }else{
        $result[]=$form_state['values']['recipient'];
    }
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_quant_time_form_form_alter(&$form,&$form_state,$form_id){
    if(hontza_grupos_mi_grupo_quant_time_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }
    $form['filter']['#title']=$fs_title;
    $form['filter']['#attributes']['id']='file_buscar_fs';
    $form['filter']['#attributes']['class']=$class;
    my_add_buscar_js();
}
function hontza_grupos_mi_grupo_quant_time_is_filter_activated(){
  $options = array(
    '1_week' => t('1 week'),
    '2_weeks' => t('2 weeks'),
    '1_month' => t('1 month'),
    '3_months' => t('3 months'),
    '6_months' => t('6 months'),
    '1_year' => t('1 year'),
    '2_years' => t('2 years'),
  );
  
  $period = filter_xss($_GET['period']);
  if (!$period || !array_key_exists($period, $options)) {
      return 0;
  }
  return 1;
}
//intelsat-2016
function hontza_grupos_mi_grupo_in_grupo(){
    global $user;
    if(is_super_admin()){
        return 1;
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        if(red_is_visualizador_pantalla()){
                return 1;
        }
        /*if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $visualizador_grupo_nid=visualizador_get_grupo_nid();
            print $visualizador_grupo_nid;
            if($my_grupo->nid==$visualizador_grupo_nid){
                return 1;
            }
        }*/
        return 1;
    }
    if(!hontza_is_user_anonimo()){    
        $my_grupo=og_get_group_context();                        
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            if(isset($user->og_groups[$my_grupo->nid]) && !empty($user->og_groups[$my_grupo->nid])){
                return 1;
            }
            $grupo_nid_array=hontza_grupos_mi_grupo_get_og_uid_grupo_nid_array($user->uid);
            if(in_array($my_grupo->nid,$grupo_nid_array)){
                return 1;
            }
            return 0;
        }        
    }    
    return 1;
}
//intelsat-2016
function hontza_grupos_mi_grupo_in_grupo_access_denied(){
    if(!hontza_grupos_mi_grupo_in_grupo()){
        drupal_access_denied();
        exit();
    }
}
//intelsat-2016
function hontza_grupos_mi_grupo_get_og_uid_grupo_nid_array($uid){
    $result=array();
    $res=db_query('SELECT * FROM {og_uid} WHERE uid=%d'.$uid);
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_bulk_actions_drupal_goto($type){
    global $base_url;
    $is_grupo=1;
    $url='user-gestion/grupos/propios';
    if($type=='delete_grupo'){
         $my_grupo=og_get_group_context();
         if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
              $my_node=hontza_get_node($my_grupo->nid,$my_grupo->vid);
              if(!(isset($my_node->nid) && !empty($my_node->nid))){
                  $is_grupo=0;   
              }
         }else{
             $is_grupo=0;            
         }    
    }
    //print $is_grupo;exit();
    if(!$is_grupo){    
        $url=$base_url.'/'.$url;
    }
    drupal_goto($url);
}
function hontza_grupo_mi_grupo_is_grupo_publico_colaborativo($grupo_nid,$grupo_node_in=''){
    $result=0;
    if(empty($grupo_nid)){
        $grupo_node=$grupo_node_in;
    }else{
        $grupo_node=node_load($grupo_nid);
    }
    if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
        $is_publico_colaborativo=1;
        $result=estrategia_is_grupo_publico($grupo_node,'',$is_publico_colaborativo);
    }
    return $result;
}
//intelsat-2016
function hontza_grupos_mi_grupo_node_delete_group_form_alter(&$form,&$form_state,$form_id){
    global $user;
    if($user->uid!=1){
        $form['target']['#options']=get_usuario_grupos_options();
    }
    $form['verb']['#default_value']=1;
}