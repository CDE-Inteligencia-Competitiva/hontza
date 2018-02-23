<?php
function panel_admin_dashboard_left_title(){
    return t('DASHBOARD');
}
function panel_admin_dashboard_block_content(){
    $html=array();
    if(red_dashboard_is_activado()){
        $html[]=l(t('Settings'),'panel_admin/dashboard/settings');
    }
    return implode('<br>',$html);
}
function panel_admin_dashboard_settings_form(){
    $form=array();
    $form['custom_dashboard_type']=array(
        '#title'=>'Dashboard type',
        '#type'=>'select',
        '#options'=>panel_admin_dashboard_get_dashboard_type_options(),
        '#default_value'=>panel_admin_dashboard_get_custom_dashboard_type(),
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#name'=>'save_btn',
        '#default_value'=>t('Save'),        
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'panel_admin'),        
    );
    return $form;
}
function panel_admin_dashboard_get_dashboard_type_options(){
    $result=array();
    $result['normal']=t('Standard');
    $result['searches']=t('Searches');
    return $result;
}
function panel_admin_dashboard_get_custom_dashboard_type(){
    $result=variable_get('custom_dashboard_type','normal');
    return $result;
}
function panel_admin_dashboard_settings_form_submit($form,&$form_state){
    $variable_array=array('custom_dashboard_type');
    if(!empty($variable_array)){
        foreach($variable_array as $i=>$my_variable){
            if(isset($form_state['values'][$my_variable]) && !empty($form_state['values'][$my_variable])){
                variable_set($my_variable,$form_state['values'][$my_variable]);
            }
        }    
    }
    drupal_set_message(t('Dashboard settings saved'));
}