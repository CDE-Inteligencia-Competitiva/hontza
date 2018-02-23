<?php
function oferta_demanda_proyecto_get_proyectos_title(){
    return t('Projects');
}
function oferta_demanda_proyecto_get_proyectos_block_content(){
    $html=array();
    $add_label=t('Add Project');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'node/add/oferta-demanda-proyecto',array('query'=>'destination=oferta_demanda/proyectos','html'=>true));   
    $title=oferta_demanda_proyecto_get_proyectos_title();
    $html[]=l($title,'oferta_demanda/proyectos');
    return implode('<BR>',$html);
}
function oferta_demanda_proyecto_proyectos_callback(){
    global $user;
        $my_limit=20;
        //    
        $sort='asc';
        $field='node.created';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_proyecto"';
        $where[]='node.uid='.$user->uid;
        $sql='SELECT node.*
        FROM {node} node
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        $headers=array();
        $headers[]=t('Name');    
        $headers[]='';
        //
        $res=db_query($sql);
        $mode='table';
        $num_rows=FALSE;
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){    
                if($mode=='nodes'){
                    $rows[]=$node;
                }else{
                    $r=array();
                    //
                    $r[0]=$node->title;
                    $r[1]=array('data'=>oferta_demanda_proyecto_proyectos_define_acciones($node),'class'=>'td_nowrap');
                    $rows[]=$r;
                }
                $num_rows=TRUE;
            }            
        }
        $rows=my_set_estrategia_pager($rows, $my_limit);    
    //
    if($mode=='nodes'){
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    }
    //    
    if ($num_rows) {
        if($mode!='nodes'){
            $output .= theme('table',$headers,$rows);
        }
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    $title=oferta_demanda_proyecto_get_proyectos_title();
    drupal_set_title($title);
    return $output;
}
function oferta_demanda_proyecto_proyectos_define_acciones($node){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$node->nid.'/edit',array('query'=>'destination=oferta_demanda/proyectos','html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$node->nid.'/delete',array('query'=>'destination=oferta_demanda/proyectos','html'=>true));    
    return implode('&nbsp;',$html);
}
function oferta_demanda_proyecto_node_form_alter(&$form,&$form_state,$form_id){
    $title=t('Add Project');
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){    
        $title=t('Edit Project');
    }
    drupal_set_title($title);                
    $form['title']['#title']=t('Name');
    $form['body_field']['body']['#title']=t('Abstract (background, results, awards, milestones)');
    unset($form['field_proyecto_area_tecnologica']);
    oferta_demanda_set_taxonomy_form_field($form,$form_id);
}
function oferta_demanda_proyecto_get_socio_principal_options(){
    return oferta_demanda_organizacion_get_organizacion_options();
}
function oferta_demanda_proyecto_get_otros_socios_options(){
    return oferta_demanda_proyecto_get_socio_principal_options();
}
function oferta_demanda_proyecto_get_area_tecnologica_options(){
    return oferta_demanda_organizacion_get_area_tecnologica_options();
}