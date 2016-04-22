<?php
//gemini
function get_term_extra_row($tid,$code){
    $where=array();
    $where[]='1';
    $where[]='te.tid='.$tid;
    $where[]='te.code="'.$code.'"';
    //
    $sql='SELECT * FROM {term_extra} te WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_row=array();
    $my_row=(object) $my_row;
    return $my_row;
}