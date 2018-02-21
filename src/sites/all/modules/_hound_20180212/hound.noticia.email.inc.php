<?php
function hound_noticia_feed_noticia_email_actualizar_callback(){
    if(hound_noticia_email_is_activado()){
        hound_noticia_feed_noticia_email_actualizar();
    }
    return date('Y-m-d H:i');
}
function hound_noticia_email_is_activado(){
    /*if(defined('_IS_NOTICIA_EMAIL') && _IS_NOTICIA_EMAIL==1){
        return 1;
    }
    return 0;*/    
    return 1;
}
function hound_noticia_feed_noticia_email_actualizar(){
    if(hound_noticia_email_is_activado()){
        file_get_contents('http://hound.hontza.es:8080/hound/index.php/mailAccounts/checkMail');
        //file_get_contents('http://hound.hontza.es/hound/index.php/mailAccounts/checkMail');
        $vid=1;
        $fid=2;
        $is_hound_noticia_feed_noticia_email=1;
        $kont=0;
        $_REQUEST['destination']='';
        $dxml=file_get_contents('http://hound.hontza.es:8080/hound/index.php/mailPages/GetRss/999998');
        //$dxml=file_get_contents('http://hound.hontza.es/hound/index.php/mailPages/GetRss/999998');
        if(red_is_xml($dxml)){
            $data = simplexml_load_string($dxml);    
            if(!isset($data->results->rss)){
                $sets = $data->channel->item;
                    $all=0;
                    if(isset($data->channel) && isset($data->channel->item)){
                        $all = sizeof($sets);
                    }
                    for($i=0;$i<$all;$i++){
                      $r = $sets[$i];
                        $info=hound_noticia_email_get_info($r);
                        $subdominio=$info['subdominio'];                  
                        $grupo_title=$info['grupo_title'];
                        $noticia_title=$info['noticia_title'];                        
                        if(hound_noticia_email_validate($info)){                        
                           if(hound_noticia_email_is_subdominio($subdominio)){
                            $grupo_node=hound_noticia_email_get_grupo_node($grupo_title);  
                            if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
                                //print $grupo_node->title.'===='.((string) $r->title).'<br>';                                
                                if(!hound_noticia_email_is_duplicado($r,$grupo_node)){
                                  $content=hound_noticia_email_get_content($r);
                                  //
                                  $node=new stdClass();
                                  $node->title=$noticia_title;
                                    //
                                    $node->body=$content;        
                                    $node->uid=hound_noticia_email_get_uid($r,$grupo_node);
                                    $node->type='noticia';
                                    $node->status=1;
                                    $node->og_groups[$grupo_node->nid]=$grupo_node->nid;
                                    $node->field_noticia_id_email[0]['value']=(string) $r->id_mail;
                                    $node->field_cuando[0]['value']=time();
                                    $user_news_term=red_solr_inc_taxonomy_get_term_by_name_vid_row('Noticias de usuario',$vid);
                                    if(isset($user_news_term->tid) && !empty($user_news_term->tid)){
                                        $node->field_item_source_tid[0]['value']=$user_news_term->tid;
                                    }
                                    node_save($node);
                                    hound_noticia_email_update_og_ancestry($node,$grupo_node->nid);
                                    //hontza_validar_con_accion($node->nid,$is_hound_noticia_feed_noticia_email);
                                    red_funciones_flag_save_validador_node($node->nid,$fid,$node->uid);
                                    if(hound_noticia_email_is_current_grupo($grupo_node)){
                                        $kont=$kont+1;
                                    }    
                                }
                            }
                          }
                        }
                    }
            } 
        }
        if($kont>0){
            drupal_set_message(t('Created !kont User news',array('!kont'=>$kont)));
        }
    }
}
function hound_noticia_email_is_duplicado($row,$grupo_node){    
    $sql='SELECT node.* 
    FROM {node} 
    LEFT JOIN {content_type_noticia} ON node.vid=content_type_noticia.vid 
    LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
    WHERE node.type="noticia" AND og_ancestry.group_nid=%d AND content_type_noticia.field_noticia_id_email_value="%s"';                    
    $res=db_query($sql,$grupo_node->nid,(string) $row->id_mail);
    while($row=db_fetch_object($res)){
        return 1;
    }
    return 0;
}
function hound_noticia_email_get_info($row){
    $result=array();
    $result['subdominio']='';                  
    $result['grupo_title']='';
    $result['noticia_title']=''; 
    $my_array=explode('.',(string) $row->title);
    if(isset($my_array[0])){
        $result['grupo_title']=substr($my_array[0],1);
    }
    if(isset($my_array[1]) && isset($my_array[2]) && isset($my_array[3])){
        $title_array=explode(' ',$my_array[3]);
        if(!(count($title_array)>1)){
            $my_array[3].=' '.hound_noticia_email_get_noticia_title_not_empty('');
            $title_array=explode(' ',$my_array[3]);
        }    
        if(count($title_array)>1){    
            $result['subdominio']=$my_array[1].'.'.$my_array[2].'.'.$title_array[0];
            $noticia_title_array=array_slice($title_array,1);
            $result['noticia_title']=implode(' ',$noticia_title_array);
            if(isset($my_array[4])){
                $tmp_array=array_slice($my_array,4);
                $result['noticia_title']=implode('.',$tmp_array);
            }
        }
    }
    $result['noticia_title']=hound_noticia_email_get_noticia_title_not_empty($result['noticia_title']);
    return $result;
}
function hound_noticia_email_is_subdominio($subdominio){
    global $base_root;
    $konp_base_root=str_replace('http://','',$base_root);
    $konp_base_root=str_replace('https://','',$konp_base_root);
    $konp_subdominio=str_replace('http://','',$subdominio);
    $konp_subdominio=str_replace('https://','',$konp_subdominio);
    /*
    //simulando
    $konp_subdominio='cursovtic.hontza.es';
    $konp_base_root='cursovtic.hontza.es';*/   
    if($konp_subdominio==$konp_base_root){
        return 1;
    }
    return 0;
}
function hound_noticia_email_notica_node_form_alter(&$form,&$form_state,$form_id){
    if(isset($form['field_noticia_id_email'])){
        unset($form['field_noticia_id_email']);
    }
}
function hound_noticia_email_get_uid($row,$grupo_node){
    $user_email=trim((string) $row->from);
    $my_user=user_load(array('mail'=>$user_email));
    if(isset($my_user->uid) && !empty($my_user->uid)){
        if(isset($my_user->og_groups[$grupo_node->nid]) && !empty($my_user->og_groups[$grupo_node->nid])){
            return $my_user->uid;
        }    
    }
    return 1;
}
function hound_noticia_email_is_hound_noticia_feed_noticia_email(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='hound'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='noticia'){            
            $param2=arg(2);
            if(!empty($param2) && $param2=='feed_noticia_email_actualizar'){
                return 1;
            }
        }
    }
    return 0;
}
function hound_noticia_email_get_grupo_node($grupo_title){
    $purl_row=hound_noticia_email_get_purl_row($grupo_title);
    if(isset($purl_row->id) && !empty($purl_row->id)){
        $grupo_node=node_load($purl_row->id);
    }else{    
        $grupo_node=node_load(array('title'=>$grupo_title));
    }
    return $grupo_node;
}
function hound_noticia_email_get_purl_row($my_value){
    $res=db_query($sql=sprintf('SELECT * FROM {purl} WHERE value="%s"',$my_value));
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function hound_noticia_email_get_content($row){
    $result=(string) $row->description;
    $pos=strpos($result,'<div dir="ltr">');
    if($pos===FALSE){
        return $result;
    }
    $result=substr($result,$pos);
    return $result;        
}
function hound_noticia_email_update_og_ancestry($node,$grupo_nid){
    db_query($sql=sprintf('UPDATE {og_ancestry} SET group_nid=%d WHERE nid=%d',$grupo_nid,$node->nid));
    //print $sql.'<br>';
}
function hound_noticia_email_validate($info){
    if(!(isset($info['noticia_title']) && !empty($info['noticia_title']))){
        return 0;
    }
    return 1;
}
function hound_noticia_email_get_noticia_title_not_empty($noticia_title){
    $result=trim($noticia_title);
    if(empty($result)){
        //$result=t('User news').' '.time();
        //$result=t('User news').' '.microtime();
        $result='Noticia de usuario';
    }
    return $result;
}
function hound_noticia_email_is_current_grupo($grupo_node){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if($my_grupo->nid==$grupo_node->nid){
            return 1;
        }
    }
    return 0;
}