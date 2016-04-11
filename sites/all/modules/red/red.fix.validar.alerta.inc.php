<?php
function red_fix_validar_alerta_callback(){
    return 'Funcion desactivada';
    if(hontza_is_sareko_id_red()){
        $types=array('item','noticia');
        $node_array=hontza_get_all_nodes($types);
        if(!empty($node_array)){
            $time=strtotime(date('2014-01-01 00:00'));                
            $kont=0;        
            foreach($node_array as $i=>$node){
                if($node->created<$time){
                    $kont++;
                    $row=new stdClass();
                    $row->nid=$node->nid;
                    hontza_delete_flag_content($row);
                    $flag_result = flag('flag','leido_interesante',$row->nid);
                }
            }
            //print $kont;exit();    
        }
    }
    return date('Y-m-d H:i');
}