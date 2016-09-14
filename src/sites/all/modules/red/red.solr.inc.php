<?php
function red_solr_inc_get_get_status_options($is_multiple=0){
    if(!$is_multiple){
        $result[0]=t('.All');
    }
    $result[2]=t('Validated');
    $result[1]=t('Pending');
    $result[3]=t('Rejected');
    if(red_solr_inc_is_status_activado()){
        $result[4]=t('Selected to Bulletin');
        $result[5]=t('Commented');
        $result[6]=t('Bookmarked');
        $result[7]=t('Highlighted');
        if(!$is_multiple){
            $result[8]=t('Validated and Highlighted');
            $result[9]=t('Validated and Commented');
            $result[10]=t('Validated and Bookmarked');
        }
    }
    return $result;
}
function red_solr_inc_is_status_activado(){
    /*if(hontza_solr_is_solr_activado()){
        if(defined('_IS_SOLR_STATUS')){
            if(_IS_SOLR_STATUS==1){
                return 1;
            }
        }
    }*/
    if(defined('_IS_SOLR_STATUS')){
        if(_IS_SOLR_STATUS==1){
            return 1;
        }
    }
    return 0;
}
function red_solr_inc_add_validate_status_filter(&$my_array,$validate_status){
    $i=count($my_array);
    //sin validar=1
    //validados=2
    //rechazados=3
    if(empty($validate_status)){
        /*$my_array[]='f['.$i.']=itm_field_item_validate_status:1';
        $i=count($my_array);
        $my_array[]='f['.$i.']=itm_field_item_validate_status:2';
        $i=count($my_array);
        $my_array[]='f['.$i.']=itm_field_item_validate_status:3';*/
    }else if($validate_status==1){
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(itm_field_item_validate_status:1 OR itm_field_noticia_validate_statu:1)';            
        }else{
            $my_array[]='f['.$i.']=itm_field_item_validate_status:1';
        }    
    }else if($validate_status==2){
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(itm_field_item_validate_status:2 OR itm_field_noticia_validate_statu:2)';
        }else{
            $my_array[]='f['.$i.']=itm_field_item_validate_status:2';        
        }    
    }else if($validate_status==3){
        //intelsat-2016-noticias-usuario-solr
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(itm_field_item_validate_status:3 OR itm_field_noticia_validate_statu:3)';
        }else{
            $my_array[]='f['.$i.']=itm_field_item_validate_status:3';
        }            
    }else if($validate_status==4){
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(itm_field_item_seleccionado_bole:1 OR itm_field_noticia_seleccionado_b:1)';
        }else{
            $my_array[]='f['.$i.']=itm_field_item_seleccionado_bole:1';
        }
    }else if($validate_status==6){
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(itm_field_item_bookmark:1 OR itm_field_noticia_bookmark:1)';
        }else{
            $my_array[]='f['.$i.']=itm_field_item_bookmark:1';
        }
    }else if($validate_status==7){
        //intelsat-2016-noticias-usuario-solr
        if(red_solr_inc_is_actualizar_noticias_usuario()){            
            $my_array[]='(itm_field_is_carpeta_noticia_des:1 OR itm_field_is_noticia_usuario_des:1)';
        }else{
            $my_array[]='f['.$i.']=itm_field_is_carpeta_noticia_des:1';
        }    
    }else if($validate_status==8){
        //intelsat-2016-noticias-usuario-solr
        $my_array[]='f['.$i.']=itm_field_item_validate_status:2';
        //$my_array[]='f['.$i.']=(itm_field_item_validate_status:2 OR itm_field_noticia_validate_statu:2)';
        $i=count($my_array);
        $my_array[]='f['.$i.']=itm_field_is_carpeta_noticia_des:1';         
    }else if($validate_status==9){
        //intelsat-2016-noticias-usuario-solr
        $my_array[]='f['.$i.']=itm_field_item_validate_status:2';
        //$my_array[]='f['.$i.']=(itm_field_item_validate_status:2 OR itm_field_noticia_validate_statu:2)';    
    }else if($validate_status==10){
        //intelsat-2016-noticias-usuario-solr
        $my_array[]='f['.$i.']=itm_field_item_validate_status:2';
        //$my_array[]='f['.$i.']=(itm_field_item_validate_status:2 OR itm_field_noticia_validate_statu:2)';    
        $i=count($my_array);
        $my_array[]='f['.$i.']=itm_field_item_bookmark:1';
    }
}
function red_solr_inc_actualizar_items(){
    //intelsat-2016
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        noticias_usuario_solr_actualizar();       
    }
    //if(red_crear_usuario_is_crear_usuario_net()){
    if(!red_solr_inc_is_actualizar_items()){    
        return;
    }
    hontza_solr_actualizar_canal_source_type();
    $item_array=hontza_solr_actualizar_item_source_tid();
    hontza_solr_actualizar_item_canal_category_tid($item_array);
    hontza_solr_actualizar_validador();
    hontza_solr_actualizar_validate_status($item_array);
    hontza_solr_actualizar_item_ficheros($item_array);
    if(red_solr_inc_is_status_activado()){
        red_solr_inc_actualizar_seleccionado_boletin($item_array);
        red_solr_inc_actualizar_bookmark($item_array);
        red_solr_inc_actualizar_rated($item_array);
        $updated=1;
        red_solr_inc_set_item_array_solr_updated($item_array,$updated);
    }
    /*if(red_solr_inc_is_hontza_item_indexado_activado()){
        red_solr_inc_save_hontza_item_indexado($item_array,1);
    }*/      
}
function red_solr_inc_actualizar_seleccionado_boletin($item_array){
    if(!empty($item_array)){
       foreach($item_array as $i=>$node){
           if(isset($node->nid) && !empty($node->nid)){
               if(hontza_solr_is_item_actualizado($node)){
                    continue;
               }               
               red_solr_inc_update_node_seleccionado_boletin($node);
           }
       }
   }
}
function red_solr_inc_update_node_seleccionado_boletin($node,$is_sended=0,$seleccionado_boletin_in=0){
   if(red_solr_inc_is_status_activado()){
    /*if($is_sended){
        $seleccionado_boletin=$seleccionado_boletin_in;
    }else{*/   
        $seleccionado_boletin=red_solr_inc_get_seleccionado_boletin($node);
    /*}
    if(empty($seleccionado_boletin)){
        $seleccionado_boletin=2;
    }*/
    red_solr_inc_update_item_seleccionado_boletin($node,$seleccionado_boletin);
    //intelsat-2015
    $updated=0;
    hontza_solr_set_item_solr_updated($node,$updated);
    if(red_solr_inc_is_rated_clear_node_index($seleccionado_boletin)){
        hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    }    
   }
}
function red_solr_inc_get_seleccionado_boletin($node,$with_sended=0){
   if(isset($node->nid) && !empty($node->nid)){
        $row=boletin_report_get_node_row($node->nid);
        if(!empty($row) && isset($row->nid) && !empty($row->nid)){
            if(!empty($row->sended) && $row->sended==1){
                if($with_sended){
                    return 2;
                }
                //return 2;                
                return 0;
            }else{
                return 1;
            }    
        }else{
            return 0;
        }    
    }
    return 0; 
}
function red_solr_inc_update_item_seleccionado_boletin($node,$seleccionado_boletin_value){   
   db_query('UPDATE {content_type_item} SET field_item_seleccionado_boletin_value=%d WHERE nid=%d AND vid=%d',$seleccionado_boletin_value,$node->nid,$node->vid); 
}
function red_solr_inc_define_entity_field_name_item_seleccionado_boletin($entity_field_name_array,$entity){
    $seleccionado_boletin_array=red_solr_inc_get_content_field_item_seleccionado_boletin_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$seleccionado_boletin_array);
}
function red_solr_inc_get_content_field_item_seleccionado_boletin_array($nid,$vid){
    $result=array();
    $result[0]=0;
    //$result[0]=2;
    $row=my_get_content_type_item($nid,$vid);
    if(isset($row->field_item_seleccionado_boletin_value) && !empty($row->field_item_seleccionado_boletin_value)){
        $result[0]=$row->field_item_seleccionado_boletin_value;
    }
    return $result;
}
function red_solr_inc_get_commented($form_state){
    $result='';
    if(isset($form_state['values']['commented'])){
        $result=$form_state['values']['commented'];
    }
    if(red_solr_inc_is_status_activado()){
        if(red_solr_inc_is_commented($form_state)){
            return 1;
        }
    }
    return $result;
}
//intelsat-2015
function red_solr_inc_update_node_seleccionado_boletin_array($sended_array){
    if(red_solr_inc_is_status_activado()){
        if(!empty($sended_array)){
            foreach($sended_array as $item_type=>$item_array){
                if(!empty($item_array)){
                    foreach($item_array as $i=>$row){
                        $node=node_load($row->nid);
                        red_solr_inc_update_node_seleccionado_boletin($node,1,0);                                        
                    }
                }
            }
        }
    }
}
function red_solr_inc_actualizar_bookmark($item_array){
    if(!empty($item_array)){
       foreach($item_array as $i=>$node){
           if(isset($node->nid) && !empty($node->nid)){
               if(hontza_solr_is_item_actualizado($node)){
                    continue;
               }
               red_solr_inc_update_node_bookmark($node);
           }
       }
   }
}   
function red_solr_inc_update_node_bookmark($node){
   if(red_solr_inc_is_status_activado()){
    $bookmark=red_solr_inc_get_bookmark($node);
    red_solr_inc_update_item_bookmark($node,$bookmark);    
    //intelsat-2016-noticias-usuario
    if($node->type=='item'){
        $updated=0;
        hontza_solr_set_item_solr_updated($node,$updated);
    }
    if(red_solr_inc_is_rated_clear_node_index($bookmark)){
        hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    }    
   } 
}
function red_solr_inc_get_bookmark($node){
    if(isset($node->nid) && !empty($node->nid)){
        $row=hontza_solr_funciones_get_bookmark_row($node->nid,$node->vid,0);
        if(!empty($row) && isset($row->nid) && !empty($row->nid)){
            return 1;
        }else{
            return 0;
        }    
    }
    return 0;
}
function red_solr_inc_update_item_bookmark($node,$bookmark_value){
    if($node->type=='item'){
        db_query('UPDATE {content_type_item} SET field_item_bookmark_value=%d WHERE nid=%d AND vid=%d',$bookmark_value,$node->nid,$node->vid);
    }else if($node->type=='noticia'){
        //intelsat-2016-noticias-usuario
        db_query('UPDATE {content_type_noticia} SET field_noticia_bookmark_value=%d WHERE nid=%d AND vid=%d',$bookmark_value,$node->nid,$node->vid);
    }    
}
function red_solr_inc_define_entity_field_name_item_bookmark($entity_field_name_array,$entity){
    $bookmark_array=red_solr_inc_get_content_field_item_bookmark_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$bookmark_array);
}
function red_solr_inc_get_content_field_item_bookmark_array($nid,$vid){
    $result=array();
    $result[0]=0;
    //$result[0]=2;
    $row=my_get_content_type_item($nid,$vid);
    if(isset($row->field_item_bookmark_value) && !empty($row->field_item_bookmark_value)){
        $result[0]=$row->field_item_bookmark_value;
    }
    return $result;
}
function red_solr_inc_define_entity_field_is_carpeta_noticia_destaca($entity_field_name_array,$entity){
    $destacado_array=red_solr_inc_get_content_field_is_carpeta_noticia_destaca_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$destacado_array);
}
function red_solr_inc_get_content_field_is_carpeta_noticia_destaca_array($nid,$vid){
    $result=array();
    $result[0]=0;
    //$result[0]=2;
    $row=my_get_content_type_item($nid,$vid);
    if(user_access('root')){
        drupal_set_message(print_r($row,1));    
    }
    if(isset($row->field_is_carpeta_noticia_destaca_value) && !empty($row->field_is_carpeta_noticia_destaca_value)){
        $result[0]=$row->field_is_carpeta_noticia_destaca_value;
    }
    return $result;
}
function red_solr_inc_add_delete_filters_content(&$result){
    if(red_solr_inc_is_status_activado()){
        $seleccionado_boletin_array=hontza_solr_funciones_add_delete_validate_status_filtros($_REQUEST['f']);
        if(!empty($seleccionado_boletin_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Status').'</i></b>';
            $result=array_merge($result,$validate_status_array);
        }
    }
}
function red_solr_inc_add_delete_validate_status_filtros($my_array){
    $result=array();
    //intelsat-2016-noticias_usuario
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=red_solr_inc_noticias_usuario_add_delete_validate_status_filtros();
        return $result;
    }
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='itm_field_item_bookmark'){
                    $validate_status=$value_array[1];
                    if(!empty($validate_status)){
                        if($validate_status==1){
                            $validate_status=6;
                        }
                        $validate_status_label=hontza_solr_funciones_get_validate_status_label($validate_status);
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_bookmark',$my_array);
                        $result[]=$icono_link.$validate_status_label;
                    }
                }else if($field=='itm_field_is_carpeta_noticia_des'){
                    $validate_status=$value_array[1];
                    if(!empty($validate_status)){
                        if($validate_status==1){
                            $validate_status=7;
                        }
                        $validate_status_label=hontza_solr_funciones_get_validate_status_label($validate_status);
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_is_carpeta_noticia_des',$my_array);
                        $result[]=$icono_link.$validate_status_label;
                    }
                }else if($field=='itm_field_item_seleccionado_bole'){
                    $validate_status=$value_array[1];
                    if(!empty($validate_status)){
                        if($validate_status==1){
                            $validate_status=4;
                        }
                        $validate_status_label=hontza_solr_funciones_get_validate_status_label($validate_status);
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_seleccionado_bole',$my_array);
                        $result[]=$icono_link.$validate_status_label;
                    }                                    
                }else if($field=='itm_field_item_validate_status'){
                    $validate_status=$value_array[1];
                    if(!empty($validate_status)){
                        $validate_status_label=hontza_solr_funciones_get_validate_status_label($validate_status);
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'itm_field_item_validate_status',$my_array);
                        $result[]=$icono_link.$validate_status_label;
                    }
                }                
            }
        }
    }    
    return $result;
}
function red_solr_inc_add_busqueda_avanzada_rated_form_field(&$form,$prefix_right3,$suffix_right3){
    if(!red_solr_inc_is_status_activado()){
        $form['commented']= array(
              '#title' => t('Commented'),
              '#type' => 'select',
              '#options' => my_get_si_no_options(),
              '#prefix'=>$prefix_right3,
              '#suffix'=>$suffix_right3,  
        );
    }else{
        $form['rated']= array(
              '#title' => t('Scoring'),
              '#type' => 'select',
              '#options' => red_solr_inc_get_rated_options(),
              '#prefix'=>$prefix_right3,
              '#suffix'=>$suffix_right3,  
        );
    }
}
function red_solr_inc_get_rated_options(){
    $result[0]='';
    $result[1]='*';
    $result[2]='**';
    $result[3]='***';
    $result[4]='****';
    $result[5]='*****';
    return $result;
}
function red_solr_inc_item_node_form_alter(&$form,&$form_state, $form_id){
    $fields=array('field_item_seleccionado_boletin','field_item_bookmark','field_item_rated');
    if(!empty($fields)){
        foreach($fields as $i=>$field){
            if(isset($form[$field])){
                unset($form[$field]);
            }
        }
    }   
}
function red_solr_inc_actualizar_rated($item_array){
    if(!empty($item_array)){
       //$kont=0; 
       foreach($item_array as $i=>$node){
           if(isset($node->nid) && !empty($node->nid)){
               if(hontza_solr_is_item_actualizado($node)){
                   continue;
               }
               //$kont++;
               red_solr_inc_update_node_rated($node);
           }
       }      
   }  
}
function red_solr_inc_update_node_rated($node){
   if(red_solr_inc_is_status_activado()){
    $rated=red_solr_inc_get_rated($node);
    if($node->type=='item'){
        red_solr_inc_update_item_rated($node,$rated);
    }else if($node->type=='noticia'){
        red_solr_inc_update_noticia_rated($node,$rated);
    }    
    $updated=0;
    hontza_solr_set_item_solr_updated($node,$updated);
    if(red_solr_inc_is_rated_clear_node_index($rated)){        
        hontza_canal_rss_solr_clear_node_index($node,$node->nid);
    }    
   } 
}
function red_solr_inc_get_rated($node){
    $result=hontza_get_node_puntuacion_media_para_txt($node->nid,1);
    return $result;
}
function red_solr_inc_update_item_rated($node,$rated_value){
    if($node->type=='item'){
        db_query('UPDATE {content_type_item} SET field_item_rated_value=%f WHERE nid=%d AND vid=%d',$rated_value,$node->nid,$node->vid);
    }else if($node->type=='noticia'){
        red_solr_inc_update_noticia_rated($node,$rated_value);
    }
}
function red_solr_inc_define_entity_field_name_item_rated($entity_field_name_array,$entity){
    $rated_array=red_solr_inc_get_content_field_item_rated_array($entity->nid,$entity->vid);
    return hontza_solr_set_indexing_numeric_value($entity_field_name_array,$rated_array);
}
function red_solr_inc_get_content_field_item_rated_array($nid,$vid){
    $result=array();
    $result[0]=0;
    //$result[0]=2;
    $row=my_get_content_type_item($nid,$vid);
    if(isset($row->field_item_rated_value) && !empty($row->field_item_rated_value)){
        $result[0]=$row->field_item_rated_value;
    }
    return $result;
}
function red_solr_inc_add_rated_filter(&$my_array,$form_state){
    $rated=$form_state['values']['rated'];
    if(!empty($rated)){
        $i=count($my_array);
        $rated=$rated*20;
        if(red_solr_inc_is_actualizar_noticias_usuario()){
            $my_array[]='(ftm_field_item_rated:['.$rated.' TO *] OR ftm_field_noticia_rated:['.$rated.' TO *])';
        }else{
            $my_array[]='f['.$i.']=ftm_field_item_rated:['.$rated.' TO *]';
        }    
    }
}
function red_solr_inc_add_beste_delete_filters_content(&$result){
    if(red_solr_inc_is_status_activado()){
        $scoring_array=red_solr_inc_add_delete_scoring_filtros($_REQUEST['f']);
        if(!empty($scoring_array)){
            $result[]=hontza_solr_funciones_get_linea_separacion($result);
            $result[]='<b><i>'.t('Scoring').'</i></b>';
            $result=array_merge($result,$scoring_array);
        }
    }
}
function red_solr_inc_add_delete_scoring_filtros($my_array){
    $result=array();
    //intelsat-2016-noticias-usuario
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=red_solr_inc_noticias_usuario_add_delete_scoring_filtros();
        return $result;
    }
    if(!empty($my_array) && is_array($my_array)){
        foreach($my_array as $i=>$value){
            $value_array=explode(':',$value);
            if(count($value_array)>1){
                $field=$value_array[0];
                if($field=='ftm_field_item_rated'){
                    $scoring=$value_array[1];
                    /*$scoring=red_solr_inc_get_scoring_value($scoring);
                    if(!empty($scoring)){
                        $scoring_label=red_solr_inc_get_scoring_label($scoring);
                        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'ftm_field_item_rated',$my_array);
                        $result[]=$icono_link.$scoring_label;
                    }*/
                    red_solr_inc_add_delete_ftm_field_item_rated_filtros($scoring,$result);
                }                
            }
        }
    }
    return $result;
}
function red_solr_inc_get_scoring_value($scoring){
    $result=$scoring;
    $pos=strpos($result,' TO');
    if($pos===FALSE){
        return $result;
    }else{
        $result=substr($result,0,$pos);
        $result=substr($result,1);
    }
    return $result;
}
function red_solr_inc_get_scoring_label($scoring){
    $result=$scoring/20;
    $my_array=red_solr_inc_get_rated_options();
    if(isset($my_array[$result]) && !empty($my_array[$result])){
        return $my_array[$result];
    }
    return '';
}
function red_solr_inc_get_div_main_inner_style(){
    $result='';
    if(hontza_solr_is_busqueda_avanzada_pantalla()){
        $categorias=my_get_categorias_canal();
        if(red_solr_inc_is_long_categorias_canal($categorias)){
            $result='style="padding-left:0px;padding-right:0.5em;padding-top:0.5em;padding-bottom:1em;"';
        }
    }
    return $result;
}
function red_solr_inc_is_long_categorias_canal($categorias){
    $long=red_solr_inc_get_long($categorias);
    //http://solr.hontza.es/mkt/hontza_solr/busqueda_avanzada_solr?f[0]=im_og_gid:188959&solrsort=ds_created%20desc
    //la direccion tiene max=67
    //$max=67;
    $max=50;
    if($long>=$max){
        return 1;
    }
    return 0;
}
function red_solr_inc_get_long($my_array){
    $result=0;
    if(!empty($my_array)){
        foreach($my_array as $id=>$value){
            if(!is_array($value)){
                $long=strlen($value);
                if($long>=$result){
                    $result=$long;
                }
            }else{
                foreach($value as $i=>$v){
                    $long=strlen($v);
                    if($long>=$result){
                        $result=$long;
                    }
                }
            }
        }
    }
    return $result;
}
function red_solr_inc_add_multiple_validate_status_form_field($prefix_right3_canal_linea,$suffix_right3,&$form){
    if(red_solr_inc_is_status_activado()){
        $validate_status_array=hontza_solr_funciones_get_status_options(1);
        $form['validate_status_multiple']= array(
              '#title' => t('Status'),
              '#type' => 'select',
              '#options' => $validate_status_array,
              '#multiple'=>true,
              '#size'=>7,
              '#prefix'=>$prefix_right3_canal_linea,
              '#suffix'=>$suffix_right3,  
        );
    }
}
function red_solr_inc_add_multiple_validate_status_filter(&$my_array,$form_state){
    if(red_solr_inc_is_status_activado()){
        if(isset($form_state['values']['validate_status_multiple']) && !empty($form_state['values']['validate_status_multiple'])){
            $validate_status_array=$form_state['values']['validate_status_multiple'];
            foreach($validate_status_array as $validate_status=>$value){
                if(!empty($value)){
                    red_solr_inc_add_validate_status_filter($my_array,$validate_status);
                }    
            }
        }            
    }
}
function red_solr_inc_is_commented($form_state){
    if(isset($form_state['values']['validate_status']) && !empty($form_state['values']['validate_status'])){
        if(red_solr_inc_is_status_commented($form_state['values']['validate_status'])){
            return 1;
        }
    }else if(isset($form_state['values']['validate_status_multiple']) && !empty($form_state['values']['validate_status_multiple'])){
        $validate_status_array=$form_state['values']['validate_status_multiple'];
        foreach($validate_status_array as $validate_status=>$value){
            if(!empty($value)){
                if(red_solr_inc_is_status_commented($validate_status)){
                    return 1;
                }
            }    
        }
    }    
    return 0;
}
function red_solr_inc_is_status_commented($value){
    if(in_array($value,array(5,9))){
        return 1;
    }
    return 0;
}
function red_solr_inc_get_fid_style(){
    if(!red_solr_inc_is_status_activado()){
        //$result=array('style'=>'width:92%;');
        $result=array('style'=>'width:98%;');
    }else{
        //$result=array('style'=>'margin-top:-100px;float:left;width:65%;');
    }
    return $result;
}
function red_solr_inc_get_fid_div_style(){
    if(!red_solr_inc_is_status_activado()){
        $result='<div style="padding-left:5px;">'; 
    }else{
        //$result='<div style="padding-left:5px;margin-top:-110px;float:left;width:72%;">';
        $result='<div style="padding-left:5px;position:relative;top:-95px;float:left;width:72%;">';
    }
    return $result;
}
function red_solr_inc_get_body_attributes($body_attributes){
    $result=$body_attributes;
    if(red_solr_inc_is_status_activado()){
        $s='id="page-hontza-solr-busqueda-avanzada';
        $pos=strpos($result,$s);
        if($pos===FALSE){            
            return $result;
        }else{
            $result=str_replace('class="','class="solr_status_activado ',$result);
            return $result;
        }
    }
    return $result;
}
function red_solr_inc_set_item_array_solr_updated($item_array,$updated){
    if(!empty($item_array)){
        foreach($item_array as $i=>$node){
            hontza_solr_set_item_solr_updated($node,$updated);
        }
    }
}    
function red_solr_inc_set_entity_id_array_solr_updated($rows,$updated){
    if(red_solr_inc_is_status_activado()){
        if(!empty($rows)){
            foreach($rows as $i=>$r){
                $node=node_load($r->entity_id);
                if(isset($node->nid) && !empty($node->nid)){
                    hontza_solr_set_item_solr_updated($node,$updated);
                }
            }
        }
    }
}
function red_solr_inc_is_hontza_item_indexado_activado(){
    if(defined('_IS_HONTZA_ITEM_INDEXADO') && _IS_HONTZA_ITEM_INDEXADO==1){
        return 1;
    }
    return 0;
}    
function red_solr_inc_save_hontza_item_indexado($item_array,$indexado){
    if(!empty($item_array)){
        foreach($item_array as $i=>$node){
           $row=red_solr_inc_get_hontza_item_indexado_row($node);
           /*echo print_r($row,1);
           exit();*/
           if(isset($row->nid) && !empty($row->nid)){
               db_query('UPDATE SET indexado=%d {hontza_item_indexado} WHERE nid=%d AND vid=%d',$indexado,$node->nid,$node->vid);
           }else{
               db_query('INSERT INTO {hontza_item_indexado}(nid,vid,indexado) VALUES(%d,%d,%d)',$node->nid,$node->vid,$indexado);
           }
        }
    }
}
function red_solr_inc_get_hontza_item_indexado_row($node){
    $res=db_query('SELECT * FROM {hontza_item_indexado} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_solr_is_inc_custom_limit_index(&$my_index_limit){
    if(defined('_IS_SOLR_CUSTOM_LIMIT_INDEX') && _IS_SOLR_CUSTOM_LIMIT_INDEX==1){
        //$my_index_limit=100;
        $my_index_limit=50;
        return 1;
    }
    return 0;
}
function red_solr_inc_is_actualizar_items(){
    $sareko_id_array=array('ROOT');
    if(in_array(_SAREKO_ID,$sareko_id_array)){
        return 0;
    }
    if(defined('_IS_SOLR_ACTUALIZAR_ITEMS') && _IS_SOLR_ACTUALIZAR_ITEMS!=1){
        return 0;
    }
    return 1;
}
function red_solr_inc_get_categoria_noticia_usuario_num_items($tid,$is_visualizador_actualidad){
    $result=0;
    //if(hontza_solr_is_solr_activado() || $is_visualizador_actualidad){
        $term_node_array=red_solr_inc_get_noticia_usuario_term_node_array($tid);
        $result=count($term_node_array);
    //}
    return $result;
}
function red_solr_inc_get_noticia_usuario_term_node_array($tid){
    $result=array();
    $sql='SELECT term_node.*
    FROM {term_node} term_node
    LEFT JOIN {node} ON term_node.vid=node.vid  
    WHERE node.type="noticia" AND tid=%d';
    $res=db_query($sql,$tid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_solr_fix_indices_callback(){
    $is_node_load=0;
    $item_array=hontza_get_all_nodes(array('item'),'','',0,'',$is_node_load);
    $updated=0;
    red_solr_inc_set_item_array_solr_updated($item_array,$updated);
    if(red_solr_inc_is_status_activado()){
        red_solr_inc_actualizar_seleccionado_boletin($item_array);
        red_solr_inc_actualizar_bookmark($item_array);
        red_solr_inc_actualizar_rated($item_array);
        $updated=1;
        red_solr_inc_set_item_array_solr_updated($item_array,$updated);
    }
    drupal_set_title('Correct Index');
    return date('Y-m-d H:i:s');
}
function red_solr_inc_is_clear_node_index(){    
    return 1;
}
function red_solr_inc_is_rated_clear_node_index($rated){
    if(red_solr_inc_is_clear_node_index()){    
        if($rated>0){
            return 1;
        }
        return 0;
    }
    return 1;
}
function red_solr_inc_is_sended(){
    $sareko_id_array=array('ROOT');
    if(in_array(_SAREKO_ID,$sareko_id_array)){
        return 1;
    }
    if(defined('_IS_SOLR_SEND')){
        if(_IS_SOLR_SEND){
            return 1;
        }
    }
    return 0;
}
function red_solr_inc_update_sended($entity_id,$sended){
    db_query('UPDATE {apachesolr_index_entities_node} SET my_sended=%d WHERE entity_id=%d',$sended,$entity_id);
}
function red_solr_canal_source_type_form_alter(&$form,&$form_state,$form_id){
    //AVISO::::en canal_de_yql también se utiliza la misma funcion
    if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
        $form['field_canal_source_type']['#attributes']=hontza_solr_set_select_multiple_style();
        $default_value=red_solr_inc_get_canal_source_type_default_value($form['field_canal_source_type']['#default_value'],$form['nid']['#value']);
        $form['field_canal_source_type']['#default_value']=
        $form['field_canal_source_type']['#pre_render']=array('hontza_solr_field_canal_source_type_pre_render');
        $form['#field_info']['field_canal_source_type']['allowed_values']=hontza_solr_get_field_canal_source_type_allowed_values();
        $form['field_canal_source_type_updated'][0]['#default_value']['value']=1;
        $form['field_canal_source_type_updated'][0]['#prefix']='<div style="display:none">';
        $form['field_canal_source_type_updated'][0]['#suffix']='</div>';
        red_despacho_node_add_source_type_form_field($form, $form_id, $nid,$default_value);        
    }
}
function red_solr_inc_field_item_source_tid_form_alter(&$form,&$form_state, $form_id,$nid){
    if(isset($form['field_item_source_tid'])){
            $form['field_item_source_tid']['#title']=t('Source Type');
            $form['field_item_source_tid']['#default_value']=red_solr_inc_set_form_field_item_source_tid_default_value($form['field_item_source_tid']['#default_value'],$nid,$form_id);
            $form['field_item_source_tid']['#pre_render']=array('hontza_solr_field_item_source_type_pre_render');
            $form['field_item_source_tid']['#attributes']=  hontza_solr_set_select_multiple_style();
            $form['#field_info']['field_item_source_tid']['allowed_values']=hontza_solr_get_field_item_source_type_allowed_values();            
    }
}
function red_solr_inc_is_noticia_usuario_source_type_activado($form_id){
    if($form_id=='noticia_node_form'){
        if(red_is_canal_usuario_activado()){
            return 1;
        }
    }
    return 0;
}
function red_solr_inc_get_item_source_value_array($node,$form_id=''){
    $result=array();
    $is_canal_usuario_source_tid=0;
    if($form_id=='noticia_node_form'){
        if(red_is_canal_usuario_activado()){
            if(!(isset($node->nid) && !empty($node->nid))){
                $is_canal_usuario_source_tid=1;
            }
        }
    }
    if($is_canal_usuario_source_tid){
        if(red_is_canal_usuario_activado()){
            $result=canal_usuario_get_canal_usuario_source_tid_array();         
        }        
    }else{
        $item_value_array=hontza_solr_get_item_source_value_array($node);
        return $item_value_array;
    }
    return $result;
}
function red_solr_exec_indexar(){
    global $base_url;
    if(red_solr_inc_is_exec_indexar_activado()){
        $url=$base_url.'/red/solr/apachesolr_index_batch_index_remaining';
        $cmd='wget '.$url;
        exec($cmd . " > /dev/null &");
    }
}
function red_solr_inc_is_exec_indexar_activado(){
    return red_solr_inc_is_sended();    
}
function red_solr_inc_apachesolr_index_batch_index_remaining_callback(){
    require_once('sites/all/modules/apachesolr/apachesolr.index.inc');    
    //require_once('sites/all/modules/apachesolr/apachesolr.admin.inc');    
    $env_id = apachesolr_default_environment();
    $status = apachesolr_index_status($env_id);
    //apachesolr_index_batch_index_remaining($env_id);
    $limit=$status['remaining'];
    $kont=apachesolr_index_entities($env_id, $limit);
    return 'kont='.$kont.'='.date('Y-m-d H:i:s');
}
function red_solr_inc_update_all_my_sended($sended){
    if(red_solr_inc_is_sended()){
        db_query('UPDATE {apachesolr_index_entities_node} SET my_sended=%d WHERE 1',$sended);
    }    
}
function red_solr_fix_apachesolr_index_entities_node_callback(){
    $apachesolr_index_entities_node_array=red_solr_get_apachesolr_index_entities_node_array();
    print count($apachesolr_index_entities_node_array);
    if(!empty($apachesolr_index_entities_node_array)){
        foreach($apachesolr_index_entities_node_array as $i=>$row){
            db_query('DELETE FROM {apachesolr_index_entities_node} WHERE apachesolr_index_entities_node.entity_id=%d',$row->entity_id);
        }
    }
    return date('Y-m-d H:i:s');
}
function red_solr_get_apachesolr_index_entities_node_array($is_deleted=1){
    $result=array();
    $res=db_query('SELECT * FROM {apachesolr_index_entities_node} WHERE 1');
    while($row=db_fetch_object($res)){
        //$node=node_load($row->entity_id);
        $node_row=red_get_node_row($row->entity_id);
        if(isset($node_row->nid) && !empty($node_row->nid)){
            if($is_deleted){
                continue;
            }else{
                $result[]=$row;
            }    
        }else{
            if($is_deleted){
                $result[]=$row;
            }
        }
    }
    return $result;
}
function red_solr_inc_guia_internet_callback(){
    if(hontza_is_sareko_id('ROOT')){
        $file_name=arg(2);
        red_funciones_print_guia($file_name);
        exit();
    }
    return '';
}
function red_solr_inc_is_node_title_len_activado(){
    if(defined('_IS_NODE_TITLE_LEN') && _IS_NODE_TITLE_LEN==1){
       return 1; 
    }
    return 0;
}
function red_solr_set_url_sin_redireccionamiento($url){
    $result=$url;
    if(hontza_is_news_google($url,'url',$url_google)){
        $result=$url_google;
    }
    return $result;
}
function red_solr_inc_node_title_length_form_alter(&$form,&$form_state, $form_id){
    if(red_solr_inc_is_node_title_length_form_alter($form_id)){
        $maxlength=red_solr_inc_get_node_title_maxlength();
        $form['title']['#maxlength']=$maxlength;
        if($form_id=='debate_node_form'){
            $form['#field_info']['field_enlace_debate']['columns']['title']['length']=$maxlength;
            $form['field_enlace_debate'][0]['#field']['columns']['title']['length']=$maxlength;            
        }else if($form_id=='wiki_node_form'){
            $form['#field_info']['field_enlace_wiki']['columns']['title']['length']=$maxlength;
            $form['field_enlace_wiki'][0]['#field']['columns']['title']['length']=$maxlength;            
        }
    }
}
function red_solr_inc_is_node_title_length_form_alter($form_id){
    if(red_solr_inc_is_node_title_len_activado()){
        //print $form_id.'<BR>';
        $form_id_array=array('item_node_form','noticia_node_form','estrategia_node_form','despliegue_node_form','decision_node_form','informacion_node_form'
        ,'debate_node_form','wiki_node_form','my_report_node_form','idea_node_form','oportunidad_node_form','proyecto_node_form');
        if(in_array($form_id,$form_id_array)){
            return 1;
        }    
    }
    return 0;
}
function red_solr_inc_get_node_title_maxlength(){
    $result=1024;
    return $result;
}
function red_solr_inc_set_form_link_maxlength(&$element){
    if(red_solr_inc_is_node_title_len_activado()){
        $maxlength=red_solr_inc_get_node_title_maxlength();
        $name_array=array('field_enlace_debate[0][title]','field_enlace_wiki[0][title]');
        if(in_array($element['#name'],$name_array)){
            $element['#maxlength']=$maxlength;
        }
    }
}
function red_solr_reset_noticia_usuario_apachesolr_index_entities_node_callback(){
    $is_node_load=0;
    $noticia_usuario_array=hontza_get_all_nodes(array('noticia'),'','',0,'',$is_node_load);
    if(!empty($noticia_usuario_array)){
       foreach($noticia_usuario_array as $i=>$row){
           if(isset($row->nid) && !empty($row->nid)){
               $apachesolr_index_entities_node_changed_row=red_solr_inc_get_apachesolr_index_entities_node_row($row->nid);
               if(isset($apachesolr_index_entities_node_changed_row->entity_id) && !empty($apachesolr_index_entities_node_changed_row->entity_id)){
                   hontza_canal_rss_update_apachesolr_index_entities_node_changed($row->nid);
               }else{
                   red_solr_inc_insert_apachesolr_index_entities_node($row->nid);                
               }
           }
       }
   }
   drupal_set_title(t('Index'));
   return date('Y-m-d H:i:s');
}
function red_solr_inc_get_apachesolr_index_entities_node_row($nid){
    $res=db_query('SELECT * FROM {apachesolr_index_entities_node} WHERE apachesolr_index_entities_node.entity_id=%d',$nid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_solr_inc_insert_apachesolr_index_entities_node($nid,$bundle='noticia'){
  $entity_type='node';
  $status=1;
  $changed=time();
  $my_sended=0;
  db_query('INSERT INTO {apachesolr_index_entities_node}(entity_type,entity_id,bundle,status,changed,my_sended) VALUES("%s",%d,"%s",%d,%d,%d)',$entity_type,$nid,$bundle,$status,$changed,$my_sended);
}
function red_solr_inc_set_form_field_item_source_tid_default_value($default_value,$nid,$form_id=''){
    $node=node_load($nid);
    $result=hontza_solr_set_form_field_item_source_tid_default_value($default_value,$nid,$form_id,$node);
    if(!(isset($node->nid) && !empty($node->nid))){
        red_solr_inc_add_source_type_values_array('Noticias de usuario',$result);
    }
    return $result;
}
function red_solr_inc_taxonomy_get_term_by_name_vid_row($name,$vid){
    require_once('sites/all/modules/feeds/mappers/taxonomy.inc');
    $my_array=taxonomy_get_term_by_name_vid($name, $vid);
    if(isset($my_array[0]) && !empty($my_array[0])){
        return $my_array[0];
    }else{
        if($vid==1 && $name=='Noticias de usuario'){
            $konp='User News';
            $my_array=taxonomy_get_term_by_name_vid($konp, $vid);
            if(isset($my_array[0]) && !empty($my_array[0])){
                return $my_array[0];
            }
        }
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_solr_inc_in_values_array($konp,$values_array){
    if(!empty($values_array)){
        foreach($values_array as $i=>$row){
            if($konp==$row['value']){
                return 1;
            }
        }
    }
    return 0;
}
function red_solr_inc_get_canal_source_type_default_value($default_value,$nid,$form_id=''){
    $canal=node_load($nid);
    $result=hontza_solr_get_canal_source_type_default_value($default_value,$nid,$form_id,$canal);
    if(!(isset($canal->nid) && !empty($canal->nid))){
        red_solr_inc_add_source_type_values_array('Noticias de usuario',$result);        
    }
    return $result;
}
function red_solr_inc_add_source_type_values_array($name,&$result){
        $vid=1;
        $user_news_term=red_solr_inc_taxonomy_get_term_by_name_vid_row($name,$vid);
        if(isset($user_news_term->tid) && !empty($user_news_term->tid)){
            if(!red_solr_inc_in_values_array($user_news_term->tid,$result)){
                $result[]=array('value'=>$user_news_term->tid);
            }
        }
}
function red_solr_inc_get_content_field_item_source_tid_array($nid,$vid,$with_row=1){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_item_source_tid} WHERE nid=%d AND vid=%d ORDER BY delta DESC',$nid,$vid));
    while($row=db_fetch_object($res)){
        if($with_row){
            $result[]=$row;
        }else{
            $result[]=$row->field_item_source_tid_value;
        }    
    }
    return $result;
}
function red_solr_inc_get_content_field_canal_source_type_array($nid,$vid){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_canal_source_type} WHERE nid=%d AND vid=%d ORDER BY delta DESC',$nid,$vid));
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_solr_inc_get_bookmarked_sql($my_grupo_in=''){
    global $user;
    $my_grupo_nid=0;
    if(isset($my_grupo_in->nid) && !empty($my_grupo_in->nid)){
        $my_grupo_nid=$my_grupo_in->nid;
    }else{
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $my_grupo_nid=$my_grupo->nid;
        }
    }    
            $where=array();
            $where[]='1';
            $where[]='hontza_bookmark.uid='.$user->uid;
            $where[]=hontza_get_vigilancia_where_filter();
            $where[]='og_ancestry.group_nid='.$my_grupo_nid;
            $result='SELECT hontza_bookmark.*
            FROM {node} node
            LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
            LEFT JOIN {hontza_bookmark} hontza_bookmark ON node.vid=hontza_bookmark.vid        
            WHERE '.implode(' AND ',$where).'
            GROUP BY hontza_bookmark.nid    
            ORDER BY hontza_bookmark.id ASC';
    return $result;        
}
function red_solr_inc_get_busqueda_simple_url($text,&$url,&$query,$is_movil=0){
    $url='my_solr/my_search';
    $my_array=array();
    if($is_movil){
        $my_grupo=red_movil_get_current_grupo_node();
    }else{
        $my_grupo=og_get_group_context();        
    }
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $i=count($my_array);
        $my_array[]='f['.$i.']=im_og_gid:'.$my_grupo->nid;
    }
    //
    $i=count($my_array);
    $search_array=array();
    $search_array[]=$text;
    if(!empty($search_array)){
        $url=$url.'/'.implode(' AND ',$search_array);
    }
    $query=implode('&',$my_array);
    if(!empty($query)){
        $query.='&solrsort=ds_created desc';
    }
    //$result=url($url,array('query'=>$query));
    //return $result;
}    
function red_solr_inc_reclasificar_tipo_fuente_item($node,$tid_array){
    hontza_solr_reset_item_source_tid($node);
    if(!empty($tid_array)){
        $delta=0;
        foreach($tid_array as $k=>$tid){
            if($delta>0 || ($delta==0 && !red_solr_inc_existe_content_field_item_source_tid_row($node))){
                $res=db_query('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
            }else{
                $res=db_query('UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$tid,$node->nid,$node->vid);
            }    
            $delta++;
        }
    }
    $updated=0;
    hontza_solr_set_item_solr_updated($node,$updated);
    hontza_solr_search_clear_cache_content($node);
}
function red_solr_inc_existe_content_field_item_source_tid_row($node){
    if(hontza_solr_is_solr_activado()){
        $res=db_query('SELECT * FROM {content_field_item_source_tid} WHERE nid=%d AND vid=%d AND delta=0',$node->nid,$node->vid);
        while($row=db_fetch_object($res)){
            return 1;
        }
    }    
    return 0;
}
function red_solr_inc_get_bookmark_reclasificar_tipo_fuente_link($style){
    $html=array();    
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('reclasificar_tipos_de_fuentes', t('Types of Sources'),''),'hontza_solr/reclasificar_tipo_fuente_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_reclasificar_tipo_fuente_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';
    return implode('',$html);
}
function red_solr_inc_is_show_bookmark_reclasificar_tipo_fuente_link(){
    if(red_despacho_is_show_categorizar_link()){
        if(hontza_solr_search_modificar_taxonomia_access()){
           return 1; 
        }
    }
    return 0;
}
function red_solr_inc_reclasificar_tipo_fuente_titles_html($node_id_array,$url_return,&$is_return,&$title,$is_solr=0){
    $html=array();
    if(!red_solr_inc_is_show_bookmark_reclasificar_tipo_fuente_link()){
        drupal_access_denied();
        exit();
    }
    $is_return=0;
    $title=t('Types of Sources');
    $html[]=red_solr_inc_reclasificar_tipo_fuente_html($node_id_array,$url_return,$is_solr);
    return implode('',$html);
}
function red_solr_inc_reclasificar_tipo_fuente_html($node_id_array,$url_return,$is_solr){
    return drupal_get_form('red_solr_inc_reclasificar_tipo_fuente_form',$node_id_array,$url_return,$is_solr);
}
function red_solr_inc_reclasificar_tipo_fuente_form(&$form_state,$node_id_array,$url_return_in,$is_solr){
    $form=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $my_destination='';
    if(isset($_REQUEST['my_destination']) && !empty($_REQUEST['my_destination'])){
        $my_destination=$_REQUEST['my_destination'];
    }else{
        $my_destination=$url_return_in;
    }
    $form['node_id_array']=array(
        '#type'=>'hidden',
        '#default_value'=>implode(',',$node_id_array),
    );
    $form['url_return']=array(
        '#type'=>'hidden',
        '#default_value'=>$url_return,
    );
    $form['is_solr']=array(
        '#type'=>'hidden',
        '#default_value'=>$is_solr,
    );
    $form['my_destination']=array(
        '#type'=>'hidden',
        '#default_value'=>$my_destination,
    );
    $id_categoria=1;
    if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
        hontza_solr_search_add_source_type_form_field($form,0,0,0,$node);        
    }    
    $form['id_categoria']=array(
        '#type'=>'hidden',
        '#default_value'=>$id_categoria,
    );    
    $form['add_btn']=array(
        '#type'=>'submit',
        '#name'=>'add_btn',
        '#default_value'=>t('Add'),
    );    
    $form['replace_btn']=array(
        '#type'=>'submit',
        '#name'=>'replace_btn',
        '#default_value'=>t('Replace'),
    );    
    //if($is_solr){
        $url_info=parse_url($url_return_in);
        $link_return=l(t('Return'),$url_info['path'],array('query'=>$url_info['query']));
    /*}else{
        $link_return=l(t('Return'),$url_return);
    }*/
    $form['return_btn']=array(
        '#value'=>$link_return,
    );
    return $form;
}
function red_solr_inc_reclasificar_tipo_fuente_form_submit($form,&$form_state){
    $node_id_array=$form_state['values']['node_id_array'];
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg_string($node_id_array);
    $node_id_array=explode(',',$node_id_array);
    $url_return=$form_state['values']['url_return'];
    $is_solr=$form_state['values']['is_solr'];
    if($is_solr){
        $url_return=base64_decode($url_return);
    }    
    red_despacho_reclasificar_tipo_fuente_save($form_state,$node_id_array);
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);        
    hontza_solr_funciones_redirect();
}
function red_solr_inc_add_reclasificar_tipo_fuente_item($node,$tid_array){
    $content_field_item_source_tid_array=hontza_solr_get_content_field_item_source_tid_array($node->nid,$node->vid,1);
    $delta=hontza_solr_search_get_next_delta($content_field_item_source_tid_array);
    //
    if(!empty($tid_array)){
        foreach($tid_array as $k=>$tid){
            if(!red_solr_inc_in_content_field_item_source_tid_array($tid,$content_field_item_source_tid_array)){
                $res=db_query('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
                $delta++;
            }               
        }
    }
    $updated=0;
    hontza_solr_set_item_solr_updated($node,$updated);
    hontza_solr_search_clear_cache_content($node);
}
function red_solr_inc_in_content_field_item_source_tid_array($tid,$content_field_item_source_tid_array){
    if(!empty($content_field_item_source_tid_array)){
        foreach($content_field_item_source_tid_array as $i=>$row){
            if($row->field_item_source_tid_value==$tid){
                return 1;
            }
        }
    }
    return 0;
}
function red_solr_inc_existe_term($tid){
    //if(red_despacho_is_activado()){
        $term=taxonomy_get_term($tid);
        if(isset($term->tid) && !empty($term->tid)){
            return 1;
        }
        return 0;
    //}
    //return 1;
}
function red_solr_inc_get_existe_term_tid_entity_field_name_array($entity_field_name_array){
    $result=array();
    if(!empty($entity_field_name_array)){
        foreach($entity_field_name_array as $i=>$row){
            if(red_solr_inc_existe_term($row['value'])){
                $result[]=$row;
            }
        }
    }
    return $result;
}
function red_solr_inc_get_existe_term_solr_left_by_widget_links($element,$type='term'){
    $result=array();
    if(!empty($element)){
        foreach($element as $key=>$row){
            if($type=='term'){
                if(red_solr_inc_existe_term($key)){
                    $result[]=$row;
                }
            }else if($type=='node'){
                if(red_solr_inc_existe_node($key)){
                    $result[]=$row;
                }
            }else{
                 $result[]=$row;
            }
        }
    }
    return $result;
}
function red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$label){
    if($is_busqueda_avanzada_solr){
        $max=45;
        if(strlen($label)>$max){
            $result=substr($label,0,$max).' ...';
            return $result;
        }
    }
    return $label;
}
function red_solr_inc_get_categorias_options_label_array($categorias,$is_busqueda_avanzada_solr){
    $result=array();
    if(!empty($categorias)){
        foreach($categorias as $tid=>$label){
            $result[$tid]=red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$label);
        }
    }
    return $result;
}
function red_solr_inc_get_select_options_label($element){
    if(is_fuentes_pipes_todas()){
        $result=$element;
        if($element['#name']=='tid'){
            if(isset($element['#options']) && !empty($element['#options'])){
                foreach($element['#options'] as $tid=>$label){
                    $result['#options'][$tid]=red_solr_inc_get_tipo_fuente_options_label(1,$label);
                }
            }
        }/*else if($element['#name']=='field_supercanal_my_tipo'){
            $result['#options']=red_solr_inc_get_fuente_origin_options_label($element['#options']);           
        }*/
        return $result;
    }    
    return $element;
}
function red_solr_inc_get_boletin_report_bulk_actions_links($style){
    $html=array();
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('boletin', t('Select for the Bulletin'),''),'hontza_solr/report_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_report_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('no-boletin', t('Unselect from Bulletin'),''),'hontza_solr/unselect_report_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_unselect_report_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';        
    return implode('',$html);
}
function red_solr_inc_unselect_report_titles_html($node_id_array,$url_return,&$is_return,&$title,$is_solr){
    $html=array();
    $title=t('Unselect from Bulletin');
    if(!empty($node_id_array)){
        $is_return=0;
        $html[]=hontza_solr_funciones_get_selected_node_titles($node_id_array);
        $html[]=hontza_solr_funciones_report_html($node_id_array,$url_return,$is_solr,'unselect_report_bookmark');    
    }    
    return implode('',$html);    
}
function red_solr_inc_get_mark_bulk_actions_links($is_solr,$style){
    $html=array();
    $html[]='<div style="'.$style.'">';
    //if($is_solr){
        $html[]=l(my_get_icono_action('bookmark', t('Mark'),''),'hontza_solr/mark_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_mark_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));        
    $html[]='</div>';
    $html[]='<div style="'.$style.'">';
    //}else{
        $html[]=l(my_get_icono_action('no_bookmark', t('Unmark'),''),'hontza_solr/unmark_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_unmark_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    //}    
    $html[]='</div>';
    return implode('',$html); 
}
function red_solr_inc_get_fuente_origin_options_label($options){
    $result=array();
    if(isset($options['All'])){
        $result['All']=$options['All'];
        $result['kimonolabs']='Kimonolabs';
    }
    return $result;
}
function red_solr_inc_get_fuente_origin_where($konp_in){
    $where=array();
    if($konp_in=='kimonolabs'){
        $konp='supercanal';
    }
    $where[]="node.type='".$konp."'";    
    if($konp_in=='kimonolabs'){
        $where[]="node_data_field_supercanal_calidad.field_is_kimonolabs_value=1";
    }
    return "(".implode(" AND ",$where).")";    
}
function red_solr_inc_get_existe_node_solr_left_by_widget_links($element){
    return red_solr_inc_get_existe_term_solr_left_by_widget_links($element,'node');
}
function red_solr_inc_existe_node($nid){
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        return 1;
    }
    return 0;
}
function red_solr_inc_resaltar_termino_busqueda($result_in,$is_title=0){
    $result=$result_in;
    if(hontza_solr_is_resultados_pantalla() || red_solr_inc_is_resaltar_termino()){
        $needle=red_solr_inc_get_termino('',0,$is_title);        
        if(!empty($needle)){
            $my_array=array();
            if(is_array($needle) && count($needle)>1){
                $my_array=$needle;
                if(!empty($my_array)){
                    foreach($my_array as $i=>$value){
                        $my_array[$i]=red_solr_inc_get_termino($value,1,$is_title);     
                    }
                }
            }else{
                $my_array[]=$needle;
            }
            if(!empty($my_array)){
                foreach($my_array as $i=>$value){                    
                    $result=red_solr_inc_resaltar_termino($result, $value,'red');
                }
                if(red_solr_inc_is_termino_in_html($result)){
                    return $result_in;
                }
                $replace='<span style="color:red;">';
                //$replace='<span style="color:red;font-weight:bold;">';                
                $result=str_replace('<abcdef>',$replace,$result);
                $result=str_replace('</abcdef>','</span>',$result);                
            }    
        }    
    }
    return $result;
}
function red_solr_inc_resaltar_termino($haystack, $needle, $highlightColorValue) {
     // return $haystack if there is no highlight color or strings given, nothing to do.
    if (strlen($highlightColorValue) < 1 || strlen($haystack) < 1 || strlen($needle) < 1) {
        return $haystack;
    }
    @preg_match_all("/$needle+/i", $haystack, $matches);
    if (is_array($matches[0]) && count($matches[0]) >= 1) {
        foreach ($matches[0] as $match) {
            //$haystack = str_replace($match, '<span style="background-color:'.$highlightColorValue.';">'.$match.'</span>', $haystack);
            $haystack = str_replace($match, '<abcdef>'.$match.'</abcdef>', $haystack);
        }
    }
    return $haystack;
}
function red_solr_inc_get_termino($result_in='',$is_param=0,$is_title=0){
    if($is_param){
        $result=$result_in;
    }else{
        if(hontza_solr_is_resultados_pantalla()){
            $result=arg(2);
        }else{
            $result=unserialize(base64_decode($_REQUEST['resaltar_termino']));
        }    
    }
    if(red_solr_inc_is_not_termino($result,$is_title)){
        return '';
    }
    $result=red_solr_inc_get_termino_multiple($result);
    if(count($result)==1){
        $result=$result[0];
    }else{
        return $result;
        //$result=$result[0];
    }
    if($is_title){
        $needle='(label:';
    }else{
        $needle='(content:';    
    }    
    $pos=strpos($result,$needle);
    if($pos===FALSE){        
        //return '';
    }else{
        if($pos==0){
            $result=str_replace($needle,'',$result);
            $result=substr($result,0,strlen($result)-1);
        }
    }    
    return $result;
}
function red_solr_inc_is_not_termino($result,$is_title=0){
    $my_array=array('(-label:','(-content:');
    if($is_title){
        $my_array[]='(content:';
    }
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            $pos=strpos($result,$value);
            if($pos===FALSE){        
                continue;
            }else{
                if($pos==0){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function red_solr_inc_get_termino_multiple($result_in){
    $result=array();
    $my_array=explode(' AND ',$result_in);
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if(!red_solr_inc_is_not_termino($value)){
                $result[]=$value;
            }            
        }
    }    
    return $result;
}
function red_solr_inc_get_title_link_param(){
    $result=array();
    if(hontza_solr_is_resultados_pantalla()){
        //$resaltar_termino=base64_encode(serialize(red_solr_inc_get_termino()));
        $resaltar_termino=base64_encode(serialize(arg(2))); 
        $result['query']='resaltar_termino='.$resaltar_termino.'&'.drupal_get_destination();
        $result['html']=TRUE;
    }
    return $result;
}
function red_solr_inc_is_resaltar_termino(){
    if(isset($_REQUEST['resaltar_termino']) && !empty($_REQUEST['resaltar_termino'])){
        return 1;
    }
    return 0;
}
function red_solr_inc_is_termino_in_html($html){
    $needle='<abcdef>';            
    $dom = new domDocument; 
                @$dom->loadHTML($html); 
                $dom->preserveWhiteSpace = false;
                $images = $dom->getElementsByTagName('img');

                foreach ($images as $image) 
                   {   
                     $result=$image->getAttribute('src');
                     if(red_solr_inc_is_termino_in_html_strpos($result,$needle)){
                         return 1;
                     }
                   }
                   
                $link_array = $dom->getElementsByTagName('a');

                foreach ($link_array as $link) 
                   {   
                     $result=$link->getAttribute('href');
                     if(red_solr_inc_is_termino_in_html_strpos($result,$needle)){
                         return 1;
                     }
                   }   
    return 0;               
}
function red_solr_inc_is_termino_in_html_strpos($value,$needle){
    $pos=strpos($value,$needle);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
//intelsat-2016
function red_solr_inc_is_actualizar_noticias_usuario(){
    if(module_exists('noticias_usuario_solr')){
        if(defined('_IS_SOLR_ACTUALIZAR_NOTICIAS_USUARIO') && _IS_SOLR_ACTUALIZAR_NOTICIAS_USUARIO==1){
            return 1;
        }
    }
    return 0;
}
//intelsat-2016
function red_solr_simular_remaining_callback(){
    $status=red_solr_inc_get_index_status();
    echo print_r($status,1);
    exit();
}
//intelsat-2016
function red_solr_inc_add_remaining_html(&$html){
    $status_html=red_solr_inc_get_index_status_html();
    if(!empty($status_html)){
        $html[]=$status_html;
    }
}
//intelsat-2016
function red_solr_inc_get_index_status(){
    require_once 'sites/all/modules/apachesolr/apachesolr.index.inc';
    $env_id = apachesolr_default_environment();
    $environment = apachesolr_environment_load($env_id);
    $status = apachesolr_index_status($environment["env_id"]);
    return $status;
}
//intelsat-2016
function red_solr_inc_get_index_status_html(){
    $html=array();        
    $status=red_solr_inc_get_index_status();
    if(isset($status['remaining']) && !empty($status['remaining'])){
        $html[]='<fieldset>';
        $html[]='<legend>'.t('Index status').'</legend>';
        $html[]='<div style="padding:5px;float:left;">';
        $html[]='<p style="color:red;">'.t('Results may be inaccurate. There are @status_remaining news waiting to be indexed',array('@status_remaining'=>$status['remaining'])).'</p>';        
        $html[]='</div>';
        $html[]='<div style="padding:5px;float:left;">';
        $html[]='<input id="index_status_btn" type="button" value="'.t('Index all queued content').'">';
        $html[]='</div>';
        $html[]='</fieldset>';
        red_solr_inc_add_index_status_js();
    }    
    return implode('',$html);
}
function red_solr_inc_add_index_status_js(){
    $destination=drupal_get_destination();
    //$is_destination=1;
    //$destination='destination='.my_get_busqueda_simple_content(0,$is_destination);
    
    $js='$(document).ready(function()
			{
			   $("#index_status_btn").click(function(){
                            window.location.href="'.url('red/solr/index/remaining',array('query'=>$destination)).'";
                           });
			});';
			
			drupal_add_js($js,'inline');
}
function red_solr_index_remaining_access(){
    if(red_funciones_is_administrador_grupo()){
        return TRUE;
    }
    return FALSE;
}
function red_solr_index_remaining_callback(){
    require_once 'sites/all/modules/apachesolr/apachesolr.admin.inc';
    $form_state=array();
    $environment = apachesolr_environment_load($env_id);
    return drupal_get_form('apachesolr_index_action_form_remaining_confirm',$form_state,$environment);   
}
function red_solr_inc_get_query_type_term_build($response,$values_in,$facet_field){
    $result=$values_in;
    $result=red_solr_inc_unset_facet_field_tipo($result,$facet_field);
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_solr_get_query_type_term_build($response,$result,$facet_field);
    }   
    return $result;
}
function red_solr_inc_get_widget_links_element($field_alias,$element_in,$field_in){
    $result=$element_in;
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_solr_get_widget_links_element($field_alias,$element_in,$field_in);
    }
    return $result;
}
function red_solr_inc_define_entity_field_name_noticia_validador_uid($entity_field_name,$entity){
    $result=$entity_field_name;
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_solr_define_entity_field_name_noticia_validador_uid($entity_field_name,$entity);
    }
    return $result;
}
function red_solr_inc_busqueda_avanzada_form_submit_search_array($search_array_in,$noticias_usuario_search_array){
    $result=$search_array_in;
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_solr_busqueda_avanzada_form_submit_search_array($result,$noticias_usuario_search_array);
    }
    return $result;
}
function red_solr_inc_facetapi_check_block_visibility($facet_name){
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        return noticias_usuario_solr_facetapi_check_block_visibility($facet_name);
    }
    return TRUE;
}
function red_solr_inc_add_delete_validador_filtros(){
    $result=array();
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_solr_inc_add_delete_validador_filtros();
    }
    return $result;
}
function red_solr_inc_add_add_delete_im_field_item_validador_uid_filtros($value,&$result,$my_array=array(),$is_noticias_usuario_in=0,$field='im_field_item_validador_uid',$validate_status=''){
    $label=$value;
    if($field=='im_field_item_validador_uid'){
        $label=hontza_get_username($value);
    }
    if(!empty($label)){
        $is_noticias_usuario=$is_noticias_usuario_in;
        if(!red_solr_inc_is_actualizar_noticias_usuario()){
            $is_noticias_usuario=0;
        }
        if($is_noticias_usuario){
            $icono_link=noticias_usuario_solr_get_canal_icono_link($value,$field,$validate_status);
        }else{
            $icono_link=hontza_solr_get_canal_icono_link($value,$field,$my_array);
        }
        $result[]=$icono_link.$label;
    }
}
function red_solr_inc_noticias_usuario_add_delete_validate_status_filtros(){
    $result=array();
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_add_delete_validate_status_filtros();
    }
    return $result;
}
function red_solr_inc_noticias_usuario_add_delete_scoring_filtros(){
    $result=array();
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        $result=noticias_usuario_add_delete_scoring_filtros();
    }
    return $result;
}
function red_solr_inc_add_delete_ftm_field_item_rated_filtros($scoring,&$result){
    $scoring=red_solr_inc_get_scoring_value($scoring);
    if(!empty($scoring)){
        $scoring_label=red_solr_inc_get_scoring_label($scoring);
        $icono_link=hontza_solr_get_canal_icono_link($value_array[1],'ftm_field_item_rated',$my_array);
        $result[]=$icono_link.$scoring_label;
    }
}
function red_solr_inc_notica_node_form_alter(&$form,&$form_state,$form_id){
    /*if(red_solr_inc_is_actualizar_noticias_usuario()){
        noticias_usuario_solr_notica_node_form_alter($form,$form_state,$form_id);
    }*/
    $unset_array=array('field_item_canal_category_tid','field_noticia_validador_uid','field_noticia_validate_status',
    'field_noticia_seleccionado_bolet','field_noticia_bookmark','field_noticia_rated','field_item_fid');
    red_movil_unset_form_field_form_alter($form,$form_state,$form_id,$unset_array);
}
function red_solr_inc_get_my_order_options(){
    $result=array();
    $result[1]=t('By date');
    //$result[2]=t('By validation');
    //$result[3]=t('By rating');
    $result[4]=t('By comments');
    //$result[5]=t('By bookmarks');
    return $result;
}
function red_solr_inc_is_my_order(){
    if(red_solr_inc_is_actualizar_noticias_usuario()){
        if(isset($_SESSION['my_order_solr']) && !empty($_SESSION['my_order_solr'])){
          return 1;
        }
    }
    return 0;
}
function red_solr_inc_get_my_limit(){
    $result=10;
    return $result;
}
function red_solr_inc_get_my_results($result_in){
    $result=$result_in;
    if(red_solr_inc_is_my_order()){
        $my_limit=red_solr_inc_get_my_limit();
        if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])){
            $page=$_REQUEST['page'];
            $pos=$page*$my_limit;
            $result=array_slice($result,$pos);
        }
    }    
    return $result;
}
function red_solr_inc_is_aplicar_my_order(){
    if(isset($_SESSION['my_order_solr']) && !empty($_SESSION['my_order_solr'])){
        if($_SESSION['my_order_solr']!=1){
            return 1;
        }
    }
    return 0;
}
function red_solr_inc_set_my_order($my_order){
    if(isset($_SESSION['my_order_solr'])){
        unset($_SESSION['my_order_solr']);
    }
    if(!empty($my_order) && $my_order!=1){
        $_SESSION['my_order_solr']=$my_order;
    }
}
function red_solr_inc_get_my_order_results($result_in){
    $result=$result_in;
    $my_order_solr='';
    if(isset($_SESSION['my_order_solr']) && !empty($_SESSION['my_order_solr'])){
        $my_order_solr=$_SESSION['my_order_solr'];
    }
    if($my_order_solr==2){
        $result=red_solr_inc_get_my_order_results_validation($result_in);
    }else if($my_order_solr==3){
        $result=red_solr_inc_get_my_order_results_rating($result_in);
    }else if($my_order_solr==4){
        $result=red_solr_inc_get_my_order_results_commented($result_in);
    }else if($my_order_solr==5){
        $result=red_solr_inc_get_my_order_results_bookmarks($result_in);
    }
    return $result;
}
function red_solr_inc_get_my_order_results_validation($result_in){
    $my_array=red_solr_inc_set_validation_order($result_in);
    $is_numeric=1;
    $result=array_ordenatu($my_array,'validation','desc', $is_numeric,2,'created');
    return $result;
}
function red_solr_inc_set_validation_order($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $my_node=$row['node'];
            $my_node->nid=$my_node->entity_id;
            $result[$i]['validation']=red_solr_inc_get_validation_order_value($my_node);
            $result[$i]['created']=$my_node->created;
        }
    }
    return $result;
}
function red_solr_inc_get_validation_order_value($my_node){
    $result=array();
    $result[1]=2;
    $result[2]=3;
    $result[3]=1;
    $value=red_despacho_get_validate_status($my_node);
    if(isset($result[$value]) && !empty($result[$value])){
        return $result[$value];
    }
    return 1000;
}
function red_solr_inc_get_my_order_results_rating($result_in){
    $my_array=red_solr_inc_set_rating_order($result_in);
    $is_numeric=1;
    $result=array_ordenatu($my_array,'rating','desc', $is_numeric,2,'created');
    return $result;
}
function red_solr_inc_set_rating_order($result_in){
    $result=$result_in;
    $is_value=1;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $my_node=$row['node'];
            $my_node->nid=$my_node->entity_id;
            $result[$i]['rating']=hontza_get_node_puntuacion_media_para_txt($my_node->entity_id,$is_value);
            $result[$i]['created']=$my_node->created;            
        }
    }
    return $result;
}
function red_solr_inc_get_my_order_results_commented($result_in){
    $my_array=red_solr_inc_set_commented_order($result_in);
    $is_numeric=1;
    $result=array_ordenatu($my_array,'commented','desc', $is_numeric,2,'created');
    return $result;
}
function red_solr_inc_set_commented_order($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $my_node=node_load($row['node']->entity_id);
            $result[$i]['commented']=$my_node->comment_count;
            $result[$i]['created']=$my_node->created;
        }
    }
    return $result;
}
function red_solr_inc_get_my_order_results_bookmarks($result_in){
     $my_array=red_solr_inc_set_bookmarks_order($result_in);
     $is_numeric=1;
     $result=array_ordenatu($my_array,'bookmarks','desc', $is_numeric,2,'created');
     return $result;
}
function red_solr_inc_set_bookmarks_order($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $my_node=$row['node'];
            $my_node->nid=$my_node->entity_id;
            $result[$i]['bookmarks']=red_solr_inc_get_bookmark($my_node);
            $result[$i]['created']=$my_node->created;
        }
    }
    return $result;
}
function red_solr_get_index_action_form_remaining_confirm_destination($path_in){
    $result=$path_in;
    if(red_solr_inc_index_remaining_pantalla()){
        if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
            $result=$_REQUEST['destination'];
        }
    }
    return $result;
}
function red_solr_get_index_action_form_remaining_confirm_destination_url($my_grupo,$query_busqueda_avanzada_solr){
    global $base_url;
    $result=url($base_url.'/'.$my_grupo->purl.'/hontza_solr/busqueda_avanzada_solr',array('query'=>$query_busqueda_avanzada_solr));
    $result=urlencode($result);
    return $result;
}
function red_solr_inc_index_remaining_pantalla(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='red'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='solr'){
            $param2=arg(2);
            if(!empty($param2) && $param2=='index'){
                $param3=arg(3);
                if(!empty($param3) && $param3=='remaining'){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function red_solr_inc_get_tipo_noticia_options(){
    $result=array();
    $result[0]='';
    //$result[1]='Automatic news';
    $result[1]=t('Automatic');
    $result[2]=t('User news');
    return $result;
}
function red_solr_inc_get_fuente_tipo_noticia_tid(){
    $vid=1;
    $user_news_term=red_solr_inc_taxonomy_get_term_by_name_vid_row('Noticias de usuario',$vid);
    if(isset($user_news_term->tid) && !empty($user_news_term->tid)){
        return $user_news_term->tid;
    }
    return '';
}
function red_solr_inc_apachesolr_index_action_form_remaining_confirm_form_alter(&$form,&$form_state,$form_id){
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url=drupal_get_destination();
        $url=urldecode(ltrim($url,'destination='));
        $url_info=parse_url($url);
        $form['actions']['cancel']['#value']=l(t('Cancel'),$url_info['path'],array('query'=>$url_info['query']));
    }
}
function red_solr_inc_add_tipo_noticia_query($form_state,&$my_array){
    if(isset($form_state['values']['tipo_noticia']) && !empty($form_state['values']['tipo_noticia'])){
        $tipo_noticia=$form_state['values']['tipo_noticia'];    
        if(!empty($tipo_noticia)){
            //$i=count($my_array);
            $tipo_noticia_tid=red_solr_inc_get_fuente_tipo_noticia_tid();            
                if($tipo_noticia==1){
                    //$my_array[]='f['.$i.']=itm_field_item_source_tid:- '.$tipo_noticia_tid;
                    //$my_array[]='f['.$i.']=itm_field_item_source_tid:([* TO *] NOT '.$tipo_noticia_tid.')';
                    //$my_array[]='f['.$i.']=itm_field_item_source_tid:(*:* NOT '.$tipo_noticia_tid.')';
                    $my_array[]='bundle:"item"';
                }else if($tipo_noticia==2){
                    //$my_array[]='f['.$i.']=itm_field_item_source_tid:'.$tipo_noticia_tid;
                    $my_array[]='bundle:"noticia"';
                }
            
        }        
    }
}
function red_solr_inc_unset_page($result_in,$query){
    $result=$result_in;
    if(isset($query['page'])){
        $konp='page='.$query['page'];
        $result=str_replace($konp.'&','',$result);
        $result=str_replace($konp,'',$result);
    }
    return $result;
}
function red_solr_inc_delete_content_field_item_canal_category_tid_solo_null($node,$content_field_item_canal_category_tid_array){
    if(empty($content_field_item_canal_category_tid_array)){
        db_query('DELETE FROM {content_field_item_canal_category_tid} WHERE nid=%d AND vid=%d AND field_item_canal_category_tid_value IS NULL',$node->nid,$node->vid);    
    }
}
function red_solr_inc_is_show_tipo($is_busqueda_avanzada_solr,$term,$noticia_usuario_tid){
    if($is_busqueda_avanzada_solr){
        if($term->tid==$noticia_usuario_tid){
            return 0;
        }
    }
    return 1;
}
function red_solr_inc_unset_facet_field_tipo($result_in,$facet_field){
    $result=array();
    if($facet_field=='itm_field_item_source_tid'){
        $noticia_usuario_tid=red_solr_inc_get_fuente_tipo_noticia_tid();
        if(!empty($result_in)){
            foreach($result_in as $tid=>$value){
                if($tid!=$noticia_usuario_tid){
                    $result[$tid]=$value;
                }
            }
            return $result;
        }
    }
    return $result_in;
}
function red_apachesolr_query_prepare($query) {
  $query->setAvailableSort('is_comment_count', array(
    'title' => t('By comments'),
    'default' => 'desc',
  ));
}
function red_solr_inc_get_query_solrsort($form_state){
  $field='ds_created';
  if(isset($form_state['values']['my_order']) && !empty($form_state['values']['my_order'])){
    if($form_state['values']['my_order']==4){
        $field='is_comment_count desc,ds_created';
    }
  }
  $result='&solrsort='.$field.' desc';
  $_SESSION['my_order_solrsort_form']=$result;
  return $result;    
}
function red_solr_inc_update_noticia_rated($node,$rated_value){
    if(red_solr_inc_is_actualizar_noticias_usuario()){        
        if($node->type=='noticia'){
            db_query('UPDATE {content_type_noticia} SET field_noticia_rated_value=%f WHERE nid=%d AND vid=%d',$rated_value,$node->nid,$node->vid);
        }
    }
}