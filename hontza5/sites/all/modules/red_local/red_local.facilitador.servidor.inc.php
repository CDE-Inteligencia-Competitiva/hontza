<?php
function red_local_facilitadores_servidor_callback(){
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
         //$where[]='c.group_nid='.$my_grupo->nid;
    }else{
         return t('No group selected');  
    }
    $headers=array();
    $headers[0]='';
    $headers[1]=t('Title');
    $headers[2]=t('Description');
    $headers[3]=t('Categories');
    //$headers[4]=t('Subdomain');
    $headers[4]=t('User');
    $headers[5]=t('Created');    
    //
    $where=array();
    $where[]='1';
    $res=db_query('SELECT fa.* FROM {red_compartir_facilitador_descargados} fa WHERE '.implode(' AND ',$where).' ORDER BY fa.fecha DESC');    
    //
    $kont=0;
    $rows=array();
    
        while($r=db_fetch_object($res)){
            $node=node_load($r->nid);
            if(isset($node->nid)){
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=array();
                    $rows[$kont][0]=red_local_facilitador_local_define_acciones($r);
                    $rows[$kont][1]=$node->title;
                    $rows[$kont][2]=$node->field_descripcion_servicios[0]['value'];
                    $rows[$kont][3]=red_compartir_facilitador_copy_set_categories($node);
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