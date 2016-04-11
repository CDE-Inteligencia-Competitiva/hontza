<?php
function hontza_canal_rss_canales_rss(){
    if(hontza_canal_rss_is_red_exportar_rss_enviar_mail_canales_rss()){
        return red_exportar_rss_enviar_mail_canales_rss();
    }else{
        $canal_nid=arg(1);
        if($canal_nid=='canal-usuarios'){
            $nid_array=hontza_canal_rss_get_canal_noticias_usuario_nid_array($canal_nid);
        }else{
            $nid_array=hontza_canal_rss_get_canal_noticias_nid_array($canal_nid);
        }
        if(!empty($nid_array)){
            hontza_solr_funciones_node_feed($nid_array);
            exit();
        }
    }
    return '';
}
function hontza_canal_rss_get_canal_noticias_nid_array($canal_nid){
    $result=array();
    $my_grupo=og_get_group_context();
    $my_grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $arg_type='ultimas';
        $sql=hontza_canales_default_sql($my_grupo, $canal_nid,$arg_type);
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row->nid;
        }
    }    
    return $result;
}
function hontza_canal_rss_is_canales_rss(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canales_rss'){
        return 1;
    }
    return 0;
}
function hontza_canal_rss_get_canal_noticias_usuario_nid_array($canal_nid){
    $result=array();
    $sql=hontza_get_canal_usuarios_sql('',0);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
function hontza_canal_rss_og_canales_pre_execute($view){
   global $user;
   //gemini-2014
   if(!red_funciones_is_show_shared_block_menu_left()){
       hontza_empty_view_pre_execute($view);
       return 1;
   }
   //
   $my_grupo=og_get_group_context();
   if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){ 
        $where=array();
        $where[]="1";
        $where[]="node_node_data_field_item_canal_reference.status <> 0";
        $where[]="og_ancestry.group_nid = ".$my_grupo->nid;
        $where[]="node_node_data_field_item_canal_reference.type in ('canal_de_supercanal','canal_de_yql')";
        $where[]='NOT node_node_data_field_item_canal_reference.nid IS NULL';
        $where[]='NOT node_node_data_field_item_canal_reference.title=""';
        //$num_records="COUNT(node.nid) AS num_records";
        //$num_records="SUM(IF(flag_content_node.uid IS NULL,1,0)) AS num_records";
        $num_records="SUM(IF(flag_content_node.uid IS NULL AND NOT node.nid IS NULL,1,0)) AS num_records";
        if(hontza_is_mis_canales_block()){
            $where[]='(node_data_field_responsable_uid.field_responsable_uid_uid = '.$user->uid.' OR node_data_field_responsable_uid2.field_responsable_uid2_uid = '.$user->uid.')';
            //$where[]='(flag_content_node.uid IS NULL)';
            //$num_records="SUM(IF(flag_content_node.uid IS NULL,1,0)) AS num_records";            
        }
        $left_join_content_field_canal_active_refresh='';
        if(hontza_is_congelar_canal_sareko_id()){
            if(hontza_canal_comodin_is_canal_comodin_activado()){
                //intelsat-2015
                /*$left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_canal_active_refresh.nid ';
                $left_join_content_field_canal_active_refresh.=' LEFT JOIN {content_field_is_canal_comodin} content_field_is_canal_comodin ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_is_canal_comodin.nid ';
                */
                $left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_node_data_field_item_canal_reference.nid=content_field_canal_active_refresh.nid ';
                $left_join_content_field_canal_active_refresh.=' LEFT JOIN {content_field_is_canal_comodin} content_field_is_canal_comodin ON node_node_data_field_item_canal_reference.nid=content_field_is_canal_comodin.nid ';
                $where[]="(content_field_canal_active_refresh.field_canal_active_refresh_value=1 OR content_field_is_canal_comodin.field_is_canal_comodin_value=1)";                
                //                
            }else{
                //intelsat-2015
                //$left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_data_field_item_canal_reference.field_item_canal_reference_nid=content_field_canal_active_refresh.nid ';
                $left_join_content_field_canal_active_refresh=' LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node_node_data_field_item_canal_reference.nid=content_field_canal_active_refresh.nid ';                
                $where[]="content_field_canal_active_refresh.field_canal_active_refresh_value=1";
                //
            }
        }
        $left_join_categoria='';
        $categoria=red_funciones_get_filtro_por_categoria();
        if(!empty($categoria)){
            $left_join_categoria=" LEFT JOIN {term_node} term_node ON node_node_data_field_item_canal_reference.nid=term_node.nid ";
            $where[]="term_node.tid=".$categoria;
        }
        //intelsat-2015
        $left_join_tipos_fuente="";
        $my_selected_tipos_fuente=red_despacho_get_selected_tipos_fuente();
        if(!empty($my_selected_tipos_fuente)){
            $left_join_tipos_fuente=" LEFT JOIN {content_field_item_source_tid} ON node_data_field_item_canal_reference.vid=content_field_item_source_tid.vid ";
            $where[]=" {content_field_item_source_tid}.field_item_source_tid_value=".$my_selected_tipos_fuente;
        }
        //
        $sql="SELECT node_node_data_field_item_canal_reference.nid AS node_data_field_item_canal_reference_field_item_canal_reference_nid, 
        node_node_data_field_item_canal_reference.title AS node_node_data_field_item_canal_reference_title, 
        ".$num_records." 
        FROM {node} node_node_data_field_item_canal_reference
        LEFT JOIN {og_ancestry} og_ancestry ON node_node_data_field_item_canal_reference.nid = og_ancestry.nid 
        LEFT JOIN content_field_responsable_uid node_data_field_responsable_uid ON node_node_data_field_item_canal_reference.nid = node_data_field_responsable_uid.nid
        LEFT JOIN content_field_responsable_uid2 node_data_field_responsable_uid2 ON node_node_data_field_item_canal_reference.nid = node_data_field_responsable_uid2.nid        
        LEFT JOIN {content_type_item} node_data_field_item_canal_reference ON node_node_data_field_item_canal_reference.nid = node_data_field_item_canal_reference.field_item_canal_reference_nid 
        LEFT JOIN {node} node ON node_data_field_item_canal_reference.vid=node.vid 
        LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id        
        ".$left_join_content_field_canal_active_refresh.$left_join_categoria.$left_join_tipos_fuente."
        WHERE ".implode(" AND ",$where)."
        GROUP BY node_node_data_field_item_canal_reference_title, node_data_field_item_canal_reference_field_item_canal_reference_nid 
        ORDER BY node_node_data_field_item_canal_reference_title ASC";
        $res=db_query($sql);
        //
        $view->build_info['query']=$sql;
        $view->build_info['count_query']=$sql;
   }//else{
   //     hontza_repasar_canal_sin_valor($view->build_info['query']);
   //}*/
}
function hontza_canal_rss_is_canal_con_canal_sin_resultados(){
   //intelsat-2015
   return 1; 
}
function hontza_canal_rss_is_sareko_id_icono_con_numero(){
   return 1;     
}
function hontza_canal_rss_coments_style($node_c_d_w){
    if(empty($node_c_d_w)){
        $result=' style="clear:none;"';
        //return $result;
    }
    return '';
}
function hontza_canal_rss_set_view_options(&$view,$options){
    $result=$options;
    if(in_array($view->name,array('og_canales_dash'))){
        $view->display['page_1']->display_options['empty']="<?php print t('There are no contents')?>";
        $view->display['page_1']->display_options['header']=hontza_canal_rss_set_channel_actions($view->display['page_1']->display_options['header']);
    }else if(in_array($view->name,array('og_home_areadebate','og_home_areadetrabajo'))){
        $view->display['block_1']->display_options['items_per_page']=5;       
    }else if(in_array($view->name,array('gestion_grupos'))){
        $view->display['page_1']->display_options['header']="<?php print l(t('Create new Group'),'node/add/grupo',array('attributes'=>array('class'=>'add')))?>";
        $view->display['page_1']->display_options['fields']['member_count']['label']=t('Users');
        /*$access=panel_admin_define_view_access();
        $view->display['default']->display_options['access']=$access;
        $view->display['page_1']->display_options['access']=$access;*/        
    }else if(in_array($view->name,array('gestion_usuarios_grupos','gestion_items','gestion_noticias','gestion_area_trabajo_wiki','gestion_area_debate','gestion_ayuda_popup'))){
        /*if($view->name=='gestion_ayuda_popup'){
           $view->display['page_1']->display_options['title']=panel_admin_set_gestion_ayuda_popup_title();
        }else */
        if($view->name=='gestion_area_trabajo_wiki'){
           $view->display['page_1']->display_options['title']='Management of wiki documents';    
        }else if($view->name=='gestion_area_debate'){
           $view->display['page_1']->display_options['title']='Management of discussions';
           $view->display['page_1']->display_options['fields']['created']['label']=t('Creation date');
           $view->display['page_1']->display_options['fields']['title']['label']=t('Discussion');
        }else if($view->name=='gestion_usuarios_grupos'){
           $view->display['page_1']->display_options['fields']['nothing']['label']=t('Actions');
        }/*else if($view->name=='gestion_items'){
           $view->display['page_1']->display_options['fields']['phpcode']['label']=t('Channel');
        }*/           
        $view->display['page_1']->display_options['header']="";
    }else if(in_array($view->name,array('gestion_ayuda'))){
        $view->display['page_1']->display_options['header']="<?php print l(t('Create help page'),'node/add/faq',array('attributes'=>array('class'=>'add')))?>";
    }else if(in_array($view->name,array('gestion_servicios'))){
        $view->display['page_1']->display_options['header']="<?php print l(t('Back to management panel'),'panel_admin',array('attributes'=>array('class'=>'back')))?>
<?php print l(t('Create Facilitator'),'node/add/servicio',array('attributes'=>array('class'=>'add')))?>
<?php print l(t('Create Service'),'admin/content/taxonomy/4/add/term',array('attributes'=>array('class'=>'add')))?>";
    }else if(in_array($view->name,array('og_area_trabajo'))){
        $view->display['page_1']->display_options['title']=t('Collaboration');
    }
    return $result;
}
function hontza_canal_rss_get_node_categorias_tematicas($node,$is_ficha_completa,$is_js=0){
    $html=array();
    $popup_array=array();
    $is_see_more=0;
    $max=2;
    $kont=0;        
    if(isset($node->taxonomy) && !empty($node->taxonomy)){
        foreach($node->taxonomy as $tid=>$term){
            if(hontza_canal_rss_is_term_categoria_tematica($term)){
                if(!$is_ficha_completa){
                    if($kont>=$max){
                        $is_see_more=1;                                        
                    }
                }
                    $term_name=taxonomy_get_term_name_by_idioma($tid);
                    if(!empty($term_name)){
                        //$pro=profundidad($tid);
                        $popup_array[]=$term_name;
                        $kont++;
                        if(!$is_see_more){
                            $url='canales/my_categorias/'.$tid.'/ultimas';
                            if(red_movil_is_activado()){
                                $html[]='<li><b>'.$term_name.'</b></li>';
                            }else{
                                if(hontza_solr_is_solr_activado()){
                                    $url=hontza_solr_search_get_categorias_tematicas_filtrado_solr_url($tid,$url,$query);
                                    if(empty($query)){
                                        $html[]='<li>'.l($term_name,$url).'</li>';
                                    }else{
                                        $html[]='<li>'.l($term_name,$url,array('query'=>$query)).'</li>';
                                    }
                                }else{    
                                    $html[]='<li>'.l($term_name,$url).'</li>';
                                }
                            }    
                        }                                       
                    }
            }        
        }
    }
    $sep=red_despacho_get_popup_character($is_js);
    $popup=implode($sep,$popup_array);
    if($is_see_more && !empty($popup)){
        $html[]='<li>'.l(t('See more'),'node/'.$node->nid,array('attributes'=>array('title'=>$popup))).'</li>';                    
    }
    return implode('',$html);
}
function hontza_canal_rss_on_node_categoria_tematica_presave(&$node){
    if(hontza_canal_rss_is_post_categoria_tematica_empty($node)){
        hontza_canal_rss_unset_node_taxonomy_categorias_tematicas($node);
    }
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
function hontza_canal_rss_is_post_categoria_tematica_empty($node){
    $hay_categoria_tematica=0;
    $konp='my_cat_';
    foreach($node as $name=>$v)
    {
        $pos=strpos($name,$konp);
        if($pos===false){
            //
        }else{
            $hay_categoria_tematica=1;
            if(!empty($v)){
                return 0;
            }
        }
    }
    if($hay_categoria_tematica){
        return 1;
    }
    return 0;
}
function hontza_canal_rss_unset_node_taxonomy_categorias_tematicas(&$node){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$my_grupo->nid));
        $taxonomy=array();
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            foreach($node->taxonomy as $tid=>$value){
                if(is_numeric($tid)){
                    //
                }else{
                    $taxonomy[$tid]=$value;
                }
            }
        }
        $node->taxonomy=$taxonomy;
    }
}
function hontza_canal_rss_is_term_categoria_tematica($term){
    $id_categoria=hontza_canal_rss_get_grupo_id_categoria();
    if($term->vid==$id_categoria){
        return 1;
    }
    return 0;
}
function hontza_canal_rss_get_grupo_id_categoria(){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$my_grupo->nid));
        return $id_categoria;
    }
    return '';
}    
function hontza_canal_rss_get_others_canal_array($canal_nid_temp_array){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $groups=array();
        $groups[]=$my_grupo->nid;
        $types=array('canal_de_supercanal','canal_de_yql');
        $canal_array=hontza_get_all_nodes($types, $groups);
        if(!empty($canal_array)){
            foreach($canal_array as $i=>$canal){
                if(!in_array($canal->nid,$canal_nid_temp_array)){
                    $result[]=$canal;
                }
            }
        }    
    }
    return $result;
}
function hontza_canal_rss_get_url_fuente_txt($node){
    $result='';
    if($node->type=='supercanal'){
        if(isset($node->field_supercanal_fuente) && isset($node->field_supercanal_fuente[0]) && isset($node->field_supercanal_fuente[0]['value'])){
            $value=$node->field_supercanal_fuente[0]['value'];
            $result='http://pipes.yahoo.com/pipes/pipe.info?_id='.$value;
        }
    }else if($node->type=='fuentedapper'){        
        if(isset($node->field_fuentedapper_fuente) && isset($node->field_fuentedapper_fuente[0]) && isset($node->field_fuentedapper_fuente[0]['value'])){
            $value=$node->field_fuentedapper_fuente[0]['value'];
            $result=$value;
        }
    }
    return $result;
}
function hontza_canal_rss_get_url_canal_txt($node){
    $result='';
    $feeds_source=get_feeds_source($node->nid);
    if(isset($feeds_source->source) && !empty($feeds_source->source)){
        $result=$feeds_source->source;
    }
    return $result;
}
function hontza_canal_rss_is_correo_rss($data,&$url_correo,$yql_obj=''){
    if(hontza_solr_search_is_canal_correo('',$yql_obj)){
        $url_correo=$data[1];
        return 1;
    }
    return 0;
}
function hontza_canal_rss_is_canal_correo($source='',$canal_in='',$url_in=''){
    if(isset($source->feed_nid) && !empty($source->feed_nid)){
        $canal_node=node_load($source->feed_nid);       
    }else{
        $canal_node=$canal_in;
    }
    //
    if((isset($canal_node->nid) && !empty($canal_node->nid)) || !empty($canal_in)){
        if(isset($canal_node->field_is_canal_correo) && isset($canal_node->field_is_canal_correo[0]) && isset($canal_node->field_is_canal_correo[0]['value'])){
            if(!empty($canal_node->field_is_canal_correo[0]['value'])){
                return 1;
            }
        }
    }else{
        if(!empty($url_in)){
            if(hontza_solr_search_is_canal_correo_by_rss($url_in)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_debate_formulario(){
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
            if(in_array($node->type,array('debate'))){
                return 1;
            }        
        }
        if(is_node_add('debate')){
            return 1;
        }
    return 0;
}
function hontza_canal_rss_is_wiki_formulario(){
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
            if(in_array($node->type,array('wiki'))){
                return 1;
            }        
        }
        if(is_node_add('wiki')){
            return 1;
        }
    return 0;
}
function hontza_canal_rss_is_fuente_ficha_tabla(){
    //intelsat-2015
    if(!hontza_canal_rss_is_visualizador_defined()){
        if(hontza_canal_rss_is_visualizador_activado()){
          return 0;  
        }
    }    
    return 1;
    /*if(is_super_admin()){
        return 1;
    }
    return 0;*/
}
function hontza_canal_rss_is_canal_ficha_tabla(){
    return hontza_canal_rss_is_fuente_ficha_tabla();
}
function hontza_canal_rss_unset_average_label($value){
    $result=str_replace('<label for="edit-vote">','<label for="edit-vote" style="display:none">',$value);
    return $result;
}
function hontza_canal_rss_canal_noticias_link($node,$destination_in=''){
    $label='';
    $options=array('attributes'=>array('title'=>t('List of News'),'alt'=>t('List of News')));
    if(!empty($destination_in)){
        $options['query']=$destination_in;
    }
    //intelsat-2015
    return l($label,'canales/'.$node->nid.'/ultimas',$options);    
}
function hontza_canal_rss_canal_import_link($node,$destination_in=''){
    $label='';
    $options=array('attributes'=>array('title'=>t('Update Channel'),'alt'=>t('Update Channel')));
    if(!empty($destination_in)){
        $options['query']=$destination_in;
    }
    return l($label,'node/'.$node->nid.'/import',$options);    
}
function hontza_canal_rss_canal_borrar_noticias_link($node,$destination_in=''){
    $label='';
    $options=array('attributes'=>array('title'=>t('Delete news of channel'),'alt'=>t('Delete news of channel')));
    if(!empty($destination_in)){
        $options['query']=$destination_in;
    }
    return l($label,'node/'.$node->nid.'/delete-items',$options);    
}
function hontza_canal_rss_set_channel_actions($content){
    $pos=strpos($content,'<fieldset>');
    if($pos===FALSE){
        $result='<?php if(strcmp(arg(0),"canales")==0):?>
        <?php print "<fieldset>";?>
        <?php print "<legend>".t("Channel Operations")."</legend>";?>
        <?php print "<div>";?>        
        <?php $nid=arg(1);?>
        <?php if(!empty($nid) && is_numeric($nid)):?>
        <?php global $user;?>
        <?php print link_notify($user->uid, $nid,"canales/".$nid);?>
        <?php print link_import_canal($nid);?>
        <?php print link_ver_canal($nid);?>
        <?php print link_edit_canal($nid);?>
        <?php print link_validar_canal($nid);?>
        <?php print "</div>";?>
        <?php print "</fieldset>";?>
        <?php print hontza_canales_menu();?>
        <?php endif;?>
        <?php endif;?>';
        return $result;
    }
    return $content;
}
function hontza_canal_rss_is_usuario_ficha_tabla(){
    /*if(is_super_admin()){
        return 1;
    }
    return 0;*/
    return 1;
}
function hontza_canal_rss_my_get_rows_gestion_grupos($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_grupos($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_usuarios_grupos($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_usuarios_grupos($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_is_show_volver_gestion(){
    if(module_exists('panel_admin')){
        return 0;
    }
    return 1;
}
function hontza_canal_rss_my_get_rows_gestion_ayuda($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_ayuda($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_items($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_items($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_noticias($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_noticias($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_area_trabajo_wiki($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_area_trabajo_wiki($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_area_debate($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_area_debate($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_my_get_rows_gestion_servicios($vars){
    if(module_exists('panel_admin')){
        return panel_admin_my_get_rows_gestion_servicios($vars);
    }
    return $vars['rows'];
}
function hontza_canal_rss_get_my_volver_link_servicio(){
    $html=array();
    $html[]='<div class="clearfix">';     
    $html[]=l(t('Back to management panel'),'panel_admin',array('attributes'=>array('class'=>'back')));
    $html[]='</div>';
    return implode('',$html);
}
function hontza_canal_rss_my_get_rows_gestion_ayuda_popup($vars){
    return panel_admin_my_get_rows_gestion_ayuda_popup($vars);    
}
function hontza_canal_rss_panel_admin_access(){
    if(module_exists('panel_admin')){
        return panel_admin_custom_access();
    }
    return FALSE;
}
function hontza_canal_rss_get_menu_desplegable(){
    if(module_exists('custom_menu')){
         //if(is_super_admin()){
            return custom_menu_get_menu_desplegable();
         //}   
    }
    return '';
}
function hontza_canal_rss_is_custom_menu_activado(){
    if(module_exists('custom_menu')){
        if(custom_menu_is_activado()){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_get_icono_grupo_privacidad($row,$title,$is_tab,$icono_red_alerta,&$title_popup){
    /*if(hontza_canal_rss_is_custom_menu_activado()){
        return custom_menu_get_icono_grupo_privacidad($row,$title,$is_tab,$icono_red_alerta);
    }else{*/
        $tid='';
        if(isset($row->type_of_group_tid) && !empty($row->type_of_group_tid)){
            $tid=$row->type_of_group_tid;
        }
        return panel_admin_get_tipo_grupo_icono($row,$icono_red_alerta,$tid,$title_popup); 
    //}
    return '';
}
function hontza_canal_rss_is_node_notifications(){
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            return 0;
        }
    }
    if(is_node_add('visualizador-contacto')){
        return 0;
    }
    $my_node=my_get_node();
    if(isset($my_node->nid) && !empty($my_node->nid)){
        if(in_array($my_node->type,array('visualizador_contacto'))){
            return 0;
        }
    }
    //intelsat-2015
    if(red_movil_is_activado()){
        return movil_is_node_notifications();
    }
    return 1;
}
function hontza_canal_rss_is_oferta_demanda_activado(){
    if(module_exists('oferta_demanda')){
        if(oferta_demanda_is_activado()){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_is_tags_oferta_demanda($vocabulary){
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            if(oferta_demanda_is_tags_oferta_demanda($vocabulary)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_tipo_organizacion($vocabulary){
     if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            if(oferta_demanda_is_tipo_organizacion($vocabulary)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_rol($vocabulary){
     if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            if(oferta_demanda_is_rol($vocabulary)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_area_tecnologica($vocabulary){
     if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            if(oferta_demanda_is_area_tecnologica($vocabulary)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_sector_actividad($vocabulary){
     if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_node()){
            if(oferta_demanda_is_sector_actividad($vocabulary)){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_is_oferta_demanda_clasificaciones(){
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        if(oferta_demanda_is_oferta_demanda_clasificaciones()){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_get_url_clasificacion_terminos($url_cancel,$is_temp=0){
    if(hontza_canal_rss_is_oferta_demanda_activado()){
        return oferta_demanda_get_url_clasificacion_terminos($url_cancel,$is_temp);        
    }
    return 0;
}
function hontza_canal_rss_is_noticia_usuario_ficha_tabla(){
    return hontza_canal_rss_is_fuente_ficha_tabla();
}
function hontza_canal_rss_get_download_txt_left_title(){
    return t('Download Txt');
}
function hontza_canal_rss_get_download_txt_block_content(){
    if(is_super_admin()){
        $html=array();
        $html[]=l(t('Download Sources txt'),'download_sources_txt',array('attributes'=>array('target'=>'_blank')));
        $html[]=l(t('Download Channels txt'),'download_channels_txt',array('attributes'=>array('target'=>'_blank')));                        
        return implode('<br>',$html);
    }
    return '';
}
function hontza_canal_rss_get_busqueda_title_links(){
    //intelsat-2015
    $modo_estrategia=1;
    if(is_administrador_grupo($modo_estrategia)){
        $html=array();
        $html[]='<input id="edit-my-borrar" class="form-submit" type="submit" value="'.t('Delete').'" name="op">';
        return implode('',$html);
    }
}
function hontza_canal_rss_is_carpeta_dinamica_guardada(){
    if(is_carpeta_dinamica()){
        if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_is_facilitador_activado(){
    if(module_exists('facilitador')){
        if(facilitador_is_facilitador_activado()){    
            return 1;
        }    
    }
    return 0;
}
function hontza_canal_rss_get_profile_item_title($attributes_in){
    $attributes=trim($attributes_in);
    $information_management_consultancy_array=array('class="profile-profile_consultoria_gestion_buscar_fuentes"',
    'class="profile-profile_consultoria_gestion_optimizacion_busquedas"');
    $innovation_consultancy_array=array('class="profile-profile_innovacion_busqueda"','class="profile-profile_innovacion_redaccion"',
    'class="profile-profile_innovacion_creatividad"','class="profile-profile_innovacion_seleccion_de_ideas"','class="profile-profile_innovacion_construccion"',
    'class="profile-profile_innovacion_evaluacion"');    
    $strategic_consultancy_array=array('class="profile-profile_consultoria_estrategica_despliegue_fcv"','class="profile-profile_consultoria_estrategica_ayuda"',
    'class="profile-profile_consultoria_estrategica_despliegue_estrategico"');
    $icts_optimisation_array=array('class="profile-profile_optimizacion_tics_crear_modulo"','class="profile-profile_optimizacion_tics_html_rss"',
    'class="profile-profile_optimizacion_tics_adaptacion"');
    if(in_array($attributes,$information_management_consultancy_array)){ 
        return t('Information Management Consultancy');
    }else if(in_array($attributes,$innovation_consultancy_array)){ 
        return t('Innovation Consultancy');
    }else if(in_array($attributes,$strategic_consultancy_array)){ 
        return t('Strategic Consultancy');
    }else if(in_array($attributes,$icts_optimisation_array)){
        return t('ICTs Optimisation');
    }else{
        print $attributes.'<BR>';    
    }
    return '';
}
function hontza_canal_rss_user_translate_profile_value($value,$s,$attributes_in){
    $attributes=trim($attributes_in);
    if($attributes=='class="profile-profile_consultoria_gestion_buscar_fuentes"'){
        return t('Search Information Sources');
    }else if($attributes=='class="profile-profile_consultoria_gestion_optimizacion_busquedas"'){
        return t('Search Optimisation');
    }else if($attributes=='class="profile-profile_innovacion_busqueda"'){
        return t('Financial consultancy');
    }else if($attributes=='class="profile-profile_innovacion_redaccion"'){
        return t('Writing Projects');
    }else if($attributes=='class="profile-profile_innovacion_creatividad"'){
        return t('Creativity and New ideas');
    }else if($attributes=='class="profile-profile_innovacion_seleccion_de_ideas"'){
        return t('Idea Selection');
    }else if($attributes=='class="profile-profile_innovacion_construccion"'){
        return t('Project building');
    }else if($attributes=='class="profile-profile_innovacion_evaluacion"'){
        return t('Project evaluation');        
    }else if($attributes=='class="profile-profile_consultoria_estrategica_despliegue_fcv"'){
        return t('Critic Watching Factors Deployment');
    }else if($attributes=='class="profile-profile_consultoria_estrategica_ayuda"'){
        return t('Consultancy on Strategy definition');
    }else if($attributes=='class="profile-profile_consultoria_estrategica_despliegue_estrategico"'){
        return t('Strategic Deployment');
    }else if($attributes=='class="profile-profile_optimizacion_tics_crear_modulo"'){
        return t('Create a new module');
    }else if($attributes=='class="profile-profile_optimizacion_tics_html_rss"'){
        return t('HTML-RSS Conversion');
    }else if($attributes=='class="profile-profile_optimizacion_tics_adaptacion"'){
        return t('Drupal and PHP Programming');
    }
    return $value;
}
function hontza_canal_rss_get_selected_rows_message($num){
    return t('You selected the following @num rows',array('@num'=>$num));
}
function hontza_canal_rss_get_canales_rss_link($node){
    $title=t('RSS');
    return l('','canales_rss/'.$node->nid,array('attributes'=>array('target'=>'_blank','title'=>$title)));
}
function hontza_canal_rss_es_categoria_tematica_del_item($node,$tid){    
    if(isset($node->field_item_canal_category_tid) && !empty($node->field_item_canal_category_tid)){
        foreach($node->field_item_canal_category_tid as $i=>$row){
            if($tid==$row['value']){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_canal_rss_on_item_presave(&$node,$op){
    $key='field_item_canal_category_tid_';
    if(hontza_canal_rss_is_field_item_canal_category_tid_presave($node,$key)){
        //$node->field_item_canal_category_tid=array();        
        hontza_canal_rss_on_field_item_canal_category_tid_presave($node,$op,$key);
    }    
}
function hontza_canal_rss_is_field_item_canal_category_tid_presave($node,$key='field_item_canal_category_tid_'){
    foreach($node as $field=>$value){
        $pos=strpos($field,$key);
        if($pos===FALSE){
            continue;
        }else{
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_on_field_item_canal_category_tid_presave(&$node,$op,$key='field_item_canal_category_tid_'){
    if($key=='categoria_tematica_canal_'){
        //
    }else{    
        $node->field_item_canal_category_tid=array();
    }
    foreach($node as $field=>$value){
        $pos=strpos($field,$key);
        if($pos===FALSE){
            continue;
        }else{
            if(!empty($value)){
                $tid=substr($field,strlen($key));                
                if($key=='categoria_tematica_canal_'){
                    $term = taxonomy_get_term($tid);
                    $node->taxonomy[$tid]=$term;
                }else{
                    $node->field_item_canal_category_tid[]['value']=$tid;
                }    
            }
        }
    }  
}
function hontza_canal_rss_add_canal_categorias_tematicas_form_field(&$form,$node_type){
    $node=hontza_get_node_by_form($form);
    $form['categorias_tematicas_fieldset']=create_categorias_tematicas_fieldset('',1,$node->nid,$node_type);
}
function hontza_canal_rss_on_categorias_tematicas_presave(&$node){
    $key='categoria_tematica_canal_';
    if(hontza_canal_rss_is_field_item_canal_category_tid_presave($node,$key)){
        hontza_canal_rss_unset_categorias_tematicas($node);
        hontza_canal_rss_delete_categorias_tematicas_term_node($node);
        hontza_canal_rss_on_field_item_canal_category_tid_presave($node,$op,$key);
    } 
}
function hontza_canal_rss_es_categoria_tematica_del_canal($node,$contenido_tid,$is_term_node=0){
    $my_grupo=panel_admin_get_node_grupo($node);
    $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s", $my_grupo->nid));
    if($is_term_node){
        $term=taxonomy_get_term($contenido_tid);
        if($term->vid==$id_categoria && $term->tid==$contenido_tid){
            return 1;
        }
    }else{
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            foreach($node->taxonomy as $tid=>$row){
                if($row->vid==$id_categoria && $row->tid==$contenido_tid){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function hontza_canal_rss_unset_categorias_tematicas(&$node){
    if(isset($node->taxonomy) && !empty($node->taxonomy)){
        $taxonomy=$node->taxonomy;
    }
    $node->taxonomy=array();
    if(!empty($taxonomy)){
        foreach($taxonomy as $tid=>$row){
            if(!hontza_canal_rss_es_categoria_tematica_del_canal($node,$tid)){
                $node->taxonomy[$tid]=$row;
            }else{
                if(isset($node->nid) && !empty($node->nid)){
                    db_query('DELETE FROM {term_node} WHERE nid=%d AND tid=%d',$node->nid,$tid);                    
                }    
            }
        }
    }    
}
function  hontza_canal_rss_delete_categorias_tematicas_term_node(&$node){
    $term_node_array=hontza_canal_rss_get_term_node_array($node);
    if(!empty($term_node_array)){
        foreach($term_node_array as $i=>$row){
            if(hontza_canal_rss_es_categoria_tematica_del_canal($node,$row->tid,1)){
                db_query('DELETE FROM {term_node} WHERE nid=%d AND tid=%d',$node->nid,$row->tid);
            }
        }
    }
}
function hontza_canal_rss_get_term_node_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {term_node} WHERE nid=%d',$node->nid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_canal_rss_get_categoria_noticia_num_items($tid,$num_items){
    $is_visualizador_actualidad=0;
    if(hontza_canal_rss_is_visualizador_activado()){
        if(publico_is_pantalla_publico('vigilancia')){
            $is_visualizador_actualidad=1;
        }
    }
    if(hontza_solr_is_solr_activado() || $is_visualizador_actualidad){
        $result=$num_items;
        if($num_items==0){
            $content_field_item_canal_category_tid_array=hontza_canal_rss_get_content_field_item_canal_category_tid_array($tid);
            $result=count($content_field_item_canal_category_tid_array);
            //return $result;
        }
        //intelsat-2015
        $result=$result+red_solr_inc_get_categoria_noticia_usuario_num_items($tid,$is_visualizador_actualidad);
        return $result;        
    }
    return $num_items;
}
function hontza_canal_rss_get_content_field_item_canal_category_tid_array($tid){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='content_field_item_canal_category_tid.field_item_canal_category_tid_value='.$tid;
    $sql='SELECT node.* 
    FROM {node} node 
    LEFT JOIN {content_field_item_canal_category_tid} content_field_item_canal_category_tid ON node.vid=content_field_item_canal_category_tid.vid 
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_canal_rss_solr_on_flag_update_node_validate_status($content_id){
    if(hontza_solr_is_solr_activado()){
        $node=node_load($content_id);
        hontza_solr_update_node_validate_status($node);
        hontza_canal_rss_solr_clear_node_index($node,$content_id);
    }    
}
function hontza_canal_rss_solr_get_tid_item_array($tid,$nid_array){
    $result=$nid_array;
    if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
        $content_field_item_canal_category_tid_array=hontza_canal_rss_get_content_field_item_canal_category_tid_array($tid);
        if(!empty($content_field_item_canal_category_tid_array)){
            foreach($content_field_item_canal_category_tid_array as $i=>$row){
                if(!in_array($row->nid,$result)){
                    $result[]=$row->nid;
                }
            }
        }
    }    
    return $result;
}
function hontza_canal_rss_update_apachesolr_index_entities_node_changed($content_id){
    if(hontza_solr_is_solr_activado()){
        if(red_solr_inc_is_sended()){
            db_query('UPDATE {apachesolr_index_entities_node} SET changed=%d,my_sended=0 WHERE entity_id=%d',time(),$content_id);
        }else{
            db_query('UPDATE {apachesolr_index_entities_node} SET changed=%d WHERE entity_id=%d',time(),$content_id);
        }    
    }
}
function hontza_canal_rss_solr_update_validador($row){
    if(hontza_solr_is_solr_activado()){
        $uid=red_funciones_flag_get_validador_node_uid($row->content_id);
        if(empty($uid)){
            $uid=$row->uid;
        }
        $nid=$row->content_id;
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $res=db_query('UPDATE {content_type_item} SET field_item_validador_uid_uid=%d WHERE nid=%d AND vid=%d',$uid,$node->nid,$node->vid);        
        }
    }    
}
function hontza_canal_rss_solr_reset_validador($row){
    if(hontza_solr_is_solr_activado()){
        $nid=$row->content_id;
        $node=node_load($nid);
        if(isset($node->nid) && !empty($node->nid)){
            $res=db_query('UPDATE {content_type_item} SET field_item_validador_uid_uid=NULL WHERE nid=%d AND vid=%d',$node->nid,$node->vid);        
        }
    }
}
function hontza_canal_rss_solr_clear_node_index($node_in,$content_id){
    if(hontza_solr_is_solr_activado()){
        $node=$node_in;
        if(isset($node->nid) && !empty($node->nid)){
           // 
        }else{
           $node=node_load($content_id); 
        }
        if(isset($node->nid) && !empty($node->nid)){
            hontza_solr_search_clear_cache_content($node);
            hontza_canal_rss_update_apachesolr_index_entities_node_changed($content_id);
        }
    }
}
function hontza_canal_rss_solr_on_bookmark_node_tag_save($nid){
    $flag_row=new stdClass();
    $flag_row->nid=$nid;
    hontza_delete_flag_content($flag_row);
    $flag_result = flag('flag','leido_interesante',$flag_row->nid);
}
function hontza_canal_rss_solr_existe_content_field_item_canal_category_tid_row($node){
    if(hontza_solr_is_solr_activado()){
        $res=db_query('SELECT * FROM {content_field_item_canal_category_tid} WHERE nid=%d AND vid=%d AND delta=0',$node->nid,$node->vid);
        while($row=db_fetch_object($res)){
            return 1;
        }
    }    
    return 0;
}
function hontza_canal_rss_solr_on_bookmark_multiple_vote($nid){
    hontza_canal_rss_solr_on_bookmark_node_tag_save($nid);
}
function hontza_canal_rss_busqueda_rss_callback(){
    $nid_array=hontza_canal_rss_get_busqueda_view_nid_array();    
    hontza_solr_funciones_node_feed($nid_array);
    exit();
}
function hontza_canal_rss_get_busqueda_view_nid_array(){
    $result=array();
    my_carpeta_dinamica_pre_execute($view);
    $sql=$view->build_info['query'];
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
function hontza_canal_rss_busqueda_rss_access(){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(in_user_groups($my_grupo->nid)){
            return TRUE;
        }
    }    
    return FALSE;
}
function  hontza_canal_rss_existe_titulo_fuente_grupo($form_state){
    $titulo='';
    $where_grupo='1';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where_grupo='og_ancestry.group_nid='.$my_grupo->nid;
    }
                  if(isset($form_state['values']['nid']) && !empty($form_state['values']['nid'])){
                        $titulo=$form_state['values']['title'];
			//intelsat-2015
                        //$node_list = db_result(db_query($sql=sprintf("SELECT n.* FROM {node} n LEFTWHERE title='%s' AND n.nid!=%d", $titulo,$form_state['values']['nid'])));
			$res= db_query($sql=sprintf("SELECT n.* FROM {node} n LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid WHERE title='%s' AND n.nid!=%d AND ".$where_grupo, $titulo,$form_state['values']['nid']));		
                        //echo $sql;exit();
                        $node_list=hontza_canal_rss_get_res_nodes_array($res);
		  }else{
                        $titulo=$form_state['input']['titulo'];
                        //intelsat-2015
                        //$node_list = db_result(db_query("SELECT n.* FROM {node} n WHERE title='%s'", $titulo));
                        $res = db_query($sql=sprintf("SELECT n.* FROM {node} n LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid WHERE title='%s' AND ".$where_grupo, $titulo));
                        //echo $sql;exit();
                        $node_list=hontza_canal_rss_get_res_nodes_array($res);                        
                  }
		  //intelsat-2015
		  //if (db_affected_rows()) {
                  if(count($node_list)>0){
                        //gemini
			form_set_error('titulo', t('The title already exists'));
			//
		  }
}
function hontza_canal_rss_get_res_nodes_array($res){
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_canal_rss_solr_indexar_callback(){
    //intelsat-2015
    if(red_solr_inc_is_status_activado()){
        //si tarda mas de 5 minutos en hontza_solr_actualizar_items
        apachesolr_cron(NULL,0);
    }else{
        apachesolr_cron();
    }    
}
//intelsat-2015-kimonolabs
function hontza_canal_rss_is_kimonolabs_activado(){
    if(module_exists('kimonolabs')){
        //if(kimonolabs_is_kimonolabs_activado()){
            return 1;
        //}
    }
   return 0;  
}
//intelsat-2015-kimonolabs
function hontza_canal_rss_create_kimonolabs_checkbox_array(){
    $result=array();
    $result[0]=t('Is Kimonolabs');
    $result[1]=t('Is Kimonolabs');
    return $result;
}
//intelsat-2015-kimonolabs
function hontza_canal_rss_canal_supercanal_origin($node){
    $is_kimonolabs=0;
    if(hontza_canal_rss_is_kimonolabs_activado()){
        $is_kimonolabs=kimonolabs_is_fuente_kimonolabs('',$node);
    }
    //
    if(!$is_kimonolabs){
        if(isset($node->field_fuente_canal[0]['view'])){
            return $node->field_fuente_canal[0]['view'];
        }
        return '';
    }    
    return 'Kimonolabs';
}
//intelsat-2015
function hontza_canal_rss_is_vcard_activado(){
    if(module_exists('vcard')){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_is_visualizador_activado(){
    if(module_exists('visualizador')){
        if(visualizador_is_visualizador_activado()){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_is_show_user_image($type=''){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(!visualizador_is_show_user_image($type)){
            return 0;
        }
    }
    if(hontza_canal_rss_is_usuario_basico()){
        return 0;
    }
    return 1;
}
//intelsat-2015
function hontza_canal_rss_get_username_item($my_user_info){
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_get_username_item($my_user_info);
    }
    return '';
}
//intelsat-2015
function hontza_canal_rss_is_debate_editar_access($node){
    return red_funciones_is_node_access('',$node,1);
}
//intelsat-2015
function hontza_canal_rss_get_node_url($node,$is_absolute=0,$is_publico=0){
    $result='node/'.$node->nid;
    if(hontza_canal_rss_is_publico_activado()){
        return publico_get_node_url($result,$node,$is_absolute,$is_publico);
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_publico_activado(){
    if(module_exists('publico')){
        if(publico_is_publico_activado()){
            return 1;
        }
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_is_publico_usuario_lector(){
    if(hontza_canal_rss_is_publico_activado()){
        return publico_is_usuario_lector();
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_sidebar_right($right){
    $result=$right;
    /*if(hontza_canal_rss_is_publico_activado()){
        $result=publico_vigilancia_get_sidebar_right($right);
    }*/
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_div_main_style(){
    $result='';
    /*if(hontza_canal_rss_is_publico_activado()){
        $result=publico_vigilancia_get_div_main_style();
    }*/
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_div_sidebar_right_style(){
    $result='';
    if(hontza_canal_rss_is_publico_activado()){
        $result=publico_vigilancia_get_div_sidebar_right_style();
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_menutop_selected(){
    $result='none';
    if(hontza_canal_rss_is_visualizador_activado()){
        $result=publico_get_menutop_selected();
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_show_categorias_tipos_fuentes_item(){
    if(hontza_solr_is_solr_activado()){
        return 1;
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_is_show_categorias_tipos_fuentes_item();
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_item_categoria_tematica($tid,&$html,$term_name){
    global $base_url;
    if(hontza_is_user_anonimo()){
        $grupo_path=visualizador_create_grupo_base_path();
        $url=$base_url.'/'.$grupo_path.'/publico/canales/categorias/'.$tid.'/ultimas';
        $html[]='<li>'.l($term_name,$url,array('absolute'=>true)).'</li>';                    
    }else{    
        $url='canales/my_categorias/'.$tid.'/ultimas';
        $url=hontza_solr_search_get_categorias_tematicas_filtrado_solr_url($tid,$url,$query);
        if(red_movil_is_activado()){
            $html[]='<li><b>'.$term_name.'</b></li>';
        }else{                
            if(empty($query)){
                $html[]='<li>'.l($term_name,$url).'</li>';
            }else{
                $html[]='<li>'.l($term_name,$url,array('query'=>$query)).'</li>';
            }
        }
    } 
}
//intelsat-2015
function hontza_canal_rss_categorias_unset_term_name_corchetes($term_name){
    global $language;
    $result=$term_name;
    if($language->language=='es'){
        $result=ltrim($result,'[');
        $result=rtrim($result,']');        
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_alerta_publico(){
    if(hontza_canal_rss_is_publico_activado()){
        return publico_alerta_is_publico();
    }
    if(hontza_is_user_anonimo()){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_alerta_user_volver_url($result_in){
    $result=$result_in;
    if(hontza_canal_rss_is_publico_activado()){
        if(publico_is_alerta_user()){
            $result='publico/'.$result;
        }
    }    
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_query_yahooapis($url){
    $result=1;
    $konp ='http://query.yahooapis.com/v1/public/yql?q=';
    $pos=strpos($url,$konp);
    if($pos===FALSE){
        $result=0;
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_modify_url_rss_multiple($result_in,$url){
    $result=$result_in;
        if(hontza_canal_rss_is_query_yahooapis($url)){
            return $result;
        }else{
            $result=$url;
        }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_comment_array($nid){
   return contenidos_debate_get_comment_array($nid);
}
//gemini
function hontza_canal_rss_create_canal_opencalais_checkbox_array(){
    $result=array();
    $result[0]=t('Apply OpenCalais');
    $result[1]=t('Apply OpenCalais');
    return $result;
}
//gemini
function hontza_canal_rss_is_canal_open_calais($feed_nid){
    $result=0;
    $canal=node_load($feed_nid);
    if(isset($canal->nid) && !empty($canal->nid)){
        if(isset($canal->field_is_canal_opencalais) && isset($canal->field_is_canal_opencalais[0]) && isset($canal->field_is_canal_opencalais[0]['value'])){
            if($canal->field_is_canal_opencalais[0]['value']==1){
                $result=1;
            }
        }
    }
    return $result;
}
//gemini
//intelsat-2015
//function hontza_canal_rss_get_content_field_item_source_tid_nid_array($tid){
function hontza_canal_rss_get_content_field_item_source_tid_nid_array($tid,$with_group=0){
    $result=array();
    //intelsat-2015
    //$res=db_query('SELECT * FROM {content_field_item_source_tid} WHERE field_item_source_tid_value=%d',$tid);
    $where=array();
    $where[]='1';
    $where[]='content_field_item_source_tid.field_item_source_tid_value='.$tid;
    if($with_group){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        }
        $where[]='NOT node.nid IS NULL';
    }
    $sql='SELECT content_field_item_source_tid.* 
    FROM {content_field_item_source_tid} 
    LEFT JOIN {og_ancestry} ON content_field_item_source_tid.nid=og_ancestry.nid 
    LEFT JOIN {node} ON content_field_item_source_tid.vid=node.vid 
    WHERE '.implode(' AND ',$where).' 
    GROUP BY content_field_item_source_tid.nid';     
    //print $sql;exit();
    $res=db_query($sql);    
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_publico_vigilancia_ultimos_menu_row($row){
    $result=0;
    if(hontza_canal_rss_is_publico_activado()){
        $result=publico_is_publico_vigilancia_ultimos_menu_row($row);
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_set_menutop_publico_vigilancia_sf_js_enabled($menutop_in){
    if(hontza_canal_rss_is_publico_activado()){
        return publico_vigilancia_set_menutop_publico_vigilancia_sf_js_enabled($menutop_in);
    }
    return $menutop_in;
}
//intelsat-2015
function hontza_canal_rss_on_opencalais_exception($exception){
    drupal_set_message(t($exception->getMessage()));        
}
//intelsat-2015
function hontza_canal_rss_get_logos_apis($powered=''){
    $html=array();
    if(!empty($powered)){
        $html[]=$powered;
    }        
    if(hontza_canal_rss_is_activado_logos_apis()){
        /*if(hontza_is_sareko_id('ROOT')){
            print 'prueba<br>';
        }*/
        $background_color=hontza_canal_rss_get_logos_apis_background_color();
        //$html[]='<div style="width:960px;margin: 20px auto -1em;float:left;'.$background_color.'">';
        $margin_top='';
        if(!hontza_canal_rss_is_visualizador_activado()){
            if(!hontza_is_user_anonimo()){
                if(!hontza_is_node_edit()){
                    $margin_top='margin-top:-10px;';
                }
            }
        }
        $width='960px';
        if(red_crear_usuario_is_custom_css_hontza()){
            $width='100%';
        }        
        $html[]='<div style="width:'.$width.';margin: 0px auto -1em;float:left;'.$background_color.$margin_top.'padding-top:10px;">';
        if(red_crear_usuario_is_custom_css_hontza()){
            $html[]='<div style="float:left;width:33%;">&nbsp;</div>';
        }
        $html[]=hontza_canal_rss_get_text_linked_services();
        $html[]=hontza_canal_rss_get_alchemy_api_logo();
        //$html[]=hontza_canal_rss_get_kimonolabs_logo();        
        $html[]=hontza_canal_rss_get_opencalais_logo();
        $html[]=hontza_canal_rss_get_yahoo_developer_network_logo();
        $html[]='</div>';
    }
    return implode('',$html);
}
//intelsat-2015
function hontza_canal_rss_get_opencalais_logo(){
    global $base_url;
    $html=array();
    $html[]='<div style="float:left;padding-left:10px;">';
    $img='<img src="'.$base_url.'/sites/default/files/logo_opencalais.png'.'" height="25"/>';
    $html[]=l($img,'http://www.opencalais.com',array('attributes'=>array('target'=>'_blank'),'absolute'=>true,'html'=>true));
    $html[]='</div>';
    return implode('',$html);
}
//intelsat-2015
function hontza_canal_rss_get_alchemy_api_logo(){
    global $base_url;
    $html=array();
    $html[]='<div style="float:left;padding-left:10px;">';
    $img='<img src="'.$base_url.'/sites/default/files/logo_alchemy_api.png'.'" height="25"/>';
    $html[]=l($img,'http://www.alchemyapi.com/',array('attributes'=>array('target'=>'_blank'),'absolute'=>true,'html'=>true));
    $html[]='</div>';
    return implode('',$html);
}
//intelsat-2015
function hontza_canal_rss_get_kimonolabs_logo(){
    global $base_url;
    $html=array();
    $background_color='background-color:white';
    //$html[]='<div style="float:left;padding-left:10px;'.$background_color.'margin-left:10px;padding-right:10px;">';
    $html[]='<div style="float:left;padding-left:10px;'.$background_color.'margin-left:10px;padding-right:10px;margin-top:-4px;">';
    //$img='<p style="font-size:20px;">Kimonolabs</p>';
    $img='<p style="font-size:14px;"><b>Kimonolabs</b></p>';
    $html[]=l($img,'http://www.kimonolabs.com/',array('attributes'=>array('target'=>'_blank'),'absolute'=>true,'html'=>true));
    $html[]='</div>';
    return implode('',$html);
}
//intelsat-2015
function hontza_canal_rss_is_activado_logos_apis(){
    return 1;
}
//intelsat-2015
function hontza_canal_rss_get_text_linked_services(){
    $html=array();
    //$html[]='<div style="float:left;margin-top:5px;">';
    $html[]='<div style="float:left;padding-left:5px;">';    
    $style='font-family:verdana;color: #A0A0A0;font-size: 12px;';
    $html[]='<p style="'.$style.'">'.t('Integrated services').':</p>';
    $html[]='</div>';
    return implode('',$html);
}
//intelsat-2015
function hontza_canal_rss_add_user_img_canal($url,$orig_url){
    global $base_root;
    $result='';
    if(hontza_canal_rss_is_canal_usuarios($url,$my_array,$pos)){
        $pos=$pos+1;
        if(isset($my_array[$pos])){
            $uid=$my_array[$pos];
            $my_user=new stdClass();
            if($uid!='all'){
                $my_user=user_load($uid);
            }
            if($uid=='all' || isset($my_user->uid) && !empty($my_user->uid)){
                $result=hontza_get_canal_usuarios_title($uid);
                $result=my_get_icono_action('user',$result);
                $result=l($result,$base_root.$orig_url,array('absolute'=>true,'html'=>true));
            }
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_canal_usuarios($url,&$my_array,&$pos){
    $result=0;
    $pos=-1;
    $my_array=explode('/',$url);
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if($value=='canal-usuarios'){
                $pos=$i;
                $result=1;
            }
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_prepare_canal_usuario($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $result[$i]->node_node_data_field_item_canal_reference_title=str_replace(t('User').': ','',$row->node_node_data_field_item_canal_reference_title);            
        }    
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_canal_rss_link($url,$canal_nid_in=''){
    $result='';
    if(empty($canal_nid_in)){
        $url=str_replace('/canales/','/canales_rss/',$url);
        $url=hontza_solr_search_prepare_redirect_url($url);
        $url_info=parse_url($url);
        $canal_nid=hontza_canal_rss_get_canal_rss_nid($url_info['path']);
    }else{
        $canal_nid=$canal_nid_in;
        $url_info['path']=$canal_nid;
    }
    $title=t('Export channel');
    if(hontza_canal_rss_is_publico_exportar_rss_enviar_mail($canal_nid,$url_info['path'])){
        //$result=l(my_get_icono_action('publico_solr_results_rss', t('RSS'),'left_rss'),$url_info['path'].'/red_exportar_rss/enviar_mail',array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('class'=>'jqm-trigger-red_exportar_rss_enviar_mail','target'=>'_blank')));        
        //$label=t('Export RSS ON');
        $canal_usuarios_uid='';
        $url=$url_info['path'].'/red_exportar_rss/enviar_mail';
        //intelsat-2016
        $url_konp=$url;
        $url=str_replace('canales_rss/canal-usuarios','canales_rss_canal_usuarios',$url);
        if($url==$url_konp){
            $result=l(my_get_icono_action('publico_solr_results_rss',$title,'left_rss'),$url,array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('class'=>'jqm-trigger-red_exportar_rss_enviar_mail_'.$canal_nid)));                
            $result.='<div id="exred_exportar_rss_enviar_mail_'.$canal_nid.'" class="jqmWindow jqmID2000"></div>';        
        }else{
            $my_array=explode('/',$url_info['path']);
            $canal_usuarios_uid=$my_array[3];
            $result=l(my_get_icono_action('publico_solr_results_rss',$title,'left_rss'),$url,array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('class'=>'jqm-trigger-red_exportar_rss_canal_usuarios_enviar_mail_'.$canal_usuarios_uid)));                
            $result.='<div id="exred_exportar_rss_canal_usuarios_enviar_mail_'.$canal_usuarios_uid.'" class="jqmWindow jqmID2000"></div>';                
        }
        hontza_canal_rss_add_red_exportar_rss_enviar_mail_js($canal_nid,$canal_usuarios_uid);        
    }else{
        $icono='solr_results_rss';
        //$title=t('RSS');        
        //if(hontza_canal_rss_is_publico_exportar_rss_enviar_mail_desactivado($canal_nid)){
            $icono='export_rss_off';
            //$title=t('Export RSS OFF');
            $title=t('Export OFF');
            $result=my_get_icono_action($icono,$title,'left_rss');
        //}else{                    
        //    $result=l(my_get_icono_action($icono,$title,'left_rss'),$url_info['path'],array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('target'=>'_blank')));
        //}    
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_canal_rss_nid($url_info_path){
    $result='';
    $my_array=explode('/',$url_info_path);
    $pos=array_search ('canales_rss', $my_array);
    $pos=$pos+1;
    if(isset($my_array[$pos])){
        $result=$my_array[$pos];
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_publico_exportar_rss_activado($canal_nid='',$url_info_path=''){
    global $user;
    $result=0;    
    if(module_exists('red_exportar_rss')){
        //if(red_is_subdominio_red_alerta() && $canal_nid!='canal-usuarios'){
        //intelsat-2016
        //if(red_is_publico_exportar_rss_activado() && $canal_nid!='canal-usuarios'){    
        if(red_is_publico_exportar_rss_activado()){
            if($canal_nid!='canal-usuarios'){
                $result=1;
            }else{
                if(hontza_canal_rss_is_administrador_grupo_exportar_canal_usuario()){
                    $result=1;
                }else{    
                    $my_array=explode('/',$url_info_path);
                    if(isset($my_array[3]) && !empty($my_array[3])){
                        $uid=$my_array[3];
                        if($user->uid==$uid){
                            $result=1;
                        }
                    }
                }    
            }    
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_publico_exportar_rss_enviar_mail($canal_nid,$url_info_path=''){
    $result=0;
    if(hontza_canal_rss_is_publico_exportar_rss_activado($canal_nid,$url_info_path)){
        if(red_exportar_rss_enviar_mail_canales_rss_access()){
            $result=1;
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_red_exportar_rss_enviar_mail_canales_rss(){
    $result=0;
    $param2=arg(2);
    if(!empty($param2) && $param2=='red_exportar_rss'){
        $param3=arg(3);
        if(!empty($param3) && $param3=='enviar_mail'){
            $result=1;
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_visualizador_inicio(){
    $result=0;
    if(hontza_canal_rss_is_visualizador_activado()){
        $result=visualizador_is_pantalla('inicio');
    }
    return $result;
}
//intelsat-2015
//intelsat-2016
//function hontza_canal_rss_add_red_exportar_rss_enviar_mail_js($canal_nid){
function hontza_canal_rss_add_red_exportar_rss_enviar_mail_js($canal_nid,$canal_usuarios_uid='',$is_view=0){    
    if(module_exists('red_exportar_rss')){
        //intelsat-2016
        red_exportar_rss_enviar_mail_add_js($canal_nid,$canal_usuarios_uid,$is_view);
    }
}
//intelsat-2015
function hontza_canal_rss_is_add_push_div(){
    return 0;
    /*if(hontza_is_tag_node_pantalla()){
        return 0;
    }
    return 1;*/
}
//intelsat-2015
function hontza_canal_rss_get_logos_apis_background_color(){
    $result='#CCCCCC';
    //$result='#808080';
    $result='background-color:'.$result.';';
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_tipo_fuente($tid){
    $result=0;
    $term=taxonomy_get_term($tid);
    if(isset($term->vid) && !empty($term->vid)){
        if($term->vid==1){
            $result=1;
        }
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_get_noticias_tipo_fuente_title($term_name){
    return t('News in Type of Source').': '.$term_name;
}
//intelsat-2015
function hontza_canal_rss_is_item_categorias(){
    if(hontza_solr_is_solr_activado()){
        return 1;
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        return 1;
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_set_flag_link($link){
    $result=$link;
    $find='rel="nofollow">';
    $pos=strpos($link,$find);
    if($pos===FALSE){
        return $result;
    }
    $s=substr($link,$pos+strlen($find));
    $pos2=strpos($s,'</a>');
    $s2=substr($s,$pos2);
    //$title='&nbsp;';
    $title='';
    $result=substr($link,0,$pos+strlen($find)).$title.$s2;
    return $result;
}
//intelsat-2015
function hontza_canal_rss_existe_content_field_item_canal_category_tid_vid_delta($vid,$delta){
    if(hontza_solr_is_solr_activado()){
        $res=db_query('SELECT * FROM {content_field_item_canal_category_tid} WHERE vid=%d AND delta=%d',$vid,$delta);
        while($row=db_fetch_object($res)){
            return 1;
        }
    }    
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_node_banner($node_row){
    if(hontza_canal_rss_is_visualizador_activado()){
        return publico_get_node_banner($node_row);
    }
    return $node_row;
}
//intelsat-2015
function hontza_canal_rss_is_pantalla_banner_node(&$banner_node){
    $banner_node=my_get_node();
    if(isset($banner_node->type) && !empty($banner_node->type)){
        if($banner_node->type=='banner'){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_wrapper_id(){
    $result='wrapper';
    if(red_is_new_font()){    
        $result='new_font_wrapper';
    }else if(hontza_canal_rss_is_visualizador_activado()){
        $result='visualizador_wrapper';    
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_publico_exportar_rss_enviar_mail_desactivado($canal_nid){
    $result=0;
    if(hontza_canal_rss_is_publico_exportar_rss_activado($canal_nid)){
        if(!red_exportar_rss_enviar_mail_canales_rss_access()){
            $result=1;
        }
    }else{
        //intelsat-2016
        $result=1;
    }
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_usuario_basico(){
    if(module_exists('usuario_basico')){
        return usuario_basico_is_usuario_basico();
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_get_usuario_basico_menu_primary_links($links){
    $result=$links;
    if(hontza_canal_rss_is_usuario_basico()){
        $result=usuario_basico_get_usuario_basico_menu_primary_links($result);
    }
    return $result;
}
//intelsat-2015    
function hontza_canal_rss_is_usuario_basico_activado(){
    if(module_exists('usuario_basico')){
        return usuario_basico_is_activado();
    }
    return 0;
}
//intelsat-2015 
function hontza_canal_rss_usuario_basico_on_grupo_inicio_save(&$node, $op){
    if(hontza_canal_rss_is_usuario_basico_activado()){
        usuario_basico_on_grupo_inicio_save($node, $op);
    }
}
//intelsat-2015
function hontza_canal_rss_unset_node_view_links($result_in){
    return str_replace('<div class="links">','<div class="links" style="display:none">',$result_in);
}
//intelsat-2015
function hontza_canal_rss_usuario_basico_access_denied(){
    if(hontza_canal_rss_is_usuario_basico_activado()){
        usuario_basico_access_denied();
    }
}
//intelsat-2015
function hontza_canal_rss_is_canal_busqueda_solr($canal_nid,$canal_in=''){
    $value='';
    if(!empty($canal_in)){
        $canal=$canal_in;
    }else{
        $canal=node_load($canal_nid);
    }
    if(isset($canal->nid) && !empty($canal->nid)){
        $value=$canal->field_canal_busqueda_busqueda[0]['value'];
        if(hontza_solr_is_solr_activado() && hontza_solr_is_busqueda_solr($value)){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_is_kimonolabs_json($content){
    if(hontza_canal_rss_is_kimonolabs_activado()){
        if(kimonolabs_is_kimonolabs_json($content)){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_is_kimonolabs_json_url($url){
    if(hontza_canal_rss_is_kimonolabs_activado()){
        if(kimonolabs_is_kimonolabs_json_url($url)){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function hontza_canal_rss_canal_node_delete_confirm_form_alter(&$form,&$form_state, $form_id){
    $canal_node=hontza_get_node_by_form($form);
    $num=hontza_canal_rss_get_canal_numero_noticias($canal_node->nid);
    $form['description']['#value'].='<p>'.t('They will be deleted @num news',array('@num'=>$num)).'.</p>';
    $form['#redirect']='vigilancia/pendientes';
}
//intelsat-2015
function hontza_canal_rss_get_canal_numero_noticias($canal_nid){
    $nid_array=hontza_canal_rss_get_canal_noticias_nid_array($canal_nid);
    $result=count($nid_array);
    return $result;
}
//intelsat-2015
function hontza_canal_rss_is_visualizador_grupos_colaborativos(){
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_is_visualizador_grupos_colaborativos();         
    }
    return 0;    
}
function hontza_canal_rss_is_visualizador_defined(){
    if(defined('_IS_VISUALIZADOR')){
        if(_IS_VISUALIZADOR==1){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_visualizador_is_red_alerta(){
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_is_red_alerta();         
    }
    return 0;  
}
function hontza_canal_rss_is_visualizador_anonimo(){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(hontza_is_user_anonimo()){
            return 1;
        }
    }
    return 0;
}
function hontza_canal_rss_get_yahoo_developer_network_logo(){
    global $base_url;
    $html=array();
    $html[]='<div style="float:left;padding-left:10px;">';
    $img='<img src="'.$base_url.'/sites/default/files/yahoo_developer_network.png'.'" height="25"/>';
    $html[]=l($img,'https://developer.yahoo.com/',array('attributes'=>array('target'=>'_blank'),'absolute'=>true,'html'=>true));
    $html[]='</div>';
    return implode('',$html);
}
function hontza_canal_rss_is_administrador_grupo_exportar_canal_usuario(){
    if(red_crear_usuario_is_rol_administrador_creador_grupo()){
        return 1;
    }
    $modo_estrategia=1;
    if(is_administrador_grupo($modo_estrategia)){
        return 1;
    }
    return 0;
}
//intelsat-2016
function hontza_canal_rss_get_porcentaje_validated_news($info_porcentajes){
    return $info_porcentajes['porcentaje_validated_news'];    
}
//intelsat-2016
function hontza_canal_rss_get_rating_validated_news($info_porcentajes){
    return $info_porcentajes['rating_validated_news'];
}
//intelsat-2016
function hontza_canal_rss_get_porcentaje_news_to_bulletins($info_porcentajes){
    return $info_porcentajes['news_to_bulletins'];
}
//intelsat-2016
function hontza_canal_rss_get_noticias_enviados_boletin($canal_nid,$is_porcentajes=0){
    $result=array();
    $canal_noticias=my_get_canal_noticias($canal_nid,$is_porcentajes);
    if(!empty($canal_noticias)){
        foreach($canal_noticias as $i=>$nid){
            $node=node_load($nid);
            if(boletin_report_is_noticia_en_boletines($node)){
                $result[]=$node;
            }
        }
    }
    return $result;
}
//intelsat-2016
function hontza_canal_rss_get_info_porcentajes($canal_node){
    $canal_nid=$canal_node->nid;
    $is_porcentajes=1;
    $canal_noticias=my_get_canal_noticias($canal_nid,$is_porcentajes);
    $canal_validados=my_get_canal_validadas($canal_nid,$is_porcentajes);
    //print count($canal_noticias).'-'.count($canal_validados);
    $info['porcentaje_validated_news']=0;
    $num_canal_noticias=count($canal_noticias);
    if($num_canal_noticias>0){
        $info['porcentaje_validated_news']=round((count($canal_validados)/$num_canal_noticias)*100,2);
    }
    $result=0;
    if(!empty($canal_validados)){
        foreach($canal_validados as $i=>$nid){
            $result+=hontza_get_node_puntuacion_media_para_txt($nid,1);
        }
    }
    $num_canal_validados=count($canal_validados);
    $info['rating_validated_news']=0;
    $info['news_to_bulletins']=0;
    if($num_canal_validados>0){
        $info['rating_validated_news']=round($result/$num_canal_validados,2);
        $canal_boletin_enviados=hontza_canal_rss_get_noticias_enviados_boletin($canal_nid,$is_porcentajes);
        $info['news_to_bulletins']=round((count($canal_boletin_enviados)/$num_canal_validados)*100,2);
    }
    return $info;
}
//intelsat-2016
function hontza_canal_rss_is_canal_fivestar_validados($node_type){
    if(in_array($node_type,array('canal_de_yql'))){
        return 1;
    }
    return 0;
}
//intelsat-2016
function hontza_canal_rss_fivestar_static($content_type, $content_id, $tag = 'vote', $node_type = NULL,$my_value='') {
  global $user;

  $criteria = array(
    'content_type' => $content_type,
    'content_id' => $content_id,
    'value_type' => 'percent',
    'tag' => 'vote',
  );
  if(!empty($my_value)){
    $votes['average']['value']=$my_value;  
  }else{
    $votes = fivestar_get_votes($content_type, $content_id, $tag);
  }  
  if ($content_type == 'node') {
    // Content type should always be passed to avoid this node load.
    if (!isset($node_type)) {
      $node = node_load($content_id);
      $node_type = $node->type;
    }

    $star_display = variable_get('fivestar_style_'. $node_type, 'average');
    $text_display = variable_get('fivestar_text_'. $node_type, 'dual');
    $title_display = variable_get('fivestar_title_'. $node_type, 1);

    $stars = variable_get('fivestar_stars_'. $node_type, 5);
    switch ($star_display) {
      case 'average':
      case 'dual':
        $star_value = $votes['average']['value'];
        $title = $title_display ? t('Average') : NULL;
        break;
      case 'user':
        $star_value = $votes['user']['value'];
        $title = $title_display ? t('Your rating') : NULL;
        break;
      case 'smart':
        $star_value = $votes['user']['value'] ? $votes['user']['value'] : $votes['average']['value'];
        $title = $title_display ? $votes['user']['value'] ? t('Your rating') : t('Average') : NULL;
        break;
    }

    // Set all text values, then unset the unnecessary ones.
    $user_value = $votes['user']['value'];
    $average_value = $votes['average']['value'];
    $count_value = $votes['count']['value'];
    switch ($text_display) {
      case 'average':
        $user_value = NULL;
        break;
      case 'user':
        $average_value = NULL;
        break;
      case 'smart':
        if ($votes['user']['value']) {
          $average_value = NULL;
        }
        else {
          $user_value = NULL;
        }
        break;
    }
  }
  // Possibly add other content types here (comment, user, etc).
  else {
    $stars = 5;
    $star_value = $votes['average']['value'];
    $user_value = $votes['user']['value'];
    $average_value = $votes['average']['value'];
    $count_value = $votes['count']['value'];
  }

  $star_display = theme('fivestar_static', $star_value, $stars);
  $text_display = $text_display == 'none' ? NULL : theme('fivestar_summary', $user_value, $average_value, $count_value, $stars, FALSE);
  $result=theme('fivestar_static_element', $star_display,'','');
  $result='<div style="float:left"><div style="float:left">'.$my_value.'&nbsp;</div><div style="float:left">'.$result.'</div></div>';
  return $result;
}
//intelsat-2016
function hontza_canal_rss_get_base_path_help_popup(){
    global $base_path;
    if($base_path=='/hontza5/'){
        return $base_path;
    }
    return '/';
}