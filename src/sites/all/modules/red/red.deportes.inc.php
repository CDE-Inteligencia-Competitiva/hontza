<?php
function red_deportes_get_menu_primary_links($result_in){
    $result=$result_in;
    if(red_deportes_is_activado()){
        return deportes_get_menu_primary_links($result_in);
    }
    return $result;
}
function red_deportes_is_activado(){
    if(module_exists('deportes')){
        return 1;
    }
    return 0;
}