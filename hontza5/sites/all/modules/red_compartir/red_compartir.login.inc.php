<?php
function red_compartir_login_authenticate_local_callback($user_login_enviar_in='',$is_on_guardar_grupo_hoja=0){
    global $base_url;
    global $language;
    //return 'Funcion desactivada';
    /*if(isset($_POST['user_login_enviar']) && !empty($_POST['user_login_enviar'])){        
        $user_login_enviar=$_POST['user_login_enviar'];*/
    $my_lang=red_compartir_login_cambiar_idioma();
    $user_login_enviar=red_compartir_login_get_user_login_enviar_by_request($user_login_enviar_in);
    if(!empty($user_login_enviar)){
        $user_login_enviar=red_compartir_grupo_decrypt_text($user_login_enviar);
        $user_string=base64_decode($user_login_enviar);
        $my_user=unserialize($user_string);
        $user_array=(array) $my_user;
        $user_result=red_compartir_login_user_authenticate($user_array);
        if(isset($user_result->uid) && !empty($user_result->uid)){
            $grupo_nid=arg(2);
            $grupo=node_load($grupo_nid);
            //
            if(isset($grupo->nid) && isset($grupo->nid)){
                //$url=url(red_compartir_define_redalerta_servidor_url().'/'.$grupo->purl.'/dashboard',array('absolute'=>TRUE));
                if(!empty($my_lang)){
                    $code=$my_lang;
                    $my_lang='/'.$my_lang;
                }else{
                    $code='en';
                }
                //$url=url($base_url.$my_lang.'/'.$grupo->purl.'/dashboard',array('absolute'=>TRUE));
                //intelsat-2015                
                //intelsat-2016
                $destination=red_compartir_login_get_destination();
                //
                $url=url($base_url.$my_lang.'/'.$grupo->purl.'/cambiar_idioma',array('absolute'=>TRUE,'query'=>'my_idioma='.trim($code,'/').$destination));
                //print $url;exit();
                drupal_goto($url);
            }else{
                //drupal_goto('dashboard');
                if($is_on_guardar_grupo_hoja){
                    drupal_goto('red_servidor/mensaje_bienvenida_red_alerta');
                }else{
                    //drupal_goto('user');
                    $url=hontza_get_pagina_de_arranque();
                    drupal_goto($url);
                }
            }
            //return red_compartir_login_error_message();
            return red_compartir_login_on_login_error();
        }
    }
    //return red_compartir_login_error_message();
    return red_compartir_login_on_login_error();
}
function red_compartir_login_user_authenticate($form_values = array()) {
  global $user;

  // Load the account to check if the e-mail is denied by an access rule.
  // Doing this check here saves us a user_load() in user_login_name_validate()
  // and introduces less code change for a security fix.
  $account = red_compartir_login_user_load(array('name' => $form_values['name'], 'pass' => trim($form_values['pass']), 'status' => 1));
  /*if ($account && drupal_is_denied('mail', $account->mail)) {
    form_set_error('name', t('The name %name is registered using a reserved e-mail address and therefore could not be logged in.', array('%name' => $account->name)));
  }*/
  
  // Name and pass keys are required.
  // The user is about to be logged in, so make sure no error was previously
  // encountered in the validation process.
  if (!form_get_errors() && !empty($form_values['name']) && !empty($form_values['pass']) && $account) {
    $user = $account;
    user_authenticate_finalize($form_values);
    return $user;
  }
  else {
    //watchdog('user', 'Login attempt failed for %user.', array('%user' => $form_values['name']));
    return '';  
  }
}
function red_compartir_login_user_load($user_info = array()) {
  // Dynamically compose a SQL query:
  $query = array();
  $params = array();

  if (is_numeric($user_info)) {
    $user_info = array('uid' => $user_info);
  }
  elseif (!is_array($user_info)) {
    return FALSE;
  }

  foreach ($user_info as $key => $value) {
    if ($key == 'uid' || $key == 'status') {
      $query[] = "$key = %d";
      $params[] = $value;
    }
    else if ($key == 'pass') {
      $query[] = "pass = '%s'";
      //gemini-2014
      //$params[] = md5($value);
      $params[] = $value;
    }
    else {
      $query[]= "LOWER($key) = LOWER('%s')";
      $params[] = $value;
    }
  }
  $result = db_query('SELECT * FROM {users} u WHERE '. implode(' AND ', $query), $params);

  if ($user = db_fetch_object($result)) {
    $user = drupal_unpack($user);

    $user->roles = array();
    if ($user->uid) {
      $user->roles[DRUPAL_AUTHENTICATED_RID] = 'authenticated user';
    }
    else {
      $user->roles[DRUPAL_ANONYMOUS_RID] = 'anonymous user';
    }
    $result = db_query('SELECT r.rid, r.name FROM {role} r INNER JOIN {users_roles} ur ON ur.rid = r.rid WHERE ur.uid = %d', $user->uid);
    while ($role = db_fetch_object($result)) {
      $user->roles[$role->rid] = $role->name;
    }
    user_module_invoke('load', $user_info, $user);
  }
  else {
    $user = FALSE;
  }

  return $user;
}
function red_compartir_login_error_message(){
    $message=t('Login error');
    form_set_error('login_error',$message);
    return $message;
}
function red_compartir_login_get_user_login_enviar_by_request($user_login_enviar_in=''){
    if(isset($_POST['user_login_enviar']) && !empty($_POST['user_login_enviar'])){        
        $user_login_enviar=$_POST['user_login_enviar'];
        return $user_login_enviar;
    }
    if(isset($_REQUEST['user_login_enviar_get']) && !empty($_REQUEST['user_login_enviar_get'])){        
        $user_login_enviar=base64_decode($_REQUEST['user_login_enviar_get']);
        return $user_login_enviar;
    }
    if(!empty($user_login_enviar_in)){
        return base64_decode($user_login_enviar_in);
    }
    return '';
}
function red_compartir_login_on_login_error(){
    /*global $user;
    if(isset($user->uid) && !empty($user->uid)){
        drupal_goto('dashboard');        
        return '';
    }else{
        drupal_goto('user');
    }
    return red_compartir_login_error_message();*/
    drupal_goto('logout');
}
function red_compartir_login_cambiar_idioma(){
    global $language;
    //
    $languages=language_list();
    $code='en';
    if(isset($_REQUEST['red_idioma']) && !empty($_REQUEST['red_idioma'])){
        $code=$_REQUEST['red_idioma'];
    }
    //   
    if(isset($languages[$code]) && !empty($languages[$code])){
        $language=$languages[$code];        
    }
    if($code=='en'){
        return '';
    }
    return $code;
}
function red_compartir_login_get_destination(){
    $destination='&destination=dashboard';
                if(isset($_REQUEST['compartir_documentos_url'])){
                    if(in_array($_REQUEST['compartir_documentos_node_type'],array('canal_de_yql'))){
                        $destination='&destination=red/copiar/crear_canal_yql/'.$_REQUEST['compartir_documentos_url'].'/'.$_REQUEST['mail_from'];
                    }else if(in_array($_REQUEST['compartir_documentos_node_type'],array('item'))){
                        $destination=red_compartir_login_get_import_documento_destination('news');
                    }else{
                        $destination=red_compartir_login_get_import_documento_destination();                    
                    }                        
                }else if(isset($_REQUEST['red_destination']) && !empty($_REQUEST['red_destination'])){
                    $destination='&destination='.$_REQUEST['red_destination'];
                }
    return $destination;            
}
function red_compartir_login_get_import_documento_destination($type_in=''){
    $type='';
    if(!empty($type_in)){
        $type='_'.$type_in;
    }
    $destination='&destination=compartir_documentos'.$type.'/importar_documento'.$type.'/'.$_REQUEST['compartir_documentos_node_type'];
    $destination.='/'.$_REQUEST['compartir_documentos_url'].'/'.$_REQUEST['mail_from'];
    return $destination;                    
}