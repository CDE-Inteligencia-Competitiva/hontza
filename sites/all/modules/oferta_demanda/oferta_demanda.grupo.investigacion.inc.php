<?php
function oferta_demanda_grupo_investigacion_get_grupo_investigacion_title(){
    return t('Research Group');
}
function oferta_demanda_grupo_investigacion_get_grupo_investigacion_block_content(){
    $html=array();
    $add_label=t('Add Research Group');
    $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
    $html[]=l($icono.$add_label,'node/add/oferta-demanda-grupo-investigaci',array('query'=>'destination=oferta_demanda/grupos_investigacion','html'=>true));   
    $title=oferta_demanda_grupo_investigacion_get_grupo_investigacion_title();
    $html[]=l($title,'oferta_demanda/grupos_investigacion');
    return implode('<BR>',$html);
}
function oferta_demanda_grupo_investigacion_grupos_investigacion_callback(){
    global $user;
        $my_limit=20;
        //    
        $sort='asc';
        $field='node.created';
        $where=array();
        $where[]='1';
        $where[]='node.status=1';
        $where[]='node.type="oferta_demanda_grupo_investigaci"';
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
                    $r[1]=array('data'=>oferta_demanda_grupo_investigacion_grupos_investigacion_define_acciones($node),'class'=>'td_nowrap');
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
    $title=oferta_demanda_grupo_investigacion_get_grupo_investigacion_title();
    drupal_set_title($title);
    return $output;
}
function oferta_demanda_grupo_investigacion_grupos_investigacion_define_acciones($node){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$node->nid.'/edit',array('query'=>'destination=oferta_demanda/grupos_investigacion','html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$node->nid.'/delete',array('query'=>'destination=oferta_demanda/grupos_investigacion','html'=>true));    
    return implode('&nbsp;',$html);
}
function oferta_demanda_grupo_investigacion_node_form_alter(&$form,&$form_state,$form_id){
    $title=t('Add Research Group');
    $node=hontza_get_node_by_form($form);
    if(isset($node->nid) && !empty($node->nid)){    
        $title=t('Edit Research Group');
    }
    drupal_set_title($title);            
    $form['title']['#title']=t('Name');
    $form['body_field']['body']['#title']=t('Abstract of RG (background, main projects, awards, milestones)');
    $form['field_investigacion_direccion'][0]['#title']=t('Work address');
    $form['field_investigacion_municipio'][0]['#title']=t('Town');
    $form['field_investigacion_codigo_posta'][0]['#title']=t('ZIP Code');
    $form['field_investigacion_pais'][0]['#title']=t('Country');
    $form['field_investigacion_expertos']['#title']=t('Experts');
    unset($form['field_investigacion_area_tecnolo']);
    oferta_demanda_set_taxonomy_form_field($form,$form_id);
}
function oferta_demanda_grupo_investigacion_get_area_tecnologica_options(){
    return oferta_demanda_organizacion_get_area_tecnologica_options();
}