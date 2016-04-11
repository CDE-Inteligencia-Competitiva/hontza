<?php
function oferta_demanda_experto_get_expertos_title(){
    return t('Experts');
}
function oferta_demanda_experto_get_expertos_block_content(){
    $html=array();
    $add_label=t('Add Expert');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'node/add/oferta-demanda-experto',array('query'=>'destination=oferta_demanda/expertos','html'=>true));   
    $title=oferta_demanda_experto_get_expertos_title();
    $html[]=l($title,'oferta_demanda/expertos');
    return implode('<BR>',$html);
}
function oferta_demanda_experto_expertos_callback(){
    global $user;
        $my_limit=20;
        //    
        $sort='asc';
        $field='node.created';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_experto"';
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
                    $r[1]=array('data'=>oferta_demanda_experto_expertos_define_acciones($node),'class'=>'td_nowrap');
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
    $title=oferta_demanda_experto_get_expertos_title();
    drupal_set_title($title);
    return $output;
}
function oferta_demanda_experto_expertos_define_acciones($node){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$node->nid.'/edit',array('query'=>'destination=oferta_demanda/expertos','html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$node->nid.'/delete',array('query'=>'destination=oferta_demanda/expertos','html'=>true));    
    return implode('&nbsp;',$html);
}
function oferta_demanda_experto_get_nivel_estudio_options(){
    $result=array();
    $result[1]=t('Graduate');
    $result[2]=t('Master');
    $result[3]=t('Postgraduate');
    $result[4]=t('Doctor');
    return $result;
}
function oferta_demanda_experto_get_area_tecnologica_options(){
    return oferta_demanda_organizacion_get_area_tecnologica_options();
}
function oferta_demanda_experto_get_rol_options(){
    $result=array();
    $result[1]=t('Motivation');
    $result[2]=t('Organisation');
    $result[3]=t('Vision');
    $result[4]=t('Realism');
    $result[5]=t('Communication');
    return $result;
}
function oferta_demanda_experto_get_estilo_trabajo_relacion_options(){
    $result=array();
    $result[1]=t('Order');
    $result[2]=t('Chaos');
    $result[3]=t('Direct');
    $result[4]=t('Iterative');
    $result[5]=t('Strict');
    $result[6]=t('Flexible');
    $result[7]=t('Direct');
    $result[8]=t('Shared');
    $result[9]=t('Conservative');
    $result[10]=t('Risky');
    return $result;
}
function oferta_demanda_experto_get_experto_options(){
    $result=array();
    $organizacion_array=oferta_demanda_experto_get_experto_array();
    if(!empty($organizacion_array)){
        foreach($organizacion_array as $i=>$row){
            $result[$row->nid]=$row->title;
        }
    }
    return $result;
}
function oferta_demanda_experto_get_experto_array(){
    global $user;
        $result=array();
        $sort='asc';
        $field='node.title';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_experto"';
        $where[]='node.uid='.$user->uid;
        $sql='SELECT node.*
        FROM {node} node
        WHERE '.implode(' AND ',$where).'
        ORDER BY '.$field.' '.$sort;
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
        return $result;
}
function oferta_demanda_experto_node_form_alter(&$form,&$form_state,$form_id){
    $title=t('Add Expert');
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){    
        $title=t('Edit Expert');
    }
    drupal_set_title($title);        
    $form['title']['#title']=t('Name');
    $form['body_field']['body']['#title']=t('Bio (Background, main projects, awards, milestones)');
    $form['field_experto_direccion_laboral'][0]['#title']=t('Work Address');
    $form['field_experto_municipio'][0]['#title']=t('Town');
    $form['field_experto_codigo_postal'][0]['#title']=t('ZIP Code');
    $form['field_experto_pais'][0]['#title']=t('Country');
    $form['field_experto_nivel_estudios']['#title']=t('Education');
    $form['field_experto_organizacion']['#title']=t('Organisation');
    $form['field_experto_departamento'][0]['#title']=t('Department');
    $form['field_experto_cargo'][0]['#title']=t('Position');
    $form['field_experto_area_tecnologica']['#title']=t('Technological Areas');
    $form['field_experto_nivel_actividad'][0]['#title']=t('Level of activity (it does not imply quality)');
    $form['field_experto_nivel_reputacion'][0]['#title']=t('Reputation level (from other users - it implies quality)');
    $form['field_experto_industrias_sectore'][0]['#title']=t('Industries/sectors of experience');
    $form['field_experto_region_experiencia'][0]['#title']=t('Geographical Regions of experience');
    $form['field_experto_actividad_prof'][0]['#title']=t('Professional activities of experience');
    $form['field_experto_rol']['#title']=t('Roles in some aspects of teamwork');
    $form['field_experto_estilo_relacion']['#title']=t('Work and relationship style');
    unset($form['field_experto_area_tecnologica']);
    oferta_demanda_set_taxonomy_form_field($form,$form_id);
}