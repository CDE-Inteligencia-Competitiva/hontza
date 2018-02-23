<?php
function panel_admin_crm_exportar_left_title(){
    return t('TAGGING & EXPORT XML');
}
function panel_admin_crm_exportar_block_content(){
	$html=array();
	if(module_exists('crm_exportar')){
		if(!crm_exportar_is_crm_exportar_categorias()){
			//$html[]=l(t('Delete tags'),'panel_admin/crm_exportar/delete_tags');
			$html[]=l(t('Upload CSV'),'panel_admin/crm_exportar/textos/importar');
			//$html[]=l(t('Download csv'),'panel_admin/crm_exportar/textos/exportar-csv',array('attributes'=>array('target'=>'_blank')));
			$html[]=l(t('Download CSV'),'panel_admin/crm_exportar/textos/exportar-csv');
			$html[]=l(t('Create Search'),'panel_admin/crm_exportar/create');	
			//$html[]=l(t('List of Searches'),'panel_admin/crm_exportar/clientes');
			//intelsat
			$html[]=l(t('Create List'),'panel_admin/crm_exportar/listas/create');
			$html[]=l(t('Lists Management'),'panel_admin/crm_exportar/listas');
			if(crm_exportar_is_crm_list_type_activado()){
				$html[]=l(t('Types Management'),'panel_admin/crm_exportar/types');	    
			}		
		}
		$html[]=l(t('Tagging & Export XML'),'crm_exportar/crear_url',array('attributes'=>array('target'=>'_blank')));
		$html[]=l(t('IP Filter'),'panel_admin/crm_exportar/filtro_ip');
    }
    return implode('<BR>',$html);
}
function panel_admin_crm_exportar_delete_tags_form(){
	drupal_access_denied();
	exit();
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
	drupal_access_denied();
	exit();
	panel_admin_crm_exportar_delete_tags();
    $destination='panel_admin';
    drupal_goto($destination);
}
function panel_admin_crm_exportar_delete_tags(){
    $nid_array=array();
	$vid=hontza_crm_inc_get_tags_vid();
	$term_data_array=panel_admin_crm_exportar_get_term_data_array($vid);
	if(!empty($term_data_array)){
		foreach($term_data_array as $i=>$row){
                    $nid_array=panel_admin_crm_exportar_get_term_node_nid_array($row->tid,$nid_array);
			taxonomy_del_term($row->tid);
			db_query('DELETE FROM {community_tags} WHERE tid='.$row->tid);
		}
	}
	db_query('DELETE FROM {term_data} WHERE vid='.$vid);
    if(!empty($nid_array)){
        foreach($nid_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                hontza_canal_rss_solr_clear_node_index($node,$nid);
            }    
        }        
    }
}
function panel_admin_crm_exportar_get_term_data_array($vid){
	$result=array();
	$where=array();
	$where[]='1';
	$where[]='term_data.vid='.$vid;
        $where[]='term_data.name LIKE "%%CRM:%%"';
	$res=db_query('SELECT * FROM {term_data} WHERE '.implode(' AND ',$where));
	while($row=db_fetch_object($res)){
		$result[]=$row;
	}
	return $result;
}
function panel_admin_crm_exportar_clientes_callback(){
	$output='';
	crm_exportar_categorias_access_denied();      
    $headers=array();
    //$headers[0]=array('field'=>'name','data'=>t('Account Name'));
    $headers[0]=array('field'=>'value','data'=>t('Tag'));
    //$headers[2]=array('field'=>'columna1','data'=>t('Column1'));
    //$headers[1]=array('field'=>'booleano','data'=>t('Boolean Search'));
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
                    case 'crm_exportar_listas_id':
                    	$where[]='crm_exportar_textos_listas.'.$f.'='.$v;
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
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Boolean Search')){
            $field='booleano';
        }
    }
    
    $sql='SELECT crm_exportar_textos.*
    FROM {crm_exportar_textos} crm_exportar_textos 
    LEFT JOIN {crm_exportar_textos_listas} crm_exportar_textos_listas ON crm_exportar_textos.id=crm_exportar_textos_listas.crm_exportar_textos_id 
    WHERE '.implode(' AND ',$where).' 
    GROUP BY crm_exportar_textos.id
    ORDER BY '.$field.' '.$sort;
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
    drupal_set_title(t('List of Searches'));
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
    $activado_link=panel_admin_crm_exportar_textos_define_accion_activado($r,$destination);
    if(!empty($activado_link)){
    	$html[]=$activado_link;
    }   
    return implode('&nbsp;',$html);
}
function panel_admin_crm_exportar_form(){
	$form=array();
	crm_exportar_categorias_access_denied();  
	$id='';
	$param=arg(3);
	$row=new stdClass();
	$crm_exportar_listas_row=new stdClass();
	$title=t('Create Search');
	$param3='';
	$crm_exportar_listas_id='';
	
	if(!empty($param) && $param=='edit'){
		$id=arg(2);
		//print $id;exit();
		$row=panel_admin_crm_exportar_get_crm_exportar_texto_row($id);
		/*echo print_r($row,1);
		exit();*/		
		$crm_exportar_listas_row=panel_admin_crm_exportar_listas_get_crm_exportar_listas_row('',$id);
		if(isset($crm_exportar_listas_row->id) && !empty($crm_exportar_listas_row->id)){
			$crm_exportar_listas_id=$crm_exportar_listas_row->id;
		}

		$form['my_id']=array(
			'#type'=>'hidden',
			'#default_value'=>$id,
		);
		$title=t('Edit Search');		
	}else{
		$param3=arg(3);
		if(!empty($param3)){
			$crm_exportar_listas_id=$param3;
		}
		//print $crm_exportar_listas_id;exit();	
	}

		$form['account_number']=array(
			'#type'=>'textfield',
			//'#title'=>t('Index key'),
			'#title'=>t('Key'),
			'#default_value'=>$row->account_number,
			//'#attributes'=>array('readonly'=>'readonly'),
		);
		$form['name']=array(
			'#type'=>'textfield',
			//'#title'=>t('Notes'),
			'#title'=>t('Name'),
			'#default_value'=>$row->name,
		);

		if(panel_admin_crm_exportar_is_crm_activar_cliente()){
			$form['is_active']=array(
				'#type'=>'checkbox',
				'#title'=>'<b>'.t('Active').'</b>',
			);
			$is_active=1;
			if(isset($row->id) && !empty($row->id)){
				$is_active=$row->is_active;
			}
			if($is_active){
				$form['is_active']['#attributes']=array('checked'=>'checked');
			}
		}

		$form['value']=array(
			'#type'=>'textfield',
			'#title'=>t('Tag'),
			'#default_value'=>$row->value,
		);
		
		$form['booleano']=array(
			'#type'=>'textarea',
			//'#title'=>t('Boolean Search'),
			'#title'=>t('Boolean'),
			'#default_value'=>$row->booleano,
		);
		/*$form['crm_exportar_listas_name']=array(
			'#type'=>'textfield',
			'#title'=>t('List'),
			'#default_value'=>$crm_exportar_listas_row->name,
			'#attributes'=>array('readonly'=>'readonly'),
		);*/
		$form['crm_exportar_listas_id']=array(
			'#type'=>'select',
			'#title'=>t('List'),
			'#options'=>panel_admin_crm_exportar_clientes_listas_get_options(1),
			'#default_value'=>$crm_exportar_listas_id,
			//'#required'=>TRUE,
		);
		$form['save_btn']=array(
			'#type'=>'submit',
			'#value'=>t('Save'),
			'#name'=>'save_btn',
		);

		if(!empty($id)){
			$form['delete_btn']=array(
				'#type'=>'submit',
				'#value'=>t('Delete'),
				'#name'=>'delete_btn',
				//'#prefix'=>'<div style="padding-left:10px;padding-right:10px;">',
				//'#suffix'=>'</div>',
				'#attributes'=>array('style'=>'margin-left:10px;margin-right:10px;'),
			);
		}
		
		$cancel_url='panel_admin/crm_exportar/clientes';
		if(!empty($param3)){
			$cancel_url='panel_admin/crm_exportar/listas';
		}

		$form['cancel_btn']=array(
			'#value'=>l(t('Cancel'),$cancel_url),
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
		/*echo print_r($form_state,1);
		exit();*/
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];			
		}
		if(!empty($id) && isset($form_state['clicked_button']['#name']) && $form_state['clicked_button']['#name']=='delete_btn'){
			/*$_REQUEST['destination']='';
			drupal_goto('panel_admin/crm_exportar/'.$id.'/delete');
			exit();*/
			$my_form_state=array();
			$my_form_state['values']['my_id']=$id;
			panel_admin_crm_exportar_delete_form_submit($my_form,$my_form_state);
		}else{
			$name=trim($values['name']);
			$value=trim($values['value']);
			$account_number=trim($values['account_number']);
			$booleano=trim($values['booleano']);
			if(empty($id)){
				db_query('INSERT INTO {crm_exportar_textos}(name,value,account_number,booleano) VALUES("%s","%s","%s","%s")',$name,$value,$account_number,$booleano);
				$id=db_last_insert_id('crm_exportar_textos','id');
			}else{
				db_query('UPDATE {crm_exportar_textos} SET name="%s",value="%s",account_number="%s",booleano="%s" WHERE id=%d',$name,$value,$account_number,$booleano,$id);
			}
			
			if(panel_admin_crm_exportar_is_crm_activar_cliente()){
				$is_active=$values['is_active'];
				panel_admin_crm_exportar_textos_update_is_active($id,$is_active);
			}

			$crm_exportar_listas_id=0;
			if(isset($values['crm_exportar_listas_id']) && !empty($values['crm_exportar_listas_id'])){
				$crm_exportar_listas_id=$values['crm_exportar_listas_id'];			
				//print $crm_exportar_listas_id;exit();
				panel_admin_crm_exportar_listas_crm_exportar_textos_listas_save($id,$crm_exportar_listas_id);
			}
		}	
	}
	drupal_goto('panel_admin/crm_exportar/clientes');	
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
			'#title'=>t('Tag'),
			'#default_value'=>$row->value,
			'#attributes'=>array('readonly'=>'readonly'),
		);
		$form['account_number']=array(
			'#type'=>'textfield',
			'#title'=>t('Index key'),
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
		drupal_set_title(t('Delete Search'));
	return $form;
}
function panel_admin_crm_exportar_delete_form_submit($form, &$form_state){
	$id='';
	if(isset($form_state['values']) && !empty($form_state['values'])){
		$values=$form_state['values'];
		if(isset($values['my_id']) && !empty($values['my_id'])){
			$id=$values['my_id'];
			db_query('DELETE FROM {crm_exportar_textos_listas} WHERE crm_exportar_textos_id=%d',$id);		
			db_query('DELETE FROM {crm_exportar_textos} WHERE id=%d',$id);			
		}
	}
	drupal_goto('panel_admin/crm_exportar/clientes');	
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
    $output=drupal_get_form('panel_admin_crm_exportar_clientes_filtro_form');
    $output.=panel_admin_crm_exportar_clientes_menu();
    return $output;
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
    
    $form['file_buscar_fs']['account_number']=array(
			'#type'=>'textfield',
			//'#title'=>t('Index key'),
			'#title'=>t('Key'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('account_number',$key),
		);

    $form['file_buscar_fs']['name']=array(
			'#type'=>'textfield',
			//'#title'=>t('Notes'),
			'#title'=>t('Name'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('name',$key),
		);
		$form['file_buscar_fs']['value']=array(
			'#type'=>'textfield',
			'#title'=>t('Tag'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('value',$key),
		);
		
		$form['file_buscar_fs']['booleano']=array(
			'#type'=>'textarea',
			//'#title'=>t('Boolean Search'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('booleano',$key),
		);
		$form['file_buscar_fs']['booleano']=array(
			'#type'=>'textarea',
			//'#title'=>t('Boolean Search'),
			'#title'=>t('Boolean'),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('booleano',$key),
		);

		$form['file_buscar_fs']['crm_exportar_listas_id']=array(
			'#type'=>'select',
			'#title'=>t('List'),
			'#options'=>panel_admin_crm_exportar_clientes_listas_get_options(),
			'#default_value'=>panel_admin_crm_exportar_get_filter_value('crm_exportar_listas_id',$key),
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
	$result=array('name','value','account_number','booleano','crm_exportar_listas_id');
    return $result;
}
function panel_admin_crm_exportar_get_filter_value($field,$key){
	$result=hontza_get_gestion_usuarios_filter_value($field,$key);
	$result=panel_admin_crm_exportar_listas_get_crm_exportar_listas_id_filter_value($field,$key,$result);
	return $result;
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
function panel_admin_crm_exportar_get_term_node_nid_array($tid,$nid_array){
  $result=$nid_array;  
  $res = db_query("SELECT * FROM {term_node} term_node WHERE term_node.tid=%d",$tid);
  while ($row = db_fetch_object($res)) {
    if(!in_array($row->nid,$result)){
        $result[]=$row->nid;
    }  
  }
  return $result;
}
function panel_admin_crm_exportar_textos_exportar_csv(){
	/*$data_csv_array=panel_admin_crm_exportar_textos_exportar_csv_create_data_csv_array();
    estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'searches');
    exit();*/
    return drupal_get_form('panel_admin_crm_exportar_textos_exportar_csv_form');		
}
function panel_admin_crm_exportar_textos_exportar_csv_create_data_csv_array($crm_exportar_listas_id=''){
	$result=array();
	$crm_exportar_textos_array=panel_admin_crm_exportar_textos_get_array('',$crm_exportar_listas_id);
	if(!empty($crm_exportar_textos_array)){
		$result[0]=panel_admin_crm_exportar_textos_get_headers();
		foreach($crm_exportar_textos_array as $i=>$row){
			$result[]=panel_admin_crm_exportar_textos_exportar_csv_create_data_csv_row($row);
		}
	}
	return $result;
}
function panel_admin_crm_exportar_textos_get_array($field_in='',$crm_exportar_listas_id=''){
	$result=array();
	$where=array();
	$where[]='1';
	$sort='asc';
    //$field='name';
    $field='value';
    if(!empty($field_in)){
    	$field=$field_in;
    }
    
    if(!empty($crm_exportar_listas_id)){
    	$where[]='crm_exportar_textos_listas.crm_exportar_listas_id='.$crm_exportar_listas_id;

    	$sql='SELECT crm_exportar_textos.*
	    FROM {crm_exportar_textos} crm_exportar_textos 
	    LEFT JOIN {crm_exportar_textos_listas} ON  crm_exportar_textos.id=crm_exportar_textos_listas.crm_exportar_textos_id
	    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    }else{
		$sql='SELECT *
	    FROM {crm_exportar_textos} crm_exportar_textos  
	    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
	}    

    $res=db_query($sql);
    while($row=db_fetch_object($res)){
    	$result[]=$row;
    }
    return $result;
}
function panel_admin_crm_exportar_textos_get_headers(){
	$result=array();	
	/*$result[0]=t('Index key');	
	$result[1]=t('Notes');
	$result[2]=t('Tag');
	$result[3]=t('Boolean Search');*/
	$result[0]='Key';
	$result[1]='Name';
	$result[2]='Tag';
	$result[3]='Boolean';
	return $result;
}
function panel_admin_crm_exportar_textos_exportar_csv_create_data_csv_row($row){
	$result=array();	
	$result[0]=$row->account_number;	
	$result[1]=$row->name;
	$result[2]=$row->value;
	$result[3]=$row->booleano;
	/*foreach($result as $i=>$value){
		if(!empty($value) && !is_numeric($value)){
			$encoding=mb_detect_encoding($value);
			if($encoding!='ASCII'){
				print $encoding.'<br>';
				print utf8_decode($value).'<br>';
				exit();
			}
		}     	
	}*/
	return $result;
}
function panel_admin_crm_exportar_textos_exportar_csv_form(){
	$form=array();
	$form['crm_exportar_listas_id']=array(
			'#type'=>'select',
			'#title'=>t('List'),
			'#options'=>panel_admin_crm_exportar_clientes_listas_get_options(1),			
		);
	$form['download_csv_btn']=array(
		'#type'=>'submit',
		'#default_value'=>t('Download CSV'),
	);
	$form['cancel_btn']=array(
		'#value'=>l(t('Cancel'),'panel_admin'),
	);
	drupal_set_title(t('Download CSV'));
	return $form;
}
function panel_admin_crm_exportar_textos_exportar_csv_form_submit(&$form, &$form_state){
	$crm_exportar_listas_id=$form_state['values']['crm_exportar_listas_id'];
	/*print $crm_exportar_listas_id;
	exit();*/
	drupal_goto('panel_admin/crm_exportar/textos/exportar-csv/'.$crm_exportar_listas_id.'/download');
}
function panel_admin_crm_exportar_textos_exportar_csv_download_callback(){
	$crm_exportar_listas_id=arg(4);
	/*print $crm_exportar_listas_id;
	exit();*/
	$data_csv_array=panel_admin_crm_exportar_textos_exportar_csv_create_data_csv_array($crm_exportar_listas_id);
    estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'searches');
    exit();    
}
function panel_admin_crm_exportar_clientes_menu(){
	$html=array();
    $html[]='<div class="tab-wrapper clearfix primary-only div_categorias_canal_menu">';
    $html[]='<div id="tabs-primary" class="tabs primary">';
    $html[]='<ul>';
    $html[]='<li>'.l(t('Create Search'),'panel_admin/crm_exportar/create').'</li>';
    $html[]='</ul>';
	$html[]='</div>';
    $html[]='</div>';
    

    return implode('',$html);
}
function panel_admin_crm_exportar_is_crear_url(){
	$param0=arg(0);
	if(!empty($param0) && $param0=='crm_exportar'){
		$param1=arg(1);
		if(!empty($param1) && $param1=='crear_url'){
			return 1;
		}	
	}
	return 0;
}
function panel_admin_crm_exportar_is_crm_activar_cliente(){
  if(module_exists('crm_exportar')){
 	return crm_exportar_is_crm_activar_cliente();
  }
  return 0;
}
function panel_admin_crm_exportar_textos_define_accion_activado($r,$destination){
	if(panel_admin_crm_exportar_is_crm_activar_cliente()){
		if(isset($r->is_active) && !empty($r->is_active)){
	        $activar='deactive_user';
	        $title=t('Deactivate');	    	        	       
	    }else{
	    	$activar='active_user';
	    	$title=t('Activate');
	    }
	    $url='panel_admin/crm_exportar/textos/activar/'.$r->id;
	    return $html[]=l(my_get_icono_action($activar,$title),$url,array('query'=>$destination,'html'=>true));
	}
	return '';
}
function panel_admin_crm_exportar_textos_activar_callback(){
	$crm_exportar_textos_id=arg(4);
	/*print 'crm_exportar_textos_id='.$crm_exportar_textos_id;
	exit();*/
	//=crm_exportar_textos_get_row($id);
	panel_admin_crm_exportar_textos_activar($crm_exportar_textos_id);
}
function panel_admin_crm_exportar_textos_activar($crm_exportar_textos_id){
	$crm_exportar_textos_row=crm_exportar_textos_get_row($crm_exportar_textos_id);
	/*echo print_r($crm_exportar_textos_row,1);
	exit();*/
	$is_active=1;
	if(isset($crm_exportar_textos_row->is_active) && !empty($crm_exportar_textos_row->is_active) && $crm_exportar_textos_row->is_active==1){
		$is_active=0;
	}
	db_query('UPDATE {crm_exportar_textos} SET is_active=%d WHERE id=%d',$is_active,$crm_exportar_textos_id);
	drupal_goto('panel_admin/crm_exportar/clientes');
}
function panel_admin_crm_exportar_textos_update_is_active($crm_exportar_textos_id,$is_active){
	db_query('UPDATE {crm_exportar_textos} SET is_active=%d WHERE id=%d',$is_active,$crm_exportar_textos_id);
}      	