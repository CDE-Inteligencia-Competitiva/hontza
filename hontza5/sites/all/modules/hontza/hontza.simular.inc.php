<?php
function hontza_simular_repasar_canal_pipe_id_callback(){
    global $base_url;
    return 'Funcion desactivada';
    $result=array();
    $kont=0;
    $canal_array=hontza_get_all_nodes(array('canal_de_supercanal'));
    if(!empty($canal_array)){
        foreach($canal_array as $i=>$node){
            $pipe_id=red_funciones_get_canal_pipe_id($node);
            $fuente_nid=$node->field_nid_fuente_canal[0]['value'];
            $fuente=node_load($fuente_nid);
            if(isset($fuente->nid) && !empty($fuente->nid)){
                $fuente_pipe_id=$fuente->field_supercanal_fuente[0]['value'];
                if($pipe_id!=$fuente_pipe_id){
                    if(!isset($result[$fuente->nid])){
                        $result[$fuente->nid]=array();
                    }
                    $result[$fuente->nid][]=$node->nid;
                    $kont++;
                }
            }
        }
    }
    //
    print 'canal kont='.count($canal_array).'<BR>';    
    print 'kont='.$kont.'<BR>';
    if(!empty($result)){
        foreach($result as $fuente_nid=>$canal_nid_array){
            print 'fuente='.$base_url.'/node/'.$fuente_nid.'<BR>';
            //
            /*$fuente_node=node_load($fuente_nid);
            red_funciones_update_canal_pipe_id($fuente_node);*/
            //
            if(!empty($canal_nid_array)){
                foreach($canal_nid_array as $i=>$canal_nid){
                    print 'canal='.$base_url.'/node/'.$canal_nid.'<BR>';
                }
            }
            print '<BR>#########################################################################################################<BR>';
        }
    }
}