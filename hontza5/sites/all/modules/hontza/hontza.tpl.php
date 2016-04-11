<?php
// $Id: hontza.tpl.php

?>
<?php global $user;?>
<?php if ($user->name):?>
<div class="menu-user"><li style="float:left;padding-left:0px;"><?php print red_funciones_get_actualizacion_activado_menu_icono();?><?php print red_funciones_get_subdominio_activado_menu_icono();?><?php print red_funciones_get_language_menu_icono();?><?php print get_language_selection_li();?></li><li class="user-m" style="float:left;background-position: top left;"><?php print red_funciones_get_user_menu_icono();?><?php print hontza_define_user_menu_input_select().hontza_get_rol_base_user($user).'</li>'.red_funciones_define_user_menu_net_resources_input_select_html().red_funciones_get_gestion_menu_icono().hontza_define_user_menu_management_input_select_html();?></div>
  <?php else: ?>
    <?php //intelsat-2015 ?>
    <?php if(hontza_canal_rss_is_visualizador_activado()):?>
<div class="menu-user"><li style="float:left;"><?php print visualizador_create_busqueda_submit_link();?><?php print visualizador_create_busqueda_form_simple();?></li><li><?php print red_funciones_get_language_menu_icono();?><?php print get_language_selection_li(1);?></li><?php //print visualizador_create_top_links();?><?php print visualizador_get_iconos_redes_sociales();?><?php print $enlaces ?></div>
    <?php else:?>
    <li><?php print $enlaces ?></li>
    <?php endif; ?>
<?php endif; ?>
