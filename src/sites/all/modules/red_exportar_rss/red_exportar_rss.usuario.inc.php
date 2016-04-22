<?php
function red_exportar_rss_usuario_callback(){
    global $base_url;
    $uid=arg(2);
    
    $xml_url=red_exportar_rss_usuario_get_xml_url_name($uid);
    
    red_exportar_rss_usuario_create($uid);
    
    drupal_goto($xml_url);
    exit();
}
function red_exportar_rss_usuario_get_xml_url_name($uid){
    global $base_url;
    $sites_filename=red_exportar_rss_usuario_get_sites_filename($uid);
    $xml_url=url($base_url.base_path().$sites_filename,array('absolute'=>TRUE));
    if(hontza_is_sareko_id('LOKALA')){
        $xml_url='http://localhost/proba/canal_usuario_rss_xml/uid_'.$uid.'.xml';
    }
    return $xml_url;
}
function red_exportar_rss_usuario_get_sites_filename($uid,$grupo_nid_in=''){
     if(empty($grupo_nid_in)){
        $grupo_nid=red_exportar_rss_usuario_get_grupo_nid();
     }else{
        $grupo_nid=$grupo_nid_in; 
     }
     //intelsat-2016
     $my_path='sites/'.strtolower(_SAREKO_ID).'.hontza.es/files';
     $file_directory_path=variable_get('file_directory_path','');
     if($file_directory_path=='sites/default/files'){
         $my_path='sites/default/files';
     }
     //print $my_path;exit();
     $sites_filename=$my_path.'/'.'uid_'.$grupo_nid.'_'.$uid.'.xml';
     return $sites_filename;
}
function red_exportar_rss_usuario_create($uid,$grupo_nid=''){
    $sites_filename=red_exportar_rss_usuario_get_sites_filename($uid,$grupo_nid);
    $xml_url=red_exportar_rss_usuario_get_xml_url_name($uid);
    /*print $xml_url.'<BR>';
    exit();*/    
    $creation_date='';
    $my_user=new stdClass();
    if($uid!='all'){
        $my_user=user_load($uid);
        $creation_date=date('Y-m-d H:i:s',$my_user->created);
    }
    //header('Content-Type: application/rss+xml; charset=utf-8');
    $canal_usuarios_title=hontza_get_canal_usuarios_title($uid);
    
    $rssfeed = '<?xml version="1.0" encoding="utf-8"?>';
    $rssfeed .= '<rss version="2.0">';
    $rssfeed .= '<channel>';
    $rssfeed .= '<title>'.check_plain($canal_usuarios_title).'</title>';
    $rssfeed .= '<link>'.check_url($xml_url).'</link>';
    $rssfeed .= '<description><![CDATA['.$canal_usuarios_title.']]></description>';
    //$rssfeed .= '<language>en-us</language>';
    $rssfeed .= '<copyright>Powered by Hontza</copyright>';
    $rssfeed .= '<creation_date>'.$creation_date.'</creation_date>';
    $rssfeed .= '<date_of_last_content></date_of_last_content>';
    $rssfeed .= '<date_of_last_update></date_of_last_update>';
    $rssfeed .= '<thematic_categories></thematic_categories>';
    $rssfeed .= '<source_type></source_type>';
    $rssfeed .= '<main_validator></main_validator>';
    $rssfeed .= '<second_validator></second_validator>';
    $rssfeed .= '<name_of_source_url></name_of_source_url>';                            
    $rssfeed .= '<rating></rating>';
    if(hontza_is_sareko_id_red()){
        $rssfeed .= '<languages></languages>';
        $rssfeed .= '<update_frequency></update_frequency>';
        $rssfeed .= '<main_language></main_language>';
        $rssfeed .= '<country_or_region></country_or_region>';
    }
    $rssfeed .= '<key_intelligence_questions></key_intelligence_questions>';
    $rssfeed .= '<value_of_the_arguments></value_of_the_arguments>';   
    //    
    $node_array=red_exportar_rss_get_noticias_de_usuario($uid,$grupo_nid);
    if(!empty($node_array)){
        foreach($node_array as $i=>$node){
                $title=$node->title;
                $description=$node->body;
                $link=red_exportar_rss_get_noticia_de_usuario_link($node);
                //
                $rssfeed .= '<item>';
                $rssfeed .= '<title>' . check_plain($title) . '</title>';
                $rssfeed .= '<description><![CDATA[' . $description . ']]></description>';
                $rssfeed .= '<link>'.check_url($link).'</link>';
                $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O",$node->created) . '</pubDate>';
                $rssfeed .= '<created>' . date("Y-m-d H:i:s",$node->created) . '</created>';
                $rssfeed .= '<my_channel>'.red_exportar_rss_get_noticia_de_usuario_canal_title($node,$uid,$canal_usuarios_title).'</my_channel>';
                $rssfeed .= '<rating>'.hontza_get_node_puntuacion_media_para_txt($node->nid,1).'</rating>';
                $rssfeed .= '<tags>'.red_exportar_rss_get_item_tags_format_xml($node).'</tags>';
                if(hontza_is_sareko_id_red()){
                    $rssfeed .= '<summary><![CDATA['.red_get_summary($node).']]></summary>';
                    $rssfeed .= '<news_types>'.red_exportar_rss_get_item_news_type_format_xml($node).'</news_types>';
                    $rssfeed .= '<units>'.red_exportar_rss_get_item_units_format_xml($node).'</units>';
                    //intelsat-2016
                    $field_red_item_web=red_fields_inc_get_field_item_web_name();
                    $url_address=red_field($node,$field_red_item_web);
                    if(empty($url_address)){
                        $url_address=$link;
                    }
                    $rssfeed .= '<url_address>'.check_url($url_address).'</url_address>';
                    $rssfeed .= '<bulletins>'.red_boletines($node).'</bulletins>';
                    //intelsat-2016
                    $field_red_fuente_noticia=red_fields_inc_get_field_red_fuente_noticia_name();
                    $rssfeed .= '<news_source>'.red_field($node,$field_red_fuente_noticia).'</news_source>';
                    $rssfeed .= '<sectorisation>'.red_sectorizacion($node).'</sectorisation>';
                    $rssfeed .= '<cnae>'.red_cnae($node).'</cnae>';
                    $rssfeed .= '<visits>'.red_reads_visitas($node).'</visits>';
                    $rssfeed .= '<key_intelligence_questions>'.red_exportar_rss_get_canal_reto_al_que_responde_xml($node).'</key_intelligence_questions>';
                }
                $rssfeed .= '</item>';                    
        }
    }
    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';
    
    if(hontza_is_sareko_id('LOKALA')){
        $sites_filename='/var/www/proba/canal_usuario_rss_xml/uid_'.$uid.'.xml';        
    }
    
    $file = fopen($sites_filename,"w");
    
    if(!$file){
        //print 'No se puede abrir el fichero';exit();
        return 0;
    }
    
    fputs($file,$rssfeed);
    fclose($file);
}
function red_exportar_rss_get_noticias_de_usuario($uid,$grupo_nid=''){
    $result=array();
    $sql=hontza_get_canal_usuarios_sql($uid,0,$grupo_nid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=node_load($row->nid);
    }
    return $result;
}
function red_exportar_rss_get_noticia_de_usuario_link($node){
    global $base_url;
    $result=$base_url.'/node/'.$node->nid;
    return $result;        
}
function red_exportar_rss_get_noticia_de_usuario_canal_title($node,$uid,$canal_usuarios_title){
    return $canal_usuarios_title;
}
function red_exportar_rss_usuario_get_grupo_nid(){
    $grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_nid=$my_grupo->nid;
    }else{
        $grupo_nid=$_REQUEST['my_grupo_nid'];
    }
    return $grupo_nid;
}
function red_exportar_rss_exportar_usuarios_tarea_callback(){
    red_exportar_rss_exportar_usuarios_tarea();
    return date('Y-m-d H:i');
}
function red_exportar_rss_exportar_usuarios_tarea(){
    $grupo_nid_array=array();
    $user_array=red_crear_usuario_get_user_array();
    if(!empty($user_array)){
        foreach($user_array as $i=>$user_row){
            if(isset($user_row->uid) && !empty($user_row->uid)){
                $my_user=user_load($user_row->uid);
                if(isset($my_user->og_groups) && !empty($my_user->og_groups)){
                    foreach($my_user->og_groups as $grupo_nid=>$grupo_row){
                        red_exportar_rss_usuario_create($user_row->uid,$grupo_nid);
                        if(!in_array($grupo_nid,$grupo_nid_array)){
                            $grupo_nid_array[]=$grupo_nid;
                        }    
                    }
                }
            }
        }
    }
    if(!empty($grupo_nid_array)){
        foreach($grupo_nid_array as $i=>$grupo_nid){
            red_exportar_rss_usuario_create('all',$grupo_nid);
        }    
    }
}