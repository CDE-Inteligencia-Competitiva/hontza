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
	$is_mcapi=0;
	if(red_informatica_is_mcapi_my_send_mail()){
		if(isset($row->mailchimp_list_id) && !empty($row->mailchimp_list_id)){
			$is_mcapi=1;
	    	$mail_array=array();
	    	//echo print_r($row,1);
	    	//exit();
	    	//$is_recibir=$row->is_recibir;
	    	$is_recibir=1;
	        boletin_report_mcapi_my_send_mail_array($mail_array,$subject, $content,$row->send_method,$is_recibir,0,0,$row,$por_correo);
	    }    
    }
    if(!$is_mcapi){
    	my_send_mail($user_mail,$subject, $content,$row->send_method,$row->is_recibir);
    }	
}
function red_informatica_boletin_grupo_save_mailchimp_fields($my_id,$values){
	if(red_informatica_is_informatica_activado()){
		informatica_boletin_grupo_save_mailchimp_fields($my_id,$values);
	}	
}
function red_informatica_apply_alchemy_api_by_feeds_node_item($s,$link,$my_grupo_in){
	if(red_informatica_is_informatica_activado()){
		  require_once($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemyapi_php-master/alchemyapi.php');
		  $api_key=get_grupo_alchemy_api_key('','field_alchemy_key',$my_grupo_in);
		  $api_key='e2b646a51e2179f45981e6e110b4162c';
		  $api_key='530808b2544c10e56d0fc19812dca65653f0e90a';
		  $alchemyapi = new AlchemyAPI($api_key);
		  $response = $alchemyapi->text('url', $link, null);
		  print 'link='.$link;
		  echo 'response='.print_r($response,1);
		  	exit();		
		  if ($response['status'] == 'OK') {
		  	echo print_r($response,1);
		  	exit();
		  }  
	}
	return $s;	
}