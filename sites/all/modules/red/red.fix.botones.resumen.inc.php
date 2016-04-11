<?php
function red_fix_botones_resumen_callback(){
    return 'Funcion desactivada';    
    if(red_fix_botones_resumen_is_fix()){
       $row=red_fix_botones_resumen_get_full_html_format();
       if(isset($row->settings) && !empty($row->settings)){
           red_fix_botones_resumen_update_format($row);
       }
    }
    return date('Y-m-d H:i');
}
function red_fix_botones_resumen_is_fix(){
    $sareko_id_array=array('LOKALA','ROOT');
    if(!hontza_in_sareko_id($sareko_id_array)){
        return 1;
    }
    return 0;
}
function red_fix_botones_resumen_get_full_html_format(){
    $sql='SELECT * FROM {hontza}.{wysiwyg} WHERE format=2';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_fix_botones_resumen_update_format($row){
    db_query('UPDATE {wysiwyg} SET settings="%s" WHERE format=%d',$row->settings,$row->format);
}