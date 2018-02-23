<?php
function movil_solr_busqueda_simple_form(){
  $form=array();
  $form['busqueda_simple_txt']=array(
      '#type'=>'textfield',
      '#title'=>'Search',
  );
  $form['busqueda_simple_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Search'),
      '#name'=>'busqueda_simple_btn',
  );
  return $form;
}
function movil_solr_busqueda_simple_form_submit($form, &$form_state){    
  if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button']) && $form_state['clicked_button']['#name']=='busqueda_simple_btn'){      
    $url='movil/solr/busqueda_simple_resultados/'.$form_state['values']['busqueda_simple_txt'];
    $url=movil_add_grupo_lang_url($url);
    drupal_goto($url);
  }  
}
function movil_solr_busqueda_simple_resultados_callback(){
    $text=arg(3);
    $output='';
    red_solr_inc_get_busqueda_simple_url($text,$url,$query,1);
    if(empty($query)){
        //drupal_goto($url);
        $url=url($url);
    }else{
        //drupal_goto($url,$query);
        $url=url($url,array('query'=>$query));
    }    
    $result=alerta_solr_get_canal_busqueda_nid_array('',$url);
    $output=movil_vigilancia_nodes_html($result,$output);
    return $output;
}