<?php
function movil_grupo_seleccionar_grupo_callback(){
    global $base_url,$language;
    $html=array();
    $html[]='<div class="div_seleccionar_grupo">';
    $grupos_array=my_get_og_grupo_list('',1);
    $my_lang='';
    if($language->language!='en'){
        $my_lang=$language->language.'/';
    }    
    $my_grupo=og_get_group_context();
    $my_grupo_nid=0;
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;
    }
    if(!empty($grupos_array)){
        foreach($grupos_array as $i=>$row){
            $url=$base_url.'/'.$my_lang.$row->value.'/'.movil_get_inicio_url_relative();
            $title=movil_grupo_get_title_resume($row->title);
            $class=movil_get_btn_class();
            if($row->nid==$my_grupo_nid){
                $class='btn_selected';
            }
            $html[]='<div class="button-wrap-inner">'.l($title,$url,array('attributes'=>array('class'=>$class))).'</div>';    
        }
    }
    $html[]='</div>';
    $output=implode('',$html);
    movil_grupo_add_seleccionar_grupo_js();
    return $output;
}
function movil_grupo_get_title_resume($title){
    $max=25;
    $result=red_funciones_cortar_node_title($title,$max);    
    return $result;
}
function movil_grupo_add_seleccionar_grupo_js(){
    $js='
        $(document).ready(function()
        {            
            $(".button-wrap-inner a").click(function()
            {
                var current_class=$(this).attr("class");
                if(current_class=="btn"){
                    movil_grupo_set_all_class("btn","btn_selected");
                    movil_grupo_set_class("btn_selected","btn",$(this));
                }
            });
            function movil_grupo_set_all_class(my_class,remove_class){
                $(".button-wrap-inner a").each(function()
                {
                    movil_grupo_set_class(my_class,remove_class,$(this));
                });
            }
            function movil_grupo_set_class(my_class,remove_class,my_object){
                my_object.removeClass(remove_class);
                my_object.addClass(my_class);            
            }
	});';

    drupal_add_js($js,'inline');
}
function  movil_grupo_get_url_default($grupo_nid){
    $url=movil_get_inicio_url_relative();
    $result=movil_add_grupo_lang_url($url,$grupo_nid);    
    return $result;
}