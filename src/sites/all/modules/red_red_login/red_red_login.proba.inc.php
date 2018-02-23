<?php
function red_red_login_proba_callback(){
    $html=array();
    $url=red_get_servidor_central_url();
    $html[]=l(t('Test Login network'),$url,array('absolute'=>TRUE,'attributes'=>array('id'=>'id_red_alerta_login')));
    return implode('',$html);
}
function red_red_login_proba_add_js(){
    $js='
		$(document).ready(function()
		{
		   $("#id_red_alerta_login").click(function(){
				alert("proba");
				return false;
		   });
		});';
 		
		drupal_add_js($js,'inline');
}                