<?php
function panel_admin_usuarios_acceso_callback(){
    return usuarios_acceso_callback();
}
function panel_admin_usuarios_acceso_is_usuarios_estadisticas($with_empresa=0,$konp='usuarios_estadisticas'){
    $param0=arg(0);
    if(!empty($param0) && strcmp($param0,'panel_admin')==0){
        $param1=arg(1);
        if(!empty($param1) && strcmp($param1,$konp)==0){
            if($with_empresa){
                $param2=arg(2);
                if(strcmp($param2,'todos')==0){
                    return 0;
                }
            }
            return 1;
        }
    }    
    return 0;
}
function panel_admin_usuarios_acceso_usuarios_captacion_informacion_callback(){
    return usuarios_captacion_informacion_callback();
}
function panel_admin_usuarios_acceso_usuarios_aportacion_valor_callback(){
    return usuarios_aportacion_valor_callback();
}
function panel_admin_usuarios_acceso_usuarios_generacion_ideas_callback(){
    return usuarios_generacion_ideas_callback();
}
function panel_admin_usuarios_acceso_add_filter_form(&$form){
    if(panel_admin_usuarios_acceso_is_estadisticas()){
        $param1=arg(1);
        if($param1!='usuarios_acceso'){
            $key=panel_admin_usuarios_acceso_get_session_key();
            $form['file_buscar_fs']['grupo_nid']=array(
                    '#type'=>'select',
                    '#title'=>t('Filter by group'),
                    '#options'=>panel_admin_items_define_filtro_grupo_options(),
                    '#default_value'=>contenidos_get_filter_value('grupo_nid',$key),
                    '#multiple'=>TRUE,
                );
        }    
    }
}
function panel_admin_usuarios_acceso_get_session_key(){
    $result='panel_admin_usuarios_acceso';
    return $result;
}
function panel_admin_usuarios_acceso_is_estadisticas(){
    $my_array=array('usuarios_acceso','usuarios_captacion_informacion','usuarios_aportacion_valor','usuarios_generacion_ideas');
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if(panel_admin_usuarios_acceso_is_usuarios_estadisticas(0,$value)){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_usuarios_acceso_add_where_grupo_nid_array(&$where,$type=''){
    $key=panel_admin_usuarios_acceso_get_session_key();
    if(isset($_SESSION[$key]['filter']['grupo_nid']) && !empty($_SESSION[$key]['filter']['grupo_nid'])){
        $grupo_nid_array=array_keys($_SESSION[$key]['filter']['grupo_nid']);
        if(count($grupo_nid_array)==1 && empty($grupo_nid_array[0])){
            return '';
        }else{
            $or_array=array();
            $grupo_nid_string=implode(',',$grupo_nid_array);
            $or_array[]='og_ancestry.group_nid IN('.$grupo_nid_string.')';
            if($type=='idea'){
                $or_array[]='idea.grupo_nid IN ('.$grupo_nid_string.')';
            }else if($type=='oportunidad'){
                $or_array[]='oportunidad.grupo_nid IN ('.$grupo_nid_string.')';
            }else if($type=='proyecto'){
                $or_array[]='proyecto.grupo_nid IN ('.$grupo_nid_string.')';
            }    
            $where[]='('.implode(' OR ',$or_array).')';
        }    
    }
}
function panel_admin_usuarios_acceso_filter_activated($fecha_inicio,$fecha_fin){
    if(empty($fecha_inicio) && empty($fecha_fin)){
        if(panel_admin_usuarios_acceso_is_estadisticas()){
            $param1=arg(1);
            if($param1!='usuarios_acceso'){
                $key=panel_admin_usuarios_acceso_get_session_key();
                $grupo_nid_array=contenidos_get_filter_value('grupo_nid',$key);
                if(empty($grupo_nid_array)){
                    return 0;
                }else{
                    $grupo_nid_array=array_keys($grupo_nid_array);
                    if(empty($grupo_nid_array)){
                        return 0;
                    }/*else if(count($grupo_nid_array)==1 && empty($grupo_nid_array[0])){
                        return 0;
                    }*/
                }    
            }else{
                return 0;
            }    
        }else{
            return 0;
        }    
    }
    return 1;
}    