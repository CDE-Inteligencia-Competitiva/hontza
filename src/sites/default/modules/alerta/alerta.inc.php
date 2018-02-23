<?php
function alerta_get_user_alerta($param_alerta){
    if(!empty($param_alerta)){
        if(isset($param_alerta->uid) && !empty($param_alerta->uid)){
            $user_alerta=user_load($param_alerta->uid);
            if(isset($user_alerta->uid) && !empty($user_alerta->uid)){                
                return $user_alerta;
            }
        }
    }
    return '';
}
function alerta_in_user_grupo($user_alerta,$node){
    if(!empty($user_alerta)){
        if(isset($user_alerta->uid) && !empty($user_alerta->uid)){
            $user_grupo_nid_array=array();
            if(isset($user_alerta->og_groups) && !empty($user_alerta->og_groups)){
                $user_grupo_nid_array=array_keys($user_alerta->og_groups);                  
            }
            //
            $node_grupo_nid_array=array();
            if(isset($node->og_groups) && !empty($node->og_groups)){
                $node_grupo_nid_array=array_keys($node->og_groups);
            }
            //
            if(!empty($user_grupo_nid_array) && !empty($node_grupo_nid_array)){
                if(!empty($node_grupo_nid_array)){
                   foreach($node_grupo_nid_array as $i=>$grupo_nid){
                       if(in_array($grupo_nid,$user_grupo_nid_array)){
                           return 1;
                       }
                   } 
                }
            }else{
                return 0;
            }
            return 0;
        }
    }
    return 1;
}
function alerta_variables_access(){
    global $user;
    if($user->uid==1){
        return 1;
    }
    /*
    //gemini-2014
    if(red_funciones_is_administrador_grupo()){
        return 1;
    }*/    
    return 0;
}
function alerta_variables_form(){
    $form=array();
    $form['my_menu']=array(
        '#value'=>create_menu_alerta_user(),
    );
    $is_alerta_show_user_photo=variable_get('is_alerta_show_user_photo',1);
    $form['is_alerta_show_user_photo']=array(
        '#title'=>t('Show user photo'),
        '#type'=>'checkbox',
    );
    if($is_alerta_show_user_photo){
        $form['is_alerta_show_user_photo']['#attributes']=array('checked'=>'checked');
    }
    $form['alerta_variables_save_btn']=array(
        '#type'=>'submit',
        '#name'=>'alerta_variables_save_btn',
        '#default_value'=>t('Save'),
    );
    return $form;
}
function alerta_variables_form_submit(&$form,&$form_state){
    if(isset($form_state['values']) && !empty($form_state['values'])){
        $is_alerta_show_user_photo=0;
        if(isset($form_state['values']['is_alerta_show_user_photo']) && !empty($form_state['values']['is_alerta_show_user_photo'])){
            $is_alerta_show_user_photo=1;
        }
        variable_set('is_alerta_show_user_photo',$is_alerta_show_user_photo);
    }
}
function alerta_is_alerta_show_user_photo($is_mail,$bg=''){
    if($is_mail){
        if(!empty($bg) && isset($bg->is_boletin_report) && !empty($bg->is_boletin_report)){
            $is_alerta_show_user_photo=variable_get('is_alerta_show_user_photo',1);
            if($is_alerta_show_user_photo){
                return 1;
            }
            return 0;
        }    
    }
    return 1;
}
function alerta_add_css($body,$is_download=0,$body_id_in='',$is_print=0){
    //intelsat-2015
    //if(!hontza_is_sareko_id('ITI')){
    if(!red_despacho_boletin_report_is_my_add_css()){
        return $body;
    }
    //}
    $body_id='';
    if(!empty($body_id_in)){
        $body_id=' id="'.$body_id_in.'"';
    }else{
        //intelsat-2015
        //intelsat-2016
        $body_id=' id="'.red_despacho_boletin_report_get_email_body_id().'"';
    }
    if($is_print){
        $body_id.=' onload="window.print()"';
    }
    $result='<html><head>'.my_add_css($is_download).'</head><body'.$body_id.'><div id="wrapper"><div id="container" class="layout-region"><div id="main"><div class="main-inner"><div id="content" class="clearfix" style="clear:both;">'.$body.'</div></div></div></div></div></body></html>';
    return $result;
}
function alerta_get_canales_que_filtro_nid_array($id,$uid=''){
    $is_canales_que_filtro=1;
    $options=get_alerta_user_canal_options(1,$id,$is_canales_que_filtro,$uid);
    $keys=array_keys($options);
    return $keys;
}
function alerta_get_param_uid($alerta_user_id){
        $param_uid=0;
        $alerta_user_list=get_alerta_user_list($alerta_user_id);
        if(count($alerta_user_list)>0){
            $my_param=$alerta_user_list[0];
            $param_uid=$my_param->uid;
            return $param_uid;
        }
        return $param_uid;
}
function alerta_order_nid_array_by_title($canal_nid_array){
    $result=array();
    if(!empty($canal_nid_array)){
        $rows=array();
        foreach($canal_nid_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $r['nid']=$nid;
                $r['title']=$node->title;
                $rows[]=$r;
            }
        }
        //
        $rows=array_ordenatu($rows,'title','asc',0);
        if(!empty($rows)){
            foreach($rows as $k=>$row){
                $result[]=$row['nid'];
            }
        }
    }
    return $result;
}
function alerta_print_canal_nid_validator($canal_nid_array){
    if(!empty($canal_nid_array)){
        foreach($canal_nid_array as $i=>$canal_nid){
            $node=node_load($canal_nid);
            if(isset($node->nid) && !empty($node->nid)){
                print '####'.$i.'=####='.$canal_nid.'####<BR>';
                print $node->field_responsable_uid[0]['uid'].'<BR>';
                print $node->field_responsable_uid2[0]['uid'].'<BR>';
            }
        }
    }
}
function alerta_update_is_canales_que_filtro($id,$is_canales_que_filtro){
    $sql='UPDATE alerta_user SET is_canales_que_filtro='.$is_canales_que_filtro.' WHERE id='.$id;
    db_query($sql);
}
function alerta_inc_etiquetas($node){
    $html=array();
    $html[]='<div class="item-categorias"'.hontza_item_categorias_style().'>';
    $html[]=hontza_todas_etiquetas_html($node);
    $html[]='</div>';
    return implode('',$html);
}
function alerta_fix_icon_src($result_in){
    global $base_url,$base_path;
    $result=$result_in;    
    //$find='/hontza3/sites/all/libraries/tinymce/jscripts/tiny_mce/plugins/emotions/img/';    
    $find='/sites/all/libraries/tinymce/jscripts/tiny_mce/plugins/emotions/img/';        
    $pos=strpos($result,$find);
    if($pos===FALSE){
        return $result;
    }else{
        $sep='<img';
        $my_array=explode($sep,$result);
        foreach($my_array as $i=>$value){
            $pos2=strpos($value,$find);
            if($pos2===FALSE){
                $my_array[$i]=$value;
            }else{
                $find3='src="';
                $pos3=strpos($value,$find3);
                if($pos3===FALSE){
                    //
                }else{
                    $s=substr($value,$pos3+strlen($find3));
                    $find4='"';
                    $pos4=strpos($s,$find4);
                    if($pos4===FALSE){
                        //
                    }else{
                        $src=substr($s,0,$pos4);
                        $pos5=strpos($src,$base_url);
                        if($pos5===FALSE){
                            if(!empty($base_path) && $base_path!='/'){
                                $src=str_replace($base_path,'',$src);
                                $src='/'.$src;
                            }                            
                            $src=$base_url.$src;
                            $v=' '.$find3.$src.substr($s,$pos4);
                            $my_array[$i]=$v;
                        }
                    }
                }
            }
        }
        $result=implode($sep,$my_array);
    }
    return $result;
}
function alerta_user_inicio_callback(){
    global $user;
    drupal_goto('alerta_user/'.$user->uid.'/my_list');
    exit();
}
function alerta_variables_access_callback(){
    if(alerta_variables_access()){
        return TRUE;
    }
    return FALSE;
}
function alerta_get_sidebar_left(){
    $html=array();
    $html[]='<a id="context-block-region-left" class="context-block-region"></a>';
    $html[]=alerta_get_alerts_block();
    $html[]=alerta_get_bulletins_block();
    $html[]=alerta_get_settings_block();
    return implode('',$html);
}
function alerta_get_alerts_block(){
    if(hontza_is_user_anonimo()){
        return '';
    }    
        $html=array();    
        $html[]='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-alerts-left">';
        //intelsat-2015
        $title=t('My Alerts');
        $icono=my_get_icono_action('alerta',$title,'alerta').'&nbsp;';
        //
        $help_block=help_popup_block(468304);
        $html[]='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
        $html[]='<div class="content">';
        $html[]=alerta_get_alerts_block_links();
        $html[]='</div>';
        $html[]='</div>';
        return implode('',$html);   
}     
function alerta_get_alerts_block_links(){
    global $user;
    $html=array();
    $html[]=l(t('My Alerts'),'alerta_user/'.$user->uid.'/my_list');
    if(is_user_invitado()){
        $html[]=l(t('Add Guest alert'),'alerta_user/add_alerta_invitado');        
    }else{
        //intelsat-2015
        $icono=my_get_icono_action('add_left',t('Add Alert by Channel')).'&nbsp;';
        $html[]=$icono.l(t('Add Alert by Channel'),'alerta_user/add_alerta_canal');
        //intelsat-2015
        $icono=my_get_icono_action('add_left',t('Add Alert by Category')).'&nbsp;';        
        $html[]=$icono.l(t('Add Alert by Category'),'alerta_user/add_alerta_categoria');
        //intelsat-2015
        $icono=my_get_icono_action('add_left',t('Add Alert by User')).'&nbsp;';
        $html[]=$icono.l(t('Add Alert by User'),'alerta_user/add_alerta_usuario');
        //intelsat-2015
        if(alerta_solr_alerta_busqueda_access()){
            $icono=my_get_icono_action('add_left',t('Add Alert by Search')).'&nbsp;';
            $html[]=$icono.l(t('Add Alert by Search'),'alerta_user/add_alerta_busqueda');        
        }
        //
    }
    return implode('<BR>',$html);
}
function alerta_get_bulletins_block(){
    if(hontza_is_user_anonimo()){
        return '';
    }
    $html=array();
    //intelsat-2015
    $html[]=boletin_grupo_get_boletin_grupo_block();
    //
    $html[]='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-bulletins-left">';
    //intelsat-2015
    $title=t('Customised Bulletins');
    $icono=my_get_icono_action('boletin_personalizado_dashboard',$title,'boletin_personalizado_dashboard').'&nbsp;';
    //
    $help_block=help_popup_block(468306);
    $html[]='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
    $html[]='<div class="content">';
    $html[]=alerta_get_bulletins_block_links();
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);                    
}
function alerta_get_bulletins_block_links(){
    $my_grupo=og_get_group_context();    
    $html=array();
    //$html[]=l(t('Group Bulletin'),'alerta_user/mis_boletines_grupo');
    if(user_access('access boletin report')){
        $html[]=l(t('Customised Bulletins'),'boletin_report/list');                        
    }
    $create_new_url='';
    $url='boletin_report/select_group';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){        
        $url='boletin_report/add';
        if(boletin_report_is_permiso_editar($my_grupo->nid)){
            //intelsat-2015
            $icono=my_get_icono_action('add_left',t('Add New Series')).'&nbsp;';
            $create_new_url.=$icono.l(t('Add New Series'),$url); 
        }    
    }else{
        $select_group_options=boletin_report_get_select_group_options();
        if(!empty($select_group_options)){
             //intelsat-2015
            $icono=my_get_icono_action('add_left',t('Add New Series')).'&nbsp;';
            $create_new_url.=$icono.l(t('Add New Series'),$url);
        }    
    }
    if(!empty($create_new_url)){
        $html[]=$create_new_url;
    }
    //intelsat-2015
    $icono=my_get_icono_action('boletin_historico_left',t('Archive')).'&nbsp;';
    $html[]=$icono.l(t('Archive'),'boletin_report/historico/todos');    
    return implode('<BR>',$html);
}
function alerta_get_settings_block(){
    $links_html=alerta_get_settings_block_links();
    if(empty($links_html)){
        return '';
    }
    $html=array();    
    $html[]='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-alerts-settings-left">';
    //intelsat-2015
    $title=t('Settings');
    $icono=my_get_icono_action('configuracion',$title,'configuracion').'&nbsp;';
    //
    $help_block=help_popup_block(468307);
    $html[]='<h3 class="title">'.$icono.$title.$help_block.'</h3>';
    $html[]='<div class="content">';
    $html[]=$links_html;
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);                    
}
function alerta_get_settings_block_links(){
    $html=array();
    if(alerta_variables_access()){
        $html[]=l(t('Alert Settings'),'alerta/variables');        
    }
    if(is_permiso_gestion_boletin_grupo('')){
        $html[]=l(t('Launch Mail Queue'),'alerta/my_execute_cron');
    }
    //intelsat-2016
    boletin_report_mcapi_add_alerta_settings_block_links($html);    
    return implode('<BR>',$html);
}
function alerta_get_img_alerta_personal_recibir($is_recibir){
    if($is_recibir){
        return my_get_icono_action('active', t('Enabled'));
    }
    return my_get_icono_action('no_active', t('Disabled'));
}
function alerta_inc_add_grupos_canales($row,&$form,$is_busqueda=0){
    global $user;
    //
    $nid_array=set_default_value_canales($row->canales);
    //
    $grupo_list=my_get_og_grupo_list($user->uid,1);
    if(count($grupo_list)>0){
        foreach($grupo_list as $i=>$row){
            $canales=hontza_get_canales_del_grupo($row->nid,$is_busqueda);
            //
            if(!empty($canales)){
                $key_fs='canal_fs_'.$row->nid;
                $form['canales_fs'][$key_fs]=array(
                    '#type'=>'fieldset',
                    '#title'=>$row->title,
                    '#collapsible'=>TRUE,
                );
            
                  foreach ($canales as $id => $canal) {
                      $pro=0;
                      $key='my_canal_'.$canal->nid;
                      //
                      $form['canales_fs'][$key_fs][$key] = array(
                        '#required' => TRUE,
                        '#type' => 'checkbox',
                        '#prefix' => '<div class=taxo'. $pro .'>',
                        '#suffix' => '</div>',
                        '#title' => $canal->title,
                        '#attributes'=>array('class'=>'my_canal_checkbox'),  
                      );
                      if(!empty($nid_array)){
                        if(in_array($canal->nid,$nid_array)){
                          $form['canales_fs'][$key_fs][$key]['#attributes']['checked']='checked';
                        }
                      } 
                  }
            }
        }
    }   
}
function alerta_inc_get_canales_checkbox_values($values){
    $result=array();
    $s='my_canal_';
    foreach($values as $f=>$v){
        $pos=strpos($f,$s);
        if($pos===FALSE){
            continue;
        }else{
            $canal_nid=str_replace($s,'',$f);
            if(is_numeric($canal_nid)){
                $canal_nid=(int) $canal_nid;
                if(!empty($v)){
                    $result[$canal_nid]=$v;
                }    
            }
        }
    }
    return $result;
}
function alerta_inc_add_canales_checkbox_js(){
    $js='$(document).ready(function()
    {
        $("#edit-is-todos-canales").change(function(){
            var my_selected=$(this).attr("checked");
            if(my_selected){
                alerta_inc_set_selected_channels_that_i_filter(false);
                alerta_inc_set_selected_all_my_canal_checkbox(false);
            }
        });
        $("#edit-is-canales-que-filtro").change(function(){
            var my_selected=$(this).attr("checked");
            if(my_selected){
                alerta_inc_set_selected_all_channels(false);
                alerta_inc_set_selected_all_my_canal_checkbox(false);
            }
        });
        $(".my_canal_checkbox").change(function(){
            var my_selected=$(this).attr("checked");
            if(my_selected){
                alerta_inc_set_selected_all_channels(false);
                alerta_inc_set_selected_channels_that_i_filter(false);
            }
        });
        function alerta_inc_set_selected_channels_that_i_filter(my_value){
            $("#edit-is-canales-que-filtro").attr("checked",my_value);
        }
        function alerta_inc_set_selected_all_channels(my_value){
            $("#edit-is-todos-canales").attr("checked",my_value);
        }
        function alerta_inc_set_selected_all_my_canal_checkbox(my_value){
            $(".my_canal_checkbox").each(function(){
                $(this).attr("checked",my_value);
            });
        }
    });';

    drupal_add_js($js,'inline');
}
function alerta_inc_add_grupos_usuarios($row,&$form){
    global $user;
    //
    $uid_array=$row->usuarios;
    //
    $grupo_list=my_get_og_grupo_list($user->uid,1);
    if(count($grupo_list)>0){
        foreach($grupo_list as $i=>$row){
            $key_fs='usuarios_fs_'.$row->nid;
            $form['usuarios_fs'][$key_fs]=array(
                '#type'=>'fieldset',
                '#title'=>$row->title,
                '#collapsible'=>TRUE,
            );
            $usuarios=hontza_get_usuarios_grupo($row->nid);
            //
            if(!empty($usuarios)){
                  foreach ($usuarios as $id => $my_user) {
                      $pro=0;
                      $key='my_usuario_'.$my_user->uid.'_'.$row->nid;
                      //
                      $form['usuarios_fs'][$key_fs][$key] = array(
                        '#required' => TRUE,
                        '#type' => 'checkbox',
                        '#prefix' => '<div class=taxo'. $pro .'>',
                        '#suffix' => '</div>',
                        '#title' => $my_user->name,
                        '#attributes'=>array('class'=>'my_usuario_checkbox'),  
                      );
                      if(!empty($uid_array)){
                        if(in_array($my_user->uid,$uid_array)){
                          $form['usuarios_fs'][$key_fs][$key]['#attributes']['checked']='checked';
                        }
                      } 
                  }
            }
        }
    }
}
function alerta_inc_get_usuarios_checkbox_values($values){
    $result=array();
    $s='my_usuario_';
    foreach($values as $f=>$v){
        $pos=strpos($f,$s);
        if($pos===FALSE){
            continue;
        }else{
            $uid=str_replace($s,'',$f);
            $my_array=explode('_',$uid);
            $uid=$my_array[0];
            if(is_numeric($uid)){
                $uid=(int) $uid;
                if(!empty($v)){
                    $result[$uid]=$v;
                }    
            }
        }
    }
    return $result;
}
function alerta_inc_add_usuarios_checkbox_js(){
    $js='$(document).ready(function()
    {
        $("#edit-is-todos-usuarios").change(function(){
            var my_selected=$(this).attr("checked");
            if(my_selected){
                alerta_inc_set_selected_all_my_usuario_checkbox(false);
            }
        });
        $(".my_usuario_checkbox").change(function(){
            var my_selected=$(this).attr("checked");
            if(my_selected){
                alerta_inc_set_selected_all_usuarios(false);
                alerta_inc_set_selected_mi_usuario($(this).attr("id"),true);
            }else{
                alerta_inc_set_selected_mi_usuario($(this).attr("id"),false);
            }    
        });
        function alerta_inc_set_selected_all_usuarios(my_value){
            $("#edit-is-todos-usuarios").attr("checked",my_value);
        }
        function alerta_inc_set_selected_all_my_usuario_checkbox(my_value){
            $(".my_usuario_checkbox").each(function(){
                $(this).attr("checked",my_value);
            });
        }
        function alerta_inc_set_selected_mi_usuario(my_id_in,my_value){
            var uid=alerta_inc_get_uid_by_html_id(my_id_in);
            $(".my_usuario_checkbox").each(function(){
                konp=alerta_inc_get_uid_by_html_id($(this).attr("id"));
                if(uid==konp){
                    $(this).attr("checked",my_value);
                }
            });
        }
        function alerta_inc_get_uid_by_html_id(my_id){
            var result=my_id.replace("edit-my-usuario-","");
            var my_array=result.split("-");
            return my_array[0];
        }
    });';

    drupal_add_js($js,'inline');
}
function alerta_inc_get_fecha_para_dentro_del_correo(){
    $fecha=date('Y-m-d H:i');
    $result='<p><i>'.$fecha.'</i></p>';
    //$result='<div style="font-weight:bold;">'.$fecha.'</div>';
    return $result;
}
function alerta_inc_flame_strip_tags($html, $allowed_tags=array()) {
  $allowed_tags=array_map(strtolower,$allowed_tags);
  $rhtml=preg_replace_callback('/<\/?([^>\s]+)[^>]*>/i', function ($matches) use (&$allowed_tags) {       
    return in_array(strtolower($matches[1]),$allowed_tags)?$matches[0]:'';
  },$html);
  return $rhtml;
}
function alerta_inc_strip_tags($node_body){
    $result=strip_tags($node_body);
    if(empty($result)){
        $result=alerta_inc_flame_strip_tags($node_body); 
    }
    return $result;
}
//intelsat-2015
function alerta_inc_get_title_alerta_user_simbolo_img(){
   $title=t('Alert');
   $result=my_get_icono_action('alerta32', $title,'alerta32');
   return $result;  
}
//intelsat-2015
function alerta_inc_is_configuracion(){
   $param0=arg(0);
   if(!empty($param0) && $param0=='alerta'){
        $param1=arg(1);
        if(!empty($param1) && in_array($param1,array('variables','my_execute_cron'))){
            return 1;
        }
   }
   return 0;
}
//intelsat-2015
function alerta_inc_get_title_configuracion_simbolo_img($title){
   $result=my_get_icono_action('configuracion32', $title,'configuracion32');
   return $result;  
}
//intelsat-2015
//intelsat-2016
//function alerta_inc_get_frecuencia_label($value){
function alerta_inc_get_frecuencia_label($value,$is_manual=0){
   //intelsat-2016 
   if($is_manual){
    $result=get_frecuencia_options(1,$is_manual);   
   }else{
    $result=get_frecuencia_options();
   }
   if(isset($result[$value]) && !empty($result[$value])){
       return $result[$value];
   }
   return '';
}
//intelsat-2015
function alerta_inc_add_tipo_eventos_form_field(&$form,$row){
    $id='';
    if(isset($row->id) && !empty($row->id)){
        $id=$row->id;
    }
    $form['tipo_eventos_fs']=array(
       '#type'=>'fieldset',
       '#title'=>'<span class="fieldset-title-required" title="'.t('This field is required').'">*</span>'.t('Status'),
   );
   $form['tipo_eventos_fs']['is_evento_latest_news']=array(
       '#type'=>'checkbox',
       '#title'=>t('Latest news, not validated'),
   );
   if(isset($row->is_evento_latest_news) && !empty($row->is_evento_latest_news)){
       $form['tipo_eventos_fs']['is_evento_latest_news']['#attributes']=array('checked'=>'checked');
   }
   $form['tipo_eventos_fs']['is_evento_validated_news']=array(
       '#type'=>'checkbox',
       '#title'=>t('Validated news'),
   );
   if(empty($id) || (isset($row->is_evento_validated_news) && !empty($row->is_evento_validated_news))){
       $form['tipo_eventos_fs']['is_evento_validated_news']['#attributes']=array('checked'=>'checked');
   }
   $form['tipo_eventos_fs']['is_evento_rejected_news']=array(
       '#type'=>'checkbox',
       '#title'=>t('Rejected news'),
   );
   if(isset($row->is_evento_rejected_news) && !empty($row->is_evento_rejected_news)){
       $form['tipo_eventos_fs']['is_evento_rejected_news']['#attributes']=array('checked'=>'checked');
   }
   $form['tipo_eventos_fs']['is_evento_edited_news']=array(
       '#type'=>'checkbox',
       '#title'=>t('Edited news'),
   );
   if(empty($id) || (isset($row->is_evento_edited_news) && !empty($row->is_evento_edited_news))){
       $form['tipo_eventos_fs']['is_evento_edited_news']['#attributes']=array('checked'=>'checked');
   }
}
function alerta_inc_set_tipo_eventos_values(&$is_evento_latest_news,&$is_evento_validated_news,&$is_evento_rejected_news,&$is_evento_edited_news,$values){
   $is_evento_latest_news=0;
   if(isset($values['is_evento_latest_news']) && !empty($values['is_evento_latest_news'])){
       $is_evento_latest_news=$values['is_evento_latest_news'];
   }
   $is_evento_validated_news=0;
   if(isset($values['is_evento_validated_news']) && !empty($values['is_evento_validated_news'])){
       $is_evento_validated_news=$values['is_evento_validated_news'];
   }   
   $is_evento_rejected_news=0;
   if(isset($values['is_evento_rejected_news']) && !empty($values['is_evento_rejected_news'])){
       $is_evento_rejected_news=$values['is_evento_rejected_news'];
   }   
   $is_evento_edited_news=0; 
   if(isset($values['is_evento_edited_news']) && !empty($values['is_evento_edited_news'])){
       $is_evento_edited_news=$values['is_evento_edited_news'];
   }   
}
function alerta_inc_is_tipo_evento_activado($row,$is_array=0){
    $my_array=array('is_evento_latest_news','is_evento_validated_news','is_evento_rejected_news','is_evento_edited_news');
    //
    if(!empty($my_array)){
        foreach($my_array as $i=>$field){
            if($is_array){
                if(isset($row[$field]) && !empty($row[$field])){
                    return 1;
                }
            }else{
                if(isset($row->$field) && !empty($row->$field)){
                    return 1;
                }
            }    
        }
    }
    return 0;
}
function alerta_inc_get_where_tipo_evento($row){
   $result=array();
   if(isset($row->is_evento_latest_news) && !empty($row->is_evento_latest_news)){
       $result[]='"insert"';
   }
   if(isset($row->is_evento_validated_news) && !empty($row->is_evento_validated_news)){
       $result[]='"validado"';   
   }   
   if(isset($row->is_evento_rejected_news) && !empty($row->is_evento_rejected_news)){
       $result[]='"rechazado"';
   }   
   if(isset($row->is_evento_edited_news) && !empty($row->is_evento_edited_news)){
       $result[]='"update"';
       $result[]='"unflag"';
   }
   if(empty($result)){
       return '1';
   }
   $result='an.my_action IN('.implode(',',$result).')';
   return $result;
}
function alerta_inc_tipo_eventos_validate($is_bg,&$form,&$form_state){
   if(!alerta_inc_is_tipo_evento_activado($form_state['values'],1)){
       form_error($form['tipo_eventos_fs'],t('Status field is required'));
   }
}
//intelsat-2015
function alerta_inc_usuario_basico_access_denied(){
   if(hontza_canal_rss_is_usuario_basico()){
       drupal_access_denied();
       exit();
   } 
}
//intelsat-2015
function alerta_inc_get_alerta_usuario_array($uid,$canal_usuario_uid){
    /*$where=array();
    $where[]="1";
    $where[]="au.tipo='usuario'";
    $where[]="au.uid=".$uid;
    $todos_canal_nid_list=get_todos_canal_nid_list();
    //gemini-2014    
    $or_array=array();
    $or_array[]="(ac.canal_nid=".$nid.')';
    if(count($todos_canal_nid_list)>0){   
        $or_array[]="(au.is_todos_canales=1 AND ".$nid." IN(".implode(',',$todos_canal_nid_list)."))";
    }
    $canales_que_filtro=alerta_get_canales_que_filtro_nid_array('',$uid);
    if(count($canales_que_filtro)){
        $or_array[]="(au.is_canales_que_filtro=1 AND ".$nid." IN(".implode(',',$canales_que_filtro)."))";
    }
    $where[]='('.implode(' OR ',$or_array).')';
    //
    $sql="SELECT au.*
    FROM {alerta_user} au
    LEFT JOIN {alerta_user_canales} ac ON au.id=ac.alerta_user_id
    WHERE ".implode(" AND ",$where)."
    GROUP BY au.id
    ORDER BY au.id ASC";
    //
    //print $sql;
    $res=db_query($sql);*/
    $result=array();
    /*while($row=db_fetch_object($res)){
        $result[]=$row;
    }*/
    return $result;
}
function alerta_inc_add_js_select_alerta(){
    if(is_alerta_user()){
        my_add_active_trail_js('id_a_alertas');		        
    }
}
function alerta_inc_is_custom_boletin_css(){
    if(red_is_rojo()){
        return 1;
    }
    if(defined('_IS_CUSTOM_BOLETIN_CSS') && _IS_CUSTOM_BOLETIN_CSS==1){
        return 1;
    }
    return 0;
}
//intelsat-2016
function alerta_inc_getimagesize($src){
    $result='';
    $imagesize=getimagesize($src);
    if(isset($imagesize[1]) && !empty($imagesize[1])){
        $height=$imagesize[1];
        if($height>=60){
            $result=' height="60"';
        }
    }    
    return $result;
}
//intelsat-2016
function alerta_inc_add_tipo_link_form_field(&$form,$fieldset_name='',$row=''){
    if(alerta_inc_is_tipo_link()){
        boletin_report_inc_add_tipo_link_form_field($form,$fieldset_name,$row);
    }
}
//intelsat-2016
function alerta_inc_is_tipo_link(){
    if(db_column_exists('alerta_user','tipo_link')){
        if(db_column_exists('boletin_grupo_array','tipo_link')){
            return 1;
        }    
    }
    return 0;
}
//intelsat-2016
function alerta_inc_save_tipo_link($my_id,$values){
    if(alerta_inc_is_tipo_link()){
        if(isset($values['tipo_link']) && !empty($values['tipo_link'])){
            db_query('UPDATE {alerta_user} SET tipo_link=%d WHERE id=%d',$values['tipo_link'],$my_id);
        }
    }
}