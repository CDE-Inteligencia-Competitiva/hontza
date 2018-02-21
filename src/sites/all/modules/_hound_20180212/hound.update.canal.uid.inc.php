<?php
function hound_update_hound_uid_callback(){
    return 'Funcion desactivada';
    $types=array('canal_de_supercanal','canal_de_yql');
    $groups=array(143311,143546);
    $uid_array=array(756,759);
    //
    //aero: 143311
    //div: 143546
    //
    $node_array=hontza_get_all_nodes($types,$groups);
    if(!empty($node_array)){
        foreach($node_array as $i=>$node){
            if(isset($node->og_groups)){
                $group_nid_array=array_keys($node->og_groups);
                if(!empty($group_nid_array)){    
                    foreach($groups as $i=>$group_nid){
                        if(in_array($group_nid,$group_nid_array)){
                            $uid=$uid_array[$i];
                            db_query('UPDATE {node} SET uid=%d WHERE nid=%d',$uid,$node->nid);
                        }
                    }
                }    
            }
        }
    }
    return 'Update '.date('Y-m-d H:i');
}
