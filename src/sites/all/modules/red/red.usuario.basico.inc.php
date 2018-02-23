<?php
function red_usuario_basico_is_show_debate_collaboration_idea(){
    if(hontza_canal_rss_is_usuario_basico_activado()){
        return usuario_basico_is_show_debate_collaboration_idea();
    }
    return 1;
}