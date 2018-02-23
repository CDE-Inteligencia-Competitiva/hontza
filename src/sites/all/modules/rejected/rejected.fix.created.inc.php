<?php
function rejected_fix_created_callback(){
    return 'Funcion desactivada';
    db_set_active('node_created_db');
    //$item_array=get_all_nodes(array('item'));
    /*if(!empty($item_array)){
        foreach($item_array as $i=>$row){
           $node=node_load($row->nid);
           $item_array[$i]=$node;
        }   
    }*/
    $item_array=rejected_get_fix_created_item_array();
    //print count($item_array);exit();
    db_set_active();
    if(!empty($item_array)){
        $kont=0;
        foreach($item_array as $i=>$row){
           //$node=node_load($row->nid);
           $node=rejected_get_fix_created_item_row($row->nid);
           if(isset($node->nid) && !empty($node->nid)){
               if($row->created!=$node->created){
                   //echo print_r($node,1);exit();
                   print $row->nid.'='.date('Y-m-d H:i',$row->created).'<---->'.date('Y-m-d H:i',$node->created).'='.$node->title.'<BR>';
                   $kont++;
               }
           }
        }
        $output=$kont.'<BR>';
    }
    return $output.date('Y-m-d H:i');
}
function rejected_get_fix_created_item_array($item_nid=''){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='node.type="item"';
    if(!empty($item_nid)){
        $where[]='node.nid='.$item_nid;
    }
    $sql='SELECT node.* 
    FROM {node}
    WHERE '.implode(' AND ',$where).'
    GROUP BY node.nid
    ORDER BY vid ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function rejected_get_fix_created_item_row($item_nid){
    $item_array=rejected_get_fix_created_item_array($item_nid);
    if(!empty($item_array)){
        return $item_array[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}