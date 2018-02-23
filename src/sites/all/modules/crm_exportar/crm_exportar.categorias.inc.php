<?php
function crm_exportar_categorias_add_categorias_url($url,$form_state){
	$result=$url;
	$is_solr=1;
	$result.='/'.$is_solr;
	$categorias=get_categorias_string_by_values($form_state['values']);
	if(empty($categorias)){
		$categorias='0';	
	}	
	$result.='/'.$categorias;
	$limite_resumen=0;
	if(isset($form_state['values']['apply_limite_resumen']) && !empty($form_state['values']['apply_limite_resumen'])){
		if($form_state['values']['apply_limite_resumen']==1){			
			if(isset($form_state['values']['limite_resumen']) && !empty($form_state['values']['limite_resumen'])){
				$limite_resumen=$form_state['values']['limite_resumen'];
			}	
		}
	}
	$result.='/'.$limite_resumen;
	$tipos_fuente=hontza_crm_inc_get_tipos_fuente_string_by_values($form_state['values']);
	if(empty($tipos_fuente)){
		$tipos_fuente='0';	
	}	
	$result.='/'.$tipos_fuente;
	return $result;
}
function crm_exportar_categorias_get_categorias_grupo($row){
	$result=array();
	$categorias=arg(6);
	$categorias_array=explode(',',$categorias);
	$categorias_grupo_tid_array=hontza_crm_inc_get_categorias_grupo_tid_array($row);
	$result=array_intersect($categorias_array,$categorias_grupo_tid_array);
	return $result;
}
function crm_exportar_categorias_format_rss_item_categorias($node){
	$output="";
	$tid_array=hontza_solr_search_get_tid_array_by_field_item_canal_category_tid($node);
	if(!empty($tid_array)){
		$output.="<categories>\n";
		foreach($tid_array as $i=>$tid){
			$term=taxonomy_get_term($tid);
			if(isset($term->tid) && !empty($term->tid)){
				$output.=" <category>\n";
				$output.="  <id>".$term->tid."</id>\n";
				$output.="  <name>".check_plain($term->name)."</name>\n";
				$output.=" </category>\n";				
			}
		}
		$output.="</categories>\n";	
	}
	return $output;
}
function crm_exportar_categorias_get_group_array_by_categorias(){
	$result=array();
	$vid_array=array();
	$categorias=arg(6);
	$categorias_array=explode(',',$categorias);
	if(!empty($categorias_array)){
		foreach($categorias_array as $i=>$tid){
			$term=taxonomy_get_term($tid);
			if(isset($term->vid) && !empty($term->vid)){
				if(!in_array($term->vid,$vid_array)){
					$og_vocab_row=hontza_crm_inc_get_og_vocab_row($term->vid);
					if(isset($og_vocab_row->nid) && !empty($og_vocab_row->nid)){
						$grupo_node=node_load($og_vocab_row->nid);
						if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
							$result[]=$grupo_node;
							$vid_array[]=$term->vid;
						}												
					}				
				}	
			}
		}
	}
	return $result;
}
function crm_exportar_categorias_get_tags($node){
	$output="<tags>\n";	
	if(isset($node->taxonomy) && !empty($node->taxonomy)){
		foreach($node->taxonomy as $tid=>$term){
			if(hontza_in_etiquetas_vocabulary($term)){
				$output.=" <tag>\n";
				$output.="  <id>".$term->tid."</id>\n";
				$output.="  <name>".check_plain($term->name)."</name>\n";
				$output.=" </tag>\n";
			}	
		}
	}
	$output.="</tags>\n";
	return $output;
}
function crm_exportar_categorias_format_rss_item_tipos_fuente($node){
	$output="";
	$tid_array=red_solr_inc_get_content_field_item_source_tid_array($node->nid,$node->vid,0);
	if(!empty($tid_array)){
		$output.="<types_of_sources>\n";
		foreach($tid_array as $i=>$tid){
			$term=taxonomy_get_term($tid);
			if(isset($term->tid) && !empty($term->tid)){
				$output.=" <type_of_source>\n";
				$output.="  <id>".$term->tid."</id>\n";
				$output.="  <name>".check_plain($term->name)."</name>\n";
				$output.=" </type_of_source>\n";				
			}
		}
		$output.="</types_of_sources>\n";	
	}
	return $output;
}
function crm_exportar_categorias_add_crear_url_form_fields(&$form){

$form_field_tipos_fuente=array();
$is_import_rss=0;
$is_node_form=0;
$is_required=0;
hontza_solr_search_add_source_type_form_field($form_field_tipos_fuente,$is_import_rss,$is_node_form,$is_required);
//echo print_r($form_field_tipos_fuente,1);exit();
$form['file_buscar_fs']['taxonomia_fs']=$form_field_tipos_fuente['taxonomia_fs'];
$form['file_buscar_fs']['taxonomia_fs']['#title']=t('Types of Sources');
$form['file_buscar_fs']['taxonomia_fs']['#collapsible']=TRUE;


$form['file_buscar_fs']['categorias_fs']=array(
          '#type'=>'fieldset',
          '#title'=>t('Categories'),
          '#collapsible'=>TRUE,
        );
      $categories_fs=array();
      $is_crm_exportar_crear_url=1;
      $form['file_buscar_fs']['categorias_fs']=add_grupos_categorias_txek_fieldset($form['file_buscar_fs']['categorias_fs'],'',$is_crm_exportar_crear_url);

$form['file_buscar_fs']['limites_fs']=array(
          '#type'=>'fieldset',
          '#title'=>t('Limit'),
          '#collapsible'=>TRUE,
        );

$form['file_buscar_fs']['limites_fs']['apply_limite_resumen']=array(
              '#type'=>'checkbox',
              '#prefix'=>'<div style="float:left;clear:both;width:100%;"><div style="float:left;width:3%;margin-top:5px;">',
              '#suffix'=>'</div>',            
            );                       
    $form['file_buscar_fs']['limites_fs']['limite_resumen']=array(
              '#type'=>'textfield',
              //'#title'=>t('Limit abstract length to'),
              '#title'=>t('Limit teaser length to'),
              '#prefix'=>'<div style="float:left;padding-left:10px;">',
              '#suffix'=>'</div>',
              //'#attributes'=>$text_attributes,
            );
}
function crm_exportar_categorias_get_node_teaser($node,$description){
	//$teaser=hontza_content_resumen($node);
    $teaser=node_teaser(strip_tags($description));
    $limite_resumen=arg(7);
    if(!empty($limite_resumen)){
    	$teaser=substr($teaser,0,$limite_resumen).' ...';
    }
    return $teaser;
}
function crm_exportar_categorias_get_item_fuente($node){
	$output='';
	$canal=hontza_crm_get_item_canal($node);
	$url='';
	$is_hound_canal=hontza_is_hound_canal($canal->nid,$canal);
	if($is_hound_canal){
		$url='';
	}else{
		$url=hontza_get_enlace_fuente_del_canal_view_html($canal,1);
		$url=strip_tags($url);
	}
	$fuente='';		
	$output.="<sources>\n";	
	if(isset($node->field_item_source_url) && !empty(field_item_source_url)){
		foreach($node->field_item_source_url as $i=>$row){
			$output.=" <source>\n";
			$source_title=red_despacho_decode_source_title_url_value($node->field_item_source_title[$i]['value']);	
			$output.="  <title>".check_plain($source_title)."</title>\n";
			$source_url=red_despacho_decode_source_title_url_value($row['value']);
			$output.="  <link>".check_plain($source_url)."</link>\n";
			$output.=" </source>\n";
		}	
	}	
	$output.="</sources>\n";	
	return $output;
}
function crm_exportar_categorias_get_tipos_fuente_array(){
	$result=array();
	$tipos_fuente=arg(8);
	if(!empty($tipos_fuente)){
		$result=explode(',',$tipos_fuente);
	}
	return $result;
}
function crm_exportar_categorias_access_denied(){
  if(crm_exportar_is_crm_exportar_categorias()){
    drupal_access_denied();
    exit();
  }
}  