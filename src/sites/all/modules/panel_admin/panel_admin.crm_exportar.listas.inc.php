<?php
function panel_admin_crm_exportar_listas_callback(){
	$output='';
	crm_exportar_categorias_access_denied();
    //return panel_admin_crm_exportar_listas_migrar_crm_exportar_textos_listas();
    $headers=array();
    $headers[0]=array('field'=>'name','data'=>t('Name'));
    $headers[1]=array('field'=>'crm_exportar_listas_types_id','data'=>t('Type'));
    $headers[2]=t('Actions');
    //$headers[1]=t('Actions');    
   
    $filter_fields=panel_admin_crm_exportar_listas_define_filter_fields();
   	$key='panel_admin_crm_exportar_listas_filtro';

    $where=array();
    $where[]='1';

    
   
  if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=panel_admin_crm_exportar_get_filter_value($f,$key);
           if(!empty($v)){
                switch($f){
                    case 'name':                        
                        $where[]='crm_exportar_listas.'.$f.' LIKE "%%'.$v.'%%"';
                        break;
                    case 'crm_exportar_listas_types_id':
                        if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
                             $where[]='crm_exportar_listas.'.$f.'='.$v;
                        }    
                        break;                        
                }
           } 
       }
   }


    $my_limit=30;
    
    $sort='asc';
    $field='name';
    $field_order='';
    $is_array_ordenatu=0;
    $is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Name')){
            $field='name';
        }else if($order==t('Type')){
            $field_order='type';
            $is_array_ordenatu=1;
        }    
    }
    
    $sql='SELECT *
    FROM {crm_exportar_listas} crm_exportar_listas   
    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->name;
      $type=panel_admin_crm_exportar_types_get_type_name($r);
      $rows[$kont][1]=$type;
      $rows[$kont][2]=array('class'=>'td_nowrap','data'=>panel_admin_crm_exportar_listas_define_acciones($r));
      $rows[$kont]['type']=$type;      
      $kont++;
    }
    
    if($is_array_ordenatu && !empty($field_order)){
        $rows=array_ordenatu($rows,$field_order,$sort,$is_numeric);
    }

    $rows=hontza_unset_array($rows,array('type'));

    $rows=my_set_estrategia_pager($rows, $my_limit);
   
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Lists Management'));
    //
    return panel_admin_crm_exportar_listas_header().$output;
}
function panel_admin_crm_exportar_listas_define_filter_fields(){
	$result=array('name');
    if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
        $result[]='crm_exportar_listas_types_id';
    }
    return $result;
}
function panel_admin_crm_exportar_listas_header(){
	my_add_buscar_js();
    $output=drupal_get_form('panel_admin_crm_exportar_listas_filtro_form');
    $output.=panel_admin_crm_exportar_listas_menu();
    return $output;
}
function panel_admin_crm_exportar_listas_filtro_form(){	
	$fs_title=t('Search');
    if(!panel_admin_crm_exportar_listas_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $key='panel_admin_crm_exportar_listas_filtro';
    $form['file_buscar_fs']['name']=array(
			'#type'=>'textfield',
			'#title'=>t('Name'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('name',$key),
		);
		
    if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
            $crm_exportar_listas_types_options=panel_admin_crm_exportar_listas_get_types_options();
            $form['file_buscar_fs']['crm_exportar_listas_types_id']=array(
                '#type'=>'select',
                '#title'=>t('Type'),
                '#default_value'=>panel_admin_crm_exportar_get_filter_value('crm_exportar_listas_types_id',$key),
                '#options'=>$crm_exportar_listas_types_options,
            );
        }    


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
function panel_admin_crm_exportar_listas_is_filter_activated(){
	$fields=panel_admin_crm_exportar_listas_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_crm_exportar_listas_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_crm_exportar_listas_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_crm_exportar_listas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_crm_exportar_listas_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_crm_exportar_listas_define_filter_fields();
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
function panel_admin_crm_exportar_listas_form(){
	$form=array();
	$id='';
	$param=arg(4);
	$row=new stdClass();
	$title=t('Create List');
	$name='';
    $crm_exportar_listas_types_id='';
	if(!empty($param) && $param=='edit'){
		$id=arg(3);
		$row=panel_admin_crm_exportar_listas_get_crm_exportar_listas_row($id);
        //echo print_r($row,1);exit();
		$form['my_id']=array(
			'#type'=>'hidden',
			'#default_value'=>$id,
		);
		$title=t('Edit List');
		$name=$row->name;
        if(isset($row->crm_exportar_listas_types_id) && !empty($row->crm_exportar_listas_types_id)){
            $crm_exportar_listas_types_id=$row->crm_exportar_listas_types_id;
        }	
    }
	$form['name']=array(
		'#type'=>'textfield',
		'#title'=>t('Name'),
		'#default_value'=>$name,
	);

        if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
            $crm_exportar_listas_types_options=panel_admin_crm_exportar_listas_get_types_options();
            $form['crm_exportar_listas_types_id']=array(
                '#type'=>'select',
                '#title'=>t('Type'),
                '#default_value'=>$crm_exportar_listas_types_id,
                '#options'=>$crm_exportar_listas_types_options,
            );
        }    
        

	$form['save_btn']=array(
        '#type'=>'submit',
        '#name'=>'save_btn',
        '#value'=>t('Save'),
    );
    $form['cancel_btn']=array(
		'#value'=>l(t('Cancel'),'panel_admin/crm_exportar/listas'),
	);
	drupal_set_title($title);
	return $form;
}
function panel_admin_crm_exportar_listas_form_submit($form, &$form_state){
	$id='';
    $name='';	
    if(isset($form_state['values']['my_id']) && !empty($form_state['values']['my_id'])){
        $id=$form_state['values']['my_id'];
    }
    if(isset($form_state['values']['name']) && !empty($form_state['values']['name'])){
		$name=$form_state['values']['name'];
	}    
	if(!empty($id)){
        db_query('UPDATE {crm_exportar_listas} SET name="%s" WHERE id=%d',$name,$id);       
    }else{
        db_query('INSERT INTO {crm_exportar_listas}(name) VALUES("%s")',$name);
        $id=db_last_insert_id('crm_exportar_listas','id'); 
    }

    panel_admin_crm_exportar_listas_types_id_save($form_state,$id);
    

    drupal_set_message(t('List saved'));
	drupal_goto('panel_admin/crm_exportar/listas');
}
function panel_admin_crm_exportar_listas_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/crm_exportar/listas';
    $html[]=l(my_get_icono_action('ver_noticias',t('List of Searches')),'panel_admin/crm_exportar/listas/'.$r->id.'/ver_clientes',array('html'=>true));
    $html[]=l(my_get_icono_action('crm_exportar_add',t('Create Search')),'panel_admin/crm_exportar/create/'.$r->id,array('html'=>true));               
    $html[]=l(my_get_icono_action('edit',t('Edit')),'panel_admin/crm_exportar/listas/'.$r->id.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'panel_admin/crm_exportar/listas/'.$r->id.'/delete',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function panel_admin_crm_exportar_listas_get_crm_exportar_listas_row($id,$crm_exportar_textos_id=''){
    //if(!empty($id)){
        $result=panel_admin_crm_exportar_listas_get_crm_exportar_listas_array($id,$crm_exportar_textos_id);
        if(count($result)>0){
            return $result[0];
        }        
    //}
    $my_result=new stdClass();
    return $my_result;
}
function panel_admin_crm_exportar_listas_get_crm_exportar_listas_array($id='',$crm_exportar_textos_id=''){
  $result=array();
  $where=array();
  if(empty($where_in)){
    $where[]='1';
  }
  //print $crm_exportar_textos_id;exit();
  if(!empty($id)){
    $where[]='crm_exportar_listas.id='.$id;
  }
  if(!empty($crm_exportar_textos_id)){
    $where[]='crm_exportar_textos_listas.crm_exportar_textos_id='.$crm_exportar_textos_id;
  }
  $sql='SELECT crm_exportar_listas.* 
  FROM {crm_exportar_listas} 
  LEFT JOIN {crm_exportar_textos_listas} ON crm_exportar_listas.id=crm_exportar_textos_listas.crm_exportar_listas_id 
  WHERE '.implode(' AND ',$where).' 
  ORDER BY id ASC';

  //print $sql;exit();  
  $res=db_query($sql);
  while($row=db_fetch_object($res)){
    $result[]=$row;
  }
  return $result;
}
function panel_admin_crm_exportar_listas_delete_form(){
    $form=array();
        $id=arg(3);
        $row=panel_admin_crm_exportar_listas_get_crm_exportar_listas_row($id);
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
            '#value'=>l(t('Cancel'),'panel_admin/crm_exportar/listas'),
        );
        drupal_set_title(t('Delete Search'));
        drupal_set_message(t('If you choose to delete the list, the entire content inside will be deleted'));

    return $form;
}
function panel_admin_crm_exportar_listas_delete_form_submit($form, &$form_state){
    $id='';
    if(isset($form_state['values']) && !empty($form_state['values'])){
        $values=$form_state['values'];
        if(isset($values['my_id']) && !empty($values['my_id'])){
            $id=$values['my_id'];
            panel_admin_crm_exportar_listas_delete_textos($id);
            db_query('DELETE FROM {crm_exportar_listas} WHERE id=%d',$id);                           
        }
    }
    drupal_goto('panel_admin/crm_exportar/listas');   
}
function panel_admin_crm_exportar_listas_ver_clientes_callback(){
    $key='panel_admin_crm_exportar_clientes_filtro';
    if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
        unset($_SESSION[$key]['filter']);
    }
    $_SESSION[$key]['filter']=array();
    $crm_exportar_listas_id=arg(3);
    $_SESSION[$key]['filter']['crm_exportar_listas_id']=$crm_exportar_listas_id;
    drupal_goto('panel_admin/crm_exportar/clientes');
    exit();    
}
function panel_admin_crm_exportar_clientes_listas_get_options($with_empty=0){
    $result=array();
    /*if($with_empty){
        $result[0]='';
    }*/
    $crm_exportar_listas_array=panel_admin_crm_exportar_listas_get_crm_exportar_listas_array();
    if(!empty($crm_exportar_listas_array)){
        foreach($crm_exportar_listas_array as $i=>$crm_exportar_listas_row){
            $result[$crm_exportar_listas_row->id]=$crm_exportar_listas_row->name;
        }
    }
    return $result;
}
function panel_admin_crm_exportar_listas_migrar_crm_exportar_textos_listas(){
    $crm_exportar_listas_id=1;
    $crm_exportar_textos_array=panel_admin_crm_exportar_textos_get_array('id');
    //echo print_r($crm_exportar_textos_array,1);exit();
    if(!empty($crm_exportar_textos_array)){
        foreach($crm_exportar_textos_array as $i=>$crm_exportar_textos_row){
            $crm_exportar_textos_listas_row=panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_row($crm_exportar_textos_row->id,$crm_exportar_listas_id);
            if(isset($crm_exportar_textos_listas_row->id) && !empty($crm_exportar_textos_listas_row->id)){
                //echo print_r($crm_exportar_textos_listas_row,1);exit();
                continue;
            }else{
                db_query('INSERT INTO crm_exportar_textos_listas(crm_exportar_textos_id,crm_exportar_listas_id) VALUES(%d,%d)',$crm_exportar_textos_row->id,$crm_exportar_listas_id);
            }
        }
    }
    return date('Y-m-d H:i:s');
}
function panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_row($crm_exportar_textos_id,$crm_exportar_listas_id){
    //print 'crm_exportar_textos_id='.$crm_exportar_textos_id;exit();
    if(!empty($crm_exportar_textos_id)){
        $crm_exportar_textos_listas_array=panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_array($crm_exportar_textos_id,$crm_exportar_listas_id);
        /*echo print_r($crm_exportar_textos_listas_array,1);
        exit();*/
        if(count($crm_exportar_textos_listas_array)>0){
            return $crm_exportar_textos_listas_array[0];
        }
    }
    $my_result=new stdClass();
    return $my_result;
}
function panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_array($crm_exportar_textos_id,$crm_exportar_listas_id){
    $result=array();
  $where=array();
  if(empty($where_in)){
    $where[]='1';
  }
  if(!empty($crm_exportar_textos_id)){
    $where[]='crm_exportar_textos_listas.crm_exportar_textos_id='.$crm_exportar_textos_id;
  }
  if(!empty($crm_exportar_listas_id)){
    $where[]='crm_exportar_textos_listas.crm_exportar_listas_id='.$crm_exportar_listas_id;
  }  
  $res=db_query('SELECT * FROM {crm_exportar_textos_listas} WHERE '.implode(' AND ',$where).' ORDER BY id ASC');
  while($row=db_fetch_object($res)){
    $result[]=$row;
  }
  return $result;
}
function panel_admin_crm_exportar_listas_crm_exportar_textos_listas_save($crm_exportar_textos_id,$crm_exportar_listas_id){
    /*print $crm_exportar_textos_id.'<br>';
    print $crm_exportar_listas_id.'<br>';
    exit();*/
    $crm_exportar_textos_listas_row=panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_row($crm_exportar_textos_id,'');
    /*echo print_r($crm_exportar_textos_listas_row,1);
    exit();*/
    if(isset($crm_exportar_textos_listas_row->id) && !empty($crm_exportar_textos_listas_row->id)){
           db_query('UPDATE {crm_exportar_textos_listas} SET crm_exportar_listas_id=%d WHERE crm_exportar_textos_id=%d',$crm_exportar_listas_id,$crm_exportar_textos_id); 
    }else{
        db_query('INSERT INTO {crm_exportar_textos_listas}(crm_exportar_textos_id,crm_exportar_listas_id) VALUES(%d,%d)',$crm_exportar_textos_id,$crm_exportar_listas_id);
    }        
}
function panel_admin_crm_exportar_listas_get_crm_exportar_listas_id_filter_value($field,$key,$result_in){
    $result=$result_in;
    if($key=='panel_admin_crm_exportar_clientes_filtro'){
        if($field=='crm_exportar_listas_id'){   
            if(empty($result)){
                $options=panel_admin_crm_exportar_clientes_listas_get_options();
                if(!empty($options)){
                    $values=array_keys($options);
                    if(count($values)>0){
                        $result=$values[0];
                    }
                }
            }
        }   
    }
    return $result;
}
function panel_admin_crm_exportar_listas_delete_crm_exportar_textos_listas_csv($crm_exportar_listas_id){
    $crm_exportar_textos_listas_array=panel_admin_crm_exportar_listas_get_crm_exportar_textos_listas_array('',$crm_exportar_listas_id);
    //echo print_r($crm_exportar_textos_listas_array,1);exit();
    if(!empty($crm_exportar_textos_listas_array)){
        foreach($crm_exportar_textos_listas_array as $i=>$crm_exportar_textos_listas_row){
            /*print $crm_exportar_textos_listas_row->crm_exportar_textos_id;
            exit();*/
            db_query('DELETE FROM {crm_exportar_textos} WHERE id=%d',$crm_exportar_textos_listas_row->crm_exportar_textos_id);
        }
    }
    db_query('DELETE FROM {crm_exportar_textos_listas} WHERE crm_exportar_listas_id=%d',$crm_exportar_listas_id);
}
function panel_admin_crm_exportar_listas_get_default_crm_exportar_listas_id(){
    $result='';
    $crm_exportar_listas_array=panel_admin_crm_exportar_listas_get_crm_exportar_listas_array();
    if(!empty($crm_exportar_listas_array)){
        $result=$crm_exportar_listas_array[0]->id;
    }
    if(empty($result)){
        $result=1;
    }
    return $result;
}
function panel_admin_crm_exportar_listas_delete_textos($crm_exportar_listas_id){
    $crm_exportar_textos_array=panel_admin_crm_exportar_textos_get_array('',$crm_exportar_listas_id);
    /*echo print_r($crm_exportar_textos_array,1);
    exit();*/
    
    db_query('DELETE FROM {crm_exportar_textos_listas} WHERE crm_exportar_listas_id=%d',$crm_exportar_listas_id);   
        
    if(!empty($crm_exportar_textos_array)){
        foreach($crm_exportar_textos_array as $i=>$crm_exportar_textos_row){
            db_query('DELETE FROM {crm_exportar_textos} WHERE id=%d',$crm_exportar_textos_row->id);
            db_query('DELETE FROM {crm_exportar_textos_listas} WHERE crm_exportar_textos_id=%d',$crm_exportar_textos_row->id);                
        }
    }    
}
function panel_admin_crm_exportar_listas_menu(){
    $html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $html[]='<li>'.l(t('Create List'),'panel_admin/crm_exportar/listas/create').'</li>';
    $html[]='</ul>';
    $html[]='</div>';
    $html[]='</div>';
    

    return implode('',$html);
}
function panel_admin_crm_exportar_listas_get_types_options(){
    $result=array();
    $result[0]='';
    $crm_exportar_listas_types_array=panel_admin_crm_exportar_types_get_crm_exportar_types_array();
    if(!empty($crm_exportar_listas_types_array)){
        foreach($crm_exportar_listas_types_array as $i=>$crm_exportar_listas_types_row){
            $result[$crm_exportar_listas_types_row->id]=$crm_exportar_listas_types_row->name;
        }
    }
    return $result;
}
function panel_admin_crm_exportar_listas_types_id_save(&$form_state,$id){
    if(panel_admin_types_crm_exportar_is_crm_list_type_activado()){
        if(isset($form_state['values']['crm_exportar_listas_types_id']) && !empty($form_state['values']['crm_exportar_listas_types_id'])){
            $crm_exportar_listas_types_id=$form_state['values']['crm_exportar_listas_types_id'];
        }
        //print $id;exit();
        db_query('UPDATE {crm_exportar_listas} SET crm_exportar_listas_types_id=%d WHERE id=%d',$crm_exportar_listas_types_id,$id);
    }
}