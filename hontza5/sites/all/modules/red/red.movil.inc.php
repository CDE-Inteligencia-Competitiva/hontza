<?php
function red_movil_inicio_callback(){
    $url='node';
    if(hontza_canal_rss_is_visualizador_activado()){
        $url='red/visualizador/frontpage';        
    }else if(red_crear_usuario_is_activado()){
        $url='crear_usuario/frontpage';
    }
    if(red_movil_is_movil()){
        $url=movil_get_inicio_url();                   
    }
    drupal_goto($url);
}
function red_movil_get_current_theme(){
    $result=basename(path_to_theme());
    if($result!='fusion_mobile'){
        if(defined('_CUSTOM_CURRENT_THEME')){
            $result=_CUSTOM_CURRENT_THEME;
        }
    }
    return $result;
}
function red_movil_is_movil(){
    $current_theme=red_movil_get_current_theme();
    if($current_theme=='fusion_mobile'){
        return 1;
    }
    return 0;
}
function red_movil_is_show_menu_primary_local_tasks(){
    if(red_movil_is_activado()){
        return movil_is_show_menu_primary_local_tasks();
    }
    return 1;
}
function red_movil_is_activado(){
    if(red_movil_is_movil()){        
        if(module_exists('movil')){
            return 1;
        }
    }
    return 0;
}
function red_movil_is_pantalla_login(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='user'){
        $param1=arg(1);
        if(empty($param1)){
            if(hontza_is_user_anonimo()){
                return 1;
            }
        }
    }
    return 0;
}
function red_movil_is_show_breadcrumb(){
    if(red_movil_is_activado()){
        return movil_is_show_breadcrumb();
    }
    return 1;
}
function red_movil_get_site_name($site_name){
    $result=$site_name;
    if(red_movil_is_activado()){
        return movil_get_site_name($site_name);
    }
    return $result;
}
function red_movil_get_title($title){
    $result=$title;
    if(red_movil_is_activado()){
        return movil_get_title($title);
    }
    return $result;
}
function red_movil_get_logo($logo){
    $result=$logo;
    if(red_movil_is_activado()){
        return movil_get_logo($logo);
    }
    return $result;
}
function red_movil_get_user_menus(){
    if(red_movil_is_activado()){
        return movil_get_user_menus();
    }
    return '';
}
function red_movil_add_js(&$vars){
    if(red_movil_is_activado()){
        movil_add_js($vars);
    }
}
function red_movil_get_item_resumen($node){
    if(red_movil_is_activado()){
        return movil_get_item_resumen($node);
    }
    return '';
}
function red_movil_is_language_default(){
    if(defined('_IS_MOVIL_LANGUAGE_DEFAULT') && _IS_MOVIL_LANGUAGE_DEFAULT==1){
        return 1;
    }
    return 0;
}
function red_movil_set_movil_inicio_language($my_lang_in,$url){
    global $base_url;
    $my_lang=$my_lang_in;
    if($my_lang=='en'){
        $my_lang='';
    }else{    
        $my_lang='/'.$my_lang;
    }
    $result=$base_url.$my_lang.$url;
    return $result;
}
function red_movil_get_language_array($field = 'language', $reset = FALSE){
  static $languages = NULL;

  // Reset language list
  if ($reset) {
    $languages = NULL;
  }

  // Init language list
  if (!isset($languages)) {
    if (variable_get('language_count', 1) > 1 || module_exists('locale')) {
      $result = db_query('SELECT * FROM {languages} ORDER BY native ASC');
      while ($row = db_fetch_object($result)) {
        $languages['language'][$row->language] = $row;
      }
    }
    else {
      // No locale module, so use the default language only.
      $default = language_default();
      $languages['language'][$default->language] = $default;
    }
  }

  // Return the array indexed by the right field
  if (!isset($languages[$field])) {
    $languages[$field] = array();
    foreach ($languages['language'] as $lang) {
      // Some values should be collected into an array
      if (in_array($field, array('enabled', 'weight'))) {
        $languages[$field][$lang->$field][$lang->language] = $lang;
      }
      else {
        $languages[$field][$lang->$field] = $lang;
      }
    }
  }
  return $languages[$field];
}
function red_movil_vigilancia_notica_node_form_alter(&$form,&$form_state,$form_id){
    if(red_movil_is_activado()){
        movil_vigilancia_notica_node_form_alter($form,$form_state,$form_id);
    }
}
function red_movil_is_wysiwyg_form_alter(&$form, &$form_state){
    if(red_movil_is_activado()){
        return movil_is_wysiwyg_form_alter($form,$form_state);
    }
    return 1;
}
function red_movil_taxonomy_form_alter(&$form, $form_state, $form_id){
    if(red_movil_is_activado()){
        movil_taxonomy_form_alter($form, $form_state, $form_id);
    }
}
function red_movil_get_grupo_nid($is_default=1){
        $my_grupo=og_get_group_context();
            $result=0;
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $result=$my_grupo->nid;
            }else{
                if($is_default){
                    $result=red_movil_get_default_grupo_nid();
                }
            }
    return $result;        
}
function red_movil_get_default_grupo_nid(){
    global $user;
    $result='';
    if(isset($user->og_groups) && !empty($user->og_groups)){
        $group_nid_array=array_keys($user->og_groups);
        if(isset($group_nid_array[0]) && !empty($group_nid_array[0])){
            $result=$group_nid_array[0];
        }
    }
    return $result;            
}                
function red_movil_menu_primary_local_tasks($level = 0, $return_root = FALSE) {
    if(red_movil_is_activado()){
        return movil_menu_primary_local_tasks($level,$return_root);
    }
    return menu_primary_local_tasks();
}
function red_movil_get_current_grupo_node(){
    $grupo_nid=red_movil_get_grupo_nid();
    $result=node_load($grupo_nid);
    return $result;
}
function red_movil_get_user_img_faktore($faktore){
    $result=$faktore;
    if(red_movil_is_activado()){
        $result=movil_get_user_img_faktore();
    }
    return $result;
}
function red_movil_item_web_link($node){
    $result='';
    $url='';
    if($node->type=='item'){
        $url=hontza_item_web_link($node,1);
    }else{
        $url=hontza_noticia_usuario_web_link($node,1);
    }
    $label=t('Web');
    $icono=my_get_icono_action('web',$label);
    $result=l($icono,$url,array('html'=>true));
    return $result;
}
function red_movil_item_comment_link($node){
    $result='';
    $url=hontza_item_comment_link($node,1);
    $label=t('Comment');
    $icono=my_get_icono_action('comment-add',$label);
    $result=l($icono,$url,array('html'=>true));
    return $result;
}
function red_movil_item_tag_link($node){
    $result='';
    /*if($node->type=='noticia'){
        $url=hontza_item_tag_link($node,1);       
    }else{*/
    $url=hontza_item_tag_link($node,1);
    //}
    $label=t('Tag');
    $icono=my_get_icono_action('tag',$label);
    $result=l($icono,$url,array('html'=>true));
    return $result;
}
function red_movil_get_footer_message_powered(){
    $html=array();
    $html[]='<div class="movil_powered">';
    $html[]='<div>'.t('Powered by @version',array('@version'=>'Hontza 5.0')).'</div>';
    if(red_is_subdominio_proyecto_alerta()){
        //$html[]='<div>'.t('Financed by').' Fondo Social Europeo</div>';
        $html[]=red_funciones_alerta_financiado_por_html();
    }
    $html[]='</div>';    
    return implode('',$html);
}
function red_movil_comment_form_alter(&$form,&$form_state,$form_id){
    //$form['attachments']['#collapsed']=false;
    if(red_movil_is_activado()){
        movil_comment_form_alter($form,$form_state,$form_id);
    }
}
function red_movil_is_view_user_field($title){
    if(red_movil_is_activado()){
        return movil_is_view_user_field($title);
    }
    return 1;
}
function red_movil_get_comentario_link_icono($key){
  $icono='delete';  
  $title=t('Delete');
  $type='comentario_link';
  if($key=='comment_delete'){  
    $icono='delete';
    $title=t('Delete');  
  }else if($key=='comment_edit'){  
    $icono='edit_comentario';
    $title=t('Edit');    
  }else if($key=='comment_reply'){  
    $icono='edit_add';
    $title=t('Reply');  
  }
  if(red_movil_is_activado()){
    return movil_get_icono_action($icono,$title,$type);  
  }else{
    return my_get_icono_action($icono,$title,$type);
  }  
}
function red_movil_unset_form_field_form_alter(&$form,&$form_state,$form_id,$unset_array){
    if(!empty($unset_array)){
        foreach($unset_array as $i=>$field){
            if(isset($form[$field])){
                unset($form[$field]);
            }
        }
    }    
}
function red_movil_set_display_none_form_alter(&$form,&$form_state,$form_id,$unset_array){
    if(!empty($unset_array)){
        foreach($unset_array as $i=>$field){    
            if(isset($form[$field])){
                $form[$field]['#prefix']='<div class="div_display_none">';
                $form[$field]['#suffix']='</div>';
            }
        }    
    }    
}
function red_movil_item_edit_link($node){
    $result='';
    $url='node/'.$node->nid.'/edit';
    $label=t('Edit');
    $icono=my_get_icono_action('edit',$label);
    $result=l($icono,$url,array('html'=>true));
    return $result;
}
function red_movil_noticia_usuario_web_link($node){
    return red_movil_item_web_link($node);
}