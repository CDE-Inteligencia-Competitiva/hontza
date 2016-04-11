<?php
function red_compartir_fuente_hoja_multiple_form(){
    global $user;
    drupal_set_title(t('Sharing Sources'));
    $form=array();
    $source_existe_multiple=array();
    $source_existe_titulo_multiple=array();
    $is_enviar=0;
    
    $url_volver='red/fuentes-pipes/todas';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_volver=$_REQUEST['destination'];
    }
    
    if(isset($_SESSION['upload_fuente_array']['user_'.$user->uid])){
        $upload_fuente_array=$_SESSION['upload_fuente_array']['user_'.$user->uid];
        global $user;
        red_compartir_grupo_is_grupo_red_alerta_access_denied();
        
        $form['#action']=red_compartir_fuente_multiple_define_url_fuente_servidor();
        $form['#attributes']=array('target'=>'_blank');
        if(!empty($upload_fuente_array)){
            $fuente_enviar_multiple=array();
            $source_name_multiple=array();           
            $existe_array=red_compartir_fuente_multiple_get_existe_en_el_servidor_array($upload_fuente_array,$node_array);
            //echo 'existe_array='.print_r($existe_array,1);exit();
            foreach($upload_fuente_array as $i=>$nid){
                if(isset($node_array[$i])){
                    $node=$node_array[$i];                    
                }else{
                    $node=node_load($nid);
                }
                //
                if(isset($existe_array[$node->nid]) && !empty($existe_array[$node->nid])){
                    if($existe_array[$node->nid]==1){
                        $source_existe_multiple[]=$node->title;
                    }else if($existe_array[$node->nid]==2){
                        $source_existe_titulo_multiple[]=$node->title;
                    }                
                }else{
                    $is_enviar=1;
                    $node->sareko_id=_SAREKO_ID;
                    $node->user_local=red_compartir_prepare_user_enviar($user);
                    $fuente_enviar=red_compartir_prepare_fuente_enviar($node);
                    $fuente_enviar_multiple[]=$fuente_enviar;
                    $source_name_multiple[]=$node->title;
                }
            }
            $fuente_enviar_multiple=red_compartir_prepare_fuente_enviar($fuente_enviar_multiple);
            $form['fuente_enviar_multiple']=array(
                    '#type'=>'hidden',
                    '#default_value'=>$fuente_enviar_multiple,
            );    
            $form['source_name_multiple']=array(
                    '#value'=>red_compartir_fuente_multiple_define_enviar_fuente_name_ul($source_name_multiple),
            );
            if(!empty($source_existe_multiple) || !empty($source_existe_titulo_multiple)){
                $form['existe_msg_ini']=array(
                        '#value'=>'<p style="color:red;font-weight:bold;"><i>'.t("I's not possible to share the following sources because").'</i>:</p>',
                );
                if(!empty($source_existe_multiple)){
                    $form['source_existe_multiple']=array(
                            '#value'=>red_compartir_fuente_multiple_existe_yahoo_id_ul($source_existe_multiple,1),
                    );
                }
                if(!empty($source_existe_titulo_multiple)){
                    $form['source_existe_titulo_multiple']=array(
                            '#value'=>red_compartir_fuente_multiple_existe_yahoo_id_ul($source_existe_titulo_multiple,2),
                    );
                }    
            }
        }
        //if(empty($source_existe_multiple) && empty($source_existe_titulo_multiple)){
        if($is_enviar){        
            $form['share_btn']=array(
                '#type'=>'submit',
                '#default_value'=>t('Share'),
            );
        }
        $form['cancelar_btn']=array(
            '#value'=>l(t('Return'),$url_volver,array('attributes'=>'back_left')),
        );
        red_compartir_fuente_multiple_add_fuente_hoja_js();        
    }else{
        $form['my_message']['#value']='<p>'.t('There are no contents').'</p>';
        $form['volver_btn']['#value']=l(t('Return'),$url_volver,array('attributes'=>'back_left'));
    }    
    return $form;
}
function red_compartir_fuente_multiple_define_url_fuente_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_fuente_hoja_multiple',array('absolute'=>TRUE));
}
function red_compartir_fuente_multiple_define_enviar_fuente_name_ul($source_name_multiple){
    $html=array();
    $html[]='<ul>';
    if(!empty($source_name_multiple)){
        foreach($source_name_multiple as $i=>$name){
            $html[]='<li><b>'.$name.'</b></li>';
        }    
    }
    $html[]='</ul><BR>';
    return implode('',$html);
}
function red_compartir_fuente_multiple_add_fuente_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_compartir_fuente_hoja_multiple_ajax();
            //return false;
        });
        function call_red_compartir_fuente_hoja_multiple_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var fuente_enviar_multiple_val=$("#edit-fuente-enviar-multiple").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_guardar_en_local_compartir_fuente_hoja_multiple_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {fuente_enviar_multiple:fuente_enviar_multiple_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('red/fuentes-pipes/todas').'";
				}
			});          
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_guardar_en_local_compartir_fuente_hoja_enviado_multiple_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['fuente_enviar_multiple']) && !empty($_POST['fuente_enviar_multiple'])){
        $fuente_enviar_multiple=unserialize(base64_decode($_POST['fuente_enviar_multiple']));
        if(!empty($fuente_enviar_multiple)){
            foreach($fuente_enviar_multiple as $i=>$value){
                $fuente_enviar=unserialize(base64_decode($value));
                red_compartir_save_red_compartir_fuente($fuente_enviar);
            }
        }
    }
    print json_encode($result);
    exit();
}
function red_compartir_fuente_multiple_get_existe_en_el_servidor_array($upload_fuente_array,&$node_array){
    $result_post=array();
    $kont=0;            
    $node_array=array();
    if(!empty($upload_fuente_array)){
        foreach($upload_fuente_array as $i=>$nid){
            $node=node_load($nid);            
            if($node->type=='supercanal'){
                $result_post[$kont]=array();                
                $result_post[$kont]['fuente']=new stdClass();
                $result_post[$kont]['fuente']->local_nid=$node->nid;                
                $result_post[$kont]['fuente']->type=$node->type;
                $result_post[$kont]['fuente']->title=$node->title;
                $result_post[$kont]['fuente']->yahoo_pipe_id='';
                if(isset($node->field_supercanal_fuente[0]['value'])){
                    $result_post[$kont]['fuente']->yahoo_pipe_id=$node->field_supercanal_fuente[0]['value'];
                }
                $kont++;
            }            
            $node_array[]=$node;
        }
    }        
    $result_post=base64_encode(serialize($result_post));
    $result_post=red_compartir_grupo_encrypt_text($result_post);
    $result_post=base64_encode($result_post);
    $postdata=array();
    $postdata['fuente_array']=$result_post;
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/fuente_multiple_existe_en_el_servidor';
    $existe_array=red_compartir_fuente_multiple_existe_en_el_servidor_postapi($url,$postdata);
    //echo print_r($existe_array,1);exit();
    return $existe_array;    
}
function red_compartir_fuente_multiple_existe_en_el_servidor_postapi($url,$postdata)
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
    
    /*echo print_r($result,1);
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
function red_compartir_fuente_multiple_existe_yahoo_id_ul($existe_name_array,$existe){
    $html=array();
    //$msg_ini="I's not possible to share the following sources because"; 
    if($existe==1){
        $msg=t("Yahoo Pipes ID is already shared");
    }else if($existe==2){
        $msg=t("Title already exists, please change it");
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