<?php
function hound_enlazar_inc_is_activado(){
    if(defined('_IS_HOUND_ENLAZAR') && _IS_HOUND_ENLAZAR==1){
        return 1;
    }
    return 0;
}
function hound_enlazar_inc_hound_key_save($form_state){
    if(hound_enlazar_inc_is_activado()){
        if(isset($form_state['values']['hound_key'])){
            variable_set('hound_key',$form_state['values']['hound_key']);
        }
    }
}
function hound_enlazar_inc_add_create_edit_form_field(&$form){
    if(hound_enlazar_inc_is_activado()){
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
            $form['hound_editing_edit_btn']=array(
                '#type'=>'button',
                '#default_value'=>t('Edit Hound'),
            );
            hound_enlazar_inc_add_edit_js($node);
        }else{
            $form['fuentes']['hound1_create_btn']=array(
                '#type'=>'button',
                '#default_value'=>t('Create Hound'),
            );
            hound_enlazar_inc_add_create_js();
        }
    }
}
function hound_enlazar_inc_add_create_js(){
    $url=hound_enlazar_inc_get_create_url();
    $js='var hound_enlazar_url="'.$url.'";
        $(document).ready(function(){
        $("#edit-hound1-create-btn").click(function(){
            window.open(hound_enlazar_url);
            return false;
        });            
    });';        
    drupal_add_js($js,'inline');
}
function hound_enlazar_inc_get_base_url(){
    $url=hontza_enlazar_inc_define_hound_url();    
    return $url;
}
function hontza_enlazar_inc_define_hound_url(){
    $url='http://hound.hontza.es/blog/public';
    return $url;
}
function hound_enlazar_inc_get_create_url(){
    $url=hontza_enlazar_inc_define_hound_url();
    $url.='/channels/crearCanal/'.hound_enlazar_inc_get_key();
    return $url;
}
function hound_enlazar_inc_get_key(){
    $hound_key=variable_get('hound_key','');
    return $hound_key;
}
function hound_enlazar_inc_add_edit_js($canal){
    $url=hound_enlazar_inc_get_edit_url($canal);
    $js='var hound_enlazar_url="'.$url.'";
        $(document).ready(function(){
        $("#edit-hound-editing-edit-btn").click(function(){
            window.open(hound_enlazar_url);
            return false;
        });            
    });';        
    drupal_add_js($js,'inline');
}
function hound_enlazar_inc_get_edit_url($canal){
    $url=hontza_enlazar_inc_define_hound_url();
    $hound_id=hontza_enlazar_inc_get_hound_id($canal);
    $url.='/channels/editarCanal/'.$hound_id.'/'.hound_enlazar_inc_get_key();
    return $url;
}
function hound_enlazar_inc_delete_canal_hound($canal){
    hound_delete_canal_hound_parametros($canal);
    hound_enlazar_inc_delete_canal_hound_servidor($canal);
}
function hound_enlazar_inc_delete_canal_hound_servidor($canal){
    if(hound_enlazar_inc_is_activado()){
        $url=hound_enlazar_inc_get_delete_url($canal);
        //print $url;exit();
        file_get_contents($url);
    }    
}
function hound_enlazar_inc_get_delete_url($canal){
    $url=hontza_enlazar_inc_define_hound_url();
    $hound_id=hontza_enlazar_inc_get_hound_id($canal);
    $url.='/channels/borrarCanal/'.$hound_id.'/'.hound_enlazar_inc_get_key();
    return $url;
}
function hontza_enlazar_inc_get_hound_id($canal){
    $hound_id=hound_get_hound_id_by_nid('',$canal);
    $hound_id=urlencode($hound_id);
    return $hound_id; 
}    