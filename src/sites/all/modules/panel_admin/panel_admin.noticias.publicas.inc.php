<?php
function panel_admin_noticias_publicas_callback(){
    my_add_buscar_js();
    $output=drupal_get_form('panel_admin_noticias_publicas_filtro_form');
    $output.=drupal_get_form('my_noticias_publicas_form');
    return $output;
}
function panel_admin_noticias_publicas_filtro_form(){
    $fs_title=t('Search');
    if(panel_admin_noticias_publicas_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    contenidos_add_filter_form_fields('panel_admin_noticias_publicas_filtro',$form);
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
function panel_admin_noticias_publicas_is_filter_activated(){
    $fields=panel_admin_noticias_publicas_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_noticias_publicas_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_noticias_publicas_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_noticias_publicas_define_filter_fields(){
    $result=array('grupo_nid','text','fecha_inicio','fecha_fin');
    return $result;
}
function panel_admin_noticias_publicas_is_gestion_noticias_publicas(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='gestion'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='my_noticias_publicas'){
            return 1;
        }
    }
    return 0;
}
function panel_admin_noticias_publicas_add_where_filtro($where_in){
    $where=$where_in;
    
    $filter_fields=panel_admin_noticias_publicas_define_filter_fields();
       
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=hontza_get_gestion_usuarios_filter_value($f,'panel_admin_noticias_publicas_filtro');
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
    
    $where[]=hontza_get_usuarios_acceso_where_time('node.changed','panel_admin_noticias_publicas_filtro','fecha_inicio','fecha_fin');
    
    return $where;
}
function panel_admin_noticias_publicas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_noticias_publicas_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_noticias_publicas_define_filter_fields();
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