<?php
function red_servidor_guardar_fuente_hoja_multiple_callback(){
    if(isset($_POST['fuente_enviar_multiple']) && !empty($_POST['fuente_enviar_multiple'])){
        $fuente_enviar_multiple=unserialize(base64_decode($_POST['fuente_enviar_multiple']));
        if(!empty($fuente_enviar_multiple)){
            foreach($fuente_enviar_multiple as $i=>$value){
                $fuente_enviar=unserialize(base64_decode($value));
                $row=red_servidor_get_red_servidor_fuente($fuente_enviar);
                if(isset($row->id) && !empty($row->id)){
                    //
                }else{
                    red_servidor_guardar_fuente_enviar($fuente_enviar);                            
                }
            }
        }
    }
    //return t('Shared sources saved').' '.date('Y-m-d H:i:s');
    drupal_goto('red_publica/fuentes');
}
function red_servidor_fuente_multiple_existe_en_el_servidor_callback(){
    $result=array();
    $result['ok']=0;
    $result['existe_array']=array();
    if(isset($_POST['fuente_array']) && !empty($_POST['fuente_array'])){
        $result['ok']=1;
        $fuente_array=$_POST['fuente_array'];
        $fuente_array=base64_decode($fuente_array);
        $fuente_array=red_compartir_grupo_decrypt_text($fuente_array);
        $fuente_array=unserialize(base64_decode($fuente_array));
        if(!empty($fuente_array)){
            foreach($fuente_array as $i=>$fuente){        
                if($fuente['fuente']->type=='supercanal'){
                    $existe=red_servidor_fuente_existe_en_el_servidor($fuente['fuente'],$sql);
                    if(!empty($existe)){
                        $result['existe_array'][$fuente['fuente']->local_nid]=$existe;                    
                    }
                }
            }    
        }
    }
    print serialize($result);
    exit();
}