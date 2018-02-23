<?php
// $Id$

/**
 * @file
 * The guts of the theme :)
 */

require_once('theme-settings.php');

/**
 * Add Custom Generated CSS File
 * This file is generated each time the theme settings page is loaded.
 */
$custom_css = file_directory_path() .'/buho/custom.css';
if (file_exists($custom_css)) {
  drupal_add_css($custom_css, 'theme', 'all', TRUE);
}

/**
 * Implementation of hook_theme().
 * This function provides a one-stop reference for all
 */
function buho_theme(&$existing, $type, $theme, $path) {
  return array(
    'breadcrumb' => array(
      'arguments' => array('breadcrumb' => array()),
      'file' => 'functions/theme-overrides.inc',
    ),
    'conditional_stylesheets' => array(
      'file' => 'functions/theme-custom.inc',
    ),
    'feed_icon' => array(
      'arguments' => array('url' => NULL, 'title' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'form_element' => array(
      'arguments' => array('element' => NULL, 'value' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'fieldset' => array(
      'arguments' => array('element' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'menu_local_tasks' => array(
      'arguments' => NULL,
      'file' => 'functions/theme-overrides.inc',
    ),
    'more_link' => array(
      'arguments' => array('url' => array(), 'title' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'pager' => array(
      'arguments' => array('tags' => array(), 'limit' => NULL, 'element' => NULL, 'parameters' => array(), 'quantity' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
   'status_messages' => array(
      'arguments' => array('display' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'status_report' => array(
      'arguments' => array('requirements' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'table' => array(
      'arguments' => array('header' => NULL, 'rows' => NULL, 'attributes' => array(), 'caption' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'render_attributes' => array(
      'arguments' => array('attributes'),
      'file' => 'functions/theme-custom.inc',
    ),
  );
}

/**
 * Implementation of hook_preprocess().
 *
 * @param $vars
 * @param $hook
 * @return Array
 */
function buho_preprocess(&$vars, $hook) {

  // Only add the admin.css file to administrative pages
  if (arg(0) == 'admin') {
    drupal_add_css(path_to_theme() .'/css/admin.css', 'theme', 'all', TRUE);
  }
 
 //gemini
 if($hook=='block'){
 	my_help_block($vars, $hook);
 }
 //
 /**
  * This function checks to see if a hook has a preprocess file associated with
  * it, and if so, loads it.
  */
  if (is_file(drupal_get_path('theme', 'buho') .'/preprocess/preprocess-'. str_replace('_', '-', $hook) .'.inc')) {
    include('preprocess/preprocess-'. str_replace('_', '-', $hook) .'.inc');
  }
}


/*Eliminar el node title en determinados tipos de contenidos*/


function buho_preprocess_page(&$vars) {

  /*itles are ignored by content type when they are not desired in the design.*/
  $vars['original_title'] = $vars['title'];
  if (!empty($vars['node']) && in_array($vars['node']->type, array('item', 'noticia'))) {
    $vars['title'] = '';
  }
  //gemini
  if (stripos($vars['title'],'icon_users.png')) {
    $vars['title'] = 'Users';
  }
  //
 
  //gemini
  //if(arg(0)=='admin' && arg(1)=='content' && arg(2)=='taxonomy' && arg(3)==1 && arg(4)==''){  	
  if(is_taxonomy_volver_link()){
    $vars['my_volver_link']='<div class="clearfix">';     
    $vars['my_volver_link'].=l(utf8_encode(t('Back to management panel')),'gestion',array('attributes'=>array('class'=>'back')));
	$vars['my_volver_link'].='</div>';
	//$vars['my_volver_link']=l(utf8_encode(t('Panel de gesti�n')),'gestion',array('attributes'=>array('class'=>'back')));
  }
  //
}
//gemini
function is_taxonomy_volver_link(){
	//arg(3)==1 categoria de la fuente bakarrra da
	if(arg(0)=='admin' && arg(1)=='content' && arg(2)=='taxonomy'){
		 $tid=arg(3);		 
		 //if(arg(3)==1){
		 if(is_categoria_de_la_fuente($tid,$my_term)){
			if(arg(4)==''){
				return 1;
			}
			if(arg(4)=='add' && arg(5)=='term'){
				return 1;
			}
		 }else if(arg(3)=='edit' && arg(4)=='term'){
		 	$tid=arg(5);
			//print 'term_id='.$term_id.'<BR>';
		 	if(is_categoria_de_la_fuente($tid,$my_term)){
				return 1;
			}
		 }					
	}
		
	return 0;  	
}
//gemini
function my_help_block(&$vars, $hook){
	if($hook=='block'){
	
		//print 'delta='.$vars['block']->delta.'<BR>';
		
		if(empty($vars['block']->subject)){
			return '';
		}		
		if(strcmp($vars['block']->delta,'og_canales-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2934);			
			return '';
		}
		if(strcmp($vars['block']->delta,'0')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2935);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_canales_busqueda-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2936);
			return '';			
		}
		if(strcmp($vars['block']->delta,'14')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2937);
			return '';			
		}
		if(strcmp($vars['block']->delta,'27')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2938);
			return '';			
		}
		if(strcmp($vars['block']->delta,'12')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2939);
			return '';			
		}
		if(strcmp($vars['block']->delta,'8')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2940);
			return '';			
		}
		if(strcmp($vars['block']->delta,'4')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2941);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_categorias_fuentes-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2942,2);
			return '';			
		}
		if(strcmp($vars['block']->delta,'13')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2943);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_area_trabajo-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2944);
			return '';			
		}
		if(strcmp($vars['block']->delta,'11')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2945);
			return '';			
		}
		if(strcmp($vars['block']->delta,'1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2946);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_usuarios-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2947);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_canales_dash-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2949);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'ca8c74ef9b29fefa6b202cd5cbf47fbb')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2950);
			return '';			
		}
		if(strcmp($vars['block']->delta,'d640a64f46f55241f1a9ed29d2536ef2')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2951);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'273bd4ba9d1b68191046f2e902e83370')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2952);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_home_faces-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2953);
			return '';			
		}
		if(strcmp($vars['block']->delta,'3')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2954);			
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_home_areadebate-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2955);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_home_areadetrabajo-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2956);
			return '';			
		}
		if(strcmp($vars['block']->delta,'21')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2957,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'25')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2958,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'16')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2959,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'20')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2960,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'7')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2961,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'17')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2962,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'18')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2963,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'22')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2964,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'30')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2965,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'15')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2966,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'19')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2967,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'24')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2968,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'23')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2969,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'31')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2970,3);
			return '';			
		}																																					
	}
}