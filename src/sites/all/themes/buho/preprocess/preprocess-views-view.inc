<?php
// $Id$

/**
 * @file
 * Passes variables to the views templates.
 * @see theme_preprocess_views_view(), templates/overrides/views-view.tpl.php
 *
 * @return $vars
 */

// Prepare the variables that will store the attributes while being processed.
$vars['views_attributes'] = array();

// These are the styles views uses by default.
$vars['views_attributes']['class'][] = 'view';
$vars['views_attributes']['class'][] = 'view-'. $vars['css_name'];
$vars['views_attributes']['class'][] = 'view-id-'. $vars['name'];
$vars['views_attributes']['class'][] = 'view-display-id-'. $vars['display_id'];
$vars['views_attributes']['class'][] = 'view-dom-id-'. $vars['dom_id'];

// Add a class for the display plugin being used.
$vars['views_attributes']['class'][] =  $vars['view']->display['$display_id']['display_plugin'];

// Add support for the Skinr module
$vars['views_attributes']['class'][] = $vars['skinr'];

// Crunch all attributes into a single string to be added to the block container.
$vars['views_attributes'] = theme('render_attributes', $vars['views_attributes']);

//gemini-2014
$current_display=$vars['view']->current_display;
        
//gemini
if(in_array($vars['view']->name,array('og_fuentes_pipes','og_fuentes_calidad','og_fuentes_exahustivo','og_fuentes_actualizacion'))){
	$vars['rows']=my_get_rows($vars);
}else if(strcmp($vars['view']->name,'og_home_noticiasvalidadas_dash')==0){
	$vars['rows']=my_get_rows_validadas_dash($vars);
}else if(strcmp($vars['view']->name,'home_publica')==0){
	//intelsat-2015        
        //$vars['rows']=my_get_rows_home_publica($vars);
        $vars['rows']='';
        //intelsat-2016
        red_crear_usuario_node_page_default_drupal_goto();
        //
}else if(strcmp($vars['view']->name,'gestion_servicios')==0){
	$vars['rows']=my_get_rows_gestion_servicios($vars);
}else if(strcmp($vars['view']->name,'og_canales_por_categorias')==0){
	$vars['rows']=my_get_rows_canales_por_categorias($vars);
}else if(strcmp($vars['view']->name,'mis_contenidos_v1')==0){
	$vars['rows']=my_get_rows_mis_contenidos_v1($vars);
}else if(strcmp($vars['view']->name,'og_home_areadebate')==0){
	$vars['rows']=my_get_rows_home_areadebate_block($vars);
}else if(strcmp($vars['view']->name,'gestion_items')==0){
	$vars['rows']=my_get_rows_gestion_items($vars);
}else if(strcmp($vars['view']->name,'gestion_canales')==0){
	$vars['rows']=my_get_rows_gestion_canales($vars);
}else if(strcmp($vars['view']->name,'og_canales_dash')==0){
  $vars['rows']=my_get_rows_canales_dash($vars);
//gemini-2014        
}else if(strcmp($vars['view']->name,'og_area_trabajo')==0){
    $vars['rows']=hontza_set_block_og_area_trabajo($vars);
    if(hontza_is_view_block($current_display)){
        $vars['rows']=red_funciones_add_create_wiki_by_block($vars);
    }    
}else if(strcmp($vars['view']->name,'og_canales')==0){
   if(hontza_is_view_block($current_display)){
       $vars['rows']=hontza_set_og_canales_noticias_de_usuario($vars);
       //intelsat-2015
       $vars['rows']=hontza_solr_search_set_og_canales_rss_link($vars);
   }
}else if(strcmp($vars['view']->name,'og_canal_aportaciones_usuarios')==0){
   $vars['rows']=hontza_set_page_og_canal_aportaciones_usuarios($vars);
}else if(strcmp($vars['view']->name,'og_members')==0){
   $vars['rows']=hontza_grupos_mi_grupo_get_rows_og_members($vars);
}else if(strcmp($vars['view']->name,'og_members_faces')==0){
   $vars['rows']=hontza_grupos_mi_grupo_get_rows_og_members_faces($vars);
}else if(strcmp($vars['view']->name,'og_area_debate_my_block')==0){
   if(hontza_is_view_block($current_display)){
        $vars['rows']=red_funciones_set_og_area_debate_my_block($vars);
   }
}else if(strcmp($vars['view']->name,'og_home_areadetrabajo')==0){
   $vars['rows']=hontza_scr_get_rows_home_areadetrabajo_block($vars);
//intelsat-2015   
}else if($vars['view']->name=='og_canales_busqueda'){
   if(hontza_is_view_block($current_display)){
       $vars['rows']=hontza_solr_search_set_og_canales_busqueda_my_block($vars);
   }
}else if($vars['view']->name=='gestion_grupos'){
   if(!hontza_is_view_block($current_display)){
       $vars['rows']=hontza_canal_rss_my_get_rows_gestion_grupos($vars);
   }
}else if($vars['view']->name=='gestion_usuarios_grupos'){
   if(!hontza_is_view_block($current_display)){
       $vars['rows']=hontza_canal_rss_my_get_rows_gestion_usuarios_grupos($vars);
   }
}else if($vars['view']->name=='gestion_ayuda'){
   if(!hontza_is_view_block($current_display)){
       $vars['rows']=hontza_canal_rss_my_get_rows_gestion_ayuda($vars);
   }
}else if($vars['view']->name=='gestion_noticias'){
   $vars['rows']=hontza_canal_rss_my_get_rows_gestion_noticias($vars);
}else if($vars['view']->name=='gestion_area_trabajo_wiki'){
   $vars['rows']=hontza_canal_rss_my_get_rows_gestion_area_trabajo_wiki($vars);
}else if($vars['view']->name=='gestion_area_debate'){
   $vars['rows']=hontza_canal_rss_my_get_rows_gestion_area_debate($vars);
}else if($vars['view']->name=='gestion_ayuda_popup'){
   $vars['rows']=hontza_canal_rss_my_get_rows_gestion_ayuda_popup($vars);
}