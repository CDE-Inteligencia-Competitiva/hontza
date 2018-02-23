<?php
function oferta_demanda_organizacion_get_organizaziones_title(){
    return t('Organisations');
}
function oferta_demanda_organizacion_get_organizaciones_block_content(){
    $html=array();
    $add_label=t('Add Organisation');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'node/add/oferta-demanda-organizacion',array('query'=>'destination=oferta_demanda/organizaciones','html'=>true));   
    $title=oferta_demanda_organizacion_get_organizaziones_title();
    $html[]=l($title,'oferta_demanda/organizaciones');
    return implode('<BR>',$html);
}
function oferta_demanda_organizacion_get_director_contacto_options(){
    return oferta_demanda_experto_get_experto_options();
}
function oferta_demanda_organizacion_get_tipo_organizacion_options(){
    $result=array();
    
    return $result;
}
function oferta_demanda_organizacion_get_sector_actividad_options(){
    $result=array();
    
    return $result;
}
function oferta_demanda_organizacion_get_area_tecnologica_options(){
    $result=array();
    
    return $result;
}
function oferta_demanda_organizacion_get_rol_options(){
    $result=array();
    
    return $result;
}
function oferta_demanda_organizacion_organizaciones_callback(){
    global $user;
        $my_limit=20;
        //    
        $sort='asc';
        $field='node.created';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_organizacion"';
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
                    $r[1]=array('data'=>oferta_demanda_organizacion_organizaciones_define_acciones($node),'class'=>'td_nowrap');
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
    $title=oferta_demanda_organizacion_get_organizaziones_title();
    drupal_set_title($title);
    return $output;
}
function oferta_demanda_organizacion_organizaciones_define_acciones($node){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$node->nid.'/edit',array('query'=>'destination=oferta_demanda/organizaciones','html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$node->nid.'/delete',array('query'=>'destination=oferta_demanda/organizaciones','html'=>true));    
    return implode('&nbsp;',$html);
}
function oferta_demanda_organizacion_get_organizacion_options(){
    $result=array();
    $organizacion_array=oferta_demanda_organizacion_get_organizacion_array();
    if(!empty($organizacion_array)){
        foreach($organizacion_array as $i=>$row){
            $result[$row->nid]=$row->title;
        }
    }
    return $result;
}
function oferta_demanda_organizacion_get_organizacion_array(){
    global $user;
        $result=array();
        $sort='asc';
        $field='node.title';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_organizacion"';
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
function oferta_demanda_organizacion_node_form_alter(&$form,&$form_state,$form_id){
    $title=t('Add Organisation');
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){    
        $title=t('Edit Organisation');
    }
    drupal_set_title($title);    
    $form['title']['#title']=t('Name');
    $form['body_field']['body']['#title']=t('Abstract (Background, main projects, awards, milestones)');
    $form['field_oferta_direccion'][0]['#title']=t('Address');
    $form['field_oferta_municipio'][0]['#title']=t('Town');
    $form['field_oferta_codigo_postal'][0]['#title']=t('ZIP Code');
    $form['field_oferta_pais'][0]['#title']=t('Country');
    $form['field_oferta_director']['#title']=t('Director/Contact');
    $form['field_oferta_tipo_organizacion']['#title']=t('Type of organisation');
    $form['field_oferta_sector_actividad']['#title']=t('Activity sector(s)');
    $form['field_oferta_area_tecnologica']['#title']=t('Technological Areas');
    $form['field_oferta_rol']['#title']=t('Role (in technology transfer)');
    $form['field_oferta_presupuesto_anual'][0]['#title']=t('Annual Budget');
    unset($form['field_oferta_tipo_organizacion']);
    unset($form['field_oferta_rol']);
    unset($form['field_oferta_area_tecnologica']);
    oferta_demanda_set_taxonomy_form_field($form,$form_id);
}