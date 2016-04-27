<?php
function red_servidor_validar_usuario_network_callback(){
    $result=array();
    $result['ok']='';
    if(isset($_POST['validar_usuario_network']) && !empty($_POST['validar_usuario_network'])){
        $validar_usuario_network=base64_decode($_POST['validar_usuario_network']);
        $validar_usuario_network=red_compartir_grupo_decrypt_text($validar_usuario_network);
        $validar_usuario_network=base64_decode($validar_usuario_network);
        $validar_usuario_network=unserialize($validar_usuario_network);
        $presave_mail=$validar_usuario_network->presave_account->mail;
        if(isset($validar_usuario_network->user_local) && !empty($validar_usuario_network->user_local)){
            $user_local=$validar_usuario_network->user_local;
            if(isset($user_local->name) && !empty($user_local->name)){
                $my_user=user_load(array('name'=>$user_local->name));
                //echo print_r($my_user,1);exit();
                if(isset($my_user->uid) && !empty($my_user->uid)){
                    if(isset($user_local->uid) && !empty($user_local->uid)){    
                        if(($my_user->mail==$user_local->mail) || $my_user->mail==$presave_mail){
                            $result['ok']='ok';                     
                        }else{
                            $result['ok']='';
                            $result['email_diferente']=1;
                        }
                    }else{
                        //si algo no funciona puede ser por esto, antes por este sitio era $result['ok']=''
                        //$result['ok']='';
                        if(($my_user->mail==$user_local->mail)){
                            $result['ok']='ok';                     
                        }else{
                            $result['ok']='';
                            $result['email_diferente']=1;
                        }
                    }    
                }else{
                    $result['ok']='ok';
                }
                //echo print_r($result,1);exit();
                if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
                    $result=red_servidor_validar_usuario_network_array($user_local,$result,$presave_mail);
                }
                //echo print_r($result,1);exit();
                if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
                    red_servidor_validar_usuario_network_save_user_subdominios_by_presave_mail($user_local,$presave_mail);
                }
                //echo print_r($result,1);exit();
            }
        }
    }
    $result=serialize($result);
    print $result;
    exit();
}
function red_servidor_validar_usuario_mail_network_para_compartir_callback(){
    $result=array();
    $result['ok']='';
    if(isset($_POST['validar_usuario_mail_network']) && !empty($_POST['validar_usuario_mail_network'])){
        $validar_usuario_network=base64_decode($_POST['validar_usuario_mail_network']);
        $validar_usuario_network=red_compartir_grupo_decrypt_text($validar_usuario_network);
        $validar_usuario_network=base64_decode($validar_usuario_network);
        $validar_usuario_network=unserialize($validar_usuario_network);
        if(isset($validar_usuario_network->user_local) && !empty($validar_usuario_network->user_local)){
            $user_local=$validar_usuario_network->user_local;
            if(isset($user_local->mail) && !empty($user_local->mail)){
                $my_user=user_load(array('mail'=>$user_local->mail));
                if(isset($my_user->uid) && !empty($my_user->uid)){
                    $result['ok']='ok';
                }
            }
        }
    }
    $result=serialize($result);
    print $result;
    exit();
}
function red_servidor_validar_usuario_network_save_user_subdominios_network_array(){
    $result=array();
    $result['ok']='ok';
    if(isset($_POST['user_enviar']) && !empty($_POST['user_enviar'])){
        $user_enviar=$_POST['user_enviar'];
        $user_enviar=base64_decode($user_enviar);
        $user_enviar=red_compartir_grupo_decrypt_text($user_enviar);
        $user_enviar=base64_decode($user_enviar);        
        $user_array=unserialize($user_enviar);        
        if(!empty($user_array)){
            foreach($user_array as $i=>$row){
                red_servidor_validar_usuario_network_save_user_subdominios_todos($row);
                $my_user=user_load(array('name'=>$row->name));
                if(isset($my_user->uid) && !empty($my_user->uid)){
                    if($my_user->mail==$row->mail){
                        continue;
                    }
                    continue;
                }else{
                    red_servidor_validar_usuario_network_save_user_subdominios($row);                    
                }                
            }
        }
    }
    $result=serialize($result);
    print $result;
    exit();
}
function red_servidor_validar_usuario_network_save_user_subdominios($row){
    $red_servidor_validar_usuario_network_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($row);
    if(count($red_servidor_validar_usuario_network_array)>0){
        return 0;
    }else{
        $red_servidor_validar_usuario_network_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($row,'',$row->name);
         if(count($red_servidor_validar_usuario_network_array)>0){   
            //si algo no funciona puede ser por esto
            db_query('UPDATE {red_servidor_validar_usuario_network} SET mail="%s" WHERE name="%s"',$row->mail,$row->name); 
            return 0; 
         }else{
            //si algo no funciona puede ser por esto
            $red_servidor_validar_usuario_network_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($row,$row->mail,'',1); 
            if(count($red_servidor_validar_usuario_network_array)>0){
                db_query('UPDATE {red_servidor_validar_usuario_network} SET name="%s" WHERE mail="%s"',$row->name,$row->mail);
            }else{
                $created=time(); 
                db_query('INSERT {red_servidor_validar_usuario_network}(name,mail,base_url,sareko_id,created) VALUES("%s","%s","%s","%s",%d)',$row->name,$row->mail,$row->base_url,$row->sareko_id,$created); 
            }               
         }
    } 
}
//si algo no funciona puede ser por esto
//function red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($user_row,$mail=''){
function red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($user_row,$mail='',$name='',$is_mail=0){
    $result=array();
    if(!empty($mail) && !$is_mail){
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network} WHERE name="%s" AND mail="%s"',$user_row->name,$mail));
    }else if(!empty($mail)){
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network} WHERE mail="%s"',$mail));
    }else if(!empty($name)){
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network} WHERE name="%s"',$name));
    }else{
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network} WHERE name="%s"',$user_row->name));
    }
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_validar_usuario_network_get_result_no_network($user_local,$result_in,$presave_mail){
    $result=$result_in;
    if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
        $red_servidor_validar_usuario_network_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($user_local);
        if(count($red_servidor_validar_usuario_network_array)>0){
            foreach($red_servidor_validar_usuario_network_array as $i=>$my_user){
                    //if(isset($user_local->uid) && !empty($user_local->uid)){    
                        if($my_user->mail==$user_local->mail || $my_user->mail==$presave_mail){
                            //$result['ok']='ok';
                            continue;
                        }else{
                            //return '';
                            $result['ok']='';
                            $result['email_diferente']=1;
                            break;
                        }
                    /*}else{
                        //$result['ok']='';
                        return '';
                    }*/                    
            }
        }
    }    
    return $result;
}
function red_servidor_validar_usuario_network_array($user_local,$result_in,$presave_mail){
    $result=$result_in;
    $result=red_servidor_validar_usuario_network_array_by_name($user_local,$result,$presave_mail);
    if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
        $result=red_servidor_validar_usuario_network_array_by_mail($user_local,$result,$presave_mail);
        if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
            $result=red_servidor_validar_usuario_network_get_result_no_network($user_local,$result,$presave_mail);
            if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
                $result=red_servidor_validar_usuario_network_array_by_name($user_local,$result,$presave_mail,0);
                if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
                    $result=red_servidor_validar_usuario_network_array_by_mail($user_local,$result,$presave_mail,0);
                }
            }
        }
    }
    return $result;
}
function red_servidor_validar_usuario_network_array_by_name($user_local,$result_in,$presave_mail,$is_network=1){
    $result=$result_in;
    $user_array=red_servidor_validar_usuario_network_get_user_array($user_local->name,'',$is_network);
    if(!empty($user_array)){
        foreach($user_array as $i=>$my_user){
                if((isset($my_user->uid) && !empty($my_user->uid) || !$is_network)){
                    if(isset($user_local->uid) && !empty($user_local->uid)){    
                        if($my_user->mail==$user_local->mail || $my_user->mail==$presave_mail){
                            //$result['ok']='ok';
                            continue;
                        }else{
                            $result['ok']='';
                            $result['email_diferente']=1;                            
                        }
                    }else{
                        //si algo no funciona puede ser por esto, antes por este sitio era $result['ok']=''
                        //$result['ok']='';
                        if(($my_user->mail==$user_local->mail)){
                                //$result['ok']='ok';
                                 continue;
                        }else{
                            $result['ok']='';
                            $result['email_diferente']=1;
                            break;
                        }
                    }    
                }else{
                    //$result['ok']='ok';
                    continue;
                }
        }
    }
    //return $result['ok'];
    return $result;
}
function red_servidor_validar_usuario_network_get_user_array($name,$mail='',$is_network=1){
    $result=array();
    if(!empty($name) && !empty($mail)){
        if($is_network){
            $res=db_query($sql=sprintf('SELECT * FROM {users} WHERE name="%s" AND mail="%s"',$name,$mail));
        }else{            
            $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network} WHERE name="%s" AND mail="%s"',$name,$mail));
        }    
    }else if(!empty($name)){
        if($is_network){
            $res=db_query('SELECT * FROM {users} WHERE name="%s"',$name);
        }else{
            $res=db_query('SELECT * FROM {red_servidor_validar_usuario_network} WHERE name="%s"',$name);
        }    
    }else if(!empty($mail)){
        if($is_network){
            $res=db_query('SELECT * FROM {users} WHERE mail="%s"',$mail);
         }else{
            $res=db_query('SELECT * FROM {red_servidor_validar_usuario_network} WHERE mail="%s"',$mail);
        }    
    }else{
        return $result;
    }
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_validar_usuario_network_array_by_mail($user_local,$result_in,$presave_mail='',$is_network=1){
    $result=$result_in;
    $user_array=red_servidor_validar_usuario_network_get_user_array('',$user_local->mail,$is_network);
    if(!empty($user_array)){
        foreach($user_array as $i=>$my_user){
                if((isset($my_user->uid) && !empty($my_user->uid) || !$is_network)){
                    if(isset($user_local->uid) && !empty($user_local->uid)){    
                        if($my_user->name==$user_local->name || $my_user->mail==$presave_mail){
                        //if($my_user->name==$user_local->name){
                            //$result['ok']='ok';
                            continue;
                        }else{
                            $result['ok']='';
                            break;
                        }
                    }else{
                        //si algo no funciona puede ser por esto, antes por este sitio era $result['ok']=''
                        //$result['ok']='';
                        if(($my_user->name==$user_local->name)){
                                //$result['ok']='ok';
                                 continue;
                        }else{
                            $result['ok']='';
                            break;
                        }
                    }    
                }else{
                    //$result['ok']='ok';
                    continue;
                }
        }
    }
    return $result;
}
function red_servidor_validar_usuario_network_save_user_subdominios_by_presave_mail($user_local,$presave_mail){
    if($user_local->mail!=$presave_mail){
        $user_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_array($user_local,$presave_mail);
        if(!empty($user_array)){
            foreach($user_array as $i=>$my_user){
                db_query('UPDATE {red_servidor_validar_usuario_network} SET mail="%s" WHERE id=%d',$user_local->mail,$my_user->id); 
            }
        }    
    }
}
function red_servidor_validar_usuario_network_registrados_callback(){
    $sort='asc';
    $is_numeric=0;
    $field='name';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }    
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Name')){
            $field='name';
        }else if($order==t('Email')){
            $field='mail';
        }else if($order==t('Hontza URL')){
            $field='base_url';    
        }else if($order==t('Hontza id')){
            $field='sareko_id';     
        }else if($order==t('Created')){
            $field='created';            
        }             
    }    
    //
    $my_limit=40;
    //
    $headers=array();
    $headers[0]=array('data'=>t('Name'),'field'=>'name');
    $headers[1]=array('data'=>t('Email'),'field'=>'mail');
    $headers[2]=array('data'=>t('Hontza URL'),'field'=>'base_url');
    $headers[3]=array('data'=>t('Hontza id'),'field'=>'sareko_id');
    $headers[4]=array('data'=>t('Date'),'field'=>'created');
    $kont=0;
    $rows=array();
    $type=arg(2);
    $table='red_servidor_validar_usuario_network';
    if(!empty($type) && $type=='registrados_todos'){
        $table='red_servidor_validar_usuario_network_todos';
    }
    $res=db_query('SELECT * FROM {'.$table.'} WHERE 1 ORDER BY '.$field.' '.$sort);    
    while($r=db_fetch_object($res)){
        $rows[$kont][0]=$r->name;
        $rows[$kont][1]=$r->mail;
        $rows[$kont][2]=$r->base_url;
        $rows[$kont][3]=$r->sareko_id;
        $rows[$kont][4]='';
        if(!empty($r->created)){    
            $rows[$kont][4]=date('Y-m-d H:i',$r->created);
        }    
        $kont++;        
    }
    $type=arg(3);
    if(!empty($type) && $type=='exportar_csv'){
        red_servidor_validar_usuario_network_registrados_exportar_csv($rows);
        exit();
    }
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    $links=red_servidor_validar_usuario_network_registrados_get_links();
    $output=red_servidor_registrar_registrados_navegar_menu().$links.$output;
    return $output;
}
function red_servidor_validar_usuario_network_save_user_subdominios_todos($row){
    $red_servidor_validar_usuario_network_todos_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_todos_array($row);
    if(count($red_servidor_validar_usuario_network_todos_array)>0){
        return 0;
    }else{    
        //si algo no funciona puede ser por esto        
        $red_servidor_validar_usuario_network_todos_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_todos_array('','',$row->name);
        if(count($red_servidor_validar_usuario_network_todos_array)>0){
            //si algo no funciona puede ser por esto
            db_query('UPDATE {red_servidor_validar_usuario_network_todos} SET mail="%s" WHERE name="%s"',$row->mail,$row->name); 
            return 0; 
        }else{
            //si algo no funciona puede ser por esto
            $red_servidor_validar_usuario_network_todos_array=red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_todos_array('',$row->mail);
            if(count($red_servidor_validar_usuario_network_todos_array)>0){
                db_query('UPDATE {red_servidor_validar_usuario_network_todos} SET name="%s" WHERE mail="%s"',$row->name,$row->mail); 
            }else{
                $created=time(); 
                db_query('INSERT {red_servidor_validar_usuario_network_todos}(name,mail,base_url,sareko_id,created) VALUES("%s","%s","%s","%s",%d)',$row->name,$row->mail,$row->base_url,$row->sareko_id,$created); 
            }    
        }
    }    
}
//si algo no funciona puede ser por esto 
//function red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_todos_array($user_row){
function red_servidor_validar_usuario_network_get_red_servidor_validar_usuario_network_todos_array($user_row,$mail='',$name=''){
    if(!empty($mail)){
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network_todos} WHERE mail="%s"',$mail));
    }else if(!empty($name)){
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network_todos} WHERE name="%s"',$name));
    }else{
        $res=db_query($sql=sprintf('SELECT * FROM {red_servidor_validar_usuario_network_todos} WHERE name="%s" AND mail="%s"',$user_row->name,$user_row->mail));
    }    
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_servidor_validar_usuario_network_registrados_exportar_csv($rows){
    $data_csv_array=array();
        $data_csv_array[0]=array('Name','Email','Hontza URL','Hontza id','Date');
        if(!empty($rows)){
            foreach($rows as $i=>$row){
                $data_csv=$row;                        
                $data_csv_array[]=$data_csv;    
            }
        }
        estrategia_call_download_resumen_preguntas_clave_canales_csv($data_csv_array,'usuarios',"\t");  
}
function red_servidor_validar_usuario_network_registrados_get_links(){
    global $base_url;
    $html=array();
    $html[]='<div>';
    /*$my_grupo=og_get_group_context();
    $grupo='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo='/'.$my_grupo->purl;
    }
    $url_exportar=$base_url.$grupo.'/'.request_uri();*/
    $url_exportar=$base_url.request_uri().'/exportar_csv';
    $html[]=l(t('Export csv'),$url_exportar,array('attributes'=>array('target'=>'_blank'),'absolute'=>true));
    $html[]='</div>';
    return implode('',$html);
}