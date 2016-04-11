<?php
function red_fix_respuesta_callback(){
    return 'Funcion desactivada';
    if(hontza_is_red_hoja() && !hontza_is_sareko_id('ALERTA')){
        $group_nid=140032;
        //$fix_group_nid=147103;
        //$fix_group_nid=147583;
        $fix_group_nid=147629;
        red_fix_idea($group_nid,$fix_group_nid);    
        red_fix_oportunidad($group_nid,$fix_group_nid);
        red_fix_proyecto($group_nid,$fix_group_nid);
        return date('Y-m-d H:i');
    }
    return '';
}
function red_fix_idea($group_nid,$fix_group_nid){
    $idea_array=red_fix_get_idea_array($group_nid);
    if(!empty($idea_array)){
        //echo print_r($idea_array,1);exit();
        foreach($idea_array as $i=>$row){
            red_fix_update_idea($row,$fix_group_nid);
        }
    }
}
function red_fix_get_idea_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='i.grupo_nid='.$group_nid;
    }else{
        return $result;
    }
    $sql='SELECT * FROM {idea} i WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_oportunidad($group_nid,$fix_group_nid){
    $oportunidad_array=red_fix_get_oportunidad_array($group_nid);
    if(!empty($oportunidad_array)){
        //echo print_r($oportunidad_array,1);exit();
        foreach($oportunidad_array as $i=>$row){
            red_fix_update_oportunidad($row,$fix_group_nid);
        }
    }
}
function red_fix_get_oportunidad_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='i.grupo_nid='.$group_nid;
    }else{
        return $result;
    }
    $sql='SELECT * FROM {oportunidad} i WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_proyecto($group_nid,$fix_group_nid){
    $proyecto_array=red_fix_get_proyecto_array($group_nid);
    if(!empty($proyecto_array)){
        //echo print_r($proyecto_array,1);exit();
        foreach($proyecto_array as $i=>$row){
            red_fix_update_proyecto($row,$fix_group_nid);
        }
    }
}
function red_fix_get_proyecto_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='i.grupo_nid='.$group_nid;
    }else{
        return $result;
    }
    $sql='SELECT * FROM {proyecto} i WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_update_idea($row,$fix_group_nid){
    db_query('UPDATE {idea} SET grupo_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$row->nid,$row->vid);
}
function red_fix_update_oportunidad($row,$fix_group_nid){
    db_query('UPDATE {oportunidad} SET grupo_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$row->nid,$row->vid);
}
function red_fix_update_proyecto($row,$fix_group_nid){
    db_query('UPDATE {proyecto} SET grupo_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$row->nid,$row->vid);
}