<?php
function red_crear_usuario_user_profile_form_alter(&$form,&$form_state,$form_id){
    if(strcmp($form_id,'user_profile_form')==0){
        if(red_crear_usuario_is_activado()){
            crear_usuario_user_profile_form_alter($form,$form_state,$form_id);
        }
        //intelsat-2016
        if(isset($form['_account']['#value']->uid) && !empty($form['_account']['#value']->uid)){
            $form['account']['name']['#attributes']['readonly']='readonly';
        }
    }
}
function red_crear_usuario_is_activado(){
    if(defined('_IS_RED_CREAR_USUARIO') && _IS_RED_CREAR_USUARIO!=1){
        return 0;
    }       
    if(module_exists('crear_usuario')){
        return 1;        
    }
    return 0;    
}
function red_crear_usuario_set_primary_links($result_in){
    $result=$result_in;
    if(red_crear_usuario_is_activado()){
        $result=crear_usuario_set_primary_links($result);
    }
    if(!is_super_admin()){
        $result=red_crear_usuario_unset_primary_links($result);
    }
    return $result;
}
function red_crear_usuario_set_secondary_links($secondary_in){
    $result=$secondary_in;
    if(red_crear_usuario_is_activado()){
        $result=crear_usuario_set_secondary_links($result);
    }
    return $result;
}
function red_crear_usuario_user_register_form_alter(&$form,&$form_state, $form_id){
    if(red_crear_usuario_is_activado()){
        crear_usuario_user_register_form_alter($form,$form_state, $form_id);
    }
}
function red_crear_usuario_add_grupos_usuario_propio($user){
    if(red_crear_usuario_is_activado()){
        crear_usuario_add_grupos_usuario_propio($user);
    }
}
function red_crear_usuario_get_profile_url(){
    if(red_crear_usuario_is_activado()){
        return crear_usuario_get_profile_url();
    }
    return url('user');
}
function red_crear_usuario_send_mail_added_usuario_grupo($account,$grupo_nid){
    if(red_crear_usuario_is_activado()){
        crear_usuario_send_mail_added_usuario_grupo($account,$grupo_nid);
    }
}
function red_crear_usuario_is_custom_css(){
    $result=0;
    if(red_crear_usuario_is_activado()){
        $result=crear_usuario_is_custom_css();
    }
    return $result;
}
function red_crear_usuario_get_path_custom_css(){
    $result='';
    if(red_crear_usuario_is_activado()){
        $result=crear_usuario_get_path_custom_css();
    }
    return $result;
}
function red_crear_usuario_add_ver_mas_usuario_sin_grupos($div_resto_grupos){
    $html=array();
    $is_ver_mas=0;
    if(!hontza_is_user_anonimo() && red_crear_usuario_is_activado()){
    //if(red_crear_usuario_is_rol_administrador_creador_grupo()){
        $is_ver_mas=1;
    //}
    }    
    if($is_ver_mas){
        $html[]=$div_resto_grupos;
        $my_link=l(my_get_icono_action('ver_mas32',t('See more'),'ver_mas32','',$style),'mis-grupos',array('html'=>TRUE,'attributes'=>array('id'=>'id_ver_mas')));
        $menu_desplegable=hontza_canal_rss_get_menu_desplegable();
        $ver_mas_html='<li id="li_menu_desplegable" class="li_menu_desplegable">'.$my_link.$menu_desplegable.'</li>';
        $html[]=$ver_mas_html;
        $html[]='</ul></div>';
    }
    return implode('',$html);
}
function red_crear_usuario_is_rol_administrador_creador_grupo(){
    global $user;    
    if(isset($user->roles[ADMINISTRADOR]) || isset($user->roles[ADMINISTRADOR_DE_GRUPO]) || isset($user->roles[CREADOR])){
        return 1;
    }
    return 0;
}
function red_crear_usuario_set_user_options_og_add_users(&$usuarios_del_grupo,&$user_options,$uid){
    if(!isset($user_options[$uid])){    
        $my_user=user_load($uid);
        if(isset($my_user->uid) && !empty($my_user->uid)){
            $usuarios_del_grupo[]=$uid;
            $user_options[$uid]=$my_user->name;
            asort($user_options);
        }
    }    
}
function red_crear_usuario_send_mail_register_pending_approval_admin($module, $key, $mail_to, $language, $params = array(),$creador_grupo){
    global $base_url;
    $my_lang='';
    if($language->language!='en'){
        $my_lang='/'.$language->language;
    }
    $html=array();
    $my_user=$params['account'];
    $html[]=t('Hi',array(),$langcode).' '.$creador_grupo->name.',';
    if(red_crear_usuario_is_red_alerta_net()){
        $url=l('http://www.redalerta.net','http://www.redalerta.net');
        $html[]=t('The user !user_name has asked for a new account at RedAlerta !link',array('!user_name'=>$my_user->name,'!link'=>$url));
    }else{
        $url=l($base_url,$base_url);
        $html[]=t('The user !user_name has asked for a new account at !link',array('!user_name'=>$my_user->name,'!link'=>$url));    
    }
    red_crear_usuario_add_user_profile_values($html,$my_user);
    //$html[]=t('You can activate this account by clicking here');
    //$edit_message=t('Also you can see/edit the rol and profile here');
    $edit_message=t('You can edit the role and activate/delete this account here');
    $html[]=$edit_message.': '.l(t('Profile'),$base_url.$my_lang.'/user/'.$my_user->uid.'/edit');
    $message=implode('<BR>',$html);
    $subject=t('Account details for !username at !site (pending admin approval)',array('!username'=>$my_user->name,'!site'=>$base_url));
    //intelsat-2016
    red_copiar_send_mail($mail_to,$subject,$message,'mimemail','');
}
function red_crear_usuario_is_red_alerta_net(){
    if(red_crear_usuario_is_activado()){
        return 1;
    }
    return 0;
}
function red_crear_usuario_add_user_profile_values(&$html,$my_user,$lang_in='',$with_profile_title=1){
    if(!empty($lang_in) && $lang_in=='es'){
        $html[]='Datos del perfil:';
        $html[]='Nombre: '.$my_user->profile_nombre;
        $html[]='Apellidos: '.$my_user->profile_apellidos;
        $html[]='Email: '.$my_user->mail;
        if(isset($my_user->profile_empresa_ciudad) && !empty($my_user->profile_empresa_ciudad)){
            $html[]='Ciudad: '.$my_user->profile_empresa_ciudad;
        }
        if(isset($my_user->profile_empresa_pais) && !empty($my_user->profile_empresa_pais)){
            $html[]='País: '.$my_user->profile_empresa_pais;
        }
    }else{
        if($with_profile_title){
            $html[]=t('His/her profile is').':';
        }
        $html[]=t('Name').': '.$my_user->profile_nombre;
        $html[]=t('Surname').': '.$my_user->profile_apellidos;
        $html[]=t('Email').': '.$my_user->mail;
        if(isset($my_user->profile_empresa_ciudad) && !empty($my_user->profile_empresa_ciudad)){
            $html[]=t('City').': '.$my_user->profile_empresa_ciudad;
        }
        if(isset($my_user->profile_empresa_pais) && !empty($my_user->profile_empresa_pais)){
            $html[]=t('Country').': '.$my_user->profile_empresa_pais;
        }
    }
    if(red_crear_usuario_is_red_alerta_net()){
        $hontza_network=red_crear_usuario_get_hontza_network_label();
        $html[]='<BR>'.t('Why do you like to become member of !network_name groups?',array('!network_name'=>$hontza_network));
        $html[]=$my_user->profile_why_become_member;
        $html[]='<BR>'.t('What can you contribute to the groups?');
        $html[]=$my_user->profile_what_contribute.'<BR>';     
    }
}
function red_crear_usuario_save_profile_values($user,$name_array_in='',$field_array_in=''){
    if(red_crear_usuario_is_activado()){
        $field_array=array();
        if(!empty($field_array_in)){
            $name_array=$field_array_in;
        }else{
            $my_array=array('profile_nombre','profile_apellidos');
            $field_array=array_merge($my_array,array('profile_empresa'));
            if(empty($name_array_in)){
                $name_array=$my_array;
            }else{
                $name_array=$name_array_in;
                $name_array=array_merge($my_array,$name_array);
            }
        }
        //echo print_r($user,1);exit();
        if(!empty($name_array)){
            foreach($name_array as $i=>$name){
                if(isset($user->$name)){
                    $profile_field_row=red_crear_usuario_get_profile_fields_row($name);
                    if(isset($profile_field_row->fid) && !empty($profile_field_row->fid)){
                        //crear_usuario_profile_values_save_row($profile_field_row->fid,$user->uid,$user->$name);
                        $is_save=red_crear_usuario_is_save($name,$field_array);
                        if($is_save){
                            red_crear_usuario_profile_values_save_row($profile_field_row->fid,$user->uid,$_POST[$name]);
                        }else if(!empty($field_array_in)){
                            //intelsat-2016
                            red_crear_usuario_profile_values_save_row($profile_field_row->fid,$user->uid,$user->$name);
                        }    
                    }
                }
            }
        }
    }
}
function red_crear_usuario_get_profile_fields_row($name){
    $where=array();
    $res=db_query('SELECT * FROM {profile_fields} WHERE profile_fields.name="%s"',$name);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_crear_usuario_profile_values_save_row($fid,$uid,$value){
    $row=crear_usuario_get_profile_values_row($fid,$uid);
    if(isset($row->fid) && !empty($row->fid)){
        db_query('UPDATE {profile_values} SET value="%s" WHERE fid=%d AND uid=%d',$value,$fid,$uid);        
    }else{
        db_query('INSERT INTO {profile_values}(fid,uid,value) VALUES(%d,%d,"%s")',$fid,$uid,$value);        
    }
}
function red_crear_usuario_get_profile_values_row($fid,$uid){
    $where=array();
    $res=db_query('SELECT * FROM {profile_values} WHERE fid=%d AND uid=%d',$fid,$uid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_crear_usuario_unset_primary_links($result_in){
    $my_array=explode('<li',$result_in);
    $find='<a href="';
    $result=array();
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if($i>0){
                $pos=strpos($value,$find);
                if($pos===FALSE){
                    $result[]=$value;
                    continue;
                }else{
                    $string=substr($value,$pos+strlen($find));
                    $pos=strpos($string,'"');
                    if($pos===FALSE){
                        $result[]=$value;
                        continue;
                    }else{
                        $string=substr($string,0,$pos);
                        $string_array=explode('/',$string);
                        $num=count($string_array);
                        if(isset($string_array[$num-1])){
                            $konp=$string_array[$num-1];
                            if(in_array($konp,array('bookmarks','messages','notifications','track'))){
                                continue;
                            } 
                        }
                        $result[]=$value;
                    }
                }
            }else{
                $result[]=$value;
            }    
        }
    }
    return implode('<li',$result);
}
function red_crear_usuario_is_view_user_field($title){
    if($title==t('Subscriptions')){
        return 0;
    }    
    if(red_movil_is_activado()){
        return red_movil_is_view_user_field($title);
    }
    return 1;
}
function red_crear_usuario_get_profile_empresa_key($value_in,$result_in){
    $value=$value_in;
    if(!empty($result_in)){
        $value=strtolower($value_in);            
        $key_array=array_keys($result_in);
        if(!empty($key_array)){
            foreach($key_array as $i=>$key){
                $konp=strtolower($key);
                if($value==$konp){
                    return $key;
                }
            }
        }
    }
    return $value;
}
function red_crear_usuario_get_hontza_network_label(){
    $result='';
    if(red_is_subdominio_red_alerta()){
        $result='Red Alerta';
    }else{
        $result='Hontza Network';    
    }
    return $result;
}
function red_crear_usuario_get_empresa_value($row,$my_user_in=''){
    $result='';
    if(empty($my_user_in)){
        $result=$row->profile_values_profile_empresa_value;
    }else{
        $my_user=$my_user_in;
    }
    if(empty($result)){
        if(!(isset($my_user->uid) && !empty($my_user->uid))){
            $my_user=user_load($row->uid);
        }
        if(isset($my_user->profile_empresa) && !empty($my_user->profile_empresa)){
            $result=$my_user->profile_empresa;
        }else{
            if(isset($my_user->data) && !empty($my_user->data)){
                $data=unserialize($my_user->data);
                $data=(array) $data;
                if(isset($data['profile_empresa']) && !empty($data['profile_empresa'])){
                    $result=$data['profile_empresa'];
                }
            }    
        }    
    }
    return $result;
}
function red_crear_usuario_get_usuario_capta($u){
    $result='';
    if(!empty($u->nombre) || !empty($u->apellidos)){
        $result=$u->nombre.' '.$u->apellidos;
    }else{
        if(isset($u->data) && !empty($u->data)){
            $data=unserialize($u->data);
            $data=(array) $data;
            if(isset($data['profile_nombre']) && isset($data['profile_apellidos'])){
                $result=$data['profile_nombre'].' '.$data['profile_apellidos'];
            }
        }
    }
    return $result;
}
function red_crear_usuario_is_save($name,$field_array){
    $result=1;
    if(in_array($name,$field_array)){
        if(isset($_POST[$name]) && !empty($_POST[$name])){
            $result=1;
        }else{
            $result=0;
        }
    }
    return $result;
}
function red_crear_usuario_get_grupos_assoc($values=array()){
    $where=array();
    $where[]='1';
    $where[]='node.type="grupo"';
    $where[]='term_data.vid=6';
    $where[]='term_data.tid IN(27,29)';
    $sql='SELECT node.*,term_node.tid 
    FROM {node} 
    LEFT JOIN {term_node} ON node.vid=term_node.vid 
    LEFT JOIN {term_data} ON term_node.tid=term_data.tid 
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        if(hontza_is_mis_grupos()){
            if(in_array($row->nid,$values)){
                continue;
            }
        }
        $grupo_title=$row->title;
        $title_popup='';
        $icono=hontza_grupos_mi_grupo_get_tipo_grupo_icono($row,$row->tid,$title_popup).'&nbsp';
        $result[$row->nid]=$icono.$grupo_title;
    }
    return $result;
}
function red_crear_usuario_get_grupo_propietario($grupo){
    $my_user=user_load($grupo->uid);
    if(isset($my_user->uid) && !empty($my_user->uid)){
        if(isset($my_user->roles[CREADOR]) && !empty($my_user->roles[CREADOR])){
            return $my_user;
        }
    }
    $editor_jefe=user_load($grupo->field_admin_grupo_uid[0]['value']);
    return $editor_jefe;
}
function red_crear_usuario_get_user_status_label($user){
    if(isset($user->status) && !empty($user->status)){
        return t('Active');
    }
    return t('Blocked');
}
function red_crear_usuario_get_user_language_label($user){
    if(isset($user->language) && !empty($user->language)){
        $language_array=language_list();
        if(isset($language_array[$user->language])){
            if(isset($language_array[$user->language]->native)){
                return $language_array[$user->language]->native;
            }
        }
        return $user->language;
    }        
    return '';        
}
function red_crear_usuario_get_user_start_page_html($user){
    $html=array();
    red_crear_usuario_get_pagina_arranque_values($grupo_options,$default_group_nid,$page_options,$default_page,1);
    $group='';
    if(isset($grupo_options[$default_group_nid])){
        $group=$grupo_options[$default_group_nid];
    }
    $html[]='<b>'.t('Group').':</b>&nbsp;'.$group;
    //$page=t('Home');
    $page='';
    if(isset($page_options[$default_page])){
        $page=$page_options[$default_page];
    }    
    $html[]='<b>'.t('Page').':</b>&nbsp;'.$page;    
    return implode('<BR>',$html);
}
function red_crear_usuario_get_pagina_arranque_values(&$grupo_options,&$default_group_nid,&$page_options,&$default_page,$with_empty=0){
    $uid=red_crear_usuario_get_pagina_arranque_uid_arg();
    $default_group_nid='';
    $default_page='';
    //intelsat-2016
    require_once('sites/all/modules/user/user.pages.inc');
    $row=user_pagina_de_arranque_get_row($uid);
    if(isset($row->id) && !empty($row->id)){
        $default_group_nid=$row->group_nid;
        $default_page=$row->start_page;
    }
    $form=array();
    $grupo_options=my_get_grupo_seguimiento_options(0);
    //simulando
    //$default_group_nid='';
    //
    if(empty($default_group_nid)){
        if($with_empty){
            return;
        }
        $group_keys=array_keys($grupo_options);
        if(isset($group_keys[0])){
            $default_group_nid=$group_keys[0];
        }
    }
    $page_options=user_get_pagina_de_arranque_pagina_options($default_group_nid);
}    
function red_crear_usuario_exportar_csv_callback(){
    //intelsat-2016
    red_crear_usuario_exportar_csv();        
}
function red_crear_usuario_is_custom_css_hontza(){
    if(defined('_IS_CUSTOM_CSS_HONTZA') && _IS_CUSTOM_CSS_HONTZA==1){
        return 1;
    }
    return 0;
}
function red_crear_usuario_is_editando_usuario_propio($form,$category=''){
    global $user;
    $result=0;
    $param=arg(3);
    $is_ok=0;
    if(empty($param)){
        if(empty($category)){
            $is_ok=1;
        }    
    }else{
        if(!empty($category)){
            if($param==$category){
                $is_ok=1;
            }
        }
    }
    if($is_ok){
        if(!empty($form)){
            if($form['_account']['#value']->uid==$user->uid){
                $result=1;
            }
        }else{
            if(is_user_editing('-1',$uid)){
                if($uid==$user->uid){
                    return 1;
                }    
            }
        }
    }        
    return $result;
}
function red_crear_usuario_get_custom_css_hontza_imagen_red($path_custom){
    $result='';
    //$result='<div style="width:400px;">&nbsp;</div>';
    $result='<div class="div_sin_red">&nbsp;</div>';            
    if(hontza_is_sareko_id_red()){
        if(hontza_is_red_hoja()){
            //$width=400;
            $width=700;
            //$result='<img class="imagen_red" src="'.$path_custom.'img/imagen_red.png" width="'.$width.'"/>';
            $result='<img src="'.$path_custom.'img/imagen_red.png"/>';
        }
    }
    return $result;
}
function red_crear_usuario_is_crear_usuario_net(){
    if(defined('_IS_CREAR_USUARIO_NET') && _IS_CREAR_USUARIO_NET==1){
        return 1;
    }
    return 0;
}
function red_crear_usuario_is_sites_default_files_buho_logo($src){
    if($src=='sites/default/files/buho_logo.png'){
        return 1;
    }
    return 0;
}
function red_crear_usuario_save_user_subdominios_network($user){
    global $base_url;
    if(red_crear_usuario_is_save_user_subdominios_network_activado()){
        $user_array=array();
        $row=new stdClass();
        $row->name=$user->name;
        $row->mail=$user->mail;
        $row->base_url=$base_url;
        $row->sareko_id=_SAREKO_ID;
        $user_array[]=$row;
        //if(module_exists('red_compartir')){
        red_crear_usuario_save_user_subdominios_network_array_postapi($user_array);
        //}    
    }    
}
function red_crear_usuario_save_user_subdominios_network_array_postapi($user_array){
    $user_enviar=red_crear_usuario_prepare_user_enviar_array($user_array);
    $postdata=array();
    $postdata['user_enviar']=$user_enviar;
    $url=red_get_servidor_central_url();
    $url.='/red_servidor/validar_usuario_network/save_user_subdominios_network_array';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query( $postdata ) );
    $data=curl_exec($curl);
    $result=unserialize(trim($data));
    curl_close($curl);
    if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
        return 'ok';
    }
    return '';
}
function red_crear_usuario_prepare_user_enviar_array($user_array){
    $result=base64_encode(serialize($user_array));
    $result=red_crear_usuario_encrypt_text($result);
    $result=base64_encode($result);    
    return $result;
}
function red_crear_usuario_encrypt_text($value) {
   if(!$value ) return false;
 
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,red_crear_usuario_define_encrypt_key1(), $value, MCRYPT_MODE_ECB,red_crear_usuario_define_encrypt_key2());
   return trim(base64_encode($crypttext));
}
function red_crear_usuario_define_encrypt_key1(){
    $result=md5('kfshsdkhdsfhsdfkl vckviucvovciobvihsjkdfhdjkdjfhdfjdfhdfjf');
    return $result;
}
function red_crear_usuario_define_encrypt_key2(){
    return md5(date('Y-m-d'));
}
function red_crear_usuario_decrypt_text($value) {
   if(!$value ) return false;
 
   $crypttext = base64_decode($value);
   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,red_crear_usuario_define_encrypt_key1(), $crypttext, MCRYPT_MODE_ECB, red_crear_usuario_define_encrypt_key2() );
   return trim($decrypttext);
}
function red_crear_usuario_on_user_profile_form_submit_goto_empresa($category,$account){
    if(red_crear_usuario_is_activado()){
        crear_usuario_on_user_profile_form_submit_goto_empresa($category,$account);
    }
}
function red_crear_usuario_is_save_user_subdominios_network_activado(){
    /*if(defined('_IS_SAVE_USER_SUBDOMINIOS_NETWORK')){
        if(_IS_SAVE_USER_SUBDOMINIOS_NETWORK!=1){
            return 0;
        }
    }*/
    return 1;
}
//intelsat-2016
function red_crear_usuario_red_servidor_validar_usuario_network_save_user_subdominios_tarea(){
    global $base_url;
    //if(hontza_is_sareko_id('BLANCO')){
        if(red_crear_usuario_is_save_user_subdominios_network_activado()){
            //$users=my_get_user_list();                
            $users=red_crear_usuario_get_user_array();
            if(!empty($users)){
                $user_array=array();
                foreach($users as $i=>$my_user){
                    if(isset($my_user->uid) && !empty($my_user->uid)){
                        $row=new stdClass();
                        $row->name=$my_user->name;
                        $row->mail=$my_user->mail;
                        $row->base_url=$base_url;
                        $row->sareko_id=_SAREKO_ID;
                        $user_array[]=$row;
                    }    
                }
                //if(module_exists('red_compartir')){
                red_crear_usuario_save_user_subdominios_network_array_postapi($user_array);
                //}
            }    
        }
    //}    
}
function red_crear_usuario_get_user_array(){
    $result=array();
    $res=db_query('SELECT * FROM {users} WHERE 1');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_crear_usuario_is_show_mi_perfil_menu(){
    if(is_user_editing('-1',$uid)){
        return 1;
    }
    return 0;
}
function red_crear_usuario_add_user_og_names_default_value($uid){
    if(!empty($uid)){
        $my_user=user_load($uid);
        if(isset($my_user->uid) && !empty($my_user->uid)){
            return $my_user->name;
        }
    }
    return '';
}
function red_crear_usuario_get_pagina_arranque_uid_arg(){
    $param0=arg(0);
    $uid=arg(1);
    if(!empty($param0) && $param0=='red_publica'){
        if(!empty($uid) && $uid=='user'){
            $uid=arg(2);
        }
    }    
    return $uid;
}
//intelsat-2016
function red_crear_usuario_gestion_usuarios_descargar_usuarios($my_user_array){    
    $param2=arg(2);
    if(!empty($param2) && $param2=='descargar_usuarios'){
        red_crear_usuario_exportar_csv(1,$my_user_array);
        exit();
    }
}
function red_crear_usuario_exportar_csv($is_param=0,$users_array_in=''){
        $data_csv_array=array();
        $data_csv_array[0]=array('Username','Nombre','Apellidos','Email','Empresa','Email corporativo','Pais','Fecha');
        if($is_param){
            $users_array=$users_array_in;
        }else{
            $users_array=hontza_solr_get_users();
        }
        if(!empty($users_array)){
            foreach($users_array as $i=>$row){
                if(isset($row->uid) && !empty($row->uid)){
                    $my_user=user_load($row->uid);
                    //echo print_r($my_user,1);
                    if(isset($my_user->uid) && !empty($my_user->uid)){
                        $data_csv[0]=$my_user->name;
                        $data_csv[1]=$my_user->profile_nombre;
                        $data_csv[2]=$my_user->profile_apellidos;
                        $data_csv[3]=$my_user->mail;
                        $data_csv[4]=$my_user->profile_empresa;
                        $data_csv[5]=$my_user->profile_empresa_email_corporativo;
                        $data_csv[6]=$my_user->profile_empresa_pais;                        
                        $data_csv[7]=date('d/m/Y',$my_user->created);
                        $data_csv_array[]=$data_csv;
                    }    
                }    
            }
        }
        estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'usuarios',"\t");
}
function red_crear_usuario_add_select_user_icon_form_field(&$form){
    if(red_crear_usuario_is_select_user_icon_activado()){
        $select_user_icon_html=red_crear_usuario_get_select_user_icon_html();
        $form['picture']['select_user_icon_fs']=array(
            '#title'=>t('Select picture'),
            '#type'=>'fieldset',
        );
        $form['picture']['select_user_icon_fs']['select_user_icon']=array(
            '#value'=>$select_user_icon_html,
        );        
    }    
}
function red_crear_usuario_get_select_user_icon_html(){
    global $base_url;
    $html=array();
    $dir='sites/default/files/select_user_icon';
    if(is_dir($dir)){
       $result=glob($dir.'/*');
       if(!empty($result)){
           $html[]='<div>';
           foreach($result as $i=>$src_file){
               $icon='<input type="checkbox" id="src_user_picture_'.$i.'" name="src_user_picture['.$i.']" class="src_user_picture_class" value="'.$src_file.'" style="float:left;">';
               //$url_file=url($src_file);
               $url_file=$base_url.'/'.$src_file;
               $icon.='<img src="'.$url_file.'" width="48" style="float:left;">';
               $html[]='<div style="float:left;padding-right:10px;padding-bottom:10px;">'.$icon.'</div>';
           }
           $html[]='</div>';
           $html[]='<div style="clear:both;padding-top:10px;">';
           $url_designed='http://www.freepik.com';
           $html[]='<p>'.t('Designed by @designed',array('@designed'=>'Freepik')).':&nbsp;'.l($url_designed,$url_designed,array('absolute'=>TRUE,attributes=>array('target'=>'_blank'))).'</p>';
           $html[]='</div>';
       }
    }    
    $html[]=red_crear_usuario_add_select_user_icon_js();    
    return implode('',$html);
}
function red_crear_usuario_is_select_user_icon_activado(){
    /*if(defined('_IS_SELECT_USER_ICON') && _IS_SELECT_USER_ICON==1){
        return 1;
    }
    return 0;*/
    return 1;
}
function red_crear_usuario_save_select_user_icon($my_user){
   global $base_path; 
   if(isset($_POST['src_user_picture']) && !empty($_POST['src_user_picture'])){
       $src_user_picture_array=array_values($_POST['src_user_picture']);
       if(isset($src_user_picture_array[0]) && !empty($src_user_picture_array[0])){
            $src_user_picture=$src_user_picture_array[0];
            if(file_exists($src_user_picture)){
                $url_picture=$my_user->picture;
                //if(!file_exists($url_picture)){
                    //if(empty($url_picture)){
                        $pathinfo=pathinfo($src_user_picture);
                        $url_picture=file_directory_path().'/pictures/picture-'.$my_user->uid.'.'.$pathinfo['extension'];
                        db_query('UPDATE {users} SET picture="%s" WHERE uid=%d',$url_picture,$my_user->uid);
                    //}
                //}
                copy($src_user_picture,$url_picture);
            }    
       } 
   }     
}
function red_crear_usuario_add_select_user_icon_js(){
   $js='';
   $js.='<script>';
   $js.='$(document).ready(function()
   {
    $(".src_user_picture_class").click(function(){
        var is_selected=$(this).is(":checked")
        if(is_selected){
            src_user_picture_other_checked($(this).attr("id"),false);
        }
    });
    function src_user_picture_other_checked(my_id,my_checked){
        $(".src_user_picture_class").each(function(){
            if($(this).attr("id")!=my_id){
                $(this).attr("checked",my_checked);
            }
        });
    }
   });';
    //drupal_add_js($js,'inline');
   $js.='</script>';
   return $js;
}