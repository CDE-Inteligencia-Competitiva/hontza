<?php
function red_compartir_facilitador_hoja_form(){
    red_compartir_grupo_is_grupo_red_alerta_access_denied();
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);    
    $node->sareko_id=_SAREKO_ID;
        $form['#action']=red_compartir_define_url_guardar_facilitador_hoja_servidor();
        $form['#attributes']=array('target'=>'_blank');
        $facilitador_enviar=red_compartir_prepare_facilitador_enviar($node);
        $form['facilitador_enviar']=array(
            '#type'=>'hidden',
            '#default_value'=>$facilitador_enviar,
        );    
        $form['facilitador_name']=array(
            '#value'=>'<p><b>'.$node->title.'</b></p>',
        );
        $form['share_btn']=array(
            '#type'=>'submit',
            '#default_value'=>t('Share'),
        );
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'node/'.$nid),
        );
        red_compartir_add_facilitador_hoja_js();
    
    return $form;
}
function red_compartir_prepare_facilitador_enviar(&$node){
    $node->subdominio=red_compartir_grupo_get_subdominio();
    return base64_encode(serialize($node));
}
function red_compartir_add_facilitador_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_compartir_facilitador_hoja_ajax();
            //return false;
        });
        function call_red_compartir_facilitador_hoja_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var facilitador_enviar_val=$("#edit-facilitador-enviar").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_guardar_en_local_compartir_facilitador_hoja_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {facilitador_enviar:facilitador_enviar_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('red/facilitadores/todas').'"
				}
			});                        
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_guardar_en_local_compartir_facilitador_hoja_enviado_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['facilitador_enviar']) && !empty($_POST['facilitador_enviar'])){
        $node=unserialize(base64_decode(($_POST['facilitador_enviar'])));
        red_compartir_save_red_compartir_facilitador($node);
    }
    print json_encode($result);
    exit();
}
function red_compartir_save_red_compartir_facilitador($node){
    global $user;
    $row=red_compartir_get_red_compartir_facilitador_row($node);
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
        db_query($sql=sprintf('UPDATE {red_compartir_facilitador} SET uid=%d,group_nid=%d,fecha=%d WHERE nid=%d AND vid=%d',$uid,$group_nid,$fecha,$nid,$vid));
    }else{
        db_query($sql=sprintf('INSERT INTO {red_compartir_facilitador}(nid,vid,uid,group_nid,fecha) VALUES(%d,%d,%d,%d,%d)',$nid,$vid,$uid,$group_nid,$fecha));
    }
    drupal_set_message(t('Facilitator %facilitador_title shared',array('%facilitador_title'=>$node->title)));
}
function red_compartir_get_red_compartir_facilitador_row($node){
    $red_compartir_facilitador_array=red_compartir_get_red_compartir_facilitador_array($node->vid,$node->nid);
    if(count($red_compartir_facilitador_array)>0){
        return $red_compartir_facilitador_array[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_get_red_compartir_facilitador_array($vid,$nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($vid)){
        $where[]='vid='.$vid;
    }
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    $sql='SELECT * FROM {red_compartir_facilitador} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_define_url_guardar_facilitador_hoja_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_facilitador_hoja',array('absolute'=>TRUE));
}
function red_compartir_copiar_facilitadores_servidor_form(){
    drupal_set_title(t('Download Facilitators'));
    boletin_report_no_group_selected_denied();
    red_compartir_grupo_is_grupo_red_alerta_access_denied();
    $form=array();
        $facilitadores_list=red_compartir_get_facilitadores_servidor_list();
        //intelsat-2015
        $prefix='<div class="n-opciones-item">';
        $form_return_btn=array(
            '#value'=>red_compartir_copiar_fuentes_servidor_volver_link(),
            '#prefix'=>$prefix,                             
        );
        //
        if(!empty($facilitadores_list)){
            /*$form['copiar_facilitador']['#tree']=true;
            foreach($facilitadores_list as $i=>$row){
                $facilitador=$row->node;
                $form['copiar_facilitador'][$row->nid]=array(
                    '#type'=>'checkbox',
                    '#title'=>$facilitador->title,
                );
            }*/
            $form['copiar_facilitador_table']=array(
                 '#value'=>red_compartir_facilitador_get_copiar_table_html($facilitadores_list),
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
            $my_msg=red_compartir_navegar_menu().t('There are no contents');
            $form['my_msg']=array(
                '#value'=>'<p>'.$my_msg.'</p>',
            );
            //intelsat-2015
            $form['return_btn']=$form_return_btn;
            $form['return_btn']['#suffix']='</div>';
            //
        }    
        /*$form['return_btn']=array(
            '#value'=>red_compartir_copiar_facilitadores_servidor_volver_link(),
        );*/
    return $form;
}
function red_compartir_copiar_facilitadores_servidor_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button']) && $form_state['clicked_button']['#name']=='my_submit'){
        /*$values=$form_state['values'];
        if(isset($values['copiar_facilitador']) && !empty($values['copiar_facilitador'])){
            $my_array=$values['copiar_facilitador'];*/
            $my_array=array();
            if(isset($_REQUEST['copiar_facilitador']) && !empty($_REQUEST['copiar_facilitador'])){
                $my_array=$_REQUEST['copiar_facilitador'];
                //echo print_r($my_array,1);
                //exit();
            }
            if(!empty($my_array)){
                //$fuentes_servidor=red_compartir_get_fuentes_servidor_list();
                $facilitadores_servidor=red_compartir_get_facilitadores_servidor_list();
                foreach($my_array as $nid=>$v){
                    if(!empty($v)){                       
                        if(!red_compartir_is_copiado_del_servidor_facilitador($nid)){
                            $facilitador_servidor=red_compartir_get_facilitador_servidor($nid,$facilitadores_servidor);
                            if(isset($facilitador_servidor->nid) && !empty($facilitador_servidor->nid)){
                                red_compartir_save_facilitador_descargados($facilitador_servidor,$fuente,$new_facilitador);
                                drupal_set_message(t('Facilitator %facilitador_title downloaded',array('%facilitador_title'=>$new_facilitador->title)));
                            }    
                        }else{
                            $facilitador_servidor=red_compartir_get_facilitador_servidor($nid,$facilitadores_servidor);
                            //print 'el facilitador existe';exit();
                            drupal_set_message(t('The Facilitator %facilitador_title exist',array('%facilitador_title'=>$facilitador_servidor->node->title)));
                        }
                    }
                }
            }
        //}
    }
    //drupal_goto('fuentes-pipes/todas');
    drupal_goto('red_compartir/red_compartir_copiar_facilitadores_servidor');
}
function red_compartir_get_facilitadores_servidor_list(){
    global $language;
    $result=array();
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/red_servidor_facilitadores_get_contents?lang_alerta='.$language->language;
    $content=file_get_contents($url);
    $facilitadores_servidor=unserialize($content);
    if(count($facilitadores_servidor)>0){
        foreach($facilitadores_servidor as $i=>$row){
            //if($row->sareko_id!=_SAREKO_ID){
                $result[]=$row;
            //}
        }
    }    
    return $result;
}
function red_compartir_copiar_facilitadores_servidor_volver_link(){
    //$url='fuentes-pipes/todas';
    $url='servicios';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url=$_REQUEST['destination'];
    }
    //intelsat-2015
    //return l(t('Return'),$url,array('attributes'=>array('class'=>'back_left')));
    return red_compartir_copiar_fuentes_servidor_volver_link(0,$url);
}
function red_compartir_is_copiado_del_servidor_facilitador($nid){
    $facilitador=red_compartir_get_facilitador_descargados_row($nid);
    if(isset($facilitador->id) && !empty($facilitador->id)){
        return 1;
    }
    return 0;
}
function red_compartir_get_facilitador_descargados_row($nid){
    $facilitador_array=red_compartir_get_facilitador_descargados_array($nid);
    if(count($facilitador_array)>0){
        return $facilitador_array[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_get_facilitador_descargados_array($nid=''){
    $where=array();
    $where[]='1';
    if(!empty($nid)){
        $where[]='servidor_nid='.$nid;
    }
    $sql='SELECT * FROM {red_compartir_facilitador_descargados} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_get_facilitador_servidor($nid,$facilitadores_servidor){
    if(!empty($facilitadores_servidor)){
        foreach($facilitadores_servidor as $i=>$row){
           if($nid==$row->nid){
               return $row;
           }
        }
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_save_facilitador_descargados($facilitador_servidor,$fuente,&$node){
    if(isset($facilitador_servidor->node)){
        $node=$facilitador_servidor->node;
        $node->field_nid_fuente_facilitador[0]['value']=$fuente->nid;                
        //
            $servidor_nid=$node->nid; 
            $servidor_vid=$node->vid; 
            $servidor_uid=$node->uid;
            $servidor_group_nid=$facilitador_servidor->group_nid;
            $fecha=time();
            $servidor_sareko_id=$facilitador_servidor->sareko_id;
            $node=red_compartir_local_preparar_facilitador($node,$grupo_local);
            node_save($node);
            $vid=$node->vid;
            $nid=$node->nid;
            $uid=$node->uid;
            $group_nid=$grupo_local->nid;
            $sql=sprintf('INSERT INTO {red_compartir_facilitador_descargados}(servidor_nid,servidor_vid,servidor_uid,servidor_group_nid,fecha,servidor_sareko_id,vid,nid,uid,group_nid) VALUES(%d,%d,%d,%d,%d,"%s",%d,%d,%d,%d)',$servidor_nid,$servidor_vid,$servidor_uid,$servidor_group_nid,$fecha,$servidor_sareko_id,$vid,$nid,$uid,$group_nid);
            db_query($sql);
    }    
}
function red_compartir_local_preparar_facilitador($node_in,&$grupo_local){
    $node=red_compartir_local_preparar_fuente($node_in,$grupo_local);
    //AVISO::::los facilitadores no son de grupos son de los subdominios
    if(isset($node->og_groups)){
        unset($node->og_groups);
    }
    if(isset($node->og_groups_both)){
        unset($node->og_groups_both);
    }
    return $node;
}
function red_compartir_facilitador_get_copiar_table_html($facilitador_list){    
    $headers=array();
    $headers[0]='';
    $headers[1]=t('Title');
    $headers[2]=t('Description');
    $headers[3]=t('Categories');
    //$headers[4]=t('Subdomain');    
    $headers[4]=t('Created');    
    //
    $kont=0;
    $rows=array();
    $_SESSION['red_compartir_copiar_facilitador_servidor']=array();
    if(!empty($facilitador_list)){
        foreach($facilitador_list as $i=>$r){
            if(isset($r->node)){
                $node=$r->node;
                $_SESSION['red_compartir_copiar_facilitador_servidor'][$r->id]=$r;
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=array();
                    $rows[$kont][0]='<div style="white-space:nowrap;float:right;">';
                    if($r->sareko_id==_SAREKO_ID){
                        $rows[$kont][0].='<div style="margin-top:-18px;">&nbsp;</div>';
                    }else{
                        $rows[$kont][0].='<input type="checkbox" id="copiar_facilitador_'.$r->nid.'" name="copiar_facilitador['.$r->nid.']" value="1"/>';
                    }
                    //intelsat-2015
                    //se ha comentado esto
                    //$rows[$kont][0].='&nbsp;'.l(my_get_icono_action('viewmag', t('View')),'red_compartir/red_compartir_view_facilitador_servidor/'.$r->id,array('html'=>true,'query'=>drupal_get_destination()));                    
                    //
                    $rows[$kont][0].='</div>';
                    //intelsat-2015
                    $rows[$kont][1]=l($node->title,'red_compartir/red_compartir_view_facilitador_servidor/'.$r->id,array('query'=>drupal_get_destination()));
                    //
                    $rows[$kont][2]=$node->field_descripcion_servicios[0]['value'];
                    $rows[$kont][3]=red_compartir_facilitador_copy_set_categories($node);
                    //$rows[$kont][4]=$r->sareko_id_label;
                    //$rows[$kont][5]=date('Y-m-d H:i',$r->fecha);
                    $rows[$kont][4]=date('Y-m-d',$r->fecha);
                    $kont++;
                }
            }
        }        
    }
    $output.=red_compartir_navegar_menu();
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));           
    }
    else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function red_compartir_facilitador_copy_set_categories($node,$is_link=0){
    if(isset($node->taxonomy)){
        $kont=0;
        foreach($node->taxonomy as $i=>$servidor_term){
            if(red_compartir_facilitador_is_categoria_servicios($servidor_term)){
                $html[]='<div class="field-item field-item-'.$kont.'">';
                $term=taxonomy_get_term_by_language($servidor_term->tid);
                if(isset($term->name) && !empty($term->name)){
                    if($kont>=2){
                        $html[]='...';
                        break;
                    }else{
                        $label=$term->name;
                        if($is_link){
                            $html[]=l($label,'taxonomy/term/'.$term->tid);
                        }else{
                            $html[]=$label;
                        }    
                    }                        
                }else{
                    $html[]=$servidor_term->name;
                }
                $html[]='</div>';
                $kont++;
            }    
        }
        return implode('',$html);
    }
    return '';
}
function red_compartir_facilitador_is_categoria_servicios($servidor_term){
    if(isset($servidor_term->vid) && !empty($servidor_term->vid) && $servidor_term->vid==4){
        return 1;
    }
    return 0;
}
function red_compartir_view_facilitador_servidor_callback(){
    $id=arg(2);
    if(isset($_SESSION['red_compartir_copiar_facilitador_servidor'][$id])){
            $row=$_SESSION['red_compartir_copiar_facilitador_servidor'][$id];
            if(isset($row->node) && !empty($row->node->title)){
                $title=$row->node->title;
            }            
            drupal_set_title($title);
            $node_view=$row->node_view;
            $node_view.='<div class="n-opciones-item">'.red_compartir_facilitador_define_servidor_facilitadores_opciones_item($row).'</div>';
            $output=$node_view;            
            $destination='red_compartir/red_compartir_copiar_facilitadores_servidor';            
            $output.=l(t('Return'),$destination);
            return $output;
    }
    return '';
}
function red_compartir_facilitador_define_servidor_facilitadores_opciones_item($row){
    $html=array();
    if($row->sareko_id!=_SAREKO_ID){
        $html[]='<div class="item-descargar-fuente">';
        $label=t('Download');
        $html[]=l('','red_compartir/'.$row->id.'/red_compartir_copiar_facilitador_del_servidor',array('attributes'=>array('title'=>$label)));
        $html[]='</div>';
    }
    return implode($html,'');
}
function red_compartir_copiar_facilitador_del_servidor_callback(){
    $id=arg(1);
    if(isset($_SESSION['red_compartir_copiar_facilitador_servidor'][$id])){    
        $row=$_SESSION['red_compartir_copiar_facilitador_servidor'][$id];
        if(isset($row->nid) && !empty($row->nid)){    
            $form=array();
            $form_state=array();
            $form_state['clicked_button']=array();
            $form_state['clicked_button']['#name']='my_submit';
            $_REQUEST['copiar_facilitador']=array();
            $_REQUEST['copiar_facilitador'][$row->nid]=1;
            red_compartir_copiar_facilitadores_servidor_form_submit($form,$form_state);
        }    
    }
    return '';
}
//intelsat-2015
function red_compartir_facilitador_view_facilitador_local_callback(){
    $nid=arg(2);
    $node=node_load($nid);
    drupal_set_title($node->title);
    return node_view($node);
}