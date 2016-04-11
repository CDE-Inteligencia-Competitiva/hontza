<?php
function diccionario_traer_red_alerta_callback(){    
    return 'Funcion desactivada';
    diccionario_traer_red_alerta_local_sources();
    return date('Y-m-d H:i');
}
function diccionario_traer_red_alerta_local_sources(){
    db_set_active('hontza_redalerta');
    $sources_array=diccionario_red_alerta_get_sources_array();
    db_set_active();
    $traer_array=array();
    $kont=0;
    if(!empty($sources_array)){
        foreach($sources_array as $i=>$row){
            $s=trim($row->source);
            if(empty($s)){
                continue;
            }
            //$new_row=clone $row;
            $r=diccionario_portugues_get_locales_source_row_by_row($row);
            if(isset($r->lid) && !empty($r->lid)){            
                //unset($new_row->source);
                //$new_row->lid=$r->lid;
                //$sql=sprintf('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);
                //diccionario_portugues_insert_into_locales_target($new_row);        
            }else{
                db_set_active('hontza_redalerta');
                $traducciones=array();
                $is_traducido=diccionario_red_alerta_is_traducido($row,$traducciones);
                db_set_active();
                if($is_traducido){
                    /*print '####################INI##########################<BR>';
                    print $row->source.'<BR>';
                    print '-------------------traducciones------------------<BR>';
                    if(!empty($traducciones)){
                        foreach($traducciones as $k=>$trad){
                            print $trad->translation.'<BR>';
                        }
                    }
                    print '####################END##########################<BR>';*/
                    $traer_array[$kont]=$row;
                    $traer_array[$kont]->traducciones=$traducciones;
                    $kont++;
                }
            }            
        }
    }
    /*print count($traer_array);
    exit();*/
    db_set_active();
    $output=diccionario_red_alerta_traer_guardar_traer_array($traer_array);
    return $output;
}
function diccionario_red_alerta_get_sources_array(){
    $result=array();
    $res=db_query('SELECT ls.* FROM {locales_source} ls WHERE ls.textgroup="default" ORDER BY ls.lid ASC');       
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function diccionario_red_alerta_is_traducido($row,&$target_array){
    if(diccionario_red_alerta_contiene_php($row->source)){
        return 0;
    }
    if(diccionario_red_alerta_contiene_curl_error($row->source)){
        return 0;
    }
    if(diccionario_red_alerta_contiene_custom_css($row->source)){
        return 0;
    }
    if(diccionario_red_alerta_contiene($row->source)){
        return 0;
    }
    //
    $konp_source=diccionario_red_alerta_normaliza_cadena($row->source);
            
    $target_array=diccionario_red_alerta_get_target_array($row->lid);
    if(count($target_array)>0){
        //return 1;
        foreach($target_array as $i=>$r){            
            if($row->source==$r->translation){
                return 0;
            }
            $konp_translation=diccionario_red_alerta_normaliza_cadena($r->translation);
            if($konp_source==$konp_translation){
                return 0;
            }
            //
            if(in_array($row->source,array('Jan 17','Jan 21','Jan 25','Nombre usuario'))){
                return 0;
            }
            //
            $char=substr($r->translation,0,1);
            if($char=='*'){
                return 0;
            }    
        }        
        return 1;
    }
    return 0;
}
function diccionario_red_alerta_get_target_array($lid){
    $result=array();
    //print 'lid===='.$lid.'BR>';
    $res=db_query('SELECT * FROM {locales_target} WHERE lid='.$lid);       
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function diccionario_red_alerta_contiene($source){
    $v=strtolower($source);
    //intelsat-2016
    $find=diccionario_alerta_get_contiene();
    if(empty($find)){
        return 0;
    }
    $pos=strpos($v,$find);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function diccionario_red_alerta_contiene_php($source){
    $v=strtolower($source);
    $find='<?php';
    $pos=strpos($v,$find);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function diccionario_red_alerta_contiene_curl_error($source){
    $v=strtolower($source);
    $find='curl error';
    $pos=strpos($v,$find);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function diccionario_red_alerta_contiene_custom_css($source){
    $v=strtolower($source);
    $find='custom.css';
    $pos=strpos($v,$find);
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function diccionario_red_alerta_traer_guardar_traer_array($traer_array){
    $html=array();
    if(!empty($traer_array)){
        foreach($traer_array as $i=>$row){
           if(isset($row->traducciones) && !empty($row->traducciones)){
                $sql=sprintf('INSERT INTO {locales_source}(location,textgroup,source,version) VALUES("%s","%s","%s","%s")',$row->location,$row->textgroup,$row->source,$row->version);
                $html[]=$i.'='.$sql.'<BR>';
                db_query('INSERT INTO {locales_source}(location,textgroup,source,version) VALUES("%s","%s","%s","%s")',$row->location,$row->textgroup,$row->source,$row->version);
                $new_lid=db_last_insert_id('locales_source','lid');
                db_set_active();
            
                foreach($row->traducciones as $k=>$r){
                    $locale_target_row=clone $r;
                    unset($locale_target_row->lid);
                    $locale_target_row->lid=$new_lid;
                    //echo print_r($traduccion,1);
                    $sql=sprintf('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);
                    $html[]=$sql.'<BR>';
                    db_query('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);                    
                }
            }
        }
    }
    return implode('',$html);
}
function diccionario_red_alerta_normaliza_cadena($cadena){
    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}