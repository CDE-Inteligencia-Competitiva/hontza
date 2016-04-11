<?php
function hontza_social_add_recurso($content_id,$fid){
    //return;
    if(hontza_social_is_activado()){
        if($fid==2){
            $result=array();
            $node=node_load($content_id);
            if(isset($node->nid) && !empty($node->nid)){
                $url=hontza_get_item_url_enlace('',$node);
                $result['title']=$node->title;
                $result['url']=$url;
                $result['description']=$node->body;
                $postdata=json_encode($result);                
                /*echo print_r($postdata,1);
                exit();*/
                $url_post=hontza_social_define_add_recurso_url();
                hontza_social_add_recurso_postapi($url_post,$postdata);
            }
        }
    }
}
function hontza_social_add_recurso_postapi($url,$postdata)
{
    $postapi_username_pass=hontza_social_define_username_pass_postapi();
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
    $result=trim($data);
    curl_close($curl);
    /*echo 'result='.print_r($result,1);
    exit();        
    if(isset($result['ok']) && !empty($result['ok'])){
        //
    }else{
        //
    }*/
}
function hontza_social_define_add_recurso_url(){
    $result=hontza_social_define_url('api/resources/');    
    //$result='http://localhost/hontza3/hontza_social/simular_social_add_recurso';    
    //$result='http://online.hontza.es/hontza_social/simular_social_add_recurso';
    return $result;
}
function hontza_social_simular_add_recurso_callback($var){
    $data=json_decode($_POST['data']);    
    $url=$data->url;
    $text=$data->text;
    db_query('INSERT INTO {social_simular_recurso}(url,texto) VALUES("%s","%s")',$url,$text);
}
function hontza_social_define_username_pass_postapi(){
    $username='hontza';
    $password='h0ntza';
    return $username.':'.$password;
}
function hontza_social_is_activado(){
    if(defined('_IS_SOCIAL_LEARNING') && _IS_SOCIAL_LEARNING==1){
        return 1;
    }
    return 0;
}
function hontza_social_simular_social_auth_callback(){
    $html=array();
    $html[]='<input id="test_social_auth_btn" type="button" value="Test social elearning authentication"/>';
    hontza_social_add_simular_social_auth_js();
    return implode('',$html);
}
function hontza_social_add_simular_social_auth_js(){
   $js='$(document).ready(function()
   {
            $("#test_social_auth_btn").click(function(){
                function make_base_auth(user, password) {
                    var tok = user + ":" + password;
                    var hash = btoa(tok);
                    return "Basic " + hash;
                }
                $.ajax({
                    url: "'.hontza_social_define_url('').'",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Authorization", make_base_auth("hontza","h0ntza"));
                    },
                    type: "POST",
                    dataType: "json",
                    contentType: "application/json",
                    //processData: false,
                    data: \'{"foo":"bar"}\',
                    success: function (data) {
                      alert(JSON.stringify(data));
                    },
                    error: function(){
                      alert("Cannot get data");
                    }
                });
            });
    });';
    drupal_add_js($js,'inline');
}
function hontza_social_simular_get_resource_id_callback(){
    $social_id=435;
    $url=hontza_social_define_url('api/resources/'.$social_id);
    $output=file_get_contents($url);
    $result=json_decode($output);
    echo print_r($result,1);
    exit();
}
function hontza_social_define_url($param){
    $url='http://social.hontza.es';
    //$url='http://217.70.191.147';
    $result=$url.'/'.$param;
    return $result;
}
function hontza_social_get_collections_block_content(){
    global $user;
    if(isset($user->uid) && !empty($user->uid)){
        $html=array();
        //$html[]=l(t('Create Collection'),'node/add/collection',array('query'=>'destination=social_learning/collections'));
        $html[]=l(t('Create Collection'),'node/add/collection',array('query'=>'destination=social_learning/upload_collection_step'));
        $html[]=l('Collections','social_learning/collections');
        return implode('<BR>',$html);
    }
    return '';
}
function hontza_social_get_api_block_content(){
    if(is_super_admin()){
        $html=array();
        $html[]=l(t('Resources'),'social_learning/api/resources');
        $html[]=l(t('Topics'),'social_learning/api/topics');
        $html[]=l(t('Mentions'),'social_learning/api/mentions');
        $html[]=l(t('Profiles'),'social_learning/api/profiles');
        return implode('<BR>',$html);
    }
    return '';
}
function hontza_social_is_json_url($url){
    if(hontza_social_is_activado()){
        $info=parse_url($url);
        if(isset($info['query']) && !empty($info['query'])){
            parse_str($info['query'],$query_array);
            //if(isset($info['host']) && !empty($info['host']) && $info['host']=='social.hontza.es'){
            if(isset($info['host']) && !empty($info['host']) && in_array($info['host'],array('social.hontza.es','217.70.191.147'))){ 
                if(isset($query_array['format']) && !empty($query_array['format']) && $query_array['format']=='json'){
                    return 1;
                }
            }    
        }
    }
    return 0;
}
function hontza_social_is_json_url_by_data($data,&$url){
    $url='';
    if(hontza_social_is_activado()){
        $values=array_values($data);
        if(count($values)==1){
            $url=$values[0];
            if(hontza_social_is_json_url($url)){
                return 1;
            }
        }
    }    
    return 0;
}
function hontza_social_add_json_items($url,$source,&$batch){
    if(hontza_social_is_activado()){
        $content=file_get_contents($url);
        $result=json_decode(trim($content));
        if(!empty($result)){
            foreach($result as $i=>$row){
              $elemento=array();
              $elemento['url']=$row->url;
              $elemento['guid']=$row->url;
              //gemini-2014
              if(hontza_is_item_duplicado($elemento,$source)){
                  continue;
              }
              if(social_learning_items_is_empty_title($row)){
                  continue;
              }
              $resources_id=social_learning_get_resource_id_by_url($row->resource);
              $collection_nid_value_array=social_learning_items_get_collection_nid_array_by_item_item_servidor($row,$collection_id_value_array);
              if(!social_learning_collections_is_documento_semilla($resources_id,'',$collection_nid_value_array)){
                  $item=array();
                  $item['title'] = $row->title;
                  $item['description'] =  $row->description;            
                  $item['link'] =  $row->url;
                  $item['guid'] =  $row->url;
                  $item['url'] =  $row->url;
                  $item['collection_item_servidor']=$row;
                  //$item['collection_item_servidor']->interest_servidor_row=social_learning_collections_get_interest_servidor_row($resources_id);
                  $item['collection_item_servidor']->interest_servidor_row=social_learning_items_get_interest_servidor_row_by_servidor_row($row);                  
                  $batch->addItem($item);
              }  
            }
        }
    }
}
function hontza_social_set_feed_node_item_url($source,&$node){
   if(hontza_social_is_activado()){
       $config=$source->getConfig();  
       $url=$config['FeedsHTTPFetcher']['source'];
       if(hontza_social_is_json_url($url)){
         if(!isset($node->feeds_node_item->url) || empty($node->feeds_node_item->url)){  
            if(isset($node->feeds_node_item->guid) && !empty($node->feeds_node_item->guid)){
                $node->feeds_node_item->url=$node->feeds_node_item->guid;
            }
         }   
       }
   } 
}
function  hontza_social_canal_de_yql_form_alter(&$form,&$form_state, $form_id){
    if(hontza_social_is_activado()){
        $node=hontza_get_node_by_form($form);
        if(hontza_social_is_canal_json($node)){
           $num=5;
           for($i=1;$i<=$num;$i++){
               unset($form['filtros'.$i]);
           }
        }
    }
}
function hontza_social_is_canal_json($node){
    if($node->type=='canal_de_yql'){
        if(isset($node->field_fuente_canal[0]['value']) && !empty($node->field_fuente_canal[0]['value']) && $node->field_fuente_canal[0]['value']=='JSON'){
            return 1;
        }        
    }
    return 0;
}
function hontza_social_set_item_servidor_fields($item,&$node){
    if(hontza_social_is_activado()){
        social_learning_items_set_item_servidor_fields($item,$node);
    }
}
function hontza_social_is_import_table_html(){
    if(hontza_social_is_activado()){
        return social_learning_collections_is_import_table_html();
    }
    return 0;
}
function hontza_social_item_node_form_alter(&$form,&$form_state, $form_id){
    if(hontza_social_is_activado()){
        social_learning_items_item_node_form_alter($form,$form_state, $form_id);
    }
}
function hontza_social_insert_item_interest_hontza_by_feed_item($node,$item){
    if(hontza_social_is_activado()){
        social_learning_items_insert_item_interest_hontza_by_feed_item($node,$item);
    }
}
function hontza_social_on_form_node_save($op,&$node){
    if(hontza_social_is_activado()){
        social_learning_step_on_form_node_save($op,$node);
    }
}
function hontza_social_get_analisis($node){
    $type_array=array('canal_busqueda','canal_de_supercanal','canal_de_yql');
    if(hontza_social_is_activado()){
        if(in_array($node->type,array('collection'))){
            return social_learning_step_estadisticas_modal(TRUE,0,$node);
        }
    }else if(in_array($node->type,$type_array)){
        return my_estadisticas_modal(TRUE,0,$node->nid);         
    }
    return '';
}
function hontza_social_define_group_searcher_type(){
    $result=array();
    if(hontza_social_is_activado()){
        $result[0]='Buscador clásico';
        $result[1]='Buscador Semántico social';
    }
    return $result;
}
function hontza_social_is_grupo_semantico_social(){
    if(hontza_social_is_activado()){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            if(isset($my_grupo->field_group_searcher_type[0]['value']) && !empty($my_grupo->field_group_searcher_type[0]['value']) && $my_grupo->field_group_searcher_type[0]['value']==1){
                return 1;
            }
        }
    }    
    return 0;
}
function hontza_social_is_social_learning(){
    if(hontza_social_is_activado()){
        $param0=arg(0);
        if(!empty($param0) && $param0=='social_learning'){
            return 1;
        }
        $node=my_get_node();
        $node_type_array=array('collection','collection_item','collection_resource','collection_topic','collection_feed','collection_temporal','collection_file');
        if(in_array($node->type,$node_type_array)){
            return 1;
        }
        //$add_node_type_array=array('collection','collection-item','collection-resource','collection-topic','collection-feed','collection-temporal');
        $add_node_type_array=social_learning_files_define_add_node_type_array($node_type_array);
        foreach($add_node_type_array as $i=>$node_type){
            if(is_node_add($node_type)){
                return 1;
            }
        }    
    }
    return 0;
}
function hontza_social_is_node_upload_files($node){
    if(hontza_social_is_activado()){
        if($node->type=='collection_file'){
            if(isset($node->files) && !empty($node->files)){
                return 0;
            }
        }
    }
    return 1;
}