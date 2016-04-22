<?php
function red_servidor_guardar_facilitador_hoja_multiple_callback(){
    if(isset($_POST['facilitador_enviar_multiple']) && !empty($_POST['facilitador_enviar_multiple'])){
        $facilitador_enviar_multiple=unserialize(base64_decode($_POST['facilitador_enviar_multiple']));
        if(!empty($facilitador_enviar_multiple)){
            foreach($facilitador_enviar_multiple as $i=>$value){
                $facilitador_enviar=unserialize(base64_decode($value));                
                $row=red_servidor_get_red_servidor_facilitador($facilitador_enviar);
                if(isset($row->id) && !empty($row->id)){
                    //
                }else{
                    red_servidor_facilitador_guardar_facilitador_enviar($facilitador_enviar);                    
                }              
            }
        }
    }
    //return t('Shared channels saved').' '.date('Y-m-d H:i:s');
    //drupal_goto('red_publica/facilitadores');
    drupal_goto('red_publica/fuentes');
}
function red_servidor_facilitador_multiple_existe_en_el_servidor_callback(){
    $result=array();
    $result['ok']=0;
    $result['existe_array']=array();
    if(isset($_POST['facilitador_array']) && !empty($_POST['facilitador_array'])){
        $result['ok']=1;
        $facilitador_array=$_POST['facilitador_array'];
        $facilitador_array=base64_decode($facilitador_array);
        $facilitador_array=red_compartir_grupo_decrypt_text($facilitador_array);
        $facilitador_array=unserialize(base64_decode($facilitador_array));
        if(!empty($facilitador_array)){
            foreach($facilitador_array as $i=>$facilitador){        
                if(in_array($facilitador['facilitador']->type,array('facilitador_de_superfacilitador','facilitador_de_yql'))){
                    $existe=red_servidor_facilitador_existe_en_el_servidor($facilitador['facilitador'],$sql);
                    if(!empty($existe)){
                        $result['existe_array'][$facilitador['facilitador']->local_nid]=$existe;                    
                    }
                }
            }    
        }
    }
    print serialize($result);
    exit();
}