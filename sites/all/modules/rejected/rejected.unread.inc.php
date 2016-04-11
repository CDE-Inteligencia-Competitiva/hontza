<?php
function rejected_unread_get_delete_unread_news_time($grupo){
    if(isset($grupo->field_delete_unread_news_time) && isset($grupo->field_delete_unread_news_time[0]) && isset($grupo->field_delete_unread_news_time[0]['value'])){
        return $grupo->field_delete_unread_news_time[0]['value'];
    }
    return 0;
}
function rejected_unread_delete_unread_news_callback(){
    return 'Funcion desactivada';
    rejected_unread_delete_unread_news();
    return date('Y-m-d H:i:s');
}
function rejected_unread_delete_unread_news(){
    $grupo_array=get_all_nodes(array('grupo'));   
    if(!empty($grupo_array)){
         foreach($grupo_array as $i=>$row){
            $grupo=node_load($row->nid);
            if(isset($grupo->nid) && !empty($grupo->nid)){                
                $meses=rejected_unread_get_delete_unread_news_time($grupo);
                if($meses>0){                    
                    $news_array=rejected_unread_get_group_news_unread_array($grupo->nid);
                    if(count($news_array)>0){
                        foreach($news_array as $i=>$news){
                            $created=date('Y-m-d H:i:s',$news->created);
                            $konp_time=strtotime(date('Y-m-d H:i:s',strtotime($created." +".$meses." month" )));
                            $time=time();
                            if($konp_time<$time){
                                //$news_node=node_load($news->nid);
                                //$news_node->uid=0;
                                //node_save($news_node);
                                //print $news->nid.'<BR>';                                
                                rejected_node_delete($news->nid);
                            }
                        }    
                    }    
                }
            }
        }
    }
    /*
    //if(hontza_is_sareko_id('ROOT')){
        print date('Y-m-d H:i:s');
        exit();
    //}
     */
}
function rejected_unread_get_group_news_unread_array($grupo_nid){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='n.type in ("item", "noticia")';
    $where[]='og_ancestry.group_nid='.$grupo_nid;
    //$where[]='flag_content_node.fid = 3';
    $where[]='flag_content_node.uid IS NULL';
    //
    $sql='SELECT n.* 
    FROM {node} n 
    LEFT JOIN {og_ancestry} ON n.nid=og_ancestry.nid
    LEFT JOIN {flag_content} flag_content_node ON n.nid = flag_content_node.content_id
    WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}