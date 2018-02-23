<?php
function social_learning_files_collection_callback($mode=''){
    $nid=arg(2);
    $collection_node=node_load($nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $html=array();
        $html[]=social_learning_files_collection_define_menu_acciones($nid);
        //
        $html[]=social_learning_collections_get_collection_info_html($collection_node);
        $html[]=social_learning_files_get_files_collection_table($collection_node,$mode);
        //
        return implode('',$html);
    }
    //    
    return '';
}
function social_learning_files_collection_define_menu_acciones($nid){
    $html=array();
    $html[]='<div>';
    $link_array=array();
    $link_array[]=l(t('Create File'),'node/add/collection-file/'.$nid,array('query'=>'destination=social_learning/files_collection/'.$nid,'attributes'=>array('class'=>'add'))); 
    $link_array[]=l(t('Table'),'social_learning/files_collection/'.$nid);  
    $link_array[]=l(t('Nodes'),'social_learning/files_nodes_collection/'.$nid);
    $html[]=implode('&nbsp;|&nbsp;',$link_array);
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_files_collection_file_node_form_alter(&$form,&$form_state,$form_id){
    $fields_array=array('field_collection_file_id','field_file_social_url_relativa');
    social_learning_collections_unset_form_field($form,$fields_array);
    boletin_report_unset_buttons(array('preview','preview_changes'),$form);
    //
    $nid=hontza_get_nid_by_form($form);
    if(empty($nid)){
        $collection_nid=arg(3);
        $form['field_file_collection_nid']['#default_value'][0]['nid']=$collection_nid;        
    }
    $form['field_file_collection_nid']['#prefix']='<div style="display:none;">';
    $form['field_file_collection_nid']['#suffix']='</div>';    
    if(isset($form['buttons']['delete'])){
        $form['buttons']['delete']['#submit'][0]='social_learning_files_delete_collection_file_form_submit';
    }
}
function social_learning_files_get_files_collection_table($collection_node,$mode=''){
    $rows=array();
    drupal_set_title(t('Files'));
    $my_grupo=og_get_group_context();
    //
    $headers=array();
    $headers[]=array('data'=>t('Title'),'field'=>'title');    
    $headers[]=array('data'=>t('Filename'),'field'=>'file_name','class'=>'th_nowrap');
    $headers[]=array('data'=>t('Server Filename'),'field'=>'server_path','class'=>'th_nowrap');
    $headers[]=array('data'=>t('File id'),'field'=>'file_id','class'=>'th_nowrap');
    $headers[]=array('data'=>'','class'=>'th_nowrap');
    //
    $my_limit=20;
    $sort='desc';
    $field='node.created';
    $is_numeric=1;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    $order='';
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='title';
            $is_numeric=0;
        }else if($order==t('Filename')){
            $is_numeric=0;
        }else if($order==t('Server Filename')){
            $is_numeric=0;
        }else if($order==t('File id')){
            $field='content_type_collection_file.field_collection_file_id_value';
            $is_numeric=1;            
        }         
    }
    //
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="collection_file"';
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        $where[]='content_field_file_collection_nid.field_file_collection_nid_nid='.$collection_node->nid;
        $sql='SELECT node.*,content_type_collection_file.field_collection_file_id_value AS file_id  
        FROM {node} node
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid
        LEFT JOIN {content_type_collection_file} content_type_collection_file ON node.vid=content_type_collection_file.vid
        LEFT JOIN {content_field_file_collection_nid} content_field_file_collection_nid ON node.vid=content_field_file_collection_nid.vid 
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
                    $file_name=social_learning_files_get_file_name($node);
                    $server_file_name=social_learning_files_get_server_file_name($node);
                    $url_file_local=social_learning_files_get_url_file_local($node);
                    //
                    $r[0]=$node->title;
                    //$r[1]=$file_name;
                    $r[1]=l($file_name,$url_file_local,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
                    $r[2]=$server_file_name;
                    $r[3]=$row->file_id;
                    $r[4]=array('data'=>social_learning_files_collection_file_define_acciones($collection_node->nid,$node),'class'=>'td_nowrap');
                    $r['file_name']=$file_name;
                    $r['server_file_name']=$server_file_name;
                    $rows[]=$r;
                }
                $num_rows=TRUE;
            }            
        }
    }
    //
    if(!empty($order)){
        if($order==t('Filename')){
            $rows=array_ordenatu($rows,'file_name',$sort,$is_numeric);
        }else if($order==t('Server Filename')){
            $rows=array_ordenatu($rows,'server_file_name',$sort,$is_numeric);
        }       
    }
    $rows=hontza_unset_array($rows,array('file_name','server_file_name'));
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
function social_learning_files_collection_file_define_acciones($collection_nid,$node){
    $html=array();    
    $html[]=l(my_get_icono_action('edit',t('Edit File')),'node/'.$node->nid.'/edit',array('html'=>TRUE,'query'=>'destination=social_learning/files_collection/'.$collection_nid));         
    $html[]=l(my_get_icono_action('viewmag',t('View File')),'node/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/files_collection/'.$collection_nid));             
    $html[]=l(my_get_icono_action('delete',t('Delete File')),'social_learning/delete_collection_file/'.$node->nid,array('html'=>TRUE,'query'=>'destination=social_learning/files_collection/'.$collection_nid));                     
    $html[]=l(my_get_icono_action('import_strategy',t('Upload File')),'social_learning/upload_collection_file/'.$node->nid,array('html'=>TRUE));         
    return implode('&nbsp;',$html);    
}
function social_learning_files_upload_collection_file_callback(){
   $nid=arg(2);
   $collection_nid='';
   social_learning_files_upload_collection_file($nid,$collection_nid);
   drupal_goto('social_learning/files_collection/'.$collection_nid);
}
function social_learning_files_upload_collection_file($nid,&$collection_nid){
    $collection_nid='';    
    $file_node=node_load($nid);
    if(isset($file_node->nid) && !empty($file_node->nid)){
        $collection_nid=social_learning_files_get_collection_file_collection_nid($file_node);
        $files_id=social_learning_files_get_collection_file_node_id($file_node);
        if(empty($files_id)){
            $result=social_learning_files_upload_collection_file_postapi($file_node,$collection_nid);
            $new_files_id=$result->id;
            if(!empty($new_files_id)){
                $file_node->field_collection_file_id[0]['value']=$new_files_id;
                //$topic_node->field_file_last_upload_time[0]['value']=time();
                $file_node->field_file_social_url_relativa[0]['value']=$result->source;            
                node_save($file_node);
                social_learning_files_on_upload_file_save_collection($collection_nid);
            }            
        }else{
            $result=social_learning_files_update_collection_file_postapi($file_node,$collection_nid);
            $file_node->field_file_social_url_relativa[0]['value']=$result->source;            
            node_save($file_node);                
        }        
    }
}
function social_learning_files_upload_collection_file_postapi($node,$collection_nid){
    $url_upload=social_learning_files_upload_file_url();
    $postapi_username_pass=hontza_social_define_username_pass_postapi();    
    //
    $postdata_array=array();
    //$postdata_array['collection']=social_learning_collections_get_collection_social_url($collection_nid);
    $collection_id=social_learning_collections_get_collection_id('',$collection_nid);
    $postdata_array['collection']=$collection_id;
    $postdata_array['name']=$node->title;
    //$postdata_array['description']=$node->body;
    $postdata_array['source']=social_learning_files_get_postdata_file($node);
    if(empty($postdata_array['source'])){
        return '';
    }
    //$postdata=json_encode($postdata_array);
    $postdata=$postdata_array;
    /*echo print_r($postdata,1);
    exit();*/
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
    'Content-Type: multipart/form-data',                
));        
    $data=curl_exec($curl);
    $result=json_decode(trim($data));        
    curl_close($curl);
    return $result;
}
function social_learning_files_upload_file_url($files_id=''){
    $param='update/files/';
    if(!empty($files_id)){
        $param.=$files_id.'/';
    }
    $result=hontza_social_define_url($param);
    //$result=hontza_social_define_url('api/topics/');
    return $result;
}
function social_learning_files_get_postdata_file($node){
    if(isset($node->files) && !empty($node->files)){
        $files=array_values($node->files);
        $file_row=$files[0];
        return '@'.$file_row->filepath.';type='.$file_row->filemime; 
    }
    return '';
}
function social_learning_files_get_collection_file_collection_nid($file_node){
    if(isset($file_node->field_file_collection_nid[0]['nid']) && !empty($file_node->field_file_collection_nid[0]['nid'])){
        return $file_node->field_file_collection_nid[0]['nid'];
    }
    return '';
}
function social_learning_files_on_upload_file_save_collection($collection_nid){
    $collection_node=node_load($collection_nid);
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        social_learning_items_save_collection_status_with_basic_start($collection_node);
    }
}
function social_learning_files_define_add_node_type_array($node_type_array){
   $result=array();
   if(!empty($node_type_array)){
        foreach($node_type_array as $i=>$node_type){
            $result[]=str_replace('_','-',$node_type);
        }
   }
   return $result;
}
function social_learning_files_delete_collection_file_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $node_title='Deleting';
    $collection_nid='';
    if(isset($node->nid) && !empty($node->nid)){
        $node_title=$node->title;
        $collection_nid=social_learning_files_get_collection_file_collection_nid($node);        
    }
    drupal_set_title(t('Are you sure you want to delete %node_title?', array('%node_title' =>$node_title)));
    //
    $form['file_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'social_learning/files_collection/'.$collection_nid);        
    return $form;
}
function social_learning_files_delete_collection_file_form_submit($form,&$form_state){
    $collection_nid='';
    $nid='';
    if(isset($form_state['values']['file_nid'])){
        $nid=$form_state['values']['file_nid'];
    }else if(isset($form_state['values']['nid'])){
        $nid=$form_state['values']['nid'];
    }
    //
        if(!empty($nid)){
            $node=node_load($nid);            
            //
            if(isset($node->nid) && !empty($node->nid)){
                $collection_nid=social_learning_topics_get_collection_topic_collection_nid($node);
                social_learning_files_delete_collection_file($collection_nid,$nid,$node);
            }            
        }    
    drupal_goto('social_learning/topics_collection/'.$collection_nid);
}
function social_learning_files_delete_collection_file($collection_nid,$nid,$node){
    $files_id=social_learning_files_get_collection_file_node_id($node);
    social_learning_files_delete_file_servidor($files_id);
    node_delete($nid);
    $collection_node=node_load($collection_nid);
    social_learning_items_save_collection_status_with_basic_start($collection_node);
}
function social_learning_files_nodes_collection_callback(){
    return social_learning_files_collection_callback('nodes');
}
function social_learning_files_get_collection_file_resumen($node){
    return hontza_content_resumen($node);
}
function social_learning_files_collection_file_edit_link($node){
    $collection_nid=social_learning_files_get_collection_file_collection_nid($node);
    return hontza_item_edit_link($node,'destination=social_learning/files_nodes_collection/'.$collection_nid);
}
function social_learning_files_collection_file_delete_link($node){
    $label='';
    $collection_nid=social_learning_files_get_collection_file_collection_nid($node);    
    return l($label,'social_learning/delete_collection_file/'.$node->nid,array('query'=>'destination=social_learning/files_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Delete File'),'alt'=>t('Delete File'))));
}
function social_learning_files_collection_file_upload_link($node){
    $label='';
    $collection_nid=social_learning_files_get_collection_file_collection_nid($node);        
    return l($label,'social_learning/upload_collection_topic/'.$node->nid,array('query'=>'destination=social_learning/files_nodes_collection/'.$collection_nid,'attributes'=>array('title'=>t('Upload File'),'alt'=>t('Upload File'))));
}
function social_learning_files_get_file_name($node){
    if(isset($node->files) && !empty($node->files)){
        $files_array=array_values($node->files);
        $file_row=$files_array[0];
        return $file_row->filename;
    }
    return '';
}
function social_learning_files_get_server_file_name($node,$is_basename=1){
    if(isset($node->field_file_social_url_relativa[0]['value']) && !empty($node->field_file_social_url_relativa[0]['value'])){
        $result=$node->field_file_social_url_relativa[0]['value'];
        $result=basename($result);
        return $result;
    }
    return '';
}
function social_learning_files_get_url_file_local($node){
    if(isset($node->files) && !empty($node->files)){
        $files_array=array_values($node->files);
        $file_row=$files_array[0];
        return hontza_get_url_file($file_row->filepath);
    }
    return '';
}
function social_learning_files_get_collection_file_node_array($collection_nid){
    $result=array();
    $content_type_collection_file_array=social_learning_files_get_content_type_collection_file_array($collection_nid);
    if(!empty($content_type_collection_file_array)){
        foreach($content_type_collection_file_array as $i=>$row){
            $file_node=node_load($row->nid);
            if(isset($file_node->nid) && !empty($file_node->nid)){
                $result[]=$file_node;
            }
        }
    }
    return $result;
}
function social_learning_files_get_content_type_collection_file_array($collection_nid){
    $result=array();
    $res=db_query('SELECT *,content_field_file_collection_nid.field_file_collection_nid_nid FROM {content_type_collection_file} LEFT JOIN {content_field_file_collection_nid} ON content_type_collection_file.vid=content_field_file_collection_nid.vid WHERE content_field_file_collection_nid.field_file_collection_nid_nid=%d',$collection_nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function social_learning_files_get_collection_file_node_id($file_node){
    if(isset($file_node->field_collection_file_id[0]['value']) && !empty($file_node->field_collection_file_id[0]['value'])){
        return $file_node->field_collection_file_id[0]['value'];
    }
    return '';
}
function social_learning_files_delete_file_servidor($files_id){
    if(!empty($files_id)){
        $url=hontza_social_define_url('api/files/'.$files_id.'/');
        social_learning_delete_object($url);
    }
}
function social_learning_files_update_collection_file_postapi($node,$collection_nid){
    $files_id=social_learning_files_get_collection_file_node_id($node);
    $url_update=hontza_social_define_url('api/files/'.$files_id.'/');
    //$postapi_username_pass=hontza_social_define_username_pass_postapi();    
    //
    $postdata_array=array();
    //$postdata_array['collection']=social_learning_collections_get_collection_social_url($collection_nid);
    $collection_id=social_learning_collections_get_collection_id('',$collection_nid);
    $postdata_array['collection']=$collection_id;
    $postdata_array['name']=$node->title;
    //$postdata_array['description']=$node->body;
    $postdata_array['source']=social_learning_files_get_postdata_file($node);
    if(empty($postdata_array['source'])){
        return '';
    }
    //$postdata=json_encode($postdata_array);
    $postdata=$postdata_array;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url_update);    
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); // note the PUT here
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: multipart/form-data',                                                                 
    ));   
    $data=curl_exec($curl);
    $result=json_decode(trim($data));
    $result=social_learning_items_repasar_post_result($result,$data);
    curl_close($curl);
    return $result;
}
function social_learning_files_repasar_files_id($files_id,$file_node){
    if(empty($files_id)){
        $content_type_collection_file_row=social_learning_files_get_content_type_collection_file_row($file_node);
        if(isset($content_type_collection_file_row->field_collection_file_id_value) && !empty($content_type_collection_file_row->field_collection_file_id_value)){
            return $content_type_collection_file_row->field_collection_file_id_value;
        }
    }
    return $files_id;
}
function social_learning_files_get_content_type_collection_file_row($file_node){
    $res=db_query('SELECT * FROM {content_type_collection_file} WHERE nid=%d AND vid=%d',$file_node->nid,$file_node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}