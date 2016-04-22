<?php
function fusion_mobile_menu_local_tasks() {
  $output = '';

  //intelsat-2015
  if(red_movil_is_show_menu_primary_local_tasks()){
    //if ($primary = menu_primary_local_tasks()) {
    if ($primary = red_movil_menu_primary_local_tasks()) {  
      $output .= "<ul class=\"tabs primary\">\n" . $primary . "</ul>\n";
    }
  }  
  if ($secondary = menu_secondary_local_tasks()) {
    $output .= "<ul class=\"tabs secondary\">\n" . $secondary . "</ul>\n";
  }

  return $output;
}
function fusion_mobile_breadcrumb($breadcrumb) {
  if(red_movil_is_show_breadcrumb()){  
    if (!empty($breadcrumb)) {
      return '<div class="breadcrumb">' . implode(' » ', $breadcrumb) . '</div>';
    }
  }
  return '';
}
function fusion_mobile_upload_form_current($form){
  $header = array('', t('Delete'), t('List'), t('Description'), t('Weight'), t('Size'));
  //intelsat-2015
  $is_movil=0;
  if(red_movil_is_activado()){      
      /*$node=my_get_node();
      if(!(isset($node->nid) && !empty($node->nid))){
          $node=hontza_get_node_by_form($form);
      }
      if(in_array($node->type,array('noticia'))){*/
        $is_movil=1;  
        $header=array(t('Description'));
      //}
  }
  
  drupal_add_tabledrag('upload-attachments', 'order', 'sibling', 'upload-weight');
  
  foreach (element_children($form) as $key) {
    // Add class to group weight fields for drag and drop.
    $form[$key]['weight']['#attributes']['class'] = 'upload-weight';
    //intelsat-2015
    if(!$is_movil){
        $row = array('');    
        $row[] = drupal_render($form[$key]['remove']);
        $row[] = drupal_render($form[$key]['list']);
    }else{
        $row=array();
        unset($form[$key]['remove']);
        unset($form[$key]['list']);
    }
    if(!$is_movil){
        $row[] = drupal_render($form[$key]['description']);    
        $row[] = drupal_render($form[$key]['weight']);
        $row[] = drupal_render($form[$key]['size']);
    }else{
        $description=$form[$key]['description']['#default_value'];
        $row[]=red_funciones_cortar_node_title($description,30);            
        unset($form[$key]['description']);
        unset($form[$key]['weight']);
        unset($form[$key]['size']);
    }
    if(!$is_movil){
        $rows[] = array('data' => $row, 'class' => 'draggable');
    }else{
        $rows[] = array('data' => $row);
    }
  }
  $output = theme('table', $header, $rows, array('id' => 'upload-attachments'));
  $output .= drupal_render($form);
  return $output;  
}
function fusion_mobile_links($links_in, $attributes = array('class' => 'links')) {
  global $language;
  $output = '';
  //intelsat-2015
  $links=$links_in;
  $is_comentario_links=0;
  if(isset($links['is_comentario_links'])){
      $is_comentario_links=$links['is_comentario_links'];
      unset($links['is_comentario_links']);
  }
  //
  if (count($links) > 0) {
    //intelsat-2015
    if($is_comentario_links){  
        $output = '<ul' . drupal_attributes($attributes) . ' style="height:auto;>';
    }else{
        $output = '<ul' . drupal_attributes($attributes) . '>';  
    }
    //
    $num_links = count($links);
    $i = 1;
    //intelsat-2015
    if($is_comentario_links){
        $links=array_reverse($links,true);
        if(isset($links[0]) && empty($links[0])){
            unset($links[0]);
            $my_array=array();
            $my_array[0]='';
            $links=array_merge($my_array,$links);
        }
    }
    //
    foreach ($links as $key => $link) {
      /*
      //intelsat-2015  
      if($is_comentario_links){
          if(empty($link)){
              continue;
          }          
      }
      //
      */
      $class = $key;

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class .= ' first';
      }
      if ($i == $num_links) {
        $class .= ' last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page())) && (empty($link['language']) || $link['language']->language == $language->language)) {
        $class .= ' active';
      }
      //intelsat-2015
      $link_style='';
      if($is_comentario_links){
        $link_style=' style="float:left;padding:0px 5px;"';
      }
      //
      $output .= '<li' . drupal_attributes(array('class' => $class)) . ''.$link_style.'>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        if($is_comentario_links){
            $icono=fusion_mobile_get_comentario_link_icono($key);
            $link['html']=true;
            $output .= l($icono, $link['href'], $link);
        }else{
            $output .= l($link['title'], $link['href'], $link);
        }    
      }
      else if (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>\n";
    }
    $output .= '</ul>';
  }

  return $output;
}
function fusion_mobile_get_comentario_link_icono($key){
  return red_movil_get_comentario_link_icono($key);  
}
function fusion_mobile_comment_upload_form_current(&$form) {
  $header = array('', t('Delete'), t('List'), t('Description'), t('Weight'), t('Size'));
  //intelsat-2015
  $is_movil=0;
  if(red_movil_is_activado()){      
    $is_movil=1;  
    $header=array(t('Description'));
  }
  drupal_add_tabledrag('comment-upload-attachments', 'order', 'sibling', 'comment-upload-weight');

  
  
  foreach (element_children($form) as $key) {
    // Add class to group weight fields for drag and drop.
    $form[$key]['weight']['#attributes']['class'] = 'comment-upload-weight';
    //intelsat-2015
    if(!$is_movil){
        $row = array('');
        $row[] = drupal_render($form[$key]['remove']);
        $row[] = drupal_render($form[$key]['list']);
    }else{
        $row = array();
        unset($form[$key]['remove']);
        unset($form[$key]['list']);
    }    
    //intelsat-2015
    if(!$is_movil){
        $row[] = drupal_render($form[$key]['description']);    
        $row[] = drupal_render($form[$key]['weight']);
        $row[] = drupal_render($form[$key]['size']);
    }else{
        $description=$form[$key]['description']['#default_value'];
        $row[]=red_funciones_cortar_node_title($description,30);            
        unset($form[$key]['description']);        
        unset($form[$key]['weight']);
        unset($form[$key]['size']);
    }
    $rows[] = array('data' => $row, 'class' => 'draggable');
  }
  $output = theme('table', $header, $rows, array('id' => 'comment-upload-attachments'));
  $output .= drupal_render($form);
  return $output;
}