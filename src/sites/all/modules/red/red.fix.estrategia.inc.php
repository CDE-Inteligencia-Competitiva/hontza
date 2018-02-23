<?php
function red_fix_estrategia_callback(){
    return 'Funcion desactivada';    
    if(hontza_is_red_hoja() && !hontza_is_sareko_id('ALERTA')){
        $group_nid=140032;
        //$fix_group_nid=147103;
        //$fix_group_nid=147583;
        $fix_group_nid=147629;
        red_fix_estrategia($group_nid,$fix_group_nid);    
        red_fix_despliegue($group_nid,$fix_group_nid);
        red_fix_decision($group_nid,$fix_group_nid);
        red_fix_informacion($group_nid,$fix_group_nid);
        return date('Y-m-d H:i');
    }
    return '';
}
function red_fix_estrategia($group_nid,$fix_group_nid){
    $estrategia_array=red_fix_get_estrategia_array($group_nid);
    if(!empty($estrategia_array)){
        foreach($estrategia_array as $i=>$row){
            red_fix_update_estrategia($row,$fix_group_nid);
        }
    }
}
function red_fix_get_estrategia_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='(e.grupo_nid='.$group_nid.' OR e.grupo_seguimiento_nid='.$group_nid.')';
    }else{
        return $result;
    }
    $sql='SELECT * FROM {estrategia} e WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_despliegue($group_nid,$fix_group_nid){
    $despliegue_array=red_fix_get_despliegue_array($group_nid);
    if(!empty($despliegue_array)){
        foreach($despliegue_array as $i=>$row){
            red_fix_update_despliegue($row,$fix_group_nid);
        }
    }
}
function red_fix_get_despliegue_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='(d.grupo_nid='.$group_nid.' OR d.grupo_seguimiento_nid='.$group_nid.')';
    }else{
        return $result;
    }
    $sql='SELECT * FROM {despliegue} d WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_decision($group_nid,$fix_group_nid){
    $decision_array=red_fix_get_decision_array($group_nid);
    if(!empty($decision_array)){
        foreach($decision_array as $i=>$row){
            //echo print_r($decision_array,1);exit();
            red_fix_update_decision($row,$fix_group_nid);
        }
    }
}
function red_fix_get_decision_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='d.grupo_nid='.$group_nid;
    }else{
        return $result;
    }
    $sql='SELECT * FROM {decision} d WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_informacion($group_nid,$fix_group_nid){
    $informacion_array=red_fix_get_informacion_array($group_nid);
    if(!empty($informacion_array)){
        //echo print_r($informacion_array,1);exit();
        foreach($informacion_array as $i=>$row){
            red_fix_update_informacion($row,$fix_group_nid);
        }
    }
}
function red_fix_get_informacion_array($group_nid){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($group_nid)){
        $where[]='d.grupo_nid='.$group_nid;
    }else{
        return $result;
    }
    $sql='SELECT * FROM {informacion} d WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fix_update_estrategia($row,$fix_group_nid){
    db_query('UPDATE {estrategia} SET grupo_nid=%d,grupo_seguimiento_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$fix_group_nid,$row->nid,$row->vid);
}
function red_fix_update_despliegue($row,$fix_group_nid){
    db_query('UPDATE {despliegue} SET grupo_nid=%d,grupo_seguimiento_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$fix_group_nid,$row->nid,$row->vid);
}
function red_fix_update_decision($row,$fix_group_nid){
    db_query('UPDATE {despliegue} SET grupo_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$row->nid,$row->vid);
}
function red_fix_update_informacion($row,$fix_group_nid){
    db_query('UPDATE {informacion} SET grupo_nid=%d WHERE nid=%d AND vid=%d',$fix_group_nid,$row->nid,$row->vid);
}