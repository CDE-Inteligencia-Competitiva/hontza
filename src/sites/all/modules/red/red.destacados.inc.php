<?php
function red_destacados_callback(){
    $html=array();
    $html[]=hontza_vigilancia_menu();
    $html[]=publico_vigilancia_callback();
    return implode('',$html);
}
function red_destacados_canales_destacados_callback(){
    $html=array();
    $html[]=hontza_canales_menu();
    $html[]=publico_vigilancia_callback();
    return implode('',$html);
}