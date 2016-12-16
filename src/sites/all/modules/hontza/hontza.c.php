<?php
function hontza_is_show_vigilancia_pendientes_tab($canal_nid_in=''){    
global $user;
$canal_nid=$canal_nid_in;
if(!empty($canal_nid_in)){
    $s=(string) $canal_nid_in;
    $pos=strpos($s,'?');
    if($pos===FALSE){
        //        
    }else{
        $canal_nid=substr($s,0,$pos);        
    }
}
//
$my_grupo=og_get_group_context();
//
$where=array();
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")"; 
}
$where[]="(node_data_field_responsable_uid.field_responsable_uid_uid = ".$user->uid." OR node_data_field_responsable_uid2.field_responsable_uid2_uid = ".$user->uid.")";

if(!empty($canal_nid)){
	$where[]="(my_canal.nid = ".$canal_nid.")"; 
}


$sql="SELECT my_canal.*
FROM {node} my_canal  
LEFT JOIN content_field_responsable_uid node_data_field_responsable_uid ON my_canal.nid = node_data_field_responsable_uid.nid
LEFT JOIN content_field_responsable_uid2 node_data_field_responsable_uid2 ON my_canal.nid = node_data_field_responsable_uid2.nid
LEFT JOIN {og_ancestry} og_ancestry ON my_canal.nid = og_ancestry.nid 
WHERE ".implode(" AND ",$where)."
GROUP BY my_canal.nid";

$res=db_query($sql);

while($row=db_fetch_object($res)){
    return 1;
}
return 0;
}
function hontza_og_vigilancia_mascomentadas_execute_view($result_in,$view_name){
    $result=array();
    if(count($result_in)>0){
        foreach($result_in as $i=>$row){
          if(hontza_is_comentada_by_row($row)){
                $result[]=$row;
          }
        }
    }
    return $result;
}
function hontza_is_comentada_by_row($row){
    if($row->node_comment_statistics_comment_count>0){
        return 1;
    }
    $node=node_load($row->nid);
    if(isset($node->nid) && !empty($node->nid)){
        $info=hontza_get_node_c_d_w_info($node);
        if(isset($info['num_wiki']) && !empty($info['num_wiki']) && is_numeric($info['num_wiki']) && $info['num_wiki']>0){
            return 1;
        }
        if(isset($info['num_debate']) && !empty($info['num_debate']) && is_numeric($info['num_debate']) && $info['num_debate']>0){
            return 1;
        }
        if(isset($info['num_ideas']) && !empty($info['num_ideas']) && is_numeric($info['num_ideas']) && $info['num_ideas']>0){
            return 1;
        }
    }
    return 0;
}
function hontza_set_menutop_monitoring_default($menutop){
    if(hontza_is_show_vigilancia_pendientes_tab()){
        return $menutop;
    }else if(red_canal_is_noticias_validadas()){
        $result=str_replace('vigilancia/pendientes','vigilancia/validados',$menutop);
        return $result;
    }else{
        $result=str_replace('vigilancia/pendientes','vigilancia/ultimas',$menutop);
        return $result;
    }
}
function hontza_is_show_canales_pendientes_tab($canal_nid){
    return hontza_is_show_vigilancia_pendientes_tab($canal_nid);
}
function hontza_is_vigilancia_mascomentadas($view_name){
    if(!empty($view_name)){
        if($view_name=='og_vigilancia_mascomentadas'){
            return 1;
        }
        if($view_name=='og_canales_dash'){
            if(hontza_is_canales('lo-mas-comentado')){
                return 1;
            }
        }
    }    
    return 0;
}
function hontza_get_canales_por_categoria($id_categoria, $tid){
    $result=array();
              $res = db_query("SELECT node.nid
                                              FROM node node LEFT JOIN {term_node term_node} ON node.vid = term_node.vid
                                              LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid
                                              WHERE (node.type in ('canal_busqueda', 'canal_de_supercanal', 'canal_de_yql')) AND term_data.vid=%d AND term_data.tid=%d
                                              ", $id_categoria, $tid);
              while($row=db_fetch_object($res)){
                  $result[]=$row->nid;
              }
              return $result;
}
function hontza_is_canales_por_categorias_filtrador($canal_nid_array){
    if(!empty($canal_nid_array)){
        foreach($canal_nid_array as $i=>$canal_nid){
            if(hontza_is_show_canales_pendientes_tab($canal_nid)){
                return 1;
            }
        }    
    }
    return 0;
}
function hontza_is_show_canales_por_categorias_pendientes_tab($tid){
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'", $my_grupo->nid));
        $canal_nid_array=hontza_get_canales_por_categoria($id_categoria, $tid);
        return hontza_is_canales_por_categorias_filtrador($canal_nid_array);
    }
    return 0;
}
function hontza_get_title_grupo_response_block(){
    //return t('Deploy Ideas');
    return t('Proposals');
}
function hontza_get_grupo_response_content($is_solo_proyectos=0){
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    $content=hontza_get_response_del_grupo_html($is_solo_proyectos);
    if(empty($content)){
        return '';
    }
    $delta=48;
    $help_block=help_popup_block(149374);
    $html='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-'.$delta.'">';
    //intelsat-2015
    $title=hontza_get_title_grupo_response_block();
    $icono=my_get_icono_action('idea_dashboard',$title,'idea_dashboard').'&nbsp;';
    //
    $html.='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
    $html.='<div class="content">';
    $html.=$content;
    $html.=hontza_inicio_view_all('ideas/arbol',t('View Proposals'));  
    $html.='</div></div>';
    return $html;
}
function hontza_get_response_del_grupo_html($is_solo_proyectos=0){
  $html=array();
  $my_grupo=og_get_group_context();
  if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){ 
    $arbol=get_idea_arbol_rows(1,'','',$is_solo_proyectos);
    $my_limit=variable_get('hontza_inicio_response_limit',15);
    $num=count($arbol);
    if($num>$my_limit){
        $arbol=array_slice($arbol,0,$my_limit);
    }
    if($num>0){
        foreach($arbol as $i=>$row){
            $html[]=$row[0];
        }
    }    
  }
  return implode('',$html);
}
function hontza_is_show_response_del_grupo(){
    if(is_dashboard()){
        $ideas=get_idea_arbol_rows();
        if(count($ideas)>0){
            return 1;
        }
    }
    return 0;
}
function hontza_get_response_del_grupo_region($is_solo_proyectos=0){
  $html=array();
  //$html[]="<div id='c_response_del_grupo'><div class='page-region'>";
  $content=hontza_get_grupo_response_content($is_solo_proyectos);
  if(empty($content)){
      return '';
  }
  $html[]=$content;
  //$html[]="</div></div>";
  return implode("",$html);
}
function hontza_inicio_view_all($url_in,$title_in=''){
    if(is_dashboard()){
        $title=$title_in;
        if(empty($title)){    
            $title=t('View all');
            if($url_in=='area-debate'){
                $title=t('View Discussion Area');
            }else if($url_in=='area-trabajo'){
                $title=t('View Collaborative Area');
            }    
        }        
        $url=$url_in;
        if($url=='channels'){
            $url='vigilancia/validados';
            if(hontza_is_show_vigilancia_pendientes_tab()){
                $url='vigilancia/pendientes';        
            }
        }
        //return '<div style="text-align:right;">'.l(t('View all'),$url).'</div>';
        //return '<div style="text-align:right;">'.l(my_get_icono_action('folder_open',t('View all')),$url,array('html'=>true,'query'=>$)).'</div>';
        //intelsat-2015
        $url_info=parse_url($url);
        return '<div style="text-align:right;">'.l(my_get_icono_action('folder_open',$title),$url_info['path'],array('html'=>true,'query'=>$url_info['query'],'attributes'=>array('target'=>'_blank'))).'</div>';        
    }
    return '';
}
function hontza_set_primer_grupo_el_seleccionado($result_in,$grupo_nid){
    $result=array();
    $primero=array();
    if(count($result_in)>0){
        foreach($result_in as $i=>$row){
            if($row->nid==$grupo_nid){
                $primero[]=$row;
            }else{
                $result[]=$row;
            }
        }
    }
    $result=array_merge($primero,$result);
    return $result;
}
function hontza_get_reports_content(){
    global $user;
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    if(isset($user->uid) && !empty($user->uid)){
        return boletin_report_get_reports_content();
    }
    return '';
}
function hontza_item_resumen($node){
    $len=150;
    $result=strip_tags($node->body);
    if(strlen($result)>$len){
        $result=drupal_substr($result, 0, $len); 
        $result.='...';
    }
    return $result;
}
function hontza_on_noticia_presave(&$node){
    global $user;
    if(!isset($node->uid) || empty($node->uid)){
        $node->uid=$user->uid;
    }
    //intelsat-2015
    //hontza_on_node_categoria_save($node);
    hontza_canal_rss_on_node_categoria_tematica_presave($node);
    //
}
function hontza_on_node_categoria_save(&$node){
    $konp='my_cat_';
    foreach($node as $name=>$v)
    {
        $pos=strpos($name,$konp);
        if($pos===false){
            //
        }else{
            if(!empty($v)){
                $tid=trim(str_replace($konp,'',$name));                    
                if(!empty($tid)){
                    if(!isset($node->taxonomy)){
                        $node->taxonomy=array();
                    }
                    $node->taxonomy[$tid]=taxonomy_get_term($tid);
                }
            }
            unset($node->$name);
        }
    }    
}
function hontza_get_nid_by_form($form){
    $nid=0;
    if(isset($form['nid'])){
        $nid=$form['nid']['#value'];
    }
    return $nid;
}
function es_categoria_tematica_del_node($node,$tid,$tid_array_in=array()){
    //intelsat-2015
    return red_es_categoria_tematica_del_node($node,$tid,$tid_array_in);
}
function hontza_on_my_report_presave(&$node){
    //intelsat-2015
    hontza_canal_rss_on_node_categoria_tematica_presave($node);        
}
function hontza_is_mis_alertas_boletines(){
    $param0=arg(0);
    if(!empty($param0)){
        if(in_array($param0,array('alerta_user','boletin_grupo','boletin_report','alerta'))){
            return 1;
        }
    }
    return 0;
}
function hontza_create_menu_mis_contenidos_block(){
    $result ='<ul class="menu sf-js-enabled">
		  					  <li class="leaf'.get_mis_contenidos_active_trail('').'">'.l(t('My Contents'),'mis-contenidos').'</li>
		  					  <li class="leaf'.get_mis_contenidos_active_trail('fuentes').'">'.l(t('My sources'),'mis-contenidos/fuentes').'</li>
                              <li class="leaf'.get_mis_contenidos_active_trail('canales').'">'.l(t('My Channels'),'mis-contenidos/canales').'</li>
                              <li class="leaf'.get_mis_contenidos_active_trail('items').'">'.l(t('My News'),'mis-contenidos/items').'</li>
                              <li class="leaf'.get_mis_contenidos_active_trail('debates').'">'.l(t('My Discussions'),'mis-contenidos/debates').'</li>
							  <li class="leaf last'.get_mis_contenidos_active_trail('area-trabajo').'">'.l(t('My Wikis'),'mis-contenidos/area-trabajo').'</li>
                              </ul>';
    return $result;    
}
function hontza_create_menu_mis_contenidos(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $html[]='<li'.hontza_mis_contenidos_menu_class('all').'>'.l(t('My Contents'),'mis-contenidos').'</li>';
    $html[]='<li'.hontza_mis_contenidos_menu_class('fuentes').'>'.l(t('My sources'),'mis-contenidos/fuentes').'</li>';    
    $html[]='<li'.hontza_mis_contenidos_menu_class('canales').'>'.l(t('My Channels'),'mis-contenidos/canales').'</li>';    
    $html[]='<li'.hontza_mis_contenidos_menu_class('items').'>'.l(t('My News'),'mis-contenidos/items').'</li>';    
    $html[]='<li'.hontza_mis_contenidos_menu_class('debates').'>'.l(t('My Discussions'),'mis-contenidos/debates').'</li>';    
    $html[]='<li'.hontza_mis_contenidos_menu_class('area-trabajo').'>'.l(t('My Wikis'),'mis-contenidos/area-trabajo').'</li>';        
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';    
    return implode('',$html);
}
function hontza_mis_contenidos_menu_class($konp){
    $result=0;
    $param0=arg(0);
    if(!empty($param0)){
        if($param0=='mis-contenidos'){
            $param1=arg(1);
            if(empty($param1)){
                $param1='all';
            }
            //
            if($param1==$konp){
                $result=1;
            }
        }
    }
    //    
    if($result){
        return ' class="active"';
    }
    return '';
}
function hontza_is_mis_grupos(){
    $param0=arg(0);
    if(!empty($param0)){
        if($param0=='mis-grupos'){
            return 1;
        }
    }
    return 0;
}
function hontza_is_pantalla_top_menu(){
    if(is_usuarios_menu() || hontza_is_mis_alertas_boletines() || is_mis_contenidos() || hontza_is_mis_grupos() || hontza_is_faq()){
       return 1;        
    }
    if(hontza_is_user_gestion() || hontza_is_gestion() || is_ficha_node('boletin-grupo-introduccion') || is_ficha_node('boletin-grupo-despedida')){
        return 1;
    }
    if(calendario_is_pantalla()){
        return 1;
    }
    //gemini-2014
    if(hontza_is_mi_grupo()){
        return 1;
    }
    if(is_alerta_user()){
        return 1;
    }
    //
    return 0;
}
function hontza_is_user_gestion(){
    return hontza_is_pantalla('user-gestion');
}
function hontza_is_pantalla($konp){
    $param0=arg(0);
    if(!empty($param0)){
        if($param0==$konp){
            return 1;
        }
    }
    return 0;
}
function hontza_is_gestion($konp=''){
    if(hontza_is_pantalla('gestion')){
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
function hontza_set_menutop_by_group($menutop){
    $my_group=og_get_group_context();
    if(isset($my_group->nid) && !empty($my_group->nid)){
        return $menutop;
    }
    return '';
}
function hontza_is_faq(){
    return hontza_is_pantalla('faq');
}
function hontza_navigation_menutop_page_header($menutop_in,$is_with=0){
    $html=array();
    //intelsat-2015
    if(red_crear_usuario_is_custom_css()){
        return '';
    }
    $menutop=$menutop_in;
    if($is_with || is_user_invitado() || hontza_is_gestion()){
        $menutop=my_get_menutop($menutop);
        $html[]='<div id="navigation">';
        $html[]='<a id="context-block-region-menutop" class="context-block-region"></a>';
        $html[]='<div class="block block-menu block-even region-odd clearfix style-menu " id="block-menu-primary-links">';
        $html[]='<h3 class="title">'.t('Primary links').'</h3>';
        $html[]='<div class="content">';
	$html[]='<ul class="menu sf-js-enabled">';
        $html[]=hontza_grupos_set_active_menutop($menutop);
        $html[]='</ul>';
	$html[]='<a class="context-block editable edit-vigilancia" id="context-block-menu-primary-links"></a>';
        $html[]='</div>';
	$html[]='</div>';
	$html[]='</div>';
        return implode('',$html);
    }
    return $menutop;
}
function hontza_add_form_menu_alerta_user(&$form){
    $form['alerta_menu_user']=array();
    $form['alerta_menu_user']['#value']=create_menu_alerta_user();
    $form['alerta_menu_user']['#weight']=-1000;
}
function hontza_is_claves(){
    return 1;
    //return hontza_claves_apply_module();
}
function hontza_repase_vigencia_maxima_pass(){
    if(hontza_is_claves()){
        claves_repase_vigencia_maxima_pass();
    }
}
function hontza_get_title_gestion_pass(){
    return t('PASSWORDS');
}
function hontza_get_gestion_pass_content(){
   if(hontza_is_claves()){
        if(user_access('Admin Keys') || user_access('access claves validez')){
            $html=array();
            if(user_access('Admin Keys')){
                $html[]=l(t('Password Settings'), 'gestion/claves');
            }
            if(user_access('access claves validez')){
                $html[]=l(t('Management of Passwords'),'gestion/claves/validez');
            }
            return implode('<BR>',$html);
        }
    }
    return '';
}
function hontza_is_footer(){
    if(defined('_SAREKO_ID')){
        if(in_array(_SAREKO_ID,array('ROOT'))){
            return 1;
        }
        return 0;        
    }
    return 1;
}
function hontza_comment_form_alter(&$form, &$form_state, $form_id){
    global $language;
    //echo print_r($form,1);
    if($language->language!='en'){
        drupal_set_title(t('Comment'));
    }
    //gemini-2014
    $cid='';
    if(isset($form['cid']['#value']) && !empty($form['cid']['#value'])){
        $cid=$form['cid']['#value'];
    }
    estrategia_comment_form_alter($form,$form_state, $form_id);
    /*if ($_REQUEST['destination']) {
    	$form['#action'].='?destination='.$_REQUEST['destination'];  		
    }*/
    if(isset($form['_author'])){
        unset($form['_author']);
    }
    //
    if(isset($form['subject'])){
        unset($form['subject']);
    }
    
    hontza_debate_comment_form_alter($form,$form_state, $form_id);
    //$form['comment_filter']['comment']['#attributes']['spellcheck']=true;
    //gemini-2014
    unset($form['preview']);
    if(!empty($cid)){
        if(user_access('administer comments') || hontza_is_mi_comentario('',$cid)){
            $form['my_delete_comment_link']=array(
                '#type'=>'markup',
                '#weight'=>20,
                '#value'=>l(t('Delete'),'comment/delete/'.$cid),                    
            );
            //intelsat-2015
            if(red_movil_is_activado()){
                $form['my_delete_comment_link']['#prefix']='<div class="div_delete_comment_link">';
                $form['my_delete_comment_link']['#suffix']='</div>';
            }        
        }
    }
    //
    $is_publico=0;
    if(hontza_canal_rss_is_publico_activado()){
        if(publico_is_pantalla_publico('vigilancia')){
            $is_publico=1;
        }
    }
    //intelsat-2015
    $is_movil_comentario=  red_movil_is_activado();
    //if($is_publico){
    if($is_publico || $is_movil_comentario){
        $form['comment_filter']['format']['#prefix']='<div style="display:none;">';
        $form['comment_filter']['format']['#suffix']='</div>';
    }
    if($is_movil_comentario){
        red_movil_comment_form_alter($form,$form_state,$form_id);
    }
}
function hontza_item_categorias_style($solo_color=0){
    $color='';
    if(red_is_rojo()){
        $color='color:#C6000B;';
    }
    if($solo_color){
        return ' style="'.$color.';font-weight:bold;"';
    }
    return ' style="float:left;'.$color.'font-weight:bold;clear:both;"';
}
function hontza_is_sareko_id($konp){
    if(defined('_SAREKO_ID') && _SAREKO_ID==$konp){
        return 1;
    }else if(!defined('_SAREKO_ID') && $konp=='ROOT'){
        return 1;
    }
    return 0;
}
function hontza_get_frase_powered_light(){
    //gemini-2014
    $alerta_html='';    
    //
    $result='Powered by Hontza';
    //intelsat-2015
    $result.=' '.red_get_powered_version();
    $url='http://www.hontza.es';
    //intelsat-2015
    //$result='<a id="id_a_powereded_light" href="'.$url.'">'.$result.'</a>';
    //$style='clear:both;padding-top:10px;';
    $margin_bottom=20;
    //intelsat-2016
    $powered_link_color='';
    if(red_crear_usuario_is_custom_css()){
        $margin_bottom=0;
        //$powered_link_color='color:white;font-weight:bold;';
        $powered_link_color='color:white;';
    }else if(hontza_is_sareko_id_red() || hontza_canal_rss_is_visualizador_activado()){
        $margin_bottom=10;    
    }
    $style='clear:both;padding-top:10px;margin-bottom:'.$margin_bottom.'px;';
    $result='<div style="'.$style.'"><a id="id_a_powereded_light" href="'.$url.'" style="font-size:12px;'.$powered_link_color.'">'.$result.'</a></div>';
    //intelsat-2015
    //if(hontza_is_sareko_id_red() && !red_is_network_sareko_id() && !red_is_servidor_network()){
    $logos=hontza_canal_rss_get_logos_apis($result);    
    //if(hontza_is_sareko_id_red() && !red_is_network_sareko_id() && !red_is_servidor_network()){
    //if((hontza_is_sareko_id_red() && !red_is_network_sareko_id() && !red_is_servidor_network()) || red_is_subdominio_alerta()){
    if(red_is_subdominio_proyecto_alerta()){    
        $alerta_html=red_funciones_alerta_financiado_por_html();
    }else{
        if(hontza_canal_rss_is_visualizador_activado()){
            $alerta_html=visualizador_get_frase_powered();
            $alerta_html.=$logos;
            $logos='';
        }
    }
    //
    //$result=$logos.$alerta_html.$result;
    $result=$logos.$alerta_html;
    return $result;
}
function hontza_claves_apply_module(){
    $claves_apply_module=variable_get('claves_apply_module',0);
    if($claves_apply_module){
        return 1;
    }
    return 0;
}
function hontza_get_type_of_group($node,$type='',&$tid){
    if(isset($node->taxonomy) && !empty($node->taxonomy)){        
        foreach($node->taxonomy as $key=>$value){
            if(!empty($type) && $type=='hontza_grupos_save'){
                $vid=$key;
                $tid=$value;
            }else{
                $tid=$key;
                $row=$value;
            }            
            if(in_array($tid,hontza_get_type_of_group_tid_array())){
                $term=taxonomy_get_term_by_language($tid,"en");
                if(isset($term->tid) && !empty($term->tid)){
                    return strtolower($term->name);
                }    
            }
        }
    }    
    return 'private';
}
function hontza_get_type_of_group_tid_array(){
    /*AVISO::::
                tid=27 Collaboration
                tid=28 Private
                tid=29 Public
                */
    $result=array(27,28,29);
    return $result;
}
function hontza_fix_eval_options_callback(){
    $html=array();
    $bakup_estrategia=hontza_get_table_array('bakup_estrategia');
    $html[]=hontza_fix_eval_table($bakup_estrategia,'estrategia',array('importancia_reto','facilidad_reto'));
    $bakup_despliegue=hontza_get_table_array('bakup_despliegue');
    $html[]=hontza_fix_eval_table($bakup_despliegue,'despliegue',array('importancia_despliegue'));
    $bakup_decision=hontza_get_table_array('bakup_decision');
    $html[]=hontza_fix_eval_table($bakup_decision,'decision',array('valor_decision'));
    $bakup_informacion=hontza_get_table_array('bakup_informacion');
    $html[]=hontza_fix_eval_table($bakup_informacion,'informacion',array('importancia','accesibilidad'));
    $html[]=hontza_fix_comparar_eval_options();
    return implode('',$html);
}
function hontza_get_table_array($table_name,$nid='',$vid=''){
    $where=array();
    //
    $where[]='1';
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    //
    if(!empty($vid)){
        $where[]='vid='.$vid;
    }
    //
    $result=array();
    $sql='SELECT * FROM {'.$table_name.'} WHERE '.implode(' AND ',$where).' ORDER BY nid DESC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_fix_eval_table($my_list,$table_name,$fields){
    $html=array();
    if(!empty($my_list)){
        foreach($my_list as $i=>$r){
            //$row=hontza_get_table_row($table_name,$r->nid,$r->vid);
            $html[]=$table_name.'-'.$r->nid.'<BR>';
            if(!empty($fields)){
                foreach($fields as $k=>$f){
                    if(isset($r->$f)){
                        $new_value=hontza_fix_eval_value($r->$f);
                        $html[]=$f.'->'.$r->$f.'->'.$new_value.'<BR>';
                        db_query($sql=sprintf('UPDATE {'.$table_name.'} SET '.$f.'=%d WHERE nid=%d AND vid=%d',$new_value,$r->nid,$r->vid));
                        $html[]=$sql.'<BR>';
                    }    
                }
            }
            $html[]='####################################################<BR>';
        }
    }
    return implode('',$html);
}
function hontza_get_table_row($table_name,$nid='',$vid=''){
    $my_list=hontza_get_table_array($table_name,$nid,$vid);
    if(count($my_list)>0){
        return $my_list[0];
    }
    $result=new stdClass();
    return $result;
}
function hontza_fix_eval_value($v){
    if(!empty($v)){
        $new_eval_values=hontza_define_fix_new_eval_values();
        if(isset($new_eval_values[$v]) && !empty($new_eval_values[$v])){
            return $new_eval_values[$v];
        }
    }
    return 0;
}
function hontza_define_fix_new_eval_values(){
    $result=array();
    $result[1]=5;
    $result[2]=4;
    $result[3]=3;
    $result[4]=2;
    $result[5]=1;
    return $result;
}
function hontza_fix_comparar_eval_options(){
    $html=array();
    $html[]='######################################COMPARAR##########################################################';
    $bakup_estrategia=hontza_get_table_array('bakup_estrategia');
    $html[]=hontza_fix_comparar_eval_table($bakup_estrategia,'estrategia',array('importancia_reto','facilidad_reto'));
    $bakup_despliegue=hontza_get_table_array('bakup_despliegue');
    $html[]=hontza_fix_comparar_eval_table($bakup_despliegue,'despliegue',array('importancia_despliegue'));
    $bakup_decision=hontza_get_table_array('bakup_decision');
    $html[]=hontza_fix_comparar_eval_table($bakup_decision,'decision',array('valor_decision'));
    $bakup_informacion=hontza_get_table_array('bakup_informacion');
    $html[]=hontza_fix_comparar_eval_table($bakup_informacion,'informacion',array('importancia','accesibilidad'));
    return implode('',$html);
}
function hontza_fix_comparar_eval_table($my_list,$table_name,$fields){
    $html=array();
    if(!empty($my_list)){
        foreach($my_list as $i=>$r){
            $row=hontza_get_table_row($table_name,$r->nid,$r->vid);
            $html[]=$table_name.'-'.$r->nid.'<BR>';
            if(!empty($fields)){
                foreach($fields as $k=>$f){
                    $html[]='<b>'.$f.'</b><BR>';
                    $html[]='old_value='.$r->$f.'<BR>';
                    $html[]='new_value='.$row->$f.'<BR>';
                }
            }
            $html[]='####################################################<BR>';
        }
    }
    return implode('',$html);
}
function hontza_limpiar_link_para_alchemy($link_in){
    $link=$link_in;
    $pos=strpos($link,'http://');
    if($pos===FALSE){
        return $link;
    }else{
        if($pos>1){
            $link=substr($link,$pos);
            return $link;
        }
    }
    return $link;
}
function hontza_canal_validate_reto_al_que_responde(&$form,&$form_state){
    /*$ok=0;
    $responde_array=hontza_canal_get_request_responde_array();
    if(!empty($responde_array)){
        foreach($responde_array as $i=>$row){
            foreach($row as $field=>$value){
                if(!empty($value)){
                    $ok=1;
                    break;
                }
            }
            if($ok){
                break;
            }
        }
    }
    if(!$ok){
        form_set_error('reto_al_que_responde',t('Associated Challenges is a required field'));
    }*/
}
function hontza_canal_get_request_responde_array(&$is_form_save){
    $result=array();
    $is_form_save=0;
    if(isset($_REQUEST['estrategia']) && !empty($_REQUEST['estrategia'])){
        $is_form_save=1;
        $responde_array=$_REQUEST['estrategia'];
        $responde_estrategia_nid=0;
        $responde_despliegue_nid=0;
        $responde_decision_nid=0;
        $responde_informacion_nid=0;
        if(!empty($responde_array)){
            foreach($responde_array as $key=>$v){                
                if(!empty($v)){
                    $responde_values=explode('_',$key);
                    $row=array();
                    $row['responde_estrategia_nid']=get_responde_value($responde_values,0);
                    $row['responde_despliegue_nid']=get_responde_value($responde_values,1);
                    $row['responde_decision_nid']=get_responde_value($responde_values,2);
                    $row['responde_informacion_nid']=get_responde_value($responde_values,3);
                    $result[]=$row;
                }
            }
        }        
    }
    if(isset($_REQUEST['form_id']) && !empty($_REQUEST['form_id'])){
        $my_form_id=$_REQUEST['form_id'];
        if(in_array($my_form_id,array('canal_de_supercanal_node_form','canal_de_yql_node_form','noticia_node_form','debate_node_form','wiki_node_form'))){
            $is_form_save=1;
        }
    }
    return $result;
}
function hontza_is_canal_formulario(){
    $is_simple=0;
    //if(is_crear_canal_de_supercanal() || is_crear_canal_filtro_rss($is_simple)){
    if(is_crear_canal_de_supercanal() || is_crear_canal_filtro_rss($is_simple,0)){
        return 1;
    }
    $node=my_get_node();
    if(isset($node->nid) && !empty($node->nid)){
        if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
            return 1;
        }        
    }
    return 0;
}
function hontza_canal_save_reto_al_que_responde($canal,$is_delete=1){
    if($is_delete){
        hontza_canal_delete_reto_al_que_responde($canal->nid,$canal->vid);
    }
    //echo print_r($canal->estrategia_responde_array,1);exit();
    if(isset($canal->estrategia_responde_array) && !empty($canal->estrategia_responde_array)){
        foreach($canal->estrategia_responde_array as $i=>$row){
            db_query($sql=sprintf('INSERT INTO {canal_estrategia}(nid,vid,responde_estrategia_nid,responde_despliegue_nid,responde_decision_nid,responde_informacion_nid) VALUES(%d,%d,%d,%d,%d,%d)',$canal->nid,$canal->vid,$row['responde_estrategia_nid'],$row['responde_despliegue_nid'],$row['responde_decision_nid'],$row['responde_informacion_nid']));
        }         
    }
}
function hontza_canal_delete_reto_al_que_responde($nid,$vid){
    db_query('DELETE FROM {canal_estrategia} WHERE nid=%d AND vid=%d',$nid,$vid);
}
function hontza_set_terms_link($node,$terms){
    if(!empty($terms)){
        if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
            $glue='href="';
            $my_array=explode($glue,$terms);
            if(!empty($my_array)){
                foreach($my_array as $i=>$s){
                    if($i<1){
                        $aurre=$s; 
                        continue;
                    }
                    $pos=strpos($s,'"');
                    if($pos===FALSE){
                        //
                    }else{
                        $v=substr($s,0,$pos);
                        $link_array=explode('/',$v);
                        $len=count($link_array);
                        if($len>0){
                            $tid=$link_array[$len-1];
                            if(!is_numeric($tid)){
                                $tid=hontza_solr_funciones_get_tid_by_term_class($tid,$aurre);
                            }
                            //$v=url('canales/my_categorias/'.$tid.'/validados');
                            $v=hontza_get_canales_por_categoria_default_url($tid);
                            $my_array[$i]=$v.substr($s,$pos);
                        }
                    }
                    $aurre=$s;                    
                }
            }
            $result=implode($glue,$my_array);
            return $result;
        }
    }
    return $terms;
}
function hontza_get_canales_por_categoria_default_url($tid){   
    $url_cat='canales/my_categorias/'. $tid;
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'", $my_grupo->nid));
        $my_canales=hontza_get_canales_por_categoria($id_categoria, $tid);
        if(!hontza_is_canales_por_categorias_filtrador($my_canales)){
            //$url_cat.='/validados';
            $url_cat.='/ultimas';
        }else{
            $url_cat.='/ultimas';
        }
    }          
    $result=url($url_cat);
    return $result;
}
function hontza_get_enlace_fuente_del_canal($node){
    if(in_array($node->type,array('canal_de_supercanal'))){
        if(isset($node->field_nombrefuente_canal[0]['value'])){
            $fuente=hontza_get_fuente_by_title($node->field_nombrefuente_canal[0]['value']);
            if(isset($fuente->nid) && !empty($fuente->nid)){
                return l($node->field_nombrefuente_canal[0]['value'],'node/'.$fuente->nid);
            }
        }         
    }else if(in_array($node->type,array('canal_de_yql'))){
        if(isset($node->field_nombrefuente_canal[0]['value'])){					
           return l($node->field_nombrefuente_canal[0]['value'],$node->field_nombrefuente_canal[0]['value'],array('absolute' => TRUE,'attributes' => array('target' => '_blank')));
        } 
    }
    if(isset($node->field_nombrefuente_canal[0]['view'])){					
        return $node->field_nombrefuente_canal[0]['view'];
    } 
}
function hontza_get_fuente_by_title($title){
    $node=node_load(array('title'=>$title));
    return $node;
}
function hontza_is_rama_estrategia($node_type){
    if(in_array($node_type,array('estrategia','despliegue','decision','informacion'))){
        return 1;
    }
    return 0;
}    
function hontza_unset_rama_estrategia_menu_links($node_type,$result_in){
    $result=$result_in;
    if($node_type=='estrategia'){
        if(estrategia_is_admin_content()){
            return $result;
        }
    }else if($node_type=='despliegue'){
        if(despliegue_is_admin_content()){
            return $result;
        }
    }else if($node_type=='decision'){
        if(decision_is_admin_content()){
            return $result;
        }
    }else if($node_type=='informacion'){
        if(informacion_is_admin_content()){
            return $result;
        }
    }    
    //
    if(!empty($result)){
        $glue='<li';
        $my_array=explode($glue,$result);
        if(!empty($my_array)){
            $link_array=array();        
            foreach($my_array as $i=>$s){
                if($i>0){
                    //
                    $pos=strpos($s,'/edit');
                    if($pos===FALSE){
                       $link_array[]=$s;
                    }
                }else{
                    $link_array[]=$s;
                }    
            }
            $result=implode($glue,$link_array);
        }
    }
    return $result;
}
function hontza_node_add_debate_link($node){
    //$url='node/add/debate/'.$node->nid;
    //if(user_access('root')){
    $url='node/'.$node->nid.'/enlazar_debate';
    //}
    //return l(t('Discuss'),$url,array('attributes'=>array('target'=>'_blank')));
    //return l(t('Discuss'),$url);
    $label='';
    //$label=t('Discuss');
    //return l($label,$url,array('attributes'=>array('title'=>t('Discuss'),'alt'=>t('Discuss'))));
    return l($label,$url,array('attributes'=>array('target'=>'_blank','title'=>t('Discuss'),'alt'=>t('Discuss'))));
}
function hontza_node_enlazar_debate_callback(){
   drupal_set_title(t('Create a new discussion or link to one discussion')); 
   $output='';
   //intelsat-2015
   if(!hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
    $nid=arg(1);
    $node=node_load($nid);
    $output.=node_view($node);
   } 
    //
    $my_limit=100;    
    $sort='asc';
    $field='node_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
        }else if($order==t('Creator')){
            $field='username';
        }else if($order==t('Creation date')){
            $field='node_created';
        }
    }    
    $sql=hontza_define_debate_list_sql($field,$sort);
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[1]=array('data'=>t('Creator'),'field'=>'username');
    $headers[2]=array('data'=>t('Creation date'),'field'=>'node_created');
    $headers[3]=t('Actions');

    $rows=array();
    //$output.=my_create_boton_boletin_volver();
    //$output.=my_create_boton_boletin_cron();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $is_activado=0;
      $icono_activado=hontza_enlazar_debate_asociado_icono_activado($r,$nid,0,$is_activado);      
      //intelsat-2015
      $icono_activado='';
      if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
         if($is_activado){
             continue;
         } 
      }
      //
      $rows[$kont]=array();      
      $rows[$kont][0]=$icono_activado.'&nbsp;'.$r->node_title;
      $rows[$kont][1]=$r->username;
      $rows[$kont][2]='';
      if(!empty($r->node_created)){
        $rows[$kont][2]=date('Y-m-d',$r->node_created);
      }
      $rows[$kont][3]=hontza_node_enlazar_debate_define_acciones($r,$nid,$is_activado);
      $kont++;
    }
    //
    $new_rows=hontza_define_debate_new_rows($nid);
    $rows=array_merge($new_rows,$rows);
    $rows=my_set_estrategia_pager($rows, $my_limit);

  $output .='<div style="float:left;width:100%;">';
  //$output.=l(t('New'),'node/add/debate/'.$nid);          
      

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
  $output .='</div>';
  
  return $output;
}
function hontza_define_debate_list_sql($field_in='node_created',$sort='desc',$is_ordenar_comentario_fecha=0){
$where=array();
$where[]="1";
//intelsat-2015
$field=$field_in;
if($is_ordenar_comentario_fecha){
    $field='c.last_comment_timestamp';
}
//
$my_grupo=og_get_group_context();
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
}else{
    $where[]="(og_ancestry.group_nid = ".get_grupo_sin_seleccionar_nid().")";
}

$where[]="(node.type in ('debate'))";
$where[]="(node.status <>0)";

$search='';
//
if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
    $search=$_REQUEST['search'];
}
if(!empty($search)){
  $where[]="(node.title LIKE '%%".$search."%%' OR nr.body LIKE '%%".$search."%%')";
}
//intelsat-2016
//Para poner a la izquierda el bloque de categorias tematicas en debates
/*if(is_area_debate()){
  print arg(1);exit();  
}*/
//
$sql="SELECT node.nid AS nid, node.created AS node_created,node.uid as node_uid,node.title AS node_title,u.name AS username
,votingapi_cache_node_average.value AS votingapi_cache_node_average_value     
FROM {node} node
LEFT JOIN {votingapi_cache} votingapi_cache_node_average ON node.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = 'node' AND votingapi_cache_node_average.function = 'average')
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
LEFT JOIN {node_revisions} nr ON node.nid=nr.nid
LEFT JOIN {users} u ON node.uid=u.uid
LEFT JOIN {node_comment_statistics} c ON node.nid=c.nid 
WHERE ".implode(" AND ",$where)."
GROUP BY node.nid";
$order_by=" ORDER BY ".$field." ".$sort;
//intelsat-2015
if(!$is_ordenar_comentario_fecha){
//    
    if($field!='node_created'){
        $order_by.=",node_created ".$sort;
    }
}
$sql.=$order_by;
return $sql;
}
function hontza_node_enlazar_debate_define_acciones($r,$item_nid,$is_activado){
    $html=array();
    //$html[]=l(my_get_icono_action('debate', t('Discuss')),'node/'.$item_nid.'/confirm_enlazar_debate/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination()));
    if($is_activado){
        //$url='node/'.$r->nid;
        $url='comment/reply/'.$r->nid;
        //$url='node/'.$r->nid.'/edit';
        //$html[]=l(my_get_icono_action('debate', t('Discuss')),$url,array('html'=>true,'query'=>'destination=node/'.$r->nid.'#comment-form'));
        $params=array('html'=>true);
        if(!empty($item_nid)){
            $params['query']='item_nid='.$item_nid;
        }
        $html[]=l(my_get_icono_action('debate', t('Link to this discussion')),$url,$params);
        $html[]=l(my_get_icono_action('delete', t('Delete discussion link')),'node/'.$item_nid.'/unlink_debate/'.$r->nid,array('html'=>true));
    }else{    
        if(empty($item_nid)){
            //intelsat-2015
            if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
                $url='node/'.$_REQUEST['node_id_array'].'/confirm_enlazar_debate/'.$r->nid;
            //    
            }else{
                $url='node/add/debate/'.$nid;
            }    
        }else{
            $url='node/'.$item_nid.'/confirm_enlazar_debate/'.$r->nid;
        }   
        $html[]=l(my_get_icono_action('debate', t('Link to this discussion')),$url,array('html'=>true));
    }        
    return implode('&nbsp;',$html);
}
function hontza_confirm_enlazar_debate_form(){
    $form=array();
    $nid=arg(1);
    $item_title='';
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        $item_title=$node->title;
    }
    $debate_title='';
    $debate_nid=arg(3);
    $debate=node_load($debate_nid);
    if(isset($node->nid) && !empty($node->nid)){
        $debate_title=$debate->title;
    }
    //
    $form['item_title']=array(
        '#type'=>'textfield',
        '#title'=>t('News'),
        '#default_value'=>$item_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
     $form['debate_title']=array(
        '#type'=>'textfield',
        '#title'=>t('Discussion'),
        '#default_value'=>$debate_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
    //
    $form['item_nid']=array('#type'=>'hidden','#value'=>$nid);
    $form['debate_nid']=array('#type'=>'hidden','#value'=>$debate_nid);        
    $form['confirm_btn']=array('#type'=>'submit','#name'=>'confirm_btn','#value'=>t('Confirm'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'node/'.$nid.'/enlazar_debate'));     
    return $form;
}
function hontza_confirm_enlazar_debate_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='confirm_btn'){
            $item_nid=$form_state['values']['item_nid'];
            $debate_nid=$form_state['values']['debate_nid'];
            /*hontza_delete_enlazar_debate($debate_nid,$item_nid);
            hontza_insert_enlazar_debate($debate_nid,$item_nid);
            //
            //drupal_goto('node/'.$nid.'/enlazar_debate');
            //drupal_goto('node/'.$debate_nid);
            //drupal_goto('node/'.$debate_nid.'/edit');
            //drupal_goto('comment/reply/'.$debate_nid);
            drupal_goto('comment/reply/'.$debate_nid,array('item_nid'=>$item_nid));*/
            hontza_confirm_enlazar_debate($item_nid,$debate_nid);
        }                
    }
   drupal_goto('vigilancia/pendientes');    
}
function hontza_delete_enlazar_debate($debate_nid,$item_nid){
    db_query('DELETE FROM {enlazar_debate} WHERE debate_nid=%d AND item_nid=%d',$debate_nid,$item_nid);
}
function hontza_insert_enlazar_debate($debate_nid,$item_nid){
    global $user;
    db_query('INSERT INTO {enlazar_debate}(debate_nid,item_nid,enlazar_uid,enlazar_created) VALUES(%d,%d,%d,%d)',$debate_nid,$item_nid,$user->uid,time());
    //gemini-2014
    hontza_validar_con_accion($item_nid);
}
function hontza_enlazar_debate_asociado_icono_activado($r,$item_nid,$is_wiki=0,&$is_activado){
    $is_activado=0;
    if(isset($r->nid) && !empty($r->nid)){
        $debate_nid=$r->nid;
        //intelsat-2015
        if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
            $is_activado=0;
        //    
        }else{
            if($is_wiki){
                if($is_wiki==1){
                    $enlazar_debate_row=hontza_get_enlazar_wiki_row($debate_nid,$item_nid);
                }else if($is_wiki==2){
                    $enlazar_debate_row=idea_get_enlazar_row($debate_nid,$item_nid);
                }                
            }else{
                $enlazar_debate_row=hontza_get_enlazar_debate_row($debate_nid,$item_nid);
            }

            if(isset($enlazar_debate_row->id) && !empty($enlazar_debate_row->id)){
                $is_activado=1;
            }
        }    
    }
    $es_grande=0;
    if($is_activado){
        if($es_grande){
            return my_get_icono_action('active48', t('Active'));
        }else{
            return my_get_icono_action('active', t('Active'));
        }
    }
    //
    if($es_grande){
        return my_get_icono_action('no_active48', t('No active'));
    }else{
        return my_get_icono_action('no_active', t('No active'));
    }
}
function hontza_get_enlazar_debate_row($debate_nid,$item_nid){
    $enlazar_debate_array=hontza_get_enlazar_debate_array($debate_nid,$item_nid);
    if(count($enlazar_debate_array)>0){
        return $enlazar_debate_array[0];
    }
    $content_type_debate_array=hontza_get_content_type_debate_array($debate_nid,$item_nid);
    if(count($content_type_debate_array)>0){
        return $content_type_debate_array[0];
    }    
    $result=new stdClass();
    return $result;
}
function hontza_get_enlazar_debate_array($debate_nid,$item_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($debate_nid)){
        $where[]='debate_nid='.$debate_nid;
    }
    if(!empty($item_nid)){
        $where[]='item_nid='.$item_nid;
    }
    //
    $sql='SELECT * FROM {enlazar_debate} WHERE '.implode(' AND ',$where);
    //print $sql;    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $item_node=node_load($row->item_nid);
        $debate_node=node_load($row->debate_nid);
        if(isset($item_node->nid) && !empty($item_node->nid) && isset($debate_node->nid) && !empty($debate_node->nid)){            
            $result[]=$row;            
        }
    }
    //
    return $result;
}
function hontza_on_debate_save($op,&$node){
    if($node->type=='debate'){
        if($op=='insert'){
            if(isset($node->nid) && !empty($node->nid)){
                if(isset($node->origin_nid) && !empty($node->origin_nid)){
                    //intelsat-2015
                    $origin_nid_array=explode(',',$node->origin_nid);
                    if(count($origin_nid_array)>1){
                        foreach($origin_nid_array as $i=>$origin_nid){
                            hontza_delete_enlazar_debate($node->nid,$origin_nid);
                            hontza_insert_enlazar_debate($node->nid,$origin_nid);
                        }
                    //    
                    }else{    
                        hontza_delete_enlazar_debate($node->nid,$node->origin_nid);
                        hontza_insert_enlazar_debate($node->nid,$node->origin_nid);
                    }
                }
            }
        }
        //intelsat-2015
        estrategia_inc_on_estrategia_responde_save($op,$node);
        //
    }
}
function hontza_get_node_debate_array($nid){
    $content_array=my_get_content_type_debate($nid);
    $enlazar_debate_array=hontza_get_enlazar_debate_array('',$nid); 
    if(!empty($content_array)){
        foreach($content_array as $i=>$row){
            if(!hontza_in_array($row->nid,'debate_nid',$enlazar_debate_array)){
                $debate_node=node_load($row->nid);
                if(isset($debate_node->nid) && !empty($debate_node->nid)){
                    $r=new stdClass();
                    $r->id=-444;
                    $r->debate_nid=$row->nid;
                    $r->item_nid=$nid;
                    $enlazar_debate_array[]=$r;
                }
            }
        }
    }
    return $enlazar_debate_array;
}
function hontza_in_array($value,$field,$my_array){
    if(!empty($my_array)){
        foreach($my_array as $i=>$row){
            if(isset($row->$field)){
                if($value==$row->$field){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function hontza_enlaces_debate_callback(){
    $output='';
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();
    $item_nid=arg(1);
    $my_limit=10;
    //$enlazar_debate_array=hontza_get_enlazar_debate_array('',$item_nid);
    $enlazar_debate_array=hontza_get_all_enlazar_debate_array('',$item_nid);
    $rows=array();
    $kont=0;
    if(count($enlazar_debate_array)>0){
        foreach($enlazar_debate_array as $i=>$row){
            $rows[$kont]=new stdClass();
            $node=node_load($row->debate_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]->view=node_view($node);
                $kont++;
            }
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  //intelsat-2015
  drupal_set_title(t('Discussion'));
  //
  return $output;
}
function hontza_debate_origin_link($node){
    /*if(my_is_existe_enlace_origen($node->field_enlace_debate[0]['url'])){
        return '<a href="'.$node->field_enlace_debate[0]['url'].'">'.t('Origin').'</a>';*/
    //$enlazar_debate_array=hontza_get_enlazar_debate_array($node->nid,'');
    $enlazar_debate_array=hontza_get_all_enlazar_debate_array($node->nid,'');
    if(count($enlazar_debate_array)>0){
        $url='node/'.$node->nid.'/origenes_debate';
    }else{                        
        $url='no_existe_enlace_origen_debate';		
    }
    //return l(t('Origin'),$url);
    return l('',$url,array('attributes'=>array('title'=>t('Origin'),'alt'=>t('Origin'))));
}
function hontza_origenes_debate_callback(){
    drupal_set_title(t('Origin of Discussion'));
    $output='';
    $debate_nid=arg(1);
    $my_limit=10;
    //$enlazar_debate_array=hontza_get_enlazar_debate_array($debate_nid,'');
    $enlazar_debate_array=hontza_get_all_enlazar_debate_array($debate_nid,'');
    $rows=array();
    $kont=0;
    if(count($enlazar_debate_array)>0){
        foreach($enlazar_debate_array as $i=>$row){
            $node=node_load($row->item_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]=new stdClass();
                $rows[$kont]->view=node_view($node);
                $kont++;
            }    
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
   
  return $output;
}
function hontza_node_add_wiki_link($node){
    //$url='node/add/wiki/'.$node->nid;
    //if(user_access('root')){
    $url='node/'.$node->nid.'/enlazar_wiki';
    //}
    //return l(t('Collaborate'),$url,array('attributes'=>array('target'=>'_blank')));
    //return l(t('Collaborate'),$url);
    $label='';
    //$label=t('Collaborate');
    //return l($label,$url,array('attributes'=>array('title'=>t('Collaborate'),'alt'=>t('Collaborate'))));
    return l($label,$url,array('attributes'=>array('target'=>'_blank','title'=>t('Collaborate'),'alt'=>t('Collaborate'))));
}
function hontza_node_enlazar_wiki_callback(){
   drupal_set_title(t('Create a new wiki or link to one wiki')); 
   $output='';
   //intelsat-2015
   if(!hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
    $nid=arg(1);
    $node=node_load($nid);
    $output.=node_view($node);
   } 
    //
    $my_limit=100;    
    $sort='asc';
    $field='node_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
        }else if($order==t('Creator')){
            $field='username';
        }else if($order==t('Creation date')){
            $field='node_created';
        }
    }    
    $sql=hontza_define_wiki_list_sql($field,$sort);
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[1]=array('data'=>t('Creator'),'field'=>'username');
    $headers[2]=array('data'=>t('Creation date'),'field'=>'node_created');
    $headers[3]=t('Actions');

    $rows=array();
    //$output.=my_create_boton_boletin_volver();
    //$output.=my_create_boton_boletin_cron();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $is_activado=0;
      $icono_activado=hontza_enlazar_wiki_asociado_icono_activado($r,$nid,$is_activado);
      //intelsat-2015
      $icono_activado='';
      //
      $rows[$kont][0]=$icono_activado.'&nbsp;'.$r->node_title;
      $rows[$kont][1]=$r->username;
      $rows[$kont][2]='';
      if(!empty($r->node_created)){
        $rows[$kont][2]=date('Y-m-d',$r->node_created);
      }
      $rows[$kont][3]=hontza_node_enlazar_wiki_define_acciones($r,$nid,$is_activado);
      $kont++;
    }
    //
    $new_rows=hontza_define_wiki_new_rows($nid);
    $rows=array_merge($new_rows,$rows);
    $rows=my_set_estrategia_pager($rows, $my_limit);

  $output .='<div style="float:left;width:100%;">';
  //$output.=l(t('New'),'node/add/wiki/'.$nid);          
      

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
  $output .='</div>';
  
  return $output;
}
function hontza_define_wiki_list_sql($field_in='node_created',$sort='desc',$is_ordenar_comentario_fecha=0){
$where=array();
$where[]="1";
//intelsat-2015
$field=$field_in;
if($is_ordenar_comentario_fecha){
    //$field='c.last_comment_timestamp';
    $field='node.changed';
}
//
$my_grupo=og_get_group_context();
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
    $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
}else{
    //AVISO::::para que cuando el grupo esté sin seleccionar devuelva una lista vacía
    $where[]="(og_ancestry.group_nid = ".get_grupo_sin_seleccionar_nid().")";
}

$where[]="(node.type in ('wiki'))";
$where[]="(node.status <>0)";

$search='';
//
if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
    $search=$_REQUEST['search'];
}
if(!empty($search)){
  $where[]="(node.title LIKE '%".$search."%' OR nr.body LIKE '%".$search."%')";
}
//
$sql="SELECT node.nid AS nid, node.created AS node_created,node.title AS node_title,u.name AS username
,votingapi_cache_node_average.value AS votingapi_cache_node_average_value    
FROM {node} node
LEFT JOIN {votingapi_cache} votingapi_cache_node_average ON node.nid = votingapi_cache_node_average.content_id AND (votingapi_cache_node_average.content_type = 'node' AND votingapi_cache_node_average.function = 'average')
LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
LEFT JOIN {node_revisions} nr ON node.nid=nr.nid 
LEFT JOIN {users} u ON u.uid=node.uid
LEFT JOIN {node_comment_statistics} c ON node.nid=c.nid 
WHERE ".implode(" AND ",$where)." 
GROUP BY node.nid";
$order_by=" ORDER BY ".$field." ".$sort;
//intelsat-2015
if(!$is_ordenar_comentario_fecha){
//    
    if($field!='node_created'){
        $order_by.=",node_created ".$sort;
    }
}    
$sql.=$order_by;
return $sql;
}
function hontza_get_enlazar_wiki_row($wiki_nid,$item_nid){
    $enlazar_wiki_array=hontza_get_enlazar_wiki_array($wiki_nid,$item_nid);
    if(count($enlazar_wiki_array)>0){
        return $enlazar_wiki_array[0];
    }
    $content_type_wiki_array=hontza_get_content_type_wiki_array($wiki_nid,$item_nid);
    if(count($content_type_wiki_array)>0){
        return $content_type_wiki_array[0];
    }  
    $result=new stdClass();
    return $result;
}
function hontza_enlazar_wiki_asociado_icono_activado($r,$nid,&$is_activado){
    return hontza_enlazar_debate_asociado_icono_activado($r,$nid,1,$is_activado);
}
function hontza_get_enlazar_wiki_array($wiki_nid,$item_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($wiki_nid)){
        $where[]='wiki_nid='.$wiki_nid;
    }
    if(!empty($item_nid)){
        $where[]='item_nid='.$item_nid;
    }
    //
    $res=db_query('SELECT * FROM {enlazar_wiki} WHERE '.implode(' AND ',$where));
    while($row=db_fetch_object($res)){
        $item_node=node_load($row->item_nid);
        $wiki_node=node_load($row->wiki_nid);
        if(isset($item_node->nid) && !empty($item_node->nid) && isset($wiki_node->nid) && !empty($wiki_node->nid)){
            $result[]=$row;
        }    
    }
    //
    return $result;
}
function hontza_node_enlazar_wiki_define_acciones($r,$item_nid,$is_activado){
    $html=array();
    //intelsat-2015
    if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
        $url='node/'.$_REQUEST['node_id_array'].'/confirm_enlazar_wiki/'.$r->nid; 
    //    
    }else{
        $url='node/'.$item_nid.'/confirm_enlazar_wiki/'.$r->nid;        
    }
    if($is_activado){
        //$url='node/'.$r->nid;
        //$url='comment/reply/'.$r->nid;
        $url='node/'.$r->nid.'/edit';
        //$html[]=l(my_get_icono_action('trabajo', t('Collaborate')),$url,array('html'=>true,'query'=>'destination=node/'.$r->nid.'#comment-form'));
        $html[]=l(my_get_icono_action('trabajo', t('Link to this wiki')),$url,array('html'=>true));
        $html[]=l(my_get_icono_action('delete', t('Delete collaboration link')),'node/'.$item_nid.'/unlink_wiki/'.$r->nid,array('html'=>true));
    }else{    
        $html[]=l(my_get_icono_action('trabajo', t('Link to this wiki')),$url,array('html'=>true));    
    }
    return implode('&nbsp;',$html);
}
function hontza_confirm_enlazar_wiki_form(){
    $form=array();
    $nid=arg(1);
    $item_title='';
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        $item_title=$node->title;
    }
    $wiki_title='';
    $wiki_nid=arg(3);
    $wiki=node_load($wiki_nid);
    if(isset($node->nid) && !empty($node->nid)){
        $wiki_title=$wiki->title;
    }
    //
    $form['item_title']=array(
        '#type'=>'textfield',
        '#title'=>t('News'),
        '#default_value'=>$item_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
     $form['wiki_title']=array(
        '#type'=>'textfield',
        '#title'=>t('Collaboration'),
        '#default_value'=>$wiki_title,
        '#attributes' => array('readonly' => 'readonly'),
        '#maxlength' => 256,
    );
    //
    $form['item_nid']=array('#type'=>'hidden','#value'=>$nid);
    $form['wiki_nid']=array('#type'=>'hidden','#value'=>$wiki_nid);        
    $form['confirm_btn']=array('#type'=>'submit','#name'=>'confirm_btn','#value'=>t('Confirm'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'node/'.$nid.'/enlazar_wiki'));     
    return $form;
}
function hontza_confirm_enlazar_wiki_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='confirm_btn'){
            $item_nid=$form_state['values']['item_nid'];
            $wiki_nid=$form_state['values']['wiki_nid'];
            /*hontza_delete_enlazar_wiki($wiki_nid,$item_nid);
            hontza_insert_enlazar_wiki($wiki_nid,$item_nid);
            //
            //drupal_goto('node/'.$nid.'/enlazar_debate');
            //drupal_goto('node/'.$wiki_nid);
            //drupal_goto('comment/reply/'.$wiki_nid);
            drupal_goto('node/'.$wiki_nid.'/edit');*/
            hontza_confirm_enlazar_wiki($item_nid,$wiki_nid);
        }                
    }
   drupal_goto('vigilancia/pendientes');    
}
function hontza_delete_enlazar_wiki($wiki_nid,$item_nid){
    db_query('DELETE FROM {enlazar_wiki} WHERE wiki_nid=%d AND item_nid=%d',$wiki_nid,$item_nid);
}
function hontza_insert_enlazar_wiki($wiki_nid,$item_nid){
    global $user;
    db_query('INSERT INTO {enlazar_wiki}(wiki_nid,item_nid,enlazar_uid,enlazar_created) VALUES(%d,%d,%d,%d)',$wiki_nid,$item_nid,$user->uid,time());
    //gemini-2014
    hontza_validar_con_accion($item_nid);
}
function hontza_get_node_wiki_array($nid){
    $content_array=my_get_content_type_wiki($nid);
    $enlazar_wiki_array=hontza_get_enlazar_wiki_array('',$nid);
    if(!empty($content_array)){
        foreach($content_array as $i=>$row){
            if(!hontza_in_array($row->nid,'wiki_nid',$enlazar_wiki_array)){
                $wiki_node=node_load($row->nid);
                if(isset($wiki_node->nid) && !empty($wiki_node->nid)){
                    $r=new stdClass();
                    $r->id=-444;
                    $r->debate_nid=$row->nid;
                    $r->item_nid=$nid;
                    $enlazar_wiki_array[]=$r;
                }    
            }
        }
    }
    return $enlazar_wiki_array;
}
function hontza_enlaces_wiki_callback(){
    $output='';
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();
    $item_nid=arg(1);
    $my_limit=10;
    //$enlazar_wiki_array=hontza_get_enlazar_wiki_array('',$item_nid);
    $enlazar_wiki_array=hontza_get_all_enlazar_wiki_array('',$item_nid);
    $rows=array();
    $kont=0;
    if(count($enlazar_wiki_array)>0){
        foreach($enlazar_wiki_array as $i=>$row){
            $rows[$kont]=new stdClass();            
            $node=node_load($row->wiki_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]->view=node_view($node);
                $kont++;
            }  
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  //intelsat-2015
  drupal_set_title(t('Collaboration'));
  // 
  return $output;
}
function hontza_wiki_origin_link($node){
    //$enlazar_wiki_array=hontza_get_enlazar_wiki_array($node->nid,'');
    $enlazar_wiki_array=hontza_get_all_enlazar_wiki_array($node->nid,'');
    if(count($enlazar_wiki_array)>0){
        $url='node/'.$node->nid.'/origenes_wiki';
    }else{                        
        $url='no_existe_enlace_origen_wiki';		
    }
    //$label=t('Origin');
    $label='';
    return l($label,$url,array('attributes'=>array('title'=>t('Origin'),'alt'=>t('Origin'))));	
}
function hontza_origenes_wiki_callback(){
    drupal_set_title(t('Origin of Collaborative Document'));
    $output='';
    $wiki_nid=arg(1);
    $my_limit=10;
    //$enlazar_wiki_array=hontza_get_enlazar_wiki_array($wiki_nid,'');
    $enlazar_wiki_array=hontza_get_all_enlazar_wiki_array($wiki_nid,'');
    $rows=array();
    $kont=0;
    if(count($enlazar_wiki_array)>0){
        foreach($enlazar_wiki_array as $i=>$row){
            $node=node_load($row->item_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $rows[$kont]=new stdClass();
                $rows[$kont]->view=node_view($node);
                $kont++;
            }    
        }
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $output.=set_array_view_html($rows);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    //$output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
   
  return $output;
}
function hontza_on_wiki_save($op,&$node){
    if($node->type=='wiki'){
        if($op=='insert'){
            if(isset($node->nid) && !empty($node->nid)){
                if(isset($node->origin_nid) && !empty($node->origin_nid)){
                    //intelsat-2015
                    $origin_nid_array=explode(',',$node->origin_nid);
                    $origin_nid_array=hontza_solr_funciones_get_node_id_array_by_arg($origin_nid_array);
                    if(count($origin_nid_array)>1){
                        foreach($origin_nid_array as $i=>$origin_nid){
                            hontza_delete_enlazar_wiki($node->nid,$origin_nid);
                            hontza_insert_enlazar_wiki($node->nid,$origin_nid);
                        }
                    //    
                    }else{
                        hontza_delete_enlazar_wiki($node->nid,$node->origin_nid);
                        hontza_insert_enlazar_wiki($node->nid,$node->origin_nid);
                    }    
                }
            }
        }
        //intelsat-2015
        estrategia_inc_on_estrategia_responde_save($op,$node);
        //intelsat-2016
        red_copiar_on_wiki_save($op,$node);
        //
    }
}
function hontza_debate_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_wiki_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_estrategia_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_despliegue_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_decision_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_informacion_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_idea_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_oportunidad_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_proyecto_resumen($node){
    return hontza_content_resumen($node);
}
function hontza_in_pantallas_enlace_debate($is_wiki=0,$pantallas_array_in=array()){
    $pantallas_array=$pantallas_array_in;
    if(empty($pantallas_array)){
        $pantallas_array=array('enlazar_debate','confirm_enlazar_debate','enlaces_debate');
    }
    if($is_wiki){
        if($is_wiki==1){
            $pantallas_array=array('enlazar_wiki','confirm_enlazar_wiki','enlaces_wiki');
        }else if($is_wiki==2){
            $pantallas_array=array('origenes_debate');
        }else if($is_wiki==3){
            $pantallas_array=array('origenes_wiki');
        }else if($is_wiki==4){
            $pantallas_array=array('origenes_idea');
        }else if($is_wiki==5){
            $pantallas_array=array('enlazar_idea','confirm_enlazar_idea','enlaces_idea');
        }            
    }
    //
    $param0=arg(0);
    if(!empty($param0) && $param0=='node'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && in_array($param2,$pantallas_array)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_in_pantallas_enlace_wiki(){
    return hontza_in_pantallas_enlace_debate(1);
}
function hontza_is_origenes_debate(){
    return hontza_in_pantallas_enlace_debate(2);
}
function hontza_is_origenes_wiki(){
    return hontza_in_pantallas_enlace_debate(3);
}
function hontza_get_content_type_debate_array($debate_nid,$item_nid){
    $result=array();
    $content_type_debate_array=my_get_content_type_debate($item_nid,$debate_nid);
    if(!empty($content_type_debate_array)){
        foreach($content_type_debate_array as $i=>$row){
            $r=new stdClass();
            $r->id=-444;
            $r->debate_nid=$row->nid;
            if(!empty($item_nid)){
                $r->item_nid=$item_nid;
            }else{
                $r->item_nid=hontza_get_nid_by_field_enlace_debate($row);
            }
            $item_node=node_load($r->item_nid);
            $debate_node=node_load($r->debate_nid);
            if(isset($item_node->nid) && !empty($item_node->nid) && isset($debate_node->nid) && !empty($debate_node->nid)){
                $result[]=$r;
            }            
        }    
    }
    return $result;
}
function hontza_get_all_enlazar_debate_array($debate_nid,$item_nid){
    $enlazar_debate_array=hontza_get_enlazar_debate_array($debate_nid,$item_nid);
    $content_array=hontza_get_content_type_debate_array($debate_nid, $item_nid);
    if(!empty($content_array)){
        foreach($content_array as $i=>$row){
            if(!hontza_in_array($row->debate_nid,'debate_nid',$enlazar_debate_array)){
                $enlazar_debate_array[]=$row;
            }
        }
    }
    return $enlazar_debate_array;
}
function hontza_get_content_type_wiki_array($wiki_nid,$item_nid){
    $result=array();
    $content_type_wiki_array=my_get_content_type_wiki($item_nid,$wiki_nid);
    if(!empty($content_type_wiki_array)){
        foreach($content_type_wiki_array as $i=>$row){
            $r=new stdClass();
            $r->id=-444;
            $r->wiki_nid=$row->nid;
            if(!empty($item_nid)){
                $r->item_nid=$item_nid;
            }else{
                $r->item_nid=hontza_get_nid_by_field_enlace_wiki($row);
            }
            //
            $item_node=node_load($r->item_nid);
            $wiki_node=node_load($r->wiki_nid);
            if(isset($item_node->nid) && !empty($item_node->nid) && isset($wiki_node->nid) && !empty($wiki_node->nid)){
                $result[]=$r;
            }
        }    
    }
    return $result;
}
function hontza_get_all_enlazar_wiki_array($wiki_nid,$item_nid){
    $enlazar_wiki_array=hontza_get_enlazar_wiki_array($wiki_nid,$item_nid);
    $content_array=hontza_get_content_type_wiki_array($wiki_nid, $item_nid);
    if(!empty($content_array)){
        foreach($content_array as $i=>$row){
            if(!hontza_in_array($row->wiki_nid,'wiki_nid',$enlazar_wiki_array)){
                $enlazar_wiki_array[]=$row;
            }
        }
    }
    return $enlazar_wiki_array;
}
function hontza_is_con_botonera(){
    if(hontza_in_pantallas_enlace_debate(0,array('enlazar_debate','enlazar_wiki','enlazar_idea','noticia_boletines'))){
        return 0;
    }
    if(hontza_is_comment_reply()){
        return 0;
    }
    if(boletin_report_pdf_inc_is_pdf_node()){
        return 0;
    }
    return 1;
}
function hontza_define_debate_new_rows($nid,$is_wiki=0){
    global $user;    
    $new_rows=array();
    $new_rows[0]=array();
    /*$is_activado=0;
    $icono_activado=hontza_enlazar_debate_asociado_icono_activado('',0,0,$is_activado);
    $new_rows[0][0]=$icono_activado.'&nbsp;'.t('Create a new discussion');
    $new_rows[0][1]=$user->name;
    $new_rows[0][2]=date('Y-m-d');
    $new_rows[0][3]=hontza_node_enlazar_debate_define_acciones('',0,$is_activado);*/
    $new_rows[0][0]='';
    $new_rows[0][1]='';
    $new_rows[0][2]='';
    if($is_wiki){
        if($is_wiki==1){
            //intelsat-2015
            $icono=my_get_icono_action('add_left',t('Create new wiki'));                
            if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
                $new_rows[0][3]=l($icono,'node/add/wiki/'.$_REQUEST['node_id_array'],array('html'=>TRUE)).'&nbsp;';
            }else{
                $new_rows[0][3]=l($icono,'node/add/wiki/'.$nid,array('html'=>TRUE)).'&nbsp;';
            }    
        }else if($is_wiki==2){
            //intelsat-2015
            $icono=my_get_icono_action('add_left',t('Create new idea'));
            if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
                $new_rows[0][3]=l($icono,'node/add/idea/'.$_REQUEST['node_id_array'],array('html'=>TRUE)).'&nbsp;';
            //    
            }else{
                $new_rows[0][3]=l($icono,'node/add/idea/'.$nid,array('html'=>TRUE)).'&nbsp;';
            }    
        }            
    }else{
        //intelsat-2015
        $icono=my_get_icono_action('add_left',t('Create new discussion')).'&nbsp;';
        if(hontza_solr_funciones_is_pantalla_bookmark_multiple_mode()){
            $new_rows[0][3]=l($icono,'node/add/debate/'.$_REQUEST['node_id_array'],array('html'=>TRUE)).'&nbsp;';
        //    
        }else{
            $new_rows[0][3]=l($icono,'node/add/debate/'.$nid,array('html'=>TRUE)).'&nbsp;';      
        }    
    }
    return $new_rows;
}
function hontza_define_wiki_new_rows($nid){
    return hontza_define_debate_new_rows($nid,1);
}
function link_import_canal($nid){
    //return '';
    global $user,$base_url;
    $label=t('Update channel');
    $html=array();
    //if(is_administrador_grupo()){
    $link='';
    $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/import_canal.png';
    $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>'; 
    if(is_user_administrador_de_grupo()){
        $html[]= '<span class="link_import_canal">';
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $url='node/'. $nid .'/import';
            $job=hontza_define_job_param($nid);
            if(hontza_is_empty_feeds_source($job)){
                $url='no_existe_feed_source';
            }
            $html[]=l($img,$url,array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));
        }else{
            return '';
        }
        $html[]= '</span>';
    }
    return implode('',$html);
}
function hontza_get_nid_by_field_enlace_debate($row,$field='field_enlace_debate_url'){
    if(isset($row->$field)){
        $my_array=explode('/',$row->$field);
        $len=count($my_array);
        if($len>0){
            return $my_array[$len-1];
        }
    }
    return 0;
}
function hontza_get_nid_by_field_enlace_wiki($row){
    return hontza_get_nid_by_field_enlace_debate($row,'field_enlace_wiki_url');
}
function hontza_is_show_node_titulo($node){
   if(in_array($node->type,array('item','noticia'))){
       return 0;
   } 
   if(hontza_is_comment_reply()){
       return 1;
   }
   return 0;
}
function hontza_is_comment_reply(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='comment'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='reply'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_node_titulo_html($node){
    $html=array();
    $html[]='<div id="flagtitulo">';
    $html[]='<div class="f-titulo"><h2>'.l(htmlkarakter($node->title),'node/'.$node->nid).'</h2></div>';
    $html[]='</div>';
    return implode('',$html);
}
function hontza_save_canal_last_import_time_by_feeds_source($source){
    $last_import_time=0;
    //if(user_access('root')){
        $canal=node_load($source->feed_nid);
        if(isset($canal->nid) && !empty($canal->nid)){            
            if(!(isset($canal->field_last_import_time) && isset($canal->field_last_import_time[0]) && isset($canal->field_last_import_time[0]['value']))){
                $canal->field_last_import_time=array();
                $canal->field_last_import_time[0]=array();                
            }
            $last_import_time=time();
            $canal->field_last_import_time[0]['value']=$last_import_time;
            //intelsat-2014
            if(hontza_solr_is_solr_activado()){
                hontza_solr_set_canal_source_info($canal);
            }
            //
            node_save($canal);
            //hontza_update_hound_feeds_source($source->feed_nid,$last_import_time);            
        }        
    //}
    return $last_import_time;    
}
function get_canal_last_import_time_format($node,$is_time=0){
    $last_import_time=get_canal_last_import_time($node);
    if(!empty($last_import_time)){
        if($is_time){
            return $last_import_time;
        }else{
            return date( 'd-m-Y H:i',$last_import_time);
        }    
    }
    return '';
}
function get_canal_last_import_time($canal){
    if(isset($canal->field_last_import_time) && isset($canal->field_last_import_time[0]) && isset($canal->field_last_import_time[0]['value'])){
        if(!empty($canal->field_last_import_time[0]['value'])){
            return $canal->field_last_import_time[0]['value'];
        }
    }
    return '';
}
function hontza_user_login_custom(){
    global $user;
   
    if(isset($user->uid) && !empty($user->uid)){
        $output=hontza_user_login_custom_menu(1);        
    }else{
         $output=hontza_user_login_custom_menu(0);
        $output.=drupal_get_form('hontza_user_login_custom_form');
    }
    return $output;
}
function hontza_user_login_custom_form(){
    $form=user_login_block();
    if(isset($_REQUEST['is_login_custom']) && !empty($_REQUEST['is_login_custom'])){
        $form['#attributes']['target'] = '_blank'; 
    }           
    $form['#action'] = url($_GET['q'], array('query' => 'destination=user'));
    unset($form['links']);
    return $form;
}
function hontza_is_user_login_custom(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='hontza_user_login_custom'){
        return 1;
    }    
    return 0;
}
function hontza_user_login_custom_menu($is_logged=0){
global $base_url;    
$html=array();
$html[]='<div class="tab-wrapper clearfix primary-only">';
$html[]='<div class="tabs primary" id="tabs-primary">';
$html[]='<ul>';

if($is_logged){
    $html[]='<li>'.l(t('Enter'),$base_url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank'))).'</li>';
    $html[]='<li>'.l(t('Logout'),'logout').'</li>';   
}else{
    if (variable_get('user_register', 1)) {
        $html[]='<li>'.l(t('Create new account'), 'user/register', array('attributes' => array('title' => t('Create a new user account.'),'target'=>'_blank'))).'</li>';
    }
    $html[]='<li>'.l(t('Request new password'), 'user/password', array('attributes' => array('title' => t('Request new password via e-mail.'),'target'=>'_blank'))).'</li>';   
}
$html[]='</div>';
$html[]='</div>';
return implode('',$html);
}
/*
function hontza_user_login_custom_form_submit($form, &$form_state){
    drupal_goto('home');
}*/
function hontza_get_gestion_creador_links_string(){
    $links='';
    $links .='<li>|&nbsp;'.l(t('Management of Groups'),'user-gestion/grupos/propios').'</li>';
    if(!hontza_is_sareko_id('ROOT') && hontza_user_access_gestion_usuarios()){
        $links .='<li>|&nbsp;'.l(t('List of Users'),'gestion/usuarios').'</li>';
    }
    return $links;
}
function gestion_usuarios_callback(){
    
   //if(!user_access('administer users') || !hontza_is_creador_de_grupo()){
   if(!hontza_user_access_gestion_usuarios()){ 
       drupal_access_denied();
       exit();
   }
    
   //$filter_fields=array('profile_values_profile_nombre_value');
   $filter_fields=hontza_define_gestion_usuarios_filter_fields();
   
   $where=array();
   $where[]="1";
   $where[]="users.uid not in ('0', '1')";
   //intelsat-2016
   $or_array=array();
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f);
           //intelsat-2016
           if(module_exists('gestion_canales')){
                $v=gestion_canales_get_grupo_nid_any_value($v,$f);
           }
           if(!empty($v)){
                switch($f){
                    case 'users_name':
                        //$where[]="users.name LIKE '%".$v."%'";
                        $or_array=array();
                        $or_array[]="users.name LIKE '%".$v."%'";
                        $or_array[]="users.name LIKE '".$v."%'";
                        $or_array[]="users.name LIKE '%".$v."'";
                        $where[]='('.implode(' OR ',$or_array).')';
                        break;
                    case 'profile_values_profile_nombre_value':
                        $where[]="profile_values_profile_nombre.value LIKE '%%".$v."%%'";
                        break;
                    case 'profile_values_profile_apellidos_value':
                        $where[]="profile_values_profile_apellidos.value LIKE '%%".$v."%%'";
                        break;
                    case 'profile_values_profile_empresa_value':
                        $where[]="profile_values_profile_empresa.value LIKE '%%".$v."%%'";
                        break;
                    //intelsat-2016
                    case 'grupo_nid':
                        $grupo_nid_array=array_keys($v);
                        $where[]='og_uid.nid IN('.implode(',',$grupo_nid_array).')';
                        break;
                    case 'mail':
                        $where[]="users.mail LIKE '%%".$v."%%'";
                        break;
                    default:
                        break;
                }
           } 
       }
   }
   
    $my_limit=40;
    //simulando
    //$my_limit=4000;
        
    $sort='desc';
    $field='users_created';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Username')){
            $field='users_name';
        }else if($order==t('Name')){
            $field='profile_values_profile_nombre_value';
        }else if($order==t('Surname')){
            $field='profile_values_profile_apellidos_value';
        }else if($order==t('E-mail')){
            $field='users_mail';
        }  
    }
    
    $sql="SELECT users.uid AS uid,
   users.name AS users_name,
   profile_values_profile_nombre.value AS profile_values_profile_nombre_value,
   profile_values_profile_nombre.uid AS profile_values_profile_nombre_uid,
   profile_values_profile_apellidos.value AS profile_values_profile_apellidos_value,
   profile_values_profile_empresa.value AS profile_values_profile_empresa_value,
   users.mail AS users_mail,
   users.status AS users_status,
   users.created AS users_created
 FROM {users} users 
 LEFT JOIN {profile_values} profile_values_profile_nombre ON users.uid = profile_values_profile_nombre.uid AND profile_values_profile_nombre.fid = '1'
 LEFT JOIN {profile_values} profile_values_profile_apellidos ON users.uid = profile_values_profile_apellidos.uid AND profile_values_profile_apellidos.fid = '2'
 LEFT JOIN {profile_values} profile_values_profile_empresa ON users.uid = profile_values_profile_empresa.uid AND profile_values_profile_empresa.fid = '3' 
 LEFT JOIN {og_uid} og_uid ON users.uid=og_uid.uid 
 WHERE ".implode(" AND ",$where)."
   GROUP BY users.uid   
   ORDER BY ".$field." ".strtoupper($sort);
    //
    /*if(user_access('root')){
        print $sql;
    }*/
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';    
    $headers[1]=array('data'=>t('Username'),'field'=>'users_name');
    //$headers[2]=array('data'=>t('Name'),'field'=>'profile_values_profile_nombre_value');    
    //$headers[4]=array('data'=>t('Surname'),'field'=>'profile_values_profile_apellidos_value');
    //$headers[5]=array('data'=>t('E-mail'),'field'=>'users_mail');    
    $headers[2]=t('Group');
    $headers[3]=t('Roles');
    //$headers[5]=t('Active');
    //$headers[4]=t('Creation Date');
    $headers[4]=t('Days');    
    $headers[5]=t('Actions');
    
    
    $rows=array();
    $kont=0;
    //intelsat-2015
    $faktore=50;
    //intelsat-2016
    $my_user_array=array();
    //
    while ($r = db_fetch_object($res)) {
      //intelsat-2015
      $my_user=user_load($r->uid);
      //intelsat-2016
      $my_user_array[]=$my_user;
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->uid.'" name="txek_uid['.$r->uid.']" class="bulk_txek" value="1">';            
      //gemini-2014
      //$rows[$kont][2]=l($r->users_name,'users/'.$r->users_name);
      //gemini-2014
      //$rows[$kont][3]=l($r->profile_values_profile_nombre_value,'users/'.$r->users_name);
      //$rows[$kont][3]=l($r->profile_values_profile_nombre_value,'user/'.$r->uid);
      //$rows[$kont][4]=$r->profile_values_profile_apellidos_value;
      //$rows[$kont][5]=l($r->users_mail,'mailto:'.$r->users_mail);
      //$rows[$kont][5]=l(hontza_fix_mail_string_td($r->users_mail),'mailto:'.$r->users_mail,array('attributes'=>array('title'=>$r->users_mail)));
      //$rows[$kont][5]=l(my_get_icono_action('users_email',$r->users_mail),'mailto:'.$r->users_mail,array('html'=>true));
      //intelsat-2015
      //intelsat-2015
      $user_image=l($r->users_name,'user/'.$r->uid).'<br>'.panel_admin_get_gestion_usuarios_name($r,$my_user).'<br>'.hontza_grupos_mi_grupo_get_user_img($r->uid,$faktore);      
      //$rows[$kont][2]=panel_admin_get_gestion_usuarios_name($r);
      $rows[$kont][1]=$user_image;      
      //
      $rows[$kont][2]=hontza_get_user_group_string($r->uid);
      $rows[$kont][3]=hontza_get_user_roles_li($r->uid);
      //$rows[$kont][5]=hontza_get_active_string($r->users_status);
      //intelsat-2015
      //$rows[$kont][4]='<i>'.format_interval(time() - $r->users_created).'</i>';
      $rows[$kont][4]='';
      if(module_exists('canal_usuario')){
        $rows[$kont][4]=canal_usuario_get_days($r->users_created);
      }
      //
      //intelsat-2015
      //$rows[$kont][5]=array('data'=>hontza_define_gestion_usuarios_acciones($r->uid,$r->users_status),'style'=>'white-space:nowrap;');
      $rows[$kont][5]=array('data'=>hontza_define_gestion_usuarios_acciones($r->uid,$r->users_status,$r),'style'=>'white-space:nowrap;');            
      
      $kont++;
    }
    //intelsat-2016
    red_crear_usuario_gestion_usuarios_descargar_usuarios($my_user_array);
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);

  //$output.=hontza_define_gestion_usuarios_header();
  
  //$output.=hontza_define_gestion_usuarios_bulk_operations();
    
  //$output .='<div style="float:left;width:100%;">';
  if (count($rows)>0) {
    $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
  //$output .='</div>';
    //intelsat-2015
    drupal_set_title(t('Management of Users'));
    //
    //return $output;
    return hontza_define_gestion_usuarios_header().drupal_get_form('hontza_gestion_usuarios_bulk_form',array($output));
}
function hontza_get_user_group_string($uid){
    $result=array();
    $grupo_assoc=get_usuario_grupos_options($uid);
    if(!empty($grupo_assoc)){
        foreach($grupo_assoc as $grupo_nid=>$grupo_title){
            $result[]=l($grupo_title,'node/'.$grupo_nid);
        }
    }
    //$glue=',';
    $glue='<BR>';
    return implode($glue,$result);
}
function hontza_get_user_roles_li($uid){
    /*$html=array();
    $user_roles_array=hontza_get_user_roles_array($uid);
    if(!empty($user_roles_array)){
        //$html[]='<ul>';
        foreach($user_roles_array as $i=>$row){
            //$html[]='<li>'.$row->name.'</li>';
            $html[]=$row->name.'<br>';
        }
        //$html[]='</ul>';
    }
    //return implode(',',$html);
    return implode('',$html);*/
    return red_funciones_get_user_roles_li($uid);    
}
function hontza_get_user_roles_array($uid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($uid)){
        $where[]='ur.uid='.$uid;
    }
    $sql='SELECT r.* 
    FROM {users_roles} ur 
    LEFT JOIN {role} r ON ur.rid=r.rid 
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    //
    return $result;
}
function hontza_get_active_string($value,$is_icono=0){
    if(!empty($value)){
       //intelsat-2015
       if($is_icono){
        return 1;
       }else{ 
        return t('Yes');
       } 
    }
    //intelsat-2015
    if($is_icono){
        return 0;
    }else{
        return t('No');
    }    
}
//intelsat-2015
//function hontza_define_gestion_usuarios_acciones($uid,$users_status){
function hontza_define_gestion_usuarios_acciones($uid,$users_status,$r=''){
    $html=array();
    $destination='destination=gestion/usuarios';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'user/'.$uid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'user/'.$uid.'/delete',array('query'=>$destination,'html'=>true));
    //intelsat-2015
    $html[]=l(my_get_icono_action('edit_roles',t('Modify roles')),'panel_admin/'.$uid.'/edit_user_roles',array('html'=>true));
    $html[]=panel_admin_define_user_status_link($uid,$users_status,$destination);
    //
    //intelsat-2015
    if(panel_admin_is_show_devel_switch_link($r)){
        $html[]=panel_admin_get_devel_switch_link($r);
    }
    //intelsat-2016
    red_copiar_add_compartir_usuario_link($html,$uid,$destination);
    $glue='&nbsp;';
    //$glue='<BR>';
    return implode($glue,$html);
}
function hontza_is_creador_de_grupo(){
    global $user;
    if((isset($user->roles[CREADOR]) && !empty($user->roles[CREADOR])) || $user->uid == 1) {
        return 1;
    }
    //intelsat-2015
    if((isset($user->roles[ADMINISTRADOR]) && !empty($user->roles[ADMINISTRADOR])) || $user->uid == 1) {
        return 1;
    }
    return 0;
}
function hontza_define_gestion_usuarios_header(){
    $html=array();
    //$html[]='<div class="view-header">';
    if(hontza_canal_rss_is_show_volver_gestion()){
        if(is_super_admin()){
            $html[]=l(t('Back to management panel'),'gestion',array('attributes'=>array('class'=>'back')));
        }
    }
    /*$html[]=l(t('Add user'),'admin/user/user/create',array('query'=>'destination=gestion/usuarios','attributes'=>array('class'=>'add')));
    if(hontza_is_claves()){
        $html[]=l(t('Management of Passwords'),'gestion/claves/validez');
    }*/
    $html[]=hontza_gestion_usuarios_filtro();
    //$html[]='<div>';
    return implode('<BR>',$html);
}
function hontza_gestion_usuarios_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    $title='';
    if(user_access('administer permissions')){
        $title=t('Modify roles');
        $form['my_bulk_operations_fs']['modify_user_roles_btn']=array(
         //'#type'=>'submit',
         '#type'=>'image_button',               
         //'#value'=>t('Modify roles'),
         '#name'=>'modify_user_roles_btn',
         '#src'=>'sites/all/themes/buho/images/icons/edit_roles.png',
         '#attributes'=>array('alt'=>$title,'title'=>$title),     
       );
    }
    //intelsat-2015
    //panel_admin_add_gestion_usuarios_delete_bulk_form_field($form);
    //
    //$title=t('Unblock the selected users');
    $title=t('Activate');
    $form['my_bulk_operations_fs']['unblock_post_btn']=array(
      //'#type'=>'submit',
      '#type'=>'image_button',
      //'#value'=>t('Unblock the selected users'),
      '#name'=>'unblock_post_btn',
      '#src'=>'sites/all/themes/buho/images/icons/active_user.png',
      '#attributes'=>array('alt'=>$title,'title'=>$title),  
    );    
    //$title=t('Block the selected users');
    $title=t('Deactivate');
    $form['my_bulk_operations_fs']['block_post_btn']=array(
      //'#type'=>'submit',
      '#type'=>'image_button',
      //'#value'=>t('Block the selected users'),
      '#name'=>'block_post_btn',
      '#src'=>'sites/all/themes/buho/images/icons/deactive_user.png',
      '#attributes'=>array('alt'=>$title,'title'=>$title),  
    );
    //intelsat-2016
    $title=t('Send message');
    $form['my_bulk_operations_fs']['send_message_btn']=array(
      '#type'=>'image_button',
      '#name'=>'send_message_btn',
      '#src'=>'sites/all/themes/buho/images/icons/email.png',
      '#attributes'=>array('alt'=>$title,'title'=>$title),  
    );
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    //$form['#submit'][]='my_noticias_publicas_form_submit';
    return $form;
}
function hontza_gestion_usuarios_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    //intelsat-2016
    $button_name_array=array('block_post_btn','unblock_post_btn','modify_user_roles_btn','delete_user_btn');
    $button_name_array[]='send_message_btn';
    if(in_array($button_name,$button_name_array)){        
        if(isset($button['#post']['txek_uid']) && !empty($button['#post']['txek_uid'])){
            //intelsat-2016
            $destination='destination=gestion/usuarios';
            $uid_array=array_keys($button['#post']['txek_uid']);
            if(strcmp($button_name,'block_post_btn')==0){
                $_SESSION['block_uid_array']=$uid_array;                
                drupal_goto('gestion/hontza_block_user_confirm');
            }else if(strcmp($button_name,'modify_user_roles_btn')==0){                
                $_SESSION['modify_user_roles_uid_array']=$uid_array;
                drupal_goto('gestion/hontza_modify_user_roles_confirm');
            }else if(strcmp($button_name,'unblock_post_btn')==0){
                $_SESSION['unblock_uid_array']=$uid_array;
                drupal_goto('gestion/hontza_unblock_user_confirm');
            //intelsat-2015                
            }else if(strcmp($button_name,'delete_user_btn')==0){
                $_SESSION['gestion_usuarios_delete_uid_array']=$uid_array;
                //drupal_goto('panel_admin/delete_user_confirm');
            //intelsat-2016
            }else if(strcmp($button_name,'send_message_btn')==0){
                //$_SESSION['gestion_usuarios_send_message_uid_array']=$uid_array;
                $uid_string=base64_encode(serialize($uid_array));
                drupal_goto('hontza_grupos/usuarios/contact/'.$uid_string,$destination);
            }
            //
        }
    }
}
function hontza_block_user_confirm_form(){
    $form=array();
    $uid_array=array();
    if(isset($_SESSION['block_uid_array'])){
        $uid_array=$_SESSION['block_uid_array'];
    }
    $form['uid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('block_uid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_block_user_confirm_content($uid_array);
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'gestion/usuarios');
    
    return $form;
}
function hontza_block_user_confirm_content($uid_array){
    //
    $html=array();
    $html[]='<div class="item-list">';
    //intelsat-2015
    $html[]='<h3>'.hontza_canal_rss_get_selected_rows_message(count($uid_array)).':</h3>';
    //
    $html[]='<ul>';
    if(count($uid_array)>0){
        foreach($uid_array as $i=>$uid){
            $user_row=user_load($uid);
            $html[]='<li>'.$user_row->name.'</li>';
        }
    }
    $html[]='</ul>';
    $html[]='</div>';
    return implode('',$html);
}
function hontza_block_user_confirm_form_submit($form, &$form_state) {
    hontza_update_block_user($form,$form_state,0,'block_user');
}
function hontza_update_user_status($uid,$status){
    //db_query('UPDATE {users} SET status=%d WHERE uid=%d',$status,$uid);
    $user_row=user_load($uid);
    if(isset($user_row->uid) && !empty($user_row->uid)){
        $my_array=array();
        $my_array['status']=$status;
        user_save($user_row,$my_array);
    }
}
function hontza_update_block_user($form,&$form_state,$status,$type){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if($button_name=='my_confirm_btn'){
        $uid_string=$form_state['values']['uid_array'];
        if(!empty($uid_string)){
            $uid_array=explode(',',$uid_string);
            foreach($uid_array as $i=>$uid){
                if($type=='modify_user_roles'){
                    $add_roles_array=$form_state['values']['add_roles_array'];
                    $remove_roles_array=$form_state['values']['remove_roles_array'];
                    $add_roles=hontza_define_add_roles($add_roles_array,$remove_roles_array);
                    hontza_save_modify_user_roles($uid,$add_roles,$remove_roles_array);
                }else{
                    hontza_update_user_status($uid,$status);
                }    
            }
        }    
    }
    drupal_goto('gestion/usuarios');
}
function hontza_unblock_user_confirm_form(){
    $form=array();
    $uid_array=array();
    if(isset($_SESSION['unblock_uid_array'])){
        $uid_array=$_SESSION['unblock_uid_array'];
    }
    $form['uid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('unblock_uid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_block_user_confirm_content($uid_array);
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'gestion/usuarios');
    
    return $form;
}
function hontza_unblock_user_confirm_form_submit($form, &$form_state) {
    hontza_update_block_user($form,$form_state,1,'unblock_user');
}
function hontza_modify_user_roles_confirm_form(){
    if(!user_access('administer permissions')){
        drupal_access_denied();
    }
    $form=array();
    $uid_array=array();
    if(isset($_SESSION['modify_user_roles_uid_array'])){
        $uid_array=$_SESSION['modify_user_roles_uid_array'];
    }
    $form['uid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('modify_user_roles_uid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    $form['my_content']['#value']=hontza_block_user_confirm_content($uid_array);
    $form['add_roles_array']=array('#type'=>'select',
        '#title'=>t('Add roles'),
        "#options"=>hontza_get_roles_options(1),
        '#size'=>5,
        "#multiple"=>TRUE
    );    
    $form['remove_roles_array']=array('#type'=>'select',
        '#title'=>t('Remove roles'),
        "#options"=>hontza_get_roles_options(1),
        '#size'=>5,
        "#multiple"=>TRUE
    );    
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'gestion/usuarios');
    
    return $form;
}
function hontza_modify_user_roles_confirm_form_submit($form, &$form_state) {
    hontza_update_block_user($form,$form_state,1,'modify_user_roles');
}
function hontza_get_roles_options($with_not_admin=0){
    $options=array();
    $where=array();
    $where[]='1';
    $where[]='NOT r.name IN("authenticated user","anonymous user")';
    $res=db_query('SELECT * FROM {role} r WHERE '.implode(' AND ',$where).' ORDER BY r.name ASC');
    while($row=db_fetch_object($res)){
        $options[$row->rid]=$row->name;
    }
    if($with_not_admin){
        if(!is_super_admin()){
            $options=hontza_unset_roles_by_not_admin($options);
        }
    }
    return $options;
}
function hontza_save_modify_user_roles($uid,$add_roles_in,$remove_roles_array){
    $add_roles=$add_roles_in;    
    if(!empty($add_roles) || !empty($remove_roles_array)){
        $user_row=user_load($uid);
        if(isset($user_row->uid) && !empty($user_row->uid)){
            $roles=$user_row->roles;
            $roles=hontza_remove_roles($roles,$remove_roles_array);
            $add_roles=hontza_remove_roles($add_roles,$remove_roles_array);
            $roles=hontza_merge_roles($roles,$add_roles);
            $my_array=array();
            $my_array['roles']=$roles;
            user_save($user_row,$my_array);
        }
    }
}
function hontza_remove_roles($roles,$remove_roles_array){
    $result=array();
    if(!empty($roles)){
        foreach($roles as $rid=>$name){
            if(!in_array($rid,$remove_roles_array)){
                $result[$rid]=$name;
            }
        }
    }
    return $result;
}
function hontza_define_add_roles($add_roles_array,$remove_roles_array){
    $result=array();
    if(!empty($add_roles_array)){
        foreach($add_roles_array as $rid=>$rid){
            if(!in_array($rid,$remove_roles_array)){
                $role=get_role_by_name('',$rid);
                if(isset($role->rid) && !empty($role->rid)){
                    $result[$rid]=$role->name;
                }    
            }
        }
    }
    return $result;
}
function hontza_merge_roles($roles,$add_roles){
    $result=$roles;
    if(!empty($add_roles)){
        foreach($add_roles as $rid=>$name){
            if(!isset($result[$rid])){
                $result[$rid]=$name;
            }
        }
    }
    return $result;
}
function hontza_user_access_gestion_usuarios(){ 
    if(user_access('administer users') && hontza_is_creador_de_grupo()){
        return 1;
    }    
    return 0;
}
function hontza_fix_mail_string_td($value){
    $max=25;
    $len=strlen($value);
    //if($len>$max){
        $pos=strpos($value,'@');
        if($pos===FALSE){
            return $value;
        }else{
            $result=substr($value,0,$pos).' @ '.substr($value,$pos+1);
            return $result;
        }
    //}
    return $value;
}