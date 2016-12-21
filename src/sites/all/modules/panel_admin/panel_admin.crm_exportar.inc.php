<?php
function panel_admin_crm_exportar_left_title(){
	return t('CRM EXPORT');
}
function panel_admin_crm_exportar_block_content(){
	$html=array();
	$html[]=l(t('Delete tags'),'panel_admin/crm_exportar/delete_tags');
	$html[]=l(t('Import'),'panel_admin/crm_exportar/textos/importar');
	$html[]=l(t('Create Client'),'panel_admin/crm_exportar/create');	
	$html[]=l(t('Clients'),'panel_admin/crm_exportar/clientes');
	return implode('<BR>',$html);
}
function panel_admin_crm_exportar_delete_tags_form(){
	$form=array();
	$form['delete_btn']=array(
		'#type'=>'submit',
		'#value'=>t('Delete'),
	);
	$form['cancel_btn']=array(
		'#value'=>l(t('Cancel'),'panel_admin'),
	);
	return $form;
}
function panel_admin_crm_exportar_delete_tags_form_submit($form,&$form_state){
    panel_admin_crm_exportar_delete_tags();
    $destination='panel_admin';
    drupal_goto($destination);
}
function panel_admin_crm_exportar_delete_tags(){
	$vid=hontza_crm_inc_get_tags_vid();
	$term_data_array=panel_admin_crm_exportar_get_term_data_array($vid);
	if(!empty($term_data_array)){
		foreach($term_data_array as $i=>$row){
			taxonomy_del_term($row->tid);
			db_query('DELETE FROM {community_tags} WHERE tid='.$row->tid);
		}
	}
	db_query('DELETE FROM {term_data} WHERE vid='.$vid);
}
function panel_admin_crm_exportar_get_term_data_array($vid){
	$result=array();
	$where=array();
	$where[]='1';
	$where[]='term_data.vid='.$vid;
	$res=db_query('SELECT * FROM {term_data} WHERE '.implode(' AND ',$where));
	while($row=db_fetch_object($res)){
		$result[]=$row;
	}
	return $result;
}
function panel_admin_crm_exportar_clientes_callback(){
	$output='';    
    $headers=array();
    //$headers[0]=array('field'=>'name','data'=>t('Account Name'));
    $headers[0]=array('field'=>'value','data'=>t('Value'));
    //$headers[2]=array('field'=>'columna1','data'=>t('Column1'));
    $headers[1]=array('field'=>'booleano','data'=>t('Boolean'));
    $headers[2]=t('Actions');
    //$headers[1]=t('Actions');    
   
    $filter_fields=panel_admin_crm_exportar_define_clientes_filter_fields();
   	$key='panel_admin_crm_exportar_clientes_filtro';

    $where=array();
    $where[]='1';

    
   
  if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=panel_admin_crm_exportar_get_filter_value($f,$key);
           if(!empty($v)){
                switch($f){
                    case 'name':                        
                    case 'value':
                    case 'booleano':
                        $where[]='crm_exportar_textos.'.$f.' LIKE "%%'.$v.'%%"';
                        break;
                    case 'account_number':    
                        $where[]='crm_exportar_textos.'.$f.'="'.$v.'"';
                        break;
                }
           } 
       }
   }


    $my_limit=30;
    
    $sort='asc';
    //$field='name';
    $field='value';
    //$is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    /*if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
        }else if($order==t('Order')){
            //$is_numeric=1;
            $field='field_pisu_value';
        }
    }*/
    
    $sql='SELECT *
    FROM {crm_exportar_textos} crm_exportar_textos  
    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      //$rows[$kont][0]=$r->name;
      $rows[$kont][0]=$r->value;
      //$rows[$kont][2]=$r->columna1;
      $rows[$kont][1]=$r->booleano;
      $rows[$kont][2]=array('class'=>'td_nowrap','data'=>panel_admin_crm_exportar_textos_define_acciones($r));
      /*$rows[$kont][0]=panel_admin_crm_exportar_get_row_html($r);
      $rows[$kont][1]=array('class'=>'td_nowrap','data'=>panel_admin_crm_exportar_textos_define_acciones($r));*/
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
    drupal_set_title(t('List of Clients'));
    //
    return panel_admin_crm_exportar_clientes_header().$output;
}
function panel_admin_crm_exportar_admin_access(){
	if(is_super_admin()){
		return TRUE;
	}
	return FALSE;
}
function panel_admin_crm_exportar_textos_importar_form(){
	if(module_exists('crm_exportar')){
		return crm_exportar_textos_importar_form();
	}
}
function panel_admin_crm_exportar_textos_importar_form_submit($form, &$form_state){
	crm_exportar_textos_importar_form_submit($form,$form_state);
}
function panel_admin_crm_exportar_textos_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/crm_exportar/clientes';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'panel_admin/crm_exportar/'.$r->id.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'panel_admin/crm_exportar/'.$r->id.'/delete',array('query'=>$destination,'html'=>true));
    //$html[]=l(my_get_icono_action('viewmag',t('View')),'node/'.$r->nid,array('query'=>$destination,'html'=>true));
    //$html[]=panel_admin_banners_define_accion_activado($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_crm_exportar_form(){
	$form=array();
	$id='';
	$param=arg(3);
	$row=new stdClass();
	$title=t('Create Client');
	if(!empty($param) && $param=='edit'){
		$id=arg(2);
		$row=panel_admin_crm_exportar_get_crm_exportar_texto_row($id);
		$form['my_id']=array(
			'#type'=>'hidden',
			'#default_value'=>$id,
		);
		$title=t('Edit Client');
	}	
		$form['name']=array(
			'#type'=>'textfield',
			'#title'=>t('Name'),
			'#default_value'=>$row->name,
		);
		$form['value']=array(
			'#type'=>'textfield',
			'#title'=>t('Value'),
			'#default_value'=>$row->value,
		);
		$form['account_number']=array(
			'#type'=>'textfield',
			'#title'=>t('Account Number'),
			'#default_value'=>$row->account_number,
			//'#attributes'=>array('readonly'=>'readonly'),
		);
		$form['booleano']=array(
			'#type'=>'textarea',
			'#title'=>t('Boolean'),
			'#default_value'=>$row->booleano,
		);
		$form['save_btn']=array(
			'#type'=>'submit',
			'#value'=>t('Save'),
		);
		$form['cancel_btn']=array(
			'#value'=>l(t('Cancel'),'panel_admin/crm_exportar/clientes'),
		);
		drupal_set_title($title);
	return $form;
}
function panel_admin_crm_exportar_get_crm_exportar_texto_row($id){
	$result=new stdClass();
	if(module_exists('crm_exportar')){	
		$result=crm_exportar_textos_get_row($id);
	}
	return $result;
}
function panel_admin_crm_exportar_form_submit($form, &$form_state){
	$id='';
	if(isset($form_state['values']) && !empty($form_state['values'])){
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];			
		}
		$name=trim($values['name']);
		$value=trim($values['value']);
		$account_number=trim($values['account_number']);
		$booleano=trim($values['booleano']);
		if(empty($id)){
			db_query('INSERT INTO {crm_exportar_textos}(name,value,account_number,booleano) VALUES("%s","%s","%s","%s")',$name,$value,$account_number,$booleano);
		}else{
			db_query('UPDATE {crm_exportar_textos} SET name="%s",value="%s",account_number="%s",booleano="%s" WHERE id=%d',$name,$value,$account_number,$booleano,$id);
		}
	}
	drupal_goto('panel_admin/crm_exportar');	
}
function panel_admin_crm_exportar_delete_form(){
	$form=array();
		$id=arg(2);
		$row=panel_admin_crm_exportar_get_crm_exportar_texto_row($id);
		$form['my_id']=array(
			'#type'=>'hidden',
			'#default_value'=>$id,
		);
		/*
		$form['name']=array(
			'#type'=>'textfield',
			'#title'=>t('Name'),
			'#default_value'=>$row->name,
		);*/
		$form['value']=array(
			'#type'=>'textfield',
			'#title'=>t('Value'),
			'#default_value'=>$row->value,
			'#attributes'=>array('readonly'=>'readonly'),
		);
		$form['account_number']=array(
			'#type'=>'textfield',
			'#title'=>t('Account Number'),
			'#default_value'=>$row->account_number,
			'#attributes'=>array('readonly'=>'readonly'),
		);
		/*$form['booleano']=array(
			'#type'=>'textarea',
			'#title'=>t('Boolean'),
			'#default_value'=>$row->booleano,
		);*/
		$form['delete_btn']=array(
			'#type'=>'submit',
			'#value'=>t('Delete'),
		);
		$form['cancel_btn']=array(
			'#value'=>l(t('Cancel'),'panel_admin/crm_exportar/clientes'),
		);
		drupal_set_title($title);
	return $form;
}
function panel_admin_crm_exportar_delete_form_submit($form, &$form_state){
	$id='';
	if(isset($form_state['values']) && !empty($form_state['values'])){
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];
			db_query('DELETE FROM {crm_exportar_textos} WHERE id=%d',$id);			
		}
	}
}
function panel_admin_crm_exportar_get_row_html($r){
	$html=array();
	$html[]='<table>';
	$html[]='<tr>';
	$html[]='<td>';
	$html[]='<b>'.t('Name').'</b>';
	$html[]='</td>';
	$html[]='<td>';
	$html[]=$r->name;
	$html[]='</td>';
	$html[]='</tr>';
	$html[]='<tr>';
	$html[]='<td>';
	$html[]='<b>'.t('Value').'</b>';
	$html[]='</td>';
	$html[]='<td>';
	$html[]=$r->value;
	$html[]='</td>';
	$html[]='</tr>';
	$html[]='<tr>';
	$html[]='<td>';
	$html[]='<b>'.t('Colum1').'</b>';
	$html[]='</td>';
	$html[]='<td>';
	$html[]=$r->columna1;
	$html[]='</td>';
	$html[]='</tr>';
	$html[]='<tr>';
	$html[]='<td>';
	$html[]='<b>'.t('Boolean').'</b>';
	$html[]='</td>';
	$html[]='<td>';
	$html[]=$r->booleano;
	$html[]='</td>';
	$html[]='</tr>';
	$html[]='</table>';
	return implode('',$html);
}
function panel_admin_crm_exportar_clientes_header(){
	 my_add_buscar_js();
    return drupal_get_form('panel_admin_crm_exportar_clientes_filtro_form');
}
function panel_admin_crm_exportar_clientes_filtro_form(){	
	$fs_title=t('Search');
    if(!panel_admin_crm_exportar_clientes_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //        
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $key='panel_admin_crm_exportar_clientes_filtro';
    $form['file_buscar_fs']['name']=array(
			'#type'=>'textfield',
			'#title'=>t('Name'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('name',$key),
		);
		$form['file_buscar_fs']['value']=array(
			'#type'=>'textfield',
			'#title'=>t('Value'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('value',$key),
		);
		$form['file_buscar_fs']['account_number']=array(
			'#type'=>'textfield',
			'#title'=>t('Account Number'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('account_number',$key),
		);
		$form['file_buscar_fs']['booleano']=array(
			'#type'=>'textarea',
			'#title'=>t('Boolean'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('booleano',$key),
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
function panel_admin_crm_exportar_clientes_is_filter_activated(){
	$fields=panel_admin_crm_exportar_define_clientes_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['panel_admin_crm_exportar_clientes_filtro']['filter'][$f]) && !empty($_SESSION['panel_admin_crm_exportar_clientes_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function panel_admin_crm_exportar_define_clientes_filter_fields(){
	$result=array('name','value','account_number','booleano');
    return $result;
}
function panel_admin_crm_exportar_get_filter_value($field,$key){
    return hontza_get_gestion_usuarios_filter_value($field,$key);
}
function panel_admin_crm_exportar_clientes_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='panel_admin_crm_exportar_clientes_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=panel_admin_crm_exportar_define_clientes_filter_fields();
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