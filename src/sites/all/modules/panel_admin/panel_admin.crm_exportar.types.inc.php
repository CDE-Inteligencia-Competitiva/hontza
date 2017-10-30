<?php
function panel_admin_crm_exportar_types_callback(){
    $output='';
    crm_exportar_categorias_access_denied();
    //return panel_admin_crm_exportar_listas_migrar_crm_exportar_textos_listas();
    $headers=array();
    $headers[0]=array('field'=>'name','data'=>t('Name'));
    $headers[1]=t('Actions');
    //$headers[1]=t('Actions');    
   
    $filter_fields=panel_admin_crm_exportar_types_define_filter_fields();
    $key='panel_admin_crm_exportar_types_filtro';

    $where=array();
    $where[]='1';

    
   
  if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=panel_admin_crm_exportar_get_filter_value($f,$key);
           if(!empty($v)){
                switch($f){
                    case 'name':                        
                        $where[]='crm_exportar_listas_types.'.$f.' LIKE "%%'.$v.'%%"';
                        break;                    
                }
           } 
       }
   }


    $my_limit=30;
    
    $sort='asc';
    $field='name';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Name')){
            $field='name';
        }
    }
    
    $sql='SELECT *
    FROM {crm_exportar_listas_types} crm_exportar_listas_types    
    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->name;
      $rows[$kont][1]=array('class'=>'td_nowrap','data'=>panel_admin_crm_exportar_types_define_acciones($r));
      $kont++;
    }
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
   
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Types Management'));
    //
    return panel_admin_crm_exportar_types_header().$output;
}
function panel_admin_crm_exportar_types_define_filter_fields(){
    $result=array('name');
    return $result;
}
function panel_admin_crm_exportar_types_header(){
    my_add_buscar_js();
    $output=drupal_get_form('panel_admin_crm_exportar_types_filtro_form');
    $output.=panel_admin_crm_exportar_types_menu();
    return $output;
}
function panel_admin_crm_exportar_types_menu(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $html[]='<li>'.l(t('Create Type'),'panel_admin/crm_exportar/types/create').'</li>';
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    

    return implode('',$html);
}
function panel_admin_crm_exportar_types_filtro_form(){ 
    $fs_title=t('Search');
    if(!panel_admin_crm_exportar_types_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $key='panel_admin_crm_exportar_types_filtro';
    $form['file_buscar_fs']['name']=array(
            '#type'=>'textfield',
            '#title'=>t('Name'),
            '#default_value'=>panel_admin_crm_exportar_get_filter_value('name',$key),
        );
        

    $form['file_buscar_fs']['file_buscar_fs']['my_submit']=array(
        '#type'=>'submit',
        '#value'=>t('Apply'),
    );
    $form['file_buscar_fs']['file_buscar_fs']['limpiar']=array(
        '#type'=>'submit',
        '#name'=>'limpiar',
        '#value'=>t('Reset'),
    );
    return $form;   
}
function panel_admin_crm_exportar_types_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_crm_exportar_types_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_crm_exportar_types_define_filter_fields();
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
function panel_admin_crm_exportar_types_is_filter_activated(){
    $fields=panel_admin_crm_exportar_types_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_crm_exportar_types_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_crm_exportar_types_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_crm_exportar_types_form(){
    $form=array();
    $id='';
    $param=arg(4);
    $row=new stdClass();
    $title=t('Create Type');
    $name='';
    if(!empty($param) && $param=='edit'){
        $id=arg(3);
        $row=panel_admin_crm_exportar_types_get_crm_exportar_types_row($id);
        //echo print_r($row,1);exit();
        $form['my_id']=array(
            '#type'=>'hidden',
            '#default_value'=>$id,
        );
        $title=t('Edit Type');
        $name=$row->name;
    }
    $form['name']=array(
        '#type'=>'textfield',
        '#title'=>t('Name'),
        '#default_value'=>$name,
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#name'=>'save_btn',
        '#value'=>t('Save'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'panel_admin/crm_exportar/types'),
    );
    drupal_set_title($title);
    return $form;
}
function panel_admin_crm_exportar_types_form_submit($form, &$form_state){
    $id='';
    $name='';   
    if(isset($form_state['values']['my_id']) && !empty($form_state['values']['my_id'])){
        $id=$form_state['values']['my_id'];
    }
    if(isset($form_state['values']['name']) && !empty($form_state['values']['name'])){
        $name=$form_state['values']['name'];
    }    
    if(!empty($id)){
        db_query('UPDATE {crm_exportar_listas_types} SET name="%s" WHERE id=%d',$name,$id);        
    }else{
        db_query('INSERT INTO {crm_exportar_listas_types}(name) VALUES("%s")',$name);
    }   
    drupal_set_message(t('Type saved'));
    drupal_goto('panel_admin/crm_exportar/types');
}
function panel_admin_crm_exportar_types_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/crm_exportar/types';
    //$html[]=l(my_get_icono_action('ver_noticias',t('List of Searches')),'panel_admin/crm_exportar/listas/'.$r->id.'/ver_clientes',array('html'=>true));
    //$html[]=l(my_get_icono_action('crm_exportar_add',t('Create Search')),'panel_admin/crm_exportar/create/'.$r->id,array('html'=>true));               
    $html[]=l(my_get_icono_action('edit',t('Edit')),'panel_admin/crm_exportar/types/'.$r->id.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'panel_admin/crm_exportar/types/'.$r->id.'/delete',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function panel_admin_crm_exportar_types_get_crm_exportar_types_row($id,$is_row=0){
    //if(!empty($id)){
        $result=panel_admin_crm_exportar_types_get_crm_exportar_types_array($id,$is_row);
        if(count($result)>0){
            return $result[0];
        }        
    //}
    $my_result=new stdClass();
    return $my_result;
}
function panel_admin_crm_exportar_types_get_crm_exportar_types_array($id='',$is_row=0){
  $result=array();
  $where=array();
  if(empty($where_in)){
    $where[]='1';
  }
  
  if(!empty($id)){
    $where[]='crm_exportar_listas_types.id='.$id;
  }else{
    if($is_row){
        return $result;
    }
  }
  
  $sql='SELECT crm_exportar_listas_types.* 
  FROM {crm_exportar_listas_types} 
  WHERE '.implode(' AND ',$where).' 
  ORDER BY id ASC';

  //print $sql;exit();  
  $res=db_query($sql);
  while($row=db_fetch_object($res)){
    $result[]=$row;
  }
  return $result;
}
function panel_admin_crm_exportar_types_delete_form(){
    $form=array();
        $id=arg(3);
        $row=panel_admin_crm_exportar_types_get_crm_exportar_types_row($id);
        $form['my_id']=array(
            '#type'=>'hidden',
            '#default_value'=>$id,
        );
       $form['name']=array(
        '#type'=>'textfield',
        '#title'=>t('Name'),
        '#default_value'=>$row->name,
        '#attributes'=>array('readonly'=>'readonly'),
        );
        $form['delete_btn']=array(
            '#type'=>'submit',
            '#value'=>t('Delete'),
        );
        $form['cancel_btn']=array(
            '#value'=>l(t('Cancel'),'panel_admin/crm_exportar/types'),
        );
        drupal_set_title(t('Delete Type'));
    return $form;
}
function panel_admin_crm_exportar_types_delete_form_submit($form, &$form_state){
    $id='';
    if(isset($form_state['values']) && !empty($form_state['values'])){
        $values=$form_state['values'];
        if(isset($values['my_id']) && !empty($values['my_id'])){
            $id=$values['my_id'];
            db_query('DELETE FROM {crm_exportar_listas_types} WHERE id=%d',$id);                           
        }
    }
    drupal_goto('panel_admin/crm_exportar/types');   
}
function panel_admin_types_crm_exportar_is_crm_list_type_activado(){
    if(module_exists('crm_exportar')){
        return crm_exportar_is_crm_list_type_activado();                        
    }
    return 0;
}
function panel_admin_crm_exportar_types_get_type_name($r){
    if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
        $is_row=1;
        $crm_exportar_listas_types_row=panel_admin_crm_exportar_types_get_crm_exportar_types_row($r->crm_exportar_listas_types_id,$is_row);
        if(isset($crm_exportar_listas_types_row->name) && !empty($crm_exportar_listas_types_row->name)){
            return $crm_exportar_listas_types_row->name;
        }
    }    
    return '';
}     