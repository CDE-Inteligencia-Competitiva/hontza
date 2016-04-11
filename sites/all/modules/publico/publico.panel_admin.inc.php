<?php
function publico_panel_admin_is_edit_contents(){
    if(publico_panel_admin_is_edit_project()){
        return 1;
    }
    if(publico_panel_admin_is_edit_about()){
        return 1;
    }
    if(publico_panel_admin_is_edit_about()){
        return 1;
    }
    if(publico_panel_admin_is_aviso_legal()){
        return 1;
    }
    if(publico_panel_admin_is_texto()){
        return 1;
    }
    if(publico_panel_admin_is_boletin_texto()){
        return 1;
    }
    if(publico_panel_admin_is_edit_logo()){
        return 1;
    }
    return 0;
}
function publico_panel_admin_is_edit_project($type=''){
    $param0=arg(0);
    if($type=='about'){
        $nid=visualizador_get_sobre_proyecto_nid();
    }else if($type=='aviso_legal'){    
        $nid=visualizador_get_aviso_legal_nid();
    }else if($type=='texto'){     
        $nid=visualizador_inicio_get_texto_nid();
    }else if($type=='boletin_texto'){     
        $nid=publico_alerta_user_get_texto_mis_boletines_grupo_nid();
    }else{
        $nid=visualizador_get_project_nid();
    }    
    if(!empty($param0) && $param0=='node'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            if($param1==$nid){
                /*$param2=arg(2);
                if(!empty($param2)){*/
                    return 1;
                //}
            }else{
                if(publico_panel_admin_is_tnid_edit_project($nid,$param1)){
                    return 1;
                }
            }
        }    
    }
    if(is_node_add('page')){
        $translation=my_get_request('translation');
        if($translation==$nid){
            return 1;
        }else{
            if(publico_panel_admin_is_tnid_edit_project($nid,$translation)){
                return 1;
            }
        }    
    }
    return 0;
}
function publico_panel_admin_is_tnid_edit_project($nid,$param){
    $node=node_load($param);
    if(isset($node->tnid) && !empty($node->tnid) && $node->tnid==$nid){
        return 1;
    }
    return 0;
}
function publico_panel_admin_is_edit_about(){
    return publico_panel_admin_is_edit_project('about');
}
function publico_panel_admin_is_aviso_legal(){
    return publico_panel_admin_is_edit_project('aviso_legal');
}
function publico_panel_admin_is_texto(){
    return publico_panel_admin_is_edit_project('texto');
}
function publico_panel_admin_is_boletin_texto(){
    return publico_panel_admin_is_edit_project('boletin_texto');
}
function publico_panel_admin_is_edit_logo(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='admin'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='build'){
            $param2=arg(2);
            if(!empty($param2) && $param2=='themes'){
                $param3=arg(3);
                if(!empty($param3) && $param3=='settings'){
                    $param4=arg(4);
                    if(!empty($param4) && $param4=='buho'){
                        return 1;
                    }
                }
            }
        }
    }        
    return 0;    
}    