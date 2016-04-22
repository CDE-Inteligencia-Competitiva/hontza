<?php
function social_learning_step_on_form_node_save($op,&$node){
    if(social_learning_step_is_post_save()){
        if($node->type=='collection'){
            if($op=='insert'){
                $_SESSION['insert_collection_nid']=$node->nid;
            }    
        }    
    }
}
function social_learning_step_is_post_save(){
    if(isset($_POST['op']) && isset($_POST['form_id'])){
        $my_op=$_POST['op'];
        $form_id=$_POST['form_id'];
        if($my_op==t('Save') && $form_id=='collection_node_form'){
            return 1;
        }
    }    
    return 0;
}
function social_learning_step_upload_collection(&$node){    
    $collection_id=social_learning_collections_get_collection_id($node);
    if(empty($collection_id)){
        social_learning_collections_upload_collection_postapi($node,$op);
    }else{
        social_learning_collections_update_collection_postapi($node,$collection_id);
        $node->field_last_upload_time[0]['value']=time();
        node_save($node);
    }
}
function social_learning_step_upload_collection_step_callback(){
    $nid=$_SESSION['insert_collection_nid'];
    if(!empty($nid)){
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            social_learning_step_upload_collection($node);
            unset($_SESSION['insert_collection_nid']);
            drupal_goto('social_learning/topics_step/'.$node->nid);
        }    
    }
    return '';
}
function social_learning_step_topics_step_form(){
    $form=array();
    $collection_nid=arg(2);
    $num=social_learning_step_define_topic_max();    
    $form['collection_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$collection_nid,
    );
    $collection_node=node_load($collection_nid);
    social_learning_step_get_collection_info_html($collection_node,$form);
    for($i=0;$i<$num;$i++){
        //$collapsed=TRUE;
        //if($i==0){
            $collapsed=FALSE;
        //}
        $fieldset='topic_fs'.$i;
        $form[$fieldset]=array(
            '#type'=>'fieldset',
            '#title'=>t('Topic').($i+1),
            '#collapsible'=>TRUE, 
            '#collapsed'=>$collapsed,                     
        );
        $form[$fieldset]['#attributes']['id']=$fieldset;
        if($i>0){
            $form[$fieldset]['#attributes']['style']='display:none';   
        }
        $form[$fieldset]['topic_title'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Title'),
        );
        $form[$fieldset]['topic_description'.$i]=array(
            '#type'=>'textarea',
            '#title'=>t('Description'),
        );        
    }
    $form['add_topic_btn']=array(
        '#type'=>'button',
        '#value'=>t('Add Topic'),
    );
    $form['undo_topic_btn']=array(
        '#type'=>'button',
        '#value'=>t('Undo Topic'),
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save')
    );
    social_learning_add_step_topics_step_js();
    return $form;
}
function social_learning_add_step_topics_step_js(){
     $js='
   $(document).ready(function()
   {
    $("#edit-add-topic-btn").click(function(){
        var num='.social_learning_step_define_topic_max().';
        var fieldset="";
        var display="";
        for(i=0;i<num;i++){
            fieldset="topic_fs"+i;
            display=$("#"+fieldset).css("display");
            if(display=="none"){
                $("#"+fieldset).css("display","block");
                break;
            }
        }
        return false;
    });
    $("#edit-undo-topic-btn").click(function(){
        var num='.social_learning_step_define_topic_max().';
        var fieldset="";
        var display="";
        var kont=num-1;
        for(i=0;i<num;i++){
            fieldset="topic_fs"+kont;
            my_title="edit-topic-title"+kont;
            my_description="edit-topic-description"+kont;
            display=$("#"+fieldset).css("display");
            if(display=="block"){
                $("#"+fieldset).css("display","none");
                $("#"+my_title).val("");
                $("#"+my_description).val("");
                break;
            }
            kont--;
        }
        return false;
    });
   });';
    drupal_add_js($js,'inline');
}
function social_learning_step_topics_step_form_submit($form,&$form_state){
    $collection_nid=$form_state['values']['collection_nid'];
    if(!empty($collection_nid)){
        $num=social_learning_step_define_topic_max();
        for($i=0;$i<$num;$i++){
            $topic_title='topic_title'.$i;
            $topic_description='topic_description'.$i;
            if(isset($form_state['values'][$topic_title]) && !empty($form_state['values'][$topic_title])){
                $topic_node=new stdClass();
                $topic_node->type='collection_topic';
                $topic_node->title=$form_state['values'][$topic_title];
                $topic_node->body=$form_state['values'][$topic_description];
                $topic_node->field_topic_collection_nid[0]['nid']=$collection_nid;
                $topic_node->field_topic_social_tags[0]['value']=social_learning_topics_define_social_tag_default_value();
                node_save($topic_node);
                social_learning_topics_upload_collection_topic($topic_node->nid,$my_collection_nid);
            }
        }
    }
    drupal_goto('social_learning/resources_step/'.$collection_nid);
}
function social_learning_step_define_topic_max(){
    return 10;
}
function social_learning_step_resources_step_form(){   
    $form=array();
    $collection_nid=arg(2);
    $num=social_learning_step_define_documentos_semilla_max();   
    $form['collection_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$collection_nid,
    );
    $collection_node=node_load($collection_nid);
    social_learning_step_get_collection_info_html($collection_node,$form);
    for($i=0;$i<$num;$i++){
        //$collapsed=TRUE;
        //if($i==0){
            $collapsed=FALSE;
        //}
        $fieldset='resource_fs'.$i;
        $form[$fieldset]=array(
            '#type'=>'fieldset',
            '#title'=>t('Resource').($i+1),
            '#collapsible'=>TRUE, 
            '#collapsed'=>$collapsed,                     
        );
        $form[$fieldset]['#attributes']['id']=$fieldset;
        if($i>0){
            $form[$fieldset]['#attributes']['style']='display:none';   
        }
        $form[$fieldset]['resource_title'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Title'),
        );
        $form[$fieldset]['resource_description'.$i]=array(
            '#type'=>'textarea',
            '#title'=>t('Description'),
        );
        $form[$fieldset]['resource_url'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Url'),
        );
    }
    $form['add_resource_btn']=array(
        '#type'=>'button',
        '#value'=>t('Add Resource'),
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save')
    );
    social_learning_add_step_resources_step_js();
    return $form;
}
function social_learning_step_define_documentos_semilla_max(){
    return social_learning_step_define_topic_max();
}
function social_learning_add_step_resources_step_js(){
     $js='
   $(document).ready(function()
   {
    $("#edit-add-resource-btn").click(function(){
        var num='.social_learning_step_define_documentos_semilla_max().';
        var fieldset="";
        var display="";
        for(i=0;i<num;i++){
            fieldset="resource_fs"+i;
            display=$("#"+fieldset).css("display");
            if(display=="none"){
                $("#"+fieldset).css("display","block");
                break;
            }
        }
        return false;
    });
   });';
    drupal_add_js($js,'inline');
}
function social_learning_step_resources_step_form_submit($form,&$form_state){
    $collection_nid=$form_state['values']['collection_nid'];
    if(!empty($collection_nid)){
        $num=social_learning_step_define_documentos_semilla_max();
        for($i=0;$i<$num;$i++){
            $resource_title='resource_title'.$i;
            $resource_description='resource_description'.$i;
            $resource_url='resource_url'.$i;
            if(isset($form_state['values'][$resource_title]) && !empty($form_state['values'][$resource_title])){
                $resource_node=new stdClass();
                $resource_node->type='collection_resource';
                $resource_node->title=$form_state['values'][$resource_title];
                $resource_node->body=$form_state['values'][$resource_description];
                $resource_node->field_collection_reference_nid[0]['nid']=$collection_nid;
                $resource_node->field_resource_url[0]['url']=$form_state['values'][$resource_url];
                $resource_node->field_social_tags[0]['value']=social_learning_step_define_documento_semilla_social_tag_default_value();
                node_save($resource_node);
                social_learning_collections_upload_collection_resource($resource_node->nid,$my_collection_nid);
            }
        }
    }
    drupal_goto('social_learning/feeds_step/'.$collection_nid);    
}
function social_learning_step_feeds_step_form(){
    $form=array();
    $collection_nid=arg(2);
    $num=social_learning_step_define_feed_max();   
    $form['collection_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$collection_nid,
    );
    $collection_node=node_load($collection_nid);
    social_learning_step_get_collection_info_html($collection_node,$form);
    for($i=0;$i<$num;$i++){
        //$collapsed=TRUE;
        //if($i==0){
            $collapsed=FALSE;
        //}
        $fieldset='feed_fs'.$i;
        $form[$fieldset]=array(
            '#type'=>'fieldset',
            '#title'=>t('Resource Container').($i+1),
            '#collapsible'=>TRUE, 
            '#collapsed'=>$collapsed,                     
        );
        $form[$fieldset]['#attributes']['id']=$fieldset;
        if($i>0){
            $form[$fieldset]['#attributes']['style']='display:none';   
        }
        $form[$fieldset]['feed_title'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Title'),
        );
        $form[$fieldset]['feed_description'.$i]=array(
            '#type'=>'textarea',
            '#title'=>t('Description'),
        );
        $form[$fieldset]['feed_url'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Url'),
        );
        $form[$fieldset]['feed_rss'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('RSS'),
        );
    }
    $form['add_feed_btn']=array(
        '#type'=>'button',
        '#value'=>t('Add Resource Container'),
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        //'#value'=>t('Finish')
        '#value'=>t('Save')
    );
    social_learning_add_step_feeds_step_js();
    return $form;
}
function social_learning_step_define_feed_max(){
    return social_learning_step_define_topic_max();
}
function social_learning_add_step_feeds_step_js(){
     $js='
   $(document).ready(function()
   {
    $("#edit-add-feed-btn").click(function(){
        var num='.social_learning_step_define_feed_max().';
        var fieldset="";
        var display="";
        for(i=0;i<num;i++){
            fieldset="feed_fs"+i;
            display=$("#"+fieldset).css("display");
            if(display=="none"){
                $("#"+fieldset).css("display","block");
                break;
            }
        }
        return false;
    });
   });';
    drupal_add_js($js,'inline');
}
function social_learning_step_feeds_step_form_submit($form,&$form_state){
    $collection_nid=$form_state['values']['collection_nid'];
    if(!empty($collection_nid)){
        $num=social_learning_step_define_feed_max();
        for($i=0;$i<$num;$i++){
            $feed_title='feed_title'.$i;
            $feed_description='feed_description'.$i;
            $feed_url='feed_url'.$i;
            $feed_rss='feed_rss'.$i;
            if(isset($form_state['values'][$feed_title]) && !empty($form_state['values'][$feed_title])){
                $feed_node=new stdClass();
                $feed_node->type='collection_feed';
                $feed_node->title=$form_state['values'][$feed_title];
                $feed_node->body=$form_state['values'][$feed_description];
                $feed_node->field_feed_collection_nid[0]['nid']=$collection_nid;
                $feed_node->field_resource_container_url[0]['url']=$form_state['values'][$feed_url];
                $feed_node->field_resource_container_rss[0]['url']=$form_state['values'][$feed_rss];
                node_save($feed_node);
                social_learning_feeds_upload_collection_feed($feed_node->nid,$my_collection_nid);
            }
        }
    }
    drupal_goto('social_learning/files_step/'.$collection_nid);
    //drupal_goto('social_learning/collections');
}
function social_learning_step_get_collection_numero_de_resultados($node){
    $collection_item_array=social_learning_step_get_collection_item_array($node->nid);
    $sin_leer=0;
    if(!empty($collection_item_array)){
        foreach($collection_item_array as $i=>$node){
            $leidos=red_reads_visitas($node);
            if(empty($leidos)){
                $sin_leer=$sin_leer+1;
            }
        }
    }
    $result['numero_de_resultados']=count($collection_item_array);
    $result['sin_leer']=$sin_leer;
    return $result;
}
function social_learning_step_get_collection_item_array($collection_nid){
    $result=array();
    $sql=social_learning_collections_define_sql_collections_item_node($collection_nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $result[]=$node;
        }    
    }
    return $result;
}
function social_learning_step_estadisticas_modal($js,$is_modal,$collection_node) {
  $salida='';
    
  if($is_modal){
    $salida = '<p>'.t('Number of new items in the collection').': '.$collection_node->title.'</p>';
  }
  
  $title = t('Statistics from last month.');
  if ($js) {
    ctools_include('ajax');
    ctools_include('modal');    
    //$period = strtotime('-1 month');
    $period=social_learning_step_get_estadisticas_period($collection_node);
    $quant = new stdClass;
    //$quant->id = 'canales_chart';    
    $quant->id = 'collection_chart';
    $quant->label = t('Number of items');  // The title of the chart
    $quant->labelsum = TRUE; // Show the total amount of items in the chart title
    //
    $where=array();
    $where[]='node.created >= %d';
    $where[]='content_field_collection_nid.field_collection_nid_nid = '.$collection_node->nid;
    $documentos_semilla_resources_id_array=social_learning_collections_get_documentos_semilla_resources_id_array($collection_node->nid);    
    if(!empty($documentos_semilla_resources_id_array)){
        $where[]='content_type_collection_item.field_collec_item_resource_id_value NOT IN('.implode(',',$documentos_semilla_resources_id_array).')';
    }
    //
    $quant->query = 'SELECT node.created 
                    FROM {node} node
                    LEFT JOIN {content_type_collection_item} content_type_collection_item ON node.vid=content_type_collection_item.vid 
                    LEFT JOIN {content_field_collection_nid} content_field_collection_nid ON node.vid=content_field_collection_nid.vid   
                    WHERE '.implode(' AND ',$where).'
                    ORDER BY node.created DESC'; // We can provide a custom query instead
    $quant->table = 'og';
    $quant->field = 'created';
    $quant->dataType = 'single';
    $quant->chartType = 'line';
    $quants[] = $quant;
  
    $salida .= '<p>'. quant_process($quant, $period) .'</p>';
    //gemini-2013
    if($is_modal){
      ctools_modal_render($title, $salida);
    }else{
        return $salida;
    }
  }
  else {
    drupal_set_title($titulo);
    return $salida;
  }    
}
function social_learning_step_get_collection_info_html($collection_node,&$form){
    if(isset($collection_node->nid) && !empty($collection_node->nid)){
        $form['collection_info_fs']=array(
            '#type'=>'fieldset',
            '#title'=>t('Collection'),
        );
        $form['collection_info_fs']['collection_info_html']['#value']=social_learning_collections_get_collection_info_html($collection_node,0);
    }
}
function social_learning_step_define_documento_semilla_social_tag_default_value(){
    return 'my_resource';
}
function social_learning_step_set_documento_semilla_social_tag_form_field(&$form,$is_edit=0){
    //if(!$is_edit){
    $form['field_social_tags'][0]['#default_value']['value']=social_learning_step_define_documento_semilla_social_tag_default_value();
    //}
    $form['field_social_tags']['#prefix']='<div style="display:none;">';
    $form['field_social_tags']['#suffix']='</div>';
}
function social_learning_step_get_estadisticas_period($collection_node){
    $my_time=$collection_node->created;
    $fecha=date('Y-m-d',$my_time);
    $result=strtotime($fecha);
    return $result;
}
function social_learning_step_set_imported_message($kont_created){
    $content_type='Collection Item';
    if($kont_created>0){
        drupal_set_message(format_plural($kont_created, 'Created @number @type node.', 'Created @number @type nodes.', array('@number' =>$kont_created, '@type' => $content_type)));
    }else{
        drupal_set_message(t('There is no new content.'));
    }
}
function social_learning_step_files_step_form(){
    $form=array();
    $form['#attributes'] = array('enctype' => "multipart/form-data");    
    $collection_nid=arg(2);
    $num=social_learning_step_define_files_max();   
    $form['collection_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$collection_nid,
    );
    $collection_node=node_load($collection_nid);
    social_learning_step_get_collection_info_html($collection_node,$form);
    for($i=0;$i<$num;$i++){
        //$collapsed=TRUE;
        //if($i==0){
            $collapsed=FALSE;
        //}
        $fieldset='file_fs'.$i;
        $form[$fieldset]=array(
            '#type'=>'fieldset',
            '#title'=>t('File').($i+1),
            '#collapsible'=>TRUE, 
            '#collapsed'=>$collapsed,                     
        );
        $form[$fieldset]['#attributes']['id']=$fieldset;
        if($i>0){
            $form[$fieldset]['#attributes']['style']='display:none';   
        }
        $form[$fieldset]['file_title'.$i]=array(
            '#type'=>'textfield',
            '#title'=>t('Title'),
        );
        $form[$fieldset]['file_file'.$i] = array(
                    '#type' => 'file',
                    '#title' => t('File'),
                    '#size' => 60,
                    );       
    }
    $form['add_file_btn']=array(
        '#type'=>'button',
        '#value'=>t('Add File'),
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Finish')
    );
    social_learning_add_step_files_step_js();
    return $form;
}
function social_learning_step_define_files_max(){
    return social_learning_step_define_topic_max();
}
function social_learning_step_files_step_form_submit($form,&$form_state){
    $collection_nid=$form_state['values']['collection_nid'];
    if(!empty($collection_nid)){
        $num=social_learning_step_define_files_max();
        for($i=0;$i<$num;$i++){
            $file_title='file_title'.$i;
            $file_description='file_description'.$i;
            $file_file='file_file'.$i;
            if(isset($form_state['values'][$file_title]) && !empty($form_state['values'][$file_title])){
                $file_node=new stdClass();
                $file_node->type='collection_file';
                $file_node->title=$form_state['values'][$file_title];
                //$file_node->body=$form_state['values'][$file_description];
                $file_node->field_file_collection_nid[0]['nid']=$collection_nid;
                node_save($file_node);
                $validators=array();
                $file=file_save_upload($file_file, $validators, file_directory_path());
                social_learning_step_insert_upload($file,$file_node);
                social_learning_files_upload_collection_file($file_node->nid,$my_collection_nid);
            }
        }
    }
    drupal_goto('social_learning/collections');
}
function social_learning_step_insert_upload($file,$node){
   db_query("INSERT INTO {upload} (fid, nid, vid, list, description, weight) VALUES (%d, %d, %d, %d, '%s', %d)", $file->fid, $node->nid, $node->vid,1, $file->filename,0);      
}
function social_learning_add_step_files_step_js(){
     $js='
   $(document).ready(function()
   {
    $("#edit-add-file-btn").click(function(){
        var num='.social_learning_step_define_files_max().';
        var fieldset="";
        var display="";
        for(i=0;i<num;i++){
            fieldset="file_fs"+i;
            display=$("#"+fieldset).css("display");
            if(display=="none"){
                $("#"+fieldset).css("display","block");
                break;
            }
        }
        return false;
    });
   });';
    drupal_add_js($js,'inline');
}