<?php
function movil_vigilancia_callback(){
    $type=arg(2);
    $sql=movil_vigilancia_get_sql($type);
    $output = '';
    $rows=array();
  $grupo_nid=red_movil_get_grupo_nid(0);
  if(!empty($grupo_nid)){
    $res=db_query($sql);
    while ($row = db_fetch_object($res)) {
      $rows[]=$row->nid;
    }
  }
  $output=movil_vigilancia_nodes_html($rows,$output);
  $title=movil_vigilancia_get_title($type);
  drupal_set_title(t($title));
  //
  return $output; 
}
function movil_vigilancia_access(){
    if(!hontza_is_user_anonimo()){
        return TRUE;
    }
    return FALSE;
}
function movil_vigilancia_get_sql($type){
    $result='';
    if($type=='pendientes'){
        my_vigilancia_pendientes_pre_execute($view);
    }else if($type=='validados'){
        hontza_og_vigilancia_validados_pre_execute($view);
    }else if($type=='lo-mas-valorado'){    
        my_og_vigilancia_lo_mas_valorado($view);
    }else if($type=='lo-mas-comentado'){
        my_og_vigilancia_mascomentadas($view);
    }else if($type=='rechazados'){    
        hontza_og_vigilancia_rechazados_pre_execute($view);
    }else if($type=='bookmarks'){    
        $result=red_solr_inc_get_bookmarked_sql();
    }else{
        my_vigilancia_ultimas_pre_execute($view);
    }
    //print $view->build_info['query'];
    if(empty($result)){
        $result=$view->build_info['query'];
    }
    return $result;
}
function movil_vigilancia_set_node_view($rows){
    $result=array();
    if(!empty($rows)){
        $kont=0;
        foreach($rows as $i=>$nid){
            $result[$kont]=new stdClass();
            $node=node_load($nid);
            $result[$kont]->view=node_view($node,1);
            $kont++;
        }    
    }
    return $result;
}
function movil_vigilancia_notica_node_form_alter(&$form,&$form_state,$form_id){
    $nid=hontza_get_nid_by_form($form);
    $form['body_field']['teaser_include']['#prefix']='<div class="teaser-checkbox div_display_none">';
    $form['body_field']['format']['#prefix']='<div class="div_display_none">';
    $form['body_field']['format']['#suffix']='</div>';
    $unset_array=array('field_enlace_noticia','menu','path','author','options','menu','revision_information','my_cat_',
    'reto_al_que_responde_id','preview_changes');
    /*if(!empty($nid)){
        $unset_array[]='attachments';
    }*/
    red_movil_unset_form_field_form_alter($form,$form_state,$form_id,$unset_array);    
    //unset($form['buttons']['preview']);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);    
    $display_none_array=array('comment_settings','field_item_source_tid');
    red_movil_set_display_none_form_alter($form,$form_state,$form_id,$display_none_array);
    $form['attachments']['#collapsed']=false;    
}
function movil_vigilancia_seleccionar_vigilancia_callback(){
    global $base_url,$language;
    $html=array();
    $html[]='<div class="div_seleccionar_vigilancia">';
    $class=movil_get_btn_class();
    $my_grupo=red_movil_get_current_grupo_node();
    $grupo_value='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_value=$my_grupo->purl;
    }
    $my_lang='/';
    if($language->language!='en'){
        $my_lang='/'.$language->language.'/';
    }
    $vigilancia_array=movil_vigilancia_get_vigilancia_array();
    if(!empty($vigilancia_array)){
        foreach($vigilancia_array as $i=>$row){
            $class=movil_get_btn_class();
            /*if($row->nid==$my_grupo_nid){
                $class='btn_selected';
            }*/
            $url=$row['url'];
            $url=$base_url.$my_lang.$grupo_value.'/'.$url;
            $html[]='<div class="button-wrap-inner">'.l(t($row['title']),$url,array('attributes'=>array('class'=>$class))).'</div>';        
        }
    }
    $html[]='</div>';
    $output=implode('',$html);
    movil_vigilancia_add_seleccionar_vigilancia_js();
    return $output;
}
function movil_vigilancia_get_vigilancia_array(){
    $result=array();
    $row=array();
    //$row['title']=$title=t('Monitoring - News pending to be filtered');
    //$row['title']='News pending to be filtered';    
    $row['title']='Pending';
    $row['url']='movil/vigilancia/pendientes';
    $result[]=$row;
    //$row['title']='Validated News';
    $row['title']='Validated';
    $row['url']='movil/vigilancia/validados';
    $result[]=$row;
    //$row['title']='Top Rated News';
    $row['title']='Top Rated';
    $row['url']='movil/vigilancia/lo-mas-valorado';
    $result[]=$row;
    //$row['title']='Most commented news';
    $row['title']='Most commented';
    $row['url']='movil/vigilancia/lo-mas-comentado';
    $result[]=$row;
    $row['title']='.Last';
    $row['url']='movil/vigilancia/ultimas';
    $result[]=$row;
    $row['title']='Rejected';
    $row['url']='movil/vigilancia/rechazados';
    $result[]=$row;
    $row['title']='Bookmarked';
    $row['url']='movil/vigilancia/bookmarks';
    $result[]=$row;
    return $result;
}
function movil_vigilancia_get_title($type){
  $result=t('Monitoring');
  switch($type){
    case 'pendientes':
        //$result='Monitoring - News pending to be filtered';
        $result='News pending to be filtered';        
        break;
    case 'validados':
        $result='Validated News';        
        break;
    case 'lo-mas-valorado':
        $result='Top Rated News';        
        break;
    case 'lo-mas-comentado':
        $result='Most commented news';        
        break;
    case 'ultimas':
        $result='Latest News';        
        break;
    case 'rechazados':
        $result='Rejected News';        
        break;
    case 'bookmarks':
        $result='Bookmarked News';        
        break;
    default:
        break;
  }
  return $result;
}
function movil_vigilancia_add_seleccionar_vigilancia_js(){
  movil_grupo_add_seleccionar_grupo_js();  
}
function movil_vigilancia_nodes_html($rows,$output_in){
  $output=$output_in;
  $my_limit=10;  
  $rows=my_set_estrategia_pager($rows,$my_limit);
  $rows=movil_vigilancia_set_node_view($rows);  
  $output.=set_array_view_html($rows);

  if (!empty($rows)) {
    $tags=array();  
    $tags[0]='<<';
    $tags[1]='<';
    $tags[3]='>';
    $tags[4]='>>';
    $output .= theme('pager', $tags, $my_limit,0,array(),5);    
  }
  else {
 
    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  return $output;
}  