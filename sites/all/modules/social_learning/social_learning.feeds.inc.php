<?php
function social_learning_feeds_collection_callback($mode=''){
    $nid=arg(2);
    $collection_node=node_load($nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $html=array();
        $html[]=social_learning_feeds_collection_define_menu_acciones($nid);
        //
        $html[]=social_learning_collections_get_collection_info_html($collection_node);
        $html[]=social_learning_feeds_get_feeds_collection_table($collection_node,$mode);
        //
        return implode('',$html);
    }
    //    
    return '';
}
function social_learning_feeds_collection_define_menu_acciones($nid){
    $html=array();
    $html[]='<div>';
    $link_array=array();
    $link_array[]=l(t('Create Resource Container'),'node/add/collection-feed/'.$nid,array('query'=>'destination=social_learning/feeds_collection/'.$nid,'attributes'=>array('class'=>'add'))); 
    $link_array[]=l(t('Table'),'social_learning/feeds_collection/'.$nid);  
    $link_array[]=l(t('Nodes'),'social_learning/feeds_nodes_collection/'.$nid);
    $html[]=implode('&nbsp;|&nbsp;',$link_array);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_feeds_get_feeds_collection_table($collection_node,$mode){
    $rows=array();
    drupal_set_title(t('Resource Containers'));
    $my_grupo=og_get_group_context();
    //
    $headers=array();
    $headers[]=array('data'=>t('Title'),'field'=>'title');    
    $headers[]=array('data'=>t('Feed id'),'field'=>'resource_container_id','class'=>'th_nowrap');  
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
        }else if(in_array($order,array(t('Container id'),t('Container'),t('Feed id')))){
            $field='content_type_collection_feed.field_resource_container_id_value';
        }        
    }
    //
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="collection_feed"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $where[]='content_field_feed_collection_nid.field_feed_collection_nid_nid='.$collection_node->nid;
        $sql='SELECT node.* 
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        LEFT JOIN {content_type_collection_feed} content_type_collection_feed ON node.vid=content_type_collection_feed.vid
        LEFT JOIN {content_field_feed_collection_nid} content_field_feed_collection_nid ON node.vid=content_field_feed_collection_nid.vid 
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        //
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
                    $resource_containers_id=social_learning_feeds_get_collection_feed_node_id($node);
                    //
                    $r[0]=$node->title;
                    $r[1]=$resource_containers_id;
                    $r[2]=array('data'=>social_learning_feeds_collection_feed_define_acciones($collection_node->nid,$node),'class'=>'td_nowrap');
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
function social_learning_feeds_collection_feed_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_resource_container_id','field_feed_last_upload_time');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
    //
    $nid=hontza_get_nid_by_form($form);
    if(empty($nid)){
        $collection_nid=arg(3);
        $form['field_feed_collection_nid']['#default_value'][0]['nid']=$collection_nid;
    }
    $form['field_feed_collection_nid']['#prefix']='<div style="display:none;">';
    $form['field_feed_collection_nid']['#suffix']='</div>';
    if(isset($form['buttons']['delete'])){
        $form['buttons']['delete']['#submit'][0]='social_learning_feeds_delete_collection_feed_form_submit';
    }    
}
function social_learning_feeds_get_collection_feed_node_id($node){
    if(isset($node->field_resource_container_id[0]['value']) && !empty($node->field_resource_container_id[0]['value'])){
        return $node->field_resource_container_id[0]['value'];
    }else{
        $content_type_collection_feed_row=social_learning_feeds_get_content_type_collection_feed_row($node);
        if(isset($content_type_collection_feed_row->field_resource_container_id_value)){
            return $content_type_collection_feed_row->field_resource_container_id_value;
        }
    }
    return '';
}
function social_learning_feeds_collection_feed_define_acciones($collection_nid,$node){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit Resource Container')),'node/'.$node->nid.'/edit',array('html'=>TRUE,'query'=>'destination=social_learning/feeds_collection/'.$collection_nid));         
    $html[]=l(my_get_icono_action('viewmag',t('View Resource Container')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/feeds_collection/'.$collection_nid));             
    $html[]=l(my_get_icono_action('delete',t('Delete Resource Container')),'social_learning/delete_collection_feed/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/feeds_collection/'.$collection_nid));                         
    $html[]=l(my_get_icono_action('import_strategy',t('Upload Resource Container')),'social_learning/upload_collection_feed/'.$node->nid,array('html'=>TRUE));         
    return implode('&nbsp;',$html);  
}
function social_learning_feeds_upload_collection_feed_callback(){
    $nid=arg(2);
    $collection_nid='';    
    social_learning_feeds_upload_collection_feed($nid,$collection_nid);
    drupal_goto('social_learning/feeds_collection/'.$collection_nid);
}
function social_learning_feeds_get_collection_feed_collection_nid($feed_node){
    if(isset($feed_node->field_feed_collection_nid[0]['nid']) && !empty($feed_node->field_feed_collection_nid[0]['nid'])){
        return $feed_node->field_feed_collection_nid[0]['nid'];
    }
    return '';
}
function social_learning_feeds_upload_collection_feed_postapi($node,$collection_nid){
    $url_upload=social_learning_feeds_upload_feed_url();
    $postapi_username_pass=hontza_social_define_username_pass_postapi();    
    //
    $postdata_array=array();
    $postdata_array['collection'][0]=social_learning_collections_get_collection_social_url($collection_nid);
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata_array['url']=social_learning_feeds_get_collection_feed_url($node);
    $postdata_array['rss']=social_learning_feeds_get_collection_feed_rss($node);
    /*print $url_upload;
    echo print_r($postdata_array,1);
    exit();*/
    $postdata=json_encode($postdata_array);
    //
    $postapi_username_pass=hontza_social_define_username_pass_postapi();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD,$postapi_username_pass);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url_upload);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$postdata);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postdata),        
));    
    
    $data=curl_exec($curl);
    //print $data;exit();
    $result=json_decode(trim($data));        
    curl_close($curl);
    return $result;
}
function social_learning_feeds_upload_feed_url($feeds_id=''){
    $param='update/feed/';
    if(!empty($feeds_id)){
        $param.=$feeds_id.'/';
    }
    $result=hontza_social_define_url($param);
    return $result;
}
function social_learning_feeds_get_collection_feed_url($node){
    if(isset($node->field_resource_container_url[0]['display_url']) && !empty($node->field_resource_container_url[0]['display_url'])){        
        //return $node->field_resource_container_url[0]['url'].'?'.$node->field_resource_container_url[0]['query'];
        $url=$node->field_resource_container_url[0]['url'];
        $query=$node->field_resource_container_url[0]['query'];
        if(!empty($query)){
            $url.='?'.$query;
        }
        return $url;
    }else{
        if(isset($node->field_resource_container_url[0]['url']) && !empty($node->field_resource_container_url[0]['url'])){
            return $node->field_resource_container_url[0]['url'];
        }
    }    
    return '';
}
function social_learning_feeds_get_collection_feed_rss($node){
    if(isset($node->field_resource_container_rss[0]['display_url']) && !empty($node->field_resource_container_rss[0]['display_url'])){        
        //return $node->field_resource_container_rss[0]['url'].'?'.$node->field_resource_container_rss[0]['query'];
        $url=$node->field_resource_container_rss[0]['url'];
        $query=$node->field_resource_container_rss[0]['query'];
        if(!empty($query)){
            $url.='?'.$query;
        }
        return $url;
    }else{
        if(isset($node->field_resource_container_rss[0]['url']) && !empty($node->field_resource_container_rss[0]['url'])){
            return $node->field_resource_container_rss[0]['url'];
        }
    }    
    return '';
}
function social_learning_feeds_update_collection_feed_postapi($node,$collection_nid){
    $resource_containers_id=social_learning_feeds_get_collection_feed_node_id($node);
    $url_update=social_learning_feeds_upload_feed_url($resource_containers_id);
    //
    $postdata_array=array();
    $postdata_array['container']=social_learning_feeds_get_resource_container_social_url($resource_containers_id);
    $postdata_array['collection']=social_learning_feeds_get_collection_social_url_array($collection_nid,$resource_containers_id);
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata_array['url']=social_learning_feeds_get_collection_feed_url($node);
    $postdata_array['rss']=social_learning_feeds_get_collection_feed_rss($node);
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
    curl_close($curl);
}
function social_learning_feeds_delete_collection_feed_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    $collection_nid='';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
        $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($node);        
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    
    $form['feed_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'social_learning/feeds_collection/'.$collection_nid);
    return $form;
}
function social_learning_feeds_delete_collection_feed_form_submit($form,&$form_state){
    $collection_nid='';
    $nid='';
    if(isset($form_state['values']['feed_nid'])){
        $nid=$form_state['values']['feed_nid'];
    }else{
        $nid=$form_state['values']['nid'];        
    }    
    //
        if(!empty($nid)){
            $node=node_load($nid);            
            //
            if(isset($node->nid) && !empty($node->nid)){
                $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($node);
                social_learning_feeds_delete_collection_feed($collection_nid,$node->nid,$node);
            }            
        }
    
    drupal_goto('social_learning/feeds_collection/'.$collection_nid);
}
function social_learning_feeds_delete_feed_servidor($resource_containers_id){
    if(!empty($resource_containers_id)){
        //$url=hontza_social_define_url('update/feed/'.$resource_containers_id.'/');
        $url=hontza_social_define_url('api/feeds/'.$resource_containers_id.'/');
        social_learning_delete_object($url);
    }
}
function social_learning_feeds_nodes_collection_callback(){
    return social_learning_feeds_collection_callback('nodes');
}
function social_learning_feeds_get_collection_feed_resumen($node){
    return hontza_content_resumen($node);
}
function social_learning_feeds_collection_feed_edit_link($node){
    $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($node);
    return hontza_item_edit_link($node,'destination=social_learning/feeds_nodes_collection/'.$collection_nid);
}
function social_learning_feeds_collection_feed_delete_link($node){
    $label='';
    $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($node);
    return l($label,'social_learning/delete_collection_feed/'.$node->nid,array('query'=>'destination=social_learning/feeds_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Delete Resource Container'),'alt'=>t('Delete Resource Container'))));
}
function social_learning_feeds_collection_feed_upload_link($node){
    $label='';
    $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($node);
    return l($label,'social_learning/upload_collection_feed/'.$node->nid,array('query'=>'destination=social_learning/feeds_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Upload Resource Container'),'alt'=>t('Upload Resource Container'))));
}
function social_learning_feeds_get_collection_feed_url_html($node){
    $url=social_learning_feeds_get_collection_feed_url($node);
    if(!empty($url)){
        return l($url,$url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
    }
    return '';
}
function social_learning_feeds_get_collection_feed_rss_html($node){
    $rss=social_learning_feeds_get_collection_feed_rss($node);
    if(!empty($rss)){
        return l($rss,$rss,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
    }
    return '';
}
function social_learning_feeds_get_collection_social_url_array($collection_nid,$resource_containers_id){
    $result=array();
    $url=social_learning_collections_get_collection_social_url($collection_nid);
    $feed_servidor_row=social_learning_feeds_get_feed_servidor_row($resource_containers_id);
    if(isset($feed_servidor_row->collection) && !empty($feed_servidor_row->collection)){
        return $feed_servidor_row->collection;
    }else{
        $result[0]=$url;
    }
    return $result;
}
function social_learning_feeds_get_resource_containers_id_by_url($container){
    $url=hontza_social_define_url('api/feeds');
    $result=str_replace($url,'',$container);
    $result=trim($result,'/');
    return $result;
}
function social_learning_feeds_get_feed_servidor_row($resource_containers_id){
    $url=social_learning_feeds_get_resource_container_social_url($resource_containers_id);
    $content=file_get_contents($url);
    $content=trim($content);
    $result=json_decode($content);
    return $result;
}
function social_learning_feeds_get_resource_container_social_url($resource_containers_id){
    $url=hontza_social_define_url('api/feeds/'.$resource_containers_id.'/');
    return $url;
}
function social_learning_feeds_get_content_type_collection_feed_array($feed_nid){
    $result=array();
    $res=db_query('SELECT *,content_field_feed_collection_nid.field_feed_collection_nid_nid FROM {content_type_collection_feed} LEFT JOIN {content_field_feed_collection_nid} ON content_type_collection_feed.vid=content_field_feed_collection_nid.vid WHERE content_field_feed_collection_nid.field_feed_collection_nid_nid=%d',$feed_nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function social_learning_feeds_set_basic_feeds($feeds,$content_type_collection_feed_array){
    //$result=array();
    $result=$feeds;
    if(!empty($content_type_collection_feed_array)){
        foreach($content_type_collection_feed_array as $i=>$row){
            if(!in_array($row->field_resource_container_id_value,$result)){
                $result[]=$row->field_resource_container_id_value;
            }
        }        
        return $result;
    }
    return $feeds;
}
function social_learning_feeds_get_collection_feed_node_array($collection_nid){
    $result=array();
    $content_type_collection_feed_array=social_learning_feeds_get_content_type_collection_feed_array($collection_nid);
    if(!empty($content_type_collection_feed_array)){
        foreach($content_type_collection_feed_array as $i=>$row){
            $feed_node=node_load($row->nid);
            if(isset($feed_node->nid) && !empty($feed_node->nid)){
                $result[]=$feed_node;
            }
        }
    }
    return $result;
}
function social_learning_feeds_on_upload_feed_save_collection($collection_nid){
    $collection_node=node_load($collection_nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        social_learning_items_save_collection_status_with_basic_start($collection_node);
    }
}
function social_learning_feeds_upload_collection_feed($nid,&$collection_nid){
    $collection_nid='';
    $feed_node=node_load($nid);
    if(isset($feed_node->nid) && !empty($feed_node->nid)){
        $collection_nid=social_learning_feeds_get_collection_feed_collection_nid($feed_node);
        $resurce_containers_id=social_learning_feeds_get_collection_feed_node_id($feed_node);
        if(empty($resurce_containers_id)){
            $result=social_learning_feeds_upload_collection_feed_postapi($feed_node,$collection_nid);
            $new_resource_containers_id=social_learning_feeds_get_resource_containers_id_by_url($result->container);
            if(!empty($new_resource_containers_id)){
                $feed_node->field_resource_container_id[0]['value']=$new_resource_containers_id;
                $feed_node->field_feed_last_upload_time[0]['value']=time();
                node_save($feed_node);
                social_learning_feeds_on_upload_feed_save_collection($collection_nid);
            }            
        }else{
            social_learning_feeds_update_collection_feed_postapi($feed_node,$collection_nid);
            $feed_node->field_feed_last_upload_time[0]['value']=time();
            node_save($feed_node);  
        }        
    }
}
function social_learning_feeds_get_content_type_collection_feed_row($feed_node){
    $res=db_query('SELECT * FROM {content_type_collection_feed} WHERE nid=%d AND vid=%d',$feed_node->nid,$feed_node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();    
    return $my_result;
}
function social_learning_feeds_delete_collection_feed($collection_nid,$nid,$node){
    $resource_containers_id=social_learning_feeds_get_collection_feed_node_id($node);
    social_learning_feeds_delete_feed_servidor($resource_containers_id);
    node_delete($nid);
    $collection_node=node_load($collection_nid);
    social_learning_items_save_collection_status_with_basic_start($collection_node);
}                