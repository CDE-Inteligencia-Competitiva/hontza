<?php
function create_usuarios_estadisticas_menu(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only" style="margin-top:0;">';
    $html[]='<div class="tabs primary" id="tabs-primary">';
    $html[]='<ul>';
    //
    $param=get_todos_param(arg(1));
    $class_usuarios=is_class_active('usuarios_estadisticas');
    $class_acceso=is_class_active('usuarios_acceso');
    $class_captacion=is_class_active('usuarios_captacion_informacion');
    $class_aportacion=is_class_active('usuarios_aportacion_valor');
    $class_generacion=is_class_active('usuarios_generacion_ideas');    
    //$html[]='<li>'.l(t('Users'),'usuarios_estadisticas/'.$param,array('query'=>drupal_get_destination())).'</li>';
    //$html[]='<li'.$class_usuarios.'>'.l(t('Users'),'usuarios_estadisticas/todos',array('query'=>drupal_get_destination())).'</li>';
    /*$html[]='<li'.$class_captacion.'>'.l(t('Information Gathering'),'usuarios_captacion_informacion/'.$param,array('query'=>drupal_get_destination())).'</li>';
    $html[]='<li'.$class_aportacion.'>'.l(t('Analysis'),'usuarios_aportacion_valor/'.$param,array('query'=>drupal_get_destination())).'</li>';
    $html[]='<li'.$class_generacion.'>'.l(t('Proposals'),'usuarios_generacion_ideas/'.$param,array('query'=>drupal_get_destination())).'</li>';
    */
    $html[]='<li'.$class_acceso.'>'.l(t('Access'),'usuarios_acceso/'.$param).'</li>';
    $html[]='<li'.$class_captacion.'>'.l(t('Information Gathering'),'usuarios_captacion_informacion/'.$param).'</li>';
    $html[]='<li'.$class_aportacion.'>'.l(t('Analysis'),'usuarios_aportacion_valor/'.$param).'</li>';
    $html[]='<li'.$class_generacion.'>'.l(t('Proposals'),'usuarios_generacion_ideas/'.$param).'</li>';
    $html[]='</ul>';
    $html[]='</div></div>';
    //
    return implode('',$html);
}
function usuarios_captacion_informacion_callback(){
  $output='';
  drupal_set_title(t('Information Gathering').get_la_empresa());
  $user_list=get_estadisticas_user_list('usuarios_captacion_informacion');  
  $output=get_usuarios_captacion_informacion_html($user_list);
  return $output;
}
function get_estadisticas_user_list($konp){
  if(is_usuarios_estadisticas(1,$konp)){
    $user_list=my_get_og_grupo_user_list('',1,arg(1));
  }else{
    $user_list=my_get_og_grupo_user_list('',1);
  }
  return $user_list;
}
function get_usuarios_captacion_informacion_html($user_list){
    $output='';
    $output=create_usuarios_estadisticas_menu();
    $output.=hontza_define_usuarios_acceso_form_filter();
    if(count($user_list)){
        //
        $headers=array();
        $headers[0]=t('Photo');
        $headers[1]=get_header_usuarios('usuario',t('User'));
        $headers[2]=get_header_usuarios('num_fuentes_creadas',t('Sources'));
        $headers[3]=get_header_usuarios('num_canales_creados',t('Channels'));
        $headers[4]=get_header_usuarios('num_noticias_usuario_creadas',t('User news'));
        $headers[5]=get_header_usuarios('num_noticias_rechazadas',t('Rejected News'));
        $headers[6]=get_header_usuarios('num_noticias_validadas',t('Validated news'));
        //
        $rows=array();
        $kont=0;
        $my_limit=hontza_define_usuarios_acceso_table_limit();
        //
        $rechazada_flag=get_flag_row('leido_no_interesante');
        $fid_rechazada=0;
        if(isset($rechazada_flag->fid) && !empty($rechazada_flag->fid)){
            $fid_rechazada=$rechazada_flag->fid;
        }
        //
        $validado_flag=get_flag_row('leido_interesante');
        $fid_validado=0;
        if(isset($validado_flag->fid) && !empty($validado_flag->fid)){
            $fid_validado=$validado_flag->fid;
        }
        $my_list=prepare_user_list_to_order($user_list,$fid_validado,$fid_rechazada);
        $my_list=call_array_ordenatu($my_list);
        //
        foreach($my_list as $i=>$capta){
            $rows[$kont]=array();
            $rows[$kont][0]=hontza_grupos_mi_grupo_get_user_img($capta->uid,50);   
            $rows[$kont][1]=l($capta->usuario,'hontza_grupos/'.$capta->uid.'/user_view',array('query'=>drupal_get_destination()));
            $rows[$kont][2]=$capta->num_fuentes_creadas;
            $rows[$kont][3]=$capta->num_canales_creados;
            $rows[$kont][4]=$capta->num_noticias_usuario_creadas;
            $rows[$kont][5]=$capta->num_noticias_rechazadas;
            $rows[$kont][6]=$capta->num_noticias_validadas;
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output.= theme('table',$headers,$rows);
        $output.= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no users'). '</div>';
    }
    return $output;
}
function get_captacion_informacion_by_user($uid,$fid_validado,$fid_rechazada){
    $result=array();
    $where_time=hontza_get_usuarios_acceso_where_time('n.created');
    $where_flag_time=hontza_get_usuarios_acceso_where_time('fc.timestamp');
    $result['num_fuentes_creadas']=get_num_fuentes_creadas($uid,$where_time);
    $result['num_canales_creados']=get_num_canales_creados($uid,$where_time);
    $result['num_noticias_usuario_creadas']=get_num_noticias_usuario_creadas($uid,$where_time);
    $result['num_noticias_rechazadas']=get_num_noticias_flag($uid,$fid_rechazada,$where_flag_time);
    $result['num_noticias_validadas']=get_num_noticias_flag($uid,$fid_validado,$where_flag_time);
    //$result['num_paginas_visitadas']=hontza_get_num_paginas_visitadas_by_usuario($uid);
    return $result;
}
function get_num_fuentes_creadas($uid,$where_time="1"){    
    $result=get_node_creadas_by_user($uid,array("'supercanal'","'fuentedapper'"),$where_time);
    return count($result);
}
function get_num_canales_creados($uid,$where_time="1"){    
    $result=get_node_creadas_by_user($uid,array("'canal_de_supercanal'","'canal_de_yql'"),$where_time);
    return count($result);
}
function get_num_noticias_usuario_creadas($uid,$where_time="1"){ 
    //$type_array=array("'noticia'","'rss_feed'","'noticias_portada'");
    $type_array=array("'noticia'");
    $result=get_node_creadas_by_user($uid,$type_array,$where_time);       
    return count($result);
}
function get_num_noticias_flag($uid,$fid,$where_flag_time="1"){
    $result=get_noticias_flag_by_user($uid,$fid,$where_flag_time);
    return count($result);
}
function get_node_creadas_by_user($uid,$type_array,$where_time="1"){
    $result=array();
    $where=array();
    $where[]=$where_time;
    $where[]="n.uid=".$uid;
    $where[]="n.type IN(".implode(",",$type_array).")";
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //
    $sql="SELECT n.* 
    FROM {node} n 
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid
    WHERE ".implode(" AND ",$where)." ORDER BY n.created ASC";
    //print $sql.'<BR>';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_noticias_flag_by_user($uid,$fid,$where_flag_time="1"){
    $where=array();
    $where[]=$where_flag_time;
    $where[]="fc.uid=".$uid;
    $where[]="fc.fid=".$fid;
    $where[]="fc.content_type='node'";
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //
    //
    $sql="SELECT fc.* 
    FROM {flag_content} fc LEFT JOIN {og_ancestry} ON fc.content_id=og_ancestry.nid  
    WHERE ".implode(" AND ",$where)." ORDER BY fc.timestamp ASC";
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function usuarios_aportacion_valor_callback(){
  $output='';
  drupal_set_title(t('Analysis').get_la_empresa());
  $user_list=get_estadisticas_user_list('usuarios_aportacion_valor');
  $output=get_usuarios_aportacion_valor_html($user_list);
  return $output;
}
function get_usuarios_aportacion_valor_html($user_list){
    $output='';
    $output=create_usuarios_estadisticas_menu();
    $output.=hontza_define_usuarios_acceso_form_filter();
    if(count($user_list)){
        //
        $headers=array();
        $headers[0]=t('Photo');
        $headers[1]=get_header_usuarios('usuario',t('User'));
        $headers[2]=get_header_usuarios('num_comentarios_realizados',t('Comments'));
        $headers[3]=get_header_usuarios('num_documentos_adjuntos',t('Attachments'));
        $headers[4]=get_header_usuarios('num_debates_creados',t('Discussions'));
        $headers[5]=get_header_usuarios('num_comentarios_debates',t('Comments in discussions'));
        $headers[6]=get_header_usuarios('num_wiki_creadas',t('Wiki pages'));
        //
        $my_list=prepare_user_list_to_order($user_list);
        $my_list=call_array_ordenatu($my_list);
        //
        $rows=array();
        $kont=0;
        $my_limit=hontza_define_usuarios_acceso_table_limit();     
        //
        foreach($my_list as $i=>$aportacion){
            $rows[$kont]=array();
            $rows[$kont][0]=hontza_grupos_mi_grupo_get_user_img($aportacion->uid,50);
            $rows[$kont][1]=l($aportacion->usuario,'hontza_grupos/'.$aportacion->uid.'/user_view');
            $rows[$kont][2]=$aportacion->num_comentarios_realizados;
            $rows[$kont][3]=$aportacion->num_documentos_adjuntos;
            $rows[$kont][4]=$aportacion->num_debates_creados;
            $rows[$kont][5]=$aportacion->num_comentarios_debates;
            $rows[$kont][6]=$aportacion->num_wiki_creadas;
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output.= theme('table',$headers,$rows);
        $output.= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no users'). '</div>';
    }
    return $output;
}
function get_aportacion_valor_by_user($uid){
    $result=array();
    $where_comment_time=hontza_get_usuarios_acceso_where_time('c.timestamp');
    $where_adjuntos_time=hontza_get_usuarios_acceso_where_time('f.timestamp');
    $where_time=hontza_get_usuarios_acceso_where_time('n.created');
    $result['num_comentarios_realizados']=get_num_comentarios_realizados($uid,$where_comment_time);
    $result['num_documentos_adjuntos']=get_num_documentos_adjuntos($uid,$where_adjuntos_time);
    $result['num_debates_creados']=get_num_debates_creados($uid,$where_time);
    $result['num_comentarios_debates']=get_num_comentarios_debates($uid,$where_comment_time);
    $result['num_wiki_creadas']=get_num_wiki_creadas($uid,$where_time);
    return $result;
}
function get_num_comentarios_realizados($uid,$where_comment_time="1"){
    $result=get_comment_list_by_user($uid,'',$where_comment_time);
    return count($result);
}
function get_num_documentos_adjuntos($uid,$where_adjuntos_time="1"){
    $result=get_documentos_adjuntos_by_user($uid,$where_adjuntos_time);
    return count($result);
}
function get_num_debates_creados($uid,$where_time="1"){
    $result=get_node_creadas_by_user($uid,array("'debate'"),$where_time);
    return count($result);
}
function get_num_comentarios_debates($uid,$where_comment_time="1"){
    $result=get_comment_list_by_user($uid,'debate',$where_comment_time);
    return count($result);
}
function get_num_wiki_creadas($uid,$where_time="1"){
    $result=get_node_creadas_by_user($uid,array("'wiki'"),$where_time);
    return count($result);
}
function get_comment_list_by_user($uid,$node_type='',$where_comment_time="1"){
    $result=array();
    $where=array();
    $where[]=$where_comment_time;
    $where[]="c.uid=".$uid;
    if(!empty($node_type)){
        if(is_array($node_type)){
            $where[]="n.type= IN ".implode(",",$node_type);
        }else{
            $where[]="n.type='".$node_type."'";
        }
    }
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
          $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //
    $sql="SELECT c.*
    FROM {comments} c
    LEFT JOIN {node} n ON c.nid=n.nid
    LEFT JOIN {og_ancestry} ON n.nid=og_ancestry.nid 
    WHERE ".implode(" AND ",$where)."
    ORDER BY c.timestamp ASC";
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_documentos_adjuntos_by_user($uid,$where_adjuntos_time="1"){
    $result=array();
    $where=array();
    $where[]=$where_adjuntos_time;
    $where[]="f.uid=".$uid;
    $where[]="(nu.type IS NULL OR nu.type IN('item','noticia','noticias_portada','rss_feed','debate','wiki','idea','oportunidad','proyecto','estrategia','despliegue','decision','informacion'))";
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
          $where[]='og_ancestry.group_nid='.$my_grupo->nid;
    }
    //
    $sql="SELECT f.* 
    FROM {files} f
    LEFT JOIN {upload} u ON f.fid=u.fid
    LEFT JOIN {comment_upload} cu ON f.fid=cu.fid
    LEFT JOIN {node} nu ON (u.nid=nu.nid AND u.vid=nu.vid)
    LEFT JOIN {og_ancestry} ON nu.nid=og_ancestry.nid 
    WHERE ".implode(" AND ",$where)."
    ORDER BY f.timestamp ASC";
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function usuarios_generacion_ideas_callback(){
  $output='';
  drupal_set_title(t('Proposals').get_la_empresa());
  $user_list=get_estadisticas_user_list('usuarios_generacion_ideas');
  $output=get_usuarios_generacion_ideas_html($user_list);
  return $output;
}
function get_usuarios_generacion_ideas_html($user_list){
    $output='';
    $output=create_usuarios_estadisticas_menu();
    $output.=hontza_define_usuarios_acceso_form_filter();
    if(count($user_list)){
        //
        $headers=array();
        $headers[0]=t('Photo');
        $headers[1]=get_header_usuarios('usuario',t('User'));
        $headers[2]=get_header_usuarios('num_ideas_creadas',t('Ideas'));
        $headers[3]=get_header_usuarios('num_oportunidades_creadas',t('Opportunities'));
        $headers[4]=get_header_usuarios('num_proyectos_creados',t('Projects'));
        //
        $my_list=prepare_user_list_to_order($user_list);
        $my_list=call_array_ordenatu($my_list);
        //
        $rows=array();
        $kont=0;
        $my_limit=hontza_define_usuarios_acceso_table_limit();
        //
        foreach($my_list as $i=>$generacion){
            $rows[$kont]=array();
            $rows[$kont][0]=hontza_grupos_mi_grupo_get_user_img($generacion->uid,50);    
            $rows[$kont][1]=l($generacion->usuario,'hontza_grupos/'.$generacion->uid.'/user_view');
            $rows[$kont][2]=$generacion->num_ideas_creadas;
            $rows[$kont][3]=$generacion->num_oportunidades_creadas;
            $rows[$kont][4]=$generacion->num_proyectos_creados;
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output.= theme('table',$headers,$rows);
        $output.= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no users'). '</div>';
    }
    return $output;
}
function get_generacion_ideas_by_user($uid){
    $result=array();
    $where_time=hontza_get_usuarios_acceso_where_time('n.created');
    $result['num_ideas_creadas']=get_num_ideas_creadas($uid,$where_time);
    $result['num_oportunidades_creadas']=get_num_oportunidades_creadas($uid,$where_time);
    $result['num_proyectos_creados']=get_num_proyectos_creados($uid,$where_time);
    return $result;
}
function get_num_ideas_creadas($uid,$where_time="1"){
    //$result=get_node_creadas_by_user($uid,array("'idea'"),$where_time);
    $result=idea_get_num_ideas_creadas($uid,$where_time);
    return count($result);
}
function get_num_oportunidades_creadas($uid,$where_time="1"){
    //$result=get_node_creadas_by_user($uid,array("'oportunidad'"),$where_time);
    $result=oportunidad_get_num_oportunidades_creadas($uid,$where_time);
    return count($result);
}
function get_num_proyectos_creados($uid,$where_time="1"){
    //$result=get_node_creadas_by_user($uid,array("'proyecto'"),$where_time);
    $result=proyecto_get_num_proyectos_creados($uid,$where_time);
    return count($result);
}
function get_todos_param($param){
    if(!empty($param)){
        return $param;
    }
    return 'todos';
}
function is_class_active($param){
    if(is_usuarios_estadisticas(0,$param)){
        return ' class="active"';
    }
    return '';
}
function get_la_empresa(){
    $param=arg(1);
    if(!empty($param) && $param!='todos'){
        return ' '.t('of').' '.$param;
    }
    return '';
}
function get_header_usuarios($field,$esaldi){
    $param=arg(1);
    $pantalla=get_pantalla_usuarios();
    //print 'pantalla==='.$pantalla;
            $my_sort='asc';
            $order=my_get_request_value('order');
            $sort=my_get_request_value('sort');
            if(strcmp($order,$field)==0 && strcmp($sort,'asc')==0){
                $my_sort='desc';
            }            
            $img='';
            if(strcmp($order,$field)==0){
                $img='<img width="13" height="13" title="'.t('sort '.$my_sort.'ending').'" alt="'.t('sort icon').'" src="/misc/arrow-'.$my_sort.'.png">';
            }
            $result=l($esaldi.$img,$pantalla.'/'.$param,array('html'=>true,'query'=>'order='.$field.'&sort='.$my_sort));
     return $result;
}
function get_pantalla_usuarios(){
    $param_array=array('usuarios_captacion_informacion','usuarios_aportacion_valor','usuarios_generacion_ideas');
    if(count($param_array)>0){
        foreach($param_array as $i=>$v){
            if(is_usuarios_estadisticas(0,$v)){
                return $v;
            }
        }
    }
    //gemini-2014
    //return '';
    return 'usuarios_acceso';
}
function prepare_user_list_to_order($user_list,$fid_validado='',$fid_rechazada=''){
    $rows=array();
    if(count($user_list)>0){
        $kont=0;
        foreach($user_list as $i=>$u){
            if(is_usuarios_estadisticas(0,'usuarios_captacion_informacion')){
                $row=get_captacion_informacion_by_user($u->uid,$fid_validado,$fid_rechazada);
            }else if(is_usuarios_estadisticas(0,'usuarios_aportacion_valor')){
                $row=get_aportacion_valor_by_user($u->uid);                
            }else if(is_usuarios_estadisticas(0,'usuarios_generacion_ideas')){
                $row=get_generacion_ideas_by_user($u->uid);
            }else if(is_usuarios_estadisticas(0,'usuarios_acceso')){
                $row=get_usuarios_acceso_by_user($u->uid);
            }
            //
            $rows[$kont]=(object) $row;
            $rows[$kont]->uid=$u->uid;
            //intelsat-2015
            //$rows[$kont]->usuario=$u->nombre.' '.$u->apellidos;
            $rows[$kont]->usuario=red_crear_usuario_get_usuario_capta($u);
            $kont++;
        }
    }
    return $rows;
}
function call_array_ordenatu($my_list){
    $order=my_get_request_value('order');
    $sort=my_get_request_value('sort');
    if(empty($order) || empty($sort)){
        //intelsat-2015
        //return $my_list;
        $order='usuario';
        $sort='asc';
        //
    }
    $is_numeric=1;
    if($order=='usuario'){
        $is_numeric=0;
    }
    //print $order.'='.$sort.'<BR>';
    //echo print_r($my_list,1);exit();
    $result=array_ordenatu($my_list, $order, $sort,$is_numeric);
    return $result;
}
function is_administrador_grupo($modo_estrategia=0,$grupo_nid_in=''){
  $grupo_nid=$grupo_nid_in;
  if(empty($grupo_nid)){
    $my_grupo=og_get_group_context();
    //  
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
          $grupo_nid=$my_grupo->nid;
    }
    //intelsat-2015
    if(empty($grupo_nid)){
        if(isset($_REQUEST['my_grupo_nid']) && !empty($_REQUEST['my_grupo_nid'])){
            $grupo_nid=$_REQUEST['my_grupo_nid'];
        }
    }
  }  
  if($modo_estrategia){
    return is_permiso_gestion_boletin_grupo($grupo_nid,1,'',1);
  }else{  
    return is_permiso_gestion_boletin_grupo($grupo_nid);  
  }  
}
function is_carpeta_dinamica(){
    if(strcmp(arg(0),'busqueda')==0){
        return 1;
    }
    return 0;
}
function is_crear_noticia_usuario(){
     if(strcmp(arg(0),'node')==0 && strcmp(arg(1),'add')==0 && strcmp(arg(2),'noticia')==0){
        return 1;
    }
    return 0;
}
function is_crear_wiki(){
     if(strcmp(arg(0),'node')==0 && strcmp(arg(1),'add')==0 && strcmp(arg(2),'wiki')==0){
        return 1;
    }
    return 0;
}
function is_crear_debate(){
     if(strcmp(arg(0),'node')==0 && strcmp(arg(1),'add')==0 && strcmp(arg(2),'debate')==0){
        return 1;
    }
    return 0;
}
function my_help_crear(){
	if(arg(1)=='fuente-supercanal'){
		return help_popup_window(2386, 'help',my_get_help_link_object());
	}else if(arg(1)=='fuente-dapper'){
		return help_popup_window(2387, 'help',my_get_help_link_object());
	}else if(is_crear_canal_de_supercanal()){
            return my_help_crear_canal_de_supercanal();
        }else if(is_crear_canal_filtro_rss(0)){
            return my_help_canal_filtro_rss();
        }else if(is_crear_canal_filtro_rss(1)){
            return my_help_canal_con_un_rss();
        }

}
function is_crear_canal_de_supercanal(){
    if(strcmp(arg(0),'crear')==0 && strcmp(arg(1),'canal-supercanal')==0){
        return 1;
    }
    return 0;
}
function is_crear_canal_filtro_rss($is_simple=1,$with_simple=1){
    if(strcmp(arg(0),'crear')==0 && strcmp(arg(1),'canal-yql')==0){
        if($with_simple){
            if(isset($_REQUEST['simple'])){
                if(empty($_REQUEST['simple']) && !$is_simple){
                    return 1;
                }else if(!empty($_REQUEST['simple']) && $_REQUEST['simple']==1 && $is_simple){
                    return 1;
                }
            }
        }else{
            return 1;
        }    
    }
    return 0;
}
function is_respuestas_a_retos(){
    if(strcmp(arg(0),'ideas')==0 && strcmp(arg(1),'respuestas_a_retos')==0){
        return 1;
    }
    return 0;
}
function my_help_carpeta_dinamica(){
    return help_popup_window(15811, 'help',my_get_help_link_object());
}
function my_help_crear_modificar_categorias(){
    return help_popup_window(15812, 'help',my_get_help_link_object());
}
function my_help_crear_noticia_usuario(){
    return help_popup_window(15813, 'help',my_get_help_link_object());
}
function my_help_crear_canal_de_supercanal(){
    return help_popup_window(15814, 'help',my_get_help_link_object());
}
function my_help_canal_filtro_rss(){
    return help_popup_window(15815, 'help',my_get_help_link_object());
}
function my_help_canal_con_un_rss(){
    return help_popup_window(15816, 'help',my_get_help_link_object());
}
function my_help_usuarios_captacion_informacion(){
    return help_popup_window(15817, 'help',my_get_help_link_object());
}
function my_help_usuarios_aportacion_valor(){
    return help_popup_window(15818, 'help',my_get_help_link_object());
}
function my_help_usuarios_generacion_ideas(){
    return help_popup_window(15819, 'help',my_get_help_link_object());
}
function my_help_crear_wiki(){
    return help_popup_window(15820, 'help',my_get_help_link_object());
}
function my_help_crear_debate(){
    return help_popup_window(15821, 'help',my_get_help_link_object());
}
function my_help_respuesta_a_retos(){
    return help_popup_window(15822, 'help',my_get_help_link_object());
}
function my_get_rows_gestion_canales($vars_in){
    $vars=$vars_in;
    $sep='<td class="views-field views-field-name-1">';
    $my_array=explode($sep,$vars['rows']);
    if(count($my_array)>0){
           //echo print_r($vars,1);exit();
           $kont=0;
           foreach($my_array as $i=>$v){
               if($i>0){
                   $pos=strpos($v, '</td>');
                   $s=substr($v,0,$pos);
                   $beste=substr($v,$pos);
                   if(isset($vars['view']->result[$kont])){
                        $nid=$vars['view']->result[$kont]->nid;
                        $canal_node=node_load($nid);
                        if(isset($canal_node->nid) && !empty($canal_node->nid)){
                            $uid='';
                            if(isset($canal_node->field_responsable_uid)){
                                $uid=$canal_node->field_responsable_uid[0]['uid'];
                            }
                            $s='';
                            if(!empty($uid)){
                                $my_user=user_load($uid);
                                if(isset($my_user->uid) && !empty($my_user->uid)){
                                    $s=$my_user->name;
                                }
                            }
                        }

                   }
                   $s.=$beste;
                   $my_array[$i]=$s;
                   $kont++;
               }
           }
        
    }
    //
    $sep='<td class="views-field views-field-name">';
    $vars['rows']=implode($sep,$my_array);
    //
    return $vars['rows'];
}
function set_enlace_fields(&$form,$node_type){
    global $base_url;
    if(is_node_add($node_type)){
        $nid=arg(3);
        if(!empty($nid) && is_numeric($nid)){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $form['field_enlace_'.$node_type][0]['#default_value']['title']=$node->title;
                //$form['field_enlace_'.$node_type][0]['#default_value']['url']=$base_url.'/node/'.$nid;
                $form['field_enlace_'.$node_type][0]['#default_value']['url']=$_SERVER['HTTP_HOST'].base_path().'node/'.$nid;
            }
        }
    }
}
function get_camino_title(){
    if(is_estrategia() || is_despliegue() || is_decision() || is_informacion()){
        return t('Strategic Deployment');
    }
    return t('Proposals');
}
function get_tipo_informacion_options($with_empty=1){
    return hontza_define_tipo_informacion_options($with_empty);
    /*$where=array();
    $where[]='1';
    //
    $result=array();
    $result['']='';
    $sql='SELECT nt.* FROM {node_type} nt WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    $rows=array();
    $kont=0;
    while($row=db_fetch_object($res)){
        if(!in_array($row->type,hontza_define_not_tipo_informacion_options())){
            //gemini-2013
            $rows[$kont]=new stdClass();        
            //
            $rows[$kont]->type=$row->type;
            $rows[$kont]->label=my_get_label_type2($row->type);
            $kont++;
        }
    }
    $is_numeric=0;
    $rows=array_ordenatu($rows, 'label', 'asc', $is_numeric);
    //
    if(count($rows)>0){
        foreach($rows as $i=>$r){
            $result[$r->type]=$r->label;
        }
    }    
    //
    return $result;*/
}
function on_node_insert(&$node){
    if($node->type=='item'){
        if(isset($node->nid) && !empty($node->nid)){
            //
        }else{
            //$node->created=mktime(0,0,0,11,3,1999);
            //print $node->nid;exit();
            $node->created=time();
            //node_save($node);
        }
    }
}
function my_get_label_type2($type){
    /*$s=my_get_label_type($type,1);
    //
    if(!empty($s)){
        return t(ucfirst($s));
    }*/
    //
    $my_array=array();
    //    
    $my_array['estrategia']='reto';
    $my_array['despliegue']='subreto';
    $my_array['informacion']='Key Question';
    $my_array['item']='noticia';
    $my_array['noticia']='Noticia de Usuario';
    $my_array['noticias_portada']='Noticia de Portada';
    $my_array['rss_feed']='Noticia Destacada';
    $my_array['servicio']='Expert';
    //
    if(isset($my_array[$type])){
        //return t(ucfirst($my_array[$type]));
        $s=ucfirst($my_array[$type]);
        $s=get_english_information_type_value($s);
        return t($s);
    }
    $s=ucfirst($type);
    $s=get_english_information_type_value($s);
    return t($s);
}
function simular_count_query($sql){
    $result=array();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    //
    print 'num='.count($result).'<BR>';
}
function simular_result_query($sql){
    $result=array();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    //
    echo print_r($result,1);
    exit();
}
function unset_invitado_user_menu_links($result_in,$my_array_in=array()){
  global $user;
  $result=$result_in;
  //
   $my_array=array();
   if(empty($my_array_in)){
        $my_array['dashboard']='My dashboard';
        $my_array['messages']='Messages';
        $my_array['notifications']='Notifications';
   }else{
       $my_array=$my_array_in;
   }
   //
   $beste_array=array('','http://'.$_SERVER['HTTP_HOST']);
   //
   if(count($my_array)>0){
       foreach($my_array as $key=>$label){
           foreach($beste_array as $k=>$raiz){
               //print $raiz.'<BR>';
               $s='<li ><a href="'.$raiz.base_path().'user/'.$user->uid.'/'.$key.'">'.t($label).'</a></li>';
               $result=str_replace($s,'',$result);
               $s='<li class="active" ><a href="'.$raiz.base_path().'user/'.$user->uid.'/'.$key.'" class="active">'.t($label).'</a></li>';
               $result=str_replace($s,'',$result);
           }
       }
   }
   return $result;

}
function my_copiar_ayuda_callback(){
    //
    return "funcion sin activar";
    //
    set_time_limit(3600);
    $result=array();
    $node_faq_list=get_node_faq_list();
    //echo print_r($node_faq_list,1);exit();
    //echo count($node_faq_list);exit();
    if(count($node_faq_list)>0){
        foreach($node_faq_list as $i=>$node){
            //echo print_r($node,1);exit();
            $result[]=$node->title;
            $my_w_list=$node->my_w_list;
            unset($node->my_w_list);
            node_save($node);
            if(count($my_w_list)>0){
                foreach($my_w_list as $k=>$w_in){
                    $w=$w_in;
                    $w->nid=$node->nid;
                    my_insert_faq_weights($w);
                }
            }
        }
    }
    $result[]="OK";
    return implode("<BR>",$result);
}
function get_node_faq_list(){
    db_set_active('my_install');
    //
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="n.type='faq'";
    $sql="SELECT n.* FROM {node} n WHERE ".implode(" AND ",$where)." ORDER BY n.created ASC";
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        //print $row->nid."<BR>";
        $node=node_load($row->nid);
        $node->my_w_list=get_my_w_list($row->nid);
        unset($node->nid);
        unset($node->vid);        
        $result[]=$node;
    }
    //
    db_set_active('default');
    return $result;
}
function get_my_w_list($nid){
    $result=array();
    $sql="SELECT fw.* FROM {faq_weights} fw WHERE nid=".$nid;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function my_insert_faq_weights($w){
    $sql=sprintf("INSERT INTO faq_weights(tid,nid,weight) VALUES(%d,%d,%d)",$w->tid,$w->nid,$w->weight);
    db_query($sql);
}
function repase_access($is_return=0){
    global $user;
    $result=1;
    red_funciones_repase_access_grupo_shared($is_return);
    $param=arg(0);
    //if(!empty($param) && in_array($param,array('dashboard','canales'))){
    if(!empty($param) && in_array($param,array('dashboard'))){
        /*$my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            //
        }else{
            drupal_goto('grupo_sin_seleccionar');
        }*/
        goto_grupo_sin_seleccionar();
    }else{
        if(is_area_debate()){
            hontza_repase_access_debate();     
        }else if(is_area_trabajo()){
            hontza_repase_access_wiki();
        //intelsat-2016    
        //}else if(is_fuentes()){
        }else if(is_fuentes_pipes_todas()){    
            red_repase_access_fuentes();
        }
        //intelsat-2015
        hontza_solr_search_lector_repase_access();
        //
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
           if($node->type=='estrategia'){
               $result=repase_access_estrategia($node,$is_return);
           }else if($node->type=='despliegue'){
               $result=repase_access_subreto($node,$is_return);
           }else if($node->type=='decision'){
               $result=repase_access_decision($node,$is_return);
           }else if($node->type=='informacion'){
               $result=repase_access_informacion($node,$is_return);
           }else if($node->type=='idea'){
               $result=repase_access_idea($node,$is_return);
           }else if($node->type=='oportunidad'){
               $result=repase_access_oportunidad($node,$is_return);
           }else if($node->type=='proyecto'){
               $result=repase_access_proyecto($node,$is_return);
           }else if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
               //gemini-2013
               $result=repase_access_canal($node,$is_return);
           //intelsat-2015    
           }else if(panel_admin_is_gestion('grupos')){
               $result=panel_admin_repase_access_gestion_grupos();
           }
           //
        }else{
           if(is_user_uid_dashboard()){
                drupal_access_denied();
                exit();
           }
        }       
    }

    //
    if(isset($user->uid) && !empty($user->uid)){
        //no hacemos nada
    }else{
        $result=0;
    }
    
    //intelsat-2016
    if($result){    
        if(!hontza_grupos_mi_grupo_in_grupo()){
            if(!$is_return){
                drupal_access_denied();
                exit();
            }
        }
    }    
    //
    
    return $result;
}
function get_og_ancestry_list($nid){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='og_ancestry.nid='.$nid;
    //
    $sql='SELECT * FROM {og_ancestry} og_ancestry WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->group_nid;
    }
    return $result;
}
function repase_access_estrategia($node,$is_return=0){
    //print $node->grupo_nid.'<BR>';exit();
    //print $node->grupo_seguimiento_nid.'<BR>';
    global $user;   
    $is_access=0;
    $my_user=user_load($user->uid);
    $gr_keys=array_keys($my_user->og_groups);
    if(in_array($node->grupo_nid,$gr_keys)){
        $is_access=1;
    }
    if(isset($node->grupo_seguimiento_nid) && !empty($node->grupo_seguimiento_nid)){
        if(in_array($node->grupo_seguimiento_nid,$gr_keys)){
            $is_access=1;
        }
    }
    //
    if(!$is_access){
        if(!$is_return){
            drupal_access_denied();
            //drupal_goto('my_access_denied');
            exit();
        }
    }
    return $is_access;
}
function repase_access_subreto($node,$is_return=0){
    return repase_access_estrategia($node,$is_return);
}
function repase_access_decision($node,$is_return=0){
    return repase_access_estrategia($node,$is_return);
}
function repase_access_informacion($node,$is_return=0){
    return repase_access_estrategia($node,$is_return);
}
function repase_access_idea($node,$is_return=0){
    //echo print_r($node,1);
    return repase_access_estrategia($node,$is_return);
}
function repase_access_oportunidad($node,$is_return=0){
    return repase_access_estrategia($node,$is_return);
}
function repase_access_proyecto($node,$is_return=0){
    return repase_access_estrategia($node,$is_return);
}
function get_grupo_sin_seleccionar_nid(){
    //AVISO::::para que cuando el grupo esté sin seleccionar devuelva una lista vacía
    return 0;
}
function my_access_denied_callback(){
    drupal_access_denied();
    //drupal_set_title('Access denied');
    //return t('You are not authorized to access this page').'.';
}
function es_borrar_campo_revision($form_id){
    if(in_array($form_id,array('wiki_node_form','debate_node_form'))){
        return 0;
    }
    return 1;
}
function repase_revision_information(&$form,&$form_state,$form_id){
    if(es_borrar_campo_revision($form_id)){
        unset($form['revision_information']);
    }else{
        $form['revision_information']['revision']['#access']=1;
    }
}
function my_grupo_sin_seleccionar_action(){    
    drupal_set_title(t('Access denied'));
    return t('You must select a group').'.';
}
function grupo_sin_seleccionar_callback(){
    return my_grupo_sin_seleccionar_action();
}
function exportar_claves_traducciones_callback(){
$node = node_load(24732);
//print_r($node->files);
$result=array();

foreach($node->files as $file){
  $src = file_get_contents($file->filepath);
  preg_match_all('/msgid "(.*)"/', $src, $zaku);
  $result=array_merge($result,$zaku[1]);

  //print_r($zaku[1]);
}
$sql="DELETE FROM traducciones_ingles WHERE 1";
db_query($sql);
$result=prepare_exportar_claves_traducciones($result);
save_traducciones_ingles_db($result);
sort($result);
$result=array_unique($result);
//print_r($result);
print implode("<BR>",$result);
//
$filename="/usr/home/hontza/www/hontza3/sites/all/modules/hontza/my_csv.csv";
$f=fopen($filename,"w");
if(count($result)>0){
     foreach($result as $i=>$value){
         $v=$value;
         $v=utf8_decode($value);
         $fields=array($v);
         fputcsv($f, $fields);        
     }
}
fclose($f);
}
function prepare_exportar_claves_traducciones($my_array){
    $result=array();
    if(count($my_array)>0){
        foreach($my_array as $i=>$value){
            $v=$value;
            //$v=html_entity_decode($v);
            /*$b=addslashes($v);
            $sql="INSERT INTO traducciones_ingles(clave) VALUES('".$b."')";
            db_query($sql);*/
            //$v=strip_tags($v);
            //$v=utf8_decode($v);
            if(!empty($v)){
                /*$b=strip_tags($v);
                if(strcmp($v,$b)==0){
                    $result[]=$v;
                }else{
                    print 'vvvv='.$v.'<BR>';
                }*/
                $result[]=$v;
            }
        }
    }
    return $result;
}
function traducciones_ingles_callback(){
  $headers=array();
  $headers[]=array('data'=>t('Key'),'field'=>'clave');
  $headers[]=array('data'=>t('Value'),'field'=>'valor');
  $headers[]='';
  //
  $where=set_where_traducciones_ingles();
  //
  $sql="SELECT * FROM traducciones_ingles WHERE ".join(" AND ",$where)." ORDER BY clave ASC";
  //print $sql;
  $my_limit=variable_get('default_nodes_main', 20);
  //$result = pager_query(db_rewrite_sql($sql), $my_limit);
  $result = db_query($sql);

  $output = '';
  $num_rows = FALSE;
  $rows=array();
  $kont=0;
  //$node_types=array();
  while ($row = db_fetch_object($result)) {
    $rows[$kont]=array();
    $rows[$kont][0]=$row->clave;
    $rows[$kont][1]=$row->valor;
    $rows[$kont][2]=l(t('Edit'),'traducciones_ingles/'.$row->id.'/edit');
    //
    $num_rows = TRUE;
    $kont++;
  }
  //echo print_r($node_types,1);
//echo print_r($rows,1);exit();

  my_add_buscar_js();
  $output .= drupal_get_form('traducciones_ingles_search_form');

  if ($num_rows) {
    /*$feed_url = url('oportunidad_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$headers=array(t('fid'),t('User'),t('Name'),t('Ruta'),t('Date'),t('nid'),t('cid'),t('c_nid'),t('Type'));
    //$headers=array_slice($headers,1);
    //

    $rows=my_set_estrategia_pager($rows,$my_limit);

    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output.= '<div id="first-time">' .t('English translation list is empty'). '</div>';
  }
  drupal_set_title(t('English translation list'));

  return $output;
}
function traducciones_ingles_search_form(){
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>'Search','#attributes'=>array('id'=>'file_buscar_fs'));
    //$form['file_buscar_fs']['fid']=array('#type'=>'textfield','#title'=>t('fid'),'#size'=>10,"#maxlength"=>10,"#default_value"=>get_file_filter_value('fid'));
    $form['file_buscar_fs']['clave']=array('#type'=>'textfield','#title'=>t('Key'),"#default_value"=>get_traducciones_ingles_filter_value('clave'));
    $form['file_buscar_fs']['valor']=array('#type'=>'textfield','#title'=>t('Value'),"#default_value"=>get_traducciones_ingles_filter_value('valor'));
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar_traducciones_ingles');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Reset'),'#name'=>'limpiar_traducciones_ingles');
    return $form;
}
function get_traducciones_ingles_filter_value($f){
    if(isset($_SESSION['traducciones_ingles']['filter']) && !empty($_SESSION['traducciones_ingles']['filter'])){
        if(isset($_SESSION['traducciones_ingles']['filter'][$f]) && !empty($_SESSION['traducciones_ingles']['filter'][$f])){
            return $_SESSION['traducciones_ingles']['filter'][$f];
        }
    }
    return '';
}
function traducciones_ingles_search_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $name=$form['clicked_button']['#name'];
        if(strcmp($name,'limpiar_traducciones_ingles')==0){
            if(isset($_SESSION['traducciones_ingles']['filter']) && !empty($_SESSION['traducciones_ingles']['filter'])){
                unset($_SESSION['traducciones_ingles']['filter']);
            }
        }else{
            $_SESSION['traducciones_ingles']['filter']=array();
            $fields=array('clave','valor');
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form['values'][$f];
                    if(!empty($v)){
                        $_SESSION['traducciones_ingles']['filter'][$f]=$v;
                    }
                }
            }
        }
    }
}
function set_where_traducciones_ingles(){
    $where=array();
    $where[]="1";
    //
    if(isset($_SESSION['traducciones_ingles']['filter']) && !empty($_SESSION['traducciones_ingles']['filter'])){
        $filter=$_SESSION['traducciones_ingles']['filter'];
        //echo print_r($filter,1);
        foreach($filter as $field=>$value){
            //print $field.'===='.$value.'<BR>';
            if(!empty($value)){
                switch($field){
                    case 'clave':
                    case 'valor':
                        $where[]=$field." LIKE '%".$value."%'";
                        break;
                    default:
                        break;
                }
            }
        }
    }
    //
    return $where;
}
function save_traducciones_ingles_db($result){
    if(count($result)>0){
        foreach($result as $i=>$value){
            $v=$value;
            //$v=utf8_decode($v);
            db_query('INSERT INTO traducciones_ingles(clave) VALUES("%s")', $v);
        }
    }
}
function traducciones_ingles_edit_form(){
    $form=array();
    $id=arg(1);
    $row=get_traducciones_ingles_row($id);
    $form['my_id']=array('#type'=>'hidden',"#value"=>$row->id);
    $form['clave']=array('#type'=>'textfield','#title'=>t('Key'),"#default_value"=>$row->clave);
    $form['valor']=array('#type'=>'textfield','#title'=>t('Value'),"#default_value"=>$row->valor);
    //
    $form['actions']['submit']=array('#type'=>'submit','#value'=>t('Save'),'#name'=>'traducciones_ingles_submit_btn');
    $form['actions']['cancel'] = array(
		//'#type' => 'markup',
		'#value' => l(t('Cancel'), 'traducciones_ingles'),
	  );
    return $form;
}
function get_traducciones_ingles_row($id){
    $result=array();
    if(!empty($id)){
        $where=array();
        $where[]="1";
        $where[]="id=".$id;        
        //
        $sql="SELECT ti.* FROM {traducciones_ingles} ti WHERE ".implode(" AND ",$where);
        //print $sql.'<BR>';
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            return $row;
        }
    }
    $my_result=(object) $result;
    return $my_result;
}
function traducciones_ingles_edit_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        //$name=$form['clicked_button']['#name'];
        $clave=$form['values']['clave'];
        $valor=$form['values']['valor'];
        $id=$form['values']['my_id'];
        //$valor=addslashes($valor);
        db_query('UPDATE traducciones_ingles SET valor="%s" WHERE id=%d',$valor,$id);
    }
    drupal_goto('traducciones_ingles');
}
function my_fuentehtml_node_form_alter(&$form,&$form_state,$form_id){
    drupal_set_title(t('(Beta) Add Source - HTML'));
    if(!is_super_admin()){
        drupal_access_denied();
    }
    $form['title']['#title']=t('Title');
    $prefix='<div style="display:none;">';
    $fields=array("body_field","revision_information","menu","comment_settings","path","author","options");
    foreach($fields as $i=>$f){
        $form[$f]['#prefix'] = $prefix;
        $form[$f]['#suffix'] = "</div>";
    }
    //
    $form['field_fuentehtml_calidad'][0]['rating']['#title']=t('Information Quality');
    $form['field_fuentehtml_exhaustividad'][0]['rating']['#title']=t('Coverage');
    $form['field_fuentehtml_actualizacion'][0]['rating']['#title']=t('Update');
    //
    /*$form['buttons']['cancel'] = array(
      '#weight' =>999,
      '#value' => '<input type="button" id="my_fuentehtml_cancel" name="my_fuentehtml_cancel" value="'.t('Cancel').'"/>',
    );*/
    //
    delete_field_form_by_type($form,'fuentehtml');
    //
    $form['buttons']['cancel'] = array(
    '#type' => 'submit',
    '#value' => t('Cancel'),
    '#weight' =>999,
    //'#validate' => array(), // This will cause this button to skip all validation. Put a function name here for validation with that function.
    '#submit' => array('my_fuentehtml_cancel_submit'),
    );
    $form['#after_build'][] = 'hontza_fuentehtml_form_after_build';
    //
    add_js_fuentehtml_form();
    //echo print_r($form,1);
}
function add_js_fuentehtml_form(){
    global $base_path,$base_url;
    //
    $my_grupo=og_get_group_context();
    //
    $purl='';
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl.'/';
    }
    $destination=drupal_get_destination();
    $destination=$purl.str_replace('destination=', '', $destination);
    //$destination=$base_root.$base_path.$destination;
    //$destination=urldecode($destination);
    $destination=url($destination);
    //
		$js='var destination_fuentehtml_cancel="'.$destination.'";
			$(document).ready(function()
			{
                            $("#edit-notifications-content-disable-wrapper").parent().parent().parent().css("display","none");
                            //$("#my_fuentehtml_cancel").click(function()
                            //{
                                //location.href=destination_fuentehtml_cancel;
                            //});
                        });';

			drupal_add_js($js,'inline');
	
}
function importar_pagina_html_form(){
    drupal_set_title(t('(Beta) Import HTML page'));
    //intelsat-2015
    //if(!is_super_admin()){
        drupal_access_denied();
        exit();
    //}
    $form=array();   
    $form['nombre_canal']=array('#type'=>'textfield','#title'=>t('Channel Name'),"#required"=>true);
    $form['my_url']=array('#type'=>'textfield','#title'=>t('Url'),"#required"=>true);
    $form['my_titulo_word_min']=array('#type'=>'textfield','#title'=>t('Minimum words in the title'),"#required"=>false);
    //
    //$form['apply_open_calais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
    //$form['apply_alchemy']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply Alchemy').'</b>');
    //
    $form['actions']['submit']=array('#type'=>'submit','#value'=>t('Save'),'#name'=>'importar_pagina_html_form_submit');
    $form['actions']['cancel'] = array(
		//'#type' => 'markup',
		'#value' => l(t('Cancel'), 'vigilancia/pendientes'),
	  );
    return $form;
}
function importar_pagina_html_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $nombre_canal=$form['values']['nombre_canal'];
        $my_url=$form['values']['my_url'];
        $rssgen=get_rss_gen($my_url);
        $titulo_word_min=0;
        if(isset($form['values']['my_titulo_word_min']) && !empty($form['values']['my_titulo_word_min'])){
            $titulo_word_min=$form['values']['my_titulo_word_min'];
        }
        //
        $apply_open_calais=0;
        if(isset($form['values']['apply_open_calais']) && !empty($form['values']['apply_open_calais'])){
            $apply_open_calais=1;
        }
        $apply_alchemy=0;
        if(isset($form['values']['apply_alchemy']) && !empty($form['values']['apply_alchemy'])){
            $apply_alchemy=1;
        }
        //
        $res=db_query("INSERT INTO import_html_source(nombre_canal,my_url,rssgen,titulo_word_min,apply_open_calais,apply_alchemy) VALUES('%s','%s','%s',%d,%d,%d)",$nombre_canal,$my_url,$rssgen,$titulo_word_min,$apply_open_calais,$apply_alchemy);
        //$res=db_query("INSERT INTO import_html_source(nombre_canal,my_url,rssgen) VALUES('%s','%s','%s')",$nombre_canal,$my_url,$rssgen);

        $id=db_last_insert_id("import_html_source", "id");
        drupal_goto('crear/canal-yql','simple=1&my_id='.$id);
    }    
}
function in_user_demo_excepcion_list($user_id=''){
    global $user;
    //
    if(empty($user_id)){
        $my_user=$user;
    }else{
        $my_user=user_load($user_id);
    }
    //
    $my_array=array('aroldan');
    //$my_array[]='aitordemo';
    //echo print_r($user,1);
    if(in_array($my_user->name,$my_array)){
        return 1;
    }
    return 0;
}
function traducir_castellano_callback(){
    /*$kont = 0;
    $filename="/usr/home/hontza/www/hontza3/sites/all/modules/hontza/Hontza-Translation-es-en.ods";
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 9000, ";")) !== FALSE) {
            if($kont>0){
                save_traducir_castellano($data);
            }
            $kont++;
        }
       fclose($handle);       
    }*/
    traducir_castellano2();
    return 'OK';
}
function save_traducir_castellano($data){
    $source=$data[2];
    $translation=$data[1];
    //
    if(!empty($data[0]) && !empty($source) && !empty($translation)){
        $location='';
        
        $textgroup='default';
        db_query("REPLACE INTO {locales_source} (location, source, textgroup) VALUES ('%s', '%s', '%s')", $location, $source, $textgroup);
        $lid = db_result(db_query("SELECT lid FROM {locales_source} WHERE source = '%s' AND textgroup = '%s'", $source, $textgroup));
        $langcode='es';
        
        $translation=utf8_encode($translation);
        db_query("REPLACE INTO {locales_target} (lid, language, translation) VALUES (%d, '%s', '%s')", $lid, $langcode, $translation);
        
    }/*else{
       
        echo print_r($data,1);
    }*/
}
function menu_by_lang($result_in){
    global $language;
    $result=array();
    if(!empty($result_in)){
        /*
        foreach($result_in as $menu_id=>$row){
            
            if($language->language==$row['langcode']){
                //print 'menu_id===='.$menu_id.'<BR>';
                $result[$menu_id]=$row;
            }
        }
        if(empty($result)){
             return $result_in;
        }*/
        $result=hontza_repase_menu_by_lang($result_in,$language->language);
        if(empty($result)){
            $result=hontza_repase_menu_by_lang($result_in,'en');
        }
    }
    $values_in=array_values($result_in);
    $values=array_values($result);
    
    //if(!empty($result) && count($values_in)==count($values)){
    if(!empty($result)){
        return $result;
    }else{
        $result=hontza_repase_menu_by_lang($result_in,'en');
    }
    return $result;    
}
function my_save_faq(&$node){
    if($node->type=='faq'){
        if(!empty($node->language) && $node->language!='es'){
           if(isset($node->translation_source)){
               $tnid=$node->translation_source->nid;
               $wa=my_get_faq_weights($tnid);
               if(isset($wa->nid) && !empty($wa->nid)){
                   db_query('INSERT INTO faq_weights(nid,weight) VALUES(%d,%d)',$node->nid,$wa->weight);
               }
           }
        }
    }
}
function my_get_faq_weights($tnid){
    if(!empty($tnid)){
        $sql='SELECT * FROM faq_weights WHERE nid='.$tnid;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            return $row;
        }
    }
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function borrar_url_alias_callback(){
    drupal_set_title('delete url alias');
    $where=array();
    $where[]='n.type IN ("faq","my_help")';
    $sql='SELECT * FROM node n WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        //print $row->type.'<BR>';
        my_delete_url_alias($row->nid);
    }
    return 'OK';
}
function my_delete_url_alias($nid){
    $sql='DELETE FROM url_alias WHERE src="node/'.$nid.'"';
    //print $sql.'<BR>';
    db_query($sql);
}
function my_translate_profile_category($s){
    $konp='';
    if(strcmp($s,'Datos personales')==0){
        $konp=t('Personal data');
    }else if(strcmp($s,'Empresa')==0){
        $konp=t('Organisation');
    }else if(strcmp($s,'Servicios')==0){
        $konp=t('Services');
    }
    if(!empty($konp)){
        if(strcmp($konp,$s)==0){
            return $s;
        }else{
            return $konp;
        }
    }
    return $s;
}
function my_translate_profile_item($s,$attributes=''){
    $konp='';
    if(strcmp($s,'Empresa')==0){
        $konp=t('Organisation');
    }else if(strcmp($s,'Nombre')==0){
        $konp=t('Name');
    }else if(strcmp($s,'Apellidos')==0){
        $konp=t('Surname');
    }else if(strcmp($s,'Código fiscal de empresa')==0){
        $konp=t('Company identifier');
    }else if(strcmp($s,'Cargo')==0){
        $konp=t('Position');
    }else if(strcmp($s,'Ciudad')==0){
        $konp=t('Town');
    }else if(strcmp($s,'País')==0){
        $konp=t('Country');
    }else if(strcmp($s,'Sitio web')==0){
        $konp=t('Website');
    }else if(strcmp($s,'Email corporativo')==0){
        $konp=t('Corporate email');
    }else if(strcmp($s,'Adjuntar documento con los servicios que ofrece tu empresa')==0){
        $konp=t('Attach document with your company services');
    }else if(strcmp($s,'Tu empresa tiene relación comercial con otros proveedores de soluciones VTIC?')==0){
        $konp=t('Do your company have commercial relations with other CI software providers?');
    //intelsat-2015        
    }else if(strcmp($s,'Perfil en Facebook')==0){
        $konp=t('Facebook URL');
    }else if(strcmp($s,'Perfil en twitter')==0){
        $konp=t('Twitter URL');
    }else if(strcmp($s,'Perfil en Linkedln')==0){
        $konp=t('Linkedln URL');
    }else if(strcmp($s,'Servicios de consultoría que ofreces')==0){ 
        $konp=t('Your Consultancy services');
    }else if(strcmp($s,'Detallar')==0){ 
        $konp=t('Details');
    }else{
        if(empty($s)){
            $konp=hontza_canal_rss_get_profile_item_title($attributes);
        }
    }
    //
    if(!empty($konp)){
        if(strcmp($konp,$s)==0){
            return $s;
        }else{
            return $konp;
        }
    }
    return $s;
}
function my_translate_taxonomy_in_form(&$form){
    if(isset($form['taxonomy'])){
        
        foreach($form['taxonomy'] as $vid=>$row){
            if(is_array($form['taxonomy'][$vid])){
                $form['taxonomy'][$vid]['#title']=my_translate_vocabulary_title($form['taxonomy'][$vid]['#title']);
            }
        }
    }
}
function my_translate_vocabulary_title($s){
    $konp='';
    if(strcmp($s,'Tipo de grupo')==0){
        $konp=t('Type of group');
    }
    //
    if(!empty($konp)){
        if(strcmp($konp,$s)==0){
            return $s;
        }else{
            return $konp;
        }
    }
    return $s;
}
function my_servicio_node_form_form_alter(&$form,&$form_state,$form_id){
    //intelsat-2015
    //$form['title']['#title']=t('Name');
    $form['title']['#title']=t('Organisation');
    //
    //$form['field_sitio_web']['#title']=t('Website');
    $form['field_sitio_web'][0]['#title']=t('Website');
    $form['field_persona_de_contacto'][0]['#title']=t('Contact person');
    $form['field_telefono'][0]['#title']=t('Phone');
    if(is_node_add()){
        drupal_set_title(t('Create Facilitator'));
        //gemini-2014
        red_facilitador_set_default_language($form,$form_state,$form_id);
    }
    //intelsat-2015
    $form['field_logo_servicios'][0]['#title']=t('Logo');
}
function my_noticias_portada_node_form_alter(&$form,&$form_state,$form_id){
   //intelsat-2015
   $nid=hontza_get_nid_by_form($form);
   if(empty($nid)){
       drupal_set_title(t('Add Public News'));
   }
   // 
   $form['field_noticias_portada_texto'][0]['#title']=t('Text');
   $form['field_noticia_portada_link'][0]['#title']=t('Link to original');
}
function my_rss_feed_node_form_alter(&$form,&$form_state,$form_id){
   $form['field_rss_link'][0]['#title']=t('Link to original');
}
function reset_url_alias_language(){
    $sql='UPDATE url_alias SET language="" WHERE 1';
    db_query($sql);
}
function traducir_castellano2(){
    $kont = 0;
    $i=0;
    $filename="/usr/home/hontza/www/hontza3/sites/all/modules/hontza/my_csv2.csv";
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 9000, ";")) !== FALSE) {
            if($kont>0){
                if(!existe_palabra_traducir($data)){
                    //print $data[2].'<BR>';
                    save_traducir_castellano($data);
                    $i++;
                }else{
                    //print $data[2].'<BR>';
                }
            }
            $kont++;
        }
       fclose($handle);
    }
    print 'todos='.$kont.'<BR>';
    print 'no existen='.$i.'<BR>';
}
function existe_palabra_traducir($data){
    $res=db_query('SELECT * FROM locales_source WHERE source="%s"',$data[2]);
    while($row=db_fetch_object($res)){
        return 1;
    }
    return 0;
}
function my_user_login_form_alter(&$form,&$form_state,$form_id){
    my_change_language();
    //echo print_r($form,1);exit();
    $form['name']['#title'] = t('Username');
    $form['name']['#description'] = my_t('Enter your username');
    $form['pass']['#title'] = t('Password');
    $form['pass']['#description'] = my_t('Enter the password that accompanies your username');
    $form['submit']['#value'] = my_t('Log in');
}
function get_language_selection_li($is_visualizador=0){
    return get_cambiar_idioma_modo_select($is_visualizador);
    global $base_url;
    $result=array();
    $languages = language_list();
    //
    if(!empty($languages)){
        //echo print_r($languages,1);exit();
        foreach($languages as $code=>$lang){
            $result[]='<li>'.l($lang->native,$base_url.'/cambiar_idioma',array('query'=>'my_idioma='.$lang->language.'&'.drupal_get_destination())).'</li>';
        }
    }
    //echo print_r($languages,1);    
    return implode('',$result);
}
function cambiar_idioma_callback($is_login=0){
    global $language;
    global $user;
    
    //445
    //print 'uid===='.$user->uid.'<BR>';exit();

    /*$my_grupo=og_get_group_context();
  //
  if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
  	$grupo_nid=$my_grupo->nid;
        print $grupo_nid;exit();

  }*/


    $languages=language_list();
    $code='en';
    if(isset($_REQUEST['my_idioma']) && !empty($_REQUEST['my_idioma'])){
        $code=$_REQUEST['my_idioma'];        
    }
    //    
    if(isset($languages[$code]) && !empty($languages[$code])){
        $language=$languages[$code];
        /*$my_user=array();
        $my_user['uid']=$user->uid;
        $my_user['language']=$language->language;
        $my_user=(object) $my_user;
        user_save($my_user);*/
        //$_SESSION['cambiar_idioma_lang']=$code;
    }
    //
    if($is_login){        
        drupal_goto('user');
    }else{
        $destination=$_REQUEST['destination'];
        //header('Location: '.$destination);
        /*if($user->uid==445){
            print $destination;
        }*/
        drupal_goto($destination);
    }
    return '';
}
function get_cambiar_idioma_modo_select($is_visualizador){
    global $base_url;
    global $language;
    //gemini-2014
    global $user;
    $result=array();
    //
    $my_grupo=og_get_group_context();
    $purl='';
    //
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $purl=$my_grupo->purl;
    }
    //
    //$result[]='<select id="select_cambiar_idioma" name="select_cambiar_idioma" style="float:left;margin-right:10px;">';
    $result[]='<select id="select_cambiar_idioma" name="select_cambiar_idioma" style="float:left;padding-left:0px;">';
    $languages = language_list();
    //gemini-2014
    $languages=red_funciones_get_languages_ordenados($languages);
        /*
        //gemini-2014
        if(hontza_is_sareko_id('ROOT') && $user->uid==22){
            //echo print_r($language,1);
            //echo print_r($_REQUEST,1);
            print $_SESSION['cambiar_idioma_lang'];exit();
            exit();
        }*/
    /*if(isset($_SESSION['cambiar_idioma_lang']) && !empty($_SESSION['cambiar_idioma_lang'])){
        if($language->language!=$_SESSION['cambiar_idioma_lang']){
            if(isset($languages[$_SESSION['cambiar_idioma_lang']]) && !empty($languages[$_SESSION['cambiar_idioma_lang']])){
                $language=$languages[$_SESSION['cambiar_idioma_lang']];
            }
        }
        //$_SESSION['cambiar_idioma_lang']='';
    }*/            
    //
    if(!empty($languages)){
        //echo print_r($languages,1);exit();
        foreach($languages as $code=>$lang){
            if(!red_funciones_is_show_language_in_options($code)){
                continue;
            }
            $selected='';
            //
            //print $language->language.'===='.$code.'<BR>';
            if($language->language==$code){
                $selected=' selected="selected"';
            }
            //
            $label=$lang->native;
            //if(hontza_is_sareko_id_red()){
                $label=strtoupper($lang->language);
                if($label=='PT-PT'){
                   $label='PT'; 
                }
            //}
            $result[]='<option value="'.$lang->language.'"'.$selected.'>'.$label.'</option>';
        }
    }
    $result[]='</select>';
    if(!empty($purl)){
        $purl=$purl.'/';
    }
    //
    $js='
        var my_cambiar_idioma_base_url="'.$base_url.'";
        var my_cambiar_idioma_destination="'.drupal_get_destination().'";
        var my_cambiar_idioma_purl="'.$purl.'"
        $(document).ready(function()
        {
            $("#select_cambiar_idioma").change(function()
            {
                var my_code=$(this).attr("value");
                var my_goto=my_cambiar_idioma_base_url+"/"+my_cambiar_idioma_purl+"cambiar_idioma?my_idioma="+my_code+"&"+my_cambiar_idioma_destination;
                location.href=my_goto;
                //alert(my_goto);
            });
	});';

    drupal_add_js($js,'inline');
    //
    return implode('',$result).'&nbsp;';
}
function my_t($param){
    global $language;
    $result=$param;
    if($language->language!='en'){
        $sql='SELECT t.*
        FROM locales_source s LEFT JOIN locales_target t ON s.lid=t.lid
        WHERE s.source="'.$param.'" AND t.language="'.$language->language.'"';
        //print $sql;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            return $row->translation;
        }
    }
    return $result;
}
function translate_user_register_menu($param,$ind=0){    
   global $base_url;
   //gemini-2014-registrado
   //$my_server_url='http://www.hontza.es/hontza3';
   //$my_server_url_array=array('http://www.hontza.es/hontza3','http://hontza.es/hontza3');
   $my_server_url_array=array('http://online.hontza.es');
   //$is_registrarse=0;
   $is_registrarse=esta_registrado_red_hontza(1);  
   //
   $registrado_key='';
   if(empty($is_registrarse)){
       $root_dir='./sites/default/files/';
       if(is_file($root_dir.'registrado/registrado.txt')){        
            $registrado_key=leer_desde_registrado_txt($root_dir.'registrado/registrado.txt'); 
       }                 
   }
   //
   //if($base_url==$my_server_url ){
   if(in_array($base_url,$my_server_url_array)){
       $is_registrarse=1;
       //$is_registrarse=0;
   }
   //simulando
   //$is_registrarse=1;
   //$is_registrarse=0;
   //
   //print $base_url.'<BR>';
   //return $param;
   //print $param;exit();
   //print 'is_registrarse==='.$is_registrarse.'<BR>';
   //intelsat-2015
   //$create_new_account_title=t('Create new account');
   $create_new_account_title=t('Request new account');
   //
   $result=array();
   if(isset($_REQUEST['my_idioma'])){
       $my_idioma=$_REQUEST['my_idioma'];
       //intelsat-2015
       //se ha comentado esto
       //if(!hontza_is_sareko_id('ROOT')){
        $result[]='<li '.get_user_register_active($ind,1).'>'.l($create_new_account_title, 'user/register', array('query'=>'my_idioma='.$my_idioma,'attributes' => array('title' => t('Create new account')))).'</li>';
       //}
       $result[]='<li '.get_user_register_active($ind,2).'>'.l(my_t('Log in'), 'user', array('query'=>'my_idioma='.$my_idioma,'attributes' =>array('title' => my_t('Log in')))).'</li>';
       $result[]='<li '.get_user_register_active($ind,3).'>'.l(my_t('Request new password'), 'user/password', array('query'=>'my_idioma='.$my_idioma,'attributes' => array('title' => my_t('Request new password')))).'</li>';
       /*
       //gemini-2014-registrado
       //gemini-2013
       if(!$is_registrarse){
        $result[]='<li '.get_user_register_active($ind,4).'>'.l(my_t('Register'), 'http://www.hontza.es/hontza3/registrar_red_hontza/'.$registrado_key, array('absolute'=>TRUE,'query'=>'my_idioma='.$my_idioma,'attributes' => array('target'=>'_blank','title' => my_t('Register')))).'</li>';                     
       } 
       */
   }else{
       //intelsat-2015
       //se ha comentado esto
       //if(!hontza_is_sareko_id('ROOT')){
        $result[]='<li '.get_user_register_active($ind,1).'>'.l($create_new_account_title, 'user/register', array('attributes' => array('title' => t('Create new account')))).'</li>';
       //}
       $result[]='<li '.get_user_register_active($ind,2).'>'.l(my_t('Log in'), 'user', array('attributes' => array('title' => my_t('Log in')))).'</li>';
       $result[]='<li '.get_user_register_active($ind,3).'>'.l(my_t('Request new password'), 'user/password', array('attributes' => array('title' => my_t('Request new password')))).'</li>';
       /*
       //gemini-2014-registrado
       //gemini-2013
       if(!$is_registrarse){
        $result[]='<li '.get_user_register_active($ind,4).'>'.l(my_t('Register'), 'http://www.hontza.es/hontza3/registrar_red_hontza/'.$registrado_key, array('absolute'=>TRUE,'attributes' => array('target'=>'_blank','title' => my_t('Register')))).'</li>';          
       }
        * 
        */        
   }
   //
   return implode('',$result);
}
function get_user_register_active($param,$ind){
   if($param==$ind){
    return 'class="active"';
   }
   return '';
}
function my_user_pass_form_alter(&$form,&$form_state,$form_id){
    my_change_language();
    $form['name']['#title'] = ucfirst(my_t('Username or e-mail address'));
    $form['submit']['#value'] = my_t('E-mail new password');
}
function user_login_by_lang_callback(){
    cambiar_idioma_callback(1);
    /*$code='en';
    if(isset($_REQUEST['my_idioma']) && !empty($_REQUEST['my_idioma'])){
        $code=$_REQUEST['my_idioma'];
        $_SESSION['language']=$code;
        drupal_goto('user');
    }*/
    return '';
}
function my_change_language($code_in=''){
    global $language;
    $code=$code_in;
    if(empty($code)){
        if(isset($_REQUEST['my_idioma']) && !empty($_REQUEST['my_idioma'])){
            $code=$_REQUEST['my_idioma'];
        }
    }
    //
    if(!empty($code)){
        $language_list=language_list();
        if(isset($language_list[$code]) && !empty($language_list[$code])){
            $language=$language_list[$code];
        }
    }
}
function my_get_user_list($rid=0){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($rid)){
        $where[]='ur.rid='.$rid;
    }
    $sql='SELECT * 
    FROM {users} u 
    LEFT JOIN {users_roles} ur ON u.uid=ur.uid
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    //
    return $result;
}
function my_get_user_demo_caducado_list(){
    $result=array();
    $user_list=my_get_user_list();
    if(count($user_list)>0){
        foreach($user_list as $i=>$u){
            if(is_user_demo_caducado($u->uid)){
                $result[]=$u;
            }
        }
    }
    return $result;
}
function is_user_demo_caducado_by_message_mail($message){
    if(isset($message['to']) && !empty($message['to'])){
        $user_list=get_user_list_by_mail($message['to']);
        if(count($user_list)>0){
            foreach($user_list as $i=>$u){
                if(is_user_demo_caducado($u->uid)){
                    if(is_mensaje_despedida_enviado($u->uid)){
                        return 1;
                    }    
                }
            }
        }
    }
    return 0;
}
function importar_fuente_html_form(){
    drupal_set_title(t('(Beta) Import HTML source'));
    //intelsat-2015
    //if(!is_super_admin()){
        drupal_access_denied();
        exit();
    //}
    $form=array();
    $form['nombre_canal']=array('#type'=>'textfield','#title'=>t('Channel Name'),"#required"=>true);
    //
    $form['my_fuente_nid'] = array(
    '#title' => t('Source html'),
    '#type' => 'select',
    '#required' => TRUE,
    '#options' => get_fuentehtml_options(),
    //'#default_value' => 2,
    );
    //
    $form['my_param1']=array('#type'=>'textfield','#title'=>t('Argument'),"#required"=>true);
    $form['my_titulo_word_min']=array('#type'=>'textfield','#title'=>t('Minimum words in the title'),"#required"=>false);
    //
    //$form['apply_open_calais']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply OpenCalais').'</b>');
    //$form['apply_alchemy']=array('#type'=>'checkbox','#title'=>'<b>'.t('Apply Alchemy').'</b>');
    //
    $form['actions']['submit']=array('#type'=>'submit','#value'=>t('Save'),'#name'=>'importar_fuente_html_form_submit');
    $form['actions']['cancel'] = array(
		//'#type' => 'markup',
		'#value' => l(t('Cancel'), 'vigilancia/pendientes'),
	  );
    return $form;
}
function get_fuentehtml_options(){
    $result=array();
    $result[0]='';
    $where=array();
    $where[]='1';
    $where[]='n.type="fuentehtml"';
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }
    //
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_nid=$my_grupo->nid;
    }
    //
    $sql='SELECT n.* FROM {node} n 
    LEFT JOIN {og_ancestry} og_ancestry ON n.nid = og_ancestry.nid 
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[$row->nid]=$row->title;
    }
    //
    return $result;
}
function importar_fuente_html_form_submit($form_id,&$form){
    if(isset($form['clicked_button']) && !empty($form['clicked_button'])){
        $my_url='';
        $nombre_canal=$form['values']['nombre_canal'];
        $my_fuente_nid=$form['values']['my_fuente_nid'];
        $node_fuente=node_load($my_fuente_nid);
        if(isset($node_fuente->nid) && !empty($node_fuente->nid) && isset($node_fuente->field_fuentehtml_fuente[0]) && isset($node_fuente->field_fuentehtml_fuente[0]['value'])){
            $my_url=$node_fuente->field_fuentehtml_fuente[0]['value'];
        }        
        $my_param1=$form['values']['my_param1'];        
        $rssgen=rssgenparam($my_url,$my_param1);
        /*
        //gemini-2013
        if(is_super_admin()){
            print 'rssgen===='.$rssgen;exit();
        }
        //
         * 
         */ 
         
        $apply_open_calais=0;
        if(isset($form['values']['apply_open_calais']) && !empty($form['values']['apply_open_calais'])){
            $apply_open_calais=1;
        }
        $apply_alchemy=0;
        if(isset($form['values']['apply_alchemy']) && !empty($form['values']['apply_alchemy'])){
            $apply_alchemy=1;
        }
        //print $rssgen;exit();
        /*AVISO::::tipo_fuentehtml_id=0 (html sin parametros)
        tipo_fuentehtml_id=1 (html con parametros)
        */
        $tipo_fuentehtml_id=1;
        //
        $titulo_word_min=0;
        if(isset($form['values']['my_titulo_word_min']) && !empty($form['values']['my_titulo_word_min'])){
            $titulo_word_min=$form['values']['my_titulo_word_min'];
        }
        //
        $res=db_query("INSERT INTO import_html_source(nombre_canal,my_url,rssgen,tipo_fuentehtml_id,param1,fuentehtml_nid,titulo_word_min,apply_open_calais,apply_alchemy) VALUES('%s','%s','%s',%d,'%s',%d,%d,%d,%d)",$nombre_canal,$my_url,$rssgen,$tipo_fuentehtml_id,$my_param1,$my_fuente_nid,$titulo_word_min,$apply_open_calais,$apply_alchemy);
        $id=db_last_insert_id("import_html_source", "id");
        drupal_goto('crear/canal-yql','simple=1&my_id='.$id);        
    }
}
function my_canal_de_yql_node_form_alter(&$form,&$form_state,$form_id){
    $value=$form['field_fuentehtml_nid'][0]['#default_value']['value'];
    $form['field_fuentehtml_nid']=array();
    $form['field_fuentehtml_nid']['#type']='hidden';
    $form['field_fuentehtml_nid']['#value']=$value;
    //
    $value=$form['field_import_html_source_id'][0]['#default_value']['value'];
    $form['field_import_html_source_id']=array();
    $form['field_import_html_source_id']['#type']='hidden';
    $form['field_import_html_source_id']['#value']=$value;
    //
    unset($form['field_is_yql_noticia_publica']);
    unset($form['field_is_yql_noticia_destacada']);
    //gemini-2013
    if(isset($form['field_nid_fuente_canal'])){
        unset($form['field_nid_fuente_canal']);
    }
    //intelsat-2015
    //se ha comentado
    /*if(isset($form['field_apply_alchemy_yql'])){
        unset($form['field_apply_alchemy_yql']);
    }*/
    if(isset($form['field_apply_open_calais_yql'])){
        unset($form['field_apply_open_calais_yql']);
    }
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
        //gemini-2014
        hontza_yql_field_nombrefuente_canal_form_alter($form,$form_state,$form_id);
    }
    //gemini-2014
    if(isset($form['field_is_atom'])){
        unset($form['field_is_atom']);
    }
    
    //
    if(isset($form['body_field']['body'])){
        $form['body_field']['body']['#title']=t('Description');
    }
    if(isset($form['field_my_opml'])){
        $form['field_my_opml'][0]['#title']=t('Opml file');
    }
    if(isset($form['field_url_html'])){
        $form['field_url_html'][0]['#title']=t('Url of Html page');
    }
    if(isset($form['field_last_import_time'])){
        unset($form['field_last_import_time']);
    }
    if(isset($form['field_is_hound'])){
        unset($form['field_is_hound']);
    }
    //
    $form['reto_al_que_responde_id']=array(
            //'#title' => t('Associated Challenge'),
            '#value'=>get_reto_al_que_responde_html(),
    );    
    $form['#validate'][] = 'hontza_canal_validate_reto_al_que_responde';
    red_yql_node_form_alter($form,$form_state,$form_id);
    hontza_unset_activate_channel_form_alter($form, $form_state, $form_id);
    //intelsat-2014
    hontza_solr_canal_de_yql_form_alter($form, $form_state, $form_id);
    hontza_social_canal_de_yql_form_alter($form, $form_state, $form_id);
    //intelsat-2015
    hontza_canal_comodin_canal_de_yql_node_form_alter($form, $form_state, $form_id);
    hontza_solr_search_canal_de_yql_form_alter($form, $form_state, $form_id);
    hontza_canal_rss_add_canal_categorias_tematicas_form_field($form,'canal_de_yql');
    //
    //intelsat-2015
    red_set_required_field_canal_source_type($form);
    //intelsat-2016
    hontza_crm_inc_canal_node_form_alter($form,$form_state, $form_id);
    red_copiar_canal_node_form_alter($form,$form_state, $form_id);
    hontza_canal_json_canal_de_yql_node_form_alter($form,$form_state, $form_id);
    $form['field_is_canal_opencalais']['#weight']=$form['field_apply_alchemy_yql']['#weight']-1;
    red_canal_add_apply_alchemy_description($form,'field_apply_alchemy_yql');
    red_canal_add_is_canal_opencalais_description($form,'field_is_canal_opencalais');
}
function get_valoracion_options(){
    $result=array('Very Bad','Bad','Normal','Good','Very good');
    $result=my_t_array($result);
    return $result;
}
function my_t_array($result_in){
    $result=$result_in;
    if(count($result)>0){
        foreach($result as $i=>$v){
            $result[$i]=t($v);
        }
    }
    return $result;
}
function my_supercanal_node_form_alter(&$form,&$form_state,$form_id){
    //intelsat-2014
    $node=hontza_get_node_by_form($form);
    $form['bakup_fuente_pipe_id']=array(
        '#type'=>'hidden',
        '#default_value'=>$node->field_supercanal_fuente[0]['value'],    
    );
    //
    //intelsat-2015
    $title_source=t('Source');
    $description_source=t('ID of Yahoo Pipes');
    if(kimonolabs_is_fuente_kimonolabs($node)){
        $title_source=t('ID of Kimono');
        $description_source=$title_source;
    }
    $form['field_supercanal_fuente'][0]['#title']=$title_source;
    $form['field_supercanal_fuente'][0]['#description']=$description_source;        
    $form['field_supercanal_exhaustividad'][0]['rating']['#title']=t('Coverage');
    //intelsat-2015
    $form['field_supercanal_args']['#title']=t('Parameter');
    $form['field_supercanal_args_desc']['#title']=t('Prompt');
    /*[taxonomy] => Array
        (
            [1] => Array
                (
                    [#type] => select
                    [#title] => Tipos de fuentes*/
    //echo print_r($form,1);
    //echo print_r($form['taxonomy'],1);
    /*if(isset($form['taxonomy'][1])){
        $form['taxonomy'][1]['#title']=t('Source Types');
    }*/
    //gemini-2014
    if(hontza_is_sareko_id_red()){
        $url='';
        if(hontza_is_red_hoja()){
            red_compartir_fuente_supercanal_form_alter($form,$form_state,$form_id);
        }
        red_supercanal_node_form_alter($form, $form_state, $form_id); 
    }
    //intelsat-2015
    red_despacho_node_add_source_type_form_field($form,$form_id,$nid);
    //intelsat-2016
    red_copiar_supercanal_node_form_alter($form,$form_id,$nid);
}
function set_category_name_t($contenido_name){
    if($contenido_name=='Categoria indefinida'){
        return t('Undefined category');
    }
    //intelsat-2015
    $result=hontza_canal_rss_categorias_unset_term_name_corchetes($contenido_name);
    return $result;
}
function my_wiki_node_form_alter(&$form,&$form_state,$form_id){
    $nid=0;
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){
        $nid=$node->nid;
    }
    if(isset($form['taxonomy']['tags'][3])){
        $form['taxonomy']['tags'][3]['#title']=t('Tags');
    }
    //
    if(isset($form['taxonomy']['tags'][3])){
        $form['taxonomy']['tags'][3]['#title']=t('Tags');
    }
    $form['title']['#title']=t('Title');
    $form['body_field']['body']['#title']=t('Body');
    if(is_node_add()){    
        drupal_set_title(t('Create Wiki Document'));
        $origin_nid=arg(3);
        $form['origin_nid']=array(
            '#type'=>'hidden',
            '#default_value'=>$origin_nid,
        );
    }
    $form['my_cat_']=create_categorias_tematicas_fieldset('',1,$nid,'wiki');
    //intelsat-2015
    $form['reto_al_que_responde_id']=array(
            //'#title' => t('Associated Challenge'),
            '#value'=>get_reto_al_que_responde_html(),
        );
    //
    $form['enlaces_html']=array(
        '#value'=>hontza_get_enlaces_view_html($node,1),
        '#weight'=>-3,
    );
    if(isset($form['field_enlace_wiki'])){
        $form['field_enlace_wiki']['#weight']=-2;
    }
    //intelsat-2016
    red_copiar_wiki_node_form_alter($form,$form_state,$form_id);
}
function my_debate_node_form_alter(&$form,&$form_state,$form_id){
    $nid=0;
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){
        $nid=$node->nid;
    }
    if(isset($form['taxonomy']['tags'][3])){
        $form['taxonomy']['tags'][3]['#title']=t('Tags');
    }
    //
    $form['title']['#title']=t('Title');
    $form['body_field']['body']['#title']=t('Description');
    $form['field_enlace_debate'][0]['#title']=t('News');    
    if(is_node_add()){    
        drupal_set_title(t('Create Discussion'));
        //$origin_nid=arg(3);
        $origin_nid=hontza_solr_funciones_get_node_id_array_by_arg_string(arg(3));
        $form['origin_nid']=array(
            '#type'=>'hidden',
            '#default_value'=>$origin_nid,
        );
    }
    $form['my_cat_']=create_categorias_tematicas_fieldset('',1,$nid,'debate');
    //intelsat-2015
    $form['reto_al_que_responde_id']=array(
            //'#title' => t('Associated Challenge'),
            '#value'=>get_reto_al_que_responde_html(),
        );
    //
    $form['enlaces_html']=array(
        '#value'=>hontza_get_enlaces_view_html($node,1),
        '#weight'=>-3,
    );    
}
function my_views_exposed_form_alter(&$form,&$form_state,$form_id){
    if(is_mis_contenidos()){
        $param=arg(1);
        if($param=='fuentes'){
            $result=array();
            $result['All']='<'.t('Any').'>';
            $result['fuentedapper'] = t('Dapper Source');
            $result['supercanal'] = t('Pipe Source');
            $result['fuentehtml'] = t('Page2RSS Source');
            /*
            //intelsat-2016
            //$result['kimonolabs'] = 'Kimonolabs';*/
            //
            $form['type']['#options']=array();
            $form['type']['#options']=$result;
        }else if($param=='canales'){
            $result=array();
            $result['All']='<'.t('Any').'>';
            $result['canal_busqueda'] = t('Search Channel');
            $result['canal_de_supercanal'] = t('Source Channel');
            $result['canal_de_yql'] = t('RSS Filter Channel');
            //
            $form['type']['#options']=array();
            $form['type']['#options']=$result;
        }
    }else if(is_fuentes_pipes_todas()){
        //gemini-2014
        $form['tid']['#options']=red_funciones_get_tipos_de_fuentes_options($form['tid']['#options']);
        //intelsat-2016
        $form['field_supercanal_my_tipo']['#options']=red_solr_inc_get_fuente_origin_options_label($form['field_supercanal_my_tipo']['#options']);
    }
}
function my_get_rows_mis_contenidos_fuentes(&$vars,$my_type='fuentes'){
    $rows=$vars['rows'];
    /*echo print_r($vars,1);
    exit();*/    
    $sep='<td class="views-field views-field-type">';
    $my_array=explode($sep,$rows);
    if(!(count($my_array)>1)){
        $sep='<td class="views-field views-field-type active">';
        $my_array=explode($sep,$rows);
        
    }    
    if(count($my_array)>0){
        foreach($my_array as $i=>$v){
            if($i>0){
                $s='';
                $pos=strpos($v,'</td>');
                if($pos===FALSE){
                    //
                }else{
                    $s=substr($v,0,$pos);
                    $beste=substr($v,$pos);
                    if($my_type=='canales'){
                        $my_array[$i]=get_tipo_canal_by_castellano($s,$vars,$i).$beste;
                    }else{
                        $my_array[$i]=get_tipo_fuente_by_castellano($s).$beste;
                    }
                }
            }
            //            
        }
        $rows=implode($sep,$my_array);
    }
    return $rows;
}
function get_tipo_fuente_by_castellano($s_in){
    $s=trim($s_in);
    if(strcmp($s,'Fuente HTML')==0){
        $s=t('Page2RSS Source');
    }else if(strcmp($s,'Fuente PIPE')==0){
        $s=t('Pipe Source');
    }else if(strcmp($s,'Fuente DAPPER')==0){
        $s=t('Dapper Source');
    }
    //
    return $s;
}
function get_tipo_canal_by_castellano($s_in,$vars,$i){    
    $row=$vars['view']->result[$i-1];
    $is_simple=0;
    if($row->node_type=='canal_de_yql'){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            $is_simple=hontza_get_canal_yql_is_simple_by_params($canal_yql_parametros_row);            
        }        
        if(hontza_is_hound_canal($row->nid)){
            if($is_simple){
                return t('Hound Channel');
            }else{
                return t('Hound Filter Channel');
            }
        }else{
            if($is_simple){
                return t('RSS Channel');
            }else{
                return t('RSS Filter Channel');
            }
        }        
    }
    $s=trim($s_in);
    if(strcmp($s,'Canal de fuente')==0 || strcmp($s,'canal_de_supercanal')==0){
        $s=t('Source Channel');
    }else if(strcmp($s,'Canal-Filtro de multiples RSS')==0){
        $s=t('RSS Filter Channel');
    }else if(strcmp($s,'canal_busqueda')==0){
        $s=t('Search Channel');
    }
    //
    
    return $s;
}
function repase_organic_group_access(){
//function repase_organic_group_access(&$node){
    global $user;
    /*
      
      $my_grupo=og_get_group_context();
      //
      if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(isset($user->uid) && !empty($user->uid)){
            return 1;
        }else{
            return 0;
        }
      }
      return 0;*/
        if(isset($user->uid) && !empty($user->uid)){
            return 1;
        }else{
            //intelsat-2015                
            if(hontza_canal_rss_is_publico_activado()){
                return publico_repase_organic_group_access();
            }else{
                return 0;
            }    
        }
    //intelsat-2015    
    return 0;  
}
function goto_grupo_sin_seleccionar(){
        $my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            //
        }else{
            drupal_goto('grupo_sin_seleccionar');
            exit();
        }
}
function my_get_rows_canales_dash($vars_in){
    $result=$vars_in['rows'];
    
    if(!repase_organic_group_access()){
        drupal_access_denied();
        exit();
    }
    
    return $result;
}
function get_user_news_block_content(){
    if(!repase_organic_group_access()){
        return '';
    }

    $content =l(t('Create User News'), 'node/add/noticia') .'<br>'.
                              l(t('View all'), 'canal-usuarios');
    return $content;
}
function my_hontza_views_pre_render(&$view) {
  if ($view->display_handler->display->display_plugin === 'block') {
      //
  }else if ($view->display_handler->display->display_plugin === 'page') {
      if(strcmp($view->name,'og_fuentes_pipes')==0){
        og_fuentes_pipes_pre_render($view);
      }
  }
}
function og_fuentes_pipes_pre_render(&$view){
    if(count($view->result)>0){
        foreach($view->result as $i=>$row){
            /*if(user_access('root')){
                echo print_r($row,1);
            }*/
            $view->result[$i]->term_data_name=get_term_extra_name($row->term_data_tid, '', $row->term_data_name);
        }
    }
}
function get_term_extra_description($tid,$code_in,$description){
    return get_term_extra_field($tid, $code_in, $description,'description');
}
function get_term_extra_field($tid,$code_in,$row_name,$field){
    //gemini
    global $language;
    if(empty($tid)){
        return '';
    }
    if($language->language=='es'){
        return $row_name;
    }
    $code=$code_in;
    if(empty($code)){
        $code=$language->language;
    }
    $name='';
    $term_extra=get_term_extra_row($tid,$code);
    //$term_extra=array();
    if(isset($term_extra->tid) && !empty($term_extra->tid)){
        if(!empty($term_extra->$field)){
            $name=$term_extra->$field;
        }
    }
    if(!empty($name)){
        $name=$name;
    }else{
        $name='['.trim(trim($row_name,'['),']').']';
    }
    return $name;
}
function get_term_extra_name($tid,$code_in,$row_name){
    return get_term_extra_field($tid, $code_in, $row_name,'name');
}
function translate_html_terms($terms){
    //print 'aaaa='.$terms.'=bbbb<BR>';
    return $terms;
}
function my_gestion_servicios_pre_execute(&$view){
    $sql=panel_admin_servicios_get_gestion_servicios_sql();
    //print $sql;
    //
    $view->build_info['query']=$sql;
    $view->build_info['count_query']=$sql;
}
function my_fuentehtml_cancel_submit(){
    //print 'prueba';exit();
}
function hontza_fuentehtml_form_after_build($form, &$form_state) {
  if($form_state['clicked_button']['#value'] == 'Cancel') {
    hontza_fuentehtml_disable_validation($form);
  }
  return $form;
}
function hontza_fuentehtml_disable_validation(&$element) {
  unset($element['#needs_validation']);
  foreach(element_children($element) as $key) {
    hontza_fuentehtml_disable_validation($element[$key]);
  }
}
function get_english_information_type_value($s){
    $my_array=array(
    'Debate'=>'Discussion',
    'Decision'=>'Decision',
    'Facilitator'=>'Facilitator',
    'Fuentehtml'=>'HTML Source',
    'Idea'=>'Idea',
    //'Need of Information'=>'Information Need',
    'Key Question'=>'Key Question',    
    'Noticia'=>'News',
    'Noticia de Portada'=>'Home News',
    'Noticia de Usuario'=>'User News',
    'Noticia Destacada'=>'Highlighted News',
    'Oportunidad'=>'Opportunity',
    'Post_frase_consultores_en_inteli'=>'Goodbye Message to IC Consultants',
    'Post_frase_facilitadores'=>'Message to Facilitators',
    'Post_frase_probar_hontza_online'=>'Message to Test Hontza Online',
    'Profile'=>'Profile',
    'Proyecto'=>'Project',
    'Reto'=>'Challenge',
    'Subreto'=>'SubChallenge',
    'Wiki'=>'Wiki');
    if(isset($my_array[$s])){
        return $my_array[$s];
    }
    return $s;
}
//gemini
function get_import_html_row($my_id,$fuentehtml_nid=''){
    $where=array();
    $where[]='1';
    if(!empty($my_id)){
        $where[]='id='.$my_id;
    }
    if(!empty($fuentehtml_nid)){
        $where[]='fuentehtml_nid='.$fuentehtml_nid;
    }
    //
    $result=array();
    if(!empty($my_id)){
        $sql='SELECT * FROM import_html_source WHERE '.implode(' AND ',$where);
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            return $row;
        }
    }
    $my_result=(object) $result;
    return $my_result;
}
function my_translate_profile_value($value,$s,$attributes=''){   
    if($s==t('Do your company have commercial relations with other CI software providers?')){
        if(strcmp($value,'No')==0){
            return t('No');
        }else if(strcmp($value,'Si')==0){
            return t('Yes');
        }
    //gemini-2013    
    }else if($s=='Sitio web'){
        return repase_http($value);
    }else{
        return hontza_canal_rss_user_translate_profile_value($value,$s,$attributes);
    }    
    return $value;
}
function is_user_uid_dashboard(){
    if(arg(0)=='user'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2=='dashboard'){
                return 1;
            }
        }
    }
    return 0;
}
function unset_my_dashoard_link($param){
    global $user;
    $result=array();
    $sep='href="';
    $my_array=explode($sep,$param);
    if(count($my_array)>0){
        foreach($my_array as $i=>$v){
            if($i>0){
                $pos=strpos($v,'"');
                if($pos===FALSE){
                    $result[]=$v;
                }else{
                    $value=substr($v,0,$pos);
                    $pos2=strpos($value,'user/'.$user->uid.'/dashboard');
                    if($pos2===FALSE){
                        $result[]=$v;
                    }
                }
            }else{
                $result[]=$v;
            }
        }
    }
    return implode($sep,$result);
}
function get_canal_last_update($canal_nid){
    $last_update=0;
    $nid_list=get_canal_nid_list($canal_nid,1);
    if(count($nid_list)>0){
        foreach($nid_list as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                if($node->created>$last_update){
                    $last_update=$node->created;
                }
            }
        }
    }
    return $last_update;
}
function is_canales_por_categorias($konp='',$view_name=''){
    //print 'view_name===='.$view_name;exit();
    $arg0=arg(0);
    if(strcmp($arg0,'canales')==0){
        $arg1=arg(1);
        if(strcmp($arg1,'categorias')==0){
            if(empty($konp)){
                if(!empty($view_name) && strcmp($view_name,'og_canales_por_categorias')==0){
                    return 1;
                }
            }else{
                $order=my_get_request('order');
                if(!empty($order) && strcmp($order,'last_update')==0){
                    if(!empty($view_name) && strcmp($view_name,'og_canales_por_categorias')==0){
                        return 1;
                    }
                }    
            }
        }
    }
    return 0;
}
function create_canales_por_categorias_order_array($my_list_in){
    $result=$my_list_in;
    $f='last_update';
    if(count($result)>0){

        foreach($result as $i=>$row){
            $result[$i]->$f=get_canal_last_update($row->nid);
        }
    }


	$info['field']=$f;
	$info['my_list']=$result;
        //echo print_r($result,1);
	return $info;
}
function get_num_canal_ideas($canal_nid){
 $kont=0;
 $nid_list=get_canal_nid_list($canal_nid);
 if(!empty($nid_list)){
 	foreach($nid_list as $i=>$nid){
            $ideas_list=get_node_ideas_list($nid);
            $kont=$kont+count($ideas_list);
	}
 }
 return $kont;
}
function get_node_ideas_list($nid){
    $where=array();
    $where[]='1';
    $where[]='i.noticia_nid='.$nid;
    //
    $sql='SELECT * FROM {idea} i WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_canal_last_update_date_format($nid,$my_format=''){
    $last_update=get_canal_last_update($nid);
    if(!empty($last_update)){
        if(empty($my_format)){
            return date( 'd-m-Y H:i',$last_update);
        }else{
            return date( 'Y-m-d H:i:s',$last_update);
        }    
    }
    return '';
}
function my_hontza_nodeapi_view(&$node, $op, $a3 = NULL, $a4 = NULL) {
    /*if($node->type=='item'){
        if(user_access('root') && hontza_is_sareko_id_red()){
            echo print_r($node->field_item_canal_reference,1);
        }
    }*/
    if(isset($node->taxonomy)){
        quitar_repetidos_node_taxonomy($node);
    }
    //intelsat-2014
    $node->my_analisis=hontza_social_get_analisis($node);
}
function my_fix_menutop($menutop){
    //return $menutop;
    return my_get_menutop($menutop,1);
}