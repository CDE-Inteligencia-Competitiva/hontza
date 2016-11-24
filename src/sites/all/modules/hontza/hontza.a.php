<?php
/*echo print_r($_SERVER,1).'<BR>';
print 'fitxategia='.$_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/dg_open_calais/opencalais.php';
exit();*/
require_once($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/dg_open_calais/opencalais.php');
//
function get_grupo_node_content($node){
    $html=array();
    $username='';
    $uid=$node->field_admin_grupo_uid[0]['value'];
    if(!empty($uid)){
        $my_user=user_load($uid);
        if(isset($my_user->uid)){
            $username=$my_user->name;
        }
    }
    //
    $html[]='<div class="field field-type-number-integer field-field-admin-grupo-uid">';
    $html[]='<div class="field-label">'.t('Group Administrator').':&nbsp;</div>';
    $html[]='<div class="field-items">';
    $html[]='<div class="field-item odd">';
    $html[]=$username.'</div>';
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);
}
function get_menutop_fix_purl($menutop){
    print $menutop;exit();
    return $menutop;
}
function limpiar_db_callback(){
   //my_delete_url_alias_all();
   return 'funcion desactivada';
   //$result=get_all_nodes(array('item','canal_de_yql','canal_de_supercanal','estrategia','decision','idea','oportunidad','proyecto','wiki','debate','canal_busqueda','fuentedapper','fuentehtml','noticia','noticias_portada','supercanal','chat'));
   //$result=get_all_nodes(array('item'));
   //$result=get_all_nodes(array('supercanal','canal_de_supercanal')); 
  // $result=get_all_nodes(array('wiki','debate','canal_busqueda','fuentedapper','fuentehtml','noticia','noticias_portada','supercanal','chat'));
   //$result=get_all_nodes(array('item'));   
   /*$result=get_all_nodes(array('informacion'));
   if(count($result)>0){
      foreach($result as $i=>$row){
           $node=node_load($row->nid);
           //if(!empty($node->grupo_nid) && $node->grupo_nid==143744){
           //    echo print_r($node,1);exit();
           //}
           //if(isset($node->field_item_canal_reference[0]['nid']) && !empty($node->field_item_canal_reference[0]['nid']) && $node->field_item_canal_reference[0]['nid']==181278){
           //    continue;
           //}
           //if($node->nid==181278){
           //    continue;
           //}
           if(isset($node->nid) && !empty($node->nid)){
                $nid=$node->nid;
                //print 'nid='.$nid;exit();
                //node_delete($nid);
           }
       }
   }*/
   //hontza_limpiar_taxonomy();
   //estrategia_limpiar_db();
   //idea_limpiar_db(); 
   return date('Y-m-d H:i:s');
}
function get_all_nodes($types){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='n.type IN("'.implode('","',$types).'")';
    $sql='SELECT * FROM {node} n WHERE '.implode(' AND ',$where).' ORDER BY n.title ASC';
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function my_delete_url_alias_all(){
    $my_list=my_get_url_alias_all();
    if(count($my_list)>0){        
        foreach($my_list as $i=>$row){
            my_delete_url_alias_nodes($row);
            my_delete_url_alias_users($row);
        }
    }
}
function my_get_url_alias_all(){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="ua.src LIKE '%%node/%%'";
    //$where[]="ua.src LIKE '%%user/%%'";
    //
    $sql="SELECT * FROM {url_alias} ua WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function my_delete_url_alias_nodes($row){
    $src=$row->src;
    $pos=strpos($src,"/");
    if($pos===FALSE){
        //
    }else{
        $s=substr($src,$pos+1);
        $s=trim($s);
        //
        if(!empty($s) && is_numeric($s)){
            $nid=$s;
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                //
                my_delete_url_alias_by_pid($row->pid);
            }else{
                my_delete_url_alias_by_pid($row->pid);
            }
        }else{
            my_delete_url_alias_by_pid($row->pid);
        }
    }
}
function my_delete_url_alias_by_pid($pid){
    $sql="DELETE FROM url_alias WHERE pid=".$pid;
    print $sql.'<BR>';
    $res=db_query($sql);
}
function my_delete_url_alias_users($row){
    $src=$row->src;
    $my_array=explode("/",$src);
    if(count($my_array)>1){
        $uid=$my_array[1];
        $user=user_load($uid);
        if(isset($user->uid) && !empty($user->uid)){
            //
            //print 'uid='.$user->uid.'<BR>';
        }else{
            my_delete_url_alias_by_pid($row->pid);
        }
    }
}
function is_usuarios_menu(){
    /*if(is_og_users()){
        return 1;
    }*/
    $param=arg(0);
    if(strcmp($param,'usuarios')==0){
        return 1;
    }
    return 0;
}
function my_menutop_usuarios_menu($html,$is_sf_js_enabled=1){
    $result=array();
    $result[]='<a id="context-block-region-menutop" class="context-block-region"></a>';
    $result[]='<div id="block-menu-primary-links" class="block block-menu block-odd region-odd clearfix style-menu ">';
    $result[]='<h3 class="title">Primary links</h3>';
    $result[]='<div class="content">';
    //intelsat-2015
    if($is_sf_js_enabled){
        $result[]='<ul class="menu sf-js-enabled">';
    }else{
        $result[]='<ul class="menu">';
    }
    $result[]=$html;
    $result[]='</div>';
    $result[]='</div>';
    return implode('',$result);
}
function is_usuarios_submenu(){
    $param=arg(0);
    if(in_array($param,array('usuarios_captacion_informacion','usuarios_aportacion_valor','usuarios_generacion_ideas'))){
        return 1;
    }
    return 0;
}
function probar_open_calais_callback(){
   return '';
   //$apikey = "7ka2pejw2v5cbtyyksu8pxd9";
$apikey=get_grupo_opencalais_api_key();
$oc = new OpenCalais($apikey);
/*
$content = <<<EOD

April 7 (Bloomberg) -- Yahoo! Inc., the Internet company that snubbed a $44.6 billion takeover bid from Microsoft Corp., may drop in Nasdaq trading after the software maker threatened to cut its bid if directors fail to give in soon.

If Yahoo's directors refuse to negotiate a deal within three weeks, Microsoft plans to nominate a board slate and take its case to investors, Chief Executive Officer Steve Ballmer said April 5 in a statement. He suggested the deal's value might decline if Microsoft has to take those steps.

The ultimatum may send Yahoo Chief Executive Officer Jerry Yang scrambling to find an appealing alternative for investors to avoid succumbing to Microsoft, whose bid was a 62 percent premium to Yahoo's stock price at the time. The deadline shows Microsoft is in a hurry to take on Google Inc., which dominates in Internet search, said analysts including Canaccord Adams's Colin Gillis.

EOD;*/

$node=node_load(37329);
$content=$node->body;

 //intelsat-2015
 $entities=array();   
 try{
    $entities = $oc->getEntities($content);
 }catch(OpenCalaisException $exception){
    hontza_canal_rss_on_opencalais_exception($exception);
 }
    foreach ($entities as $type => $values) {

            echo "<b>" . $type . "</b>";
            echo "<ul>";

            foreach ($values as $entity) {
                    echo "<li>" . $entity . "</li>";
            }

            echo "</ul>";
    }
}
function node_opencalais_callback(){
    //return '';
    $html=array();
    $nid=arg(1);
    //$apikey = "7ka2pejw2v5cbtyyksu8pxd9";
    $apikey=get_grupo_opencalais_api_key();
    $oc = new OpenCalais($apikey);

    $content='';

    $node=node_load($nid);
    //
    if(isset($node->nid) && !empty($node->nid)){
        //_set_default_menu($node->nid);
        if($node->type=='item'){
            $content=$node->body;
        }else{
            return t('Only item type');
        }
    }
    if(empty($content)){
        return '';
    }
    //
    //intelsat-2015
    $entities=array();    
    try{
        $entities = $oc->getEntities($content);
    }catch(OpenCalaisException $exception){
        hontza_canal_rss_on_opencalais_exception($exception);
    }
    foreach ($entities as $type => $values) {

            $html[]="<b>" . $type . "</b>";
            $html[]="<ul>";
            taxonomy_opencalais_term_save($node,$type,$values);
            foreach ($values as $entity) {
                    $html[]="<li>" . $entity . "</li>";
                    //taxonomy_opencalais_term_save($node,$type,$entity);
            }

            $html[]="</ul>";
    }
    return implode('',$html);
}
function taxonomy_opencalais_term_save($node,$opencalais_type,$entity_array,$my_tags=''){
     global $user;
     $vid=3;
     $vids=array();
     $vids[0]=$vid;
     if(empty($my_tags)){
        $tags=_community_tags_get_node_tags($node->nid,$vids);
     }else{
        $tags=$my_tags;
     }
     //
     $old_array=get_names_by_tags($tags);
     $new_entity_array=my_merge_array2($old_array, $entity_array);
     //
     $tags_and_terms=array();
     $tags_and_terms['tags']=array();
     $tags_and_terms['tags'][$vid]=array();
     $tags_and_terms['tags'][$vid]=$new_entity_array;
     //
     community_tags_taxonomy_node_save($node, $tags_and_terms, $is_owner, $user->uid);     
}
function is_open_calais_by_source($source,$canal_nid='',$node_in=''){
    //gemini-2013
    if(!empty($source)){
        $is_open=is_open_calais_by_fuente($source);
        return $is_open;
    }
    //
    if(!empty($source)){
        $canal_node=node_load($source->feed_nid);
    }else if(!empty($canal_nid)){
        $canal_node=node_load($canal_nid);
    }else{
        $canal_node=$node_in;
    }
    if(isset($canal_node->nid) && !empty($canal_node->nid)){
        if(strcmp($canal_node->type,'canal_de_yql')==0){
            if(isset($canal_node->field_apply_open_calais_yql) && isset($canal_node->field_apply_open_calais_yql[0])){
               if(isset($canal_node->field_apply_open_calais_yql[0]['value']) && !empty($canal_node->field_apply_open_calais_yql[0]['value'])){
                    return 1;
               }
            }
        }else{
             if(isset($canal_node->field_apply_open_calais) && isset($canal_node->field_apply_open_calais[0])){
               if(isset($canal_node->field_apply_open_calais[0]['value']) && !empty($canal_node->field_apply_open_calais[0]['value'])){
                    return 1;
               }
            }
        }
    }
    return 0;
}
function canal_open_calais_term_save($node,$my_tags=''){
    //return '';
    $nid=$node->nid;
    //$apikey = "7ka2pejw2v5cbtyyksu8pxd9";
    $apikey=get_grupo_opencalais_api_key();
    $oc = new OpenCalais($apikey);

    $content='';

    $node=node_load($nid);
    //
    if(isset($node->nid) && !empty($node->nid)){
        //_set_default_menu($node->nid);
        $content=$node->body;
    }
    if(empty($content)){
        return '';
    }
    //
    //intelsat-2015
    $entities=array();
    try{
        $entities = $oc->getEntities($content);
    }catch(OpenCalaisException $exception){
        hontza_canal_rss_on_opencalais_exception($exception);
    }
    
    $entity_array=array();

    foreach ($entities as $type => $values) {

            //$html[]="<b>" . $type . "</b>";
            //$html[]="<ul>";

            foreach ($values as $entity) {
                    //$html[]="<li>" . $entity . "</li>";
                    //taxonomy_opencalais_term_save($node,$type,$entity);
                    $entity_array[]=$entity;
            }

            //$html[]="</ul>";
    }
    if(count($entity_array)>0){
        taxonomy_opencalais_term_save($node,$type,$entity_array,$my_tags);
    }
}
function on_canal_open_calais_save(&$node){
    /*if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        if(is_open_calais_by_source('','',$node)){
            $nid_list=get_canal_node_nid_list($node);
            if(count($nid_list)>0){
                foreach($nid_list as $i=>$nid){
                    $my_node=node_load($nid);
                    //echo print_r($my_node,1);
                    if(isset($my_node->nid) && !empty($my_node->nid)){
                        canal_open_calais_term_save($my_node);
                    }
                }
            }
        }       
    }*/
}
function on_canal_save($op,&$node){
     if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
         if(!empty($op) && $op=='update'){
             $canal=$node;
             //if(!hontza_is_batch()){
             if(hontza_is_save_canal_save_reto_al_que_responde()){
                $is_form_save=0; 
                $canal->estrategia_responde_array=hontza_canal_get_request_responde_array($is_form_save);
                if($is_form_save){
                    hontza_canal_save_reto_al_que_responde($canal);
                }    
             }
         }
         on_canal_alchemy_save($node);
         on_canal_open_calais_save($node);
     }
}
function fix_node_canal_array_values(&$node){
            if(isset($node->field_fuentehtml_nid) && isset($node->field_fuentehtml_nid[0])){
                $a=$node->field_fuentehtml_nid[0];
                if(is_numeric($a)){
                    $v=$node->field_fuentehtml_nid[0];
                    $node->field_fuentehtml_nid[0]=array();
                    $node->field_fuentehtml_nid[0]['value']=$v;
                }
            }
            //
            if(isset($node->field_import_html_source_id) && isset($node->field_import_html_source_id[0])){
                $a=$node->field_import_html_source_id[0];
                if(is_numeric($a)){
                    $v=$node->field_import_html_source_id[0];
                    $node->field_import_html_source_id[0]=array();
                    $node->field_import_html_source_id[0]['value']=$v;
                }
            }
   
}
function on_canal_presave(&$node){     
    if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        fix_node_canal_array_values($node);
        //gemini-2014
        //hontza_argumentos_canal_presave($node);
        //intelsat-2015
        hontza_canal_rss_on_categorias_tematicas_presave($node);
        //
    }
}
function set_node_vid_entity_array($node,$entity_array){
    $result=array();
    if(count($entity_array)>0){
        //echo print_r($entity_array,1);exit();
        foreach($entity_array as $i=>$s){
            if(!in_node_vid_term($node,3,$s)){
                $result[]=$s;
            }
        }
    }
    return $result;
}
function in_node_vid_term($node,$vid,$s){
    $term_list=taxonomy_node_get_terms($node);
    if(!empty($term_list)){
        foreach($term_list as $tid=>$row){
            if($row->vid==$vid && $row->name==$s){
                //echo print_r($row,1);exit();
                return 1;
            }
        }
    }
    return 0;
}
function get_names_by_tags($tags){
    $result=array();
    if(!empty($tags)){
      foreach($tags as $tid=>$row){
          $result[]=$row->name;
      }
    }
    return $result;
}
function delete_node_tags($node,$tags){
    if(!empty($tags)){
        foreach($tags as $tid=>$row){
            _community_tags_delete_tags($node->nid,$row->tid);
        }
    }
}
function my_merge_array2($my_array,$my_array2){
    $result=array();
    $my_result=array_merge($my_array,$my_array2);
    if(count($my_result)>0){
        foreach($my_result as $i=>$v){
            if(!in_array($v,$result)){
                $result[]=$v;
            }
        }
    }
    return $result;
}
function unset_no_tid($taxonomy){
    $result=array();
    if(!empty($taxonomy)){
        foreach($taxonomy as $tid=>$row){
            if(is_numeric($tid)){
                $result[$tid]=$row;
            }
        }
    }
    return $result;
}
function quitar_repetidos_node_taxonomy(&$node){
    if(isset($node->taxonomy)){
        $taxonomy=$node->taxonomy;
        if(!empty($taxonomy)){
            $node->taxonomy=array();
            $my_array=array();
            foreach($taxonomy as $tid=>$term){
                if(!in_array($term->name,$my_array)){
                    $node->taxonomy[$tid]=$term;
                    $my_array[]=$term->name;
                }
            }
        }
    }
}
function my_prepare_node_taxonomy(&$node){
    quitar_repetidos_node_taxonomy($node);
}
function probar_alchemy_callback(){
    require_once($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/AlchemyAPI.php');

    $alchemyObj = new AlchemyAPI();


    // Load the API key from disk.
    $alchemyObj->loadAPIKey($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/api_key.txt');

    $htmlFile = file_get_contents("http://www.marca.com/2012/10/30/ciclismo/1351598536.html");


    // Extract a title from a HTML document.
    //$result = $alchemyObj->HTMLGetTitle($htmlFile, "http://www.marca.com/2012/10/30/ciclismo/1351598536.html");
    //echo "$result<br/><br/>\n";


    // Extract page text from a HTML document (ignoring navigation links, ads, etc.).
    $result = $alchemyObj->HTMLGetText($htmlFile, "http://www.marca.com/2012/10/30/ciclismo/1351598536.html");
    
    //echo "$result<br/><br/>\n";


    return $result;
}
function apply_alchemy_api($item){
    global $user;
    //if($user->uid!=1){
        //return $item['description'];
    //}
    //
    $link_array=(array) $item['link'];
    $link='';
    if(isset($link_array[0])){
        $link=$link_array[0];
    }
    if(empty($link)){
        return $item['description'];
    }
    //
    /*print $item['description'].'<BR>';
    print 'luz1===='.strlen($item['description']).'<BR>';
    $s=strip_tags($item['description']);
    print '----------------------------------------<BR>';
    print $s.'<BR>';
    print 'luz2===='.strlen($s).'<BR>';*/
    //
    require_once($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/AlchemyAPI.php');

    $alchemyObj = new AlchemyAPI();


    // Load the API key from disk.
    $alchemyObj->loadAPIKey($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/api_key.txt');

    $htmlFile = file_get_contents($link);



    // Extract page text from a HTML document (ignoring navigation links, ads, etc.).
    $result = $alchemyObj->HTMLGetText($htmlFile, $link);
   
    return $result;
}
function in_user_groups($grupo_nid,$my_user_in=''){
    $my_user=my_set_global_user($my_user_in);
    //
    if(isset($my_user->og_groups) && !empty($my_user->og_groups)){
        $gr_keys=array_keys($my_user->og_groups);
        return in_array($grupo_nid,$gr_keys);
    }
    return 0;
}
function is_user_administrador_de_grupo($my_user_in=''){
    global $user;
    if($user->uid==1){
        return 1;
    }
    $my_user=my_set_global_user($my_user_in);
    if(isset($my_user->roles) && !empty($my_user->roles)){
        $roles_keys=array_keys($my_user->roles);
        //intelsat-2015
        //return in_array(ADMINISTRADOR_DE_GRUPO,$roles_keys);
        if(in_array(ADMINISTRADOR_DE_GRUPO,$roles_keys)){
            return 1;
        }
        if(in_array(ADMINISTRADOR,$roles_keys)){
            return 1;
        }        
    }
    return 0;
}
function my_set_global_user($my_user_in=''){
    global $user;
    if(empty($my_user_in)){
        $my_user=$user;
    }else{
        $my_user=$my_user_in;
    }
    return $my_user;
}
function is_alchemy_by_source($source,$canal_nid='',$node_in=''){
    //intelsat-2015
    //se ha comentado esto, se ha pesuto abajo
    /*
    //gemini-2013
    if(!empty($source)){
        $is_alchemy=is_alchemy_by_fuente($source);
        return $is_alchemy;
    }
    //
    */
    if(!empty($source)){
        $canal_node=node_load($source->feed_nid);
    }else if(!empty($canal_nid)){
        $canal_node=node_load($canal_nid);
    }else{
        $canal_node=$node_in;
    }
    if(isset($canal_node->nid) && !empty($canal_node->nid)){
        if(strcmp($canal_node->type,'canal_de_yql')==0){
            if(isset($canal_node->field_apply_alchemy_yql) && isset($canal_node->field_apply_alchemy_yql[0])){
               if(isset($canal_node->field_apply_alchemy_yql[0]['value']) && !empty($canal_node->field_apply_alchemy_yql[0]['value'])){
                    return 1;
               }
            }
        }else{
             if(isset($canal_node->field_apply_alchemy) && isset($canal_node->field_apply_alchemy[0])){
               if(isset($canal_node->field_apply_alchemy[0]['value']) && !empty($canal_node->field_apply_alchemy[0]['value'])){
                    return 1;
               }
            }
        }
    }
    //intelsat-2015
    if(!empty($source)){
        $is_alchemy=is_alchemy_by_fuente($source);
        return $is_alchemy;
    }
    return 0;
}
function apply_alchemy_api_by_feeds_node_item($s,$feeds_node_item){
    global $user;
    //
    $link='';
    if(isset($feeds_node_item->url) && !empty($feeds_node_item->url)){
        $link=$feeds_node_item->url;
    }

    if(empty($link)){
        $link=$feeds_node_item->guid;
        if(empty($link)){
            return $s;
        }
    }
    //
    $link=hontza_limpiar_link_para_alchemy($link);
    //
    require_once($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/AlchemyAPI.php');

    $alchemyObj = new AlchemyAPI();


    // Load the API key from disk.
    $alchemyObj->loadAPIKey($_SERVER['DOCUMENT_ROOT'].base_path().'sites/all/libraries/alchemy_api/my_module/api_key.txt');

    //gemini-2013
    //$htmlFile = file_get_contents($link);
    $htmlFile = @file_get_contents($link);
    
    if($htmlFile==false){
       return $s;
    }


    
    




    // Extract page text from a HTML document (ignoring navigation links, ads, etc.).
    $result = $alchemyObj->HTMLGetText($htmlFile, $link);
    $xml = new SimpleXMLElement($result);
    if(isset($xml->text)){
        //return $xml->text;
        $my_value=$xml->text;
        if(empty($my_value)){
           return $s; 
        }
        return $my_value;
    }
    //return '';
    return $s;
}
function on_canal_alchemy_save(&$node){
    /*if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        if(is_alchemy_by_source('','',$node)){
            $nid_list=get_canal_node_nid_list($node);
            if(count($nid_list)>0){
                foreach($nid_list as $i=>$nid){
                    $my_node=node_load($nid);
                    //echo print_r($my_node,1);
                    if(isset($my_node->nid) && !empty($my_node->nid)){
                        $my_node->body=apply_alchemy_api_by_feeds_node_item($my_node->body,$my_node->feeds_node_item);
                        node_save($my_node);                       
                    }
                }
            }
        }
    }*/
}
function node_alchemy_callback(){
    $nid=arg(1);
    $node=node_load($nid);
    //
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='item'){
            $node->body=apply_alchemy_api_by_feeds_node_item($node->body, $node->feeds_node_item);
            node_save($node);
        }else{
            return t('Only item type');
        }
    }
    return t('Alchemy Api executed');
}
function is_listado_terminos_canales_por_categorias(){
	$my_url='';
	if(strcmp(arg(0),'node')==0 && is_numeric(arg(1)) && strcmp(arg(2),'og')==0 && strcmp(arg(3),'vocab')==0  && strcmp(arg(4),'terms')==0 && is_numeric(arg(5))){
		$vid=arg(5);
		//
		return is_vid_in_og_vocab($vid);
	}
	return 0;
}
function canales_my_categorias_callback($is_rss_param=0){
    global $language;
    $is_rss=0;
    if(!empty($is_rss_param) && is_numeric($is_rss_param)){
        if($is_rss_param==1){
            $is_rss=$is_rss_param;
        }
    }
    $output='';
    $tid=arg(2);    
    $num_rows=false;
    $term_name='';
    $arg_type=arg(3);
    //intelsat-2015
    $is_headers=1;
    if(hontza_canal_rss_is_publico_activado()){
        $info=publico_vigilancia_get_canales_categorias_params($tid,$arg_type);
        $tid=$info['tid'];
        $arg_type=$info['arg_type'];
        $is_headers=$info['is_headers'];
    }
    if($is_headers){
        $output.=hontza_canales_por_categorias_menu();
        /*$output.='<div class="view-header">';
        $output.=link_validar_canal('',1);
        $output.='</div>';*/
        $output.=hontza_define_vigilancia_form_filter();
    }    
    if($arg_type=='bookmarks'){
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            return '';
        }
        $bookmark_form_ini=hontza_solr_funciones_get_bookmark_ini(0);
        $output.=$bookmark_form_ini;
    }
    //            
    if(!empty($tid) && is_numeric($tid)){
        $term=taxonomy_get_term($tid);        
        if(isset($term->tid) && !empty($term->tid)){
            $term_name=$term->name;
        }
        //
        $my_limit=20;
        //intelsat-2015
        /*$is_tipos_fuente=0;        
        if(hontza_canal_rss_is_visualizador_activado()){
            $is_tipos_fuente=publico_vigilancia_get_is_tipos_fuente();
        }
        if($is_tipos_fuente){
            $item_list=publico_vigilancia_get_source_type_tid_item_list($tid);
        }else{*/
            //intelsat-2015
            $item_list=array();
            if(!(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado())){                 
                $item_list=get_tid_item_list($tid);
            }
            //intelsat-2015
            $item_list=hontza_canal_rss_solr_get_tid_item_array($tid,$item_list);
            //
            //intelsat-2015
            $item_list=red_get_tid_noticia_array($tid,$item_list);            
        //}
        $item_list=hontza_canales_por_categorias($item_list);
        if(hontza_canal_rss_is_publico_activado()){
            $item_list=publico_vigilancia_get_destacadas_item($item_list);                        
        }
        //intelsat-2015
        $item_list=red_ordenar_canales_my_categorias_item_array($item_list);
        $my_list=array();
        $kont=0;
        $num=count($item_list);
        $max=100;
        if($num>0){
            /*if($num>$max){
                $item_list=array_slice($item_list,0,$max);                
            }*/
            foreach($item_list as $i=>$nid){
                    /*$my_node=node_load($nid);
                    $my_list[$kont]=new stdClass();
                    $my_list[$kont]->view= node_view($my_node, 1);                
                    $my_list[$kont]->created=$my_node->created;
                    */
                    $my_list[$kont]=new stdClass();
                    $my_list[$kont]->nid=$nid;
                    $my_list[$kont]->view= '';
                    $created=hontza_get_node_created($nid);
                    $my_list[$kont]->created=$created;                
                $kont++;
                $num_rows=true;
            }
        }
        if(!empty($my_list)){
            if(empty($arg_type) || in_array($arg_type,array('pendientes','ultimas','validados','rechazados'))){
                $my_list=array_ordenatu($my_list,'created','desc',1);
            }
            if(!$is_rss){
                $my_list=my_set_estrategia_pager($my_list,$my_limit);
            }
            foreach($my_list as $z=>$row_page){
                $my_node=node_load($row_page->nid);
                $my_list[$z]->view=node_view($my_node, 1);                
            }
            if($is_rss){
                return $my_list;
            }
            $output.=set_array_view_html($my_list);
        }
    }

    if($is_rss){
        $item_array=array();
        return $item_array;
    }
    
      if ($num_rows) {
        /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
        drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
        $output .= theme('pager', NULL, $my_limit);
        
      }
      else {

        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
        
      }
      if(!empty($term_name)){
        $term_name=get_term_extra_name($tid, $language->language,$term_name);
        $my_title=t('News in category').': '.$term_name;
        if(hontza_canal_rss_is_tipo_fuente($tid)){            
            $my_title=hontza_canal_rss_get_noticias_tipo_fuente_title($term_name);
        }        
      }else{
        $my_title=t('News in category');  
      }

      drupal_set_title($my_title);
    //
    return $output;
}
function get_tid_item_list($tid){
    $result=array();
    $canal_nid_list=get_categoria_canal_nid_list($tid);
    if(count($canal_nid_list)>0){
        foreach($canal_nid_list as $i=>$canal_nid){
            $nid_list=get_canal_nid_list($canal_nid);
            $result=array_merge($result,$nid_list);
        }
    }
    return $result;
}
function my_get_request_fecha_value($f){
    $fecha_array=my_get_request_value($f);
    //gemini-2013
    $result='--';
    if(isset($fecha_array['year']) && isset($fecha_array['month']) && isset($fecha_array['day'])){
        $result=$fecha_array['year'].'-'.hontza_crm_add_cero($fecha_array['month']).'-'.hontza_crm_add_cero($fecha_array['day']);
    }
    if($result=='--'){
        return '';
    }
    //print $result;exit();
    return $result;
    //echo print_r($fecha_array,1);exit();
    return $fecha_array;
}
function my_array_shift($my_array){
    $result=array();
    if(!empty($my_array)){
        $kont=0;    
        foreach($my_array as $key=>$v){
            if($kont>0){
                $result[$key]=$v;
            }
            $kont++;
        }
    }
    return $result;
}
function my_array_unshift($my_array,$elem){
    $result=array();
    $result['']=$elem;
    if(!empty($my_array)){
        foreach($my_array as $key=>$v){
            $result[$key]=$v;
        }
    }
    return $result;
}
//gemini_lento: a ver si modificando la consulta va más rapido porque tarda 7 segundos
function my_og_home_ultimasnoticias_dash_pre_execute(&$view){
    $where=array();
    $where[]="1";
    $where[]="(node.type in ('item', 'noticia'))";
    $where[]="node.status <> 0";
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }

    $sql="SELECT node.nid AS nid,
       node.nid AS prueba_lento,
       node.title AS node_title,
       node.language AS node_language,
       flag_content.content_id AS flag_content_content_id,
       flag_content2.content_id AS flag_content2_content_id,
       node.created AS node_created
     FROM node node
     LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
     LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id AND flag_content_node.fid = 2
     LEFT JOIN flag_content flag_content_node_1 ON node.nid = flag_content_node_1.content_id AND flag_content_node_1.fid = 3
     LEFT JOIN flag_content flag_content ON node.nid = flag_content.content_id AND flag_content.fid = 2
     LEFT JOIN flag_content flag_content2 ON node.nid = flag_content2.content_id AND flag_content2.fid = 3
     WHERE ".implode(" AND ",$where)."
       ORDER BY node_created DESC";

    /*print $sql.'<BR>';
    print $view->build_info['query'];exit();*/
    $view->build_info['query']=$sql;
    $view->build_info['count_query']=$sql;
}
function my_item_node_form_alter(&$form,&$form_state, $form_id){
    if($form_id=='item_node_form'){
        unset($form['field_is_carpeta_noticia_publica']);
        unset($form['field_is_carpeta_noticia_destaca']);
        red_item_node_form_alter($form, $form_state, $form_id);
        if(isset($form['field_uniq'])){
            unset($form['field_uniq']);
        }
        //intelsat-2014
        hontza_solr_item_node_form_alter($form,$form_state, $form_id);
        hontza_social_item_node_form_alter($form,$form_state, $form_id);
        //
        //intelsat-2015
        red_despacho_item_node_form_alter($form,$form_state, $form_id);
        //intelsat-2016
        hontza_crm_inc_node_form_alter($form,$form_state, $form_id);
        red_copiar_item_node_form_alter($form,$form_state, $form_id);
    }
}
function my_canal_de_supercanal_node_form_alter(&$form,&$form_state, $form_id){
    if($form_id=='canal_de_supercanal_node_form'){
        unset($form['field_is_canal_noticia_publica']);
        unset($form['field_is_canal_noticia_destacada']);
        //gemini-2013
        if(isset($form['title'])){
            $form['title']['#title']=t('Title');
        }
        if(isset($form['field_fuente_canal'])){
            $form['field_fuente_canal'][0]['#title']=t('Origin');
        }
        if(isset($form['field_responsable_uid'])){
            $form['field_responsable_uid'][0]['#title']=t('Main Validator');
        }
        if(isset($form['field_responsable_uid2'])){
            $form['field_responsable_uid2'][0]['#title']=t('Second Validator');
        }
        if(isset($form['field_nombrefuente_canal'])){
            $form['field_nombrefuente_canal'][0]['#title']=t('Name of source');
        }
        if(isset($form['body_field']['body'])){
            $form['body_field']['body']['#title']=t('Description');
        }
        //gemini-2014
        $node=hontza_get_node_by_form($form);
        //intelsat-2015
        $nid=$node->nid;
        //intelsat-2015-kimonolabs
        $is_kimonolabs=0;
        if(hontza_canal_rss_is_kimonolabs_activado()){
            if(kimonolabs_is_fuente_kimonolabs('',$node)){
                $is_kimonolabs=1;
            }
        }
        if(!$is_kimonolabs){
            //intelsat-2014
            $my_pipe_id=red_funciones_get_canal_pipe_id($node);
            //
            $form['my_pipe_id']=array(
                '#title'=>'Pipe id',
                '#type'=>'textfield',
                '#default_value'=>$my_pipe_id,
            );
            //
        }else{
            kimonolabs_add_kimonolabs_api_key_form_field($form,$node);
        }    
        $form['hontza_parametros_del_canal']=array(
            '#value'=>hontza_parametros_del_canal($node,1),
        );
        $form['feeds']['FeedsHTTPFetcher']['source']['#attributes']['readonly']='readonly';                 
        $form['#after_build'][] = 'hontza_canal_de_supercanal_node_after_build';
        //
        $form['reto_al_que_responde_id']=array(
            //'#title' => t('Associated Challenge'),
            '#value'=>get_reto_al_que_responde_html(),
        );
        if(isset($form['field_last_import_time'])){
            unset($form['field_last_import_time']);
        }
        //intelsat-2015-kimonolabs
        if(hontza_canal_rss_is_kimonolabs_activado()){
            kimonolabs_add_origin_value_form_field_canal($form,$node);
        }
        //
        $form['#validate'][] = 'hontza_canal_validate_reto_al_que_responde';
        red_canal_de_supercanal_node_form_alter($form,$form_state,$form_id);
        //intelsat-2014
        //if(hontza_solr_is_solr_activado()){
        //intelsat-2015        
        if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
            hontza_solr_canal_de_supercanal_form_alter($form,$form_state,$form_id);
        }
        $form['field_nombrefuente_canal'][0]['#default_value']['value']=red_funciones_get_name_of_source_by_nid_fuente_canal($node,0,$form['field_nombrefuente_canal'][0]['#default_value']['value']);
        //intelsat-2015
        hontza_canal_comodin_canal_de_supercanal_node_form_alter($form,$form_state,$form_id);
        hontza_canal_rss_add_canal_categorias_tematicas_form_field($form,'canal_de_supercanal');
        //
        //intelsat-2015
        red_set_required_field_canal_source_type($form);
        //intelsat-2016
        hontza_crm_inc_canal_node_form_alter($form,$form_state, $form_id);
        red_copiar_canal_node_form_alter($form,$form_state, $form_id);
    }
    //gemini-2013
    unset_field_validate_api($form);
    if(isset($form['field_apply_alchemy'])){
        unset($form['field_apply_alchemy']);        
    }    
    if(isset($form['field_apply_open_calais'])){
        unset($form['field_apply_open_calais']);
    }    
    if(isset($form['field_nid_fuente_canal'])){
        unset($form['field_nid_fuente_canal']);
    }
    hontza_unset_activate_channel_form_alter($form, $form_state, $form_id); 
}
function get_group_member_count($row){
    //AVISO::::funcion definida en oportunidad.module
    if(isset($row->nid) && !empty($row->nid)){
        $user_list=my_get_og_grupo_user_list($row->nid);
        return count($user_list);
    }
    return 0;
}
function set_message_site_name($my_msg_in){
    $msg=$my_msg_in;
    $site_name=variable_get('site_name', '');
    $msg=str_replace('!site',$site_name,$msg);
    $msg=str_replace('!sitio',$site_name, $msg);
    return $msg;
}
function get_api_keys_content(){
    if(is_administrador_grupo()){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $html=array();
            $html[]=l(t('API Keys'),'group_api_keys/'.$my_grupo->nid);
            return implode('<BR>',$html);
        }
    }
    return '';
}
function group_api_keys_callback(){
     if(is_administrador_grupo()){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid=arg(1);
            if(!empty($grupo_nid)){
                return drupal_get_form('group_api_keys_form');
            }
        }
     }
     return '';
}
function group_api_keys_form(){
    $grupo_nid=arg(1);
    $form=array();
    $alchemy_key='';
    $opencalais_key='';
    $grupo=node_load($grupo_nid);
    if(isset($grupo->nid) && !empty($grupo->nid)){
        if(isset($grupo->field_alchemy_key[0]['value']) && !empty($grupo->field_alchemy_key[0]['value'])){
            $alchemy_key=$grupo->field_alchemy_key[0]['value'];
        }
        //
         if(isset($grupo->field_opencalais_key[0]['value']) && !empty($grupo->field_opencalais_key[0]['value'])){
            $opencalais_key=$grupo->field_opencalais_key[0]['value'];
        }
    }    
    $form['grupo_nid']=array('#type'=>'hidden',"#value"=>$grupo_nid);
    $form['alchemy_key']=array('#type'=>'textfield','#title'=>t('Alchemy API Key'),"#default_value"=>$alchemy_key);
    $form['opencalais_key']=array('#type'=>'textfield','#title'=>t('Opencalais Key'),"#default_value"=>$opencalais_key);
    $form['submit']=array('#type'=>'submit','#value'=>t('Save'),'#name'=>'api_keys_save_btn');
    return $form;
}
function group_api_keys_form_submit($form_id,&$form){
    //OHARRA::::puede que sean $form,&form_state los parametros de la funcion
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $grupo_nid=$form['values']['grupo_nid'];
        $alchemy_key=$form['values']['alchemy_key'];
        $opencalais_key=$form['values']['opencalais_key'];
        $grupo=node_load($grupo_nid);
        if(isset($grupo->nid) && !empty($grupo->nid)){
            $sql = 'delete from cache_content where cid = "content:'.$grupo_nid.':'.$grupo->vid.'"';
            db_query($sql);
            //
            $where=array();
            $where[]='nid='.$grupo_nid;
            $where[]='vid='.$grupo->vid;
            $sql='UPDATE content_type_grupo SET field_alchemy_key_value="'.$alchemy_key.'",field_opencalais_key_value="'.$opencalais_key.'" WHERE '.implode(' AND ',$where);
            db_query($sql);
        }
        drupal_set_message(t('API Keys saved'),'status');
   }
}
function get_grupo_alchemy_api_key($api_key_in,$field_name='field_alchemy_key'){
    $result=$api_key_in;
    $my_grupo=og_get_group_context();
     
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
       if(isset($my_grupo->$field_name)) {
            $my_array=$my_grupo->$field_name;
            if(isset($my_array[0]) && isset($my_array[0]['value']) && !empty($my_array[0]['value'])){
                return $my_array[0]['value'];
            }
       }
    }
    return $result;
}
function get_grupo_opencalais_api_key(){
    //intelsat-2015
    //$api_key=get_grupo_alchemy_api_key("7ka2pejw2v5cbtyyksu8pxd9",'field_opencalais_key');
    $api_key=get_grupo_alchemy_api_key('','field_opencalais_key');
    if(empty($api_key)){
        drupal_set_message('Estás utilizando la api genérica de Opencalais');
        $api_key='EuXqzZznh2VvbaLKI0dXMhiym9CumMki';
    }
    return $api_key;
}
function add_sources_vars_rows_apply_api($vars_in,$is_admin_grupo){
    $vars=$vars_in;
    $my_list=array('phpcode','phpcode-1','phpcode-2');
	//$vars['rows']=my_get_tr_fuentes_pipes($vars['rows'],$my_list);
	foreach($my_list as $k=>$field){
		$a='<td class="views-field views-field-'.$field.'">';
		$my_array=explode($a,$vars['rows']);
		//echo print_r($my_array,1);exit();
		if(count($my_array)==1){
			$a='<td class="views-field views-field-'.$field.' active">';
			$my_array=explode($a,$vars['rows']);
		}
		if(!empty($my_array)){
			//echo print_r($my_array,1);
			foreach($my_array as $i=>$my_value){
				if($i>0){
					//print $field.'='.$i.'='.$my_value.'<BR>';
					$pos=strpos($my_value,'</td>');
					if($pos!==FALSE){
						$s=trim(substr($my_value,0,$pos));
						$my_array[$i]=get_fuentes_table_api_value($i,$vars,$field,$is_admin_grupo).substr($my_value,$pos);
                                        }
				}
			}
		}
		$vars['rows']=implode($a,$my_array);
	}
    return $vars['rows'];
}
function get_fuentes_table_api_value($i_in,$vars,$field_in,$is_admin_grupo){
    global $user;
    $field=get_phpcode_field($field_in);
    $i=$i_in-1;
    //if($user->uid==1){
        $fuentes_list=$vars['view']->result;
        if(isset($fuentes_list[$i])){
            $fuente=node_load($fuentes_list[$i]->nid);
            if(isset($fuente->nid) && !empty($fuente->nid)){
                $api_fields=get_api_fields_by_type($fuente->type);
                if(isset($api_fields[$field])){
                    $f=$api_fields[$field];
                    $v=get_fuente_field_value($f,$fuente,1);
                    return $v;
                }
            }
        }
        return '';
    //}
}
function get_api_fields_by_type($type){
    $resul=array();
    //html
    $result['opencalais']='field_source_html_opencalais';
    $result['alchemy']='field_source_html_alchemy';
    $result['full_text_rss']='field_apply_full_text_rss_html';
    //
    if($type=='supercanal'){
        $result['opencalais']='field_apply_source_opencalais';
        $result['alchemy']='field_apply_source_alchemy';
        $result['full_text_rss']='field_apply_full_text_rss';
    }else if($type=='fuentedapper'){
        $result['opencalais']='field_apply_source_opencal';
        $result['alchemy']='field_dapper_source_alchemy';
        $result['full_text_rss']='field_apply_full_text_rss_dapper';
    }else if(in_array($type,array('canal_de_supercanal','canal_de_yql'))){
        $result['opencalais']='field_is_canal_opencalais';
        $result['alchemy']='field_apply_alchemy_yql';        
    }    
    return $result;
}
function get_phpcode_field($field){
    $my_array=define_source_phpcode_fields();
    if(isset($my_array[$field])){
        return $my_array[$field];
    }
    return $field;
}
function define_source_phpcode_fields(){
    $fields=array();
    $fields['phpcode']='alchemy';
    $fields['phpcode-1']='opencalais';
    $fields['phpcode-2']='full_text_rss';
    return $fields;
}
function get_fuente_field_value($f,$fuente,$is_image=1){
    $is_txeked=0;
    if(isset($fuente->$f)){
        $value_array=$fuente->$f;
        if(isset($value_array[0]) && isset($value_array[0]['value']) && !empty($value_array[0]['value'])){
            if($is_image){
                $is_txeked=1;
            }else{
                return 1;
            }
        }
    }
    if($is_image){
        //$checked='';
        $icon_name='api_cancel.png';
        if($is_txeked){
            $icon_name='api_ok.png';
            //$checked=' checked="checked"';
        }
        //return '<input type="checkbox" id="fuente_'.$fuente->nid.'_txek_'.$f.'" name="fuente_'.$fuente->nid.'_txek_'.$f.'" value="1"'.$checked.'/>';
        //return file_directory_path();
        return '<img src="'.base_path().'sites/all/themes/buho/images/icons/'.$icon_name.'" width="12" height="12">';
    }else{
        return 0;
    }
}
function add_ajax_fuente_api_txek($is_admin_grupo,$vars){
   global $base_url;
   $fuentes_list=$vars['view']->result;
   $id_array=get_fuente_txek_id_array_by_list($fuentes_list);
   $id_string=implode(',',$id_array);
   $js='var fuente_txek_nid_array=new Array('.$id_string.');
   $(document).ready(function()
   {
    if(fuente_txek_nid_array.length>0){
        for(var kont_f in fuente_txek_nid_array){
            $("#"+fuente_txek_nid_array[kont_f]).click(function(){
                var is_txeked=0;
                if($(this).attr("checked")){
                    is_txeked=1;
                }
                jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/ajax_save_source_key?my_time="+new Date().getTime(),
				data: {txek_id:$(this).attr("id"),is_txeked:is_txeked},
				dataType:"json",
				success: function(my_result){
                                    //
				}
			});
            });
        }
    }
   });';
    drupal_add_js($js,'inline');
}
function get_fuente_txek_id_array_by_list($node_list){
    $result=array();
    $field_array=define_source_phpcode_fields();
    if(!empty($field_array)){
        if(count($node_list)>0){
            foreach($node_list as $i=>$node){
                $fuente=node_load($node->nid);
                if(isset($fuente->nid) && !empty($fuente->nid)){
                    $api_fields=get_api_fields_by_type($fuente->type);
                    foreach($field_array as $k=>$field){
                        if(isset($api_fields[$field])){
                            $f=$api_fields[$field];
                            $v='"fuente_'.$fuente->nid.'_txek_'.$f.'"';
                            $result[]=$v;
                        }
                    }
                }
            }
        }
    }
    return $result;
}
function ajax_save_source_key_callback(){
    $my_post=$_POST;
    if(isset($my_post['txek_id'])){
        $info=get_info_by_txek_id($my_post['txek_id']);
        if(!empty($info['nid']) && !empty($info['field'])){
            $fuente=node_load($info['nid']);
            if(isset($fuente->nid) && !empty($fuente->nid)){
                $table=get_content_type_table($fuente->type);
                if(!empty($table)){
                    $sql = 'delete from cache_content where cid = "content:'.$fuente->nid.':'.$fuente->vid.'"';
                    db_query($sql);
                    //
                    $where=array();
                    $where[]='nid='.$fuente->nid;
                    $where[]='vid='.$fuente->vid;
                    //
                    $sql='UPDATE '.$table.' SET '.$info['field'].'_value='.$my_post['is_txeked'].' WHERE '.implode(' AND ',$where);
                    db_query($sql);
                }
            }
        }        
    }
    echo print_r($my_post,1);
    exit();
    return json_encode($my_post);
    exit();
}
function get_info_by_txek_id($txek_id){
    $result=array();
    $result['nid']='';
    $result['field']='';
    $s=str_replace('fuente_','',$txek_id);
    $pos=strpos($s,'_txek_');
    if($pos===FALSE){
        //
    }else{
        $result['nid']=trim(substr($s,0,$pos));
        $result['field']=trim(str_replace($result['nid'].'_txek_','',$s));
    }
    return $result;
}
function get_content_type_table($type){
    return 'content_type_'.$type;
}
function delete_field_form_by_type(&$form,$type){
    if(!is_administrador_grupo()){
        $api_fields=get_api_fields_by_type($type);
        if(!empty($api_fields)){
            $values=array_values($api_fields);
            if(count($values)>0){
                foreach($values as $i=>$f){
                    if(isset($form[$f])){
                        unset($form[$f]);                        
                    }
                }
            }
        }        
    }
}
function my_fuentedapper_node_form_alter(&$form,&$form_state,$form_id){
    delete_field_form_by_type($form,'fuentedapper');
    red_supercanal_node_form_alter($form,$form_state,$form_id);
}
function is_alchemy_by_fuente($source,$my_type_in=''){
    $my_type='alchemy';
    if(!empty($my_type_in)){
        $my_type=$my_type_in;
    }
    //return 0;
    $nid=$source->feed_nid;
    //echo print_r($source,1);exit();
    $canal=node_load($nid);
    $fuente_nid='';
    if(isset($canal->nid) && !empty($canal->nid)){
        if(isset($canal->field_nid_fuente_canal) && isset($canal->field_nid_fuente_canal[0]) && isset($canal->field_nid_fuente_canal[0]['value']) && !empty($canal->field_nid_fuente_canal[0]['value'])){
            $fuente_nid=$canal->field_nid_fuente_canal[0]['value'];
        }
    }
    //
    if(!empty($fuente_nid)){
        return is_alchemy_by_fuente_nid($fuente_nid,$my_type);
    }
    return 0;
}
function is_super_admin(){
    global $user;
    if($user->uid==1){
        return 1;
    }
    return 0;
}
function is_open_calais_by_fuente($source){
    $result=is_alchemy_by_fuente($source,'opencalais');    
    return $result;
}
function is_alchemy_by_yahoo($fuente_nid){
    if(!empty($fuente_nid)){
        return is_alchemy_by_fuente_nid($fuente_nid,'alchemy');
    }
    return 0;
}
function is_alchemy_by_fuente_nid($fuente_nid,$my_type){
    $fuente=node_load($fuente_nid);
    if(isset($fuente->nid) && !empty($fuente->nid)){
        $api_fields=get_api_fields_by_type($fuente->type);
        //
        if(isset($api_fields[$my_type])){
            $f=$api_fields[$my_type];
            $v=get_fuente_field_value($f,$fuente,0);
            return $v;
        }
    }
    return 0;
}
function mostrar_rss_html($elemento,$is_alchemy,$is_opencalais){
    $html=array();
    $description=$elemento->description;
    if($is_alchemy){
        $my_obj=array();
        $my_obj['guid']=$elemento->link;
        $my_obj=(object) $my_obj;
        //
        try {
            $description=apply_alchemy_api_by_feeds_node_item($description,$my_obj);
        } catch (Exception $e) {
            //print 'Excepcion capturada: '.$e->getMessage().'<BR>';
        }        
    }
    if($is_opencalais){
         $etiquetas=apply_opencalais_by_description($description);         
    }
    //
    $html[]='<li class="ybi">';
    $html[]='<div class="pipesImgdescription">';
    $html[]='<div class="pipesHolder">';
    $html[]='<div class="pipesThumbnail">';
    $html[]='</div>';
    $html[]='<div style="margin-left:0px;" class="pipesText">';
    $html[]='<div class="pipesTitle">';
    $html[]='<a class="a_noticia_title_show_validar_canal" target="_blank" href="'.$elemento->link.'">'.$elemento->title.'</a>';
    $html[]='</div>';
    $html[]='<div class="pipesDescription">'.$description.'</div>';
    if(!empty($etiquetas)){
        $html[]='<fieldset><legend>'.t('Opencalais tags').'</legend><div>'.$etiquetas.'</div></fieldset>';
    }
    $html[]='<ul class="pipesSmallthumb" style="margin-top: 0px;"></ul>';
    $html[]='</div></div></div></li>';
    return implode('',$html);
}
function is_opencalais_by_yahoo($fuente_nid){
    if(!empty($fuente_nid)){
        return is_alchemy_by_fuente_nid($fuente_nid,'opencalais');
    }
    return 0;
}
function apply_opencalais_by_description($content){
    //return '';
    //simulando
    /*$html=array();
    $html[]='<ul>';
    $html[]='<li class="li_show_validar_canal">aaaa</li><li class="li_show_validar_canal">bbbb</li><li class="li_show_validar_canal">cccc</li><li class="li_show_validar_canal">dddd</li>';
    $html[]='</ul>';
    return implode('',$html);*/
    //
    $apikey=get_grupo_opencalais_api_key();
    $oc = new OpenCalais($apikey);

    if(empty($content)){
        return '';
    }
    //
    //intelsat-2015
    $entities=array();
    try{
        $entities = $oc->getEntities($content);
    }catch(OpenCalaisException $exception){
        hontza_canal_rss_on_opencalais_exception($exception);
    }
    if(empty($entities)){
        return '';
    }
    
    $entity_array=array();
    $html=array();
    $html[]='<ul>';
    foreach ($entities as $type => $values) {

            

            foreach ($values as $entity) {
                    $html[]='<li class="li_show_validar_canal">'.$entity.'</li>';
                    $entity_array[]=$entity;
            }

            
    }
    $html[]='</ul>';
    //return $entity_array;
    return implode('',$html);
}
function probar_full_text_rss_callback(){
    //$url_full_text_rss="http://fulltextrssfeed.com/feed.php?url=";
    //$url_full_text_rss="http://fulltextrssfeed.com/";
    $nid=32589;
    $nid=928;
    //$canal=node_load($nid);
    $feeds_source=get_feeds_source($nid);
    if(isset($feeds_source->feed_nid) && !empty($feeds_source->feed_nid)){
        if(isset($feeds_source->source) && !empty($feeds_source->source)){
            //$url=$url_full_text_rss.urlencode($feeds_source->source);
            //$source=str_replace("http://","",$feeds_source->source);
            //$url=$url_full_text_rss.$source;
            $url=get_url_full_text_rss($feeds_source->source);
            //print $url;exit();
            $content=file_get_contents($url);
            print $content;exit();
        }
    }
    return 'es una prueba';
}
function get_feeds_source($nid){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="fs.feed_nid=".$nid;
    $sql='SELECT * FROM {feeds_source} fs WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=(object) $result;
    return $result;
}
function is_full_text_rss_by_yahoo($fuente_nid){
    if(!empty($fuente_nid)){
        return is_alchemy_by_fuente_nid($fuente_nid,'full_text_rss');
    }
    return 0;
}
function get_url_full_text_rss($url_in){
    $url_full_text_rss="http://fulltextrssfeed.com/";
    $source=str_replace("http://","",$url_in);
    return $url_full_text_rss.$source;
}
function get_fuente_by_canal($canal_nid,$mirar_tipo=1){
    $result=array();
    $result=(object) $result;
    $node=node_load($canal_nid);
    //AVISO::::por ahora no se le puede aplicar al yql el fulltextrss
    if($mirar_tipo && isset($node->nid) && !empty($node->nid) && $node->type!='canal_de_supercanal'){
        return $result;
    }
    //
        if(isset($node->field_nid_fuente_canal[0]) && isset($node->field_nid_fuente_canal[0]['value']) && !empty($node->field_nid_fuente_canal[0]['value'])){
            $fuente_nid=$node->field_nid_fuente_canal[0]['value'];
            if(!empty($fuente_nid)){
                $fuente=node_load($fuente_nid);
                return $fuente;
            }
        }
    
    
    return $result;
}
function create_canal_api_validate_form(){
    return drupal_get_form('canal_api_form');
}
function canal_api_form(){
    $form=array();
    $form['canal_api_fieldset']=array('#type'=>'fieldset','#title'=>t('Validate API types'));
    //
    $form['canal_api_fieldset']['validate_alchemy_submit']=array('#type'=>'submit','#value'=>t('Validate Alchemy'),'#name'=>'validate_alchemy_btn');
    $form['canal_api_fieldset']['validate_full_text_rss_submit']=array('#type'=>'submit','#value'=>t('Validate FulltextRSSFeed'),'#name'=>'validate_full_text_rss_btn');
    $form['canal_api_fieldset']['validate_opencalais_submit']=array('#type'=>'submit','#value'=>t('Validate OpenCalais'),'#name'=>'validate_opencalais_btn');
    //
    $form['canal_api_fieldset']['validate_all_submit']=array('#type'=>'submit','#value'=>t('Validate all'),'#name'=>'validate_all_btn');
    return $form;
}
function vaciar_taxonomy_callback(){
    borrar_todos_nodos_por_tipo('chat');
    return 'OK<BR>';
    //
    $my_list=taxonomy_get_vocabularies();
    if(count($my_list)>0){
        foreach($my_list as $i=>$v){
            $vid=$v->vid;
            //$tree=taxonomy_get_tree($vid);
            //$num=count($tree);
            //print 'vid===='.$vid.'===='.$num.'<BR>';
            print 'vid===='.$vid.'===='.$v->name.'<BR>';
            //taxonomy_del_vocabulary($vid);
           
        }
    }
    return '<BR>OK';
}
function borrar_todos_nodos_por_tipo($type){
    $result=my_get_nodes_by_type($type);
    if(count($result)>0){
        foreach($result as $i=>$node){
            //node_delete($node->nid);
            //print 'nid='.$node->nid.'<BR>';
            //$r=node_load($node->nid);
            //echo print_r($r,1);
            exit();
        }
    }
}
function my_get_nodes_by_type($type){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='type="'.$type.'"';
    $sql='SELECT * FROM node WHERE '.implode(' AND ',$where);
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function my_get_source_view_api_icon($node,$my_type){
    $v=0;
    $api_fields=get_api_fields_by_type($node->type);
    //
    if(isset($api_fields[$my_type])){
        $f=$api_fields[$my_type];
        $v=get_fuente_field_value($f,$node,0);        
    }
    $icon_name='api_cancel.png';
    if($v){
        $icon_name='api_ok.png';
        //$checked=' checked="checked"';
    }
    return '<img src="'.base_path().'sites/all/themes/buho/images/icons/'.$icon_name.'" width="12" height="12">';
}
function hontza_link_alter(&$links, $node, $comment = NULL){
    //gemini-2014
    $result=array();
    //    
    if(in_array($node->type,array('fuentehtml','supercanal','fuentedapper'))){
        if(isset($links['comment_add'])){
            if(is_administrador_grupo()){
                $links['show_rss_source']=array(
                'title'=>t('Show RSS'),
                'href'=>'show_validar_fuente_rss/'.$node->nid,
                'attributes'=>array(
                    'title'=>t('Show RSS'),
                'target'=>'_blank'),                
                );
            }
            unset($links['comment_add']);
        }        
        if(isset($links['statistics_counter'])){
            unset($links['statistics_counter']);
        }
    }/*else if(in_array($node->type,array('noticia','item'))){
        //gemini-2014
        if(!empty($links)){
            foreach($links as $key=>$value){
                if(!empty($key) && $key=='statistics_counter'){
                    $result[$key]=$value;
                }
            }
            $links=$result;
        }
        //
    }*/
}
function show_validar_fuente_rss_callback(){
    if(!is_administrador_grupo()){
        drupal_access_denied();
        exit();
    }
    $html=array();
    //$api_form=create_canal_api_validate_form();
    //$api_form='';
    //$html[]=$api_form;
    $html[]='<div class="ybr">';
    //
    $nid=arg(1);
    if(!empty($nid)){
        $rss='';
        $node=node_load($nid);
        $is_import_html=0;
        if($node->type=='fuentehtml'){
            $url=$node->field_fuentehtml_fuente[0]['value'];            
            if(!empty($url)){
                //$rss=get_rss_gen($url);
                $rss=rssgenparam($url,'');                
            }
            $is_import_html=1;
        }else if($node->type=='supercanal'){
            $pipe_id=$node->field_supercanal_fuente[0]['value'];
            $rss ='http://pipes.yahoo.com/pipes/pipe.run?_id='.$pipe_id.'&_render=rss';                                   
        }else if($node->type=='fuentedapper'){
            $rss ='http://open.dapper.net/transform.php?dappName='.$node->title.'&transformer='.$node->field_fuentedapper_fuente[0]['value'].$node->field_fuentedapper_extraargs[0]['value'];                                             
        }                
        //
        if(!empty($rss)){
            $is_alchemy=is_alchemy_by_yahoo($nid);
            $is_opencalais=is_opencalais_by_yahoo($nid);
            $is_full_text_rss=is_full_text_rss_by_yahoo($nid);
            //
            //print $_SESSION['url_show_validar_canal']['url'];exit();
            if($is_full_text_rss){
                $my_url=get_url_full_text_rss($rss);
                $content=@file_get_contents($my_url);                
            }else{
                $content=@file_get_contents($rss);
            }
            //
            if($content==false){
                
            }else{    
                $xml = new SimpleXMLElement($content);
                 $sets = $xml->channel;
                 $all = sizeof($sets);
                 //$my_kont=0;
                 for ($i=0; $i<$all; $i++) {
                   $r = $sets[$i];                    
                     foreach ($r->item as $elemento) {
                       /*if($my_kont>=5){
                           break;
                       }*/
                       $my_row=array();
                       $my_row=(object) $my_row;
                       $source=array();
                       $source['feed_nid']=$nid;
                       $source=(object) $source;
                       //
                       if(is_filtrar_numero_palabras($elemento->title,$is_import_html,$my_row,$source,$node)){                                              
                           $html[]=mostrar_rss_html($elemento,$is_alchemy,$is_opencalais,$is_full_text_rss);
                           //$my_kont++;
                       }
                     }
                 }
            }
        }        
    }
    $html[]='</div>';
    //$html[]=$api_form;
    return implode('',$html);
}
function is_filtrar_numero_palabras($title_in,$is_import_html,$my_row,$source,$node_source_in=''){
    if(!$is_import_html){
        return 1;
    }
    //
    $title=$title_in;
    //$title=str_replace(' ','&nbsp;', $title);
    $title=html_entity_decode($title);
    $title = str_replace("\xA0", ' ', $title);
    $my_array=explode(' ',$title);
    $my_array=trim_array_blanks($my_array);
    $word_min=0;
    if(isset($my_row->titulo_word_min) && !empty($my_row->titulo_word_min) && is_numeric($my_row->titulo_word_min)){
        if($my_row->titulo_word_min>0){
            $word_min=$my_row->titulo_word_min;
        }
    }
    if(empty($word_min)){
        if(!empty($node_source_in) && isset($node_source_in->nid) && !empty($node_source_in->nid)){
            $node_source=$node_source_in;            
        }else{
            $node_source=array();
            $node_source=(object) $node_source;
            $node=node_load($source->feed_nid);
            if(isset($node->nid) && !empty($node->nid)){
                if(isset($node->field_fuentehtml_nid[0]['value']) && !empty($node->field_fuentehtml_nid[0]['value'])){
                    $node_source=node_load($node->field_fuentehtml_nid[0]['value']);
                }
            }
        }
        //    
        if(isset($node_source->nid) && !empty($node_source->nid)){
            if(isset($node_source->field_titulo_word_min[0]['value']) && !empty($node_source->field_titulo_word_min[0]['value'])){
                $word_min=$node_source->field_titulo_word_min[0]['value'];
                $word_min=(int) $word_min;
                //print 'word_min===='.$word_min.'<BR>';exit();
            }
        }
        
        
    }
    //
    if(count($my_array)>=$word_min){
        return 1;
    }
    return 0;
}
function trim_array_blanks($my_array){
    $result=array();
    if(count($my_array)>0){
        foreach($my_array as $i=>$v){
            $value=trim($v);
            if(!empty($value)){
                $result[]=$v;
            }
        }
    }
    return $result;
}
function unset_field_validate_api(&$form){
        if(isset($form['field_validate_alchemy'])){
            unset($form['field_validate_alchemy']);
        }
        if(isset($form['field_validate_opencalais'])){
            unset($form['field_validate_opencalais']);
        }
        if(isset($form['field_validate_full_text_rss'])){
            unset($form['field_validate_full_text_rss']);
        }
}
//gemini-2014
function hontza_argumentos_canal_presave(&$node){
    if($node->type=='canal_de_supercanal'){
        hontza_argumentos_canal_de_supercanal_presave($node);            
    }else if($node->type=='canal_de_yql'){
        if(isset($node->nid) && !empty($node->nid)){
            if(hontza_is_hound_canal($node->nid)){
                hontza_argumentos_canal_hound_presave($node);
            }else{
                hontza_canal_de_yql_presave($node);
            }
        }    
    }
}
//gemini-2014
function hontza_argumentos_canal_de_supercanal_presave(&$node){
    if($node->type=='canal_de_supercanal'){
        if(isset($node->field_fuente_canal) && isset($node->field_fuente_canal[0]) && isset($node->field_fuente_canal[0]['value']) && !empty($node->field_fuente_canal[0]['value'])){
            if($node->field_fuente_canal[0]['value']=='PIPE'){
                $url_rss='';                    
                if(isset($node->feeds) && isset($node->feeds['FeedsHTTPFetcher']) && isset($node->feeds['FeedsHTTPFetcher']['source']) && !empty($node->feeds['FeedsHTTPFetcher']['source'])){
                    $url_rss=$node->feeds['FeedsHTTPFetcher']['source'];
                    $pipe_id=hontza_get_pipe_id_by_canal_feeds_source($url_rss);
                    if(isset($_REQUEST['filtro_parametros']) && !empty($_REQUEST['filtro_parametros'])){
                       if(!empty($pipe_id)){
                            $url_new=hontza_set_canal_de_supercanal_feeds_source($pipe_id,$_REQUEST['filtro_parametros']);
                            $node->feeds['FeedsHTTPFetcher']['source']=$url_new;                            
                        }
                    }    
                    red_funciones_set_canal_pipe_id_by_request($pipe_id,$node);
                }                
            }else if(in_array($node->field_fuente_canal[0]['value'],array('Kimonolabs','Dapper'))){
                //intelsat-2015-kimonolabs
                if(hontza_canal_rss_is_kimonolabs_activado()){
                    kimonolabs_argumentos_canal_de_supercanal_presave($node);
                }
            }
        }
    }
}
//gemini-2014
function hontza_get_pipe_id_by_canal_feeds_source($url_rss){
    $konp=hontza_get_pipe_url_ini();
    $pos=strpos($url_rss,$konp);
    if($pos===FALSE){
        return '';
    }else{
        $s=str_replace($konp,'',$url_rss);
        $sep='&';
        $my_array=explode($sep,$s);
        if(isset($my_array[0]) && !empty($my_array[0])){
            return $my_array[0];
        }
    }
    return '';
}
//gemini-2014
function hontza_get_pipe_url_ini($with_id=1){
    $result='http://pipes.yahoo.com/pipes/pipe.run?';
    if($with_id){
        $result.='_id=';
    }
    return $result;
}
//gemini-2014
function hontza_set_canal_de_supercanal_feeds_source($pipe_id,$filtro_parametros){
    $url=hontza_get_pipe_url_ini().$pipe_id.'&_render=rss';
    $params='';
    if(!empty($filtro_parametros)){
        $my_array=array();
        foreach($filtro_parametros as $name=>$value){
            $my_array[]=$name.'='.urlencode($value);
        }
        $params=implode('&',$my_array);
    }
    if(!empty($params)){
        $url.='&'.$params;
    }
    return $url;
}
//gemini-2014
function hontza_canal_de_supercanal_node_after_build($form, &$form_state) {                           
  $field = 'field_nombrefuente_canal';
  $node=hontza_get_node_by_form($form);
  if(!hontza_is_name_of_source_url_editable($node)){
    $form[$field][0]['value']['#attributes']['readonly'] = 'readonly';          
  }
  //$form_state['values'][$field][0]['value'] = $form[$field]['#default_value']['value'];
  return $form;                                                               
}
//gemini-2014
function hontza_yql_field_nombrefuente_canal_form_alter(&$form,&$form_state,$form_id){
    //if(!is_super_admin()){
    if(!is_administrador_grupo()){        
        $form['feeds']['FeedsHTTPFetcher']['source']['#attributes']['readonly']='readonly';
    }
    $form['#after_build'][] = 'hontza_canal_de_yql_node_after_build';    
    $form['field_nombrefuente_canal'][0]['#title']=t('Name of source').'/'.t('URL');
    //
    $nid=hontza_get_nid_by_form($form);    
    if(hontza_is_hound_canal($nid)){
        hound_editing_form_alter($form,$form_state,$form_id,$nid);
    }else{
        $rss_array=hontza_get_rss_array_by_field_nombrefuente_canal($form);
        if(count($rss_array)>1){
            $form['field_nombrefuente_canal'][0]['#prefix']='<div style="display:none;">';
            $form['field_nombrefuente_canal'][0]['#suffix']='</div>';    
            foreach($rss_array as $i=>$rss){
                $form['rss_part'.$i]=array(
                    '#type'=>'textfield',
                    '#title'=>t('Name of source').'/'.t('URL').$i,
                    '#name'=>'rss_part['.$i.']',
                    '#default_value'=>$rss,
                    '#weight'=>0,
                );
            }    
        }
        hontza_set_form_filtros_yql(0,1,$form);
    }    
}
//gemini-2014
function hontza_get_rss_array_by_field_nombrefuente_canal($form){
    $result=array();
    if(isset($form['field_nombrefuente_canal']) && isset($form['field_nombrefuente_canal'][0]) && isset($form['field_nombrefuente_canal'][0]['#default_value']) && isset($form['field_nombrefuente_canal'][0]['#default_value']['value'])){
        $value=$form['field_nombrefuente_canal'][0]['#default_value']['value'];
        $result=explode(',',$value);
        if(count($result)>0){
            foreach($result as $i=>$v){
                $result[$i]=trim($v);
            }
        }
    }
    return $result;
}
//gemini-2014
function hontza_canal_de_yql_node_after_build($form, &$form_state) {
    return hontza_canal_de_supercanal_node_after_build($form,$form_state);
}
//gemini-2014
function hontza_get_enlace_fuente_del_canal_view_html($node,$is_len=0){
    if(in_array($node->type,array('canal_de_supercanal'))){
        return red_get_enlace_fuente_del_canal_view_html($node);   
    }else if(in_array($node->type,array('canal_de_yql'))){
        //intelsat-2015
        if(hontza_is_hound_canal($node->nid)){
            $hound_url=hontza_get_hound_url_by_nid($node->nid,1);
            //intelsat-2016
            $hound_url=red_canal_get_url_by_len($hound_url,$is_len);
            $hound_url=l($hound_url,$hound_url,array('absolute' => TRUE,'attributes' => array('target' => '_blank')));
            return $hound_url;
        }
        if(isset($node->field_nombrefuente_canal[0]['value'])){					
            $value=$node->field_nombrefuente_canal[0]['value'];
            $my_array=explode(',',$value);
            $num=count($my_array);
            if($num>0){
                foreach($my_array as $i=>$v){
                    $url=trim($v);
                    //intelsat-2016
                    $url=red_canal_get_url_by_len($url,$is_len);
                    $link_array[]=l($url,$url,array('absolute' => TRUE,'attributes' => array('target' => '_blank')));
                }
                //
                $output='';
                if($num>1){
                    $output='<BR>';
                }
                $output.=implode('<BR>',$link_array);
                return $output;
            }            
        } 
    }
    if(isset($node->field_nombrefuente_canal[0]['view'])){					
        return $node->field_nombrefuente_canal[0]['view'];
    }
}
//gemini-2014
function hontza_get_hound_search_value_by_nid($nid){
    $hound_url=hontza_get_hound_url_by_nid($nid);
    $my_array=explode('&',$hound_url);
    if(count($my_array)>0){
        foreach($my_array as $i=>$v){
            $konp='hound_search=';
            $pos=strpos($v,$konp);
            if($pos===FALSE){
                //
            }else{
                $s=substr($v,$pos+strlen($konp));
                return $s;
            }
        }    
    }
    return '';
}
//gemini-2014
function hontza_argumentos_canal_hound_presave(&$node){
    //if(user_access('root')){
        if($node->type=='canal_de_yql'){            
            if(isset($node->nid) && !empty($node->nid)){
                if(hontza_is_hound_canal($node->nid)){
                    if(hontza_is_hound_text_input()){
                        hound_argumentos_canal_hound_presave($node);
                    }else{
                        if(isset($node->field_fuente_canal) && isset($node->field_fuente_canal[0]) && isset($node->field_fuente_canal[0]['value']) && !empty($node->field_fuente_canal[0]['value'])){
                            //if($node->field_fuente_canal[0]['value']=='RSS'){
                                if(isset($_REQUEST['hound_search_editing'])){
                                    $url_hound='';
                                    if(isset($node->feeds) && isset($node->feeds['FeedsHTTPFetcher']) && isset($node->feeds['FeedsHTTPFetcher']['source']) && !empty($node->feeds['FeedsHTTPFetcher']['source'])){
                                        $url_hound=$node->feeds['FeedsHTTPFetcher']['source'];
                                        $url_new=hontza_replace_hound_search($url_hound,$_REQUEST['hound_search_editing']);
                                        $node->feeds['FeedsHTTPFetcher']['source']=$url_new;
                                    }
                                }
                            //}
                        }
                    }    
                }
            }    
        }
    //}    
}
//gemini-2014
function hontza_replace_hound_search($url_hound,$hound_search_value_in){
    $hay_hound_search=0;
    $my_array=explode('&',$url_hound);
    if(count($my_array)>0){
        $result=array();        
        foreach($my_array as $i=>$v){            
            $konp='hound_search=';
            $pos=strpos($v,$konp);
            if($pos===FALSE){
                $result[]=$v;
            }else{
                $hound_search_value=hontza_limpiar_returns($hound_search_value_in);
                $result[]=$konp.$hound_search_value;
                $hay_hound_search=1;
            }
        }
        if(!$hay_hound_search){
            $hound_search_value=hontza_limpiar_returns($hound_search_value_in);
            $result[]=$konp.$hound_search_value;
        }
        return implode('&',$result);
    }
    return $url_hound;
}
//gemini-2014
//intelsat-2016
//function hontza_get_hound_title_by_nid($nid){
function hontza_get_hound_title_by_nid($nid,$is_br=0){
    if(hontza_is_hound_text_input()){
        return hound_get_title_by_nid($nid,$is_br);
    }else{
        $url_hound=hontza_get_hound_url_by_nid($nid);
        $parts = parse_url($url_hound);
        parse_str($parts['query'], $query);
        if(isset($query['title'])){
            return $query['title'];
        }
        return $url_hound;
    }
}
//gemini-2014
function hontza_canal_de_yql_presave(&$node){
    if($node->type=='canal_de_yql'){
        if(isset($node->nid) && !empty($node->nid)){
            if(isset($_REQUEST['rss_part']) && !empty($_REQUEST['rss_part'])){
                $rss_string_list="'".implode("','",$_REQUEST['rss_part'])."'";
                $data=$_REQUEST['rss_part'];            
                $node->field_nombrefuente_canal[0]['value']=implode(",",$data);
            }else{
                $rss_string_list="'".$node->field_nombrefuente_canal[0]['value']."'";
                $data=array();
                $data[0]=$node->field_nombrefuente_canal[0]['value'];
            }
            //gemini-2014
            if(!hontza_is_canal_atom($node)){
                if(isset($node->feeds) && isset($node->feeds['FeedsHTTPFetcher']) && isset($node->feeds['FeedsHTTPFetcher']['source']) && !empty($node->feeds['FeedsHTTPFetcher']['source'])){
                    $url_rss=$node->feeds['FeedsHTTPFetcher']['source'];
                    $url_rss=hontza_modify_url_rss_multiple($data,$rss_string_list,$url_rss,$my_canal_array);
                    $my_canal=(object) $my_canal_array['yql_obj'];
                    $my_canal->nid=$node->nid;
                    $my_canal->vid=$node->vid;
                    hontza_yql_save_canal_yql_parametros($my_canal,1);
                    $node->feeds['FeedsHTTPFetcher']['source']=$url_rss;
                }
            }    
        }    
    }
}
//gemini-2014
function hontza_modify_url_rss_multiple($data,$rss_string_list,$url_rss,&$form_state){
    $form=array();
    $form_state=hontza_define_filtros_editing_canales_rss();
    $filter=my_encode_yql_filter($form,$form_state);
    $result_temp=hontza_yql_wizard_define_url($data,$filter);
    //intelsat-2015
    if(hontza_canal_rss_is_query_yahooapis($result_temp)){
        $result=hontza_canal_rss_modify_url_rss_multiple($result_temp,$url_rss);
    }
    return $result;
    /*$s=urldecode($url_rss);
    $find=' from xml where url in(';
    $pos=strpos($s,$find);
    if($pos===FALSE){
        return $url_rss;
    }else{
        $a=substr($s,$pos+strlen($find));
        $pos_paren=strpos($a,')');
        if($pos_paren===FALSE){
            return $url_rss;
        }else{
            $b=substr($a,$pos);
            $result=substr($s,0,$pos).$find.$rss_string_list.substr($a, $pos_paren);
            return urlencode($result);
        }
    }*/
}
//gemini-2014
function hontza_limpiar_returns($s){
    $result=$s;
    //$result=eregi_replace("[\n|\r|\n\r]"," ",$result);
    $my_array=array("\n","\r","\n\r");
    foreach($my_array as $i=>$v){
        $result=str_replace($v,"",$result);
    }
    //quitar dobles espacios
    //$result=str_replace("  "," ",$result);
    //$result=eregi_replace("[\n|\r|\n\r]","",$s);
    return $result;
}
//gemini-2014
function hontza_set_form_filtros_yql($is_hound,$is_edit,&$form){
    $form['filtros1'] = array(
      '#title' => t('Apply filter 1 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros1'),
	  //
    );
    $form['filtros1']['todos'] = array(
      '#title' => t('General Search'),
      '#type' => 'textfield',
      '#size' => 20,
      '#description' => t('There will be shown only items containing the term in the title or description'),
    );
    $form['filtros2'] = array(
      '#title' => t('Apply filter 2 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros2'),
	  //
    );
    $form['filtros2']['titulo'] = array(
      '#title' => t('Contains this word in the title'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros2']['cbox1'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros2']['descripcion'] = array(
      '#title' => t('Contains this word in the description'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros3'] = array(
      '#title' => t('Apply filter 3 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	   //gemini
	  '#attributes'=>array('id'=>'my_filtros3'),
	  //
    );
    $form['filtros3']['no_titulo'] = array(
      '#title' => t('It does not contain this word in the title'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros3']['cbox2'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros3']['no_descripcion'] = array(
      '#title' => t('It does not contain this word in the description'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4'] = array(
      '#title' => t('Apply filter 4 to RSS feeds'),
      '#type' => 'fieldset',
      '#description' => t(''),
	  //gemini
	  '#attributes'=>array('id'=>'my_filtros4'),
	  //
    );
    $form['filtros4']['contiene'] = array(
      '#title' => t('Contains this word in the'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4']['select1'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('Title'), t('Description')),
    );
    $form['filtros4']['con'] = array(
      //'#title' => t('Anidación de filtros'),
      '#title' => t('Filter nesting'),  
      '#type' => 'fieldset',	   
    );
    $form['filtros4']['con']['cbox3'] = array(
      '#type' => 'radios',
      '#default_value' => 0,
      '#options' => array(t('OR'), t('AND')),
    );
    $form['filtros4']['no_contiene'] = array(
      '#title' => t('It does not contain the word'),
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['filtros4']['select2'] = array(
      '#type' => 'radios',
      '#default_value' => 1,
      '#options' => array(t('Title'), t('Description')),
    );
    
    //textarea entrada manual de filtros
    $form['filtros5'] = array(
      '#title' => t('Apply filter 5 to RSS feeds'),
      '#type' => 'fieldset',
      //gemini-2014
      //'#description' => t("By enabling filter 5 the other filters are disabled. It's the user responsibility.<br>")
      '#description' => t("By enabling filter 5 all the other filters will be disabled").'<br>  
      <A HREF="http://developer.yahoo.com/yql/guide/index.html" target="_blank"><b>Manual YQL</b></A>' ,
	   //gemini
	  '#attributes'=>array('id'=>'my_filtros5'),
	  //
    );
    $form['filtros5']['checkarea'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable to create filters manually'),
      '#prefix' => '<div class="filtro-manual">',
      '#suffix' => '</div>',
    );
    $form['filtros5']['area'] = array(
      '#title' => t('Code to create the filter manually'),
      '#type' => 'textarea',
      '#default_value' => 'and channel.item.title like "%Android%"',
      '#disabled' => false,
      '#prefix' => '<div class="area-filtro-manual">',
      '#suffix' => '</div>',
      '#weight' => 5,
      '#description' =>t('Ej: and (channel.item.title like "%Android%" and channel.item.description not like "%HTC%"'),
    );
    
    //gemini-2013
    if($is_hound){
        $form['filtros5']['#prefix']='<div style="display:none;">';
        $form['filtros5']['#suffix']='</div>';        
    }
    if($is_edit){
            $node=hontza_get_node_by_form($form);
            $canal_params=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            if(!empty($canal_params)){
                $html=array();
                $html[]='<div class="div_parametros_del_canal">';
                $html[]='<fieldset>';
                $html[]='<legend>'.t('Value of Parameters').'</legend>';
                $html[]='<div class="fieldset-wrapper">';
                if(hontza_is_canal_params_filtro1($canal_params)){
                    $form['filtros1']['todos']['#default_value']=$canal_params->todos;           
                }else if(hontza_is_canal_params_filtro2($canal_params)){
                    $form['filtros2']['titulo']['#default_value']=$canal_params->titulo;
                    if(!empty($canal_params->filtrosSI)){
                        $form['filtros2']['cbox1']['#default_value']=1;
                    }
                    $form['filtros2']['descripcion']['#default_value']=$canal_params->descripcion;
                }else if(hontza_is_canal_params_filtro3($canal_params)){
                    $form['filtros3']['no_titulo']['#default_value']=$canal_params->no_titulo;
                    if(!empty($canal_params->filtrosNO)){
                        $form['filtros3']['cbox2']['#default_value']=1;
                    }
                    $form['filtros3']['no_descripcion']['#default_value']=$canal_params->no_descripcion;
                }else if(hontza_is_canal_params_filtro4($canal_params)){
                    $form['filtros4']['campo_contiene']['#default_value']=$canal_params->campo_contiene;
                    $form['filtros4']['campo_no_contiene']['#default_value']=$canal_params->campo_no_contiene;
                    if(!empty($canal_params->conjuncion)){
                        $form['filtros4']['cbox3']['#default_value']=1;
                    }
                    $form['filtros4']['contiene']['#default_value']=$canal_params->contiene;
                    $form['filtros4']['no_contiene']['#default_value']=$canal_params->no_contiene;
                }else if(hontza_is_canal_params_filtro5($canal_params)){
                    if(!empty($canal_params->area)){
                        $form['filtros5']['checkarea']['#default_value']=1;
                    }
                    $form['filtros5']['area']['#default_value']=$canal_params->area;
                }else{
                    return '';
                }
                $html[]='</div>';
                $html[]='</fieldset>';
                $html[]='</div>';
                return implode('',$html);        
            }
    }    
}
//gemini-2013
function my_encode_yql_filter(&$form,&$form_state){
  //gemini
  $filter=''; 
  //Filtro manual
  if($form_state['yql_obj']->filtros['area']){
    $filter = $form_state['yql_obj']->filtros['area'];    
  }
  //Resto de filtros
  else{
    //gemini-2013  
    $form_state['yql_obj']->filtros['titulo']=my_yql_replace($form_state['yql_obj']->filtros['titulo']);
    $form_state['yql_obj']->filtros['descripcion']=my_yql_replace($form_state['yql_obj']->filtros['descripcion']);
    $form_state['yql_obj']->filtros['no_titulo']=my_yql_replace($form_state['yql_obj']->filtros['no_titulo']);
    $form_state['yql_obj']->filtros['no_descripcion']=my_yql_replace($form_state['yql_obj']->filtros['no_descripcion']);
    $form_state['yql_obj']->filtros['contiene']=my_yql_replace($form_state['yql_obj']->filtros['contiene']);
    $form_state['yql_obj']->filtros['no_contiene']=my_yql_replace($form_state['yql_obj']->filtros['no_contiene']);
    //        
    //Filtro 1 todos
    if($form_state['yql_obj']->filtros['todos']){
        //gemini-2013
        $form_state['yql_obj']->filtros['todos']=my_yql_replace($form_state['yql_obj']->filtros['todos']);
        //                     
        //gemini
        //if(!empty($filter)){
        $filter.=' and ';
        //}
        //$filter=' and (channel.item.title like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.description like "%'.$form_state['yql_obj']->filtros['todos'].'%")';
        //gemini-2013
        //$filter.='(channel.item.title like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.description like "%'.$form_state['yql_obj']->filtros['todos'].'%")';
        $filter.='(channel.item.title like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.description like "%'.$form_state['yql_obj']->filtros['todos'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['todos'].'%")';        
        //         
    }
    //Filtro 2 
    elseif($form_state['yql_obj']->filtros['titulo'] or $form_state['yql_obj']->filtros['descripcion']){              
      if($form_state['yql_obj']->filtros['titulo'] and $form_state['yql_obj']->filtros['descripcion']){
          
        if($form_state['yql_obj']->filtrosSI==0){
          $f='or';
        }
        else{
          $f='and';
        }
        
        //gemini-2013
        //$filter=' and (channel.item.title like "%'.$form_state['yql_obj']->filtros['titulo'].'%" '.$f.' channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%")';       
        $filter=' and (channel.item.title like "%'.$form_state['yql_obj']->filtros['titulo'].'%" '.$f.' (channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['descripcion'].'%"))';
        
      }
      elseif($form_state['yql_obj']->filtros['titulo']){
        $filter=' and channel.item.title like "%'.$form_state['yql_obj']->filtros['titulo'].'%"';
      }
      elseif($form_state['yql_obj']->filtros['descripcion']){
        //gemini-2013  
        //$filter=' and channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%"';
        $filter=' and (channel.item.description like "%'.$form_state['yql_obj']->filtros['descripcion'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['descripcion'].'%")';
      }
    }
    
    //Filtro 3
    elseif($form_state['yql_obj']->filtros['no_titulo'] or $form_state['yql_obj']->filtros['no_descripcion']){
      if($form_state['yql_obj']->filtros['no_titulo'] and $form_state['yql_obj']->filtros['no_descripcion']){
        if($form_state['yql_obj']->filtrosNO==0){
          $f='or';
        }
        else{
          $f='and';
        }
        //gemini-2013
        //$filter=' and (channel.item.title not like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%" '.$f.' channel.item.description not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%")';
        $filter=' and (channel.item.title not like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%" '.$f.' (channel.item.description not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%" and channel.item.content:encoded not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%"))';
        
        
      }
      elseif($form_state['yql_obj']->filtros['no_titulo']){
        //gemini-2013
        //$filter=' and channel.item.title like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%"';
        $filter=' and channel.item.title not like "%'.$form_state['yql_obj']->filtros['no_titulo'].'%"';  
      }
      elseif($form_state['yql_obj']->filtros['no_descripcion']){
        //gemini-2013  
        //$filter=' and channel.item.description like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%"';
        $filter=' and (channel.item.description not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%" and channel.item.content:encoded not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%")';  
      }
    }
    //Filtro 4
    elseif($form_state['yql_obj']->filtros['contiene'] or $form_state['yql_obj']->filtros['no_contiene']){
      //Tipo del contiene
      if($form_state['yql_obj']->campo_contiene==0){
          $name1='title';
        }
        else{
          $name1='description';
        }
        //Tipo del no_contiene
      if($form_state['yql_obj']->campo_no_contiene==0){
          $name2='title';
        }
        else{
          $name2='description';
        }
      if($form_state['yql_obj']->filtros['contiene'] and $form_state['yql_obj']->filtros['no_contiene']){
        if($form_state['yql_obj']->conjuncion==0){
          $f='or';
        }
        else{
          $f='and';
        }
        //gemini-2013
        //$filter=' and (channel.item.'.$name1.' like "%'.$fo,$form_state['yql_obj']rm_state['yql_obj']->filtros['contiene'].'%" '.$f.' channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%")';
        if($name1=='description' && $name2=='description'){
            $filter=' and ((channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['contiene'].'%") '.$f.' (channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%" and channel.item.content:encoded not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%"))';        
        }else if($name1=='description'){
            $filter=' and ((channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['contiene'].'%") '.$f.' channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%")';        
        }else if($name2=='description'){
            $filter=' and (channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" '.$f.' (channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%" and channel.item.content:encoded not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%"))';                    
        }else{
            $filter=' and (channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" '.$f.' channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%")';        
        }
        //
      }
      elseif($form_state['yql_obj']->filtros['contiene']){
        //gemini-2013  
        //$filter=' and channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%"';  
        if($name1=='description'){  
            $filter=' and (channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%" or channel.item.content:encoded like "%'.$form_state['yql_obj']->filtros['contiene'].'%")';
        }else{
            $filter=' and channel.item.'.$name1.' like "%'.$form_state['yql_obj']->filtros['contiene'].'%"';  
        }    
        
      }
      elseif($form_state['yql_obj']->filtros['no_contiene']){
        //gemini-2013  
        //$filter=' and channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_descripcion'].'%"';
        if($name2=='description'){ 
            $filter=' and (channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%" and channel.item.content:encoded not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%")';
        }else{  
            $filter=' and channel.item.'.$name2.' not like "%'.$form_state['yql_obj']->filtros['no_contiene'].'%"';
        }    
      }
    }
  }
    //print $filter;exit();
  return $filter;
}
//gemini-2013
function my_yql_replace($s){
    $result=str_replace("\"","",$s);
    $result=str_replace("'","",$result);    
    return $result;
}