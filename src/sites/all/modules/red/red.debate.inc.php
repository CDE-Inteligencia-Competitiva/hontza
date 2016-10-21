<?php
function red_debate_inc_get_categorias_title(){
	return t('Categories');
}
function red_debate_inc_get_categorias_block_content(){
	$html=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$my_grupo->nid));
        if(!empty($id_categoria)){
        	$categoria_array=taxonomy_get_custom_tree($id_categoria);
            if(!empty($categoria_array)){
                foreach($categoria_array as $i=>$term){
                    //if(despacho_vigilancia_is_categoria_noticias($term->tid)){
                        $term_name=$term->name;
                        $description='';
                        $term_lang=taxonomy_get_term_by_language($term->tid);
                          $description=$term->description;
                          if(isset($term_lang->name) && !empty($term_lang->name)){                      
                              $term_name=$term_lang->name;
                          }
                          if(isset($term_lang->description) && !empty($term_lang->description)){
                              $description=$term_lang->description;
                          }
                        $style=' style="list-style-image: none;list-style-type: none;"';
                        /*$my_selected_tipos_fuente=red_despacho_get_selected_tipos_fuente();   
                        if($term->tid==$my_selected_tipos_fuente){
                            $link=red_despacho_get_term_link_delete_filter($term_name);
                        }else{
                            $link=l($term_name,'despacho/canales/tipos_fuente/'.$term->tid);
                        }*/
                        $link=l($term_name,'area-debate/'.$term->tid);
                        $html[]='<li class="nivel'.$term->depth.'"'.$style.'>'.$link.'</li>';
                    //}    
                }
            }    
        }
    }        
    return implode('',$html);
}