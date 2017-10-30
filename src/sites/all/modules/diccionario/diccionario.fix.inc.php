<?php
function diccionario_fix_callback(){
	//diccionario_fix_compartir_busqueda();
	return date('Y-m-d H:i:s');
}
function diccionario_fix_compartir_busqueda(){
	$lid=18808;	
	//$lid_target=19396;
	//$lid_target=19395;	
	//$lid_target=19411;
	//$lid_target=19412;
	//$lid_target=19413;
	//$lid_target=19415;
	$lid_target=19419;
	$locales_target_array=diccionario_fix_get_locales_target_array($lid);
	//echo print_r($locales_target_array,1);exit();
	if(!empty($locales_target_array)){
		diccionario_fix_locales_target_delete($lid_target);
		foreach($locales_target_array as $i=>$locales_target_row){
			diccionario_fix_locales_target_save($lid_target,$locales_target_row);
		}
	}
}
function diccionario_fix_get_locales_target_array($lid='',$language=''){
	$result=array();
	$where=array();
	$where[]='1';
	if(!empty($lid)){
		$where[]='lid='.$lid;
	}
	$sql='SELECT * FROM {locales_target} WHERE '.implode(' AND ',$where);
	$res=db_query($sql);
	while($row=db_fetch_object($res)){
		$result[]=$row;
	}
	return $result;
}
function diccionario_fix_locales_target_save($lid_target,$locales_target_row){
	$translation=$locales_target_row->translation;
	if($lid_target==19395){
		$translation=str_replace('canal24','save_current_search24',$translation);
	}else if($lid_target==19411){
		$translation=str_replace('canal24','estrategia24',$translation);
	}else if($lid_target==19412){
		$translation=str_replace('canal24','item24',$translation);
	}else if($lid_target==19413){
		$translation=str_replace('canal24','wiki24',$translation);
	}else if($lid_target==19415){
		$translation=str_replace('canal24','proyecto24',$translation);
	}else if($lid_target==19419){
		$translation=str_replace('canal24','user24',$translation);
	}
	/*$sql=sprintf('INSERT INTO {locales_target}(lid,translation,language) VALUES(%d,"%s","%s")',$lid_target,$translation,$locales_target_row->language);
	print $sql;
	exit();*/
	db_query('INSERT INTO {locales_target}(lid,translation,language) VALUES(%d,"%s","%s")',$lid_target,$translation,$locales_target_row->language);
}
function diccionario_fix_locales_target_delete($lid_target){
	$sql='DELETE FROM {locales_target} WHERE lid='.$lid_target;
	db_query($sql);
}