<?php
function red_validar_usuario_network($form_state_values){
    $url=red_get_servidor_central_url().'/red_servidor/validar_usuario_network';
    $user_string=red_validar_usuario_network_prepare_user_enviar($form_state_values);
    if($form_state_values['name']=='admin'){
        return 1;
    }
    $postdata=array();
    $postdata['validar_usuario_network']=$user_string;
    $validar_usuario=red_validar_usuario_network_postapi($url,$postdata,$result);
    //echo print_r($result,1);exit();
    //intelsat-2016
    if($validar_usuario){
        return 1;
    }
    if(isset($result['email_diferente']) && !empty($result['email_diferente'])){
        if($result['email_diferente']==1){            
            form_set_error('name', t('%name is already registered with a different email', array('%name' => $form_state_values['name'])));        
        }
    }
    //form_set_error('name', t('The name %name is already taken.', array('%name' => $form_state_values['name'])));    
    form_set_error('name', t('%mail is already registered with a different username', array('%mail' => $form_state_values['mail']))); 
    return 0;
}
function red_validar_usuario_network_postapi($url,$postdata,&$result){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query( $postdata ) );
    $data=curl_exec($curl);
    $result=unserialize(trim($data));
    curl_close($curl);
    //echo print_r($data,1);exit();
    if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
        return 1;
    }    
    return 0;
}
function red_validar_usuario_network_prepare_user_enviar($form_state_values){
    $my_user=new stdClass();
    $user_local=new stdClass();
    $user_local->uid='';
    if(isset($form_state_values['_account']) && isset($form_state_values['_account']->uid)){
        $user_local->uid=$form_state_values['_account']->uid;
    }    
    $user_local->name=$form_state_values['name'];
    $user_local->mail=$form_state_values['mail'];    
    $my_user->user_local=$user_local;
    $my_user->presave_account=$form_state_values['_account'];
    $result=base64_encode(serialize($my_user));
    $result=red_crear_usuario_encrypt_text($result);
    $result=base64_encode($result);
    
    return $result;
}
//intelsat-2016
function red_validar_usuario_mail_network_para_compartir($mail){
    $url=red_get_servidor_central_url().'/red_servidor/validar_usuario_mail_network_para_compartir';
    $user_string=red_validar_usuario_mail_network_prepare_user_enviar_para_compartir($mail);
    $postdata=array();
    $postdata['validar_usuario_mail_network']=$user_string;
    $validar_usuario=red_validar_usuario_network_postapi($url,$postdata,$result);
    if($validar_usuario){
        return 1;
    }
    return 0;
}
//intelsat-2016
function red_validar_usuario_mail_network_prepare_user_enviar_para_compartir($mail){
    $my_user=new stdClass();
    $my_user->user_local=new stdClass();
    $my_user->user_local->mail=$mail;
    $result=base64_encode(serialize($my_user));
    $result=red_crear_usuario_encrypt_text($result);
    $result=base64_encode($result);    
    return $result;
}