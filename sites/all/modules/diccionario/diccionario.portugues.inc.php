<?php
function diccionario_traer_portugues_callback(){    
    return 'Funcion desactivada';
    //diccionario_traer_portugues();
    //diccionario_traer();
    return date('Y-m-d H:i');
}
function diccionario_traer_portugues(){
    db_set_active('hontza_otroalerta');
    $target_array=diccionario_portugues_get_target_array('pt-pt');
    db_set_active();
    if(!empty($target_array)){
        foreach($target_array as $i=>$row){
            $new_row=clone $row;
            $r=diccionario_portugues_get_locales_source_row_by_row($row->source_row);
            if(isset($r->lid) && !empty($r->lid)){            
                //unset($new_row->source);
                $new_row->lid=$r->lid;
                /*echo print_r($new_row,1);
                exit();*/
                //$sql=sprintf('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);
                //diccionario_portugues_insert_into_locales_target($new_row);        
            }else{
                //print $row->source_row->source.'<BR>';
            }
            /*echo print_r($row,1);
            exit();*/
        }
    }    
}
function diccionario_portugues_get_target_array($my_lang){
    $result=array();
    $res=db_query('SELECT * FROM {locales_target} WHERE language="'.$my_lang.'"');
    $kont=0;
    while($row=db_fetch_object($res)){
        $source=diccionario_portugues_get_locales_source_row($row->lid);
        if(isset($source->lid)){
            $result[$kont]=$row;
            unset($result[$kont]->lid);
            $result[$kont]->source_row=$source;
            $kont++;
        }
    }
    return $result;
}
function diccionario_portugues_get_locales_source_string($lid){
    $result=array();
    $res=db_query('SELECT * FROM {locales_source} WHERE lid="'.$lid.'"');
    while($row=db_fetch_object($res)){
        return $row->source;
    }
    return '';
}
function diccionario_portugues_insert_into_locales_target($locale_target_row){
    $new_lid=$locale_target_row->lid;
    db_query('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);                    
}
function diccionario_portugues_get_locales_source_row($lid,$table_name='locales_source'){
    $result=array();
    $res=db_query('SELECT * FROM {'.$table_name.'} WHERE lid="'.$lid.'"');
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function diccionario_portugues_get_locales_source_row_by_row($source_row){
    $res=db_query('SELECT * FROM {locales_source} WHERE location="%s" AND textgroup="%s" AND source="%s" AND version="%s"',$source_row->location,$source_row->textgroup,$source_row->source,$source_row->version);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $res2=db_query('SELECT * FROM {locales_source} WHERE textgroup="%s" AND source="%s" AND version="%s"',$source_row->textgroup,$source_row->source,$source_row->version);
    while($row2=db_fetch_object($res2)){
        return $row2;
    }
    $res3=db_query('SELECT * FROM {locales_source} WHERE textgroup="%s" AND source="%s"',$source_row->textgroup,$source_row->source);
    while($row3=db_fetch_object($res3)){
        return $row3;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function diccionario_traer(){
    db_set_active('hontza_insalerta');
    $target_array=diccionario_portugues_get_target_array('fr');
    db_set_active();
    /*print count($target_array);
    exit();*/
    if(!empty($target_array)){
        foreach($target_array as $i=>$row){
            $new_row=clone $row;
            /*echo print_r($new_row,1);
            exit();*/
            $r=diccionario_portugues_get_locales_source_row_by_row($row->source_row);
            if(isset($r->lid) && !empty($r->lid)){            
                //unset($new_row->source);
                $new_row->lid=$r->lid;
                /*echo print_r($new_row,1);
                exit();*/
                //$sql=sprintf('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$new_lid,$locale_target_row->translation,$locale_target_row->language,$locale_target_row->plid,$locale_target_row->plural,$locale_target_row->l10n_status,$locale_target_row->i18n_status);
                //diccionario_portugues_insert_into_locales_target($new_row);        
            }else{
                print $row->source_row->source.'<BR>';
            }
            /*echo print_r($row,1);
            exit();*/
        }
    }
}