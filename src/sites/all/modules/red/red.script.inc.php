<?php
function red_script_print_regiones_proyecto_alerta_callback(){
    $regiones=array();
    $canales=hontza_get_all_nodes(array('canal_de_supercanal','canal_de_yql'));
    if(!empty($canales)){
        foreach($canales as $i=>$node){
            //intelsat-2016
            $field_red_country=red_fields_inc_get_field_country_name();
            if(isset($node->$field_red_country)){
                $row=$node->$field_red_country;
                if(isset($row[0]['value'])){
                    $v=$row['value'];
                    if(!empty($v)){
                        if(!in_array($v,$regiones)){
                            $regiones[]=$v;                        
                        }
                    }
                }
            }    
        }
    }
    sort($regiones);
    return implode('<BR>',$regiones);
}
function red_script_migrar_regiones_canal_proyecto_alerta_callback(){
    return 'Funcion desactivada';
    $html=array();
    $canales=hontza_get_all_nodes(array('canal_de_supercanal','canal_de_yql'));
    if(!empty($canales)){
        foreach($canales as $i=>$node){
            //intelsat-2016
            $field_red_country=red_fields_inc_get_field_country_name();
            if(isset($node->$field_red_country)){
                $row=$node->$field_red_country;
                if(isset($row[0]['value'])){
                    $v=$row['value'];
                    if(!empty($v)){
                        $region_value=red_script_get_region_value_by_proyecto_alerta($v);
                        $html[]=$v;
                        $html[]=$region_value;
                        $html[]='---------------------';
                        red_regiones_save_content_field_canal_region_row($node->nid, $node->vid, $region_value);
                    }
                }
            }    
        }
    }
    $html[]=date('Y-m-d H:i:s');
    return implode('<BR>',$html);
}
function red_script_get_region_value_by_proyecto_alerta($v_in){
    $v=$v_in;
    if($v=='Valencia'){
        $v='Comunidad Valenciana';
    }else if($v=='Barcelona'){
        $v='Cataluña';
    }else if($v=='Sevilla'){
        $v='Andalucía';
    }else if($v=='España'){
        $v='Spain';
    }else if($v=='Francia'){
        $v='France';
    }else if($v=='Irlanda'){
        $v='Ireland';
    }else if($v=='Euskadi'){
        $v='País Vasco';
    }else if(in_array($v,array('Reino Unido','Escocia - Reino Unido'))){    
        $v='The United Kingdom';
    }else if($v=='Islas Baleares'){    
        $v='Baleares';
    }else if($v=='Alemania'){    
        $v='Germany';
    }else if($v=='Luxemburgo'){    
        $v='Luxembourg';
    }else if($v=='Suiza'){    
        $v='Switzerland';
    }else if($v=='Suecia'){    
        $v='Sweden';
    }else if($v=='Italia'){    
        $v='Italy';    
    }else if(in_array($v,array('América Latina y El Caribe','Latinoamérica'))){
        return '#AMERICA#South America#';
    }else if(in_array($v,array('Chile','Argentina','Brasil','Colombia'))){
        return '#AMERICA#South America#'.$v.'#';
    }else if(in_array($v,array('España y Latinoamérica','Iberoamérica'))){
        return '#AMERICA#South America#';        
    }else if(in_array($v,array('Europa','Mediterráneo'))){
        return '#EUROPE#';
    }else if(in_array($v,array('OCDE','Internacional'))){
        return '#INTERNACIONAL#';
    }  
    //
    $regiones=red_regiones_define_regiones_options();
    $my_array=array();
    $my_array[0]='EUROPE';
    $my_array[1]='Western Europe';
    $my_array[2]='Spain';
    $my_array[3]=$v;
    $autonomias_options=red_regiones_define_regiones_autonomias_options(0,$my_array[0],$my_array[1],$my_array[2]);
    if(isset($autonomias_options[$v])){
        $filtro_region_value='#'.implode('#',$my_array).'#';    
        return $filtro_region_value;
    }else if(in_array($v,array('Spain','France','Ireland','Portugal','The United Kingdom','Germany','Bulgaria','Luxembourg','Italy','Switzerland','Estonia','Sweden'))){
        /*unset($my_array[3]);
        $my_array[2]=$v;*/
        $europe_array=$regiones['EUROPE'];
        foreach($europe_array as $key=>$paises){
            if(isset($europe_array[$key][$v])){
                return '#EUROPE#'.$key.'#'.$v.'#';
            }
        }        
        /*$filtro_region_value='#'.implode('#',$new_array).'#';    
        return $filtro_region_value;*/
        print $v;exit();
    }else{
        print $v;exit();
    }
}
function red_script_migrar_regiones_fuente_proyecto_alerta_callback(){
    return 'Funcion desactivada';
    $html=array();
    $fuentes=hontza_get_all_nodes(array('supercanal','fuentedapper'));
    if(!empty($fuentes)){
        foreach($fuentes as $i=>$node){
            //intelsat-2016
            $field_red_country_fuente=red_fields_inc_get_field_country_fuente_name();
            if(isset($node->$field_red_country_fuente)){
                $row=$node->$field_red_country_fuente;
                if(isset($row[0]['value'])){
                    $v=$node->$row[0]['value'];
                    if(!empty($v)){
                        $region_value=red_script_get_region_value_by_proyecto_alerta($v);
                        $html[]=$v;
                        $html[]=$region_value;
                        $html[]='---------------------';
                        red_regiones_save_content_field_fuente_region_row($node->nid, $node->vid, $region_value);
                    }
                }
            }    
        }
    }
    $html[]=date('Y-m-d H:i:s');
    return implode('<BR>',$html);
}
function red_script_print_items_sin_valor_canal_callback(){
    $html=array();
    $result=red_script_get_items_sin_valor_canal();
    foreach($result as $i=>$row){
        $html[]=l($row->node_nid.'='.$row->node_title,'node/'.$row->node_nid,array('attributes'=>array('target'=>'_blank'))).'<BR>';
    }
    return implode('<BR>',$html);
}
function red_script_get_items_sin_valor_canal(){
    $sql="SELECT node_data_field_item_canal_reference.field_item_canal_reference_nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid,
    node_node_data_field_item_canal_reference.title AS node_node_data_field_item_canal_reference_title,
    node.nid AS node_nid,
    node.title AS node_title
    FROM {node} node 
    LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
    LEFT JOIN {node} node_node_data_field_item_canal_reference ON node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid 
    WHERE node.type in ('item') AND (node_data_field_item_canal_reference.field_item_canal_reference_nid IS NULL)
    ORDER BY node.title ASC"; 
    $res=db_query($sql);
    //print $sql;exit();
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;        
    }
    return $result;
}
function red_script_borrar_items_sin_valor_canal_callback(){
    return 'Funcion desactivada';
    $html=array();
    $result=red_script_get_items_sin_valor_canal();
    foreach($result as $i=>$row){
        $html[]='deleting '.$row->node_nid.'='.$row->node_title.'<BR>';
        node_delete($row->node_nid);
    }
    return implode('<BR>',$html);
}
function red_script_borrar_items_sin_grupos_callback(){
    $items_sin_grupos=red_script_get_items_sin_grupos();
    if(!empty($items_sin_grupos)){
        foreach($items_sin_grupos as $i=>$row){
            node_delete($row->nid);
        }
    }
    drupal_set_message('num_items='.count($items_sin_grupos));
    return date('Y-m-d H:i:s');
}
function red_script_get_items_sin_grupos(){
    $result=array();
    $where_type='node.type="item"';
    //$where_type='node.type!="grupo"';
    $sql='SELECT node.*,og_ancestry.group_nid 
    FROM {node}
    LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
    LEFT JOIN {node} node_grupo ON og_ancestry.group_nid=node_grupo.nid 
    WHERE '.$where_type.' AND (og_ancestry.group_nid IS NULL OR node_grupo.nid IS NULL)';
    /*$sql='SELECT node.* 
    FROM {node}
    WHERE '.$where_type.' AND 1';*/
    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    $result=red_script_prepare_sin_grupo($result);
    return $result;
}
function red_script_prepare_sin_grupo($result_in){
    $result=array();
    $types=array();
    if(!empty($result_in)){
        foreach($result_in as $i=>$row){
            if(!in_array($row->type,$types)){
                $types[]=$row->type;
            }
            if(isset($row->group_nid) && !empty($row->group_nid)){
                $grupo_node=node_load($row->group_nid);
                if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
                    continue;
                }else{
                    $result[]=$row;
                }
            }else{
                $result[]=$row;
            }
        }
    }
    drupal_set_message('types:'.implode('<br>',$types));
    return $result;
}
//intelsat-2015
function red_script_fix_flag_content_callback(){
    $kont=0;
    $flag_content_fix_array=red_script_get_flag_content_fix_array();
    foreach($flag_content_fix_array as $i=>$row){
        if(!in_array($row->uid,array(9))){
            $res=db_query('UPDATE {flag_content_fix} SET uid=1 WHERE flag_content_fix.content_id=%d AND flag_content_fix.uid=%d',$row->content_id,$row->uid);
        }
    }
    return date('Y-m-d H:i:s');
}
//intelsat-2015
function red_script_get_flag_content_fix_array(){
    $result=array();
    $res=db_query('SELECT * FROM {flag_content_fix} WHERE 1');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
//intelsat-2015
function red_script_fix_noticia_usuario_source_type_default_callback(){
    $vid=1;
    $user_news_term=red_solr_inc_taxonomy_get_term_by_name_vid_row('Noticias de usuario',$vid);
    if(isset($user_news_term->tid) && !empty($user_news_term->tid)){
        red_script_fix_noticia_usuario_source_type_default($user_news_term);
        red_script_fix_canal_usuario_source_type_default($user_news_term);
    }
    return date('Y-m-d H:i:s');
}
//intelsat-2015
function red_script_fix_noticia_usuario_source_type_default($user_news_term){
        $is_node_load=1;    
        $noticia_usuario_array=hontza_get_all_nodes(array('noticia'),'','',0,'',$is_node_load);
        if(!empty($noticia_usuario_array)){
           foreach($noticia_usuario_array as $i=>$node){
               if(isset($node->nid) && !empty($node->nid)){
                   //echo print_r($node->field_item_source_tid,1);
                   if(!red_solr_inc_in_values_array($user_news_term->tid,$node->field_item_source_tid)){
                        $content_field_item_source_tid_array=red_solr_inc_get_content_field_item_source_tid_array($node->nid,$node->vid);
                        $delta=0;
                        $is_insert=1;
                        if(!empty($content_field_item_source_tid_array)){
                           $delta=$content_field_item_source_tid_array[0]->delta; 
                           $num=count($content_field_item_source_tid_array);
                           if($num==1){
                               if(empty($content_field_item_source_tid_array[0]->field_item_source_tid_value)){
                                   $is_insert=0;
                                   $res=db_query('UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=%d WHERE nid=%d AND vid=%d',$user_news_term->tid,$node->nid,$node->vid);                                            
                               }
                           }
                        }  
                        if($is_insert){
                            $delta=$delta+1;
                            $res=db_query($sql=sprintf('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$user_news_term->tid,$node->nid,$node->vid,$delta));                                                                                     
                            //print $sql.'<BR>';                            
                        }
                        hontza_solr_search_clear_cache_content($node);
                   } 
               }
           }
        }
}
function red_script_fix_canal_usuario_source_type_default($user_news_term){
     $is_node_load=1;    
     $canal_usuario_array=hontza_get_all_nodes(array('canal_usuario'),'','',0,'',$is_node_load);
     if(!empty($canal_usuario_array)){
           foreach($canal_usuario_array as $i=>$node){
               if(isset($node->nid) && !empty($node->nid)){
                   //echo print_r($node->field_canal_source_type,1);                        
                   if(!red_solr_inc_in_values_array($user_news_term->tid,$node->field_canal_source_type)){
                        $content_field_canal_source_type_array=red_solr_inc_get_content_field_canal_source_type_array($node->nid,$node->vid);
                        $delta=0;
                        $is_insert=1;
                        if(!empty($content_field_canal_source_type_array)){
                           $delta=$content_field_canal_source_type_array[0]->delta; 
                           $num=count($content_field_canal_source_type_array);
                           if($num==1){
                               if(empty($content_field_canal_source_type_array[0]->field_canal_source_type_value)){
                                   $is_insert=0;
                                   $res=db_query('UPDATE {content_field_canal_source_type} SET field_canal_source_type_value=%d WHERE nid=%d AND vid=%d',$user_news_term->tid,$node->nid,$node->vid);                                            
                               }
                           }
                        }  
                        if($is_insert){
                            $delta=$delta+1;
                            $res=db_query($sql=sprintf('INSERT INTO {content_field_canal_source_type}(field_canal_source_type_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$user_news_term->tid,$node->nid,$node->vid,$delta));                                                                                     
                            //print $sql.'<BR>';                            
                        }
                        hontza_solr_search_clear_cache_content($node);
                   } 
               }
           }
        }
}