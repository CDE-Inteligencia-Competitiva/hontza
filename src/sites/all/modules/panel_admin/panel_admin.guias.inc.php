<?php
function panel_admin_guias_left_title(){
    return t('Guides');
}
function panel_admin_guias_get_block_content(){
    if(panel_admin_guias_access()){
        $html=array();
        $html[]=l(t('Guides'),'panel_admin/guias');
        return implode('<BR>',$html);
    }
    return '';
}
function panel_admin_guias_access(){
    if(hontza_is_sareko_id('ROOT')){    
        if(is_super_admin()){
            return TRUE;
        }
    }    
    return FALSE;
}
function panel_admin_guias_callback(){
    $headers=array();
    $headers[0]=t('Title');         
    $headers[1]=t('Actions');	
    $my_limit=20;
    $rows=array();
    $fields=panel_admin_guias_get_fields();
    if(!empty($fields)){
        foreach($fields as $i=>$field){
            $row=array();
            $row[0]=$field[0];
            $row[1]=panel_admin_guias_define_acciones($field[1]);
            $rows[]=$row;
        }
    }    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
      $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_canales'));
      $output .= theme('pager', NULL, $my_limit);    
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Guides'));
    return $output;
}
function panel_admin_guias_get_fields(){
    $fields=array();
    $fields[0][0]='Guide for Users';
    $fields[0][1]='users_network_guide';
    $fields[1][0]='Guide for Admins';
    $fields[1][1]='admins_network_guide';
    $fields[2][0]='Guide for Red Alerta Users';
    $fields[2][1]='users_guide';
    $fields[3][0]='Guide for Red Alerta Admins';
    $fields[3][1]='admins_guide';
    return $fields;
}
function panel_admin_guias_define_acciones($type){
    $html=array();
    $title_view=t('View');
    $url=panel_admin_guias_get_guia_url($type);
    $html[]=l(my_get_icono_action('viewmag',$title_view),$url,array('absolute'=>true,'html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('title'=>$title_view,'alt'=>$title_view,'target'=>'_blank')));    
    $title_view=t('Edit');
    $html[]=l(my_get_icono_action('edit',$title_view),'panel_admin/guias/'.$type.'/upload',array('absolute'=>true,'html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('title'=>$title_view,'alt'=>$title_view)));        
    return implode('&nbsp;',$html);
}
function panel_admin_guias_get_guia_url($type){
    return 'red/guia_internet/'.$type;    
}
function panel_admin_guias_upload_form(){
    $form=array();
    $form['#attributes'] = array('enctype' => 'multipart/form-data');
    $file_name=arg(2);
    $form['file_name']=array(
        '#type'=>'hidden',
        '#value'=>$file_name,
    );
    $form['guia_file']=array('#type'=>'file',
        '#title'=>t('Guide file'),
    );
    $form['upload_btn']=array(
        '#type'=>'submit',
        '#default_value'=>t('Upload'),
    );
    $form['return_btn']=array(
        '#value'=>l(t('Return'),'panel_admin/guias'),
    );
    $title=t(panel_admin_guias_get_upload_title($file_name));
    drupal_set_title($title);
    return $form;
}
function panel_admin_guias_get_filepath($file_name){
    $result='/home/hontza3_files/'.$file_name.'.pdf';
    return $result;
}
function panel_admin_guias_upload_form_submit($form, &$form_state){
    if(isset($form_state['values']) && isset($form_state['values']['file_name'])){
        $file_name=$form_state['values']['file_name'];
        foreach ($_FILES['files']['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
               $tmp_name = $_FILES['files']['tmp_name'][$key];
               $filepath=panel_admin_guias_get_filepath($file_name);
               move_uploaded_file($tmp_name,$filepath);
               break;
            }
        }
        drupal_set_message(t('File uploaded'));
        drupal_goto('panel_admin/guias');
    }
}
function panel_admin_guias_get_upload_title($file_name){
    $result=array();
    $result['users_network_guide']='Upload Guide for Users';
    $result['admins_network_guide']='Upload Guide for Admins';
    $result['users_guide']='Upload Guide for Red Alerta Users';
    $result['admins_guide']='Upload Guide for Red Alerta Admins';
    if(isset($result[$file_name])){
        return $result[$file_name];
    }
    return '';
}
function panel_admins_guias_get_file_url($file_name){
    $result='http://online.hontza.es/red/guia_internet/'.$file_name;
    return $result;
}