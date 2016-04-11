<?php
function red_despacho_is_activado(){
    if(module_exists('despacho')){
        if(alerta_solr_is_cron()){
            //if(hontza_is_sareko_id('DESPACHO')){
                return 1;
            //}
        }
        if(defined('_IS_DESPACHO')){
            if(_IS_DESPACHO==1){
                return 1;
            }
        }
    }
    return 0;
}
function red_despacho_is_show_custom_activo_pestana($key){
    if(red_despacho_is_activado()){
        return despacho_is_show_custom_activo_pestana($key);
    }
    return 0;
}
function red_despacho_is_show_lo_mas_comentado(){
    if(red_despacho_is_activado()){
        return despacho_is_show_lo_mas_comentado();
    }
    return 1;
}
function red_despacho_is_show_lo_mas_valorado(){
    return red_despacho_is_show_lo_mas_comentado();
}
function red_despacho_is_show_boletin_report_link(){
    if(red_despacho_is_activado()){
        return despacho_is_show_boletin_report_link();
    }
    return 1;
}
function red_despacho_is_show_comment_link(){
    if(red_despacho_is_activado()){
        return despacho_is_show_comment_link();
    }
    return 1;
}
function red_despacho_get_source_types_title(){
    $icono=my_get_icono_action('tipos_de_fuentes',$label);
    return $icono.'&nbsp;'.t('Types of Sources');
}
function red_despacho_get_source_types_block_content(){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_source_types_content();
    }
    return '';
}
function red_despacho_is_vigilancia(){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_vigilancia();
    }
    return 0;
}
function red_despacho_is_vigilancia_left(){
    return red_despacho_is_vigilancia();
}
function red_despacho_get_canales_por_categorias_url_tab($tid,$type,$type_tab){
    $result='canales/my_categorias/'.$tid.'/'.$type_tab;
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_canales_por_categorias_url_tab($tid,$type,$result,$type_tab);
    }
    return $result;
}
function red_despacho_get_selected_tipos_fuente(){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_selected_tipos_fuente();
    }
    return '';
}
function red_despacho_add_filter_block_term(&$html,$tid){
    $term_name=taxonomy_get_term_name_by_idioma($tid);
    if(!empty($term_name)){
        $link=red_despacho_get_term_link_delete_filter($term_name);
        $html[]='<li'.red_despacho_get_li_style().'>'.$link.'</li>';
    }    
}
function red_despacho_get_li_style(){
    $result=' style="list-style-image: none;list-style-type: none;"';
    return $result;
}
function red_despacho_get_term_link_delete_filter($term_name){
    $url_cat='cambiar_consulta_canales_my_categorias_block';
    $img=my_get_icono_action('delete_solr_filter', t('Delete filter'));
    $link=l($img.$term_name,$url_cat,array('html'=>true,'attributes'=>array('class'=>'active')));
    return $link;
}
function red_despacho_vigilancia_order_term_array($term_array){
    $result=array_ordenatu($term_array,'name','asc',0);
    return $result;
}
function red_despacho_is_types_of_sources_term($tid){
    $term=taxonomy_get_term($tid);
    if(isset($term->vid) && !empty($term->vid) && $term->vid==1){
        return 1;
    }
    return 0;
}
function red_despacho_is_show_categorizar_link(){
    if(red_despacho_is_activado()){
        return despacho_is_show_categorizar_link();
    }
    return 0;
}
function red_despacho_get_categorizar_link($node){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_categorizar_link($node);
    }
    return 0;
}
function red_despacho_add_category_checked($node,$contenido,&$form){
    if(red_despacho_is_activado()){
        despacho_vigilancia_add_category_checked($node,$contenido,$form);
    }
}
function red_despacho_on_node_save($op,&$node){
    if(red_despacho_is_activado()){
        despacho_vigilancia_on_node_save($op,$node);        
    }
    if($node->type=='item'){
        //intelsat-2016
        if(red_despacho_vigilancia_is_item_post_url_save()){
            red_despacho_vigilancia_item_post_url_save($op,$node);
        }
    }
}
function red_despacho_get_node_taxonomy_tid_array($node,$id_categoria_in='',$is_value=0){
    $result=array();
    if(!empty($id_categoria_in)){
        $id_categoria=$id_categoria_in;
    }else{
        $id_categoria=hontza_canal_rss_get_grupo_id_categoria();
    }    
    if(!empty($id_categoria)){
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            foreach($node->taxonomy as $i=>$term){
                if(isset($term->vid) && !empty($term->vid) && $term->vid==$id_categoria){
                    if(isset($term->tid) && !empty($term->tid)){
                        if($is_value){
                            $result[]=array('value'=>$term->tid);
                        }else{
                            $result[]=$term->tid;
                        }    
                    }
                }
            }
        }
    }
    return $result;
}
function red_despacho_vigilancia_get_node_categorias_tematicas_tid_array($node){
    $result=array();
    if(in_array($node->type,array('item'))){
        $result=hontza_solr_get_content_field_item_canal_category_tid($node->nid,$node->vid,0);
    }else if(in_array($node->type,array('noticia'))){
        $result=red_despacho_get_node_taxonomy_tid_array($node);
    }
    return $result;
}
function red_despacho_get_validate_status($node){
    $flag_content_array=hontza_get_flag_content_array($node->nid);
    //sin validar=1, validado=2, rechazado=3
    $validate_status=1;
    if(!empty($flag_content_array)){
        $flag_row=$flag_content_array[0];
        if(isset($flag_row->fid)){
            $validate_status=$flag_row->fid;
        }
    }
    return $validate_status;
}
function red_despacho_modificar_taxonomia_save(&$form_state,&$node_array,$node_id_array){
    $node_array=array();
    $id_categoria=$form_state['values']['id_categoria'];
    //
    $tid_array=hontza_solr_search_get_form_state_values_tid_array($form_state['values'],$id_categoria);
    //$value_array=hontza_solr_search_set_value_array($tid_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            $node_array[]=$node;
            if(isset($node->nid) && !empty($node->nid)){
                if($node->type=='item'){
                    if(hontza_solr_is_solr_activado()){
                        //intelsat-2015
                        if(!(isset($node->field_item_canal_category_tid))){
                            $node->field_item_canal_category_tid=array();
                        }
                        //
                            if(hontza_solr_search_is_replace_categorias($form_state,'replace_categories_btn')){
                                hontza_solr_search_modificar_item_categorias($node,$tid_array);
                            }else if(hontza_solr_search_is_replace_categorias($form_state,'add_categories_btn')){
                                hontza_solr_search_add_item_categorias($node,$tid_array); 
                            }
                            //intelsat-2015
                            hontza_canal_rss_solr_clear_node_index($node,$nid);
                            //                                                
                    }                    
                }else if($node->type=='noticia'){
                    if(hontza_solr_search_is_replace_categorias($form_state,'replace_categories_btn')){
                        hontza_solr_search_modificar_noticia_usuario_categorias($node,$tid_array);
                    }else if(hontza_solr_search_is_replace_categorias($form_state,'add_categories_btn')){
                        hontza_solr_search_add_noticia_usuario_categorias($node,$tid_array); 
                    }
                }
            }
        }
    }
}
function red_despacho_is_categorizar_noticia_pantalla($type){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_categorizar_noticia_pantalla($type);
    }
    return 0;
}
function red_despacho_get_wrapper_style(){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_wrapper_style();
    }
    return '';
}
function red_despacho_is_on_categorizar_noticia_pantalla($type){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_on_categorizar_noticia_pantalla($type);
    }
    return 0;
}
function red_despacho_boletin_report_add_tipos_fuente_fieldset($row,&$form){
    if(red_despacho_is_activado()){
        despacho_boletin_report_add_tipos_fuente_fieldset($row,$form);
    }
}
function red_despacho_boletin_report_is_content_field_checked($field){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_is_content_field_checked($field);
    }    
    return 1;
}
function red_despacho_is_show_user_image(){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_show_user_image();
    }
    return hontza_canal_rss_is_show_user_image();
}
function red_despacho_unset_hound_id_item_title(&$node,$canal_nid){
    if(red_despacho_is_activado()){
        despacho_vigilancia_unset_hound_id_item_title($node,$canal_nid);
    }
}
function red_despacho_item_node_form_alter(&$form,&$form_state, $form_id){
    //if(red_despacho_is_activado()){
        if($form_id=='item_node_form'){
            $nid=hontza_get_nid_by_form($form);
            $url=hontza_get_item_url_enlace($nid,'',0);
            $form['despacho_item_url']=array(
                '#type'=>'textfield',
                '#title'=>t('Url'),
                '#default_value'=>$url,
                '#maxlength'=>10000,
            );
            $form['title']['#maxlength']=1024;
            /*hontza_solr_search_add_source_type_form_field($form,0,1);
            red_despacho_set_node_source_type_selected_form_field($form,$form_id,$nid);
            if(isset($form['field_item_source_tid'])){
                unset($form['field_item_source_tid']);
            }*/
            red_despacho_node_add_source_type_form_field($form,$form_id,$nid);
        }        
    //}    
}
function red_despacho_get_feeds_node_item_row($nid){
    $res=db_query('SELECT * FROM {feeds_node_item} WHERE nid=%d',$nid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_despacho_vigilancia_item_post_url_save($op,$node){
    if($op=='update'){    
        if(isset($_POST['despacho_item_url']) && !empty($_POST['despacho_item_url'])){
            $feeds_node_item_row=red_despacho_get_feeds_node_item_row($node->nid);
            if(isset($feeds_node_item_row->nid) && !empty($feeds_node_item_row->nid)){
                if(isset($feeds_node_item_row->url) && !empty($feeds_node_item_row->url)){
                    db_query('UPDATE {feeds_node_item} SET url="%s" WHERE nid=%d',$_POST['despacho_item_url'],$node->nid);
                }else{
                    db_query('UPDATE {feeds_node_item} SET guid="%s" WHERE nid=%d',$_POST['despacho_item_url'],$node->nid);
                }
                hontza_solr_search_clear_cache_content($node);
            }
        }
    }    
}
function red_despacho_get_item_url_enlace_view($node){
    $url=hontza_get_item_url_enlace('',$node,0);
    $max=89;
    $title=$url;
    /*$len=strlen($title);
    if($len>$max){
        $title=substr($url,0,$max);
        $title.=' ...';
    }*/
    $result=l($title,$url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
    return $result;
}
function red_despacho_boletin_report_save_tipos_fuente($id,$values){
    if(red_despacho_is_activado()){
        despacho_boletin_report_save_tipos_fuente($id,$values);
    }
}
function red_despacho_boletin_report_get_tipos_fuente_tid_array($id){
    $result=array();
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_tipos_fuente_tid_array($id);
    }
    return $result;
}
function red_despacho_boletin_report_in_tipos_fuente($nid,$tipos_fuente,$grupo_nid,$node_type,&$in_tipos_fuente){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_in_tipos_fuente($nid,$tipos_fuente,$grupo_nid,$node_type,$in_tipos_fuente);
    }
    return 0;
}
function red_despacho_boletin_report_por_tipos_fuente($br,&$html,$items){
    if(red_despacho_is_activado()){
        despacho_boletin_report_por_tipos_fuente($br,$html,$items);
    }
}
function red_despacho_boletin_report_get_tipos_fuente_boletin_title($title,$tipos_fuente_row){
    $result=$title;
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_tipos_fuente_boletin_title($title,$tipos_fuente_row);
    }
    $result=my_upper($result);
    return $result;
}
function red_despacho_boletin_report_get_boletin_despedida($despedida){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_boletin_despedida($despedida);
    }
    return $despedida;
}
function red_despacho_get_boletin_introduccion($introduccion,$boletin_report_titulo_mail){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_boletin_introduccion($introduccion,$boletin_report_titulo_mail);
    }
    return $introduccion;
}
function red_despacho_boletin_report_is_show_comments($bg,$param_alerta){
    //if(isset($bg->is_boletin_report) && !empty($bg->is_boletin_report)){
    if(isset($bg->id) && !empty($bg->id)){
        if(isset($bg->apply_limite_resumen_comentario) && !empty($bg->apply_limite_resumen_comentario)){
            if(isset($bg->limite_resumen_comentario) && $bg->limite_resumen_comentario<=0){
                return 0;
            }
        }
        return 1;
    }
    /*if(red_despacho_is_activado()){
        return 0;
    }*/
    if(isset($param_alerta->limite_resumen_comentario)){
        if($param_alerta->limite_resumen_comentario<=0){
            return 0;
        }
    }
    return 1;
}
function red_despacho_boletin_report_get_limite_resumen_comentario($row,$is_alerta_personalizada=0){        
    if(red_despacho_is_activado()){
        if(!(isset($row->id) && !empty($row->id))){
            return 0;
        }
    }
    if(isset($row->id) && !empty($row->id)){
        return $row->limite_resumen_comentario;
    }
    $max=boletin_grupo_define_max_limite_resumen_comentario();
    if($is_alerta_personalizada){
        $max=alerta_define_max_limite_resumen_comentario();
    }
    $result=get_alerta_my_limite($row->limite_resumen_comentario,$max);    
    return $result;
}
function red_despacho_boletin_report_get_limite_resumen($row,$is_alerta_personalizada=0){        
    if(red_despacho_is_activado()){
        if(!(isset($row->id) && !empty($row->id))){
            return 0;
        }
    }
    if(isset($row->id) && !empty($row->id)){
        return $row->limite_resumen;
    }
    $max=boletin_grupo_define_max_limite_resumen();
    if($is_alerta_personalizada){
        $max=alerta_define_max_limite_resumen();
    }
    $result=get_alerta_my_limite($row->limite_resumen,$max);    
    return $result;
}
function red_despacho_boletin_report_is_show_resumen($bg,$param_alerta){
    //if(isset($bg->is_boletin_report) && !empty($bg->is_boletin_report)){
    if(isset($bg->id) && !empty($bg->id)){
        if(isset($bg->apply_limite_resumen) && !empty($bg->apply_limite_resumen)){
            if(isset($bg->limite_resumen) && $bg->limite_resumen<=0){
                return 0;
            }
        }
        return 1;
    }
    /*if(red_despacho_is_activado()){
        return 0;
    }*/
    if(isset($param_alerta->limite_resumen)){
        if($param_alerta->limite_resumen<=0){
            return 0;
        }
    }
    return 1;
}
function red_despacho_boletin_report_get_th_categoria_title_class($tipos_fuente_row){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_th_categoria_title_class($tipos_fuente_row);
    }
    return '';
}
function red_despacho_is_noticia_usuario_source_type_show($node){
    if(isset($node->field_item_source_tid)){
        return 1;
    }
    return 0;        
}
function red_despacho_set_node_source_type_selected_form_field(&$form,$form_id,$nid){
    /*$my_array=red_solr_inc_set_form_field_item_source_tid_default_value($form['field_item_source_tid']['#default_value'],$nid,$form_id);
    $values=array();
    if(!empty($my_array)){
        foreach($my_array as $i=>$row){
            $values[]=$row['value'];
        }
    }*/
    $tid_array=red_despacho_get_form_field_item_source_tid_array($form,$form_id,$nid);
    $form['taxonomia_fs']['taxonomia']['#default_value']=$values;
}
function red_despacho_node_add_source_type_form_field(&$form,$form_id,$nid,$default_value=''){
    if(isset($form['field_item_source_tid'])){
        //hontza_solr_search_add_source_type_form_field($form,0,1);
        //red_despacho_set_node_source_type_selected_form_field($form,$form_id,$nid);        
        red_despacho_add_source_type_form_field($form,$form_id,$nid);
        //if(isset($form['field_item_source_tid'])){
        unset($form['field_item_source_tid']);
        //}
    }else if(isset($form['field_canal_source_type'])){
        red_despacho_add_source_type_form_field($form,$form_id,$nid,$default_value);
        unset($form['field_canal_source_type']);
    }else if($form_id=='supercanal_node_form'){
        red_despacho_add_source_type_form_field($form,$form_id,$nid);
    }    
}
function red_despacho_node_item_source_tid_presave(&$node){
    if(in_array($node->type,array('item','noticia'))){
        if(isset($_POST['form_id']) && !empty($_POST['form_id']) && in_array($_POST['form_id'],array('item_node_form','noticia_node_form'))){
            /*if(isset($node->field_item_source_tid)){
                unset($node->field_item_source_tid);
            }*/
            if(red_despacho_is_node_item_source_tid_presave_activado($_POST['form_id'])){
                $node->field_item_source_tid=array();
                /*if(isset($_POST['taxonomia'])){
                    $node->field_item_source_tid=array();
                    foreach($_POST['taxonomia'] as $tid=>$value){
                        $row=array();
                        $row['value']=$tid;
                        $node->field_item_source_tid[]=$row;
                    }
                }*/
                $values=red_despacho_boletin_report_get_tipos_fuente_by_values($_POST,'my_source_type_');
                if(!empty($values)){
                    foreach($values as $i=>$tid){
                        $row=array();
                        $row['value']=$tid;
                        $node->field_item_source_tid[]=$row;
                    }
                }    
            }    
        }
    }else if(in_array($node->type,array('canal_de_supercanal','canal_de_yql','canal_usuario'))){
        red_despacho_canal_source_type_presave($node);
    }else if(in_array($node->type,array('supercanal'))){
        red_despacho_supercanal_source_type_presave($node);
    }
}
function red_despacho_is_node_item_source_tid_presave_activado($form_id){
    if($form_id=='noticia_node_form'){    
        if(red_solr_inc_is_noticia_usuario_source_type_activado($form_id)){
            return 1;
        }
        return 0;
    }    
    return 1;
}
function red_despacho_add_source_type_form_field(&$form,$form_id,$nid,$default_value=''){
    $tid_array=red_despacho_get_form_field_item_source_tid_array($form,$form_id,$nid,$default_value);    
    $source_type_array=taxonomy_get_custom_tree(1);    
    if(!empty($source_type_array)){
        $form['node_source_type_fs']=array(
            '#type'=>'fieldset',
            '#title'=>t('Source Types'),
        );                        
        $num=count($source_type_array);
                foreach ($source_type_array as $id => $contenido) {
                     //if(boletin_report_is_parent_zero($contenido)){   
                        //$pro=0;
                        //intelsat-2015
                        $pro=profundidad($contenido->tid);
                        $key='my_source_type_'.$contenido->tid;
                        //
                        $div_prefix='';
                        $div_suffix='';
                        /*if($kont==0){
                            $div_prefix='<div id="id_despacho_boletin_report_tipos_fuente"><label><b>'.t('Types os Sources').'</b></label>';
                        }else if($kont==($num-1)){
                            $div_suffix='</div></div>';
                        }*/
                        $form['node_source_type_fs'][$key] = array(
                          '#required' => TRUE,
                          '#type' => 'checkbox',
                          '#prefix' => $div_prefix.'<div class=taxo'. $pro .'>',
                          '#suffix' => '</div>'.$div_suffix,
                          '#title' => $contenido->name
                        );
                        if(in_array($contenido->tid,$tid_array)){
                           $form['node_source_type_fs'][$key]['#attributes']=array('checked' => 'checked');
                        }
                        $kont++;
                     //}   
                  }
    }
}
function red_despacho_get_form_field_item_source_tid_array(&$form,$form_id,$nid,$default_value=''){
    if(in_array($form_id,array('item_node_form','noticia_node_form'))){
        $my_array=red_solr_inc_set_form_field_item_source_tid_default_value($form['field_item_source_tid']['#default_value'],$nid,$form_id);
    }else if(in_array($form_id,array('supercanal_node_form'))){
        $node=$form['#node'];
        //$node=hontza_get_node_by_form($form);
        $my_array=red_despacho_get_node_taxonomy_tid_array($node,1,1);
    }else{
        $my_array=$default_value;
    }
    $values=array();
    if(!empty($my_array)){
        foreach($my_array as $i=>$row){
            $values[]=$row['value'];
        }
    }
    return $values;
}
function red_despacho_boletin_report_get_tipos_fuente_by_values($values,$search='my_source_type_'){    
    $result=array();
    if(!empty($values)){
        foreach($values as $field=>$value){
            $pos=strpos($field,$search);
            if($pos===FALSE){
                continue;
            }else{
                if(!empty($value)){
                    $result[]=str_replace($search,'',$field);
                }
            }
        }
    }
    return $result;
}
function red_despacho_boletin_report_get_styles_inline($url_layout,$url_style,$hasi_url){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_styles_inline($url_layout,$url_style,$hasi_url);
    }
    return '';
}
function red_despacho_boletin_report_is_styles_inline(){
    if(defined('IS_DESPACHO_BOLETIN_REPORT_STYLES_INLINE') && IS_DESPACHO_BOLETIN_REPORT_STYLES_INLINE==1){
        return 1;
    }
    return 0;
}
function red_despacho_canal_source_type_presave(&$node){
    if(in_array($node->type,array('canal_de_supercanal','canal_de_yql','canal_usuario'))){
       $type_array=array('canal_de_supercanal_node_form','canal_de_yql_node_form','canal_usuario_node_form'); 
       if(isset($_POST['form_id']) && !empty($_POST['form_id']) && in_array($_POST['form_id'],$type_array)){
            if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
                $node->field_canal_source_type=array();
                $values=red_despacho_boletin_report_get_tipos_fuente_by_values($_POST,'my_source_type_');
                if(!empty($values)){
                    foreach($values as $i=>$tid){
                        $row=array();
                        $row['value']=$tid;
                        $node->field_canal_source_type[]=$row;
                    }
                }    
            }    
        }
    }
}
function red_despacho_set_tree_source_type_form_field(&$form,$taxo,$node='',$form_state=''){
    unset($form['taxonomia_fs']['taxonomia']['#options']);
    if(!empty($taxo)){
        $kont=0;
        $tid_array=array();
        $is_reclasificar_tipo_fuente=red_despacho_is_reclasificar_tipo_fuente_noticia_pantalla('popup');
        if($is_reclasificar_tipo_fuente){     
            $tid_array=red_solr_inc_get_content_field_item_source_tid_array($node->nid,$node->vid,0);            
        }
        $term_noticia_usuario=red_solr_inc_taxonomy_get_term_by_name_vid_row('Noticias de usuario',1);         
        foreach($taxo as $tid=>$title){
            $pro=profundidad($tid);
            $row=array(
            '#type' => 'checkbox',
            '#prefix' => '<div class=taxo'. $pro .'>',
            '#suffix' => '</div>',
            '#title' => $title);
            if($is_reclasificar_tipo_fuente){
                if(in_array($tid,$tid_array)){
                    $row['#attributes']['checked']='checked';
                }
            }
            if(red_copiar_is_importar_rss_user_news_tipo_fuente_selected($form_state,$tid,$term_noticia_usuario)){
                $row['#attributes']['checked']='checked';
            }
            $form['taxonomia_fs']['taxonomia'][$tid]=$row;
            $kont++;
        }
    }    
}
function red_despacho_get_source_type_options(){
    $result=array();
    $taxo=taxonomy_get_custom_tree(1);
    if(!empty($taxo)){
        foreach($taxo as $i=>$term){
            $result[$term->tid]=$term->name;
        }
    }
    return $result;
}
function red_despacho_boletin_report_get_current_content($current_content,$subject,$bulletin_text_nid){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_get_current_content($current_content,$subject,$bulletin_text_nid);
    }
    return $current_content;
}
function red_despacho_boletin_report_add_logo_attachment(&$message){
    if(red_despacho_is_activado()){
        despacho_boletin_report_word_add_logo_attachment($message);
    }
}
function red_despacho_boletin_report_is_my_add_css(){
    if(red_despacho_is_activado()){
        return 0;
        //return despacho_boletin_report_word_is_my_add_css();
    }
    return 1;
}
function red_despacho_boletin_report_is_custom_content(){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_is_custom_content();
    }
    return 0;
}
function red_despacho_is_gestionar_tipos_fuente(){
    global $user;
    if($user->roles[CREADOR]){
        return 1;
    }
    return 0;
}
function red_despacho_boletin_report_is_add_introduccion_despedida_html($bulletin_text_nid,$is_edit_content){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_is_add_introduccion_despedida_html($bulletin_text_nid,$is_edit_content);
    }
    return 1;
}
function red_despacho_boletin_report_get_page_styles($styles){
    if(red_despacho_is_activado()){
        return despacho_boletin_word_report_get_page_styles($styles);
    }
    return $styles;
}   
function red_despacho_boletin_report_get_forward_attributes(){
    $result=array('title'=>t('Forward Bulletin'),'alt'=>t('Forward Bulletin'));
    /*if(red_despacho_is_activado()){
        $result=despacho_boletin_report_word_get_forward_attributes($result);
    }*/
    return $result;
}
function red_despacho_boletin_report_is_forward(){
    if(red_despacho_is_activado()){
        return boletin_report_inc_is_forward();
    }
    return 0;
}
function red_despacho_boletin_report_get_boletin_report_forward_content_view_web($content){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_get_boletin_report_forward_content_view_web($content);
    }
    return $content;
}
function red_despacho_boletin_report_get_download_content($content,$is_download){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_get_download_content($content,$is_download);
    }
    return $content;
}
function red_despacho_taxonomy_form_alter(&$form,&$form_state, $form_id){
    if($form_id=='supercanal_node_form'){
       if(isset($form['taxonomy'][1])){
           unset($form['taxonomy'][1]);
       }
    }
}
function red_despacho_supercanal_source_type_presave(&$node){
    if(in_array($node->type,array('supercanal'))){
       $type_array=array('supercanal_node_form'); 
       if(isset($_POST['form_id']) && !empty($_POST['form_id']) && in_array($_POST['form_id'],$type_array)){
            red_despacho_prepare_node_taxonomy_source_type_presave($node);
            $values=red_despacho_boletin_report_get_tipos_fuente_by_values($_POST,'my_source_type_');
            if(!empty($values)){
                foreach($values as $i=>$tid){
                    $term=taxonomy_get_term($tid);
                    $node->taxonomy[$tid]=$term;
                }
            }    
           
        }
    }
}
function red_despacho_prepare_node_taxonomy_source_type_presave(&$node){
    if(!isset($node->taxonomy)){
        $node->taxonomy=array();
    }
    if(isset($node->taxonomy) && !empty($node->taxonomy)){
        foreach($node->taxonomy as $tid=>$term){
            if(red_is_tipos_de_fuente_vocabulary($term)){
                unset($node->taxonomy[$tid]);
            }
        }
    }
}
function red_despacho_boletin_report_get_options_view_next_group_bulletin(){
    $result=array('html'=>true,'query'=>drupal_get_destination());
    if(red_despacho_is_activado()){
        $result=despacho_boletin_report_get_options_view_next_group_bulletin($result);
    }
    return $result;
}
function red_despacho_boletin_report_is_boletin_grupo_no_styles(){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_is_boletin_grupo_no_styles();
    }
    return 0;
}
function red_despacho_boletin_report_fix_pdf($content){
    $result=$content;
    //$content=str_replace('charset=windows-1252','charset=UTF-8',$content);
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_fix_pdf($content);
    }
    return $result;        
}
function red_despacho_boletin_report_get_options_view_alerta(){
    $result=array('html'=>true,'query'=>drupal_get_destination());
    if(red_despacho_is_activado()){
        $result=despacho_boletin_report_word_get_get_options_view_alerta($result);
    }
    return $result;
}
function red_despacho_boletin_report_get_limit_comments_length_display(){
    if(red_despacho_is_activado()){
        return despacho_boletin_report_get_limit_comments_length_display();
    }
    return '';
}
function red_despacho_get_content_resumen_lenght($len_in){
    $result=$len_in;
    if(red_despacho_is_activado()){
        $result=300;
    }
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(isset($my_grupo->field_group_limit_abstract) && isset($my_grupo->field_group_limit_abstract[0])){
            if(isset($my_grupo->field_group_limit_abstract[0]['value'])){
                if(!empty($my_grupo->field_group_limit_abstract[0]['value'])){
                    return $my_grupo->field_group_limit_abstract[0]['value'];
                }
            }
        }
    }    
    return $result;
}
function red_despacho_unset_content_resumen_caracteres($result_in){
    $result=$result_in;
    $result=str_replace("\t"," ",$result);
    $result=preg_replace('/ +/',' ', $result);
    return $result;
}
function red_despacho_boletin_report_access_denied(){
    if(red_despacho_is_activado()){
        despacho_boletin_report_word_access_denied();
    }
}
function red_despacho_boletin_report_unset_headers($headers){
    $result=$headers;
    if(red_despacho_is_activado()){
        return despacho_boletin_report_word_unset_headers($result);
    }
    return $result;
}
function red_despacho_boletin_report_set_edition_type_value_string($is_edit,$is_archive=0){
    if(red_despacho_is_activado()){
        $result=despacho_boletin_report_word_set_edition_type_value_string($is_edit,$is_archive);
    }else{
        $result=boletin_report_set_edition_type_value_string($is_edit,$is_archive);
    }
    return $result;
}
function red_despacho_get_canal_duplicate_news_options(){
    $result=array();
    $label=t('Duplicate URLs');
    $result[0]=$label;
    $result[1]=$label;
    return $result;
}
function red_despacho_is_canal_duplicate_news($canal_nid){
    $canal=node_load($canal_nid);
    if(isset($canal->field_canal_is_duplicate_news) && !empty($canal->field_canal_is_duplicate_news)){
        if(isset($canal->field_canal_is_duplicate_news[0]) && !empty($canal->field_canal_is_duplicate_news[0])){
            if(isset($canal->field_canal_is_duplicate_news[0]['value']) && !empty($canal->field_canal_is_duplicate_news[0]['value'])){
                return 1;
            }
        }
    }
    return 0;
}
function red_despacho_get_protected_value($obj,$name) {
  $array = (array)$obj;
  $prefix = chr(0).'*'.chr(0);
  if(isset($array[$prefix.$name])){
    return $array[$prefix.$name];
  }
  if(isset($array[$name])){
    return $array[$name];  
  }
  return '';
}
function red_despacho_vigilancia_add_import_canal_duplicate_news_form_field(&$form){
    if(red_despacho_vigilancia_is_add_import_canal_duplicate_news_form_field_activado()){
        $form['canal_is_duplicate_news']=array('#type'=>'checkbox','#title'=>'<b>'.t('Duplicate URLs').'</b>');
    }
}
function red_despacho_vigilancia_is_add_import_canal_duplicate_news_form_field_activado(){
    if(defined('_IS_CANAL_DUPLICATE_NEWS') && _IS_CANAL_DUPLICATE_NEWS==1){
        return 1;
    }
    return 0;
}
function red_despacho_vigilancia_add_import_canal_duplicate_news_form_state_values($form_state,&$fuente){
    if(red_despacho_vigilancia_is_add_import_canal_duplicate_news_form_field_activado()){
        $fuente->field_canal_is_duplicate_news=array();
        $fuente->field_canal_is_duplicate_news[0]=array();
        $fuente->field_canal_is_duplicate_news[0]['value']=0;
        if(isset($form_state['values']['canal_is_duplicate_news']) && !empty($form_state['values']['canal_is_duplicate_news'])){
            $fuente->field_canal_is_duplicate_news[0]['value']=1;
        }       
    }    
}
function red_despacho_get_reclasificar_tipo_fuente_link($node){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_get_reclasificar_tipo_fuente_link($node);
    }
    return 0;
}
function red_despacho_is_reclasificar_tipo_fuente_noticia_pantalla($type){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_reclasificar_tipo_fuente_noticia_pantalla($type);
    }
    return 0;
}
function red_despacho_is_on_reclasificar_tipo_fuente_noticia_pantalla($type){
    if(red_despacho_is_activado()){
        return despacho_vigilancia_is_on_reclasificar_tipo_fuente_noticia_pantalla($type);
    }
    return 0;
}
function red_despacho_reclasificar_tipo_fuente_save(&$form_state,$node_id_array){
    $id_categoria=$form_state['values']['id_categoria'];
    //
    $tid_array=hontza_solr_search_get_form_state_values_tid_array($form_state['values'],$id_categoria,'taxonomia');
    //$value_array=hontza_solr_search_set_value_array($tid_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                if(in_array($node->type,array('item','noticia'))){
                    if(hontza_solr_is_solr_activado()){                        
                        //intelsat-2015
                        if(!(isset($node->field_item_source_tid))){
                            $node->field_item_source_tid=array();
                        }
                        //
                            if(hontza_solr_search_is_replace_categorias($form_state,'replace_btn')){
                                red_solr_inc_reclasificar_tipo_fuente_item($node,$tid_array);
                            }else if(hontza_solr_search_is_replace_categorias($form_state,'add_btn')){
                                red_solr_inc_add_reclasificar_tipo_fuente_item($node,$tid_array);
                            }
                            //intelsat-2015
                            hontza_canal_rss_solr_clear_node_index($node,$nid);
                            //                        
                    }                    
                }/*else if($node->type=='noticia'){
                    if(hontza_solr_search_is_replace_categorias($form_state,'replace_categories_btn')){
                        hontza_solr_search_modificar_noticia_usuario_categorias($node,$tid_array);
                    }else if(hontza_solr_search_is_replace_categorias($form_state,'add_categories_btn')){
                        hontza_solr_search_add_noticia_usuario_categorias($node,$tid_array); 
                    }
                }*/
            }
        }
    }
}
function red_despacho_get_item_source_tid_ul_id($nid){
    return 'id_ul_item_source_tid_'.$nid;
}
function red_despacho_is_ficha_completa(){
    $node_type_array=array('item','noticia');
    if(!empty($node_type_array)){
        foreach($node_type_array as $i=>$node_type){
            if(is_ficha_node($node_type)){
                return 1;
            }
        }
    }    
    return 0;
}
function red_despacho_get_item_canal_category_tid_ul_id($nid){
    return 'id_ul_item_canal_category_tid_'.$nid;
}
function red_despacho_get_popup_character($is_js){
    $sep="\n";
    if($is_js){
        $sep="&#xA;";
    }
    return $sep;
}
function red_despacho_vigilancia_validar_categorizar_node_array($node_array){
    if(red_despacho_is_activado()){
        despacho_vigilancia_validar_categorizar_node_array($node_array);
    }
}
function red_despacho_boletin_report_inc_replace_title_boletin_report_automatico_editados($content,$titulo_boletin){
    if(red_despacho_is_activado()){
        $result=despacho_boletin_report_word_replace_title_boletin_report_automatico_editados($content,$titulo_boletin);
    }else{
        $result=boletin_report_inc_replace_title_boletin_report_automatico_editados($content,$titulo_boletin);
    }
    return $result;
}
function red_despacho_boletin_report_get_limite_default_value($row,$name,$field_name){
    //if(red_despacho_is_activado()){
    if(in_array($field_name,array('is_todos_items'))){ 
        return get_alerta_my_limite($row->$name,100);
    }else{
        return get_alerta_my_limite($row->$name);
    }
}
function red_despacho_boletin_report_get_tipo_link_default_value($row){
    //if(red_despacho_is_activado()){
        if(isset($row->tipo_link)){
            return $row->tipo_link;
        }else{
            return 2;
        }
    /*}else{
        if(isset($row->tipo_link)){
            return $row->tipo_link;
        }
    }*/
    return '';
}
function red_despacho_vigilancia_is_item_post_url_save(){
    if(in_array(_SAREKO_ID,array('ROOT'))){
        return 1;
    }
    if(defined('_IS_ITEM_POST_URL_SAVE') && _IS_ITEM_POST_URL_SAVE==1){
        return 1;
    }
    return 0;
}
function red_despacho_boletin_report_get_email_body_id(){
    if(defined('_DESPACHO_EMAIL_BODY_ID') && _DESPACHO_EMAIL_BODY_ID==1){
        return _DESPACHO_EMAIL_BODY_ID;
    }
    return '';
}        