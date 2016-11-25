<?php
function red_informatica_is_informatica_activado(){
	if(module_exists('informatica')){
		if(informatica_is_informatica_activado()){
			return 1;
		}
	}	
	return 0;
}
function red_informatica_mcapi_add_boletin_report_form_fields($row,&$form){
	if(red_informatica_is_informatica_activado()){
		boletin_report_mcapi_add_boletin_report_form_fields($row,$form);
	}
}
function red_informatica_alerta_save_mailchimp_fields($my_id,$values){
	if(red_informatica_is_informatica_activado()){
		informatica_alerta_save_mailchimp_fields($my_id,$values);
	}	
}
function red_informatica_is_mcapi_my_send_mail(){
	if(red_informatica_is_informatica_activado()){
		if(boletin_report_mcapi_is_activado()){
			return 1;
		}	
	}
	return 0;	
}
function red_informatica_my_send_mail($user_mail,$subject,$content,$row,$por_correo){
	if(red_informatica_is_mcapi_my_send_mail()){
    	$mail_array=array();
    	//echo print_r($row,1);
    	//exit();
        boletin_report_mcapi_my_send_mail_array($mail_array,$subject, $content,$row->send_method,$row->is_recibir,0,0,$row,$por_correo);
    }else{
    	my_send_mail($user_mail,$subject, $content,$row->send_method,$row->is_recibir);
    }
}