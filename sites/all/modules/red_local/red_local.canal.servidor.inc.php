<?php
function red_local_canales_servidor_callback(){
   $where=array();
   $where[]="1";
   //
   /*$group_nid_array=red_local_get_user_group_nid_array();
   if(!empty($group_nid_array)){
       $where[]='c.group_nid IN('.implode(',',$group_nid_array).')';
   }*/
   $my_grupo=og_get_group_context();
   if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='c.group_nid='.$my_grupo->nid;
   }else{
        return t('No group selected');  
   }
    $headers=array();
    $headers[0]='';
    $headers[1]=t('Title');
    $headers[2]=t('Type');		
    $headers[3]=t('Rating');
    //$headers[4]=t('Subdomain');
    $headers[4]=t('User');
    $headers[5]=t('Created');
    $kont=0;
    //
    $res=db_query('SELECT c.* 
    FROM {node} n 
    LEFT JOIN {red_compartir_canal_descargados} c ON n.vid=c.vid
    LEFT JOIN {content_type_canal_de_yql} content_type_canal_de_yql ON c.vid=content_type_canal_de_yql.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY c.fecha DESC');
    //
    $rows=array();
    
        while($r=db_fetch_object($res)){
            $node=node_load($r->nid);
            if(isset($node->nid)){
                if(isset($node->nid) && !empty($node->nid)){
                    $node->votingapi_cache_row=red_local_canal_get_estrellas_average_row($node->nid);
                    $rows[$kont]=array();
                    $rows[$kont][0]=red_local_canal_local_define_acciones($r);
                    $rows[$kont][1]=$node->title;
                    $rows[$kont][2]=red_compartir_canal_get_type($node);
                    $rows[$kont][3]=red_compartir_canal_get_avg_rating_by_node($node);
                    //$rows[$kont][4]=red_local_set_subdomain_name($r->servidor_sareko_id);
                    $rows[$kont][4]=hontza_get_username($r->uid);
                    //$rows[$kont][6]=date('Y-m-d H:i',$r->fecha);
                    $rows[$kont][5]=date('Y-m-d',$r->fecha);
                    $kont++;
                }
            }
        }        
   
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));           
    }
    else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    $output=red_local_pantallas_menu().$output;
    return $output;
}