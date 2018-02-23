<?php
function diccionario_exportar_csv_callback(){
    //return 'Funcion desactivada';
    $output='';
    $data_csv_array=diccionario_define_data_csv_array();
    //exit();
    estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'locales');
    exit();
    /*$output=date('Y-m-d H:i:s');
    return $output;*/
}
function diccionario_define_data_csv_array(){   
    $data_csv_array=array();
    $languages_array=array('es','eu','fr','pt-pt');
    $data_csv_array[0]=array('lid','en','es','eu','fr','pt-pt');
    $i=1;
    $locales_source_array=diccionario_get_locales_source_array('');
    if(!empty($locales_source_array)){
        foreach($locales_source_array as $kont=>$row){
            $pos=strpos($row->source,'_');
            if($pos===FALSE){
               //             
            }else{
                continue;
            }
            $row_script=diccionario_drupal_locales_get_source_row($row->source);
            if(isset($row_script->source) && !empty($row_script->source)){    
                $row_script->uses=diccionario_drupal_locales_get_uses($row_script->lid);
                if(diccionario_drupal_locales_is_in_uses_by_modulo($row_script->uses,$existe,$module_name,$is_templates)){
                    $data_csv_array[$i][0]=$row->lid;                    
                    $data_csv_array[$i][1]=$row->source;
                    $data_csv_array[$i][2]='';
                    $data_csv_array[$i][3]='';
                    $data_csv_array[$i][4]='';
                    $data_csv_array[$i][5]='';
                    $locales_target_array=diccionario_get_locales_target_array($row->lid,'',1);
                    if(!empty($locales_target_array)){
                        foreach($locales_target_array as $k=>$r){
                            foreach($languages_array as $a=>$idioma){
                                if($r->language=='es'){
                                    $data_csv_array[$i][2]=$r->translation;
                                }else if($r->language=='eu'){
                                    $data_csv_array[$i][3]=$r->translation;
                                }else if($r->language=='fr'){
                                    $data_csv_array[$i][4]=$r->translation;
                                }else if($r->language=='pt-pt'){
                                    $data_csv_array[$i][5]=$r->translation;
                                }
                            }
                        }
                    }
                    if(empty($data_csv_array[$i][4]) || empty($data_csv_array[$i][5])){
                        //print $module_name.'='.$row->source.'<BR>';                    
                        $i++;
                    }else{
                        unset($data_csv_array[$i]);
                    }
                }    
            }    
        }
    }
    
    if(!empty($data_csv_array)){
        foreach($data_csv_array as $a=>$b){
            unset($b[3]);
            $new_row=array_values($b);
            $data_csv_array[$a]=$new_row;
        }
    }
    
    return $data_csv_array;
}    
function diccionario_guardar_para_traducir_callback(){
    //return 'Funcion desactivada';
    $output='';
    $data_csv_array=diccionario_define_data_csv_array();
    $data_csv_array=array_slice($data_csv_array,1);
    $table='para_traducir';
    if(hontza_is_sareko_id('ALERTA')){
        $table='para_traducir_alerta';
    }else if(red_is_servidor_central()){
        $table='para_traducir_red_alerta';
    }
    db_query('DELETE FROM {'.$table.'} WHERE 1');
    //        
    if(!empty($data_csv_array)){
        foreach($data_csv_array as $i=>$row){
            $en=$row[0];
            $es=$row[1];
            $eu=$row[2];
            $fr=$row[3];
            $pt_pt=$row[4];
            db_query('INSERT INTO {'.$table.'}(en,es,eu,fr,pt_pt) VALUES("%s","%s","%s","%s","%s")',$en,$es,$eu,$fr,$pt_pt);
        }
    }
    $output=date('Y-m-d H:i:s');
    return $output;
}
function diccionario_crear_para_traducir_todo_callback(){
    $traducir_alerta_array=diccionario_get_para_traducir_alerta_array();
    if(!empty($traducir_alerta_array)){
        foreach($traducir_alerta_array as $i=>$row){
            echo print_r($row,1);
            exit();
        }
    }
}
function diccionario_get_para_traducir_alerta_array(){
    $result=array();
    db_set_active('alerta');
    $res=db_query('SELECT * FROM {para_traducir_alerta} WHERE 1');
    db_set_active('default');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}