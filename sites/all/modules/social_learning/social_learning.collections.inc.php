<?php
function social_learning_collections_callback($mode=''){
    boletin_report_no_group_selected_denied(); 
    $output='';
    $output.=social_learning_collections_define_menu_acciones();
    $headers=array();
    $headers[]=array('data'=>t('Title'),'field'=>'title');    
    $headers[]=array('data'=>t('Collection'),'field'=>'field_social_collection_id_value');
    $headers[]=t('Status');
    $headers[]='';
    $my_limit=20;
    //    
    $sort='asc';
    $field='node.created';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='title';
        }else if($order==t('Collection id')){
            $field='content_type_collection.field_social_collection_id_value';
        }        
    }
    //
    $rows=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        //$where[]='n.status=1';
        $where[]='node.type="collection"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $sql='SELECT node.*,content_type_collection.field_collection_status_value 
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        LEFT JOIN {content_type_collection} content_type_collection ON node.vid=content_type_collection.vid 
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        $res=db_query($sql);
        $num_rows=FALSE;
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){    
                if($mode=='nodes'){
                    $rows[]=$node;
                }else{
                    $r=array();
                    //
                    $collection_id=social_learning_collections_get_collection_id($node);
                    //
                    $r[0]=$node->title;
                    $r[1]=$collection_id;
                    $r[2]=social_learning_items_get_collection_status($node,$row->field_collection_status_value);
                    $r[3]=array('data'=>social_learning_collections_define_acciones($node,$collection_id),'class'=>'td_nowrap');
                    $rows[]=$r;
                }
                $num_rows=TRUE;
            }            
        }
    }    
    $rows=my_set_estrategia_pager($rows, $my_limit);    
    //
    if($mode=='nodes'){
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    }
    //    
    if ($num_rows) {
        if($mode!='nodes'){
            $output .= theme('table',$headers,$rows);
        }
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    
    return $output;
}
function social_learning_collections_get_collection_id($node,$collection_nid=''){
    $content_type_collection_row=social_learning_collections_content_type_collection_row($node,$collection_nid);
    if(isset($content_type_collection_row->field_social_collection_id_value) && !empty($content_type_collection_row->field_social_collection_id_value)){
        return $content_type_collection_row->field_social_collection_id_value;
    }
    return '';
}
function social_learning_collections_define_acciones($node){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit Collection')),'node/'.$node->nid.'/edit',array('html'=>TRUE,'query'=>'destination=social_learning/collections'));         
    $html[]=l(my_get_icono_action('viewmag',t('View Collection')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/collections'));             
    //$html[]=l(my_get_icono_action('delete',t('Delete Collection')),'node/'.$node->nid.'/delete',array('html'=>TRUE,'query'=>'destination=social_learning/collections'));                 
    $html[]=l(my_get_icono_action('delete',t('Delete Collection')),'social_learning/delete_collection/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/collections'));                     
    $html[]=l(my_get_icono_action('resources_add',t('Resources')),'social_learning/resources_collection/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/collections'));             
    $html[]=l(my_get_icono_action('import_strategy',t('Upload Collection')),'social_learning/upload_collection/'.$node->nid,array('html'=>TRUE));     
    $html[]=l(my_get_icono_action('download_collection_items',t('Download collection items')),'social_learning/download_collection_items/'.$node->nid,array('html'=>TRUE)); 
    $html[]=l(my_get_icono_action('boletin_historico',t('Results')),'social_learning/results_download_collection_items/'.$node->nid,array('html'=>TRUE)); 
    //$html[]=l(my_get_icono_action('filtrar',t('Create RSS')),'social_learning/create_rss/'.$node->nid,array('html'=>TRUE)); 
    $html[]=l(my_get_icono_action('rss',t('Create RSS')),'social_learning/create_rss/'.$node->nid,array('html'=>TRUE));
    return implode('&nbsp;',$html);
}
function social_learning_collections_download_collection_items_callback(){
    $nid=arg(2);
    if(!empty($nid)){
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $collection_id=social_learning_collections_get_collection_id($node);
            if(!empty($collection_id)){
                return social_learning_collections_save_collection_items($node);
            }else{
                return 'No se pueden descargar noticias desde esta colección porque todavía no tiene asignado "Collection id"';
            }
        }        
    }
    return '';
}
function social_learning_collections_content_type_collection_row($node_in,$collection_nid=''){
    if(isset($node_in->nid) && !empty($node_in->nid)){
        $node=clone $node_in;
    }else{
        $node=node_load($collection_nid);
    }
    $res=db_query('SELECT * FROM {content_type_collection} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_collections_upload_collection_callback(){
    $nid=arg(2);
    if(!empty($nid)){
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            social_learning_step_upload_collection($node);   
                $html=array();
                $html[]='<p><i>En esta pantalla enviaremos a social.hontza.es una colección de hontza para añadirla en social.hontza.es, titulo y descripcion a social.hontza.es y recibiremos como respuesta "Collection id"</i></p>';
                //
                $style='style="float:left;clear:both;padding-bottom:5px;"';
                //
                $html[]='<div '.$style.'>';
                $html[]='<label style="float:left;">';
                $html[]='<b>'.t('Title').':&nbsp;</b>';
                $html[]='</label>';
                $html[]='<div style="float:left;">'.$node->title.'</div>';
                $html[]='</div>';
                //
                $html[]='<div '.$style.'>';
                $html[]='<label style="float:left;">';
                $html[]='<b>'.t('Description').':&nbsp;</b>';
                $html[]='</label>';
                $html[]='<div style="float:left;">'.$node->body.'</div>';
                $html[]='</div>';
                //
                $html[]='<div '.$style.'>';
                $html[]=l(t('Return'),'social_learning/collections');
                $html[]='</div>';
                return implode('',$html);               
            /*}else{
               return 'La colección ya existe';                
            }*/
        }
    }
    return '';
}
function social_learning_collections_delete_collection_items($collection_node,$nid=''){    
    $content_type_collection_item_array=social_learning_collections_get_collection_items($collection_node,$nid);
    if(!empty($content_type_collection_item_array)){
        foreach($content_type_collection_item_array as $i=>$row){
            //para que no salgan los mensajes que se han borrado los items
            //social_learning_collections_node_delete($row->nid);
            node_delete($row->nid);
        }
    }
}
function social_learning_collections_get_collection_items($collection_node,$nid_in=''){
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $nid=$collection_node->nid;
    }else{
        $nid=$nid_in;
    }
    $result=array();
    if(!empty($nid)){
        $res=db_query($sql=sprintf('SELECT *,content_field_collection_nid.field_collection_nid_nid FROM {content_type_collection_item} content_type_collection_item LEFT JOIN {content_field_collection_nid} content_field_collection_nid ON content_type_collection_item.vid=content_field_collection_nid.vid WHERE content_field_collection_nid.field_collection_nid_nid=%d',$nid));
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
    }    
    return $result;
}
function social_learning_collections_node_delete($nid) {

  // Clear the cache before the load, so if multiple nodes are deleted, the
  // memory will not fill up with nodes (possibly) already removed.
  $node = node_load($nid, NULL, TRUE);

  if (node_access('delete', $node)) {
    db_query('DELETE FROM {node} WHERE nid = %d', $node->nid);
    db_query('DELETE FROM {node_revisions} WHERE nid = %d', $node->nid);
    db_query('DELETE FROM {node_access} WHERE nid = %d', $node->nid);

    // Call the node-specific callback (if any):
    node_invoke($node, 'delete');
    node_invoke_nodeapi($node, 'delete');

    // Clear the page and block caches.
    cache_clear_all();

    // Remove this node from the search index if needed.
    if (function_exists('search_wipe')) {
      search_wipe($node->nid, 'node');
    }
    watchdog('content', '@type: deleted %title.', array('@type' => $node->type, '%title' => $node->title));
    //drupal_set_message(t('@type %title has been deleted.', array('@type' => node_get_types('name', $node), '%title' => $node->title)));
  }
}
function social_learning_collections_item_node_list_html($collection_node){
    $output='';
    $rows=array();
    $content_type_collection_item_array=social_learning_collections_get_collection_items($collection_node);
    $num_rows=FALSE;
    $my_limit=10;
    $sql=social_learning_collections_define_sql_collections_item_node($collection_node->nid);
    $res = pager_query(db_rewrite_sql($sql),$my_limit);
    
    $output = '';
    $num_rows = FALSE;
    while ($row = db_fetch_object($res)) {
      $node=node_load($row->nid);
      $output .= node_view($node,TRUE);
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
function social_learning_collections_get_collection_item_resumen($node,$len=150,$is_cortar=1){
    return hontza_content_resumen($node);
}
function social_learning_collections_is_item_duplicado($item_link,$collection_node){
    $elemento=(array) $r;
    $item_uniq_array=social_learning_collections_get_guid_url_item_array($item_link,$collection_node);
    if(count($item_uniq_array)>0){
        return 1;
    }
    return 0;
}
function social_learning_collections_get_guid_url_item_array($item_link,$collection_node){
    $where=array();
    $where[]='content_type_collection_item.field_collection_item_url_url="'.$item_link.'"';
    $where[]='node.status = 1'; 
    $where[]='content_field_collection_nid.field_collection_nid_nid='.$collection_node->nid;
    $sql='SELECT node.nid, node.sticky, node.created 
    FROM {node} node
    LEFT JOIN {content_type_collection_item} ON node.vid=content_type_collection_item.vid
    LEFT JOIN {content_field_collection_nid} ON node.vid=content_field_collection_nid.vid
    WHERE '.implode(' AND ',$where).'
    ORDER BY node.sticky DESC, node.created,node.nid DESC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function social_learning_collections_results_download_collection_items_callback(){
    $nid=arg(2);
    $collection_node=node_load($nid);
    drupal_set_title(t('Results').': '.$collection_node->title);    
    $html=array();
    $html[]=social_learning_collections_results_create_menu($nid);
    $html[]=social_learning_collections_get_collection_info_html($collection_node);
    $html[]=social_learning_collections_item_node_list_html($collection_node);
    return implode('',$html);
}
function social_learning_collections_results_create_menu($nid){
    $html=array();
    $html[]='<div style="padding-bottom:5px;">';
    $links=array();
    $links[]=l('Collections','social_learning/collections');    
    //$links[]=l('Upload ratings','social_learning/upload_rating_items/'.$nid);
    $links[]=l('Create RSS','social_learning/create_rss/'.$nid);
    $links[]=l('Order by Date','social_learning/results_download_collection_items_order_by_date/'.$nid,array('query'=>'my_order_by=created'));    
    $links[]=l('Order by Interest','social_learning/results_download_collection_items/'.$nid);    
    $links[]=l('Order by Score Average','social_learning/results_download_collection_items_order_by_score_average/'.$nid,array('query'=>'my_order_by=score_average'));
    $links[]=l('Order by Votes','social_learning/results_download_collection_items_order_by_votes/'.$nid,array('query'=>'my_order_by=votes'));    
    $html[]=implode('&nbsp;|&nbsp',$links);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_collections_upload_rating_items_callback(){
    $nid=arg(2);
    $html=array();
    $html[]='<p>En esta pantalla subíriamos todas las puntuaciones de la colleción a social.hontza.es</p>';
    return implode('',$html);
}
function social_learning_collections_create_rss_callback(){
    $nid=arg(2);
    $html=array();
    $html[]='<p>En esta pantalla pediremos a social.hontza.es, que nos cree un rss para que el usuario luego lo pueda utilizar para crear un canal</p>';
    $html[]=drupal_get_form('social_learning_collections_filtro_create_rss_form');
    return implode('',$html);
}
function social_learning_collections_define_menu_acciones(){
    $html=array();
    $html[]='<div>';
    $link_array=array();
    //$link_array[]=l(t('Create Collection'),'node/add/collection',array('query'=>'destination=social_learning/collections','attributes'=>array('class'=>'add'))); 
    $link_array[]=l(t('Create Collection'),'node/add/collection',array('query'=>'destination=social_learning/upload_collection_step','attributes'=>array('class'=>'add')));     
    if(is_super_admin()){
        $link_array[]=l('Api','social_learning/api/resources');
    }
    $link_array[]=l(t('Table'),'social_learning/collections');
    $link_array[]=l(t('Nodes'),'social_learning/collections_nodes');
    $html[]=implode('&nbsp;|&nbsp;',$link_array);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_collections_get_collection_item_node_by_url($item_link,$collection_node,$resource_id=''){
    $collection_item_nid_array=social_learning_collections_get_collection_item_nid_array_by_url($item_link,$collection_node,$resource_id);
    if(!empty($collection_item_nid_array)){
        $node=node_load($collection_item_nid_array[0]);
        return $node;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_collections_get_collection_item_nid_array_by_url($item_link,$collection_node,$resource_id=''){
    $result=array();
    $where=array();
    if(!empty($resource_id)){
        $where[]='content_type_collection_item.field_collec_item_resource_id_value='.$resource_id;
    }else if(!empty($item_link)){
        $where[]='content_type_collection_item.field_collection_item_url_url="'.$item_link.'"';
    }else{
        return $result;
    }
    $where[]='node.status = 1'; 
    $where[]='content_field_collection_nid.field_collection_nid_nid='.$collection_node->nid;
    $sql='SELECT node.nid, node.sticky, node.created 
    FROM {node} node
    LEFT JOIN {content_type_collection_item} content_type_collection_item ON node.vid=content_type_collection_item.vid
    LEFT JOIN {content_field_collection_nid} content_field_collection_nid ON node.vid=content_field_collection_nid.vid
    WHERE '.implode(' AND ',$where).'
    ORDER BY node.sticky DESC, node.created,node.nid DESC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
function social_learning_collections_get_collection_item_node_by_resource_id($resource_id,$collection_node){
    $node=social_learning_collections_get_collection_item_node_by_url('',$collection_node,$resource_id);
    return $node;
}
function social_learning_collections_get_interest_value($node){
    if(isset($node->field_collection_item_interest[0]['value']) && !empty($node->field_collection_item_interest[0]['value'])){
        return $node->field_collection_item_interest[0]['value'];
    }
    return 0;
}
function social_learning_collections_get_social_interest($interest){
    if(empty($interest)){
        return 0;
    }
    return $interest;
}
function social_learning_collections_get_score_average($row){
    if(isset($row->topics) && !empty($row->topics)){
            $num=count($row->topics);
            $result=0;
            foreach($row->topics as $i=>$topic_row){
                $result=$result+$topic_row->score;
            }
            $result=$result/$num;
            return $result;        
    }
    return 0;
}
function social_learning_collections_get_score_average_value($node){
    if(isset($node->field_collec_item_score_average[0]['value']) && !empty($node->field_collec_item_score_average[0]['value'])){
        return $node->field_collec_item_score_average[0]['value'];
    }
    return 0;
}
function social_learning_collections_item_web_link($node,$is_url=0,$in_vista_compacta=0){
    $url='';
    if(isset($node->field_collection_item_url[0]['display_url']) && !empty($node->field_collection_item_url[0]['display_url'])){
        //$url=$node->field_collection_item_url[0]['display_url'];
        $url=$node->field_collection_item_url[0]['url'];
        $query=$node->field_collection_item_url[0]['query'];
        if(!empty($query)){
            $url.='?'.$query;
        }
    }else if(isset($node->field_collection_item_url[0]['url']) && !empty($node->field_collection_item_url[0]['url'])){
        $url=$node->field_collection_item_url[0]['url'];
    }
    //
    if(empty($url) || hontza_is_nolink($url)){
        $url='no_existe_enlace_origen';    
    }    
    if($is_url){
        return $url;
    }
    $label='';
    //$label=t('Web');
    $attributes=array('target'=>'_blank','title'=>t('Web'),'alt'=>t('Web'));
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    return l($label,$url,array('attributes'=>$attributes));            
}
function social_learning_collections_item_upload_rating_link($node){
    $label='';
    //$label=t('Web');
    $attributes=array('title'=>t('Upload Rating'),'alt'=>t('Upload Rating'));
    //$attributes['target']='_blank';
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    $url='social_learning/upload_item_rating/'.$node->nid;
    return l($label,$url,array('attributes'=>$attributes));
}
function social_learning_collections_upload_item_rating_callback(){
    $nid=arg(2);
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        social_learning_collections_upload_item_rating($nid);
        if(social_learning_collections_is_pantalla('upload_item_rating')){
            $collection_nid=social_learning_collections_get_collection_nid($nid);        
            drupal_goto('social_learning/results_download_collection_items/'.$collection_nid);
        }
    }    
}
function social_learning_collections_is_show_item_upload_rating_link(){
    if(social_learning_admin_grupo_access()){
        return 1;
    }
    return 0;
}
function social_learning_collections_filtro_create_rss_form(){
    $form=array();
    $collection_nid=arg(2);
    //
    $form['#attributes']['target'] = '_blank';
    $form['collection_nid']=array(
        '#type'=>'hidden',
        '#value'=>$collection_nid,
    );
    //$form['collection_multiple_nid']=array(
    $form['collection']=array(
        '#title'=>t('Collection'),
        '#type'=>'select',
        '#options'=>social_learning_collections_define_collection_options(),
        '#multiple'=>TRUE,
    );    
    $form['title']=array(
        '#type'=>'textfield',
        '#title'=>t('Title'),
    );
    $form['description']=array(
        '#type'=>'textfield',
        '#title'=>t('Description'),
    );
    /*$form['tags']=array(
        '#type'=>'textfield',
        '#title'=>t('Tags'),
    );*/
    $form['relevance']=array(
        '#type'=>'textfield',
        '#title'=>t('Relevance'),
    );
    $form['interest']=array(
        '#type'=>'textfield',
        '#title'=>t('Interest'),
    );
    $form['interest_hontza']=array(
        '#type'=>'textfield',
        '#title'=>'Interest Hontza',
    );
    $form['username']=array(
        '#type'=>'textfield',
        '#title'=>t('Author'),
    );
    /*$form['source_website']=array(
        '#type'=>'textfield',
        '#title'=>t('Source').'/'.t('Website'),
    );*/
    $form['site']=array(
        '#type'=>'textfield',
        '#title'=>t('Site'),
    );
    $form['topic']=array(
        '#type'=>'textfield',
        '#title'=>t('Topic'),
    );
    $form['content']=array(
        '#type'=>'select',
        '#options'=>social_learning_collections_define_content_options(),
        '#title'=>t('Format'),
    );
     $form['socialnetwork']=array(
        '#type'=>'select',
        '#options'=>social_learning_collections_define_social_network_options(),
        '#title'=>t('Social Network'),
    );
    $form['language']=array(
        '#type'=>'select',
        '#options'=>social_learning_collections_define_language_options(),
        '#title'=>t('Language'),
    );
    $form['create_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Create'),
    );    
    $cancel_url='social_learning/results_download_collection_items/'.$collection_nid;
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),$cancel_url),
    );
    //
    return $form;
}
function social_learning_collections_define_content_options(){
    $result=array();
    $result['']='';            
    //$result['office']='Office';
    $result['BLOG']=t('Blog'); 
    $result['PDF']='Pdf';    
    $result['VIDEO']=t('Video');
    $result['WEB']='Html/'.t('Webpage');
    return $result;
}
function social_learning_collections_filtro_create_rss_form_submit($form, &$form_state){
    if(isset($form_state['values']['collection_nid'])){
        $collection_nid=$form_state['values']['collection_nid'];
        /*if(isset($form_state['values']['collection_multiple_nid']) && !empty($form_state['values']['collection_multiple_nid'])){
            $collection_nid_array=array_keys($form_state['values']['collection_multiple_nid']);
            $collection_nid=$collection_nid_array[0];
        }*/
        $query=social_learning_collections_create_filtro_temporal_query($form_state['values'],$collection_nid);        
        //drupal_goto('social_learning/simular_collection_create_rss/'.$collection_nid);
        //drupal_goto('social_learning/simular_collection_filtro_temporal/'.$collection_nid);
        //$url='social_learning/collection_filtro_temporal/'.$collection_nid;
        $url='crear/canal-yql';
        if(!empty($query)){
            $query.='&simple=1&collection_nid='.$collection_nid;
            drupal_goto($url,$query);
        }else{
            $query='simple=1&collection_nid='.$collection_nid;
            drupal_goto($url);
        }    
    }    
}
function social_learning_collections_upload_collection_postapi($node){
    $url=hontza_social_define_upload_collection_url();
    $postapi_username_pass=hontza_social_define_username_pass_postapi();    
    //
    $postdata_array=array();
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata=json_encode($postdata_array);
    //
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD,$postapi_username_pass);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);    
    curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postdata),        
));    
    $data=curl_exec($curl);
    $result=json_decode(trim($data));        
    curl_close($curl);
    social_learning_collections_on_upload_collection_postapi($result,$node,$collection_id);    
}
function hontza_social_define_upload_collection_url(){
    $result=hontza_social_define_url('api/collections/');  
    return $result;
}
function social_learning_collections_on_upload_collection_postapi($result,$node_in,$collection_id_in){
    $node=$node_in;
    $url=$result->url;
    $name=$result->name;
    $description=$result->description;
    $resources=$result->resources;
    if(empty($collection_id_in)){
        $collection_id=social_learning_collections_get_collection_id_by_url($url);
        $node->field_social_collection_id[0]['value']=$collection_id;
    }
    $node->field_last_upload_time[0]['value']=time();
    node_save($node);
}
function social_learning_collections_get_collection_id_by_url($collection){
    $result='';
    $my_array=array('api/collections','update/collection/');
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            $url=hontza_social_define_url($value);
            $result=str_replace($url,'',$collection);
            $result=trim($result,'/');
            if(!empty($result) && is_numeric($result)){
                return $result;
            }
        }
    }
    return $result;
}
function social_learning_collections_save_collection_items($collection_node,$is_cron=0){
    $collection_id=social_learning_collections_get_collection_id($collection_node);
    if(!empty($collection_id)){
        $url=hontza_social_define_url('api/collections/'.$collection_id);
        $content=file_get_contents($url);
        $content=trim($content);
        $result=json_decode($content);
        social_learning_collections_save_collection_items_by_resource_array($result,$collection_node);
        if(!$is_cron){
            drupal_goto('social_learning/results_download_collection_items/'.$collection_node->nid);
        }    
    }else{
        return 'El Collection no existe en el servidor social.hontza.es';
    }
}
function social_learning_collections_save_collection_items_by_resource_array($result,$collection_node){
    $kont_created=0;
    $status_with_results=0;
    if(isset($result->resources) && !empty($result->resources)){        
        foreach($result->resources as $i=>$row){
            if(!empty($row->title)){
                 $resources_id=social_learning_get_resource_id_by_url($row->resource);
                 if(!social_learning_collections_is_documento_semilla($resources_id,$collection_node->nid)){
                    social_learning_collections_collection_item_node_save_by_resource($row,$collection_node,$kont_created);
                    $status_with_results=1;
                 }   
            }    
        }
    }
    social_learning_items_save_collection_status_with_results($collection_node,$status_with_results);
    social_learning_step_set_imported_message($kont_created);
}
function social_learning_collections_collection_item_node_save_by_resource($row,$collection_node,&$kont_created){
    $item_link=$row->url;
    $resource_id=social_learning_get_resource_id_by_url($row->resource);
    $interest=social_learning_collections_get_social_interest($row->interest);
    $score_average=social_learning_collections_get_score_average($row);
    $serialized_tags=social_learning_collections_get_serialized_tags($row);
    $serialized_topics=social_learning_collections_get_serialized_topics($row);
    $serialized_mentions=social_learning_collections_get_serialized_mentions($row);
    if(!social_learning_collections_is_item_duplicado($item_link,$collection_node)){
        //
        $node=new stdClass();
        $node->type='collection_item';
        $node->title=$row->title;
        $node->body=$row->description;
        $node->field_collection_item_url[0]=array('url'=>$item_link,'title'=>'','attributes'=>'');
        $node->field_collection_nid[0]['nid']=$collection_node->nid;
        //$node->field_collection_nid=social_learning_collections_get_field_collection_nid_multiple_value_array_by_collection($row->collection,$collection_node);
        $node->field_collec_item_resource_id[0]['value']=$resource_id;
        $node->field_collection_item_interest[0]['value']=$interest;
        $node->field_collec_item_score_average[0]['value']=$score_average;
        $node->field_collection_item_tags[0]['value']=$serialized_tags;
        $node->field_collection_item_topics[0]['value']=$serialized_topics;
        $node->field_collection_item_mentions[0]['value']=$serialized_mentions;
        $node->field_collection_item_status[0]['value']=$row->status;
        //seguramente aquí entrará solo el cron
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            $node->og_groups=$collection_node->og_groups;            
        }
        node_save($node);
        social_learning_items_on_collection_item_node_insert_by_resource($collection_node,$node,$resource_id,$row);
        $kont_created++;
    }else{
        //$node=social_learning_collections_get_collection_item_node_by_url($item_link,$collection_node);
        $node=social_learning_collections_get_collection_item_node_by_resource_id($resource_id,$collection_node);
        if(isset($node->nid) && !empty($node->nid)){
            //$node->field_collection_nid=social_learning_collections_get_field_collection_nid_multiple_value_array_by_collection($row->collection,$collection_node);
            $node->field_collection_item_interest[0]['value']=$interest;
            $node->field_collec_item_score_average[0]['value']=$score_average;
            $node->field_collection_item_tags[0]['value']=$serialized_tags;
            $node->field_collection_item_tags[0]['value']=$serialized_tags;
            $node->field_collection_item_topics[0]['value']=$serialized_topics;
            $node->field_collection_item_mentions[0]['value']=$serialized_mentions;
            $node->field_collection_item_status[0]['value']=$row->status;
            node_save($node);            
        }       
    }
}
function social_learning_collections_delete_collection_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    
    $form['collection_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'social_learning/collections');
        
    return $form;
}
function social_learning_collections_delete_collection_form_submit($form,&$form_state){
    $nid='';
    if(isset($form_state['values']['collection_nid'])){
        $nid=$form_state['values']['collection_nid'];
    }else if(isset($form_state['values']['nid'])){
        $nid=$form_state['values']['nid'];        
    }
    //
        if(!empty($nid)){
            $node=node_load($nid);            
            social_learning_collections_delete_collection_items($node,$nid);
            //
            if(isset($node->nid) && !empty($node->nid)){
                social_learning_items_collection_delete_all($node->nid);
                $collection_id=social_learning_collections_get_collection_id($node);
                social_learning_collections_delete_collection_servidor($collection_id);
                //
                node_delete($nid);                               
            }            
        }
    
    drupal_goto('social_learning/collections');
}
function social_learning_collections_get_serialized_tags($row){
    if(isset($row->tags) && !empty($row->tags)){
        return serialize($row->tags);
    }
    return '';
}
function social_learning_collections_get_tags_value_html($node){
    if(isset($node->field_collection_item_tags[0]['value']) && !empty($node->field_collection_item_tags[0]['value'])){
        $value=unserialize($node->field_collection_item_tags[0]['value']);
        $result=implode(',',$value);
        return $result;
    }
    return '';
}
function social_learning_collections_get_serialized_topics($row){
    if(isset($row->topics) && !empty($row->topics)){
        return serialize($row->topics);
    }
    return '';
}
function social_learning_collections_get_topics_value_html($node){
    if(isset($node->field_collection_item_topics[0]['value']) && !empty($node->field_collection_item_topics[0]['value'])){
        $topics_array=unserialize($node->field_collection_item_topics[0]['value']);
        $name_array=social_learning_collections_get_topics_name_array($topics_array);
        $result=implode(',',$name_array);
        return $result;
    }
    return '';
}
function social_learning_collections_get_topics_name_array($topics_array){
    $result=array();
    if(!empty($topics_array)){
       foreach($topics_array as $i=>$topic_row){
           $result[]=$topic_row->name;
       }
    }
    return $result;
}
function social_learning_collections_get_topics_value_html_table($node,$field_in='field_collection_item_topics'){
    $r=$node->$field_in; 
    if(isset($r[0]['value']) && !empty($r[0]['value'])){
        $html=array();
        $topics_array=unserialize($r[0]['value']);
        if(!empty($topics_array)){
            $html[]='<table>';
            $html[]='<tr>';
            $html[]='<th>Id</th>';
            $html[]='<th>'.t('Name').'</th>';
            $html[]='<th>'.t('Tags').'</th>';
            $html[]='<th>'.t('Score').'</th>';
            $html[]='</tr>';
            foreach($topics_array as $i=>$topics_row){
                $html[]='<tr>';
                $topics_id=social_learning_topics_get_topics_id_by_url($topics_row->topic);
                $html[]='<td>'.$topics_id.'</td>';
                $html[]='<td>'.$topics_row->name.'</td>';
                $html[]='<td>'.implode(',',$topics_row->tags).'</td>';
                $html[]='<td>'.$topics_row->score.'</td>';                
                $html[]='</tr>';
            }
            $html[]='</table>';
        }
        return implode('',$html);
    }
    return '';
}
function social_learning_collections_update_collection_postapi($node,$collection_id){
    $url=hontza_social_define_url('api/collections/'.$collection_id);
    $url_update=hontza_social_define_url('/update/collection/'.$collection_id.'/');
    /*if(social_learning_collections_is_with_resources($url)){
        return;
    }*/
    $content_type_collection_resource_array=social_learning_collections_get_content_type_collection_resource_array($node->nid);
    $content_type_collection_feed_array=social_learning_feeds_get_content_type_collection_feed_array($node->nid);
    $content_type_collection_topic_array=social_learning_topics_get_content_type_collection_topic_array($node->nid);
    //$collection_row=social_learning_collections_get_collection_servidor_row($url);
    $collection_row=social_learning_collections_get_collection_servidor_row($url_update);
    //
    $postdata_array=array();
    $postdata_array['url']=$url;    
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata_array['resources']=array();
    $postdata_array['feeds']=array();
    $postdata_array['topics']=array();
    //
    if(isset($collection_row->resources) && !empty($collection_row->resources)){
        //$postdata_array['resources']=social_learning_collections_get_resources_id_array($collection_row->resources);
        $postdata_array['resources']=$collection_row->resources;
    }
    $postdata_array['resources']=social_learning_collections_set_basic_resources($postdata_array['resources'],$content_type_collection_resource_array);
    //
    if(isset($collection_row->feeds) && !empty($collection_row->feeds)){
        $postdata_array['feeds']=$collection_row->feeds;
    }
    $postdata_array['feeds']=social_learning_feeds_set_basic_feeds($postdata_array['feeds'],$content_type_collection_feed_array);
    //
    if(isset($collection_row->topics) && !empty($collection_row->topics)){
        $postdata_array['topics']=$collection_row->topics;
    }
    $postdata_array['topics']=social_learning_topics_set_basic_topics($postdata_array['topics'],$content_type_collection_topic_array);    
    //
    /*print $url_update.'<BR>';
    echo print_r($postdata_array,1);
    exit();*/    
    //
    $postdata=json_encode($postdata_array);
    //
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url_update);    
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($postdata)                                                                       
    ));               
    $data=curl_exec($curl);
    $result=json_decode(trim($data));
    //echo 'postapi='.print_r($result,1);exit();
    curl_close($curl);   
}
function social_learning_collections_get_serialized_mentions($row){
    if(isset($row->mentions) && !empty($row->mentions)){
        return serialize($row->mentions);
    }
    return '';
}
function social_learning_collections_get_mentions_value_html_table($node,$field_in='field_collection_item_mentions'){
    $r=$node->$field_in;
    if(isset($r[0]['value']) && !empty($r[0]['value'])){
        $html=array();
        $mentions_array=unserialize($r[0]['value']);
        if(!empty($mentions_array)){
            $html[]='<table>';
            $html[]='<tr>';
            //$html[]='<th>Id</th>';
            //$html[]='<th>'.t('Url').'</th>';
            $html[]='<th>'.t('Mention').'</th>';
            $html[]='<th>'.t('Card').'</th>';
            $html[]='</tr>';
            foreach($mentions_array as $i=>$mentions_row){
                $html[]='<tr>';
                $url='';
                $mentions_id=social_learning_mentions_get_mention_id_by_url($url); 
                $url_api_mention='social_learning/api/mentions/'.$mentions_id;
                //$html[]='<td>'.l($mentions_id,$url_api_mention,array('attributes'=>array('target'=>'_blank'))).'</td>';
                //$html[]='<td>'.l($url,$url_api_mention,array('attributes'=>array('target'=>'_blank'))).'</td>';
                $html[]='<td>'.$mentions_row->mention.'</td>';
                $html[]='<td>'.social_learning_collections_create_text_links($mentions_row->card).'</td>';
                $html[]='</tr>';
            }
            $html[]='</table>';
        }
        return implode('',$html);
    }
    return '';
}
function social_learning_collections_is_with_resources($url){
    $collection_row=social_learning_collections_get_collection_servidor_row($url);
    if(isset($collection_row->resources) && !empty($collection_row->resources)){
        return 1;
    }
    return 0;
}
function social_learning_collections_get_collection_servidor_row($url){
    $content=file_get_contents($url);
    $result=json_decode($content);
    return $result;
}
function social_learning_collections_get_resources_id_array($resources){
    $result=array();
    if(!empty($resources)){    
        foreach($resources as $i=>$row){
            $resources_id=social_learning_get_resource_id_by_url($row->resource);
            $result[]=$resources_id;
        }
    }
    return $result;
}
function social_learning_collections_collection_item_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_collection_nid','field_collec_item_resource_id','field_collection_item_interest','field_collec_item_score_average',
    'field_collection_item_tags','field_collection_item_topics','field_collection_item_mentions','field_coll_item_last_upload_time','field_collection_item_status');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
}
function social_learning_collections_create_text_links($s){
    $result= preg_replace("/((http|https|www)[^\s]+)/", '<a target=\"_blank\" href="$1">$0</a>', $s);
    $result= preg_replace("/href=\"www/", 'href="http://www', $result);
    $result = preg_replace("/(@[^\s]+)/", '<a target=\"_blank\"  href="http://twitter.com/intent/user?screen_name=$1">$0</a>', $result);
    //$result = preg_replace("/(#[^\s]+)/", '<a target=\"_blank\"  href="http://twitter.com/search?q=$1">$0</a>', $result);
    return $result;
}
function social_learning_collections_delete_resource_servidor($resource_id){
    if(!empty($resource_id)){
        $url=hontza_social_define_url('api/resources/'.$resource_id.'/');
        social_learning_delete_object($url);
    }
}
function social_learning_delete_object($url){
    $curl = curl_init();
    //
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);    
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE"); 
    $data=curl_exec($curl);
    $result=json_decode(trim($data));
    curl_close($curl);
    return $result;
}
function social_learning_collections_delete_collection_servidor($collection_id){
    if(!empty($collection_id)){
        $url=hontza_social_define_url('api/collections/'.$collection_id.'/');
        social_learning_delete_object($url);
    }    
}
function social_learning_collections_define_sql_collections_item_node($collection_nid){
    $where=array();
    //$where[]='n.promote = 1';
    $where[]='n.status = 1'; 
    $where[]='content_field_collection_nid.field_collection_nid_nid='.$collection_nid;
    $documentos_semilla_resources_id_array=social_learning_collections_get_documentos_semilla_resources_id_array($collection_nid);    
    if(!empty($documentos_semilla_resources_id_array)){
        $where[]='content_type_collection_item.field_collec_item_resource_id_value NOT IN('.implode(',',$documentos_semilla_resources_id_array).')';
    }
    //
    $order_by_main='content_type_collection_item.field_collection_item_interest_value DESC,';
    if(isset($_REQUEST['my_order_by']) && !empty($_REQUEST['my_order_by'])){
        $my_order_by=$_REQUEST['my_order_by'];
        if($my_order_by=='score_average'){
            $order_by_main='content_type_collection_item.field_collec_item_score_average_value DESC,';
        }else if($my_order_by=='votes'){
            $order_by_main='votingapi_cache.value DESC,';
        }else if($my_order_by=='created'){
            $order_by_main='';
        }       
    }
    //
    $sql='SELECT n.nid, n.sticky, n.created 
    FROM {node} n
    LEFT JOIN {content_type_collection_item} ON n.vid=content_type_collection_item.vid
    LEFT JOIN {content_field_collection_nid} ON n.vid=content_field_collection_nid.vid 
    LEFT JOIN {votingapi_cache} votingapi_cache ON (n.nid=votingapi_cache.content_id AND votingapi_cache.function="average")
    WHERE '.implode(' AND ',$where).'
    ORDER BY '.$order_by_main.'n.created DESC,n.sticky DESC,n.nid DESC';
    return $sql;
}
function social_learning_collections_get_all_collection_items($collection_nid,$is_nid_array=0){
    $result=array();
    $sql=social_learning_collections_define_sql_collections_item_node($collection_nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        if($is_nid_array){
            $result[]=$row->nid;
        }else{
            $result[]=$row;
        }
    }
    return $result;
}
function social_learning_collections_get_all_collection_items_nid_array($collection_nid){
    return social_learning_collections_get_all_collection_items($collection_nid,1);
}
function social_learning_collections_resources_collection_callback($mode=''){
    $nid=arg(2);
    $collection_node=node_load($nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $html=array();
        $html[]=social_learning_collections_resources_define_menu_acciones($nid);
        //
        $html[]=social_learning_collections_get_collection_info_html($collection_node);
        $html[]=social_learning_collections_get_resources_collection_table($collection_node,$mode);
        //
        return implode('',$html);
    }
    //    
    return '';
}
function social_learning_collections_resources_define_menu_acciones($nid){
    $html=array();
    $html[]='<div>';
    $link_array=array();
    $link_array[]=l(t('Create Resource'),'node/add/collection-resource/'.$nid,array('query'=>'destination=social_learning/resources_collection/'.$nid,'attributes'=>array('class'=>'add'))); 
    $link_array[]=l(t('Table'),'social_learning/resources_collection/'.$nid);    
    $link_array[]=l(t('Nodes'),'social_learning/resources_nodes_collection/'.$nid);
    $link_array[]=l(t('Topics'),'social_learning/topics_collection/'.$nid);
    $link_array[]=l(t('Resource Containers'),'social_learning/feeds_collection/'.$nid);
    $link_array[]=l(t('Files'),'social_learning/files_collection/'.$nid);
    $html[]=implode('&nbsp;|&nbsp;',$link_array);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_collections_get_resources_collection_table($collection_node,$mode=''){
    $rows=array();
    $my_grupo=og_get_group_context();
    //
    $headers=array();
    $headers[]=array('data'=>t('Title'),'field'=>'title');    
    //$headers[]=array('data'=>t('Resource id'),'field'=>'resource_id','class'=>'th_nowrap');
    $headers[]=array('data'=>t('Resource'),'field'=>'resource_id','class'=>'th_nowrap');  
    $headers[]=array('data'=>'','class'=>'th_nowrap');
    //
    $my_limit=20;
    $sort='desc';
    $field='node.created';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='title';
        }else if(in_array($order,array(t('Resource id'),t('Resource')))){
            $field='content_type_collection_resource.field_social_resource_id_value';
        }        
    }
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        //$where[]='n.status=1';
        $where[]='node.type="collection_resource"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $where[]='content_field_collection_reference_nid.field_collection_reference_nid_nid='.$collection_node->nid;
        $sql='SELECT node.* 
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        LEFT JOIN {content_type_collection_resource} content_type_collection_resource ON node.vid=content_type_collection_resource.vid
        LEFT JOIN {content_field_collection_reference_nid} content_field_collection_reference_nid ON node.vid=content_field_collection_reference_nid.vid 
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        $res=db_query($sql);
        $num_rows=FALSE;
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){  
                if($mode=='nodes'){
                    $rows[]=$node;
                }else{
                    $r=array();
                    //
                    $resources_id=social_learning_collections_get_collection_resource_node_id($node);
                    //
                    $r[0]=$node->title;
                    $r[1]=$resources_id;
                    $r[2]=array('data'=>social_learning_collections_collection_resource_define_acciones($collection_node->nid,$node,$resources_id),'class'=>'td_nowrap');
                    $rows[]=$r;
                }
                $num_rows=TRUE;
            }            
        }
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //
    if($mode=='nodes'){
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    }
    //
    if ($num_rows) {
        if($mode!='nodes'){
            $output .= theme('table',$headers,$rows);
        }
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function social_learning_collections_collection_resource_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_social_resource_id','field_resource_last_upload_time','field_social_resource_status');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
    //
    $nid=hontza_get_nid_by_form($form);
    $is_edit=1;
    if(empty($nid)){
        $collection_nid=arg(3);
        $form['field_collection_reference_nid']['#default_value'][0]['nid']=$collection_nid;
        $is_edit=0;
    }
    social_learning_step_set_documento_semilla_social_tag_form_field($form,$is_edit);
    $form['field_collection_reference_nid']['#prefix']='<div style="display:none;">';
    $form['field_collection_reference_nid']['#suffix']='</div>';
    if(isset($form['buttons']['delete'])){
        $form['buttons']['delete']['#submit'][0]='social_learning_collections_delete_collection_resource_form_submit';
    } 
}
function social_learning_collections_get_collection_resource_node_id($node){
    if(isset($node->field_social_resource_id[0]['value']) && !empty($node->field_social_resource_id[0]['value'])){
        $resources_id=$node->field_social_resource_id[0]['value'];
        return $resources_id;
    }else{
        return social_learning_items_get_resource_field_social_resource_id_value($node);
    }
    return '';
}
function social_learning_collections_collection_resource_define_acciones($collection_nid,$node,$resources_id){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit Resource')),'node/'.$node->nid.'/edit',array('html'=>TRUE,'query'=>'destination=social_learning/resources_collection/'.$collection_nid));         
    $html[]=l(my_get_icono_action('viewmag',t('View Resource')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/resources_collection/'.$collection_nid));             
    $html[]=l(my_get_icono_action('delete',t('Delete Resource')),'social_learning/delete_collection_resource/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/resources_collection/'.$collection_nid));                     
    $html[]=l(my_get_icono_action('import_strategy',t('Upload Resource')),'social_learning/upload_collection_resource/'.$node->nid,array('html'=>TRUE));     
    return implode('&nbsp;',$html);
}
function social_learning_collections_upload_collection_resource_callback(){
    $nid=arg(2);
    $collection_nid='';
    social_learning_collections_upload_collection_resource($nid,$collection_nid);
    drupal_goto('social_learning/resources_collection/'.$collection_nid);
}
function social_learning_collections_get_collection_resource_collection_nid($resource_node){
    if(isset($resource_node->field_collection_reference_nid[0]['nid']) && !empty($resource_node->field_collection_reference_nid[0]['nid'])){
        $nid=$resource_node->field_collection_reference_nid[0]['nid'];
        return $nid;
    }
    return '';
}
function social_learning_collections_delete_collection_resource_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    $collection_nid='';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
        $collection_nid=social_learning_collections_get_collection_resource_collection_nid($node);
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    
    $form['resource_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'social_learning/resources_collection/'.$collection_nid);
        
    return $form;
}
function social_learning_collections_delete_collection_resource_form_submit($form,&$form_state){
    $collection_nid='';
    $nid='';
    if(isset($form_state['values']['resource_nid'])){
        $nid=$form_state['values']['resource_nid'];
    }else if(isset($form_state['values']['nid'])){
        $nid=$form_state['values']['nid'];
    }
    //
        if(!empty($nid)){
            $node=node_load($nid);            
            //
            if(isset($node->nid) && !empty($node->nid)){
                $collection_nid=social_learning_collections_get_collection_resource_collection_nid($node);
                social_learning_items_delete_collection_resource($collection_nid,$nid,$node);
            }            
        }
    drupal_goto('social_learning/resources_collection/'.$collection_nid);
}
function social_learning_collections_upload_collection_resource_postapi($node,$collection_nid){
    $url=social_learning_collections_get_collection_resource_url($node);
    $postdata_array['collection'][0]=social_learning_collections_get_collection_social_url($collection_nid);
    $postdata_array['title']=$node->title;
    $postdata_array['url']=$url;
    $postdata_array['description']=$node->body;
    $postdata_array['tags']=social_learning_simular_get_social_tags($node);
    /*echo print_r($postdata_array,1);
    exit();*/
    $postdata=json_encode($postdata_array);                
    $url_post=hontza_social_define_add_recurso_url();
    //
    $postapi_username_pass=hontza_social_define_username_pass_postapi();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD,$postapi_username_pass);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url_post);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postdata),        
));    
    $data=curl_exec($curl);
    $result=trim($data);
    curl_close($curl);
    return json_decode($result);
}
function social_learning_collections_get_collection_resource_url($node){
    if(isset($node->field_resource_url[0]['url']) && !empty($node->field_resource_url[0]['url'])){
        $url=$node->field_resource_url[0]['url'];
        return $url;
    }
    return '';
}
function social_learning_collections_get_content_type_collection_resource_array($collection_nid){
    $result=array();
    $res=db_query('SELECT *,content_field_collection_reference_nid.field_collection_reference_nid_nid FROM {content_type_collection_resource} LEFT JOIN {content_field_collection_reference_nid} ON content_type_collection_resource.vid=content_field_collection_reference_nid.vid WHERE content_field_collection_reference_nid.field_collection_reference_nid_nid=%d',$collection_nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function social_learning_collections_set_basic_resources($resources,$content_type_collection_resource_array){
    //$result=array();
    $result=$resources;
    if(!empty($content_type_collection_resource_array)){
        foreach($content_type_collection_resource_array as $i=>$row){
            if(!in_array($row->field_social_resource_id_value,$result)){
                $result[]=$row->field_social_resource_id_value;
            }
        }
        return $result;
    }
    return $resources;
}
function social_learning_collections_update_collection_resource_postapi($resource_node,$collection_nid){
    $resources_id=social_learning_collections_get_collection_resource_node_id($resource_node);
    $resource_servidor_row=social_learning_get_resources_row($resources_id);
    if(isset($resource_servidor_row->resource) && !empty($resource_servidor_row->resource)){
        $resource_servidor_row->title=$resource_node->title;
        $resource_servidor_row->description=$resource_node->body;
        $url_update=hontza_social_define_url('api/resources/'.$resources_id.'/');
        $postdata=json_encode($resource_servidor_row);
        //
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_URL, $url_update);    
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($postdata)                                                                       
        ));               
        $data=curl_exec($curl);
        $result=json_decode(trim($data));
        $result=social_learning_items_repasar_post_result($result,$data);
        curl_close($curl);
        $resource_node->field_resource_last_upload_time[0]['value']=time();        
        $resource_node->field_social_resource_status[0]['value']=$result->status;
        node_save($resource_node);
        social_learning_collections_on_upload_documento_semilla_save_collection($collection_nid);
        return $result;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_collections_is_documento_semilla($resources_id,$collection_nid,$collection_nid_value_array=''){
    if(empty($collection_nid)){
        if(!empty($collection_nid_value_array)){
            foreach($collection_nid_value_array as $i=>$r){
                $collection_nid=$r['nid'];
                if(social_learning_collections_collection_item_is_documento_semilla($resources_id,$collection_nid)){
                    return 1;
                }
            }
        }        
    }else{    
        return social_learning_collections_collection_item_is_documento_semilla($resources_id,$collection_nid);
    }    
    return 0;
}
function social_learning_collections_get_documentos_semilla_resources_id_array($collection_nid){
    $result=array();
    $content_type_collection_resource_array=social_learning_collections_get_content_type_collection_resource_array($collection_nid);
    if(!empty($content_type_collection_resource_array)){
        foreach($content_type_collection_resource_array as $i=>$row){
            if(!empty($row->field_social_resource_id_value)){
                $result[]=$row->field_social_resource_id_value;
            }    
        }    
    }
    return $result;
}
function social_learning_collections_get_collection_item_resources_id($node){
    if(isset($node->field_collec_item_resource_id[0]['value']) && !empty($node->field_collec_item_resource_id[0]['value'])){
        $resources_id=$node->field_collec_item_resource_id[0]['value'];
        return $resources_id;
    }
    return '';
}
function social_learning_collections_get_documento_semilla_node_by_resources_id($resources_id){
    $res=db_query('SELECT * FROM {content_type_collection_resource} WHERE field_social_resource_id_value=%d',$resources_id);
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        return $node;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_collections_download_all_collection_items(){
    $collection_node_array=hontza_get_all_nodes(array('collection'));
    foreach($collection_node_array as $i=>$collection_node){
        if(isset($collection_node->nid) && !empty($collection_node->nid)){
            social_learning_collections_save_collection_items($collection_node,1);
        }
    }
}
function social_learning_collections_get_collection_info_html($collection_node,$with_fieldset=1){
        $html=array();
        if($with_fieldset){
            $html[]='<fieldset>';
            $html[]='<legend>'.t('Collection').'</legend>';
        }
        $html[]='<div>';
        $style='style="float:left;clear:both;padding:5px;"';
        $html[]='<div '.$style.'>';
        $html[]='<label style="float:left;">';
        $html[]='<b>'.t('Title').':&nbsp;</b>';
        $html[]='</label>';
        $html[]='<div style="float:left;">'.$collection_node->title.'</div>';
        $html[]='</div>';
        $html[]='</div>';
        if($with_fieldset){
            $html[]='</fieldset>';
        }
        return implode('',$html);
}
function social_learning_collections_get_last_upload_date($node){
    if(isset($node->field_last_upload_time[0]['value']) && !empty($node->field_last_upload_time[0]['value'])){
        $value=$node->field_last_upload_time[0]['value'];
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_collections_documento_semilla_web_link($node,$is_url=0,$in_vista_compacta=0){
    $url='';
    if(isset($node->field_resource_url[0]['display_url']) && !empty($node->field_resource_url[0]['display_url'])){
        //$url=$node->field_resource_url[0]['display_url'];
        $url=$node->field_resource_url[0]['url'];
        $query=$node->field_resource_url[0]['query'];
        if(!empty($query)){
            $url.='?'.$query;
        }
    }else if(isset($node->field_resource_url[0]['url']) && !empty($node->field_resource_url[0]['url'])){
        $url=$node->field_resource_url[0]['url'];        
    }
    if(empty($url) || hontza_is_nolink($url)){
        $url='no_existe_enlace_origen';    
    }    
    if($is_url){
        return $url;
    }
    $label='';
    //$label=t('Web');
    $attributes=array('target'=>'_blank','title'=>t('Web'),'alt'=>t('Web'));
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    return l($label,$url,array('attributes'=>$attributes));
}
function social_learning_collections_documento_semilla_upload_link($node){
    $label='';
    //$label=t('Web');
    $attributes=array('title'=>t('Upload Resource'),'alt'=>t('Upload Resource'));
    //$attributes['target']='_blank';
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    $url='social_learning/upload_collection_resource/'.$node->nid;
    return l($label,$url,array('attributes'=>$attributes));
}
function social_learning_collections_define_collection_options(){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $groups=array();
        $groups[]=$my_grupo->nid;
        $collection_node_array=hontza_get_all_nodes(array('collection'), $groups);
        if(!empty($collection_node_array)){
            foreach($collection_node_array as $i=>$collection_node){
                $result[$collection_node->nid]=$collection_node->title;
            }        
        }
    }
    return $result;
}
function social_learning_collections_collection_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_social_collection_id','field_collection_rss_test','field_last_upload_time','field_colle_res_last_upload_time',
    'field_res_last_download_time','field_coll_news_last_upload_time','field_collection_status');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
    if(isset($form['buttons']['delete'])){
        $form['buttons']['delete']['#submit'][0]='social_learning_collections_delete_collection_form_submit';
    }   
}
function social_learning_collections_unset_form_field(&$form,$fields_array){
    if(!empty($fields_array)){
        foreach($fields_array as $i=>$f){
            if(isset($form[$f])){
                unset($form[$f]);
            }
        }
    }
}
function social_learning_collections_collection_filtro_temporal_callback(){
    $collection_nid=arg(2);
    return social_learning_collections_collection_filtro_temporal($collection_nid);
}
function social_learning_collections_collection_filtro_temporal($collection_nid){
    $node=node_load($collection_nid);
    $collection_id=social_learning_collections_get_collection_id($node);
    if(!empty($collection_id)){
        $output='';
        if(!is_crear_canal_filtro_rss()){
            $output.=social_learning_collections_create_filtro_temporal_fieldset($collection_nid);
        }
        $rows=array();
        $my_limit=10;
        $num_rows = FALSE;
        $collection_temporal_array=social_learning_collections_create_collection_temporal($node);        
        $collection_temporal_array=my_set_estrategia_pager($collection_temporal_array,$my_limit);
        $_SESSION['collection_temporal_array']=$collection_temporal_array;
        foreach($collection_temporal_array as $i=>$node){
            $output.=node_view($node,TRUE);
            $num_rows = TRUE;
        }
        //
        if ($num_rows) {
            $output .= theme('pager', NULL, $my_limit);
        }
        else {
            $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
        }
        return $output;
    }else{
        return 'No se pueden descargar noticias desde esta colección porque todavía no tiene asignado "Collection id"';
    }         
}
function social_learning_collections_create_collection_temporal($collection_node){
    global $user;
    $result=array();
    //$url=social_learning_collections_get_collection_url_rss('',$collection_node,'json');
    $url=social_learning_collections_create_filtro_url('json');
    if(!empty($url)){
        $content=file_get_contents($url);
        $content=trim($content);
        /*
         $collection_servidor=json_decode($content);
         //
         if(isset($collection_servidor->resources) && !empty($collection_servidor->resources)){        
            foreach($collection_servidor->resources as $i=>$row){*/
        $resources=json_decode($content);
        if(!empty($resources)){        
            foreach($resources as $i=>$row){        
                if(!empty($row->title)){
                    //if(social_learning_collections_is_filtro_resultado_in_collection($collection_node,$row)){ 
                        $resources_id=social_learning_get_resource_id_by_url($row->resource);
                        if(!social_learning_collections_is_documento_semilla($resources_id,$collection_node->nid)){
                            $collection_temporal=new stdClass();
                            $collection_temporal=clone $row;
                            $collection_temporal->status=1;
                            $collection_temporal->type='collection_temporal';
                            $collection_temporal->content=array();
                            $collection_temporal->content['body']['#value']=$row->description;
                            $collection_temporal->created=time();
                            $collection_temporal->resources_id=$resources_id;
                            $collection_temporal->uid=$user->uid;
                            $result[]=$collection_temporal;
                        }
                    //}    
                }    
            }
        }       
    }
    return $result;
}
function social_learning_collections_create_filtro_temporal_fieldset($collection_nid){
    global $base_url;
    $html=array();
    //$url=$base_url.url('social_learning/simular_collection_create_rss/'.$collection_nid);
    //$url=social_learning_collections_get_collection_url_rss($collection_nid);
    $url_rss=social_learning_collections_create_filtro_url('rss');
    $url_json=social_learning_collections_create_filtro_url('json');
    $div_style='style="padding:10px;"';
    $html[]='<fieldset>';
    $html[]='<legend>'.t('Create RSS').'</legend>';
    $html[]='<div '.$div_style.'><label><b>'.t('JSON').':</b></label><input type="text" value="'.$url_json.'" style="width:90%;" readonly="readonly"/></div>';        
    $html[]='<div '.$div_style.'><label><b>'.t('RSS').':</b></label><input type="text" value="'.$url_rss.'" style="width:90%;" readonly="readonly"/></div>';    
    //$html[]='<div '.$div_style.'>'.l(t('Create RSS'),'social_learning/simular_collection_create_rss/'.$collection_nid,array('attributes'=>array('target'=>'_blank'))).'</div>';
    $html[]='<div '.$div_style.'>'.l(t('Create RSS'),$url_rss,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank'))).'</div>';    
    $html[]='</fieldset>';
    return implode('',$html);
}
function social_learning_collections_collection_temporal_web_link($node){
    $url=$node->url;
    if(empty($url) || hontza_is_nolink($url)){
        $url='no_existe_enlace_origen';    
    }    
    if($is_url){
        return $url;
    }
    $label='';
    //$label=t('Web');
    $attributes=array('target'=>'_blank','title'=>t('Web'),'alt'=>t('Web'));
    if($in_vista_compacta){
        $attributes['style']='padding-right:0px;';
    }
    return l($label,$url,array('attributes'=>$attributes));
}
function social_learning_collections_collection_temporal_view_callback(){
    $resources_id=arg(2);
    $node=social_learning_collections_get_collection_temporal_row($resources_id);
    if(isset($node->resources_id) && !empty($node->resources_id)){
        drupal_set_title($node->title);
        return node_view($node,TRUE,1);
    }
    return '';
}
function social_learning_collections_get_collection_temporal_row($resources_id){
    if(isset($_SESSION['collection_temporal_array']) && !empty($_SESSION['collection_temporal_array'])){
        foreach($_SESSION['collection_temporal_array'] as $i=>$node){
            if($node->resources_id==$resources_id){
                return $node;
            }
        }
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_collections_get_last_resource_upload_date($node){
    $row=social_learning_collections_content_type_collection_row($node);
    if(isset($row->field_colle_res_last_upload_time_value) && !empty($row->field_colle_res_last_upload_time_value)){
        $value=$row->field_colle_res_last_upload_time_value;
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_collections_get_documento_semilla_last_upload_date($node){
    if(isset($node->field_resource_last_upload_time[0]['value']) && !empty($node->field_resource_last_upload_time[0]['value'])){
        $value=$node->field_resource_last_upload_time[0]['value'];
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_collections_resources_nodes_collection_callback(){
    return social_learning_collections_resources_collection_callback('nodes');
}
function social_learning_collections_get_documento_semilla_resumen($node){
    return hontza_content_resumen($node);
}
function social_learning_collections_documento_semilla_edit_link($node){
    $collection_nid=social_learning_collections_get_collection_resource_collection_nid($node);
    return hontza_item_edit_link($node,'destination=social_learning/resources_nodes_collection/'.$collection_nid);
}
function social_learning_collections_documento_semilla_delete_link($node){
    $label='';
    $collection_nid=social_learning_collections_get_collection_resource_collection_nid($node);
    return l($label,'social_learning/delete_collection_resource/'.$node->nid,array('query'=>'destination=social_learning/resources_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Delete Resource'),'alt'=>t('Delete Resource'))));
}
function social_learning_collections_get_collection_temporal_resumen($node,$len=150,$is_cortar=1){
    $value=$node->description;    
    $result=strip_tags($value);
    $result=trim($result);
    if($is_cortar){
        if(strlen($result)>$len){
            $result=drupal_substr($result, 0, $len); 
            $result.='...';
            return $result;
        }
    }
    return $result;
}
function social_learning_collections_nodes_callback(){
    return social_learning_collections_callback('nodes');
}
function social_learning_collections_get_collection_resumen($node){
    return hontza_content_resumen($node);
}
function social_learning_collections_on_upload_documento_semilla_save_collection($collection_nid,$type=''){
    $collection_node=node_load($collection_nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $my_time=time();
        db_query('UPDATE {content_type_collection} SET field_colle_res_last_upload_time_value=%d WHERE nid=%d AND vid=%d',$my_time,$collection_node->nid,$collection_node->vid);
        if($type=='upload'){
            social_learning_items_save_collection_status_with_basic_start($collection_node);
        }    
    }    
}
function social_learning_collections_collection_edit_link($node){
    return hontza_item_edit_link($node,'destination=social_learning/collections_nodes');
}
function social_learning_collections_collection_delete_link($node){
    $label='';
    $collection_nid=arg(2);
    return l($label,'social_learning/delete_collection/'.$node->nid,array('query'=>'destination=social_learning/collections_nodes','attributes'=>array('title'=>t('Delete Collection'),'alt'=>t('Delete Collection'))));
}
function social_learning_collections_get_last_resource_download_date($node){
    $row=social_learning_collections_content_type_collection_row($node);
    if(isset($row->field_res_last_download_time_value) && !empty($row->field_res_last_download_time_value)){
        $value=$row->field_res_last_download_time_value;
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_collections_collection_update_last_download_time($collection_node){
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $my_time=time();
        db_query('UPDATE {content_type_collection} SET field_res_last_download_time_value=%d WHERE nid=%d AND vid=%d',$my_time,$collection_node->nid,$collection_node->vid);
    }
}
function social_learning_collections_collection_resources_link($node){
    $label='';
    return l($label,'social_learning/resources_collection/'.$node->nid,array('query'=>'destination=social_learning/collections_nodes','attributes'=>array('title'=>t('Resources'),'alt'=>t('Resources'))));                
}
function social_learning_collections_collection_upload_link($node){
    $label='';
    return l($label,'social_learning/upload_collection/'.$node->nid,array('query'=>'destination=social_learning/collections_nodes','attributes'=>array('title'=>t('Upload Collection'),'alt'=>t('Upload Collection'))));         
}
function social_learning_collections_collection_item_download_link($node){
    $label='';
    return l($label,'social_learning/download_collection_items/'.$node->nid,array('attributes'=>array('title'=>t('Download collection items'),'alt'=>t('Download collection items'))));             
}
function social_learning_collections_collection_results_link($node){
    $label='';
    return l($label,'social_learning/results_download_collection_items/'.$node->nid,array('attributes'=>array('title'=>t('Results'),'alt'=>t('Results'))));
}
function social_learning_collections_collection_filtro_rss_link($node){
    $label='';
    return l($label,'social_learning/create_rss/'.$node->nid,array('attributes'=>array('title'=>t('Create RSS'),'alt'=>t('Create RSS'))));
}
function social_learning_collections_get_collection_url_rss($collection_nid,$collection_node_in='',$format='rss'){
    if(empty($collection_nid)){
        $collection_node=clone $collection_node_in;
    }else{
        $collection_node=node_load($collection_nid);
    }
    //
    $collection_id=social_learning_collections_get_collection_id($collection_node);
    if(!empty($collection_id)){
        $url=hontza_social_define_url('api/collections/'.$collection_id.'/?format='.$format);
        return $url;
    }
    return '';
}    
function social_learning_collections_get_collection_social_url($collection_nid){
    $collection_id=social_learning_collections_get_collection_id('',$collection_nid);
    $url=hontza_social_define_url('api/collections/'.$collection_id.'/');
    return $url;
}
function social_learning_collections_create_filtro_temporal_query($form_state_values,$collection_nid){
    $result=array();
    $field_array=social_learning_collections_define_filtro_field_array();
    foreach($field_array as $i=>$field){
        if($field=='collection'){
            $result[]=$field.'='.social_learning_collections_create_filtro_temporal_collection_values($form_state_values,$collection_nid,$field);
        }else{
            if(isset($form_state_values[$field]) && !empty($form_state_values[$field])){
                $result[]=$field.'='.$form_state_values[$field];    
            }
        }    
    }
    return implode('&',$result);
}
function social_learning_collections_create_filtro_url($format){
    $query_array=array();
    $field_array=social_learning_collections_define_filtro_field_array();
    foreach($field_array as $i=>$field){
        if(isset($_REQUEST[$field]) && !empty($_REQUEST[$field])){
            $query_array[]=$field.'='.$_REQUEST[$field];
        }
    }
    $query_array[]='format='.$format;
    $query=implode('&',$query_array);
    $url=hontza_social_define_url('search?'.$query);
    return $url;
}
function social_learning_collections_define_filtro_field_array(){
    $result=array('title','description','content','language','site','interest','interest_hontza','topic','relevance','socialnetwork','username','collection');
    return $result;
}
function social_learning_collections_is_filtro_resultado_in_collection($collection_node,$row){
    if(isset($row->collection) && !empty($row->collection)){
        $konp_collection_id=social_learning_collections_get_collection_id($collection_node);
        foreach($row->collection as $i=>$collection){
            $collection_id=social_learning_collections_get_collection_id_by_url($collection);
            if($collection_id==$konp_collection_id){
                return 1;
            }
        }
    }
    return 0;
}
function social_learning_collections_define_language_options(){
    $result=array();
    $result['']='';            
    $result['es']='ES';
    $result['en']='EN';
    $result['fr']='FR';
    return $result;
}
function social_learning_collections_define_social_network_options(){
    $result=array();
    $result['']='';            
    $result['twitter']='Twitter';
    $result['youtube']='Youtube';
    $result['delicious']='Delicious';
    $result['slideshare']='Slideshare';
    return $result;
}
function social_learning_collections_upload_item_rating($nid,$resources_id_in=''){
    $node=node_load($nid);    
    if(isset($node->nid) && !empty($node->nid)){        
        if($node->type=='collection_item'){
            $resources_id=social_learning_collections_get_collection_item_resources_id($node);
        }else if($node->type=='collection_resource'){
            $resources_id=social_learning_collections_get_collection_resource_node_id($node);            
        }
        if(empty($resources_id)){
            $resources_id=$resources_id_in;
        }
        $value=hontza_get_node_puntuacion_media_para_txt($nid,1);
        if(empty($value)){
            $value=0;
        }
        //
        $interest_servidor_row=social_learning_collections_get_interest_servidor_row($resources_id);
        $interest_servidor_row->interest_hontza=$value;
        $url_update=social_learning_collections_define_update_interest_url($resources_id);        
        $postdata=json_encode($interest_servidor_row);
        //
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_URL, $url_update);    
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($postdata)                                                                       
        ));               
        $data=curl_exec($curl);
        $result=json_decode(trim($data));
        curl_close($curl);
        //echo 'postapi='.print_r($result,1);exit();
        drupal_set_message($node->title.': '.t('Interest uploaded').' ('.date('Y-m-d H:i:s').')');
        if($node->type=='collection_item'){
            social_learning_items_on_collection_item_upload($node);
        }
    }    
}
function social_learning_collections_get_interest_servidor_row($resources_id){
    $url=social_learning_collections_define_update_interest_url($resources_id);
    $content=file_get_contents($url);
    $content=trim($content);
    $result=json_decode($content);
    return $result;
}
function social_learning_collections_define_update_interest_url($resources_id){
    $url=hontza_social_define_url('update/interest/'.$resources_id.'/');
    return $url;
}
function social_learning_collections_is_pantalla($pantalla){
    $param0=arg(0);
    if(!empty($param0) && $param0=='social_learning'){
        $param1=arg(1);
        if(!empty($param1) && $param1==$pantalla){
            return 1;
        }
    }
    return 0;
}
function social_learning_collections_get_collection_nid($nid,$node_in=''){
    $node=social_learning_items_get_collection_node_by_params($nid,$node_in);
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='collection_item'){
            return social_learning_collections_get_collection_item_collection_nid($node);
        }
    }
    return '';
}
function social_learning_collections_get_collection_item_collection_nid($node){
    if(isset($node->field_collection_nid[0]['nid']) && !empty($node->field_collection_nid[0]['nid'])){                
        return $node->field_collection_nid[0]['nid'];
    }
    return '';
}
function social_learning_collections_create_filtro_temporal_collection_values($form_state_values,$collection_nid,$field){
    $result=array();
    $collection_nid_array=array();
    if(isset($form_state_values[$field]) && !empty($form_state_values[$field])){
        $collection_nid_array=array_keys($form_state_values[$field]);
    }else{
        $collection_nid_array[]=$collection_nid;        
    }
    //
    if(!empty($collection_nid_array)){
        foreach($collection_nid_array as $i=>$collection_nid_value){
            $collection_id=social_learning_collections_get_collection_id('',$collection_nid_value);
            if(!empty($collection_id)){
                $result[]=$collection_id;
            }    
        }
        return implode(',',$result);
    }
    return '';
}
function social_learning_collections_get_import_table_html(){
    if(hontza_social_is_activado()){
        if(isset($_REQUEST['collection_nid']) && !empty($_REQUEST['collection_nid'])){
            $collection_nid=$_REQUEST['collection_nid'];
            $output='<div>&nbsp;</div>';
            $output.=social_learning_collections_collection_filtro_temporal($collection_nid);
            return $output;
        }
    }    
    return '';
}
function social_learning_collections_is_import_table_html(){
    if(is_crear_canal_filtro_rss()){
        if(isset($_REQUEST['collection_nid']) && !empty($_REQUEST['collection_nid'])){
            return 1;
        }
    }
    return 0;
}
function social_learning_collections_collection_item_is_documento_semilla($resources_id,$collection_nid){
    $content_type_collection_resource_array=social_learning_collections_get_content_type_collection_resource_array($collection_nid);
    if(!empty($content_type_collection_resource_array)){
        foreach($content_type_collection_resource_array as $i=>$row){
            if($row->field_social_resource_id_value==$resources_id){
                return 1;
            }
        }    
    }    
}
function social_learning_collections_get_field_collection_nid_multiple_value_array_by_collection($collection_array,$collection_node){
    $result=array();
    $nid_array=array();
    $nid_array[0]=$collection_node->nid;
    if(!empty($collection_array)){
        foreach($collection_array as $i=>$collection_url){
            $collection_id=social_learning_collections_get_collection_id_by_url($collection_url);
            $my_collection_node=social_learning_items_get_collection_node_by_collection_id($collection_id);
            if(isset($my_collection_node->nid) && !empty($my_collection_node->nid)){
                if(!in_array($my_collection_node->nid,$nid_array)){
                    $nid_array[]=$my_collection_node->nid;
                }
            }
        }
    }
    $result=social_learning_collections_set_nid_value_array($nid_array);
    return $result;
}
function social_learning_collections_set_nid_value_array($nid_array){
    $result=array();
    if(!empty($nid_array)){
        foreach($nid_array as $i=>$nid){
            $result[$i]['nid']=$nid;
        }
    }
    return $result;
}
function social_learning_collections_item_delete_link($node){
    $label='';
    $collection_nid=social_learning_collections_get_collection_item_collection_nid($node);
    return l($label,'node/'.$node->nid.'/delete',array('query'=>'destination=social_learning/results_download_collection_items/'.$collection_nid,'attributes'=>array('title'=>t('Delete Collection Item'),'alt'=>t('Delete Collection Item'))));
}
function social_learning_collections_upload_collection_resource($nid,&$collection_nid){
    $collection_nid='';
    $new_resources_id='';
    $resource_node=node_load($nid);
    if(isset($resource_node->nid) && !empty($resource_node->nid)){
        $collection_nid=social_learning_collections_get_collection_resource_collection_nid($resource_node);    
        $resources_id=social_learning_collections_get_collection_resource_node_id($resource_node);
        if(empty($resources_id)){
            $result=social_learning_collections_upload_collection_resource_postapi($resource_node,$collection_nid);
            $new_resources_id=social_learning_get_resource_id_by_url($result->resource);
            if(!empty($new_resources_id)){
                $resource_node->field_social_resource_id[0]['value']=$new_resources_id;
                $resource_node->field_resource_last_upload_time[0]['value']=time();
                $resource_node->field_social_resource_status[0]['value']=$result->status;
                node_save($resource_node);
                social_learning_collections_on_upload_documento_semilla_save_collection($collection_nid,'upload');
            }            
        }else{
            social_learning_collections_update_collection_resource_postapi($resource_node,$collection_nid);
        }
        social_learning_collections_upload_item_rating($nid,$new_resources_id);
    }
}    