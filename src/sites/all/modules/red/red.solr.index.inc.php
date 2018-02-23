<?php 
function red_solr_index_inc_is_before(){
	/*if(defined('_IS_SOLR_INDEX_BEFORE') && _IS_SOLR_INDEX_BEFORE==1){
		//if(hontza_solr_my_access()){
			return 1;
		//}
	}	
	return 0;*/
	return 1;
}
function red_solr_index_before_callback(){
	$info_url=red_solr_index_get_advandec_search_link_info();		
	$status=red_solr_inc_get_index_status();
    if(isset($status['remaining']) && !empty($status['remaining'])){
		drupal_goto('red/solr/index/before/remaining');		
	}else{
		drupal_goto($info_url['url'],$info_url['query_busqueda_avanzada_solr']);    	
	}		
}	
function red_solr_index_before_remaining_callback(){
	global $base_url;
	$info_url=red_solr_index_get_advandec_search_link_info();		
	$status=red_solr_inc_get_index_status();
    if(isset($status['remaining']) && !empty($status['remaining'])){
    	/*drupal_goto('red/solr/index/remaining/advanced_link','destination='.url($base_url.'/'.$info_url['url'],array('absolute'=>TRUE,'query'=>$info_url['query_busqueda_avanzada_solr'])));*/
		drupal_goto('red/solr/index/remaining/advanced_link','destination='.urlencode($info_url['url'].'?'.$info_url['query_busqueda_avanzada_solr']));		
	}else{
		drupal_goto($info_url['url'],$info_url['query_busqueda_avanzada_solr']);    	
	}	
}
function red_solr_index_get_advandec_search_link_info(){
	$result=array();
	$my_grupo=og_get_group_context();
	    $my_grupo_nid='';
	    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
	        $my_grupo_nid=$my_grupo->nid;        
	    }
	    $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
 	$result['url']='hontza_solr/busqueda_avanzada_solr';
 	$result['query_busqueda_avanzada_solr']=$query_busqueda_avanzada_solr;
 	return $result;   	
}
function red_solr_index_inc_apachesolr_index_action_form_remaining_confirm_form_alter(&$form,&$form_state,$form_id){
	/*$msg=red_solr_index_inc_get_index_status_msg();
	//$form['description']['#value']=$msg.$form['description']['#value'];
	$form['description']['#value']=$msg;
	$info_url=red_solr_index_get_advandec_search_link_info();
    $form['actions']['cancel']['#value']=l('Skip to Advanced Search',$info_url['url'],array('query'=>$info_url['query_busqueda_avanzada_solr']));*/
    drupal_set_title(t('Indexing'));
    //$form['description']['#value']='<p>'.t('Submitting content to Solr...').'</p>';
    $form['description']['#value']='';
    $div_display_none='<div style="display:none">';
    $form['actions']['submit']['#prefix']=$div_display_none;
    $form['actions']['submit']['#suffix']='</div>';
    $form['actions']['cancel']['#prefix']=$div_display_none;
    $form['actions']['cancel']['#suffix']='</div>';
    $percentage=0;
    //$percentage='';
    //$message=t('Submitting content to Solr...');
    $message=t('Preparing to submit content to Solr for indexing...');
    $output = theme('progress_bar', $percentage, $message);
    $form['description']['#value'].=$output;
    red_solr_index_inc_add_apachesolr_index_action_form_remaining_confirm_form_alter_js();    
}    
function red_solr_index_inc_get_index_status_msg(){
	$html=array();
	$status=red_solr_inc_get_index_status();
	if(!isset($status['remaining'])){
		$status['remaining']='';
	}	
	 $html[]='<p style="color:red;">'.t('Results may be inaccurate. There are @status_remaining news waiting to be indexed',array('@status_remaining'=>$status['remaining'])).'</p>';
	return implode('',$html); 
}
function red_solr_index_inc_add_apachesolr_index_action_form_remaining_confirm_form_alter_js(){
    $js='$(document).ready(function()
			{
			   $("#edit-submit").click();
			});';
			
			drupal_add_js($js,'inline');
}          