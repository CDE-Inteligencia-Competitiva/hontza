<?php
function social_learning_topics_api_topics_callback(){
    $output='';
    $output.=social_learning_create_menu();
    $headers=array();
    $headers[]=array('data'=>t('id'),'field'=>'id');
    $headers[]=array('data'=>t('Name'),'field'=>'name');
    $headers[]='';
    $topics_array=social_learning_topics_get_topics_array();
    $my_limit=20;
    //
    $sort='desc';
    $field='id';
    $is_numeric=1;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('id')){
            $field='id';
        }else if($order==t('Name')){
            $field='name';
            $is_numeric=0;
        }      
    }
    //
    $rows=array();
    $num_rows = FALSE;
    if(!empty($topics_array)){
        foreach($topics_array as $i=>$r){
                $id=social_learning_topics_get_topics_id_by_url($r->topic);
                $row[0]=$id;
                $row[1]=$r->name;
                $row[2]=social_learning_topics_api_topics_define_acciones($row[0]);
                $row['id']=$id;
                $row['name']=$r->name;
                $rows[]=$row;
                $num_rows = TRUE;    
        }
    }
    
    $rows=array_ordenatu($rows,$field,$sort,$is_numeric);
    $rows=hontza_unset_array($rows,array('id','name'));
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if ($num_rows) {
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    
    return $output;
}
function social_learning_topics_get_topics_array(){
    $url=hontza_social_define_url('api/topics');
    $content=file_get_contents($url);
    $result=json_decode($content);
    return $result;
}
function social_learning_topics_api_topics_define_acciones($id){
    $html=array();
    $html[]=l(t('Relevances'),'social_learning/api/topics/'.$id);
    return implode('&nbsp;',$html);
}
function social_learning_topics_get_topics_id_by_url($topic){
    $url=hontza_social_define_url('api/topics');
    $result=str_replace($url,'',$topic);
    $result=trim($result,'/');
    return $result;
}
function social_learning_topics_api_topics_relevances_callback(){
    $id=arg(3);
    $topics_row=social_learning_topics_get_topics_row($id);
    if(isset($topics_row->name) && !empty($topics_row->name)){
        drupal_set_title(t('Relevances').': '.$topics_row->name);
    }
    $relevance_array=$topics_row->relevance;
    //    
    $output='';
    $output.=social_learning_create_menu();
    $output.=social_learning_topics_get_topic_info_html($topics_row);
    $headers=array();
    $headers[]=array('data'=>t('Resource id'),'field'=>'resource_id');
    $headers[]=array('data'=>t('Resource'),'field'=>'resource_title');
    $headers[]=array('data'=>t('Score'),'field'=>'score');
    //$headers[]='';
    $my_limit=20;
    $rows=array();
    $num_rows = FALSE;
    if(!empty($relevance_array)){
        foreach($relevance_array as $i=>$r){
                $resource_id=social_learning_get_resource_id_by_url($r->resource);
                //$resource_row=social_learning_get_resources_row($resource_id);
                $row[0]=l($resource_id,'social_learning/api/resources/'.$resource_id);
                $row[1]='';
                /*if(isset($resource_row->title) && !empty($resource_row->title)){
                    $row[1]=$resource_row->title;
                }*/
                $row[2]=$r->score;
                $row['resource_id']=$resource_id;
                $rows[]=$row;
                $num_rows = TRUE;    
        }
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=social_learning_topics_set_resource_fields($rows);
    $rows=hontza_unset_array($rows,array('resource_id'));
    //
    if ($num_rows) {
        $output .= theme('table',$headers,$rows);
        $output.=l(t('Return'),'social_learning/api/topics');
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    
    return $output;    
}
function social_learning_topics_get_topics_row($id){
     $url=hontza_social_define_url('api/topics/'.$id);
     $content=file_get_contents($url);
     $result=json_decode($content);     
     return $result;
}
function social_learning_topics_set_resource_fields($rows){
    $result=$rows;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $resource_row=social_learning_get_resources_row($row['resource_id']);
            if(isset($resource_row->title) && !empty($resource_row->title)){                    
                $result[$i][1]=$resource_row->title;
            }            
        }
    }
    return $result;
}
function social_learning_topics_get_topic_info_html($topics_row){
        $html=array();
        $html[]='<fieldset>';
        $html[]='<legend>'.t('Topic').'</legend>';
        $html[]='<div>';
        $style='style="float:left;clear:both;padding:5px;"';
        $html[]='<div '.$style.'>';
        $html[]='<label style="float:left;">';
        $html[]='<b>'.t('Name').':&nbsp;</b>';
        $html[]='</label>';
        $html[]='<div style="float:left;">'.$topics_row->name.'</div>';
        $html[]='</div>';
        $html[]='</div>';
        $html[]='</fieldset>';
        return implode('',$html);
}
function social_learning_topics_upload_collection_topic_postapi($node,$collection_nid){
    $url_upload=social_learning_topics_upload_topic_url();
    $postapi_username_pass=hontza_social_define_username_pass_postapi();    
    //
    $postdata_array=array();
    $postdata_array['collection']=social_learning_collections_get_collection_social_url($collection_nid);
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata_array['tags']=social_learning_topics_get_social_tags($node);
    /*echo print_r($postdata_array,1);
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
function social_learning_topics_upload_topic_url($topics_id=''){
    $param='update/topic/';
    if(!empty($topics_id)){
        $param.=$topics_id.'/';
    }
    $result=hontza_social_define_url($param);
    //$result=hontza_social_define_url('api/topics/');
    return $result;
}
function social_learning_topics_update_collection_topic_postapi($node,$collection_nid=''){
    $topics_id=social_learning_topics_get_collection_topic_node_id($node);
    $url_update=social_learning_topics_upload_topic_url($topics_id);
    //
    $postdata_array=array();
    $postdata_array['collection']=social_learning_collections_get_collection_social_url($collection_nid);
    $postdata_array['name']=$node->title;
    $postdata_array['description']=$node->body;
    $postdata_array['tags']=social_learning_topics_get_social_tags($node);
    /*echo print_r($postdata_array,1);
    exit();*/    
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
    /*echo 'postapi='.print_r($result,1);
    exit();*/
    /*social_learning_topics_on_upload_topic_postapi($result,$node,$collection_id);*/
}
function social_learning_topics_collection_callback($mode=''){
    $nid=arg(2);
    $collection_node=node_load($nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $html=array();
        $html[]=social_learning_topics_collection_define_menu_acciones($nid);
        //
        $html[]=social_learning_collections_get_collection_info_html($collection_node);
        $html[]=social_learning_topics_get_topics_collection_table($collection_node,$mode);
        //
        return implode('',$html);
    }
    //    
    return '';
}
function social_learning_topics_collection_define_menu_acciones($nid){
    $html=array();
    $html[]='<div>';
    $link_array=array();
    $link_array[]=l(t('Create Topic'),'node/add/collection-topic/'.$nid,array('query'=>'destination=social_learning/topics_collection/'.$nid,'attributes'=>array('class'=>'add'))); 
    $link_array[]=l(t('Table'),'social_learning/topics_collection/'.$nid);  
    $link_array[]=l(t('Nodes'),'social_learning/topics_nodes_collection/'.$nid);
    $html[]=implode('&nbsp;|&nbsp;',$link_array);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_topics_get_topics_collection_table($collection_node,$mode=''){
    $rows=array();
    drupal_set_title(t('Topics'));
    $my_grupo=og_get_group_context();
    //
    $headers=array();
    $headers[]=array('data'=>t('Title'),'field'=>'title');    
    $headers[]=array('data'=>t('Topic id'),'field'=>'topic_id','class'=>'th_nowrap');  
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
        }else if(in_array($order,array(t('Topic id'),t('Topic')))){
            $field='content_type_collection_topic.field_topic_id_value';
        }        
    }
    //
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="collection_topic"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $where[]='content_field_topic_collection_nid.field_topic_collection_nid_nid='.$collection_node->nid;
        $sql='SELECT node.* 
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        LEFT JOIN {content_type_collection_topic} content_type_collection_topic ON node.vid=content_type_collection_topic.vid
        LEFT JOIN {content_field_topic_collection_nid} content_field_topic_collection_nid ON node.vid=content_field_topic_collection_nid.vid 
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
                    $topics_id=social_learning_topics_get_collection_topic_node_id($node);
                    //
                    $r[0]=$node->title;
                    $r[1]=$topics_id;
                    $r[2]=array('data'=>social_learning_topics_collection_topic_define_acciones($collection_node->nid,$node,$topics_id),'class'=>'td_nowrap');
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
function social_learning_topics_get_collection_topic_node_id($node){
    if(isset($node->field_topic_id[0]['value']) && !empty($node->field_topic_id[0]['value'])){
        return $node->field_topic_id[0]['value'];
    }else{
        $content_type_collection_topic_row=social_learning_topics_get_content_type_collection_topic_row($node);
        if(isset($content_type_collection_topic_row->field_topic_id_value)){
            return $content_type_collection_topic_row->field_topic_id_value;
        }
    }
    return '';
}
function social_learning_topics_collection_topic_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_topic_id','field_topic_last_upload_time');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
    //
    $nid=hontza_get_nid_by_form($form);
    if(empty($nid)){
        $collection_nid=arg(3);
        $form['field_topic_collection_nid']['#default_value'][0]['nid']=$collection_nid;
        //
        $form['field_topic_social_tags'][0]['#default_value']['value']=social_learning_topics_define_social_tag_default_value();
        $form['field_topic_social_tags']['#prefix']='<div style="display:none;">';
        $form['field_topic_social_tags']['#suffix']='</div>'; 
    }
    $form['field_topic_collection_nid']['#prefix']='<div style="display:none;">';
    $form['field_topic_collection_nid']['#suffix']='</div>';    
    if(isset($form['buttons']['delete'])){
        $form['buttons']['delete']['#submit'][0]='social_learning_topics_delete_collection_topic_form_submit';
    }  
}
function social_learning_topics_collection_topic_define_acciones($collection_nid,$node,$topics_id){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit Topic')),'node/'.$node->nid.'/edit',array('html'=>TRUE,'query'=>'destination=social_learning/topics_collection/'.$collection_nid));         
    $html[]=l(my_get_icono_action('viewmag',t('View Topic')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/topics_collection/'.$collection_nid));             
    $html[]=l(my_get_icono_action('delete',t('Delete Topic')),'social_learning/delete_collection_topic/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/topics_collection/'.$collection_nid));                     
    $html[]=l(my_get_icono_action('import_strategy',t('Upload Topic')),'social_learning/upload_collection_topic/'.$node->nid,array('html'=>TRUE));         
    return implode('&nbsp;',$html);    
}
function social_learning_topics_upload_collection_topic_callback(){
    $nid=arg(2);
    $collection_nid='';
    social_learning_topics_upload_collection_topic($nid,$collection_nid);
    drupal_goto('social_learning/topics_collection/'.$collection_nid);
}
function social_learning_topics_get_collection_topic_collection_nid($topic_node){
    if(isset($topic_node->field_topic_collection_nid[0]['nid']) && !empty($topic_node->field_topic_collection_nid[0]['nid'])){
        return $topic_node->field_topic_collection_nid[0]['nid'];
    }
    return '';
}
function social_learning_topics_get_social_tags($node){
    $result=array();
    if(isset($node->field_topic_social_tags[0]['value']) && !empty($node->field_topic_social_tags[0]['value'])){
        $result=explode(',',$node->field_topic_social_tags[0]['value']);
    }
    return $result;
}
function social_learning_topics_delete_collection_topic_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    $collection_nid='';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
        $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);        
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    
    $form['topic_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'social_learning/topics_collection/'.$collection_nid);
        
    return $form;
}
function social_learning_topics_delete_collection_topic_form_submit($form,&$form_state){
    $collection_nid='';
    $nid='';
    if(isset($form_state['values']['topic_nid'])){
        $nid=$form_state['values']['topic_nid'];
    }else if(isset($form_state['values']['nid'])){
        $nid=$form_state['values']['nid'];
    }
    //
        if(!empty($nid)){
            $node=node_load($nid);            
            //
            if(isset($node->nid) && !empty($node->nid)){
                $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);
                social_learning_topics_delete_collection_topic($collection_nid,$nid,$node);
            }            
        }    
    drupal_goto('social_learning/topics_collection/'.$collection_nid);
}
function social_learning_topics_delete_topic_servidor($topics_id){
    if(!empty($topics_id)){
        $url=hontza_social_define_url('api/topics/'.$topics_id.'/');
        social_learning_delete_object($url);
    }
}
function social_learning_topics_nodes_collection_callback(){
    return social_learning_topics_collection_callback('nodes');
}
function social_learning_topics_get_collection_topic_resumen($node){
    return hontza_content_resumen($node);
}
function social_learning_topics_get_collection_topic_last_upload_date($node){
    if(isset($node->field_topic_last_upload_time[0]['value']) && !empty($node->field_topic_last_upload_time[0]['value'])){
        return $node->field_topic_last_upload_time[0]['value'];
    }
    return '';            
}
function social_learning_topics_collection_topic_edit_link($node){
    $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);
    return hontza_item_edit_link($node,'destination=social_learning/topics_nodes_collection/'.$collection_nid);
}
function social_learning_topics_collection_topic_delete_link($node){
    $label='';
    $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);
    return l($label,'social_learning/delete_collection_topic/'.$node->nid,array('query'=>'destination=social_learning/topics_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Delete Topic'),'alt'=>t('Delete Topic'))));
}
function social_learning_topics_collection_topic_upload_link($node){
    $label='';
    $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);
    return l($label,'social_learning/upload_collection_topic/'.$node->nid,array('query'=>'destination=social_learning/topics_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Upload Topic'),'alt'=>t('Upload Topic'))));
}
function social_learning_topics_get_content_type_collection_topic_array($collection_nid){
    $result=array();
    $res=db_query('SELECT *,content_field_topic_collection_nid.field_topic_collection_nid_nid FROM {content_type_collection_topic} LEFT JOIN {content_field_topic_collection_nid} ON content_type_collection_topic.vid=content_field_topic_collection_nid.vid WHERE content_field_topic_collection_nid.field_topic_collection_nid_nid=%d',$collection_nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function social_learning_topics_set_basic_topics($topics,$content_type_collection_topic_array){
    //$result=array();
    $result=$topics;
    if(!empty($content_type_collection_topic_array)){
        foreach($content_type_collection_topic_array as $i=>$row){
            if(!in_array($row->field_topic_id_value,$result)){
                $result[]=$row->field_topic_id_value;
            }
        }        
        return $result;
    }
    return $topics;
}
function social_learning_topics_get_collection_topic_node_array($collection_nid){
    $result=array();
    $content_type_collection_topic_array=social_learning_topics_get_content_type_collection_topic_array($collection_nid);
    if(!empty($content_type_collection_topic_array)){
        foreach($content_type_collection_topic_array as $i=>$row){
            $topic_node=node_load($row->nid);
            if(isset($topic_node->nid) && !empty($topic_node->nid)){
                $result[]=$topic_node;
            }
        }
    }
    return $result;
}
function social_learning_topics_upload_collection_topic($nid,&$collection_nid){
    $collection_nid='';    
    $topic_node=node_load($nid);
    if(isset($topic_node->nid) && !empty($topic_node->nid)){
        $collection_nid=social_learning_topics_get_collection_topic_collection_nid($topic_node);
        $topics_id=social_learning_topics_get_collection_topic_node_id($topic_node);
        if(empty($topics_id)){
            $result=social_learning_topics_upload_collection_topic_postapi($topic_node,$collection_nid);
            $new_topics_id=social_learning_topics_get_topics_id_by_url($result->topic);
            if(!empty($new_topics_id)){
                $topic_node->field_topic_id[0]['value']=$new_topics_id;
                $topic_node->field_topic_last_upload_time[0]['value']=time();
                node_save($topic_node);
                social_learning_topics_on_upload_topic_save_collection($collection_nid);
            }            
        }else{
            social_learning_topics_update_collection_topic_postapi($topic_node,$collection_nid);            
        }        
    }
}
function social_learning_topics_on_upload_topic_save_collection($collection_nid){
    $collection_node=node_load($collection_nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        social_learning_items_save_collection_status_with_basic_start($collection_node);
    }
}
function social_learning_topics_get_content_type_collection_topic_row($topic_node){
    $res=db_query('SELECT * FROM {content_type_collection_topic} WHERE nid=%d AND vid=%d',$topic_node->nid,$topic_node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();    
    return $my_result;
}
function social_learning_topics_define_social_tag_default_value(){
    return 'my_topic';
}
function social_learning_topics_delete_collection_topic($collection_nid,$nid,$node){
    $topics_id=social_learning_topics_get_collection_topic_node_id($node);
    social_learning_topics_delete_topic_servidor($topics_id);
    node_delete($nid);
    $collection_node=node_load($collection_nid);
    social_learning_items_save_collection_status_with_basic_start($collection_node);
}                