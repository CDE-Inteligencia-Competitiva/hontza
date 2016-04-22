<?php
function compartir_documentos_fuente_supercanal_update_on_send($rss_name,$frecuencia,$hora,$is_shared,$node){    
    $content_type_supercanal_array=compartir_documentos_fuente_get_content_type_supercanal_array($node->nid);
    //echo print_r($content_type_supercanal_array,1);exit();
        if(!empty($content_type_supercanal_array)){
            foreach($content_type_supercanal_array as $i=>$row){
                db_query($sql=sprintf('UPDATE {content_type_supercanal} SET field_supercanal_rss_name_value="%s" WHERE nid=%d AND vid=%d',$rss_name,$node->nid,$node->vid));
                //print $sql;exit();
            }
        }else{
            db_query('INSERT INTO {content_type_supercanal}(vid,nid,field_supercanal_rss_name_value) VALUES(%d,%d,"%s")',$node->vid,$node->nid,$rss_name);
        }
}
function compartir_documentos_fuente_get_content_type_supercanal_array($nid,$field_supercanal_rss_name_value=''){
    $result=array();
    if(!empty($nid)){
        $res=db_query('SELECT * FROM {content_type_supercanal} WHERE nid=%d',$nid);
    }else if(!empty($field_supercanal_rss_name_value)){
        $res=db_query($sql=sprintf('SELECT * FROM {content_type_supercanal} WHERE field_supercanal_rss_name_value="%s" ORDER BY vid DESC',$field_supercanal_rss_name_value));
        //print $sql;exit();
    }else{
        return $result;
    }
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function compartir_documentos_fuente_supercanal_get_node_download($type){
    $field_supercanal_rss_name_value=arg(2);
        //print $field_supercanal_rss_name_value;exit();
        $content_type_supercanal_array=compartir_documentos_fuente_get_content_type_supercanal_array('',$field_supercanal_rss_name_value);
        //echo print_r($content_type_supercanal_array,1);exit();
        if(isset($content_type_supercanal_array[0])){
            $row=$content_type_supercanal_array[0];
            $node=node_load($row->nid);
            //echo print_r($node,1);exit();
            return $node;
        }
    $my_result=new stdClass();
    return $my_result;
}
function compartir_documentos_fuente_supercanal_reset($node){
        $content_type_supercanal_array=compartir_documentos_fuente_get_content_type_supercanal_array($node->nid);
        if(!empty($content_type_supercanal_array)){
            foreach($content_type_supercanal_array as $i=>$row){
                db_query($sql=sprintf('UPDATE {content_type_supercanal} SET field_supercanal_rss_name_value="" WHERE nid=%d AND vid=%d',$node->nid,$node->vid));                                
                //print $sql;exit();
            }
        }
}
function compartir_documentos_fuente_supercanal_node_form_alter(&$form,&$form_id,$nid){
    $unset_array=array('field_supercanal_rss_name');
    compartir_documentos_unset_form_field_form_alter($form,$form_state, $form_id, $unset_array);
}
function compartir_documentos_fuente_get_fuente_title_color($result_in,$row){
    $result=$result_in;
    $node=node_load($row->nid);
    $class=compartir_documentos_get_title_imported_class($node);
    $result=str_replace('<a','<a'.$class,$result);
    return $result;
}