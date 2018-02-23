<?php
function red_compartir_canal_hoja_form(){
    global $user;
    red_compartir_grupo_is_grupo_red_alerta_access_denied();
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $is_hound_canal=hontza_is_hound_canal('',$node);
    if($is_hound_canal){
       $msg=t('This channel can not be shared because it is a Hound Channel');
       return red_compartir_canal_form_with_msg($nid,$msg,$form); 
    }
    //        
    $existe=red_compartir_canal_existe_en_el_servidor($node);
    if(!empty($existe)){
        $msg='';
            if($existe==1){
                $msg=t('The url is already shared in the Server');        
            }else if($existe==2){
                $msg=t('The channel title already exists in the Server, please change it');        
            }
        
        /*$form['msg_existe']['#value']='<p>'.$msg.'</p>';
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'node/'.$nid),
        );
        return $form;*/
        return red_compartir_canal_form_with_msg($nid,$msg,$form);    
    }
    $node->sareko_id=_SAREKO_ID;    
    if(red_compartir_canal_is_compartible($node)){
        $form['#action']=red_compartir_define_url_guardar_canal_hoja_servidor();
        $form['#attributes']=array('target'=>'_blank');
        $node->user_local=red_compartir_prepare_user_enviar($user);
        $canal_enviar=red_compartir_prepare_canal_enviar($node);
        $form['canal_enviar']=array(
            '#type'=>'hidden',
            '#default_value'=>$canal_enviar,
        );    
        $form['canal_name']=array(
            '#value'=>'<p><b>'.$node->title.'</b></p>',
        );
        $form['share_btn']=array(
            '#type'=>'submit',
            '#default_value'=>t('Share'),
        );
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'node/'.$nid),
        );
        red_compartir_add_canal_hoja_js();
    }else{
        $form['my_message']['#value']='<p>no se puede compartir el canal porque su fuente no esta compartida</p>';
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'node/'.$nid),
        );
    }
    return $form;
}
function red_compartir_canal_is_compartible($node){
    if($node->type=='canal_de_supercanal'){
        $fuente=hontza_get_fuente($node);
        if(red_compartir_is_fuente_local_compartida($fuente)){
            return 1;
        }
        return 0;
    }else if($node->type=='canal_de_yql'){
        return 1;
    }
    return 0;
}
function red_compartir_is_fuente_local_compartida($fuente){
    $fuente_row=red_compartir_get_red_compartir_fuente_row($fuente);
    if(isset($fuente_row->id) && !empty($fuente_row->id)){
        return 1;
    }
    return 0;
}
function red_compartir_define_url_guardar_canal_hoja_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_canal_hoja',array('absolute'=>TRUE));
}
function red_compartir_prepare_canal_enviar(&$node){
    red_compartir_add_node_feeds($node);
    $node->votingapi_cache_row=red_compartir_get_estrellas_average_row($node->nid);
    if(in_array($node->type,array('canal_de_yql'))){
        $node->canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($node->vid, $node->nid);
        if(hontza_is_hound_canal($node->nid)){
            $node->canal_hound_parametros_row=hound_get_canal_hound_parametros_row($node->nid);
        }
    }
    //AVISO::::por ahora la taxonomia va vacía
    //red_compartir_unset_categorias_tematicas_by_node($node);
    $node->taxonomy=array();
    if(isset($node->field_responsable_uid[0]['uid'])){
        $node->field_responsable_uid[0]['uid']=1;
    }
    if(isset($node->field_responsable_uid2[0]['uid'])){
        $node->field_responsable_uid2[0]['uid']='';
    }
    $node->subdominio=red_compartir_grupo_get_subdominio();
    return base64_encode(serialize($node));
}
function red_compartir_add_canal_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_compartir_canal_hoja_ajax();
            //return false;
        });
        function call_red_compartir_canal_hoja_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var canal_enviar_val=$("#edit-canal-enviar").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_guardar_en_local_compartir_canal_hoja_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {canal_enviar:canal_enviar_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('red/canales/todas').'"
				}
			});                        
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_guardar_en_local_compartir_canal_hoja_enviado_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['canal_enviar']) && !empty($_POST['canal_enviar'])){
        $node=unserialize(base64_decode(($_POST['canal_enviar'])));
        red_compartir_save_red_compartir_canal($node);
    }
    print json_encode($result);
    exit();
} 
function red_compartir_save_red_compartir_canal($node){
    global $user;
    $row=red_compartir_get_red_compartir_canal_row($node);
    $nid=$node->nid;
    $vid=$node->vid;
    $uid=$user->uid;
    $group_nid=0;
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $group_nid=$my_grupo->nid;
    }
    $fecha=time();
    if(isset($row->id) && !empty($row->id)){
        db_query($sql=sprintf('UPDATE {red_compartir_canal} SET uid=%d,group_nid=%d,fecha=%d WHERE nid=%d AND vid=%d',$uid,$group_nid,$fecha,$nid,$vid));
    }else{
        db_query($sql=sprintf('INSERT INTO {red_compartir_canal}(nid,vid,uid,group_nid,fecha) VALUES(%d,%d,%d,%d,%d)',$nid,$vid,$uid,$group_nid,$fecha));
    }
    drupal_set_message(t('Channel %canal_title shared',array('%canal_title'=>$node->title)));
}
function red_compartir_get_red_compartir_canal_row($node){
    $red_compartir_canal_array=red_compartir_get_red_compartir_canal_array($node->vid,$node->nid);
    if(count($red_compartir_canal_array)>0){
        return $red_compartir_canal_array[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_get_red_compartir_canal_array($vid,$nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($vid)){
        $where[]='vid='.$vid;
    }
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    $sql='SELECT * FROM {red_compartir_canal} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_copiar_canales_servidor_form(){
    drupal_set_title(t('Download Channels'));    
    boletin_report_no_group_selected_denied();
    $form=array();
        $canales_list=red_compartir_get_canales_servidor_list();
        //intelsat-2015
        $prefix='<div class="n-opciones-item">';
        $form_return_btn=array(
            '#value'=>red_compartir_copiar_fuentes_servidor_volver_link(),
            '#prefix'=>$prefix,                             
        );
        //
        if(!empty($canales_list)){
            /*$form['copiar_canal']['#tree']=true;
            foreach($canales_list as $i=>$row){
                $canal=$row->node;
                $form['copiar_canal'][$row->nid]=array(
                    '#type'=>'checkbox',
                    '#title'=>$canal->title,
                );
            }*/
            $form['copiar_canal_table']=array(
                 '#value'=>red_compartir_canal_get_copiar_table_html($canales_list),
             ); 
             //intelsat-2015
             $form['return_btn']=$form_return_btn;
             $form['my_submit']=array(
                 //intelsat-2015
                 //'#type'=>'submit',
                 '#type'=>'image_button',
                 '#name'=>'my_submit',
                 '#value'=>t('Download'),
                 //'#prefix'=>'<div style="float:left;">',                 
                 '#suffix'=>'</div>',
                 '#src'=>red_compartir_fuente_descargar_path(),
                 );
             //
        }else{            
            $form['my_msg']=array(
                '#value'=>'<p>'.t('There are no contents').'</p>',
            );
            //intelsat-2015
            $form['return_btn']=$form_return_btn;
            $form['return_btn']['#suffix']='</div>';
            //
        }    
        /*$form['return_btn']=array(
            '#value'=>red_compartir_copiar_canales_servidor_volver_link(),
        );*/
    return $form;
}
function red_compartir_add_node_feeds(&$node){
    if(!(isset($node->feeds) && isset($node->feeds['FeedsHTTPFetcher']) && isset($node->feeds['FeedsHTTPFetcher']['source']))){
        $node->feeds=array();
        $node->feeds['FeedsHTTPFetcher']=array();        
    } 
    //
    $feeds_source=hontza_get_feeds_source($node->nid);
    if(isset($feeds_source->feed_nid) && !empty($feeds_source->feed_nid)){
        $node->feeds['FeedsHTTPFetcher']['source']=$feeds_source->source;        
    }
    $fuente_title=hontza_get_content_field_nombrefuente_canal_value($node->vid,$node->nid);
    if(!empty($fuente_title)){
        $node->field_nombrefuente_canal[0]['value']=$fuente_title;
    }    
}
function red_compartir_get_estrellas_average_row($nid){
    return hontza_get_avg_rating($nid);
}
function red_compartir_get_canales_servidor_list(){
    $result=array();
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/red_servidor_canales_get_contents';
    $filtro_get_contents=red_compartir_canales_set_copy_filtro_get_contents();    
    if(!empty($filtro_get_contents)){
        $url.='?red_alerta_filtro='.$filtro_get_contents;
    }
    /*if(user_access('root')){
        print $url;exit();
    }*/
    $content=file_get_contents($url);
    $content_array=unserialize($content);
    $_SESSION['user_autocomplete_fuentes']['user_autocomplete_options']=$content_array['user_autocomplete_options'];
    $canales_servidor=$content_array['canales_array'];
    if(count($canales_servidor)>0){
        foreach($canales_servidor as $i=>$row){
            //if($row->sareko_id!=_SAREKO_ID){
                $result[]=$row;
            //}
        }
    }    
    return $result;
}
function red_compartir_copiar_canales_servidor_volver_link(){
    return red_compartir_copiar_fuentes_servidor_volver_link(1);
}
function red_compartir_copiar_canales_servidor_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button']) && $form_state['clicked_button']['#name']=='my_submit'){
        /*$values=$form_state['values'];
        if(isset($values['copiar_canal']) && !empty($values['copiar_canal'])){
            $my_array=$values['copiar_canal'];*/
            $my_array=array();
            if(isset($_REQUEST['copiar_canal']) && !empty($_REQUEST['copiar_canal'])){
                $my_array=$_REQUEST['copiar_canal'];
            }
            if(!empty($my_array)){
                $fuentes_servidor=red_compartir_get_fuentes_servidor_list();
                $canales_servidor=red_compartir_get_canales_servidor_list();
                foreach($my_array as $nid=>$v){
                    if(!empty($v)){                       
                        if(!red_compartir_is_copiado_del_servidor_canal($nid)){
                            $canal_servidor=red_compartir_get_canal_servidor($nid,$canales_servidor);
                            if(isset($canal_servidor->nid) && !empty($canal_servidor->nid)){
                                red_compartir_save_fuente_del_canal_del_servidor($canal_servidor,$fuentes_servidor,$fuente);                            
                                red_compartir_save_canal_descargados($canal_servidor,$fuente,$new_canal);
                                drupal_set_message(t('Channel %canal_title downloaded',array('%canal_title'=>$new_canal->title)));
                            }    
                        }else{
                            drupal_set_message(t('The channel exists'));
                        }
                    }
                }
            }
        //}
    }
    //drupal_goto('fuentes-pipes/todas');
    drupal_goto('red_compartir/red_compartir_copiar_canales_servidor');
}
function red_compartir_is_copiado_del_servidor_canal($nid){
    $canal=red_compartir_get_canal_descargados_row($nid);
    if(isset($canal->id) && !empty($canal->id)){
        return 1;
    }
    return 0;
}
function red_compartir_get_canal_descargados_row($nid){
    $canal_array=red_compartir_get_canal_descargados_array($nid);
    if(count($canal_array)>0){
        return $canal_array[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_get_canal_descargados_array($nid=''){
    $where=array();
    $where[]='1';
    if(!empty($nid)){
        $where[]='servidor_nid='.$nid;
    }
    $sql='SELECT * FROM {red_compartir_canal_descargados} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_get_canal_servidor($nid,$canales_servidor){
    if(!empty($canales_servidor)){
        foreach($canales_servidor as $i=>$row){
           if($nid==$row->nid){
               return $row;
           }
        }
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_save_fuente_del_canal_del_servidor($canal_servidor,$fuentes_servidor,&$node_fuente){
    if(isset($canal_servidor->node)){
        $node=$canal_servidor->node;
        if(isset($node->nid) && !empty($node->nid) && $node->type=="canal_de_supercanal"){
            if(isset($node->field_nid_fuente_canal) && isset($node->field_nid_fuente_canal[0]) && isset($node->field_nid_fuente_canal[0]['value'])){
                 $fuente_nid=$node->field_nid_fuente_canal[0]['value'];
                 if(!red_compartir_is_copiado_del_servidor($fuente_nid)){
                    $fuente_servidor=red_compartir_get_fuente_servidor($fuente_nid,$fuentes_servidor);
                    red_compartir_save_fuente_descargados($fuente_servidor,$node_fuente);
                 }
            }
        }
    }
}
function red_compartir_save_canal_descargados($canal_servidor,$fuente,&$node){
    if(isset($canal_servidor->node)){
        $node=$canal_servidor->node;
        $node->field_nid_fuente_canal[0]['value']=$fuente->nid;                
        //
            $servidor_nid=$node->nid; 
            $servidor_vid=$node->vid; 
            $servidor_uid=$node->uid;
            $servidor_group_nid=$canal_servidor->group_nid;
            $fecha=time();
            $servidor_sareko_id=$canal_servidor->sareko_id;
            $node=red_compartir_local_preparar_canal($node,$grupo_local);
            node_save($node);
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_local->nid;
            red_compartir_save_canal_estrellas_average($node);
            red_compartir_save_canal_yql_parametros($node);
            red_compartir_save_canal_hound_parametros($node);
            $sql=sprintf('INSERT INTO {red_compartir_canal_descargados}(servidor_nid,servidor_vid,servidor_uid,servidor_group_nid,fecha,servidor_sareko_id,vid,nid,uid,group_nid) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d)',$servidor_nid,$servidor_vid,$servidor_uid,$servidor_group_nid,$fecha,$servidor_sareko_id,$vid,$nid,$uid,$group_nid);
            db_query($sql);
    }    
}
function red_compartir_local_preparar_canal($node,&$grupo_local){
    return red_compartir_local_preparar_fuente($node,$grupo_local);
}
function red_compartir_save_canal_estrellas_average($node){
    $content_id=$node->nid;
    $content_type='node';
    $value=0;
    if(isset($node->votingapi_cache_row->value)){
        $value=$node->votingapi_cache_row->value;
    }    
    $value_type='percent';
    $tag='vote';
    $function='average';
    $timestamp=time();
    if(isset($node->votingapi_cache_row->value)){
        $timestamp=$node->votingapi_cache_row->timestamp;
    }
    //
    $row=hontza_get_avg_rating($node->nid);
    if(isset($row->vote_cache_id) && !empty($row->vote_cache_id)){
        $res=db_query('UPDATE {votingapi_cache} SET content_id=%d,content_type="%s",value=%d,value_type="%s",tag="%s",function="%s",timestamp=%d WHERE vote_cache_id=%d',$content_id,$content_type,$value,$value_type,$tag,$function,$timestamp,$row->vote_cache_id);
    }else{
        $res=db_query('INSERT INTO {votingapi_cache}(content_id,content_type,value,value_type,tag,function,timestamp) VALUES(%d,"%s",%d,"%s","%s","%s",%d)',$content_id,$content_type,$value,$value_type,$tag,$function,$timestamp);
    }
}
function red_compartir_save_canal_yql_parametros($node){
    if($node->type=='canal_de_yql'){
        if(isset($node->canal_yql_parametros_row) && isset($node->canal_yql_parametros_row->nid) && !empty($node->canal_yql_parametros_row->nid)){
            $canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            if(isset($canal_yql_parametros_row->nid) && !empty($canal_yql_parametros_row->nid)){
                if($is_edit){
                    hontza_delete_canal_yql_parametros($node);
                    $is_insert=1;
                }
            }else{
                $is_insert=1;
            }
            //
            $vid=$node->vid;
            $nid=$node->nid;
            $todos='';
            $titulo='';
            $descripcion='';
            $no_titulo='';
            $no_descripcion='';
            $contiene='';
            $no_contiene='';
            $filtrosSI='';
            $filtrosNO='';
            $campo_contiene='';
            $campo_no_contiene='';
            $conjuncion='';
            $area='';
            //
            $fields=array('filtrosSI','filtrosNO','campo_contiene','campo_no_contiene','conjuncion'
            ,'todos','titulo','descripcion','no_titulo','no_descripcion','contiene','no_contiene','area');            
            //
            if(!empty($fields)){
                foreach($fields as $i=>$f){
                    if(isset($node->canal_yql_parametros_row->$f)){
                        ${$f}=$node->canal_yql_parametros_row->$f;
                    }    
                }
            }
            //
            if($is_insert){
                db_query($sql=sprintf('INSERT INTO {canal_yql_parametros}(vid,nid,todos,titulo,descripcion,no_titulo,no_descripcion,contiene,no_contiene,filtrosSI,filtrosNO,campo_contiene,campo_no_contiene,conjuncion,area) VALUES(%d,%d,"%s","%s","%s","%s","%s","%s","%s",%d,%d,%d,%d,%d,"%s")',$vid,$nid,$todos,$titulo,$descripcion,$no_titulo,$no_descripcion,$contiene,$no_contiene,$filtrosSI,$filtrosNO,$campo_contiene,$campo_no_contiene,$conjuncion,$area));
                //print $sql.'<BR>';
            }                    
        }
    }
}
function red_compartir_save_canal_hound_parametros(&$node){
    if($node->type=='canal_de_yql'){
        if(hontza_is_hound_canal($node->nid)){
            if(isset($node->canal_hound_parametros_row) && isset($node->canal_hound_parametros_row->nid) && !empty($node->canal_hound_parametros_row->nid)){
                $node->canal_hound_parametros=$node->canal_hound_parametros_row;
                hound_save_canal_hound_parametros($node);
            }
        }
    }
}
function red_compartir_canal_get_copiar_table_html($canal_list){
    $output='';
    //$output.=red_compartir_copiar_canales_servidor_volver_link();
    $sort='asc';
    $is_numeric=0;
    $field='canal_title';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }    
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Region')){
            $field='canal_region';
        }else if($order==t('Rating')){
            $is_numeric=1;
            $field='canal_rating';            
        }else if($order==t('User')){
            $field='user_local_name';            
        }else if($order==t('Downloads')){
            $field='numero_de_descargas';            
        }             
    }
    
    $canal_list=array_ordenatu($canal_list,$field,$sort,$is_numeric);
    
    $my_limit=100;
    
    $headers=array();
    $headers[0]='';
    $headers[1]=array('data'=>t('Description'),'field'=>'canal_title');
    $headers[2]=array('data'=>t('Region'),'field'=>'canal_region');		
    $headers[3]=array('data'=>t('Rating'),'field'=>'votingapi_cache_row');
    $headers[4]=array('data'=>t('Downloads'),'field'=>'numero_de_descargas');
    $headers[5]=array('data'=>t('User'),'field'=>'user_local_name');
    $kont=0;
    $rows=array();
    $_SESSION['red_compartir_copiar_canal_servidor']=array();
    if(!empty($canal_list)){
        foreach($canal_list as $i=>$r){
            if(isset($r->node)){
                $node=$r->node;
                $_SESSION['red_compartir_copiar_canal_servidor'][$r->id]=$r;
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=array();
                    $rows[$kont][0]='<div style="white-space:nowrap;float:right;">';
                    if($r->sareko_id==_SAREKO_ID){
                        $rows[$kont][0].='<div style="margin-top:-18px;">&nbsp;</div>';
                    }else{ 
                        $rows[$kont][0].='<input type="checkbox" id="copiar_canal_'.$r->nid.'" name="copiar_canal['.$r->nid.']" value="1"/>';
                    }
                    //intelsat-2015
                    //se ha coemntado esto
                    //$rows[$kont][0].='&nbsp;'.l(my_get_icono_action('viewmag', t('View')),'red_compartir/red_compartir_view_canal_servidor/'.$r->id,array('html'=>true,'query'=>drupal_get_destination()));                    
                    //
                    $rows[$kont][0].='</div>';
                    $rows[$kont][1]=red_compartir_canal_get_description($node,'red_compartir/red_compartir_view_canal_servidor/'.$r->id);
                    //$rows[$kont][2]=red_compartir_canal_get_type($node);
                    $rows[$kont][2]=  red_regiones_get_region_decode_value(red_compartir_canal_get_region($r));
                    //$rows[$kont][3]=$r->sareko_id_label;
                    $rows[$kont][3]=red_compartir_canal_get_avg_rating_by_node($node);
                    $rows[$kont][4]=$r->numero_de_descargas;
                    //$rows[$kont][5]=date('Y-m-d H:i',$r->fecha);
                    $rows[$kont][5]=$rows[$kont][5]=red_compartir_canal_get_user_name($r);
                    $kont++;
                }
            }
        }        
    }
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));           
    }
    else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function red_compartir_canal_get_type($node){
    if($node->type=='canal_de_supercanal'){
        return t('Source Channel');
    }else if($node->type=='canal_de_yql'){
        if(hontza_is_hound_canal('',$node)){
            return t('Hound');
        }
        return t('RSS Filter Channel');
    }
    return '';
}
function red_compartir_canal_get_avg_rating_by_node($node,$is_value=0){
    /*if(isset($node->votingapi_cache_row)){
        if(isset($node->votingapi_cache_row->value)){
            if($is_value){
                return $node->votingapi_cache_row->value;
            }else{
                return red_compartir_canal_create_stars_view($node->votingapi_cache_row->value);
            }    
        }
    }
    if($is_value){
        return 0;
    }else{
        return '';
    }*/
    return red_canal_get_avg_rating_by_node($node,$is_value);
}
function red_compartir_canal_create_stars_view($value){
    /*$v=(int) ($value/20);
    return my_create_stars_view($v);*/
    return red_canal_create_stars_view($value);
}
function red_compartir_copiar_canales_servidor_form_callback(){
    red_compartir_grupo_is_grupo_red_alerta_access_denied();
    $output=red_compartir_canales_copy_header().drupal_get_form('red_compartir_copiar_canales_servidor_form');
    return $output;
}
function red_compartir_canales_copy_header(){
    $html=array();
    $html[]=red_compartir_navegar_menu();
    $html[]=red_compartir_canales_copy_filtro();
    return implode('',$html);
}
function red_compartir_canales_copy_filtro(){
    my_add_buscar_js();
    return drupal_get_form('red_compartir_canales_copy_filtro_form');
}
function red_compartir_canales_copy_filtro_form(){
    $form=array();
    $fs_title=t('Search');
    if(!red_compartir_canales_copy_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //    
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['canal_title']=
        array('#type'=>'textfield',
        '#title'=>t('Title'),
        "#default_value"=>red_compartir_canales_copy_get_filter_value('canal_title'));
    //
    $form['file_buscar_fs']['canal_description']=
        array('#type'=>'textfield',
        '#title'=>t('Description'),
        "#default_value"=>red_compartir_canales_copy_get_filter_value('canal_description'));
    /*$form['file_buscar_fs']['canal_type']=
        array('#type'=>'select',
        '#title'=>t('Type'),
        '#options'=>red_compartir_canales_copy_define_canal_type_options(),    
        "#default_value"=>red_compartir_canales_copy_get_filter_value('canal_type'));
    // 
    $form['file_buscar_fs']['sareko_id']=
        array('#type'=>'select',
        '#title'=>t('Sudomain'),
        '#options'=>red_compartir_canales_copy_define_subdomain_options(),    
        "#default_value"=>red_compartir_canales_copy_get_filter_value('sareko_id'));*/
    
     /*$form['file_buscar_fs']['region']=
        array('#type'=>'textfield',
        '#title'=>t('Region'),
        "#default_value"=>red_compartir_canales_copy_get_filter_value('region'));*/
    /*
    $form['file_buscar_fs']['region']=
        array('#type'=>'select',
        '#title'=>t('Region'),
        '#options'=>  red_regiones_define_regiones_options(1),    
        "#default_value"=>red_compartir_canales_copy_get_filter_value('region'));*/
     //$form['file_buscar_fs']['region_fs']=red_regiones_define_fieldset('', red_compartir_canales_copy_get_filter_value('region'));       
    
    $form['file_buscar_fs']['average']=
        array('#type'=>'select',
        '#title'=>t('Rating'),
        '#options'=>red_compartir_canales_copy_define_average_options(),    
        "#default_value"=>red_compartir_canales_copy_get_filter_value('average'));
    /*
    $form['file_buscar_fs']['numero_de_descargas']=
        array('#type'=>'textfield',
        '#title'=>t('Downloads'),
        "#default_value"=>red_compartir_canales_copy_get_filter_value('numero_de_descargas'));*/
    
    $form['file_buscar_fs']['numero_de_descargas']=
        array('#type'=>'select',
        '#title'=>t('Downloads'),
        '#options'=>red_compartir_canal_copy_define_numero_de_descargas_options(),    
        "#default_value"=>red_compartir_canales_copy_get_filter_value('numero_de_descargas'));
    
    /*$form['file_buscar_fs']['user_local_name']=
        array('#type'=>'textfield',
        '#title'=>t('User'),
        "#default_value"=>red_compartir_canales_copy_get_filter_value('user_local_name'));*/
    
    $form['file_buscar_fs']['user_local_name']= array(
	  '#title' => t('User'),
	  '#type' => 'textfield',
	  '#maxlength' => 255,
	  '#autocomplete_path' => 'red_compartir/user_compartir_autocomplete',
	  '#default_value' => red_compartir_canales_copy_get_filter_value('user_local_name')
	);
     
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div id="red_red_fuentes_filtro_botones">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form;
}
function red_compartir_canales_copy_get_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'red_compartir_canales_copy');
}
function red_compartir_canales_copy_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['red_compartir_canales_copy']['filter']) && !empty($_SESSION['red_compartir_canales_copy']['filter'])){
                unset($_SESSION['red_compartir_canales_copy']['filter']);
            }
        }else{
            $_SESSION['red_compartir_canales_copy']['filter']=array();
            $fields=red_compartir_canales_copy_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if($f=='region'){
                        $v=red_regiones_get_region_post_value();
                    }else{
                        $v=$form_state['values'][$f];
                    }
                    if(!empty($v)){
                        $_SESSION['red_compartir_canales_copy']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function red_compartir_canales_copy_filter_fields(){
    $filter_fields=array('canal_title','canal_description','region','average','numero_de_descargas','user_local_name');
    return $filter_fields;
}
function red_compartir_canales_set_copy_filtro_get_contents(){
    $result=array();        
            $fields=red_compartir_canales_copy_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=red_compartir_canales_copy_get_filter_value($f);
                    if(!empty($v)){
                        $result[$f]=$v;
                    }
                }
            }
    if(!empty($result)){
        return base64_encode(serialize($result));
    }
    return '';
}
function red_compartir_canales_copy_define_canal_type_options(){
    $result=array();
    $result[0]='';
    $result['hound']=t('Hound');
    $result['canal_de_yql']=t('RSS Filter Channel');
    $result['canal_de_supercanal']=t('Source Channel');
    return $result;
}
function red_compartir_canales_copy_define_subdomain_options(){
    return red_compartir_fuentes_copy_define_subdomain_options();
}
function red_compartir_canales_copy_define_average_options(){
    /*$result=array();
    $result[0]='';
    $result[20]='1';
    $result[40]='2';
    $result[60]='3';
    $result[80]='4';
    $result[100]='5';
    return $result;*/
    return red_canales_define_average_options();
}
function red_compartir_unset_categorias_tematicas_by_node(&$node){
    if(isset($node->taxonomy) && !empty($node->taxonomy)){    
        $taxonomy=array();
        $my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'", $my_grupo->nid));
            if(!empty($id_categoria)){
                foreach($node->taxonomy as $tid=>$term){
                    if($term->vid!=$id_categoria){
                        $taxonomy[$tid]=$term;
                    }
                }
                $node->taxonomy=$taxonomy;
            }
        }
    }    
}
function red_compartir_canal_get_description($node,$url=''){
    return red_compartir_fuente_get_description($node,$url);
}
function red_compartir_canal_get_region($r){
    return $r->canal_region;
}
function red_compartir_canal_get_user_name($r){
    return red_compartir_fuente_get_user_name($r,1);
}
function red_compartir_view_canal_servidor_callback(){
    $id=arg(2);
    if(isset($_SESSION['red_compartir_copiar_canal_servidor'][$id])){
            $row=$_SESSION['red_compartir_copiar_canal_servidor'][$id];
            if(isset($row->canal_title) && !empty($row->canal_title)){
                $title=$row->canal_title;
            }else{
                $title=$row->title;
            }            
            drupal_set_title($title);
            $node_view=$row->node_view;                        
            //red_compartir_canal_disable_all_links($node_view);
            //$node_view=str_replace('<div class="n-opciones-item">','<div class="n-opciones-item" style="display:none;">',$node_view);
            $node_view=red_compartir_canal_repasar_node_view($node_view,$row);
            $output=$node_view;            
            $destination='red_compartir/red_compartir_copiar_canales_servidor';            
            $output.=l(t('Return'),$destination);
            return $output;
    }
    return '';
}
function red_compartir_canal_get_numero_de_descargas_get_contents_callback(){
    $nid=arg(2);
    //print $nid;
    $canal_descargados_array=red_compartir_get_canal_descargados_array($nid);
    print count($canal_descargados_array);
    exit();
}
function red_compartir_view_user_canal_servidor_callback(){
    $id=arg(2);
    if(isset($_SESSION['red_compartir_copiar_canal_servidor'][$id])){            
            $user_html=$_SESSION['red_compartir_copiar_canal_servidor'][$id]->user_local->user_html;
            drupal_set_title($_SESSION['red_compartir_copiar_canal_servidor'][$id]->user_local->name);
            $user_html=base64_decode($user_html);
            $output=$user_html;            
            //$output=red_compartir_get_user_html_compartidor_de_la_fuente($_SESSION['red_compartir_copiar_fuentes_servidor'][$id]);
            $destination='red_compartir/red_compartir_copiar_canales_servidor';            
            $output.=l(t('Return'),$destination);
            return $output;
    }
    drupal_set_title(t('User'));    
    return '';
}
function red_compartir_canal_copy_define_numero_de_descargas_options(){
    return red_compartir_fuentes_copy_define_numero_de_descargas_options();
}
function red_compartir_canales_copy_is_filter_activated(){
    $filter_fields=red_compartir_canales_copy_filter_fields();
    if(!empty($filter_fields)){
        foreach($filter_fields as $i=>$f){
            $v=red_compartir_canales_copy_get_filter_value($f);
            if(!empty($v)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_compartir_canal_existe_en_el_servidor($node){
    if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        $result=array();
        $result['canal']=new stdClass();
        $result['canal']->local_nid=$node->nid;
        $result['canal']->type=$node->type;
        $result['canal']->title=$node->title;        
        $feeds_source=hontza_get_feeds_source($node->nid);        
        $result['canal']->feeds_source=$feeds_source;
        $result_post=base64_encode(serialize($result));
        $result_post=red_compartir_grupo_encrypt_text($result_post);
        $result_post=base64_encode($result_post);
        $postdata=array();
        $postdata['canal']=$result_post;
        $url=red_compartir_define_redalerta_servidor_url();
        $url.='/red_servidor/canal_existe_en_el_servidor';
        $existe=red_compartir_canal_existe_en_el_servidor_postapi($url,$postdata);
        return $existe;
        
   }
   return 0;
}
function red_compartir_canal_existe_en_el_servidor_postapi($url,$postdata)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query( $postdata ) );
    $data=curl_exec($curl);
    $result=unserialize(trim($data));
    curl_close($curl);
    /*echo print_r($result,1);
    exit();*/        
    if(isset($result['ok']) && !empty($result['ok'])){
        return $result['existe'];
    }else{
        /*echo print_r($result,1);
        exit();*/
        return 0;
    }
    return 0;
}
function red_compartir_canal_form_with_msg($nid,$msg,$form_in){
    $form=$form_in;
        $form['msg_existe']['#value']='<p>'.$msg.'</p>';
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'node/'.$nid),
        );
        return $form;
}
function red_compartir_canal_repasar_node_view($node_view,$row){
    $result=$node_view;
    $replace='<div class="n-opciones-item" style="display:none;">';
    $result=str_replace('<div class="n-opciones-item">',$replace,$result);
    $pos_opciones=strpos($result,$replace);
    if($pos_opciones===FALSE){
        return $result;
    }else{
        $pre=substr($result,0,$pos_opciones);
        $result=$pre.'<div class="n-opciones-item">'.red_compartir_canal_define_servidor_canales_opciones_item($row).'</div>'.substr($result,$pos_opciones);
    }
    return $result;
}
function red_compartir_canal_define_servidor_canales_opciones_item($row){
    $html=array();
    if($row->sareko_id!=_SAREKO_ID){
        $html[]='<div class="item-descargar-fuente">';
        $label=t('Download');
        $html[]=l('','red_compartir/'.$row->id.'/red_compartir_copiar_canal_del_servidor',array('attributes'=>array('title'=>$label)));
        $html[]='</div>';
    }
    return implode($html,'');
}
function red_compartir_copiar_canal_del_servidor_callback(){
    $id=arg(1);
    if(isset($_SESSION['red_compartir_copiar_canal_servidor'][$id])){    
        $row=$_SESSION['red_compartir_copiar_canal_servidor'][$id];
        if(isset($row->nid) && !empty($row->nid)){    
            $form=array();
            $form_state=array();
            $form_state['clicked_button']=array();
            $form_state['clicked_button']['#name']='my_submit';
            $_REQUEST['copiar_canal']=array();
            $_REQUEST['copiar_canal'][$row->nid]=1;
            red_compartir_copiar_canales_servidor_form_submit($form,$form_state);
        }    
    }
    return '';
}
//intelsat-2015
function red_compartir_canal_view_canal_local_callback(){
    $nid=arg(2);
    $node=node_load($nid);
    drupal_set_title(hontza_get_title_canal_simbolo_img().$node->title);
    return node_view($node,FALSE,1);
}