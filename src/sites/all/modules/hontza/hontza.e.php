<?php
function hontza_get_resumen_etiqueta($node_taxonomy,$is_ficha_completa=0,$node=''){
    //intelsat-2015
    if(hontza_solr_is_solr_activado()){
        return hontza_solr_search_get_resumen_etiqueta($node_taxonomy,$is_ficha_completa,$node);
    }else{
        if(!empty($node_taxonomy)){
        	$my_array=array();
		foreach($node_taxonomy as $etiqueta){
                    if(hontza_in_etiquetas_vocabulary($etiqueta)){
                        //print $etiqueta->name.'<BR>';
			$my_array[]=$etiqueta->name;
                    }    
		}
		$s=implode(' ',$my_array);
		if(red_is_rojo()){
                    return $s;
                }
                //$max=48;
		//$max=33;
                $max=70;
		//return $s;
		if(strlen($s)>$max){
			$s=substr($s,0,$max-3).'..';			
			return $s;
		}
		return $s;
	}
	return '';
    }    
}
function hontza_in_etiquetas_vocabulary($term){
    if(isset($term->vid) && !empty($term->vid)){
        //vid=3 Etiquetas
        //vid=10 Etiquetas de idea
        $etiquetas_vid_array=hontza_define_etiquetas_vid_array();
        if(in_array($term->vid,$etiquetas_vid_array)){
            return 1;
        }
    }
    return 0;
}
function hontza_todas_etiquetas_html($node,$is_array=0){
    if(hontza_solr_is_solr_activado() && !$is_array){
        $is_ficha_completa=1;
        return hontza_solr_search_get_resumen_etiqueta($node->taxonomy,$is_ficha_completa,$node);
    }else{    
        $html=array();
        if($node->taxonomy){
            foreach($node->taxonomy as $etiqueta){
                if(hontza_in_etiquetas_vocabulary($etiqueta)){
                    //$html[]='<b>'.$etiqueta->name.'</b>';
                    $html[]=$etiqueta->name;
                }    
            }              
        }
        if($is_array){
            return $html;
        }
        return implode(' ',$html);
    }    
}
function hontza_define_etiquetas_vid_array(){
    //vid=3 Etiquetas
    //vid=10 Etiquetas de idea
    return array(3,10);
}
function hontza_is_canal_view(){
    $param0=arg(0);    
    if(!empty($param0) && $param0=='node'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(empty($param2)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_is_picture_bug($picture){
    if(!hontza_is_sareko_id('ROOT')){
        $pos=strpos($picture,'/home/hontza3_files');
        if($pos===FALSE){
            return 0;
        }else{                    
            return 1;
        }
    }
    return 0;
}
function hontza_is_node_view(){
    $param0=arg(0);
    if($param0=='node'){
        $param1=arg(1);
        if(is_numeric($param1)){
            $param2=arg(2);
            if(empty($param2)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_define_filtros_editing_canales_rss(){
  $filtros=array();
  $form_state=array();
  $form_state['yql_obj']=new stdClass();
  if(isset($_REQUEST['checkarea']) && !empty($_REQUEST['checkarea'])){
    $filtros['area']=$_REQUEST['area'];
  }else{
    //Filtro 1
    if($_REQUEST['todos']){
      $filtros['todos'].=$_REQUEST['todos'];
    }
    //Filtro 2
    if($_REQUEST['titulo']){
      $filtros['titulo'].=$_REQUEST['titulo'];
    }
    if($_REQUEST['descripcion']){
      $filtros['descripcion'].=$_REQUEST['descripcion'];
    }
    //Filtro 3
    if($_REQUEST['no_titulo']){
      $filtros['no_titulo'].=$_REQUEST['no_titulo'];
    }
    if($_REQUEST['no_descripcion']){
      $filtros['no_descripcion'].=$_REQUEST['no_descripcion'];
    }
    //Filtro 4
    if($_REQUEST['contiene']){
      $filtros['contiene'].=$_REQUEST['contiene'];
    }
    if($_REQUEST['no_contiene']){
      $filtros['no_contiene'].=$_REQUEST['no_contiene'];
    }
    //Filtro 2
    $form_state['yql_obj']->filtrosSI = $_REQUEST['cbox1'];
    //Filtro 3
    $form_state['yql_obj']->filtrosNO = $_REQUEST['cbox2'];
    //Filtro 4
    $form_state['yql_obj']->campo_contiene = $_REQUEST['select1'];
    $form_state['yql_obj']->campo_no_contiene = $_REQUEST['select2'];
    $form_state['yql_obj']->conjuncion = $_REQUEST['cbox3'];
  }  
  $form_state['yql_obj']->filtros= $filtros;

  return $form_state;
}
function hontza_yql_wizard_define_url($data_in,$filter,$yql_obj=''){
  $data=$data_in;
  /*$values=array_values($data);
  if(count($values)==1){
      if(hontza_is_atom_by_url($values[0])){
          $new_url=hontza_atom2rrs($values[0],1);
          //print $new_url;exit();
      }
  }*/
  
  if(hontza_social_is_json_url_by_data($data,$url_json)){
    $url=$url_json;
  }else if(hontza_canal_rss_is_correo_rss($data,$url_correo,$yql_obj)){  
    //intelsat-2015
    $url=$url_correo;  
  }else{
    $url ='http://query.yahooapis.com/v1/public/yql?q=';  
    $query = "select channel.title, channel.link, channel.item.title, channel.item.link, channel.item.description, channel.item.guid from xml where url in('".implode("', '",$data)."')";
    $post_query=" | unique(field='channel.item.title') | unique(field='channel.item.link') | unique(field='channel.item.description')";
    $url.=urlencode($query.$filter.$post_query);
  }
  return $url;
}
function hontza_yql_save_canal_yql_parametros($canal,$is_edit=0){
        $vid=0;
        $nid=0;
        $todos='';
        $titulo='';
        $descripcion='';
        $no_titulo='';
        $no_descripcion='';
        $contiene='';
        $no_contiene='';
        $filtrosSI='';
        $filtrosNO='';
        $campo_contiene='';
        $campo_no_contiene='';
        $conjuncion='';
        $area='';
        //
        $fields=array('nid','vid','filtrosSI','filtrosNO','campo_contiene','campo_no_contiene','conjuncion');
        $filtros_fields=array('todos','titulo','descripcion','no_titulo','no_descripcion','contiene','no_contiene','area');
        if(!empty($fields)){
            foreach($fields as $i=>$f){
                if(isset($canal->$f) && !empty($canal->$f)){
                    ${$f}=$canal->$f;
                }
            }
        }
        //
        if(isset($canal->filtros) && !empty($canal->filtros) && !empty($filtros_fields)){
            foreach($filtros_fields as $k=>$ff){
                if(isset($canal->filtros[$ff]) && !empty($canal->filtros[$ff])){
                    ${$ff}=$canal->filtros[$ff];
                }
            }
        }        
        //
        $canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($canal->vid,$canal->nid);
        if(isset($canal_yql_parametros_row->nid) && !empty($canal_yql_parametros_row->nid)){
            if($is_edit){
                hontza_delete_canal_yql_parametros($canal);
                $is_insert=1;
            }
        }else{
            $is_insert=1;
        }
        if($is_insert){
            db_query('INSERT INTO {canal_yql_parametros}(vid,nid,todos,titulo,descripcion,no_titulo,no_descripcion,contiene,no_contiene,filtrosSI,filtrosNO,campo_contiene,campo_no_contiene,conjuncion,area) VALUES(%d,%d,"%s","%s","%s","%s","%s","%s","%s",%d,%d,%d,%d,%d,"%s")',$vid,$nid,$todos,$titulo,$descripcion,$no_titulo,$no_descripcion,$contiene,$no_contiene,$filtrosSI,$filtrosNO,$campo_contiene,$campo_no_contiene,$conjuncion,$area);
        }
}
function hontza_is_name_of_source_url_editable($node){
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='canal_de_yql'){
            return 1;
        }
        return 0;
    }
    return 1;
}
function hontza_is_hound_text_input(){    
    $my_array=hontza_define_hound_sareko_id_array();
    //simulando
    //$my_array=array_merge(hontza_simular_hound_sareko_id_array());
    //
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            if(hontza_is_sareko_id($v)){
                return 1;
            }
        }
    }
    $hound_is_active=variable_get('hound_is_active',0);
    if($hound_is_active){
        return 1;
    }
    //intelsat-2015
    if(defined('_IS_HOUND')){
        if(_IS_HOUND){
            return 1;
        }
    }
    return 0;
}
function hontza_simular_hound_sareko_id_array(){
    $result=array();
    return $result;
}
function hontza_is_red_hoja(){
    //AVISO::::hontza_define_red_sareko_id_array mirar ahi tambien
    $my_array=array('LOKALA');
    if(hontza_registrar_is_sareko_id_red_desactivado()){
        return 0;
    }
    //intelsat-2016
    if(defined('_IS_RED_HOJA') && _IS_RED_HOJA==1){
        return 1;
    }
    $temp_array=array();
    $my_array=array_merge($my_array,$temp_array);
    if(hontza_is_sareko_id_red()){
        foreach($my_array as $i=>$sareko_id){
            if(hontza_is_sareko_id($sareko_id)){
                 return 1;
            }
        }
    }
    //gemini-2014
    if(red_is_network_sareko_id()){
        return 1;
    }
    return 0;
}
function hontza_red_compartir_fuente_link($node){
    if(hontza_is_sareko_id_red()){
        if(red_compartir_grupo_is_grupo_red_alerta()){    
            $url='';
            if(hontza_is_red_hoja()){
               $url='red_compartir/compartir_fuente_hoja/'.$node->nid;
               /*if(!red_compartir_fuente_is_con_datos_rellenados($node)){
                   $url='node/'.$node->nid.'/edit/no_se_puede_compartir';
               }*/
            }
            if(!empty($url)){
                /*$html=array();
                $html[]='<div class="field field-type-text field-field-supercanal-red-compartir-fuente" style="float:left;clear:both;">';
                $html[]=l(t('Share Source'),$url);
                $html[]='</div>';
                return implode('',$html);*/
                $label='';
                return l($label,$url,array('attributes'=>array('title'=>t('Share Source'),'alt'=>t('Share Source'))));
            }
        }    
    }
    return '';
}
function hontza_copy_server_sources_link($is_label_long=1){
    if(hontza_is_sareko_id_red()){
        $url='';
        if(hontza_is_red_hoja()){
           $url='red_compartir/red_compartir_copiar_fuentes_servidor';
           if($is_label_long){
            //$label=t('Copy server sources');
            $label=t('Download sources from RedAlerta server');
           }else{
            //$label=t('Copy sources');
            $label=t('Download Sources');   
           }
           //return l($label,$url,array('query'=>drupal_get_destination()));
           return l($label,$url);
        }        
    }
    return '';
}
function hontza_community_tags_form_alter(&$form,&$form_state,$form_id){
    /*
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){
        $my_node_view_html=node_view(node_build_content($node),FALSE,TRUE);
        //$form['my_node_view']=array(
        //    '#value'=>$my_node_view_html,
        //    '#weight'=>0,
        //);
        $my_value=$form['cloud']['#value'];
        $form['cloud']['#title']='';
        $form['cloud']['#value']=$my_node_view_html.'<div style="clear:both;"><label><b>'.t('All tags').':</b></label>'.$my_value.'</div>';
    }*/    
}
function hontza_taxonomy_my_set_menu_primary_links($primary_in){
    global $user;
    $primary=$primary_in;
    if(hontza_is_og_vocab_terms()){
        //gemini-2014
        if(is_show_modificar_vocab() && $user->uid!=1){
            $primary=hontza_grupos_mi_grupo_define_editar_crear_categorias_primary();
        }else{
            //
            $result=array();
            $sep='<a href="';
            $my_array=explode($sep,$primary);
            if(count($my_array)>0){
                foreach($my_array as $i=>$v){
                    $pos=strpos($v,'"');
                    if($pos===FALSE){
                        $result[]=$v;
                    }else{
                        $href=substr($v,0,$pos);
                        if(hontza_is_add_term_by_href_value($href)){
                            $result[]=$href.'">'.t('Add category').'</a></li>';
                        }else{
                            $result[]=$v;
                        }
                    }
                }
            }
            return implode($sep,$result);
        }    
    }
    
    return $primary;
}
function hontza_is_og_vocab_terms($is_empty=0){
    $param0=arg(0);
    if(!empty($param0) && $param0=='node'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2=='og'){
                $param3=arg(3);
                if(!empty($param3) && $param3=='vocab'){
                    $param4=arg(4);
                    if(!empty($param4) && $param4=='terms'){
                        $param5=arg(5);
                        if(!empty($param5) && is_numeric($param5)){
                            //gemini-2014
                            if($is_empty){
                                $param6=arg(6);
                                if(empty($param6)){
                                    return 1;
                                }
                            }else{
                                return 1;
                            }    
                        }                                 
                    }
                }
            }
        }
    }
    return 0;
}
function hontza_is_add_term_by_href_value($href){
    $my_array=explode('/',$href);
    $num=count($my_array);
    if($num>0){
        if($my_array[$num-1]=='add_term'){
            return 1;
        }
    }
    return 0;
}
function hontza_is_save_canal_save_reto_al_que_responde(){
    if(hontza_is_batch()){
        return 0;
    }
    if(hound_is_updating_channel_new_hound_ids()){
       return 0; 
    }
    return 1;
}
function hontza_download_blanco_callback(){
    $output=l('Download blanco','http://blanco.hontza.es/sites/blanco.hontza.es/files/hontza_blanco.zip',array('absolute'=>TRUE,'attributes'=>(array('target'=>'_blank'))));
    return $output;
}
function hontza_red_compartir_canal_link($node){
    if(hontza_is_sareko_id_red()){
        if(red_compartir_grupo_is_grupo_red_alerta()){
        //simulando
        //if(red_compartir_grupo_is_grupo_red_alerta() || hontza_is_sareko_id('LOKALA')){
            $url='';
            if(hontza_is_red_hoja()){
                if(!hontza_is_hound_canal('',$node)){
                    $url='red_compartir/compartir_canal_hoja/'.$node->nid;
                }
            }            
            if(!empty($url)){
                /*$html=array();
                $html[]='<div class="field field-type-text field-field-canal-red-compartir-fuente" style="float:left;clear:both;">';
                $html[]=l(t('Share Channel'),$url);
                $html[]='</div>';
                return implode('',$html);*/
                $label='';
                return l($label,$url,array('attributes'=>array('title'=>t('Share channel'),'alt'=>t('Share channel'))));
            }
        }    
    }
    return '';
}
function hontza_confirm_enlazar_wiki_callback(){
    $item_nid=arg(1);
    $wiki_nid=arg(3);
    hontza_confirm_enlazar_wiki($item_nid,$wiki_nid);
}
function hontza_confirm_enlazar_wiki($item_nid,$wiki_nid){
    //intelsat-2015
    $item_nid_array=explode(',',$item_nid);
    $item_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($item_nid_array);    
    //intelsat-2015
    $num=count($item_nid_array);
    if($num>1){
        foreach($item_nid_array as $i=>$my_item_nid){
            hontza_delete_enlazar_wiki($wiki_nid,$my_item_nid);
            hontza_insert_enlazar_wiki($wiki_nid,$my_item_nid);
        }
    }else{
        hontza_delete_enlazar_wiki($wiki_nid,$item_nid);
        hontza_insert_enlazar_wiki($wiki_nid,$item_nid);
    }
    //
    //drupal_goto('node/'.$nid.'/enlazar_debate');
    //drupal_goto('node/'.$wiki_nid);
    //drupal_goto('comment/reply/'.$wiki_nid);
    //intelsat-2015
    red_set_bulk_command_executed_message($num);
    drupal_goto('node/'.$wiki_nid.'/edit');
    exit();
}
function hontza_confirm_enlazar_debate_callback(){
    $item_nid=arg(1);
    $debate_nid=arg(3);
    hontza_confirm_enlazar_debate($item_nid,$debate_nid);
}
function hontza_confirm_enlazar_debate($item_nid,$debate_nid){
    $item_nid_array=explode(',',$item_nid);
    //intelsat-2015
    $item_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($item_nid_array);
    $num=count($item_nid_array);    
    //if(count($item_nid_array)>1){
    if($num>1){    
        foreach($item_nid_array as $i=>$value){
            hontza_delete_enlazar_debate($debate_nid,$value);
            hontza_insert_enlazar_debate($debate_nid,$value);
        }
    }else{
        hontza_delete_enlazar_debate($debate_nid,$item_nid);
        hontza_insert_enlazar_debate($debate_nid,$item_nid);
    }    
    //
    //drupal_goto('node/'.$nid.'/enlazar_debate');
    //drupal_goto('node/'.$debate_nid);
    //drupal_goto('node/'.$debate_nid.'/edit');
    //drupal_goto('comment/reply/'.$debate_nid);
    //intelsat-2015
    red_set_bulk_command_executed_message($num);
    drupal_goto('comment/reply/'.$debate_nid,array('item_nid'=>$item_nid));
    exit();
}
function hontza_set_supercanal_argument_title($supercanal,$id){ 
    if(isset($supercanal->field_supercanal_args_desc[$id]['value'])){
        $v=$supercanal->field_supercanal_args_desc[$id]['value'];
        $v=trim($v);
        if(!empty($v)){
            return $v;
        }
        if(isset($supercanal->field_supercanal_args[$id]['value'])){
            return $supercanal->field_supercanal_args[$id]['value'];
        }    
    }
    return '';
}
function hontza_add_tag_window_open_js($nid,$url_in){
    //window.open ("'.$url."","mywindow","menubar=0,resizable=1,width=350,height=250");                    
    $url=url($url_in);
    $js='$(document).ready(function()
   {            
            on_tag_link_click();
            function on_tag_link_click(){
                $("#'.$nid.'_tag_link").click(function(){
                    HontzaPopupCenter("'.$url.'","mywindow",350,250);
                    return false;
                });
            }
            function HontzaPopupCenter(url, title, w, h) {
                // Fixes dual-screen position                         Most browsers      Firefox
                var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
                var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

                width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                var top = ((height / 2) - (h / 2)) + dualScreenTop;
                var newWindow = window.open(url, title, "scrollbars=yes, width=" + w + ", height=" + h + ", top=" + top + ", left=" + left);

                // Puts focus on the newWindow
                if (window.focus) {
                    newWindow.focus();
                }
            }
   });';
   drupal_add_js($js,'inline');
}
function hontza_is_tag_node_pantalla(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='node'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if($param2=='tag'){              
                return 1;
            }
        }
    }
    //intelsat-2015
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode('tag')){
        return 1;
    }
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode('fivestar')){
        return 1;
    }
    //
    //intelsat-2015
    if(red_despacho_is_categorizar_noticia_pantalla('popup')){
        return 1;
    }
    if(red_despacho_is_on_categorizar_noticia_pantalla('popup')){
        return 1;
    }
    /*if(red_despacho_boletin_report_is_forward()){
        return 1;
    }*/
    if(red_despacho_boletin_report_is_boletin_grupo_no_styles()){
        return 1;
    }
    //intelsat-2016
    if(red_despacho_is_reclasificar_tipo_fuente_noticia_pantalla('popup')){
        return 1;
    }
    if(red_despacho_is_on_reclasificar_tipo_fuente_noticia_pantalla('popup')){
        return 1;
    }
    
    return 0;
}
function hontza_og_canales_create_order_array($my_list_in){
    $result=$my_list_in;
    $f='valor_estrategico';
    if(count($result)>0){
        foreach($result as $i=>$row){
            //echo print_r($row,1);exit();
            //gemini-2014
            if(!empty($row->node_data_field_item_canal_reference_field_item_canal_reference_nid) && is_numeric($row->node_data_field_item_canal_reference_field_item_canal_reference_nid)){
            //    
                $node=node_load($row->node_data_field_item_canal_reference_field_item_canal_reference_nid);
                if(isset($node->nid) && !empty($node->nid)){
                    $result[$i]->$f=estrategia_get_canal_valor_estrategico($node);
                }else{
                    $result[$i]->$f=0;
                }    
            }else{
                $result[$i]->$f=0;
            }
        }
    }
    /*if(user_access('root') && hontza_is_sareko_id('ROOT')){
        echo print_r($result,1);
    }*/    
        $info['field']=$f;
	$info['my_list']=$result;
	return $info;
}
//intelsat-2016
//function hontza_get_all_nodes($types,$groups='',$fecha_ini='',$is_limit_in=0,$nid_in='',$is_node_load=1){
function hontza_get_all_nodes($types,$groups='',$fecha_ini='',$is_limit_in=0,$nid_in='',$is_node_load=1,$is_noticia_usuario_my_sended=0){
    //intelsat-2015
    $is_limit=$is_limit_in;
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='n.type IN("'.implode('","',$types).'")';
    if(!empty($groups)){
        $where[]='og_ancestry.group_nid IN("'.implode('","',$groups).'")';
    }
    if(!empty($fecha_ini)){
        $where[]='n.created>='.$fecha_ini;
    }
    //intelsat-2014
    if(!empty($nid_in)){
        $where[]='n.nid='.$nid_in;
    }
    //intelsat-2015
    $left_join_hontza_item_indexado='';
    /*if(red_solr_inc_is_hontza_item_indexado_activado()){
        $left_join_hontza_item_indexado=' LEFT JOIN {hontza_item_indexado} ON n.vid=hontza_item_indexado.vid ';
        $where[]='hontza_item_indexado.indexado!=1';
    }*/
    //intelsat-2016
    if($is_noticia_usuario_my_sended){
        $where[]='apachesolr_index_entities_node.my_sended=0';
        $left_join_apachesolr_index_entities_node=' LEFT JOIN {apachesolr_index_entities_node} ON n.nid=apachesolr_index_entities_node.entity_id ';
    }
    $sql='SELECT n.* 
    FROM {node} n
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
    '.$left_join_hontza_item_indexado.$left_join_apachesolr_index_entities_node.'
    WHERE '.implode(' AND ',$where);
    //intelsat-2016
    if($is_noticia_usuario_my_sended){
        $sql.=' GROUP BY n.nid ';
    }    
    //intelsat-2015
    /*
    if(red_solr_inc_is_hontza_item_indexado_activado()){
    if(red_crear_usuario_is_crear_usuario_net()){
       $is_limit=1; 
    }*/
    if($is_limit){
        $sql.=' LIMIT 0,10';
    }
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        if($is_node_load){
            $result[]=node_load($row->nid);
        }else{
            $result[]=$row;
        }    
    }
    return $result;
}
function hontza_download_sources_txt_callback(){
    $sources=hontza_get_all_sources(1);
    if(!empty($sources)){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $salto_linea="\r\n";
            $file = date('Ymd-His');
            $prefijo=t('Sources');
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=$prefijo-$file.txt");
            header("Pragma: no-cache");
            header("Expires: 0");
            $output = fopen("php://output", "w");
            //
            fputs($output,t('SOURCES GROUP').': '.$my_grupo->title.$salto_linea);
            fputs($output,$salto_linea);
            fputs($output,$salto_linea);
            foreach($sources as $i=>$row){
                $node=node_load($row->nid);
                if(isset($node->nid) && !empty($node->nid)){                
                    $creador=hontza_get_username($node->uid);
                    $tipo_array=hontza_get_source_tipos_de_fuentes($node);                
                    fputs($output,t('Title').': '.$node->title.$salto_linea);
                    //intelsat-2015
                    $url_fuente_txt=hontza_canal_rss_get_url_fuente_txt($node);
                    fputs($output,'Url: '.$url_fuente_txt.$salto_linea);                    
                    //
                    fputs($output,t('Creator').': '.$creador.$salto_linea);
                    if(!empty($tipo_array)){
                        $kont=0;
                        foreach($tipo_array as $tid=>$term){
                            if($kont<1){
                                fputs($output,t('Type').': '.$term->name.$salto_linea);
                            }else{
                                fputs($output,'      '.$term->name.$salto_linea);
                            }
                            $kont++;
                        }
                    }
                    fputs($output,t('Text').': '.strip_tags($node->body).$salto_linea);
                    fputs($output,t('Quality').': '.hontza_get_fuente_stars_value($node,'calidad').$salto_linea);
                    fputs($output,t('Coverage').': '.hontza_get_fuente_stars_value($node,'exhaustividad').$salto_linea);
                    fputs($output,t('Update').': '.hontza_get_fuente_stars_value($node,'actualizacion').$salto_linea);
                    fputs($output,$salto_linea);
                }
            }
            fclose($output);
            exit();
        }    
    }
    return t('There are no contents');
}
function hontza_get_all_sources($is_current_group=0,$group_nid=''){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='n.type IN("supercanal","fuentedapper")';
    if(!empty($is_current_group)){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        }
    }
    //
    if(!empty($group_nid)){
        $where[]='og_ancestry.group_nid='.$group_nid;
    }
    //
    $sql='SELECT n.*
    FROM {node} n
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
    WHERE '.implode(' AND ',$where).'
    ORDER BY n.title ASC';
    //print $sql;exit();
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_get_tipos_de_fuentes_vid(){
    $v=my_vocabulary_load('Categoría de la fuente');
    if(isset($v->vid) && !empty($v->vid)){
        return $v->vid;
    }
    return 1;
}
function hontza_get_source_tipos_de_fuentes($node,$vid_in=''){
    $result=array();
    if(empty($vid_in)){
        $vid=hontza_get_tipos_de_fuentes_vid();
    }else{
        $vid=$vid_in;
    }
    if(isset($node->taxonomy) && !empty($node->taxonomy)){
        foreach($node->taxonomy as $tid=>$term){
            if(isset($term->vid) && !empty($term->vid) && $term->vid==$vid){
                $result[$tid]=$term;
            }
        }
    }
    return $result;
}
function hontza_get_fuente_stars_value($node,$sufijo,$is_view=0){
    $field='field_'.$node->type.'_'.$sufijo;
    return hontza_get_fuente_stars_view($node, $field,$is_view);
}
function hontza_download_channels_txt_callback(){
    //$grupo_array=get_all_nodes(array('grupo'));
    //if(!empty($grupo_array)){
        //foreach($grupo_array as $i=>$grupo){
            
        //}
    //}
    
    //$informacion_array=informacion_get_all_array();
    $informacion_array=informacion_get_array();
    //intelsat-2015
    $canal_nid_temp_array=array();
    $num=count($informacion_array);
    $informacion_array[$num]=new stdClass();
    $informacion_array[$num]->nid='others';
    //
    if(!empty($informacion_array)){
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            return t('There are no contents');
        }
        $salto_linea="\r\n";
        $tab="\t";
        $file = date('Ymd-His');
        $prefijo=t('Channels');
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=$prefijo-$file.txt");
        header("Pragma: no-cache");
        header("Expires: 0");
        $output = fopen("php://output", "w");
        //
        fputs($output,'CHANNELS GROUP: '.$my_grupo->title.$salto_linea);
        fputs($output,$salto_linea);
        fputs($output,$salto_linea);
        foreach($informacion_array as $i=>$row){
            //intelsat-2015
            if($row->nid=='others'){
                $informacion=new stdClass();
                $informacion->nid=$row->nid;
            }else{
            //    
                $informacion=node_load($row->nid);
            }
            if(isset($informacion->nid) && !empty($informacion->nid)){
                //intelsat-2015
                if($informacion->nid=='others'){
                    fputs($output,t('Others').$salto_linea);
                    $canal_array=hontza_canal_rss_get_others_canal_array($canal_nid_temp_array);
                }else{
                    fputs($output,despliegue_list_camino_para_txt($informacion).$salto_linea);
                    $canal_array=informacion_get_canal_informacion_array('',$informacion->nid);                                
                }
                //intelsat-2015
                if($informacion->nid!='others'){
                    fputs($output,'=>'.$informacion->title.$salto_linea);
                }
                if(!empty($canal_array)){
                    fputs($output,$salto_linea);
                    foreach($canal_array as $i=>$canal_row){
                        $canal=node_load($canal_row->nid);
                        if(isset($canal->nid) && !empty($canal->nid)){
                            //intelsat-2015
                            $canal_nid_temp_array[]=$canal->nid;
                            //
                            $creador=hontza_get_username($canal->uid);
                            $categorias_tematicas_array=hontza_get_canal_categorias_tematicas_array($canal);                
                            fputs($output,$tab.t('Title').': '.$canal->title.$salto_linea);
                            //intelsat-2015
                            $url_canal_txt=hontza_canal_rss_get_url_canal_txt($canal);
                            fputs($output,$tab.'RSS: '.$url_canal_txt.$salto_linea);                    
                            //
                            fputs($output,$tab.t('Creator').': '.$creador.$salto_linea);                            
                            if(!empty($categorias_tematicas_array)){
                                $kont=0;
                                foreach($categorias_tematicas_array as $tid=>$term){
                                    if($kont<1){
                                        fputs($output,$tab.t('Thematic Categories').': '.$term->name.$salto_linea);
                                    }else{
                                        fputs($output,$tab.'                      '.$term->name.$salto_linea);
                                    }
                                    $kont++;
                                }
                            }
                            $validador_principal=hontza_get_canal_validador_principal_name($canal);
                            $segundo_validador=hontza_get_canal_segundo_validador_name($canal);
                            fputs($output,$tab.t('Main Validator').': '.$validador_principal.$salto_linea);
                            fputs($output,$tab.t('Second Validator').': '.$segundo_validador.$salto_linea);
                            if($canal->type=='canal_de_supercanal'){
                                $canal_params=hontza_get_canal_params($canal);
                                if(!empty($canal_params)){
                                    foreach($canal_params as $i=>$params){
                                        if($i<1){
                                            fputs($output,$tab.t('Search arguments').': '.$params['description'].':'.$params['value'].$salto_linea);
                                        }else{
                                            fputs($output,$tab.'                        '.$params['description'].':'.$params['value'].$salto_linea);
                                        }    
                                    }        
                                }
                            }else if($canal->type=='canal_de_yql'){                                
                                if(hontza_is_hound_canal($canal->nid)){
                                    $kont_hound_param=0;
                                    $canal_hound_parametros=hound_get_canal_hound_parametros_row($canal->nid);
                                    if(isset($canal_hound_parametros->hound_title) && !empty($canal_hound_parametros->hound_title)){
                                        fputs($output,$tab.t('Hound').': '.$canal_hound_parametros->hound_title.$salto_linea);
                                    }
                                    if(isset($canal_hound_parametros->hound_id) && !empty($canal_hound_parametros->hound_id)){
                                        fputs($output,$tab.t('Hound Channel Id').': '.$canal_hound_parametros->hound_id.$salto_linea);
                                    }        
                                    if(isset($canal_hound_parametros->parametros) && !empty($canal_hound_parametros->parametros)){                                         
                                         $parametros=unserialize($canal_hound_parametros->parametros);
                                         if(!empty($parametros)){
                                            foreach($parametros as $param_key=>$param_row){
                                                foreach($param_row as $param_name=>$param_value){
                                                    $s=hound_api_param_key_label($param_name).' ('.$param_key.')';
                                                    $s.=': '.$param_value;
                                                    fputs($output,$tab.$s.$salto_linea);
                                                    /*if($kont_hound_param<1){
                                                        fputs($output,$tab.t('Search arguments'). :'.$s.$salto_linea);
                                                    }else{
                                                        fputs($output,$tab.'                        '.$s.$salto_linea);
                                                    }*/
                                                    $kont_hound_param++;
                                                }
                                            }
                                         }   
                                    }
                                }else{
                                    $canal_params=hontza_get_canal_yql_parametros_row($canal->vid,$canal->nid);
                                    if(!empty($canal_params)){
                                        if(hontza_is_canal_params_filtro1($canal_params)){
                                            fputs($output,$tab.t('Search arguments').': '.t('Apply filter 1 to RSS feeds').$salto_linea);
                                            fputs($output,$tab.$tab.t('General Search').': '.$canal_params->todos.$salto_linea);
                                        }else if(hontza_is_canal_params_filtro2($canal_params)){
                                            $yql_op='OR';
                                            if(!empty($canal_params->filtrosSI)){
                                                $yql_op='AND';
                                            }    
                                            fputs($output,$tab.t('Search arguments').': '.t('Apply filter 2 to RSS feeds').$salto_linea);
                                            fputs($output,$tab.$tab.t('Contains this word in the title').': '.$canal_params->titulo.$salto_linea);
                                            fputs($output,$tab.$tab.$yql_op.$salto_linea);
                                            fputs($output,$tab.$tab.t('Contains this word in the description').': '.$canal_params->descripcion.$salto_linea);
                                        }else if(hontza_is_canal_params_filtro3($canal_params)){
                                            $yql_op='OR';
                                            if(!empty($canal_params->filtrosNO)){
                                                $yql_op='AND';
                                            }
                                            fputs($output,$tab.t('Search arguments').': '.t('Apply filter 3 to RSS feeds').$salto_linea);
                                            fputs($output,$tab.$tab.t('It does not contain this word in the title').': '.$canal_params->no_titulo.$salto_linea);                                            
                                            fputs($output,$tab.$tab.$yql_op.$salto_linea);
                                            fputs($output,$tab.$tab.t('It does not contain this word in the description').': '.$canal_params->no_descripcion.$salto_linea);
                                        }else if(hontza_is_canal_params_filtro4($canal_params)){
                                            $contiene_field='title';
                                            if(!empty($canal_params->campo_contiene)){
                                                $contiene_field='description';
                                            }
                                            //
                                            $no_contiene_field='title';
                                            if(!empty($canal_params->campo_no_contiene)){
                                                $no_contiene_field='description';
                                            }
                                            //
                                            $yql_op='OR';
                                            if(!empty($canal_params->conjuncion)){
                                                $yql_op='AND';
                                            }
                                            fputs($output,$tab.t('Search arguments').': '.t('Apply filter 4 to RSS feeds').$salto_linea);
                                            fputs($output,$tab.$tab.html_entity_decode(t('Contains this word in the '.$contiene_field)).': '.$canal_params->contiene.$salto_linea);                                            
                                            fputs($output,$tab.$tab.$yql_op.$salto_linea);
                                            fputs($output,$tab.$tab.html_entity_decode(t('It does not contain this word in the '.$no_contiene_field)).': '.$canal_params->no_contiene.$salto_linea);
                                        }else if(hontza_is_canal_params_filtro5($canal_params)){
                                            fputs($output,$tab.t('Search arguments').': '.t('Apply filter 5 to RSS feeds').$salto_linea);
                                            fputs($output,$tab.$tab.html_entity_decode(t('Code to create the filter manually')).': '.$canal_params->area.$salto_linea);                                        
                                        }else{
                                            //
                                        }
                                    }
                                }
                            }                            
                            fputs($output,$tab.t('Url of Html page').': '.hontza_get_url_html_value($canal).$salto_linea);
                            fputs($output,$tab.t('Rating').': '.hontza_get_node_puntuacion_media_para_txt($canal->nid).$salto_linea);
                            fputs($output,$salto_linea);                            
                        }
                    }
                }
                fputs($output,$salto_linea);
                fputs($output,$salto_linea);
            }
        }
        fclose($output);
        exit();
    }
    
    return t('There are no contents');
}
function hontza_get_canal_categorias_tematicas_array($node){
    if(isset($node->og_groups) && !empty($node->og_groups)){
        $group_nid_array=array_keys($node->og_groups);
        if(isset($group_nid_array[0])){
            $grupo_nid=$group_nid_array[0];
            $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",$grupo_nid));
            if(!empty($id_categoria)){
                return hontza_get_source_tipos_de_fuentes($node,$id_categoria);
            }
        }    
    }    
    return $result;
}
function hontza_get_canal_validador_principal_name($node){
    if(isset($node->field_responsable_uid) && isset($node->field_responsable_uid[0]) && isset($node->field_responsable_uid[0]['uid'])){
        $uid=$node->field_responsable_uid[0]['uid'];
        if(!empty($uid)){
            return hontza_get_username($uid);
        }
    }
    return '';
}
function hontza_get_canal_segundo_validador_name($node){
    if(isset($node->field_responsable_uid2) && isset($node->field_responsable_uid2[0]) && isset($node->field_responsable_uid2[0]['uid'])){
        $uid=$node->field_responsable_uid2[0]['uid'];
        if(!empty($uid)){
            return hontza_get_username($uid);
        }
    }
    return '';
}
function hontza_get_avg_rating($nid){
    $where=array();
    $where[]="1";
    $where[]="v.content_type='node'";
    $where[]="v.tag='vote'";
    $where[]="v.function='average'";
    $where[]="v.content_id=".$nid;
    $sql="SELECT v.* FROM {votingapi_cache} v WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        //$result[]=$row;            
        return $row;
    }
    //
    $my_result=(object) array();
    return $my_result;
}
function hontza_get_node_puntuacion_media_para_txt($nid,$is_value=0){
    $value=0;
    $canal_rating=hontza_get_avg_rating($nid);
    if(isset($canal_rating->value) && !empty($canal_rating->value)){
        $value=$canal_rating->value;        
    }
    if($is_value){
        return $value;
    }
    return hontza_get_puntuacion_media_texto_para_txt($value);
}
function hontza_get_puntuacion_media_texto_para_txt($value){
    $v=(int) $value;
    if($v<1){
        return t('No votes');
    }else if($v>0 && $v<=20){
        return t('Poor');
    }else if($v>20 && $v<=40){
        return t('Okey');
    }else if($v>40 && $v<=60){
        return t('Good');
    }else if($v>60 && $v<=80){
        return t('Great');
    }else if($v>80){
        return t('Awesome');
    }
}
function hontza_get_url_html_value($canal){
    if(isset($canal->field_url_html[0])){
        if(isset($canal->field_url_html[0]['value'])){
            return $canal->field_url_html[0]['value'];
        }
    }
    return '';
}
function hontza_delete_carpeta_dinamica($node_in=''){
    global $user;
	if(empty($node_in)){
            $node=hontza_get_carpeta_dinamica();
        }else{
            $node=$node_in;            
        }
        if(isset($node->nid) && !empty($node->nid)){
            $node->uid=$user->uid;
            node_save($node);
            node_delete($node->nid);
        }
}
function hontza_get_carpeta_dinamica(){
        $my_query=my_get_query();
	if(!empty($my_query)){
		$node=my_load_carpeta_dinamica(array('field_canal_busqueda_busqueda_value'=>$my_query));			
                return $node;
        }
        $my_result=new stdClass();
        return $my_result;
}
function hontza_carpeta_dinamica_borrar_access(&$node_carpeta_dinamica){
    global $user;
    boletin_report_no_group_selected_denied();
    $ok=0;
    $node_carpeta_dinamica=hontza_get_carpeta_dinamica();
    if(isset($node_carpeta_dinamica->uid) && !empty($node_carpeta_dinamica->uid)){
        if(isset($user->uid) && !empty($user->uid)){
            if($node_carpeta_dinamica->uid==$user->uid){
                $ok=1;
            }else{
                if(hontza_is_field_administrador_de_grupo()){
                    $ok=1;
                }
            }
            if(is_user_administrador_de_grupo($user)){
                $ok=1;
            }
        }
    }
    if(!$ok){
        drupal_access_denied();
        return 0;
    }
    return 1;
}
function hontza_is_carpeta_dinamica_form_filter($form_id){
    $find='views-exposed-form-canal-busqueda-page';
    $pos=strpos($form_id,$find);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_is_carpeta_dinamica_selected(&$node_carpeta_dinamica){
    $node_carpeta_dinamica=hontza_get_carpeta_dinamica();
    if(isset($node_carpeta_dinamica->nid) && !empty($node_carpeta_dinamica->nid)){
        return 1;
    }
    return 0;
}
function hontza_is_field_administrador_de_grupo(){
    global $user;
    if(is_super_admin()){
        return 1;
    }
    $grupo=og_get_group_context();
    $uid='';
    //
            if(isset($grupo->field_admin_grupo_uid[0]['uid'])){
                $uid=$grupo->field_admin_grupo_uid[0]['uid'];
            }else if(isset($grupo->field_admin_grupo_uid[0]['value'])){
                $uid=$grupo->field_admin_grupo_uid[0]['value'];
            }
    //        
    if(!empty($uid)){
        if(isset($user->uid) && !empty($user->uid)){
            if($uid==$user->uid){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_set_logo_url_facilitadores($my_array_in,$is_system){
    $my_array=$my_array_in;
                if(isset($my_array[1]) && $is_system){
                    $my_array[1]=str_replace('"/>','',$my_array[1]);
                    $basename=basename($my_array[1]);
                    $my_array[1]=$base_url.'/system/files/'.$basename.'"/>';                    
                }
    return $my_array;            
}
function hontza_define_hound_sareko_id_array(){
    $result=array();
    /*$hound_is_active=variable_get('hound_is_active',0);
    if($hound_is_active){
        $result[]=_SAREKO_ID;
    }*/
    if(defined('_IS_HOUND') && _IS_HOUND==1){
        $result[]=_SAREKO_ID;
    }
    return $result;
}
function hontza_unlink_wiki_callback(){
    //intelsat-2015
    hontza_solr_search_is_usuario_lector_access_denied();
    //
    $nid=arg(1);
    $wiki_nid=arg(3);
    hontza_delete_enlazar_wiki($wiki_nid,$nid);
    if(isset($_REQUEST['destination'])){
        drupal_goto($_REQUEST['destination']);
    }else{
        drupal_goto('node/'.$nid.'/enlazar_wiki');
    }
    exit();
}
function hontza_unlink_debate_callback(){
    //intelsat-2015
    hontza_solr_search_is_usuario_lector_access_denied();
    //
    $nid=arg(1);
    $debate_nid=arg(3);
    hontza_delete_enlazar_debate($debate_nid,$nid);
    if(isset($_REQUEST['destination'])){
        drupal_goto($_REQUEST['destination']);
    }else{
        drupal_goto('node/'.$nid.'/enlazar_debate');
    }
    exit();
}
function hontza_create_carpeta_dinamica_validador_filter_field(&$form,&$form_state, $form_id){
    $my_array=array(
		  'operator' => 'responsable_uid_op',
		  'value' => 'responsable_uid',
		  //'label' => t($my_text.' (múltiplos de 20)'));
		  'label'=>t('Validator').':');
    //
    $my_array2=array(
	  //'#title' => t('Validator'),
	  '#type' => 'textfield',
	  //'#required' => $is_required,	 
	  //'#maxlength' => 60,
          '#maxlength' => 255,
	  //'#autocomplete_path' => 'user/autocomplete',
	  '#autocomplete_path' =>'userreference/autocomplete/field_responsable_uid',
	  '#default_value' => '',
	  //'#weight' => -10,
          '#attributes'=>array('class'=>'carpeta_dinamica_validator'),  
	);
    //
	$form['#info']['filter-responsable_uid']=$my_array;
	$form['responsable_uid']=$my_array2;        
}
function hontza_copy_server_channels_link($is_label_long=1){
    if(hontza_is_sareko_id_red()){
        $url='';
        if(hontza_is_red_hoja()){
           $url='red_compartir/red_compartir_copiar_canales_servidor';
           if($is_label_long){
               //$label=t('Copy channels from Network server');
               $label=t('Download channels from RedAlerta server');
           }else{
               //$label=t('Copy channels');
               $label=t('Download Channels');
           }
           //intelsat-2015
           //return l($label,$url,array('query'=>drupal_get_destination()));
           return l($label,$url);
           //
        }        
    }
    return '';
}
function hontza_on_save_source($op,&$node){
    $is_change_pipe_id=0;
    if(in_array($node->type,array('supercanal','fuentedapper'))){
        if($op=='update'){
            $nid_fuente_array=hontza_get_content_field_nid_fuente_canal($node->nid);
            if(!empty($nid_fuente_array)){
                foreach($nid_fuente_array as $i=>$row){
                    hontza_update_content_field_nombrefuente_canal($row->vid,$row->nid,$node->title);
                }
            }
            //intelsat-2014            
            $is_change_pipe_id=red_funciones_is_change_pipe_id($node);
            //
        }
        red_regiones_on_save_source($op,$node);
    }
    if($is_change_pipe_id){
        $_REQUEST['destination']='hontza/validar_cambio_fuente_pipe_id/'.$node->nid;        
    }
}
function hontza_get_content_field_nid_fuente_canal($fuente_nid){
    $result=array();
    $where=array();
    $where[]='content_field_nid_fuente_canal.field_nid_fuente_canal_value='.$fuente_nid;
    $sql='SELECT * FROM {content_field_nid_fuente_canal} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;        
    }
    return $result;
}
function hontza_update_content_field_nombrefuente_canal($canal_vid,$canal_nid,$fuente_title){
    /*$nombrefuente_array=hontza_get_content_field_nombrefuente_canal($canal_vid,$canal_nid);
    foreach($nombrefuente_array as $i=>$row){
        hontza_update_content_field_nombrefuente_canal($row->vid,$row->nid,$node->title);
    }*/
    db_query('UPDATE {content_field_nombrefuente_canal} SET field_nombrefuente_canal_value="%s" WHERE vid=%d AND nid=%d',$fuente_title,$canal_vid,$canal_nid);
}
function hontza_get_content_field_nombrefuente_canal($canal_vid,$canal_nid){
    $result=array();
    $where=array();
    $where[]='vid='.$canal_vid;
    $where[]='nid='.$canal_nid;
    $sql='SELECT * FROM {content_field_nombrefuente_canal} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;        
    }
    return $result;
}
function hontza_get_content_field_nombrefuente_canal_value($vid,$nid){
    $my_array=hontza_get_content_field_nombrefuente_canal($vid,$nid);
    if(!empty($my_array)){
        return $my_array[0]->field_nombrefuente_canal_value;
    }
    return '';
}
function hontza_red_compartir_facilitador_link($node){
    if(hontza_is_sareko_id_red()){
        if(red_compartir_grupo_is_grupo_red_alerta()){   
            $url='';
            if(hontza_is_red_hoja()){
               $url='red_compartir/compartir_facilitador_hoja/'.$node->nid;
            }
            if(!empty($url)){
                $html=array();
                $html[]='<div class="field field-type-text field-field-canal-red-compartir-facilitador" style="float:left;clear:both;">';
                $html[]=l(t('Share Facilitator'),$url);
                $html[]='</div>';
                return implode('',$html);
            }
        }    
    }
    return '';
}
function hontza_copy_server_facilitators_link($is_label_long=1){
    if(hontza_is_sareko_id_red()){
        $url='';
        if(hontza_is_red_hoja()){
           $url='red_compartir/red_compartir_copiar_facilitadores_servidor';
           if($is_label_long){
               //$label=t('Copy facilitators from Network server');
               $label=t('Download facilitators from RedAlerta server');
           }else{
               //$label=t('Copy facilitators');
               $label=t('Download Facilitators');
           }
           //intelsat-2015
           //return l($label,$url,array('query'=>drupal_get_destination()));
           return l($label,$url);
        }        
    }
    return '';
}
function hontza_user_access_recursos_red_alerta(){
    if(hontza_is_servidor_red_alerta()){
        return 1;
    }
    return 0;
}
function hontza_is_servidor_red_alerta(){
    if(hontza_is_sareko_id_red()){
        if(red_is_servidor_central()){
            return 1;
        }
        //simulando
        /*if(hontza_is_sareko_id('LOKALA')){
            return 1;
        }*/
    }
}
function hontza_is_user_anonimo(){
    global $user;
    if(isset($user->uid) && !empty($user->uid)){
        return 0;
    }
    return 1;
}
function hontza_unset_all_option($result_in){
    $result=$result_in;
    if(isset($result['All'])){
        unset($result['All']);
        $options[0]='';
        $options=array_merge($options,$result);
        return $options;
    }
    return $result;
}
function hontza_define_clasificaciones_fuente($with_empty=0){
  $padres = taxonomy_get_tree(1, 0, -1, 1);
  $taxo = array();
  if($with_empty){
      $taxo[0]='';
  }
  foreach ($padres as $padre){
    $taxo[$padre->tid] = $padre->name;
    $hijos = taxonomy_get_children($padre->tid);
    if (!empty($hijos)){
      foreach ($hijos as $hijo){
        $taxo[$hijo->tid] = '--'.$hijo->name;
      }
    }
  }
  return $taxo;
}  
function hontza_is_fivestar_enabled($node){
    if(isset($node->content)){
        if(isset($node->content['fivestar_widget'])){
            if(isset($node->content['fivestar_widget']['#value'])){
                return 1;
            }
        }    
    }
    return 0;
}
function hontza_is_area_debate_node_list($konp=''){
    $param0=arg(0);
    if(!empty($param0) && $param0=='area-debate'){
        if(empty($konp)){
            return 1;
        }else{
            $param1=arg(1);
            if(!empty($param1) && $param1==$konp){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_area_debate_create_menu_node_list(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    //intelsat-2015
    $last_title=t('Latest Comments');
    $html[]='<li'.hontza_area_debate_menu_class('').'>'.l($last_title,'area-debate').'</li>';
    $html[]='<li'.hontza_area_debate_menu_class('lo-mas-valorado').'>'.l(t('Top Rated Discussions'),'area-debate/lo-mas-valorado').'</li>';
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    //
    $output=implode('',$html);
    return $output;
}
function hontza_area_debate_menu_class($arg_type){
    $result=0;
    $param0=arg(0);
    if($param0=='area-debate'){
        $param1=arg(1);
        if($param1==$arg_type){
            $result=1;
        }
    }    
    if($result){
        return ' class="active"';
    }
    return '';
}
function hontza_is_area_trabajo_node_list($konp=''){
    $param0=arg(0);
    if(!empty($param0) && $param0=='area-trabajo'){
        if(empty($konp)){
            return 1;
        }else{
            $param1=arg(1);
            if(!empty($param1) && $param1==$konp){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_area_trabajo_create_menu_node_list(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $last_title=t('Latest Inputs');
    $html[]='<li'.hontza_area_trabajo_menu_class('').'>'.l($last_title,'area-trabajo').'</li>';
    $html[]='<li'.hontza_area_trabajo_menu_class('lo-mas-valorado').'>'.l(t('Top Rated Documents'),'area-trabajo/lo-mas-valorado').'</li>';
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    //
    $output=implode('',$html);
    return $output;
}
function hontza_area_trabajo_menu_class($arg_type){
    $result=0;
    $param0=arg(0);
    if($param0=='area-trabajo'){
        $param1=arg(1);
        if($param1==$arg_type){
            $result=1;
        }
    }    
    if($result){
        return ' class="active"';
    }
    return '';
}
function hontza_user_access_red_local(){
    //gemini
    if(module_exists('red_local')){
        if(hontza_is_red_hoja()){
            if(hontza_is_sareko_user()){
                if(red_compartir_grupo_is_grupo_red_alerta('')){
                    return 1;
                }    
            }

        }
    }
    return 0;
}
function hontza_is_group_active_refresh($job){
    if($job['callback']=='feeds_source_import'){
        $active_refresh=variable_get('active_refresh_subdomain',0);
        if(empty($active_refresh)){
            return 0;
        }
        $feed_nid=$job['id'];
        //simulando
        //$feed_nid=163175;
        $node=node_load($feed_nid);
        if(isset($node->nid) && !empty($node->nid)){
            if(isset($node->og_groups) && !empty($node->og_groups) && is_array($node->og_groups)){
               $group_nid_array=array_values($node->og_groups);
               if(count($group_nid_array)>0){
                    $group_nid=$group_nid_array[0];
                    $grupo_node=node_load($group_nid);
                    if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
                        if(isset($grupo_node->field_group_active_refresh) && isset($grupo_node->field_group_active_refresh[0]) && isset($grupo_node->field_group_active_refresh[0]['value'])){                            
                           $v=$grupo_node->field_group_active_refresh[0]['value'];
                           if(!empty($v) && $v==1){
                                if(hontza_is_congelar_canal_sareko_id()){
                                    if(hontza_is_canal_congelado($node)){
                                        return 0;
                                    }
                                    return 1;
                                }else{
                                    //return 1;
                                    //intelsat-2015 
                                    return hontza_canal_comodin_is_congelado_by_comodin($node);
                                }    
                           }                                    
                        }else{                           
                           //return 1;
                           //intelsat-2015 
                           return hontza_canal_comodin_is_congelado_by_comodin($node); 
                        }    
                    }                            
               } 
            }    
        }    
    }
    return 0;
}
function hontza_activar_actualizacion_subdominio_form(){
    drupal_set_title(t('Activate all Channels'));
    $form=array();
    $active_refresh=variable_get('active_refresh_subdomain',0);
    $form['active_refresh']=array(
        '#type'=>'checkbox',
        '#title'=>t('Activate all Channels'),        
    );
    if(!empty($active_refresh)){
        $form['active_refresh']['#attributes']['checked']='checked';
    }
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    //gemini-2014
    //$form['my_cancel']['#value']=l(t('Cancel'),'user-gestion/grupos/propios');
    //intelsat-2015
    //$form['my_cancel']['#value']=l(t('Cancel'),'gestion');
    $form['my_cancel']['#value']=l(t('Cancel'),'panel_admin');
    //
    return $form;
}
function hontza_activar_actualizacion_subdominio_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='my_confirm_btn'){
           $values=$form_state['values'];
           if(isset($values['active_refresh'])){
               variable_set('active_refresh_subdomain',$values['active_refresh']);
           }else{
               variable_set('active_refresh_subdomain',0);
           }
        }                
    }
    //gemini-2014
    //drupal_goto('user-gestion/grupos/propios');
    //intelsat-2015
    //drupal_goto('gestion');
    $_REQUEST['destination']='';
    drupal_goto('panel_admin');
    //
}
function hontza_define_red_sareko_id_array($with_lokala=1){
    //AVISO::::hontza_is_red_hoja mirar ahi tambien
    $sareko_id_array=array();
    //intelsat-2016
    if(red_is_servidor_central()){
        $sareko_id_array[]=_SAREKO_ID;
    }
    if($with_lokala){
        $sareko_id_array[]='LOKALA';
    }
    if(defined('_IS_RED_HOJA') && _IS_RED_HOJA==1){
        $sareko_id_array[]=_SAREKO_ID;
    }
    return $sareko_id_array;
}
function hontza_is_item_duplicado($elemento,$source='',$canal_in='',$url_rss_in=''){
    //intelsat-2015
    //if(hontza_is_sareko_id_desduplicados()){
    if(hontza_is_sareko_id_desduplicados($source)){
        //intelsat-2016
        /*
        //intelsat-2015
        if(hontza_solr_search_is_canal_correo($source,$canal_in,$url_rss_in)){
            return 0;
        }
        //
        $info_cut=hontza_get_item_url_cut($elemento);
        $item_uniq_array=hontza_get_guid_url_item_array($elemento,1,$source,$info_cut);
        //echo print_r($item_uniq_array,1);exit();
        if(count($item_uniq_array)>0){
            return 1;
        }
        //$uniqTxtID=hontza_get_elemento_uniq($elemento);
        //$item_uniq_txt_id_array=hontza_get_uniq_item_array($uniqTxtID,1,$source);
        //if(count($item_uniq_txt_id_array)>0){
        //    return 1;
        //}
        */
        return red_despacho_is_item_duplicado($elemento,$source,$canal_in,$url_rss_in,1);
    }else{
        //intelsat-2016
        if(!red_despacho_is_sareko_id_desduplicados($source)){
            return red_despacho_is_item_duplicado($elemento,$source,$canal_in,$url_rss_in,0);
        }
    }
    return 0;
}
function hontza_uniqTxtID($content){
            $content= trim(strip_tags($content));
            $c1=strtolower($content);
            $c1=preg_replace('/[^A-Za-z0-9]/','',$c1);
            $r=md5($c1);
            return $r;
}
//intelsat-2015
//function hontza_is_sareko_id_desduplicados(){
function hontza_is_sareko_id_desduplicados($source_in=''){
    /*if(defined('_IS_DESDUPLICADOS')){
        if(_IS_DESDUPLICADOS==1){
            return 1;
        }
        return 0;
    }*/
    if(!red_despacho_is_sareko_id_desduplicados($source_in)){
        return 0;
    }
    return 1;
}
function hontza_get_uniq_item_array($uniq,$with_grupo=1,$source=''){
    $result=array();
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    if($with_grupo=1 && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }else if($with_grupo){
            $my_grupo=hontza_get_grupo_by_feed_nid($source);
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $where[]='og_ancestry.group_nid='.$my_grupo->nid;
            }
    }   
    //
    if(!empty($uniq)){
        $where[]='c.field_uniq_value="'.$uniq.'"';
    }
    $where[]='(NOT c.nid IS NULL)'; 
    $sql='SELECT c.*
    FROM {node} n 
    LEFT JOIN {content_type_item} c ON n.vid=c.vid 
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
    WHERE '.implode(' AND ',$where);
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_get_elemento_uniq($elemento){
        $content=(string) $elemento->title;
        $content.=(string) $elemento->description;        
        $uniqTxtID=hontza_uniqTxtID($content);
        return $uniqTxtID;
}
function hontza_fix_uniq_item_callback(){
    return 'Funcion desactivada';
    $item_array=hontza_get_uniq_item_array('',0);
    if(!empty($item_array)){    
        foreach($item_array as $i=>$row){
           if(isset($row->field_uniq_value) && !empty($row->field_uniq_value)){
           //if($row->nid!=170318 || $row->nid==176358){
           //if($row->nid!=176358){ 
                continue;
           }else{
               $node=node_load($row->nid);
               if(isset($node->nid) && !empty($node->nid)){
                    //echo print_r($node,1);
                    //exit();
                    $elemento=new stdClass();
                    $elemento->title=$node->title;
                    $elemento->description=$node->body;
                    if(empty($elemento->description)){
                        //print $node->teaser;exit();
                        $elemento->description=$node->teaser;
                    }
                    //print $elemento->description;exit();
                    $uniq=hontza_get_elemento_uniq($elemento);
                    db_query('UPDATE {content_type_item} SET field_uniq_value="%s" WHERE nid=%d AND vid=%d',$uniq,$row->nid,$row->vid);
               }             
           }
        }
    }
    return date('Y-m-d H:i:s');
}
function hontza_link_exportar_rss_canal($nid){
    $html = '<span class="link_exportar_rss_canal">';
    $html .=  l(t('Export RSS channel'), 'red_exportar_rss/canal/'.$nid,array('attributes'=>array('target'=>'_blank')));
    $html .= '</span>';
    return $html;
}
function hontza_is_congelar_canal_sareko_id(){
    return 1;
}
function hontza_activar_actualizacion_canal_form(){
    drupal_set_title(t('Activated channel'));
    $canal_nid=arg(1);
    $form=array();
    $active_refresh=0;
    $canal=node_load($canal_nid);
    if(isset($canal->nid) && !empty($canal->nid)){
        $v=hontza_get_content_field_canal_active_refresh_value($canal);
        if(!empty($v)){
            $active_refresh=$v;
        }
    }
    $form['my_nid']=array(
        '#type'=>'hidden',
        '#value'=>$canal_nid,
    );
    $form['active_refresh']=array(
        '#type'=>'checkbox',
        '#title'=>t('Activated channel'),        
    );
    if(!empty($active_refresh)){
        $form['active_refresh']['#attributes']['checked']='checked';
    }
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'node/'.$canal_nid);
    return $form;
}
function hontza_get_content_field_canal_active_refresh_value($canal){
    $row=hontza_get_content_field_canal_active_refresh_row($canal);
    if(isset($row->nid) && !empty($row->nid)){
        $v=$row->field_canal_active_refresh_value;
        if(!empty($v)){
            return 1;
        }
        //return 0;
    }
    return 0;
}
function hontza_get_content_field_canal_active_refresh_row($canal){
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_canal_active_refresh} WHERE nid=%d AND vid=%d',$canal->nid,$canal->vid));
    //print $sql;
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function hontza_activar_actualizacion_canal_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='my_confirm_btn'){
           $values=$form_state['values'];
           $active_refresh=0;
           if(isset($values['active_refresh']) && !empty($values['active_refresh'])){
               $active_refresh=$values['active_refresh'];
           }
           $canal_nid=$values['my_nid'];
           //
           $canal=node_load($canal_nid);
           if(isset($canal->nid) && !empty($canal->nid)){
               $res=db_query('UPDATE {content_field_canal_active_refresh} SET field_canal_active_refresh_value=%d WHERE nid=%d AND vid=%d',$active_refresh,$canal->nid,$canal->vid);
           }
        }                
    }
    drupal_goto('node/'.$canal_nid);
}
function hontza_is_canal_congelado($node){
    $v=hontza_get_content_field_canal_active_refresh_value($node);
    if(!empty($v) && $v==1){
        return 0;
    }
    return 1;
}
function hontza_is_sareko_user($with_servidor=1,$my_user='',$sin_mirar_grupo_compartido_status=0){
    if(hontza_is_sareko_id_red()){        
        if($with_servidor && hontza_is_servidor_red_alerta()){
            return 1;
        }
        $uid='';
        if(isset($my_user->uid) && !empty($my_user->uid)){
            $uid=$my_user->uid;
        }
        $grupo_nid_array=hontza_get_user_group_nid_array($uid);
        if($sin_mirar_grupo_compartido_status){
            $compartir_grupo_array=hontza_get_red_compartir_grupo($grupo_nid_array,'');
        }else{
            $compartir_grupo_array=hontza_get_red_compartir_grupo($grupo_nid_array,1);
        }    
        if(count($compartir_grupo_array)>0){
            return 1;
        }
    }
    return 0;
}
function hontza_get_user_group_nid_array($uid=''){
    global $user;
    $grupo_nid_array=array();
    if(!empty($uid)){
        $my_user=user_load($uid);
    }else{
        $my_user=$user;
    }
    //
    if(isset($my_user->og_groups) && !empty($my_user->og_groups)){
        $grupo_nid_array=array_keys($my_user->og_groups);
    }
    return $grupo_nid_array;
}
function hontza_get_red_compartir_grupo($grupo_nid_array='',$status_in=0){
    $result=array();
    $where=array();
    $where[]='1';
    if(empty($grupo_nid_array)){
       return $result; 
    }else{
       $where[]='nid IN ('.implode(',',$grupo_nid_array).')'; 
    }
    //gemini-2014
    if(!empty($status_in)){
        $status=1;
        //intelsat-2015
        //if($status==2){
        if($status_in==2){
            $status=0;
        }
        $where[]='status='.$status;
    }
    //
    $sql='SELECT * FROM {red_compartir_grupo} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_get_usuarios_grupo($grupo_nid_in=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($grupo_nid_in)){ 
        $grupo_nid=$grupo_nid_in;
    }else{
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid=$my_grupo->nid;
        }else{
            return $result;
        }
    }
    $where[]='u.status=1';    
    $where[]='og_uid.nid='.$grupo_nid;
    //
    $sql='SELECT u.* FROM
    {users} u
    LEFT JOIN {og_uid} og_uid ON u.uid=og_uid.uid
    WHERE '.implode(' AND ',$where).' ORDER BY u.name ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=user_load($row->uid);
    }
    $result=hontza_unset_usuarios_caducados($result);
    return $result;
}
function hontza_content_full_text($node,$is_content_body_value=0){
    $result='';
    //intelsat-2015
    $is_send=0;
    if(hontza_canal_rss_is_visualizador_activado()){
        if(publico_vigilancia_is_send()){
            $is_send=1;
        }
    }
    if($is_send){
        $result=hontza_content_resumen($node);
    }else{
        if(hontza_node_has_body($node)){
            $result=$node->content['body']['#value'];        
        }else{
            if(!$is_content_body_value){
                $my_node=node_load($node->nid);
                if(isset($my_node->teaser)){
                    $result=$my_node->teaser;                
                }
            }    
        }
    }
    //intelsat-2016
    $result=red_solr_inc_resaltar_termino_busqueda($result);
    //
    return $result;
}
//intelsat-2016
//function hontza_get_guid_url_item_array($elemento,$with_grupo=1,$source='',$info_cut=''){
function hontza_get_guid_url_item_array($elemento,$with_grupo=1,$source='',$info_cut='',$is_solo_url=1){        
    $result=array();
    $link='';
    $guid='';
    $url='';
    //intelsat-2016
    $title='';
    //
    if(is_array($elemento)){
        if(isset($elemento['link'])){
            $link=(string) $elemento['link'];
        }
        if(isset($elemento['guid'])){
            $guid=(string) $elemento['guid'];
        }
        if(isset($elemento['url'])){
            $url=(string) $elemento['url'];
        }
        //intelsat-2016
        if(isset($elemento['title'])){
            $title=(string) $elemento['title'];
        }
    }else{
        if(isset($elemento->link)){
            $link=(string) $elemento->link;
        }
        if(isset($elemento->guid)){
            $guid=(string) $elemento->guid;
        }
        if(isset($elemento->url)){
            $url=(string) $elemento->url;
        }
        //intelsat-2016
        if(isset($elemento->title)){
            $title=(string) $elemento->title;
        }    
    }
    if(empty($link) && empty($guid) && empty($url)){
        return $result;
    }else{
        $where=array();
        $where[]='1';
        $or=array();
        if(!empty($link)){
            $or[]='feeds_node_item.url="'.$link.'"';
            $or[]='feeds_node_item.guid="'.$link.'"';
            //intelsat-2015
            if(hontza_is_news_google($link,'link',$link_google)){
                $or[]='feeds_node_item.url LIKE "%%&url='.$link_google.'"';
                $or[]='feeds_node_item.guid LIKE "%%&url='.$link_google.'"';                
            }
            if(isset($info_cut['link']) && !empty($info_cut['link'])){
                $or[]='feeds_node_item.url LIKE "'.$info_cut['link'].'%%"';
                $or[]='feeds_node_item.guid LIKE "'.$info_cut['link'].'%%"';
            }
            $link_ini='';
            $link_end='';
            if(hound_is_item_espacenet($link,$link_ini,$link_end)){
                $or[]='feeds_node_item.url LIKE "'.$link_ini.'%%'.$link_end.'"';
                $or[]='feeds_node_item.guid LIKE "'.$link_ini.'%%'.$link_end.'"';
            }
        }
        if(!empty($guid)){
            $or[]='feeds_node_item.guid="'.$guid.'"';
            if(hontza_is_news_google($guid,'guid',$guid_google)){
                //
            }
            if(isset($info_cut['guid']) && !empty($info_cut['guid'])){
                $or[]='feeds_node_item.url LIKE "'.$info_cut['guid'].'%%"';
                $or[]='feeds_node_item.guid LIKE "'.$info_cut['guid'].'%%"';
            }
            $link_ini='';
            $link_end='';
            if(hound_is_item_espacenet($guid,$link_ini,$link_end)){
                $or[]='feeds_node_item.url LIKE "'.$link_ini.'%%'.$link_end.'"';
                $or[]='feeds_node_item.guid LIKE "'.$link_ini.'%%'.$link_end.'"';
            }
        }
        if(!empty($url)){
            //$is_print=0;
            $or[]='feeds_node_item.url="'.$url.'"';
            if(hontza_is_news_google($url,'url',$url_google)){
                $or[]='feeds_node_item.url LIKE "%%&url='.$url_google.'"';
                $or[]='feeds_node_item.guid LIKE "%%&url='.$url_google.'"';
                //$is_print=1;
            }
            if(isset($info_cut['url']) && !empty($info_cut['url'])){
                $or[]='feeds_node_item.url LIKE "'.$info_cut['url'].'%%"';
                $or[]='feeds_node_item.guid LIKE "'.$info_cut['url'].'%%"';
            }
            $link_ini='';
            $link_end='';
            if(hound_is_item_espacenet($url,$link_ini,$link_end)){
                $or[]='feeds_node_item.url LIKE "'.$link_ini.'%%'.$link_end.'"';
                $or[]='feeds_node_item.guid LIKE "'.$link_ini.'%%'.$link_end.'"';
            }
        }
        //intelsat-2016
        //$where[]='('.implode(' OR ',$or).')';
        $or_url='('.implode(' OR ',$or).')';
        if($is_solo_url){
            $where[]=$or_url;
        }else{
            $where[]=$or_url.' AND n.title="%s"';
        }
        $my_grupo=og_get_group_context();
        //simulando
        //http://192.168.110.210/proba/hontza/estatico.rss
        //$my_grupo='';
        if($with_grupo==1 && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        }else if($with_grupo){
            $my_grupo=hontza_get_grupo_by_feed_nid($source);
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $where[]='og_ancestry.group_nid='.$my_grupo->nid;
            }
            //print implode(' AND ',$where);exit();
        }        
        $where[]='(NOT c.nid IS NULL)'; 
        $sql='SELECT c.*
        FROM {node} n 
        LEFT JOIN {content_type_item} c ON n.vid=c.vid 
        LEFT JOIN {feeds_node_item} feeds_node_item ON c.nid=feeds_node_item.nid
        LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
        WHERE '.implode(' AND ',$where);
        //print $sql;exit();
        /*if($is_print){
            print $sql;exit();
        }*/
        /*if(!empty($info_cut['guid'])){
            print $sql;exit();
        }*/
        //intelsat-2016
        if($is_solo_url){
            $res=db_query($sql);
        }else{
            $res=db_query($sql,$title);
        }
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
        return $result;
    }
}
function hontza_carpeta_dinamica_borrar_add_js($url){
    $js='
        var carpeta_dinamica_borrar_url="'.$url.'";
                        $(document).ready(function(){
                            $("#edit-my-borrar").click(function(){
                                window.location.href=carpeta_dinamica_borrar_url;
                                return false;
                            });    
			});';
			
			drupal_add_js($js,'inline');    
}
function hontza_login_red_alerta_add_js(){
    //intelsat-2015
    if(custom_menu_red_is_activado()){
        return;
    }
    if(hontza_is_sareko_id_red()){
        //$redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
        //$login_red_alerta_url=url($redalerta_servidor_url.'/red_servidor/authenticate_red_alerta',array('absolute'=>TRUE));
        $js='
                            $(document).ready(function(){
                                $(".a_login_red_alerta_class").click(function(){
                                    my_id=$(this).attr("id");
                                    red_alerta_grupo_nid=my_id.replace("login_red_alerta_go_","");
                                    red_alerta_grupo_nid=red_alerta_grupo_nid.replace("login_red_alerta_","");
                                    my_array=red_alerta_grupo_nid.split("_");
                                    len=my_array.length;
                                    subdominio="";
                                    if(len>1){
                                        red_alerta_grupo_nid=my_array[len-1];
                                        sub_array=my_array.slice(0,len-1);
                                        subdominio=sub_array.join("_")+"_";                                        
                                    }
                                    $("#"+subdominio+"id_form_red_alerta_"+red_alerta_grupo_nid).submit();
                                    return false;
                                });                                
                            });';

                            drupal_add_js($js,'inline');
    }                                                 
}
function hontza_login_red_alerta_formulario($login_red_alerta_url_in,$grupo_nid,$is_print=1,$subdominio='',$user_login_enviar_in='',$is_target_blank=0){
    $login_red_alerta_url=url($login_red_alerta_url_in,array('absolute'=>TRUE));
    if(!empty($user_login_enviar_in)){
        $user_login_enviar=$user_login_enviar_in;
    }else{
        $user_login_enviar=hontza_define_user_login_enviar();
    }
    /*$target='';
    if($is_target_blank){*/
        $target=' target="_blank"';
    //}
    $html=array();
    $method='POST';
    //print $login_red_alerta_url.'<BR>';
    $html[]='<form id="'.$subdominio.'id_form_red_alerta_'.$grupo_nid.'" action="'.$login_red_alerta_url.'" method="'.$method.'" style="display:none;"'.$target.'>';
    $html[]='<input type="hidden" name="user_login_enviar" value="'.$user_login_enviar.'">';
    $html[]='</form>';
    $result=implode('',$html);
    /*
    print '-----------------------------------INI----------------------<BR>';
    print $result;
    print '-----------------------------------END----------------------<BR>';*/
    if($is_print){
        print $result;
    }else{        
        return $result;
    }    
}
function hontza_define_user_login_enviar(){
    global $user;
    //$my_user=$user;
    $my_user=new stdClass();
    $my_user->uid=$user->uid;
    $my_user->name=$user->name;
    $my_user->mail=$user->mail;
    $my_user->pass=$user->pass;
    //
    $user_string=serialize($my_user);
    $result=base64_encode($user_string);
    $result=red_compartir_grupo_encrypt_text($result);
    //print $result;
    return $result;
}
function hontza_is_grupo_subdominio($row){
    if(isset($row->is_grupo_red_alerta) && !empty($row->is_grupo_red_alerta)){
        if(isset($row->is_grupo_subdominio) && !empty($row->is_grupo_subdominio)){
            return 1;
        }
    }
    return 0;
}
function hontza_set_subdominio_id_name($subdominio){
    if(!empty($subdominio)){
        $result=str_replace('.','_',$subdominio);
        $result=str_replace('-','_',$result);
        $result.='_';
        $result=str_replace('__','_',$result);
        return $result;
    }
    return $subdominio;
}
function hontza_destacar_access_denied(){
    if(!is_show_destacar_link()){
        drupal_access_denied();
        exit();
    }    
}
function hontza_get_og_vocab_array($vid){
    $result=array();
    $sql='SELECT * FROM {og_vocab} WHERE vid='.$vid;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_is_busqueda_en_blanco(){
    if(hontza_is_sareko_id_busqueda_en_blanco()){
        $my_request=$_REQUEST;
        //echo print_r($my_request,1);
        if(!empty($my_request)){
            foreach($my_request as $key=>$value){
                if($key=='q'){
                    continue;
                }else{
                    return 0;
                }
            }
        } 
        return 1;
    }
    return 0;
}
function hontza_is_sareko_id_busqueda_en_blanco(){
    if(defined('_IS_BUSQUEDA_EN_BLANCO') && _IS_BUSQUEDA_EN_BLANCO==1){
        return 1;
    }
    return 0;
}
function hontza_in_sareko_id($sareko_id_array){
    if(!empty($sareko_id_array)){
        foreach($sareko_id_array as $i=>$sareko_id){
            if(hontza_is_sareko_id($sareko_id)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_is_busqueda_en_blanco_view($view_name,$query=''){
    //print $view_name;
    if(!empty($query)){
        $konp=hontza_get_busqueda_en_blanco_sql();
        if($query==$konp){
            return 1;
        }
    }
    if(in_array($view_name,array('canal_busqueda','og_canales_busqueda'))){
        if(hontza_is_busqueda_en_blanco()){
            return 1;
        }
    }
    return 0;
}
function hontza_is_mi_comentario($comment_in='',$cid=''){
    global $user;
    if(!empty($comment_in)){
        $comment=$comment_in;
    }else{
        $comment=_comment_load($cid);
    }
    //
    if(isset($user->uid) && !empty($user->uid)){
        if(isset($comment->uid) && !empty($comment->uid)){
            if($user->uid==$comment->uid){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_activar_actualizacion_canal_access($node){
    return hontza_canal_access($node);    
}
function hontza_fix_canal_active_refresh_callback(){
    return 'Funcion desactivada';
    $active_refresh=1;
    $node_array=get_all_nodes(array('canal_de_supercanal','canal_de_yql'));
    if(!empty($node_array)){
        foreach($node_array as $i=>$row){
            $r=hontza_get_content_field_canal_active_refresh_row($row);
            if(isset($r->nid) && !empty($r->nid)){
                $res=db_query('UPDATE {content_field_canal_active_refresh} SET field_canal_active_refresh_value=%d WHERE nid=%d AND vid=%d',$active_refresh,$row->nid,$row->vid);           
            }else{
                $res=db_query('INSERT INTO {content_field_canal_active_refresh}(nid,vid,field_canal_active_refresh_value) VALUES(%d,%d,%d)',$row->nid,$row->vid,$active_refresh);                       
            }
        }
    }
    return date('Y-m-d H:i:s');
}
function hontza_unset_usuarios_caducados($user_array_in,$is_value=0){
    $result=array();
    foreach($user_array_in as $i=>$u){
        if($is_value){
            $uid=$u;
        }else{
            $uid=$u->uid;
        }
        if(!is_user_demo_caducado($uid)){
            $result[]=$u;
        }
    }
    return $result;
}
function hontza_get_empresas_grupo_list(){
    $result=array();
    //
    $where=array();
    $where[]="1";
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
  	$where[]="(og_uid.nid = ".$my_grupo->nid.")";
    }
    $where[]="users.status <> 0";
    //
    $sql="SELECT profile_values_profile_empresa.value AS profile_values_profile_empresa_value
    ,users.uid 
    FROM {users} users LEFT JOIN {og_uid} og_uid ON users.uid = og_uid.uid
    LEFT JOIN {profile_values} profile_values_profile_empresa ON users.uid = profile_values_profile_empresa.uid AND profile_values_profile_empresa.fid = '3'
    WHERE ".implode(" AND ",$where)."
    ORDER BY profile_values_profile_empresa_value ASC";
    //print $sql;
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        if(!is_user_demo_caducado($row->uid)){
            //intelsat-2015
            //$key=$row->profile_values_profile_empresa_value;
            //$empresa_value=$row->profile_values_profile_empresa_value;
            //intelsat-2015
            $empresa_value=red_crear_usuario_get_empresa_value($row);            
            $key=red_crear_usuario_get_profile_empresa_key($empresa_value,$result);
            if(isset($result[$key])){
                //$result[$key]->profile_values_profile_empresa_value=$row->profile_values_profile_empresa_value;
                $result[$key]->profile_values_profile_empresa_value=$empresa_value;
                $result[$key]->num_records=$result[$key]->num_records+1;
            }else{
                $result[$key]=new stdClass();
                //$result[$key]->profile_values_profile_empresa_value=$row->profile_values_profile_empresa_value;
                $result[$key]->profile_values_profile_empresa_value=$empresa_value;
                $result[$key]->num_records=1;
            }
        }
    }
    $result=array_values($result);
    return $result;
}
function hontza_canal_access($node){
    return repase_access_canal($node,1);    
}
function hontza_unset_activate_channel_form_alter(&$form, &$form_state, $form_id){
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){
        if(!hontza_canal_access($node)){    
            unset($form['field_canal_active_refresh']);
        }    
    }
}
function hontza_is_doc_filter_activated(){
            $fields=hontza_define_doc_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if(isset($_SESSION['my_doc_list']['filter'][$f]) && !empty($_SESSION['my_doc_list']['filter'][$f])){
                        return 1;
                    }
                }
            }
            return 0;
}
function hontza_define_doc_filter_fields(){
    $fields=array('fid','username','filename','node_title','comment_title','node_type','filemime');
    return $fields;
}    
function hontza_in_doc_filter($row,$filter){
    $filename=$row->filename;
    $info=pathinfo($filename);
    if(isset($filter['filemime']) && !empty($filter['filemime'])){
        if($filter['filemime']=='excel'){
            if(in_array($info['extension'],array('xlsx','xlsm','xltx','xltm','xlsb','xlam'))){
                return 1;
            }
        }else if($filter['filemime']=='powerpoint'){
            if(in_array($info['extension'],array('pptx','pptm','potx','potm','ppam','ppsx','ppsm','sldx','sldm','thmx'))){
                return 1;
            }
        }else if($filter['filemime']=='word'){
            if(in_array($info['extension'],array('docx','docm','dotx','dotm'))){
                return 1;
            }
        }else if($filter['filemime']=='pdf'){
            if(in_array($info['extension'],array('pdf'))){
                return 1;
            }
        }else if($filter['filemime']=='image'){
            if(in_array($info['extension'],array('gif','jpg','jpeg','png','tiff'))){
                return 1;
            }
        }else if($filter['filemime']=='txt'){
            if(in_array($info['extension'],array('txt'))){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_get_activated_string($node,$is_icono=0){
    $v=hontza_get_content_field_canal_active_refresh_value($node);
    return hontza_get_active_string($v,$is_icono);
}
function hontza_is_mis_contenidos_canales(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='mis-contenidos'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='canales'){
            return 1;
        }    
    }
    return 0;
}
function hontza_mis_contenidos_canales_pre_execute(&$view){        
    if(hontza_is_congelar_canal_sareko_id()){
        global $user;
        $sql='SELECT node.nid AS nid,
        node.title AS node_title,
        node.language AS node_language,
        node.type AS node_type,
        node_data_field_fuente_canal.field_fuente_canal_value AS node_data_field_fuente_canal_field_fuente_canal_value,
        node.vid AS node_vid,
        og_ancestry.nid AS og_ancestry_nid,
        node.created AS node_created,
        content_field_canal_active_refresh.field_canal_active_refresh_value AS node_status,
        node.uid AS node_uid,
        node_revisions.format AS node_revisions_format,
        node.changed AS node_changed
        FROM {node} node
        INNER JOIN {users} users ON node.uid = users.uid 
        LEFT JOIN {content_field_fuente_canal} node_data_field_fuente_canal ON node.vid = node_data_field_fuente_canal.vid 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid
        LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node.vid=content_field_canal_active_refresh.vid 
        WHERE (users.uid = '.$user->uid.') AND (node.type in ("canal_de_supercanal","canal_de_yql","canal_busqueda")) ORDER BY node_changed DESC'; 
        //    
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;
    }
}
function hontza_get_item_fecha_created($node){
    if(hontza_is_sareko_id_red()){
        return red_get_item_fecha_created($node);
    }    
    return date('d/m/Y',$node->created);
}
function hontza_set_block_og_area_trabajo($vars){
    /*if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }*/
    $rows=$vars['rows'];
    $my_array=explode('<div class="views-field-edit-node">',$rows);
    if(!empty($my_array)){
        $result=array();
        foreach($my_array as $i=>$v){
            if($i>0){
                $find='</div>';
                $pos=strpos($v,$find);
                if($pos===FALSE){
                    $result[]=$v;
                    continue;
                }else{
                    $s=substr($v,$pos+strlen($find));
                    $result[]=$s;
                }
            }else{
                $result[]=$v;
            }
        }
        return implode('',$result);
    }
    return $rows;
}
function hontza_is_mostrar_recursos_compartidos_del_servidor_red(){
    //simulando
    //return 1;
    //return 0;
    if(hontza_is_sareko_id('LOKALA')){
        return 1;
    }
    //
    if(hontza_is_sareko_id_red()){
        if(hontza_is_red_hoja()){
            if(red_compartir_grupo_is_grupo_red_alerta()){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_get_gestion_canales_block_content(){
  //intelsat-2015
  if(user_access('access gestion_canales')){
    $html=array();  
    $label=t('List of channels');
    if(module_exists('gestion_canales')){
      $html[]=l($label, 'gestion/gestion_canales');    
    }else{
      $html[]=l($label, 'gestion/canales');
    }
    //gemini-2014
    if(is_super_admin()){
      //intelsat-2015    
      //$html[]=l(t('Active channels'),'activar_actualizacion_subdominio',array('query'=>'destination=gestion'));
      $html[]=l(t('Active channels'),'activar_actualizacion_subdominio',array('query'=>'destination=panel_admin'));
      //  
    }
    //
    return implode('<BR>',$html);
  }
  return '';
}