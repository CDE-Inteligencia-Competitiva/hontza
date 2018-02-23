<?php
function hontza_set_display_none_opciones_item($output_in,&$opciones){
    $output=$output_in;
    $find='<div class="opciones-item">';
    $pos=strpos($output,$find);
    if($pos===FALSE){
        return $output;
    }else{
        $s=substr($output,$pos+strlen($find));
        $sep='<div class="item';
        $my_array=explode($sep,$s);
        $num=count($my_array);
        $last=$my_array[$num-1];
        $pos_end=strpos($last,'</div>');
        $my_array[$num-1]=substr($last,0,$pos_end).'</div>';
        $opciones='<div style="float:left;">'.implode($sep,$my_array);
        $opciones.='</div>';
    }
    $output=str_replace($find,'<div class="opciones-item" style="display:none">',$output);    
    return $output;
}
function hontza_unset_roles_by_not_admin($roles){
    $result=array();
    $my_array=array();
    $my_array[]=AUTHENTICATED;
    $my_array[]=CREADOR;
    $my_array[]=ADMINISTRADOR_DE_GRUPO;
    if(!empty($roles)){
        foreach($roles as $rid=>$label){
            if(in_array($rid,$my_array)){
                $result[$rid]=$label;
            }
        }
    }
    return $result;
}
function hontza_hay_repetidos_callback(){
    return 'Funcion desactivada';
    boletin_report_no_group_selected_denied();
    $my_grupo=og_get_group_context();
    $group_array=array();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $group_array[]=$my_grupo->nid;
        $time_ini='';
        $time_ini=strtotime('2014-03-21 00:00');
        $item_array=hontza_get_all_nodes(array('item'), $group_array,$time_ini);
        if(!empty($item_array)){
            $repetidos=array();
            foreach($item_array as $i=>$row){
                //echo print_r($row,1);
                //exit();
                $current_repetidos=array();
                $my_node=node_load($row->nid);
                if(hontza_hay_titulo_repetido($my_node,$item_array,$current_repetidos)){
                   if(!isset($repetidos[$row->nid])){
                        $repetidos[$row->nid]=$row->title;                        
                   }
                   if(!empty($current_repetidos)){
                        foreach($current_repetidos as $current_nid=>$current_title){
                            if(!isset($repetidos[$current_nid])){
                                $repetidos[$current_nid]=$current_title;
                            }
                        }
                   }
                }
            }
        }
    }
    $output='';
    $repe_kont=0;
    if(!empty($repetidos)){
        asort($repetidos);
        foreach($repetidos as $repe_nid=>$repe_title){
            $node=node_load($repe_nid);
            //echo print_r($node,1);exit();
            $output.=$repe_nid.'='.$repe_title.'<BR>';
            $output.='url='.hontza_item_web_link($node,1).'<BR>';            
            $output.='#####################################################<BR>';
            $repe_kont++;
        }
    }    
    $output.='<BR>'.$repe_kont.'<BR>';
    $output.=date('Y-m-d H:i');
    return $output;
}
function hontza_hay_titulo_repetido($r,$item_array,&$repetidos){
        $result=0;
        $repetidos=array();
        if(!empty($item_array)){
            foreach($item_array as $i=>$row){
                if($r->nid!=$row->nid){
                    $konp_node=node_load($row->nid);
                    //
                    $a=hontza_item_web_link($r,1);
                    $b=hontza_item_web_link($konp_node,1);
                    //
                    //if($r->title==$row->title){
                    if($a==$b){
                        $result=1;
                        $repetidos[$row->nid]=$row->title;
                    }
                }
            }
        }
        if($result){
            return 1;
        }
        return 0;
}
function hontza_get_grupo_by_feed_nid($source){
    if(!empty($source)){    
        $canal=node_load($source->feed_nid);
        if(isset($canal->og_groups)){
            $key_array=array_keys($canal->og_groups);
            if(isset($key_array[0]) && !empty($key_array[0])){
                $grupo=node_load($key_array[0]);
                return $grupo;
            }
        }
    }    
    return '';
}
function hontza_get_icono_grupo_red_alerta($row,$is_mis_grupos=0){
    //intelsat-2015    
    //if(hontza_is_sareko_user()){
    $is_sareko_user=hontza_is_sareko_user();
    $alguna_vez_has_estado_conectato=red_funciones_alguna_vez_has_estado_conectato();
    if($is_sareko_user || $alguna_vez_has_estado_conectato){    
        //intelsat-2015
        $icon_name='';
        return red_compartir_grupo_get_icono_red_alerta($row,$is_mis_grupos,0,$icon_name);
    }
}
function hontza_grupo_shared_active_tabs_access($is_return=0){
    if(hontza_is_servidor_red_alerta()){
        if(module_exists('red_servidor')){
            if(red_servidor_is_grupo_shared()){
                //if(hontza_is_node_edit()){
                //if(!hontza_is_canal_edit()){
                    if($is_return){
                        return 0;
                    }else{
                        drupal_access_denied();
                        exit();
                    }
                //}
                //}    
            }
        }
    }
    if($is_return){
        return 1;
    }
}
function hontza_get_add_wiki_page_block_content(){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    $html=array();
    $html[]=l(t('Create Wiki Document'), 'node/add/wiki');
    return implode('<BR>',$html);
}
function hontza_is_og_area_trabajo_block_display_grupo_shared($current_display){
    return hontza_is_view_block($current_display);
}
function hontza_is_view_block($current_display){
    $pos=strpos($current_display,'block_');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_og_home_noticiasvalidadas_dash_block_pre_execute(&$view){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        hontza_empty_view_pre_execute($view);
    }else{
        red_node_og_home_noticiasvalidadas_dash_block_pre_execute($view);
    }
}
function hontza_empty_view_pre_execute(&$view){
    $sql=hontza_get_busqueda_en_blanco_sql();
    $view->build_info['query']=$sql; 	
    $view->build_info['count_query']=$sql;
}
function hontza_og_canales_busqueda_block_pre_execute(&$view){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        hontza_empty_view_pre_execute($view);
    }
}
function hontza_og_home_areadebate_block_pre_execute(&$view){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        hontza_empty_view_pre_execute($view);
    }
}
function hontza_og_home_areadetrabajo_block_pre_execute(&$view){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        hontza_empty_view_pre_execute($view);
    }
}
function hontza_is_node_edit($type='edit'){
    $param0=arg(0);
    if($param0=='node'){
        $param1=arg(1);
        if(is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2==$type){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_is_canal_edit(){
    if(hontza_is_node_edit()){
        $nid=arg(1);
        $node=node_load($nid);
        if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
            return 1;
        }    
    }
    return 0;
}
function hontza_get_busqueda_en_blanco_sql(){
    $sql='SELECT * FROM {busqueda_en_blanco} WHERE id=1234';
    return $sql;
}
function hontza_is_nolink($url){
    if(!empty($url)){
        $pos=strpos($url,'nolink://');
        if($pos===FALSE){
            return 0;
        }else{
            return 1;
        }
    }
    return 0;
}
function hontza_is_mail_hound_channel($job){
    if($job['callback']=='feeds_source_import'){
        $feed_nid=$job['id'];
        //simulando
        //$feed_nid=181768;
        $canal=node_load($feed_nid);
        if(isset($canal->nid) && !empty($canal->nid)){
            if(hontza_is_hound_canal($feed_nid, $canal)){
                if(module_exists('hound')){
                    $canal_hound_parametros=hound_get_canal_hound_parametros_row($canal->nid);
                    if(isset($canal_hound_parametros->hound_id) && !empty($canal_hound_parametros->hound_id)){
                        //intelsat-2016
                        if(defined('_EMAIL_CANAL')){    
                            if($canal_hound_parametros->hound_id==_EMAIL_CANAL){
                                return 1;
                            }
                        }    
                    }    
                }
            }
        }    
        return 0;
    }
    return 0;
}
function hontza_is_node_view_type($node_type_array,&$title){
    $title='';
    if(hontza_is_node_view()){
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
            //if($node->type==$node_type){
            if(in_array($node->type,$node_type_array)){    
                $title=estrategia_set_title_max_len($node->title);
                return 1;
            }
        }
    }
    return 0;
}
function hontza_array_ordenatu_categorias_lo_mas_valorado($result_in){
    	$result=$result_in;
        if(count($result)<=0){
            return $result;
        }
	// Obtain a list of columns
	$average=array();
        $created=array();
        $title=array();
	foreach ($result as $key => $row) {
            $average[$key]  = strtoupper($row->votingapi_cache_node_average_value);
            $created[$key]  = strtotime($row->node_created_date);
            $title[$key]  = strtoupper($row->node_title); 
	}
	array_multisort($average, SORT_DESC,SORT_NUMERIC,$created,SORT_DESC,SORT_NUMERIC,$title,SORT_ASC,SORT_STRING,$result);
		
	return $result;
}
function hontza_define_page_actions(){
    /*if(!is_super_admin()){
        return '';
    }
    if(hontza_is_vigilancia_pantalla('pendientes')){
        hontza_add_rechazar_pagina_js();
        $html=array();
        $html[]=l(t('Validate page'),'vigilancia/validar_pagina',array('attributes'=>array('id'=>'id_a_validar_pagina')));
        $html[]=l(t('Reject page'),'vigilancia/rechazar_pagina',array('attributes'=>array('id'=>'id_a_rechazar_pagina')));    
        return implode('&nbsp;|&nbsp;',$html);
    }
    return '';*/
    return '';
}
function hontza_add_rechazar_pagina_js(){
   global $base_url;
   $purl='';
   $my_grupo=og_get_group_context(); 
   if(isset($my_grupo->purl) && !empty($my_grupo->purl)){
       $purl=$my_grupo->purl;
   }
   $destination=$_SERVER['REQUEST_URI'];
		$js='
                    var validar_pagina_redirect_url="'.$destination.'";
			$(document).ready(function()
			{
			  $("#id_a_validar_pagina").click(function(){
                            validar_pagina_js();
                            //validar_pagina_redirect_js();
                            return false;
                          });                          
                          $("#id_a_rechazar_pagina").click(function(){
                            rechazar_pagina_js();
                            //rechazar_pagina_redirect_js();
                            return false;
                          });
                          function validar_pagina_js(){
                            $(".node").each(function(){
                                var nid=$(this).attr("id");
                                nid=nid.replace("node-","");
                                $("div.f-interesante a").click();
                            });
                          };
                          function rechazar_pagina_js(){
                            $(".node").each(function(){
                                var nid=$(this).attr("id");
                                nid=nid.replace("node-","");
                                $("div.f-no-interesante a").click();
                            });
                          };
                          function validar_pagina_redirect_js(){
                            var nid_array=new Array();
                            $(".node").each(function(){
                                var nid=$(this).attr("id");
                                nid=nid.replace("node-","");
                                nid_array.push(nid);
                            });
                            jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/'.$purl.'/vigilancia/validar_pagina_ajax?my_time="+new Date().getTime(),
				data: {nid_array_string:nid_array.join(",")},
				dataType:"json",
				success: function(my_result){
                                    window.location.href=validar_pagina_redirect_url;
				}
                            });
                          }
                          function rechazar_pagina_redirect_js(){
                            var nid_array=new Array();
                            $(".node").each(function(){
                                var nid=$(this).attr("id");
                                nid=nid.replace("node-","");
                                nid_array.push(nid);
                            });
                            jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/'.$purl.'/vigilancia/rechazar_pagina_ajax?my_time="+new Date().getTime(),
				data: {nid_array_string:nid_array.join(",")},
				dataType:"json",
				success: function(my_result){
                                    window.location.href=validar_pagina_redirect_url;
				}
                            });
                          }
			});';

			drupal_add_js($js,'inline');

}
function hontza_validar_pagina_ajax_callback(){
    $nid_array=explode(',',$_POST['nid_array_string']);
    if(!empty($nid_array)){    
        foreach($nid_array as $i=>$nid){
            $row=new stdClass();
            $row->nid=$nid;
            hontza_delete_flag_content($row);
            $flag_result = flag('flag','leido_interesante',$row->nid);
        }
    }
    $result=array();
    $result['ok']=1;
    print json_encode($result);
    exit();
}
function hontza_rechazar_pagina_ajax_callback(){
    $nid_array=explode(',',$_POST['nid_array_string']);
    if(!empty($nid_array)){    
        foreach($nid_array as $i=>$nid){
            $row=new stdClass();
            $row->nid=$nid;
            hontza_delete_flag_content($row);
            $flag_result = flag('flag','leido_no_interesante',$row->nid);
        }
    }
    $result=array();
    $result['ok']=1;
    print json_encode($result);
    exit();
}
function hontza_is_vigilancia_pantalla($type){
    if(hontza_is_vigilancia($type)){
        return 1;
    }
    if(hontza_is_canales($type)){
        return 1;
    }
    if(hontza_is_canales_categorias($type)){
        return 1;
    }
    return 0;
}
function hontza_is_vigilancia($type){
    $param0=arg(0);
    if(!empty($param0) && $param0=='vigilancia'){
        $param1=arg(1);
        if(!empty($param1) && $param1==$type){
            return 1;
        }
    }
    return 0;
}
function hontza_is_canales_categorias($type){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canales'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='my_categorias'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                $param3=arg(3);
                if(!empty($param3) && $param3==$type){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function hontza_link_validar_pagina($is_a_la_par_del_menu=0){
    global $base_url;
    /*if(!is_super_admin()){
        return '';
    }*/
    if(hontza_is_vigilancia_pantalla('pendientes')){
        $html=array();
        $label=t("Validate this page's news");
        if($is_a_la_par_del_menu){
            $html[]= '<span class="link_validar_pagina" style="padding:0;margin:0;">';
        }else{
            $html[]= '<span class="link_validar_pagina">';
        }  
        $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/validar_pagina.png';
        $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';        
        $html[]=l($img,'vigilancia/validar_pagina',array('html'=>TRUE,'attributes'=>array('id'=>'id_a_validar_pagina','class'=>'a_validar_pagina')));
        $html[]= '</span>';
        
        return implode('',$html);
    }
    return '';
}
function hontza_link_rechazar_pagina($is_a_la_par_del_menu=0){
    global $base_url;
    if(hontza_is_vigilancia_pantalla('pendientes')){
        $html=array();
        $label=t("Reject this page's news");
        if($is_a_la_par_del_menu){
            $html[]= '<span class="link_rechazar_pagina" style="padding:0;margin:0;">';
        }else{
            $html[]= '<span class="link_rechazar_pagina">';
        }    
        $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/rechazar_pagina.png';
        $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';        
        $html[]=l($img,'vigilancia/rechazar_pagina',array('html'=>TRUE,'attributes'=>array('id'=>'id_a_rechazar_pagina','class'=>'a_validar_pagina')));
        
        $html[]= '</span>';
        return implode('',$html);
    }
    return '';
}
function hontza_is_validar_page_header($view_name=''){
    if(empty($view_name) || in_array($view_name,array('og_vigilancia_pendientes'))){
        if(hontza_is_vigilancia('pendientes')){
            return 1;
        }
    }
    return 0;
}
function validar_page_header(){
    return link_validar_canal('',1,1);
}
function hontza_get_pagina_de_arranque(){
    global $user,$base_url;
    //intelsat-2016
    if(hontza_registrar_is_pagina_arranque_registrar()){
        return hontza_registrar_get_pagina_arranque_registrar();
    }
    //intelsat-2015
    if(red_movil_is_activado()){
        return movil_get_pagina_de_arranque();
    }
    if(hontza_is_servidor_red_alerta()){
        return $base_url;
    }
    //intelsat-2015
    $my_lang='';
    $url='user/'. $user->uid;
    if(red_crear_usuario_is_custom_css()){
        $my_lang='/es';
    }
    $url=$base_url.$my_lang.'/'.$url;            
    $url_users_start_page=hontza_get_url_users_start_page($my_lang);        
    if(!empty($url_users_start_page)){
        return $url_users_start_page;
    }
    if(isset($user->og_groups) && !empty($user->og_groups)){
        $group_nid_array=array_keys($user->og_groups);
        if(isset($group_nid_array[0]) && !empty($group_nid_array[0])){
            $group_nid=$group_nid_array[0];
            $grupo=node_load($group_nid);
            if(isset($grupo->purl) && !empty($grupo->purl)){
                $pestana='/vigilancia/pendientes';
                if(!hontza_is_show_vigilancia_pendientes_tab()){
                    $pestana='/vigilancia/validados';
                }
                if(red_crear_usuario_is_rol_invitado()){
                    $pestana='/ideas';   
                }
                $url=$base_url.$my_lang.'/'.$grupo->purl.$pestana;                
            }
        }
    }
    return $url;
}
function hontza_set_session_is_iframe(){
    if(!hontza_is_iframe()){
        $_SESSION['is_iframe']=0;
    }
    if(isset($_REQUEST['is_iframe']) && !empty($_REQUEST['is_iframe'])){
        $_SESSION['is_iframe']=$_REQUEST['is_iframe'];
    }
}
function hontza_is_iframe(){
    global $user;
    if(isset($_REQUEST['is_iframe']) && !empty($_REQUEST['is_iframe'])){
        return 1;
    }
    if(isset($_SESSION['is_iframe']) && !empty($_SESSION['is_iframe'])){
        return 1;
    }
    if(isset($user->is_iframe) && !empty($user->is_iframe)){
        return 1;
    }
    return 0;
}
//intelsat-2015
//function hontza_get_url_users_start_page(){
function hontza_get_url_users_start_page($my_lang=''){    
    global $base_url;
    require_once('sites/all/modules/user/user.pages.inc');
    //intelsat-2015
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        $url=$base_url.'/oferta_demanda';
        return $url;
    }
    //
    $row=user_pagina_de_arranque_get_row();
    if(isset($row->id) && !empty($row->id)){
        if(empty($row->group_nid) || empty($row->start_page)){
            return '';
        }
        $grupo=node_load($row->group_nid);
        if(isset($grupo->purl) && !empty($grupo->purl)){            
            if(!hontza_grupos_is_url_menutop_active($row->start_page,$row->group_nid)){
                $row->start_page='vigilancia/pendientes';
            }            
            if($row->start_page=='vigilancia/pendientes'){
                $pestana='/vigilancia/pendientes';
                if(!hontza_is_show_vigilancia_pendientes_tab()){
                    $pestana='/vigilancia/validados';
                }
            }else{
                $pestana='/'.$row->start_page;
            }
            //intelsat-2015
            //$url=$base_url.'/'.$grupo->purl.$pestana;
            $url=$base_url.$my_lang.'/'.$grupo->purl.$pestana;
            return $url;            
        }
    }
    return '';
}
function hontza_og_get_node_groups($node) {
    $result=array();
    if(isset($node->grupo_nid) && !empty($node->grupo_nid)){
        $row=hontza_get_node($node->grupo_nid);
        if(isset($row->nid) && !empty($row->nid)){
            $result[$row->nid]=$row->title;
        }
    }
    return $result;
}
//intelsat-2016
//function hontza_get_node($nid){
function hontza_get_node($nid,$vid=''){
    if(!empty($vid)){
        $res=db_query('SELECT n.* FROM {node} n WHERE nid=%d AND vid=%d',$nid,$vid);
    }else{
        $res=db_query('SELECT n.* FROM {node} n WHERE nid=%d',$nid);
    }
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function hontza_repase_url_group($path,$original){
    if(hontza_is_node_url($path,$nid)){
        $node=my_get_node();
        if(isset($node->nid)){
            if($node->nid==$nid){
                if(isset($node->grupo_nid)){
                    $grupo=node_load($node->grupo_nid);
                    if(isset($grupo->purl) && !empty($grupo->purl)){
                        return $grupo->purl.'/'.$path;
                    }
                }
            }
        }    
    }
    return $path;
}
function hontza_is_node_url($url,&$nid){
    $nid='';
    $my_array=explode('/',$url);
    if(isset($my_array[0]) && !empty($my_array[0]) && $my_array[0]=='node'){
        if(isset($my_array[1]) && !empty($my_array[1]) && is_numeric($my_array[1])){
            $nid=$my_array[1];
            if(isset($my_array[2]) && !empty($my_array[2])){
                if($my_array[2]=='edit'){
                    return 1;
                }
            }else{
                return 1;
            }
        }
    }/*else if(isset($my_array[0]) && !empty($my_array[0]) && $my_array[0]=='content'){
        if(isset($my_array[1]) && !empty($my_array[1])){
            $nid=$my_array[1];
            if(isset($my_array[2]) && !empty($my_array[2])){
                return 0;
            }else{
                return 1;
            }
        }    
    }*/
    return 0;
}
function hontza_redirect_by_grupo(){
    global $base_url;
    global $base_path;
    //print $base_path;exit();
    $node=my_get_node();
    if(!(isset($node->og_groups) && !empty($node->og_groups))){
        if(isset($node->grupo_nid) && !empty($node->grupo_nid)){
            //$query_string=str_replace('q=','',$_SERVER['QUERY_STRING']);
            $s=trim($base_path,'/');
            $find='';
            $query_string=$_SERVER['REDIRECT_URL'];
            if(!empty($s)){
                $find=$base_path;
                $query_string=str_replace($find,'',$query_string);            
            }
            //print $query_string;exit();
            //
            if(hontza_is_node_url($query_string,$my_nid)){
                //print $my_nid;exit();
                $url=hontza_repase_url_group($query_string,'');
                drupal_goto($base_url.'/'.$url);
            }    
        }
    }
}
function hontza_repasar_canal_sin_valor($sql_in){
    $sql="SELECT node_data_field_item_canal_reference.field_item_canal_reference_nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid, node_node_data_field_item_canal_reference.title AS node_node_data_field_item_canal_reference_title, COUNT(node.nid) AS num_records FROM {node} node LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node.vid = node_data_field_item_canal_reference.vid LEFT JOIN {node} node_node_data_field_item_canal_reference ON node_data_field_item_canal_reference.field_item_canal_reference_nid = node_node_data_field_item_canal_reference.nid WHERE node.type in ('item') GROUP BY node_node_data_field_item_canal_reference_title, node_data_field_item_canal_reference_field_item_canal_reference_nid ORDER BY node_node_data_field_item_canal_reference_title ASC"; 
    $res=db_query($sql);
    //print $sql;exit();
    while($row=db_fetch_object($res)){
        echo print_r($row,1);exit();
    }
}
function hontza_activar_actualizacion_canal_callback(){
    $title=t('Channel');
    $canal_nid=arg(1);
    /*
    $active_refresh=0;
    $canal=node_load($canal_nid);
    if(isset($canal->nid) && !empty($canal->nid)){
        $title=$canal->title;
        $v=hontza_get_content_field_canal_active_refresh_value($canal);
        if(!empty($v)){
            $active_refresh=$v;
        }
        //
        $new_value=1;
        if($active_refresh){
            $new_value=0;
        }
        db_query('UPDATE {content_field_canal_active_refresh} SET field_canal_active_refresh_value=%d WHERE nid=%d AND vid=%d',$new_value,$canal->nid,$canal->vid);
    }*/
    $new_value=hontza_activar_actualizacion_canal($canal_nid);
    //intelsat-2015
    $canal=node_load($canal_nid);
    hontza_solr_search_clear_cache_content($canal);
    if(isset($_REQUEST['is_gestion_canales']) && !empty($_REQUEST['is_gestion_canales'])){
        drupal_goto($_REQUEST['destination']);
    }
    //
    drupal_set_title($title);
    $output='';    
    if($new_value){
        $output.='<p>'.t('Channel activated').'</p>';
    }else{
        $output.='<p>'.t('Channel deactivated').'</p>';
    }
    $url_return='node/'.$canal_nid;
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_return=$_REQUEST['destination'];
    }
    $output.=l(t('Return'),$url_return);
    return $output;
}
function hontza_get_canal_yql_is_simple_by_params($canal_yql_parametros_row){
    $my_array=array('todos', 
    'titulo', 
    'descripcion', 
    'no_titulo',
    'no_descripcion', 
    'contiene',
    'no_contiene', 
    'filtrosSI',
    'filtrosNO',
    'campo_contiene',
    'campo_no_contiene',
    'conjuncion',
    'area');    
    if(!empty($canal_yql_parametros_row)){
        if(!empty($my_array)){
            foreach($my_array as $i=>$f){
                if($f=='campo_no_contiene'){
                    continue;
                }else{
                   if(isset($canal_yql_parametros_row->$f) && !empty($canal_yql_parametros_row->$f)){
                       return 0;
                   } 
                }
            }
        }
        return 1;
    }
    return 1;
}
function hontza_add_activar_actualizacion_canal_js($nid){
    global $base_url;
    $js='$(document).ready(function(){
            var activar_canal_nid='.$nid.';
			  $("#id_a_activar_canal_'.$nid.'").click(function(){
                            hontza_activar_actualizacion_canal_ajax(activar_canal_nid);
                            return false;
                          });
                          function hontza_activar_actualizacion_canal_ajax(activar_canal_nid){
                            jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/vigilancia/activar_actualizacion_canal_ajax?my_time="+new Date().getTime(),
				data: {activar_canal_nid:activar_canal_nid},
				dataType:"json",
				success: function(my_result){
                                    $("#id_a_activar_canal_'.$nid.'").text(my_result.label);
                                    $("#id_div_activar_canal_'.$nid.'").text(my_result.yes_label);    
				}
                            });
                          }
    });';                          
    drupal_add_js($js,'inline');
}
function hontza_activar_actualizacion_canal($canal_nid,$value_in='none'){
    $active_refresh=0;
    $canal=node_load($canal_nid);
    if(isset($canal->nid) && !empty($canal->nid)){
        $title=$canal->title;
        $v=hontza_get_content_field_canal_active_refresh_value($canal);
        if(!empty($v)){
            $active_refresh=$v;
        }
        //
        if($value_in=='none'){
            $new_value=1;
            if($active_refresh){
                $new_value=0;
            }
        }else{
            $new_value=$value_in;
        }    
        db_query('UPDATE {content_field_canal_active_refresh} SET field_canal_active_refresh_value=%d WHERE nid=%d AND vid=%d',$new_value,$canal->nid,$canal->vid);
    }
    return $new_value;
}
function hontza_activar_actualizacion_canal_ajax_callback(){
    $result=array();
    $new_value=0;
    if(isset($_POST['activar_canal_nid']) && !empty($_POST['activar_canal_nid'])){
        $new_value=hontza_activar_actualizacion_canal($_POST['activar_canal_nid']);
    }
    $result['label']=t('Activated channel');
    $result['yes_label']=t('No');
    if($new_value){
        $result['label']=t('Deactivate Channel');
        $result['yes_label']=t('Yes');
    }
    print json_encode($result);
    exit();
}
function hontza_create_mis_contenidos_canales_order_array($my_list){
    $result=array();
    $order_info=my_get_order_info_canales_por_categorias();
    if(!empty($order_info['order']) && strcmp($order_info['order'],'title')==0){
        $info['field']='node_title';
        $info['my_list']=$my_list;
        return $info;
    }else if(!empty($order_info['order']) && strcmp($order_info['order'],'type')==0){
        $vars=array();
        $vars['view']=new stdClass();
        $vars['view']->result=$my_list;
        if(!empty($my_list)){
            foreach($my_list as $i=>$row){
                $result[$i]=$row;
                $result[$i]->type_order=get_tipo_canal_by_castellano($row->node_type,$vars,$i+1);                
            }
        }        
        //        
        $info['field']='type_order';
        $info['my_list']=$result;
        return $info;
    }else if(!empty($order_info['order']) && strcmp($order_info['order'],'field_fuente_canal_value')==0){
        $result=my_set_mis_contenidos_canales($my_list);
        $info['field']='node_data_field_fuente_canal_field_fuente_canal_value';
        $info['my_list']=$result;
        return $info;
    }        
}
function hontza_is_news_google($url,$type,&$url_google){
    $url_google='';
    //intelsat-2015
    //if($type=='url'){
    if(in_array($type,array('url','link'))){
        $pos=strpos($url,'news.google.com');
        if($pos===FALSE){
            return 0;
        }else{
            $find='&url=';
            $pos_url=strpos($url,$find);
            if($pos_url===FALSE){
                return 0;
            }else{
                $url_google=substr($url,$pos_url+strlen($find));
                return 1;
            }
        }        
    }
    return 0;
}
//gemini-2014
//intelsat-2016
//function hontza_validar_con_accion($nid){
//intelsat
//function hontza_validar_con_accion($nid,$is_hound_noticia_feed_noticia_email=0){                            
function hontza_validar_con_accion($nid,$is_hound_noticia_feed_noticia_email=0,$is_value=0,$value=0){    
    $leido=get_leido_interesante($nid);
    $row_temp=new stdClass();
    $row_temp->nid=$nid;
    if(isset($leido->fcid) && !empty($leido->fcid)){
        //intelsat
        //hontza_delete_flag_content($row_temp);        
        if($is_value){
            if(!empty($value)){
                hontza_delete_flag_content($row_temp);
            }
        }else{    
            hontza_delete_flag_content($row_temp);
        }
        //intelsat-2016
        //$flag_result = flag('flag','leido_interesante',$row_temp->nid);
        if(red_node_is_flag($is_value,$value)){
            $flag_result = flag('flag','leido_interesante',$row_temp->nid,NULL,$is_hound_noticia_feed_noticia_email);
        }
    }else{
        //intelsat-2016
        //$flag_result = flag('flag','leido_interesante',$row_temp->nid);
        //intelsat
        if(red_node_is_flag($is_value,$value)){
            $flag_result = flag('flag','leido_interesante',$row_temp->nid,NULL,$is_hound_noticia_feed_noticia_email);
        }    
    }
}
//gemini-2014
function hontza_on_insert_noticia_de_usuario(&$node){
    if($node->type=='noticia'){
        //intelsat-2016
        //hontza_validar_con_accion($node->nid);
        $is_hound_noticia_feed_noticia_email=hound_noticia_email_is_hound_noticia_feed_noticia_email();
        hontza_validar_con_accion($node->nid,$is_hound_noticia_feed_noticia_email);        
        if(hontza_canal_rss_is_publico_activado()){
            publico_vigilancia_on_insert_noticia_de_usuario_send_message_admin($node);
        }
    }
}
//gemini-2014
function hontza_set_og_canales_noticias_de_usuario($vars){
    global $user;
    /*if(hontza_is_mis_canales_block()){
        $label=hontza_get_canal_usuarios_title($user->uid);
        $html[]=l($label, 'canal-usuarios/'.$user->uid);
    }
    */
     /*else{
        $html[]=l(t('View all'), 'canal-usuarios');
    }*/
    if(!red_funciones_is_show_shared_block_menu_left()){
        return '';
    }
    //
    $selected_canal_nid=red_funciones_get_filtro_por_canal();
    /*if(!empty($selected_canal_nid)){
        $html[]='<div class="views-summary views-summary-unformatted create-separacion" style="color:black;"><div '.red_funciones_get_filter_activated_style().'>'.t('Filter Activated').'</div>&nbsp;| '.l(t('Clean'),'vigilancia/validados',array('attributes'=>array('style'=>'background:none;padding-left:0px;color: #0A5C75;'))).'</div>';    
    }*/
    //intelsat-2015
    $html=array();
    $result='';
    if(!hontza_solr_search_is_usuario_lector()){
    //    
        //intelsat-2015
        $html[]=  red_funciones_get_create_link(t('Create User News'),'node/add/noticia',array('attributes'=>array('class'=>'a_create_user_news')));
        //intelsat-2016
        red_copiar_add_importar_noticia_link($html);
        $result=implode('',$html);    
        $result='<div class="views-summary views-summary-unformatted create-separacion">'.$result.'</div>';            
    }
    $my_rows=red_funciones_set_active_canal_link($vars['rows'],$selected_canal_nid);
    $result=$result.$my_rows;
    return $result;
}
//gemini-2014
function hontza_og_canal_aportaciones_usuarios($view){
    $uid=arg(1);
    $sql=hontza_get_canal_usuarios_sql($uid); 
    //print $sql;    
    $view->build_info['query']=$sql; 	
    $view->build_info['count_query']=$sql;
}
function hontza_get_canal_usuarios_sql($uid_in,$with_filtro=1,$grupo_nid_in=''){
    //intelsat-2015
    $uid=$uid_in;
    if(hontza_canal_rss_is_canales_rss()){
        $arg_type=arg(2);
        if(is_numeric($arg_type)){
            $uid=arg(2);
            //$arg_type=arg(3);
            $add_uid='/'.$uid;
        }
        $arg_type='ultimas';            
    }else{
    //    
        $arg_type=arg(1);
        if(is_numeric($arg_type)){
            $uid=arg(1);
            $arg_type=arg(2);
            $add_uid='/'.$uid;
        }
    }    
    if(empty($arg_type)){
        $arg_type='pendientes';
    }
    //
    $where=array();
    $where[]='(node.status <> 0)';
    $where[]='(node.type in ("noticia"))';
    $grupo_nid=0;
    $my_grupo=og_get_group_context();
    $group_array=array();
    $grupo_nid='';
    if(!empty($grupo_nid_in)){
        $grupo_nid=$grupo_nid_in;
    }else if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_nid=$my_grupo->nid;
    }else{
        $grupo_nid=$_REQUEST['my_grupo_nid'];
    }            
    if(!empty($grupo_nid)){    
        $where[]='(og_ancestry.group_nid = '.$grupo_nid.')';    
    }    
    if(!empty($uid)){
        if(is_numeric($uid)){
            $where[]='node.uid='.$uid;
        }    
    }
    if($with_filtro){
        $where[]=hontza_get_vigilancia_where_filter();
    }
    //
    $order_by=' ORDER BY node.created DESC';
    if(in_array($arg_type,array('pendientes','validados','rechazados'))){
        if($arg_type=='pendientes'){
            $where[]='flag_content_node.uid IS NULL';
        }else if($arg_type=='validados'){
            $where[]='flag_content_node.fid = 2'; 
        }else if($arg_type=='rechazados'){
            $where[]='flag_content_node.fid = 3'; 
        }    
        //
        $sql='SELECT node.nid AS nid, node.created AS node_created 
        FROM {node} node 
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
        LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id 
        WHERE '.implode(' AND ',$where).'
        GROUP BY nid '.$order_by;
    }else if($arg_type=='lo-mas-valorado'){
        $order_by=' ORDER BY votingapi_cache_node_average_value DESC,node_created_date DESC,node.title ASC';
        $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
        $where[]='votingapi_cache_node_average.value>0';
        $sql='SELECT DISTINCT(node.nid) AS nid,
        votingapi_cache_node_average.value AS votingapi_cache_node_average_value,
        node.created AS node_created_date
        FROM {node} node
        LEFT JOIN {votingapi_cache} votingapi_cache_node_average ON node.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = "node" AND votingapi_cache_node_average.function = "average")     
        LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
        LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id 
        WHERE '.implode(' AND ',$where).'
        GROUP BY nid '.$order_by;
    }else if($arg_type=='lo-mas-comentado'){
        $order_by=' ORDER BY node_comment_statistics_comment_count DESC,node.created DESC'; 
        $where[]='(flag_content_node.fid IS NULL OR flag_content_node.fid != 3)';
        $sql='SELECT DISTINCT(node.nid) AS nid, node_comment_statistics.comment_count AS node_comment_statistics_comment_count
        FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid
        LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id
        WHERE '.implode(' AND ',$where).'
        GROUP BY nid'.$order_by;
    }else if($arg_type=='bookmarks' && hontza_solr_funciones_is_bookmark_activado()){
        //intelsat-2015
        $sql=hontza_solr_search_get_canal_usuarios_bookmarks_sql($uid,$where,$order_by);
    }else{
        $sql='SELECT node.nid AS nid, node.created AS node_created 
        FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
        WHERE '.implode(' AND ',$where).'
        GROUP BY nid '.$order_by;
    }    
    return $sql;
}
function hontza_set_page_og_canal_aportaciones_usuarios($vars){
    $result='';
    /*$uid=arg(1);
    if(!empty($uid) && is_numeric($uid)){
        $html[]=l(t('Edit Channel Title'), 'canal-usuarios-title/'.$uid.'/edit');
    }else{
        $uid='all';
    }
    if(hontza_is_sareko_id_red()){
        $html[]=l(t('Export RSS channel'), 'red_exportar_rss/usuario/'.$uid);
    }
    if(!empty($html)){
        $result=implode('&nbsp;|&nbsp;',$html);        
    }*/
    $result.=hontza_define_canal_usuarios_menu();
    $result.=$vars['rows'];
    return $result;
}
function hontza_canal_usuarios_title_form(){
    $form=array();
    $uid=arg(1);
    $canal_usuarios_title=hontza_get_canal_usuarios_title($uid);    
    //
    $form['canal_usuarios_title_uid']=array(
        '#type'=>'hidden',
        '#default_value'=>$uid,
    );
    $form['canal_usuarios_title']=array(
        '#title'=>t('Title'),
        '#type'=>'textfield',
        '#default_value'=>$canal_usuarios_title,
    );
    $form['guardar_btn']=array(
        '#name'=>'guardar_btn',
        '#type'=>'submit',
        '#default_value'=>t('Save'),
    );
    $form['volver_btn']=array(
        '#value'=>l(t('Return'),'canal-usuarios/'.$uid),
    );
    //
    return $form;
}
function hontza_get_canal_usuarios_row($uid){
    $res=db_query('SELECT * FROM {canal_usuarios} WHERE uid=%d',$uid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function hontza_canal_usuarios_title_access(){
    global $user;
    $uid=arg(1);
    if(is_administrador_grupo()){
        return TRUE;
    }
    if(!empty($uid)){
        if($uid==1){
            return TRUE;
        }
        if($uid==$user->uid){
            return TRUE;
        }
    }
    return FALSE;
}
function hontza_canal_usuarios_title_form_submit($form, &$form_state) {
    $uid=0;
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='guardar_btn'){
            $uid=$form_state['values']['canal_usuarios_title_uid'];
            $title=$form_state['values']['canal_usuarios_title'];
            $canal_usuarios_row=hontza_get_canal_usuarios_row($uid);
            if(isset($canal_usuarios_row->id) && !empty($canal_usuarios_row->id)){
                db_query('UPDATE {canal_usuarios} SET title="%s" WHERE id=%d',$title,$canal_usuarios_row->id);
            }else{
                db_query('INSERT INTO {canal_usuarios}(uid,title) VALUES(%d,"%s")',$uid,$title);
            }
        }                
    }
   //
   //intelsat-2016 
   //drupal_goto('canal-usuarios/'.$uid);
   drupal_goto('canal-usuarios/'.$uid.'/validados');
}
function hontza_is_canal_usuarios($arg_type=''){
   $param0=arg(0);
   if(!empty($param0) && $param0=='canal-usuarios'){
       $param1=arg(1);
       if(!empty($param1) && is_numeric($param1)){
           if(empty($arg_type)){
                return 1;
           }else{
               $param2=arg(2);
               if(!empty($param2) && $param2==$arg_type){
                   return 1;
               }
           } 
       }
   }
   return 0;
}
//intelsat-2015
//function hontza_get_canal_usuarios_title($uid_in=''){
function hontza_get_canal_usuarios_title($uid_in='',$is_user_label=1,$is_red_exportar_rss_usuario=0){
    //intelsat-2015
    $result=t('View all');    
    if(!empty($uid_in)){
        $uid=$uid_in;
        if($uid=='all'){
            //intelsat-2015
            //$result=t('User').': '.t('All');
            $result='';
            //if($is_user_label){
            //intelsat-2016
            if(!$is_red_exportar_rss_usuario){    
                $result=t('User').': ';
            }
            $result.=t('All');
            return $result;
        }
    }else{
        $uid=arg(1);
    }
    $my_user=user_load($uid);
    /*if(user_access('root')){
        echo print_r($my_user,1);
    }*/
    $canal_usuarios_row=hontza_get_canal_usuarios_row($uid);
    if(isset($canal_usuarios_row->id) && !empty($canal_usuarios_row->id)){
        $result=$canal_usuarios_row->title;
        if(!empty($result)){
            return $result;
        }    
    }
    //if(empty($result)){
        if(isset($my_user->name)){
            //$result=t('Channel').':'.$my_user->name;
            //intelsat-2015
            //$result=t('User').': '.$my_user->name;            
            $result='';
            //if($is_user_label){
            //intelsat-2016
            if(!$is_red_exportar_rss_usuario){    
                $result=t('User').': ';
            }
            $result.=$my_user->name;
        }
    //}
    //intelsat-2015    
    //$result=my_get_icono_action('user',$result);      
    return $result;
}
function hontza_is_canales_usuarios_all($view_name){
    if($view_name=='og_canales'){
        if(!hontza_is_mis_canales_block()){
            return 1;
        }
    }
    return 0;
}
function hontza_set_og_canales_view_all_canales_usuarios($result_in){
    global $base_url;
    if(!empty($result_in)){
        $result=$result_in;
    }else{
        $result=array();
    }
    $usuario_array=hontza_get_usuarios_grupo();
    if(!empty($usuario_array)){
        foreach($usuario_array as $i=>$u){
            //intelsat-2015
            //$title=hontza_get_canal_usuarios_title($u->uid);
            $title=hontza_get_canal_usuarios_title($u->uid,0);
            $row=new stdClass();
            $row->node_data_field_item_canal_reference_field_item_canal_reference_nid='canal-usuarios/'.$u->uid;
            $row->node_node_data_field_item_canal_reference_title=$title;
            $row->num_records=hontza_get_canal_personal_num_records($u->uid,1);
            $result[]=$row;
        }        
    }
    $row=new stdClass();
    $row->node_data_field_item_canal_reference_field_item_canal_reference_nid='canal-usuarios/all';
    $row->node_node_data_field_item_canal_reference_title=t('User').': '.t('All');
    $row->num_records=hontza_get_canal_personal_num_records('',1);
    $result[]=$row;
    return $result;
}
function hontza_array_slice($result_in,$offset, $items_per_page){
    $result=array_slice($result_in,$offset,$items_per_page);
    return $result;
}
function hontza_is_canales_usuarios_mis_canales($view_name){
    if($view_name=='og_canales'){
        if(hontza_is_mis_canales_block()){
            return 1;
        }
    }
    return 0;
}
function hontza_set_og_canales_view_canal_personal($result_in){
    global $user;
    $result=array();
    $title=hontza_get_canal_usuarios_title($user->uid);
    $row=new stdClass();
    $row->node_data_field_item_canal_reference_field_item_canal_reference_nid='canal-usuarios/'.$user->uid;
    $row->node_node_data_field_item_canal_reference_title=$title;
    $row->num_records=hontza_get_canal_personal_num_records($user->uid);
    $row->valor_estrategico=0;
    $result[]=$row;
    $result=array_merge($result,$result_in);    
    //
    return $result;
}
function hontza_get_canal_usuarios_link($uid){
    $title=hontza_get_canal_usuarios_title($uid);
    //return l($title,'canal-usuarios/'.$uid);
    return l($title,'canal_usuario/'.$uid.'/view');
}
function hontza_og_canales_mis_canales_create_order_array($my_list_in){
    $result=$my_list_in;
    $info['field']='node_node_data_field_item_canal_reference_title';
    $info['my_list']=$result;
    return $info;
}
function hontza_get_canal_personal_num_records($uid,$is_all=0){
    $result=0;
    $sql=hontza_get_canal_usuarios_sql($uid);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        if($is_all){
            $result=$result+1;
        }else{    
            $flag_content_array=hontza_get_flag_content_array($row->nid);
            if(!(count($flag_content_array)>0)){
                $result=$result+1;
            }
        }    
    }
    return $result;
}
function hontza_get_flag_content_array($nid){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="fc.content_type='node'";
    $where[]="fc.content_id=".$nid;
    $sql="SELECT fc.*
    FROM {flag_content} fc
    WHERE ".implode(" AND ",$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_set_og_canales_block_link($content_in){
    //intelsat-2015
    $selected_canal_nid=red_funciones_get_filtro_por_canal();
    //
                    $content_array=explode('href="',$content_in);
                    if(!empty($content_array)){
                        foreach($content_array as $i=>$v_orig){
                            $v=$v_orig;
                            $pos=strpos($v,'"');
                            $href=substr($v,0,$pos);
                            $my_array=explode("/",$href);
                            $num=count($my_array);
                            if($my_array[$num-2]=='canales'){
                                $canal_nid=trim($my_array[$num-1]);
                                //intelsat-2015
                                if($canal_nid==$selected_canal_nid){
                                    //
                                    //if(!hontza_is_show_canales_pendientes_tab($canal_nid)){
                                        $value=str_replace('canales/'.$canal_nid,'vigilancia/validados',$v);
                                        $result[$i]=hontza_canales_add_popup_en_indices($value,$canal_nid);
                                    /*}else{
                                        $result[$i]=hontza_canales_add_popup_en_indices($v,$canal_nid);
                                    }*/
                                }else{
                                    //
                                    if(!hontza_is_show_canales_pendientes_tab($canal_nid)){
                                        $value=str_replace('canales/'.$canal_nid,'canales/'.$canal_nid.'/validados',$v);
                                        $result[$i]=hontza_canales_add_popup_en_indices($value,$canal_nid);
                                    }else{
                                        $result[$i]=hontza_canales_add_popup_en_indices($v,$canal_nid);
                                    }
                                }    
                            }else{
                                $uid=arg(1);
                                /*
                                //intelsat-2015
                                if($uid=='canal-usuarios'){
                                    $uid=arg(2);                                    
                                }
                                //
                                */
                                //gemini-2014
                                if($uid==$selected_canal_nid){
                                    $value=str_replace('canales/canal-usuarios/'.$uid,'vigilancia/validados',$v);
                                    //$value=str_replace('canal-usuarios/','vigilancia/validados',$v);
                                    $value=str_replace('canal-usuarios/all','vigilancia/validados',$value);
                                    //intelsat-2015
                                    $value=str_replace('canales/canal-usuarios/','canal-usuarios/',$v);                                
                                    $value=str_replace('canal-usuarios/all','canal-usuarios',$value);
                                    //
                                }else{
                                    $value=str_replace('canales/canal-usuarios/','canal-usuarios/',$v);                                
                                    $value=str_replace('canal-usuarios/all','canal-usuarios',$value);                                                                        
                                }
                                //intelsat-2016
                                $value=red_copiar_get_noticia_usuario_validados_href($value);
                                $result[$i]=$value;
                            }
                        }
                    }
                    $content=implode('href="',$result);
                    return $content;
}
function hontza_fuente_comment_link($node){
    return hontza_item_comment_link($node);
}
function hontza_fuente_edit_link($node){
    return hontza_item_edit_link($node);
}
function hontza_fuente_delete_link($node){
    return hontza_item_delete_link($node);
}
function hontza_canal_edit_link($node){
    return hontza_item_edit_link($node);
}
function hontza_canal_delete_link($node){
    return hontza_item_delete_link($node,t('Delete channel'));
}
function hontza_define_canal_usuarios_menu(){
        $uid='';
        $add_uid='';
        $arg_type=arg(1);
        if(is_numeric($arg_type)){
            $uid=arg(1);
            $arg_type=arg(2);
            $add_uid='/'.$uid;
        }
        if(empty($arg_type)){
            $arg_type='pendientes';
        }

        $html=array();
        if(!empty($arg_type) && $arg_type=='pendientes'){
            $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
        }else{
            $html[]='<div class="tab-wrapper clearfix primary-only">';
        }
        $html[]='<div id="tabs-primary" class="tabs primary">';
        $html[]='<ul>';
        //if(hontza_is_show_vigilancia_pendientes_tab()){
            $html[]='<li'.hontza_canal_usuarios_menu_class('pendientes',$arg_type).'>'.l(t('Pending'),'canal-usuarios'.$add_uid.'/pendientes').'</li>';
        //}
        $html[]='<li'.hontza_canal_usuarios_menu_class('validados',$arg_type).'>'.l(t('Validated'),'canal-usuarios'.$add_uid.'/validados').'</li>';
        //intelsat-2015
        if(red_despacho_is_show_lo_mas_valorado()){
            $html[]='<li'.hontza_canal_usuarios_menu_class('lo-mas-valorado',$arg_type).'>'.l(t('Top Rated'),'canal-usuarios'.$add_uid.'/lo-mas-valorado').'</li>';
        }
        //intelsat-2015
        if(red_despacho_is_show_lo_mas_comentado()){
            $html[]='<li'.hontza_canal_usuarios_menu_class('lo-mas-comentado',$arg_type).'>'.l(t('Most commented'),'canal-usuarios'.$add_uid.'/lo-mas-comentado').'</li>';
        }
        $html[]='<li'.hontza_canal_usuarios_menu_class('ultimas',$arg_type).'>'.l(t('All'),'canal-usuarios'.$add_uid.'/ultimas').'</li>';
        $html[]='<li'.hontza_canal_usuarios_menu_class('rechazados',$arg_type).'>'.l(t('Rejected'),'canal-usuarios'.$add_uid.'/rechazados').'</li>';
        //intelsat-2015
        if(hontza_solr_funciones_is_bookmark_activado()){
            $html[]='<li'.hontza_canal_usuarios_menu_class('bookmarks',$arg_type).'>'.l(t('Bookmarked'),'canal-usuarios'.$add_uid.'/bookmarks').'</li>';
        }
        //
        $html[]='</ul>';
        $html[]='</div>';
        $html[]='</div>';
        $output=implode('',$html);
        $output.=hontza_define_vigilancia_form_filter();
        //intelsat-2015
        /*$bookmark_form_ini=hontza_solr_funciones_get_bookmark_ini(0);
        if($arg_type=='bookmarks' && hontza_solr_funciones_is_bookmark_activado()){
            $output.=$bookmark_form_ini;
        }*/
        //
        //intelsat-2015
        $output=canal_usuario_get_canal_usuario_acciones($uid).$output;
        return $output;
}
function hontza_canal_usuarios_menu_class($arg_type,$param_in=''){
    $result=0;
    if(empty($param_in)){
        $param=arg(1);
    }else{
        $param=$param_in;
    }
    if(empty($param)){
        if($arg_type=='pendientes'){
            $result=1;
        }
    }else{
        if($param==$arg_type){
            $result=1;
        }
    }    
    if($result){
        return ' class="active"';
    }
    return '';
}
function hontza_get_title_fuente_simbolo_img(){
    //intelsat-2016
    global $base_url;       
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/fuente32.png"/>';
    return implode('',$html);
}
function hontza_get_title_wiki_simbolo_img(){
    //intelsat-2016
    global $base_url;  
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/wiki32.png"/>';
    return implode('',$html);
}
function hontza_get_wiki_img(){
    //intelsat-2016
    global $base_url;  
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/trabajo.png"/>';
    return implode('',$html);
}
function hontza_get_title_debate_simbolo_img(){
    //intelsat-2016
    global $base_url;  
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/debate32.png"/>';
    return implode('',$html);
}
function hontza_get_debate_img(){
    //intelsat-2016
    global $base_url;  
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/debate.png"/>';
    return implode('',$html);
}
function hontza_get_title_canal_simbolo_img(){
    //intelsat-2016
    global $base_url;  
    $html=array();
    $html[]='<img src="'.$base_url.'/sites/all/themes/buho/images/icons/canal32.png"/>';
    return implode('',$html);
}
function hontza_is_atom_by_xml($xml){
    if(isset($xml->entry->content)){
        return 1;
    }
    return 0;
}
function hontza_atom2rrs($url,$is_save=0){
    global $base_url;
    $chan = new DOMDocument();
    $chan->load($url); /* load channel */
    $sheet = new DOMDocument();
    //$sheet->load($base_url.'/sites/all/modules/hontza/atom2rss.xsl'); /* use stylesheet from this page */
    $sheet->load('sites/all/modules/hontza/atom2rss.xsl');
    $processor = new XSLTProcessor();
    $processor->registerPHPFunctions();
    $processor->importStylesheet($sheet);
    $result = $processor->transformToXML($chan);
    /*if($is_save){
        $new_url='atom_tmp/'.time().'.xml';
        file_put_contents($new_url,$result); 
        return $new_url;  
    }*/
    return $result;
}
function hontza_get_elemento_link_by_atom($elemento){
    $result='';
    $sets = $elemento->link;
    $all=$sets->count();
    if($all>0){
        return (string) $sets[$all-1];
    }
    return $result;
}
function hontza_is_atom_by_url($url){
     $content=file_get_contents($url);   
     $xml = new SimpleXMLElement($content);
     return hontza_is_atom_by_xml($xml);
}
function hontza_is_canal_atom($node){
    if($node->type=='canal_de_yql'){
        $row=hontza_get_content_type_canal_de_yql($node->nid,$node->vid);
        if(isset($row->field_is_atom_value) && !empty($row->field_is_atom_value)){
            return 1;
        }
    }
    return 0;
}
function hontza_get_content_type_canal_de_yql($nid,$vid){
    $res=db_query('SELECT * FROM {content_type_canal_de_yql} WHERE nid=%d AND vid=%d',$nid,$vid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();    
    return $my_result;
}
function hontza_get_icono_grupo_congelado($row,$with_transparente,&$is_congelado){
    global $base_url;
    //
    return '';
    //
    $is_congelado=0;
    $path=$base_url.'/'.drupal_get_path('theme','buho').'/images/icons/';
    $icon_name='transparente';
    
            if(empty($title)){
                if(isset($row->title)){
                    $title=$row->title;
                }else if($row->node_title){
                    $title=$row->node_title;
                }
            }
            
    if(isset($row->is_grupo_en_subdominio_congelado)){
        if(!empty($row->is_grupo_en_subdominio_congelado)){
            $is_congelado=1;
        }
    }else{        
        if(hontza_is_grupo_congelado($row)){
            $is_congelado=1;
        }    
    }    
    if($is_congelado){    
        $icon_name='grupo_desactivado';
        $icon=$path.$icon_name.'.png';        
    }    
    $icon=$path.$icon_name.'.png';
    
    if(!$with_transparente){
        if($icon_name=='transparente'){
            return '';
        }
    }
        
    return '<img class="icono_grupo_red_alerta" src="'.$icon.'" title="'.$title.'" alt="'.$title.'"/>';
}
function hontza_is_grupo_congelado($row,$is_grupo_seleccionado=0){
    $ok=0;
    if(hontza_is_congelar_canal_sareko_id()){
        $active_refresh=variable_get('active_refresh_subdomain',0);
        if(empty($active_refresh)){
            return 1;
        }
        if(isset($row->nid) && !empty($row->nid)){
            $grupo_node=node_load($row->nid);
        }else{
            //intelsat-2015
            $grupo_node=og_get_group_context();
        }
        if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
            if(isset($grupo_node->field_group_active_refresh) && isset($grupo_node->field_group_active_refresh[0]) && isset($grupo_node->field_group_active_refresh[0]['value'])){                            
                $v=$grupo_node->field_group_active_refresh[0]['value'];
                if(!empty($v) && $v==1){
                    $ok=1;
                }
            }    
        }else{
            if($is_grupo_seleccionado){
                return 0;
            }
        }
        if($ok){
            /*if(hontza_is_todos_los_canales_del_grupo_congelados($grupo_node->nid)){                
                return 1;
            }*/
            return 0;
        }
        return 1;
    }
    return 0;
}
function hontza_is_todos_los_canales_del_grupo_congelados($grupo_nid){
    $canal_array=hontza_get_canales_del_grupo($grupo_nid);
    if(!empty($canal_array)){
        foreach($canal_array as $i=>$row){
            if(!hontza_is_canal_congelado($row)){
                return 0;
            }
        }
        return 1;
    }
    return 0;
}
function hontza_get_canales_del_grupo($grupo_nid,$is_busqueda=0){
    $where=array();
    $where[]='1';
    $where[]='og_ancestry.group_nid='.$grupo_nid;
    if($is_busqueda){
        $where[]='n.type IN("canal_busqueda")';
    }else{
        $where[]='n.type IN("canal_de_supercanal","canal_de_yql")';    
    }
    $sql='SELECT n.* 
    FROM {node} n
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
    WHERE '.implode(' AND ',$where).'
    ORDER BY n.title ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_set_icono_congelado_by_pagina($rows){
    $result=array();
    if(!empty($rows)){
        foreach($rows as $i=>$r){
            $result[$i]=$r;
            $result[$i][3]=$r['title_temp'];
            unset($result[$i]['title_temp']);
            unset($result[$i]['is_congelado']);
        }
    }
    return $result;   
}
function hontza_get_node_field($nid,$field){
    $node=hontza_get_node($nid);
    if(isset($node->$field)){
        return $node->$field;
    }
    return '';
}
function hontza_get_item_url_cut($elemento){
    $link='';
    $guid='';
    $url='';
    $result['link']='';
    $result['guid']='';
    $result['url']='';
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
    }
    //
    $find='www.airbus.com';
    $link=hontza_cut_url($link,$find);
    $guid=hontza_cut_url($guid,$find);
    $url=hontza_cut_url($url,$find);
    if(!empty($link)){
        $result['link']=$link;
    }
    if(!empty($guid)){
        $result['guid']=$guid;
    }
    if(!empty($url)){
        $result['url']=$url;
    }
    red_canal_fix_import_rss_item_url($result);
    /*echo print_r($result,1);
    exit();*/
    return $result;
}
//function hontza_cut_url($url,$find){
function hontza_cut_url($url_in,$find){    
    $url=$url_in;           
    $pos=strpos($url,$find);
    if($pos===FALSE){
        return '';
    }
    //
    $pos2=strpos($url,'?');
    if($pos2===FALSE){
        return '';
    }
    //
    $result=substr($url,0,$pos2);
    return $result;
}
function hontza_repase_menu_by_lang($result_in,$lang){
    //global $language;
    $result=array();
    if(!empty($result_in)){
        foreach($result_in as $menu_id=>$row){
            if($lang==$row['langcode']){
                $result[$menu_id]=$row;
            }
        }              
    }
    return $result;
}
function hontza_get_rol_base_user($user){
    return '';
    if($user->uid==1){
        return '<div><b>('.t('Root').')</b></div>';
    }
    $rol_order_array=red_funciones_define_rol_order_array();
    //
    if(isset($user->roles) && !empty($user->roles)){
        foreach($rol_order_array as $rid=>$label){
            foreach($user->roles as $key=>$my_label){
                if($rid==$key){
                    /*if($rid==2){
                        return '<b>('.t('User').')</b>';
                    }else{*/    
                    return '<div><b>('.$label.')</b></div>';    
                    //}
                }
            }
        }
    }
    return '<div><b>('.t('User').')</b></div>';
}
function hontza_define_user_menu_input_select(){
    global $base_url;
    global $user;
    if(red_funciones_is_desplegables_comprimidos()){
        return red_funciones_define_user_menu_input_select_comprimidos();
    }
    $result=array();
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }
    //
    $result[]='<select id="menu_user_select" name="menu_user_select" style="clear:both;padding-left:0px;">';
    //$selected='';
    $selected=' selected="selected"';
    /*$selected_user=hontza_get_selected_mi_perfil();
    $selected_mis_contenidos=hontza_get_selected_mis_contenidos();
    $selected_mis_grupos=hontza_get_selected_mis_grupos();
    $selected_mis_alertas=hontza_get_selected_mis_alertas();*/
    $selected_user='';
    $selected_mis_contenidos='';
    $selected_mis_grupos='';
    $selected_mis_alertas='';
    $selected_ayuda='';
    $selected_logout='';
    /*$selected_panel_de_gestion=hontza_get_selected_panel_de_gestion();
    $selected_panel_gestion_grupos=hontza_get_selected_panel_gestion_grupos();
    $selected_panel_gestion_usuarios=hontza_get_selected_panel_gestion_usuarios();
    $selected_translate_interface=hontza_get_selected_translate_interface();
    $selected_network=hontza_get_selected_network();
    $selected_fuentes_local=hontza_get_selected_fuentes_local();*/        
    //$result[]='<option value="none"'.$selected.'>'.$user->name.' '.hontza_get_rol_base_user($user).'</option>';
    $result[]='<option value="none"'.$selected.'>'.$user->name.'</option>';    
    $result[]='<option value="'.url('user').'"'.$selected_user.'>'.t('My Profile').'</option>';
    $result[]='<option value="'.url('mis-contenidos').'"'.$selected_mis_contenidos.'>'.t('My Contents').'</option>';
    $result[]='<option value="'.url('mis-grupos').'"'.$selected_mis_grupos.'>'.t('My Groups').'</option>';
    $result[]='<option value="'.url('alerta_user/'.$user->uid.'/my_list').'"'.$selected_mis_alertas.'>'.t('My Alerts').'</option>';
    /*$result_mana=array();
    if ($user->roles[ADMINISTRADOR] or $user->uid == 1) {
        $result_mana[]='<option value="'.url('gestion').'"'.$selected_panel_de_gestion.'>'.t('Management panel').'</option>';    
    }
    if ($user->roles[CREADOR] or $user->uid == 1) {
        $result_mana[]='<option value="'.url('user-gestion/grupos/propios').'"'.$selected_panel_gestion_grupos.'>'.t('Groups').'</option>';
        if(!hontza_is_sareko_id('ROOT') && hontza_user_access_gestion_usuarios()){
          $result_mana[]='<option value="'.url('gestion/usuarios').'"'.$selected_panel_gestion_usuarios.'>'.t('List of Users').'</option>';
        } 
    }
    if ($user->roles[TRADUCTORES]) {
        $result_mana[]='<option value="'.url('admin/build/translate').'"'.$selected_translate_interface.'>'.t('Translate interface').'</option>';
    }*/ 
    
    if(hontza_is_sareko_id_red()){
      if(!hontza_is_user_anonimo()){
        if(hontza_user_access_recursos_red_alerta()){ 
           $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
           $result[]='<option value="'.url('red_red/fuentes').'"'.$selected_network.'>'.t('Network').'</option>';           
        }
        /*if(hontza_user_access_red_local()){ 
           $result[]='<option value="'.url('red_local/fuentes_local').'"'.$selected_fuentes_local.'>'.t('Shared').'</option>';        
        }*/
      }
    }
    if(hontza_is_mostrar_recursos_compartidos_del_servidor_red()){
        if(!is_super_admin()){
            $result[]='<option value="'.url('red_compartir/borrarme_del_servidor').'"'.$selected_network.'>'.t('Delete my account').'</option>';
        }    
    }/*else{
        //simulando
        if(hontza_is_sareko_id('ROOT') && is_super_admin()){
            $result[]='<option value="'.url('red_compartir/borrarme_del_servidor').'"'.$selected_network.'>'.t('Delete my account').'</option>';
        }
    }*/
    //$result[]='<option value="'.url('logout').'"'.$selected_logout.'>'.t('Logout').'</option>';
    $result[]='<option value="logout"'.$selected_logout.'>'.t('Logout').'</option>';
    $result[]='</select>';
    if(!empty($purl)){
        $purl=$purl.'/';
    }
    //
    $js='
        $(document).ready(function()
        {
            $("#menu_user_select option")[0].selected=true;
            $("#menu_user_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url=="logout"){
                    location.href="'.url('logout').'";
                }else if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_select option")[0].selected=true;
                }
                //alert(my_menu_user_url);                
            });            
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function hontza_get_selected_mi_perfil(){
    if(is_mi_perfil(0)){
       return ' selected="selected"';     
    }
    return '';
}
function hontza_get_selected_mis_contenidos(){
    if(is_mis_contenidos()){
       return ' selected="selected"';     
    }
    return '';
}
function hontza_get_selected_mis_grupos(){
    if(hontza_is_mis_grupos()){
         return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_mis_alertas(){
    if(hontza_is_mis_alertas_boletines()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_panel_de_gestion(){
    if(hontza_is_gestion()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_panel_gestion_grupos(){
    if(hontza_is_user_gestion()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_panel_gestion_usuarios(){
    if(hontza_is_gestion('usuarios')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_translate_interface(){
    if(hontza_is_admin_build_translate()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_is_admin_build_translate(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='admin'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='build'){
            $param2=arg(2);
            if(!empty($param2) && $param2=='translate'){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_get_selected_network(){
    if(red_is_red_red_fuentes()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_fuentes_local(){
    if(red_is_fuentes_local()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_define_user_menu_management_input_select(){
    global $base_url;
    global $user;
    if(red_funciones_is_desplegables_comprimidos()){
        return red_funciones_define_user_menu_management_input_select_comprimidos();
    }
    $result=array();
    $gestion_array=array();
    //
    $my_grupo=og_get_group_context();
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }
    //
    $result[]='<select id="menu_user_gestion_select" name="menu_user_gestion_select" style="clear:both;">';
    $selected='';
    $selected_panel_de_gestion='';
    $selected_panel_gestion_grupos='';
    $selected_panel_gestion_usuarios='';
    $selected_translate_interface='';
    $selected_network='';
    $selected_fuentes_local='';
    $selected_gestion_faq='';
    $selected_gestion_estadisticas='';
    $selected_gestion_post_form='';
    $selected_gestion_claves='';
    $selected_gestion_fuentes='';
    $selected_gestion_canales='';
    $selected_gestion_respuesta='';
    $selected_gestion_wiki='';
    $selected_gestion_debate='';
    $selected_gestion_servicios='';
    $selected_gestion_noticias_publicas='';
    $selected_gestion_ayuda_popup='';
    //
    $result[]='<option value="none"'.$selected.'>'.t('Help/Management').'</option>';
    if(hontza_is_sareko_id_red()){
        $result[]='<option value="'.url('red/guia_usuario').'">'.t("User's Guide").'</option>';
        $result[]='<option value="'.url('red/guia_administrador').'">'.t("Administrator's Guide").'</option>';    
    }
    if($user->uid==1){
        //$label_faq=t('FAQ');
        $label_faq=t('Faq');
        $gestion_array[]='<option value="'.url('panel_admin/ayuda').'"'.$selected_gestion_faq.'>'.$label_faq.'</option>';        
    }else{
        $result[]='<option value="'.url('faq').'"'.$selected_ayuda.'>'.t('Help').'</option>';    
    }
    if($user->uid==1){
        //$label_ayuda_popup=t('POPUP HELP');
        $label_ayuda_popup=t('Popup Help');
        $gestion_array[]='<option value="'.url('panel_admin/ayuda_popup').'"'.$selected_gestion_ayuda_popup.'>'.$label_ayuda_popup.'</option>';        
    }    
    if ($user->roles[ADMINISTRADOR] or $user->uid == 1) {
        //$label_panel=t('Management panel')
        $label_panel=t('Panel');
        $gestion_array[]='<option value="'.url('gestion').'"'.$selected_panel_de_gestion.'>'.$label_panel.'</option>';    
    }
    //intelsat-2015
    //if ($user->roles[CREADOR] || $user->uid == 1) {
    if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR] || $user->uid == 1) {    
    //if ($user->roles[CREADOR] || $user->roles[ADMINISTRADOR_DE_GRUPO] || $user->uid == 1) {
        if(hontza_user_access_gestion_usuarios()){      
            $label_users=t('Users');
            $gestion_array[]='<option value="'.url('gestion/usuarios').'"'.$selected_panel_gestion_usuarios.'>'.$label_users.'</option>';
        }
        $label_groups=t('Groups');
        $gestion_array[]='<option value="'.url('user-gestion/grupos/propios').'"'.$selected_panel_gestion_grupos.'>'.$label_groups.'</option>';       
    }
    //if ($user->roles[ADMINISTRADOR] or $user->uid == 1) {
    $label_servicios=t('Experts');        
    if($user->uid == 1){
        $label_fuentes=t('Sources');
        $gestion_array[]='<option value="'.url('admin/content/taxonomy/1').'"'.$selected_gestion_fuentes.'>'.$label_fuentes.'</option>';        
        $label_channels=t('Channels');
        $gestion_array[]='<option value="'.url('gestion/gestion_canales').'"'.$selected_gestion_canales.'>'.$label_channels.'</option>';        
        $label_channels=t('News');
        $gestion_array[]='<option value="'.url('panel_admin/items').'">'.$label_channels.'</option>';
        $label_channels=t('User News');
        $gestion_array[]='<option value="'.url('panel_admin/noticias').'">'.$label_channels.'</option>';
        $label_debate=t('Discussion');
        $gestion_array[]='<option value="'.url('panel_admin/debate').'"'.$selected_gestion_debate.'>'.$label_debate.'</option>';        
        //$label_wiki=t('Management - WIKI');
        $label_wiki=t('Collaboration');
        $gestion_array[]='<option value="'.url('panel_admin/collaboration').'"'.$selected_gestion_wiki.'>'.$label_wiki.'</option>';        
        /*$label_respuesta=t('Proposals');
        $gestion_array[]='<option value="'.url('admin/content/hontza/my_idea_settings').'"'.$selected_gestion_respuesta.'>'.$label_respuesta.'</option>';*/
        //$label_servicios=t('FACILITATORS');
        $gestion_array[]='<option value="'.url('panel_admin/servicios').'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';                 
        if(!hontza_is_sareko_id_red()){
            $label_noticias_publicas=t('Public News');
            $gestion_array[]='<option value="'.url('gestion/my_noticias_publicas').'"'.$selected_gestion_noticias_publicas.'>'.$label_noticias_publicas.'</option>';
        }
        //$label_estadisticas=t('STATISTICS');
        $label_estadisticas=t('Statistics');
        $gestion_array[]='<option value="'.url('analytics').'"'.$selected_gestion_estadisticas.'>'.$label_estadisticas.'</option>';
        $label_post_form=t('Forms');
        $gestion_array[]='<option value="'.url('frases_post_formulario').'"'.$selected_gestion_post_form.'>'.$label_post_form.'</option>';        
        //$label_claves=t('PASSWORDS');
        $label_claves=t('Passwords');
        $gestion_array[]='<option value="'.url('gestion/claves').'"'.$selected_gestion_claves.'>'.$label_claves.'</option>';                        
    }else{
        $result[]='<option value="'.url('servicios').'"'.$selected_gestion_servicios.'>'.$label_servicios.'</option>';
    }
    if ($user->roles[TRADUCTORES]) {
        $gestion_array[]='<option value="'.url('admin/build/translate').'"'.$selected_translate_interface.'>'.t('Translate interface').'</option>';
    } 
    /*if(hontza_is_sareko_id_red()){
      if(!hontza_is_user_anonimo()){
        if(hontza_user_access_recursos_red_alerta()){ 
           $result[]='<option value="'.url('red_red/fuentes').'"'.$selected_network.'>'.t('Network').'</option>';
        }
        if(hontza_user_access_red_local()){ 
           $result[]='<option value="'.url('red_local/fuentes_local').'"'.$selected_fuentes_local.'>'.t('Shared').'</option>';        
        }
      }
    }*/
    /*if(empty($gestion_array)){
        return '';
    }*/
    $result[]=implode('',$gestion_array);
    $result[]='</select>';
    if(!empty($purl)){
        $purl=$purl.'/';
    }
    //
    $js='
        $(document).ready(function()
        {
            $("#menu_user_gestion_select option")[0].selected = true;
            $("#menu_user_gestion_select").change(function()
            {
                var my_menu_user_url=$(this).attr("value");
                if(my_menu_user_url!="none"){
                    //location.href=my_menu_user_url;
                    window.open(my_menu_user_url);
                    $("#menu_user_gestion_select option")[0].selected = true;
                }
                //alert(my_menu_user_url);                
            });
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function hontza_get_selected_panel_gestion_faq(){
    if(hontza_is_gestion('faq')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_define_user_menu_management_input_select_html(){
    $html='';
    $input_select=hontza_define_user_menu_management_input_select();
    if(!empty($input_select)){
        //$html='<li style="float:left;">'.t('Management').'<div>'.$input_select.'</div></li>';
        //intelsat-2016
        //$html='<li style="float:left;padding-left:0px;">'.$input_select.'</li>';
        $html='<li style="padding-left:0px;">'.$input_select.'</li>';
    }
    return $html;
}
function hontza_get_selected_gestion_estadisticas(){
    if(hontza_is_analytics()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_is_analytics(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='analytics'){
        return 1;
    }
    return 0;
}
function hontza_get_selected_gestion_post_form(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='frases_post_formulario'){
        return 1;
    }
    return 0;
}
function hontza_get_selected_gestion_claves(){
    if(hontza_is_gestion('claves')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_gestion_fuentes(){
    if(hontza_is_gestion('lista-publicas')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_gestion_canales(){
     if(hontza_is_gestion('gestion_canales')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_gestion_respuesta(){
    if(hontza_is_gestion_respuesta()){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_is_gestion_respuesta(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='admin'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='content'){
            $param2=arg(2);
            if(!empty($param2) && $param2=='hontza'){
                $param3=arg(3);
                if(!empty($param3) && $param3=='my_idea_settings'){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function hontza_get_selected_gestion_wiki(){
    if(hontza_is_gestion('wiki')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_gestion_debate(){
    if(hontza_is_gestion('debate')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_get_selected_gestion_servicios(){
    if(hontza_is_gestion('servicios')){
        return ' selected="selected"'; 
    }
    return '';
}
function hontza_add_js_mi_grupo(){
	if(hontza_is_mi_grupo()){
            my_add_active_trail_js('id_a_mi_grupo');		
	}
}
function hontza_is_mi_grupo(){    
    return hontza_grupos_mi_grupo_is_mi_grupo();
}
function hontza_get_responsable_secundario_user($canal_nid){
    return red_funciones_get_responsable($canal_nid,1);
}