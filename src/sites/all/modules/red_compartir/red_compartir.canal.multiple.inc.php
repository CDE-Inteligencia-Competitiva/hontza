<?php
function red_compartir_canal_hoja_multiple_form(){
    global $user;
    $form=array();
    $canal_existe_multiple=array();
    $canal_existe_titulo_multiple=array();
    $canal_hound_multiple=array();
    $is_enviar=0;
    if(isset($_SESSION['upload_canal_array']['user_'.$user->uid])){
        $upload_canal_array=$_SESSION['upload_canal_array']['user_'.$user->uid];
        red_compartir_grupo_is_grupo_red_alerta_access_denied();        
        $form['#action']=red_compartir_canal_multiple_define_url_canal_servidor();
        $form['#attributes']=array('target'=>'_blank');
        if(!empty($upload_canal_array)){
            $canal_enviar_multiple=array();
            $canal_name_multiple=array();
            $existe_array=red_compartir_canal_multiple_get_existe_en_el_servidor_array($upload_canal_array,$node_array);
            foreach($upload_canal_array as $i=>$nid){
                if(isset($node_array[$i])){
                    $node=$node_array[$i];                    
                }else{
                    $node=node_load($nid);
                }
                if(red_compartir_canal_is_compartible($node)){
                    if(hontza_is_hound_canal('',$node)){
                        $canal_hound_multiple[]=$node->title;
                    }else{                    
                        if(isset($existe_array[$node->nid]) && !empty($existe_array[$node->nid])){
                            if($existe_array[$node->nid]==1){
                                $canal_existe_multiple[]=$node->title;
                            }else if($existe_array[$node->nid]==2){
                                $canal_existe_titulo_multiple[]=$node->title;
                            }                
                        }else{
                            $is_enviar=1;
                            $node->sareko_id=_SAREKO_ID;
                            $node->user_local=red_compartir_prepare_user_enviar($user);
                            $canal_enviar=red_compartir_prepare_canal_enviar($node);
                            $canal_enviar_multiple[]=$canal_enviar;
                            $canal_name_multiple[]=$node->title;
                        }
                    }    
                }    
            }
            $canal_enviar_multiple=red_compartir_canal_multiple_encode($canal_enviar_multiple);
            $form['canal_enviar_multiple']=array(
                    '#type'=>'hidden',
                    '#default_value'=>$canal_enviar_multiple,
            );    
            $form['canal_name_multiple']=array(
                    '#value'=>red_compartir_canal_multiple_define_enviar_canal_name_ul($canal_name_multiple),
            );
            if(!empty($canal_existe_multiple) || !empty($canal_existe_titulo_multiple) || !empty($canal_hound_multiple)){
                $form['existe_msg_ini']=array(
                        '#value'=>'<p style="color:red;font-weight:bold;"><i>'.t("I's not possible to share the following sources because").'</i>:</p>',
                );
                if(!empty($canal_existe_multiple)){
                    $form['canal_existe_multiple']=array(
                            '#value'=>red_compartir_canal_multiple_existe_ul($canal_existe_multiple,1),
                    );
                }
                if(!empty($canal_existe_titulo_multiple)){
                    $form['canal_existe_titulo_multiple']=array(
                            '#value'=>red_compartir_canal_multiple_existe_ul($canal_existe_titulo_multiple,2),
                    );
                }
                if(!empty($canal_hound_multiple)){
                    $form['canal_hound_multiple']=array(
                            '#value'=>red_compartir_canal_multiple_existe_ul($canal_hound_multiple,'',1),
                    );
                }
            }
        }
        //if(empty($canal_existe_multiple) && empty($canal_existe_titulo_multiple)){
        if($is_enviar){    
            $form['share_btn']=array(
                '#type'=>'submit',
                '#default_value'=>t('Share'),
            );
        }
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),'red/canales/todas'),
        );
        red_compartir_canal_multiple_add_canal_hoja_js();        
    }else{
        $form['my_message']['#value']='<p>'.t('There are no contents').'</p>';
        $form['volver_btn']['#value']=l(t('Return'),'red/canales/todas');
    }    
    return $form;
}
function red_compartir_canal_multiple_define_url_canal_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_canal_hoja_multiple',array('absolute'=>TRUE));
}
function red_compartir_canal_multiple_define_enviar_canal_name_ul($canal_name_multiple){
    return red_compartir_fuente_multiple_define_enviar_fuente_name_ul($canal_name_multiple);
}
function red_compartir_canal_multiple_encode($canal_enviar_multiple){
    return base64_encode(serialize($canal_enviar_multiple));
}
function red_compartir_canal_multiple_add_canal_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_compartir_canal_hoja_multiple_ajax();
            //return false;
        });
        function call_red_compartir_canal_hoja_multiple_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var canal_enviar_multiple_val=$("#edit-canal-enviar-multiple").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_guardar_en_local_compartir_canal_hoja_multiple_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {canal_enviar_multiple:canal_enviar_multiple_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('red/canales/todas').'";
				}
			});          
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_guardar_en_local_compartir_canal_hoja_enviado_multiple_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['canal_enviar_multiple']) && !empty($_POST['canal_enviar_multiple'])){
        $canal_enviar_multiple=unserialize(base64_decode($_POST['canal_enviar_multiple']));
        if(!empty($canal_enviar_multiple)){
            foreach($canal_enviar_multiple as $i=>$value){
                $canal_enviar=unserialize(base64_decode($value));
                red_compartir_save_red_compartir_canal($canal_enviar);               
            }
        }
    }
    print json_encode($result);
    exit();
}
function red_compartir_canal_multiple_get_existe_en_el_servidor_array($upload_canal_array,&$node_array){
    $result_post=array();
    $kont=0;            
    $node_array=array();
    if(!empty($upload_canal_array)){
        foreach($upload_canal_array as $i=>$nid){
            $node=node_load($nid);            
            if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
                $result_post[$kont]=array();                
                $result_post[$kont]['canal']=new stdClass();
                $result_post[$kont]['canal']->local_nid=$node->nid;                
                $result_post[$kont]['canal']->type=$node->type;
                $result_post[$kont]['canal']->title=$node->title;
                $feeds_source=hontza_get_feeds_source($node->nid);        
                $result_post[$kont]['canal']->feeds_source=$feeds_source;
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
    $postdata['canal_array']=$result_post;
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/canal_multiple_existe_en_el_servidor';
    $existe_array=red_compartir_canal_multiple_existe_en_el_servidor_postapi($url,$postdata);
    //echo print_r($existe_array,1);exit();
    return $existe_array;    
}
function red_compartir_canal_multiple_existe_en_el_servidor_postapi($url,$postdata)
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
function red_compartir_canal_multiple_existe_ul($existe_name_array,$existe,$is_hound=0){
    $html=array();
    //$msg_ini="I's not possible to share the following sources because"; 
    if($is_hound){
        $msg=t('It is a Hound Channel');
    }else{    
        if($existe==1){
            $msg=t("The Url is already shared");
        }else if($existe==2){
            $msg=t("Title already exists, please change it");
        }
    }    
    $html[]='<p style="color:red;font-weight:bold;">'.$msg.'</p>';    
    $html[]='<ul style="color:red;">';
    if(!empty($existe_name_array)){
        foreach($existe_name_array as $i=>$name){
            $html[]='<li><b>'.$name.'</b></li>';
        }    
    }
    $html[]='</ul><BR>';
    return implode('',$html);
}