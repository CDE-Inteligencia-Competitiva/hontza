<?php
function compartir_documentos_proyecto_update_on_send($rss_name,$frecuencia,$hora,$is_shared,$node){    
    $content_type_proyecto_array=compartir_documentos_proyecto_get_content_type_proyecto_array($node->nid);
        if(!empty($content_type_proyecto_array)){
            foreach($content_type_proyecto_array as $i=>$row){
                db_query($sql=sprintf('UPDATE {content_type_proyecto} SET field_proyecto_rss_name_value="%s" WHERE nid=%d AND vid=%d',$rss_name,$node->nid,$node->vid));
            }
        }else{
            db_query('INSERT INTO {content_type_proyecto}(vid,nid,field_proyecto_rss_name_value) VALUES(%d,%d,"%s")',$node->vid,$node->nid,$rss_name);
        }
}
function compartir_documentos_proyecto_get_node_download($type){
        $field_proyecto_rss_name_value=arg(2);
        $content_type_proyecto_array=compartir_documentos_proyecto_get_content_type_proyecto_array('',$field_proyecto_rss_name_value);
        if(isset($content_type_proyecto_array[0])){
            $row=$content_type_proyecto_array[0];
            $node=node_load($row->nid);
            return $node;
        }
        $my_result=new stdClass();
        return $my_result;
}
function compartir_documentos_proyecto_get_content_type_proyecto_array($nid,$field_report_rss_name_value=''){
    $result=array();
    if(!empty($nid)){
        $res=db_query('SELECT * FROM {content_type_proyecto} WHERE nid=%d',$nid);
    }else if(!empty($field_report_rss_name_value)){
        $res=db_query('SELECT * FROM {content_type_proyecto} WHERE field_proyecto_rss_name_value="%s" ORDER BY vid DESC',$field_report_rss_name_value);
    }else{
        return $result;
    }
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function compartir_documentos_get_importar_proyecto_link(){
    return l(t('Import Project'),'compartir_documentos/importar_documento/proyecto');
}
function compartir_documento_proyecto_reset($node){
    $content_type_proyecto_array=compartir_documentos_proyecto_get_content_type_proyecto_array($node->nid);
    if(!empty($content_type_proyecto_array)){
            foreach($content_type_proyecto_array as $i=>$row){
                db_query('UPDATE {content_type_proyecto} SET field_proyecto_rss_name_value="" WHERE nid=%d AND vid=%d',$node->nid,$node->vid);                                
            }
        }
}
function compartir_documentos_proyecto_node_form_alter(&$form,&$form_state,$form_id){
    $unset_array=array('field_proyecto_rss_name');
    compartir_documentos_unset_form_field_form_alter($form,$form_state, $form_id, $unset_array);
}