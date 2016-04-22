<?php
function social_learning_items_set_item_servidor_fields($item,&$node){
    if(isset($item['collection_item_servidor']) && !empty($item['collection_item_servidor'])){
        $row=$item['collection_item_servidor'];
        //
        $collection_id_value_array=array();
        //$collection_node=social_learning_items_get_collection_node_by_item_item_servidor($row,$collection_id);
        $collection_nid_value_array=social_learning_items_get_collection_nid_array_by_item_item_servidor($row,$collection_id_value_array);
        $resource_id=social_learning_get_resource_id_by_url($row->resource);
        $interest=social_learning_collections_get_social_interest($row->interest);
        $score_average=social_learning_collections_get_score_average($row);
        $serialized_tags=social_learning_collections_get_serialized_tags($row);
        $serialized_topics=social_learning_collections_get_serialized_topics($row);
        $serialized_mentions=social_learning_collections_get_serialized_mentions($row);
        //
        $node->field_item_is_json[0]['value']=1;
        $node->field_item_collection_id=$collection_id_value_array;
        $node->field_item_collection_nid=$collection_nid_value_array;
        $node->field_item_collec_resource_id[0]['value']=$resource_id;
        $node->field_item_collection_interest[0]['value']=$interest;        
        $node->field_item_collec_score_average[0]['value']=$score_average;
        $node->field_item_collection_tags[0]['value']=$serialized_tags;
        $node->field_item_collection_topics[0]['value']=$serialized_topics;
        $node->field_item_collection_mentions[0]['value']=$serialized_mentions;
        $node->field_item_collection_status[0]['value']=$row->status;
    }
}
function social_learning_items_get_collection_node_by_item_item_servidor($item_servidor,&$collection_id){
    $collection_id='';
    if(isset($item_servidor->collection) && !empty($item_servidor->collection)){
        $collection_url=$item_servidor->collection[0];
        $collection_id=social_learning_collections_get_collection_id_by_url($collection_url);
        $collection_node=social_learning_items_get_collection_node_by_collection_id($collection_id);
        return $collection_node;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_items_get_collection_node_by_collection_id($collection_id){
    $content_type_collection=social_learning_items_get_content_type_collection_row_by_collection_id($collection_id);
    if(isset($content_type_collection->nid) && !empty($content_type_collection->nid)){
        $collection_node=node_load($content_type_collection->nid);
        return $collection_node;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_items_get_content_type_collection_row_by_collection_id($collection_id){
    $res=db_query('SELECT * FROM {content_type_collection} WHERE field_social_collection_id_value=%d',$collection_id);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_items_item_node_form_alter(&$form,&$form_state, $form_id){
    $fields_array=array('field_item_is_json','field_item_collection_id','field_item_collection_nid','field_item_collec_resource_id','field_item_collection_interest','field_item_collec_score_average',
    'field_item_collection_tags','field_item_collection_topics','field_item_collection_mentions','field_item_collection_status');
     social_learning_collections_unset_form_field($form,$fields_array);
}
function social_learning_items_get_collection_nid_array_by_item_item_servidor($item_servidor,&$collection_id_value_array){
    $result=array();
    $collection_id_value_array=array();
    $kont=0;
    if(isset($item_servidor->collection) && !empty($item_servidor->collection)){
        $collection_url=$item_servidor->collection[0];
        foreach($item_servidor->collection as $i=>$collection_url){
            $collection_id=social_learning_collections_get_collection_id_by_url($collection_url);
            $collection_node=social_learning_items_get_collection_node_by_collection_id($collection_id);
            $collection_id_value_array[$i]['value']=$collection_id;
            if(isset($collection_node->nid) && !empty($collection_node->nid)){
                $result[$kont]['nid']=$collection_node->nid;
                $kont++;
            }     
        }    
    }
    return $result;
}
function social_learning_items_is_empty_title($row){
    if(isset($row->title) && !empty($row->title)){
        return 0;
    }
    return 1;
}
function social_learning_items_get_item_resources_id($node){
    if(isset($node->field_item_collec_resource_id[0]['value']) && !empty($node->field_item_collec_resource_id[0]['value'])){
        return $node->field_item_collec_resource_id[0]['value'];
    }
    return '';
}
function social_learning_items_get_item_interest_value($node){
    if(isset($node->field_item_collection_interest[0]['value']) && !empty($node->field_item_collection_interest[0]['value'])){
        return $node->field_item_collection_interest[0]['value'];
    }
    return '';
}
function social_learning_items_get_item_score_average_value($node){
    if(isset($node->field_item_collec_score_average[0]['value']) && !empty($node->field_item_collec_score_average[0]['value'])){
        return $node->field_item_collec_score_average[0]['value'];
    }
    return '';
}
function social_learning_items_get_item_tags_value_html($node){
    if(isset($node->field_item_collection_tags[0]['value']) && !empty($node->field_item_collection_tags[0]['value'])){
        $value=unserialize($node->field_item_collection_tags[0]['value']);
        $result=implode(',',$value);
        return $result;
    }
    return '';
}
function social_learning_items_get_item_topics_value_html_table($node){
    return social_learning_collections_get_topics_value_html_table($node,'field_item_collection_topics');
}
function social_learning_items_get_item_mentions_value_html_table($node){
    return social_learning_collections_get_mentions_value_html_table($node,'field_item_collection_mentions');
}
function social_learning_items_insert_item_interest_hontza_by_feed_item($node,$item){
    if(isset($item['collection_item_servidor']->interest_servidor_row) && isset($item['collection_item_servidor']->interest_servidor_row->interest_hontza)){
        $value=$item['collection_item_servidor']->interest_servidor_row->interest_hontza;
        if(!empty($value)){
            social_learning_items_insert_votingapi($node,$value);
        }
    }    
}
function social_learning_items_insert_collection_item_interest_hontza($node,$resources_id,$servidor_row){
    /*$interest_servidor_row=social_learning_collections_get_interest_servidor_row($resources_id);
    if(isset($interest_servidor_row->interest_hontza) && !empty($interest_servidor_row->interest_hontza)){
        $value=$interest_servidor_row->interest_hontza;*/
    if(isset($servidor_row->interest_hontza) && !empty($servidor_row->interest_hontza)){
        $value=$servidor_row->interest_hontza;
        if(!empty($value)){
            social_learning_items_insert_votingapi($node,$value);
        }
    }
}
function social_learning_items_insert_votingapi_vote($node,$value,&$timestamp){
    $content_type='node';
    $content_id=$node->nid;
    $value_type='percent';
    $tag='vote';
    $uid=1;
    $timestamp=time();
    $vote_source = ip_address();
    db_query('INSERT INTO {votingapi_vote}(content_type,content_id,value,value_type,tag,uid,timestamp,vote_source) VALUES("%s",%d,%f,"%s","%s",%d,%d,"%s")',$content_type,$content_id,$value,$value_type,$tag,$uid,$timestamp,$vote_source);
}
function social_learning_items_insert_votingapi_cache($node,$value_in,$timestamp){
    $content_type='node';
    $content_id=$node->nid;
    $value_type='percent';
    $tag='vote';
    $timestamp=time();
    //
    $my_function='count';
    $value=1;
    //
    social_learning_items_insert_into_votingapi_cache($content_type,$content_id,$value,$value_type,$tag,$my_function,$timestamp);
    //
    $my_function='average';
    $value=$value_in;
    social_learning_items_insert_into_votingapi_cache($content_type,$content_id,$value,$value_type,$tag,$my_function,$timestamp);    
}
function social_learning_items_insert_into_votingapi_cache($content_type,$content_id,$value,$value_type,$tag,$my_function,$timestamp){
    db_query('INSERT INTO {votingapi_cache}(content_type,content_id,value,value_type,tag,function,timestamp) VALUES("%s",%d,%f,"%s","%s","%s",%d)',$content_type,$content_id,$value,$value_type,$tag,$my_function,$timestamp);    
}
function social_learning_items_insert_votingapi($node,$value){
    social_learning_items_insert_votingapi_vote($node,$value,$timestamp);
    social_learning_items_insert_votingapi_cache($node,$value,$timestamp);
}
function social_learning_items_save_collection_news_last_upload_date($node){
    $last_upload_time=time();
    $collection_nid=social_learning_collections_get_collection_nid('',$node);
    if(!empty($collection_nid)){
        $collection_node=node_load($collection_nid);
        if(isset($collection_node->nid) && !empty($collection_node->nid)){
            social_learning_items_save_collection_news_last_upload_time($collection_node,$last_upload_time);
        }
    }
    social_learning_items_save_collection_item_news_last_upload_time($node,$last_upload_time);
}
function social_learning_items_get_collection_node_by_params($nid,$node_in){
    if(empty($nid)){
        $result=clone $node_in;        
    }else{
        $result=node_load($nid);
    }
    return $result;
}
function social_learning_items_on_collection_item_upload($node){
    social_learning_items_save_collection_news_last_upload_date($node);
}
function social_learning_items_save_collection_news_last_upload_time($collection_node,$last_upload_time){
    db_query('UPDATE {content_type_collection} SET field_coll_news_last_upload_time_value=%d WHERE nid=%d AND vid=%d',$last_upload_time,$collection_node->nid,$collection_node->vid);
}
function social_learning_items_on_collection_item_node_insert_by_resource($collection_node,$node,$resource_id,$servidor_row){
    social_learning_collections_collection_update_last_download_time($collection_node);
    social_learning_items_insert_collection_item_interest_hontza($node,$resource_id,$servidor_row);
}
function social_learning_items_get_collection_last_news_upload_date($node){
    $row=social_learning_collections_content_type_collection_row($node);
    if(isset($row->field_coll_news_last_upload_time_value) && !empty($row->field_coll_news_last_upload_time_value)){
        $value=$row->field_coll_news_last_upload_time_value;
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_items_save_collection_item_news_last_upload_time($node,$last_upload_time){
    db_query('UPDATE {content_type_collection_item} SET field_coll_item_last_upload_time_value=%d WHERE nid=%d AND vid=%d',$last_upload_time,$node->nid,$node->vid);
}
function social_learning_items_get_collection_item_last_news_upload_date($node){    
    $row=social_learning_items_get_content_type_collection_item_row($node);
    if(isset($row->field_coll_item_last_upload_time_value) && !empty($row->field_coll_item_last_upload_time_value)){
        $value=$row->field_coll_item_last_upload_time_value;
        return date('d/m/Y H:i',$value);
    }
    return '';
}
function social_learning_items_get_content_type_collection_item_row($node_in,$nid=''){
    if(isset($node_in->nid) && !empty($node_in->nid)){
        $node=clone $node_in;
    }else{
        $node=node_load($nid);
    }
    $res=db_query('SELECT * FROM {content_type_collection_item} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function social_learning_items_get_collection_status($collection_node,$field_collection_status_value){
    $color_value=social_learning_items_get_collection_status_color($collection_node,$field_collection_status_value);
    if(!empty($color_value)){
        $title='';
        $html=array();
        $html[]='<div class="div_estrategia_'.$color_value.'" title="'.$title.'">';
        $html[]='&nbsp;';
        $html[]='</div>';    
        return implode('',$html);
    }
    return '';
}
function social_learning_items_get_collection_status_color($collection_node,$field_collection_status_value){
    $result='red';
    //$status=social_learning_items_get_collection_status_value($collection_node);
    $status=$field_collection_status_value;
    if(!empty($status)){
        switch($status){
            case 1:
                //$result='yellow';
                $result='orange';
                break;
            case 2:  
                $result='green';
                break;
            default:
                $result='red';
                break;
        }
    }
    return $result;
}
function social_learning_items_get_collection_status_value($collection_node){
    $row=social_learning_collections_content_type_collection_row($collection_node);
    if(isset($row->field_collection_status_value)){
        return $row->field_collection_status_value;
    }
    return '';
}
function social_learning_items_save_collection_status_with_results($collection_node,$status_with_results){
    if(!empty($status_with_results)){
        social_learning_items_save_collection_status($collection_node,2);
    }    
}
function social_learning_items_save_collection_status($collection_node,$status){
    db_query('UPDATE {content_type_collection} SET field_collection_status_value=%d WHERE nid=%d AND vid=%d',$status,$collection_node->nid,$collection_node->vid);
}
function social_learning_items_save_collection_status_with_basic_start($collection_node){
    $status=social_learning_items_get_collection_status_value($collection_node);
    if($status!=2){
        $status_with_basic_start=0;
        $is_with_documentos_semilla=social_learning_items_is_collection_with_documentos_semilla($collection_node->nid);
        $is_with_resource_container=social_learning_items_is_collection_with_resource_container($collection_node->nid);
        $is_with_topic=social_learning_items_is_collection_with_topic($collection_node->nid);
        $is_with_files=social_learning_items_is_collection_with_files($collection_node->nid);
        //    
        if($is_with_documentos_semilla){
           $status_with_basic_start=1;
        }
        if($is_with_resource_container){
           $status_with_basic_start=1; 
        }
        if($is_with_topic){
           $status_with_basic_start=1;  
        }
        if($is_with_files){
           $status_with_basic_start=1;  
        }
        //
        if(!empty($status_with_basic_start)){
            social_learning_items_save_collection_status($collection_node,1);
        }else{
            social_learning_items_save_collection_status($collection_node,0);
        }
    }    
}
function social_learning_items_is_collection_with_documentos_semilla($collection_nid){
    $resource_node_array=social_learning_items_get_collection_documentos_semilla_node_array($collection_nid);
    if(!empty($resource_node_array)){
        foreach($resource_node_array as $i=>$resource_node){
            $resources_id=social_learning_collections_get_collection_resource_node_id($resource_node);
            if(!empty($resources_id)){
                return 1;
            }
        }
    }
}
function social_learning_items_get_collection_documentos_semilla_node_array($collection_nid){
    $result=array();
    $content_type_collection_resource_array=social_learning_collections_get_content_type_collection_resource_array($collection_nid);
    if(!empty($content_type_collection_resource_array)){
        foreach($content_type_collection_resource_array as $i=>$row){
            $resource_node=node_load($row->nid);
            if(isset($resource_node->nid) && !empty($resource_node->nid)){
                $result[]=$resource_node;
            }
        }
    }
    return $result;
}
function social_learning_items_is_collection_with_resource_container($collection_nid){
    $feed_node_array=social_learning_feeds_get_collection_feed_node_array($collection_nid);
    if(!empty($feed_node_array)){
        foreach($feed_node_array as $i=>$feed_node){
            $resource_containers_id=social_learning_feeds_get_collection_feed_node_id($feed_node);
            if(!empty($resource_containers_id)){
                return 1;
            }
        }
    }
}
function social_learning_items_is_collection_with_topic($collection_nid){
    $topic_node_array=social_learning_topics_get_collection_topic_node_array($collection_nid);
    if(!empty($topic_node_array)){
        foreach($topic_node_array as $i=>$topic_node){
            $topics_id=social_learning_topics_get_collection_topic_node_id($topic_node);
            if(!empty($topics_id)){
                return 1;
            }
        }
    }
}
function social_learning_items_get_resource_field_social_resource_id_value($node){
    $content_type_collection_resource_row=social_learning_items_get_content_type_collection_resource_row($node);
    if(isset($content_type_collection_resource_row->field_social_resource_id_value)){
        return $content_type_collection_resource_row->field_social_resource_id_value;
    }
}
function social_learning_items_get_content_type_collection_resource_row($resource_node){
    $res=db_query('SELECT * FROM {content_type_collection_resource} WHERE nid=%d AND vid=%d',$resource_node->nid,$resource_node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();    
    return $my_result;
}
function social_learning_items_collection_delete_all($collection_nid){
    social_learning_items_collection_delete_documentos_semilla_all($collection_nid);
    social_learning_items_collection_delete_resource_containers_all($collection_nid);
    social_learning_items_collection_delete_topics_all($collection_nid);
    social_learning_items_collection_delete_files_all($collection_nid);
}
function social_learning_items_collection_delete_documentos_semilla_all($collection_nid){
    $resource_node_array=social_learning_items_get_collection_documentos_semilla_node_array($collection_nid);
    if(!empty($resource_node_array)){
        foreach($resource_node_array as $i=>$resource_node){
            social_learning_items_delete_collection_resource($collection_nid,$resource_node->nid,$resource_node);
        }
    }    
}
function social_learning_items_delete_collection_resource($collection_nid,$nid,$node){
    $resources_id=social_learning_collections_get_collection_resource_node_id($node);
    social_learning_collections_delete_resource_servidor($resources_id);
    node_delete($nid);
    $collection_node=node_load($collection_nid);
    social_learning_items_save_collection_status_with_basic_start($collection_node);    
}
function social_learning_items_collection_delete_resource_containers_all($collection_nid){
    $feed_node_array=social_learning_feeds_get_collection_feed_node_array($collection_nid);
    if(!empty($feed_node_array)){
        foreach($feed_node_array as $i=>$feed_node){
            social_learning_feeds_delete_collection_feed($collection_nid,$feed_node->nid,$feed_node);
        }
    }    
}
function social_learning_items_collection_delete_topics_all($collection_nid){
    $topic_node_array=social_learning_topics_get_collection_topic_node_array($collection_nid);
    if(!empty($topic_node_array)){
        foreach($topic_node_array as $i=>$topic_node){
            social_learning_topics_delete_collection_topic($collection_nid,$topic_node->nid,$topic_node);
        }
    }    
}
function social_learning_items_is_collection_with_files($collection_nid){
    $file_node_array=social_learning_files_get_collection_file_node_array($collection_nid);
    if(!empty($file_node_array)){
        foreach($file_node_array as $i=>$file_node){
            /*$server_file_name=social_learning_files_get_server_file_name($file_node);
            if(!empty($server_file_name)){
                return 1;
            }*/
            $files_id=social_learning_files_get_collection_file_node_id($file_node);
            $files_id=social_learning_files_repasar_files_id($files_id,$file_node);
            if(!empty($files_id)){
                return 1;
            }
        }
    }
}
function social_learning_items_get_interest_servidor_row_by_servidor_row($row){
    $result=new stdClass();
    $result->interest_hontza=0;
    $result->interest_social=0;
    $result->interest_resource=0;
    //
    if(isset($row->interest_hontza) && !empty($row->interest_hontza)){
        $result->interest_hontza=$row->interest_hontza;
    }
    if(isset($row->interest_social) && !empty($row->interest_social)){
        $result->interest_social=$row->interest_social;
    }
    if(isset($row->interest_resource) && !empty($row->interest_resource)){
        $result->interest_resource=$row->interest_resource;
    }
    return $result;
}
function social_learning_items_collection_delete_files_all($collection_nid){
    $file_node_array=social_learning_files_get_collection_file_node_array($collection_nid);
    if(!empty($file_node_array)){
        foreach($file_node_array as $i=>$file_node){
            social_learning_files_delete_collection_file($collection_nid,$file_node->nid,$file_node);
        }
    }    
}
function social_learning_items_get_collection_item_server_status_label($node){
    $status=social_learning_items_get_collection_item_server_status($node);
    return social_learning_items_get_server_status_label($status);
}
function social_learning_items_get_collection_item_server_status($node){
    if(isset($node->field_collection_item_status[0]['value']) && !empty($node->field_collection_item_status[0]['value'])){
        return $node->field_collection_item_status[0]['value'];
    }
    return '';
}
function social_learning_items_get_server_status_label($status){
    $result=social_learning_items_define_server_status_options();
    if(isset($result[$status]) && !empty($result[$status])){
        return t($result[$status]);
    }
    return '';        
}
function social_learning_items_define_server_status_options(){
    $result=array();
    $result[0]='Added';
    $result[1]='Described';
    $result[2]='Discovered';
    $result[3]='Expanded';
    return $result;
}
function social_learning_items_trim_no_json($data){
    $result='';
    $find='Content-Type: application/json';
    $pos=strpos($data,$find);
    if($pos===FALSE){
        return $data;
    }else{
        $pos=$pos+strlen($find);
        $result=substr($data,$pos);
    }
    return $result;
}
function social_learning_items_repasar_post_result($result_in,$data_in){
    if(empty($result_in)){
        $result=array();
        $data=social_learning_items_trim_no_json($data_in);
        $result=json_decode(trim($data));
        return $result;
    }
    return $result_in; 
}
function social_learning_items_get_documento_semilla_server_status_label($node){
    $status=social_learning_items_get_documento_semilla_server_status($node);
    return social_learning_items_get_server_status_label($status);
}
function social_learning_items_get_documento_semilla_server_status($node){
    if(isset($node->field_social_resource_status[0]['value']) && !empty($node->field_social_resource_status[0]['value'])){
        return $node->field_social_resource_status[0]['value'];
    }
    return '';
}
function social_learning_items_get_item_collection_server_status_label($node){
    $status=social_learning_items_get_item_collection_server_status($node);
    return social_learning_items_get_server_status_label($status);
}
function social_learning_items_get_item_collection_server_status($node){
    if(isset($node->field_item_collection_status[0]['value']) && !empty($node->field_item_collection_status[0]['value'])){
        return $node->field_item_collection_status[0]['value'];
    }
    return '';
}