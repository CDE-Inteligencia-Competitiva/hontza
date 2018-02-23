<?php
function despacho_boletin_report_add_tipos_fuente_fieldset($row,&$form){
    despacho_boletin_report_add_categorias_fs_div($form);
    $tid_array=array();    
    if(isset($row->tipos_fuente) && !empty($row->tipos_fuente)){
        $tid_array=$row->tipos_fuente;
    }
    $source_type_array=taxonomy_get_custom_tree(1);
    if(!empty($source_type_array)){
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
                        if($kont==0){
                            $div_prefix='<div id="id_despacho_boletin_report_tipos_fuente"><label><b>'.t('Types of Sources').'</b></label>';
                        }else if($kont==($num-1)){
                            $div_suffix='</div></div>';
                        }
                        $form['categorias_fs'][$key] = array(
                          '#required' => TRUE,
                          '#type' => 'checkbox',
                          '#prefix' => $div_prefix.'<div class=taxo'. $pro .'>',
                          '#suffix' => '</div>'.$div_suffix,
                          '#title' => despacho_boletin_report_get_tipos_fuente_title_by_length($contenido->name)
                        );
                        if(in_array($contenido->tid,$tid_array)){
                           $form['categorias_fs'][$key]['#attributes']=array('checked' => 'checked');
                        }
                        $kont++;
                     //}   
                  }
    }        
}
function despacho_boletin_report_add_categorias_fs_div(&$form){
    $id_array=array_keys($form['categorias_fs']);
    $id_array=despacho_boletin_get_my_cat_id_array($id_array);
    if(!empty($id_array)){
        $prefix_id=$id_array[0];
        $num=count($id_array);
        $suffix_id=$id_array[$num-1];
        $div_prefix='<div id="id_despacho_boletin_report_div"><div id="id_despacho_boletin_report_categorias"><label><b>'.t('Thematic').'</b></label>';
        $div_suffix='</div></div>';
        $form['categorias_fs'][$prefix_id]['#prefix']=$div_prefix.$form['categorias_fs'][$prefix_id]['#prefix'];
        $form['categorias_fs'][$suffix_id]['#suffix']=$form['categorias_fs'][$suffix_id][$suffix_id]['#suffix'].$div_suffix;        
    }    
}
function despacho_boletin_get_my_cat_id_array($id_array,$search='my_cat_'){
    $result=array();
    if(!empty($id_array)){
        foreach($id_array as $i=>$value){
            $pos=strpos($value,$search);
            if($pos===FALSE){
                continue;
            }else{
                $result[]=$value;
            }
        }
    }
    return $result;
}
function despacho_boletin_report_is_content_field_checked($field){
    if(in_array($field,array('is_todos_wikis','is_todos_debates','is_todos_reports'))){
        return 0;        
    }
    return 1;    
}
function despacho_boletin_report_save_tipos_fuente($id,$values){
    $tipos_fuente=despacho_boletin_report_get_tipos_fuente_by_values($values);
    despacho_boletin_report_delete_tipos_fuente($id);    
    if(count($tipos_fuente)>0){
        foreach($tipos_fuente as $i=>$tid){
            despacho_boletin_report_insert_tipos_fuente($id,$tid);
        }            
    }
}
function despacho_boletin_report_delete_tipos_fuente($id){
    $sql='DELETE FROM {boletin_report_array_tipos_fuente} WHERE boletin_report_array_id='.$id;
    db_query($sql);
}
function despacho_boletin_report_get_tipos_fuente_by_values($values,$search='my_source_type_'){        
    return red_despacho_boletin_report_get_tipos_fuente_by_values($values,$search);
}
function despacho_boletin_report_insert_tipos_fuente($id,$tid){
    $sql='INSERT {boletin_report_array_tipos_fuente}(boletin_report_array_id,tid) VALUES('.$id.','.$tid.')';
    db_query($sql);
}
function despacho_boletin_report_get_tipos_fuente_tid_array($id){
    $result=array();
    $sql='SELECT boletin_report_array_tipos_fuente.tid FROM {boletin_report_array_tipos_fuente}  WHERE boletin_report_array_tipos_fuente.boletin_report_array_id='.$id;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row->tid;
    }
    return $result;
}
function despacho_boletin_report_in_tipos_fuente($nid,$tipos_fuente,$grupo_nid,$node_type,&$in_tipos_fuente){
    $result=array();
    $node=node_load($nid);
    $node_tipos_fuente=red_solr_inc_get_content_field_item_source_tid_array($node->nid,$node->vid,0);
    //
    if(!empty($tipos_fuente) && !empty($node_tipos_fuente)){
        foreach($tipos_fuente as $i=>$tid){            
                if(in_array($tid,$node_tipos_fuente)){
                    //return 1;
                    if(!in_array($tid,$result)){
                        $result[]=$tid;
                    }    
                }                
        }
        /*$id_categoria = 1;
        foreach($tipos_fuente as $k=>$tid){
            $tree=taxonomy_get_tree($id_categoria,$tid);
            if(!empty($tree)){
                foreach($tree as $a=>$tree_row){
                    if(in_array($tree_row->tid,$node_tipos_fuente)){
                        //return 1;
                        if(!in_array($tid,$result)){
                            $result[]=$tid;
                        }
                    }
                }
            }
        }*/
    }
    $in_tipos_fuente=$result;
    //
    if(count($result)>0){
        return 1;
    }
    return 0;
}
function despacho_boletin_report_por_tipos_fuente($br,&$html_out,$items){
    //intelsat-2015
    $html=array();
    $categorias=$br->tipos_fuente;
    $categorias=despacho_boletin_report_get_categorias_parents($categorias,$items);
    $categorias=boletin_report_inc_add_categorias_titulo($categorias);
    //
    $categorias_orden=despacho_boletin_report_get_tipos_fuente_orden($categorias,$tipos_fuente_term_array);
    $tid_indice_array=array();
    foreach($categorias_orden as $i=>$tid){
        $term_items=boletin_report_get_items_by_tid($items,$tid,'in_tipos_fuente');
        $term_name=boletin_report_get_term_name($tid);        
        if(in_array($tid,$br->tipos_fuente)){
            $num=count($term_items);
            if($num>0){
                //echo print_r($term_items,1);exit();
                if(in_array($tid,array('debate','wiki','my_report'))){
                    //$term_items=array_ordenatu($term_items,'fecha','asc', $is_numeric);                
                }else{
                    //print 'tid='.$tid.'<BR>';
                    $term_items=array_ordenatu($term_items,'votingapi_cache_node_average_value','desc', $is_numeric);                
                }                                
                $hay_novedades_out=0;
                $content=create_boletin_mail_html($term_items,'categorias_report',$hay_novedades_out,$num, '', '', '',$br->grupo_nid,$br->limite_resumen,$br,$term_name,$tipos_fuente_term_array[$tid]);
                if(!empty($content) && $hay_novedades_out){
                    $html[]=$content;
                    $tid_indice_array[]=$tid;
                }    
            }else{
                //$html[]=no_hay_novedades_html('categorias_report', '',$my_message,'',$term_name);
                despacho_boletin_report_tree_html($num,$categorias_orden,$tid,$items,$my_message,$term_name,$tipos_fuente_term_array[$tid],$html,$tid_indice_array);
            }
        }else{
            //intelsat-2015
            /*if(boletin_report_inc_categorias_titulo_activado()){
                $html[]=no_hay_novedades_html('categorias_report', '',$my_message,'',$term_name,1);
            }*/
            despacho_boletin_report_tree_html($num,$categorias_orden,$tid,$items,$my_message,$term_name,$tipos_fuente_term_array[$tid],$html,$tid_indice_array);
        }    
    }
    $html_indice=despacho_boletin_report_get_html_indice($categorias_orden,$tid_indice_array);
    $html_out=array_merge($html_out,$html_indice);        
    $html_out=array_merge($html_out,$html);
}
function despacho_boletin_report_get_tipos_fuente_orden($categorias,&$term_array){
    $result=array();
    $term_array=array();
    if(!empty($categorias)){
        $tipos_fuente=taxonomy_get_custom_tree(1);
        if(!empty($tipos_fuente)){
            foreach($tipos_fuente as $i=>$row){
                if(in_array($row->tid,$categorias)){
                    $result[]=$row->tid;
                    $term_array[$row->tid]=$row;
                }
            }
        }
    }
    return $result;
}
function despacho_boletin_report_get_tipos_fuente_boletin_title($title,$tipos_fuente_row,$with_name=1,$br=''){
    if(isset($tipos_fuente_row->depth) && !empty($tipos_fuente_row->depth)){
        if($tipos_fuente_row->depth==1){
            //return $title;
            return '<span name="tipos_fuente_'.$tipos_fuente_row->tid.'">'.$title.'</span>';
        }
        return '<i name="tipos_fuente_'.$tipos_fuente_row->tid.'">'.$title.'</i>';
    }else{
        $style='';
        if(red_informatica_despacho_boletin_report_is_resumen_activado()){
            //$style=' style="text-decoration:none;color:white !important;"';

            $mailchimp_color_letra_tipo_documento='white';
                     if(isset($br->mailchimp_color_letra_tipo_documento) && !empty($br->mailchimp_color_letra_tipo_documento)){
                        $mailchimp_color_letra_tipo_documento=$br->mailchimp_color_letra_tipo_documento;
                     }

            $style=' style="text-decoration:none;color:'.$mailchimp_color_letra_tipo_documento.' !important;font-size:14pt !important;"';
            $result=$title;
        }else{
            $result=my_upper($title);
        }
        if(!$with_name){
            return $result;
        }
        return '<a name="tipos_fuente_'.$tipos_fuente_row->tid.'"'.$style.'>'.$result.'</a>';
    }
}
function despacho_boletin_report_get_boletin_despedida($despedida){
    $nid=140721;
    $node=node_load($nid);
    if(isset($node->body)){
        return '<hr class="hr_footer">'.$node->body;
    }
    return $despedida;
}
function despacho_boletin_report_get_boletin_introduccion($introduccion,$boletin_report_titulo_mail){    
    global $base_url;
    $html=array();
    $html[]='<div class="div_boletin_banner">';
    //intelsat-2016
    $sareko_id=strtolower(_SAREKO_ID);
    $path=$base_url.'/sites/'.$sareko_id.'.hontza.es/files/';    
    $src=$path.$sareko_id.'_boletin_banner_completo.png';
    $html[]='<div class="div_boletin_banner_completo"><img src="'.$src.'"></div>';
    $html[]='<div class="div_boletin_banner_titulo"><h1 class="boletin_banner_titulo">'.strip_tags($boletin_report_titulo_mail).'</h1></div>';        
    $html[]='</div>';
    return implode('',$html);
}
function despacho_boletin_report_get_num_childs($num,$categorias_orden,$tid,$items){
    if($num==0){
        $tree_array=taxonomy_get_tree(1,$tid);
        if(!empty($tree_array)){
            foreach($tree_array as $i=>$term){
                $term_items=boletin_report_get_items_by_tid($items,$term->tid,'in_tipos_fuente');
                if(count($term_items)>0){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function despacho_boletin_report_num_childs_html($my_message,$term_name,$tipos_fuente_row){
    return no_hay_novedades_html('categorias_report', '',$my_message,'',$term_name,1,$tipos_fuente_row);
}    
function despacho_boletin_report_get_categorias_parents($categorias,$items){
    $result=$categorias;
    foreach($categorias as $i=>$tid){
        $term_items=boletin_report_get_items_by_tid($items,$tid,'in_tipos_fuente');
        if(count($term_items)>0){
            $my_array=taxonomy_get_parents_all($tid);
            if(!empty($my_array)){
                foreach($my_array as $c=>$term){
                    if($term->tid!=$tid && !in_array($term->tid,$result)){
                        $result[]=$term->tid;
                    }
                }
            }
        }
    }
    return $result;
}
function despacho_boletin_report_tree_html($num,$categorias_orden,$tid,$items,$my_message,$term_name,$tipos_fuente_row,&$html,&$tid_indice_array){    
    $num_childs=despacho_boletin_report_get_num_childs($num,$categorias_orden,$tid,$items);
    if($num_childs>0){
        $tid_indice_array[]=$tid;
        $html[]=despacho_boletin_report_num_childs_html($my_message,$term_name,$tipos_fuente_row);                
    }
}
function despacho_boletin_report_get_th_categoria_title_class($tipos_fuente_row){
    if(isset($tipos_fuente_row->depth) && !empty($tipos_fuente_row->depth)){
        if($tipos_fuente_row->depth==1){
            return '';
        }
        return '';
    }else{
        return ' class="th_boletin_categoria"';        
    }
}
function despacho_boletin_report_get_html_indice($categorias_orden,$tid_indice_array){
    $html=array();
    if(!empty($categorias_orden)){
        //$html[]='<div class="boletin_indice">';
        $html[]='<table class="mail_table indice_table" style="width:100%;border:0px;">';
        $html[]='<tr>';
        $html[]='<td>';
        $html[]='<ul>';
        foreach($categorias_orden as $i=>$tid){
            $depth=profundidad($tid);
            if(empty($depth) && in_array($tid,$tid_indice_array)){
                $tipos_fuente_row=taxonomy_get_term($tid);
                $tipos_fuente_row->depth=$depth;
                $title=boletin_report_get_term_name($tid);
                $title=despacho_boletin_report_get_tipos_fuente_boletin_title($title,$tipos_fuente_row,0);
                //$html[]='<li>'.l($title,'prueba').'</li>';
                /*$font_size='';
                if(red_informatica_despacho_boletin_report_is_resumen_activado()){
                    $font_size='font-size:14pt;';
                }*/
                $html[]='<li><a href="#tipos_fuente_'.$tid.'" style="text-decoration:none;">'.$title.'</a></li>';
            }
        }
        $html[]='</ul>';
        //$html[]='</div>';
        $html[]='</td>';
        $html[]='</tr>';
        $html[]='</table>';        
    }
    return $html;
}
function despacho_boletin_report_get_styles_inline($url_layout,$url_style,$hasi_url){
    $html=array();
    $html[]='<style>';
    /*$html[]=file_get_contents($url_layout);
    $html[]=file_get_contents($url_style);*/
    $html[]=file_get_contents($hasi_url.'sites/default/modules/admin_menu/admin_menu.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/help_popup/jq_modal.css?3" />');
        $html[]=file_get_contents($hasi_url.'modules/node/node.css?3" />');
        $html[]=file_get_contents($hasi_url.'modules/system/defaults.css?3" />');
        $html[]=file_get_contents($hasi_url.'modules/system/system.css?3" />');
        $html[]=file_get_contents($hasi_url.'modules/user/user.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/cck/theme/content-module.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/ctools/css/ctools.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/date/date.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/date/date_popup/themes/datepicker.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/date/date_popup/themes/jquery.timeentry.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/filefield/filefield.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/fivestar/css/fivestar.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/og/theme/og.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/tagadelic/tagadelic.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/cck/modules/fieldgroup/fieldgroup.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/views/css/views.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/modules/context/plugins/context_reaction_block.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/all/themes/buho/css/layout.css?3" />');
        $html[]=file_get_contents($hasi_url.'sites/default/files/buho/custom.css?3" />');
        $html[]=file_get_contents($url_style);
    $html[]='</style>';
    return implode('',$html);
}
function despacho_boletin_report_get_limit_comments_length_display(){
    $result='display:none;';
    return $result;
}
function despacho_boletin_report_get_tipos_fuente_title_by_length($title,$max=60){
    $result=$title;
    if(strlen($result)>$max){
        $result=substr($result,0,$max).' ...';
    }
    return $result;
}
function despacho_boletin_report_is_resumen_activado(){
    if(defined('_IS_DESPACHO_BOLETIN_REPORT_RESUMEN') && _IS_DESPACHO_BOLETIN_REPORT_RESUMEN==1){
        return 1;
    }
    return 0;
}