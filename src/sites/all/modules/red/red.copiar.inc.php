<?php
function red_copiar_is_copiar_activado(){
    if(module_exists('compartir_documentos')){
        return compartir_documentos_is_compartir_documentos_activado();
    }
    return 0;
}
function red_copiar_is_enviar_mail($type=''){
    if(red_copiar_is_copiar_activado()){
        return compartir_documentos_is_enviar_mail($type);
    }
    return 0;
}
function red_copiar_wiki_node_form_alter(&$form,&$form_state,$form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_wiki_node_form_alter($form,$form_state,$form_id);
    }
}
function red_copiar_is_wiki_compartir_fields_show(){
    if(red_copiar_is_copiar_activado()){
        return compartir_documentos_is_wiki_compartir_fields_show();
    }
    return 0;
}
function red_copiar_get_node_update_link($node,$result_in){
    $result=$result_in;
    if(red_copiar_is_copiar_activado()){
        return compartir_documentos_get_node_update_link($node,$result);
    }
    return $result;
}
function red_copiar_on_wiki_save($op,&$node){
    if(red_copiar_is_copiar_activado()){
        if($node->type=='wiki'){
            compartir_documentos_on_wiki_save($op,$node);
        }
    }
}
function red_copiar_get_href($html){
   preg_match("/href=\"(.*?)\"/i",$html, $matches);
   return $matches[1];    
}
function red_copiar_get_canal_import_rss_by_arg($rss){
   $result=$rss; 
   if(isset($_REQUEST['rss_url']) && !empty($_REQUEST['rss_url'])){
       $result=base64_decode($_REQUEST['rss_url']);
   }
   return $result;
}
function red_copiar_crear_canal_yql_callback(){
   $rss_url=arg(3);
   $mail_from=arg(4);
   drupal_goto('crear/canal-yql','simple=1&rss_url='.$rss_url.'&mail_from='.$mail_from);
   exit();
}
function red_copiar_crear_canal_yql_access(){
   if(hontza_is_user_anonimo()){
       return FALSE;
   } 
   return TRUE;
}
function red_copiar_email_html_is_activado(){
   if(module_exists('email_html')){
        if(email_html_is_activado()){
            return 1;
        }
   }
   return 0;
}
function red_copiar_send_mail($mail_to,$subject,$body_in,$send_method,$alert_type,$is_mensaje_despedida=0) {
   $message=$body_in; 
   if(red_copiar_email_html_is_activado()){
        $message=email_html_add_html($message,1);
        //print $message;exit();
   }
   $body=$message;
   my_call_send_mail($mail_to,$subject,$body,$send_method,$alert_type,$is_mensaje_despedida);        
}
function red_copiar_get_caducidad(){
   $result=time();
   $result=base64_encode($result);
   return $result;
}
function red_copiar_get_email_message($mail,$compartir_servidor_url,$node,$document_type_in='document',$is_validar_usuario_network=1,$message_text=''){
    global $user;
    //simulando
    //$is_validar_usuario_network=0;
    if($is_validar_usuario_network){
        $node_title=red_copiar_get_exportar_rss_node_title($node);
        $html=array();
        $document_type=red_copiar_get_document_type($node,$document_type_in);
        $html[]='<p>Hi @mail_to,</p>';
        $html[]='<p>You are receiving this email because @userid_origen would like to share this @document_type with you.</p>';
        $html[]='<p><u>Message</u>: @message_text<p>';
        $html[]='<p>Please click on it and then select the group where you want to import it</p>';
        $icono_documento=red_copiar_get_icono_documento($node);
        $style=' style="text-decoration:none;"';
        //$parrafo_style=' style="white-space:nowrap;"';
        $parrafo_style='';
        $html[]='<p'.$parrafo_style.'><a href="@link">'.my_get_icono_action('copiar_nodo24','').'</a> '.$icono_documento.' <a href="@link"'.$style.'><b><span style="color:#FF5722;font-size:18px;">@node_title</span></b></a></p>';
        $html[]='<p>Update settings: @update_settings</p>';
        //$html[]='<p>Thank you for using Hontza Network!</p>';
        //$html[]='<p>Hontza Team</p>';
        $html[]='<p>Note: This link has a validity of 24h and it can be used only once.</p>';
        $params=array('@mail_to'=>$mail,'@userid_origen'=>$user->mail,'@link'=>$compartir_servidor_url,'@node_title'=>$node_title,'@message_text'=>$message_text);
        $frecuencia='';
        $frecuencia_hora='';
        $frecuencia_value='';
        if(red_copiar_is_copiar_activado()){
            $frecuencia=compartir_documentos_get_wiki_frecuencia($node,1);
            $frecuencia_value=compartir_documentos_get_wiki_frecuencia($node);
            $frecuencia_hora=compartir_documentos_get_wiki_frecuencia_hora($node);
        }
        if(empty($frecuencia) && empty($frecuencia_hora)){
            $frecuencia=t('Final document');
        }else{
            if($frecuencia_value!='manual'){
                $frecuencia.=' '.$frecuencia_hora;
            }    
        }
        $params['@update_settings']=$frecuencia;
        $params['@document_type']=$document_type;
        $html=t(implode('',$html),$params);
        //print $html;exit();
        return $html;
    }else{
        return red_copiar_get_email_message_no_network($mail,$compartir_servidor_url,$node,$document_type_in,$message_text);
    }
}
function red_copiar_is_help_popup_node($node){
    $type_array=array('save_current_search','guardar_resultado_solr','save_current_rss','send_message_popup','red_exportar_rss_enviar_mail'
    ,'compartir_documentos');
    if(!in_array($node->nid,$type_array)){
        return 1;
    }
    return 0;
}
function red_copiar_get_popup_title($nid){
    if(red_copiar_is_copiar_activado()){
        return '<b>'.compartir_documentos_get_popup_title($nid).'</b>';
    }
    return '';
}
function red_copiar_get_enviar_mail_message($node){
    $node_title=red_copiar_get_exportar_rss_node_title($node);
    $result='<p><i>Exporting <b>@node_title</b></i><p>';
    $result.='<p><i>Please indicate a valid email</i></p>';
    $result=t($result,array('@node_title'=>$node_title));
    return $result;
}
function red_copiar_get_cancel_link($url_cancel,$with_icono=0,$is_popup=1){
    $js=hontza_solr_add_help_close_window_js();
    //return $js.l(my_get_icono_action('delete',$title),'hontza_solr/help_close_window',array('html'=>true,'attributes'=>array('id'=>'id_a_close_window','class'=>'jqmClose','title'=>$title,'alt'=>$title,'onclick'=>"self.close();")));
    $label=t('Cancel');
    $options=array();
    if($is_popup){
        //$options['attributes']=array('id'=>'id_a_close_window','class'=>'jqmClose','title'=>$title,'alt'=>$title,'onclick'=>"this.close();");
        $options['attributes']=array('id'=>'id_a_close_window','class'=>'jqmClose','title'=>$title,'alt'=>$title);        
        if($with_icono){
            $label=my_get_icono_action('delete',$title);
            $options['html']=true;
        }
    }
    $result=$js.l($label,$url_cancel,$options);    
    return $result;
}
function red_copiar_get_document_type($node,$document_type_in='document'){
    $result=$document_type_in;
    if(in_array($node->type,array('wiki'))){
        return t($node->type);
        //return t('Document/Wiki');
    }else if($node->type=='my_report'){
        return t('report');
        //return t('Document/Wiki');
    }else if(in_array($node->type,array('canal_de_yql','canal_de_supercanal'))){
        return t('channel');
    }else if(in_array($node->type,array('canal_busqueda'))){
        return t('channel/saved search');    
    }else if($node->type=='item'){
        //return t('news');
        return t('content');
    }else if($node->type=='noticia'){
        //return t('news');
        return t('content');
    }else if($node->type=='estrategia'){
        return t('strategy');
    }else if($node->type=='user'){
        return t('user');
    }   
    return t($result);
}
function red_copiar_validar_usuario_mail_network_para_compartir($mail){
    if(module_exists('red_compartir')){
        if(red_validar_usuario_mail_network_para_compartir($mail)){
            return 1;
        }
    }
    return 0;
}
function red_copiar_get_email_message_no_network($mail,$compartir_servidor_url,$node,$document_type_in,$message_text=''){
    //if(in_array($node->type,array('item','noticia'))){
        return red_copiar_get_email_message_item_no_network($mail,$compartir_servidor_url,$node,$document_type_in,$message_text);
    //}
}
function red_copiar_get_email_message_item_no_network($mail,$compartir_servidor_url,$node,$document_type_in,$message_text=''){
    global $user;
    $html=array();
    //$document_type=red_copiar_get_document_type($node,$document_type_in);
    $node_type_label='Document/Wiki';
    $link_of=$node_type_label;
    $tab='Collaboration';
    $import_label='document';
    $node_title=red_copiar_get_exportar_rss_node_title($node);
    if(in_array($node->type,array('item','noticia'))){
        //$node_type_label='news';
        $node_type_label='content';
        $link_of='News';
        $tab='Monitoring';
        $import_label=$link_of;
    }else if(in_array($node->type,array('canal_de_supercanal','canal_de_yql','canal_busqueda'))){
        $node_type_label='channel/saved search';
        $link_of='Channel/Saved search';
        $tab='Monitoring';
        $import_label='RSS';
    }
    $html[]='<p>Hi !mail_to,</p>';
    $html[]='<p>You are receiving this email because !userid_origen would like to share this '.$node_type_label.' with you.</p>';
    $html[]='<p><u>Message</u>: !message_text<p>';
    $html[]='<p>Link of '.$link_of.': <a href="!link">!link</a></p>';
    $html[]='<p>Please execute the following steps</p>';
    $html[]='<p>1.- Login to your Hontza platform</p>';
    $html[]='<p>2.- Select the group where you would like to import %node_title</p>';
    $html[]='<p>3.- Go to '.$tab.' Tab,</p>';
    $html[]='<p>4.- Click on <u><b>Import '.$import_label.'</b></u> and paste the link</p>';
    //$html[]='<p>Thank you for using Hontza Network!</p>';
    //$html[]='<p>Hontza Team</p>';
    $params=array('!mail_to'=>$mail,'!userid_origen'=>$user->mail,'!link'=>$compartir_servidor_url,'%node_title'=>$node_title,'!message_text'=>$message_text);        
    $html=t(implode('',$html),$params);
    return $html;
}
function red_copiar_get_enviar_mail_message_user($node,$is_validar_usuario_network,$mail_to){
    $node_title=red_copiar_get_exportar_rss_node_title($node);
    if($is_validar_usuario_network){
        $message_user=t('"%mail_to" Is a member of Hontza Network. A message has been sent with a link for a direct import of "%node_title"',array('%mail_to'=>$mail_to,'%node_title'=>$node_title));
    }else{
        $message_user=t('"%mail_to" Is not a member the Hontza Network. A message has been sent explaining the steps to import of "%node_title"',array('%mail_to'=>$mail_to,'%node_title'=>$node_title));
    }
    return $message_user;
}
function red_copiar_importar_enviar_mail_from($mail_from,$node_title='',$node=''){
    global $user;
    if(!empty($mail_from)){
        $mail_to=base64_decode($mail_from);
        //simulando
        //$mail_to='bulegoa@netkam.com';
        //$html='Tu documento compartido ha sido importado';
        $html=t('"!node_title" has been successfully imported by "!email destinatario"',array('!node_title'=>$node_title,'!email destinatario'=>$user->mail));
        $subject=$html;
        /*echo print_r($node,1);
        exit();*/
        $icono_documento=red_copiar_get_icono_documento($node,0);
        $html=$icono_documento.' '.$html;
        red_copiar_send_mail($mail_to,$subject,$html,'mimemail','');
        //red_copiar_send_mail('bulegoa@netkam.com',$subject,$html,'mimemail','');
    }
}
function red_copiar_add_importar_noticia_link(&$html){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_add_importar_noticia_link($html);        
    }
}
function red_copiar_item_node_form_alter(&$form,&$form_state, $form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_item_node_form_alter($form,$form_state, $form_id);
    }
}
function red_copiar_noticia_node_form_alter(&$form,&$form_state, $form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_noticia_node_form_alter($form,$form_state, $form_id);
    }
}
function red_copiar_get_popup_destination($param='&'){
    $destination='';
    /*if(isset($_REQUEST['my_current_destination']) && !empty($_REQUEST['my_current_destination'])){
        $destination=$param.'destination='.$_REQUEST['destination'];        
    }else*/ 
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $destination=$param.'destination='.$_REQUEST['destination'];
    }else{    
        $destination=$param.drupal_get_destination();
    }
    return $destination;
}
function red_copiar_is_grupo_conectado(){
    if(hontza_is_sareko_id_red()){
        if(hontza_is_red_hoja()){
            $my_grupo=og_get_group_context();
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                if(red_compartir_grupo_is_grupo_red_alerta($my_grupo->nid)){
                    return 1;
                }
            }    
        }
    }
    return 0;
}
function red_copiar_get_mail_firma(){
    $html=array();
    $texto='<p>Thank you for using Hontza Network!</p>';
    $texto.='<p>Hontza Team</p>';
    $html[]=t($texto);
    return implode('',$html);
}
function red_copiar_get_icono_documento($node,$is_link=1){
    $html=array();
    $icono='canal24';
    if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        $icono='canal24';
    }else if(in_array($node->type,array('canal_busqueda'))){
        $icono='save_current_search24';        
    }else if(in_array($node->type,array('wiki'))){
        $icono='wiki24';
    }else if(in_array($node->type,array('my_report'))){
        $icono='boletin_personalizado24';
    }else if(in_array($node->type,array('item','noticia'))){
        $icono='item24';
    }else if(in_array($node->type,array('estrategia'))){
        $icono='estrategia24';
    }else if(in_array($node->type,array('supercanal'))){
        $icono='fuente24';
    }else if(in_array($node->type,array('proyecto'))){
        $icono='proyecto24';
    }else if(in_array($node->type,array('user'))){
        $icono='user24';
    }   
    $icono_documento=my_get_icono_action($icono,'');
    if($is_link){
        $html[]='<a href="@link">'.$icono_documento.'</a>';
    }else{
        $html[]=$icono_documento;
    }
    return implode('',$html);
}
function red_copiar_add_compartir_usuario_link(&$html,$uid,$destination){
    if(red_copiar_is_copiar_activado()){
        //$html[]=l(my_get_icono_action('copiar_nodo',t('User exportation')),'compartir_documentos_usuario/copiar_usuario/'.$uid.'/enviar_mail',array('query'=>$destination,'html'=>true));
        compartir_documentos_usuario_add_compartir_usuario_link($html,$uid,$destination);
    }
}
function red_copiar_is_importar_rss_user_news_tipo_fuente_selected($form_state,$tid,$term_noticia_usuario){
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        if($tid==$term_noticia_usuario->tid){
            return 1;
        }
    }
    return 0;
}
function red_copiar_add_undefined_category_checked($contenido,&$form,$indefinida_tid,$form_state){
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        if($contenido->tid==$indefinida_tid){
            $form['cat'][$contenido->tid]['#attributes']['checked']='checked';
        }            
    }    
}
function red_copiar_get_nombre_canal_importar_rss(&$form_state,$nombre_canal){
    $result=$nombre_canal;
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        $url_rss=red_copiar_get_canal_import_rss_by_arg('');
        $content=file_get_contents($url_rss);
        $canal=new stdClass();
        red_copiar_get_canal_title_importar_rss($canal,$content);
        if(isset($canal->compartir_documentos_from_canal_title)){
            $result=$canal->compartir_documentos_from_canal_title;
            $form_state['yql_obj']->compartir_documentos_from_canal_title;
        }        
    }
    return $result;
}
function red_copiar_get_canal_title_importar_rss(&$canal,$dxml){
    if(red_is_xml($dxml)){
           $data = simplexml_load_string($dxml);
           if(isset($data->channel) && isset($data->channel->title) && !empty($data->channel->title)){
            $canal->compartir_documentos_from_canal_title=$data->channel->title;
           } 
    }        
}
function red_copiar_get_exportar_rss_node_title($node){
    $node_title='';
    if($node->type=='user'){
        $my_user=$node;
        $node_title=$my_user->name;
    }else{
        if(isset($node->title)){
            $node_title=$node->title;
        }else{
            $node_title=$node->name;
        }
    }    
    if(empty($node_title)){
        $node_title=t('All');
    }
    return $node_title;
}
function red_copiar_get_noticia_usuario_validados_href($value){
    $result=$value;
    $sep='canal-usuarios';
    $pos=strpos($result,$sep);
    if($pos===FALSE){
        return $result;
    }
    $s=substr($result,$pos+strlen($sep));
    $pos_link=strpos($s,'"');
    if($pos_link===FALSE){
        return $result;
    }
    $link=substr($s,0,$pos_link);
    /*if(empty($link)){
        $link='/all';
    }*/
    $link.='/validados';
    $result=substr($result,0,$pos+strlen($sep)).$link.substr($s,$pos_link);
    return $result;
}
function red_copiar_get_wiki_import_links($node,$enlaces){
    $result=$enlaces;
    if(red_copiar_is_copiar_activado()){
        $result=compartir_documentos_get_wiki_import_links($node,$enlaces);
    }
    return $result;
}
function red_copiar_add_wiki_import_enlaces_html(&$html,$row,$is_view,$con_fecha){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_add_wiki_import_enlaces_html($html,$row,$is_view,$con_fecha);
    }
}
function red_copiar_get_enlaces_stars_td(){
    $result='<td style="white-space:nowrap;width:90px;">';
    return $result;
}
function red_copiar_estrategia_node_form_alter(&$form,&$form_state,$form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_estrategia_node_form_alter($form,$form_state,$form_id);
    }
}
function red_copiar_supercanal_node_form_alter(&$form,&$form_id,$nid){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_fuente_supercanal_node_form_alter($form,$form_id,$nid);
    }
}
function red_copiar_get_title_imported_class($node_in='',$title_in=''){
    if(red_copiar_is_copiar_activado()){
        return compartir_documentos_get_title_imported_class($node_in,$title_in);
    }
    return '';
}
function red_copiar_add_field_is_imported(&$node,$is_compartir_documentos){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_add_field_is_imported($node,$is_compartir_documentos);
    }
}
function red_copiar_get_fuente_title_color($result_in,$row){
    $result=$result_in;
    if(red_copiar_is_copiar_activado()){
        $result=compartir_documentos_fuente_get_fuente_title_color($result,$row);
    }
    return $result;
}
function red_copiar_canal_node_form_alter(&$form,&$form_state,$form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_canal_node_form_alter($form,$form_state,$form_id);
    }
    if(isset($form['field_is_usuario_exportado'])){
        unset($form['field_is_usuario_exportado']);
    }
}
function red_copiar_get_compartir_estrategia_link($node){
    $result='';
    if(red_copiar_is_copiar_activado()){
        return compartir_documentos_get_estrategia_link($node);
    }                                                
    return $result;
}
function red_copiar_proyecto_node_form_alter(&$form,&$form_state,$form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_proyecto_node_form_alter($form,$form_state,$form_id);
    }
}
function red_copiar_my_report_node_form_alter(&$form,&$form_state,$form_id){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_my_report_node_form_alter($form,$form_state,$form_id);
    }
}
function red_copiar_is_canal_usuario_exportado_save($source,$xml){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_is_canal_usuario_exportado_save($source,$xml);
    }
}
function red_copiar_is_canal_usuario_exportado_add(&$canal,$data){
    if(red_copiar_is_copiar_activado()){
        compartir_documentos_is_canal_usuario_exportado_add($canal,$data);
    }
}
function red_copiar_is_canal_usuario_exportado_add_img($canal_nid,$result_in){
    $result=$result_in;
    if(red_copiar_is_copiar_activado()){
        $result=compartir_documentos_is_canal_usuario_exportado_add_img($canal_nid,$result);
    }
    return $result;
}
function red_copiar_is_yql_wizard_responsable_uid_required($form_state){
    if(isset($form_state['yql_obj']) && isset($form_state['yql_obj']->mail_from) && !empty($form_state['yql_obj']->mail_from)){
        return 0;
    }
    /*if(isset($_REQUEST['rss_url']) && !empty($_REQUEST['rss_url'])){
        return 0;
    }
    if(isset($_REQUEST['mail_from']) && !empty($_REQUEST['mail_from'])){
        return 0;
    }*/
    return 1;
}