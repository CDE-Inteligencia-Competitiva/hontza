<?php
function compartir_documentos_usuario_copiar_usuario_enviar_mail_form($form_state,$uid_in='',$my_grupo_nid=''){
    //print 'Sin implementar';exit();
    $is_popup=0;
    if(!empty($nid_in)){
        $nid=$nid_in;
        $is_popup=1;
    }else{
        $uid=arg(2);
    }
    $my_user=user_load($uid);
    $form['uid']=array(
        '#type'=>'hidden',
        '#default_value'=>$uid,
    );
    $form['my_grupo_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$my_grupo_nid,
    );
    $form['is_compartir_documentos_usuario']=array(
        '#type'=>'hidden',
        '#default_value'=>1,
    );
    $my_user->type='user';
    $enviar_mail_message=compartir_documentos_get_enviar_mail_message($my_user);
    $form['enviar_mail_message']=array(
        '#value'=>$enviar_mail_message,
    );
    $form['email']=array(
        '#type'=>'textfield',
        '#title'=>t('To'),
        '#required'=>true,
    );
    $form['rss_name']=array(
        '#type'=>'hidden',
        '#default_value'=>compartir_documentos_get_rss_name($my_user),
    );
    if(compartir_documentos_is_node_frecuencia_fields($my_user)){
        compartir_documentos_add_frecuencia_form_fields($form);
    }
    $form['send_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Send'),
    );
    //$_SESSION['enviar_mail_destination']=request_uri();
    $url_cancel='vigilancia/pendientes';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_cancel=$_REQUEST['destination'];
    }
    $url_cancel=compartir_documentos_copiar_on_enviar_mail_drupal_goto($my_user,$my_grupo_nid,$url_cancel,1);
    //$url_cancel=l(t('Cancel'),$url_cancel);
    $url_cancel=red_copiar_get_cancel_link($url_cancel,0,$is_popup);    
    $form['cancel_btn']['#value']=$url_cancel; 
    compartir_documentos_get_export_info($my_user->type,$copiar,$export_title,$compartir_documentos);
    drupal_set_title(t($export_title));
    return $form;
}
function compartir_documentos_usuario_add_compartir_usuario_link(&$html,$uid,$destination){
    $html[]=l(my_get_icono_action('copiar_nodo',t('Export person')),'compartir_documentos_usuario/copiar_usuario/'.$uid.'/enviar_mail',array('query'=>$destination,'html'=>true));
}
function compartir_documentos_usuario_copiar_usuario_enviar_mail_form_submit($form,&$form_state){
    global $base_url;
    global $user;
    $values=$form_state['values'];
    $uid=$form_state['values']['uid'];
    $my_user=user_load($uid);
    $my_user->type='user';
    $mail=$form_state['values']['email'];
    $rss_name=$form_state['values']['rss_name'];
    $my_grupo_nid=$form_state['values']['my_grupo_nid'];
    $message_text=$form_state['values']['message_text'];
    $frecuencia='';
    $hora='';
    /*if(compartir_documentos_is_node_frecuencia_fields($node)){    
        $frecuencia=$form_state['values']['frecuencia'];
        $hora=str_pad($values['hora_array']['hour'], 2, '0', STR_PAD_LEFT).':'.str_pad($values['hora_array']['minute'], 2, '0', STR_PAD_LEFT);
    }*/
    $is_shared=1;
    compartir_documentos_usuario_update_on_send($rss_name,$frecuencia,$hora,$is_shared,$uid,$my_user);
    $rss_url=$base_url.'/compartir_documentos/download_documento/'.$rss_name;
    $rss_url.='/'.$my_user->type;
    $html=$rss_url;
    //print $html;exit();
    $servidor_central_url=red_get_servidor_central_url();
    $is_validar_usuario_network=red_copiar_validar_usuario_mail_network_para_compartir($mail);
    if($is_validar_usuario_network){
        $caducidad=red_copiar_get_caducidad();
        $mail_from=base64_encode($user->mail);
        $compartir_servidor_url=$servidor_central_url.'/compartir_servidor_documentos/'.$my_user->type.'/'.base64_encode($rss_url).'/'.$caducidad.'/'.$mail_from;
    }else{
        $compartir_servidor_url=$rss_url;
    }    
    //print $compartir_servidor_url;exit();
    //$html=l($compartir_servidor_url,$compartir_servidor_url);
    //$html=l(my_get_icono_action('copiar_nodo'),$compartir_servidor_url,array('html'=>TRUE));
    //$html=my_get_icono_action('copiar_nodo');
    $html=compartir_documentos_get_email_message($mail,$compartir_servidor_url,$my_user,'',$is_validar_usuario_network,$message_text);
    //print $html;exit();
    red_copiar_send_mail($mail,$my_user->name,$html,'mimemail','',0);
    //drupal_set_message(t('The message has been sent.'));
    $document_type=red_copiar_get_document_type($node,'document');
    $message_user=red_copiar_get_enviar_mail_message_user($my_user,$is_validar_usuario_network,$mail);
    drupal_set_message($message_user);
    //print $rss_url;exit();    
    compartir_documentos_copiar_on_enviar_mail_drupal_goto($my_user,$my_grupo_nid);
}
function compartir_documentos_usuario_update_on_send($rss_name,$frecuencia,$hora,$is_shared,$uid,$my_user){
    $compartir_documentos_usuario=compartir_documentos_get_compartir_documentos_usuario_array($uid);
    if(!empty($compartir_documentos_usuario)){
        foreach($compartir_documentos_usuario as $i=>$row){
            db_query('UPDATE {compartir_documentos_usuario} SET field_user_rss_name_value="%s",is_shared=%d WHERE uid=%d',$rss_name,$is_shared,$uid);
        }
    }else{
        db_query('INSERT INTO {compartir_documentos_usuario}(uid,field_user_rss_name_value,is_shared) VALUES(%d,"%s",%d)',$uid,$rss_name,$is_shared);
    }
}
function compartir_documentos_get_compartir_documentos_usuario_array($uid,$field_user_rss_name_value=''){
    $result=array();
    if(!empty($uid)){
        $res=db_query('SELECT * FROM {compartir_documentos_usuario} WHERE uid=%d',$uid);    
    }else if(!empty($field_user_rss_name_value)){
        $res=db_query('SELECT * FROM {compartir_documentos_usuario} WHERE field_user_rss_name_value="%s" ORDER BY id DESC',$field_user_rss_name_value);
    }else{
        return $result;
    }    
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function compartir_documentos_usuario_download_documento(){
    $my_user=compartir_documentos_usuario_get_usuario_download();
        if(isset($my_user->uid) && !empty($my_user->uid)){
            $my_user->type='user';
            unset($my_user->uid);
            unset($my_user->og_groups);
            //unset($node->og_groups_both);
            unset($my_user->groups_selected);
            //echo print_r($my_user,1);exit();
            $result=array();
            $result['node']=compartir_documentos_encrypt_text(base64_encode(serialize($my_user)));
            $result=json_encode($result);
            print $result;
            exit();
        }
}
function compartir_documentos_usuario_get_usuario_download(){
        $field_user_rss_name_value=arg(2);
        $compartir_documentos_usuario=compartir_documentos_get_compartir_documentos_usuario_array('',$field_user_rss_name_value);
        if(isset($compartir_documentos_usuario[0])){
            $row=$compartir_documentos_usuario[0];
            $my_user=user_load($row->uid);
            return $my_user;
        }
        $my_result=new stdClass();
        return $my_result;
}
function compartir_documentos_usuario_importar($my_user){
    $my_result=new stdClass();
    $existe_user=user_load(array('name'=>$my_user->name));
    if(isset($existe_user->uid) && !empty($existe_user->uid)){
        return $my_result;
    }
    $existe_user=user_load(array('mail'=>$my_user->mail));
    if(isset($existe_user->uid) && !empty($existe_user->uid)){
        return $my_result;
    }
    /*$my_user=(array) $my_user;
    $my_user=user_save($my_user);*/
    if(module_exists('red_compartir')){
        $uid=red_compartir_grupo_user_save($my_user);
        $my_result=user_load($uid);
        compartir_documentos_usuario_save_on_imported($uid);
    }    
    return $my_result;
}
function compartir_documentos_usuario_save_on_imported($uid){
    $is_imported=1;
    $compartir_documentos_usuario=compartir_documentos_get_compartir_documentos_usuario_array($uid);
    if(!empty($compartir_documentos_usuario)){
        foreach($compartir_documentos_usuario as $i=>$row){
            db_query('UPDATE {compartir_documentos_usuario} SET field_user_rss_name_value="%s",is_imported=%d WHERE uid=%d',$rss_name,$is_imported,$uid);
        }
    }else{
        db_query('INSERT INTO {compartir_documentos_usuario}(uid,field_user_rss_name_value,is_imported) VALUES(%d,"%s",%d)',$uid,$rss_name,$is_imported);
    }
}
function compartir_documentos_usuario_is_imported($uid){
    print 'uid='.$uid.'<br>';
    $compartir_documentos_usuario=compartir_documentos_get_compartir_documentos_usuario_array($uid);
    if(!empty($compartir_documentos_usuario)){
        foreach($compartir_documentos_usuario as $i=>$row){
            if($row->is_imported){
                return 1;
            }
        }
    }
    return 0;
}