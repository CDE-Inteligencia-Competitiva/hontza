<?php
function panel_admin_crm_exportar_filtro_ip_callback(){
  $output='';
  //simulando
  //return 'Desactivado';
    $headers=array();
    $headers[0]=array('field'=>'value','data'=>'Ip');
    $headers[1]=array('field'=>'value','data'=>t('Name'));    
    $headers[2]=t('Actions');    
   
    $filter_fields=panel_admin_crm_exportar_define_filtro_ip_filter_fields();
   	$key='panel_admin_crm_exportar_filtro_ip_filtro';

    $where=array();
    $where[]='1';

    
   
  if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=panel_admin_crm_exportar_filtro_ip_get_filter_value($f,$key);
           if(!empty($v)){
                switch($f){
                    case 'ip':                        
                    case 'name':                                            
                        $where[]='crm_exportar_ip.'.$f.' LIKE "%%'.$v.'%%"';
                        break;
                    case 'account_number':    
                        $where[]='crm_exportar_ip.'.$f.'="'.$v.'"';
                        break;
                }
           } 
       }
   }


    $my_limit=30;
    
    $sort='asc';
    $field='ip';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order=='Ip'){
            $field='ip';
        }else if($order==t('Name')){
            $field='name';
        }
    }
    
    $sql='SELECT *
    FROM {crm_exportar_ip} crm_exportar_ip  
    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->ip;
      $rows[$kont][1]=$r->name;
      $rows[$kont][2]=array('class'=>'td_nowrap','data'=>panel_admin_crm_exportar_filtro_ip_define_acciones($r));
      $kont++;
    }
    //
    /*if($order==t('Title')){
        $rows=array_ordenatu($rows,'node_title', $sort,0);        
    }*/
    //intelsat-2016
    //$unset_array=array('node_title');
    //$unset_array=array('node_title','nid','tnid');
    //$rows=hontza_unset_array($rows,$unset_array);
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
   
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('IP filter'));
    //
    return panel_admin_crm_exportar_filtro_ip_header().$output;
}
function panel_admin_crm_exportar_define_filtro_ip_filter_fields(){
    $result=array('ip','name');
    return $result;
}
function panel_admin_crm_exportar_filtro_ip_get_filter_value($field,$key){
    return hontza_get_gestion_usuarios_filter_value($field,$key);
}
function panel_admin_crm_exportar_filtro_ip_header(){
    $html=array();
    my_add_buscar_js();
    $html[]=panel_admin_crm_exportar_filtro_ip_get_header_links();
    $html[]=drupal_get_form('panel_admin_crm_exportar_filtro_ip_filtro_form');
    return implode('',$html);
}
function panel_admin_crm_exportar_filtro_ip_filtro_form(){
    $fs_title=t('Search');
    if(!panel_admin_crm_exportar_filtro_ip_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $key='panel_admin_crm_exportar_filtro_ip_filtro';
    $form['file_buscar_fs']['ip']=array(
			'#type'=>'textfield',
			'#title'=>'Ip',
			'#default_value'=>panel_admin_crm_exportar_filtro_ip_get_filter_value('ip',$key),
		);
	 $form['file_buscar_fs']['name']=array(
            '#type'=>'textfield',
            '#title'=>t('Name'),
            '#default_value'=>panel_admin_crm_exportar_filtro_ip_get_filter_value('name',$key),
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
function panel_admin_crm_exportar_filtro_ip_get_header_links(){
    $html=array();
    $html[]=l(t('Create'),'panel_admin/crm_exportar/filtro_ip/create');
    $html[]=l(t('Activate Ip Filter'),'panel_admin/crm_exportar/filtro_ip/activar');
    return implode('&nbsp;|&nbsp;',$html);
}
function panel_admin_crm_exportar_filtro_ip_form(){
    $form=array();
    $title='Create Ip';
    $id=arg(3);
    $action=arg(4);
    $ip='';
    $row='';
    $name='';
    if($action=='edit'){
        $title='Edit Ip';
        $form['my_id']=array(
            '#type'=>'hidden',
            '#value'=>$id,
        );
        $row=panel_admin_crm_exportar_filtro_ip_get_row($id);
        $ip=$row->ip;
        $name=$row->name;
    }
    $form['ip']=array(
        '#type'=>'textfield',
        '#title'=>'Ip',
        '#default_value'=>$ip,
    );
    $form['my_name']=array(
        '#type'=>'textfield',
        '#title'=>'Name',
        '#default_value'=>$name,
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'panel_admin/crm_exportar/filtro_ip'),
    );
    drupal_set_title(t($title));
    return $form;
}
function panel_admin_crm_exportar_filtro_ip_form_submit($form, &$form_state){
	$id='';
    $name='';
	if(isset($form_state['values']) && !empty($form_state['values'])){
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];			
		}
		$ip=trim($values['ip']);
		if(isset($values['my_name']) && !empty($values['my_name'])){
            $name=$values['my_name'];           
        }
        if(empty($id)){
			db_query('INSERT INTO {crm_exportar_ip}(ip,name) VALUES("%s","%s")',$ip,$name);
		}else{
			db_query('UPDATE {crm_exportar_ip} SET ip="%s",name="%s" WHERE id=%d',$ip,$name,$id);
		}
	}
	drupal_goto('panel_admin/crm_exportar/filtro_ip');	
}
function panel_admin_crm_exportar_filtro_ip_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/crm_exportar/filtro_ip';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'panel_admin/crm_exportar/filtro_ip/'.$r->id.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'panel_admin/crm_exportar/filtro_ip/'.$r->id.'/delete',array('query'=>$destination,'html'=>true));
    //$html[]=l(my_get_icono_action('viewmag',t('View')),'node/'.$r->nid,array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function panel_admin_crm_exportar_filtro_ip_get_row($id){
    $result=panel_admin_crm_exportar_filtro_ip_get_array($id);
    if(count($result)>0){
        return $result[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function panel_admin_crm_exportar_filtro_ip_get_array($id){
    $result=array();
    $res=db_query('SELECT * FROM {crm_exportar_ip} WHERE crm_exportar_ip.id=%d'.$id);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function panel_admin_crm_exportar_filtro_ip_delete_form(){
    $form=array();
    $title='Delete Ip';
    $id=arg(3);
    $ip='';
    $name='';
    $row='';
        $form['my_id']=array(
            '#type'=>'hidden',
            '#value'=>$id,
        );
        $row=panel_admin_crm_exportar_filtro_ip_get_row($id);
        $ip=$row->ip;
        $name=$row->name;    
    $form['ip']=array(
        '#type'=>'textfield',
        '#title'=>'Ip',
        '#default_value'=>$ip,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $form['name']=array(
        '#type'=>'textfield',
        '#title'=>t('Name'),
        '#default_value'=>$name,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $form['delete_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Delete'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'panel_admin/crm_exportar/filtro_ip'),
    );
    drupal_set_title(t($title));
    return $form;
}
function panel_admin_crm_exportar_filtro_ip_delete_form_submit($form, &$form_state){
	$id='';
	if(isset($form_state['values']) && !empty($form_state['values'])){
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];
			db_query('DELETE FROM {crm_exportar_ip} WHERE id=%d',$id);			
		}
	}
	drupal_goto('panel_admin/crm_exportar/filtro_ip');	
}
function panel_admin_crm_exportar_filtro_ip_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_crm_exportar_filtro_ip_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_crm_exportar_define_filtro_ip_filter_fields();
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
function panel_admin_crm_exportar_filtro_ip_is_filter_activated(){
	$fields=panel_admin_crm_exportar_define_filtro_ip_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_crm_exportar_filtro_ip_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_crm_exportar_filtro_ip_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_crm_exportar_filtro_ip_activar_form(){
    $form=array();    
    $is_crm_exportar_filtro_ip_activar=variable_get('is_crm_exportar_filtro_ip_activar',0);
    $form['is_crm_exportar_filtro_ip_activar']=array(
        '#type'=>'checkbox',
        '#title'=>'Activate',
    );
    if($is_crm_exportar_filtro_ip_activar){
        $form['is_crm_exportar_filtro_ip_activar']['#attributes']=array('checked'=>'checked');
    }
    $form['save_btn']=array(
			'#type'=>'submit',
			'#value'=>t('Save'),
		);
		$form['cancel_btn']=array(
			'#value'=>l(t('Cancel'),'panel_admin/crm_exportar/filtro_ip'),
		);
    drupal_set_title('Activate Ip Filter');    
    return $form;
}
function panel_admin_crm_exportar_filtro_ip_activar_form_submit(&$form, &$form_state){
    $is_crm_exportar_filtro_ip_activar=0;
    if(isset($form_state['values']['is_crm_exportar_filtro_ip_activar']) && !empty($form_state['values']['is_crm_exportar_filtro_ip_activar'])){
        $is_crm_exportar_filtro_ip_activar=1;
    }
    variable_set('is_crm_exportar_filtro_ip_activar',$is_crm_exportar_filtro_ip_activar);
    drupal_goto('panel_admin/crm_exportar/filtro_ip');
}