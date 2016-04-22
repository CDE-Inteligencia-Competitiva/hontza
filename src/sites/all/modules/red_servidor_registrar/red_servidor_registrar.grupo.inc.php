<?php
function red_servidor_registrar_grupo_guardar_grupo_hoja($red_servidor_grupo_id,$grupo_enviar){
    $local_nid=$grupo_enviar->registrar_grupo->local_nid; 
    $local_vid=$grupo_enviar->registrar_grupo->local_vid; 
    $local_uid=$grupo_enviar->registrar_grupo->local_uid;
    $grupo_title=$grupo_enviar->registrar_grupo->grupo_title;
    $group_owner_username=$grupo_enviar->registrar_grupo->group_owner_username;
    $group_owner_nombre=$grupo_enviar->registrar_grupo->group_owner_nombre;
    $group_owner_apellidos=$grupo_enviar->registrar_grupo->group_owner_apellidos;
    $group_owner_email=$grupo_enviar->registrar_grupo->group_owner_email;
    $group_main_language=$grupo_enviar->registrar_grupo->group_main_language;
    $organisation=$grupo_enviar->registrar_grupo->organisation;
    $country=$grupo_enviar->registrar_grupo->country;
    $website=$grupo_enviar->registrar_grupo->website;
    $tags=$grupo_enviar->registrar_grupo->tags;
    $tags_geograficos=$grupo_enviar->registrar_grupo->tags_geograficos;
    $red_servidor_registrar_local=$grupo_enviar->registrar_grupo->red_servidor_registrar_local;
    $red_registrar_empresa_post=$grupo_enviar->registrar_grupo->red_registrar_empresa_post;
    $red_servidor_registrar_nid=$grupo_enviar->registrar_grupo->red_servidor_registrar_nid;
    $red_servidor_registrar_vid=$grupo_enviar->registrar_grupo->red_servidor_registrar_vid;
    $red_servidor_registrar_grupo_row=red_servidor_registrar_grupo_get_row($red_servidor_grupo_id);
    $fecha_registrar=time();
    $group_short_name=$grupo_enviar->registrar_grupo->group_short_name;
    $base_url=$grupo_enviar->registrar_grupo->base_url;
    $base_root=$grupo_enviar->registrar_grupo->base_root;
    $remote_addr=$_SERVER['REMOTE_ADDR'];
    //$remote_addr=$grupo_enviar->registrar_grupo->remote_addr;
    $red_servidor_registrar_node=node_load($red_servidor_registrar_nid);
    if(isset($red_servidor_registrar_node->field_registrar_remote_addr[0]['value'])){
        $remote_addr=$red_servidor_registrar_node->field_registrar_remote_addr[0]['value'];
    }
    $uniq_id=$grupo_enviar->registrar_grupo->uniq_id;
    //simulando
    //$red_servidor_registrar_grupo_row='';
    if(isset($red_servidor_registrar_grupo_row->id) && !empty($red_servidor_registrar_grupo_row->id)){
        //simulando
        /*$local_nid=1;
        $local_vid=2;
        $local_uid=3;
        $grupo_title='aaaa';
        $group_owner_username='bbbb';
        $group_owner_nombre='cccc';
        $group_owner_apellidos='dddd';
        $group_owner_email='eeee';
        $group_main_language='eu';
        $organisation='ffff';
        $country='llll';
        $website='mmmm';
        $tags='nnnn';
        $red_servidor_registrar_local='tttt';
        $red_registrar_empresa_post='oooo';
        $red_servidor_registrar_nid=4;
        $red_servidor_registrar_vid=5;*/      
        db_query('UPDATE {red_servidor_registrar_grupo} 
        SET local_nid=%d,
        local_vid=%d,
        local_uid=%d,
        grupo_title="%s",
        group_owner_username="%s",
        group_owner_nombre="%s",
        group_owner_apellidos="%s",
        group_owner_email="%s",
        group_main_language="%s",
        organisation="%s",
        country="%s",
        website="%s",
        tags="%s",
        tags_geograficos="%s",
        red_servidor_registrar_local="%s",
        red_registrar_empresa_post="%s",
        red_servidor_registrar_nid=%d,
        red_servidor_registrar_vid=%d,
        fecha_registrar_update=%d,
        group_short_name="%s"
        WHERE id=%d',
        $local_nid,
        $local_vid,
        $local_uid,
        $grupo_title,
        $group_owner_username,
        $group_owner_nombre,
        $group_owner_apellidos,
        $group_owner_email,
        $group_main_language,
        $organisation,
        $country,
        $website,
        $tags,
        $tags_geograficos,        
        $red_servidor_registrar_local,
        $red_registrar_empresa_post,
        $red_servidor_registrar_nid,
        $red_servidor_registrar_vid,
        $fecha_registrar,
        $group_short_name,        
        $red_servidor_registrar_grupo_row->id);
    }else{
        //print 'group_short_name='.$group_short_name;exit();
        db_query('INSERT INTO {red_servidor_registrar_grupo}
        (red_servidor_grupo_id,
        local_nid,
        local_vid,
        local_uid,
        grupo_title,
        group_owner_username,
        group_owner_nombre,
        group_owner_apellidos,
        group_owner_email,
        group_main_language,
        organisation,
        country,
        website,
        tags,
        tags_geograficos,
        red_servidor_registrar_local,
        red_registrar_empresa_post,
        red_servidor_registrar_nid,
        red_servidor_registrar_vid,
        fecha_registrar,
        group_short_name,
        base_url,
        base_root,
        remote_addr,
        uniq_id) 
        VALUES(%d,%d,%d,%d,"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%d,%d,%d,"%s","%s","%s","%s","%s")',
        $red_servidor_grupo_id,
        $local_nid,
        $local_vid,
        $local_uid,
        $grupo_title,
        $group_owner_username,
        $group_owner_nombre,
        $group_owner_apellidos,
        $group_owner_email,
        $group_main_language,
        $organisation,
        $country,
        $website,
        $tags,
        $tags_geograficos,        
        $red_servidor_registrar_local,
        $red_registrar_empresa_post,
        $red_servidor_registrar_nid,
        $red_servidor_registrar_vid,                
        $fecha_registrar,
        $group_short_name,
        $base_url,
        $base_root,
        $remote_addr,
        $uniq_id);
    }    
}
function red_servidor_registrar_grupo_get_row($red_servidor_grupo_id,$id=''){
    $red_servidor_registrar_grupo_array=red_servidor_registrar_grupo_get_array($red_servidor_grupo_id,$id);
    if(count($red_servidor_registrar_grupo_array)>0){
        return $red_servidor_registrar_grupo_array[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_servidor_registrar_grupo_get_array($red_servidor_grupo_id,$id=''){
    $result=array();
    if(!empty($red_servidor_grupo_id)){
        $res=db_query('SELECT * FROM {red_servidor_registrar_grupo} WHERE red_servidor_grupo_id=%d',$red_servidor_grupo_id);
    }else if(!empty($id)){
        $res=db_query('SELECT * FROM {red_servidor_registrar_grupo} WHERE id=%d',$id);
    }else{
         return $result;
    }
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_registrar_grupo_registrados_grupos_callback(){
    $output='';
    $headers=array();
    //$headers[0]=t('Logo');         
    $headers[0]=array('data'=>t('Group Name'),'field'=>'grupo_title');
    $headers[1]=array('data'=>t('Group Owner'),'field'=>'group_owner');
    $headers[2]=array('data'=>t('Owner Email'),'field'=>'group_owner_email');
    $headers[3]=array('data'=>t('Organisation'),'field'=>'organisation');
    $headers[4]=t('Actions');				    
    
    $my_limit=40;
    
    $sort='asc';
    $field='grupo_title';
    /*$sort='desc';
    $field='node_created';*/
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    
    $where=array();
    $where[]='1';
    $where[]='red_servidor_grupo.status=1';
    
    $sql='SELECT red_servidor_registrar_grupo.* 
    FROM {red_servidor_registrar_grupo}  
    LEFT JOIN {red_servidor_grupo} ON red_servidor_registrar_grupo.red_servidor_grupo_id=red_servidor_grupo.id 
    WHERE '.implode(' AND ',$where).'
    ORDER BY '.$field.' '.$sort;
    //print $sql;
    $rows=array();
    $res=db_query($sql);
    $kont=0;
    $faktore=red_registrar_get_logo_faktore();
    while($r=db_fetch_object($res)){
        $row=array();        
        $row[0]=$r->grupo_title;        
        $row[1]=red_registar_get_user_name('',$r,1);
        $row[2]=$r->group_owner_email;
        $row[3]=$r->organisation;
        $row[4]=red_servidor_registrar_grupo_define_acciones($r->id);
        $rows[]=$row;
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
      $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_canales'));
      $output .= theme('pager', NULL, $my_limit);    
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Groups'));
    $output=red_servidor_registrar_registrados_define_header().$output;
    return $output;
}
function red_servidor_registrar_grupo_define_acciones($id){
    $html=array();
    $title_view=t('View');
    $html[]=l(my_get_icono_action('viewmag',$title_view),'red_servidor_registrar/registrados_grupo/'.$id.'/view',array('absolute'=>true,'html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('title'=>$title_view,'alt'=>$title_view)));    
    return implode('&nbsp;',$html);
}
function red_servidor_registrar_grupo_registrados_grupo_view_callback(){
    $id=arg(2);
    $html=array();
    $red_servidor_registrar_row=red_servidor_registrar_grupo_get_row('',$id);
    if(isset($red_servidor_registrar_row->id) && !empty($red_servidor_registrar_row->id)){
        $html[]=red_servidor_registrar_registrados_define_header();
        $html[]='<table class="table_node_view" style="clear:both;">';
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Group Name',$red_servidor_registrar_row->grupo_title);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Group Owner Username',$red_servidor_registrar_row->group_owner_username);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Group Owner Name',$red_servidor_registrar_row->group_owner_nombre);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Group Owner Surname',$red_servidor_registrar_row->group_owner_apellidos);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Owner Email',$red_servidor_registrar_row->group_owner_email);
        $group_main_language=red_servidor_registrar_grupo_get_group_main_language_label($red_servidor_registrar_row->group_main_language);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Group main language',$group_main_language);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Organisation',$red_servidor_registrar_row->organisation);
        $country=red_servidor_registrar_get_country_view_html('',$red_servidor_registrar_row->country);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Country',$country);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Website',$red_servidor_registrar_row->website);
        $html[]=red_servidor_registrar_grupo_registrados_grupo_view_tr('Tags',$red_servidor_registrar_row->tags);
        $html[]='</table>';
    }
    $html[]=l(t('Return'),'red_servidor_registrar/registrados_grupos');
    return implode('',$html);
}
function red_servidor_registrar_grupo_registrados_grupo_view_tr($label,$value){
    $html=array();
    $html[]='<tr class="tr_node_view">';
    $html[]='<td class="td_label_node_view"><b>'.t($label).'</b>&nbsp;</td>';
    $html[]='<td class="td_value_node_view">'.$value.'</td>';
    $html[]='</tr>';
    return implode('',$html);
}
function red_servidor_registrar_grupo_get_group_main_language_label($code){
    $language_array=language_list();
    if(isset($language_array[$code]) && !empty($language_array[$code])){
        return $language_array[$code]->native;
    }
    return '';
}