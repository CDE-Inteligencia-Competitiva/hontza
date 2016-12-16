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
	$vid=3;
	if(!empty($result)){
		if(isset($result->nid_tag_array) && !empty($result->nid_tag_array)){
			$nid_tag_array=$result->nid_tag_array;
			foreach($nid_tag_array as $i=>$my_row){
				$nid=$my_row->nid;
				$row=$my_row->row;
				if(isset($row->name) && !empty($row->name)){
					$term_name='CRM:'.$row->name;
					$term_row=red_solr_inc_taxonomy_get_term_by_name_vid_row($term_name,$vid);
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
						}	
					}					
				}
			}
		}
	}
}