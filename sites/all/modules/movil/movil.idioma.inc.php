<?php
function movil_idioma_seleccionar_idioma_callback(){
    global $base_url,$language;
    $html=array();
    $html[]='<div class="div_seleccionar_idioma">';
    $grupos_array=my_get_og_grupo_list('',1);
    $my_grupo=red_movil_get_current_grupo_node();
    $grupo_value='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_value=$my_grupo->purl;
    }
    $language_array=red_movil_get_language_array();
    $my_lang='';
    if(!empty($language_array)){
        foreach($language_array as $code=>$row){
            $my_lang='/';
            if($code!='en'){
                $my_lang='/'.$code.'/';
            }    
            $url=$base_url.$my_lang.$grupo_value.'/'.movil_get_inicio_url_relative();
            $title=movil_grupo_get_title_resume($row->native);
            $class=movil_get_btn_class();
            if($code==$language->language){
                $class='btn_selected';
            }
            $html[]='<div class="button-wrap-inner">'.l($title,$url,array('attributes'=>array('class'=>$class))).'</div>';    
        }
    }
    $html[]='</div>';
    $output=implode('',$html);
    movil_idioma_add_seleccionar_idioma_js();
    return $output;
}
function movil_idioma_add_seleccionar_idioma_js(){
    movil_grupo_add_seleccionar_grupo_js();
}    