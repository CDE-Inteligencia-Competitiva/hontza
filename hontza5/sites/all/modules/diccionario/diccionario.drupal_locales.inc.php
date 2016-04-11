<?php
function diccionario_traer_drupal_locales_source_callback(){    
    return 'Funcion desactivada';
    $output=diccionario_traer_drupal_locales_source();
    return $output.'<BR>'.date('Y-m-d H:i');
}
function diccionario_traer_drupal_locales_source(){
    db_set_active('drupal_locales');
    $sources_array=diccionario_traer_get_drupal_locales_sources_array();
    db_set_active();
    $traer_array=array();
    //print 'sources_array===='.count($sources_array);exit();
    if(!empty($sources_array)){
        foreach($sources_array as $i=>$row){
            if(isset($row->source) && !empty($row->source)){
                $r=diccionario_traer_drupal_locales_get_locales_source_row($row->source);
                if(isset($r->lid) && !empty($r->lid)){
                    continue;
                }else{   
                    $traer_array[]=$row;
                    //print $row->source.'<BR>';
                }    
            }
        }
    }
    /*print count($traer_array);
    exit();*/
    db_set_active();
    $output=diccionario_traer_drupal_locales_guardar_traer_array($traer_array);
    return $output;
}
function diccionario_traer_get_drupal_locales_sources_array(){
    $result=array();
    $res=db_query('SELECT * FROM {sources} WHERE 1 ORDER BY source ASC');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function diccionario_traer_drupal_locales_get_locales_source_row($source){
    $my_array=diccionario_get_locales_source_array($source);
    if(count($my_array)>0){
        return $my_array[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function diccionario_traer_drupal_locales_guardar_traer_array($traer_array){
    $html=array();
    if(!empty($traer_array)){
        foreach($traer_array as $i=>$traer_row){
                $row=clone $traer_row;
                $row->textgroup='default';
                $row->version='none';
                db_set_active('drupal_locales');
                $location=diccionario_traer_drupal_locales_get_location($row);
                $location='podx:'.$location;
                db_set_active();                
                $sql=sprintf('INSERT INTO {locales_source}(location,textgroup,source,version) VALUES("%s","%s","%s","%s")',$location,$row->textgroup,$row->source,$row->version);
                $html[]=$i.'='.$sql.'<BR>';
                db_query('INSERT INTO {locales_source}(location,textgroup,source,version) VALUES("%s","%s","%s","%s")',$location,$row->textgroup,$row->source,$row->version);
                //$new_lid=db_last_insert_id('locales_source','lid');
           
        }
    }
    return implode('',$html);
}
function diccionario_traer_drupal_locales_get_location($row){
    $res=db_query('SELECT * FROM uses WHERE lid='.$row->lid);
    while($r=db_fetch_object($res)){
        return $r->file;
    }
    return '';
}
function diccionario_exportar_drupal_locales_source_by_modulo_callback(){
    $result=array();
    $locales_sources_array=diccionario_get_locales_source_array();
    if(!empty($locales_sources_array)){
        foreach($locales_sources_array as $i=>$row){
            $r=diccionario_drupal_locales_get_source_row($row->source);
            if(isset($r->source) && !empty($r->source)){    
                $r->uses=diccionario_drupal_locales_get_uses($r->lid);
                if(diccionario_drupal_locales_is_in_uses_by_modulo($r->uses,$existe,$module_name,$is_templates)){
                    $result[]=$row;
                }
            }    
        }        
    }
    print count($result);exit();
    //diccionario_drupal_locales_print_no_existe($existe);
    
    return $output;
}
function diccionario_drupal_locales_set_uses($sources_array_in){
    $sources_array=$sources_array_in;
    if(!empty($sources_array)){
        foreach($sources_array as $i=>$row){
            $sources_array[$i]->uses=diccionario_drupal_locales_get_uses($row->lid);
        }
    }    
    return $sources_array;
}
function diccionario_drupal_locales_get_uses($lid){
    $result=array();
    db_set_active('drupal_locales');
    $res=db_query('SELECT uses.* FROM {uses} uses WHERE uses.lid='.$lid);
    db_set_active();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function diccionario_drupal_locales_get_source_row($source){
    $my_array=diccionario_drupal_locales_get_source_array($source);
    /*if(count($my_array)>1){
        print $source.'<BR>';
    }*/
    if(isset($my_array[0])){
        return $my_array[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function diccionario_drupal_locales_get_source_array($source){
    $result=array();
    db_set_active('drupal_locales');
    $res=db_query('SELECT * FROM {'.$table_name.'} sources WHERE sources.source="%s"',$source);
    db_set_active();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function diccionario_drupal_locales_is_in_uses_by_modulo($uses,&$existe,&$module_name,&$is_templates){    
    $module_name='';
    $modules_array=diccionario_drupal_locales_define_modules_array();
    if(!empty($uses)){
        foreach($uses as $i=>$row){
            if(diccionario_drupal_locales_in_all_and_default_modules($row->file,$new_file_name,$is_templates)){
                if($is_templates){
                    if($is_templates==1){
                        $module_name='templates';                        
                    }else if($is_templates==2){
                        $module_name='functions';     
                    }else if($is_templates==3){
                        $module_name='preprocess';     
                    }
                    return 1;
                }
                //
                $module_name=diccionario_drupal_locales_get_module_name_by_file($new_file_name);
                if(in_array($module_name,$modules_array)){
                    if(!in_array($module_name,$existe)){
                        $existe[]=$module_name;                        
                    }
                    //
                    return 1;
                }
            }/*else{
                print $row->file.'<BR>';
                exit();
            }*/
        }
    }
    return 0;
}
function diccionario_drupal_locales_in_all_and_default_modules($uses_file,&$result,&$is_templates){
    $ok=0;
    $is_templates=0;
    $my_array=array('/sites/all/modules/','/sites/default/modules/','/sites/all/themes/buho/templates/overrides/','/sites/all/themes/buho/functions/','/sites/all/themes/buho/preprocess/');    
    $result=$uses_file;
    if(!empty($my_array)){
        foreach($my_array as $i=>$v){
            $pos=strpos($uses_file,$v);
            if($pos===FALSE){
                //
            }else{
                $result=str_replace($v,'',$result); 
                if($v=='/sites/all/themes/buho/templates/overrides/'){
                   $is_templates=1;                    
                }else if($v=='/sites/all/themes/buho/functions/'){
                   $is_templates=2;
                }else if($v=='/sites/all/themes/buho/preprocess/'){
                   $is_templates=3;
                }
                $ok=1;
            }
        }
    }
    if($ok){
        return 1;
    }
    return 0;
}
function diccionario_drupal_locales_get_module_name_by_file($s){
    $pos=strpos($s,'/');
    if($pos===FALSE){
        return $s;
    }else{
        return substr($s,0,$pos);
    }
}
function diccionario_drupal_locales_define_modules_array(){
    $modules_array=array(
        'boletin_report',
        'calendario',
        'claves',
        'diccionario',
        'gestion_canales',
        'hontza',
        'hontza_features',
        'hontza_forms',
        'hontza_grupos',
        'hontza_notify',
        'hontza_profile',
        'hontza_viewsfield',
        'hontzafeeds',
        'hound',
        'red',
        'red_compartir',
        'red_exportar_rss',
        'red_local',
        'red_red',
        'red_red_login',
        'red_servidor',
        'rejected',
        'traducir',
        'user',
        'user_delete',
        //default/modules
        'alerta',
        'boletin_grupo',
        'borrar_pendientes',
        'decision',
        'despliegue',
        'estrategia',
        'idea',
        'informacion',
        'oportunidad',
        'proyecto',
        'red_migracion',
        'red_publica',
    );
    return $modules_array;
}
function diccionario_drupal_locales_print_no_existe($existe){
    $modules_array=diccionario_drupal_locales_define_modules_array();
    if(!empty($modules_array)){
        foreach($modules_array as $i=>$my_name){
            if(!in_array($my_name,$existe)){
                print 'no existe modulo='.$my_name.'<BR>';
            }
        }
    }
}