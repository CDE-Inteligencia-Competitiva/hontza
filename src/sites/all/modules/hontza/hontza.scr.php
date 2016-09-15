<?php
//gemini
//OHARRA::::php fitxategi hau geminik sortu du
function my_get_title_tipos_de_servicios_block(){
	//return t(utf8_encode('Categoríaa de Servicios'));
	//return t(utf8_encode('Categoría de Facilitadores'));
        //return t('Categoría de Facilitadores');
    //return t('Categories');
    return t('Services');
}
function my_get_gestionar_tipos_de_servicios_content(){
    global $user;
    //intelsat-2016
    //se ha comentado esto
    /*if(isset($user->uid) && !empty($user->uid)){
	$result=my_get_tipo_de_servicios_li();
	return implode('',$result);
    }*/    
    return '';     
}
function my_get_tipo_de_servicios_li(){
	$result=array();
	$my_array=my_get_tipo_de_servicos_array();
	if(!empty($my_array)){
		foreach($my_array as $i=>$row){
                        //gemini-2014
                        $pro=profundidad($row->tid);                        
                        //$pro=variable_get('profundidad_valor', 0);                        
                        $title=$row->description;
			$term=taxonomy_get_term_by_language($row->tid);
                        if(isset($term->description) && !empty($term->description)){
                            $title=$term->description;
                        }
                        $result[$i]='<li class="nivel'.$pro.'">'.l($row->name.' ('.$row->num_empresas.')','cat_servicio_view/'.$row->tid,array('attributes'=>array('title'=>$title))).'</li>';
		}
	}
	return $result;
}
function my_get_tipo_de_servicos_array(){
	$categorias=array();
	//$vocab=my_vocabulary_load(utf8_encode('Categor�a Servicios'));
        $vocab=my_vocabulary_load('Categoría Servicios');
	if(isset($vocab->vid) && !empty($vocab->vid)){	
		$categorias=taxonomy_get_tree($vocab->vid);
		if(!empty($categorias)){
			foreach($categorias as $i=>$row){
				$categorias[$i]->num_empresas=my_get_num_empresas_by_cat($row->tid);
			}
		}
	}	
	return $categorias;
}
function my_get_num_empresas_by_cat($tid){
	global $language;
        $where=array();
	$where[]='tn.tid='.$tid;
        $where[]='n.type="servicio"';
        $where[]='n.language="'.$language->language.'"';
	//$sql="SELECT COUNT(*) as num_empresas FROM term_node tn WHERE ".implode(" AND ",$where);
        $sql="SELECT COUNT(*) as num_empresas
        FROM term_node tn LEFT JOIN node n ON tn.nid=n.nid
        WHERE ".implode(" AND ",$where);
	//print $sql;
	$result = db_query($sql);
	 
		  while ($row = db_fetch_object($result)) {		
			return $row->num_empresas;
		  }
	
	return 0;
}
function beste_td_content($field,$content,$row_in=array()){
	$result=$content;
	$param=arg(1);
	if(strcmp(arg(0),'servicios')==0 && empty($param)){
		return servicios_td_content($field,$content,$row_in);
	}
	return $result;
}
function beste_form_alter(&$form,&$form_state,$form_id){
    /*if(user_access('root')){
        print $form_id.'<BR>';
    }*/    
        if(strcmp($form_id,'user_profile_form')==0){
            my_user_profile_form_alter($form,$form_state,$form_id);
	}else if(strcmp($form_id,'rss_feed_node_form')==0){
            //print drupal_get_title().'<BR>';
            if(is_new_form($form['nid'])){
                drupal_set_title(t('Add Highlighted News'));
            }
            my_rss_feed_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'noticia_node_form')==0){
            my_set_notica_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'boletin_grupo_introduccion_node_form')==0){
            my_boletin_grupo_introduccion_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'boletin_grupo_despedida_node_form')==0){
            my_boletin_grupo_despedida_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'grupo_node_form')==0){
            //AVISO::::en hontza_grupos.module hontza_grupos_form_alter
            //hontza_grupo_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'fuentehtml_node_form')==0){
            my_fuentehtml_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'servicio_node_form')==0){
            my_servicio_node_form_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'noticias_portada_node_form')==0){
            my_noticias_portada_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'estrategia_node_form')==0){
            estrategia_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'despliegue_node_form')==0){
            despliegue_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'user_login')==0){
            my_user_login_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'user_pass')==0){
            my_user_pass_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'canal_de_yql_node_form')==0){
            my_canal_de_yql_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'supercanal_node_form')==0){
            my_supercanal_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'views_exposed_form')==0){
            my_views_exposed_form_alter($form,$form_state,$form_id);
        //gemini-2013
        }else if(strcmp($form_id,'fuentedapper_node_form')==0){
            my_fuentedapper_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'og_add_users')==0){
            my_og_add_users_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'faq_node_form')==0){
            hontza_faq_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'my_report_node_form')==0){
            boletin_report_my_report_node_form_alter($form,$form_state,$form_id);
            //intelsat-2016
            red_copiar_my_report_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'bulletin_text_node_form')==0){
            boletin_report_bulletin_text_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'decision_node_form')==0){
            decision_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'community_tags_form')==0){
            hontza_community_tags_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_item_node_form')==0){
            social_learning_collections_collection_item_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_resource_node_form')==0){
            social_learning_collections_collection_resource_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_node_form')==0){
            social_learning_collections_collection_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_topic_node_form')==0){
            social_learning_topics_collection_topic_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_feed_node_form')==0){
            social_learning_feeds_collection_feed_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'collection_file_node_form')==0){
            social_learning_files_collection_file_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'banner_node_form')==0){
            panel_admin_banners_banner_node_form_alter($form,$form_state,$form_id);
        //intelsat-2015    
        }else if(strcmp($form_id,'visualizador_contacto_node_form')==0){
            panel_admin_contacts_visualizador_contacto_node_form_alter($form,$form_state,$form_id);
        }else if(strcmp($form_id,'grupo_inicio_node_form')==0){
            if(hontza_canal_rss_is_usuario_basico_activado()){
                usuario_basico_grupo_inicio_node_form_alter($form,$form_state,$form_id);
            }
        }else if(strcmp($form_id,'canal_usuario_node_form')==0){
            canal_usuario_canal_usuario_node_form_alter($form,$form_state,$form_id);
        //intelsat-2016            
        }else if($form_id=='quant_time_form'){
            hontza_grupos_mi_grupo_quant_time_form_form_alter($form,$form_state,$form_id);
        }else if($form_id=='apachesolr_index_action_form_remaining_confirm'){
            red_solr_inc_apachesolr_index_action_form_remaining_confirm_form_alter($form,$form_state,$form_id);
        }
        //intelsat-2015
        red_solr_inc_node_title_length_form_alter($form,$form_state, $form_id);
}
function my_get_rows_gestion_servicios($vars_in){	
	$vars=$vars_in;
	//$sep='<td class="views-field views-field-field-logo-servicios-fid">';
	$sep='<td class="views-field views-field-title">';
	$my_array=explode($sep,$vars['rows']);
	$sep2='src="';
	//echo print_r($vars,1);exit();
	foreach($my_array as $i=>$v){
		/*if($i>0){				
			$beste_array=my_create_new_img($sep2,$v);
			$my_array[$i]=implode($sep2,$beste_array);
		}*/            
		$nid=0;
		if($i>0){
			$nid=$vars['view']->result[$i-1]->nid;
		}
		if(!empty($nid)){
			$value=my_get_logo(array('nid'=>$nid),$node);			
			$beste_array=explode('</td>',$my_array[$i]);
			$b=my_create_new_img($sep2,$value,$is_system);
                        //simulando
                        //$is_system=1;
                        $b=hontza_set_logo_url_facilitadores($b, $is_system);
                        $beste_array[0]=$beste_array[0].'<BR>'.implode($sep2,$b);			
                        $my_array[$i]=implode('</td>',$beste_array);                        
		}
	}        
	$vars['rows']=implode($sep,$my_array);
	$vars['rows']=set_categorias_link($vars);
        //intelsat-2015
        $vars['rows']=red_funciones_set_gestion_servicios_titles($vars['rows']);                
        $vars['rows']=hontza_canal_rss_my_get_rows_gestion_servicios($vars);
        //
        return $vars['rows'];
}
function servicios_td_content($field,$content,$row_in=array()){
	global $base_url;
        $result=$content;
	/*if(strcmp($field,'field_logo_servicios_fid')==0){
		$sep2='src="';
		$beste_array=my_create_new_img($sep2,$result);
		$result=implode($sep2,$beste_array);		
	}*/
	//$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
	//print $field.'<BR>';
	if(strcmp($field,'title')==0){
		//echo print_r($row_in,1);
		/*$node=node_load(array('title'=>$row_in['title']));
		$logo_src='';
		$b='';
		if(isset($node->nid) && !empty($node->nid)){
			if(isset($node->field_logo_servicios[0]['filepath'])){
        		$logo_src=$node->field_logo_servicios[0]['filepath'];
				//print $logo_src.'<BR>';			
				$b='<img src="'.$src_hasi.'/'.$logo_src.'"/>';
			}
		}*/
		$b=my_get_logo(array('title'=>$row_in['title']),$node);				
		//						
		$my_array=my_create_new_img('src="',$b,$is_system);
                $my_array=hontza_set_logo_url_facilitadores($my_array,$is_system);
		$b=implode('src="',$my_array);
		$result=l($content,'node/'.$node->nid).'<BR>'.$b;
	}elseif(strcmp($field,'field_categoria_servicios_value')==0){
		$node=node_load(array('title'=>$row_in['title']));
		$result=my_get_empresa_categorias_html($node->field_categoria_servicios);
	}
	return $result;
}
function my_create_new_img($sep2,$v,&$is_system){
        $is_system=0;
	$max_wa=100;
	//$sep2='src="';	
			$beste_array=explode($sep2,$v);
			if(count($beste_array)>0){			
				$my_array2=explode('"',$beste_array[1]);
                                $path=$my_array2[0];
				$wa='';
				$ha='';
				$info_size=@getimagesize($path);
				if(isset($info_size[0]) && isset($info_size[1])){
					$wa=$info_size[0];
					$ha=$info_size[1];
				}else{
                                    $basename=basename($path);
                                    $dir=variable_get('file_directory_path','');
                                    if(!empty($dir)){
                                        $path=$dir.'/'.$basename;
                                    }
                                    $info_size=@getimagesize($path);
                                    if(isset($info_size[0]) && isset($info_size[1])){
                                            $is_system=1;
                                            $wa=$info_size[0];
                                            $ha=$info_size[1];
                                    }
                                }
				//
				if(!empty($wa) && !empty($ha)){	
					//
					if($wa>$max_wa){						
						$faktore=$max_wa/$wa;
						$wa=$max_wa;
						$ha=$ha*$faktore;
					}
				}
			}
	$beste_array[0]='<img width="'.$wa.'" height="'.$ha.'" class="imagecache imagecache-logotipo imagecache-default imagecache-logotipo_default" title="" alt="" ';					
	//echo print_r($beste_array,1);exit();
	return $beste_array;
}
function cat_servicio_view_callback($js=FALSE){
	$tid=arg(1);
	//
	$term=taxonomy_get_term($tid);
	if(!empty($term) && isset($term->tid) && !empty($term->tid)){
            $term_name=get_term_extra_name($term->tid, '', $term->name);
            $description=$term->description;
            $description=get_term_extra_description($term->tid, '', $description);
		$num_empresas=my_get_num_empresas_by_cat($tid);
		$html='<h1>'.$term_name.' ('.$num_empresas.')</h1>';
		$html.='<div class="node">';
		$html.=$description;
		$html.='</div>';
		$html.='<h3>'.t('List of companies').'</h3>';
		$html.='<div class="view-content">'.my_get_cat_servicio_empresas_li($tid).'</div>';
	}	
	//	
	return $html;
}
function my_get_cat_servicio_empresas_li($tid){
	$result=array();
	$my_array=my_get_cat_servicio_empresas_array($tid);
	$num=count($my_array);
	$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
	$sep2='src="';	
	if($num>0){
		$result[]='<table class="views-table cols-7"><thead><tr>';
      	//$col_array=array('title'=>'Nombre','logo-servicios-fid'=>'','descripcion-servicios-value'=>'','categoria-servicios-value'=>'');
		//$col_array=array('title'=>'Nombre','logo-servicios-fid'=>'','my_description'=>'','categoria-servicios-value'=>'');
		$col_array=array('title'=>'Name','my_description'=>'','categoria-servicios-value'=>'');
		foreach($col_array as $name=>$value){ 
		 	$result[]='<th class="views-field views-field-'.$name.'">';
			$title='';
			if(!empty($value)){
				$title=t($value);
			}
			$result[]=$title.'</th>';
		} 
		$result[]='</thead><tbody>';    
 		
			
		foreach($my_array as $i=>$row){
			//$result[]='<li>'.$row->title.'</li>';
			$garrena='';
			if(empty($i)){
				$garrena=' views-row-first';
			}else if($i==($num-1)){
				$garrena=' views-row-last';
			}
			//
			$my_mod=($i % 2 );
			$even='even';
			if(empty($my_mod)){
				$even='odd';
			}
						
			$tr='<tr class="'.$even.$garrena.'">';
			$result[]=$tr;
			      
   			foreach($col_array as $name=>$value){
				$v='';
				$style='';
				if(strcmp($name,'title')==0){
					$v=l($row->title,'node/'.$row->nid,array('query'=>drupal_get_destination())); 
				//}else if(strcmp($name,'logo-servicios-fid')==0){
					if(!empty($row->logo_src)){
						$v.='<BR>';
						$b='<img src="'.$src_hasi.'/'.$row->logo_src.'"/>';
						$my_array=my_create_new_img('src="',$b,$is_system);
                                                if(isset($my_array[1]) && $is_system){
                                                    $my_array[1]=str_replace('"/>','',$my_array[1]);
                                                    $basename=basename($my_array[1]);
                                                    $my_array[1]=$base_url.'/system/files/'.$basename.'"/>';                    
                                                }
						$b=implode('src="',$my_array);
						$v.=$b;
					}
				}else if(strcmp($name,'my_description')==0){
					//$v=my_prepare_description($row->my_description);
					$v=$row->my_description;
					$v=_filter_url($v,'');
				}else if(strcmp($name,'categoria-servicios-value')==0){
					//echo print_r($row,1);exit();
					if(isset($row->field_categoria_servicios) && count($row->field_categoria_servicios)>0){ 
						$style=' style="white-space:nowrap;"';
						$v=my_get_empresa_categorias_html($row->field_categoria_servicios);
					}
				}					
				//
				$result[]='<td class="views-field views-field-'.$name.'"'.$style.'>'.$v.'</td>';
			}
                  			
			
			//
			/*$s='<div class="views-row views-row-1 views-row-'.$even.$garrena.'">';
      		$s.='<div class="views-field-nothing">';
            $s.='<span class="field-content">';
			$s.=l($row->title,'node/'.$row->nid,array('query'=>drupal_get_destination()));
			$s.='</span></div></div>';
			$result[]=$s;*/
		}
		$result[]='</tbody></table>'; 
	}
	return implode('',$result);
}
function my_get_cat_servicio_empresas_array($tid){
    global $language;
	$vid=4;
	//$vocab=my_vocabulary_load(utf8_encode('Categor�a Servicios'));
	$vocab=my_vocabulary_load('Categoría Servicios');
        if(isset($vocab->vid) && !empty($vocab->vid)){
		$vid=$vocab->vid;
	}
	//print $vid;exit();
	$where=array();
	$where[]='n.type="servicio"';
	$where[]='tn.tid='.$tid;
	$where[]='td.vid='.$vid;
        $where[]='n.language="'.$language->language.'"';
	//	
	$result=array();
	//$sql='SELECT n.* FROM node n  	
	$sql='SELECT n.*, td.description as descripcion_servicios_value FROM node n
	LEFT JOIN term_node tn ON n.nid=tn.nid 
	LEFT JOIN term_data td ON tn.tid=td.tid
	WHERE '.implode(' AND ',$where);
	//
	$res = db_query($sql);
	// 
	$i=0;
	while ($row = db_fetch_object($res)) {
		$node=node_load($row->nid);
		//echo print_r($node,1);exit();				
		$result[$i]=$row;		
		$result[$i]->logo_src='';
		if(isset($node->field_logo_servicios[0]['filepath'])){
        	$result[$i]->logo_src=$node->field_logo_servicios[0]['filepath'];			
		}
		if(isset($node->field_categoria_servicios)){	
			$result[$i]->field_categoria_servicios=$node->field_categoria_servicios;
		}
		if(isset($node->field_descripcion_servicios)){	
			$result[$i]->my_description=$node->field_descripcion_servicios[0]['value'];
		}
		//    
		$i++;
	}
        //print 'i===='.$i.'<BR>';
	//
	return $result;
}
function my_get_empresa_categorias_html($cat_array){
	$where=array();
	//
	//echo print_r($cat_array,1);
	$my_array=array();
	foreach($cat_array as $i=>$row){
		$my_array[]=$row['value'];
	}
	//
	$where[]='td.tid IN ('.implode(',',$my_array).')';
	//echo print_r($where,1);
	$sql="SELECT td.* FROM term_data td WHERE ".implode(" AND ",$where).' ORDER BY td.name ASC';
	//print $sql;
	
	$result = db_query($sql);
	$my_list=array();
	$i=0; 
	while ($row = db_fetch_object($result)) {
                $row_name=get_term_extra_name($row->tid, '', $row->name);
            	$my_list[]='<div class="field-item field-item-'.$i.'">'.l($row_name,'taxonomy/term/'.$row->tid).'</div>';
		$i++;
	}
	//echo print_r($my_list,1);exit();
	//return implode('<BR>',$my_list);
	return implode('',$my_list);
}
function my_get_logo($param,&$node){
	$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
	$node=node_load($param);
	$logo_src='';
	$b='';
	if(isset($node->nid) && !empty($node->nid)){
		if(isset($node->field_logo_servicios[0]['filepath'])){
        	$logo_src=$node->field_logo_servicios[0]['filepath'];
			//print $logo_src.'<BR>';			
			$b='<img src="'.$src_hasi.'/'.$logo_src.'"/>';
		}
	}
	return $b;				
}
function set_categorias_link($vars_in){	
	$vars=$vars_in;
	//$sep='<td class="views-field views-field-field-logo-servicios-fid">';
	$sep='<td class="views-field views-field-field-categoria-servicios-value">';
	$my_array=explode($sep,$vars['rows']);
	foreach($my_array as $i=>$v){		
		$nid=0;
		if($i>0){
			$nid=$vars['view']->result[$i-1]->nid;			
		}
		if(!empty($nid)){
			$node=node_load($nid);
			if(isset($node->field_categoria_servicios) && count($node->field_categoria_servicios)>0){ 
				$value=my_get_empresa_categorias_html($node->field_categoria_servicios);
				$beste_array=explode('</td>',$v);
				$beste_array[0]=$value;
				$my_array[$i]=implode('</td>',$beste_array);
			}			
		}		
	}
	$vars['rows']=implode($sep,$my_array);
	//
	return $vars['rows'];
}
function my_servicio_access($node,$with_return=0){
	global $user;
        if(hontza_is_servidor_red_alerta()){
            if($user->uid==$node->uid && strcmp($node->type,'servicio')==0){
                //if($with_return){
                    return 1;
                //}
            }
        }
        //
        //intelsat-2015
	//if($user->uid!=1 && strcmp($node->type,'servicio')==0){
        if(!red_is_administrador() && strcmp($node->type,'servicio')==0){
		$my_user=user_load($user->uid);
		if(isset($my_user->profile_empresa) && !empty($my_user->profile_empresa)){
			$my_empresa=node_load(array('title'=>$my_user->profile_empresa));
			if($node->nid!=$my_empresa->nid){
				if($with_return){
					return 0;
				}else{
					drupal_access_denied();
					exit();
				}	
			}else{
				if(!is_user_editor_empresa_servicio($user->uid,1)){
					if($with_return){
						return 0;
					}else{
						drupal_access_denied();
						exit();
					}	
				}
			}
		}
	}
	if($with_return){
		return 1;
	}
}
function is_term_view_orig($voc_name){
	$tid='';		
	if(strcmp(arg(0),'taxonomy')==0 && strcmp(arg(1),'term')==0 && is_numeric(arg(2))){		
		$tid=arg(2);			
		$result=is_tid_in_vocabulary($tid,array('name'=>$voc_name));	
		//print "$tid=".$result."<BR>";	
		return $result;
	}	
		
	return 0;
}
function add_js_servicios(){
	//if(is_term_view_orig('Categor�a Servicios')){
        if(is_term_view_orig('Categoría Servicios')){
		my_add_active_trail_js('id_a_servicios');		
	}
}
function get_area_debate_left(){	
	/*$view = views_get_view('og_area_debate_my_block');
	$content = $view->execute_display('block');
	//print $content;
	return $content;*/
	$left='<a id="context-block-region-left" class="context-block-region"></a>';
    		//
			$delta_array=array(11,'og_area_debate_my_block');
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
	return $left;		
}
function my_get_title_anadir_debate_block(){	
    return t('Add Discussion');
}
function my_get_anadir_debate_content(){
    //gemini-2014
    return '';
    //
    global $user;
    if(isset($user->uid) && !empty($user->uid)){
	//intelsat-2015
        return red_funciones_get_create_link(t('Create Discussion'),'node/add/debate');
    }
    return '';
}
function my_get_title_area_debate_block(){	
    return t('Discussion');
}
function my_get_area_debate_content(){
	$view = views_get_view('og_area_debate_my_block');
	$content = $view->execute_display('block');
	return $content;
}
function is_ficha_node_left($konp='item',$my_type=''){
	$node=my_get_node();
	//print $node->type.'<BR>';
	if(!empty($node) && isset($node->nid) && !empty($node->nid)){
		//if(in_array($node->type,array('item'))){
		if(strcmp($node->type,$konp)==0){
			//print $node->type.'<BR>';
			//return 1;
			$param=arg(2);
			if(empty($my_type)){
                            
				if(!empty($param) && in_array($param,array('delete','tag','node_export','devel'))){
					return 1;
				}
			}else if(strcmp($my_type,'canal')==0){
				if(!empty($param) && in_array($param,array('delete','node_export','delete-items','devel'))){
					return 1;
				}
			}else if(strcmp($my_type,'fuente')==0){
				if(!empty($param) && in_array($param,array('delete','node_export','devel'))){
					return 1;
				}
			}else if(in_array($my_type,array('idea','oportunidad','proyecto','estrategia','despliegue','decision','informacion','chat'))){
				if(!empty($param) && in_array($param,array('delete','tag','node_export','devel'))){
					return 1;
				}
			}				
		}
	}
	return 0;	
}
function is_vigilancia_left($is_block=0){
	if(is_ficha_node_left('canal_de_supercanal','canal')){
		return 1;
	}
	if(is_ficha_node_left('canal_de_yql','canal')){
		return 1;
	}
	//intelsat-2016
	if(red_node_is_delete(array('item','noticia'))){
		return 1;
	}
	    if($is_block){
            if(in_array(arg(0),array('busqueda'))){
                return 1;
            }
            /*
            //intelsat-2014    
            if(hontza_solr_is_busqueda_avanzada_pantalla()){
                return 1;
            }
            //
            */            
        }
        if(hontza_is_canal_usuarios('bookmarks')){
            return 1;
        }
        //intelsat-2015
        if(module_exists('canal_usuario')){
            if(canal_usuario_is_canal_usuario()){
                return 1;
            }
        }
        if(hontza_canal_rss_is_red_exportar_rss_enviar_mail_canales_rss()){
            return 1;
        }                
        if(hontza_is_vigilancia('destacados')){
            return 1;
        }        
        if(hontza_canal_rss_is_publico_activado()){
            return publico_vigilancia_is_vigilancia_left($is_block);
        }
        //intelsat-2015
        if(is_ficha_node('canal_usuario')){
            return 1;
        }
        /*if(red_despacho_is_vigilancia_left()){
            return 1;
        }*/
        if(hontza_canal_json_is_pantalla()){
            return 1;
        }
        return 0;
}
function is_fuentes_left(){
	if(is_ficha_node_left('supercanal','fuente')){
		return TRUE;
	}
	if(is_ficha_node_left('fuentedapper','fuente')){
		return TRUE;
	}
        if(red_funciones_is_validate_change_pipe_id()){
            return 1;
        }        
	return FALSE;
}
function is_fuentes(){
	if(is_fuentes_pipes_todas()){
		return 1;
	}
        //gemini-2014
        if(red_is_fuentes_en_intercambiar_recursos()){
           return 1; 
        }
        //
	if(is_ficha_node('supercanal')){
		return 1;
	}
	if(is_ficha_node('fuentedapper')){
		return 1;
	}
        //gemini-2014
        if(is_ficha_node('fuentehtml')){
		return 1;
	}
        if(is_ficha_fuente()){
            return 1;
        }
        if(red_funciones_is_validate_change_pipe_id()){
            return 1;
        }
        //
	return 0;
}
//function is_user_editing($param='',&$uid){
function is_user_editing($param,&$uid){
	if(strcmp(arg(0),'user')==0 && is_numeric(arg(1)) && strcmp(arg(2),'edit')==0){
           //gemini-2013
           if(strcmp($param,"-1")==0){   
               $uid=arg(1);
               return 1;
           }else{
           //    
                if(strcmp(arg(3),$param)==0){
                   $uid=arg(1);
                   return 1;
                }
           }     
	}
	return 0;
}
function is_user_editor_empresa_servicio($uid,$editando_servicio=0){
	$my_user=user_load($uid);
	if(isset($my_user->uid) && !empty($my_user->uid)){
		if(isset($my_user->profile_es_empresa_de_servicios) && !empty($my_user->profile_es_empresa_de_servicios)){
			if(isset($my_user->profile_codigo_empresa) && !empty($my_user->profile_codigo_empresa)){
				return 1;
			}
		}
		//
		//print $my_user->profile_empresa.'='.$uid.'<BR>';
		if(empty($editando_servicio)){	
			if(!is_in_other_user_profile($my_user->profile_empresa,$uid)){
				return 1;
			}
		}	
	}
	return 0;
}
function is_in_other_user_profile($profile_empresa,$uid='',$codigo=''){
	$where=array();
	$where[]="1";
	if(!empty($profile_empresa)){
		if(empty($codigo)){
			$where[]="pv.value='".$profile_empresa."'";
		}else{
			$where[]="pv.value!='".$profile_empresa."'";
		}
	}
	if(!empty($uid)){
		$where[]="pv.uid!='".$uid."'";
	}
	$where[]="pf.name='profile_empresa'";
	$sql="SELECT pv.* FROM profile_values pv LEFT JOIN profile_fields pf ON pv.fid=pf.fid WHERE ".implode(" AND ",$where);
	$result = db_query($sql);
	//print $sql; 
	while ($row = db_fetch_object($result)) {		
		$my_user=user_load($row->uid);		
		if(isset($my_user->uid) && !empty($my_user->uid)){
			if(isset($my_user->profile_es_empresa_de_servicios) && !empty($my_user->profile_es_empresa_de_servicios)){
				//echo print_r($my_user,1);
				if(isset($my_user->profile_codigo_empresa) && !empty($my_user->profile_codigo_empresa)){
					//echo print_r($my_user,1);
					if(empty($codigo)){
						//echo print_r($my_user,1);
						return 1;
					}else if(strcmp($my_user->profile_codigo_empresa,$codigo)==0){
						return 1;
					}	
				}
			}
		}
	}
	return 0;
}
function my_user_profile_form_alter(&$form,&$form_state,$form_id){
	if(strcmp($form_id,'user_profile_form')==0){
                if(is_user_editing('Empresa',$my_uid)){
                    my_user_empresa_form_alter($form,$form_state,$form_id,$my_uid);
                    my_user_empresa2_form_alter($form,$form_state,$form_id);
		}else if(is_user_editing('Consultoria_en_gestion_de_la_informacion',$my_uid)){
                    //intelsat-2016
                    red_facilitador_acces_denied();
                    my_user_consultoria_en_gestion_de_la_informacion_form_alter($form,$form_state,$form_id);
                    //intelsat-2015
                    my_user_consultoria_en_innovacion_form_alter($form,$form_state,$form_id);
                    my_user_consultoria_estrategica_form_alter($form,$form_state,$form_id);
                    my_user_optimizacion_tics_form_alter($form,$form_state,$form_id);
                    my_user_servicios2_form_alter($form,$form_state,$form_id);
                    //intelsat-2016
                    red_facilitador_user_profile_form_alter($form,$form_state,$form_id);
                    //
                }else if(is_user_editing('Consultoria_en_innovacion',$my_uid)){
                    my_user_consultoria_en_innovacion_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Consultoria_estrategica',$my_uid)){
                    my_user_consultoria_estrategica_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Datos personales',$my_uid)){
                    my_user_datos_personales_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Optimizacion_tics',$my_uid)){
                    my_user_optimizacion_tics_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Otro_servicio',$my_uid)){
                    my_user_otro_servicio_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Perfiles_web',$my_uid)){
                    my_user_perfiles_web_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Preguntas',$my_uid)){
                    my_user_preguntas_form_alter($form,$form_state,$form_id);
                }else if(is_user_editing('Servicios',$my_uid)){
                    my_user_servicios2_form_alter($form,$form_state,$form_id);
                }else{
                    //intelsat-2015
                    my_user_datos_personales_form_alter($form,$form_state,$form_id);
                    my_user_perfiles_web_form_alter($form,$form_state,$form_id);
                    //
                    red_funciones_user_profile_add_vista_compacta_field($form,$form_state,$form_id);
                }
	}
        my_user_profile_servicios_form_alter($form,$form_state,$form_id);
        //intelsat-2015
        red_crear_usuario_user_profile_form_alter($form,$form_state,$form_id);
        //intelsat-2016
        hontza_registrar_user_profile_account_form_alter($form,$form_state,$form_id);
}
function my_user_empresa_form_alter(&$form,&$form_state,$form_id,$my_uid){	
    global $user;                   
    if($user->uid==1 || is_user_editor_empresa_servicio($my_uid)){
        add_empresa_de_servicios_js();			
	$form['#validate'][]='my_es_empresa_de_servicios_validate';
    }else{
        unset($form['Empresa']['profile_es_empresa_de_servicios']);
        unset($form['Empresa']['profile_codigo_empresa']);
    }
    /*
    //intelsat-2015
    if(!hontza_is_user_anonimo()){
        if(isset($form['Empresa']['profile_es_empresa_de_servicios'])){
            unset($form['Empresa']['profile_es_empresa_de_servicios']);
        }
    }
    //
    */
}
function my_es_empresa_de_servicios_validate($form, &$form_state) {   
  //echo print_r($form_state['values'],1);
  $is_checked=$form_state['values']['profile_es_empresa_de_servicios'];
  if($is_checked){
  	$codigo=$form_state['values']['profile_codigo_empresa'];
	if(empty($codigo)){
		//form_set_error('empresa_profile', t(utf8_encode('El c�digo de la empresa no puede ser vac�o')));
            form_set_error('empresa_profile', t('The company code can not be empty'));
	}else{
		$my_uid=arg(1);		
		if(is_in_other_user_profile($form_state['values']['profile_empresa'],$my_uid,$codigo,0)){
			//form_set_error('empresa_profile', t(utf8_encode('El c�digo de la empresa existe')));
                    form_set_error('empresa_profile', t('The company code already exists'));
		} 
	}
  }
}
function add_empresa_de_servicios_js(){
	$js='
			$(document).ready(function()
			{			
			  var is_checked=$("#edit-profile-es-empresa-de-servicios").attr("checked");
			  if(is_checked){
			  	//
			  }else{
			  	$("#edit-profile-codigo-empresa-wrapper").hide();
			  }
			  //
			  $("#edit-profile-es-empresa-de-servicios").click(function()
			  {
				var v=$(this).attr("checked");
				if(v){
					$("#edit-profile-codigo-empresa-wrapper").show();
				}else{
					$("#edit-profile-codigo-empresa-wrapper").hide();
					$("#edit-profile-codigo-empresa").attr("value","");
				}
			  });
			});';
			
			drupal_add_js($js,'inline');
}
function is_servicios_left(){
	$node=my_get_node();
	//echo print_r($node,1);exit();
	if(!empty($node) && isset($node->nid) && !empty($node->nid) && strcmp(arg(2),'edit')==0){
		//if(in_array($node->type,array('item'))){
		if(strcmp($node->type,'servicio')==0){			
			if(!my_servicio_access($node,1)){
				return 1;
			}
		}
	}
        //intelsat-2015
        if(panel_admin_is_panel_admin('servicios')){
            return 1;
        }
	return 0;
}
function my_get_rows_canales_por_categorias($vars){
	if(strcmp(arg(0),'canales')==0 && strcmp(arg(1),'categorias')==0 && is_numeric(arg(2))){
		//
	}else{
		return $vars['rows'];
	} 
	//
	$fields=array('node_title'=>'Channel','node_created'=>'Date','last_update'=>'Last update','uid'=>'Creator','responsable_uid'=>'Filtering','num_noticias'=>'N','num_validadas'=>'V','num_comentadas'=>'C','num_debates'=>'F','num_wikis'=>'W','num_ideas'=>'I');
	//
	$order_info=my_get_order_info_canales_por_categorias();
	//
	$result=array();
	$result[]='<table class="views-table cols-7">';
	$result[]='<thead><tr>';
	foreach($fields as $f=>$label){	
		$result[]='<th class="views-field views-field-'.$f.'">';
		//$result[]=t($label);	 
		$result[]=my_get_tr_label_canales_por_categorias($f,$label,$order_info);
		$result[]='</th>';
	}	
	$result[]='</thead></tr>';	
	//
	/*
	if(isset($vars['view']->result) && count($vars['view']->result)>0){
		$canales=$vars['view']->result;*/
	$canales=my_get_canales_por_categorias($order_info);
	$canales=my_prepare_canales($canales,$order_info,$fields);
	if(count($canales)>0){	
		foreach($canales as $i=>$canal){
			$garrena='';
			if(empty($i)){
				$garrena=' views-row-first';
			}else if($i==($num-1)){
				$garrena=' views-row-last';
			}
			//
			$my_mod=($i % 2 );
			$even='even';
			if(empty($my_mod)){
				$even='odd';
			}
			//echo print_r($canal,1);			
			$tr='<tr class="'.$even.$garrena.'">';
			$result[]=$tr;
			$node=node_load($canal->nid);
			//echo print_r($node,1);			
			foreach($fields as $f=>$label){				
				$result[]='<td class="views-field views-field-'.$f.'">';
				//
				if(in_array($f,array('node_title','num_noticias','num_validadas','num_comentadas','num_debates','num_wikis','num_ideas'))){
					$result[]=$canal->$f;
				}else if(in_array($f,array('node_created'))){					
					//$result[]=format_date($canal->$f, 'custom', 'Y-m-d H:i');
                                        $result[]=format_date($canal->$f, 'custom', 'Y-m-d');
				}else if(in_array($f,array('uid'))){					
					$username='';
					/*if(isset($node->uid) && !empty($node->uid)){
						$my_user=user_load($node->uid);
						if(isset($my_user->uid) && !empty($my_user->uid)){
							$username=$my_user->name;
						}
					}*/
					$username=$canal->username;
					$result[]=$username;
				}else if(in_array($f,array('responsable_uid'))){					
					$username='';
					/*if(isset($node->field_responsable_uid[0]['uid']) && !empty($node->field_responsable_uid[0]['uid'])){
						$my_user=user_load($node->field_responsable_uid[0]['uid']);
						if(isset($my_user->uid) && !empty($my_user->uid)){
							$username=$my_user->name;
						}
					}*/
					$username=$canal->responsable_name;
					$result[]=$username;
				}else if($f=='last_update'){
                                    $last_update=get_canal_last_update($canal->nid);
                                    if(!empty($last_update)){
                                        $result[]=format_date($last_update, 'custom', 'Y-m-d H:i');
                                    }else{
                                        $result[]='';
                                    }
                                }
                                /*else if(in_array($f,array('num_noticias'))){
					$result[]=get_num_canal_noticias($canal->nid);
				}else if(in_array($f,array('num_validadas'))){
					$result[]=get_num_canal_validadas($canal->nid);
				}else if(in_array($f,array('num_comentadas'))){
					$result[]=get_num_canal_comentadas($canal->nid);
				}else if(in_array($f,array('num_debates'))){
					$result[]=get_num_canal_debates($canal->nid);
				}else if(in_array($f,array('num_wikis'))){
					$result[]=get_num_canal_wikis($canal->nid);
				}*/					
				$result[]='</td>';
			}
			$result[]='</tr>';
		}
	}
	//
	$result[]='</table>';
	return implode('',$result);
}
function get_num_canal_noticias($canal_nid){
	$noticias=my_get_canal_noticias($canal_nid);
	return count($noticias);
}
//intelsat-2016
//function my_get_canal_noticias($canal_nid){
function my_get_canal_noticias($canal_nid,$is_porcentajes=0){
 $result=array();
 $where=array();
 $where[]="1";
 $where[]="n.type = 'item'";
 $my_grupo=og_get_group_context(); 
 if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og.group_nid = ".$my_grupo->nid.")"; 
 } 
 $where[]="fn.feed_nid=".$canal_nid;
 //intelsat-2016
 $where=red_canal_get_porcentajes_where_fecha($where,$is_porcentajes);
 // 
	$items = db_query($sql=sprintf("SELECT fn.nid FROM {feeds_node_item} fn LEFT JOIN {og_ancestry} og ON fn.nid = og.nid LEFT JOIN {node} n ON fn.nid=n.nid WHERE ".implode(" AND ",$where)).' GROUP BY fn.nid');        
		//print $sql;
		while ($item = db_result($items)) {		  
		  $result[]=$item;
		}
	return $result;
}
function get_num_canal_validadas($canal_nid){
	return count(my_get_canal_validadas($canal_nid));
}
//intelsat-2016
//function my_get_canal_validadas($canal_nid){
function my_get_canal_validadas($canal_nid,$is_porcentajes=0){
 $result=array();
 $where=array();
 $where[]="1";
 $where[]="n.type = 'item'";
 $my_grupo=og_get_group_context(); 
 if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og.group_nid = ".$my_grupo->nid.")"; 
 } 
 $where[]="fc.fid = 2";
 $where[]="fn.feed_nid=".$canal_nid;
 //intelsat-2016
 $where=red_canal_get_porcentajes_where_fecha($where,$is_porcentajes);
 // 
 $sql="SELECT fc.* FROM {flag_content} fc LEFT JOIN {node} n ON n.nid = fc.content_id
                      LEFT JOIN {feeds_node_item} fn ON n.nid=fn.nid
					  LEFT JOIN {og_ancestry} og ON n.nid = og.nid
                      WHERE ".implode(" AND ",$where).' GROUP BY fc.content_id';
 //					  
 $validadas = db_query($sql);
 
 //while ($item= db_result($validadas)) {
 while ($item= db_fetch_object($validadas)) {
		  //$result[]=$item;
                  $result[]=$item->content_id;
		}
 return $result;
} 
function get_num_canal_comentadas($canal_nid){
	return count(my_get_canal_comentadas($canal_nid));
}
function my_get_canal_comentadas($canal_nid){
 $result=array();
 $where=array();
 $where[]="1";
 $where[]="n.type = 'item'";
 $my_grupo=og_get_group_context(); 
 if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og.group_nid = ".$my_grupo->nid.")"; 
 }
 $where[]="fn.feed_nid=".$canal_nid;
  $where[]="(NOT ISNULL(c.cid))"; 
 //
 $group_by="";
 //AVISO::::con el group by noticias comentadas, y sin el cuantos comentarios
 $group_by=" GROUP BY fn.nid";
 //
 $sql="SELECT fn.nid 
 FROM {feeds_node_item} fn 
 LEFT JOIN {og_ancestry} og ON fn.nid = og.nid 
 LEFT JOIN {node} n ON fn.nid=n.nid
 LEFT JOIN {comments} c ON n.nid=c.nid 
 WHERE ".implode(" AND ",$where).$group_by; 
 // 
	$items = db_query($sql);        
		
		while ($item = db_result($items)) {		  
		  $result[]=$item;
		}
	return $result;
}
function get_num_canal_debates($canal_nid){
 $kont=0;
 $noticias=my_get_canal_noticias($canal_nid);
 if(!empty($noticias)){
 	foreach($noticias as $i=>$row){
		$nid=$row;
		//$debates=my_get_content_type_debate($nid);
                $debates=hontza_get_node_debate_array($nid);
		$num=count($debates);
		//AVISO::::cuantos debates o un nodo que contenga por lo menos un debate
		if($num>0){
			$kont=$kont+1;
		}		
		//$kont=$kont+$num;		
	}
 }
 return $kont;
}
function my_get_content_type_debate($nid="",$debate_nid=""){
	//$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
        $src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
        $src_hasi2=$_SERVER['HTTP_HOST'].base_path();
        $debate_url_array=array();
        $debate_url_array[]="'".$src_hasi."node/".$nid."'";
        $debate_url_array[]="'".$src_hasi2."node/".$nid."'";
        //
	$where=array();
	$where[]="1";
        if(!empty($nid)){
            $where[]="ct.field_enlace_debate_url IN(".implode(',',$debate_url_array).")";
	}
        if(!empty($debate_nid)){
            $where[]="ct.nid=".$debate_nid;
	}
	$sql="SELECT ct.* FROM content_type_debate ct WHERE ".implode(" AND ",$where);
	$items = db_query($sql);        
		
		//while ($item = db_result($items)) {
		while ($item = db_fetch_object($items)) {		  
		  $result[]=$item;
		}
                
	return $result;
}
function get_num_canal_wikis($canal_nid){
 $kont=0;
 $noticias=my_get_canal_noticias($canal_nid);
 if(!empty($noticias)){
 	foreach($noticias as $i=>$row){
		$nid=$row;
		//$wikis=my_get_content_type_wiki($nid);
                $wikis=  hontza_get_node_wiki_array($nid);
		$num=count($wikis);
		//AVISO::::cuantos debates o un nodo que contenga por lo menos un debate
		if($num>0){
			$kont=$kont+1;
		}		
		//$kont=$kont+$num;		
	}
 }
 return $kont;
}
function my_get_content_type_wiki($nid="",$wiki_nid=""){
	$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
        $src_hasi2=$_SERVER['HTTP_HOST'].base_path();
        $wiki_url_array=array();
        $wiki_url_array[]="'".$src_hasi."node/".$nid."'";
        $wiki_url_array[]="'".$src_hasi2."node/".$nid."'";
        $where=array();
	$where[]="1";
	if(!empty($nid)){
            $where[]="ct.field_enlace_wiki_url IN(".implode(',',$wiki_url_array).")";
	}
        if(!empty($wiki_nid)){
            $where[]="ct.nid=".$wiki_nid;
	}
	$sql="SELECT ct.* FROM content_type_wiki ct WHERE ".implode(" AND ",$where);
	$items = db_query($sql);        
		//while ($item = db_result($items)) {
		while ($item = db_fetch_object($items)) {				  
		  $result[]=$item;
		}
	return $result;
}
function my_get_canales_por_categorias($order_info){
$result=array();
$tid=arg(2);
//
$where=array();
$where[]="(node.type in ('canal_busqueda', 'canal_de_supercanal', 'canal_de_yql'))"; 
$my_grupo=og_get_group_context(); 
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$vid='';
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")"; 
	$vocab_list=my_get_og_vocab_list(array("nid"=>$my_grupo->nid));
	if(count($vocab_list)>0){
		$vid=$vocab_list[0]->vid;
	}
	if(!empty($vid)){
		$where[]="(term_data.vid=".$vid.")";
	}
}

$where[]="(term_node.tid =".$tid.")";
//
$order_by="node_created DESC";
if(!empty($order_info['order'])){
	if(in_array($order_info['order'],array('node_title','node_created'))){
		$order_by=$order_info['order']." ".$order_info['sort'];
	}else if(strcmp($order_info['order'],'uid')==0){
		$order_by="users.name ".$order_info['sort'];
	}
}
// 
$sql="SELECT DISTINCT(node.nid) AS nid, node.title AS node_title, node.vid AS node_vid, node.created AS node_created
,users.name username 
FROM {node} node 
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
LEFT JOIN {term_node} term_node ON node.vid = term_node.vid 
LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid
LEFT JOIN {users} users ON node.uid=users.uid 
WHERE ".implode(" AND ",$where)." 
GROUP BY nid 
ORDER BY ".$order_by;
//print $sql;
$items = db_query($sql);        
		$my_kont=0;
		while ($item = db_fetch_object($items)) {		  
		  $result[$my_kont]=$item;
                  $result[$my_kont]->last_update=get_canal_last_update($item->nid);
                  $my_kont++;
		}
	return $result;

}
function my_prepare_canales($canales,$order_info,$fields){
	$result=array();
	//
	complete_canales($canales,$fields);	
	if(in_array($order_info['order'],array('responsable_uid'))){
            $canales=array_ordenatu($canales,'responsable_name',$order_info['sort'],0);
	}else if(in_array($order_info['order'],array('num_noticias','num_validadas','num_comentadas','num_debates','num_wikis','last_update','num_ideas'))){
            $canales=array_ordenatu($canales,$order_info['order'],$order_info['sort'],1);
	}
	//
	$page=my_get_request_value('page',1);
	//
	/*$_SESSION['my_canales_por_categorias']['order']
	$_SESSION['my_canales_por_categorias']['sort']*/
	//print 'page='.$page.'<BR>';
	$item_per_page=30;
	//simulatzeko
	//$item_per_page=2;
	$i=$item_per_page*$page;
	//$num=$item_per_page*($page+1);	
	$result=array_slice($canales,$i,$item_per_page);
	return $result;
}
function my_get_order_info_canales_por_categorias(){
	$info=array();
	$info['order']='';
	$info['sort']='';	
	if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
		$info['order']=$_REQUEST['order'];		
	}
	if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
		$info['sort']=$_REQUEST['sort'];		
	}
	return $info;
}
function my_get_tr_label_canales_por_categorias($f,$label_in,$order_info,$url_in=''){
        $label=$label_in;
        if($f=='node_created'){
            $label='Creation date';
        }
	/*print $f.'<BR>';
	echo $label.'<BR>';*/
	$tid=arg(2);
	//
	$my_array=array();
	//
	$my_array[]='order='.$f;
	$img='';
	$sort='asc';	
	if(!empty($order_info['order'])){				
		if(strcmp($f,$order_info['order'])==0){
			if(strcmp($order_info['sort'],'asc')==0){
				$sort='desc';
			}else{
				$sort='asc';
			}
			$img='<img width="13" height="13" title="'.t('sort ascending').'" alt="'.t('sort icon').'" src="'.base_path().'misc/arrow-'.$sort.'.png">';	
		}else{
			$sort='asc';
		}			
	}
	$my_array[]='sort='.$sort;	
	$page=my_get_request_value('page',1);
	//
	if(!empty($page)){
		$my_array[]='page='.$page;
	}
	//
	$query=implode('&',$my_array);
	//
	if(empty($url_in)){
		$result=l(t($label).$img,'canales/categorias/'.$tid,array('query'=>$query,'html'=>TRUE));
	}else{
		$result=l(t($label).$img,$url_in,array('query'=>$query,'html'=>TRUE));
	}
	//
	return $result;
}
function complete_canales($canales_in,$fields){
	$canales=$canales_in;
	//
	if(count($canales)>0){	
		foreach($canales as $i=>$canal){
			$node=node_load($canal->nid);
			//echo print_r($node,1);			
			foreach($fields as $f=>$label){				
				if(in_array($f,array('responsable_uid'))){					
					$username='';
					if(isset($node->field_responsable_uid[0]['uid']) && !empty($node->field_responsable_uid[0]['uid'])){
						$my_user=user_load($node->field_responsable_uid[0]['uid']);
						if(isset($my_user->uid) && !empty($my_user->uid)){
							$username=$my_user->name;
						}
					}
					$canales[$i]->responsable_name=$username;
				}else if(in_array($f,array('num_noticias'))){
					$canales[$i]->$f=get_num_canal_noticias($canal->nid);
				}else if(in_array($f,array('num_validadas'))){
					$canales[$i]->$f=get_num_canal_validadas($canal->nid);
				}else if(in_array($f,array('num_comentadas'))){
					$canales[$i]->$f=get_num_canal_comentadas($canal->nid);
				}else if(in_array($f,array('num_debates'))){
					$canales[$i]->$f=get_num_canal_debates($canal->nid);
				}else if(in_array($f,array('num_wikis'))){
					$canales[$i]->$f=get_num_canal_wikis($canal->nid);
				}else if(in_array($f,array('num_ideas'))){
					$canales[$i]->$f=get_num_canal_ideas($canal->nid);
				}
			}			
		}
	}
	
	return $canales;
}
function my_get_rows_mis_contenidos_v1($vars_in){
	$vars=$vars_in;
        //
	$url=get_url_mis_contenidos();
        if(!empty($url)){		
		$vars['rows']=my_create_th_order($vars,'group-nid',$url);
		if(strcmp($url,'mis-contenidos/canales')==0){
                    $vars['rows']=my_get_rows_mis_contenidos_fuentes($vars,'canales');
		}else if(strcmp($url,'mis-contenidos/fuentes')==0){
                    $vars['rows']=my_get_rows_mis_contenidos_fuentes($vars);
                }
	}	
	
	return $vars['rows'];
}
function get_url_mis_contenidos(){
	$url='';
	$param=arg(1);
	//
	//print arg(0);
	if(strcmp(arg(0),'mis-contenidos')==0 && (empty($param) || in_array($param,array('fuentes','canales','items','debates','area-trabajo')))){
		$url='mis-contenidos';
		if(!empty($param)){
			$url.='/'.$param;
		}
	}
	return $url;
}
function create_mis_contenidos_order_array($my_list){
	if(is_mis_contenidos('canales')){
            return hontza_create_mis_contenidos_canales_order_array($my_list);
        }else{
            $result=array();
            if(count($my_list)>0){
                    foreach($my_list as $i=>$row){
                            $result[$i]=$row;
                            //echo print_r($row,1);
                            //print $row->og_ancestry_nid.'<BR>';
                            //$grupo=my_get_grupo($row->og_ancestry_nid);
                            $grupo_node=my_get_grupo($row->nid);
                            $grupo_name='';
                            if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
                                    //print $grupo_node->title.'<BR>';
                                    $grupo_name=$grupo_node->title;
                            }
                            $result[$i]->grupo_name=$grupo_name;
                    }
            }
            $info['field']='grupo_name';
            $info['my_list']=$result;
            return $info;
        }
}
function my_get_grupo($nid){
	$where=array();
	$where[]="1";
	if(!empty($nid)){
		$where[]="gr.nid=".$nid;
	}
	$sql="SELECT n.* FROM {og_ancestry} gr LEFT JOIN {node} n ON gr.group_nid=n.nid WHERE ".implode(" AND ",$where);
	$result = db_query($sql);
	 
	while ($row = db_fetch_object($result)) {		
		return $row;
	}
	//
	$my_result=(object) array();
			
	return $my_result;	
}
function my_create_th_order($vars_in,$field,$url){
	$vars=$vars_in;
	//
	$my_array=explode('<th class="views-field views-field-'.$field.'">',$vars['rows']);
		//
		if(count($my_array)>1){
			//foreach($my_array as $i=>$v){
				$beste_array=explode('</th>',$my_array[1]);
				//print $beste_array[0].'<BR>';
				$label=trim($beste_array[0]);
				$order_info=my_get_order_info_canales_por_categorias();
				//echo print_r($order_info,1);
				//print $url.'<BR>';
				$beste_array[0]=my_get_tr_label_canales_por_categorias($field,$label,$order_info,$url);
				$my_array[1]=implode('</th>',$beste_array);
			//}
			$vars['rows']=implode('<th class="views-field views-field-'.$field.'">',$my_array);
		}
	return $vars['rows'];	
}
function is_add_canal_taxonomy_no_selected($taxonomy){
	if(!empty($taxonomy)){
		foreach($taxonomy as $tid=>$v){
			if(strcmp($tid,'form_token')==0){
				//
			}else{
				return 0;
			}
		}
	}
	return 1;
}
function my_get_rows_home_areadebate_block($vars_in){
	$vars=$vars_in;
	if(are_comments($vars['view']->result)){
		return '';
	}else{
            //intelsat-2015
            return hontza_solr_search_set_icono_home_areadebate_block($vars);
        }
	return $vars['rows'];
}
function are_comments($my_array){
	if(count($my_array)>0){
		$row=$my_array[0];
		if(isset($row->comments_cid) && !empty($row->comments_cid)){
			return 1;
		}
	}
	return 0;
}
function my_get_node_files($node){
	$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
	//print $src_hasi.'<BR>';
	$result=array();
	if(isset($node->files) && !empty($node->files)){
		$result[]='<div class="my_fcc">';
		$result=my_get_files_html($result,$node->files);		
		//
		$result[]='</div>';
	}
	return implode('',$result);
}
function my_download_callback($js = FALSE) {
  $src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
  $file=my_get_file(arg(1));
  if(isset($file->fid) && !empty($file->fid)){
       if(file_exists($file->filepath)){
                header("Content-Disposition: attachment; filename=".$file->filename);
		header("Content-Type: application/force-download");
                header('Content-Description: File Transfer');
		$url=$src_hasi.$file->filepath;
                if(hontza_is_private_download($file->filepath)){
                    $url='http://'.$_SERVER['HTTP_HOST'].base_path().'system/files/'.basename($file->filepath);;
                }
                readfile($url);
                exit();
	}
  }
  exit; 
}
function my_get_file($fid){
	$where=array();
	$where[]="1";
	if(!empty($fid)){
		$where[]="f.fid=".$fid;
	}
	$sql="SELECT * FROM files f WHERE ".implode(" AND ",$where);
	//print $sql;
	$result = db_query($sql);
	 
	while ($row = db_fetch_object($result)) {		
		return $row;
	}
	//
	$my_result=(object) array();
			
	return $my_result;	
}
function my_get_node_comment_list($nid){
	$result=array();
	//
	$where=array();
	$where[]="1";
	$where[]="u.nid=".$nid;
	
	$sql="SELECT f.* FROM files f LEFT JOIN comment_upload u ON f.fid=u.fid WHERE ".implode(" AND ",$where);
	
	$res = db_query($sql);
	 
	while ($row = db_fetch_object($res)) {		
		$result[]=$row;		
	}
	
	return $result;
}
function my_get_comment_files($comment){
	$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
	//print $src_hasi.'<BR>';
	//echo print_r($comment,1);exit();
	$result=array();
	if(isset($comment->files) && !empty($comment->files)){
		//echo print_r($comment->files,1);exit();
		$result[]='<div class="my_fcc">';
		$result=my_get_files_html($result,$comment->files);		
		//
		$result[]='</div>';
	}
	return implode('',$result);
}
function my_get_files_html($my_array,$files){
    //intelsat-2015
    if(red_visualizador_is_anonimo_visualizador_activado()){
        return '';
    }
	$result=$my_array;
	foreach($files as $my_id=>$my_row){
			if(is_array($my_row)){
				$row=(object) $my_row;
			}else{
				$row=$my_row;
			}
			//print $row->fid.'<BR>';
			//intelsat-2015
                        /*$img='<img src="'.$src_hasi.'sites/all/themes/buho/images/download_manager.png" width="12" height="12"/>';
			$img='';*/
                        $icono=my_get_icono_action('ficheros_adjuntos',t('Attachments')).'&nbsp;';
                        //
			$result[]='<div>'.$icono.l($row->filename,hontza_get_url_file($row->filepath),array('attributes'=>array('target'=>'_blank','absolute'=>TRUE))).'</div>';
	}
	return $result;
}
function my_get_node_c_d_w($node){
	$info=hontza_get_node_c_d_w_info($node);
        if(isset($info['html']) && !empty($info['html'])){
            return $info['html'];
        }
        return '';
}
function my_get_tr_fuentes_pipes($rows_html_in,$headers){
	$rows_html=$rows_html_in;
	
	if(count($headers)>0){
	
		$order_info=my_get_order_info_canales_por_categorias();
	
		foreach($headers as $i=>$field){
                    
			$my_field='field_supercanal_'.$field.'_rating';
			$active='';
			if(!empty($order_info['order']) && strcmp($order_info['order'],$my_field)==0){
				$active=' active';
			}
			$sep='<th class="views-field views-field-field-supercanal-'.$field.'-rating'.$active.'">';
			$my_array=explode($sep,$rows_html);
			$num=count($my_array);
			//		
			if($num>1){
				$beste_array=explode('</th>',$my_array[1]);
				/*$pos=strpos($beste_array[0], '</a>');
				if($pos===false){*/
					//print $field.'<BR>';
					$label=trim($beste_array[0]);
					$beste_array[0]=my_get_tr_label_canales_por_categorias($my_field,$label,$order_info,'fuentes-pipes/todas');
					$my_array[1]=implode('</th>',$beste_array);					
				//}
			}
			$rows_html=implode($sep,$my_array);
		}
	}
        /*
        //gemini-2014
        if(hontza_is_mostrar_recursos_compartidos_del_servidor_red()){
            $red_copy_server_sources=hontza_copy_server_sources_link();
            $red_copy_server_channels=hontza_copy_server_channels_link();
            $red_copy_server_facilitators=hontza_copy_server_facilitators_link();
        }else{
            $red_copy_server_sources='';
            $red_copy_server_channels='';
            $red_copy_server_facilitators='';
        }
        //
        */
        /*if(!empty($red_copy_server_channels)){
            $red_copy_server_sources.='|&nbsp;'.$red_copy_server_channels;
        }
        if(!empty($red_copy_server_facilitators)){
            $red_copy_server_sources.='|&nbsp;'.$red_copy_server_facilitators;
        }*/
        /*if(is_super_admin()){
            $copy_server_sources='';*/
            /*if(hontza_is_sareko_id_red()){
                $copy_server_sources='';
            }else{
                $copy_server_sources='|&nbsp;'.l(t('Copy server sources'),'copiar_fuentes_servidor');            
            }*/            
            //$copy_server_sources='<BR>'.$red_copy_server_sources.$copy_server_sources';
            /*$copy_server_sources.='|&nbsp;'.l(t('Download Sources txt'),'download_sources_txt',array('attributes'=>array('target'=>'_blank')));
            $copy_server_sources.='|&nbsp;'.l(t('Download Channels txt'),'download_channels_txt',array('attributes'=>array('target'=>'_blank')));
            $copy_server_sources.='<BR>';
            $rows_html=$copy_server_sources.$rows_html;            
        }else{
            if(!empty($red_copy_server_sources)){
                $rows_html='<BR>'.$red_copy_server_sources.'<BR>'.$rows_html;
            }
        }*/
        return $rows_html;
}
function interactiva_callback($js = FALSE) {
	$my_link=get_chat_grupo_link();
	if(!empty($my_link)){
		header('Location: '.$my_link);
	}
	//return t(utf8_encode('M�dulo de �rea interactiva'));
        return t('Interactive Area Module');
}
function my_get_title_chat_block(){
	return t('Manage Chats');
}
function my_get_gestionar_chat_content(){
	$result=array();
	$chat_grupo_link=get_chat_grupo_link();
	$result[]=l('Chat del grupo',$chat_grupo_link);
	//$result[]=l(t(utf8_encode('A�adir chat')),'node/add/chat');
        $result[]=l(t('Add chat'),'node/add/chat');
	$result[]=l(t('List of my chats'),'chat_list');
	return implode('<BR>',$result);
}
//gemini
function is_chat_left(){
	if(is_chat()){
		return 1;
	}
	$node=my_get_node();
	//echo print_r($node,1);exit();
	if(!empty($node) && isset($node->nid) && !empty($node->nid) && strcmp(arg(2),'edit')==0){
		//if(in_array($node->type,array('item'))){
		if(strcmp($node->type,'chat')==0){			
			return 1;
		}
	}
	if(is_ficha_node_left('chat','chat')){
		return 1;
	}				
	return 0;
}
//gemini
function is_chat(){
	if(strcmp(arg(0),'chat')==0){
		return 1;
	}
	if(strcmp(arg(0),'chat_list')==0){
		return 1;
	}
	if(strcmp(arg(0),'interactiva')==0){
		return 1;
	}
	if(is_ficha_node('chat')){
		return 1;
	}	
	return 0;
}
function add_js_chat(){
	if(is_chat()){
		my_add_active_trail_js('id_a_interactiva');		
	}
}
function get_chat_grupo_link(){
	$my_grupo=og_get_group_context();
	if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
		$chat_list=my_get_grupo_chat_list($my_grupo->nid);
		if(count($chat_list)>0){
			$chat_nid=$chat_list[0]->chat_nid;
			return 'node/'.$chat_nid;
		}
 	} 
	return 'no_existe';
}
function chat_list_callback() {	
  global $user;
  $where=array();
  //
  //$where[]='n.promote = 1';
  $where[]='n.status = 1';
  $where[]='n.type="chat"'; 
  $where[]='n.uid='.$user->uid;
  //$where[]='cg.id IS NULL'; 
  $where[]='cg.chat_nid IS NULL'; 
  //
  $sql='SELECT n.nid, n.sticky, n.created 
  FROM {node} n
  LEFT JOIN {chat_my_grupo} cg ON n.nid=cg.chat_nid   
  WHERE '.implode(' AND ',$where).'
  ORDER BY n.sticky DESC, n.created DESC';
  //print $sql;exit();
  $result = pager_query(db_rewrite_sql($sql), variable_get('default_nodes_main', 10));

  $output = '';
  $num_rows = FALSE;
  $rows=array();
  while ($node = db_fetch_object($result)) {
    //$output .= node_view(node_load($node->nid), 1);
	$my_node=node_load($node->nid);
	$my_link=l($my_node->title,'node/'.$my_node->nid);
	$my_fecha=format_date($my_node->created, 'custom','d/m/Y H:i');
	$rows[]=array($my_link,$my_fecha);
    $num_rows = TRUE;
  }

  if ($num_rows) {
    $feed_url = url('chat_list_rss.xml', array('absolute' => TRUE));
    //drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));
	$headers=array(t('Title'),t('Date'));
    $output .= theme('table',$headers,$rows);
	$output .= theme('pager', NULL, variable_get('default_nodes_main', 10));
  }
  else {
 
    $output = '<div id="first-time">' .t('You do not have personal chats'). '</div>';
  }

  drupal_set_title(t('List of my chats'));
  
  return $output;
}	
//gemini
function create_categorias_tematicas_fieldset($description='',$is_idea=0,$idea_nid=0,$my_type=''){
    $result=array(
    '#type'=>'fieldset',
    '#title'=>t('Thematic Categories'),
    '#description'=>$description,   
    );
    /*if(!empty($my_type) && $my_type=='idea'){
        $result['#required']=false;
    }*/
    //
    //$my_array=array();
    $node=new stdClass();
    //intelsat-2015
    $canal_usuario_tid_array=red_get_canal_usuario_tid_array();
    if(!empty($idea_nid)){
        $node=node_load($idea_nid);
    }
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s", $my_grupo->nid));
        //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
        $categorias=taxonomy_get_tree($id_categoria);
        if(!my_hay_categorias(1,$categorias)){
	 //form_set_error('categorias', t('There are no categories. You have to create at least one category before continuing.'));
         return '';
        }
        //gemini
	$indefinida_tid=my_get_term_data_tid('Categoria indefinida');
	//
        foreach ($categorias as $id => $contenido) {
              //gemini
              //AVISO::::117 categoria indefinida, solo para importaciones simples, opml, y se asigna por defecto
              //intelsat-2016
              //se ha comentado esto  
              /*if($contenido->tid==$indefinida_tid){
                    continue;
              }*/
              //
              //echo print_r($contenido,1);
              //gemini-2014
              $pro=profundidad($contenido->tid);
              //$pro=variable_get('profundidad_valor', 0);
              $key=$contenido->tid;
              if(strcmp($my_type,'oportunidad')==0){
                $key='categoria_tematica_oportunidad_'.$contenido->tid;
              }else if(strcmp($my_type,'proyecto')==0){
                $key='categoria_tematica_proyecto_'.$contenido->tid;
              }else if(in_array($my_type,array('noticia','my_report','debate','wiki'))){
                $key='my_cat_'.$contenido->tid;  
              //intelsat-2015  
              }else if(in_array($my_type,array('item'))){
                $key='field_item_canal_category_tid_'.$contenido->tid; 
              }else if(in_array($my_type,array('canal_de_supercanal','canal_de_yql','canal_usuario'))){
                $key='categoria_tematica_canal_'.$contenido->tid;  
              }
              //

              $result[$key] = array(
                //'#required' => TRUE,
                '#type' => 'checkbox',
                '#prefix' => '<div class=taxo'. $pro .'>',
                '#suffix' => '</div>',
                '#title' => $contenido->name
              );
              if($is_idea){
                  if(strcmp($my_type,'idea')==0){
                       //$result[$contenido->tid]['#name']='cat_list['.$contenido->tid.']';
                       if(!empty($idea_nid) && es_categoria_tematica_de_la_idea($idea_nid,$contenido->tid)){
                         $result[$key]['#attributes']=array('checked' => 'checked');
                       }
                  }else if(strcmp($my_type,'oportunidad')==0){
                       if(!empty($idea_nid) && es_categoria_tematica_de_la_oportunidad($idea_nid,$contenido->tid)){
                         $result[$key]['#attributes']=array('checked' => 'checked');
                       }
                  }else if(strcmp($my_type,'proyecto')==0){
                       if(!empty($idea_nid) && es_categoria_tematica_del_proyecto($idea_nid,$contenido->tid)){
                         $result[$key]['#attributes']=array('checked' => 'checked');
                       }
                  }else if(in_array($my_type,array('noticia','my_report','debate','wiki'))){
                       //intelsat-2015
                       if(red_is_categoria_tematica_selected($idea_nid,$my_type,$contenido->tid,$canal_usuario_tid_array,$node)){
                           $result[$key]['#attributes']=array('checked' => 'checked');
                       } 
                  //intelsat-2015     
                  }else if(in_array($my_type,array('item'))){
                       if(!empty($idea_nid) && hontza_canal_rss_es_categoria_tematica_del_item($node,$contenido->tid)){
                         $result[$key]['#attributes']=array('checked' => 'checked');
                       }
                  }else if(in_array($my_type,array('canal_de_supercanal','canal_de_yql','canal_usuario'))){
                      if(!empty($idea_nid) && hontza_canal_rss_es_categoria_tematica_del_canal($node,$contenido->tid)){
                         $result[$key]['#attributes']=array('checked' => 'checked');
                       }
                  }
                  //
              }
        }
    }
    /*if(empty($is_idea)){
        $result=array_merge($result,$my_array);
    }else{
        $result['cat_list']=$my_array;
    }*/
    //
    return $result;
}
function my_doc_list_callback(){
  //gemini-2014
  hontza_grupo_shared_active_tabs_access();
  //
  $headers=array();
  //$headers[]=array('data'=>t('fid'),'field'=>'fid');
  $headers[]=array('data'=>t('Creator'),'field'=>'username');
  $headers[]=array('data'=>t('Name'),'field'=>'filename');
  $headers[]=array('data'=>t('Information title'),'field'=>'node_title');
  $headers[]=array('data'=>t('Comment'),'field'=>'comment_title');
  $headers[]=array('data'=>t('Type'),'field'=>'node_type');
  $headers[]=array('data'=>t('Date'),'field'=>'timestamp','sort'=>'asc');
  $headers[]=array('data'=>t('Rating'),'field'=>'evaluacion');
  $headers[]='';

  //AVISO:::: $is_manual_filemime y $filter son parametros out
  $sql=get_my_doc_list_sql('',$headers,$is_manual_filemime,$filter);
  //$my_limit=variable_get('default_nodes_main', 20);
  $my_limit=20;
  //$result = pager_query(db_rewrite_sql($sql), $my_limit);
  $result = db_query($sql);
  
  $output = '';
  $num_rows = FALSE;
  $rows=array();
  $kont=0;
  //$node_types=array();
  while ($row = db_fetch_object($result)) {
    if($is_manual_filemime){
        if(!hontza_in_doc_filter($row,$filter)){
            continue;
        }    
    }  
    //$output .= node_view(node_load($node->nid), 1);
    $u=user_load($row->uid);
    $my_node=get_file_node($row);
    $c=_comment_load($row->cid);
    $rows[$kont]=array();
    $rows[$kont][0]=$row->fid;
    $rows[$kont][1]=$u->name;
    //$rows[$kont][2]=$row->filename;
    $rows[$kont][2]=l($row->filename,hontza_get_url_file($row->filepath),array('attributes'=>array('target'=>'_blank')));
    //$rows[$kont][3]=$row->filepath;
    //$rows[$kont][5]=$row->nid;
    $rows[$kont][3]='';
    if(!empty($my_node) && isset($my_node->nid) && !empty($my_node->nid)){
        //$rows[$kont][3]=l($my_node->title,'node/'.$my_node->nid);
        $rows[$kont][3]=l($row->node_title,'node/'.$my_node->nid);
    }
    //$rows[$kont][6]=$row->cid;
    $rows[$kont][4]='';
    //echo print_r($c,1);
    if(!empty($c) && isset($c->cid) && !empty($c->cid)){
        $rows[$kont][4]=l($c->subject,'node/'.$row->comment_nid,array('fragment'=>'comment-'.$row->cid));
    }
    //$rows[$kont][7]=$row->comment_nid;
    $rows[$kont][5]='';
    if(!empty($my_node) && isset($my_node->nid) && !empty($my_node->nid)){
        /*if(empty($row->cid)){
            $rows[$kont][5]=$my_node->type;
        }else{
            $rows[$kont][5]=t('comentario');
        }*/
        $rows[$kont][5]=my_get_label_type2($my_node->type);
        /*if(!in_array($my_node->type,$node_types)){
            $node_types[]=$my_node->type;
        }*/
    }
    //$rows[$kont][6]=date('d-m-Y H:i',$row->timestamp);
    $rows[$kont][6]=date('d-m-Y',$row->timestamp);
    $rows[$kont][7]='';
    if(!empty($row->evaluacion)){
        //$rows[$kont][7]=$row->evaluacion;
        $rows[$kont][7]=my_create_stars_view($row->evaluacion,1,'');
    }
    $rows[$kont][8]=l(t('Evaluate'),'evaluar_doc/'.$row->fid);
    
    $rows[$kont]=array_slice($rows[$kont],1);
    $num_rows = TRUE;
    $kont++;
  }
  
  $rows=my_set_estrategia_pager($rows, $my_limit);
  
  my_add_buscar_js();
  $output .= drupal_get_form('hontza_search_file_form');

  if ($num_rows) {
    /*$feed_url = url('oportunidad_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$headers=array(t('fid'),t('User'),t('Name'),t('Ruta'),t('Date'),t('nid'),t('cid'),t('c_nid'),t('Type'));
    //$headers=array_slice($headers,1);
    //    
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output.= '<div id="first-time">' .t('There are no attachments in the group'). '</div>';
  }
  drupal_set_title(t('List of attachments'));

  return $output;
}
function get_file_node($row){
    $nid='';
    if(!empty($row->nid)){
        $nid=$row->nid;
    }
    //
    if(!empty($row->comment_nid)){
        $nid=$row->comment_nid;
    }
    //
    if(!empty($row->servicio_nid)){
        $nid=$row->servicio_nid;
    }
    //
    if(!empty($row->proyecto_nid)){
        $nid=$row->proyecto_nid;
    }
    //
    if(!empty($nid)){
        $node=node_load($nid);
        return $node;
    }   
    return '';
}
function hontza_search_file_form(){    
    //gemini-2014
    $fs_title=t('Search');
    if(hontza_is_doc_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    //$form['file_buscar_fs']['fid']=array('#type'=>'textfield','#title'=>t('fid'),'#size'=>10,"#maxlength"=>10,"#default_value"=>get_file_filter_value('fid'));
    //$form['file_buscar_fs']['username']=array('#type'=>'textfield','#title'=>t('User'),'#size'=>30,"#maxlength"=>30,"#default_value"=>get_file_filter_value('username'));
    $form['file_buscar_fs']['username']= array(
	   '#title' => t('User'),
	  '#type' => 'textfield',
	  '#maxlength' => 60,
	  '#autocomplete_path' => 'user/autocomplete',
	  //'#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  "#default_value"=>get_file_filter_value('username'),
	  //'#weight' => -1,
	);
    //
    $form['file_buscar_fs']['filename']=array('#type'=>'textfield','#title'=>t('Name'),'#size'=>30,"#maxlength"=>30,"#default_value"=>get_file_filter_value('filename'));
    $form['file_buscar_fs']['filemime'] = array(
  '#type' => 'select',
  '#title' => t('File type'),
  "#default_value"=>get_file_filter_value('filemime'),
  '#options'=>my_get_filemime_options(),
  '#multiple'=>FALSE,
  //'#size'=>10,
  '#required' => FALSE
);
    $form['file_buscar_fs']['node_title']=array('#type'=>'textfield','#title'=>t('Information'),'#size'=>60,"#maxlength"=>60,"#default_value"=>get_file_filter_value('node_title'));
    $form['file_buscar_fs']['comment_title']=array('#type'=>'textfield','#title'=>t('Comment'),'#size'=>60,"#maxlength"=>60,"#default_value"=>get_file_filter_value('comment_title'));
    //$form['file_buscar_fs']['node_type']=array('#type'=>'textfield','#title'=>t('Information Type'),'#size'=>30,"#maxlength"=>30,"#default_value"=>get_file_filter_value('node_type'));
    $form['file_buscar_fs']['node_type']=array('#type'=>'select','#title'=>t('Information Type'),"#options"=>get_tipo_informacion_options(0),"#default_value"=>get_file_filter_value('node_type'),'#multiple'=>TRUE,'#size'=>14);
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar');
    return $form;
}
function hontza_search_file_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $name=$form['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['my_doc_list']['filter']) && !empty($_SESSION['my_doc_list']['filter'])){
                unset($_SESSION['my_doc_list']['filter']);
            }
        }else{
            $_SESSION['my_doc_list']['filter']=array();
            //$fields=array('fid','username','filename','node_title','comment_title','node_type','filemime');
            $fields=hontza_define_doc_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form['values'][$f];
                    if(!empty($v)){
                        $_SESSION['my_doc_list']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function get_file_filter_value($f){
    if(isset($_SESSION['my_doc_list']['filter']) && !empty($_SESSION['my_doc_list']['filter'])){
        if(isset($_SESSION['my_doc_list']['filter'][$f]) && !empty($_SESSION['my_doc_list']['filter'][$f])){
            return $_SESSION['my_doc_list']['filter'][$f];
        }
    }
    return '';
}
function get_where_node_title($value){
    $or=array();
    $or[]='n.title LIKE "%'.$value.'%"';
    $or[]='nc.title LIKE "%'.$value.'%"';
    $or[]='ns.title LIKE "%'.$value.'%"';
    return '('.implode(' OR ',$or).')';
}
function get_where_node_type($value_array){
    $keys=array_keys($value_array);
    $value=implode('","',$keys);
    $or=array();    
    $or[]='n.type IN("'.$value.'")';
    $or[]='nc.type IN("'.$value.'")';
    $or[]='ns.type IN("'.$value.'")';
    return '('.implode(' OR ',$or).')';
}
function my_add_buscar_js(){

		$js='
			$(document).ready(function()
			{
			  $("#file_buscar_fs .fieldset-wrapper").hide();
                          $("#file_buscar_fs legend").click(function(){
                            if ($("#file_buscar_fs .fieldset-wrapper").css("display") == "none"){
                                $("#file_buscar_fs .fieldset-wrapper").show();
                            }else{
                                $("#file_buscar_fs .fieldset-wrapper").hide();
                            }
                          });
			});';

			drupal_add_js($js,'inline');

}
function my_get_title_grupo_reto_block($with_icono=0,$title_in=''){
    //return t('Group Challenge');
    //intelsat-2015
    $result=t('Strategy');
    if(!empty($title_in)){
        $result=$title_in;
    }  
    if($with_icono){
       //intelsat-2015 
       $icono=get_estrategia_simbolo_img().'&nbsp;';
       $result=$icono.$result;      
    }
    //
    return $result;
}
function my_get_grupo_reto_content($is_solo_preguntas_clave=0){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    $content=get_reto_del_grupo_html($is_solo_preguntas_clave);
    if(empty($content)){
        return '';
    }
    $delta=36;
    $help_block=help_popup_block(15218);
    $html='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-'.$delta.'">';
    $title=my_get_title_grupo_reto_block();
    if($is_solo_preguntas_clave){
        $title=t('Key Questions');
    }
    //intelsat-2015
    $icono=get_informacion_simbolo_img(0,$title);
    //
    $html.='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
    $html.='<div class="content">';
    $html.=$content;
    //$html.='<div style="text-align:right;">'.l(t('View all'),'estrategias/arbol_estrategico').'</div>';
    $html.=hontza_inicio_view_all('estrategias/arbol_estrategico',t('View Strategy'));
    $html.='</div></div>';
    return $html;
}
function hontza_is_show_reto_del_grupo(){
    if(is_dashboard()){
        $estrategia_list=get_estrategia_list_by_grupo('');
        if(count($estrategia_list)>0){
            return 1;
        }
    }
    return 0;
}
function is_dashboard(){
    $param=arg(1);
    if(strcmp(arg(0),'dashboard')==0 && empty($param)){
        return 1;
    }
    return 0;
}
function my_get_reto_del_grupo_region(){
  $html=array();
  $html[]="<div id='c_reto_del_grupo'><div class='page-region'>";
  $html[]=my_get_grupo_reto_content();
  $html[]="</div></div>";
  return implode("",$html);
}
function get_reto_del_grupo_html($is_solo_preguntas_clave=0){
  $html=array();
  $my_grupo=og_get_group_context();
  if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
    /*$estrategia_list=get_estrategia_list_by_grupo($my_grupo->nid);
    if(count($estrategia_list)>0){
        $html[]='<ul>';
        foreach($estrategia_list as $i=>$row){
            $node=node_load($row->nid);
            $texto=$node->title;
            //print $node->body.'<BR>';
            if(!empty($node->body)){
                $texto=$node->body;
            }
            $html[]='<li>'.$texto.'</li>';
        }
        $html[]='</ul>';
    }*/
    $estrategia_list=get_reto_list_by_grupo($my_grupo->nid);
    //print 'num='.count($estrategia_list).'<BR>';
    $arbol=create_arbol($estrategia_list,$is_solo_preguntas_clave);
    $my_limit=variable_get('hontza_inicio_estrategia_limit',15);
    $num=count($arbol);
    if($num>$my_limit){
        $arbol=array_slice($arbol,0,$my_limit);
    }
    if(empty($arbol)){
        return '';
    }
    if($num>0){
        foreach($arbol as $i=>$row){
            $html[]=$row[0];
        }
    }
  }
  return implode('',$html);
}
function get_estrategia_list_by_grupo($grupo_nid_in){
    $result=array();
    //
    $grupo_nid=$grupo_nid_in;
    if(empty($grupo_nid)){
        $grupo_nid=get_grupo_nid();
    }
    $where=array();
    $where[]="1";
    if(!empty($grupo_nid)){
        $where[]="e.grupo_seguimiento_nid=".$grupo_nid;
    }
    //
    $sql="SELECT n.*
    FROM {node} n
    LEFT JOIN {estrategia} e ON n.nid=e.nid
    WHERE ".implode(" AND ",$where)." ORDER BY n.created ASC";
    //print $sql;
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_grupo_nid(){
      $my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            return $my_grupo->nid;
        }
       return '';
}
function my_get_busqueda_simple_wiki_content(){
    global $user;
    //gemini-2014
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    //
    if(isset($user->uid) && !empty($user->uid)){
            $value='';
            /*$is_busqueda_simple=my_get_request_value('is_busqueda_simple',1);
            if(!empty($is_busqueda_simple)){*/
                    $value=my_get_request_value('search');
            //}
            //
            $html='<input type="text" id="busqueda_simple_wiki_txt" name="busqueda_simple_wiki_txt" value="'.$value.'"/>';
            $html.='<BR><input type="button" id="busqueda_simple_wiki_btn" name="busqueda_simple_wiki_btn" value="'.t('Search').'"/>';
            $html.='&nbsp;<input type="button" id="limpiar_wiki_btn" name="limpiar_wiki_btn" value="'.t('Clean').'"/>';
            //$html.='&nbsp;'.l('Advanced','busqueda');
            //$html.='&nbsp;'.l('Advanced','busqueda',array('query'=>'is_avanzada=1'));
            return $html;
    }
    return '';
}
function add_busqueda_simple_wiki_js(){
	$js='
			var url_busqueda_simple_wiki="'.url('area-trabajo').'";			
			$(document).ready(function()
			{
			 	$("#busqueda_simple_wiki_btn").click(function(){
                                       var v=$("#busqueda_simple_wiki_txt").attr("value");
					location.href=url_busqueda_simple_wiki+"?search="+v;
				});
                                $("#limpiar_wiki_btn").click(function(){
                                       location.href=url_busqueda_simple_wiki;
				});
			});';

			drupal_add_js($js,'inline');
}
function my_area_trabajo_pre_execute(&$view){
    if(hontza_grupo_shared_active_tabs_access(1)){
        $field='node_created';
        if(hontza_is_area_trabajo_node_list('lo-mas-valorado')){
            $field='votingapi_cache_node_average_value';    
        }
        //intelsat-2015
        $sql=hontza_define_wiki_list_sql($field,'desc',1);
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;    
    }else{
        $sql=hontza_get_busqueda_en_blanco_sql();
        $view->build_info['query']=$sql; 	
        $view->build_info['count_query']=$sql;
    }    
}
function is_current_display($current_display,$s){
    $pos=strpos($current_display,$s);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function my_area_debate_by_node_pre_execute(&$view){
    $field='node_created';
    if(hontza_is_area_debate_node_list('lo-mas-valorado')){
        $field='votingapi_cache_node_average_value';    
    }
    $sql=hontza_define_debate_list_sql($field,'desc',1);
    $view->build_info['query']=$sql;
    $view->build_info['count_query']=$sql;
}
function my_get_busqueda_simple_debate_content(){
    global $user;
    //gemini-2014
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    //
    if(isset($user->uid) && !empty($user->uid)){
	$value='';
	/*$is_busqueda_simple=my_get_request_value('is_busqueda_simple',1);
	if(!empty($is_busqueda_simple)){*/
		$value=my_get_request_value('search');
	//}
	//
	$html='<input type="text" id="busqueda_simple_debate_txt" name="busqueda_simple_debate_txt" value="'.$value.'"/>';
	$html.='<BR><input type="button" id="busqueda_simple_debate_btn" name="busqueda_simple_debate_btn" value="'.t('Search').'"/>';
	$html.='&nbsp;<input type="button" id="limpiar_debate_btn" name="limpiar_debate_btn" value="'.t('Clean').'"/>';
        //$html.='&nbsp;'.l('Advanced','busqueda');
	//$html.='&nbsp;'.l('Advanced','busqueda',array('query'=>'is_avanzada=1'));
	return $html;
   }     
   return '';             
}
function add_busqueda_simple_debate_js(){
	$js='
			var url_busqueda_simple_debate="'.url('area-debate').'";
			$(document).ready(function()
			{
			 	$("#busqueda_simple_debate_btn").click(function(){
                                       var v=$("#busqueda_simple_debate_txt").attr("value");
					location.href=url_busqueda_simple_debate+"?search="+v;
				});
                                $("#limpiar_debate_btn").click(function(){
                                       location.href=url_busqueda_simple_debate;
				});
			});';

			drupal_add_js($js,'inline');
}
function is_new_form($nid_array){
    $v=$nid_array['#value'];
    if(empty($v)){
        return 1;
    }
    return 0;
}
function my_get_noticias_publicas_canales_content($is_html=1){
   $html=array();
   $publicas_list=get_noticias_publicas_list($is_html);
   $item_list=get_noticias_publicas_canales_list();
   $usuarios_publicas_list=get_noticias_usuarios_publicas_list();
   $item_list=array_merge($publicas_list,$item_list,$usuarios_publicas_list);
   $item_list=array_ordenatu($item_list,'node_created','desc',1);
   //echo print_r($item_list,1);
   if(!$is_html){
    return $item_list;
   }
      if(count($item_list)>0){
        $item_list=array_slice($item_list,0,variable_get('home_noticias_publicas_num',100));
        //print 'num='.count($item_list).'<BR>';
        foreach($item_list as $i=>$row){
            //$html[]=$row->title.'<BR>';
            $html[]=create_row_noticia_canal_publica_html($row);
        }
       }
       return implode('',$html);   
}
function get_noticias_publicas_canales_list($my_type='noticia_publica'){
    $where=array();
    $where[]="1";
    $where[]="node.status <> 0";
    $where[]="node.type='item'";
    //
    if(strcmp($my_type,'noticia_publica')==0){
        $where[]="(node_data_field_item_canal_reference.field_is_carpeta_noticia_publica_value=1 OR ct_canal.field_is_canal_noticia_publica_value=1 OR ct_canal_yql.field_is_yql_noticia_publica_value)";
    }else if(strcmp($my_type,'noticia_destacada')==0){
        $where[]="(node_data_field_item_canal_reference.field_is_carpeta_noticia_destaca_value=1 OR ct_canal.field_is_canal_noticia_destacada_value=1 OR ct_canal_yql.field_is_yql_noticia_destacada_value)";
    }
    //intelsat-2015
    if(panel_admin_noticias_publicas_is_gestion_noticias_publicas()){
        $where=panel_admin_noticias_publicas_add_where_filtro($where);
    }else if(panel_admin_noticias_destacadas_is_gestion_noticias_destacadas()){
        $where=panel_admin_noticias_destacadas_add_where_filtro($where);
    }
    //
    $sql="SELECT node.*,node.created as node_created,node.type as node_type,ct_canal.nid as ct_canal_nid,ct_canal_yql.nid as ct_canal_yql_nid,
 node.title as node_title,node.changed as node_changed 
 FROM node node
 LEFT JOIN content_type_item node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid
 LEFT JOIN {content_type_canal_de_supercanal} ct_canal ON (node_data_field_item_canal_reference.field_item_canal_reference_nid=ct_canal.nid)
 LEFT JOIN {content_type_canal_de_yql} ct_canal_yql ON (node_data_field_item_canal_reference.field_item_canal_reference_nid=ct_canal_yql.nid) 
 LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid 
 LEFT JOIN {node_revisions} node_revisions ON node.vid=node_revisions.vid 
WHERE ".implode(" AND ",$where)."
 GROUP BY node.nid
  ORDER BY node.created DESC";
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    /*if(strcmp($my_type,'noticia_destacada')==0){
        //echo print_r($result,1);exit();
        print $sql;
    }*/

    return $result;
}
function get_noticias_publicas_list($is_html=1){
    $where=array();
    $where[]="1";
    if($is_html){
        $where[]="node.status <> 0";
    }
    $where[]="node.type='noticias_portada'";
    //simulando
    //$where[]="node.type='item'";
    //intelsat-2015
    if(panel_admin_noticias_publicas_is_gestion_noticias_publicas()){
        $where=panel_admin_noticias_publicas_add_where_filtro($where);
    }
    //
    $sql="SELECT node.nid AS nid,
    node.created AS node_created,
    node.title AS node_title,
    node_data_field_noticias_portada_texto.field_noticias_portada_texto_value AS node_data_field_noticias_portada_texto_field_noticias_portada_texto_value,
    node_data_field_noticias_portada_texto.field_noticias_portada_texto_format AS node_data_field_noticias_portada_texto_field_noticias_portada_texto_format,
    node.type AS node_type, node.vid AS node_vid,
    node.status as node_status,
    node.changed AS node_changed 
    FROM {node} node
    LEFT JOIN {content_type_noticias_portada} node_data_field_noticias_portada_texto ON node.vid = node_data_field_noticias_portada_texto.vid 
    LEFT JOIN {og_ancestry} og_ancestry ON og_ancestry.nid=node.nid 
    LEFT JOIN {node_revisions} node_revisions ON node_revisions.vid=node.vid 
    WHERE ".implode(" AND ",$where)." ORDER BY node_created DESC";
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_noticias_usuarios_publicas_list($is_publica=1){
    $where=array();
    $where[]="1";
    $where[]="node.status <> 0";
    $where[]="node.type='noticia'";
    if($is_publica){
        $where[]="ctn.field_is_noticia_usuario_publica_value=1";
    }else{
        $where[]="ctn.field_is_noticia_usuario_destaca_value=1";
    }
    //intelsat-2015
    if(panel_admin_noticias_publicas_is_gestion_noticias_publicas()){
        $where=panel_admin_noticias_publicas_add_where_filtro($where);
    }else if(panel_admin_noticias_destacadas_is_gestion_noticias_destacadas()){
        $where=panel_admin_noticias_destacadas_add_where_filtro($where);
    }
    //
    $sql="SELECT node.nid AS nid,
    node.created AS node_created,
    node.title AS node_title,
    ctn.field_is_noticia_usuario_publica_value,
    ctn.field_is_noticia_usuario_destaca_value,
    node.type AS node_type, node.vid AS node_vid,node.uid,
    node.changed AS node_changed  
    FROM {node} node
    LEFT JOIN {content_type_noticia} ctn ON node.vid = ctn.vid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid=og_ancestry.nid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid=node_revisions.vid  
    WHERE ".implode(" AND ",$where)." ORDER BY node_created DESC";
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_reto_list_by_grupo($grupo_nid_in){
    $result=array();
    //
    $grupo_nid=$grupo_nid_in;
    if(empty($grupo_nid)){
        $grupo_nid=get_grupo_nid();
    }
    $where=array();
    $where[]="1";
    if(!empty($grupo_nid)){
        $where[]="(e.grupo_seguimiento_nid=".$grupo_nid." OR d.grupo_seguimiento_nid=".$grupo_nid.")";
    }
    //
    $sql="SELECT n.*
    FROM {node} n
    LEFT JOIN {estrategia} e ON n.nid=e.nid
    LEFT JOIN {despliegue} d ON e.nid=d.estrategia_nid
    WHERE ".implode(" AND ",$where)."
    GROUP BY n.nid 
    ORDER BY n.created ASC";
    //print $sql;
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_my_doc_list_sql($fid='',$headers=array(),&$is_manual_filemime,&$filter){
  $where=array();
  $where[]="1";
  if(!empty($fid)){
      $where[]="f.fid=".$fid;
  }
  //
  /*$where[]='n.promote = 1';
  $where[]='n.status = 1';
  $where[]='n.type="oportunidad"';*/
  //
  $my_grupo=og_get_group_context();
  $or_grupo=array();
  if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $or_grupo[]='og_ancestry.group_nid='.$my_grupo->nid;
        $or_grupo[]='nc_ancestry.group_nid='.$my_grupo->nid;
	$or_grupo[]='ns_ancestry.group_nid='.$my_grupo->nid;
        $where[]='('.implode(' OR ',$or_grupo).')';
  }else{
        $or_grupo[]='og_ancestry.group_nid='.get_grupo_sin_seleccionar_nid();
        $or_grupo[]='nc_ancestry.group_nid='.get_grupo_sin_seleccionar_nid();
	$or_grupo[]='ns_ancestry.group_nid='.get_grupo_sin_seleccionar_nid();
        $where[]='('.implode(' OR ',$or_grupo).')';
  }
  //
  /*$sql='SELECT f.*,u.nid,cu.cid,cu.nid as comment_nid,ts.nid as servicio_nid
  FROM {files} f
  LEFT JOIN {upload} u ON f.fid=u.fid
  LEFT JOIN {comment_upload} cu ON f.fid=cu.fid
  LEFT JOIN {content_type_servicio} ts ON f.fid=ts.field_logo_servicios_fid
  WHERE '.implode(' AND ',$where).'
  ORDER BY f.timestamp ASC';*/

  $is_filter_node_type=0;
  $is_manual_filemime=0;
  $filter=array();
  if(isset($_SESSION['my_doc_list']['filter']) && !empty($_SESSION['my_doc_list']['filter'])){
    $filter=$_SESSION['my_doc_list']['filter'];
    //echo print_r($filter,1);
    foreach($filter as $field=>$value){
        //print $field.'===='.$value.'<BR>';
        if(!empty($value)){
            switch($field){
                case 'fid':
                    $where[]='f.'.$field.'='.$value;
                    break;
                case 'username':
                    $where[]='users.name="'.$value.'"';
                    break;
                case 'filename':
                    $where[]='f.'.$field.' LIKE "%'.$value.'%"';
                    break;
                case 'filemime':
                    /*if(!in_array($value,array('excel','powerpoint','word'))){
                        $where[]='f.filemime="'.$value.'"';
                    }else{*/
                        $is_manual_filemime=1;
                    //}    
                    break;
                case 'node_title':
                    $where[]=get_where_node_title($value);
                    break;
                case 'comment_title':
                    $where[]='c.subject LIKE "%'.$value.'%"';
                    break;
                case 'node_type':
                    $where[]=get_where_node_type($value);
                    $is_filter_node_type=1;
                    break;
                default:
                    break;
            }
        }
    }
  }
  
  if(!$is_filter_node_type){
    $where[]=hontza_where_node_type_docs();
  }
  //IF(IF(n.title <> NULL,nc.title,n.title) <> NULL,IF(n.title <> NULL,nc.title,n.title),ns.title) AS node_title

  /*,IF(IF(n.title IS NULL,nc.title,n.title) IS NULL,ns.title,IF(n.title IS NULL,nc.title,n.title)) IS NULL,np.title,IF(IF(n.title IS NULL,nc.title,n.title) IS NULL,ns.title,IF(n.title IS NULL,nc.title,n.title)) AS node_title
  LEFT JOIN {content_type_proyecto} tp ON f.fid=tp.field_imagen_o_logo_fid
  LEFT JOIN {node} np ON tp.nid=np.nid*/

  $sql='SELECT f.*,u.nid,cu.cid,cu.nid as comment_nid,ts.nid as servicio_nid,users.name as username
  ,IF(IF(n.title IS NULL,nc.title,n.title) IS NULL,ns.title,IF(n.title IS NULL,nc.title,n.title)) AS node_title
  ,c.subject AS comment_title,ed.evaluacion
  ,IF(IF(n.type IS NULL,nc.type,n.type) IS NULL,ns.type,IF(n.type IS NULL,nc.type,n.type)) AS node_type
  FROM {files} f
  LEFT JOIN {upload} u ON f.fid=u.fid
  LEFT JOIN {comment_upload} cu ON f.fid=cu.fid
  LEFT JOIN {content_type_servicio} ts ON f.fid=ts.field_logo_servicios_fid
  LEFT JOIN {users} users ON f.uid=users.uid
  LEFT JOIN {node} n ON u.nid=n.nid
  LEFT JOIN {node} nc ON cu.nid=nc.nid
  LEFT JOIN {node} ns ON ts.nid=ns.nid
  LEFT JOIN {comments} c ON cu.cid=c.cid
  LEFT JOIN {evaluacion_doc} ed ON f.fid=ed.fid
  LEFT JOIN {og_ancestry} og_ancestry ON n.nid = og_ancestry.nid
  LEFT JOIN {og_ancestry} nc_ancestry ON nc.nid = nc_ancestry.nid
  LEFT JOIN {og_ancestry} ns_ancestry ON ns.nid = ns_ancestry.nid
  WHERE '.implode(' AND ',$where).'
  GROUP BY f.fid '.tablesort_sql($headers);
  return $sql;
}
function hontza_get_node_c_d_w_info($node){
        $info=array();
        $info['num_comentario']=0;
        $info['num_debate']=0;
        $info['html']='';
        $html=array();
	//
	//$html[]='|';
	if($node->comment_count>0){
		//$html[]=l(t($node->comment_count.' C '),'node/'.$node->nid,array('fragment'=>'comments'));                
                //$label_comment_count='1 '.t('Comment');
                $label_comment_count=t('1 Comment');
                if($node->comment_count>1){
                    $label_comment_count=$node->comment_count.' '.t('Comments');
                }
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='<div class="div_icono_con_numero">';
                    $html[]='<span class="span_icono_con_numero">';
                    $html[]=$node->comment_count;
                    $html[]='</span>';
                }
                //
                $comentario_url='node/'.$node->nid;
                if(hontza_canal_rss_is_publico_activado()){
                    $comentario_url=publico_get_node_url($comentario_url,$node);
                }
                $html[]=l(my_get_icono_action("comment-star",$label_comment_count),$comentario_url,array('html'=>true,'fragment'=>'comments'));
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='</div>';
                }
                $info['num_comentario']=$node->comment_count;
	}else{
		//$html[]=' 0 C ';
                $info['num_comentario']=0;
	}
	//intelsat-2015
        //se ha añadido debate
	if(in_array($node->type,array('idea','oportunidad','proyecto','estrategia','despliegue','decision','informacion','chat','debate'))){
		if($node->comment_count<1){
                    //return '';
                    $info['html']='';
                    return $info;
		}
                //intelsat-2015
                $info['html']=implode('',$html);
                //
                return $info;
	}
	//
        //intelsat-2015
        if(!red_usuario_basico_is_show_debate_collaboration_idea()){
            $info['html']=implode('',$html);
            return $info;
        }	
	//$html[]='|';
	//$debate_list=my_get_content_type_debate($node->nid);
        $debate_list=hontza_get_node_debate_array($node->nid);
	$num_debate=count($debate_list);
        $info['num_debate']=$num_debate;
	if($num_debate>0){
            //echo print_r($debate_list[0],1);
            //$html[]=l(t($num_debate.' D '),'node/'.$debate_list[0]->nid);
            //$html[]=l(t($num_debate.' D '),'node/'.$node->nid.'/enlaces_debate');
                $label_num_debate='1 '.t('Discussion');
                if($num_debate>1){
                    $label_num_debate=$num_debate.' '.t('Discussions');
                }
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='<div class="div_icono_con_numero">';
                    $html[]='<span class="span_icono_con_numero">';
                    $html[]=$num_debate;
                    $html[]='</span>';
                }
                //
                $html[]=l(my_get_icono_action("debate-star",$label_num_debate),'node/'.$node->nid.'/enlaces_debate',array('html'=>true));                
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='</div>';
                }
	}else{
		//$html[]=' 0 D ';
	}
	//	
	//$html[]='|';
	//$wiki_list=my_get_content_type_wiki($node->nid);
        $wiki_list=hontza_get_node_wiki_array($node->nid);
	$num=count($wiki_list);
        $info['num_wiki']=$num;
        //intelsat-2015
        $num_wiki=$num;
	if($num>0){
		//echo print_r($debate_list[0],1);
		//$html[]=l(t($num.' W '),'node/'.$wiki_list[0]->nid);
                //$html[]=l(t($num.' W '),'node/'.$node->nid.'/enlaces_wiki');
                $label_num_wiki='1 '.t('Collaboration');
                if($num_wiki>1){
                    $label_num_debate=$num_wiki.' '.t('Collaborations');
                }
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='<div class="div_icono_con_numero">';
                    $html[]='<span class="span_icono_con_numero">';
                    $html[]=$num_wiki;
                    $html[]='</span>';
                }
                //
                $html[]=l(my_get_icono_action("trabajo-star",$label_num_wiki),'node/'.$node->nid.'/enlaces_wiki',array('html'=>true));                
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='</div>';
                }
	}else{
		//$html[]=' 0 W ';
	}					
        //	
	//$html[]='|';
	$idea_list=idea_get_all_enlazar_array('',$node->nid);
	$num_ideas=count($idea_list);
        $info['num_ideas']=$num_ideas;
	if($num_ideas>0){
		//$html[]=l(t($num_ideas.' I '),'node/'.$node->nid.'/enlaces_idea');
                $label_num_ideas='1 '.t('Idea');
                if($num_ideas>1){
                    $label_num_ideas=$num_ideas.' '.t('Ideas');
                }
                //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='<div class="div_icono_con_numero">';
                    $html[]='<span class="span_icono_con_numero">';
                    $html[]=$num_ideas;
                    $html[]='</span>';
                }
                //
                $html[]=l(my_get_icono_action("idea-star",$label_num_ideas),'node/'.$node->nid.'/enlaces_idea',array('html'=>true));
                 //intelsat-2015
                if(hontza_canal_rss_is_sareko_id_icono_con_numero()){
                    $html[]='</div>';
                }
	}else{
		//$html[]=' 0 I ';
	}
        //        
	if($node->comment_count<1 && $num_debate<1 && $num<1 && $num_ideas<1){
		//return '';
                $info['html']='';
                return $info;
	}
	//
	$info['html']=implode('',$html);
        return $info;
}
//intelsat-2015
function hontza_scr_get_rows_home_areadetrabajo_block($vars_in){
    $vars=$vars_in;
    $sep='<div class="views-field-title">';
    $result=explode($sep,$vars['rows']);
    if(!empty($result)){
        foreach($result as $i=>$v){
            if($i>0){            
                $result[$i]=my_get_icono_action('trabajo_left',t('Collaboration')).$v;
            }    
        }
        $vars['rows']=implode($sep,$result);        
    }
    return $vars['rows'];
}