<?php
function hontza_registrar_goto(){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_goto();
    }    
}
function hontza_registrar_is_registrar_activado(){
    if(module_exists('red_registrar')){
        return red_registrar_is_registrar_activado();
    }
    return 0;
}
function hontza_registrar_add_registrados_central_server_link(&$result){
    if(hontza_red_servidor_registrar_is_activado()){
        red_servidor_registrar_add_registrados_central_server_link($result);
    }
}
function hontza_red_servidor_registrar_is_activado(){
    if(red_is_servidor_central()){
        if(module_exists('red_servidor_registrar')){
            return 1;
        }
    }
    return 0;
}
function hontza_registrar_get_url_sign_network($nid){
    $result='red_compartir/compartir_grupo_hoja/'.$nid;
    /*if(hontza_registrar_is_registrar_activado()){
        return red_registrar_get_url_sign_network($nid,$result);
    }*/
    return $result;
}
function hontza_registrar_is_pagina_arranque_registrar(){
    if(hontza_registrar_is_registrar_activado()){
        return red_registrar_is_pagina_arranque_registrar();
    }
    return 0;
}
function hontza_registrar_get_pagina_arranque_registrar(){
    if(hontza_registrar_is_registrar_activado()){
        return red_registrar_goto(1);
    }
    return '';
}
function hontza_registrar_get_secondary_local_tasks($secondary){
    if(hontza_registrar_is_registrar_activado()){
        return red_registrar_get_secondary_local_tasks($secondary);
    }
    return $secondary;
}
function hontza_registrar_on_user_profile_form_submit($category,$account){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_on_user_profile_form_submit($category,$account);
    }
    red_crear_usuario_on_user_profile_form_submit_goto_empresa($category,$account);
}
function hontza_registrar_user_profile_empresa_form_alter(&$form,&$form_state,$form_id){
    if(hontza_registrar_is_registrar_activado()){
        red_registrar_user_profile_empresa_form_alter($form,$form_state,$form_id);
    }
}
function hontza_registrar_user_profile_account_form_alter(&$form,&$form_state,$form_id){
     if(hontza_registrar_is_registrar_activado()){
         red_registrar_user_profile_account_form_alter($form,$form_state,$form_id);
     }
}
function hontza_registrar_is_sareko_id_red_desactivado(){
    if(hontza_registrar_is_registrar_activado()){
        return red_registrar_is_sareko_id_red_desactivado();
    }
    return 0;
}
function hontza_registrar_yes_no_options($with_empty=0,$with_no_value=0){
    $result=array();
    if($with_empty){
        $result['']='';
    }
    $no_value=0;
    if($with_no_value){
        $no_value=2;
    }
    $result[$no_value]=t('No');        
    $result[1]=t('Yes');
    return $result;
}
function hontza_registrar_get_primary_local_tasks($primary){
    if(hontza_registrar_is_registrar_activado()){
        return red_registrar_get_primary_local_tasks($primary);
    }
    return $primary;
}
function hontza_registrar_is_administrador($account=''){
    global $user;
    if(isset($account->uid) && !empty($account->uid)){
        $my_user=$account;
    }else{
        if(is_super_admin()){
            return 1;
        }    
        $my_user=$user;
    }
    if($my_user->uid==1){
        return 1;
    }
    if(isset($my_user->roles[ADMINISTRADOR]) && !empty($my_user->roles[ADMINISTRADOR])){
        return 1;
    }
    return 0;
}