<?php
function hound_javascript_simular_callback(){
    $hound_id=770;
    $javascript_type=arg(3);
    hound_javascript_crear_canal($hound_id,$javascript_type);
}
function hound_javascript_add_javascript_form_field(&$form,$javascript_type){
    $form['fuentes']['hound1_javascript_btn']=array(
                '#type'=>'button',
                '#default_value'=>'Javascript',
            );
            hound_javascript_add_javascript_js($javascript_type);
}
function hound_javascript_add_javascript_js($javascript_type){
    $url=url('hound/javascript/simular');
    $js='var hound_javascript_url="'.$url.'/'.$javascript_type.'";
        $(document).ready(function(){
        $("#edit-hound1-javascript-btn").click(function(){
            window.open(hound_javascript_url);
            return false;
        });            
    });';        
    drupal_add_js($js,'inline');
}
function hound_javascript_get_javascript_js($hound_id,$javascript_type){
    $url=url('hound/javascript/simular');
    $edit_hound_id='edit-hound1-id';
    $edit_hound_load_btn='edit-hound1-load-btn';
    if(!empty($javascript_type) && in_array($javascript_type,array('edit','editar'))){
        $edit_hound_id='edit-hound-id-editing';
        $edit_hound_load_btn='edit-hound-editing-load-btn';
    }
    $js='<script type="text/javascript">';
    $js.='var edit_hound_id="'.$edit_hound_id.'";
        var edit_hound_load_btn="'.$edit_hound_load_btn.'";
        var hound_id="'.$hound_id.'";
        $(document).ready(function(){            
            //window.opener.$("edit-hound1-id").text(hound_id);
            //$("edit-hound1-id",window.opener.document).val(hound_id);
            var hound_id_text = window.opener.document.getElementById(edit_hound_id);
            hound_id_text.value=hound_id;
            hound_load_btn=window.opener.document.getElementById(edit_hound_load_btn);
            hound_load_btn.click();
            window.close();
    });';
    $js.='</script>';
    return $js;
}
function hound_javascript_custom_access(){
    if(user_access('access content')){
        return TRUE;
    }
    return FALSE;
}
function hound_javascript_crear_canal_callback(){
    $hound_id=arg(2);
    $javascript_type=arg(3);
    file_get_contents('http://192.169.1.17/hound2/Query.php?channels='.$hound_id);
    hound_javascript_crear_canal($hound_id,$javascript_type);
    //sortu berri den canala eskrapeatzeko
    
}
function hound_javascript_crear_canal($hound_id,$javascript_type){
    $javascript=hound_javascript_get_javascript_js($hound_id,$javascript_type);
    $html='<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link rel="stylesheet" href="http://hound.hontza.es/blog/public/css/style.css">
		<link rel="stylesheet" type="text/css" href="http://hound.hontza.es/blog/public/css/form.css">	
		<link rel="stylesheet" type="text/css" href="http://hound.hontza.es/blog/public/css/screen.css" media="screen, projection">
		<link rel="stylesheet" type="text/css" href="http://hound.hontza.es/blog/public/css/print.css" media="print">
		<link rel="stylesheet" type="text/css" href="http://hound.hontza.es/blog/public/css/gridviewStyle.css">
		<link rel="stylesheet" type="text/css" href="http://hound.hontza.es/blog/public/css/pager.css">
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
		
	<script type="text/javascript" src="http://hound.hontza.es/blog/public/javascript/channelParams.js"></script>
	<script type="text/javascript" src="http://hound.hontza.es/blog/public/javascript/jquery.js"></script>
	<script type="text/javascript" src="http://hound.hontza.es/blog/public/javascript/jquery.ba-bbq.js"></script>
	<script type="text/javascript" src="http://hound.hontza.es/blog/public/javascript/jquery.yiigridview.js"></script>
	<script type="text/javascript" src="http://hound.hontza.es/blog/public/javascript/tablefilter/tablefilter.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

    </head>
    <body>
<div class="container">
<div id="header">
		<div id="logo"><img src="http://hound.hontza.es/hound2/public/Hound.png" width="300"  /></div>
	</div>
	 
	
<div class="span-5 last">
	 
</div>
 <div class="span-24 ">
    
<h2>Tu código del canal es: <b>'.$hound_id.'</b></h2>'.$javascript.'
    </div>

</div>
    </body>

</html>';
    print $html;
    exit();
    
}