<?php
function oferta_demanda_area_tecnologica_get_areas_tecnologicas_title(){
    return t('Technological Areas');
}
function oferta_demanda_area_tecnologica_get_areas_tecnologicas_block_content(){
    $html=array();
    $add_label=t('Add Technological Area');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'oferta_demanda/area_tecnologica/add/term',array('query'=>'destination=oferta_demanda/areas_tecnologicas','html'=>true));  
    $title=oferta_demanda_area_tecnologica_get_areas_tecnologicas_title();
    $html[]=l($title,'oferta_demanda/areas_tecnologicas');
    return implode('<BR>',$html);
}
function oferta_demanda_area_tecnologica_areas_tecnologicas_callback(){    
    $vocabulary=oferta_demanda_area_tecnologica_vocabulary_load();
    $result=drupal_get_form('taxonomy_overview_terms',$vocabulary);
    drupal_set_title(oferta_demanda_area_tecnologica_get_areas_tecnologicas_title());
    return $result;
}
function oferta_demanda_area_tecnologica_vocabulary_load(){
    $vid=27;
    $vocabulary=taxonomy_vocabulary_load($vid);
    return $vocabulary;
}
function oferta_demanda_area_tecnologica_edit_callback(){
    return oferta_demanda_tipo_organizacion_edit_callback();
}
function oferta_demanda_area_tecnologica_delete_callback(){
    return oferta_demanda_tipo_organizacion_delete_callback();
}
function oferta_demanda_area_tecnologica_add_term_callback(){
  $vocabulary=oferta_demanda_area_tecnologica_vocabulary_load();  
  taxonomy_my_set_add_term_page_title($vocabulary);
  return drupal_get_form('oferta_demanda_tipo_organizacion_taxonomy_form_term' , $vocabulary); 
}
function oferta_demanda_area_tecnologica_areas_tecnologicas_temp_callback(){
    drupal_goto('oferta_demanda/areas_tecnologicas');
}