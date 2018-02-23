<?php
function contenidos_canales_callback(){
    global $user;
    //
    $headers=array();
    $headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';    
    //$headers[1]=array('data'=>t('Creator'),'field'=>'users_name');
    $headers[1]=array('data'=>t('Channel'),'field'=>'node_title');
    //$headers[1]=array('data'=>t('Type'),'field'=>'node_type');
    //$headers[2]=array('data'=>t('Service'),'field'=>'node_data_field_fuente_canal_field_fuente_canal_value');
    $headers[2]=array('data'=>t('Group'),'field'=>'og_ancestry_group_nid');
    //$headers[4]=array('data'=>t(t('Date')),'field'=>'node_created');
    $headers[3]=array('data'=>t('Last download'),'field'=>'node_title','class'=>'gestion_canales_last_download');  
    $headers[4]=array('data'=>t(t('Last update')),'field'=>'node_created');
    $headers[5]=t('Actions');
    $my_limit=30;
    
    $filter_fields=contenidos_canales_define_filter_fields();
   
    $where=array();
    $where[]='1';
    $where[]='users.uid = '.$user->uid;
    $where[]='node.type in ("canal_de_supercanal","canal_de_yql","canal_busqueda")';
    
    if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f,'contenidos_canales_filtro');
           if(!empty($v)){
                switch($f){
                    case 'grupo_nid':
                        //intelsat-2016
                        //$where[]='og_ancestry.group_nid='.$v;
                        $grupo_nid_array=array_keys($v);
                        $where[]='og_ancestry.group_nid IN('.implode(',',$grupo_nid_array).')';                        
                        break;
                    case 'text':
                        //intelsat-2015
                        //$where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        $where[]=contenidos_canales_get_text_where($v);
                        break;
                    /*case 'fecha_ini':
                    case 'fecha_fin':    
                        $where[]=hontza_get_usuarios_acceso_where_time('node.changed','contenidos_filtro','fecha_ini','fecha_fin');
                        break;*/
                }
           } 
       }
   }
    
    $where[]=hontza_get_usuarios_acceso_where_time('content_field_last_import_time.field_last_import_time_value','contenidos_canales_filtro','fecha_inicio','fecha_fin');
    
    $sort='asc';
    $field='node_title';
    $is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Channel')){
            $field='node_title';
        }else if($order==t('Type')){
            $field='node_type';
        }else if($order==t('Service')){
            $field='node_data_field_fuente_canal_field_fuente_canal_value';
        }else if($order==t('Group')){
            $field='og_ancestry_group_nid';
        }else if($order==t('Date')){
            $field='node_created';
        }else if($order==t('Last update')){
            //$is_numeric=1;
            $field='node_created';
        }else if($order==t('Last download')){
            //$is_numeric=1;
            $field='node_created';
        }
    }
    //
    //intelsat-2015
    $left_join_canal_hound_parametros=contenidos_canales_get_left_join_canal_hound_parametros();
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    node.type AS node_type,
    node_data_field_fuente_canal.field_fuente_canal_value AS node_data_field_fuente_canal_field_fuente_canal_value,
    node.vid AS node_vid,
    og_ancestry.nid AS og_ancestry_nid,
    node.created AS node_created,
    content_field_canal_active_refresh.field_canal_active_refresh_value AS node_status,
    node.uid AS node_uid,
    node_revisions.format AS node_revisions_format,
    node.changed AS node_changed,
    node.status AS node_status, 
    og_ancestry.group_nid AS og_ancestry_group_nid 
    FROM {node} node 
    INNER JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {content_field_fuente_canal} node_data_field_fuente_canal ON node.vid = node_data_field_fuente_canal.vid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    LEFT JOIN {content_field_canal_active_refresh} content_field_canal_active_refresh ON node.vid=content_field_canal_active_refresh.vid
    LEFT JOIN {content_field_last_import_time} content_field_last_import_time ON node.vid=content_field_last_import_time.vid 
    '.$left_join_canal_hound_parametros.' 
    LEFT JOIN {feeds_source} feeds_source ON node.nid=feeds_source.feed_nid     
    WHERE '.implode(' AND ',$where).' 
    ORDER BY '.$field.' '.$sort;
    //
    $res=db_query($sql);            
    $rows=array();
    $kont=0;
    $faktore=50;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $node=node_load($r->nid);
      $rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';
      /*$user_image=hontza_grupos_mi_grupo_get_user_img($r->node_uid,$faktore);
      $rows[$kont][1]=$user_image;*/  
      $rows[$kont][1]=l(red_funciones_resumir_titulo($r->node_title),'node/'.$r->nid);
      $node_type_label=contenidos_canales_get_node_type_label($r);  
      /*$rows[$kont][1]=$node_type_label;
      $canal_service=contenidos_canales_get_service($r);
      $rows[$kont][2]=$canal_service;*/
      $grupo_nid=$r->og_ancestry_group_nid;
      $rows[$kont][2]=contenidos_get_grupo_link($grupo_nid,$grupo_title);
      $last_download=contenido_canales_get_last_download($r->nid,$last_download_time);
      $rows[$kont][3]=$last_download;
      //$rows[$kont][2]=date('Y-m-d H:i',$r->node_created);
      $last_update=contenido_canales_get_last_update($node,$r->node_changed,$last_update_time); 
      $rows[$kont][4]=$last_update;
      $activated_label=gestion_canales_get_activated_value_label($node,1,1);
      $rows[$kont][5]=array('data'=>contenidos_canales_define_acciones($r,$activated_label),'style'=>'white-space:nowrap;');
      $rows[$kont]['node_type_label']=$node_type_label;
      $rows[$kont]['grupo_title']=$grupo_title;
      $rows[$kont]['canal_service']=$canal_service;
      //intelsat-2015
      $rows[$kont]['last_update']=$last_update_time;
      $rows[$kont]['last_download']=$last_download_time;
      //
      $kont++;
    }
    //
    if($order==t('Type')){
        $rows=array_ordenatu($rows,'node_type_label', $sort,0);        
    }else if($order==t('Last update')){
        $rows=array_ordenatu($rows,'last_update', $sort,$is_numeric);        
    }else if($order==t('Last download')){
        $rows=array_ordenatu($rows,'last_download', $sort,$is_numeric);        
    }else if($order==t('Group')){
        //intelsat-2015
        $rows=array_ordenatu($rows,'grupo_title', $sort,$is_numeric);        
    }
    $rows=hontza_unset_array($rows,array('node_type_label','grupo_title','canal_service','last_update','last_download'));
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_grupos'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //$output=contenidos_my_menu().contenidos_canales_define_filtro().$output;
    $output=contenidos_canales_define_filtro().drupal_get_form('contenidos_canales_bulk_form',array($output));
    return $output;
}
function contenidos_canales_get_node_type_label($row){    
    $result='';
    if($row->node_type=='canal_de_yql'){
        $node=node_load($row->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $canal_yql_parametros_row=hontza_get_canal_yql_parametros_row($node->vid,$node->nid);
            $is_simple=hontza_get_canal_yql_is_simple_by_params($canal_yql_parametros_row);            
        }        
        if(hontza_is_hound_canal($row->nid)){
            if($is_simple){
                return t('Hound Channel');
            }else{
                return t('Hound Filter Channel');
            }
        }else{
            if($is_simple){
                return t('RSS Channel');
            }else{
                return t('RSS Filter Channel');
            }
        }        
    }
    if($row->node_type=='canal_de_supercanal'){
        $result=t('Source Channel');
    }else if($row->node_type=='canal_de_yql'){
        $result=t('RSS Filter Channel');
    }else if($row->node_type=='canal_busqueda'){
        $result=t('Search Channel');
    }
    //    
    return $s;
}
function contenidos_canales_get_service($row){
            if($row->node_type=='canal_busqueda'){
               $row->node_status=1;
               if($row->node_data_field_fuente_canal_field_fuente_canal_value=='Search'){
                return t('Search');
               }
            }else if(in_array($row->node_type,array('canal_de_supercanal','canal_de_yql'))){
                $node=node_load($row->nid);
                if(isset($node->nid) && !empty($node->nid)){    
                    if($row->node_type=='canal_de_yql'){
                        if(hontza_is_hound_canal('',$node)){
                            return t('Hound');
                        }else{
                            if($row->node_data_field_fuente_canal_field_fuente_canal_value=='YQL'){
                                return t('RSS');
                            }
                        }
                        return $row->node_data_field_fuente_canal_field_fuente_canal_value;
                    }else{
                        return 'PIPE';
                    }
                }    
            }
    return '';    
}
function contenidos_canales_define_acciones($r,$is_activated){
    //return contenidos_define_acciones($r,drupal_get_destination());
    $html=array();
    //intelsat-2015
    $destination='destination=contenidos/canales';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    //$html[]=panel_admin_items_define_accion_publish($r,$destination);
    $html[]=gestion_canales_get_activar_canal_link($r->nid,$is_activated);
    //intelsat-2015
    $html[]=gestion_canales_get_canal_noticias_link($r->nid);
    $html[]=gestion_canales_get_actualizar_noticias_link($r->nid,$destination);
    return implode('&nbsp;',$html);
}
function contenidos_canales_define_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function contenidos_canales_define_filtro(){
    my_add_buscar_js();
    return drupal_get_form('contenidos_canales_filtro_form');
}
function contenidos_canales_filtro_form(){
    $fs_title=t('Search');
    if(contenidos_canales_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    /*$form['file_buscar_fs']['node_type']=array(
        '#type'=>'select',
        '#title'=>t('Select the type of Source'),
        '#options'=>contenidos_canales_define_node_type_options(),
        '#default_value'=>contenidos_canales_get_filter_value('node_type'),
    );*/
    contenidos_add_filter_form_fields('contenidos_canales_filtro',$form);
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
function contenidos_canales_get_filter_value($field){
    return hontza_get_gestion_usuarios_filter_value($field,'contenidos_canales_filtro');
}
function contenidos_canales_define_node_type_options(){
    $result=array();
    $result['']='';
    $result['canal_busqueda']=t('Search Channel');
    $result['canal_de_supercanal']=t('Source Channel');
    $result['canal_de_yql']=t('RSS Filter Channel');
    return $result;
}
function contenidos_canales_is_filter_activated(){
    $fields=contenidos_fuentes_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['contenidos_canales_filtro']['filter'][$f]) && !empty($_SESSION['contenidos_canales_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function contenidos_canales_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='contenidos_canales_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=contenidos_canales_define_filter_fields();
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
function contenido_canales_get_last_update($node,$node_changed,&$last_update_time){
    $last_update=get_canal_last_import_time_format($node,1);
    if(!empty($last_update)){
        $result=date('d-m-Y H:i',$last_update);
        $last_update_time=$last_update;
    }else{
        $result=date('d-m-Y H:i',$node_changed);
        $last_update_time=$node_changed;
    }
    return $result;   
}
function contenido_canales_get_last_download($nid,&$last_content){
    $last_content_en=get_canal_last_update_date_format($nid,'en');
    $last_content=strtotime($last_content_en);
    if(empty($last_content)){
        $last_content=0;
        return '';
    }else{
        return date('d-m-Y H:i',$last_content);
    }
    return '';
}
function contenidos_canales_bulk_form(){
    $vars=func_get_args();
    $table=$vars[1][0];
    return contenidos_canales_bulk($table);
}
function contenidos_canales_bulk($table){
    $form=array();
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    $form['my_bulk_operations_fs']['delete_btn']=array(
      //'#type'=>'submit',
      '#type'=>'image_button',
      //'#value'=>t('Delete node'),
      '#name'=>'delete_btn',
      '#src'=>'sites/all/themes/buho/images/icons/delete.png',
      '#attributes'=>array('alt'=>t('Delete node'),'title'=>t('Delete')),
    );
        $form['my_bulk_operations_fs']['activate_btn']=array(
          //'#type'=>'submit',
          '#type'=>'image_button',  
          //'#value'=>t('Publish'),
          '#name'=>'activate_btn',
          '#src'=>'sites/all/themes/buho/images/icons/active_user.png',
          '#attributes'=>array('alt'=>t('Activate'),'title'=>t('Activate')),  
        );
        $form['my_bulk_operations_fs']['deactivate_btn']=array(
          //'#type'=>'submit',
          '#type'=>'image_button',   
          //'#value'=>t('Unpublish'),
          '#name'=>'deactivate_btn',
          '#src'=>'sites/all/themes/buho/images/icons/deactive_user.png',
          '#attributes'=>array('alt'=>t('Deactivate'),'title'=>t('Deactivate')),    
        );
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$table);
    //
    return $form;
}
function contenidos_canales_bulk_form_submit($form, &$form_state) {
    contenidos_canales_bulk_submit($form,$form_state,'contenidos_canales');
}
function contenidos_canales_bulk_submit($form,&$form_state,$type=''){
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('delete_btn','activate_btn','deactivate_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['block_gestion_canales_nid_array']=$nid_array;                
            if(strcmp($button_name,'delete_btn')==0){
                if($type=='contenidos_canales'){
                    drupal_goto('contenidos/canales/delete_canales_confirm');
                }else{    
                    drupal_goto('gestion/gestion_canales/delete_canales_confirm');
                }
            }else if(strcmp($button_name,'activate_btn')==0){
                if($type=='contenidos_canales'){
                    drupal_goto('contenidos/canales/activate_confirm');
                }else{
                    drupal_goto('gestion/gestion_canales/activate_confirm');
                }    
            }else if(strcmp($button_name,'deactivate_btn')==0){
                if($type=='contenidos_canales'){
                    drupal_goto('contenidos/canales/deactivate_confirm');
                }else{
                    drupal_goto('gestion/gestion_canales/deactivate_confirm');
                }    
            }
        }
    }
}
function contenidos_canales_delete_canales_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_gestion_canales_nid_array','contenidos/canales');
    return $form;
}
function contenidos_canales_delete_canales_confirm_form_submit($form,&$form_state){
    global $base_url;
    $url=$base_url.'/contenidos/canales';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function contenidos_canales_activate_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('activate_channel','block_gestion_canales_nid_array','contenidos/canales');
    return $form;
}
function contenidos_canales_activate_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/contenidos/canales';
    call_bulk_confirm_form_submit($form,$form_state,'activate_channel',$url,true);
}
function contenidos_canales_deactivate_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('activate_channel','block_gestion_canales_nid_array','contenidos/canales');
    return $form;
}
function contenidos_canales_deactivate_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/contenidos/canales';
    call_bulk_confirm_form_submit($form,$form_state,'deactivate_channel',$url,true);
}
//intelsat-2015
function contenidos_canales_get_text_where($v){
    $or=array();
    $or[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
    if(hontza_is_hound_text_input()){
        $or[]='(canal_hound_parametros.hound_title LIKE "%%'.$v.'%%")';
        $or[]='(canal_hound_parametros.hound_id LIKE "%%'.$v.'%%")';
        $or[]='(canal_hound_parametros.parametros LIKE "%%'.$v.'%%")';
    }
    $or[]='(feeds_source.config LIKE "%%'.$v.'%%")';
    $or[]='(feeds_source.source LIKE "%%'.$v.'%%")';
    $or[]='(feeds_source.config LIKE "%%'.urlencode($v).'%%")';
    $or[]='(feeds_source.source LIKE "%%'.urlencode($v).'%%")';
    return '('.implode(' OR ',$or).')';                        
}
//intelsat-2015
function contenidos_canales_get_left_join_canal_hound_parametros(){
    $result='';
    if(hontza_is_hound_text_input()){
        $result=' LEFT JOIN {canal_hound_parametros} canal_hound_parametros ON node.vid=canal_hound_parametros.vid ';
    }
    return $result;
}        