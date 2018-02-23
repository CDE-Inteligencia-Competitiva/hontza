<?php
function red_servidor_guardar_canal_hoja_callback(){
    if(isset($_POST['canal_enviar']) && !empty($_POST['canal_enviar'])){
        $result['ok']=1;
        $canal_enviar=unserialize(base64_decode($_POST['canal_enviar']));
        $row=red_servidor_get_red_servidor_canal($canal_enviar);
        if(isset($row->id) && !empty($row->id)){
            return 'The channel exist in the network';            
        }else{            
            /*$local_nid=$canal_enviar->nid; 
            $local_vid=$canal_enviar->vid; 
            $local_uid=$canal_enviar->uid;
            $local_group_nid=0;
            $local_group_nid=red_servidor_get_grupo_nid_by_node($canal_enviar);
            $fecha=time();
            $sareko_id=$canal_enviar->sareko_id;
            $node=red_servidor_preparar_canal($canal_enviar,$grupo_servidor);
            if(is_super_admin()){
                red_servidor_node_save($node);
            }else{
                node_save($node);
            }
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_servidor->nid;
            red_servidor_save_canal_estrellas_average($node);
            red_servidor_save_canal_yql_parametros($node);
            red_servidor_save_canal_hound_parametros($node);
            $sql=sprintf('INSERT INTO {red_servidor_canal}(local_nid,local_vid,local_uid,local_group_nid,fecha,sareko_id,vid,nid,uid,group_nid) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d)',$local_nid,$local_vid,$local_uid,$local_group_nid,$fecha,$sareko_id,$vid,$nid,$uid,$group_nid);
            db_query($sql);*/
            red_servidor_canal_guardar_canal_enviar($canal_enviar);
        }
    }
    //return t('Shared channel saved');
    //drupal_set_message(t('Shared channel saved').' '.date('Y-m-d H:i:s'));
    drupal_goto('red_publica/canales');
}
function red_servidor_get_red_servidor_canal($canal_enviar){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_canal} WHERE local_nid=%d AND local_vid=%d AND sareko_id="%s"',$canal_enviar->nid,$canal_enviar->vid,$canal_enviar->sareko_id));
    //print 'red_servidor_get_red_servidor_fuente (sql)='.$sql;exit();
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_servidor_preparar_canal($canal_enviar,&$grupo_servidor){
    $node=$canal_enviar;
    $user_local_uid='';
    if(isset($canal_enviar->user_local) && !empty($canal_enviar->user_local)){
        $user_local_uid=red_servidor_canal_enviar_get_uid($canal_enviar->user_local);
    }
    unset($canal_enviar->user_local);
    unset($node->nid);
    unset($node->vid);
    unset($node->created);
    unset($node->changed);
    $node->uid=1;
    if(!empty($user_local_uid)){
        $node->uid=$user_local_uid;
    }
    unset($node->sareko_id);
    $grupo_servidor=red_servidor_get_grupo_canales();
    
    if(isset($node->og_groups)){
        unset($node->og_groups);
    }
    if(isset($node->og_groups_both)){
        unset($node->og_groups_both);
    }
    if(isset($grupo_servidor->nid) && !empty($grupo_servidor->nid)){
        $grupo_servidor_nid=$grupo_servidor->nid;
        $node->og_groups[$grupo_servidor_nid]=$grupo_servidor_nid;
        $node->og_groups_both[$grupo_servidor_nid]=$grupo_servidor->title;
    }
    $node->field_responsable_uid[0]['uid']=1;
    if($node->type=='canal_de_supercanal'){
        $fuente_local_nid=$node->field_nid_fuente_canal[0]['value'];
        $node->field_nid_fuente_canal[0]['value']=red_get_servidor_fuente_nid($fuente_local_nid);
    }
    return $node;
}
function red_servidor_get_grupo_canales(){
    return red_servidor_get_grupo_fuentes();
}
function red_servidor_save_canal_estrellas_average($node){
    red_compartir_save_canal_estrellas_average($node);
}
function red_servidor_canales_get_contents_callback(){
    $result=array();
    $result['canales_array']=red_servidor_get_red_servidor_canales_array();
    $result['user_autocomplete_options']=red_servidor_get_user_autocomplete_options();
    print serialize($result);
    exit();
}
function red_servidor_get_red_servidor_canales_array($local_nid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($local_nid)){
        $where[]='c.local_nid='.$local_nid;
    }
    $grupo_shared=red_servidor_get_grupo_shared();
    if(isset($grupo_shared->nid) && !empty($grupo_shared->nid)){
        $where[]='og_ancestry.group_nid='.$grupo_shared->nid;
    }
    $is_node_type=1;    
    $filtro=array();
    if(isset($_REQUEST['red_alerta_filtro']) && !empty($_REQUEST['red_alerta_filtro'])){
        $filtro=unserialize(base64_decode($_REQUEST['red_alerta_filtro']));
        if(!empty($filtro)){
            foreach($filtro as $f=>$v){
                switch($f){
                    case 'canal_title':
                        $where[]="n.title LIKE '%%".$v."%%'";
                        break;
                     /*case 'canal_type':
                        if($v=='hound'){
                            $canal_type='canal_de_yql';
                            $where[]="n.type='".$canal_type."'"; 
                            $where[]="content_type_canal_de_yql.field_is_hound_value=1";
                        }else{ 
                            $where[]="n.type='".$v."'";
                            $where[]="content_type_canal_de_yql.field_is_hound_value!=1";
                        }                          
                     break;
                     case 'sareko_id':
                        $where[]="c.sareko_id='".$v."'";                        
                        break;*/
                     case 'region':
                        $v=red_regiones_get_region_decode_value($v);                        
                        if(!empty($v)){ 
                            $where[]='content_field_canal_region.field_canal_region_value LIKE "%%#'.$v.'#%%"';
                        }    
                        break;
                }
            }
        }
    }
    if($is_node_type){
        $where[]='n.type IN("canal_de_supercanal","canal_de_yql")';
    }
    $res=db_query($sql='SELECT c.id,
    c.local_nid,
    c.local_vid,
    c.local_uid,
    c.local_group_nid,
    c.fecha,
    c.vid,
    c.uid,
    c.group_nid,
    c.sareko_id,
    c.subdominio,    
    n.title,
    n.nid AS nid,
    content_field_canal_numero_de_descargas.field_canal_numero_de_descargas_value AS numero_de_descargas     
    FROM {node} n 
    LEFT JOIN {red_servidor_canal} c ON n.vid=c.vid
    LEFT JOIN {content_type_canal_de_yql} content_type_canal_de_yql ON c.vid=content_type_canal_de_yql.vid 
    LEFT JOIN {content_field_canal_region} content_field_canal_region ON n.vid=content_field_canal_region.vid
    LEFT JOIN {og_ancestry} ON n.nid=og_ancestry.nid 
    LEFT JOIN {content_field_canal_numero_de_descargas} content_field_canal_numero_de_descargas ON n.vid=content_field_canal_numero_de_descargas.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY n.created DESC');
    //print $sql;exit();
    $kont=0;
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $result[$kont]=$row;
            if(empty($result[$kont]->id)){
                $result[$kont]->id=$row->nid.'_servidor';
            }
            $fuente_title=hontza_get_content_field_nombrefuente_canal_value($node->vid,$node->nid);
            if(!empty($fuente_title)){
                $node->field_nombrefuente_canal[0]['value']=$fuente_title;
            }
            red_compartir_add_node_feeds($node);
            $node->votingapi_cache_row=red_servidor_get_estrellas_average_row($node->nid);
            $node->canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            $node->canal_hound_parametros_row=hound_get_canal_hound_parametros_row($node->nid);
            $result[$kont]->node=$node;
            $result[$kont]->sareko_id_label=red_red_set_subdomain_name($row->sareko_id);
            if(isset($row->local_uid) && !empty($row->local_uid)){
                $user_local=red_servidor_canal_get_user_local($row);            
            }else{
                $user_local=red_servidor_canal_get_user_servidor($node->uid);
            }                        
            $result[$kont]->user_local=$user_local;
            $result[$kont]->user_local_name='';
            if(isset($user_local->name) && !empty($user_local->name)){
                $result[$kont]->user_local_name=$user_local->name;
            }
            $result[$kont]->numero_de_descargas=red_servidor_canal_get_numero_de_descargas($row);
            $result[$kont]->node_view=red_servidor_canal_node_view($node);
            $result[$kont]->canal_region=red_regiones_get_region_decode_value($node->field_canal_region[0]['value']);
            $result[$kont]->canal_rating=red_compartir_canal_get_avg_rating_by_node($node,1);
            $kont++;
        }    
    }
    //echo 'result===='.print_r($result,1);exit();
    $result=red_servidor_canales_array_repasar_filtro($result,$filtro);
    return $result;
}
function red_servidor_get_estrellas_average_row($nid){
    return hontza_get_avg_rating($nid);
}
function red_servidor_save_canal_yql_parametros($node){
    red_compartir_save_canal_yql_parametros($node);
}
function red_servidor_save_canal_hound_parametros(&$node){
    red_compartir_save_canal_hound_parametros($node);
}
function red_servidor_canales_array_repasar_filtro($result_in,$filtro){
    $result=array();
    $fields=array('canal_description','average','numero_de_descargas','user_local_name');
    if(!empty($result_in)){
        $kont=0;
        foreach($result_in as $i=>$row){
            $ok=1;
            foreach($fields as $k=>$f){
                if(isset($filtro[$f]) && !empty($filtro[$f])){
                    if($f=='average'){    
                        $v=0;
                        if(isset($row->node->votingapi_cache_row) && isset($row->node->votingapi_cache_row->value)){
                            $v=$row->node->votingapi_cache_row->value;
                            if(empty($v)){
                                $v=0;
                            }
                            if($v<$filtro[$f]){
                                $ok=0;
                                break;
                            }
                        }else{
                            $ok=0;
                            break;
                        }                     
                    }else if($f=='canal_description'){
                            $v=$filtro[$f];
                            if(!red_servidor_is_description_like($v,$row->node)){
                                $ok=0;
                                break;
                            }
                    }else if($f=='user_local_name'){
                            $v=$filtro[$f];
                            if(!red_servidor_is_user_local_name_like($v,$row->user_local)){
                                $ok=0;
                                break;
                            }
                    }else if($f=='numero_de_descargas'){
                            $rango=red_servidor_get_filtro_rango($filtro[$f]);
                            $numero_de_descargas=(int) $row->numero_de_descargas;
                            if($rango['end']=='+'){
                                if(!($numero_de_descargas>=$rango['ini'])){
                                    $ok=0;
                                    break;
                                }
                            }else{
                                if(!($numero_de_descargas>=$rango['ini'] && $numero_de_descargas<=$rango['end'])){
                                    $ok=0;
                                    break;
                                }
                            }
                            //
                   }               
                }
            }
            if($ok){
                $result[$kont]=$row;
                $kont++;
            }
        }    
    }
    return $result;
}
function red_servidor_canal_get_user_local($row){
    return red_servidor_fuente_get_user_local($row);
}
function red_servidor_canal_get_numero_de_descargas($row){
    /*$subdominio_array=red_servidor_grupo_get_subdominio_array('',1);
    if(!empty($subdominio_array)){
        $kont=0;
        foreach($subdominio_array as $i=>$subdominio){
            if(empty($subdominio)){
                continue;
            }
            $konp=red_get_subdominio_by_sareko_id($row->sareko_id);
            if($subdominio!=$konp){
                if(!empty($subdominio)){
                    //$url='http://'.$subdominio.'/red_compartir/red_compartir_canal_get_numero_de_descargas_get_contents/'.$row->nid;
                    //if(user_access('root')){
                    //    print $url.'<BR>';
                    //}
                    $content=file_get_contents($url);
                    $content=trim($content);
                    if(!empty($content)){
                        $n=(int) $content;
                        if(!empty($n)){
                            //print $n;exit();
                            $kont=$kont+1;
                        }    
                    }
                }    
            }
        }
        return $kont;
    }
    return 0;*/
    if(isset($row->numero_de_descargas)){
        return $row->numero_de_descargas;
    }
    return 0;
}
function red_servidor_canal_update_subdominio($id,$subdominio){
    $sql=db_query('UPDATE {red_servidor_canal} SET subdominio="%s" WHERE id=%d',$subdominio,$id);    
}
function red_servidor_canal_get_simple_array(){
    $result=array();
    $where=array();
    $where[]='1';
    $sql='SELECT *   
        FROM {red_servidor_canal} 
        WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_canal_update_uid($row,$uid){
    db_query('UPDATE {red_servidor_canal} SET uid=%d WHERE id=%d',$uid,$row->id);    
}
function red_servidor_canal_enviar_get_uid($user_local){
    return red_servidor_fuente_enviar_get_uid($user_local);
}
function red_servidor_canal_guardar_canal_enviar($canal_enviar){
            $local_nid=$canal_enviar->nid; 
            $local_vid=$canal_enviar->vid; 
            $local_uid=$canal_enviar->uid;
            $local_group_nid=0;
            $local_group_nid=red_servidor_get_grupo_nid_by_node($canal_enviar);
            $fecha=time();
            $sareko_id=$canal_enviar->sareko_id;
            $node=red_servidor_preparar_canal($canal_enviar,$grupo_servidor);
            if(is_super_admin()){
                red_servidor_node_save($node);
            }else{
                node_save($node);
            }
            drupal_set_message(t('Channel %canal_title shared',array('%canal_title'=>$node->title)));
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_servidor->nid;
            $subdominio=$canal_enviar->subdominio;
            red_servidor_save_canal_estrellas_average($node);
            red_servidor_save_canal_yql_parametros($node);
            red_servidor_save_canal_hound_parametros($node);
            $sql=sprintf('INSERT INTO {red_servidor_canal}(local_nid,local_vid,local_uid,local_group_nid,fecha,sareko_id,vid,nid,uid,group_nid,subdominio) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d,"%s")',$local_nid,$local_vid,$local_uid,$local_group_nid,$fecha,$sareko_id,$vid,$nid,$uid,$group_nid,$subdominio);
            db_query($sql);
}
function red_servidor_canal_existe_en_el_servidor_callback(){
    $result=array();
    $result['ok']=0;
    if(isset($_POST['canal']) && !empty($_POST['canal'])){
        $canal=$_POST['canal'];
        $canal=base64_decode($canal);
        $canal=red_compartir_grupo_decrypt_text($canal);
        $canal=unserialize(base64_decode($canal));
            if(in_array($canal['canal']->type,array('canal_de_supercanal','canal_de_yql'))){
                $existe=red_servidor_canal_existe_en_el_servidor($canal['canal'],$sql);
                if(!empty($existe)){
                    $result['ok']=1;
                    $result['existe']=$existe;
                }
                
            }
        //$result['sql']=$sql;
    }
    print serialize($result);
    exit();
}
function red_servidor_canal_existe_en_el_servidor($canal,&$sql){
    if(isset($canal->feeds_source) && isset($canal->feeds_source->source) && !empty($canal->feeds_source->source)){
        $res=db_query('SELECT feeds_source.* 
        FROM {feeds_source} feeds_source 
        WHERE feeds_source.source="%s"',$canal->feeds_source->source);
        while($row=db_fetch_object($res)){
            return 1;
        }
        
        $res=db_query('SELECT n.* 
        FROM {node} n 
        WHERE n.type IN("canal_de_supercanal","canal_de_yql") AND n.title="%s"',$canal->title);
        while($row=db_fetch_object($res)){
            return 2;
        }
        
    }
    return 0;
}
function red_servidor_canal_get_user_servidor($uid){
    return red_servidor_fuente_get_user_servidor($uid);
}