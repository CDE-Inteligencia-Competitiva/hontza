<?php
function estrategia_active_tabs_access(){
    //gemini-2014
    if(is_estrategia()){
    //    
        return hontza_grupos_active_access_tab('estrategia');
    }
    return 1;
}
function estrategia_node_form_alter(&$form,&$form_state,$form_id){
   $form['title']['#title']=t('Strategic challenge');
   $form['body_field']['body']['#title']=t('Details');
   $url_cancel='estrategias/arbol_estrategico';
   hontza_set_form_buttons($url_cancel,$form);
   //intelsat-2016
   red_copiar_estrategia_node_form_alter($form,$form_state,$form_id);
}
function estrategia_user_access_importar_strategia($nid){
   //gemini-2014
   //if(user_access('Importar estrategia')){
   if(user_access('Import strategy')){
        return 1;
   }
   return 0;
}
function estrategia_importar_form(){    
  $form = array();
  //simulando
  /*$form['my_msg']['#value']='Funcion desactivada';
  return $form;*/
  //
  
  boletin_report_no_group_selected_denied();
  $form['browser'] = array(
    '#type' => 'fieldset',
    '#title' => t('Browser Upload'),
    '#collapsible' => TRUE,
    '#description' => t("Upload a CSV file."),
  );
  //intelsat-2016
  $form['browser']['import_type'] = array(
    '#type' => 'select',
    '#title' => t('Type'),
    '#options'=>estrategia_inc_get_import_type_options(),
    '#default_value'=>2,  
  );
  //$file_size = t('Maximum file size: !size MB.', array('!size' => file_upload_max_size()));
  $file_size ='';
  $form['browser']['upload_file'] = array(
    '#type' => 'file',
    '#title' => t('CSV File'),
    '#size' => 40,
    '#description' => t('Select the CSV file to be upload.').' '.$file_size,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Upload CSV File'),
  );

  $form['#attributes']['enctype'] = "multipart/form-data";
  return $form;
}
function estrategia_importar_form_submit($form, &$form_state) {
    if(isset($_FILES) && !empty($_FILES)){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            if(isset($_FILES['files']['type']) && !empty($_FILES['files']['type']) && ($_FILES['files']['type']['upload_file']=='text/csv' || estrategia_importar_is_csv_by_name($_FILES['files']['name']['upload_file']))){
                $file_path='/tmp/'.$_FILES['files']['name']['upload_file'];
                move_uploaded_file($_FILES['files']['tmp_name']['upload_file'],$file_path);
                //intelsat-2016
                //estrategia_import_csv($file_path);                
                estrategia_import_csv($file_path,$form_state);        
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }        
}
//intelsat-2016
//function estrategia_import_csv($file_path){
function estrategia_import_csv($file_path,$form_state=''){
    $lineas=estrategia_get_lineas_csv($file_path);
    if(!empty($lineas)){
        //intelsat-2016
        //estrategia_crear_retos_csv($lineas);
        estrategia_crear_retos_csv($lineas,$form_state);
    }
}
function estrategia_get_lineas_csv($file_path,$find_in='',$is_importar_crm=0){
    //$is_utf8=estrategia_importar_is_utf8($file_path);
    $lineas=array();
    //intelsat-2015
    $find=";";
    $find=estrategia_inc_get_csv_character($file_path,$is_importar_crm);
    //intelsat-2016
    if(!empty($find_in)){
        $find=$find_in;
    }
    //
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $length=10000;
        //$campos=array();
        $i=0;
        $lineas=array();
        while (($data = fgetcsv($handle,$length,$find)) !== FALSE) {
        //while (($data = fgetcsv($handle,$length, "\t")) !== FALSE) {  
            if($i<1){
                //$campos=$data;
            }else{
                /*$num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $lineas[$i-1][$campos[$c]]=$data[$c];                    
                }*/
                if(!estrategia_is_empty_linea($data)){
                    $lineas[$i-1]=$data;
                }    
            }
            $i++;
        }
        fclose($handle);
    }else{
        drupal_set_message(t('fopen csv error'),'error');
    }
    return $lineas;
}
//intelsat-2016
//function estrategia_crear_retos_csv($lineas){
function estrategia_crear_retos_csv($lineas,$form_state='',$is_compartir_documentos=0){
    global $user;
    $my_grupo=og_get_group_context(); 
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(!empty($lineas)){
            //intelsat-2016
            if(estrategia_inc_is_replace($form_state)){
                estrategia_delete_all($my_grupo->nid);
                despliegue_delete_all($my_grupo->nid);
                decision_delete_all($my_grupo->nid);
                informacion_delete_all($my_grupo->nid);
                $title_array=array();
            }else{
                //$info_estrategia_all=estrategia_inc_get_info_estrategia_all($my_grupo->nid);            
                $title_array=estrategia_inc_get_grupo_all_title_array($my_grupo->nid);
            }
            foreach($lineas as $i=>$row){
                $values=array_values($row);
                $values=estrategia_trim_array($values);
                //echo print_r($values,1);exit();
                $title=trim($values[1]);
                if(!empty($title)){
                    if(!in_array($title,$title_array)){
                        $title_array[]=$title;
                        $node=new stdClass();
                        $node->title=$title;
                        $node->type='estrategia';
                        $node->uid=$user->uid;
                        $node->status=1;
                        $node->promote=1;
                        $fecha_cumplimiento_array=estrategia_create_fecha_array($values[0]);
                        if(!empty($fecha_cumplimiento_array)){
                            $node->fecha_cumplimiento=array();
                            $node->fecha_cumplimiento['year']=$fecha_cumplimiento_array[2];
                            $node->fecha_cumplimiento['month']=$fecha_cumplimiento_array[1];
                            $node->fecha_cumplimiento['day']=$fecha_cumplimiento_array[0];
                        }
                        $node->importancia_reto=$values[2];
                        $node->facilidad_reto=$values[3];
                        $node->grupo_seguimiento_nid=$my_grupo->nid;
                        $node->og_groups=array();
                        //$node->og_groups[$my_grupo->nid]=$my_grupo->nid;
                        //$node->og_groups_both[$my_grupo->nid]=$my_grupo->nid->title;
                        red_copiar_add_field_is_imported($node,$is_compartir_documentos);
                        node_save($node);
                        estrategia_crear_subreto_by_linea($node,$values);
                    }else{
                        $estrategia=estrategia_get_estrategia($title,$my_grupo->nid);
                        if(isset($estrategia->nid) && !empty($estrategia->nid)){
                            estrategia_crear_subreto_by_linea($estrategia,$values);
                        }
                    }
                }    
            }        
        }
    }    
}
function estrategia_create_fecha_array($fecha,$is_key_string=0){
    $result=array();
    $my_array=explode('/',$fecha);
    $num=count($my_array);
    if($num==3){
        //
    }else{
        $my_array=explode('-',$fecha);
        $num=count($my_array);
    }
    //
    if($num==3){
        if($is_key_string){
            $result['year']=(int) $my_array[0];
            $result['month']=(int) $my_array[1];
            $result['day']=(int) $my_array[2];
            return $result;
        }else{
            return $my_array;
        }    
    }
    return $result;
}
function estrategia_get_reto_all_nid_array(){
    $result=array();
    $rows=get_estrategia_arbol_rows();
    if(!empty($rows)){
        foreach($rows as $i=>$r){
            if(isset($r[2])){
                $nid=estrategia_get_nid_by_div_actions($r[2]);
                if(!empty($nid)){
                    $result[]=$nid;
                }
            }
        }
    }
    return $result;
}
function estrategia_limpiar_db(){
    boletin_report_no_group_selected_denied();
    $reto_nid_array=estrategia_get_reto_all_nid_array();
    $result=get_all_nodes(array('estrategia','despliegue','decision','informacion'));
    if(!empty($result)){
        foreach($result as $i=>$row){
            if(isset($row->nid) && !empty($row->nid)){
                if(in_array($row->nid,$reto_nid_array)){
                    //
                    //print 'nid(ok)='.$row->nid.'<BR>';
                }else{
                    node_delete($row->nid);
                    print 'nid(delete)='.$row->nid.'<BR>';
                }
            }
        }
    }
}
function estrategia_get_nid_by_div_actions($actions){
    $find='<a href="/fe/node/';
    $pos=strpos($actions,$find);
    if($pos===FALSE){
        //        
    }else{
        $s=substr($actions,$pos+strlen($find));
        $pos_delete=strpos($s,'/delete');
        if($pos_delete==FALSE){
            //
        }else{
            $nid=substr($s,0,$pos_delete);
            return $nid;
        }
    }
    return '';    
}
function estrategia_resumen_preguntas_clave_canales_callback(){
    drupal_set_title(t('Table Questions - Channels'));
    boletin_report_no_group_selected_denied();
    hontza_grupo_shared_active_tabs_access();
    $output='';
    //$output.=estrategia_resumen_preguntas_clave_canales_html();
    //$output.=estrategia_resumen_preguntas_clave_canales_html(1);
    $output.=estrategia_create_menu_resumen_preguntas_clave_canales();
    $output.=estrategia_create_menu_resumen_preguntas_clave_canales_para_ordenar();
    $output.='<div>'.estrategia_resumen_preguntas_clave_canales_volver_link().'&nbsp;|&nbsp;';
    $output.=l(t('Download csv'),'download_resumen_preguntas_clave_canales').'&nbsp;|&nbsp;';
    $output.=l(t('Print'),'imprimir_resumen_preguntas_clave_canales',array('attributes'=>array('target'=>'_blank'))).'&nbsp;|&nbsp;';
    $output.=l(t('Fullscreen'),'imprimir_resumen_preguntas_clave_canales/fullscreen',array('attributes'=>array('target'=>'_blank'))).'&nbsp;';
    $output.='</div>';
    $output.=estrategia_resumen_preguntas_clave_canales_mensaje_de_los_navegadores();
    $output.=estrategia_resumen_preguntas_clave_canales_html(2);
    return $output;    
}
function estrategia_get_canales_del_grupo(){
    $informacion_ordenada=informacion_get_array(0,1);    
    $result=array();    
    //
    $where=array();
    $where[]='1';
    $where[]='n.type IN("canal_de_supercanal","canal_de_yql")';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='oa.group_nid='.$my_grupo->nid;
    }else{
        return $result;
    }
    //
    $sql='SELECT n.* 
    FROM {node} n
    LEFT JOIN {og_ancestry} oa ON n.nid=oa.nid
    WHERE '.implode(' AND ',$where).'
    GROUP BY n.nid 
    ORDER BY n.title ASC';
    $res=db_query($sql);
    $kont=0;
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        $result[$kont]=$node;
        $result[$kont]->valor_estrategico=estrategia_get_canal_valor_estrategico($node);
        $valor_array=estrategia_get_canal_valor_estrategico_array($node,$informacion_ordenada);
        $result[$kont]->valor_estrategico_array=array_values($valor_array);
        $result[$kont]->valor_order_array=array_keys($valor_array);
        //echo print_r($result[$kont]->valor_estrategico_array,1);
        $kont++;
    }
    if(count($result)>0){
        //$result=array_ordenatu($result,'valor_estrategico','desc',1);
        $result=estrategia_ordenar_canales_by_valor_estrategico_array($result);
    }
    /*echo print_r($result,1);
    exit();*/
    return $result;
}
function estrategia_resumen_preguntas_clave_canales_vertical($canales,$vertical){
    $html=array();
    $html[]='<tr>';
    $html[]='<th></th>';
    if(!empty($canales)){
        foreach($canales as $i=>$canal){
            if(empty($vertical)){
                $html[]='<th><abbr title="'.$canal->title.'">'.($i+1).'</abbr></th>';
            }else if($vertical==1){
                $html[]='<th>'.$canal->title.'</th>';
            }else if($vertical==2){
                //$html[]='<th><div class="vertical-text"><div class="vertical-text__inner">'.estrategia_set_title_una_linea_de_alto_columna($canal->title).'</th>';
                $html[]='<th><div class="vertical-text"><div class="vertical-text__inner">'.estrategia_set_title_una_linea_de_alto_columna($canal->title).'('.$canal->valor_estrategico.')</th>';
            }    
        }
    }
    $html[]='</tr>';
    return implode('',$html);
}
function estrategia_get_canal_estrategia_array($canal_nid,$estrategia_nid){
    $result=array();
                        $where=array();
                        $where[]="1";
                        $where[]="nid=".$canal_nid;
                        $where[]="responde_estrategia_nid=".$estrategia_nid;
                        $sql="SELECT * FROM {canal_estrategia} WHERE ".implode(" AND ",$where)." ORDER BY id ASC";
                        
            $res=db_query($sql);
            
            while($row=db_fetch_object($res)){
                
                $result[]=$row;
            }
            return $result;
}
function estrategia_resumen_preguntas_clave_canales_html($vertical=0){
    $html=array();
    $html[]='<table>';
    $rows=informacion_get_array();

    if(!empty($rows)){
        $canales=estrategia_get_canales_del_grupo();        
        if(!empty($canales)){
            $html[]=estrategia_resumen_preguntas_clave_canales_vertical($canales,$vertical);
            foreach($rows as $i=>$informacion){            
                $html[]='<tr>';
                $s=estrategia_set_title_una_linea_de_alto_fila($informacion,$my_title,$my_value);
                /*$html[]='<td style="white-space:nowrap;" title="'.$my_title.'">';                
                $html[]=$my_value;*/
                $html[]='<td style="white-space:nowrap;">';                
                $html[]=$s;
                $html[]='</td>';
                if(!empty($canales)){
                    foreach($canales as $i=>$canal){
                        $responde_array=informacion_get_canal_informacion_array($canal->nid,$informacion->nid);
                        $ekis='';
                        if(count($responde_array)>0){
                            if(empty($vertical)){
                                $ekis='<abbr title="'.$canal->title.'">X</abbr>';
                            }else if($vertical==1){
                                $ekis='<abbr title="'.$informacion->title.'">X</abbr>';
                            }else if($vertical==2){
                                $ekis='<abbr title="'.$informacion->title.'<->'.$canal->title.'">X</abbr>';
                            }
                        }
                        $html[]=estrategia_resumen_preguntas_clave_canales_set_td_ekis($ekis,$informacion->title.'<->'.$canal->title);    
                    }
                }
                $html[]='</tr>';
            }
        }    
    }
    $html[]='</table>';
    return implode('',$html);
}
function estrategia_set_title_una_linea_de_alto_columna($title){
    $len=strlen($title);
    $max=estrategia_get_max_char_resumen_preguntas_clave_canales_canal();
    if($len<=$max){
        return $title;
        /*$result='<abbr title="'.$title.'">'.$title.'</abbr>';
        return $result;*/
    }else{
        $v=substr($title,0,$max).' ...';
        $result='<abbr title="'.$title.'">'.$v.'</abbr>';
        return $result;
    }
}
function estrategia_create_menu_resumen_preguntas_clave_canales(){
    global $user;
    /*$html=array();
    $html[]='<div style="float:left"><b>'.t('Change view').':'.'&nbsp;</b></div>';
    $html[]='<div class="tab-wrapper clearfix primary-only" style="margin-top:0;">';
    $html[]='<div class="tabs primary" id="tabs-primary-alerta_user">';    
    $html[]='<ul>';
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active('resumen_preguntas_clave_canales').'" id="li_resumen_preguntas_clave_canales">';
    $html[]=l(t('Channel').' - '.t('Key Question'),'resumen_preguntas_clave_canales');
    $html[]='</li>';
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active('resumen_preguntas_clave_canales_fila_canal').'" id="li_resumen_preguntas_clave_canales_fila_canal">';
    $html[]=l(t('Key Question').' - '.t('Channel'),'resumen_preguntas_clave_canales_fila_canal');
    $html[]='</li>';    
    $html[]='</ul>';
    $html[]='</div></div>';*/
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only" style="margin:0;">';
    $html[]='<div class="tabs primary" id="tabs-primary-alerta_user" style="width:100%;">';    
    $html[]='<ul style="width:100%;">';
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active('resumen_preguntas_clave_canales').'" id="li_resumen_preguntas_clave_canales" style="width:50%;">';
    //$html[]=l(t('Channel').' - '.t('Key Question'),'resumen_preguntas_clave_canales',array('attributes'=>array('style'=>'width:100%;padding-left:0px;padding-right:0px;')));
    $html[]=l(t('Channel').' - '.t('Key Question'),'resumen_preguntas_clave_canales',array('attributes'=>array('style'=>'width:100%;')));    
    $html[]='</li>';
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active('resumen_preguntas_clave_canales_fila_canal').'" id="li_resumen_preguntas_clave_canales_fila_canal" style="width:50%;border-right:none;">';
    //$html[]=l(t('Key Question').' - '.t('Channel'),'resumen_preguntas_clave_canales_fila_canal',array('attributes'=>array('style'=>'width:100%;padding-left:0px;padding-right:0px;')));
    $html[]=l(t('Key Question').' - '.t('Channel'),'resumen_preguntas_clave_canales_fila_canal',array('attributes'=>array('style'=>'width:100%;')));    
    $html[]='</li>';    
    $html[]='</ul>';
    $html[]='</div></div>';
    //
    return implode('',$html);
}
function estrategia_set_title_una_linea_de_alto_fila($node,&$text,&$v){
    $title=$node->title;
    $len=strlen($title);
    $max=estrategia_get_max_char_resumen_preguntas_clave_canales_informacion();
    if($len<=$max){
        $v=$title;
    }else{
        $v=substr($title,0,$max).' ...';        
    }
    $text=informacion_list_camino_text($node);
    $v.='('.$node->puntuacion_total.')';
    $result='<abbr title="'.$text.'">'.$v.'</abbr>';    
    return $result;
}
function estrategia_resumen_preguntas_clave_get_class_active($konp,$konp2='',$is_view_empty=0){
    $ok=0;
    $param0=arg(0);
    if($param0==$konp){
        if(empty($konp2)){
            $param1=arg(1);
            if($is_view_empty){
                if(empty($param1)){
                    $ok=1;
                }
            }else{
                $ok=1;
            }    
        }else{
            $param1=arg(1);
            if(!empty($param1) && $param1==$konp2){
                 $ok=1;
            }
        }
    }
    if($ok){
        return ' active';
    }
    return '';
}
function estrategia_get_max_char_resumen_preguntas_clave_canales_informacion(){
    return 45;
}
function estrategia_get_max_char_resumen_preguntas_clave_canales_canal(){
    return 45;
}
function estrategia_download_resumen_preguntas_clave_canales_callback(){
    $data_csv_array=estrategia_create_resumen_preguntas_clave_canales_data_csv_array();
    estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array);
    exit();
}
function estrategia_create_resumen_preguntas_clave_canales_data_csv_array(){
    $result=array();
    $rows=informacion_get_array();
    if(!empty($rows)){
        $canales=estrategia_get_canales_del_grupo();        
        if(!empty($canales)){    
            $result[0]=estrategia_resumen_preguntas_clave_canales_headers_csv($canales);
            $kont=1;
            foreach($rows as $i=>$informacion){            
                $result[$kont][0]=$informacion->title;
                $k=1;
                if(!empty($canales)){
                    foreach($canales as $i=>$canal){
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
function estrategia_resumen_preguntas_clave_canales_headers_csv($canales){
    $result=array();
    $result[]='';
    if(!empty($canales)){
        foreach($canales as $i=>$canal){
            $result[]=$canal->title;    
        }
    }
    return $result;
}
function estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,$prefijo='resumen',$sep=";"){
    $file = date('Ymd-His');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=$prefijo-$file.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    

    $output = fopen("php://output", "w");
    foreach ($data_csv_array as $val) {
        //fputcsv($output, $val,"\t");
        //fputcsv($output, $val,";");
        fputcsv($output, $val,$sep);
    }
    fclose($output);
    exit();
}
function estrategia_resumen_preguntas_clave_canales_mensaje_de_los_navegadores(){
    $html=array();
    /*$html[]='<p><i><b>'.t("This table is optimised to Firefox 26.0.").'</b></i></p>';
    $html[]='<p><i><b>'.t("If your browser doesn't support vertical displaying of column headings please download the csv file.").'</b></i></p>';*/
    $html[]='<p><i><b>'.t('If you have problems viewing this table, try another navigator (Firefox recommended) or download csv file').'.</b></i></p>';
    return implode('',$html);
}
function estrategia_is_download_resumen_preguntas_clave_canales(){
    $my_array=array('download_resumen_preguntas_clave_canales','download_resumen_preguntas_clave_canales_fila_canal');
    $param0=arg(0);
    if(!empty($param0) && in_array($param0,$my_array)){
        return 1;
    }
    return 0;
}
function estrategia_resumen_preguntas_clave_canales_set_td_ekis($ekis,$title){
    $result='';
    //if(estrategia_is_download_resumen_preguntas_clave_canales()){
    if(estrategia_resumen_preguntas_clave_canales_is_ekis_letra()){
        $result='<td><b>'.$ekis.'</b></td>';
    }else{
        if(empty($ekis)){
            $result='<td>'.$ekis.'</td>';
        }else{
            $bgcolor=estrategia_resumen_preguntas_clave_canales_get_ekis_color();
            $result='<td style="background-color:'.$bgcolor.';" title="'.estrategia_limpiar_comillas($title).'"></td>';
        }
    }
    return $result;
}
function estrategia_resumen_preguntas_clave_canales_get_ekis_color(){
    $result='#2E5753';
    //if(hontza_is_sareko_id('GESTION_CLAVES')){
    if(red_is_claves_activado()){    
        $result='#6380A5';
    }else if(red_is_rojo()){
        $result='#C6000B';        
    }
    return $result;
}
function estrategia_create_menu_resumen_preguntas_clave_canales_para_ordenar(){
    global $user;
    /*$param0=arg(0);
    $html=array();
    $html[]='<div style="float:left"><b>'.t('Order of Key Questions').':'.'&nbsp;</b></div>';
    $html[]='<div class="tab-wrapper clearfix primary-only" style="margin-top:0;">';
    $html[]='<div class="tabs primary" id="tabs-primary-alerta_user">';
    $html[]='<ul>';
    $url_pregunta_canales='resumen_preguntas_clave_canales';
    if(!empty($param0) && $param0=='resumen_preguntas_clave_canales_fila_canal'){
        $url_pregunta_canales='resumen_preguntas_clave_canales_fila_canal';
    }
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active($url_pregunta_canales,'',1).'" id="li_resumen_preguntas_clave_canales">';
    $html[]=l(t('By Hierarchy'),$url_pregunta_canales);
    $html[]='</li>';
    $html[]='<li class="alerta_user_menu_li'.estrategia_resumen_preguntas_clave_get_class_active($url_pregunta_canales,'order_importance').'" id="li_resumen_preguntas_clave_canales_fila_canal">';
    $html[]=l(t('By Score'),$url_pregunta_canales.'/order_importance');
    $html[]='</li>';    
    $html[]='</ul>';
    $html[]='</div></div>';*/
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only" style="margin-top:0;">';
    $html[]='<div class="tabs primary" id="tabs-primary-alerta_user" style="width:100%;">';
    $html[]='<ul style="width:100%;">';
    $url_canales_pregunta='resumen_preguntas_clave_canales';
    //$a_style='width:100%;padding-left:0px;padding-right:0px;margin-top:0;';
    $a_style='width:100%;margin-top:0;';
    $active=estrategia_resumen_preguntas_clave_get_class_active($url_canales_pregunta,'',1);
    $add_style='';
    /*if(!empty($active)){
        $add_style='padding-top:0px;';
    }*/
    $html[]='<li class="alerta_user_menu_li'.$active.'" id="li_resumen_preguntas_clave_canales" style="width:25%;">';
    $html[]=l(t('By Hierarchy'),$url_canales_pregunta,array('attributes'=>array('style'=>$a_style.$add_style)));
    $html[]='</li>';
    $active=estrategia_resumen_preguntas_clave_get_class_active($url_canales_pregunta,'order_importance');
    $add_style='';
    /*if(!empty($active)){
        $add_style='padding-top:0px;';
    }*/
    $html[]='<li class="alerta_user_menu_li'.$active.'" id="li_resumen_preguntas_clave_canales_fila_canal" style="width:25%;border-right:none;">';
    $html[]=l(t('By Score'),$url_canales_pregunta.'/order_importance',array('attributes'=>array('style'=>$a_style.$add_style)));
    $html[]='</li>';    
    //
    $url_pregunta_canales='resumen_preguntas_clave_canales_fila_canal';
    $active=estrategia_resumen_preguntas_clave_get_class_active($url_pregunta_canales,'',1);
    $add_style='';
    /*if(!empty($active)){
        $add_style='padding-top:0px;';
    }*/    
    $html[]='<li class="alerta_user_menu_li'.$active.'" id="li_resumen_preguntas_clave_canales" style="width:25%;">';
    $html[]=l(t('By Hierarchy'),$url_pregunta_canales,array('attributes'=>array('style'=>$a_style.$add_style)));
    $html[]='</li>';
    
    $active=estrategia_resumen_preguntas_clave_get_class_active($url_pregunta_canales,'order_importance');
    $add_style='';
    /*if(!empty($active)){
        $add_style='padding-top:0px;';
    }*/
    $html[]='<li class="alerta_user_menu_li'.$active.'" id="li_resumen_preguntas_clave_canales_fila_canal" style="width:25%;border-right:none;">';
    $html[]=l(t('By Score'),$url_pregunta_canales.'/order_importance',array('attributes'=>array('style'=>$a_style.$add_style)));
    $html[]='</li>';
    //
    $html[]='</ul>';
    $html[]='</div></div>';    
    //
    return implode('',$html);
}
function estrategia_resumen_preguntas_clave_canales_is_order_importance(){
    $param0=arg(0);
    $my_array=array('resumen_preguntas_clave_canales','resumen_preguntas_clave_canales_fila_canal');
    if(!empty($param0) && in_array($param0,$my_array)){
        $param1=arg(1);
        if(!empty($param1) && $param1=='order_importance'){
            return 1;
        }    
    }
    return 0;
}
function estrategia_is_resumen_preguntas_clave_canales_pantalla(){
    $param0=arg(0);
    $my_array=array('resumen_preguntas_clave_canales','resumen_preguntas_clave_canales_fila_canal');
    if(!empty($param0) && in_array($param0,$my_array)){
        return 1;
    }
    return 0;
}
function estrategia_resumen_preguntas_clave_canales_volver_link(){
    $volver='estrategias/arbol_estrategico';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $volver=$_REQUEST['destination'];
    }
    return l(t('Return'),$volver,array('attributes'=>array('class'=>'back_resumen')));
}
function estrategia_define_fecha_cumplimiento($node='',$field='fecha_cumplimiento'){
    if(isset($node->nid) && !empty($node->nid)){
        return $node->$field;
    }else{
        $fecha=date("Y-m-d", strtotime("+6 months"));
        $result=estrategia_create_fecha_array($fecha,1);
        return $result;
    }
}
function estrategia_update_fecha_controles_callback(){
    $param2=arg(2);
    if(empty($param2)){
        return l(t('Update all control dates'),'estrategia/update_fecha_controles/confirm');
    }else{
        estrategia_update_fecha_controles_confirm();
        return 'Updated '.date('Y-m-d H:i:s');
    }    
}
function estrategia_update_fecha_controles_confirm(){
    $estrategia_array=estrategia_get_all();
    if(!empty($estrategia_array)){
        foreach($estrategia_array as $i=>$row){
            $fecha_cumplimiento=strtotime('2014-6-1');
            db_query('UPDATE {estrategia} SET fecha_cumplimiento=%d WHERE nid=%d AND vid=%d',$fecha_cumplimiento,$row->nid,$row->vid);
        }
    }
    //
    $despliegue_array=despliegue_get_all();
    if(!empty($despliegue_array)){
        foreach($despliegue_array as $i=>$row){
            $fecha_cumplimiento=strtotime('2014-6-1');
            db_query('UPDATE {despliegue} SET fecha_cumplimiento=%d WHERE nid=%d AND vid=%d',$fecha_cumplimiento,$row->nid,$row->vid);
        }        
    }
    $decision_array=decision_get_all();
    if(!empty($decision_array)){
        foreach($decision_array as $i=>$row){
            $fecha_cumplimiento=strtotime('2014-6-1');
            db_query('UPDATE {decision} SET fecha_cumplimiento=%d WHERE nid=%d AND vid=%d',$fecha_cumplimiento,$row->nid,$row->vid);
        }        
    }
}
function estrategia_get_all(){
    $result=array();
    $sql='SELECT * FROM {estrategia} e WHERE 1';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function estrategia_imprimir_resumen_preguntas_clave_canales_callback(){
    $output='';
    $output.=estrategia_resumen_preguntas_clave_canales_html(2);
    $is_print=estrategia_is_javascript_print_resumen_preguntas_clave_canales();
    print alerta_add_css($output,0,'imprimir_resumen_preguntas_clave_canales',$is_print);
    exit();
}
function estrategia_resumen_preguntas_clave_canales_is_ekis_letra(){
    if(estrategia_is_download_resumen_preguntas_clave_canales()){
        return 1;
    }
    $my_array=array('imprimir_resumen_preguntas_clave_canales','imprimir_resumen_preguntas_clave_canales_fila_canal');
    $param0=arg(0);
    if(!empty($param0) && in_array($param0,$my_array)){
        $param1=arg(1);
        if(empty($param1)){
            return 1;
        }else if($param1=='fullscreen'){
            return 0;
        }    
    }
    return 0;
}    
function estrategia_is_javascript_print_resumen_preguntas_clave_canales(){
    $param1=arg(1);
    if(empty($param1)){
        return 1;
    }else if($param1=='fullscreen'){
        return 0;
    }    
    return 1;
}
function estrategia_limpiar_comillas($title){
    $result=$title;
    $result=str_replace('"','',$result);
    return $result;
}
function estrategia_get_canal_valor_estrategico($node){
    $result=0;
    $responde_array=informacion_get_canal_informacion_array($node->nid,$informacion_nid='');
    if(!empty($responde_array)){
        foreach($responde_array as $i=>$row){
            if(isset($row->responde_informacion_nid) && !empty($row->responde_informacion_nid)){
                $informacion=node_load($row->responde_informacion_nid);
                if(isset($informacion->nid) && !empty($informacion->nid)){
                    if(isset($informacion->puntuacion_total) && !empty($informacion->puntuacion_total)){
                        if($informacion->puntuacion_total>$result){
                           $result=$informacion->puntuacion_total; 
                        }
                    }
                }
            }
        }
    }
    return $result;
}
function estrategia_get_canal_valor_estrategico_array($node,$informacion_ordenada){
    $result=array();
    $responde_array=informacion_get_canal_informacion_array($node->nid,$informacion_nid='');
    if(!empty($responde_array)){
        foreach($responde_array as $i=>$row){
            if(isset($row->responde_informacion_nid) && !empty($row->responde_informacion_nid)){
                $informacion=node_load($row->responde_informacion_nid);
                if(isset($informacion->nid) && !empty($informacion->nid)){
                    if(isset($informacion->puntuacion_total) && !empty($informacion->puntuacion_total)){
                        //if($informacion->puntuacion_total>$result){
                            $pos=informacion_get_pos($informacion_ordenada,$informacion->nid);
                            $result[$pos]=$informacion->puntuacion_total;
                           
                        //}
                    }
                }
            }
        }
    }
    asort($result,SORT_NUMERIC);
    $result=array_reverse($result,TRUE);
    $result=estrategia_set_igual_pos($result);
    return $result;
}
function estrategia_ordenar_canales_by_valor_estrategico_array($result_in){
    $max=array();
    $temp_array=$result_in;
    foreach($result_in as $i=>$row){       
        $max=estrategia_get_max_valor_estrategico_array($temp_array);
        $result[]=$max;
    }
    return $result;
}
function estrategia_get_max_valor_estrategico_array(&$temp_array){
    $my_array=$temp_array;
    $max=$my_array[0];
    if(!empty($my_array)){
        foreach($my_array as $i=>$row){
            /*if($row->title){
                echo print_r($row->valor_estrategico_array,1);
            }*/
            if($i>0){
                if(!empty($row->valor_estrategico_array) && isset($row->valor_estrategico_array[0])){
                    foreach($row->valor_estrategico_array as $k=>$v){
                        if(isset($max->valor_estrategico_array[$k]) && !empty($max->valor_estrategico_array[$k])){
                            if($max->valor_estrategico_array[$k]<$v){
                               $max=$row;
                               break;
                            }else if($max->valor_estrategico_array[$k]==$v){   
                               if($max->valor_order_array[$k]>$row->valor_order_array[$k]){
                                    $max=$row;
                                    break;
                               }else if($max->valor_order_array[$k]==$row->valor_order_array[$k]){     
                                    /*$num_max=count($max->valor_order_array);
                                    $num_row=count($row->valor_order_array);
                                    if($num_max==$num_row){
                                        $max=$row;
                                        break;
                                    }*/
                               }else{
                                    break;
                               }
                            }else{
                                break;
                            }
                        }else{
                            $max=$row;
                            break;
                        }
                    }
                }
            }
        }
    }
    
    $temp_array=estrategia_unset_max($max,$temp_array);
    /*
    if($max->title=='BizNar-ATL'){
        print '##########################INI#################################<BR>';    
        foreach($temp_array as $a=>$b){
            echo 'valor='.print_r($b->valor_estrategico_array,1);
            echo 'order='.print_r($b->valor_order_array,1);
        }
        echo 'max===='.print_r($max->valor_estrategico_array,1);
        echo 'max===='.print_r($max->valor_order_array,1);
        print '##########################END#################################<BR>';
    }*/
    return $max;
}
function estrategia_unset_max($max,$temp_array){
    $result=array();
    if(!empty($temp_array)){
        foreach($temp_array as $i=>$row){
            if(!($row->vid==$max->vid && $row->nid==$max->nid)){
               $result[]=$row;
            }
        }
    }
    return $result;
}
function estrategia_define_key_questions_botones(){
    $html=array();
    $html[]='<div>';
    //$links[]=l(t('Download csv'),'estrategias/tabla_csv_download',array('attributes'=>array('target'=>'_blank')));    
    //$links[]=l(t('Return'),'estrategias',array('attributes'=>array('class'=>'back_resumen')));
    $links[]=l(t('Return'),'estrategias/arbol_estrategico',array('attributes'=>array('class'=>'back_resumen')));
    $html[]=implode('&nbsp;|',$links);
    $html[]='</div>';
    return implode('',$html);
}
function estrategia_tabla_csv_download_callback(){
    //intelsat-2016
    estrategia_tabla_csv_download();
}
function estrategia_tabla_strip_tags($tabla){
    $result=array();
    if(!empty($tabla)){    
        foreach($tabla as $i=>$row){
            if(!empty($row)){
                foreach($row as $k=>$v){
                    $result[$i][$k]=strip_tags($v);
                }
            }
        }
    }
    return $result;
}
function estrategia_tabla_define_headers($is_tabla_csv_download=0){
    $headers=array();
    if($is_tabla_csv_download){
        $headers[]=t('Control Date');   
    }
    $headers[]=t('Challenge');    
    //intelsat-2015
    if($is_tabla_csv_download){
        $headers[]=t('Importance');
        $headers[]=t('Feasibility');
        $headers[]=t('Control Date');   
    }else{
        $headers[]=t('Value');        
    }
    //
    $headers[]=t('SubChallenge');
    $headers[]=t('Value');
    $headers[]=t('Decision');
    $headers[]=t('Value');
    $headers[]=t('Key Question');
    $headers[]=t('Importance');
    $headers[]=t('Accessibility');
    $headers[]=t('Score');
    $headers[]=t('Ranking');
    return $headers;
}
function estrategia_set_igual_pos($result_in){
    if(!empty($result_in)){
        $my_array=array();
        foreach($result_in as $pos=>$v){
            if(!isset($my_array[$v])){
                $my_array[$v]=array();
                $my_array[$v][]=$pos;
            }else{
                $my_array[$v][]=$pos;
            }
        }
        //
        $result=array();
        if(!empty($my_array)){
            foreach($my_array as $value=>$pos_array){
                sort($pos_array);
                foreach($pos_array as $k=>$p){
                    $result[$p]=$value;
                }
            }
        }
        return $result;
    }
    return $result_in;
}
function estrategia_get_despliegue_estrategico_info(){
    $result=array();
    $rows=get_estrategia_arbol_rows(0);
    return estrategia_create_arbol_despliegue_estrategico($rows);
}
function estrategia_create_arbol_despliegue_estrategico($my_list){
        $arbol=array();
        $result_informacion_list=array();
 	if(count($my_list)>0){
		$kont=0;
		foreach($my_list as $i=>$node){
                    if(!is_reto_del_grupo($node->grupo_seguimiento_nid)){                        
                        continue;
                    }
                    $arbol[$kont]['estrategia_nid']=$node->nid;
                    $despliegue_list=get_estrategia_despliegue_list($node->nid);
                    if(count($despliegue_list)>0){
                        foreach($despliegue_list as $k=>$despliegue_row){
                            $arbol[$kont]['despliegue_list'][$k]['despliegue_nid']=$despliegue_row->nid;
                            $decision_list=get_despliegue_decision_list($despliegue_row->nid);
                            if(count($decision_list)>0){
                                foreach($decision_list as $a=>$decision_row){
                                    $arbol[$kont]['despliegue_list'][$k]['decision_list'][$a]['decision_nid']=$decision_row->nid;
                                    $informacion_list=get_decision_informacion_list($decision_row->nid);
                                    if(count($informacion_list)>0){
                                        foreach($informacion_list as $b=>$informacion_row){
                                            $arbol[$kont]['despliegue_list'][$k]['decision_list'][$a]['informacion_list'][$b]=$informacion_row->nid;
                                            $result_informacion_list[]=$informacion_row->nid;
                                        }
                                    }    
                                }
                            }    
                        }
                    }
                    $kont++;
                }
        }
        //print count($informacion_list);exit();
        $result=array();
        $result['arbol']=$arbol;
        $result['informacion_list']=$result_informacion_list;
        return $result;
}
function estrategia_get_fecha_control($node){
    return date('d-m-Y',my_mktime($node->fecha_cumplimiento));
}
function estrategia_set_title_max_len($title_in){
    $result=$title_in;
    $my_array=explode(" ",$result);
    if(count($my_array)>1){
        return $result;
    }
    //
    $len=strlen($result);
    $max_len=44;
    if($len>$max_len){
        $result=substr($result,0,$max_len).' ...';
    }
    return $result;
}
//gemini-2014
function estrategia_delete_all($grupo_nid){
    $estrategia_array=estrategia_get_grupo_all($grupo_nid);
    if(!empty($estrategia_array)){
        foreach($estrategia_array as $i=>$row){
            node_delete($row->nid);
        }
    }
}
//gemini-2014
function estrategia_get_grupo_all($grupo_nid){
    $result=array();
    if(!empty($grupo_nid)){
        $where=array();
        $where[]='1';
        //$where[]='og_ancestry.group_nid='.$grupo_nid;
        //$where[]='(og_ancestry.group_nid='.$grupo_nid.' OR e.grupo_seguimiento_nid='.$grupo_nid.')';
        //$where[]='e.grupo_seguimiento_nid='.$grupo_nid;
        $where[]='(e.grupo_nid='.$grupo_nid.' OR e.grupo_seguimiento_nid='.$grupo_nid.')';
        $where[]='n.type="estrategia"';
        /*$sql='SELECT n.* 
        FROM {node} n 
        LEFT JOIN {estrategia} e ON n.vid=e.vid
        LEFT JOIN {og_ancestry} og_ancestry ON n.nid=og_ancestry.nid 
        WHERE '.implode(' AND ',$where);*/
        $sql='SELECT n.* 
        FROM {node} n 
        LEFT JOIN {estrategia} e ON n.vid=e.vid
        WHERE '.implode(' AND ',$where);
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
    }
    return $result;
}
//gemini-2014
function estrategia_crear_subreto_by_linea($estrategia,$values){
    global $user;
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                //intelsat-2015
                //$title=trim($values[4]);
                $title=trim($values[5]);
                //
                if(!empty($title)){
                    if(!despliegue_exist($title,$my_grupo->nid,$estrategia,$despliegue)){
                        $node=new stdClass();
                        $node->title=$title;
                        $node->type='despliegue';
                        $node->uid=$user->uid;
                        $node->status=1;
                        $node->promote=1;
                        $node->fecha_cumplimiento=$estrategia->fecha_cumplimiento;
                        //intelsat-2015
                        //$node->importancia_despliegue=$values[5];
                        $node->importancia_despliegue=$values[6];                        
                        //
                        $node->grupo_seguimiento_nid=$estrategia->grupo_seguimiento_nid;
                        $f=$estrategia->nid;
                        $node->$f=1;
                        /*echo print_r($node,1);
                        exit();*/
                        //$node->og_groups[$my_grupo->nid]=$my_grupo->nid;
                        //$node->og_groups_both[$my_grupo->nid]=$my_grupo->nid->title;
                        node_save($node);
                        estrategia_crear_decision_by_linea($node,$values,$estrategia);
                    }else{
                        estrategia_crear_decision_by_linea($despliegue,$values,$estrategia);
                    }
                }    
    }    
}
//gemini-2014
function estrategia_trim_array($values){
    $result=$values;
    if(!empty($result)){
        foreach($result as $i=>$v){
            $result[$i]=trim($v);
        }
    }
    return $result;
}
//gemini-2014
function estrategia_get_estrategia($title,$grupo_nid){
    $where=array();
    $where[]='1';
    $where[]='e.grupo_nid='.$grupo_nid;
    $where[]='n.type="estrategia"';
    $where[]='n.title="'.$title.'"';
    $sql='SELECT n.* 
    FROM {node} n 
    LEFT JOIN {estrategia} e ON n.vid=e.vid 
    WHERE '.implode(' AND ',$where);
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $node=node_load($row->nid);
        return $node;
    }
    $my_result=new stdClass();
    return $my_result;
}
//gemini-2014
function estrategia_crear_decision_by_linea($despliegue,$values){
    global $user;
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        //echo print_r($values,1);exit();
                //intelsat-2015
                //$title=trim($values[6]);
                $title=trim($values[7]);
                //
                if(!empty($title)){
                    if(!decision_exist($title,$my_grupo->nid,$despliegue,$decision)){
                        $node=new stdClass();
                        $node->title=$title;
                        $node->type='decision';
                        $node->uid=$user->uid;
                        $node->status=1;
                        $node->promote=1;
                        $node->fecha_cumplimiento=$despliegue->fecha_cumplimiento;
                        //intelsat-2015
                        //$node->valor_decision=$values[7];
                        $node->valor_decision=$values[8];
                        //
                        if(isset($despliegue->grupo_seguimiento_nid)){
                            $node->grupo_seguimiento_nid=$despliegue->grupo_seguimiento_nid;
                        }    
                        $f=$despliegue->nid;
                        $node->$f=1;
                        node_save($node);
                        estrategia_crear_informacion_by_linea($node,$values);
                    }else{
                        estrategia_crear_informacion_by_linea($decision,$values);
                    }
                }    
    }    
}
//gemini-2014
function estrategia_crear_informacion_by_linea($decision,$values){
    global $user;
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        //echo print_r($values,1);exit();
                //intelsat-2015
                //$title=trim($values[8]);
                $title=trim($values[9]);
                //
                if(!empty($title)){
                    if(!informacion_exist($title,$my_grupo->nid,$decision,$informacion)){
                        $node=new stdClass();
                        $node->title=$title;
                        $node->type='informacion';
                        $node->uid=$user->uid;
                        $node->status=1;
                        $node->promote=1;
                        $node->fecha_cumplimiento=$decision->fecha_cumplimiento;
                        //intelsat-2015
                        //$node->importancia=$values[9];
                        //$node->accesibilidad=$values[10];
                        $node->importancia=$values[10];
                        $node->accesibilidad=$values[11];                        
                        //
                        if(isset($decision->grupo_seguimiento_nid)){
                            $node->grupo_seguimiento_nid=$decision->grupo_seguimiento_nid;
                        }
                        $f=$decision->nid;
                        $node->$f=1;
                        node_save($node);                    
                    }/*else{
                        print 'existe informacion===='.$title;exit();
                    }*/
                }    
    }    
}
function estrategia_is_empty_linea($data_in){
    $num_values=estrategia_importar_define_num_values();    
    $data=array_slice($data_in,0,$num_values);
    foreach($data as $i=>$value){
        $v=trim($value);
        if(!empty($v)){
            return 0;
        }
    }
    return 1;
}
function estrategia_importar_define_num_values(){
    return 11;
}
function estrategia_importar_is_utf8($file_path){
    $content=file_get_contents($file_path);
    print mb_detect_encoding($content);exit();
}
function estrategia_importar_is_csv_by_name($filename){
    $info=pathinfo($filename);
    if(isset($info['extension']) && !empty($info['extension'])){
        if($info['extension']=='csv'){
            return 1;
        }
    }
    return 0;
}
//gemini-2014
function estrategia_set_preguntas_clave_arbol($arbol){
    $result=array();
    $kont=0;
    if(!empty($arbol)){
        foreach($arbol as $i=>$row){
            $pos=strpos($row[0],'informacion.png');
            if($pos===FALSE){
                continue;
            }else{
                $result[$kont]=$row;
                $result[$kont][0]=str_replace('<div style="padding-left:150px">','<div style="padding-left:0px">',$row[0]);
                $kont++;
            }
        }
    }
    return $result;
}
//gemini-2014
function estrategia_importar_estrategia_access_callback(){
    //if(hontza_grupos_mi_grupo_in_grupo()){
        if(is_administrador_grupo(1)){
            return TRUE;
        }
    //}
    return FALSE;
}
//gemini-2014
function estrategia_get_simbolo_style($is_taula_header=0){
    $style='';
    if($is_taula_header){
        //intelsat-2016
        //$padding_right=8;
        $padding_right=5;
        $style=' style="vertical-align:middle;padding-right:'.$padding_right.'px;"';
    }
    return $style;
}
//gemini-2014
function estrategia_is_fecha_cumplimiento_cero($fecha_array){
    if(isset($fecha_array['year']) && !empty($fecha_array['year']) && $fecha_array['year']==1970){
        if(isset($fecha_array['month']) && !empty($fecha_array['month']) && $fecha_array['month']==1){
            if(isset($fecha_array['day']) && !empty($fecha_array['day']) && $fecha_array['day']==1){
                return 1;
            }
        }
    }
    return 0;
}
//gemini-2014
function estrategia_is_arbol_sin_link(){
    if(is_crear_canal_de_supercanal()){
        return 1;        
    }
    if(is_crear_canal_filtro_rss(0,0)){
        return 1;
    }
    if(is_node_add() || hontza_is_node_edit()){
        $node=my_get_node();
        if(isset($node->type) && !empty($node->type) && in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
            return 1;
        }
    }
    return 0;
}
//gemini-2014
function estrategia_get_arbol_div_title($description){    
    if(estrategia_is_arbol_sin_link()){
        $title=strip_tags($description);
        if(!empty($title)){
            return ' title="'.$title.'"';
        }
    }
    return '';
}
function estrategia_inc_add_no_control_date_js(){
    $js='$(document).ready(function()
			{
			    $("#edit-no-control-date").change(function(){
                               if($(this).attr("checked")){
                                   estrategia_inc_set_control_date_enabled(true);
                               }else{
                                   estrategia_inc_set_control_date_enabled(false);
                               }
                            });
                            function estrategia_inc_set_control_date_enabled(my_value){
                                $("#edit-fecha-cumplimiento-year").attr("disabled",my_value);
                                $("#edit-fecha-cumplimiento-month").attr("disabled",my_value);
                                $("#edit-fecha-cumplimiento-day").attr("disabled",my_value);
                            }
			});';

			drupal_add_js($js,'inline');
}
function estrategia_inc_custom_date_element($element) {
  // Default to current date
  if (empty($element['#value'])) {
    $element['#value'] = array('day' => format_date(time(), 'custom', 'j'),
                            'month' => format_date(time(), 'custom', 'n'),
                            'year' => format_date(time(), 'custom', 'Y'));
  }

  $element['#tree'] = TRUE;

  // Determine the order of day, month, year in the site's chosen date format.
  $format = variable_get('date_format_short', 'm/d/Y - H:i');
  $sort = array();
  $sort['day'] = max(strpos($format, 'd'), strpos($format, 'j'));
  $sort['month'] = max(strpos($format, 'm'), strpos($format, 'M'));
  $sort['year'] = strpos($format, 'Y');
  asort($sort);
  $order = array_keys($sort);

  // Output multi-selector for date.
  foreach ($order as $type) {
    switch ($type) {
      case 'day':
        $options = drupal_map_assoc(range(1, 31));
        break;
      case 'month':
        $options = drupal_map_assoc(range(1, 12), 'map_month');
        break;
      case 'year':
        $start_year = isset($element['#start_year']) ? $element['#start_year'] : 1900;
        $end_year = isset($element['#end_year']) ? $element['#end_year'] : format_date(time(), 'custom', 'Y');
        $options = drupal_map_assoc(range($start_year, $end_year));
        break;
    }
    $parents = $element['#parents'];
    $parents[] = $type;
    $element[$type] = array(
      '#type' => 'select',
      '#value' => $element['#value'][$type],
      '#attributes' => $element['#attributes'],
      '#options' => $options,
    );
  }
  return $element;
}
function estrategia_inc_define_fecha_cumplimiento_start_year(){
    return 2009;
}
function estrategia_inc_define_fecha_cumplimiento_end_year(){
    return format_date(time(), 'custom', 'Y')+4;
}
function estrategia_inc_define_no_control_date_prefix(){
    return '<div style="float:left;padding-left:10px;">';
}
function estrategia_inc_define_control_date_fs($fecha_cumplimiento,$no_control_date,&$form){

$form['control_date_fs']=array(
    '#type'=>'fieldset',
    '#title' => t('Control Date'),
    '#prefix'=>'<div style="float:left;width:100%;">',
    '#suffix'=>'</div>',   
);
    
$form['control_date_fs']['fecha_cumplimiento'] = array(
    '#type' => 'date',
    //'#title' => t('Control Date'),
    '#default_value' =>$fecha_cumplimiento,
    '#process' => array('estrategia_inc_custom_date_element'),
    '#start_year' => estrategia_inc_define_fecha_cumplimiento_start_year(),
    '#end_year' => format_date(time(), 'custom', 'Y')+4,
    '#prefix'=>'<div style="float:left;">',
    '#suffix'=>'</div>', 
  );
//gemini-2014
$form['control_date_fs']['no_control_date'] = array(
    '#type' => 'checkbox',
    '#title' => t('No Control Date'),
    '#prefix'=>estrategia_inc_define_no_control_date_prefix(),
    '#suffix'=>'</div>',   
  );
    if(!empty($no_control_date)){
        $form['control_date_fs']['no_control_date']['#attributes']['checked']='checked';
        $form['control_date_fs']['fecha_cumplimiento']['#attributes']['disabled']='disabled';
    }
    estrategia_inc_add_no_control_date_js();
}
//intelsat-2015
function estrategia_inc_set_fecha_by_language($fecha){
    global $language;
    if($language->language=='es'){
        $time=strtotime($fecha);
        return date('d-m-Y',$time);
    }
    return $fecha;
}
//intelsat-2015
function estrategia_inc_is_estrategia_main(){
    $param0=arg(0);
    if(!empty($param0)){
        if(in_array($param0,array('resumen_preguntas_clave_canales','resumen_preguntas_clave_canales_fila_canal'))){
            return 1;
        }
        if(in_array($param0,array('estrategia','estrategias'))){    
            $param1=arg(1);
            if(!empty($param1)){
                $my_array=array('importar_estrategia','arbol_estrategico','tabla','tabla_puntuacion_total');
                if(!empty($my_array)){
                    foreach($my_array as $i=>$value){
                        if(is_estrategia($value)){
                            return 1;
                        }
                    }
                }
            }
        }    
    }    
    return 0;    
}
//intelsat-2015
function estrategia_inc_get_title_estrategia_main_simbolo_img(){
   $result=my_get_icono_action('estrategia_main32', $title,'estrategia_main32');
   return $result;
}
//intelsat-2015
function estrategia_inc_on_estrategia_responde_save($op,&$node){
    $is_form_save=0;
    $node->estrategia_responde_array=hontza_canal_get_request_responde_array($is_form_save);
    if($is_form_save){
        if($node->type=='noticia'){
            red_noticia_de_usuario_save_reto_al_que_responde($node);
        }else if($node->type=='debate'){
            estrategia_inc_debate_save_reto_al_que_responde($node);
        }else if($node->type=='wiki'){
            estrategia_inc_wiki_save_reto_al_que_responde($node);
        }else if($node->type=='canal_usuario'){
            canal_usuario_save_reto_al_que_responde($node);
        }     
    }
}
//intelsat-2015
function estrategia_inc_is_reto_responde_formulario(){
    if(hontza_is_canal_formulario() || red_is_item_formulario() || red_is_noticia_de_usuario_formulario()){
        return 1;
    }
    if(hontza_canal_rss_is_debate_formulario() || hontza_canal_rss_is_wiki_formulario()){
        return 1;
    }
    //intelsat-2015
    if(module_exists('canal_usuario')){
        if(canal_usuario_is_canal_usuario_formulario()){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function estrategia_inc_debate_save_reto_al_que_responde($node,$is_delete=1){
    if($is_delete){
        estrategia_inc_debate_delete_reto_al_que_responde($node->nid,$node->vid);
    }
    if(db_table_exists('debate_estrategia')){
        if(isset($node->estrategia_responde_array) && !empty($node->estrategia_responde_array)){
            foreach($node->estrategia_responde_array as $i=>$row){
                db_query($sql=sprintf('INSERT INTO {debate_estrategia}(nid,vid,responde_estrategia_nid,responde_despliegue_nid,responde_decision_nid,responde_informacion_nid) VALUES(%d,%d,%d,%d,%d,%d)',$node->nid,$node->vid,$row['responde_estrategia_nid'],$row['responde_despliegue_nid'],$row['responde_decision_nid'],$row['responde_informacion_nid']));
            }         
        }
    }    
}
//intelsat-2015
function estrategia_inc_debate_delete_reto_al_que_responde($nid,$vid){
    if(db_table_exists('debate_estrategia')){
        db_query('DELETE FROM {debate_estrategia} WHERE nid=%d AND vid=%d',$nid,$vid);
    }    
}
//intelsat-2015
function estrategia_inc_wiki_save_reto_al_que_responde($node,$is_delete=1){
    if($is_delete){
        estrategia_inc_wiki_delete_reto_al_que_responde($node->nid,$node->vid);
    }
    if(db_table_exists('collaboration_estrategia')){
        if(isset($node->estrategia_responde_array) && !empty($node->estrategia_responde_array)){
            foreach($node->estrategia_responde_array as $i=>$row){
                db_query($sql=sprintf('INSERT INTO {collaboration_estrategia}(nid,vid,responde_estrategia_nid,responde_despliegue_nid,responde_decision_nid,responde_informacion_nid) VALUES(%d,%d,%d,%d,%d,%d)',$node->nid,$node->vid,$row['responde_estrategia_nid'],$row['responde_despliegue_nid'],$row['responde_decision_nid'],$row['responde_informacion_nid']));
            }         
        }
    }    
}
//intelsat-2015
function estrategia_inc_wiki_delete_reto_al_que_responde($nid,$vid){
    if(db_table_exists('collaboration_estrategia')){
        db_query('DELETE FROM {collaboration_estrategia} WHERE nid=%d AND vid=%d',$nid,$vid);
    }    
}
//intelsat-2015
function estrategia_inc_get_csv_character($file_path,$is_importar_crm=0){
    $result=";";
    $lineas=array();
    $find_array=array(";",",");
    if($is_importar_crm){
        $find_array=array(";","/t");
    }
    //
    if(!empty($find_array)){
        foreach($find_array as $i=>$find){
            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $length=10000;
                $i=0;
                $linea=array();
                while (($data = fgetcsv($handle,$length,$find)) !== FALSE) {
                    $linea=$data;
                    if(count($linea)>=14){
                        fclose($handle);
                        return $find;
                    }
                }
                fclose($handle);
            }else{
                drupal_set_message(t('fopen csv error'),'error');
            }
        }    
    }    
    return $result;
}
function estrategia_inc_get_control_date($node){
    if(isset($node->no_control_date) && !empty($node->no_control_date)){
        return t('No Control date');
    }
    $result=date('d-m-Y',my_mktime($node->fecha_cumplimiento));
    return $result;
}
//intelsat-2015
function estrategia_inc_is_show_reto_al_que_responde_html(){
    if(hontza_grupos_is_activo_pestana('estrategia')){
        if(estrategia_inc_existen_preguntas_clave()){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function estrategia_inc_existen_preguntas_clave(){
    $rows=get_estrategia_arbol_rows();
    if(!empty($rows)){
        foreach($rows as $i=>$r){
            if(isset($r[1]) && !empty($r[1])){
                return 1;
            }
        }
    }
    return 0;
}
//intelsat-2016
function estrategia_inc_get_import_type_options(){
    $result=array();
    $result[1]=t('Add');
    $result[2]=t('Replace');
    return $result;
}
//intelsat-2016
function estrategia_inc_get_grupo_all_title_array($grupo_nid){
    $result=array();
    $estrategia_array=estrategia_get_grupo_all($grupo_nid);
    if(!empty($estrategia_array)){
        foreach($estrategia_array as $i=>$row){
            $result[]=$row->title;
        }
    }
    return $result;
}
//intelsat-2016
function estrategia_inc_is_replace($form_state){
    if(isset($form_state['values']) && isset($form_state['values']['import_type']) && !empty($form_state['values']['import_type'])){ 
        if($form_state['values']['import_type']==2){
            return 1;
        }
    }
    return 0;
}
//intelsat-2016
function estrategia_inc_get_info_estrategia_all($grupo_nid){
    $result=array();
    /*$result['estrategia']=estrategia_get_grupo_all($grupo_nid);
    $result['despliegue']=despliegue_get_grupo_all($grupo_nid);
    $result['decision']=decision_get_grupo_all($grupo_nid);
    $result['informacion']=informacion_get_grupo_all($grupo_nid);*/
    return $result;
}
//intelsat-2016
function estrategia_tabla_csv_download($compartir_documentos_estrategia_nid='',$is_result=0){
    $is_tabla_csv_download=1;
    $my_list=estrategia_tabla_callback(0,1,$is_tabla_csv_download,$compartir_documentos_estrategia_nid);
    if(!empty($my_list)){
        $headers=array();
        $headers[0]=estrategia_tabla_define_headers($is_tabla_csv_download);
        $my_list=array_merge($headers,$my_list);
        if($is_result){
            return $my_list;
        }
        estrategia_call_download_resumen_preguntas_clave_canales_csv($my_list,'key-questions',";",$is_result);        
    }else{
        return t('There are no contents');
    }
}
//intelsat-2016
function estrategia_inc_fivestar($node){
    if(estrategia_inc_is_voto_activado()){
        return traducir_average($node->content['fivestar_widget']['#value'],1);
    }else{
        return hontza_solr_search_fivestar_botonera($node,0,'',0,1);
    }
}
//intelsat-2016
function estrategia_inc_is_voto_activado(){
    if(estrategia_inc_is_estrategia_congelar_voto_activado()){
        $is_active_votes=estrategia_inc_get_grupo_is_active_votes();
        if($is_active_votes==2){
            return 0;
        }    
    }
    return 1;
}
//intelsat-2016
function estrategia_inc_is_estrategia_congelar_voto_activado(){
    if(defined('_IS_ESTRATEGIA_CONGELAR_VOTO') && _IS_ESTRATEGIA_CONGELAR_VOTO==1){
        if(estrategia_is_grupo_publico()){
            return 1;
        }    
    }
    return 0;
}
//intelsat-2016
function estrategia_settings_form(){
    $form=array();
    drupal_set_title(t('Settings'));    
    $is_active_votes=estrategia_inc_get_grupo_is_active_votes();
    $form['is_active_votes']=array(
        '#title'=>t('Activate Voting'),
        '#type'=>'select',
        '#options'=>hontza_registrar_yes_no_options(0,1),
        '#default_value'=>$is_active_votes,
        '#prefix'=>'<div style="clear:both;"><div style="float:left;">',
        '#suffix'=>'</div>',
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save'),
        '#name'=>'save_btn',
        '#prefix'=>'<div style="float:left;padding-top:35px;padding-left:32px;">',
        '#suffix'=>'</div></div>',
    );
    $form['reset_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Reset Voting'),
        '#name'=>'reset_btn',
        '#prefix'=>'<div style="clear:both">',
        '#suffix'=>'</div>',
    );
    return $form;
}
//intelsat-2016
function estrategia_settings_access(){
    if(estrategia_inc_is_estrategia_congelar_voto_activado()){
        if(estrategia_is_grupo_publico()){
            if(red_funciones_is_administrador_grupo()){
                return TRUE;
            }
        }
    }
    return FALSE;
}
//intelsat-2016
function estrategia_inc_get_grupo_is_active_votes(){
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        if(isset($my_grupo->field_is_active_votes[0]['value'])){
            if($my_grupo->field_is_active_votes[0]['value']==2){
                return 2;
            }
        }
        return 1;
    }
    return 2;
}
//intelsat-2016
function estrategia_settings_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']['#name'])){
        if($form_state['clicked_button']['#name']=='save_btn'){
            estrategia_inc_settings_save($form_state);
            drupal_set_message(t('Saved'));
        }else if($form_state['clicked_button']['#name']=='reset_btn'){
            estrategia_inc_reset_votes();
            drupal_set_message(t('Reset'));
        }
        $_REQUEST['destination']='';
        drupal_goto('estrategia/settings');
    } 
}
//intelsat-2016
function estrategia_inc_settings_save($form_state){
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $is_active_votes=$form_state['values']['is_active_votes'];
        db_query('UPDATE {content_type_grupo} SET field_is_active_votes_value=%d WHERE nid=%d AND vid=%d',$is_active_votes,$my_grupo->nid,$my_grupo->vid);
        hontza_solr_clear_cache_content($my_grupo->nid,1);
    }    
}
//intelsat-2016
function estrategia_inc_reset_votes(){
    $my_grupo=og_get_group_context();    
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_nid=$my_grupo->nid;
        $estrategia_array=estrategia_get_grupo_all($grupo_nid);
        $despliegue_array=despliegue_get_grupo_all($grupo_nid);
        $decision_array=decision_get_grupo_all($grupo_nid);
        $estrategia_array=array_merge($estrategia_array,$despliegue_array,$decision_array);
        if(!empty($estrategia_array)){
            foreach($estrategia_array as $i=>$estrategia_row){
                db_query('DELETE FROM {votingapi_vote} WHERE content_id=%d',$estrategia_row->nid);
                db_query('DELETE FROM {votingapi_cache} WHERE content_id=%d',$estrategia_row->nid);
            }
        }
    }
}
//intelsat-2016
function estrategia_inc_add_evaluacion_disabled_option($my_type,&$result){
    if(in_array($my_type,array('importancia_despliegue','valor_decision'))){
        $result[0]='0='.t('Disabled');
    }
}
//intelsat-2016
function estrategia_inc_get_score($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            if(isset($row['node'])){    
                $node=$row['node'];
                unset($result[$i]['node']);
                $score=0;
                    if(estrategia_is_grupo_estrellas()){
                        $score=$node->suma_votos;
                    }else{
                        /*if($node->type=='estrategia'){                
                            $score=$node->valor_reto;
                        }else if($node->type=='despliegue'){
                            $score=$node->importancia_despliegue;
                        }else if($node->type=='decision'){
                            $score=$node->valor_decision;
                        }else if($node->type=='informacion'){
                            $score='';
                        }*/
                        continue;
                    }                    
                    $status_color=$result[$i][1];
                    $result[$i][1]=$score;
                    $result[$i][3]=$result[$i][2];
                    $result[$i][2]=$status_color;
            }    
        }
    }
    return $result;
}
function estrategia_inc_get_headers(){
    $headers=array(get_estrategia_simbolo_img(1).t('Challenge').'&nbsp;'.get_menu_gezia_img(2).get_despliegue_simbolo_img(1).t('SubChallenge').'&nbsp;'.get_menu_gezia_img(2).get_decision_simbolo_img(1).t('Decision').'&nbsp;'.get_menu_gezia_img(2).get_informacion_simbolo_img(1).t('Key Question').'&nbsp;'.get_menu_gezia_img(2),t('Score'),t('Status'),t('Action'));
    if(!estrategia_is_grupo_estrellas()){
        unset($headers[1]);
        $headers=array_values($headers);
    }
    return $headers;
}
//intelsat-2016
function estrategia_inc_update_suma_votos_array(){
            if(estrategia_inc_is_ordenar_suma_votos()){    
                estrategia_inc_update_estrategia_suma_votos_array();
                despliegue_update_despliegue_suma_votos_array();
                decision_update_despliegue_suma_votos_array();
            }    
}
//intelsat-2016
function estrategia_inc_update_suma_votos($nid,$vid,$suma_votos){
    db_query('UPDATE {estrategia} SET suma_votos=%f WHERE nid=%d AND vid=%d',$suma_votos,$nid,$vid);
}
//intelsat-2016
function estrategia_inc_is_ordenar_suma_votos(){
    if(db_column_exists('estrategia','suma_votos')){
        if(is_estrategia('arbol_estrategico')){
            if(estrategia_is_grupo_estrellas()){
                return 1;
            }
        }
    }
    return 0;
}    
//intelsat-2016
function estrategia_inc_get_order_by($order_by){
    $result=$order_by;
    if(estrategia_inc_is_ordenar_suma_votos()){
        $result=' ORDER BY e.suma_votos DESC,e.peso ASC,n.sticky DESC, n.created ASC,n.nid ASC';
    }
    return $result;
}
//intelsat-2016
function estrategia_inc_update_estrategia_suma_votos_array(){
    $is_link=0;
                $estrategia_array=get_estrategia_arbol_rows($is_link);
                if(!empty($estrategia_array)){
                    foreach($estrategia_array as $i=>$estrategia_row){
                        estrategia_inc_update_suma_votos($estrategia_row->nid,$estrategia_row->vid,$estrategia_row->suma_votos);
                    }
                }
}                