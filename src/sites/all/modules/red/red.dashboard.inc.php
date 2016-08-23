<?php
function red_dashboard_is_activado(){
    if(module_exists('custom_dashboard')){
        //if(red_despacho_is_activado()){
            return 1;
        //}
    }
    return 0;
}
function red_dashboard_is_despacho_no_dashboard(){
    if(red_dashboard_is_activado()){
        return 0;
    }
    if(red_despacho_is_activado()){
        return 1;
    }
    return 0;
}
function red_dashboard_is_searches(){
    if(red_dashboard_is_activado()){
        return custom_dashboard_is_searches();
    }
    return 0;
}
function red_dashboard_get_content_html(){
    if(red_dashboard_is_searches()){
        return custom_dashboard_get_content_html();
    }
    return '';
}
function red_dashboard_get_url_solr_search($canal_busqueda){
    return hontza_canal_rss_is_canal_busqueda_solr('',$canal_busqueda,1);
}
function red_dashboard_get_hasi_orri($my_action_in){
    $result=$my_action_in;
    if(red_dashboard_is_activado()){
        $result='/mi-grupo';
    }
    return $result;
}
function red_dashboard_add_node_links($node_in,$teaser = FALSE, $page = FALSE, $links = TRUE){
  $node=$node_in;  
  $node = node_build_content($node, $teaser, $page);
  if ($links) {
    $node->links = module_invoke_all('link', 'node', $node, $teaser);
    drupal_alter('link', $node->links, $node);
  }
  
  return $node;
}  