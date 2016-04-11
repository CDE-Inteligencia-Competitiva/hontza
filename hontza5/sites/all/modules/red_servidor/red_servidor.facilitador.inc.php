<?php
function red_servidor_guardar_facilitador_hoja_callback(){
    if(isset($_POST['facilitador_enviar']) && !empty($_POST['facilitador_enviar'])){
        $result['ok']=1;
        $facilitador_enviar=unserialize(base64_decode($_POST['facilitador_enviar']));
        $row=red_servidor_get_red_servidor_facilitador($facilitador_enviar);
        if(isset($row->id) && !empty($row->id)){
            //            
        }else{
            /*$local_nid=$facilitador_enviar->nid; 
            $local_vid=$facilitador_enviar->vid; 
            $local_uid=$facilitador_enviar->uid;
            $local_group_nid=0;
            $local_group_nid=red_servidor_get_grupo_nid_by_node($facilitador_enviar);
            $fecha=time();
            $sareko_id=$facilitador_enviar->sareko_id;
            $node=red_servidor_preparar_facilitador($facilitador_enviar,$grupo_servidor);
            if(is_super_admin()){
                red_servidor_node_save($node);
            }else{
                node_save($node);
            }
            drupal_set_message(t('Facilitator %facilitador_title shared',array('%facilitador_title'=>$node->title)));
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_servidor->nid;
            $subdominio=$facilitador_enviar->subdominio;
            $sql=sprintf('INSERT INTO {red_servidor_facilitador}(local_nid,local_vid,local_uid,local_group_nid,fecha,sareko_id,vid,nid,uid,group_nid,subdominio) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d,"%s")',$local_nid,$local_vid,$local_uid,$local_group_nid,$fecha,$sareko_id,$vid,$nid,$uid,$group_nid,$subdominio);
            db_query($sql);*/
            red_servidor_facilitador_guardar_facilitador_enviar($facilitador_enviar);
        }
    }
    //return t('Shared facilitator saved');
    drupal_goto('red_publica/fuentes');
}
function red_servidor_get_red_servidor_facilitador($facilitador_enviar){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_facilitador} WHERE local_nid=%d AND local_vid=%d AND sareko_id="%s"',$facilitador_enviar->nid,$facilitador_enviar->vid,$facilitador_enviar->sareko_id));
    //print 'red_servidor_get_red_servidor_fuente (sql)='.$sql;exit();
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_servidor_preparar_facilitador($facilitador_enviar,&$grupo_servidor){
    $node=$facilitador_enviar;
    unset($node->nid);
    unset($node->vid);
    unset($node->created);
    unset($node->changed);
    $node->uid=1;
    unset($node->sareko_id);    
    //AVISO::::los facilitadores no son de grupos son de los subdominios
    /*$grupo_servidor=red_servidor_get_grupo_facilitadores();
    
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
    }*/    
    return $node;
}
function red_servidor_get_red_servidor_facilitadores_array($local_nid='',$my_lang=''){
    global $language;
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($local_nid)){
        $where[]='fa.local_nid='.$local_nid;
    }
    $lang=$language->language;
    if(empty($my_lang)){
        if(isset($_REQUEST['alerta_lang']) && !empty($_REQUEST['alerta_lang'])){
            $lang=$_REQUEST['alerta_lang'];
        }
    }else{
        $lang=$my_lang;
    }
    $where[]='(n.language in ("'.$lang.'"))';
    $where[]='n.type IN("servicio")';
    $sql='SELECT fa.id,
    fa.local_nid,
    fa.local_vid,
    fa.local_uid,
    fa.local_group_nid,
    fa.vid,
    fa.uid,
    fa.group_nid,
    fa.sareko_id,
    fa.subdominio,    
    n.title,
    n.nid AS nid,
    n.created AS fecha
    FROM {node} n
    LEFT JOIN {red_servidor_facilitador} fa ON n.vid=fa.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY fa.fecha DESC';
    //print $sql;exit();
    $res=db_query($sql);
    $kont=0;
    while($row=db_fetch_object($res)){
        $result[$kont]=$row;
        if(empty($result[$kont]->id)){
            $result[$kont]->id=$row->nid.'_servidor';
        }
        $node=node_load($row->nid);
        //
        $result[$kont]->node=$node;
        $result[$kont]->sareko_id_label=red_red_set_subdomain_name($row->sareko_id);
        $result[$kont]->node_view=red_servidor_facilitador_node_view($node);
        $kont++;
    }
    return $result;
}
function red_servidor_facilitadores_get_contents_callback(){
    $facilitador_array=red_servidor_get_red_servidor_facilitadores_array();
    if(empty($facilitador_array)){
        $facilitador_array=red_servidor_get_red_servidor_facilitadores_array('','en');
    }
    print serialize($facilitador_array);
    exit();
}
function red_servidor_facilitador_get_simple_array(){
    $result=array();
    $where=array();
    $where[]='1';
    $sql='SELECT *   
        FROM {red_servidor_facilitador} 
        WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_facilitador_update_uid($row,$uid){
    db_query('UPDATE {red_servidor_facilitador} SET uid=%d WHERE id=%d',$uid,$row->id);    
}
function red_servidor_facilitador_existe_en_el_servidor_callback(){
    $result=array();
    $result['ok']=0;
    if(isset($_POST['facilitador']) && !empty($_POST['facilitador'])){
        $facilitador=$_POST['facilitador'];
        $facilitador=base64_decode($facilitador);
        $facilitador=red_compartir_grupo_decrypt_text($facilitador);
        $facilitador=unserialize(base64_decode($facilitador));
            if(in_array($facilitador['facilitador']->type,array('servicio'))){
                $existe=red_servidor_facilitador_existe_en_el_servidor($facilitador['facilitador'],$sql);
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
function red_servidor_facilitador_existe_en_el_servidor($facilitador,&$sql){    
    return 0;
}
function red_servidor_facilitador_guardar_facilitador_enviar($facilitador_enviar){
            $local_nid=$facilitador_enviar->nid; 
            $local_vid=$facilitador_enviar->vid; 
            $local_uid=$facilitador_enviar->uid;
            $local_group_nid=0;
            $local_group_nid=red_servidor_get_grupo_nid_by_node($facilitador_enviar);
            $fecha=time();
            $sareko_id=$facilitador_enviar->sareko_id;
            $node=red_servidor_preparar_facilitador($facilitador_enviar,$grupo_servidor);
            if(is_super_admin()){
                red_servidor_node_save($node);
            }else{
                node_save($node);
            }
            drupal_set_message(t('Facilitator %facilitador_title shared',array('%facilitador_title'=>$node->title)));
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_servidor->nid;
            $subdominio=$facilitador_enviar->subdominio;
            $sql=sprintf('INSERT INTO {red_servidor_facilitador}(local_nid,local_vid,local_uid,local_group_nid,fecha,sareko_id,vid,nid,uid,group_nid,subdominio) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d,"%s")',$local_nid,$local_vid,$local_uid,$local_group_nid,$fecha,$sareko_id,$vid,$nid,$uid,$group_nid,$subdominio);
            db_query($sql);
}
//intelsat-2016
function red_servidor_facilitador_download_servicios_experto_callback($is_print_exit=1){
    $result=array();
    $servicios_experto_options=red_servidor_facilitador_download_servicios_experto_options();
    $result['servicios_experto_options']=red_compartir_grupo_encrypt_text(base64_encode(serialize($servicios_experto_options)));
    $result=json_encode($result);
    if($is_print_exit){
        print $result;
        exit();
    }
    return $result;
}
function red_servidor_facilitador_download_servicios_experto_options(){
    $vid=red_facilitator_get_servicios_experto_vid();
    $servicios_experto_array=taxonomy_get_tree($vid);
    $parent = array();
    $term_options=array();
    foreach ($servicios_experto_array as $term) {
      /*if($ptid = $term->parents[0]) {
        $term_options[$parent[$ptid]][$term->tid]=$term->name;
      } else {
        $parent[$term->tid] = $term->name;
        $term_options[$term->name] = array();
      }*/
      $term_options[$term->tid] = $term->name;  
    }
    asort($term_options);
    return $term_options;
}
function red_servidor_facilitador_save_servicios_experto_callback(){
    if(isset($_POST['servicios_experto_textarea']) && !empty($_POST['servicios_experto_textarea'])){
        $vid=red_facilitator_get_servicios_experto_vid();
        $servicios_experto_textarea=$_POST['servicios_experto_textarea'];
        //$servicios_experto_textarea=json_decode($servicios_experto_textarea);
        $servicios_experto_textarea=red_compartir_grupo_decrypt_text($servicios_experto_textarea);
        $servicios_experto_textarea=base64_decode($servicios_experto_textarea);
        $servicios_experto_textarea=unserialize($servicios_experto_textarea);
        if(!empty($servicios_experto_textarea)){
            foreach($servicios_experto_textarea as $i=>$servicio_name){
                $servicio_array=taxonomy_my_get_term_by_name($servicio_name,$vid);
                //echo print_r($servicio_array,1);
                if(count($servicio_array)>0){
                    $servicio=$servicio_array[0];
                    if(isset($servicio->tid) && !empty($servicio->tid)){
                        //echo print_r($servicio,1);
                        continue;
                    }
                }else{
                    $servicio_experto=array();
                    $servicio_experto['vid']=$vid;
                    /*$servicio_name=ltrim($servicio_name,'[');
                    $servicio_name=rtrim($servicio_name,']');*/
                    $servicio_name=red_facilitador_unset_traduccion($servicio_name);
                    $servicio_experto['name']=$servicio_name;
                    taxonomy_save_term($servicio_experto);
                }
            }
        }
    }
    $result=red_servidor_facilitador_download_servicios_experto_callback(0);
    print $result;
    exit();
}