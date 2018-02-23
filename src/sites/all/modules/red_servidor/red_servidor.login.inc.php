<?php
function red_servidor_login_authenticate_red_alerta_callback($user_login_enviar='',$is_on_guardar_grupo_hoja=0){
    //return 'Funcion desactivada';
    return red_compartir_login_authenticate_local_callback($user_login_enviar,$is_on_guardar_grupo_hoja);
}