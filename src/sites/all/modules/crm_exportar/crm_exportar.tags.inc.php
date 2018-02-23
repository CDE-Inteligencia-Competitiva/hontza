<?php
function crm_exportar_tags_automatic_callback(){
	/*global $base_url;
	if(crm_exportar_is_crm_exportar_tag()){
		$url=$base_url.'/crm_exportar/textos_exportar_todas_noticias_automatic_tags/todas/0/0?is_duplicate_news=1';
		$content=file_get_contents($url);
		$result=json_decode(base64_decode(red_crear_usuario_decrypt_text($content)));
		crm_exportar_tags_automatic_save($result);
	}
	return date('Y-m-d H:i');*/
	return 'Desactivado';
}
function crm_exportar_textos_exportar_todas_noticias_automatic_tags_callback(){
	$is_automatic_tags=1;
	$automatic_result=crm_exportar_textos_exportar_todas_noticias($is_automatic_tags);
	print red_crear_usuario_encrypt_text(base64_encode(json_encode($automatic_result)));
	exit();
}
function crm_exportar_tags_automatic_save($result){
	$vid=hontza_crm_inc_get_tags_vid();
	$uid=1;	
	if(!empty($result)){
		if(isset($result->nid_tag_array) && !empty($result->nid_tag_array)){
			$nid_tag_array=$result->nid_tag_array;
			foreach($nid_tag_array as $i=>$my_row){
				$nid=$my_row->nid;
				$row=$my_row->row;
				$uid=1;
				$my_user_info=my_get_user_info($node);
				if(empty($my_user_info['uid'])){
					$uid=0;
				}else{
					$uid=$my_user_info['uid'];
				}				
				if(isset($row->value) && !empty($row->value)){
					$term_name='CRM:'.$row->value;
					//$term_row=red_solr_inc_taxonomy_get_term_by_name_vid_row($term_name,$vid);
                                        $term_row=crm_exportar_tags_taxonomy_get_term_by_name_vid_row($term_name,$vid);
                                        if(!isset($term_row->tid)){
						$term = array(
						 'name' => $term_name,
						 'vid' => $vid,				
						);
						taxonomy_save_term($term);
						$term_row=red_solr_inc_taxonomy_get_term_by_name_vid_row($term_name,$vid);
					}
					if(isset($term_row->tid)){
						$node=node_load($nid);
						if(isset($node->nid)){
							if(!isset($node->taxonomy[$term_row->tid])){
								/*$node->taxonomy[$term_row->tid]->tid=$term_row->tid;
								$node->taxonomy[$term_row->tid]->vid=$vid;
								$node->taxonomy[$term_row->tid]->name=$term_name;*/
								db_query('INSERT INTO {term_node} (nid, vid, tid) VALUES (%d, %d, %d)', $node->nid, $node->vid,$term_row->tid);
								hontza_canal_rss_solr_clear_node_index($node,$nid);								
							}
                                                        $community_tags_uid=$uid;
							$community_tags_array=crm_exportar_tags_get_community_tags_array($node->nid,$term_row->tid,$community_tags_uid);
							$num=count($community_tags_array);
							if($num==0){
								$time=time();
								$date_field='date';
								db_query('INSERT INTO {community_tags} (nid,tid,uid,'.$date_field.') VALUES (%d,%d,%d,%d)', $node->nid,$term_row->tid,$community_tags_uid,$time);
							}
						}	
					}					
				}
			}
		}
	}
}
function crm_exportar_tags_get_community_tags_array($nid,$tid,$uid){
	$result=array();
	$where=array();
	$where[]='community_tags.nid='.$nid;
	$where[]='community_tags.tid='.$tid;
	$where[]='community_tags.uid='.$uid;
	$sql='SELECT * FROM {community_tags} WHERE '.implode(' AND ',$where);
	$res=db_query($sql);
	while($row=db_fetch_object($res)){
		$result[]=$row;
	}
	return $result;
}
function crm_exportar_tags_get_crm_boolean($row){
	$result=$row->value;
	if(!empty($row->booleano)){
		$result='('.$row->booleano.')';		
	}
	return $result;
}
function crm_exportar_tags_taxonomy_get_term_by_name_vid_row($term_name,$vid) {
  $res = db_query("SELECT t.tid, t.name FROM {term_data} t WHERE LOWER(t.name) = LOWER('%s') AND t.vid = %d",trim($term_name), $vid);
  //$result = array();
  while ($term = db_fetch_object($res)) {
    //$result[] = $term;
    return $term;  
  }
  //return $result;
  $my_result=new stdClass();
  return $my_result;
}
function crm_exportar_tags_get_item_title($title_in,&$fuente_corchete_title){
	$fuente_corchete_title='';
	if(crm_exportar_is_crm_exportar_tag()){ 
		$title=check_plain($title_in);
		$title=trim($title);
		$pos=strpos($title,'[');
		if($pos===FALSE){
			return $title_in;
		}else{
			if($pos==0){
				$pos_end=strpos($title,']');
				if($pos_end===FALSE){
					return $title_in;
				}else{
					$result=substr($title,$pos_end+1);
					$result=trim($result);
					$fuente_corchete_title=substr($title,0,$pos_end);
					$fuente_corchete_title=rtrim($fuente_corchete_title,']');
					$fuente_corchete_title=ltrim($fuente_corchete_title,'[');
					$fuente_corchete_title=trim($fuente_corchete_title);
					return $result;
				}
			}
			return $title_in;				
		}			
		return $result;
	}	
	return $title_in;	
}
function crm_exportar_tags_get_item_fuente_corchete_title($fuente_corchete_title){
	$output='';
	$output.="<sources>\n";	
	$output.=" <source>\n";
	$output.="  <title>".check_plain($fuente_corchete_title)."</title>\n";
	$source_url='';
	$output.="  <link>".check_plain($source_url)."</link>\n";
	$output.=" </source>\n";
	$output.="</sources>\n";
	return $output;	
}