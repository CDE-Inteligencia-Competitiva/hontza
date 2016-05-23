<?php
function red_visualizador_frontpage_callback(){
    if(hontza_canal_rss_is_visualizador_grupos_colaborativos()){
        visualizador_frontpage_callback();
    }else{
        drupal_goto('node');
    }    
}
function red_visualizador_is_menu_sin_flechas(){
    if(red_is_menu_sin_flechas()){
        return 1;
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_is_menu_sin_flechas();
    }    
    return 0;
}
function red_visualizador_is_show_boomark_link(){
    if(hontza_solr_funciones_is_bookmark_activado()){
        if(hontza_canal_rss_is_visualizador_activado()){
            if(publico_is_pantalla_publico()){
                return 0;
            }
        }
    }
    if(hontza_is_user_anonimo()){
        return 0;
    }
    return 1;
}
function red_visualizador_is_anonimo_visualizador_activado(){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(hontza_is_user_anonimo()){
            return 1;
        }
    }
    return 0;
}
function red_is_visualizador_pantalla(){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(visualizador_is_pantalla() || publico_is_pantalla_publico()){
            return 1;
        }
    }
    return 0;
}
function red_visualizador_is_show_banner_slider(){
    if(hontza_canal_rss_is_visualizador_activado()){
        return visualizador_is_show_banner_slider();
    }
    return 0;
}