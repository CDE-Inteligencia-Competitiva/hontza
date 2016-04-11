<?php
function hontza_canal_comodin_menu_items($items_in){
    $items=$items_in;    
    $items['hontza_canal_comodin/backup/%']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_canal_comodin_backup_callback',
    'access callback' => 'hontza_solr_my_access',    
    );
    $items['simular_grupos_categorias']=array(
    'title'=>'Bookmarks',
    'page callback' => 'hontza_canal_comodin_simular_grupos_categorias_callback',
    'access arguments' => array('root'),    
    );
    $items['busqueda_rss']=array(
    'title'=>'RSS',
    'page callback' => 'hontza_canal_rss_busqueda_rss_callback',
    'access callback' => 'hontza_canal_rss_busqueda_rss_access',    
    );
    return $items;
}
function hontza_canal_comodin_checkbox_options(){
    $my_array=array();
    $my_array[0]=t('Backup channel');
    $my_array[1]=t('Backup channel');
    return $my_array;
}
function hontza_canal_comodin_canal_de_supercanal_node_form_alter(&$form,&$form_state,$form_id){
   if(isset($form['field_is_canal_comodin']) && !empty($form['field_is_canal_comodin'])){
       unset($form['field_is_canal_comodin']);
   }
}
function hontza_canal_comodin_canal_de_yql_node_form_alter(&$form,&$form_state,$form_id){
   hontza_canal_comodin_canal_de_supercanal_node_form_alter($form,$form_state,$form_id);
}
function hontza_canal_comodin_backup_channel($node_id_array,$canal_nid){
   /*$canal_comodin=hontza_canal_comodin_get_canal_comodin();
   if(!isset($canal_comodin->nid) || empty($canal_comodin->nid)){
       $canal_comodin=hontza_canal_comodin_crear_canal_comodin();
   }*/
   $canal_comodin=node_load($canal_nid); 
   //
   if(isset($canal_comodin->nid) && !empty($canal_comodin->nid)){
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                //$node->field_item_canal_reference[0]['nid']=$canal_comodin->nid;
                //node_save($node);
                hontza_canal_comodin_save_field_item_canal_reference($node,$canal_comodin->nid);
                hontza_canal_comodin_save_feeds_node_item($node,$canal_comodin->nid);
                //intelsat-2015
                if(hontza_solr_is_solr_activado()){
                    $updated=0;                
                    hontza_solr_set_item_solr_updated($node,$updated);
                }
                hontza_solr_search_clear_cache_content($node);
                hontza_canal_rss_solr_clear_node_index($node,$nid);
                //
            }
        }
    }
   }
   //intelsat-2015
   red_set_bulk_command_executed_message(2);    
}
function hontza_canal_comodin_get_canal_comodin(){
   $my_grupo=og_get_group_context();
   if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
       $where=array();
       $where[]='1';
       $where[]='node.type IN("canal_de_supercanal","canal_de_yql")';
       $where[]='og_ancestry.group_nid='.$my_grupo->nid;
       $where[]='content_field_is_canal_comodin.field_is_canal_comodin_value=1';
       //        
       $sql='SELECT node.* 
       FROM {node} node
       LEFT JOIN {content_field_is_canal_comodin} content_field_is_canal_comodin ON node.vid=content_field_is_canal_comodin.vid
       LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid 
       WHERE '.implode(' AND ',$where);
       //
       $res=db_query($sql);
       while($row=db_fetch_object($res)){
           $node=node_load($row->nid);
           return $node;
       }
   } 
   //
   $my_result=new stdClass();
   return $my_result;
}
function hontza_canal_comodin_is_canal_comodin_activado(){
    return hontza_solr_funciones_is_bookmark_activado();
}
function hontza_canal_comodin_backup_channel_html($node_id_array,$url_return,$is_solr=0){
    //return '<div style="padding-bottom:10px;">'.l(t('Backup'),'hontza_canal_comodin/backup/'.implode(',',$node_id_array)).'</div>';
    return drupal_get_form('hontza_canal_comodin_backup_form',$node_id_array,$url_return,$is_solr);
}
function hontza_canal_comodin_backup_callback(){
    $node_id_array=arg(2);
    $node_id_array=explode(',',$node_id_array);    
    hontza_canal_comodin_backup_channel($node_id_array);
}
function hontza_canal_comodin_crear_canal_comodin(){
    $node=new stdClass();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        //$node->title='Backup channel ('.$my_grupo->title.')';
        $node->title='Backup channel';
        $node->type='canal_de_yql';
        $node->status=1;
        $node->uid=1;
        $node->field_is_canal_comodin[0]['value']=1;
        node_save($node);
    }    
    //
    return $node;        
}
function hontza_canal_comodin_save_field_item_canal_reference($node,$canal_nid){
    db_query('UPDATE {content_type_item} SET field_item_canal_reference_nid=%d WHERE nid=%d AND vid=%d',$canal_nid,$node->nid,$node->vid);
}
function hontza_canal_comodin_backup_form(&$form_state,$node_id_array,$url_return,$is_solr){
    $form=array();
    //
    if(!hontza_canal_comodin_backup_access()){
        drupal_access_denied();
        exit();
    }
    //
    $default_value=hontza_canal_comodin_get_canal_comodin_nid();
    $form['canal_nid']=array(
        '#type'=>'select',
        '#title'=>t('Channel'),
        '#options'=>hontza_canal_comodin_get_canal_options(),
        '#default_value'=>$default_value,
    );
    $form['node_id_array']=array(
        '#type'=>'hidden',
        '#default_value'=>implode(',',$node_id_array),
    );
    $form['backup_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Move'),
    );
    /*$link_return=l(t('Return'),$url_return);
    if($is_solr){*/
    $url_info=parse_url($url_return);
    $link_return=l(t('Return'),$url_info['path'],array('query'=>$url_info['query']));
    //}    
    $form['return_btn']=array(
        '#value'=>$link_return,
    );
    return $form;
}
function hontza_canal_comodin_get_canal_options(){
    $result=array();
    $canal_comodin=hontza_canal_comodin_get_canal_comodin();
    if(!isset($canal_comodin->nid) || empty($canal_comodin->nid)){
       $canal_comodin=hontza_canal_comodin_crear_canal_comodin();
    }
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $canal_array=hontza_get_canales_del_grupo($my_grupo->nid);
        if(!empty($canal_array)){
            foreach($canal_array as $i=>$canal){
                $result[$canal->nid]=$canal->title;
            }
        }
    }    
    return $result;
}
function hontza_canal_comodin_get_canal_comodin_nid(){
    $canal_comodin=hontza_canal_comodin_get_canal_comodin();
    //simulando
    /*$job['callback']='feeds_source_import';
    $job['id']=211271;
    hontza_is_group_active_refresh($job);*/
    //
    if(isset($canal_comodin->nid) && !empty($canal_comodin->nid)){
        return $canal_comodin->nid;
    }
    return '';
}
function hontza_canal_comodin_backup_form_submit(&$form,&$form_state){
    $_REQUEST['destination']='';
    $canal_nid=$form_state['values']['canal_nid'];
    $node_id_array=explode(',',$form_state['values']['node_id_array']);
    hontza_canal_comodin_backup_channel($node_id_array,$canal_nid);
    //drupal_goto('canales/'.$canal_nid.'/ultimas');
    hontza_solr_funciones_redirect();
}
function hontza_canal_comodin_is_canal_comodin($node){
    if(isset($node->field_is_canal_comodin[0]['value']) && !empty($node->field_is_canal_comodin[0]['value'])){
        return 1;
    }
    return 0;
}
function hontza_canal_comodin_is_congelado_by_comodin($node){
    if(hontza_canal_comodin_is_canal_comodin_activado()){
        if(hontza_canal_comodin_is_canal_comodin($node)){
            return 0;
        }
        return 1;
    }else{    
        return 1;
    }
    return 1;
}
function hontza_canal_comodin_backup_access(){
    if(is_administrador_grupo(1)){
        return 1;
    }
    if(hontza_is_creador_de_grupo()){
        return 1;
    }
    return 0;
}
function hontza_canal_comodin_simular_grupos_categorias_callback(){
    /*hontza_canal_comodin_activar_canales();
    return date('Y-m-d H:i:s');*/
    //
    global $base_url;
    $group_array=hontza_get_all_nodes(array('grupo'));
    if(!empty($group_array)){
        foreach($group_array as $i=>$grupo_node){
            $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$grupo_node->nid));
            print $base_url.'/admin/content/taxonomy/edit/vocabulary/'.$id_categoria.'<BR>';
        }
    }
}
function hontza_canal_comodin_save_feeds_node_item($node,$canal_nid){
    db_query('UPDATE {feeds_node_item} SET feed_nid=%d WHERE nid=%d',$canal_nid,$node->nid);
}
function hontza_canal_comodin_activar_canales(){
    $node_array=hontza_get_all_nodes(array('canal_de_yql','canal_de_supercanal'));
    if(!empty($node_array)){
        foreach($node_array as $i=>$canal){
            if(isset($canal->field_canal_active_refresh[0]['value']) && !empty($canal->field_canal_active_refresh[0]['value'])){
                continue;
            }else{
                $row=hontza_get_content_field_canal_active_refresh_row($canal);
                if(isset($row->nid) && !empty($row->nid)){
                    db_query('UPDATE {content_field_canal_active_refresh} SET field_canal_active_refresh_value=1 WHERE nid=%d AND vid=%d',$canal->nid,$canal->vid);
                }else{
                    db_query('INSERT INTO {content_field_canal_active_refresh}(nid,vid,field_canal_active_refresh_value) VALUES(%d,%d,1)',$canal->nid,$canal->vid);
                }
            }
        }
    }
}