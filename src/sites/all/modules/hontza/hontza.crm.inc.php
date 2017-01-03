<?php
function hontza_crm_inc_is_crm_exportar_noticias(){
    if(hontza_crm_is_activado()){
        return crm_exportar_is_exportar_noticias();
    }
    return 0;
}
function hontza_crm_is_activado(){
    if(module_exists('crm_exportar')){
        return 1;
    }
    return 0;
}
function hontza_crm_inc_get_tags_vid(){
    $vid=3;
    return $vid;
}
function hontza_crm_inc_node_form_alter(&$form,&$form_state, $form_id){
    if(hontza_crm_is_activado()){
        crm_exportar_node_form_alter($form,$form_state, $form_id);
    }
}
function hontza_crm_inc_get_canal_link_type_options(){
    return boletin_report_get_tipo_link_options();
}
function hontza_crm_inc_canal_node_form_alter(&$form,&$form_state, $form_id){
    if(hontza_crm_is_activado()){
        crm_exportar_canal_node_form_alter($form,$form_state, $form_id);
    }
}
function hontza_crm_validate_date($date, $format = 'Y-m-d H:i:s')
{
    $version = explode('.', phpversion());
    if (((int) $version[0] >= 5 && (int) $version[1] >= 2 && (int) $version[2] > 17)) {
        $d = DateTime::createFromFormat($format, $date);
    } else {
        $d = new DateTime(date($format, strtotime($date)));
    }
    return $d && $d->format($format) == $date;    
}
function hontza_crm_add_cero($value, $num = 2)
{
    return str_pad($value, $num, '0', STR_PAD_LEFT);
}
function hontza_crm_get_item_canal($node){
    if(isset($node->field_item_canal_reference) && isset($node->field_item_canal_reference[0]) && isset($node->field_item_canal_reference[0]['nid'])){
        $result=node_load($node->field_item_canal_reference[0]['nid']);
        return $result;
    }
    $my_result=new stdClass();
    return $my_result;
}    
function hontza_crm_link_type_action_class($node,$is_view=0){
    if(hontza_crm_is_activado()){
        return crm_exportar_link_type_action_class($node,$is_view);
    }
    return '';
}
function hontza_crm_web_platform_type_link($node){
    if(hontza_crm_is_activado()){
        return crm_exportar_web_platform_type_link($node);
    }
    return '';
}
function hontza_crm_inc_change_link_type_add_js(){
    if(hontza_crm_is_activado()){
        crm_exportar_change_link_type_add_js();
    }
}
function hontza_crm_is_show_link_type_action($node=''){
    if(hontza_crm_is_activado()){
        return crm_exportar_is_show_link_type_action($node);
    }
    return 0;
}
function hontza_crm_inc_is_busqueda_solr_publico(){
    $param1=arg(1);
    /*if(!empty($param1) && $param1=='textos_exportar_todas_noticias_automatic_tags'){
        return  1;
    }*/
    if(!empty($param1) && $param1=='textos_exportar_todas_noticias'){
        return  1;
    }
    return 0;
}