<?php
function publico_alerta_user_inicio_callback(){
    alerta_user_inicio_callback();
}
function publico_alerta_user_mis_boletines_grupo_callback(){
    //return boletin_grupo_table();
    drupal_set_title(t('Customised Bulletins'));
    $output=publico_alerta_user_get_texto_mis_boletines_grupo();
    $output.=boletin_report_table(0,1);
    $output.='<fieldset>';
    $output.='<legend>'.t('Register').'</legend>';
    $output.=drupal_get_form('publico_register_user_register_form');
    $output.='</fieldset>';
    return $output;
}
function publico_alerta_boletin_grupo_my_web_callback(){
    return boletin_grupo_web_callback();
}
function publico_alerta_boletin_grupo_historico_callback(){
    return boletin_grupo_historico_callback();
}
function publico_alerta_is_publico(){
    if(hontza_canal_rss_is_visualizador_activado()){
        if(visualizador_is_pantalla()){
            return 1;
        }
    }
    if(publico_is_pantalla_publico()){
        return 1;
    }
    if(hontza_is_user_anonimo()){
        return 1;
    }
    if(publico_alerta_is_pantalla_alerta()){
        return 1;
    }
    return 0;
}
function publico_alerta_is_pantalla_alerta(){
    $param0=arg(0);
    //if(!empty($param0) && $param0=='publico_boletin_grupo'){
    if(!empty($param0) && $param0=='publico_boletin_report'){    
        return 1;
    }
    if(publico_vigilancia_is_publico_register_validar_email('boletin_subscribir')){
        return 1;
    }
    return 0;
}
function publico_alerta_boletin_report_previsualizacion_boletin_callback(){
    drupal_set_title(t('View Customised Bulletin on the web'));
    publico_alerta_boletin_report_previsualizacion_boletin_access_denied();
    $boletin_id=arg(1);
    publico_alerta_boletin_report_publico_access($boletin_id);
    return boletin_report_web_callback();
}
function publico_alerta_get_subscribir_link($row){
    //echo print_r($row,1);exit();
    //$is_activo_subscribir=publico_alerta_is_activo_subscribir($row);
    //intelsat-2015
    //$icono_no_recibir=my_get_icono_action('boletin_subscribir', t("I'd like to subscribe"));
    $icono_no_recibir=my_get_icono_action('boletin_subscribir', t("Manage subscription"));
    /*if($is_activo){
        $icono_no_recibir=my_get_icono_action('no_recibir', t("I'd like to unsubscribe"));
    }*/
    //$subscribir='publico_boletin_report/'.$row->id.'/subscribir';
    //$result=l($icono_no_recibir,$subscribir,array('html'=>true,'query'=>drupal_get_destination()));
    $subscribir='publico/validar_email/boletin_subscribir/'.$row->id;
    $result=l($icono_no_recibir,$subscribir,array('html'=>true));
    return $result;
}
function publico_alerta_boletin_report_subscribir_callback(){
    drupal_set_title(t('Subscribe'));
    return drupal_get_form('publico_alerta_boletin_report_subscribir_form');
}
function publico_alerta_boletin_report_subscribir_form(){
    $form=array();
    $boletin_id=arg(1);
    publico_alerta_boletin_report_publico_access($boletin_id);
    $form['id']=array(
        '#type'=>'hidden',
        '#value'=>$boletin_id,
    );
    $mail='';
    if(isset($_SESSION['publico_register_email_validado']) && !empty($_SESSION['publico_register_email_validado'])){
        $mail=$_SESSION['publico_register_email_validado'];
    }
    $form['email']=array(
    '#title'=>t('Mail'),
    '#type'=>'textfield',
    '#required'=>true,
    '#default_value'=>$mail,    
    );
    $boletin_report_subscrided=publico_alerta_is_boletin_report_subscrided($mail,$boletin_id,$row);
    if(!$boletin_report_subscrided){
        $form['subscribe_btn']=array(
            '#type'=>'submit',
            '#value'=>t('Subscribe'),
            //'#weight'=>1000,
            '#name'=>'subscribe_btn',
        );
    }else{
        $form['unsubscribe_btn']=array(
            '#type'=>'submit',
            '#value'=>t('Unsubscribe'),
            //'#weight'=>1001,
            '#name'=>'unsubscribe_btn',
        );
        drupal_set_title(t('Unsubscribe'));
    }
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'publico/alerta_user/mis_boletines_grupo')
    );
    return $form;
}
function publico_alerta_boletin_report_subscribir_form_submit($form,&$form_state){
    if(publico_existe_register($form_state['values']['email' ],$register_row)){
        $row=new stdClass();
        if(publico_alerta_is_boletin_report_subscrided($form_state['values']['email'],$form_state['values']['id'],$row)){
            if($form_state['clicked_button']['#name']=='subscribe_btn'){
                drupal_set_message(t('You are already subscribed to the bulletin').': <i><b>'.$row->titulo_boletin.'</b></i>');
            }else{
                publico_alerta_boletin_report_unset_email_externo($row,$form_state['values']['email']);
                drupal_set_message(t('You have been unsubscribed to the bulletin').': <i><b>'.$row->titulo_boletin.'</b></i>');
            }    
        }else{
            if($form_state['clicked_button']['#name']=='subscribe_btn'){
                publico_alerta_boletin_report_add_email_externo($row,$form_state['values']['email']);
                drupal_set_message(t('You have been subscribed to the bulletin').': <i><b>'.$row->titulo_boletin.'</b></i>');         
            }else{
                drupal_set_message(t('You are not subscribed to the bulletin').': <i><b>'.$row->titulo_boletin.'</b></i>');
            }    
        }       
    }else{
        drupal_set_message(t('This email is not registered'),'error');
    }
    drupal_goto('publico/alerta_user/mis_boletines_grupo');        
}
function publico_alerta_is_boletin_report_subscrided($email,$id,&$row){
    $row=boletin_report_get_row($id);
    if(isset($row->id) && !empty($row->id)){
        $email_externos=explode(',',$row->email_externos);
        if(in_array($email,$email_externos)){
            return 1;
        }        
    }
    return 0;
}
function publico_alerta_boletin_report_add_email_externo($row,$email){
    $email_externos=explode(',',$row->email_externos);
    $email_externos[]=$email;
    $email_externos=implode(',',$email_externos);
    db_query('UPDATE {boletin_report_array} SET email_externos="%s" WHERE id=%d',$email_externos,$row->id);
}
function publico_alerta_boletin_report_unset_email_externo($row,$email){
    $email_externos=explode(',',$row->email_externos);
    $unset_email_externos=array();
    $unset_email_externos[]=$email;
    $email_externos=array_diff($email_externos,$unset_email_externos);    
    $email_externos=implode(',',$email_externos);
    db_query('UPDATE {boletin_report_array} SET email_externos="%s" WHERE id=%d',$email_externos,$row->id);
}
function publico_alerta_add_is_boletin_publico_form_field(&$form,$boletin){
      $form['metodo_fs']['is_boletin_publico'] = array(
        '#type' => 'checkbox',
        '#title' => t('Public Bulletin'),
      );
      if(publico_alerta_boletin_report_is_boletin_publico($boletin)){
        $form['metodo_fs']['is_boletin_publico']['#attributes']=array('checked'=>'checked');
      }
}
function publico_alerta_boletin_report_is_boletin_publico($boletin_param,$boletin_id=''){
    $is_boletin_publico=0;
    if(isset($boletin_param->id) && !empty($boletin_param->id)){
        $boletin=$boletin_param;
    }else{
        $boletin=boletin_report_get_row($boletin_id);
    }
    if(isset($boletin->is_boletin_publico) && !empty($boletin->is_boletin_publico)){
        $is_boletin_publico=1;
    }
    return $is_boletin_publico;
}
function publico_alerta_boletin_report_publico_access($boletin_id){
    if(!publico_alerta_boletin_report_is_boletin_publico('',$boletin_id)){
        drupal_access_denied();
        exit();
    }
}
function publico_alerta_boletin_report_historico_callback(){
    return boletin_report_historico_callback();
}
function publico_alerta_boletin_report_download_html_callback(){
    boletin_report_download_html_callback();
}
function publico_alerta_boletin_report_forward_validate_email_callback(){
    return drupal_get_form('publico_alerta_boletin_report_forward_validate_email_form');
}
function publico_alerta_boletin_report_forward_validate_email_form(){
    $form=array();
    $form['email']=array(
    '#title'=>t('Your email'),
    '#type'=>'textfield',
    '#required'=>true
    );
    $form['validar_email_type']=array(
    '#type'=>'hidden',
    '#value'=>'forward_bulletin'
    );
    $boletin_report_array_id=arg(1);
    $form['boletin_report_array_id']=array(
    '#type'=>'hidden',
    '#value'=>$boletin_report_array_id
    );
    $form['id']=array(
    '#type'=>'hidden',
    '#value'=>arg(3)
    );    
    $form['validar_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Validate'),
        '#name'=>'validate_btn',
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'publico_boletin_report/'.$boletin_report_array_id.'/historico')
    );
    return $form;
}
function publico_alerta_boletin_report_forward_validate_email_form_submit($form,&$form_state){
    publico_register_validar_email_form_submit($form,$form_state);
}
function publico_alerta_boletin_report_forward_validate_email_goto($form_state){
    $_REQUEST['destination']='';
    drupal_goto('publico_boletin_report/'.$form_state['values']['boletin_report_array_id'].'/forward/'.$form_state['values']['id']);
}
function publico_alerta_boletin_report_forward_callback(){
    return boletin_report_forward_callback();
}
function publico_alerta_boletin_report_forward_form(){
    return boletin_report_forward_form();
}
function publico_alerta_boletin_report_forward_form_submit($form,&$form_state){
    boletin_report_forward_form_submit($form,$form_state);
}
function publico_alerta_boletin_report_download_html_validate_email_callback(){
    return drupal_get_form('publico_alerta_boletin_report_download_html_validate_email_form');
}
function publico_alerta_boletin_report_download_html_validate_email_form(){
    $form=array();
    $form['email']=array(
    '#title'=>t('Your email'),
    '#type'=>'textfield',
    '#required'=>true
    );
    $form['validar_email_type']=array(
    '#type'=>'hidden',
    '#value'=>'download_html'
    );
    $boletin_report_array_id=arg(1);
    $form['boletin_report_array_id']=array(
    '#type'=>'hidden',
    '#value'=>$boletin_report_array_id
    );
    $form['id']=array(
    '#type'=>'hidden',
    '#value'=>arg(3)
    );    
    $form['validar_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Validate'),
        '#name'=>'validate_btn',
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'publico_boletin_report/'.$boletin_report_array_id.'/historico')
    );
    return $form;
}
function publico_alerta_boletin_report_download_html_validate_email_goto($form_state){
    $_REQUEST['destination']='';
    drupal_goto('publico_boletin_report/'.$form_state['values']['boletin_report_array_id'].'/download_html/'.$form_state['values']['id']);
}
function publico_alerta_boletin_report_download_html_validate_email_form_submit($form,&$form_state){
    publico_register_validar_email_form_submit($form,$form_state);
}
function publico_alerta_boletin_report_previsualizacion_boletin_access_denied(){
    $is_alerta_publico=hontza_canal_rss_is_alerta_publico();
    if($is_alerta_publico && hontza_is_user_anonimo()){
        $boletin_report_array_id=arg(1);
        $id=arg(3);
        $access_denied=0;
        if(empty($id)){
            $access_denied=1;
        }/*else if(!publico_alerta_is_boletin_report_sended($boletin_report_array_id,$id)){
            $access_denied=1;
        }*/
        if($access_denied){
            drupal_access_denied();
            exit();
        }
    }
}
function publico_alerta_is_boletin_report_sended($boletin_report_array_id,$id){
    $where=array();
    $where[]='1';
    $where[]='e.boletin_report_array_id='.$boletin_report_array_id;
    $where[]='e.fecha_sended!="0000-00-00 00:00:00"';
    $where[]='e.id='.$id;
    $sql='SELECT * FROM {boletin_report_array_edit} e WHERE '.implode(' AND ',$where);    
    $res=db_query($sql);
    while($r=db_fetch_object($res)){
        return 1;
    }    
    return 0;
}
function publico_alerta_user_get_texto_mis_boletines_grupo(){
    $nid=publico_alerta_user_get_texto_mis_boletines_grupo_nid();
    $node=hontza_node_load_by_lang($nid);
    if(isset($node->body) && !empty($node->body)){
        return $node->body;
    }
    return '';
}
function publico_alerta_user_get_texto_mis_boletines_grupo_nid(){
    if(visualizador_is_red_alerta()){
        return 144782;
    }else{
        $visualizador_boletin_texto_node=visualizador_get_boletin_texto_node();
        if(isset($visualizador_boletin_texto_node->nid) && !empty($visualizador_boletin_texto_node->nid)){
            return $visualizador_boletin_texto_node->nid;
        }        
    }
    return '';
}
function publico_alerta_get_unsubscribe_link_mail($result_in,$br,$mail){
    $result=$result_in;
    $r=publico_register_get_register_row($mail);
    if(isset($r->id) && !empty($r->id)){
        $url='publico/validar_email/boletin_subscribir/'.$br->id;
        $link=l(t('Unsubscribe to this Bulletin, please click here'),$url,array('absolute'=>TRUE));
        $result='<p style="text-align:center;font-size:10px;">'.$link.'</p>'.$result;
    }
    return $result;
}