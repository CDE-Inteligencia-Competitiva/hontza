<?php
function red_servidor_grupo_guardar_grupo_hoja_callback(){
    if(isset($_POST['grupo_enviar']) && !empty($_POST['grupo_enviar'])){
        $result['ok']=1;
        $grupo_enviar=$_POST['grupo_enviar'];
        $grupo_enviar=base64_decode($grupo_enviar);
        $grupo_enviar=red_compartir_grupo_decrypt_text($grupo_enviar);
        $grupo_enviar=unserialize(base64_decode($grupo_enviar));
        //$pais_enviar=$_POST['pais'];
        //$temas_enviar=$_POST['temas'];
        //$region_enviar=$_POST['region'];
        $pais_enviar='';
        $temas_enviar='';
        $region_enviar='';
        $status=1;
        $row=red_servidor_grupo_get_red_servidor_grupo($grupo_enviar);
        if(isset($row->id) && !empty($row->id)){
            db_query($sql=sprintf('UPDATE {red_servidor_grupo} SET country="%s",topics_of_interest="%s",regions_of_interest="%s",status=%d WHERE id=%d',$pais_enviar,$temas_enviar,$region_enviar,$status,$row->id));
            red_servidor_grupo_crear_usuarios($grupo_enviar,$row);
            $red_servidor_grupo_id=$row->id;
            //intelsat-2016
            if(module_exists('red_servidor_registrar')){
                red_servidor_registrar_grupo_guardar_grupo_hoja($red_servidor_grupo_id,$grupo_enviar);
            }
            red_servidor_grupo_authenticate_on_guardar_grupo_hoja($grupo_enviar);            
            return 'The group exist in the network';
        }else{
            $local_nid=$grupo_enviar->nid; 
            $local_vid=$grupo_enviar->vid; 
            $local_uid=$grupo_enviar->uid;
            $fecha=time();
            $sareko_id=$grupo_enviar->sareko_id;
            $subdominio=$grupo_enviar->subdominio;
            $grupo_title=$grupo_enviar->grupo_title;
            //
            $sql=sprintf('INSERT INTO {red_servidor_grupo}(local_nid,local_vid,local_uid,fecha,sareko_id,subdominio,grupo_title,country,topics_of_interest,regions_of_interest,status) VALUES(%d,%d,%d,"%s","%s","%s","%s","%s","%s","%s",%d)',$local_nid,$local_vid,$local_uid,$fecha,$sareko_id,$subdominio,$grupo_title,$pais_enviar,$temas_enviar,$region_enviar,$status);
            db_query($sql);
            //intelsat-2016
            $red_servidor_grupo_id=db_last_insert_id('red_servidor_grupo','id');
            red_servidor_grupo_crear_usuarios($grupo_enviar,'');
            //intelsat-2016
            if(module_exists('red_servidor_registrar')){
                red_servidor_registrar_grupo_guardar_grupo_hoja($red_servidor_grupo_id,$grupo_enviar);
            }
            red_servidor_grupo_authenticate_on_guardar_grupo_hoja($grupo_enviar);            
        }        
    }
    return t('Shared group saved').' '.date('Y-m-d H:i:s');
}
function red_servidor_grupo_get_red_servidor_grupo($grupo_enviar){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_grupo} WHERE local_nid=%d AND local_vid=%d AND subdominio="%s"',$grupo_enviar->nid,$grupo_enviar->vid,$grupo_enviar->subdominio));
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_servidor_grupo_get_user_grupos_red_alerta_get_contents_callback(){
    /*$result=array();
    if(isset($_REQUEST['user_mail']) && !empty($_REQUEST['user_mail'])){
            $user_mail=base64_decode($_REQUEST['user_mail']);
            $uid=red_get_user_uid_by_mail($user_mail);
            if(!empty($uid)){
                $result=my_get_og_grupo_list($uid,1,0,0);                
            }            
    }
    print base64_encode(serialize($result));
    exit();*/
    $output=red_compartir_grupo_get_user_grupos_subdominio_get_contents_callback(0);
    //$result=unserialize(base64_decode($output));
    //echo print_r($result,1);
    print $output;
    exit();    
}
function red_servidor_grupo_crear_usuarios($grupo_enviar,$row_in=''){    
    if(empty($row_in)){
        $row=red_servidor_grupo_get_red_servidor_grupo($grupo_enviar);
    }else{
        $row=$row_in;
    }
    $fecha=time();
    $red_servidor_grupo_id=$row->id;
    //        
    if(isset($grupo_enviar->user_array) && !empty($grupo_enviar->user_array)){
        foreach($grupo_enviar->user_array as $i=>$r){
            //if($r->uid!=1){
            //AVISO::::lo de arriba estaba comentado, no lo hemos comentado al poner red_is_not_admin
            //intelsat-2015-redalerta-admin-user  
            if(red_is_not_admin($r)){    
                $local_uid=$r->uid;
                $uid=red_get_user_uid_by_mail($r->mail);
                if(empty($uid)){
                    $uid=red_servidor_grupo_user_save($r);
                }else{
                    $uid=red_servidor_grupo_user_save($r,1,$uid);
                }                
                $servidor_uid=$uid;
                $red_servidor_usuario_array=red_servidor_grupo_get_red_servidor_usuario_array($red_servidor_grupo_id,$servidor_uid);
                if(count($red_servidor_usuario_array)>0){
                    continue;
                }else{
                    db_query('INSERT INTO {red_servidor_usuario}(red_servidor_grupo_id,local_uid,fecha,servidor_uid) VALUES(%d,%d,%d,%d)',$red_servidor_grupo_id,$local_uid,$fecha,$servidor_uid);
                }
            }
        }
    }
    //admin siempre tiene que estar
    $servidor_admin_uid=1;
    $red_servidor_usuario_admin_array=red_servidor_grupo_get_red_servidor_usuario_array($red_servidor_grupo_id,$servidor_admin_uid);
    if(!(count($red_servidor_usuario_admin_array)>0)){
        $local_admin_uid=1;
        db_query('INSERT INTO {red_servidor_usuario}(red_servidor_grupo_id,local_uid,fecha,servidor_uid) VALUES(%d,%d,%d,%d)',$red_servidor_grupo_id,$local_admin_uid,$fecha,$servidor_admin_uid);
    }
}
function red_servidor_grupo_user_save($u_in,$is_edit=0,$uid_edit=0){
    //AVISO::::se ha tomado como base sareratu_user_update de sareratu.user.inc
    /*$u=$u_in;
    if($is_edit){
        $result = db_query('REPLACE INTO {users} (uid,name, pass, mail, mode, sort, threshold, theme, signature, created, access, login, status, timezone, language, picture, init, data, signature_format) VALUES(%d,"%s", "%s", "%s", %d, %d, %d, "%s", "%s", %d, %d, %d, %d, "%s", "%s", "%s", "%s", "%s", %d)',$uid_edit,$u->name, $u->pass, $u->mail, $u->mode, $u->sort, $u->threshold, $u->theme, $u->signature, $u->created, $u->access, $u->login, $u->status, $u->timezone, $u->language, $u->picture, $u->init, $extra_data, $u->signature_format);
    }else{
        $result = db_query('INSERT INTO {users} (name, pass, mail, mode, sort, threshold, theme, signature, created, access, login, status, timezone, language, picture, init, data, signature_format) VALUES("%s", "%s", "%s", %d, %d, %d, "%s", "%s", %d, %d, %d, %d, "%s", "%s", "%s", "%s", "%s", %d)',$u->name, $u->pass, $u->mail, $u->mode, $u->sort, $u->threshold, $u->theme, $u->signature, $u->created, $u->access, $u->login, $u->status, $u->timezone, $u->language, $u->picture, $u->init, $extra_data, $u->signature_format);
    }
    $uid=red_get_user_uid_by_mail($u->mail);
    $u->uid=$uid;
    unset($u->pass);
    user_save($u);
    // egitekoa mugatu: 2 => izena emandako erabiltzailea
    //db_query('REPLACE INTO {users_roles}(rid, uid) VALUES(2, %d)', $u->uid);
    if(isset($u->my_profile_values) && !empty($u->my_profile_values)){
        foreach($u->my_profile_values as $i=>$f){
            db_query('REPLACE INTO {profile_values}(fid, uid, value) VALUES(%d, %d, "%s")', $f->fid,$uid,$f->value);
        }    
    }
    return $uid;*/
    return red_compartir_grupo_user_save($u_in,$is_edit,$uid_edit,1);
}
function red_servidor_grupo_get_user_grupos_red_alerta_mis_grupos_get_contents_callback(){
    $result=array();
    if(isset($_REQUEST['user_mail']) && !empty($_REQUEST['user_mail'])){
            $user_mail=base64_decode($_REQUEST['user_mail']);
            $uid=red_get_user_uid_by_mail($user_mail);
            if(!empty($uid)){
                $result=hontza_grupos_get_mis_grupos_rows($uid,'',1,1);
                $result=red_servidor_grupo_unset_grupo_shared($result);
            }            
    }
    print base64_encode(serialize($result));
    exit();
}
function red_servidor_grupo_get_red_servidor_usuario_array($red_servidor_grupo_id='',$uid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($red_servidor_grupo_id)){
        $where[]='red_servidor_grupo_id='.$red_servidor_grupo_id;
    }
    if(!empty($uid)){
        $where[]='servidor_uid='.$uid;
    }
    //
    $sql='SELECT * FROM {red_servidor_usuario} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_grupo_get_grupos_red_alerta($sub_in='',$my_user=''){    
    $result=array();
    $subdominio_array=red_servidor_grupo_get_subdominio_array($my_user);
    if(!empty($subdominio_array)){
        foreach($subdominio_array as $i=>$subdominio){
            if(empty($sub_in) || $subdominio!=$sub_in){
                $sub_grupos=red_servidor_grupo_get_grupos_subdominio($subdominio,$my_user);
                if(!is_array($sub_grupos)){
                    $sub_grupos=array();
                }
                $result=array_merge($result,$sub_grupos);
            }    
        }
    }
    return $result;
}
function red_servidor_grupo_get_subdominio_array($my_user='',$is_all=0){
    global $user;
    if(isset($my_user->uid) && !empty($my_user->uid)){
        $uid=$my_user->uid;
    }else{
        $uid=$user->uid;
    }
    //
    $where=array();
    $where[]='1';
    if(!$is_all){
        $where[]='red_servidor_usuario.servidor_uid='.$uid;
    }
    $where[]='red_servidor_grupo.status=1';
    $where[]='red_servidor_grupo.sareko_id!="LOKALA"';
    $sql='SELECT DISTINCT(red_servidor_grupo.subdominio) 
    FROM {red_servidor_grupo}
    LEFT JOIN {red_servidor_usuario} ON red_servidor_grupo.id=red_servidor_usuario.red_servidor_grupo_id
    WHERE '.implode(' AND ',$where);
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->subdominio;
    }
    return $result;
}
function red_servidor_grupo_get_grupos_subdominio($subdominio,$my_user=''){
    global $user;
    if(isset($my_user->uid) && !empty($my_user->uid)){
        $user_mail=$my_user->mail;
    }else{
        $user_mail=$user->mail;
    }
    $result=array();
    if(!empty($subdominio)){
        if($subdominio=='localhost'){
            return $result;
        }
        $url='http://'.$subdominio;
        $url.='/red_compartir/red_compartir_grupo_get_user_grupos_subdominio_get_contents?user_mail='.base64_encode($user_mail);
        /*if(user_access('root')){
            print $url.'<BR>';
            //exit();
        }*/
        $content=@file_get_contents($url);
        $result=unserialize(base64_decode($content));
        if(!empty($result)){
            foreach($result as $key=>$row){
                $result[$key]=$row;
                $result[$key]->is_grupo_red_alerta=1;
                $result[$key]->is_grupo_subdominio=1;
                $result[$key]->subdominio=$subdominio;            
            }
        }
    }
    return $result;
}
function red_servidor_grupo_get_user_grupos_red_alerta_mis_grupos(){
    //$kont=0;
    //$rows=array();
    $grupos_red_alerta=red_servidor_grupo_get_grupos_red_alerta();
    //
    /*if(!empty($grupos_red_alerta)){
        foreach($grupos_red_alerta as $i=>$row){
            $data=$row;
            $data->node_title=$row->title;
            $data->term_data_tid=$row->type_of_group_tid;
            //echo print_r($data,1);
            hontza_grupos_set_mis_grupos_row($data,'',$kont,'',2,$rows);
            $kont++;                
        }
    }*/
    
    $rows=red_compartir_grupo_set_mis_grupos_row($grupos_red_alerta);
        
    return $rows;    
}
function red_servidor_grupo_user_change_callback(){
    /*$result=array();
    $result['ok']='ok';
    if(isset($_POST['user_enviar']) && !empty($_POST['user_enviar'])){
        $user_enviar=unserialize(red_compartir_grupo_decrypt_text($_POST['user_enviar']));
        if(isset($user_enviar->mail) && !empty($user_enviar->mail)){
            
                $uid=red_get_user_uid_by_mail($user_enviar->mail);
                if(!empty($uid)){
                    $uid=red_servidor_grupo_user_save($user_enviar,1,$uid);
                }
                
        }        
    }
    print serialize($result);
    exit();*/
    red_compartir_grupo_user_change_callback();
}
function red_servidor_grupo_get_user_grupos_otros_subdominios_get_contents_callback(){
    $result=array();
    if(isset($_REQUEST['user_mail']) && !empty($_REQUEST['user_mail'])){
            $user_mail=base64_decode($_REQUEST['user_mail']);
            if(isset($_REQUEST['subdominio']) && !empty($_REQUEST['subdominio'])){
                $subdominio=base64_decode($_REQUEST['subdominio']);
                $uid=red_get_user_uid_by_mail($user_mail);
                if(!empty($uid)){
                    $my_user=user_load($uid);
                    $result=red_servidor_grupo_get_grupos_red_alerta($subdominio,$my_user);                    
                }                
            }            
    }
    print base64_encode(serialize($result));
    exit();    
}
function red_servidor_grupo_add_user_group_callback(){
    $result=array();
    $result['ok']='ok';
    if(isset($_POST['grupo_enviar']) && !empty($_POST['grupo_enviar'])){
        $grupo_enviar=unserialize(base64_decode((red_compartir_grupo_decrypt_text(base64_decode($_POST['grupo_enviar'])))));        
        red_servidor_grupo_crear_usuarios($grupo_enviar);
    }
    print serialize($result);
    exit();
}
function red_servidor_grupo_get_subdominio_array_get_contents_callback(){
    $result=array();
    if(isset($_REQUEST['user_mail']) && !empty($_REQUEST['user_mail'])){
            $user_mail=base64_decode($_REQUEST['user_mail']);
                $uid=red_get_user_uid_by_mail($user_mail);
                if(!empty($uid)){
                    $my_user=user_load($uid);
                    $result=red_servidor_grupo_get_subdominio_array($my_user);                 
                }
    }            
    print base64_encode(serialize($result));
    exit();            
}
function red_servidor_grupo_authenticate_on_guardar_grupo_hoja($grupo_enviar){
    $my_user=red_servidor_grupo_get_user_by_grupo_enviar($grupo_enviar);
    //echo print_r($grupo_enviar,1);
    //exit();
    if(isset($my_user->name) && !empty($my_user->name)){        
        $user_login_enviar=base64_encode(serialize($my_user));
        $user_login_enviar=red_compartir_grupo_encrypt_text($user_login_enviar);
        $user_login_enviar=base64_encode($user_login_enviar);
        red_servidor_login_authenticate_red_alerta_callback($user_login_enviar,1);
    }
}
function red_servidor_grupo_get_user_by_grupo_enviar($grupo_enviar){
    /*$uid=$grupo_enviar->uid;
    if(isset($grupo_enviar->user_array) && !empty($grupo_enviar->user_array)){
        foreach($grupo_enviar->user_array as $i=>$user_row){
            if($uid==$user_row->uid){
                return $user_row;
            }
        }            
    }*/
    if(isset($grupo_enviar->user_local) && isset($grupo_enviar->user_local->uid) && !empty($grupo_enviar->user_local->uid)){
        return $grupo_enviar->user_local;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_servidor_grupo_mensaje_bienvenida_red_alerta_callback(){
    /*drupal_set_title(t('Welcome to Alert Network').'!');
    $html=array();
    //$html[]='<p>'.t('Welcome to Alert Network!').'!</p>';
    $html[]='<p>'.t('From now on, you will be able to share').':</p>';
    $html[]='- '.t('information sources').'<BR>';
    $html[]='- '.t('information channels').'<BR>';
    //intelsat-2016
    //$html[]='- '.t('facilitators').'<BR>';
    $html[]='- '.t('experts').'<BR>';
    $html[]='<p>'.t('You will be able to create shared groups in the Central Server with other users of the Alert Network').'</p>';*/
    drupal_set_title(t('Welcome, your group is now connected to Hontza/Alerta Network').'!');
    /*$html=array();
    $html[]='<p>'.t('From now on, you will be able to ').':</p>';
    $html[]='<p>a) '.t('Interact to other Hontza/Alerta Network groups in the following ways').':</p>';
    $html[]='- '.t('Export/import challenges').'<BR>';
    $html[]='- '.t('Export/import channels or searches').'<BR>';
    $html[]='- '.t('Export/import news').'<BR>';
    $html[]='- '.t('Export/import wikis').'<BR>';
    $html[]='- '.t('Export/import reports').'<BR>';
    $html[]='- '.t('Export/import projects').'<BR>';
    $html[]='- '.t('Export/import experts').'<BR>';
    $html[]='<BR><p>b) '.t('Share contents on the central server').':</p>';
    $html[]='- '.t('Share channels').'<BR>';
    $html[]='<BR><p>c) '.t('Search contents of the Hontza/Alerta Network').':</p>';
    $html[]='- '.t('Hontza/Alerta platforms').'<BR>';
    $html[]='- '.t('Collaborative or open groups').'<BR>';
    $html[]='- '.t('Experts').'<BR>';
    $html[]='- '.t('Shared channels').'<BR>';
    $html[]='<BR><p>d) '.t('If you are authorised, you will be able to create open or collaborative groups in the Central Server and invite to other users of the Alert Network').'</p>';
    */
    $html[]=t("<p>If your role is Group administrator, now you can export contents to other groups!</p>
    - Challenges<BR>
    - Channels, local Searches and News<BR>
    - Wikis, Reports and Projects<BR>
    - Experts<BR><BR>
    <p>Now you can share channels on the central server!</p>
    <p>Now you can search the main players and contents of the Network!</p>
    - Hontza/Alerta Platforms<BR>
    - Collaborative & Open Groups<BR>
    - Experts<BR>
    - Shared Channels<BR><BR>
    <p>Also selected users can create groups in the Central Server and invite any Hontza/Alerta Network user</p>
    <p>Enjoy! It's time to watch together!</p>");    
    return implode('',$html);   
}
function red_servidor_grupo_guardar_no_compartir_grupo_hoja_callback(){
    if(isset($_POST['grupo_enviar']) && !empty($_POST['grupo_enviar'])){
        $result['ok']=1;
        $grupo_enviar=$_POST['grupo_enviar'];
        $grupo_enviar=base64_decode($grupo_enviar);
        $grupo_enviar=red_compartir_grupo_decrypt_text($grupo_enviar);
        $grupo_enviar=unserialize(base64_decode($grupo_enviar));
        $row=red_servidor_grupo_get_red_servidor_grupo($grupo_enviar);
        $status=0;
        if(isset($row->id) && !empty($row->id)){
            db_query($sql=sprintf('UPDATE {red_servidor_grupo} SET status=%d WHERE id=%d',$status,$row->id));
        }    
    }
    return t('Disconnected from Network').' '.date('Y-m-d H:i:s');
}
function red_servidor_grupo_unset_grupo_shared($result_in){
    if(hontza_is_servidor_red_alerta()){
        $result=array();
        if(!empty($result_in)){
            foreach($result_in as $i=>$row){
                if(isset($row['my_grupo_nid']) && !empty($row['my_grupo_nid'])){
                    if(!red_servidor_is_grupo_shared($row['my_grupo_nid'])){
                        $result[]=$row;
                    }
                }else{
                    $result[]=$row;
                }
            }
        }
        return $result;
    }
    return $result_in;
}
function red_servidor_grupo_simular_subdominios_erroneos_callback(){
    red_servidor_grupo_repasar_subdominios_erroneos();
}
function red_servidor_grupo_get_red_servidor_grupo_array($with_status=0,$status=''){
    $result=array();
    $where=array();
    $where[]='1';
    if($with_status){
        if(is_numeric($status)){
            $where[]='status='.$status;
        }    
    }
    $sql='SELECT * FROM {red_servidor_grupo} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_grupo_repasar_subdominios_erroneos(){
    $timeout_array=array();
    $red_servidor_grupo_array=red_servidor_grupo_get_red_servidor_grupo_array(1,1);
    if(!empty($red_servidor_grupo_array)){
        $context = stream_context_create(array(
                'http' => array(
                    'timeout' => 15
                )
            )
        );             
        foreach($red_servidor_grupo_array as $i=>$row){
            $content=file_get_contents('http://'.$row->subdominio,0,$context);
            if($content===FALSE || $row->subdominio=='localhost'){
            //if(!red_servidor_grupo_url_exists('http://'.$row->subdominio)){
                $timeout_array[]=$row;
            }
        }
    }
    //
    if(!empty($timeout_array)){
        $result=array();
        $result[]='Subdominios erroneos en network.hontza.es';
        $result[]="\n";
        foreach($timeout_array as $i=>$row){
            red_servidor_grupo_update_status($row->id,0);
            $result[]="red_servidor_grupo_id:".$row->id;
            $result[]="red_id:".$row->sareko_id;
            $result[]="subdominio:".$row->subdominio;
            $result[]="grupo:".$row->grupo_title;
            $result[]="\n";
        }
        $message=implode("\n",$result);
        $subject='Subdominios erroneos en network.hontza.es';
        $mail_to_array=array('hontza@hontza.es');
        if(!empty($mail_to_array)){
            foreach($mail_to_array as $b=>$mail_to){
                //intelsat-2016
                red_copiar_send_mail($mail_to,$subject,$message,'text','',1);
            }    
        }    
    }
}
function red_servidor_grupo_url_exists($url) {
    $h = get_headers($url);
    $status = array();
    preg_match('/HTTP\/.* ([0-9]+) .*/', $h[0] , $status);
    return ($status[1] == 200);
}
function red_servidor_grupo_update_status($id,$status){
    db_query('UPDATE {red_servidor_grupo} SET status=%d WHERE id=%d',$status,$id);
}