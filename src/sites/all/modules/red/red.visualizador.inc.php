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
function red_visualizador_is_visulizador_grupo_defined(){
    if(module_exists('visualizador')){
        if(defined('_IS_VISUALIZADOR_GRUPO') && _IS_VISUALIZADOR_GRUPO==1){
            return 1;
        }
    }
    return 0;
}
function red_visualizador_get_grupo_activate_observatory_link(){
    $html=array();
    $activate_url='grupo/activate_observatory/edit';
    $html[]=l(t('Activate Observatory'),$activate_url);
    return implode('',$html);
}
function red_visualizador_is_visualizador_pantalla(){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(module_exists('publico')){
            if(publico_is_pantalla_publico()){
                return 1;
            }
        }
    }
    return 0;
}
function red_visualizador_is_show_banners(){
    if(hontza_canal_rss_is_visualizador_inicio()){
        return TRUE;
    }
    return FALSE;
}