<?php
function boletin_report_mcapi_my_send_mail($mail_to_in,$subject,$body_in,$send_method,$is_recibir,$historico_nid='',$is_mail_externo=0,$br_in='') {
    $is_mcapi=0;
    if(boletin_report_mcapi_is_activado()){
        $is_mcapi=1;
    }
    $body=my_send_mail($mail_to_in,$subject,$body_in,$send_method,$is_recibir,$historico_nid,$is_mail_externo,$br_in,$is_mcapi);
    /*if($is_mcapi){
        hontza_mcapi_my_send_mail($mail_to_in,$subject,$body,$send_method,$is_recibir,$historico_nid,$is_mail_externo,$br_in);
    }*/
}
function boletin_report_mcapi_is_activado(){
    if(module_exists('hontza_mcapi')){
        return 1;
    }
    return 0;
}
function boletin_report_mcapi_my_send_mail_array($mail_array,$subject,$body_in,$send_method,$is_recibir,$historico_nid='',$is_mail_externo=0,$br_in='',$por_correo=0,$is_forward=0){
    if($por_correo){
        $body=$body_in;
        $is_mcapi=0;
        if(boletin_report_mcapi_is_activado()){
            $is_mcapi=1;
            $mail_to_in=$mail_array[0];
            if(!$is_forward){
                $body=my_send_mail($mail_to_in,$subject,$body,$send_method,$is_recibir,$historico_nid,$is_mail_externo,$br_in,$is_mcapi);
            }
            if($is_mcapi){
                hontza_mcapi_my_send_mail($mail_to_in,$subject,$body,$send_method,$is_recibir,$historico_nid,$is_mail_externo,$br_in);
            }
        }
    }    
}
function boletin_report_mcapi_add_alerta_settings_block_links(&$html){
    if(boletin_report_mcapi_is_activado()){
        hontza_mcapi_add_alerta_settings_block_links($html);
    }
}
function boletin_report_mcapi_add_boletin_report_form_fields($row,&$form){
    if(boletin_report_mcapi_is_activado()){
        hontza_mcapi_add_boletin_report_form_fields($row,$form);
    }
}
function boletin_report_mcapi_save_mailchimp_list_id($id,$values){
    if(boletin_report_mcapi_is_activado()){
        hontza_mcapi_save_mailchimp_list_id($id,$values);
    }
}
function boletin_report_mcapi_get_forward_user_mail_array($user_mail_array_in){
    if(boletin_report_mcapi_is_activado()){
        return hontza_mcapi_get_forward_user_mail_array($user_mail_array_in);
    }
    return $user_mail_array_in;
}
function boletin_report_mcapi_add_boletin_report_forward_form_fields($id,&$form){
    if(boletin_report_mcapi_is_activado()){
        hontza_mcapi_add_boletin_report_forward_form_fields($id,$form);
    }
}
function boletin_report_mcapi_save_mailchimp_fields($id,$values){
    if(boletin_report_mcapi_is_activado()){
        hontza_mcapi_save_mailchimp_fields($id,$values);
    }
}