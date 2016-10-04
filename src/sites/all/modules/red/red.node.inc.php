<?php
function red_node_is_show_debate_link($node){
    if(hontza_grupos_is_activo_pestana('debate','')){
        return 1;
    }
    return 0;
}
function red_node_is_show_collaboration_link($node){
    if(hontza_grupos_is_activo_pestana('wiki','')){
        return 1;
    }
    return 0;
}
function red_node_is_show_idea_link($node){
    if(hontza_grupos_is_activo_pestana('idea','')){
        return 1;
    }
    return 0;
}
function red_node_og_home_noticiasvalidadas_dash_block_pre_execute(&$view){
    $where=array();
    $where[]='1';
    $where[]='node.type in ("item", "noticia")';
    $where[]='node.status <> 0';
    $my_grupo=og_get_group_context();
    $my_grupo_nid=0;
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;
    }
    $where[]='og_ancestry.group_nid='.$my_grupo_nid;    
    $sql='SELECT node.nid AS nid,
    flag_content.content_id AS flag_content_content_id,
    flag_content2.content_id AS flag_content2_content_id,
    node.title AS node_title,
    node.language AS node_language,
    node_comment_statistics.comment_count AS node_comment_statistics_comment_count,
    flag_content_node.timestamp AS flag_content_node_timestamp
    FROM {node} node 
    INNER JOIN {flag_content} flag_content_node ON node.nid = flag_content_node.content_id AND flag_content_node.fid = 2 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    LEFT JOIN {flag_content} flag_content ON node.nid = flag_content.content_id AND flag_content.fid = 2 
    LEFT JOIN {flag_content} flag_content2 ON node.nid = flag_content2.content_id AND flag_content2.fid = 2 
    INNER JOIN {node_comment_statistics} node_comment_statistics ON node.nid = node_comment_statistics.nid 
    WHERE '.implode(' AND ',$where).' ORDER BY flag_content_node_timestamp DESC,node_comment_statistics_comment_count DESC';    
    $view->build_info['query']=$sql; 	
    $view->build_info['count_query']=$sql;
}
function red_node_items_deleted_drupal_set_message($kont_items){
    if($kont_items>0){
        drupal_set_message(t('@kont_items news have been deleted',array('@kont_items'=>$kont_items)));
    }
}
function red_node_is_delete($node_type_array){
    $param2=arg(2);
    if(!empty($param2) && $param2=='delete'){    
        if(!empty($node_type_array)){
            foreach($node_type_array as $i=>$node_type){
                if(is_ficha_node_left($node_type)){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function red_node_inc_is_unset_de_la_botonera_gris_del_node(){
    $node=my_get_node();
    if(isset($node->nid) && !empty($node->nid)){
        $node_type_array=array('canal_de_supercanal','canal_de_yql','debate','decision','despliegue','estrategia','fuentedapper','fuentehtml','     idea','informacion','item','my_report','noticia');
        if(in_array($node->type,$node_type_array)){
            return 1;
        }
        if($node->type=='grupo'){
            if(hontza_is_og_vocab_terms()){
                if(is_show_modificar_vocab()){            
                    return 0;
                }
                return 1;
            }
            return 1;
        }
    }
    if(hontza_is_og_vocab_terms()){
        if(is_show_modificar_vocab()){            
            return 0;
        }
        return 1;
    }        
    return 0;
}