<?php
function publico_fix_tipos_fuente_callback(){
    return 'Funcion desactivada';
    $instalador_tipos_fuente=publico_fix_get_instalador_tipos_fuente();
    db_set_active();
    if(!empty($instalador_tipos_fuente)){
        foreach($instalador_tipos_fuente as $i=>$row){
            $term=taxonomy_get_term($row->tid);
            if(!(isset($term->tid) && !empty($term->tid))){
                
            }
        }
    }    
    $instalador_term_extras=publico_fix_get_instalador_term_extras();    
    db_set_active();
    publico_fix_delete_tipos_fuente_term_extra();
    publico_fix_tipos_fuente($instalador_term_extras);
    $output=date('Y-m-d H:i:s');
    return $output;
}
function publico_fix_get_instalador_tipos_fuente(){
    db_set_active('blanco');
    $result=array();
    $sql='SELECT term_data.* FROM {term_data} term_data WHERE vid=1';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    db_set_active();
    return $result;
}
function publico_fix_get_instalador_term_extras(){
    db_set_active('blanco');
    $result=array();
    $sql='SELECT term_extra.* FROM {term_extra} term_extra WHERE vid=1';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    db_set_active();
    return $result;
}
function  publico_fix_delete_tipos_fuente_term_extra(){
    $sql='DELETE FROM {term_extra} WHERE vid=1';
    $res=db_query($sql);
}
function publico_fix_tipos_fuente($term_extras){
    if(!empty($term_extras)){
        foreach($term_extras as $i=>$row){
            db_query('INSERT INTO {term_extra}(code,tid,vid,name,description) VALUES("%s",%d,%d,"%s","%s")',$row->code,$row->tid,$row->vid,$row->name,$row->description);
        }    
    }
}