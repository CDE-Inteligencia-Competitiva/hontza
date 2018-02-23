<?php
function red_servidor_usuarios_csv_inc_crear_csv_callback(){
    $users_array=hontza_solr_get_users();
    $users_array=red_servidor_get_users_name_mail($users_array);
    $red_servidor_validar_usuario_network_array=red_servidor_usuarios_csv_inc_get_red_servidor_validar_usuario_network_array();
    $red_servidor_validar_usuario_network_array=red_servidor_usuarios_csv_inc_get_red_servidor_validar_usuario_network_todos_array($red_servidor_validar_usuario_network_array);
    $users_array=array_merge($users_array,$red_servidor_validar_usuario_network_array);
    red_servidor_usuarios_csv_inc_crear_csv($users_array);
}
function red_servidor_get_users_name_mail($users_array){
    $result=array();
    if(!empty($users_array)){
        foreach($users_array as $i=>$my_user){
            if(isset($my_user->uid) && !empty($my_user->uid)){
                $my_id=$my_user->name.'-'.$my_user->mail;
                $result[$my_id]=array();
                $result[$my_id]['name']=$my_user->name;
                $result[$my_id]['mail']=$my_user->mail;
                $sareko_id_array=array();
                $subdominio_array=array();
                red_servidor_usuarios_csv_inc_get_sareko_id_csv($my_user->uid,$sareko_id_array,$subdominio_array);
                $result[$my_id]['sareko_id']=implode('<->',$sareko_id_array);
                $result[$my_id]['subdominio']=implode('<->',$subdominio_array);                
            }
        }
    }
    return $result;
}
function red_servidor_usuarios_csv_inc_get_red_servidor_validar_usuario_network_array(){
    $result=array();
    $res=db_query('SELECT * FROM {red_servidor_validar_usuario_network} WHERE 1');
    while($my_user=db_fetch_object($res)){
                $my_id=$my_user->name.'-'.$my_user->mail.'-'.$my_user->sareko_id.'-'.$my_user->base_url;
                $result[$my_id]=array();
                $result[$my_id]['name']=$my_user->name;
                $result[$my_id]['mail']=$my_user->mail;
                $result[$my_id]['sareko_id']=$my_user->sareko_id;
                $result[$my_id]['subdominio']=$my_user->base_url;
    }
    return $result;
}
function red_servidor_usuarios_csv_inc_add_name_mail($users_array,$red_servidor_validar_usuario_network_array){
    $result=$users_array;
    
    return $result;
}
function red_servidor_usuarios_csv_inc_get_sareko_id_csv($uid,&$sareko_id_array,&$subdominio_array){
    $result=array();
    $res=db_query('SELECT red_servidor_grupo.* FROM {red_servidor_usuario} LEFT JOIN {red_servidor_grupo} ON red_servidor_usuario.red_servidor_grupo_id=red_servidor_grupo.id WHERE red_servidor_usuario.servidor_uid=%d',$uid);
    while($row=db_fetch_object($res)){
        if(!in_array($row->sareko_id,$sareko_id_array)){
            $sareko_id_array[]=$row->sareko_id;
        }
        if(!in_array($row->subdominio,$subdominio_array)){
            $subdominio_array[]=$row->subdominio;
        }
    }
    return $result;
}
function red_servidor_usuarios_csv_inc_get_red_servidor_validar_usuario_network_todos_array($red_servidor_validar_usuario_network_array){
    $result=$red_servidor_validar_usuario_network_array;
    $res=db_query('SELECT * FROM {red_servidor_validar_usuario_network_todos} WHERE 1');
    while($my_user=db_fetch_object($res)){
            $my_id=$my_user->name.'-'.$my_user->mail.'-'.$my_user->sareko_id.'-'.$my_user->base_url;               
            if(!isset($result[$my_id])){
                $result[$my_id]=array();
                $result[$my_id]['name']=$my_user->name;
                $result[$my_id]['mail']=$my_user->mail;
                $result[$my_id]['sareko_id']=$my_user->sareko_id;
                $result[$my_id]['subdominio']=$my_user->base_url;
            }   
    }
    return $result;
}
function red_servidor_usuarios_csv_inc_crear_csv($users_array){
    $data_csv_array=array();
        $data_csv_array[0]=array('Username','Mail','Red_id','Subdominio');
        if(!empty($users_array)){
            foreach($users_array as $i=>$my_user){
                $data_csv=array();
                        $data_csv[0]=$my_user['name'];
                        $data_csv[1]=$my_user['mail'];
                        $data_csv[2]=$my_user['sareko_id'];                        
                        $data_csv[3]=$my_user['subdominio'];
                        $data_csv_array[]=$data_csv;        
            }
        }
        estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'usuarios',"\t");
}        