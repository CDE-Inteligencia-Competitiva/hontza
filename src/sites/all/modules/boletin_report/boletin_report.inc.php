<?php
function boletin_report_is_sin_categoria($node_type){
    if(is_numeric($node_type)){
        return 0;
    }
    if(in_array($node_type,array('debate','wiki','my_report'))){
        return 1;
    }
    return 0;
}
function boletin_report_get_sin_categoria($nid){
    $result=array();
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        if(boletin_report_is_sin_categoria($node->type)){
            $result[0]=$node->type;
        }
    }
    return $result;
}
function boletin_report_get_term_name($tid){
    if(boletin_report_is_sin_categoria($tid)){
        $term_name=boletin_report_get_sin_categoria_name($tid);
    }else{
        //$term_name=taxonomy_get_term_name_by_language($tid);
        $term_name=taxonomy_get_term_name_by_language($tid,'',1);
    }
    return $term_name;
}
function boletin_report_get_sin_categoria_name($node_type){
    $info_array=boletin_report_define_sin_categoria_info_array();
    if(isset($info_array[$node_type]) && !empty($info_array[$node_type])){
        return $info_array[$node_type];
    }
    return '';
}
function boletin_report_define_sin_categoria_info_array(){
    $result=array();
    $result['debate']=t('Discussions');
    $result['wiki']=t('Collaborations');
    $result['my_report']=t('Reports');
    return $result;
}
function boletin_report_is_apply_limite_resumen($node,$current_type){
    //intelsat-2015
    //se ha comentado esto
    /*if($current_type=='categorias_report' && boletin_report_is_sin_categoria($node->type)){
        return 0;
    }*/
    return 1;
}
function boletin_report_get_enlaces_html($node){
    if(boletin_report_is_con_enlaces($node->type)){
       $enlaces_derecha_html=boletin_report_get_enlaces_derecha_html($node,0,1);
       if(!empty($enlaces_derecha_html)){
            $html=array();
            $html[]='<tr>';
            $html[]='<td>';
            $html[]='<table class="mail_table" style="width:100%;border:0px;">';
            $html[]='<tr><th><b>'.t('Links').'</b></th></tr>';
            $html[]='<tr>';
            $html[]='<td>'.$enlaces_derecha_html.'</td>';
            $html[]='</tr>';
            $html[]='</table>';
            $html[]='</td>';
            $html[]='</tr>';
            return implode('',$html);
       }     
    }
    return '';
}
function boletin_report_is_con_enlaces($node_type){
    if(in_array($node_type,array('debate','wiki'))){
        return 1;
    }
    return 0;
}
function boletin_report_get_enlaces_derecha_html($node,$is_view=0,$con_fecha=0){
    //gemini-2014
    if(isset($node->nid) && !empty($node->nid)){
    //    
        $enlaces=array();    
        if($node->type=='debate'){
            $debate_nid=$node->nid;
            $enlazar_debate_array=hontza_get_all_enlazar_debate_array($debate_nid,'');
            $enlaces=$enlazar_debate_array;
        }else if($node->type=='wiki'){
            $wiki_nid=$node->nid;
            $enlazar_wiki_array=hontza_get_all_enlazar_wiki_array($wiki_nid,'');
            $enlaces=$enlazar_wiki_array;
            //intelsat-2016
            $enlaces=red_copiar_get_wiki_import_links($node,$enlaces);
        }else if($node->type=='idea'){
            $idea_nid=$node->nid;
            $enlazar_idea_array=idea_get_all_enlazar_array($idea_nid,'');
            $enlaces=$enlazar_idea_array;        
        }
        //
        if(!empty($enlaces)){
            $html=array();
            if($is_view){
                $html[]='<table>';            
            }else{    
                $html[]='<table class="enlaces_mail_table">';
            }
            foreach($enlaces as $i=>$row){
                //intelsat-2016
                if(isset($row->is_wiki_import) && !empty($row->is_wiki_import)){
                    red_copiar_add_wiki_import_enlaces_html($html,$row,$is_view,$con_fecha);
                    continue;
                }
                $nid=$row->item_nid;
                $my_node=node_load($nid);
                if(isset($my_node->nid) && !empty($my_node->nid)){
                    $url=hontza_get_item_url_enlace($item_nid,$my_node);
                    $html[]='<tr>';
                    //AVISO::::$is_view==1 es cuando no es boletin_report o alerta al editar en este caso tambien es is_view
                    if($node->type=='debate'){
                        if($is_view && user_access('Enlazar debate')){
                            $html[]='<td>';
                            //intelsat-2015
                            if(!hontza_solr_search_is_usuario_lector()){
                            //    
                                $html[]=l(my_get_icono_action('delete', t('Delete discussion link')),'node/'.$nid.'/unlink_debate/'.$node->nid,array('html'=>true,'query'=>'destination=node/'.$node->nid));
                            }
                            $html[]='</td>';                    
                        }
                    }else if($node->type=='wiki'){
                        if($is_view && user_access('Enlazar wiki')){
                            $html[]='<td>';
                             //intelsat-2015
                            if(!hontza_solr_search_is_usuario_lector()){
                            //   
                                $html[]=l(my_get_icono_action('delete', t('Delete collaboration link')),'node/'.$nid.'/unlink_wiki/'.$node->nid,array('html'=>true,'query'=>'destination=node/'.$node->nid));
                            }
                            $html[]='</td>';                    
                        }
                    }else if($node->type=='idea'){
                        if($is_view && user_access('Enlazar idea')){
                            $html[]='<td>';
                            //intelsat-2015
                            if(!hontza_solr_search_is_usuario_lector()){
                                $html[]=l(my_get_icono_action('delete', t('Delete idea link')),'node/'.$nid.'/unlink_idea/'.$node->nid,array('html'=>true,'query'=>'destination=node/'.$node->nid));
                            }                            
                            $html[]='</td>';                    
                        }
                    }
                    if($con_fecha){
                        $html[]='<td>';
                        //$html[]=date('d/m/Y H:i',$node->created);
                        $html[]=date('d/m/Y',$my_node->created);
                        $html[]='</td>';
                    }
                    //intelsat-2016
                    $html[]=red_copiar_get_enlaces_stars_td();
                    $my_node->votingapi_cache_node_average_value=boletin_report_get_votingapi_cache_node_average_value($my_node->nid);
                    $html[]=my_get_stars_idea($my_node);
                    $html[]='</td>';    
                    //print $node->content['fivestar_widget']['#value']                
                    $html[]='<td>';
                    $html[]=l($my_node->title,$url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
                    $html[]='</td>';
                    $html[]='</tr>';
                }   
            }
            $html[]='</table>';
            return implode('',$html);
        }
    }
    return '';
}
function boletin_report_edit_user_access($grupo_nid){
    if(user_access('edit customised bulletin') && boletin_report_group_access($grupo_nid)){
        return 1;
    }
    return 0;
}
function boletin_report_my_access($grupo_nid='',$with_admin_grupo=1){
    /*if(!boletin_report_is_admin()){
        drupal_access_denied();
        exit();
    }
    if(!empty($grupo_nid)){
        if(!boletin_report_is_permiso_editar($grupo_nid)){
            drupal_access_denied();
            exit();
        }
    }*/
    if(!boletin_report_group_access($grupo_nid)){
         drupal_access_denied();
         exit();
    }
    //gemini-2014
    if($with_admin_grupo){
        boletin_report_admin_grupo_access($grupo_nid);
    }    
}
function boletin_report_group_access($grupo_nid){
    if(!empty($grupo_nid)){
        if(!in_user_groups($grupo_nid)){
            return 0;
        }
    }
    return 1;
}
function boletin_report_define_url_reload($r,$purl=''){
    global $base_url;
    if(boletin_report_is_editable_by_row($r)){
        if(boletin_report_hay_report_text_sin_enviar($r)){
            $nid=boletin_report_get_editable_nid($r);
            //return 'boletin_report/'.$nid.'/reload';
            return $base_url.'/'.$purl.'/boletin_report/'.$nid.'/reload';
        }
    }
    return '';
}
function boletin_report_reload_form(){
   boletin_report_no_group_selected_denied(); 
   boletin_report_my_access('',0);      
        $form['my_nid']=array(
            '#type'=>'hidden',
            '#default_value' =>arg(1),
        );
    //
    $form['reload_msg']=array(
        '#value'=>t('This action cannot be undone.').'<BR>',
    );

    $form['boletin_report_reload_btn_submit']=array(
  '#type' => 'submit',
  '#value' => t('Reload'),
);
    
  
$my_destination='boletin_report/list';
    

$form['boletin_report_reload_volver']=array(
  '#value' => l(t('Cancel'),$my_destination),  

);

    return $form;
}
function boletin_report_reload_form_submit(&$form, &$form_state){
    $values=$form_state['values'];    
    if(isset($values['my_nid']) && !empty($values['my_nid']) && is_numeric($values['my_nid'])){
        $nid=$values['my_nid'];
        boletin_report_delete_bulletin_text_node($nid);
        node_delete($nid);        
    }    
}
function boletin_report_delete_bulletin_text_node($nid){
    db_query('DELETE FROM {boletin_report_array_edit} WHERE nid=%d',$nid);    
}
function boletin_report_set_sending_value_string($is_sended){
    if($is_sended){
        return t('Sent');
    }
    return t('Pending');
}
function boletin_report_set_edition_type_value_string($is_edit,$is_archive=0){
    if($is_edit){
        //if($is_archive){
            return t('Manual');
        /*}else{
            return t('Editable');
        }*/    
    }
    return t('Automatic');
}
function boletin_report_add_reload_btn_js($nid){
    $js='$(document).ready(function()
    {
        $("#edit-reload-btn").click(function(){
            window.location.href="boletin_report/'.$nid.'/reload?destination=boletin_report/list";
            return false;
        });
    });';        
    drupal_add_js($js,'inline');        
}
function boletin_report_unset_buttons($name_array,&$form){
    if(!empty($name_array)){
        foreach($name_array as $i=>$name){
            if(isset($form['buttons'][$name]) && !empty($form['buttons'][$name])){
                unset($form['buttons'][$name]);
            }
        }
    }
}
function boletin_report_is_content_editado($bulletin_text_nid,$is_edit_content){
    if(!empty($bulletin_text_nid) || $is_edit_content){
        return 1;
    }
    return 0;
}
function boletin_report_get_sended_array($br){
    $html=array();
    $items=array();
    $noticias_usuario=array();
    $reports=array();
    $debates=array();
    $wikis=array();
    $sended_array=array(); 
    //
    
                if($br->is_todos_items){
                    $items=boletin_report_get_items($br);
                    $sended_array['items']=$items;                                       
                }
                //
                if($br->is_todos_noticias_usuario){
                    $noticias_usuario=boletin_report_get_items($br,'noticia','noticia_report');
                    $sended_array['noticias_usuario']=$noticias_usuario;                                      
                }
                if($br->is_todos_reports){
                    $reports=boletin_report_get_items($br,'my_report','node_report');
                    $sended_array['reports']=$reports;                    
                }                
                if($br->is_todos_debates){
                    $debates=boletin_report_get_items($br,'debate','debate_report');
                    $sended_array['debates']=$debates;                    
                }
                if($br->is_todos_wikis){
                    $wikis=boletin_report_get_items($br,'wiki','wiki_report');
                    $sended_array['wikis']=$wikis;                    
                }
    return $sended_array;                
}
function boletin_report_download_html_callback(){
    $id=arg(1);
    $boletin_report_array_edit_id=arg(3);
    $filename='bulletin_'.$id.'_'.$boletin_report_array_edit_id.'.html';
    header('Content-type: text/xml');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    boletin_report_get_content_html(1);
}
function boletin_report_get_content_html($is_download=0,$is_print_exit=1,$id_in='',$boletin_report_array_edit_id_in=''){
    $content='';
    if(!empty($id_in)){
        $id=$id_in;
    }else{
        $id=arg(1);
    }
    if(!empty($boletin_report_array_edit_id_in)){
        $boletin_report_array_edit_id=$boletin_report_array_edit_id_in;        
    }else{
        $boletin_report_array_edit_id=arg(3);
    }
    $bulletin_text_nid=0;
    $text_row='';
    $is_edit_content=0;
    if(!empty($boletin_report_array_edit_id)){
        //$text_row=boletin_report_get_edit_text_row('',$boletin_report_array_edit_id);
        $text_row=boletin_report_get_edit_text_row_by_id($boletin_report_array_edit_id);
        $bulletin_text_nid=$text_row->nid;
        $is_edit_content=1;        
    }
    $content=boletin_report_get_content($id,$bulletin_text_nid,$text_row,$is_edit_content);
    //intelsat-2015
    //if(red_despacho_is_activado()){
    if(red_dashboard_is_despacho_no_dashboard()){    
        $content=red_despacho_boletin_report_get_download_content($content,$is_download);
    }
    if($is_edit_content && $is_print_exit){
        $content=alerta_add_css($content,$is_download);
        //intelsat-2015
        $content=boletin_report_inc_fix_https($content);
        print $content;
        exit();
    }
    $is_pdf=boletin_report_pdf_is_pdf();
    if($is_pdf){
        $content=alerta_add_css($content,$is_download);
        //if(red_despacho_is_activado()){
        if(red_dashboard_is_despacho_no_dashboard()){
            //$content=str_replace('charset=windows-1252','charset=UTF-8',$content);
            $content=red_despacho_boletin_report_fix_pdf($content);
            //print $content;exit();
        }    
    }
    //intelsat-2015
    if(!red_despacho_is_activado()){
        $content=boletin_report_inc_fix_https($content);
    }
    return $content;
}
function boletin_report_forward_callback(){
    $is_download=0;
    $is_print_exit=0;
    $content=boletin_report_get_content_html($is_download,$is_print_exit);
    //intelsat-2015
    $is_alerta_publico=hontza_canal_rss_is_alerta_publico();
    if($is_alerta_publico && hontza_is_user_anonimo()){
        $html=drupal_get_form('publico_alerta_boletin_report_forward_form');
    }else{    
        $html=drupal_get_form('boletin_report_forward_form');
    }
    $html.=red_despacho_boletin_report_get_boletin_report_forward_content_view_web($content);    
    return $html;
}
function boletin_report_forward_form(){
    $form=array();
    $id=arg(1);
    $boletin_report_array_edit_id=arg(3);
    $form['boletin_report_array_id']=array(
        '#type'=>'hidden',
        '#default_value'=>$id,
    );
    $form['boletin_report_array_edit_id']=array(
        '#type'=>'hidden',
        '#default_value'=>$boletin_report_array_edit_id,
    );
    $form['forward_receptores_fs']['email_externos'] = array(
          '#type' => 'textarea',
          '#title' => t('External recipients'),
          //'#default_value' => '',
          '#description'=>t('Please include the email of recipients separated by commas')." (".t("Please verify your email list, It won't be checked the validity of emails").")",  
          '#required' => FALSE
          );
    //intelsat-2016
    boletin_report_mcapi_add_boletin_report_forward_form_fields($id,$form);
    
    $form['forward_btn']=array(
        '#type'=>'submit',
        '#name'=>'forward_btn',
        '#default_value'=>t('Forward Bulletin'),
    );
    $url_cancel='boletin_report/'.$id.'/historico';
    //intelsat-2015
    $is_alerta_publico=hontza_canal_rss_is_alerta_publico();
    if($is_alerta_publico && hontza_is_user_anonimo()){
        $url_cancel='publico_'.$url_cancel;
    }
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),$url_cancel)
    );
    return $form;
}
function boletin_report_forward_form_submit($form, &$form_state){
    global $user;
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button']) && $form_state['clicked_button']['#name']=='forward_btn'){        
        //
        $values=$form_state['values'];
        $boletin_report_array_id=$values['boletin_report_array_id'];
        $boletin_report_array_edit_id=$values['boletin_report_array_edit_id'];
        $email_externos=$values['email_externos'];
        //intelsat-2016
        $mailchimp_list_id='';
        if(isset($values['mailchimp_list_id']) && !empty($values['mailchimp_list_id'])){
            $mailchimp_list_id=$values['mailchimp_list_id'];
        }
        $mailchimp_template_id='';
        if(isset($values['mailchimp_template_id']) && !empty($values['mailchimp_template_id'])){
            $mailchimp_template_id=$values['mailchimp_template_id'];
        }
        //boletin_report_forward_loop($boletin_report_array_id,$boletin_report_array_edit_id,$email_externos);
        boletin_report_forward_loop($boletin_report_array_id,$boletin_report_array_edit_id,$email_externos,$mailchimp_list_id,$mailchimp_template_id);
        $uid=$user->uid;
        $fecha_forward=date('Y-m-d H:i:s');
        db_query('INSERT INTO {boletin_report_array_forward}(boletin_report_array_id,boletin_report_array_edit_id,email_externos,uid,fecha_forward) VALUES(%d,%d,"%s",%d,"%s")',$boletin_report_array_id,$boletin_report_array_edit_id,$email_externos,$uid,$fecha_forward);
        //intelsat-2015
        $is_alerta_publico=hontza_canal_rss_is_alerta_publico();
        $url='boletin_report/'.$boletin_report_array_id.'/historico';
        if($is_alerta_publico && hontza_is_user_anonimo()){
            $url='publico_'.$url;
        }
        drupal_goto($url);        
    }
}
//intelsat-2016
//function boletin_report_forward_loop($id,$boletin_report_array_edit_id,$email_externos){
function boletin_report_forward_loop($id,$boletin_report_array_edit_id,$email_externos,$mailchimp_list_id='',$mailchimp_template_id=''){    
    $br=boletin_report_get_row($id);
    if(isset($br->id) && !empty($br->id)){
        $br_title=': '.$br->titulo;
        $subject=t('Customised Bulletin').$br_title;                
        $is_download=0;
        $is_print_exit=0;
        $content=boletin_report_get_content_html($is_download,$is_print_exit,$id,$boletin_report_array_edit_id);
        $mail_html=alerta_add_css($content,0);
        $user_mail_array=explode(',',$email_externos);
        //intelsat-2016
        $current_content='';
        //si los emails ahora van por lista de MailChimp seguramente esto viene vacio, por lo tanto poner un mail temporal para que entre en el foreach
        $user_mail_array=boletin_report_mcapi_get_forward_user_mail_array($user_mail_array);        
        if(count($user_mail_array)>0){
            $text_row=boletin_report_get_edit_text_row_by_id($boletin_report_array_edit_id);
            $bulletin_text_nid=$text_row->nid;
            foreach($user_mail_array as $i=>$user_mail){
                $is_activo=1;
                $current_content=$mail_html;
                //intelsat-2016
                if(boletin_report_mcapi_is_activado()){
                    boletin_report_mcapi_my_send_mail($user_mail,$subject, $current_content,$br->send_method,$is_activo,$bulletin_text_nid,1,$br);                
                }else{
                    my_send_mail($user_mail,$subject,$current_content,$br->send_method,$is_activo,$bulletin_text_nid);
                }
            }
        }
        //intelsat-2016
        $por_correo=1;
        if(!empty($mailchimp_list_id)){
            $br->mailchimp_list_id=$mailchimp_list_id;
            $br->mailchimp_template_id=$mailchimp_template_id;
            boletin_report_mcapi_my_send_mail_array($user_mail_array,$subject, $current_content,$br->send_method,$is_activo,$bulletin_text_nid,0,$br,$por_correo,1);            
        }
        //intelsat-2015
        drupal_set_message(t('The Bulletin %br_titulo has been forwarded to your email list',array('%br_titulo'=>$br->titulo)));                
    }     
}
function boletin_report_select_group_form(){
    $form=array();
    //$select_group_options=my_get_grupo_seguimiento_options(0);
    $select_group_options=boletin_report_get_select_group_options();
    if(empty($select_group_options)){
        drupal_access_denied();
        exit();
    }
    $form['grupo_nid'] = array(
  '#type' => 'select',
  '#title' => t('Group'),
  //'#default_value' =>'',
  '#options'=>$select_group_options,
  '#multiple'=>FALSE,
  //'#size'=>10,
  '#required' => TRUE
);
    $form['select_group_send_submit']=array(
  '#type' => 'submit',
  '#value' => t('Send'),
);
    
    $form['select_group_volver']=array(
  '#value' => l(t('Cancel'),'boletin_report/list'),  

);
    
    return $form;
}
function boletin_report_select_group_form_submit(&$form, &$form_state){
    global $base_url;
    $values=$form_state['values'];    
    if(isset($values['grupo_nid']) && !empty($values['grupo_nid'])){
        $grupo_nid=$values['grupo_nid'];
        $grupo=node_load($grupo_nid);
        if(isset($grupo->nid) && !empty($grupo->nid)){
            drupal_goto($base_url.'/'.$grupo->purl.'/boletin_report/add');        
        }
    }
    drupal_goto('boletin_report/list');
}
function boletin_report_get_grupo_purl($grupo_nid){
    $grupo=node_load($grupo_nid);
    if(isset($grupo->nid) && !empty($grupo->nid)){
        return $grupo->purl;
    }
    return '';
}
function boletin_report_save_noticia_boletines($sended_array,$text_row,$fecha_sended,$param,$boletin_report_array_today_id){
    global $user;
    if(!empty($sended_array)){
        foreach($sended_array as $item_type=>$item_array){
            if(!empty($item_array)){
                foreach($item_array as $i=>$row){
                    //$fecha_sended=date("Y-m-d H:i:s");
                    db_query('INSERT INTO {boletin_report_noticia_boletines}(nid,report_type,uid_sended,fecha_sended,boletin_report_array_id,boletin_report_array_today_id,boletin_report_array_edit_id) VALUES(%d,"%s",%d,"%s",%d,%d,%d)',$row->nid,$row->report_type,$user->uid,$fecha_sended,$text_row->boletin_report_array_id,$boletin_report_array_today_id,$text_row->id);
                }
            }
        }    
    }
}
function boletin_report_is_save_noticia_boletines_sareko_id(){
    /*if(hontza_is_sareko_id_red()){
        return 1;
    }
    return 0;*/
    return 1;
}
function boletin_report_noticia_boletines_link($node){
    if(boletin_report_is_noticia_en_boletines($node)){
        $label=t('Bulletins');
        $label='';
        $destination=boletin_report_get_node_destination($node);        
        return l($label,'node/'.$node->nid.'/noticia_boletines',array('query'=>$destination));
    }
    return '';
}
function boletin_report_noticia_boletines_callback(){
   //
   $output='';
   $my_limit=20;
   $headers=boletin_report_noticia_boletines_headers();
   $nid=arg(1);
   $noticia=node_load($nid);
   $output.=node_view($noticia);
   //
   $where=array();
   $where[]='1';
   $where[]='nb.nid='.$nid;   
   //
   $sql='SELECT * 
   FROM {boletin_report_noticia_boletines} nb 
   WHERE '.implode(' AND ',$where).'
   ORDER BY id ASC';
   $res=db_query($sql);
   $kont=0;
   while($r=db_fetch_object($res)){
      $node=node_load($r->nid);
      $report_row=boletin_report_get_row($r->boletin_report_array_id);
      $edit_row=boletin_report_get_edit_text_row_by_id($r->boletin_report_array_edit_id);
      $rows[$kont][0]=$report_row->titulo;
      $rows[$kont][1]=$r->fecha_sended;
      $rows[$kont][2]=boletin_report_set_edition_type_value_string($edit_row->is_edit,1);
      $rows[$kont][3]=boletin_report_get_acciones_noticia_boletines($r);
      $kont++;
   }
   $rows=my_set_estrategia_pager($rows, $my_limit);

   $output .='<div style="float:left;width:100%;">';
   
   if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
   }
   else {
    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
   }
   $url_volver='node/'.$nid;
   if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
       $url_volver=$_REQUEST['destination'];
   }
   $output.=l(t('Return'),$url_volver);
   $output .='</div>';
   //intelsat-2016
   drupal_set_title(t('List of bulletins'));
   //
   return $output;
}
function boletin_report_noticia_boletines_headers(){
    $headers=array();
    $headers[0]=array('data'=>t('Title'),'field'=>'title');
    //$headers[1]=array('data'=>t('Sending'),'field'=>'is_sended');
    $headers[1]=array('data'=>t('Date'),'field'=>'fecha_sended');
    $headers[2]=array('data'=>t('Edition'),'field'=>'is_edit');
    $headers[3]=t('Actions');
    return $headers;
}
function boletin_report_get_acciones_noticia_boletines($noticia_boletines_row){
    $destination='destination=node/'.$noticia_boletines_row->nid.'/noticia_boletines';
    $text_row=boletin_report_get_edit_text_row_by_id($noticia_boletines_row->boletin_report_array_edit_id);
    if(isset($text_row->id) && !empty($text_row->id)){
        return boletin_report_get_acciones_historico($text_row,$destination,1);
    }
    return '';
}
function boletin_report_is_noticia_en_boletines($node){
    $noticia_boletines_array=boletin_report_get_noticia_boletines_array($node->nid);
    if(count($noticia_boletines_array)>0){
        return 1;
    }
    return 0;
}
function boletin_report_get_noticia_boletines_array($nid=0){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    //
    $sql='SELECT * 
    FROM {boletin_report_noticia_boletines} nb 
    WHERE '.implode(' AND ',$where).'
    ORDER BY id ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function boletin_report_get_node_destination($node){
    $destination='';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $destination='destination='.$_REQUEST['destination'];
    }else{
        if(hontza_is_node_view()){
            $destination='destination=node/'.$node->nid;
        }else{
            $destination=drupal_get_destination();
        }
    }
    return $destination;
}
function boletin_report_debate_boletines_link($node){
    return boletin_report_noticia_boletines_link($node);
}
function boletin_report_wiki_boletines_link($node){
    return boletin_report_noticia_boletines_link($node);
}
function boletin_report_boletines_link($node){
    return boletin_report_noticia_boletines_link($node);
}
function boletin_report_set_img_src_absolutos($content_in){
    global $base_url;
    $content=$content_in;
    /*if(!user_access('root')){
        return $content;
    }*/
    $sep_array=array('<IMG','<img');
    foreach($sep_array as $k=>$sep){  
        //$sep='<IMG src="';
        $my_array=explode($sep,$content);
        if(count($my_array)>0){
            foreach($my_array as $i=>$my_value){                
                if($i>0){
                    $pos_src=strpos($my_value,'src=');
                    if($pos_src===FALSE){
                        //
                    }else{
                        $v=substr($my_value,$pos_src+strlen('src=')+1);
                        $pos=strpos($v,'"');
                        if($pos===FALSE){
                            //
                        }else{
                            $pos_base_url=strpos($url,$base_url);                                
                            if($pos_base_url===FALSE){                            
                                $url=substr($v,0,$pos);
                                $pos_system=strpos($url,'system/files');
                                if($pos_system===FALSE){
                                    //
                                }else{
                                    $url=url($base_url.'/'.$url,array('absolute'=>TRUE));
                                    $value=substr($my_value,0,$pos_src+strlen('src=')+1).$url.substr($v,$pos);
                                    $my_array[$i]=$value;
                                }
                            }
                        }
                    }
                }
            }
        }
        $content=implode($sep,$my_array);
    }    
    return $content;
}
function boletin_report_get_votingapi_cache_node_average_value($nid){
    $sql='SELECT votingapi_cache_node_average.value AS votingapi_cache_node_average_value
    FROM votingapi_cache votingapi_cache_node_average WHERE votingapi_cache_node_average.content_id='.$nid.' AND (votingapi_cache_node_average.content_type = "node" AND votingapi_cache_node_average.function = "average")';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row->votingapi_cache_node_average_value;
    }
    //
    return '';
}
function boletin_report_launch_callback(){
    return boletin_report_launch();
}
function boletin_report_get_select_group_options(){
    global $user;
    $select_group_options=my_get_grupo_seguimiento_options(0);
    if(is_super_admin()){
        return $select_group_options;
    }    
        if(!empty($select_group_options)){
            $result=array();        
            foreach($select_group_options as $grupo_nid=>$grupo_title){
                $grupo=node_load($grupo_nid);
                if(isset($grupo->field_admin_grupo_uid[0]['value']) && !empty($grupo->field_admin_grupo_uid[0]['value'])){
                    if($grupo->field_admin_grupo_uid[0]['value']==$user->uid){
                        $result[$grupo_nid]=$grupo_title;
                    }
                }
            }
            return $result;
        }
    
    return $select_group_options;
}
function boletin_report_admin_grupo_access($grupo_nid){
    if(!boletin_report_is_permiso_editar($grupo_nid)){
            drupal_access_denied();
            exit();
    }
}
function boletin_report_get_boletines_historicos_inicio(){
    //intelsat-2015
    $content=boletin_grupo_get_ultimo_boletin_grupo_content();
    //
    $content.=boletin_report_get_boletines_historicos_content();
    if(empty($content)){
        return '';
    }
    $help_block=help_popup_block(468297);
    $html='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-home-boletines-personalizados">';    
    //intelsat-2015
    //$title=t('Customised Bulletins');
    $title=t('Bulletins');    
    $icono=my_get_icono_action('boletin_personalizado_dashboard',$title,'boletin_personalizado_dashboard').'&nbsp;';
    //
    $html.='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
    $html.='<div class="content">';
    $html.=$content;
    $html.=hontza_inicio_view_all('boletin_report/list',t('View Customised Bulletins'));  
    $html.='</div></div>';
    return $html;
}
function boletin_report_get_boletines_historicos_content(){
    $boletin_report_historico_array=boletin_report_get_boletines_historicos_array();
    if(!empty($boletin_report_historico_array)){
        $html=array();
        $my_limit=variable_get('boletin_report_historico_inicio_limit',3);
        $boletin_report_historico_array=array_slice($boletin_report_historico_array,0,$my_limit);
        $num=count($boletin_report_historico_array);
        $kont=1;
        foreach($boletin_report_historico_array as $i=>$row){        
            $kont=$kont+1;
            $odd='even';
            if(($i % 2)==1){
                $odd='odd';
            }
            $last='';
            if($i<1){
                //$last=' views-row-first';
            }else if($i==($num-1)){
                $last=' views-row-last';
            }
            $html[]='<div class="views-row views-row-'.$kont.' views-row-'.$odd.$last.'">';
            $html[]='<div class="views-field-title">';
            $html[]='<span class="field-content">';
            $html[]=my_get_icono_action('boletin_personalizado_dashboard', t('Customised Bulletin')).l($row->node->title,'boletin_report/'.$row->boletin_report_array_id.'/my_web/'.$row->boletin_report_array_edit_id,array('attributes'=>array('target'=>'_blank')));
            $html[]='</span>';
            $html[]='</div>';
            $html[]='</div>';
        }
        return implode('',$html);
    }
    return '';
}
function boletin_report_get_boletines_historicos_array(){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='NOT (boletin_report_array.id IS NULL)';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='boletin_report_array.grupo_nid='.$my_grupo->nid;
    }else{
        return $result;
    }
    $sql='SELECT boletin_report_array_edit.nid,
    boletin_report_array_edit.boletin_report_array_id,
    boletin_report_array_edit.id AS boletin_report_array_edit_id
    FROM {boletin_report_array_edit}
    LEFT JOIN {boletin_report_array} ON boletin_report_array_edit.boletin_report_array_id=boletin_report_array.id
    WHERE '.implode(' AND ',$where).'
    ORDER BY boletin_report_array_edit.fecha_sended DESC';
    $res=db_query($sql);
    $kont++;
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $result[$kont]=$row;
            $result[$kont]->node=$node;
            $kont++;
        }
    }
    return $result;
}
function boletin_report_get_reports_content(){
    $html=array();
    $report_array=boletin_report_get_report_array(10);
    $class='';
    if(!hontza_solr_search_is_usuario_lector()){
        if(!empty($report_array)){
            $class=' create-separacion';
        }
        $html[]='<div class="views-summary views-summary-unformatted'.$class.'">';
        //intelsat-2015
        $html[]=red_funciones_get_create_link(t('Create Report'),'node/add/my-report');    
        $html[]='</div>';    
        //
    }    
    $html_list=array();
    if(!empty($report_array)){
        $num=count($report_array);
        $last=$num-1;
        foreach($report_array as $i=>$row){
            $first='none';
            $even='even';
            if(($i % 2)==0){
                $even='odd';
            }
            if($i>0){
                if($i==$last){
                    $first='last';
                }
            }else{
                $first='first';
            }
            if(in_array($first,array('first','last'))){
                $html_list[]='<div class="views-row views-row-1 views-row-'.$even.' views-row-'.$first.'">';
            }else{
                $html_list[]='<div class="views-row views-row-1 views-row-'.$even.'">';
            }
            $html_list[]='<div class="views-field-title">';
            $html_list[]='<span class="field-content">'.l($row->title,'node/'.$row->nid).'</span>';
            $html_list[]='</div>';
            $html_list[]='</div>';            
        }
        $html[]=implode('',$html_list);
    }
    //$html[]=l(t('View all'), 'boletin_report/report_view_list');
    //return implode('<BR>',$html);
    return implode('',$html);
}
function boletin_report_get_report_array($my_limit=''){
  $result=array();
  $where=array();
  $where[]='1';
  $where[]='n.type="my_report"'; 
  //$where[]='n.promote = 1'; 
  $where[]='n.status = 1';
  $my_grupo=og_get_group_context();
  if(isset($my_grupo->nid) && !empty($my_grupo->nid)){     
      $where[]='og_ancestry.group_nid='.$my_grupo->nid;
  }
  $sql='SELECT n.nid, n.sticky, n.created,n.title  
  FROM {node} n 
  LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
  WHERE '.implode(' AND ',$where).' ORDER BY n.created DESC';
  if(!empty($my_limit)){
      $sql.=' LIMIT 0,'.$my_limit;
  }
  $res=db_query($sql);
  while($row=db_fetch_object($res)){
      $result[]=$row;
  }
  return $result;
}
function boletin_report_create_menu_node_list(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $html[]='<li'.boletin_report_menu_node_list_class('').'>'.l(t('Last'),'boletin_report/report_view_list').'</li>';
    $html[]='<li'.boletin_report_menu_node_list_class('lo-mas-valorado').'>'.l(t('Top Rated'),'boletin_report/report_view_list/lo-mas-valorado').'</li>';
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    //
    $output=implode('',$html);
    return $output;
}
function boletin_report_menu_node_list_class($arg_type){
    $result=0;
    $param0=arg(0);
    if(!empty($param0) && $param0=='boletin_report'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='report_view_list'){
            $param2=arg(2);
            if(empty($arg_type) && empty($param2)){
                $result=1;
            }else{    
                if(!empty($param2) && $param2==$arg_type){
                    $result=1;
                }
            }    
        }
    }    
    if($result){
        return ' class="active"';
    }
    return '';
}
function boletin_report_save_historico($br,$my_message,$is_no_hay,$user,$content_in,$grupo,$subject,$is_boletin_report,$por_correo,$sended_array,$bulletin_text_nid,$text_row){
    $content=$content_in;
    if(is_user_invitado()){
        $respuesta_content=get_invitado_respuesta_content($br,$my_message,$is_no_hay,$user);
        $content=$respuesta_content;
    }
    // 
    if(!empty($content)){
        $content=add_introduccion_despedida_html($content,$grupo,$user->uid,$subject,'',$is_boletin_report);            
    }
    //
    if(empty($content) && !$por_correo){
        $content=get_content_by_my_message($content, $por_correo, $my_message,1);
    }
    $historico_nid=boletin_report_save_aviso_procesado($br, $por_correo, $my_message,$sended_array,$bulletin_text_nid,$text_row,$content);
    return $historico_nid;
}
function boletin_report_get_url_historico($historico_nid){
    global $base_url;
    $row=boletin_report_get_edit_text_row_by_nid('',$historico_nid);
    if(isset($row->id) && !empty($row->id)){
        $result=$base_url;
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $result.='/'.$my_grupo->purl;
        }
        $result.='/boletin_report/'.$row->boletin_report_array_id.'/my_web/'.$row->id;        
        return $result;        
    }
    return '';
}
//intelsat-2015
function boletin_report_bookmark_callback(){
    boletin_report_bookmark();
}
//intelsat-2015
function boletin_report_inc_is_boletin_report(){
    $param0=arg(0);
    if(!empty($param0)){    
        if(strcmp($param0,'boletin_report')==0){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function boletin_report_report_rss_pdf_link($node){
   $label='';
   $attributes=array('title'=>t('Pdf'),'alt'=>t('Pdf'),'target'=>'_blank');
   return l($label,'boletin_report/'.$node->nid.'/pdf_node',array('attributes'=>$attributes));
}
//intelsat-2015
function boletin_report_inc_get_boletin_personalizado_title_simbolo_img($title){
   $result=my_get_icono_action('boletin_personalizado32', $title,'boletin_personalizado32');
   return $result; 
}
//intelsat-2015
function boletin_report_inc_is_orden_content_activado(){
    return 1;
}
//intelsat-2015
function boletin_report_inc_set_orden_values($values,$orden_name){
    $result=0;
    if(isset($values[$orden_name]) && !empty($values[$orden_name]) && is_numeric($values[$orden_name])){
        $result=$values[$orden_name];
    }
    return $result;
}
//intelsat-2015
function boletin_report_inc_items_merge_orden($items,$noticias_usuario,$reports,$debates,$wikis,$br,&$orden_values_array){
    $orden_values_array=array();
    $result=array_merge($items,$noticias_usuario,$reports,$debates,$wikis);
    if(boletin_report_inc_is_orden_content_activado()){
        if(!boletin_report_inc_is_ordenes_empty($br)){
            $orden_array=array();
            $orden_array['items']=$items;
            $orden_array['noticias_usuario']=$noticias_usuario;
            $orden_array['reports']=$reports;
            $orden_array['debates']=$debates;
            $orden_array['wikis']=$wikis;
            //
            $orden_values_array=boletin_report_get_orden_values_array($br,array_keys($orden_array));
            if(!empty($orden_values_array)){
                //$result=array();
                $orden_values_array=array_keys($orden_values_array);
                /*foreach($orden_values_array as $i=>$field){
                    $result=array_merge($result,$orden_array[$field]);
                }*/
            }
        }
    }
    return $result;
}
//intelsat-2015
function boletin_report_inc_is_ordenes_empty($br){
    $contenido_fields=boletin_report_create_contenido_fields();
    if(!empty($contenido_fields)){    
        foreach($contenido_fields as $i=>$f){
            $orden_name=$f['name'].'_orden';
            if(isset($br->$orden_name) && !empty($br->$orden_name)){
                return 0;
            }
        }    
    }
    return 1;
}
//intelsat-2015
function boletin_report_get_orden_values_array($br,$orden_array){
    $result=array();
    if(!empty($orden_array)){
        foreach($orden_array as $i=>$field){
            $field_name='is_todos_'.$field.'_orden';
            $result[$field]=0;
            if(isset($br->$field_name) && !empty($br->$field_name)){
                $result[$field]=$br->$field_name;
            }
        }
    }
    asort($result);
    return $result;
}
//intelsat-2015
function boletin_report_inc_get_categorias_orden($categorias,$orden_values_array){
    $result=array();
    if(!empty($categorias) && !empty($orden_values_array)){
        foreach($orden_values_array as $i=>$value){
            foreach($categorias as $a=>$tid){
                if($tid=='my_report'){
                    if($value=='reports'){
                        if(!in_array($tid,$result)){
                            $result[]=$tid;
                        }    
                    }
                }else if($tid=='debate'){
                     if($value=='debates'){
                        if(!in_array($tid,$result)){ 
                            $result[]=$tid;
                        }    
                    }
                }else if($tid=='wiki'){
                    if($value=='wikis'){
                        if(!in_array($tid,$result)){ 
                            $result[]=$tid;
                        }    
                    }
                }else{
                    if(is_numeric($tid)){
                        if(in_array($value,array('items','noticias_usuario'))){
                            if(!in_array($tid,$result)){ 
                                $result[]=$tid;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    return $categorias;
}
//intelsat-2015
function boletin_report_inc_set_noticias_usuario_fields_by_items(&$result){
    $result->is_todos_noticias_usuario=$result->is_todos_items;
    $result->limite_todos_noticias_usuario=$result->limite_todos_items;
}
//intelsat-2015
function boletin_report_inc_set_noticias_usuario_fields_presave_by_items(&$c){
    $c['is_todos_noticias_usuario']=$c['is_todos_items'];
    $c['is_todos_noticias_usuario_orden']=$c['is_todos_items_orden'];
    $c['limite_todos_noticias_usuario']=$c['limite_todos_items'];
}
//intelsat-2015
function boletin_report_inc_define_orden_options(){
    $result=array();
    $result[1]=1;
    $result[2]=2;
    $result[3]=3;
    $result[4]=4;
    return $result;
}
//intelsat-2015
function boletin_report_inc_get_orden_value_default($field){
    $result=1;
    $f=str_replace('is_todos_','',$field);
    $orden_assoc=boletin_report_inc_define_orden_assoc();
    if(isset($orden_assoc[$f]) && !empty($orden_assoc[$f])){
        return $orden_assoc[$f];
    }
    return $result;
}
//intelsat-2015
function boletin_report_inc_define_orden_assoc(){
    $result=array();
    $result['items']=1;
    $result['debates']=2;
    $result['wikis']=3;
    $result['reports']=4;
    return $result;
}
//intelsat-2015  
function boletin_report_inc_get_title_of_series($nid){
    $boletin_report_array_edit_array=boletin_report_get_edit_text_array_by_nid('',$nid);
    if(count($boletin_report_array_edit_array)>0){    
        $row=boletin_report_get_row($boletin_report_array_edit_array[0]->boletin_report_array_id);
        if(isset($row->id) && !empty($row->id)){
            return $row->titulo;
        }
        //
    }
    return '';
}
//intelsat-2015
function boletin_report_inc_title_of_bulletin_automatico_form(&$form_state,$content_in){
    $content=$content_in;
    $boletin_report_array_id=arg(1);    
    //$_SESSION['boletin_report_inc_title_of_bulletin_automatico_form_content_'.$boletin_report_array_id]=$content;
    $session_id=session_id();
    $variable_field=boletin_report_inc_get_title_of_bulletin_automatico_form_variable_field($boletin_report_array_id,$session_id);
    variable_set($variable_field,$content);
    $content=base64_encode($content_in);
    $form=array();
    $form['my_id']=array(
        '#type'=>'hidden',
        '#default_value' =>$boletin_report_array_id,
    );
    $form['my_session_id']=array(
        '#type'=>'hidden',
        '#default_value' =>$session_id,
    );                
    $form['title_of_bulletin_fs']=array(
        '#type'=>'fieldset',
    );
    $title_of_bulletin=boletin_report_inc_get_title_of_bulletin_automatico($boletin_report_array_id);
    $form['title_of_bulletin_fs']['title_of_bulletin']=array(
        '#type'=>'textfield',
        '#title'=>t('Title and number of Bulletin'),
        '#default_value'=>$title_of_bulletin,
    );
    //intelsat-2016
    $is_is_save_boletin_report_automatico=0;
    if(boletin_report_inc_is_save_boletin_report_automatico()){
        $is_is_save_boletin_report_automatico=1;
        $form['content']=array(
        '#type'=>'hidden',
        '#default_value' =>$content,
        );
    }
    $form['title_of_bulletin_fs']['save_btn']=array(
        '#type'=>'submit',
        //intelsat-2016
        '#name'=>'save_btn',
        '#value'=>t('Save'),
    );
    //intelsat-2016
    if($is_is_save_boletin_report_automatico){
        $form['title_of_bulletin_fs']['reload_btn']=array(
        '#type'=>'submit',
        //intelsat-2016    
        '#name'=>'reload_btn',    
        '#value'=>t('Reload'),
        );
    }
    return $form;
}
//intelsat-2015
function boletin_report_inc_get_title_of_bulletin_automatico($id,$is_edit_content=0,$row_in=''){
    if(empty($id)){
        $row=$row_in;
    }else{
        $row=boletin_report_get_row($id);
    }
    if(!$is_edit_content){
        if(isset($row->titulo_boletin) && !empty($row->titulo_boletin)){
            return $row->titulo_boletin;
        }
    }    
    //
    if(isset($row->titulo) && !empty($row->titulo)){
        return $row->titulo;
    }
    return '';
}
//intelsat-2015
function boletin_report_inc_title_of_bulletin_automatico_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if(isset($form_state['values']) && !empty($form_state['values'])){
            $boletin_report_array_id=$form_state['values']['my_id'];  
            //$form_state['values']['content']=$_SESSION['boletin_report_inc_title_of_bulletin_automatico_form_content_'.$boletin_report_array_id];
            //$_SESSION['boletin_report_inc_title_of_bulletin_automatico_form_content_'.$boletin_report_array_id]='';                                      
            $session_id=$form_state['values']['my_session_id'];
            $variable_field=boletin_report_inc_get_title_of_bulletin_automatico_form_variable_field($boletin_report_array_id,$session_id);
            $form_state['values']['content']=variable_get($variable_field,'');            
            variable_del($variable_field);        
            if($form_state['clicked_button']['#name']=='save_btn'){           
                    $titulo_boletin=$form_state['values']['title_of_bulletin'];
                    boletin_report_inc_update_titulo_boletin($boletin_report_array_id,$titulo_boletin);
                    //intelsat-2016
                    if(boletin_report_inc_is_save_boletin_report_automatico()){
                        boletin_report_inc_save_boletin_report_automatico_editados($boletin_report_array_id,$form_state['values']['content'],$titulo_boletin);
                    }
            }else if($form_state['clicked_button']['#name']=='reload_btn'){
                boletin_report_inc_update_boletin_report_automatico_editados_reload($boletin_report_array_id,1);
            }
        }    
    }    
}
//intelsat-2015
function boletin_report_inc_update_titulo_boletin($boletin_report_array_id,$titulo_boletin){
    db_query('UPDATE {boletin_report_array} SET titulo_boletin="%s" WHERE id=%d',$titulo_boletin,$boletin_report_array_id);
}
//intelsat-2015
//function boletin_report_inc_get_previsualizacion_link($r,$purl){
function boletin_report_inc_get_previsualizacion_link($r,$purl,$is_alerta_publico=0){
    global $base_url;
    //intelsat-2015
    global $language;    
    $title=t('Preview');
    //intelsat-2015
    $url='boletin_report';
    if($is_alerta_publico){
        $url='publico_boletin_report';
    }
    $my_lang='';
    if($language->language!='en'){
        $my_lang='/'.$language->language;
    }
    //$url=$base_url.'/'.$purl.'/'.$url.'/'.$r->id.'/previsualizacion_boletin';
    $url=$base_url.$my_lang.'/'.$purl.'/'.$url.'/'.$r->id.'/previsualizacion_boletin';
    //intelsat-2015
    $attributes=array('title'=>$title,'alt'=>$title);
    if(red_despacho_is_activado()){
        $attributes['target']='_blank';
    }
    $html=l(my_get_icono_action('viewmag',$title),$url,array('absolute'=>true,'html'=>true,'query'=>drupal_get_destination(),'attributes'=>$attributes));     
    return $html;
}
//intelsat-2015
function boletin_report_inc_previsualizacion_boletin_callback(){
    drupal_set_title(t('Preview'));
    return boletin_report_launch(1);
}
//intelsat-2015
function boletin_report_launch($is_previsualizacion=0){
    $output='';
    if(!$is_previsualizacion){
        $output=drupal_get_form('boletin_report_launch_form');
    }
    $id=arg(1);
    $boletin_report_array_edit_id='';
    $r=boletin_report_get_row($id);
    if(boletin_report_is_editable_by_row($r)){
        if(boletin_report_hay_report_text_sin_enviar($r)){
            $nid=boletin_report_get_editable_nid($r);
            $text_row=boletin_report_get_edit_text_row($r->boletin_report_array_id);
            if(isset($text_row->id) && !empty($text_row->id)){
                $boletin_report_array_edit_id=$text_row->id;
            }
        }    
    }
    $content=boletin_report_get_content_html(0,0,'',$boletin_report_array_edit_id);
    if($is_previsualizacion){
       //if(red_despacho_is_activado()){
       if(red_dashboard_is_despacho_no_dashboard()){ 
           print $content;
           exit();
       }     
       $output.=l(t('Return'),'boletin_report/list',array('attributes'=>array('class'=>'back_left')));
    }else{
       //intelsat-2016
       $content=red_despacho_boletin_report_get_boletin_report_forward_content_view_web($content);        
    }
    $output.=$content;
    return $output;
}
//intelsat-2015
function boletin_report_inc_add_categorias_titulo($categorias){
    $result=array();
    if(!empty($categorias)){
        foreach($categorias as $i=>$tid){
            $term_array=taxonomy_get_parents_all($tid);
            if(!empty($term_array)){
                foreach($term_array as $i=>$term){
                    if(!in_array($term->tid,$result) && !in_array($term->tid,$categorias)){
                        $result[]=$term->tid;
                    }
                }
            }
            $result[]=$tid;
        }
    }
    return $result;
}
//intelsat-2015
function boletin_report_inc_categorias_titulo_activado(){
    return 1;
}
//intelsat-2015
function boletin_report_inc_fix_https($content){
    global $base_url;
    $result=$content;
    //intelsat-2016
    if(boletin_report_inc_is_fix_imagenes_sin_http()){
        $result=str_replace('src="//','src="http://',$result);
    }        
    $pos=strpos($base_url,'https:');
    if($pos===FALSE){
        return $result;
    }else{
        $s=str_replace('https:','http:',$base_url);
        $result=str_replace($s,$base_url,$result);
    }
    return $result;
}
//intelsat-2015
function boletin_report_inc_get_display_contents($field){
    $result='';
    if(red_despacho_is_activado()){
        if(!red_despacho_boletin_report_is_content_field_checked($field)){
            $result='display:none;';
            return $result;
        }
    }else{
        if(hontza_canal_rss_is_visualizador_activado()){
            if($field!='is_todos_items'){
                $result='display:none;';
            }
        }
    }    
    return $result;
}
//intelsat-2015
function boletin_report_inc_noticia_usuario_boletines_link($node){
    return boletin_report_noticia_boletines_link($node);
}
//intelsat-2015
function boletin_report_inc_get_rows_array_num($r){
    $result=0;
    if(!empty($r)){
        foreach($r as $i=>$value){
            if(is_numeric($i)){
                if($i>$result){
                    $result=$i;
                }
            }
        }
    }
    $result=$result+1;
    return $result;
}
//intelsat-2015
function boletin_report_inc_get_archive_url($r,$is_alerta_publico){
    global $language,$base_url;   
    $result='';
    $my_lang='';
    if($language->language!='en'){
        $my_lang='/'.$language->language;
    }
    $purl=boletin_report_get_grupo_purl($r->grupo_nid);      
    $url='boletin_report';
    if($is_alerta_publico){
        if(hontza_is_user_anonimo()){
            $url='publico_boletin_report';
        }
    }
    $result=$base_url.$my_lang.'/'.$purl.'/'.$url.'/'.$r->id.'/historico';
    return $result;
}
//intelsat-2015
function boletin_report_inc_is_boletin_report_my_web($konp='my_web',$is_only_konp=0){
    $param0=arg(0);
    if(!empty($param0) && $param0=='boletin_report'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2==$konp){
                if($is_only_konp){
                    return 1;
                }else{
                    $param3=arg(3);
                    if(!empty($param3) && is_numeric($param3)){
                        return 1;
                    }
                }
            }
        }
    }
    return 0;
}
//intelsat-2015
function boletin_report_inc_is_forward(){
    return boletin_report_inc_is_boletin_report_my_web('forward');
}
//intelsat-2015
function boletin_report_inc_is_previsualizacion_boletin(){
    return boletin_report_inc_is_boletin_report_my_web('previsualizacion_boletin',1);
}
//intelsat-2016
function boletin_report_unselect_report_bookmark_callback(){
    boletin_report_bookmark(0);
}
//intelsat-2016
function boletin_report_bookmark($is_select_boletin=1){
    $node_id_array=explode(',',arg(2));
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg($node_id_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);            
            if($is_select_boletin){
                boletin_report_insert_yes($nid);
                hontza_validar_con_accion($nid);
            }else{
                boletin_report_delete_node_row($nid);
            }
            red_solr_inc_update_node_seleccionado_boletin($node);
        }    
    }
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);    
    hontza_solr_funciones_redirect();
}
//intelsat-2016
function boletin_report_in_is_launch(){
    return boletin_report_inc_is_boletin_report_my_web('launch',1);
}
//intelsat-2016
function boletin_report_inc_is_save_boletin_report_automatico(){
    if(defined('_IS_GUARDAR_BOLETIN_REPORT_AUTOMATICO') && _IS_GUARDAR_BOLETIN_REPORT_AUTOMATICO==1){
        return 1;
    }
    return 0;
}
//intelsat-2016
function boletin_report_inc_save_boletin_report_automatico_editados($boletin_report_array_id,$content_in,$titulo_boletin){
    if(db_table_exists('boletin_report_automatico_editados')){
        //$content=base64_decode($content_in);
        $content=$content_in;
        $content=red_despacho_boletin_report_inc_replace_title_boletin_report_automatico_editados($content,$titulo_boletin);
        db_query('INSERT INTO {boletin_report_automatico_editados}(boletin_report_array_id,content) VALUES(%d,"%s")',$boletin_report_array_id,$content);
    }
}
//intelsat-2016
function boletin_report_inc_get_boletin_report_automatico_content($br,$content){
    $result=$content;
    if(boletin_report_inc_is_save_boletin_report_automatico()){
        if(isset($br->is_editable) && !empty($br->is_editable)){
            return $result;
        }else{
            //intelsat-2016
            //if(!red_despacho_is_activado()){
                $editados_content=boletin_report_inc_get_boletin_report_automatico_editados_content($br);
                if(!empty($editados_content)){
                    $result=$editados_content;
                }
            //}    
        }
    }
    return $result;
}
//intelsat-2016
function boletin_report_inc_get_boletin_report_automatico_editados_content($br){
    $result='';
    $boletin_report_automatico_editados_array=boletin_report_inc_get_boletin_report_automatico_editados_array($br->id);
    if(isset($boletin_report_automatico_editados_array[0])){
        $result=$boletin_report_automatico_editados_array[0]->content;
        //$result=base64_decode($result);        
    }
    return $result;
}
//intelsat-2016
function boletin_report_inc_get_boletin_report_automatico_editados_array($boletin_report_array_id){
    $result=array();
    $res=db_query('SELECT * FROM {boletin_report_automatico_editados} WHERE boletin_report_array_id=%d AND sended=0 ORDER BY id DESC',$boletin_report_array_id);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
//intelsat-2016
function boletin_report_inc_hay_novedades_boletin_report_automatico($br){
    if(boletin_report_inc_is_save_boletin_report_automatico()){
        if(isset($br->is_editable) && !empty($br->is_editable)){
            return 0;
        }else{
            $editados_content=boletin_report_inc_get_boletin_report_automatico_editados_content($br);
            if(!empty($editados_content)){
                return 1;
            }
        }
    }
    return 0;
}
//intelsat-2016
function boletin_report_inc_update_boletin_report_automatico_editados_sended($br,$por_correo){
    if(boletin_report_inc_is_save_boletin_report_automatico()){
        if(in_array($br->send_method,array('mail','mimemail')) && $por_correo){
            if(!(isset($br->is_editable) && !empty($br->is_editable))){
                $editados_content=boletin_report_inc_get_boletin_report_automatico_editados_content($br);
                if(!empty($editados_content)){
                    $fecha_sended=date('Y-m-d H:i:s');
                    db_query('UPDATE {boletin_report_automatico_editados} SET sended=1,fecha_sended="%s" WHERE boletin_report_array_id=%d AND sended=0',$fecha_sended,$br->id);
                }
            }    
        }
    }
    return 0;
}
//intelsat-2016
function boletin_report_inc_update_boletin_report_automatico_editados_reload($boletin_report_array_id,$is_reload){
    $fecha_reload=date('Y-m-d H:i:s');
    if($is_reload){        
        db_query('UPDATE {boletin_report_automatico_editados} SET sended=1,is_reload=%d,fecha_reload="%s" WHERE boletin_report_array_id=%d AND sended=0',$is_reload,$fecha_reload,$boletin_report_array_id);
    }else{
        db_query('UPDATE {boletin_report_automatico_editados} SET is_reload=%d WHERE boletin_report_array_id=%d AND sended=0',$is_reload,$boletin_report_array_id);    
    }          
}
//intelsat-2016
function boletin_report_inc_replace_title_boletin_report_automatico_editados($content,$titulo_boletin,$needle='<h3 class="title_boletin_report_automatico_editados">',$my_needle='</h3>'){
    $result=$content;
    //$needle='<h3 class="title_boletin_report_automatico_editados">';
    $pos=strpos($content,$needle);
    if($pos===FALSE){
        return $result; 
    }else{
        $s=substr($result,0,$pos);
        $my_string=substr($result,$pos+strlen($needle));
        //$my_needle='</h3>';
        $pos=strpos($my_string,$my_needle);
        if($pos===FALSE){
            return $result;
        }else{
            $my_string=substr($my_string,$pos+strlen($my_needle));
            $result=$s.$needle.$titulo_boletin.$my_needle.$my_string;
        }    
    }        
    return $result;        
}
//intelsat-2016
function boletin_report_inc_get_wiki_import_links($node,$enlaces){
    
}
//intelsat-2016
function boletin_report_inc_is_fix_imagenes_sin_http(){
    if(defined('_IS_FIX_IMAGENES_SIN_HTTP')){
        if(_IS_FIX_IMAGENES_SIN_HTTP==1){
            return 1;
        }
    }
    return 0;
}
//intelsat-2016
function boletin_report_inc_get_title_of_bulletin_automatico_form_variable_field($boletin_report_array_id,$session_id){
    $result='boletin_report_inc_title_of_bulletin_automatico_form_content_'.$boletin_report_array_id.'_'.$session_id;
    return $result;
}
//intelsat-2016
function boletin_report_inc_is_boletin_report_row($br){
    if(isset($br->is_boletin_report) && !empty($br->is_boletin_report) && $br->is_boletin_report==1){
        return 1;
    }
    return 0;
}
//intelsat-2016
function boletin_report_inc_is_show_etiquetas($br){
    if(boletin_report_inc_is_boletin_report_row($br)){
        return 0;
    }
    return 1;
}
//intelsat-2016      
function boletin_report_inc_add_tipo_link_form_field(&$form,$fieldset_name=''){
    $tipo_link=red_despacho_boletin_report_get_tipo_link_default_value($row);
    $tipo_link_form_field= array(
        '#type' => 'select',
        '#title' => t('Links'),
        '#options' => boletin_report_get_tipo_link_options(),
        '#default_value' =>$tipo_link,
        //'#required' => TRUE
    );
    if(!empty($fieldset_name)){
        $form[$fieldset_name]['tipo_link']=$tipo_link_form_field;        
    }else{
        $form['tipo_link']=$tipo_link_form_field;  
    }
}
//intelsat-2016 
function boletin_report_inc_is_tipo_link_row($bg,$param_alerta){
    if(!empty($bg) && isset($bg->is_boletin_report) && !empty($bg->is_boletin_report)){
        return 1;
    }
    if(isset($param_alerta->tipo_link)){
        return 1;
    }
    if(isset($bg->tipo_link)){
        return 1;
    }
    return 0;    
}
function boletin_report_inc_is_links_to_web($bg,$param_alerta){
    if(!empty($bg->tipo_link) && $bg->tipo_link==2){
        return 1;        
    }
    if(!empty($param_alerta->tipo_link) && $param_alerta->tipo_link==2){
        return 1;        
    }
    if(!empty($bg->tipo_link) && $bg->tipo_link==2){
        return 1;        
    }
    return 0;    
}
//intelsat-2016 
function boletin_report_inc_is_noticia_boletines(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='node'){
         $param1=arg(1);
         if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2=='noticia_boletines'){
                return 1;
            }    
         }   
    }
    return 0;
}    