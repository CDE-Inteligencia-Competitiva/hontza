<?php
/**
 * help.php - Displays help page.
 *
 */

require_once '/var/www/hontza3/includes/bootstrap.inc';

// Get URL parameters.
$nid   = $_GET['nid'];

// Load drupal and initialize theme so that the popup takes the site theme
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
theme();

// We put the css and content into a page.tpl template
$css = drupal_add_css();
$query_string = '?' . substr(variable_get('css_js_query_string', '0'), 0, 1);
foreach ($css as $media => $types) {
  foreach ($types as $type => $files) {
    foreach ($types[$type] as $file => $preprocess) {
      // Only include the stylesheet if it is a theme stylesheet.
      if ($type == "theme" && file_exists($file)) {
        $styles .= '<link type="text/css" rel="stylesheet" media="' . $media . '" href="' . base_path() . $file . $query_string . '" />' . "\n";
      }
    }
  }
}
  


//intelsat-2014
$is_online=0;
if(isset($_GET['is_online']) && !empty($_GET['is_online'])){
    if(!hontza_is_sareko_id('ROOT')){    
        $is_online=1;
        //db_set_active('online');
    }    
}
//

//intelsat-2014
if($nid=='save_current_search'){
  $node=new stdClass();
  $node->nid='save_current_search';
  $node->title=t('Save current search');
  $node->body=drupal_get_form('hontza_solr_save_search_form');
  $helpfound = TRUE;
}else if($nid=='guardar_resultado_solr'){
  $node=new stdClass();
  $node->nid='guardar_resultado_solr';
  $node->title='Save search result';
  $node->body=drupal_get_form('hontza_solr_funciones_guardar_resultado_solr_form');
  $helpfound = TRUE;  
//intelsat-2015
}else if($nid=='save_current_rss'){
  $node=new stdClass();
  $node->nid='save_current_rss';
  $node->title=t('Please save your current search to Generate RSS');
  $node->body=drupal_get_form('hontza_solr_funciones_save_rss_form');
  $helpfound = TRUE;
}else if($nid=='tag_bookmark_multiple_mode'){  
  $node=new stdClass();
  $node->nid='tag_bookmark_multiple_mode';
  $node->title=t('Tag');
  $node->body=hontza_solr_search_get_tag_popup_html();
  $helpfound = TRUE;
}else if($nid=='send_message_popup'){
  $node=new stdClass();
  $node->nid='send_message_popup';
  //$node->title=t('Please save your current search to Generate RSS');
  $node->body=drupal_get_form('hontza_grupos_mi_grupo_contact_form',$_REQUEST['uid'],$_REQUEST['destination_send_message_popup']);
  $helpfound = TRUE;
//intelsat-2015  
}else if($nid=='red_exportar_rss_enviar_mail'){
  $node=new stdClass();
  $node->nid='red_exportar_rss_enviar_mail';
  //$node->title=t('Send RSS email');
  $node->title='<b>'.t('Channel exportation').'</b>';
  $node->body=red_exportar_rss_enviar_mail_canales_rss();
  $helpfound = TRUE;
}else if($nid=='compartir_documentos'){  
  $node=new stdClass();
  $node->nid='compartir_documentos';
  $node->title=red_copiar_get_popup_title($_REQUEST['compartir_documentos_nid']);
  $node->body=drupal_get_form('compartir_documentos_copiar_enviar_mail_form',$_REQUEST['compartir_documentos_nid'],$_REQUEST['my_grupo_nid']);
  $helpfound = TRUE;
}else if ($nid > 0) {
  if($is_online){
    $node=help_popup_remote_node_load($nid);
  }else{
    $node = node_load($nid);  
  }  
  $helpfound = TRUE;
}

//intelsat-2014
/*if($is_online){
    db_set_active();
}*/
//

// Display an error if necessary.
if (!$helpfound) {
  $node->title = t('Error');
  $node->body = t('Help file could not be found!');
}
//  These might be useful in the template
$node->height = $_GET['h'];
$node->width = $_GET['w'];

if ($_GET['type'] == "standard"){
    print theme('help_popup', $styles, $node);
}else{
    print theme('help_popup_js', $node);
}
/////////////////////////////////
function help_popup_remote_node_load($nid){
  global $language;
  /*$my_lang='';
  if($language->language!='en'){
    $my_lang=$language->language.'/';
  }
  $url='http://online.hontza.es/'.$my_lang.'panel_admin/ayuda_popup/get_help_popup_node/'.$nid;*/
  $url='http://online.hontza.es/panel_admin/ayuda_popup/get_help_popup_node/'.$nid.'/'.$language->language;
  //print $url;exit();
  $content=file_get_contents($url);
  $node=json_decode($content);
  return $node;
}