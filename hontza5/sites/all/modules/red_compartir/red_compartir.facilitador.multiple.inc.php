<?php
function red_compartir_facilitador_hoja_multiple_form(){
    global $user;
    $form=array();
    $facilitador_existe_multiple=array();
    $facilitador_existe_titulo_multiple=array();
    $facilitador_hound_multiple=array();
    $is_enviar=0;
    if(isset($_SESSION['upload_facilitador_array']['user_'.$user->uid])){
        $upload_facilitador_array=$_SESSION['upload_facilitador_array']['user_'.$user->uid];
        red_compartir_grupo_is_grupo_red_alerta_access_denied();        
        $form['#action']=red_compartir_facilitador_multiple_define_url_facilitador_servidor();
        $form['#attributes']=array('target'=>'_blank');
        if(!empty($upload_facilitador_array)){
            $facilitador_enviar_multiple=array();
            $facilitador_name_multiple=array();
            $existe_array=red_compartir_facilitador_multiple_get_existe_en_el_servidor_array($upload_facilitador_array,$node_array);
            foreach($upload_facilitador_array as $i=>$nid){
                if(isset($node_array[$i])){
                    $node=$node_array[$i];                    
                }else{
                    $node=node_load($nid);
                }
                if(red_compartir_facilitador_is_compartible($node)){
                        if(isset($existe_array[$node->nid]) && !empty($existe_array[$node->nid])){
                            if($existe_array[$node->nid]==1){
                                $facilitador_existe_multiple[]=$node->title;
                            }else if($existe_array[$node->nid]==2){
                                $facilitador_existe_titulo_multiple[]=$node->title;
                            }                
                        }else{
                            $is_enviar=1;
                            $node->sareko_id=_SAREKO_ID;
                            $node->user_local=red_compartir_prepare_user_enviar($user);
                            $facilitador_enviar=red_compartir_prepare_facilitador_enviar($node);
                            $facilitador_enviar_multiple[]=$facilitador_enviar;
                            $facilitador_name_multiple[]=$node->title;
                        }    
                }    
            }
            $facilitador_enviar_multiple=red_compartir_facilitador_multiple_encode($facilitador_enviar_multiple);
            $form['facilitador_enviar_multiple']=array(
                    '#type'=>'hidden',
                    '#default_value'=>$facilitador_enviar_multiple,
            );    
            $form['facilitador_name_multiple']=array(
                    '#value'=>red_compartir_facilitador_multiple_define_enviar_facilitador_name_ul($facilitador_name_multiple),
            );
            if(!empty($facilitador_existe_multiple) || !empty($facilitador_existe_titulo_multiple) || !empty($facilitador_hound_multiple)){
                $form['existe_msg_ini']=array(
                        '#value'=>'<p style="color:red;font-weight:bold;"><i>'.t("I's not possible to share the following sources because").'</i>:</p>',
                );
                if(!empty($facilitador_existe_multiple)){
                    $form['facilitador_existe_multiple']=array(
                            '#value'=>red_compartir_facilitador_multiple_existe_ul($facilitador_existe_multiple,1),
                    );
                }
                if(!empty($facilitador_existe_titulo_multiple)){
                    $form['facilitador_existe_titulo_multiple']=array(
                            '#value'=>red_compartir_facilitador_multiple_existe_ul($facilitador_existe_titulo_multiple,2),
                    );
                }
                if(!empty($facilitador_hound_multiple)){
                    $form['facilitador_hound_multiple']=array(
                            '#value'=>red_compartir_facilitador_multiple_existe_ul($facilitador_hound_multiple,'',1),
                    );
                }
            }
        }
        //if(empty($facilitador_existe_multiple) && empty($facilitador_existe_titulo_multiple)){
        if($is_enviar){    
            $form['share_btn']=array(
                '#type'=>'submit',
                '#default_value'=>t('Share'),
            );
        }
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'red/facilitadores/todas'),
        );
        red_compartir_facilitador_multiple_add_facilitador_hoja_js();        
    }else{
        $form['my_message']['#value']='<p>'.t('There are no contents').'</p>';
        $form['volver_btn']['#value']=l(t('Return'),'red/facilitadores/todas');
    }    
    return $form;
}
function red_compartir_facilitador_multiple_define_url_facilitador_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_facilitador_hoja_multiple',array('absolute'=>TRUE));
}
function red_compartir_facilitador_multiple_get_existe_en_el_servidor_array($upload_facilitador_array,&$node_array){
    $result_post=array();
    $kont=0;            
    $node_array=array();
    if(!empty($upload_facilitador_array)){
        foreach($upload_facilitador_array as $i=>$nid){
            $node=node_load($nid);            
            if(in_array($node->type,array('servicio'))){
                $result_post[$kont]=array();                
                $result_post[$kont]['facilitador']=new stdClass();
                $result_post[$kont]['facilitador']->local_nid=$node->nid;                
                $result_post[$kont]['facilitador']->type=$node->type;
                $result_post[$kont]['facilitador']->title=$node->title;
                $feeds_source=hontza_get_feeds_source($node->nid);        
                $result_post[$kont]['facilitador']->feeds_source=$feeds_source;
                //
                $kont++;
            }            
            $node_array[]=$node;
        }
    }        
    $result_post=base64_encode(serialize($result_post));
    $result_post=red_compartir_grupo_encrypt_text($result_post);
    $result_post=base64_encode($result_post);
    $postdata=array();
    $postdata['facilitador_array']=$result_post;
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/facilitador_multiple_existe_en_el_servidor';
    $existe_array=red_compartir_facilitador_multiple_existe_en_el_servidor_postapi($url,$postdata);
    //echo print_r($existe_array,1);exit();
    return $existe_array;    
}
function red_compartir_facilitador_multiple_existe_en_el_servidor_postapi($url,$postdata)
{
    $existe_array=array();
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query( $postdata ) );
    $data=curl_exec($curl);
    $result=unserialize(trim($data));
    curl_close($curl);    
    /*echo 'curl_result='.print_r($result,1);
    exit();*/        
    if(isset($result['ok']) && !empty($result['ok'])){
        return $result['existe_array'];
    }else{
        /*echo print_r($result,1);
        exit();*/
        return $existe_array;
    }
    return $existe_array;
}
function red_compartir_facilitador_is_compartible($node){
    return 1;
}
function red_compartir_facilitador_multiple_encode($facilitador_enviar_multiple){
    return base64_encode(serialize($facilitador_enviar_multiple));
}
function red_compartir_facilitador_multiple_define_enviar_facilitador_name_ul($facilitador_name_multiple){
    return red_compartir_fuente_multiple_define_enviar_fuente_name_ul($facilitador_name_multiple);
}
function red_compartir_facilitador_multiple_add_facilitador_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_compartir_facilitador_hoja_multiple_ajax();
            //return false;
        });
        function call_red_compartir_facilitador_hoja_multiple_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var facilitador_enviar_multiple_val=$("#edit-facilitador-enviar-multiple").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_guardar_en_local_compartir_facilitador_hoja_multiple_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {facilitador_enviar_multiple:facilitador_enviar_multiple_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('red/facilitadores/todas').'";
				}
			});          
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_guardar_en_local_compartir_facilitador_hoja_enviado_multiple_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['facilitador_enviar_multiple']) && !empty($_POST['facilitador_enviar_multiple'])){
        $facilitador_enviar_multiple=unserialize(base64_decode($_POST['facilitador_enviar_multiple']));
        if(!empty($facilitador_enviar_multiple)){
            foreach($facilitador_enviar_multiple as $i=>$value){
                $facilitador_enviar=unserialize(base64_decode($value));
                red_compartir_save_red_compartir_facilitador($facilitador_enviar);               
            }
        }
    }
    print json_encode($result);
    exit();
}