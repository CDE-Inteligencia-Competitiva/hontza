<?php
function visualizador_custom_menu_create_menu_despegable(){
    $result='';
    //Por ejemplo para ponerlo en page.tpl.php
    /*<?php   
          $visualizador_custom_menu_despegable='';
        if(hontza_canal_rss_is_visualizador_activado()){
            $visualizador_custom_menu_despegable=visualizador_custom_menu_create_menu_despegable();
            print $visualizador_custom_menu_despegable;
        }
        ?>
    */    
    //if(hontza_is_user_anonimo()){
        $html=array();
        $ul_class=custom_menu_get_ul_class();
        //$html[]='<ul id="id_ul_latest_news" class="'.$ul_class.'">';
        $html[]='<ul id="ul_a">';
        $categorias_array=visualizador_custom_menu_get_categorias_array();
        $html[]=visualizador_custom_menu_categorias_html($categorias_array,1);
        $html[]='</ul>';
        $result='';
        $result=visualizador_custom_menu_add_js_menu_desplegable();
        $result.=implode('',$html);
        return $result;
    //}
    return $result;
}
function visualizador_custom_menu_get_categorias_array(){
    $result=visualizador_categorias_get_categorias_array();
    return $result;
}
function visualizador_custom_menu_add_js_menu_desplegable(){
    $js='$(document).ready(function()
        {
            $("#id_a_latest_news").click(function(e){
                e.stopPropagation(); 
                visualizador_custom_menu_show_custom_menu(true);                
                return false;
            });
            function visualizador_custom_menu_show_custom_menu(show_type){               
                var my_display=$("#id_ul_latest_news").css("display");
                var my_value="none";
                    if(show_type){
                        my_value="block";
                    }
                $("#id_ul_latest_news").css({display: my_value});
                /*if(my_value=="none"){
                    show_grupo_custom_menu(false,"","","","");
                }*/
                return false;
            }
    });';
    $result='<script type="text/javascript">';
    $result.=$js;
    $result.='</script>';
    return $result;
}
function visualizador_custom_menu_get_subcategorias_html($tid_in,$id_ul){
    $html=array();
    $categorias_array=visualizador_categorias_get_categorias_array($tid_in);
    if(!empty($categorias_array)){
        $html[]='<ul id="'.$id_ul.'" class="ul_submenu">';
        $html[]=visualizador_custom_menu_categorias_html($categorias_array);
        $html[]='</ul>';
    }
    return implode('',$html);
}
function visualizador_custom_menu_categorias_html($categorias_array,$is_principal=0){
    $html=array();
    if(!empty($categorias_array)){
        foreach($categorias_array as $i=>$term){
                    $term_name=$term->name;
                    $term_lang=taxonomy_get_term_by_language($term->tid);
                        $description=$term->description;
                        if(isset($term_lang->name) && !empty($term_lang->name)){
                            $term_name=$term_lang->name;
                        }
                        if(isset($term_lang->description) && !empty($term_lang->description)){
                            $description=$term_lang->description;
                        }
                    $description=visualizador_categorias_get_categoria_description($description);
                    $id='id_categoria_latest_news_'.$term->tid;
                    $id_ul='ul_categoria_latest_news_'.$term->tid;
                    $url=visualizador_categorias_get_categoria_url('publico/canales/categorias/'.$term->tid.'/ultimos');
                    //$html[]='<li id="'.$id.'" class="li_grupo_custom_menu li_pointer" my_pos="'.$i.'">'.$link.'</li>';
                    $visualizador_subcategorias_html='';
                    /*if($is_principal){
                        $visualizador_subcategorias_html=visualizador_custom_menu_get_subcategorias_html($term->tid,$id_ul);
                    }*/
                    $options=array();
                    if($is_principal){
                        $is_subcategorias=visualizador_custom_is_subcategorias($term->tid);
                        /*if($is_subcategorias){
                            $options['attributes']['class']='a_subcategorias';
                        }else{
                            $options['attributes']['class']='a_subcategorias_no';
                        }*/
                    }
                    $link=l($term_name,$url,$options);                                                
                    $html[]='<li id="'.$id.'" class="li_pointer" my_pos="'.$i.'">'.$link.$visualizador_subcategorias_html.'</li>';
        }
    }
    return implode('',$html);
}
function visualizador_custom_is_subcategorias($tid){
    $is_subcategorias=0;
    $categorias_array=visualizador_categorias_get_categorias_array($tid);
    if(!empty($categorias_array)){
        $is_subcategorias=1;
    }
    return $is_subcategorias;
}                        