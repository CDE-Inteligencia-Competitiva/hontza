<?php
function oferta_demanda_rol_get_roles_title(){
    return t('Roles');
}
function oferta_demanda_rol_get_roles_block_content(){
    $html=array();
    $add_label=t('Add Role');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'oferta_demanda/rol/add/term',array('query'=>'destination=oferta_demanda/roles','html'=>true));  
    $title=oferta_demanda_rol_get_roles_title();
    $html[]=l($title,'oferta_demanda/roles');
    return implode('<BR>',$html);
}
function oferta_demanda_rol_roles_callback(){
    $vocabulary=oferta_demanda_rol_vocabulary_load();
    $result=drupal_get_form('taxonomy_overview_terms',$vocabulary);
    drupal_set_title(oferta_demanda_rol_get_roles_title());    
    return $result;
}
function oferta_demanda_rol_vocabulary_load(){
    $vid=26;
    $vocabulary=taxonomy_vocabulary_load($vid);
    return $vocabulary;
}
function oferta_demanda_rol_edit_callback(){
    return oferta_demanda_tipo_organizacion_edit_callback();
}
function oferta_demanda_rol_delete_callback(){
    return oferta_demanda_tipo_organizacion_delete_callback();
}
function oferta_demanda_rol_add_term_callback(){
  $vocabulary=oferta_demanda_rol_vocabulary_load();  
  taxonomy_my_set_add_term_page_title($vocabulary);
  return drupal_get_form('oferta_demanda_tipo_organizacion_taxonomy_form_term' , $vocabulary); 
}
function oferta_demanda_rol_roles_temp_callback(){
    drupal_goto('oferta_demanda/roles');
}