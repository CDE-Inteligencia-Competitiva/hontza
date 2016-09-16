<?php
//gemini
//OHARRA::::php fitxategi hau geminik sortu du
function my_set_contenidos_del_grupo_filter_form(&$form,&$form_state,$form_id){
	//if(strcmp($form['#id'],'views-exposed-form-contenidos-del-grupo-page-4')==0){
	$pos=strpos($form['#id'],'views-exposed-form-contenidos-del-grupo-page-');
	if($pos!==FALSE){										
		$my_array=array('wiki','debate','canal_busqueda','canal_de_yql','rss','opml','item');
		if(!empty($my_array)){
			foreach($my_array as $i=>$f){
				$form['type']['#options'][$f]=t(my_get_label_type($f));		
			}
		}			 			
	}
}  			  
function my_replace_contenidos_del_grupo_table($field,$content,$row_in){
	$result=$content;
	
	switch($field){
		case 'type':
			$result=my_replace_contenidos_del_grupo_td_type($result,$row_in);
			break;
		default:
			break;	
	}
	
	return $result;
}
function my_replace_contenidos_del_grupo_td_type($content,$row){
	$result=$content;
	$type_row=my_node_type_load(array('name'=>$content));
	if(!empty($type_row) && isset($type_row->type) && !empty($type_row->type)){
		//echo print_r($row,1);
		if(in_array($row['field_fuente_canal_value'],array('RSS','OPML'))){
			$label=my_get_label_type(strtolower($row['field_fuente_canal_value']),1);
		}else{		
			$label=my_get_label_type($type_row->type,1);
		}
		//		
		if(!empty($label)){
			return $label;
		}
	}
	return $result; 	
}
function my_get_label_type($f,$with_empty=0){
	$my_array=array();
	//
	$my_array['wiki']='Página Wiki';
	$my_array['debate']='Hilo de Debate';
	$my_array['canal_busqueda']='Carpeta Dinámica';
	$my_array['canal_de_yql']='RSS Filter';
	$my_array['rss']='RSS';
	$my_array['opml']='OPML';
	$my_array['item']='Noticia';
	//
	if(isset($my_array[$f])){
            //return utf8_encode($my_array[$f]);
            return $my_array[$f];
	}
	if(!$with_empty){
		return 'my_get_label_type, no existe el indice';	
	}
	return '';
}
function my_node_type_load($param_array){		
	if(!empty($param_array)){
		$where=array();				
		
		foreach($param_array as $f=>$v){
			$where[]="nt.".$f."='".$v."'";
		}
		
		$sql="SELECT nt.* FROM node_type nt WHERE ".implode(" AND ",$where);
		$result = db_query($sql);
	 
		  while ($row = db_fetch_object($result)) {		
			return $row;
		  }
	}	
		
	$my_result=(object) array();
			
	return $my_result;	
}
function my_besteak_pre_execute(&$view){
        if(strcmp($view->name,'contenidos_del_grupo')==0){
            my_contenidos_del_grupo_pre_execute($view);
        }else if(strcmp($view->name,'canal_busqueda')==0){
            my_carpeta_dinamica_pre_execute($view);
	}else if(strcmp($view->name,'og_area_trabajo')==0){
            if(is_current_display($view->current_display,'page') || hontza_is_og_area_trabajo_block_display_grupo_shared($view->current_display)){
                my_area_trabajo_pre_execute($view);
            }
        }else if(strcmp($view->name,'og_area_debate_by_node')==0){
             if(is_current_display($view->current_display,'page')){
                my_area_debate_by_node_pre_execute($view);
            }
        }/*else if(strcmp($view->name,'gestion_items')==0){
            //print $view->build_info['query'].'<BR>';
            //my_gestion_items_pre_execute($view);
        }*/
        else if($view->name=='taxonomy_term'){
            my_taxonomy_term_pre_execute($view);
        }else if($view->name=='gestion_servicios'){
            my_gestion_servicios_pre_execute($view);
        }else if($view->name=='og_home_ultimasnoticias_dash'){
            //gemini_lento: a ver si modificando la consulta va más rapido porque tarda 7 segundos
            my_og_home_ultimasnoticias_dash_pre_execute($view);
            //
        }else if($view->name=='og_canales_dash'){
            //gemini-2013
            //my_og_canales_dash_pre_execute($view);
            hontza_og_canales_dash_pre_execute($view); 
        }
        //gemini-2013
        else if($view->name=='og_vigilancia_lo_mas_valorado'){
             my_og_vigilancia_lo_mas_valorado($view);
        }else if($view->name=='og_vigilancia_mascomentadas'){
             my_og_vigilancia_mascomentadas($view);   
        }else if($view->name=='gestion_grupos_propios'){
            hontza_gestion_grupos_propios($view);
        }else if($view->name=='og_vigilancia_validados'){
            hontza_og_vigilancia_validados_pre_execute($view);
        }else if($view->name=='og_canales'){
            hontza_og_canales_pre_execute($view); 
        }else if($view->name=='og_vigilancia_rechazados'){
            hontza_og_vigilancia_rechazados_pre_execute($view);
        }else if(hontza_is_mis_contenidos_canales()){
            hontza_mis_contenidos_canales_pre_execute($view);
        }else if($view->name=='gestion_canales'){
            //print $view->build_info['query'];exit();
        }else if($view->name=='og_home_noticiasvalidadas_dash'){
            if(hontza_is_view_block($view->current_display)){
                hontza_og_home_noticiasvalidadas_dash_block_pre_execute($view);
            }
        }else if($view->name=='og_canales_busqueda'){
            if(hontza_is_view_block($view->current_display)){
                hontza_og_canales_busqueda_block_pre_execute($view);
            }
        }else if($view->name=='og_home_areadebate'){
            if(hontza_is_view_block($view->current_display)){
                hontza_og_home_areadebate_block_pre_execute($view);
            }    
        }else if($view->name=='og_home_areadetrabajo'){
            if(hontza_is_view_block($view->current_display)){
                hontza_og_home_areadetrabajo_block_pre_execute($view);
            }    
        }else if($view->name=='og_canal_aportaciones_usuarios'){
            //gemini-2014
            hontza_og_canal_aportaciones_usuarios($view);
        }else if($view->name=='og_categorias_fuentes'){
            if(hontza_is_view_block($view->current_display)){
                red_funciones_og_categorias_fuentes_block_pre_execute($view);
            } 
        }else if($view->name=='home_publica'){
           hontza_empty_view_pre_execute($view);
        }/*else{
           print $view->build_info['query'];exit();
        }*/      
}
function my_contenidos_del_grupo_pre_execute(&$view){
	if(isset($_REQUEST['type']) && !empty($_REQUEST['type']) && in_array($_REQUEST['type'],array('rss','opml'))){
		$type=$_REQUEST['type'];
$where=array();
$where[]="1";

$my_grupo=og_get_group_context();
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")"; 
}

$where[]="(node.type in ('canal_de_yql'))";
$label=my_get_label_type($type,1);
$where[]="(node_data_field_fuente_canal.field_fuente_canal_value in ('".$label."'))"; 
   
   
$sql="SELECT node.nid AS nid, 
node.title AS node_title, 
node.type AS node_type, 
og_ancestry.nid AS og_ancestry_nid, 
node_data_field_fuente_canal.field_fuente_canal_value AS 
node_data_field_fuente_canal_field_fuente_canal_value, 
node.vid AS node_vid, 
node.created AS node_created 
FROM {node} node 
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
LEFT JOIN {content_field_fuente_canal} node_data_field_fuente_canal ON node.vid = node_data_field_fuente_canal.vid
WHERE ".implode(" AND ",$where)."
ORDER BY node_created DESC";   
   
   
		/*print $sql.'<BR>';
		print $view->build_info['query'];exit();*/
		$view->build_info['query']=$sql; 	
		$view->build_info['count_query']=$sql;
		
	}	
}
function my_add_menu_node_link($param){
	global $user;
        $result=$param;
        //gemini-2014
        $result_shared=red_funciones_set_node_link_servidor_alerta($param);
        if(!empty($result_shared)){
            return $result_shared;
        }
        //
        $result=unset_my_dashoard_link($param);
        if(arg(0)=='user'){
            $arg1=arg(1);
            if(empty($arg1) && empty($user->uid)){
                $result=translate_user_register_menu($param,2);
                return $result;
            }
            if($arg1=='register' && empty($user->uid)){
                $result=translate_user_register_menu($param,1);
                return $result;
            }
            if($arg1=='password' && empty($user->uid)){
                $result=translate_user_register_menu($param,3);
                return $result;
            }            
        }
        //
        if(arg(0)=='node' && is_numeric(arg(1))){
		$nid=arg(1);
		$node=node_load($nid);
		if(!empty($node) && isset($node->nid) && !empty($node->nid)){
			//print $node->type;
			if(in_array($node->type,array('canal_de_yql','canal_de_supercanal'))){
                            if(repase_access_canal($node,1)){
                                //intelsat-2015
				$result.='<li>'.l(t('List of News'),'canales/'.$nid.'/ultimas').'</li>';
                                if(hontza_is_sareko_id_red()){
                                    //intelsat-2015
                                    //if(!is_show_modificar_vocab()){
                                    if(is_show_modificar_vocab()){
                                        $result.='<li>'.l(t('Export RSS channel'),'red_exportar_rss/canal/'.$nid,array('attributes'=>array('target'=>'_blank'))).'</li>';
                                    }                                            
                                }
                                if(hontza_is_congelar_canal_sareko_id() && hontza_activar_actualizacion_canal_access($node)){
                                    $label_activar='';
                                    if(hontza_is_canal_congelado($node)){
                                        $label_activar=t('Activated channel');
                                    }else{
                                        $label_activar=t('Deactivate channel');
                                    }
                                    $result.='<li>'.l($label_activar,'activar_actualizacion_canal/'.$nid,array('query'=>drupal_get_destination(),'attributes'=>array('id'=>'id_a_activar_canal_'.$node->nid))).'</li>';
                                    hontza_add_activar_actualizacion_canal_js($node->nid);
                                }
                                //intelsat-2016
                                if(hontza_is_hound_text_input() && module_exists('hound')){
                                    $result.=hound_add_reset_hound_menu_node_link($nid);
                                }
                            }else{    
                                return '';                                
                            }
                        }else if(in_array($node->type,array('item','noticia'))){
                            add_js_volver_noticia();                                
                            //$result.='<li>'.l(t('OpenCalais'),'node/'.$node->nid.'/opencalais').'</li>';
                            $result.='<li>'.l(t('Return'),'my_volver',array('attributes'=>array('id'=>'my_volver_js'))).'</li>';
                        }else if(hontza_is_rama_estrategia($node->type)){
                            $result=hontza_unset_rama_estrategia_menu_links($node->type,$result);
                        }else if($node->type=='wiki'){
                            //intelsat-2016
                            $result=red_copiar_get_node_update_link($node,$result);
                        }
		}
                $result=red_funciones_unset_de_la_botonera_gris_del_node($result);
                if(hontza_is_sareko_id_red(0,1)){
                    if(!is_show_modificar_vocab()){
                        $result.='<li>'.l(t('Export RSS channel'),'red_exportar_rss/canal/'.$nid,array('attributes'=>array('target'=>'_blank'))).'</li>';
                    }    
                }
	}else if(is_mi_perfil(0) && is_user_invitado()){
           $result=unset_invitado_user_menu_links($result);
        }else if(is_mi_perfil(0)){
           $my_array=array();
           $my_array['dashboard']='My dashboard';
           $result=unset_invitado_user_menu_links($result,$my_array);
        //intelsat-2015   
        }else if(panel_admin_is_type_of_sources() || panel_admin_is_user_create()){
           return ''; 
        }
        //
        //quitar_mi_perfil_notifications_and_messages($result);
	
	return $result;
}
function is_normal_user_profile($profile){
	global $user;
	
	if(isset($profile['Empresa']) && isset($profile['Datos personales'])){
		return 1;
	}		
	return 0;
}
function my_show_other_user_profile(){
	global $user;
	$result=array();
	$result['with_info']=0;
	if(arg(0)=='user' && is_numeric(arg(1))){
		$uid=arg(1);
		if(!empty($user->uid)){
			$other_user=user_load($uid);
			if(!empty($other_user) && isset($other_user->uid) && is_numeric($other_user->uid)){
				//echo print_r($other_user,1);
				$result['with_info']=1;
				$result['profile_empresa']=$other_user->profile_empresa;
				$result['profile_nombre']=$other_user->profile_nombre;
				$result['profile_apellidos']=$other_user->profile_apellidos;
				$result['member_for']=format_interval(time() - $other_user->created);
				$result['groups_li']='';
				if(isset($other_user->og_groups)){
                                    $is_link=0;
                                    if($user->uid==$uid){
                                        $is_link=1;
                                    }
					$result['groups_li']=my_get_user_groups_li($other_user->og_groups,$is_link);
				}					
			}
		}
	}	
	return $result;
}
function my_get_user_groups_li($og_groups,$is_link=0){
	if(!empty($og_groups)){
		$result=array();
		foreach($og_groups as $id=>$gr){
                    if($is_link){
                        $result[]='<li>'.l($gr['title'],$url).'</li>';                        
                    }else{    
                        $result[]='<li>'.$gr['title'].'</li>';
                    }    
		}
		return implode('',$result);
	}
	return '';
}
function add_js_li_usuarios(){
        
	if(is_usuarios_todos() || my_is_usuarios_empresa() || is_og_users()){            
		$js='
			$(document).ready(function()
			{			
			  $("#id_a_usuarios_todos").parent().attr("class","leaf active-trail");
			});';
			
			drupal_add_js($js,'inline');
	}	
}
function is_usuarios_todos(){
	if(arg(0)=='usuarios' && (strcmp(arg(1),'todos')==0)){
		return 1;
	}
        /*if(arg(0)=='usuarios_estadisticas' && (strcmp(arg(1),'todos')==0)){
		return 1;
	}*/
        if(is_usuarios_estadisticas()){
            return 1;
        }
	return 0;	
}
function my_hontza_init(){
        //gemini-2014
        if(function_exists('red_set_session_is_iframe')){
            red_set_session_is_iframe();
        }else{
            hontza_set_session_is_iframe();
        }
        //add_js_li_usuarios();
	add_js_ficha_fuente();
	add_js_vigilancia();
	add_js_area_trabajo();
	add_js_debate();
	//save_path_term_delete();
	add_js_busqueda_simple();
	add_js_servicios();
	add_js_idea();
	add_js_oportunidad();
	add_js_proyecto();
	add_js_estrategia();
	add_js_despliegue();
	add_js_decision();
	add_js_informacion();
	add_js_chat();
        //gemini-2014
        hontza_add_js_mi_grupo();
        //my_add_noticias_publicas_select_all_js();
        add_js_borrar_items_canal_no_existe();
        //repase_organic_group_access();
        repase_access();
        //gemini_lento: se ha comentado porque tarda 1.5 seg
        //reset_url_alias_language();
        //gemini-2013
        hontza_repase_vigencia_maxima_pass();
        if(module_exists('calendario')){
            calendario_add_ajax_navegacion();
        }
        hontza_add_destacar_ajax();
        hontza_add_noticia_usuario_destacar_ajax();
        if(module_exists('boletin_report')){
            boletin_report_add_update_ajax();
        }
        hontza_login_red_alerta_add_js();
        hontza_redirect_by_grupo();
        //intelsat-2014
        hontza_solr_funciones_add_bookmark_ajax();
        //
        //intelsa-2015
        //alerta_inc_add_js_select_alerta();
        //intelsat-2016
        hontza_crm_inc_change_link_type_add_js();
}
function add_js_ficha_fuente(){
	if(is_ficha_fuente()){
            	my_add_active_trail_js('id_a_ficha_fuente');		
	}
}
function add_js_vigilancia(){
	if(is_vigilancia()){
		my_add_active_trail_js('id_a_vigilancia');		
	}
}
function add_js_area_trabajo(){
	if(is_area_trabajo()){
		my_add_active_trail_js('id_a_area_trabajo');
                add_busqueda_simple_wiki_js();
	}
}
function add_js_debate(){
	if(is_area_debate()){
		my_add_active_trail_js('id_a_area_debate');
                 add_busqueda_simple_debate_js();
	}
}
function is_ficha_fuente(){
    	$node=my_get_node();
	if(!empty($node) && isset($node->nid) && !empty($node->nid)){
		if(in_array($node->type,array('fuentedapper','supercanal','fuentehtml'))){
                    return 1;
		}
	}
	//
	if(strcmp(arg(0),'crear')==0 && in_array(arg(1),array('fuente-supercanal','fuente-dapper'))){
		return 1;
	}
	//
	return 0;
}
function my_get_node(){
	$nid='';
	if(arg(0)=='node' && is_numeric(arg(1))){
		$nid=arg(1);
	}else if(arg(0)=='comment'){	 
		if(arg(1)=='reply' &&  is_numeric(arg(2))){
			$nid=arg(2);
		}else if(arg(1)=='edit' &&  is_numeric(arg(2))){
			$cid=arg(2);
			if(!empty($cid)){	
				$comment=_comment_load($cid);
				if(!empty($comment) && isset($comment->nid) && !empty($comment->nid)){	
					$nid=$comment->nid;
				}
			}
		}
	}else if(arg(0)=='canales' && is_numeric(arg(1))){
            //gemini-2013
            $nid=arg(1);
        }else if(arg(0)=='red_exportar_rss' && arg(1)=='canal' && is_numeric(arg(2))){
            //gemini-2014
            $nid=arg(2);
        }
	//
        if(!empty($nid)){	
		$node=node_load($nid);
		if(!empty($node) && isset($node->nid) && !empty($node->nid)){			
			return $node;
		}
	}
	$my_result=(object) array();
			
	return $my_result;	
}
function my_add_id_to_link(&$link){
    /*if(strcmp($link['link_path'],'usuarios_captacion_informacion/todos')==0){
        echo print_r($link,1);
    }*/

    /*
    if(user_access('root')){
        echo print_r($link,1);
    }*/
    
    	$s='';
	//if(strcmp($link['link_path'],'usuarios/todos')==0){
        //gemini-2014
        //if(in_array($link['link_path'],array('usuarios/todos','usuarios_estadisticas/todos','usuarios_captacion_informacion/todos','mi-grupo'))){
        if(in_array($link['link_path'],array('mi-grupo'))){
		//if(is_usuarios_todos() || my_is_usuarios_empresa() || is_og_users()){
                //gemini-2014
                if(is_usuarios_todos() || my_is_usuarios_empresa() || is_og_users() || is_usuarios_submenu() || hontza_is_mi_grupo()){
			//$s='id_a_usuarios_todos';
                        $s='id_a_mi_grupo';
		}
	}
	//
	//print $link['link_path'].'<BR>';
	if(strcmp($link['link_path'],'fuentes-pipes/todas')==0){
                if(is_ficha_fuente()){
                	$s='id_a_ficha_fuente';
		}
	}
	//	
	if(strcmp($link['link_path'],'vigilancia/pendientes')==0){	
		//if(is_vigilancia()){
                if(is_menutop_vigilancia()){
                    $s='id_a_vigilancia';
		}
	}
	//	
	if(strcmp($link['link_path'],'area-trabajo')==0){	
		if(is_area_trabajo()){
			$s='id_a_area_trabajo';
		}
	}
	//	
	if(strcmp($link['link_path'],'area-debate')==0){	
		if(is_area_debate()){
			$s='id_a_area_debate';
		}
	}
	//
	if(strcmp($link['link_path'],'servicios')==0){	
		if(is_term_view_orig('Categoría Servicios')){
			$s='id_a_servicios';
		}
	}
	//
	if(strcmp($link['link_path'],'ideas')==0){
                if(is_idea() || is_oportunidad() || is_proyecto()){
			$s='id_a_idea';
		}
	}
	//gemini-2014
        //if(strcmp($link['link_path'],'estrategias')==0){
	if(strcmp($link['link_path'],'estrategias/arbol_estrategico')==0){	
		if(is_estrategia() || is_despliegue() || is_decision() || is_informacion()){
                    $s='id_a_estrategia';
		}
	}
	//
	if(strcmp($link['link_path'],'interactiva')==0){	
		if(is_chat()){
			$s='id_a_interactiva';
		}
	}
        //gemini-2014
        if(in_array($link['link_path'],array('alerta_user/inicio'))){
            	if(is_alerta_user()){
                    $s='id_a_alertas';
		}
	}
        //					
	/*if(user_access('root')){
            print 's='.$s.'<BR>';
            print $link['link_path'].'<BR>';
            print '-----------------<BR>';
        }*/
        //
	if(!empty($s)){
		if(!isset($link['localized_options']['attributes'])){
			$link['localized_options']['attributes']=array();
		}
		$link['localized_options']['attributes']['id']=$s;
		$link['localized_options']['attributes']['class']='active';
	}
        //$link=repase_link_by_lang($link);
}
function my_add_active_trail_js($a_id){
	//print $a_id.'<BR>';
        //if(is_usuarios_todos()){
		$js='
			$(document).ready(function()
			{			
			  $("#'.$a_id.'").parent().attr("class","leaf active-trail");
			});';
			
			drupal_add_js($js,'inline');
	//}	
}
function is_ficha_node($konp='item'){
	$node=my_get_node();
	//print $node->type.'<BR>';
	if(!empty($node) && isset($node->nid) && !empty($node->nid)){
		//if(in_array($node->type,array('item'))){
		if(strcmp($node->type,$konp)==0){
			//print $node->type.'<BR>';
			return 1;
		}
	}else if(arg(0)=='node' && arg(1)=='add' && strcmp(arg(2),$konp)==0){
		return 1;
	}else if(arg(0)=='node' && arg(1)=='add'){
		$temp_konp=str_replace("_","-",$konp);
                if(strcmp(arg(2),$temp_konp)==0){
                    return 1;
                }
	}
	return 0;
}
function is_vigilancia(){
    if(hontza_in_pantallas_enlace_debate()){
        return 0;
    }else if(hontza_in_pantallas_enlace_wiki()){
        return 0;
    }else if(idea_in_pantallas_enlace()){
        return 0;
    }
    //
    if(hontza_is_origenes_debate() || hontza_is_origenes_wiki() || idea_is_origenes()){
        return 1;
    }
        //gemini-2014
        if(red_is_vigilancia_en_intercambiar_recursos()){
           return 1; 
        }
        //
	if(is_ficha_node() || is_ficha_node('noticia') || is_ficha_node('canal_de_supercanal') || is_ficha_node('canal_de_yql')){
		return 1;
	}
	//	
	if(in_array(arg(0),array('vigilancia','canales','busqueda','canal-busqueda','canal-usuarios','importar_pagina_html','importar_fuente_html'))){
		return 1;
	}else if(strcmp(arg(0),'taxonomy')==0 && strcmp(arg(1),'term')==0 && is_numeric(arg(2))){
		if(!is_term_view_orig('Categoría Servicios')){
			return 1;
		}
	}else if(strcmp(arg(0),'crear')==0 && in_array(arg(1),array('canal-supercanal','canal-yql'))){
		return 1;
	}else if(strcmp(arg(0),'tagadelic')==0 && strcmp(arg(1),'chunk')==0 && arg(2)==3){
		return 1;
	}else if(strcmp(arg(0),'node')==0 && arg(1)==89 && strcmp(arg(2),'og')==0 && strcmp(arg(3),'vocab')==0 && strcmp(arg(4),'terms')==0 && arg(5)==7){
		return 1;
	}else if(is_term_delete($my_url)){
		return 1;
	}else if(is_term_edit()){
		return 1;
	}else if(is_listado_terminos_canales_por_categorias()){
            return 1;
        }
    //intelsat-2014    
    if(hontza_solr_is_busqueda_avanzada_pantalla()){
        return 1;
    }
    if(hontza_solr_is_resultados_pantalla()){
        return 1;
    }
    if(hontza_solr_funciones_in_pantalla_resultados_guardados()){
        return 1;
    }
    //intelsat-2015
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
        return 1;
    }
    if(hontza_is_canal_usuarios('bookmarks')){
        return 1;
    }
    if(hontza_canal_rss_is_red_exportar_rss_enviar_mail_canales_rss()){
        return 1;
    }
    if(hontza_canal_rss_is_publico_activado()){
        //return publico_is_pantalla_publico('vigilancia');
        return publico_vigilancia_is_vigilancia_ultimos();
    }
    //
    //intelsat-2015
    if(is_ficha_node('canal_usuario')){
        return 1;
    }
    if(red_despacho_is_vigilancia()){
        return 1;
    }
    //intelsat-2016
    if(hontza_canal_json_is_pantalla()){
        return 1;
    }
    if(red_solr_inc_is_index_remaining_pantalla()){
        return 1;
    } 
    //
	return 0;
}
function is_area_trabajo(){
	if(is_ficha_node('wiki')){
            if(hontza_is_origenes_wiki()){
                return 0;
            }else{
            	return 1;
            }    
	}
	//
	if(strcmp(arg(0),'no_existe_enlace_origen')==0){
            //intelsat-2016
            $param=arg(1);
            if(!empty($param) && in_array($param,array('item'))){
                return 0;
            }
            return 1;
	}
        //
        if(strcmp(arg(0),'area-trabajo')==0){
            return 1;
        }
        //
        if(is_ficha_node('my_report')){
		return 1;
	}
        //
        if(strcmp(arg(0),'boletin_report')==0){
            if(strcmp(arg(1),'report_view_list')==0){
                return 1;
            }    
        }
        //
        if(hontza_in_pantallas_enlace_wiki()){
            return 1;
        }
        //intelsat-2015
        if(red_copiar_is_enviar_mail()){
            return 1;
        }
	return 0;
}
function is_area_debate(){
	if(is_ficha_node('debate')){
            if(hontza_is_origenes_debate()){
                return 0;
            }else{
            	return 1;
            }  
	}
	//
	if(strcmp(arg(0),'no_existe_enlace_origen_debate')==0){
		return 1;
	}
        //
        if(strcmp(arg(0),'area-debate')==0){
            return 1;
        }
        if(hontza_in_pantallas_enlace_debate()){
            return 1;
        }
	return 0;
}
function my_is_empresa($konp){
	//print $konp;
	$my_list=my_user_empresa_list($konp);
	if(count($my_list)>0){
		return 1;
	}
	return 0;
}
function my_user_empresa_list($value=''){
	$where=array();	
	$where[]="f.name='profile_empresa'";
	if(!empty($value)){
		$where[]="v.value='".$value."'";
	}
	$sql="SELECT v.* FROM profile_values v LEFT JOIN profile_fields f ON v.fid=f.fid WHERE ".implode(" AND ",$where);
	$result = db_query($sql);
	//
	$row_list=array(); 
	while ($row = db_fetch_object($result)) {		
		$row_list[]=$row;
	}
	return $row_list;
}
function my_is_usuarios_empresa(){
	if(arg(0)=='usuarios' && my_is_empresa(arg(1))){
		return 1;
	}
	return 0;	
}
function is_mis_contenidos($konp=''){
	if(strcmp(arg(0),'mis-contenidos')==0){
            if(empty($konp)){
                return 1;
            }else{
                $type=arg(1);
                if(!empty($type)){
                    if($type==$konp){
                        return 1;
                    }
                }
            }    
	}
	return 0;
}
function get_mis_contenidos_active_trail($konp){
	if(is_mis_contenidos()){		
		if(strcmp(arg(1),$konp)==0){
			return ' active-trail';
		}		
	}
	return '';
}
function my_get_menutop($menutop,$is_fix_in=0,$is_despegable=0){
        if(hontza_is_tag_node_pantalla()){
            return '';
        }
        $my_group=og_get_group_context();
        //intelsat-2015        
        $is_show_top=0;
        if(hontza_canal_rss_is_visualizador_activado()){
            $is_show_top=publico_is_show_top();
            /*if(hontza_is_user_anonimo()){
                return '';
            }*/
        }
        if(!$is_show_top){
            if(!(isset($my_group->nid) && !empty($my_group->nid))){
                //gemini-2014
                //intelsat-2015
                //if(!red_funciones_is_perfil_usuario()){
                if(!$is_despegable){
                    if(!hontza_canal_rss_is_publico_activado()){
                        return '';
                    }else{
                        if(!hontza_is_user_anonimo() && !hontza_canal_rss_visualizador_is_red_alerta()){
                            return '';
                        }
                    }
                }            
            }
        }
	$is_fix=$is_fix_in;
        $is_fix=0;
        //
        //$sel=0;
        $sel='none';
        //gemini-2014
        //if(empty($menutop) || $is_fix){
        //if(empty($menutop) || $is_fix){
        if(!is_user_invitado()){
                //if(strcmp(arg(0),'tagadelic')==0 && strcmp(arg(1),'chunk')==0 && arg(2)==3){
		if(is_menutop_vigilancia()){	
			$sel='vigilancia/pendientes';
                        /*if(!hontza_is_show_vigilancia_pendientes_tab()){
                            $sel='vigilancia/validados';
                        }*/                        
		/*}else if(is_og_users()){
                    	//$sel='usuarios/todos';*/
                        //intelsat-2015
                        if(hontza_canal_rss_is_publico_activado()){
                            $sel=publico_vigilancia_get_selected_active($sel);
                        }
		}else if(is_menutop_servicios()){
			$sel='servicios';
		}else if(is_area_debate()){
			$sel='area-debate';
		}else if(is_area_trabajo()){
			$sel='area-trabajo';
		}else if(is_fuentes()){
			$sel='fuentes-pipes/todas';
		}else if(is_idea() || is_oportunidad() || is_proyecto()){
			$sel='ideas';
		}else if(is_estrategia() || is_despliegue() || is_decision() || is_informacion()){
			$sel='estrategias/arbol_estrategico';
		}else if(is_chat()){
			$sel='interactiva';
		//gemini-2014                        
                }else if(hontza_is_mi_grupo()){
                    $sel='mi-grupo';
                }else if(is_alerta_user()){
                    $sel='alerta_user/inicio';
                }else if(is_dashboard()){
                    $sel='dashboard';
                }else{
                    //intelsat-2015
                    $sel=hontza_canal_rss_get_menutop_selected();
                }
                //
	}else if(is_user_invitado()){
            return get_menutop_invitado();
        }
        if(red_funciones_is_perfil_usuario()){
            return '';
        }
                
        //if(!empty($sel) || $is_fix){
        //if(!empty($sel) || is_usuarios_menu() || $is_fix || hontza_is_mis_alertas_boletines() || is_mis_contenidos() || hontza_is_mis_grupos()){
        if(!empty($sel) || $is_fix || hontza_is_pantalla_top_menu()){
            $is_empty_sel=0;
            if(empty($sel)){
                 $is_empty_sel=1;
            }
            if(is_usuarios_menu()){
                $sel='usuarios_captacion_informacion/todos';
            }            
            	//gemini-2014
                //$result=menu_primary_links();
                $result=red_funciones_define_menu_primary_links();
                //intelsat-2015
                $result=hontza_canal_rss_get_usuario_basico_menu_primary_links($result);
                $result=red_menu_primary_links_strtoupper($result);
                if(hontza_canal_rss_is_visualizador_activado()){
                    $result=visualizador_prepare_menu_menu_primary_links($result);
                }
                $result=menu_by_lang($result);
                //intelsat-2016
                $result=red_deportes_get_menu_primary_links($result);
                //
                //intelsat-2015
                $is_sf_js_enabled=1;
		if(!empty($result)){
			$my_array=array();
			$atzizkia='';			
			$kont=0;
			$num=count($result);
			foreach($result as $key=>$row){
                            //intelsat-2015
                            $is_publico_vigilancia_ultimos_menu_row=hontza_canal_rss_is_publico_vigilancia_ultimos_menu_row($row);                            
				$s='';
				$atzizkia='';
                                //intelsat-2015
                                $top_item_activado=0;
                                if(strcmp($row['href'],$sel)==0){
                                        //intelsat-2015
                                        $top_item_activado=1;
                                        $s=' active-trail';
                                }else{
                                    if(hontza_canal_rss_is_visualizador_activado()){
                                        if(visualizador_is_top_item_activado($row['href'],$sel)){
                                            //intelsat-2015
                                            $top_item_activado=1;
                                            $s=' active-trail';
                                        }
                                    }
                                }
                                //
				if($kont<0){
					$atzizkia=' first';
				}else if($kont==($num-1)){	
					$atzizkia=' last';
				}else{
					$atzizkia='';
				}
				//
                                //print $row['href'].'<BR>';
                                //print $row['title'].'<BR>';
                                $style_publico='';
                                $style_li='';
                                $attributes=array();
                                //intelsat-2015
                                //if(hontza_canal_rss_is_visualizador_activado()){
                                if(hontza_canal_rss_is_visualizador_activado() || red_is_menu_sin_flechas()){
                                    if(red_visualizador_is_menu_sin_flechas()){
                                        $menu_info_css=publico_get_menu_info_css($top_item_activado);
                                        $style_publico=$menu_info_css['style_publico'];
                                        $style_li=$menu_info_css['style_li'];
                                        $attributes['style']=$style_publico;
                                        if(visualizador_is_top_item_activado($row['href'],'publico/vigilancia/ultimos')){
                                            $attributes['id']='id_a_latest_news';
                                        }
                                    }
                                }
                                //intelsat-2015
                                $categorias_menu='';
                                if($is_publico_vigilancia_ultimos_menu_row){
                                    $is_sf_js_enabled=0;
                                    if(hontza_canal_rss_is_publico_activado()){
                                        $categorias_menu=publico_vigilancia_get_categorias_menu();
                                    }            
                                }
                                $my_array[$kont]='<li class="leaf'.$s.$atzizkia.' li_latest_news"'.$style_li.'>'.l($row['title'],$row['href'],array('attributes'=>$attributes)).$categorias_menu.'</li>';
                                $kont++;                                
			}
                        //return implode('',$my_array);
                        $html=implode('',$my_array);
                        if(is_usuarios_menu() && $is_empty_sel){
                            //print $is_sf_js_enabled.'<br>';exit();
                            return my_menutop_usuarios_menu($html,$is_sf_js_enabled);
                        }
                        return $html;
		}		
	}
        $menutop=red_funciones_set_menutop_active_trail($menutop);        
        return $menutop;
}
function my_get_nombredelgrupo($param){
	global $user;
	if(!isset($user->uid) || empty($user->uid)){
		return $param;
	}
	if(empty($param)){		
		$og=og_get_group_context();
		
		if($og){
			return $og->title;
		}
	}
	return $param;
}
function my_set_carpeta_dinamica_form_filter(&$form,&$form_state, $form_id){
        
        $is_busqueda_simple=my_get_request_value('is_busqueda_simple',1);
	if(is_show_borrar_carpeta_dinamica($my_query,$out_node)){
		/*
                if($is_busqueda_simple){
                    $form['my_actualizar_btn']= array(
                     '#value'=>'<input type="button" id="my_actualizar_btn" name="my_actualizar_btn" value="'.t('Update Public/Featured').'"/>');
                }*/
                /*$form['my_borrar'] = array(
		'#type' => 'markup',
		'#value' => l(t('Delete'), 'carpeta_dinamica/borrar',array('query'=>str_replace('busqueda?','',$my_query))));*/
                $form['my_borrar'] = array(
		'#type' => 'button',
		'#value' => t('Delete'),
                '#attributes'=>array('style'=>'display:none;'));
                //$url=url(t('Delete'), 'carpeta_dinamica/borrar',array('query'=>str_replace('busqueda?','',$my_query)));
                $url=url('carpeta_dinamica/borrar',array('query'=>str_replace('busqueda?','',$my_query)));
                hontza_carpeta_dinamica_borrar_add_js($url);
		//
		/*$form['my_query'] = array(
		  '#type' => 'hidden',
		  '#value' => $my_query,
		);*/
	}
        //
	create_carpeta_dinamica_tipo_filter_field($form,$form_state, $form_id);
	create_carpeta_dinamica_categoria_canal_filter_field($form,$form_state, $form_id);
        //gemini-2014
        if(!$is_busqueda_simple){
            hontza_create_carpeta_dinamica_validador_filter_field($form,$form_state, $form_id);        
        }
        //
        create_carpeta_dinamica_etiquetas_filter_field($form,$form_state, $form_id);
	create_carpeta_dinamica_comentado_filter_field($form,$form_state, $form_id);
	create_carpeta_dinamica_fecha_filter_field($form,$form_state, $form_id);
        create_publica_destacada_fieldset($form,$out_node);
        /*
        //intelsat-2015
        red_create_carpeta_dinamica_status_filter_field($form,$form_state, $form_id*/
        $form['submit']['#value']=t('Search');
}
//intelsat-2015
//function array_ordenatu($sarrera,$izena,$order_erantzuna,$is_numeric=1){
function array_ordenatu($sarrera,$izena,$order_erantzuna,$is_numeric=1,$is_type_of_group=0,$second_field='node_title'){
	$cfg=$sarrera;
        if(count($cfg)<=0){
            return $cfg;
        }
        // Obtain a list of columns
	$myArray=array();
	foreach ($cfg as $key => $row) {
            if(is_array($row)){
                $myArray[$key]  = strtoupper($row[$izena]);
                if($is_type_of_group){
                    $second_array[$key]=strtoupper($row[$second_field]);
                }
            }else{
		$myArray[$key]  = strtoupper($row->$izena);
                if($is_type_of_group){
                    if(isset($row->$second_field)){
                        $second_array[$key]=strtoupper($row->$second_field);
                    }else{
                        $second_array[$key]=strtoupper($row[$second_field]);
                    }    
                }
            }    
	}
        //echo print_r($myArray,1);
	//$cfg=my_convert_array($cfg);	
	//echo print_r($cfg,1);
	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	//print 'sort='.$order_erantzuna.'<BR>';
	if(strcmp(strtolower($order_erantzuna),'desc')==0){	
		//intelsat-2016
                //if($is_numeric){
                if($is_numeric && $is_type_of_group!=2){
			array_multisort($myArray, SORT_DESC, $cfg);
		}else{
                    //intelsat-2015
                    if($is_type_of_group){
                        if($is_type_of_group==2){
                            array_multisort($myArray, SORT_DESC,$second_array, SORT_DESC,$cfg);
                        }else{
                            array_multisort($myArray, SORT_DESC,$second_array, SORT_ASC,$cfg);
                        }   
                    }else{
			array_multisort($myArray, SORT_DESC,SORT_STRING, $cfg);
                    }    
		}
	}else{
		if($is_numeric){
			array_multisort($myArray, SORT_ASC, $cfg);
		}else{
                    //intelsat-2015
                    if($is_type_of_group){
                        array_multisort($myArray, SORT_ASC,$second_array, SORT_ASC,$cfg);
                    }else{
			array_multisort($myArray, SORT_ASC,SORT_STRING, $cfg);
                    }    
		}	
	}
	//$cfg=my_convert_object($cfg);	
	//	
	return $cfg;
}
function my_order_view($my_list_in,$offset, $items_per_page,$view_name){
	//echo print_r($my_list_in,1);
        /*if(in_array($view_name,array('og_vigilancia_mascomentadas'))){
            $my_list=$my_list_in;
        }else{*/
        $my_sort='';
            $url=get_url_mis_contenidos();
            if(empty($url)){
                if(is_gestion_items()){
                    $info=create_gestion_items_order_array($my_list_in);
                }else if(is_canales_por_categorias('last_update',$view_name)){
                    $info=create_canales_por_categorias_order_array($my_list_in);
                }else if($view_name=='og_canales'){
                    //gemini-2014
                    if(hontza_is_canales_usuarios_all($view_name)){
                        $info=hontza_og_canales_mis_canales_create_order_array($my_list_in);
                        $my_sort='asc';
                    //    
                    }else{
                        $info=hontza_og_canales_create_order_array($my_list_in);                    
                        $my_sort='desc';
                    }                    
                }else{                                        
                    $info=create_order_array($my_list_in);
                }
            }else{
                    $info=create_mis_contenidos_order_array($my_list_in);
                    //return $my_list_in;		
            }
            //
            if(empty($my_sort)){
                $my_sort=my_get_request('sort');
            }
            $my_list=array_ordenatu($info['my_list'],$info['field'],$my_sort);            
        //}
        $my_list=array_slice($my_list,$offset,$items_per_page);
	//echo print_r($my_list,1);
        return $my_list;
}
function is_my_order_view($view_name){
        
        $order=my_get_request('order');	
	//
	if(is_fuentes_pipes_todas() && in_array($order,array('field_supercanal_calidad_rating','field_supercanal_exhaustividad_rating','field_supercanal_actualizacion_rating'))){
		return 1;
	}
	//
	$url=get_url_mis_contenidos();
	if(!empty($url)){
            	$order_info=my_get_order_info_canales_por_categorias();
                if(!empty($order_info['order']) && strcmp($order_info['order'],'group-nid')==0){
			return 1;
		}
                //gemini-2014
                if(is_mis_contenidos('canales')){
                    if(!empty($order_info['order']) && strcmp($order_info['order'],'title')==0){
                        return 1;
                    }else if(!empty($order_info['order']) && strcmp($order_info['order'],'type')==0){
                        return 1;
                    }else if(!empty($order_info['order']) && strcmp($order_info['order'],'field_fuente_canal_value')==0){
                        return 1;
                    }
                }
	}
        //
        if(is_gestion_items() && strcmp($order,'canales')==0){            
            return 1;
        }
        if(is_canales_por_categorias('last_update',$view_name) && strcmp($order,'last_update')==0){
            return 1;
        }
        //gemini-2014
        if($view_name=='og_canales'){
            if(hontza_is_mis_canales_block()){
                return 1;
            }    
        }
        //
	return 0;
} 
function is_fuentes_pipes_todas(){	
	if(arg(0)=='fuentes-pipes' && arg(1)=='todas'){
		return 1;		
	}
	return 0;
}
function create_order_array($my_list){
	$field='';
	$field_dapper='';
	//
	$my_array=array('calidad','exhaustividad','actualizacion');		
	//
	$f='';
	foreach($my_array as $i=>$my_f){	
		if(strcmp(my_get_request('order'),'field_supercanal_'.$my_f.'_rating')==0){
			$f=$my_f;
			$field='node_data_field_supercanal_calidad_field_supercanal_'.$f.'_rating';
			$field_dapper='node_data_field_fuentedapper_calidad_field_fuentedapper_'.$f.'_rating';
			break;
		}
	}
	//
	$result=array();
	if(!empty($my_list)){
		//echo print_r($my_list,1);
		foreach($my_list as $i=>$row){
			$result[$i]=$row;			
			if(strcmp($row->node_type,'supercanal')==0){
                            $result[$i]->$f=$my_list[$i]->$field;
			}else{
                            $result[$i]->$f=$my_list[$i]->$field_dapper;
			}
		}
	}
	//
	$info['field']=$f;
	$info['my_list']=$result;
	//echo print_r($info['my_list'],1);	
	return $info;
}
function my_get_request($field){
	if(isset($_REQUEST[$field]) && !empty($_REQUEST[$field])){
		return $_REQUEST[$field];
	}
	return '';
}
function my_convert_array($my_list){
	$result=array();
	
	foreach($my_list as $i=>$row){
		$result[$i]=(array) $row;
	}
	//echo print_r($result,1);
	return $result;
}
function my_convert_object($my_list){
	$result=array();
	
	foreach($my_list as $i=>$row){
		$result[$i]=(object) $row;
	}
	//echo print_r($result,1);
	return $result;
}
function is_menutop_vigilancia(){	
	if(strcmp(arg(0),'tagadelic')==0 && strcmp(arg(1),'chunk')==0 && arg(2)==3){
		return 1;
	}
	if(is_term_delete($my_url)){
		$tid=arg(5);
		/*$term=taxonomy_get_term($tid);
		if(!empty($term) && isset($term->vid) && !empty($term->vid)){
			$vocab_list=my_get_og_vocab_list(array('vid'=>$term->vid));
			//echo print_r($vocab_list,1);
			if(!empty($vocab_list)){
				return 1;
			}
		}*/
		return is_tid_in_og_vocab($tid);
	}
        if(is_vigilancia()){
            	return 1;
	}
	return 0;
}
function my_get_og_vocab_list($fields){
	$where=array();
	$where[]='1';
	if(!empty($fields)){
		foreach($fields as $f=>$v){	
			if(!empty($v)){
				$where[]='og_vocab.'.$f.'='.$v;
			}
		}	
	}
	$sql='SELECT og_vocab.* FROM og_vocab WHERE '.implode(' AND ',$where);
	$result=db_query($sql);
	$my_result=array();
	while($row=db_fetch_object($result)){
		$my_result[]=$row;
	}
	return $my_result;
}
function is_term_delete(&$my_url){
	$my_url='';
	if(strcmp(arg(0),'admin')==0 && strcmp(arg(1),'content')==0 && strcmp(arg(2),'taxonomy')==0 && strcmp(arg(3),'delete')==0  && strcmp(arg(4),'term')==0 && is_numeric(arg(5))){
		$my_url=arg(0).'/'.arg(1).'/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5);
		return 1;
	}
	return 0;	
}
function save_path_term_delete(){
	if(is_term_delete($my_url)){
		update_context_path($c,$my_url);
		$c=my_get_context('vigilancia');
		if(!empty($c) && isset($c->name) && !empty($c->name)){
			$conditions=unserialize($c->conditions);
			if(isset($conditions['path']['values']) && !empty($conditions['path']['values'])){
				//echo print_r($conditions['path']['values'],1);
				$path_array=array_keys($conditions['path']['values']);
				//print $my_url.'<BR>';				
				if(!in_array($my_url,$path_array)){
					update_context_path($c,$my_url);
				}
			}
		}
	}
}
function my_get_context($name){	
	$where=array("1");
	if(!empty($name)){
		$where[]="name='".$name."'";
	}
	$sql="SELECT * FROM context WHERE ".implode(" AND ",$where);
	//print $sql;exit();	
	$result=db_query($sql);
	$my_result=array();
	while($row=db_fetch_object($result)){
		return $row;
	}
	//
	$my_result=(object) array();			
	return $my_result;	
}
function update_context_path($c,$my_url){
	$where=array();
	$where[]="name='".$c->name."'";
	$conditions=unserialize($c->conditions);
	$conditions['path']['values'][$my_url]=$my_url;
	$v=serialize($conditions);
	$sql="UPDATE context SET conditions='".$v."' WHERE ".implode(" AND ",$where);
	//print $sql;
		//db_query($sql);
}
function is_term_edit(){
	$my_url='';
	if(strcmp(arg(0),'node')==0 && is_numeric(arg(1)) && strcmp(arg(2),'og')==0 && strcmp(arg(3),'vocab')==0  && strcmp(arg(4),'terms')==0 && strcmp(arg(5),'edit')==0 && is_numeric(arg(6))){
		$tid=arg(6);
		//
		return is_tid_in_og_vocab($tid);
	}
	return 0;	
}
function is_tid_in_og_vocab($tid){
	  
		$term=taxonomy_get_term($tid);
		if(!empty($term) && isset($term->vid) && !empty($term->vid)){
			$vocab_list=my_get_og_vocab_list(array('vid'=>$term->vid));
			//echo print_r($vocab_list,1);
			if(!empty($vocab_list)){
				return 1;
			}
		}
		return 0;
}
function my_set_taxonomy_form_term(&$form, &$form_state, $form_id){
    //intelsat-2015
    if(!hontza_canal_rss_is_oferta_demanda_clasificaciones()){
    //
   	$form['spaces']['purl']['value']['#required']=0;
	$form['#submit'][]='my_taxonomy_form_term_submit';	
	//
	//print $form_id;exit();	
	if(isset($form['confirm']) && isset($form['confirm']['#value']) && $form['confirm']['#value']){		
		if(isset($form['actions']) && isset($form['actions']['cancel'])){						
			if(isset($form['#term']) && isset($form['#term']['tid'])){
				$bai=is_categoria_de_la_fuente($form['#term']['tid'],$my_term);
				//if($bai){	
					//echo print_r($my_term,1);
					if(!empty($my_term) && isset($my_term->vid) && !empty($my_term->vid)){						
						//print $my_term->vid;exit();
						$vocab_list=my_get_og_vocab_list(array('vid'=>$my_term->vid));
						$bai=0;
						if(count($vocab_list)>0){							
							if(is_destination_og_vocab()){
								$form['actions']['cancel']['#value']=l(t('Cancel'),'node/'.$vocab_list[0]->nid.'/og/vocab/terms/'.$vocab_list[0]->vid);							
								$bai=1;
							}
						}
						if(!$bai){	
							$form['actions']['cancel']['#value']=l(t('Cancel'),'admin/content/taxonomy/'.$my_term->vid);
							add_hide_menutop_left();
						}
					}
				//}					
			}
		}
	}else{			
		$bai=0;		
		if(is_term_edit()){
			$tid=$form['#term']['tid'];			
			$og_vocab=my_get_og_vocab_row(array('tid'=>$tid));
			$bai=1;
		}else if(is_term_add()){
			$vid=my_get_addterm_vid();						
			$og_vocab=my_get_og_vocab_row(array('vid'=>$vid));
			//echo print_r($og_vocab,1);
			$bai=1;
		}
		//		
		if($bai && isset($og_vocab->nid)){	
			$form['#action']=my_set_action_destination_form_term($form['#action'],$og_vocab->nid,$og_vocab->vid);
			if(isset($form['destination']['#value'])){				
				$form['destination']['#value']='node/'.$og_vocab->nid.'/og/vocab/terms/'.$og_vocab->vid;
			}
		}else if(is_admin_add_term()){
			if(isset($form['destination']['#value'])){				
				$form['destination']['#value']='admin/content/taxonomy/'.arg(3);
				//print $form['destination']['#value'];
			}
		}/*else if(is_admin_edit_term()){
			if(isset($form['destination']['#value'])){
				$current_term=taxonomy_get_term(arg(5));
				if(isset($current_term->vid) && !empty($current_term->vid)){			
					$form['destination']['#value']='admin/content/taxonomy/'.$current_term->vid;
				}
			}
		}*/
	}
    }    
}
function my_taxonomy_form_term_submit(&$form, &$form_state) {
	if(isset($form['#vocabulary']) && isset($form['#vocabulary']['vid']) && !empty($form['#vocabulary']['vid'])){	
		if(isset($form['#term']) && isset($form['#term']['tid'])){
			$tid=$form['#term']['tid'];
			//print 'tid='.$tid;exit();
			$term=taxonomy_get_term($tid);
			//echo print_r($term,1);exit();
			if(!empty($term) && isset($term->vid) && !empty($term->vid)){
				$vocab_list=my_get_og_vocab_list(array('vid'=>$term->vid));
				//echo print_r($vocab_list,1);exit();
				if(!empty($vocab_list)){
					//$form_state['redirect'] =url('node/'.$vocab_list[0]->nid.'/og/vocab/terms/'.$vocab_list[0]->vid);
					$form_state['redirect'] ='node/'.$vocab_list[0]->nid.'/og/vocab/terms/'.$vocab_list[0]->vid;
					//print 'aaaa='.$form_state['redirect'];exit();					
				}else{
					$form_state['redirect'] ='admin/content/taxonomy/'.$term->vid;
				}
			}
		}else{
			//echo 'vid='.$form['#vocabulary']['vid'];exit();
			$vid=$form['#vocabulary']['vid'];
			if(!empty($vid)){
				if(is_term_add()){
					$og_vocab=my_get_og_vocab_row(array('vid'=>$vid));
					if(!empty($og_vocab) && isset($og_vocab->nid) && !empty($og_vocab->nid)){
						$form_state['redirect'] ='node/'.$og_vocab->nid.'/og/vocab/terms/'.$og_vocab->vid;									
					}
				}else{
					$form_state['redirect'] = 'admin/content/taxonomy/'.$vid;
				}						
			}else{
				$form_state['redirect'] = 'admin/content/taxonomy/'.$vid;
			}
		}	
	}
}
function my_set_action_destination_form_term($action,$nid,$vid){
	//return url('node/'.$nid.'/og/vocab/terms/'.$vid);
	$pos=strpos($action,'?');
	$lotu='?';
	if($pos!==FALSE){
		$lotu='&';
	}
	return $action.$lotu.'destination=node/'.$nid.'/og/vocab/terms/'.$vid;
}
function my_get_og_vocab_row($fields){
	$vid='';
	//
	if(isset($fields['vid']) && !empty($fields['vid'])){
		$vid=$fields['vid'];
	}
	//
	if(empty($vid)){
		$term=taxonomy_get_term($fields['tid']);
		//echo print_r($term,1);exit();
		if(!empty($term) && isset($term->vid) && !empty($term->vid)){
			$vid=$term->vid;
		}
	}
	//
	if(!empty($vid)){
		$vocab_list=my_get_og_vocab_list(array('vid'=>$vid));
		//echo print_r($vocab_list,1);exit();
		if(!empty($vocab_list)){
			return $vocab_list[0];
		}
	}
	//	
	$my_result=(object) array();			
	return $my_result;		
}
function add_hide_menutop_left(){
$js='$(document).ready(function()
{   
   $("#navigation").hide();
   $("#sidebar-left").hide();
   $("#main").css("background-color","#F4F4F4");    
});';
 drupal_add_js($js,'inline');
 //
}
function my_get_addterm_vid(){
	return arg(5);
}
function is_term_add(){
	$my_url='';
	if(strcmp(arg(0),'node')==0 && is_numeric(arg(1)) && strcmp(arg(2),'og')==0 && strcmp(arg(3),'vocab')==0  && strcmp(arg(4),'terms')==0 && is_numeric(arg(5)) && strcmp(arg(6),'add_term')==0){
		$vid=arg(5);
		return is_vid_in_og_vocab($vid);		
	}
	return 0;	
}
function is_vid_in_og_vocab($vid){
		if(!empty($vid)){
			$vocab_list=my_get_og_vocab_list(array('vid'=>$vid));
			//echo print_r($vocab_list,1);exit();
			if(!empty($vocab_list)){
				return 1;
			}
		}
		return 0;
}
function is_destination_og_vocab(){
	$my_destination=drupal_get_destination();
	$pos=strpos(urldecode($my_destination),"admin/content/taxonomy");
	//print 'pos='.$pos.'<BR>';
	if($pos!==FALSE){
		return 0;
	}
	return 1;
}
function is_admin_add_term(){
	//admin/content/taxonomy/7/add/term
	if(strcmp(arg(0),'admin')==0 && strcmp(arg(1),'content')==0 && strcmp(arg(2),'taxonomy')==0 && is_numeric(arg(3)) && strcmp(arg(4),'add')==0 && strcmp(arg(5),'term')==0){
		return 1;
	}
	return 0;	
}
function is_admin_edit_term(){
	//admin/content/taxonomy/edit/term/241
	if(strcmp(arg(0),'admin')==0 && strcmp(arg(1),'content')==0 && strcmp(arg(2),'taxonomy')==0 && strcmp(arg(3),'edit')==0 && strcmp(arg(4),'term')==0 && is_numeric(arg(5))){
		return 1;
	}
	return 0;	
}
function my_help_analisis(){
	$param=arg(1);	
	if(empty($param)){
		return help_popup_window(11143, 'help',my_get_help_link_object());
	}	
}
function my_help_contenidos_del_grupo(){
	$param=arg(1);	
	if(empty($param)){
		return help_popup_window(11144, 'help',my_get_help_link_object());
	}	
}
function my_help_servicios(){
	$param=arg(1);	
	if(empty($param)){
		return help_popup_window(11145, 'help',my_get_help_link_object());
	}	
}
function my_set_taxonomy_overview_terms(&$form,&$form_state, $form_id){
	//print $form_id.'<BR>';
	//print $form['#id'].'<BR>';	
	if(strcmp($form_id,'taxonomy_overview_terms')==0){
		if(is_crear_modificar_categorias()){
			$form['#submit'][]='my_taxonomy_overview_terms_submit';					
			if(isset($form['destination']['#value'])){	
				//$form['destination']['#value']='node/'.arg(1).'/og/vocab/terms/'.arg(5);
				$form['destination']['#value']='vigilancia/pendientes';
			}						
			/*$form['#action']=my_set_action_destination_form_term($form['#action'],arg(1),arg(5));								
			print $form['#action'].'<BR>';				
			*/			
			//
			if(isset($form['actions']['cancel']['#value'])){
				$form['actions']['cancel']['#value']=l(t('Cancel'),'node/'.arg(1).'/og/vocab/terms/'.arg(5),array('attributes'=>array('class'=>'a_urdina')));				
			}			
		}
	}
}
function is_crear_modificar_categorias(){
	if(strcmp(arg(0),'node')==0 && is_numeric(arg(1)) && strcmp(arg(2),'og')==0 && strcmp(arg(3),'vocab')==0 && strcmp(arg(4),'terms')==0 && is_numeric(arg(5)) && is_vid_in_og_vocab(arg(5))){
		//$vid=arg(5);
		//print $vid;            
		return 1;
	}
	return 0;
}
function my_taxonomy_overview_terms_submit(&$form, &$form_state) {
	if(isset($form['#vocabulary']['vid']) && !empty($form['#vocabulary']['vid'])){
		$vid=$form['#vocabulary']['vid'];
		$vocab_list=my_get_og_vocab_list(array('vid'=>$vid));
		/*echo print_r($vocab_list,1);
		exit();*/
		if(!empty($vocab_list)){
			$form_state['redirect'] ='node/'.$vocab_list[0]->nid.'/og/vocab/terms/'.$vocab_list[0]->vid;
			//$form_state['redirect'] ='vigilancia/pendientes';
		}
	}
}
function my_alphabetical_redirect(&$form_state){	
	/*if(isset($form['#vocabulary']['vid']) && !empty($form['#vocabulary']['vid'])){
		$vid=$form['#vocabulary']['vid'];*/
	/*if(isset($form_state['values']['vid']) && !empty($form_state['values']['vid'])){
		$vid=$form_state['values']['vid'];
		//print 'vid='.$vid;exit();	
		$vocab_list=my_get_og_vocab_list(array('vid'=>$vid));
		//echo print_r($vocab_list,1);
		if(!empty($vocab_list)){			
			$form_state['redirect'] ='node/'.$vocab_list[0]->nid.'/og/vocab/terms/'.$vocab_list[0]->vid;							
		}
	}*/
    if(!hontza_canal_rss_is_oferta_demanda_clasificaciones()){
        $form_state['redirect'] ='vigilancia/pendientes';
    }    
}
function is_og_users(){
	if(strcmp(arg(0),'og')==0 && strcmp(arg(1),'users')==0 && is_numeric(arg(2))){
		//print arg(2);
		$nid=arg(2);
		$my_list=my_get_og_vocab_list(array('nid'=>$nid));
		if(count($my_list)>0){
			return 1;
		}
	}
        
	return 0;
}
function carpeta_dinamica_borrar_callback($js=FALSE){
    if(hontza_carpeta_dinamica_borrar_access($node_carpeta_dinamica)){
	//my_delete_carpeta_dinamica();
	//drupal_goto('busqueda');
	hontza_delete_carpeta_dinamica($node_carpeta_dinamica);
        drupal_goto('canal-busqueda');
	return '';
    }    
}
function my_get_query($param='busqueda'){
	$result=array();
	foreach($_GET as $name=>$value){
                if(in_array($name,array('is_my_submit','is_carpeta_noticia_publica','is_carpeta_noticia_destacada'))){
                    continue;
                }
                //
		if(strcmp($name,'q')==0){
			//
                }else if(in_array($name,array('fecha_inicio','fecha_fin'))){
                    if(!empty($value)){
                        foreach($value as $f=>$v){
                            $result[]=$name.'['.$f.']='.$v;
                        }
                    }
                }else{
			$result[]=$name.'='.$value;
		}
	}
	return $param.'?'.implode('&',$result);
}
function is_show_borrar_carpeta_dinamica(&$my_query,&$node){       
	$my_query=my_get_query();
	if(!empty($my_query)){
                //print 'my_query='.$my_query.'<BR>';
		$node=my_load_carpeta_dinamica(array('field_canal_busqueda_busqueda_value'=>$my_query));
		if(isset($node->nid) && !empty($node->nid)){
			return 1;
		}
	}
        //
        $node=array();
        $node=(object) $node;
	return 0;
}
function my_load_carpeta_dinamica($fields){
	$where=array();
	$where[]='1';
	if(!empty($fields)){
                foreach($fields as $f=>$v){
                    //intelsat-2015
                    //$where[]=$f.'="'.$v.'"';
                    $where[]=$f.'="'.addslashes($v).'"';
		}    
	}
	$sql='SELECT c.* FROM content_type_canal_busqueda c WHERE '.implode(' AND ',$where);
	
	$result = db_query($sql);
	 
		  while ($row = db_fetch_object($result)) {		
			//return $row;
		  	$node=node_load(array('nid'=>$row->nid));
		  	return $node;
		  }
		
	$my_result=(object) array();
			
	return $my_result;	
	
}
function create_carpeta_dinamica_tipo_filter_field(&$form,&$form_state, $form_id){
	$tipo_array=array();
	$tipo_array=array(
		  'operator' => 'tipo_op',
		  'value' => 'tipo',
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  'label'=>t('Type').':');
	//
	$select_array=array(
			'#type' => 'select',
			//'#size' => 30,
			'#options'=>my_get_tipo_options());	
	//
	$form['#info']['filter-tipo']=$tipo_array;
	$form['tipo']=$select_array;			
	//echo print_r($form,1);
}
//intelsat-2016
//function my_get_tipo_options(){
function my_get_tipo_options($is_busqueda_avanzada_solr=0){
  $padres = taxonomy_get_tree(1, 0, -1, 1);
  $taxo = array();
  $taxo[0]=t('Any');
  //intelsat-2016
  $noticia_usuario_tid='';
  if($is_busqueda_avanzada_solr){
    $noticia_usuario_tid=red_solr_inc_get_fuente_tipo_noticia_tid();
  }
  foreach ($padres as $padre){
    //intelsat-2016
    if(!red_solr_inc_is_show_tipo($is_busqueda_avanzada_solr,$padre,$noticia_usuario_tid)){
        continue;
    }  
    $taxo[$padre->tid] = red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$padre->name);
    $hijos = taxonomy_get_children($padre->tid);
    if (!empty($hijos)){
      foreach ($hijos as $hijo){
        //intelsat-2016  
        $label='--'.$hijo->name;
        $label=red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$label);
        $taxo[$hijo->tid] =$label;
      }
    }
  }
  return $taxo;
}
function my_carpeta_dinamica_pre_execute(&$view,$my_request_in='',$grupo_nid_in='',$is_alerta=0){
global $user;
//intelsat-2015
$my_request=$my_request_in;
if(empty($my_request)){
    $my_request=$_REQUEST;
}
//
if(hontza_is_busqueda_en_blanco()){
    $sql=hontza_get_busqueda_en_blanco_sql();
    $view->build_info['query']=$sql; 	
    $view->build_info['count_query']=$sql;
    return '';
}

$where=array();
$where[]="1";
//
$type_array=array("item", "noticia");
$bai_publica=0;
if(isset($my_request['is_carpeta_noticia_publica']) && !empty($my_request['is_carpeta_noticia_publica'])){
    $bai_publica=1;
    $type_array[]="noticias_portada";
    $where[]="(node.type='noticias_portada' OR node_data_field_item_canal_reference.field_is_carpeta_noticia_publica_value=1 OR content_type_canal_de_supercanal.field_is_canal_noticia_publica_value=1 OR content_type_canal_de_yql.field_is_yql_noticia_publica_value=1 OR content_type_noticia.field_is_noticia_usuario_publica_value=1)";
}
$bai_destacada=0;
if(isset($my_request['is_carpeta_noticia_destacada']) && !empty($my_request['is_carpeta_noticia_destacada'])){
    $bai_destacada=1;
    $type_array[]="rss_feed";
    $where[]="(node.type='rss_feed' OR node_data_field_item_canal_reference.field_is_carpeta_noticia_destaca_value=1 OR content_type_canal_de_supercanal.field_is_canal_noticia_destacada_value=1 OR content_type_canal_de_yql.field_is_yql_noticia_destacada_value=1 OR content_type_noticia.field_is_noticia_usuario_destaca_value=1)";
}
$where[]="(node.type in ('".implode("','",$type_array)."'))";
//intelsat-2015
if(!empty($grupo_nid_in)){
    $my_grupo=node_load($grupo_nid_in);
}else{
//    
    $my_grupo=og_get_group_context();
}
//
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
    if($bai_publica && $bai_destacada){
        //$where[]="(og_ancestry.group_nid = ".$my_grupo->nid." OR node.type='noticias_portada' OR node.type='rss_feed')";
    }else if($bai_publica){
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid." OR node.type='noticias_portada')";
    }else if($bai_destacada){
        //$where[]="(og_ancestry.group_nid = ".$my_grupo->nid." OR node.type='rss_feed')";
    }else{
        $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }
}
//
$comentado_left_join="";
if(isset($my_request['search']) && !empty($my_request['search'])){
	//$where[]="(search_index.word = '".$my_request['search']."')";
	$where[]=my_get_search_index_word($my_request['search']);
	//$where[]="(search_index.type = 'node')";
	$comentado_left_join="LEFT JOIN {comments} comments ON node.nid=comments.nid";
}		
//
$or=array();
if(isset($my_request['title']) && !empty($my_request['title'])){
	$or[]="(node.title LIKE '%%".$my_request['title']."%%')";
}
/*if(isset($my_request['search']) && !empty($my_request['search'])){
	$or[]="(node.title LIKE '%%".$my_request['search']."%%')";
}*/
if(isset($my_request['body']) && !empty($my_request['body'])){
	$or[]="(node_revisions.body LIKE '%%".$my_request['body']."%%')";
}
/*if(isset($my_request['search']) && !empty($my_request['search'])){
	$or[]="(node_revisions.body LIKE '%%".$my_request['search']."%%')";
}*/
if(!empty($or)){
	$where[]='('.implode(" OR ",$or).')';
	//print '('.implode(" OR ",$or).')';exit();
}
//
$or=array();
if(isset($my_request['no_title']) && !empty($my_request['no_title'])){
	$or[]="(node.title NOT LIKE '%%".$my_request['no_title']."%%')";
}
if(isset($my_request['no_body']) && !empty($my_request['no_body'])){
	$or[]="(node_revisions.body NOT LIKE '%%".$my_request['no_body']."%%')";
}
if(!empty($or)){
	$where[]='('.implode(" OR ",$or).')';
}
//
if(isset($my_request['title_2']) && !empty($my_request['title_2'])){
	$where[]="(node_node_data_field_item_canal_reference.title LIKE ('%%".$my_request['title_2']."%%'))";
}
//
$tipo_left_join="";
if(isset($my_request['tipo']) && !empty($my_request['tipo'])){
	$where[]="(term_node.tid=".$my_request['tipo'].")";
	$tipo_left_join="LEFT JOIN {feeds_node_item} feeds_node_item ON node.nid=feeds_node_item.nid
LEFT JOIN {node} node_canal ON feeds_node_item.feed_nid=node_canal.nid
LEFT JOIN {content_field_nombrefuente_canal} c ON node_canal.nid=c.nid
LEFT JOIN {node} node_fuente ON c.field_nombrefuente_canal_value=node_fuente.title
LEFT JOIN {term_node} term_node ON node_fuente.nid=term_node.nid";
}
//
$etiquetas_left_join="";
if(isset($my_request['etiquetas']) && !empty($my_request['etiquetas'])){
	//$where[]="(term_data_etiquetas.name='".$my_request['etiquetas']."')";
	$where[]='(term_data_etiquetas.vid=3)';
	$where[]=get_where_etiquetas($my_request['etiquetas']);
	$etiquetas_left_join="
LEFT JOIN {term_node} term_node_etiquetas ON node.nid=term_node_etiquetas.nid
LEFT JOIN {term_data} term_data_etiquetas ON term_node_etiquetas.tid=term_data_etiquetas.tid";
}
//
//$comentado_left_join="";
if(isset($my_request['comentado']) && !empty($my_request['comentado'])){
	if($my_request['comentado']==1){
		$where[]="(NOT comments.nid IS NULL)";
	}else{
		$where[]="(comments.nid IS NULL)";
	}
	if(empty($comentado_left_join)){
		$comentado_left_join="LEFT JOIN {comments} comments ON node.nid=comments.nid";
	}
}
//gemini-2014
$validator_left_join="";
if(isset($my_request['responsable_uid']) && !empty($my_request['responsable_uid'])){
    $validator_name=$my_request['responsable_uid'];
    $validator_user=user_load(array('name'=>$validator_name));
    if(isset($validator_user->uid) && !empty($validator_user->uid)){
        $where[]="flag_content_node.uid=".$validator_user->uid;
        if(empty($validator_left_join)){
            $validator_left_join="LEFT JOIN {flag_content} flag_content_node ON (node.nid = flag_content_node.content_id AND flag_content_node.fid = 2)";
	}
    }
}    
//
/*if(is_fecha_filter()){
	$where[]=get_fecha_filter();
}
if(is_fecha_filter("fecha_fin")){
	$where[]=get_fecha_filter("fecha_fin");
}*/
$ini=get_fecha_filter('fecha_inicio',1,$is_alerta);
if(!empty($ini)){
	$where[]=$ini;
}
$fin=get_fecha_filter("fecha_fin",1,$is_alerta);
if(!empty($fin)){
	$where[]=$fin;
}
//
$categoria_canal_left_join="";
if(isset($my_request['categoria_canal']) && !empty($my_request['categoria_canal'])){
	$where[]="(term_node_categoria_canal.tid=".$my_request['categoria_canal'].")";
	$categoria_canal_left_join="LEFT JOIN {feeds_node_item} feeds_node_item_categoria_canal ON node.nid=feeds_node_item_categoria_canal.nid
LEFT JOIN {node} node_categoria_canal ON feeds_node_item_categoria_canal.feed_nid=node_categoria_canal.nid
LEFT JOIN {term_node} term_node_categoria_canal ON node_categoria_canal.nid=term_node_categoria_canal.nid";
}
//
/*$sql="SELECT node.nid AS nid, SUM(search_index.score * search_total.count) AS score,node.title,node_node_data_field_item_canal_reference.title as title2
FROM {node} node 
LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
LEFT JOIN {node} node_node_data_field_item_canal_reference ON 
node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid 
LEFT JOIN {search_index} search_index ON node.nid = search_index.sid 
LEFT JOIN {search_total} search_total ON search_index.word = search_total.word 
LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
LEFT JOIN {content_type_canal_de_supercanal} content_type_canal_de_supercanal ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_type_canal_de_supercanal.nid
LEFT JOIN {content_type_canal_de_yql} content_type_canal_de_yql ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_type_canal_de_yql.nid
LEFT JOIN {content_type_noticia} content_type_noticia ON node.nid=content_type_noticia.nid
".$tipo_left_join." ".$etiquetas_left_join." ".$comentado_left_join." ".$categoria_canal_left_join." ".$validator_left_join." 
 WHERE ".implode(" AND ",$where)."
GROUP BY search_index.sid, nid 
HAVING COUNT(*) >= 1
ORDER BY node.created DESC";*/
//AVISO:::: hemos quitado search_index porque va muy lento.
$sql="SELECT node.nid AS nid,node.title,node_node_data_field_item_canal_reference.title as title2
FROM {node} node 
LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid 
LEFT JOIN {node} node_node_data_field_item_canal_reference ON 
node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid  
LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
LEFT JOIN {content_type_canal_de_supercanal} content_type_canal_de_supercanal ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_type_canal_de_supercanal.nid
LEFT JOIN {content_type_canal_de_yql} content_type_canal_de_yql ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_type_canal_de_yql.nid
LEFT JOIN {content_type_noticia} content_type_noticia ON node.nid=content_type_noticia.nid
".$tipo_left_join." ".$etiquetas_left_join." ".$comentado_left_join." ".$categoria_canal_left_join." ".$validator_left_join." 
 WHERE ".implode(" AND ",$where)."
ORDER BY node.created DESC";

/*if($user->uid==1){
    print $sql;
}*/
//save_carpeta_publica_destacada($sql);

//simular_count_query($sql);
//simular_result_query($sql);
$view->build_info['query']=$sql; 	
$view->build_info['count_query']=$sql;			
}
function my_get_tipo_informacion($nid){
	$where=array();
	$where[]="1";
	if(!empty($nid)){
		$where[]="node.nid=".$nid;
	}
	$sql="SELECT node_fuente.* 
	FROM node 
	LEFT JOIN feeds_node_item ON node.nid=feeds_node_item.nid
	LEFT JOIN node node_canal ON feeds_node_item.feed_nid=node_canal.nid
	LEFT JOIN content_field_nombrefuente_canal c ON node_canal.nid=c.nid
	LEFT JOIN node node_fuente ON c.field_nombrefuente_canal_value=node_fuente.title
	WHERE ".implode(" AND ",$where);
	
	$my_result=array();
	
	$result = db_query($sql);
	 
		  while ($row = db_fetch_object($result)) {		
			$my_result[]=$row;
		  }
		
	//echo print_r($my_result,1);
			
	return $my_result;	
}
function create_carpeta_dinamica_etiquetas_filter_field(&$form,&$form_state, $form_id){
	$my_array=array();
	$my_array=array(
		  'operator' => 'etiquetas_op',
		  'value' => 'etiquetas',
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  'label'=>t('Tag').':');
	//
	/*$my_array2=array(
			'#type' => 'textfield',
			'#size' => 30,
			'#default_value' =>'');*/
        //
        $vid=3;
        $my_array2= array('#type' => 'textfield',
          //'#title' => $vocabulary->name,
          //'#title' => set_vocabulary_name_title($vocabulary->name),
          //'#description' => $help,
          //'#required' => $vocabulary->required,
          //'#default_value' => $typed_string,
          '#autocomplete_path' => 'taxonomy/autocomplete/is_busqueda',
          //'#weight' => $vocabulary->weight,
          //'#maxlength' => 1024,
        );        
	//
	$form['#info']['filter-etiquetas']=$my_array;
	$form['etiquetas']=$my_array2;		
}
function get_where_etiquetas($etiquetas){
	//return "(term_data_etiquetas.name='".$_REQUEST['etiquetas']."')";
	$my_array=explode(",",$etiquetas);	
	foreach($my_array as $i=>$v){
		//$result[]="(term_data_etiquetas.name='".$v."')";
		$result[]="(term_data_etiquetas.name LIKE '%%".$v."%%')";
	}
	//	
	//return '('.implode(" OR ",$result).')';
	return '('.implode(" OR ",$result).')';
}
function create_carpeta_dinamica_comentado_filter_field(&$form,&$form_state, $form_id){	
	$my_array=array(
		  'operator' => 'comentado_op',
		  'value' => 'comentado',
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  'label'=>t('Commented').':');
	//
	$my_array2=array(
			'#type' => 'select',
			//'#size' => 30,
			'#options'=>my_get_si_no_options());	
	//
	$form['#info']['filter-comentado']=$my_array;
	$form['comentado']=$my_array2;			
}
function my_get_si_no_options(){
	$result=array();
	//
	$result[0]=t('Any');
	$result[1]=t('Yes');
	$result[2]=t('No');
	//	
	return $result;
}
function create_carpeta_dinamica_fecha_filter_field(&$form,&$form_state, $form_id){
	//return '';
	$my_array=array(
		  'operator' => 'fecha_inicio_op',
		  'value' => 'fecha_inicio');
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  //'label'=>t('Fecha inicio'));
	//
	$my_array2=array(
			'#type' => 'date_select',
                        //'#type' => 'date',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('Start Date'),
                        //'#date_year_range' => '-3:+3',
                        '#default_value'=>my_get_request_fecha_value('fecha_inicio'),
                        );

        /*$default_value=my_get_request_fecha_value('fecha_inicio');
	if(!empty($default_value)){
            //print $default_value;exit();
            $my_array2['#default_value']=$default_value;
        }else{
            $my_array2['#default_value']='';
        }*/
        //echo print_r($_SESSION,1);
        //print $my_array2['#default_value'].'<BR>';
	/*if(empty($my_array2['#default_value'])){
            unset($my_array2['#default_value']);
	}*/
	//echo print_r($my_array2,1);
	//
	$form['#info']['filter-fecha_inicio']=$my_array;
	$form['fecha_inicio']=$my_array2;
	//$form['fecha_inicio']['#validated'] = true;
	//
	$my_array=array(
		  'operator' => 'fecha_fin_op',
		  'value' => 'fecha_fin');
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  //'label'=>t('Fecha inicio'));
	//
	$my_array2=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('End date'),
			'#default_value'=>my_get_request_fecha_value('fecha_fin'));
	//print $my_array2['#default_value'].'<BR>';			
	//
	//if(empty($my_array2['#default_value'])){
		//unset($my_array2['#default_value']);			
	//}
	//return '';
	$form['#info']['filter-fecha_fin']=$my_array;
	$form['fecha_fin']=$my_array2;
	//$form['fecha_fin']['#validated'] = true;
	//
        //echo print_r($form,1);exit();
}
function is_fecha_filter($f='fecha_inicio'){
	$my_array=array('year','month','day');
	foreach($my_array as $i=>$v){
		if(!is_array($_REQUEST[$f]) || !isset($_REQUEST[$f][$v]) || empty($_REQUEST[$f][$v])){			
			return 0;
		}
	}
	return 1;
}
//intelsat-2015
//se ha añadido is_alerta
function get_fecha_filter($f='fecha_inicio',$is_mysql=1,$is_alerta=0){
    //intelsat-2015
    if($is_alerta){
        return '';
    }
    //
	$comp=">=";
	$hora=" 00:00:00";
	if(strcmp($f,"fecha_fin")==0){
		$comp="<=";
		$hora=" 23:59:59";
	}
	if(is_fecha_filter($f)){		
		$fecha=$_REQUEST[$f]['year']."-".$_REQUEST[$f]['month']."-".$_REQUEST[$f]['day'];		
		if($is_mysql){
			$fecha.=$hora;
			return "node.created".$comp."UNIX_TIMESTAMP('".$fecha."')";
		}
		return $fecha;
	}/*else{		
		$s=my_get_request_value($f);
		if($is_mysql){			
			return "node.created".$comp."UNIX_TIMESTAMP('".$s."')";
		}
		return $s;
	}*/
	return '';
}
function create_carpeta_dinamica_categoria_canal_filter_field(&$form,&$form_state, $form_id){ 
	$categorias=my_get_categorias_canal();
	//
	//$label=utf8_encode(t('Categoría de canal'));
        $label=t('Category').':';
	$my_array=array(
		  'operator' => 'categoria_canal_op',
		  'value' => 'categoria_canal',
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  'label'=>$label);
	//
	$my_array2=array(
			'#type' => 'select',
			//'#size' => 30,
			'#options'=>my_get_categorias_canal());	
	//
	$form['#info']['filter-categoria_canal']=$my_array;
	$form['categoria_canal']=$my_array2;			
}
//intelsat-2016
//function my_get_categorias_canal($with_prepare=1){
function my_get_categorias_canal($with_prepare=1,$is_busqueda_avanzada_solr=0){
$my_og=og_get_group_context();
$categorias=array();
if ($my_og) {
    //Obtener el ID del grupo en el que se esta
    $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s", $my_og->nid));
    
    //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
    $categorias=taxonomy_get_tree($id_categoria);
    //intelsat-2014
    if($with_prepare){
        $categorias=my_prepare_categorias_canal($categorias,$is_busqueda_avanzada_solr);
        /*if($is_busqueda_avanzada_solr){
            $categorias=red_solr_inc_get_categorias_options_label_array($categorias,$is_busqueda_avanzada_solr);
        }*/
    }    
}
return $categorias;
}
//intelsat-2016
//function my_prepare_categorias_canal($tree){
function my_prepare_categorias_canal($tree,$is_busqueda_avanzada_solr=0){	   
    $parent = "";
    $options = array();
	$options[0]= t('Any');
	$indefinida_tid=my_get_term_data_tid('Categoria indefinida');
    foreach ($tree as $term) {
		if($term->tid==$indefinida_tid){
			$options[$term->tid]= red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$term->name);
			continue;
		}
        if ($term->depth == 0) {
            $parent = $term->name;
            $parent=red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$parent);
			$options[$parent][$term->tid] = red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$term->name);
        }
        else {
            if ($term->depth> 1) {
                $options[$parent][$term->tid] = red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,str_repeat('-', $term->depth) . ' ' . $term->name);
            }
            else {
                $options[$parent][$term->tid] = red_solr_inc_get_tipo_fuente_options_label($is_busqueda_avanzada_solr,$term->name);   
            }   
        }
    }
    return $options;
}
function my_get_request_value($f,$is_zenb=0){
	if(isset($_REQUEST[$f]) && !empty($_REQUEST[$f])){
		$s="";
		/*if(in_array($f,array('fecha_inicio','fecha_fin'))){
			$s=" 00:00:00";
		}
                return $_REQUEST[$f].$s;*/
                return $_REQUEST[$f];
	}
	if($is_zenb){
		return 0;
	}
	return "";
}
function my_is_busqueda(){
	if(isset($_GET['search'])){
		foreach($_GET as $f=>$v){
			if(in_array($f,array('fecha_inicio','fecha_fin'))){
				//
			}else{
				if(!empty($v)){
					return 1;
				}
			}
		}
		//
		$ini=get_fecha_filter();
		if(!empty($ini)){
			return 1;
		}
		$fin=get_fecha_filter("fecha_fin");
		if(!empty($fin)){
			return 1;
		}
	}
	return 0;
}
function my_get_search_index_word($search){
	/*$result="(search_index.word = '".$search."')";
	$len=strlen($search);
	if($len>0){
		$last=substr($search,$len-1);
		if(strcmp($last,"*")==0){
			$v=substr($search,0,$len-1);
			//print $v;
			$result="(search_index.word LIKE '".$v."%%')";
		}
	}
	return $result;*/
	$result=array();
	//$result[]="(search_index.word LIKE '%%".$search."%%')";
	$result[]="(comments.subject LIKE '%%".$search."%%')";
	$result[]="(comments.comment LIKE '%%".$search."%%')";
        $result[]="(node.title LIKE '%%".$search."%%')";
        $result[]="(node_revisions.body LIKE '%%".$search."%%')";
	return "(".implode(" OR ",$result).")";
}
//intelsat-2016
//function my_get_busqueda_simple_content($is_publico=0){
function my_get_busqueda_simple_content($is_publico=0,$is_destination=0){
    //intelsat-2015
    $my_grupo=og_get_group_context();
    $my_grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;        
    }
    $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
    //
    $is_organic_group_access=repase_organic_group_access();
    if(!$is_organic_group_access || !$is_publico){
        //intelsat-2015
        if(hontza_canal_rss_is_publico_activado()){
            if(publico_is_pantalla_publico('vigilancia')){
                if(!$is_publico){
                    return '';
                }    
            }else{
                if(!$is_organic_group_access){
                    return '';
                }    
            }
        }else{
            if(!$is_organic_group_access){
                return '';
            }
        }    
    }
    //gemini-2014
    if(!red_funciones_is_show_shared_block_menu_left()){
        return '';
    }
    //intelsat-2015
    //intelsat-2016
    if(hontza_solr_is_resultados_pantalla() && !$is_destination){        
        return '';
    }
	$value='';
	$is_busqueda_simple=my_get_request_value('is_busqueda_simple',1);
	if(!empty($is_busqueda_simple)){
		$value=my_get_request_value('search');
	}
	//
	$html='<input type="text" id="busqueda_simple_txt" name="busqueda_simple_txt" value="'.$value.'"/>';
	$html.='<BR><input type="button" id="busqueda_simple_btn" name="busqueda_simple_btn" value="'.t('Search').'"/>';
	//intelsat-2014
        $is_solr_activado=hontza_solr_is_solr_activado();
        if($is_solr_activado){
            if(!empty($my_grupo_nid)){
                //intelsat-2016
                if($is_destination){
                    $result=red_solr_get_index_action_form_remaining_confirm_destination_url($my_grupo,$query_busqueda_avanzada_solr);
                    return $result;
                }else{
                    $html.='&nbsp;'.l(t('Advanced'),'hontza_solr/busqueda_avanzada_solr',array('query'=>$query_busqueda_avanzada_solr));
                }
            }else{
                $html.='&nbsp;'.l(t('Advanced'),'hontza_solr/busqueda_avanzada');
            }    
        }else{
            $advanced_url='busqueda';
            if($is_publico){
                $advanced_url='publico/vigilancia/busqueda_avanzada';
            }
            $html.='&nbsp;'.l(t('Advanced'),$advanced_url);            
        }
        //$html.='&nbsp;'.l('Advanced','busqueda',array('query'=>'is_avanzada=1'));       
	return $html;
}
function add_js_busqueda_simple(){
        $is_publico=0;
        if(hontza_canal_rss_is_publico_activado()){
            if(publico_is_pantalla_publico('vigilancia')){
                $is_publico=1;
            }
        }
        $url_busqueda='';
        if($is_publico){
            $url_busqueda=url('publico/vigilancia/busqueda');
        }else{
            $url_busqueda=url('busqueda');
        }
        //intelsat-2014
        $is_solr_activado=hontza_solr_is_solr_activado();
        //$url_busqueda_solr_simple=url('my_solr/my_search/');
        $url_busqueda_solr_simple=url('hontza_solr/busqueda_simple/');
        //intelsat-2014    
        $solr_param=hontza_solr_get_busqueda_simple_solr_param_js();
        //
        $is_show_borrar=is_show_borrar_carpeta_dinamica($my_query,$out_node);
	$js='var is_solr_activado='.$is_solr_activado.';
			var url_busqueda_simple="'.$url_busqueda.'";
			var url_busqueda_solr_simple="'.$url_busqueda_solr_simple.'";
                        var solr_param="'.$solr_param.'";
                        var is_busqueda_simple='.my_get_request_value('is_busqueda_simple',1).';
			var is_show_borrar='.$is_show_borrar.';
			$(document).ready(function()
			{			
			 	$("#busqueda_simple_btn").click(function(){
					var v=$("#busqueda_simple_txt").attr("value");
                                        if(is_solr_activado){
                                            location.href=url_busqueda_solr_simple+v+solr_param;
                                        }else{
                                            location.href=url_busqueda_simple+"?search="+v+"&is_busqueda_simple=1";
                                        }    
				});
				//
				if(is_busqueda_simple==1){
					//$("#block-stored-views-save").hide();
					if(is_show_borrar==1){						
						var simple_fields_array=new Array("keys","title","body","title_1","body_1","title_2","tipo","categoria_canal","etiquetas","comentado","fecha_inicio","fecha_fin");					
						for(i in simple_fields_array){
							$(".filter-"+simple_fields_array[i]).hide();
						}
						$("#edit-submit-canal-busqueda").hide();
                                                //
                                                $("#my_actualizar_btn").click(function(){
                                                    var v=$("#busqueda_simple_txt").attr("value");
                                                    var nora=url_busqueda_simple+"?search="+v+"&is_busqueda_simple=1&is_my_submit=1";
                                                    var a=$("#edit-is-carpeta-noticia-publica").attr("checked");
                                                    if(a){
                                                        nora=nora+"&is_carpeta_noticia_publica=1";
                                                    }
                                                    var b=$("#edit-is-carpeta-noticia-destacada").attr("checked");
                                                    if(b){
                                                        nora=nora+"&is_carpeta_noticia_destacada=1";
                                                    }
                                                    location.href=nora;
                                                    //alert(nora);
                                                });
					}else{
						$(".views-exposed-form").hide();
					}
				}
			});';
			
			drupal_add_js($js,'inline');
}
function my_help_besteak(){
	/*if(strcmp(arg(0),'user-gestion')==0 && strcmp(arg(1),'grupos')==0 && strcmp(arg(2),'propios')==0 ){
		return help_popup_window('', 'help',my_get_help_link_object());						
	}*/
        if(is_ficha_node_post_formulario()){
            return my_help_ficha_node_post_formulario();
        }else if(is_respuestas_a_retos()){
            return my_help_respuesta_a_retos();
        }else if(strcmp(arg(0),'user-gestion')==0){
		return my_help_user_gestion();
        }else if(is_crear_modificar_categorias()){
            return my_help_crear_modificar_categorias();
	}else if(is_crear_noticia_usuario()){
            return my_help_crear_noticia_usuario();
	}else if(is_crear_canal_de_supercanal()){
            return my_help_crear_canal_de_supercanal();
        }else if(is_crear_wiki()){
            return my_help_crear_wiki();
        }else if(is_crear_debate()){
            return my_help_crear_debate();
        }else if(is_alerta_user()){
            return my_help_alerta_user();
        }else if(strcmp(arg(0),'node')==0){
		return my_help_node();
	}else if(strcmp(arg(0),'user')==0){
		return my_help_user();
	}else if(strcmp(arg(0),'mis-contenidos')==0){
		return my_help_mis_contenidos();
	}else if(strcmp(arg(0),'mis-grupos')==0){
		return my_help_mis_grupos();
	}else if(strcmp(arg(0),'faq')==0){
		return my_help_faq();
	}else if(is_area_debate()){
		return my_help_area_debate();
	}else if(is_estrategia()){            
            return my_help_estrategia();
        }else if(is_despliegue()){
            return my_help_despliegue();
        }else if(is_decision()){
            return my_help_decision();
        }else if(is_informacion()){
            return my_help_informacion();
        }else if(is_estadisticas()){
            return my_help_estadisticas();
        }else if(is_docs()){
            return my_help_docs();
        }else if(is_area_trabajo()){
            return my_help_area_trabajo();
	}else if(is_idea()){
            return my_help_idea();
        }else if(is_oportunidad()){
            return my_help_oportunidad();
        }else if(is_proyecto()){
            return my_help_proyecto();
        }else if(is_usuarios_estadisticas()){
            return my_help_usuarios_estadisticas();
        }else if(is_carpeta_dinamica()){
            return my_help_carpeta_dinamica();
        }else if(is_usuarios_estadisticas(0, 'usuarios_captacion_informacion')){
            return my_help_usuarios_captacion_informacion();
        }else if(is_usuarios_estadisticas(0, 'usuarios_aportacion_valor')){
            return my_help_usuarios_aportacion_valor();
        }else if(is_usuarios_estadisticas(0, 'usuarios_generacion_ideas')){
            return my_help_usuarios_generacion_ideas();
        }else if(is_url_frases_post_formulario()){
            return my_help_frases_post_formulario();
        }else if(hontza_solr_is_busqueda_avanzada_pantalla()){
            return hontza_solr_get_help_busqueda_avanzada();
        }
	return '';
}
function my_help_user_gestion(){
	if(strcmp(arg(1),'grupos')==0){
		if(strcmp(arg(2),'propios')==0 ){
			return help_popup_window(12730, 'help',my_get_help_link_object());
		}							
	}else if(strcmp(arg(1),'usuarios-grupos')==0){
		return help_popup_window(12731, 'help',my_get_help_link_object());
	}
	return '';
}
function my_help_node(){
	if(strcmp(arg(1),'add')==0){
		if(strcmp(arg(2),'grupo')==0){
			return help_popup_window(12732, 'help',my_get_help_link_object());
                }else if(is_estrategia()){
                    return my_help_estrategia();
                }else if(is_despliegue()){
                    return my_help_despliegue();
                }else if(is_decision()){
                    return my_help_decision();
                }else if(is_informacion()){
                    return my_help_informacion();
                }else if(is_idea()){
                    return my_help_idea();
                }else if(is_oportunidad()){
                    return my_help_oportunidad();
                }else if(is_proyecto()){
                    return my_help_proyecto();
                }
	}else if(is_estrategia()){            
            return my_help_estrategia();
        }else if(is_despliegue()){
            return my_help_despliegue();
        }else if(is_decision()){
            return my_help_decision();
        }else if(is_informacion()){
            return my_help_informacion();
        }else if(is_idea()){
            return my_help_idea();
        }else if(is_oportunidad()){
            return my_help_oportunidad();
        }else if(is_proyecto()){
            return my_help_proyecto();
        }
	return '';
}
function my_help_user(){
	$param=arg(2);
	$tres='';	
	if(is_numeric(arg(1))){
		if(empty($param)){		
			return help_popup_window(12733, 'help',my_get_help_link_object());
		}else if(strcmp($param,'edit')==0){
			$tres=arg(3);
			if(empty($tres)){	
				return help_popup_window(12734, 'help',my_get_help_link_object());
			}else if(strcmp($tres,'groups')==0){
				return help_popup_window(12735, 'help',my_get_help_link_object());
			}else if(strcmp($tres,'Datos personales')==0){
				return help_popup_window(12736, 'help',my_get_help_link_object());
			}else if(strcmp($tres,'Empresa')==0){
				return help_popup_window(12737, 'help',my_get_help_link_object());
			}
		}else if(strcmp($param,'bookmarks')==0){
			return help_popup_window(12738, 'help',my_get_help_link_object());
		}else if(strcmp($param,'features')==0){
			$tres=arg(3);
			if(empty($tres)){	
				return help_popup_window(12783, 'help',my_get_help_link_object());
			}else if(strcmp($tres,'overrides')==0){
				return help_popup_window(12784, 'help',my_get_help_link_object());
			}
		}else if(strcmp($param,'suscripciones')==0){
			//return help_popup_window(12785, 'help',my_get_help_link_object());
			return my_help_suscripciones();
		}else if(strcmp($param,'contact')==0){
			return help_popup_window(12786, 'help',my_get_help_link_object());
		}else if(strcmp($param,'imce')==0){
			return help_popup_window(12787, 'help',my_get_help_link_object());
		}else if(strcmp($param,'devel')==0){
			return help_popup_window(12788, 'help',my_get_help_link_object());
		}
	}else if(strcmp(arg(1),'suscripciones')==0){
		return my_help_suscripciones();
	}
	//
	return '';
}
function my_help_mis_contenidos(){
	$param=arg(1);
	if(empty($param)){
		return help_popup_window(12789, 'help',my_get_help_link_object());
	}else if(strcmp($param,'fuentes')==0){
		return help_popup_window(12790, 'help',my_get_help_link_object());
	}else if(strcmp($param,'canales')==0){
		return help_popup_window(12791, 'help',my_get_help_link_object());
	}else if(strcmp($param,'items')==0){
		return help_popup_window(12792, 'help',my_get_help_link_object());
	}else if(strcmp($param,'debates')==0){
		return help_popup_window(12793, 'help',my_get_help_link_object());
	}else if(strcmp($param,'area-trabajo')==0){
		return help_popup_window(12794, 'help',my_get_help_link_object());
	}
	//
	return '';
}
function my_help_mis_grupos(){
	return help_popup_window(12795, 'help',my_get_help_link_object());
}
function my_help_suscripciones(){
	return help_popup_window(12785, 'help',my_get_help_link_object());
}
function my_help_faq(){
	return help_popup_window(12796, 'help',my_get_help_link_object());
}
function my_get_rows_validadas_dash($vars_in){
	$vars=$vars_in;
	
	$html='';	
	if(isset($vars['view']->result) && count($vars['view']->result)>0){	
		foreach($vars['view']->result as $i=>$r){
			$node=node_load($r->nid);
			$links=flag_link('node',$node);
			//echo print_r($links,1);exit();
			if(empty($i)){
				$html.='<div class="views-row views-row-'.($i+1).' views-row-odd views-row-first">';
			}else{
				$last='';
				$my_mod=($i % 2 );
				$even='even';
				if(empty($my_mod)){
					$even='odd';
				}
				
				if($i==(count($vars['view']->result)-1)){
					$last=' views-row-last';
				}
				//print $even.'<BR>';
				$html.='<div class="views-row views-row-2 views-row-'.$even.$last.'">';
			}
			$html.='<div class="views-field-nothing">';
			$html.='<span class="field-content">';
			//intelsat-2015
                        //$html.=$links['flag-leido_interesante']['title'];
			$html.=hontza_canal_rss_set_flag_link($links['flag-leido_interesante']['title']);
                        //$html.=$links['flag-leido_no_interesante']['title'];
			$html.=hontza_canal_rss_set_flag_link($links['flag-leido_no_interesante']['title']);
                        $html.=l($r->node_title,'node/'.$r->nid);
			$html.='</span>';
			$html.='</div>';
			$html.='</div>';
		}
		
		
		
		
		/*print '#########################################<BR>';
		print $vars['rows'];
		print '#########################################<BR>';
		exit();*/
		$vars['rows']=$html;
	}	
	return $vars['rows'];
}
function my_get_og_grupo_list($user_id='',$all=0,$grupo_nid=0,$is_set_primer=1,$is_grupo_local=0,$is_resto_grupos=0,$is_visualizador_grupos_colaborativos=0){
	global $user;
	$uid=$user_id;
	if(empty($uid)){
		$uid=$user->uid;
	}
        //intelsat-2015
        $is_publico=0;
        if(!hontza_canal_rss_is_visualizador_activado() || $is_visualizador_grupos_colaborativos){    
            if(empty($uid)){
                if($is_resto_grupos){
                    if(hontza_canal_rss_is_publico_activado()){
                        $is_publico=1;
                    }
                }
            }
        }    
        //
	$my_result=array();	
	if(!empty($uid) || $is_publico){
		$where=array();
		$where[]='1';
                //intelsat-2015
                if(!$is_publico){
                    $where[]='og_uid.uid='.$uid;                
                }else{
                    if($is_visualizador_grupos_colaborativos){
                        $where[]='term_data.vid=6';
                        $where[]='term_data.tid IN(27,29)';
                    }
                }
                //
                $where[]='og_uid.is_active=1';
                $where[]='n.type="grupo"';
                if(!$all){
                    $my_grupo=og_get_group_context();
                    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
                            $where[]="(og_uid.nid != ".$my_grupo->nid.")";
                    }
                }
		$sql='SELECT n.nid,n.title,purl.value
                FROM {node} n
                LEFT JOIN {og_uid} og_uid ON n.nid=og_uid.nid
                LEFT JOIN {purl} purl ON n.nid=purl.id
                LEFT JOIN {term_node} ON n.vid=term_node.vid 
                LEFT JOIN {term_data} ON term_node.tid=term_data.tid     
                WHERE '.implode(' AND ',$where).' GROUP BY n.nid ORDER BY n.title ASC';
		//print $sql;
		$result = db_query($sql);
                $kont=0;
		  while ($row = db_fetch_object($result)) {
                        $node=node_load($row->nid);
                        //intelsat-2016
                        if(hontza_grupos_mi_grupo_is_show_grupo_colaborativo_observatorio($is_visualizador_grupos_colaborativos,$node)){
                            $my_result[$kont]=$row;
                            $my_result[$kont]->type_of_group=hontza_get_type_of_group($node,'',$group_tid);
                            $my_result[$kont]->type_of_group_tid=$group_tid;
                            $my_result[$kont]->users_in_group=get_group_member_count($row);
                            $my_result[$kont]->subject_area=hontza_grupos_get_subject_area_by_row($row);
                            if($is_grupo_local){
                                $my_result[$kont]->is_grupo_local=1;
                            }
                            //intelsat-2015
                            hontza_grupos_mi_grupo_set_tabs_activados_remoto($node,$row,$my_result[$kont]);
                            $kont++;
                        }    
		  }
	}
	if(!empty($grupo_nid) && $is_set_primer){
            $my_result=hontza_set_primer_grupo_el_seleccionado($my_result,$grupo_nid);
        }
        return $my_result;
}
function my_resto_grupos_html(){
        $html='';
	$my_list=my_get_og_grupo_list();
        if(count($my_list)>0){
		foreach($my_list as $i=>$row){
			//$my_link=l($row->title,'dashboard');
			
			//$my_link='<a href="/hontza3/node/'.$row->nid.'">'.$row->title.'</a>';
			$my_url='http://'.$_SERVER['HTTP_HOST'].base_path();
			$my_link='<a href="'.$my_url.'node/'.$row->nid.'">'.$row->title.'</a>';
			$html.='<div id="gruponame tipo_grupo_'.$row->type_of_group.'">                        
				<a id="context-block-region-nombredelgrupo" class="context-block-region"></a>
				<div class="block block-hontza block-even region-odd clearfix " id="block-hontza-5">
					<div class="content">'.$my_link
						.'<a class="context-block editable edit-vigilancia" id="context-block-hontza-5"></a>  
					</div>
				</div>
			</div>';
			//$html.=$html;
		}
	}
	return $html;
}
function my_is_existe_enlace_origen($my_url){	
	$konp='http://'.$_SERVER['HTTP_HOST'].base_path().'node/';
	if(strcmp($my_url,$konp)==0){
		return 0;
	}
	return 1;
}
function no_existe_enlace_origen_callback($js=FALSE){
	//$html='<p>'.t(utf8_encode('The discussion has not source link')).'</p>';
        $param=arg(1);
        if(!empty($param) && in_array($param,array('item'))){
            $result=t('The news has not source link');
        }else{
            $result=t('The discussion has not source link');
        }
        $html='<p>'.$result.'</p>';	
        return $html;
}
function no_existe_enlace_origen_debate_callback($js=FALSE){
        drupal_set_title(t('Origin of Discussion'));
	$html='<p>'.t('The discussion has no link to any news').'</p>';
	return $html;
}
function is_add_primera_ayuda($my_kont,$type='vigilancia'){
	if(empty($my_kont)){
		if(strcmp($type,'vigilancia')==0){
			if(is_vigilancia()){
				return 1;
			}
		}else if(strcmp($type,'idea')==0){
			if(is_idea()){
				return 1;
			}
		}else if(strcmp($type,'oportunidad')==0){
			if(is_oportunidad()){
				return 1;
			}
		}else if(strcmp($type,'proyecto')==0){
			if(is_proyecto()){
				return 1;
			}
		}else if(strcmp($type,'estrategia')==0){
			if(is_estrategia()){
				return 1;
			}
		}else if(strcmp($type,'despliegue')==0){
			if(is_despliegue()){
				return 1;
			}
		}else if(strcmp($type,'decision')==0){
			if(is_decision()){
				return 1;
			}
		}else if(strcmp($type,'informacion')==0){
			if(is_informacion()){
				return 1;
			}
		}else if(strcmp($type,'chat')==0){
			if(is_chat()){
				return 1;
			}
		}												
		return 0;
	}
	return 0;
}
function my_help_primera_noticia($node){
	if(is_node_primera_ayuda($node)){
		if(is_add_primera_ayuda(0)){
			$my_is_mouse_over=1;
			//$my_is_mouse_over=0;
			//return help_popup_window(13179, 'help',my_get_help_link_object(),0,0,2,$my_is_mouse_over);
			return help_popup_window(13179, 'help',my_get_help_link_object(),0,0,3,$my_is_mouse_over);
		}
	}	
	return '';
}
function is_node_primera_ayuda($node,$types=array('item','noticia')){
	//if(in_array($node->type,array('item','noticia'))){
	if(in_array($node->type,$types)){	
		if(isset($node->view) && isset($node->view->result) && count($node->view->result)>0){
			$konp_nid=$node->view->result[0]->nid;
			if($node->nid==$konp_nid){
				return 1;
			}
		}
	}	
	return 0;
}
function my_get_rows_home_publica($vars_in){
	$vars=$vars_in;
	
	$html='';
	//echo print_r($vars['view'],1);
	
	//simulatzeko
	//$vars=simulatu_vars_home_publica($vars);
	//
	//echo print_r($vars,1);	
	if(isset($vars['view']->result) && count($vars['view']->result)>0){							
		foreach($vars['view']->result as $i=>$r){
			//echo print_r($r,1);			
			if(empty($i)){
				$html.='<div class="views-row views-row-'.($i+1).' views-row-odd views-row-first">';
			}else{
				$last='';
				$my_mod=($i % 2 );
				$even='even';
				if(empty($my_mod)){
					$even='odd';
				}
				
				if($i==(count($vars['view']->result)-1)){
					$last=' views-row-last';
				}
				//print $even.'<BR>';
				$html.='<div class="views-row views-row-2 views-row-'.$even.$last.'">';
			}
			$html.='<div class="views-field-nothing">';
			$html.='<span class="field-content">';
			//$html.=l($r->node_title,'node/'.$r->nid);
			$html.=l($r->node_title,'my_noticia_de_usuario/'.$r->nid);
			$html.='</span>';
			$html.='</div>';
			$html.='</div>';			
		}
		//echo print_r($vars['view']->result,1);
		$vars['rows']=$html;
	}
        //gemini-2013
        //AVISO::::hasta averiguar porque las noticias de usuario van a la home publica
        //$vars['view']->result=array();
        $vars['rows']='';
	//
        return $vars['rows'];
}
function my_noticia_de_usuario_callback($js=FALSE){
    	$html='';	
	if(strcmp(arg(0),'my_noticia_de_usuario')==0){
		$nid=arg(1);
		hontza_og_access($nid);
		if(!empty($nid) && is_numeric($nid)){
			$node=node_load($nid);
			//echo print_r($node,1);
			if(isset($node->nid) && !empty($node->nid)){
				$username='anonymous';
				if(isset($node->uid) && !empty($node->uid)){	
					$my_user=user_load($node->uid);
					if(isset($my_user->uid) && !empty($my_user->uid)){	
						$username=$my_user->name;
					}
				}
				$html.='<h1 class="title">'.$node->title.'</h1>';
				$html.='<div style="clear:both;" class="clearfix" id="content">';
            	$html.='<div class="node clearfix node-noticias-portada node-full published promoted sticky without-photo " id="node-'.$node->nid.'">';
    			$html.='<div class="meta">';
                $html.='<p>'.t('Submitted by').' '.$username.' '.t('on').' '.format_date($node->created).'</p>';
                $html.='</div>';
  				$html.='<div class="content clearfix">';
    			$html.='<div class="field field-type-text field-field-noticias-portada-texto">';
   			    $html.='<div class="field-items">';
            	$html.='<div class="field-item odd">';
				$html.=$node->body;
                $html.='</div></div></div></div></div></div>';  
			}			
		}
	}
	return $html;
}
function simulatu_vars_home_publica($vars_in){
	$vars=$vars_in;
	if(!isset($vars['view']->result) || count($vars['view']->result)<1){
		$vars['view']->result=array();
		$node=node_load(5928);
		$vars['view']->result[0]->nid=$node->nid;
		$vars['view']->result[0]->node_title=$node->title;
	}
	//echo print_r($vars['view']->result,1);
	//
	return $vars;
}
function my_class_primera_noticia($node,$page){
    return '';
	if(empty($page) && is_node_primera_ayuda($node)){
		return ' my_primera_noticia';
	}
	return '';
}
function my_get_gestionar_servicios_content(){
	global $user;
	if(isset($user->uid) && !empty($user->uid)){
            $result=array();
            if($user->uid==1){
                    //$result[]=l(utf8_encode('Categoría de Servicios'),'admin/content/taxonomy/4');
                    //$result[]=l('Categoría de Servicios','admin/content/taxonomy/4');
                    $result[]=l(t('Services'),'admin/content/taxonomy/4');
                    //$result[]=l('Empresas','gestion/servicios');
                    $result[]=l(t('Experts'),'panel_admin/servicios');
            }else{
                    $my_user=user_load($user->uid);
                    if(isset($my_user->profile_empresa) && !empty($my_user->profile_empresa)){
                            $node=node_load(array('title'=>$my_user->profile_empresa));
                            if(isset($node->nid) && !empty($node->nid)){
                                    $result[]=l(t('Organisation'),'node/'.$node->nid);
                            }
                    }
            }
            return implode('<BR>',$result);
        }
        return '';
}
function is_menutop_servicios(){	
	if(strcmp(arg(0),'servicios')==0){
		return 1;
	}else if(strcmp(arg(0),'cat_servicio_view')==0){
		return 1;
	}else if(strcmp(arg(0),'gestion')==0 && strcmp(arg(1),'servicios')==0){
		return 1;
	}else if(strcmp(arg(0),'admin')==0 && strcmp(arg(1),'content')==0 && strcmp(arg(2),'taxonomy')==0 && is_numeric(arg(3))){
		//return my_is_vocabulary(arg(3),utf8_encode('Categoría Servicios'));
                return my_is_vocabulary(arg(3),'Categoría Servicios');
	}else if(is_ficha_node('servicio')){
		return 1;
	}else if(is_term_edit_orig('Categoría Servicios')){
		return 1;
	}else if(is_term_view_orig('Categoría Servicios')){
		return 1;
	//gemini-2014                
        }else if(red_is_facilitadores_en_intercambiar_recursos()){
            return 1;
        }
	
	return 0;
}
function is_tid_in_vocabulary($tid,$field_array,$vid_in=''){
	$konp='';
	$vid='';
	if(isset($field_array['name'])){
            //$konp=utf8_encode($field_array['name']);
            $konp=$field_array['name'];
	}
	//
	if(!empty($vid_in)){
		$vid=$vid_in;
	}
	//
	if(!empty($tid)){
		$term=taxonomy_get_term($tid);
		if(!empty($term) && isset($term->vid) && !empty($term->vid)){
			$vid=$term->vid;
		}
	}
	if(!empty($vid)){		
		if(!empty($konp)){
			return my_is_vocabulary($vid,$konp);
		}else if($vid==$field_array['vid']){
			return 1;
		}
	}
	return 0;	
}
function is_term_edit_orig($voc_name){
	$tid='';
	if(strcmp(arg(0),'admin')==0  && strcmp(arg(1),'content')==0  && strcmp(arg(2),'taxonomy')==0  && strcmp(arg(3),'edit')==0  && strcmp(arg(4),'term')==0  && is_numeric(arg(5))){
		$tid=arg(5);
		return is_tid_in_vocabulary($tid,array('name'=>$voc_name));
	}
	//
	if(strcmp(arg(0),'admin')==0  && strcmp(arg(1),'content')==0  && strcmp(arg(2),'taxonomy')==0  && is_numeric(arg(3)) && strcmp(arg(4),'add')==0  && strcmp(arg(5),'term')==0){
		$vid=arg(3);
		return is_tid_in_vocabulary('',array('name'=>$voc_name),$vid);
	}	
	return 0;
}
function my_get_sidebar_left($left_in){
	//OHARRA::::my_get_body_attributes ere begiratu page.tpl-tik ere deitzen da ta
        //intelsat-2015
        if(hontza_canal_rss_is_publico_activado()){
            if(publico_is_pantalla_publico('vigilancia')){
                
            }else{
                if(hontza_is_user_anonimo()){
                    return '';
                }
            }
        }else{
            if(hontza_is_user_anonimo()){
                return '';
            }
        }    
        //
	$left=$left_in;
	if(empty($left) || is_term_view_orig('Categoría Servicios')){
		if(is_term_edit_orig('Categoría Servicios') || is_term_view_orig('Categoría Servicios')){
			$left='<a id="context-block-region-left" class="context-block-region"></a>';
    		//
			$delta_array=array(33,34);
			if(count($delta_array)>0){
				foreach($delta_array as $i=>$delta){
					$my_mod=($i % 2 );
					$even='even';
					if(empty($my_mod)){
						$even='odd';
					}
					//
					//print 'delta='.$delta.'<BR>';
					$left.=my_create_block_html($delta,$even);
				}
			}
		}else if(empty($left)){
			if(is_area_debate()){
				//return get_area_debate_left();
			}else if(is_alerta_user()){
                            return alerta_get_sidebar_left();
                        }
		}
	}
	return $left;
}
function my_get_title_gestionar_servicios_block(){
	//return t('Gestionar Servicios');
	//return t('Gestionar Facilitadores');
    return t('Manage');
}
function my_create_block_html($delta,$even){
    global $user;
    if(isset($user->uid) && !empty($user->uid)){
	$help_block='';
	switch($delta){
		case 11:
			$help_block=help_popup_block(2945);
			break;
		case 33:
			$help_block=help_popup_block(14116,3);
			break;
		case 34:
			$help_block=help_popup_block(14117,3);
			break;
		case 'og_area_debate_my_block':
			$help_block=help_popup_block(14115,3);
			break;	
		default:
			break;		
	}
	//
	$html='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-'.$delta.'">';
        $html.='<h3 class="title">'.my_get_title_by_delta($delta).$help_block.'</h3>';
        $html.='<div class="content">';
  	$html.=my_get_content_by_delta($delta);
	$html.='</div></div>';
	return $html;
    }
    return '';
}
function my_get_title_by_delta($delta){
	$title='';
	switch($delta){
		case 11:
			$title=my_get_title_anadir_debate_block();
			break;
		case 33:
			$title=my_get_title_gestionar_servicios_block();
			break;
		case 34:
			$title=my_get_title_tipos_de_servicios_block();
			break;
		case 'og_area_debate_my_block':
			$title=my_get_title_area_debate_block();
			break;		
		default:
			break;
	}
	return $title;
}
function my_get_content_by_delta($delta){
	$content='';
	switch($delta){
		case 11:
			$content=my_get_anadir_debate_content();
			break;
		case 33:
			$content=my_get_gestionar_servicios_content();
			break;
		case 34:
			$content=my_get_gestionar_tipos_de_servicios_content();
			break;
		case 'og_area_debate_my_block':
			$content=my_get_area_debate_content();
			break;		
		default:
			break;
	}
	return $content;
}
function my_get_body_attributes($left,$body_attributes_in){    
	$body_attributes=$body_attributes_in;
        //if(!empty($left) && (is_term_edit_orig('Categoría Servicios') || is_area_debate())){
	if(red_funciones_is_body_attributes_left($left)){	
                $my_array=explode('class="',$body_attributes);
		if(count($my_array)>1){
			$beste_array=explode('"',$my_array[1]);
			$class_list=explode(' ',$beste_array[0]);
			if(!in_array('left',$class_list)){
				$class_list[]='left tableHeader-processed admin-menu';
                                $class_string=implode(' ',$class_list);
				$beste_array[0]=str_replace('no-sidebars','',$class_string);
				$my_array[1]=implode('"',$beste_array);
				$body_attributes=implode('class="',$my_array);
				//print $body_attributes;
			}
		}
	}
        if(red_is_iframe()){
            $body_attributes=str_replace('admin-menu','',$body_attributes);
        }
        //intelsat-2015
        $body_attributes=hontza_solr_funciones_set_page_id($body_attributes);
        if(red_solr_inc_is_status_activado()){
            $body_attributes=red_solr_inc_get_body_attributes($body_attributes);
        }
        //
        return $body_attributes;
}
function get_invitado_user_profile($user_profile){
    $group_list=my_get_og_grupo_list('',1);
    if(count($group_list)>0){
        foreach($group_list as $i=>$gr){
            //echo print_r($gr,1);
            //<a href="/hontza3/node/904">AscensoresLift-NProveedores</a>
            $s='<a href="'.base_path().'node/'.$gr->nid.'">'.$gr->title.'</a>';
            $r='<a href="'.base_path().$gr->value.'/ideas">'.$gr->title.'</a>';
            $user_profile=str_replace($s,$r,$user_profile);
        }
    }
    return $user_profile;
}
//intelsat-2016
//function is_mi_perfil($is_empty=1){
function is_mi_perfil($is_empty='',$is_current_user=''){
    global $user;
    if(strcmp(arg(0),'user')==0){
        $param=arg(1);
        if(empty($param)){
            return 1;
        }else if(is_numeric($param)){
            if(($is_current_user && $param==$user->uid) || !$is_current_user){
                $param2=arg(2);
                if($is_empty){
                    if(empty($param2)){
                        return 1;
                    }
                }else{
                    return 1;
                }
            }    
        }
    }else if(strcmp(arg(0),'users')==0){
        $param=arg(1);
        if(!empty($param)){
            if($is_current_user && strcmp($param,$user->name)){
                return 1;
            }else if(!$is_current_user){
                $my_user=user_load(array('name'=>$param));
                if(isset($my_user->uid) && !empty($my_user->uid)){
                    return 1;
                }
            }    
        }
    }
    return 0;
}
function my_resto_grupos_li(){
    global $language,$base_url;
    $my_lang='';
    if($language->language!='en'){
        $my_lang=$language->language.'/';
    }
    //    
	$html='';
        //$my_list=my_get_og_grupo_list();
	$my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid=$my_grupo->nid;
        }
        //intelsat-2015
        $is_resto_grupos=1;
        $is_visualizador_grupos_colaborativos=hontza_canal_rss_is_visualizador_grupos_colaborativos();
        //
        $my_list=my_get_og_grupo_list('',1,$grupo_nid,1,1,$is_resto_grupos,$is_visualizador_grupos_colaborativos);
        $my_list=red_funciones_unset_shared_group($my_list);        
        //if(hontza_is_sareko_id_red(0)){
        //if(hontza_is_sareko_id_red()){
        //if(hontza_is_sareko_user()){
        if(hontza_is_sareko_user() && !custom_menu_red_is_activado()){
            //echo print_r($my_list,1);
            //$grupos_red_alerta=red_get_user_grupos_red_alerta();
            if(hontza_is_servidor_red_alerta()){
                $grupos_red_alerta=red_servidor_grupo_get_grupos_red_alerta();
                //echo print_r($grupos_red_alerta,1);
            }else{
                $grupos_red_alerta=red_compartir_grupo_get_user_grupos_red_alerta();
                //echo print_r($grupos_red_alerta,1);
            }            
            $my_list=array_merge($my_list,$grupos_red_alerta);
            
        }
        //intelsat-2015
        $my_list=custom_menu_set_max_title_tabs($my_list,1);
        //
        //intelsat-2015
        $ul_my_resto_grupos_class=custom_menu_get_ul_my_resto_grupos_class();
        $div_resto_grupos='<div id="div_my_resto_grupos"><ul id="ul_my_resto_grupos" class="ul_menu_desplegable'.$ul_my_resto_grupos_class.'">';
        $num_grupos=count($my_list);
        if($num_grupos>0){
            /*if(user_access('root')){
                echo print_r($my_list,1);
            }*/    
                //intelsat-2015
		$html.=$div_resto_grupos;
		//$kont=0;
		//for($k=0;$k<6;$k++){
		$barra='|';
		$hasi_orri='/dashboard';
                if(is_user_invitado()){
                    $hasi_orri='/ideas';
                }
                /*
                //intelsat-2015
                if(hontza_canal_rss_is_visualizador_activado()){
                    //$hasi_orri='/visualizador/inicio';
                    $hasi_orri=visualizador_get_inicio_url($hasi_orri);
                }
                //
                */
                $max=7;
                if(hontza_is_sareko_user()){
                    $max=6;
                }
                //gemini-2014                
                $max=red_funciones_get_grupo_menu_max($my_list,$max);
                //                        
                foreach($my_list as $i=>$row){
                        if($i>=$max){
                            	//$my_link=l(utf8_encode('See more'),'mis-grupos');
                                //$my_link=l(t('See more'),'mis-grupos');
                                //intelsat-2015
                                $style=' style="margin-bottom:-23px;"';
                                if(!custom_menu_is_user_grupo($grupo_nid) || $max==0){
                                    $style='';
                                }
                                $my_link=l(my_get_icono_action('ver_mas32',t('See more'),'ver_mas32','',$style),'mis-grupos',array('html'=>TRUE,'attributes'=>array('id'=>'id_ver_mas')));
				//intelsat-2015
                                $menu_desplegable=hontza_canal_rss_get_menu_desplegable();
                                //
                                $ver_mas_html='<li id="li_menu_desplegable" class="li_menu_desplegable">'.$my_link.$menu_desplegable.'</li>';
				if($num_grupos>1){
                                    $html.=$ver_mas_html;
                                    break;
                                }    
			}
			if($i==(count($my_list)-1)){
				$barra='';
			}
			//$my_link=l($row->title,'dashboard');
			//$my_link='<a href="/hontza3/node/'.$row->nid.'">'.$row->title.'</a>';
			//$my_link='<a href="/hontza3/'.$row->value.'/dashboard">'.$row->title.'</a>';			
                        //if(isset($row->sareko_id)){
                        $my_action=$hasi_orri;
                        //intelsat-2015
                        $icono_red_alerta=hontza_get_icono_grupo_red_alerta($row);
                        //
                        $icono_privacidad=hontza_canal_rss_get_icono_grupo_privacidad($row,$title,1,$icono_red_alerta,$title_popup);
                        if(isset($row->is_grupo_red_alerta)){
                            $subdominio='';
                            $user_login_enviar=hontza_define_user_login_enviar();
                            if(hontza_is_grupo_subdominio($row)){
                                $subdominio=$row->subdominio;
                                $my_url='http://'.$row->subdominio.'/';
                                $my_action='red_compartir/authenticate_local';
                            }else{    
                                $my_url=red_compartir_define_redalerta_servidor_url().'/';
                                $my_action='red_servidor/authenticate_red_alerta';
                            }
                            $subdominio=hontza_set_subdominio_id_name($subdominio);
                            $id_a=$subdominio.'login_red_alerta_'.$row->nid;
                            $login_red_alerta_url=$my_url.$my_lang.$my_action.'/'.$row->nid;
                            //$my_link='<a id="'.$id_a.'" class="a_login_red_alerta_class" href="'.$login_red_alerta_url.'?user_login_enviar_get='.base64_encode($user_login_enviar).'&red_idioma='.trim($my_lang,'/').'" title="'.red_funciones_get_grupo_privacidad_name($row).'">'.hontza_get_icono_grupo_congelado($row,0,$is_congelado).$icono_privacidad.hontza_get_icono_grupo_red_alerta($row).$row->title.'</a>';
                            $my_link='<a id="'.$id_a.'" class="a_login_red_alerta_class" href="'.$login_red_alerta_url.'?user_login_enviar_get='.base64_encode($user_login_enviar).'&red_idioma='.trim($my_lang,'/').'" target="_blank" title="'.$title_popup.'">'.hontza_get_icono_grupo_congelado($row,0,$is_congelado).$icono_privacidad.$row->title.'</a>';                            
                            hontza_login_red_alerta_formulario($login_red_alerta_url.'&red_idioma='.trim($my_lang,'/'),$row->nid,1,$subdominio,$user_login_enviar);                            
                        }else{
                            //intelsat-2015
                            //$my_url='http://'.$_SERVER['HTTP_HOST'].base_path();
                            $my_url=$base_url.'/';
                            $my_action=$hasi_orri;
                            $my_action=red_dashboard_get_hasi_orri($my_action);                        
                            //$my_link='<a href="'.$my_url.$my_lang.$row->value.$my_action.'" title="'.red_funciones_get_grupo_privacidad_name($row).'">'.hontza_get_icono_grupo_congelado($row,0,$is_congelado).$icono_privacidad.hontza_get_icono_grupo_red_alerta($row).$row->title.'</a>';
                            $my_link='<a href="'.$my_url.$my_lang.$row->value.$my_action.'" target="_blank" title="'.$title_popup.'">'.hontza_get_icono_grupo_congelado($row,0,$is_congelado).$icono_privacidad.$row->title.'</a>';                                                        
                        }    
                        //                        
			$barra='';
                        //if($row->nid!=$grupo_nid || isset($row->sareko_id)){
                        if($row->nid!=$grupo_nid || isset($row->is_grupo_red_alerta)){
                           if(!hontza_canal_rss_is_custom_menu_activado()){
                                $html.='<li ><div class="gruponame tipo_grupo_'.$row->type_of_group.'"><div class="block block-hontza block-even region-odd clearfix " id="block-hontza-5"> <div class="content">'.$my_link.$barra.'</div></div></div></li>';
                           }     
                                
                        }else{
                            $my_id='gruponame_no tipo_grupo_'.$row->type_of_group;
                            if($i>0){
                                $my_id='gruponame_no tipo_grupo_'.$row->type_of_group;
                            }
                            $html.='<li ><div class="'.$my_id.'">
                                    <a id="context-block-region-nombredelgrupo" class="context-block-region"></a>
                                    <div class="block block-hontza block-even region-odd clearfix " id="block-hontza-5">
                                            <div class="content">'.$my_link
                                                    .'<a class="context-block editable edit-vigilancia" id="context-block-hontza-5"></a>
                                            </div>
                                    </div>
                            </div></li>';
                        }
			//$kont++;
		}
		//}
                //intelsat-2015
                if($num_grupos==1){
                    $html.=$ver_mas_html;
                }
                //
		$html.='</ul></div>';
	}else{
            //intelsat-2015
            $html.=red_crear_usuario_add_ver_mas_usuario_sin_grupos($div_resto_grupos);
        }
        return $html;
}
function add_js_volver_noticia(){
    $js='
        $(document).ready(function()
        {
            $("#my_volver_js").click(function(){
                history.back(-1);
                return false;
            });
        });';

    drupal_add_js($js,'inline');
}