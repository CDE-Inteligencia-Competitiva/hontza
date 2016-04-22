<?php
function red_servidor_guardar_canal_hoja_multiple_callback(){
    if(isset($_POST['canal_enviar_multiple']) && !empty($_POST['canal_enviar_multiple'])){
        $canal_enviar_multiple=unserialize(base64_decode($_POST['canal_enviar_multiple']));
        if(!empty($canal_enviar_multiple)){
            foreach($canal_enviar_multiple as $i=>$value){
                $canal_enviar=unserialize(base64_decode($value));                
                $row=red_servidor_get_red_servidor_canal($canal_enviar);
                if(isset($row->id) && !empty($row->id)){
                    //
                }else{
                    red_servidor_canal_guardar_canal_enviar($canal_enviar);                    
                }              
            }
        }
    }
    //return t('Shared channels saved').' '.date('Y-m-d H:i:s');
    drupal_goto('red_publica/canales');
}
function red_servidor_canal_multiple_existe_en_el_servidor_callback(){
    $result=array();
    $result['ok']=0;
    $result['existe_array']=array();
    if(isset($_POST['canal_array']) && !empty($_POST['canal_array'])){
        $result['ok']=1;
        $canal_array=$_POST['canal_array'];
        $canal_array=base64_decode($canal_array);
        $canal_array=red_compartir_grupo_decrypt_text($canal_array);
        $canal_array=unserialize(base64_decode($canal_array));
        if(!empty($canal_array)){
            foreach($canal_array as $i=>$canal){        
                if(in_array($canal['canal']->type,array('canal_de_supercanal','canal_de_yql'))){
                    $existe=red_servidor_canal_existe_en_el_servidor($canal['canal'],$sql);
                    if(!empty($existe)){
                        $result['existe_array'][$canal['canal']->local_nid]=$existe;                    
                    }
                }
            }    
        }
    }
    print serialize($result);
    exit();
}