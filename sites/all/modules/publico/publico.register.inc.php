<?php
function publico_register_user_register_callback(){
    drupal_goto('publico/alerta_user/mis_boletines_grupo');
    exit();
    //return drupal_get_form('publico_register_user_register_form');
}
function publico_register_user_register_form(){
    $form=array();
    $prefix_left='<div style="width:100%;float:left;"><div style="width:45%;float:left;">';
    $suffix_left='</div>';
    $prefix_right='<div style="width:45%;float:left;padding-left:20px;">';
    $suffix_right='</div></div>';
    
    $form['email']=array(
        '#title'=>t('Mail'),
        '#type'=>'textfield',
        '#required'=>true,
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
    );    
    
    
    $form['name']=array(
        '#title'=>t('Name'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
    );
    
    
    $form['organisation']=array(
        '#title'=>t('Organisation'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right, 
    );    
    
   
    /*$form['surname']=array(
        '#title'=>t('Surname').' 1',
        '#type'=>'textfield',
        '#required'=>true,
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
    );    
    $form['surname2']=array(
        '#title'=>t('Surname').' 2',
        '#type'=>'textfield',
        '#required'=>true,
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
    );*/    
    $form['address']=array(
        '#title'=>t('Address'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
    );    
    $form['codigo_postal']=array(
        '#title'=>t('Postal code'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right, 
    );    
     $form['provincia']=array(
        '#title'=>t('Province'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left, 
    );    
    $form['country']=array(
        '#title'=>t('Country'),
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right, 
    );
    $form['cif']=array(
        '#title'=>'CIF',
        '#type'=>'textfield',
        //'#required'=>true,
        '#prefix'=>'<div style="display:none">'.$prefix_right,
        '#suffix'=>$suffix_right.'</div>',
    );            
    $form['message']=array(
        '#title'=>t('Message'),
        '#type'=>'textarea',
        '#prefix'=>'<div style="display:none">',
        '#suffix'=>'</div>',
    );     
     /*$boletines=publico_register_user_register_boletines_options();
     $form['boletines']=array(
        '#title'=>t('Bulletins'),
        '#type'=>'select',
        '#options'=>$boletines,
        '#multiple'=>true,         
    );*/
     
      $form['send_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Send'),
        '#weight'=>1000,
        '#attributes'=>array('style'=>'margin-top:10px;'),  
    );
     
    return $form;
}
function publico_register_user_register_form_submit($form,&$form_state){
    if(!publico_existe_register($form_state['values']['email'],$register_row)){
        /*$surname=$form_state['values']['surname'];
        $surname2=$form_state['values']['surname2'];*/
        $surname='';
        $surname2='';
        $timestamp=time();
        db_query('INSERT INTO {visualizador_register}(name,cif,surname,address,surname2,provincia,email,country,organisation,codigo_postal,message,timestamp) VALUES("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%d)',$form_state['values']['name'],$form_state['values']['cif'],$surname,$form_state['values']['address'],$surname2,$form_state['values']['provincia'],$form_state['values']['email'],$form_state['values']['country'],$form_state['values']['organisation'],$form_state['values']['codigo_postal'],$form_state['values']['message'],$timestamp);            
        /*if(isset($form_state['values']['boletines']) && !empty($form_state['values']['boletines'])){
            publico_register_user_register_susbscribir_boletines($form_state['values']['email'],$form_state['values']['boletines']);
        }*/
        drupal_set_message(t('Your email has been registered'));
        publico_register_user_register_send_message_user($form_state);
        publico_register_user_register_send_message_user_admin($form_state);
        drupal_goto('publico/alerta_user/mis_boletines_grupo');
    }else{
        drupal_set_message(t('This user is already registered'));
    }    
}
function publico_existe_register($mail,&$register_row){
    $result=0;
    $register_row=publico_register_get_register_row($mail);
    if(isset($register_row->id) && !empty($register_row->id)){
       $result=1; 
    }
    return $result;
}
function publico_register_get_register_row($mail='',$id=''){
    $where=array();
    $where[]='1';
    if(!empty($mail)){
        $where[]='email="'.$mail.'"';
    }
    if(!empty($id)){
        $where[]='id='.$id;
    }
    $res=db_query('SELECT * FROM {visualizador_register} WHERE '.implode(' AND ',$where));
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function publico_register_user_register_boletines_options(){
    $options=array();
    $sql=boletin_report_table(1,1);
    $query=db_query($sql);
    while($boletin=db_fetch_object($query)){
        $options[$boletin->id]=$boletin->titulo;
    }
    return $options;
}
function publico_register_user_register_susbscribir_boletines($mail,$boletines){
    if(!empty($boletines)){
        foreach($boletines as $boletin_id=>$title){
            if(!publico_alerta_is_boletin_report_subscrided($mail,$boletin_id,$boletin)){                
                publico_alerta_boletin_report_add_email_externo($boletin,$mail);
            }   
        }
    }
}
function publico_register_validar_email_form(){
    $form=array();
    $form['email']=array(
    '#title'=>t('Your email'),
    '#type'=>'textfield',
    '#required'=>true
    );
    $form['validar_email_type']=array(
    '#type'=>'hidden',
    '#value'=>arg(2)
    );
    $form['validar_email_id']=array(
    '#type'=>'hidden',
    '#value'=>arg(3)
    );
    $form['validar_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Validate'),
        '#name'=>'validate_btn',
    );    
    return $form;
}
function publico_register_validar_email_form_submit($form,&$form_state){
    $mail=$form_state['values']['email'];
    if(isset($_SESSION['publico_register_email_validado'])){
        unset($_SESSION['publico_register_email_validado']);
    }
    if(publico_existe_register($mail,$register_row)){
        $_SESSION['publico_register_email_validado']=$mail;
        if($form_state['values']['validar_email_type']=='forward_bulletin'){
            publico_alerta_boletin_report_forward_validate_email_goto($form_state);
        }else if($form_state['values']['validar_email_type']=='download_html'){
            publico_alerta_boletin_report_download_html_validate_email_goto($form_state);
        }else{
            publico_register_validar_email_drupal_goto($form_state['values']['validar_email_type'],$form_state['values']['validar_email_id']);
        }    
    }else{
        drupal_set_message(t('You have to register your email'));
        $_REQUEST['destination']='';
        drupal_goto('publico/user/register');
    }
}
function publico_register_validar_email_drupal_goto($validar_email_type,$boletin_id){
    $goto='';
    if($validar_email_type=='node_add_noticia'){
        drupal_goto('node/add/noticia',array('destination'=>'publico/vigilancia/ultimos'));
    }else if($validar_email_type=='boletin_subscribir'){
        drupal_goto('publico_boletin_report/'.$boletin_id.'/subscribir',array('destination'=>'publico/alerta_user/mis_boletines_grupo'));
    }else if($validar_email_type=='comment'){
        $_REQUEST['destination']='';
        drupal_goto('publico_comment/reply/'.$boletin_id);
    }
    return $goto;
}
function publico_register_user_register_send_message_user($form_state){
    global $base_url,$language;
    $mail_to=$form_state['values']['email'];
    $subject=t('Your email  has been registered');
    $br='<br><br>';    
    $message=t('Hi').' '.$form_state['values']['name'].','.$br;
    $grupo=visualizador_create_grupo_base_path();
    $result=$result_in;
    $langcode='';
    if($language->language!='en'){
         $langcode='/'.$language->language;
    }
    $boletines_url=$base_url.$langcode.'/'.$grupo.'/publico/alerta_user/mis_boletines_grupo';
    $boletines_url=l($boletines_url,$boletines_url,array('absolute'=>true));
    $message.=t('You have been registered successfully with your email "!correo". Now you can get subscribed to any of the available bulletins here !boletines_url',array('!correo'=>$mail_to,'!boletines_url'=>$boletines_url)).$br;
    //print $message;exit();
    //intelsat-2016
    red_copiar_send_mail($mail_to,$subject,$message,'mimemail','');
}
function publico_register_user_register_send_message_user_admin($form_state){
    global $base_url,$language;
    $subject=t('A new user has been registered');
    $br='<br><br>';
    $user=user_load(1);
    $mail_to=$user->mail;
    $message=t('Hi').' '.$user->name.','.$br;
    $grupo=visualizador_create_grupo_base_path();
    $result=$result_in;
    $langcode='';
    if($language->language!='en'){
         $langcode='/'.$language->language;
    }
    $boletines_url=$base_url.$langcode.'/'.$grupo.'/publico/alerta_user/mis_boletines_grupo';
    $boletines_url=l($boletines_url,$boletines_url,array('absolute'=>true));
    $message.=t('The user "!user" with email "!correo" has been registered',array('!user'=>$form_state['values']['name'],'!correo'=>$form_state['values']['email'])).$br;
    //print $message;exit();
    //intelsat-2016
    red_copiar_send_mail($mail_to,$subject,$message,'mimemail','');
}
function publico_register_comment_validate_email_reply_form(){
    $form=array();
    $form['email']=array(
    '#title'=>t('Your email'),
    '#type'=>'textfield',
    '#required'=>true
    );
    $form['validar_email_type']=array(
    '#type'=>'hidden',
    '#value'=>'comment'
    );
    $form['validar_email_id']=array(
    '#type'=>'hidden',
    '#value'=>arg(2)
    );
    $form['validar_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Validate'),
        '#name'=>'validate_btn',
    );    
    return $form;
}
function publico_register_comment_validate_email_reply_form_submit($form,&$form_state){
    publico_register_validar_email_form_submit($form,$form_state);
}
function publico_register_comentario_user_register($comment,$author){
    $result=$author;
    $r=publico_register_get_visualizador_comments_row($comment->cid);
    if(isset($r->id) && !empty($r->id)){
        $register=publico_register_get_register_row('',$r->visualizador_register_id);
        if(isset($register->id) && !empty($register->id)){
            $result=$register->name;
        }
    }
    return $result;
}
function publico_register_get_visualizador_comments_row($cid){
    $res=db_query('SELECT * FROM {visualizador_comments} WHERE cid=%d',$cid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $visualizador_comments_row=new stdClass();
    return $visualizador_comments_row;
}
function publico_register_registered(){
    $output='';
    //simulando
    //$res=db_query('UPDATE {visualizador_register} SET timestamp=%d WHERE timestamp=0',time());
    $headers=array();
    $headers[0]=array('data'=>t('Name'),'field'=>'name');
    $headers[1]=array('data'=>t('Mail'),'field'=>'email');
    $headers[2]=array('data'=>t('Organisation'),'field'=>'organisation');
    $headers[3]=array('data'=>t('Date'),'field'=>'timestamp');
    $headers[4]=t('Actions');
    
    $my_limit=30;
    
    $sort='desc';
    $field='timestamp';
    $is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Name')){
            $field='name';
        }else if($order==t('Mail')){
            $field='email';
        }else if($order==t('Organisation')){
            $field='organisation';
        }else if($order==t('Date')){
            $field='timestamp';
        }
    }
        
    $where=array();
    $where[]='1';
    
    
    $sql='SELECT *
    FROM {visualizador_register} visualizador_register 
    WHERE '.implode(' AND ',$where).' 
    ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->name;
      $rows[$kont][1]=$r->email;
      $rows[$kont][2]=$r->organisation;
      $rows[$kont][3]=date('Y-m-d',$r->timestamp);
      $rows[$kont][4]=array('data'=>publico_register_define_acciones($r),'style'=>'white-space:nowrap;'); 
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Registered Users'));
    //
    return $output;
}
function publico_register_define_acciones($r){
    $html=array();
    $html[]=l(my_get_icono_action('viewmag',t('View')),'panel_admin/registered/'.$r->id.'/view',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'panel_admin/registered/'.$r->id.'/delete',array('query'=>$destination,'html'=>true));
    return implode('',$html);
}
function publico_register_registered_view(){
    drupal_set_title(t('Registered User')); 
    $r=publico_register_get_register_row('',arg(2));
    $html[]='<table class="table_node_view">';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Name').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->name.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Mail').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->email.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Organisation').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->organisation.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('CIF').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->cif.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Address').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->address.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Postal code').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->codigo_postal.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Province').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->provincia.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Country').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->country.'</td>';
    $html[]='</tr>';
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t('Message').'</b></td>';
    $html[]='<td class="td_value_node_view">'.$r->message.'</td>';
    $html[]='</tr>';
    $html[]='</table>';
    $html[]='<div>'.l(t('Return'),'panel_admin/registered').'</div>';
    return implode('',$html);
}
function publico_register_registered_delete_html(){
    return drupal_get_form('publico_register_registered_delete_form');
}
function publico_register_registered_delete_form(){
    $form=array();
    $id=arg(2);
    $r=publico_register_get_register_row('',$id);
    drupal_set_title(t('Are you sure you want to delete %name?', array('%name' =>$r->name)));
    $form['id']=array(
      '#type'=>'hidden',
      '#default_value'=>$id,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $url_cancel='panel_admin/registered';
    $form['cancel_btn']['#value']=l(t('Cancel'),$url_cancel);        
    
    return $form;
}
function publico_register_registered_delete_form_submit($form,&$form_state){
    $id='';
    if(isset($form_state['values']['id'])){
       $id=$form_state['values']['id'];
    }
    //
    if(!empty($id)){
       publico_register_registered_delete($id);
    }
    drupal_goto('panel_admin/registered');    
}
function publico_register_registered_delete($id){
    db_query('DELETE FROM {visualizador_register} WHERE id=%d',$id);
}