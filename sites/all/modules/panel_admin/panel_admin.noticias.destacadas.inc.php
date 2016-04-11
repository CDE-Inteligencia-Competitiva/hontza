<?php
function panel_admin_noticias_destacadas_callback(){
    my_add_buscar_js();
    $output=drupal_get_form('panel_admin_noticias_destacadas_filtro_form');
    $noticias_destacadas_html=my_noticias_destacadas_callback();
    $output.=drupal_get_form('panel_admin_noticias_destacadas_bulk_form',array($noticias_destacadas_html));    
    return $output;
}
function panel_admin_noticias_destacadas_is_gestion_noticias_destacadas(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='gestion'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='my_noticias_destacadas'){
            return 1;
        }
    }
    return 0;
}
function panel_admin_noticias_destacadas_add_where_filtro($where_in){
    $where=$where_in;
    
    $filter_fields=panel_admin_noticias_destacadas_define_filter_fields();
       
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_noticias_destacadas_filtro');
           if(!empty($v)){
                switch($f){
                    case 'grupo_nid':
                        $where[]='og_ancestry.group_nid='.$v;
                        break;
                    case 'text':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                    /*case 'fecha_ini':
                    case 'fecha_fin':    
                        $where[]=hontza_get_usuarios_acceso_where_time('node.changed','contenidos_filtro','fecha_ini','fecha_fin');
                        break;*/
                }
           } 
       }
   }
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_noticias_destacadas_filtro','fecha_inicio','fecha_fin');
    
    return $where;
}
function panel_admin_noticias_destacadas_define_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_noticias_destacadas_filtro_form(){
    $fs_title=t('Search');
    if(panel_admin_noticias_destacadas_is_filter_activated()){
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
        '#options'=>panel_admin_noticias_destacadas_define_node_type_options(),
        '#default_value'=>panel_admin_noticias_destacadas_get_filter_value('node_type'),
    );*/
    contenidos_add_filter_form_fields('panel_admin_noticias_destacadas_filtro',$form);
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
function panel_admin_noticias_destacadas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_noticias_destacadas_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_noticias_destacadas_define_filter_fields();
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
function panel_admin_noticias_destacadas_is_filter_activated(){
    $fields=panel_admin_noticias_destacadas_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_noticias_destacadas_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_noticias_destacadas_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_noticias_destacadas_bulk_form(){
    $form=array();
    $vars=func_get_args();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    panel_admin_items_add_bulk_operations_form_fields($form,1);
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']=array('#value'=>$vars[1][0]);
    //
    return $form;
}
function panel_admin_noticias_destacadas_bulk_form_submit($form, &$form_state) {
    panel_admin_items_gestion_items_bulk_submit($form,$form_state,'panel_admin_noticias_destacadas');
}
function panel_admin_noticias_destacadas_delete_noticias_destacadas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('delete_node','block_panel_admin_noticias_destacadas_nid_array','gestion/my_noticias_destacadas');
    return $form;
}
function panel_admin_noticias_destacadas_delete_noticias_destacadas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url=$base_url.'/gestion/my_noticias_destacadas';
    call_bulk_confirm_form_submit($form,$form_state,'delete_node',$url,true);
}
function panel_admin_noticias_destacadas_publish_post_noticias_destacadas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('publish_post','block_panel_admin_noticias_destacadas_nid_array','panel_admin/noticias_destacadas');
    return $form;
}
function panel_admin_noticias_destacadas_unpublish_post_noticias_destacadas_confirm_form(){
    $form=panel_admin_grupos_get_grupos_confirm('unpublish_post','block_panel_admin_noticias_destacadas_nid_array','panel_admin/noticias_destacadas');
    return $form;
}
function panel_admin_noticias_destacadas_publish_post_noticias_destacadas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='gestion/my_noticias_destacadas';
    call_bulk_confirm_form_submit($form,$form_state,'publish_post',$url,true);    
}
function panel_admin_noticias_destacadas_unpublish_post_noticias_destacadas_confirm_form_submit(&$form, &$form_state){
    global $base_url;
    $url='gestion/my_noticias_destacadas';
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post',$url,true);    
}