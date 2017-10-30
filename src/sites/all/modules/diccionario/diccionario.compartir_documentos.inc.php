<?php
function diccionario_compartir_documentos_callback(){
	$where=array();
	
	$headers=array();
	$headers[0]='lid';
	$headers[1]='Source';
	$headers[2]='';

	$my_limit=30;

	$where[]='1';
	$where[]='source LIKE "%%You are receiving this email because%%"';
	
	$sql='SELECT * FROM {locales_source} WHERE '.implode(' AND ',$where);
	$res=db_query($sql);        
    
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->lid;
      $rows[$kont][1]=$r->source;
      $rows[$kont][2]=diccionario_compartir_documentos_define_acciones($r);
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_grupos'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //
    drupal_set_title('Locales source');
    //
    //$output=contenidos_my_menu().contenidos_define_filtro().$output;
    return $output;
}
function diccionario_compartir_documentos_define_acciones($r,$destination='destination=diccionario/compartir_documentos'){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'diccionario/compartir_documentos/'.$r->lid.'/edit_locales_source',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('edit_roles','Edit Locales target'),'diccionario/compartir_documentos/'.$r->lid.'/edit_locales_target',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function diccionario_compartir_documentos_locales_source_form(){
	$form=array();
	$lid=arg(2);
	$locales_source_row=diccionario_compartir_documentos_get_locales_source_row($lid);
	/*echo print_r($locales_source_row,1);
	exit();*/
	
	$form['lid']=array(
		'#type'=>'hidden',
		'#default_value'=>$lid,
	);
	$form['source']=array(
		'#type'=>'textarea',
		'#default_value'=>$locales_source_row->source,
		'#rows'=>10,
	);

	$form['save_btn']=array(
		'#type'=>'submit',
		'#value'=>t('Save'),
	);

	$form['cancel_btn']=array(
		'#value'=>l(t('Cancel'),'diccionario/compartir_documentos'),
	);

	drupal_set_title('Edit Locales source');
	return $form;
}
function diccionario_compartir_documentos_locales_source_form_submit($form, &$form_state){
   $lid=$form_state['values']['lid'];
   $source=$form_state['values']['source'];
   //$sql=sprintf('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$source,$lid);
   //print $sql;exit();
   db_query('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$source,$lid);
   drupal_goto('diccionario/compartir_documentos');    
}	
function diccionario_compartir_documentos_locales_target_form(){
	$form=array();
	$lid=arg(2);

	$form['lid']=array(
		'#type'=>'hidden',
		'#default_value'=>$lid,
	);
	$locales_target_assoc_array=diccionario_compartir_documentos_get_locales_target_assoc_array($lid);
	//echo print_r($locales_target_assoc_array,1);exit();	
	$language_array=language_list();
	if(!empty($language_array)){
		foreach($language_array as $lang_code=>$language_row){
			if($lang_code!='en'){
				//echo print_r($locales_target_assoc_array[$lang_code],1);exit();
				$form['translation_'.$lang_code]=array(
					'#type'=>'textarea',
					'#default_value'=>$locales_target_assoc_array[$lang_code]->translation,
					'#rows'=>10,
				);
			}	
		}
	}

	$form['save_btn']=array(
		'#type'=>'submit',
		'#value'=>t('Save'),
	);

	$form['cancel_btn']=array(
		'#value'=>l(t('Cancel'),'diccionario/compartir_documentos'),
	);

	return $form;
}
function diccionario_compartir_documentos_locales_target_form_submit($form, &$form_state){
   $lid=$form_state['values']['lid'];
   $language_array=language_list();
	if(!empty($language_array)){
		foreach($language_array as $lang_code=>$language_row){
			if($lang_code!='en'){
				$my_name='translation_'.$lang_code;
				$translation=$form_state['values'][$my_name];
				//$sql=sprintf('UPDATE {locales_target} SET translation="%s" WHERE lid=%d AND language="%s"',$translation,$lid,$lang_code);
				//print $sql;exit();
				db_query('UPDATE {locales_target} SET translation="%s" WHERE lid=%d AND language="%s"',$translation,$lid,$lang_code);
			}
		}
	}			
   drupal_goto('diccionario/compartir_documentos');
}	
function diccionario_compartir_documentos_get_locales_target_assoc_array($lid){
	$result=array();
	$locales_target_array=diccionario_get_locales_target_array($lid);
	if(!empty($locales_target_array)){
		foreach($locales_target_array as $i=>$locales_target_row){
			$result[$locales_target_row->language]=$locales_target_row;
		}
	}
	return $result;
}
function diccionario_compartir_documentos_get_locales_source_row($lid){
	$result=diccionario_compartir_documentos_get_locales_source_array($lid);
	if(count($result)>0){
		return $result[0];
	}
	$my_result=new stdClass();
	return $my_result;
}
function diccionario_compartir_documentos_get_locales_source_array($lid){
	$result=array();
	$where=array();
	$where[]='lid='.$lid;
    $res=db_query('SELECT * FROM {locales_source} WHERE '.implode(' AND ',$where).' ORDER BY lid ASC');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}