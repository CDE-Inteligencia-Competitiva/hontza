<?php
function social_learning_simular_guardar_collection_items($collection_node){
    if(isset($collection_node->field_collection_rss_test[0]['url']) && !empty($collection_node->field_collection_rss_test[0]['url'])){
        $url=$collection_node->field_collection_rss_test[0]['url']; 
        if(social_learning_simular_is_social_api_url($url)){
            social_learning_simular_guardar_collection_items_by_resource_array($collection_node);
        }else{    
            $dxml = file_get_contents($url);

            $data = simplexml_load_string($dxml);
            if(isset($data->results->rss)){
                return 'El rss no tiene el formato esperado en esta página';
            }else{
                //social_learning_collections_delete_collection_items($collection_node);
                $sets = $data->channel->item;
                $all=0;
                if(isset($data->channel) && isset($data->channel->item)){
                    $all = sizeof($sets);
                }
                for($i=0;$i<$all;$i++){
                  $r = $sets[$i];
                  //
                  social_learning_simular_collection_item_node_save($r,$collection_node);
                }
            }
        }
        drupal_goto('social_learning/results_download_collection_items/'.$collection_node->nid);
    }else{
        return 'El Collection no tiene un rss asignado';
    }    
}
function social_learning_simular_collection_item_node_save($r,$collection_node){
    $item_link=(string) $r->link;
    if(!social_learning_collections_is_item_duplicado($item_link,$collection_node)){
        //
        $node=new stdClass();
        $node->type='collection_item';
        $node->title=(string) $r->title;
        $node->body=(string) $r->description;
        $node->field_collection_item_url[0]=array('url'=>$item_link,'title'=>'','attributes'=>'');
        $node->field_collection_nid[0]['nid']=$collection_node->nid;
        node_save($node);
    }    
}
function social_learning_simular_is_social_api_url($url){
    if(in_array($url,array('http://social.hontza.es/api/resources/','http://217.70.191.147/api/resources/'))){
        return 1;
    }
    return 0;
}
function social_learning_simular_guardar_collection_items_by_resource_array($collection_node){
    $resources_array=social_learning_get_resources_array();
    if(!empty($resources_array)){
        $topics_array=social_learning_topics_get_topics_array();    
        foreach($resources_array as $i=>$row){
            if(!empty($row->title)){
                 social_learning_simular_collection_item_node_save_by_resource($row,$collection_node,$topics_array);
            }    
        }
    }
}
function social_learning_simular_collection_item_node_save_by_resource($row,$collection_node,$topics_array){
    $item_link=$row->url;
    $resource_id=social_learning_get_resource_id_by_url($row->resource);        
    $interest=social_learning_collections_get_social_interest($row->interest);
    $score_average=social_learning_collections_get_score_average($resource_id,$row,$topics_array,$resource_topics_array);
    if(!social_learning_collections_is_item_duplicado($item_link,$collection_node)){
        //
        $node=new stdClass();
        $node->type='collection_item';
        $node->title=$row->title;
        $node->body=$row->description;
        $node->field_collection_item_url[0]=array('url'=>$item_link,'title'=>'','attributes'=>'');
        $node->field_collection_nid[0]['nid']=$collection_node->nid;
        $node->field_collec_item_resource_id[0]['value']=$resource_id;
        $node->field_collection_item_interest[0]['value']=$interest;
        $node->field_collec_item_score_average[0]['value']=$score_average; 
        node_save($node);
    }else{
        //$node=social_learning_collections_get_collection_item_node_by_url($item_link,$collection_node);
        $node=social_learning_collections_get_collection_item_node_by_resource_id($resource_id,$collection_node);
        if(isset($node->nid) && !empty($node->nid)){
            $node->field_collection_item_interest[0]['value']=$interest;
            $node->field_collec_item_score_average[0]['value']=$score_average;            
            node_save($node);
        }       
    }    
}
function social_learning_simular_delete_callback(){
    //$resource_id=697;
    //social_learning_collections_delete_resource_servidor($resource_id);
    return '';
}
function social_learning_simular_collection_create_rss($collection_nid){
    $nid_array=social_learning_collections_get_all_collection_items_nid_array($collection_nid);        
    $channel = array();
    social_learning_simular_node_feed($nid_array,$channel);  
    exit();
}
function social_learning_simular_node_feed($nids = FALSE, $channel = array(),$is_print=1) {
  global $base_url, $language;

  if ($nids === FALSE) {
    $nids = array();
    $result = db_query_range(db_rewrite_sql('SELECT n.nid, n.created FROM {node} n WHERE n.promote = 1 AND n.status = 1 ORDER BY n.created DESC'), 0, variable_get('feed_default_items', 10));
    while ($row = db_fetch_object($result)) {
      $nids[] = $row->nid;
    }
  }

  $item_length = variable_get('feed_item_length', 'teaser');
  $namespaces = array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/');

  $items = '';
  foreach ($nids as $nid) {
    // Load the specified node:
    $item = node_load($nid);
    $item->build_mode = NODE_BUILD_RSS;
    $item->link = url("node/$nid", array('absolute' => TRUE));

    if ($item_length != 'title') {
      $teaser = ($item_length == 'teaser') ? TRUE : FALSE;

      // Filter and prepare node teaser
      if (node_hook($item, 'view')) {
        $item = node_invoke($item, 'view', $teaser, FALSE);
      }
      else {
        $item = node_prepare($item, $teaser);
      }

      // Allow modules to change $node->content before the node is rendered.
      node_invoke_nodeapi($item, 'view', $teaser, FALSE);

      // Set the proper node property, then unset unused $node property so that a
      // bad theme can not open a security hole.
      $content = drupal_render($item->content);
      if ($teaser) {
        $item->teaser = $content;
        unset($item->body);
      }
      else {
        $item->body = $content;
        unset($item->teaser);
      }
    
      // Allow modules to modify the fully-built node.
      node_invoke_nodeapi($item, 'alter', $teaser, FALSE);
    }

    // Allow modules to add additional item fields and/or modify $item
    $extra = node_invoke_nodeapi($item, 'rss item');
    $extra = array_merge($extra, array(array('key' => 'pubDate', 'value' => gmdate('r', $item->created)), array('key' => 'dc:creator', 'value' => $item->name), array('key' => 'guid', 'value' => $item->nid .' at '. $base_url, 'attributes' => array('isPermaLink' => 'false'))));
    foreach ($extra as $element) {
      if (isset($element['namespace'])) {
        $namespaces = array_merge($namespaces, $element['namespace']);
      }
    }

    // Prepare the item description
    switch ($item_length) {
      case 'fulltext':
        $item_text = $item->body;
        break;
      case 'teaser':
        $item_text = $item->teaser;
        if (!empty($item->readmore)) {
          $item_text .= '<p>'. l(t('read more'), 'node/'. $item->nid, array('absolute' => TRUE, 'attributes' => array('target' => '_blank'))) .'</p>';
        }
        break;
      case 'title':
        $item_text = '';
        break;
    }

    $items .= format_rss_item($item->title, $item->link, $item_text, $extra);
  }

  $channel_defaults = array(
    'version'     => '2.0',
    'title'       => variable_get('site_name', 'Drupal'),
    'link'        => $base_url,
    'description' => variable_get('site_mission', ''),
    'language'    => $language->language
  );
  $channel = array_merge($channel_defaults, $channel);

  $output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
  $output .= "<rss version=\"". $channel["version"] ."\" xml:base=\"". $base_url ."\" ". drupal_attributes($namespaces) .">\n";
  $output .= format_rss_channel($channel['title'], $channel['link'], $channel['description'], $items, $channel['language']);
  $output .= "</rss>\n";

  if($is_print){
    drupal_set_header('Content-Type: application/rss+xml; charset=utf-8');  
    print $output;
  }else{
    return $output;  
  }  
}
function social_learning_simular_collection_create_rss_callback(){
    $collection_nid=arg(2);
    //social_learning_simular_collection_create_rss($collection_nid);
    social_learning_simular_social_collection_create_rss($collection_nid);
}
function social_learning_simular_download_documentos_semilla(){
    $nid=141617;
    if(!empty($nid)){
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $collection_id=social_learning_collections_get_collection_id($node);
            if(!empty($collection_id)){
                return social_learning_simular_save_documentos_semilla($node);
            }else{
                return 'No se pueden descargar noticias desde esta colección porque todavía no tiene asignado "Collection id"';
            }
        }        
    }
    return '';
}
function social_learning_simular_save_documentos_semilla($collection_node){
    $collection_id=social_learning_collections_get_collection_id($collection_node);
    if(!empty($collection_id)){
        $url=hontza_social_define_url('api/collections/'.$collection_id);
        $content=file_get_contents($url);
        $content=trim($content);
        $result=json_decode($content);
        social_learning_simular_save_save_documentos_semilla_by_resource_array($result,$collection_node);        
        drupal_goto('social_learning/results_download_collection_items/'.$collection_node->nid);
    }else{
        return 'El Collection no existe en el servidor social.hontza.es';
    }
}
function social_learning_simular_save_save_documentos_semilla_by_resource_array($result,$collection_node){
    if(isset($result->resources) && !empty($result->resources)){        
        foreach($result->resources as $i=>$row){
            if(!empty($row->title)){
                 $resources_id=social_learning_get_resource_id_by_url($row->resource);   
                 if(social_learning_collections_is_documento_semilla($resources_id,$collection_node->nid)){
                    social_learning_simular_save_save_documentos_semilla_by_resource($row,$collection_node);
                 }   
            }    
        }
    }
}
function social_learning_simular_save_save_documentos_semilla_by_resource($row,$collection_node){
   $resources_id=social_learning_get_resource_id_by_url($row->resource);
   $documento_semilla_node=social_learning_collections_get_documento_semilla_node_by_resources_id($resources_id);
   if(isset($documento_semilla_node->nid) && !empty($documento_semilla_node->nid)){
      $documento_semilla_node->body=$row->description;
      node_save($documento_semilla_node);
   }
}
function social_learning_simular_collections_upload_collection_resource_callback(){
    //social_learning_collections_download_all_collection_items();
    return date('Y-m-d H:i');
}
function social_learning_simular_get_social_tags($node){
    $result=array();
    if(isset($node->field_social_tags[0]['value']) && !empty($node->field_social_tags[0]['value'])){
        //$result[]=$node->field_social_tags[0]['value'];
        $result=explode(',',$node->field_social_tags[0]['value']);
    }
    return $result;
}
function social_learning_simular_collection_filtro_temporal_callback(){
    $collection_nid=arg(2);
    //return social_learning_simular_collection_filtro_temporal($collection_nid);
    return '';    
}
function social_learning_simular_collection_filtro_temporal($collection_nid){
    $node=node_load($collection_nid);
    $collection_id=social_learning_collections_get_collection_id($node);
    if(!empty($collection_id)){
        $save_result=social_learning_simular_save_collection_temporal($node);
        if(empty($save_result)){
           //return social_learning_simular_collection_temporal_html();
           drupal_goto('social_learning/simular_results_collection_filtro_temporal'); 
        }else{
           return $save_result; 
        }
    }else{
        return 'No se pueden descargar noticias desde esta colección porque todavía no tiene asignado "Collection id"';
    }         
}
function social_learning_simular_save_collection_temporal($collection_node,$is_cron=0){
    $collection_id=social_learning_collections_get_collection_id($collection_node);
    if(!empty($collection_id)){
        $url=hontza_social_define_url('api/collections/'.$collection_id);
        $content=file_get_contents($url);
        $content=trim($content);
        $result=json_decode($content);
        social_learning_simular_save_collection_temporal_by_resource_array($result,$collection_node);
        /*if(!$is_cron){
            drupal_goto('social_learning/results_download_collection_items/'.$collection_node->nid);
        }*/
    }else{
        return 'El Collection no existe en el servidor social.hontza.es';
    }
}
function social_learning_simular_save_collection_temporal_by_resource_array($result,$collection_node){
    social_learning_simular_delete_all_collection_temporal();
    if(isset($result->resources) && !empty($result->resources)){        
        foreach($result->resources as $i=>$row){
            if(!empty($row->title)){
                 $resources_id=social_learning_get_resource_id_by_url($row->resource);   
                 if(!social_learning_collections_is_documento_semilla($resources_id,$collection_node->nid)){
                    social_learning_simular_collection_temporal_node_save_by_resource($row,$collection_node);
                 }   
            }    
        }
    }
}
function social_learning_simular_collection_temporal_node_save_by_resource($row,$collection_node){    
    $item_link=$row->url;
    $resource_id=social_learning_get_resource_id_by_url($row->resource);
    $interest=social_learning_collections_get_social_interest($row->interest);
    $score_average=social_learning_collections_get_score_average($row);
    $serialized_tags=social_learning_collections_get_serialized_tags($row);
    $serialized_topics=social_learning_collections_get_serialized_topics($row);
    $serialized_mentions=social_learning_collections_get_serialized_mentions($row);
    //if(!social_learning_collections_is_item_duplicado($item_link,$collection_node)){
        //
        $node=new stdClass();
        $node->type='collection_temporal';
        $node->title=$row->title;
        $node->body=$row->description;
        /*$node->field_collection_item_url[0]=array('url'=>$item_link,'title'=>'','attributes'=>'');
        $node->field_collection_nid[0]['nid']=$collection_node->nid;
        $node->field_collec_item_resource_id[0]['value']=$resource_id;
        $node->field_collection_item_interest[0]['value']=$interest;
        $node->field_collec_item_score_average[0]['value']=$score_average;
        $node->field_collection_item_tags[0]['value']=$serialized_tags;
        $node->field_collection_item_topics[0]['value']=$serialized_topics;
        $node->field_collection_item_mentions[0]['value']=$serialized_mentions;*/
        //seguramente aquí entrará solo el cron
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            $node->og_groups=$collection_node->og_groups;            
        }
        node_save($node);
        //
    /*}else{
        //$node=social_learning_collections_get_collection_item_node_by_url($item_link,$collection_node);
        $node=social_learning_collections_get_collection_item_node_by_resource_id($resource_id,$collection_node);
        if(isset($node->nid) && !empty($node->nid)){
            $node->field_collection_item_interest[0]['value']=$interest;
            $node->field_collec_item_score_average[0]['value']=$score_average;
            $node->field_collection_item_tags[0]['value']=$serialized_tags;
            $node->field_collection_item_tags[0]['value']=$serialized_tags;
            $node->field_collection_item_topics[0]['value']=$serialized_topics;
            $node->field_collection_item_mentions[0]['value']=$serialized_mentions;
            node_save($node);
        }       
    }*/
}
function social_learning_simular_delete_all_collection_temporal(){
    $node_array=hontza_get_all_nodes(array('collection_temporal'));
    if(!empty($node_array)){    
        foreach($node_array as $i=>$node){
            //para que no salgan los mensajes que se han borrado los items
            social_learning_collections_node_delete($node->nid);
        }
    }    
}
function social_learning_simular_collection_temporal_html(){
    $output='';
    $rows=array();
    $num_rows=FALSE;
    $my_limit=10;
    $sql='SELECT * FROM {node} n WHERE n.type="collection_temporal" ORDER BY n.created DESC';
    $res = pager_query(db_rewrite_sql($sql),$my_limit);
    
    $output = '';
    $num_rows = FALSE;
    while ($row = db_fetch_object($res)) {
      $node=node_load($row->nid);
      $output .= node_view($node, 1);
      $num_rows = TRUE;
    }

    if ($num_rows) {
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function social_learning_simular_results_collection_filtro_temporal_callback(){
    return social_learning_simular_collection_temporal_html();
}
function social_learning_simular_topics_upload_topic_postapi_callback(){
    return 'Funcion desactivada';
    $nid=142271;
    $node=node_load($nid);
    //social_learning_topics_upload_topic_postapi($node);
    //social_learning_topics_update_topic_postapi($node);
}
function social_learning_simular_social_collection_create_rss($collection_nid){
    $url=social_learning_collections_get_collection_url_rss($collection_nid);
    if(!empty($url)){
        print file_get_contents($url);
        exit();
    }    
}