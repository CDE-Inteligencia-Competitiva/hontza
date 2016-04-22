<?php
function red_servidor_fix_sources_and_channels_creator_callback(){
    //$subdominio_array=red_servidor_grupo_get_subdominio_array('',$is_all);
    red_servidor_fix_fuentes_creadores();
    red_servidor_fix_canales_creadores();
    red_servidor_fix_facilitadores_creadores();
    return date('Y-m-d H:i');
}
function red_servidor_fix_subdominio_callback(){
    red_servidor_fix_subdominio_fuentes();
    red_servidor_fix_subdominio_canales();
    //
    return date('Y-m-d H:i');
}
function red_servidor_fix_subdominio_fuentes(){
    $fuentes_array=red_servidor_get_red_servidor_fuentes_array();
    if(!empty($fuentes_array)){
        foreach($fuentes_array as $i=>$fuente_row){
            $subdominio=red_get_subdominio_by_sareko_id($fuente_row->sareko_id);
            if($fuente_row->sareko_id=='LOKALA'){
                $subdominio='localhost';
            }
            red_servidor_fuente_update_subdominio($fuente_row->id,$subdominio);
        }
    }
}
function red_servidor_fix_subdominio_canales(){
    $canales_array=red_servidor_get_red_servidor_canales_array();
    if(!empty($canales_array)){
        foreach($canales_array as $i=>$canal_row){
            $subdominio=red_get_subdominio_by_sareko_id($canal_row->sareko_id);
            if($canal_row->sareko_id=='LOKALA'){
                $subdominio='localhost';
            }
            red_servidor_canal_update_subdominio($canal_row->id,$subdominio);
        }
    }
}
function red_servidor_fix_fuentes_creadores(){
    $fuentes_array=red_servidor_fuente_get_simple_array();
    if(!empty($fuentes_array)){
        foreach($fuentes_array as $i=>$row){
            if($row->sareko_id!='LOKALA'){
                $user_local=red_servidor_get_user_local_by_uid($row);
                if(isset($user_local->mail) && !empty($user_local->mail)){
                    $mail=$user_local->mail;
                    $uid=red_get_user_uid_by_mail($mail);
                    if(!empty($uid)){
                        red_servidor_fuente_update_uid($row,$uid);
                        red_servidor_node_update_uid($row,$uid);
                    }
                }    
            }
        }
    }
}
function red_servidor_fix_canales_creadores(){
    $canales_array=red_servidor_canal_get_simple_array();
    if(!empty($canales_array)){
        foreach($canales_array as $i=>$row){
            if($row->sareko_id!='LOKALA'){
                $user_local=red_servidor_get_user_local_by_uid($row);
                if(isset($user_local->mail) && !empty($user_local->mail)){
                    $mail=$user_local->mail;
                    $uid=red_get_user_uid_by_mail($mail);
                    if(!empty($uid)){
                        red_servidor_canal_update_uid($row,$uid);
                        red_servidor_node_update_uid($row,$uid);
                    }
                }    
            }
        }
    }
}
function red_servidor_fix_facilitadores_creadores(){
    $facilitadores_array=red_servidor_facilitador_get_simple_array();
    if(!empty($facilitadores_array)){
        foreach($facilitadores_array as $i=>$row){
            if($row->sareko_id!='LOKALA'){
                $user_local=red_servidor_get_user_local_by_uid($row);
                if(isset($user_local->mail) && !empty($user_local->mail)){
                    $mail=$user_local->mail;
                    $uid=red_get_user_uid_by_mail($mail);
                    if(!empty($uid)){
                        red_servidor_facilitador_update_uid($row,$uid);
                        red_servidor_node_update_uid($row,$uid);
                    }
                }    
            }
        }
    }
}