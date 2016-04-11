<?php
function hontza_canal_json_is_activado(){
    if(defined('_IS_CANAL_JSON')){
        if(_IS_CANAL_JSON){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_json_is_canal_json($source,&$json_fields){
    $json_fields='';
    if(hontza_canal_json_is_activado()){
        return canal_canal_json_is_canal_json($source,$json_fields);
    }    
    return 0;
}
function hontza_canal_json_is_pantalla(){
    if(hontza_canal_json_is_activado()){
        if(canal_json_is_pantalla()){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_json_canal_de_yql_node_form_alter(&$form,&$form_state, $form_id){
    if(hontza_canal_json_is_activado()){
        canal_json_canal_de_yql_node_form_alter($form,$form_state, $form_id);
    }
}
function hontza_canal_json_is_csv($url_json){
    if(hontza_canal_json_is_activado()){
        return canal_json_is_csv($url_json);
    }
    return 0;
}
function hontza_canal_json_decode_json_fields($json_fields){
    if(hontza_canal_json_is_activado()){
        return canal_json_decode_json_fields($json_fields);
    }
    return '';    
}