<?php
function panel_admin_servicios_callback(){
    $output='';
    //intelsat-2016
    red_facilitador_acces_denied();
    if(hontza_canal_rss_is_facilitador_activado()){
        return facilitador_facilitadores_callback();
    }
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[1]=t('Organisation');
    $headers[2]=t('Services');
    //$headers[3]=t('Published');
    $headers[3]=t('Actions');
    
    $my_limit=30;
    
    
    $sql=panel_admin_servicios_get_gestion_servicios_sql();
    
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $rows[$kont][1]=$r->node_title;
      $rows[$kont][2]='';
      //$rows[$kont][3]=panel_admin_grupos_get_published_label($r->node_status);
      $rows[$kont][3]=$r;            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=panel_admin_servicios_set_fields($rows);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_servicios'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Management of Facilitators'));
    //
    return panel_admin_servicios_define_gestion_servicios_header().drupal_get_form('panel_admin_servicios_gestion_servicios_bulk_form',array($output));
}
function panel_admin_servicios_define_gestion_servicios_filter_fields(){
    $filter_fields=array('grupo_nid');
    return $filter_fields;
}
function panel_admin_servicios_define_gestion_servicios_header(){
    /*my_add_buscar_js();
    return drupal_get_form('panel_admin_servicios_filtro_form');*/
    $html=array();
    $html[]='<div class="view-header">';
    $html[]=l(t('Back to management panel'),'panel_admin',array('attributes'=>array('class'=>'back')));
    $html[]=l(t('Create Facilitator'),'node/add/servicio',array('attributes'=>array('class'=>'add')));
    $html[]=l(t('Create Service'),'admin/content/taxonomy/4/add/term',array('attributes'=>array('class'=>'add')));
    $html[]='</div>';
    return implode('',$html);
}
function panel_admin_servicios_filtro_form(){
    $form=array();
    $form['file_buscar_fs']=array(
        '#type'=>'fieldset',
        '#title'=>t('Search'),
        '#attributes'=>array('id'=>'file_buscar_fs'),
    );
    $form['file_buscar_fs']['grupo_nid']=array(
        '#type'=>'select',
        '#title'=>t('Filter by group'),
        '#options'=>panel_admin_servicios_define_filtro_grupo_options(),
        '#default_value'=>hontza_get_gestion_usuarios_filter_value('grupo_nid','panel_admin_servicios_filtro'),
    );
    $form['file_buscar_fs']['my_submit']=array(
        '#type'=>'submit',
        '#value'=>t('Apply'),
    );
    $form['file_buscar_fs']['limpiar']=array(
        '#type'=>'submit',
        '#name'=>'limpiar',
        '#value'=>t('Reset'),
    );
    return $form;
}
function panel_admin_servicios_define_filtro_grupo_options(){
    return panel_admin_items_define_filtro_grupo_options();
}
function panel_admin_servicios_gestion_servicios_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function panel_admin_servicios_set_fields($rows_in){
    $result=$rows_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $node_title=$row[1];
            $nid=$row[3]->nid;
            $value=my_get_logo(array('nid'=>$nid),$node);
            $sep='src="';
            $b=my_create_new_img($sep,$value,$is_system);
            $b=hontza_set_logo_url_facilitadores($b, $is_system);
            $result[$i][1]=l($node_title,'node/'.$nid).'<BR>'.implode($sep,$b);
            $node=node_load($nid);
            if(isset($node->field_categoria_servicios) && count($node->field_categoria_servicios)>0){ 
                $value=my_get_empresa_categorias_html($node->field_categoria_servicios);
            }
            $result[$i][2]=$value;
            $result[$i][3]=array('data'=>panel_admin_servicios_define_acciones($result[$i][3]),'style'=>'white-space:nowrap;');
        }
    }
    return $result;
}
function panel_admin_servicios_gestion_servicios_bulk_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_node_btn','publish_post_btn','unpublish_post_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_panel_admin_servicios_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_node_btn')==0){
                drupal_goto('panel_admin/delete_servicios_confirm');
            }else if(strcmp($button_name,'publish_post_btn')==0){                
                drupal_goto('panel_admin/publish_post_servicios_confirm');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                drupal_goto('panel_admin/unpublish_post_servicios_confirm');
            }
        }
    }
}
function panel_admin_servicios_delete_servicios_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_servicios_nid_array','panel_admin/servicios');
    return $form;
}
function panel_admin_servicios_delete_servicios_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/panel_admin/servicios';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_servicios_publish_post_servicios_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_servicios_nid_array','panel_admin/servicios');
    return $form;
}
function panel_admin_servicios_unpublish_post_servicios_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_servicios_nid_array','panel_admin/servicios');
    return $form;
}
function panel_admin_servicios_publish_post_servicios_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/servicios';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_servicios_unpublish_post_servicios_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='panel_admin/servicios';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}
function panel_admin_servicios_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_servicios_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_servicios_define_gestion_servicios_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];
                    if(!empty($v)){
                        $_SESSION[$key]['filter'][$f]=$v;
                    }
                }
            }            
        }
    } 
}
function panel_admin_servicios_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/servicios';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_servicios_define_publish_post_accion($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_servicios_get_gestion_servicios_sql(){
    global $language;
    $where=array();
    $where[]='node.type in ("servicio")';
    $where[]='node.language="'.$language->language.'"';
    //$where[]='(node.language="'.$language->language.'" OR node.language="")';
    //
    //$sql=$view->build_info['query'];
    //
    $result='SELECT node.nid AS nid,
    node_data_field_logo_servicios.field_logo_servicios_fid AS node_data_field_logo_servicios_field_logo_servicios_fid, 
    node_data_field_logo_servicios.field_logo_servicios_list AS node_data_field_logo_servicios_field_logo_servicios_list, 
    node_data_field_logo_servicios.field_logo_servicios_data AS node_data_field_logo_servicios_field_logo_servicios_data, 
    node.language AS node_language, 
    node.type AS node_type, 
    node.vid AS node_vid, 
    node.title AS node_title, 
    node.status AS node_status, 
    node.uid AS node_uid, 
    node_revisions.format AS node_revisions_format, 
    node.created AS node_created 
    FROM {node} node LEFT JOIN {content_type_servicio} node_data_field_logo_servicios ON node.vid = node_data_field_logo_servicios.vid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC';
    return $result;
}
function panel_admin_servicios_block_content(){
    //intelsat-2016
    //if(panel_admin_admin_access()){
    if(red_facilitador_user_access() && panel_admin_admin_access()){
        $html=array();
        //intelsat-2016
        //se ha comentado esto
        //$html[]=l(t('Create Facilitator'), 'node/add/servicio');
        //$html[]=l(t('List of Facilitators and Services'), 'panel_admin/servicios');
        //$html[]=l(t('List of Experts and Services'), 'panel_admin/servicios');
        $html[]=l(t('List of Experts and Services'), 'facilitador/facilitadores');
        return implode('<br>',$html);
    }
    return '';
}
function panel_admin_servicios_define_publish_post_accion($r,$destination){
    return panel_admin_items_define_accion_publish($r,$destination);
}