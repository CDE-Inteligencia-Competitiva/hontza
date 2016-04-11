<?php
function estrategia_resumen_preguntas_clave_canales_fila_canal_callback(){
    drupal_set_title(t('Table Questions - Channels'));
    boletin_report_no_group_selected_denied();
    $output='';
    $output.=estrategia_create_menu_resumen_preguntas_clave_canales();
    $output.=estrategia_create_menu_resumen_preguntas_clave_canales_para_ordenar();
    $output.='<div>'.estrategia_resumen_preguntas_clave_canales_volver_link().'&nbsp;|&nbsp;';
    $output.=l(t('Download csv'),'download_resumen_preguntas_clave_canales_fila_canal').'&nbsp;|&nbsp;';
    $output.=l(t('Print'),'imprimir_resumen_preguntas_clave_canales_fila_canal',array('attributes'=>array('target'=>'_blank'))).'&nbsp;|&nbsp;';
    $output.=l(t('Fullscreen'),'imprimir_resumen_preguntas_clave_canales_fila_canal/fullscreen',array('attributes'=>array('target'=>'_blank'))).'&nbsp;';
    $output.='</div>';
    $output.=estrategia_resumen_preguntas_clave_canales_mensaje_de_los_navegadores();
    $output.=estrategia_resumen_preguntas_clave_canales_fila_canal_html();
    return $output;    
}
function estrategia_resumen_preguntas_clave_canales_fila_canal_html(){
    $html=array();
    $html[]='<table>';
    $rows=informacion_get_array();
    if(!empty($rows)){
        $canales=estrategia_get_canales_del_grupo();        
        if(!empty($canales)){    
            $html[]=estrategia_resumen_preguntas_clave_canales_fila_canal_vertical($rows);
            foreach($canales as $i=>$canal){            
                $html[]='<tr>';            
                $html[]='<td style="white-space:nowrap;">';                
                $html[]=estrategia_set_title_una_linea_de_alto_fila_canal($canal);
                $html[]='</td>';
                if(!empty($rows)){
                    foreach($rows as $i=>$informacion){
                        $responde_array=informacion_get_canal_informacion_array($canal->nid,$informacion->nid);
                        $ekis='';
                        if(count($responde_array)>0){
                            $ekis='<abbr title="'.$canal->title.'<->'.$informacion->title.'">X</abbr>';                            
                        }
                        $html[]=estrategia_resumen_preguntas_clave_canales_set_td_ekis($ekis,$canal->title.'<->'.$informacion->title);
                    }
                }
                $html[]='</tr>';
            }
        }    
    }
    $html[]='</table>';
    return implode('',$html);
}
function estrategia_resumen_preguntas_clave_canales_fila_canal_vertical($informaciones){
    $html=array();
    $html[]='<tr>';
    $html[]='<th></th>';
    if(!empty($informaciones)){
        foreach($informaciones as $i=>$informacion){
            $html[]='<th><div class="vertical-text"><div class="vertical-text__inner">'.estrategia_set_title_una_linea_de_alto_columna_fila_canal($informacion).'</th>';                
        }
    }
    $html[]='</tr>';
    return implode('',$html);
}
function estrategia_set_title_una_linea_de_alto_columna_fila_canal($node){
    return estrategia_set_title_una_linea_de_alto_fila($node,$my_title,$my_value);
}
function estrategia_set_title_una_linea_de_alto_fila_canal($canal){
    $title=$canal->title;
    $title.='('.$canal->valor_estrategico.')';         
    return estrategia_set_title_una_linea_de_alto_columna($title);
}
function estrategia_download_resumen_preguntas_clave_canales_fila_canal_callback(){
    $data_csv_array=estrategia_create_resumen_preguntas_clave_canales_data_fila_canal_csv_array();
    estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array);
    exit();
}
function estrategia_create_resumen_preguntas_clave_canales_data_fila_canal_csv_array(){
    $result=array();
    $rows=informacion_get_array();
    if(!empty($rows)){
        $canales=estrategia_get_canales_del_grupo();        
        if(!empty($canales)){    
            $result[0]=estrategia_informacion_headers_csv($rows);
            $kont=1;
            foreach($canales as $i=>$canal){            
                $result[$kont][0]=$canal->title;
                $k=1;
                if(!empty($rows)){
                    foreach($rows as $i=>$informacion){
                        $responde_array=informacion_get_canal_informacion_array($canal->nid,$informacion->nid);
                        $ekis='';
                        if(count($responde_array)>0){
                            $ekis='X';
                        }
                        $result[$kont][$k]=$ekis;
                        $k++;
                    }
                }
                $kont++;
            }
        }    
    }
    return $result;
}
function estrategia_informacion_headers_csv($informaciones){
    $result=array();
    $result[]='';
    if(!empty($informaciones)){
        foreach($informaciones as $i=>$informacion){
            $result[]=$informacion->title;    
        }
    }
    return $result;
}
function estrategia_imprimir_resumen_preguntas_clave_canales_fila_canal_callback(){
    $output='';
    $output.=estrategia_resumen_preguntas_clave_canales_fila_canal_html();
    $is_print=estrategia_is_javascript_print_resumen_preguntas_clave_canales();
    print alerta_add_css($output,0,'imprimir_resumen_preguntas_clave_canales',$is_print);
    exit();
}