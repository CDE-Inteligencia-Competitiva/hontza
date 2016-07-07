<?php
function red_compartir_grupo_hoja_callback(){
    return drupal_get_form('red_compartir_grupo_hoja_form'); 
}
function red_compartir_grupo_hoja_form(){
    //intelsat-2016
    global $base_root;
    $form=array();
    //intelsat-2016
    red_compartir_grupo_hoja_access_denied();
    $form['#action']=red_compartir_grupo_define_url_grupo_servidor();
    //print red_compartir_grupo_define_url_grupo_servidor();
    $form['#attributes']=array('target'=>'_blank');
    $nid=arg(2);
    $node=node_load($nid);
    $node->sareko_id=_SAREKO_ID;
    $grupo_enviar=red_compartir_grupo_prepare_grupo_enviar($node);
    $form['grupo_enviar']=array(
        '#type'=>'hidden',
        '#default_value'=>$grupo_enviar,
    );
    //intelsat-2016
    $share=t('Saving group settings in "@nombre_servidor_local"',array('@nombre_servidor_local'=>$base_root));    
    $form['grupo_name']=array(
         //intelsat-2016
         //'#value'=>'<p><b>'.$node->title.'</b></p>
         '#value'=>'<p>'.$share.'</p>',
    );
    /*
    //intelsat-2016
    $is_publico_colaboratico=1;
    if(hontza_registrar_is_registrar_activado() && estrategia_is_grupo_publico($node,0,$is_publico_colaboratico)){
        red_compartir_registrar_grupo_add_form_fields($node,$form);
    }*/
    /*$form['grupo_name']=array(
        '#title'=>t('Group'),
        '#type'=>'textfield',        
        '#default_value'=>$node->title,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $grupo_admin=hontza_grupos_get_admin_grupo($node);
    $form['grupo_admin']=array(
        '#title'=>t('Group Administrator'),
        '#type'=>'textfield',        
        '#default_value'=>$grupo_admin->name,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $form['grupo_admin_name']=array(
        '#title'=>t('Group Administrator Name'),
        '#type'=>'textfield',        
        '#default_value'=>$grupo_admin->profile_nombre,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $form['grupo_admin_surname']=array(
        '#title'=>t('Group Administrator Surname'),
        '#type'=>'textfield',        
        '#default_value'=>$grupo_admin->profile_apellidos,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $form['pais']=array(
        '#title'=>t('Country'),
        '#type'=>'textfield',
        '#default_value'=>red_compartir_grupo_get_pais_value($node),
    );
    $form['temas']=array(
        '#title'=>t('Topics of interest'),
        '#type'=>'textfield',
        '#default_value'=>red_compartir_grupo_get_temas_value($node),
    );
    $form['region']=array(
        '#title'=>t('Regions of interest'),
        '#type'=>'textfield',
        '#default_value'=>red_compartir_grupo_get_region_value($node),
    );*/
    $form['share_btn']=array(
        '#type'=>'submit',
        //intelsat-2016
        //'#default_value'=>t('Connect to Network'),
        '#default_value'=>t('Please click here to continue'),
    );
    $destination='node/'.$nid;
    if(isset($_REQUEST['destination'])){
        $destination=$_REQUEST['destination'];
    }
    //intelsat-2016
    //se ha comentado
    /*$form['cancelar_btn']=array(
        '#value'=>l(t('Return'),$destination),
    );*/
    //intelsat-2016
    //drupal_set_title($share);
    drupal_set_title('Saving Group Settings');
    red_compartir_grupo_add_hoja_js();
    return $form;
}
function red_compartir_grupo_add_hoja_js(){
    //intelsat-2016
    $js='';
    /*$js.='var red_registrar_is_registrar_activado=0;';    
    if(hontza_registrar_is_registrar_activado()){
        $js.='red_registrar_is_registrar_activado=1;';
    }*/
    $js.='$(document).ready(function(){
        //$("#edit-share-btn").click();
        $("#edit-share-btn").click(function(){
            call_red_compartir_grupo_hoja_ajax();
            //return false;
        });
        //$("#red-compartir-grupo-hoja-form").submit();
        function call_red_compartir_grupo_hoja_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var grupo_enviar_val=$("#edit-grupo-enviar").val();
            var pais_enviar_val=$("#edit-pais").val();
            //var temas_enviar_val=$("#edit-temas").val();
            var region_enviar_val=$("#edit-region").val();
            var registrar_grupo_name="";
            /*if(red_registrar_is_registrar_activado){
                registrar_grupo_name=$("#edit-registrar-grupo-name").val();
            }*/
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_grupo_guardar_en_local_compartir_grupo_hoja_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {grupo_enviar:grupo_enviar_val,pais_enviar:pais_enviar_val,region_enviar:region_enviar_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('user-gestion/grupos/propios').'";
				}
			});          
        }
        /*if(red_registrar_is_registrar_activado){
            $("#edit-share-btn").click();
        }*/        
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_grupo_prepare_grupo_enviar($node,$user_array_in=''){
    global $user;
    //AVISO::::es mejor enviar así porque mandando el local tenemos problemas
    $my_user=user_load($user->uid);
    $grupo_enviar=new stdClass();
    $grupo_enviar->nid=$node->nid;
    $grupo_enviar->vid=$node->vid;
    $grupo_enviar->grupo_title=$node->title;
    $grupo_enviar->sareko_id=$node->sareko_id;
    $grupo_enviar->subdominio=red_compartir_grupo_get_subdominio();
    $grupo_enviar->uid=$my_user->uid;
    $grupo_enviar->user_local=$my_user;
    if(!empty($user_array_in)){
        $user_array=$user_array_in;
    }else{
        $user_array=hontza_get_usuarios_grupo($node->nid);
    }
    $grupo_enviar->user_array=red_compartir_grupo_prepare_user_enviar_array($user_array);
    //simulando
    //$grupo_enviar=new stdClass();
    //$grupo_enviar->proba='hola';
    //intelsat-2016
    if(hontza_registrar_is_registrar_activado()){
        $grupo_enviar->registrar_grupo=red_compartir_registrar_grupo_get_registrar_grupo_enviar($grupo_enviar,$node);
    }
    //
    $result=base64_encode(serialize($grupo_enviar));
    $result=red_compartir_grupo_encrypt_text($result);
    $result=base64_encode($result);
    
    return $result;    
}
function red_compartir_grupo_get_subdominio(){
    global $base_url;
    $result=$base_url;
    $url_array=parse_url($result);
    if(isset($url_array['host'])){
        return $url_array['host'];
    }
    return $result;
}
function red_compartir_grupo_define_url_grupo_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_grupo_hoja',array('absolute'=>TRUE));
}
function red_compartir_grupo_guardar_en_local_compartir_grupo_hoja_enviado_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['grupo_enviar']) && !empty($_POST['grupo_enviar'])){
        $grupo_enviar=$_POST['grupo_enviar'];
        $grupo_enviar=base64_decode($grupo_enviar);
        $grupo_enviar=red_compartir_grupo_decrypt_text($grupo_enviar);
        $grupo_enviar=unserialize(base64_decode($grupo_enviar));
        /*$pais_enviar=$_POST['pais_enviar'];
        $temas_enviar=$_POST['temas_enviar'];
        $region_enviar=$_POST['region_enviar'];*/
        $pais_enviar='';
        $temas_enviar='';
        $region_enviar='';
        red_compartir_grupo_save_red_compartir_grupo($grupo_enviar,$pais_enviar,$temas_enviar,$region_enviar);
        //intelsat-2016
        /*if(hontza_registrar_is_registrar_activado()){
            red_compartir_registrar_local_save();
        }*/
        //intelsat-2016
        //drupal_set_message(t('Group %grupo_title shared',array('%grupo_title'=>$grupo_enviar->grupo_title)));
        drupal_set_message(t('<p>Group %grupo_title has been saved</p><p>Welcome to Hontza Network!</p>',array('%grupo_title'=>$grupo_enviar->grupo_title))); 
    }
    print json_encode($result);
    exit();
}
function red_compartir_grupo_save_red_compartir_grupo($grupo_enviar,$pais_enviar='',$temas_enviar='',$region_enviar=''){
    global $user;
    $row=red_compartir_grupo_get_red_compartir_grupo_row($grupo_enviar);
    if(isset($grupo_enviar->nid) && !empty($grupo_enviar->nid)){
        $nid=$grupo_enviar->nid;
        $vid=$grupo_enviar->vid;
        $uid=$user->uid;
        $fecha=time();
        $status=1;
        if(isset($row->id) && !empty($row->id)){
            db_query($sql=sprintf('UPDATE {red_compartir_grupo} SET uid=%d,fecha=%d,country="%s",topics_of_interest="%s",regions_of_interest="%s",status=%d WHERE nid=%d AND vid=%d',$uid,$fecha,$pais_enviar,$temas_enviar,$region_enviar,$status,$nid,$vid));
        }else{
            db_query($sql=sprintf('INSERT INTO {red_compartir_grupo}(nid,vid,uid,fecha,country,topics_of_interest,regions_of_interest,status) VALUES(%d,%d,%d,%d,"%s","%s","%s",%d)',$nid,$vid,$uid,$fecha,$pais_enviar,$temas_enviar,$region_enviar,$status));
        }
    }    
}
function red_compartir_grupo_get_red_compartir_grupo_row($node){
    $red_compartir_grupo_array=red_compartir_grupo_get_red_compartir_grupo_array($node->vid,$node->nid);
    if(count($red_compartir_grupo_array)>0){
        return $red_compartir_grupo_array[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function red_compartir_grupo_get_red_compartir_grupo_array($vid,$nid,$with_status=0){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($vid)){
        $where[]='vid='.$vid;
    }
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    if($with_status){
        $where[]='status=1';
    }
    $sql='SELECT * FROM {red_compartir_grupo} WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    //print $sql.'<BR>';
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_grupo_get_user_grupos_red_alerta(){
    global $user;
    $subdominio=red_compartir_grupo_get_subdominio();
    $result=array();
    $url=red_compartir_define_redalerta_servidor_url();
    //$url.='/red_servidor/red_servidor_grupo_get_user_grupos_red_alerta_get_contents?user_mail='.base64_encode($user->mail).'&subdominio='.base64_encode($subdominio);
    $url.='/red_servidor/red_servidor_grupo_get_user_grupos_red_alerta_get_contents?user_mail='.base64_encode($user->mail);    
    //print $url;exit();
    $content=file_get_contents($url);
    //print $content;exit();
    $result=unserialize(base64_decode($content));
    if(!empty($result)){
        //echo print_r($result,1);
        foreach($result as $key=>$row){
            $result[$key]=$row;
            $result[$key]->is_grupo_red_alerta=1;
        }
    }
    if(hontza_is_red_hoja()){
        $otros_sub_grupos=red_compartir_grupo_get_grupos_otros_subdominios($subdominio);
        if(!empty($otros_sub_grupos) && is_array($otros_sub_grupos)){
            $result=array_merge($otros_sub_grupos,$result);
        }    
    }
    return $result;
}
function red_compartir_grupo_prepare_user_enviar_array($user_array,$with_pass=1){
    $result=array();
    if(!empty($user_array)){
        $kont=0;
        foreach($user_array as $i=>$user){
            //intelsat-2015-redalerta-admin-user
            //if($user->uid!=1){
            if(red_is_not_admin($user)){    
                $result[$kont]=$user;
                if(!$with_pass){
                    unset($result[$kont]->pass);
                }
                unset($result[$kont]->og_groups);
                $result[$kont]->my_profile_values=red_compartir_grupo_get_user_profile_values($user->uid);
                //intelsat-2016
                $result[$kont]->users_facilitators_row=red_compartir_grupo_get_users_facilitators_enviar_row($user->uid);
                $kont++;
            }    
        }
        //echo print_r($result,1);exit();
        return $result;
    }
    return $user_array;
}
function red_compartir_grupo_get_user_profile_values($uid){
    $result=array();
    $sql='SELECT * FROM {profile_values} WHERE uid='.$uid;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_compartir_grupo_get_user_grupos_red_alerta_mis_grupos(){
    global $user;
    $result=array();
    $subdominio=red_compartir_grupo_get_subdominio();
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/red_servidor_grupo_get_user_grupos_red_alerta_mis_grupos_get_contents?user_mail='.base64_encode($user->mail);
    //print $url;exit();
    $content=file_get_contents($url);
    $decode=base64_decode($content);
    $result=unserialize($decode);
    if(!empty($result)){
        foreach($result as $key=>$row){
            //echo print_r($row,1);exit();
            $result[$key]=$row;
            $result[$key]['is_grupo_red_alerta']=1;
            //hontza_login_red_alerta_formulario($row['login_red_alerta_url'], $row['my_grupo_nid']);
        }
    }
    if(hontza_is_red_hoja()){
        $otros_sub_grupos=red_compartir_grupo_get_grupos_otros_subdominios_mis_grupos($subdominio);        
        $result=array_merge($otros_sub_grupos,$result);
    }
    return $result;
}
function red_compartir_grupo_encrypt_text($value) {
   //intelsat-2016 
   /*if(!$value ) return false;
 
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,red_compartir_grupo_define_encrypt_key1(), $value, MCRYPT_MODE_ECB,red_compartir_grupo_define_encrypt_key2());
   return trim(base64_encode($crypttext));*/
   return red_crear_usuario_encrypt_text($value);
}
function red_compartir_grupo_define_encrypt_key1(){
    //intelsat-2016 
    /*$result=md5('kfshsdkhdsfhsdfkl vckviucvovciobvihsjkdfhdjkdjfhdfjdfhdfjf');
    return $result;*/
    return red_crear_usuario_define_encrypt_key1();
}
function red_compartir_grupo_define_encrypt_key2(){
    //intelsat-2016 
    //return md5(date('Y-m-d'));
    return red_crear_usuario_define_encrypt_key2();
}
function red_compartir_grupo_decrypt_text($value) {
   //intelsat-2016  
   /*if(!$value ) return false;
 
   $crypttext = base64_decode($value);
   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,red_compartir_grupo_define_encrypt_key1(), $crypttext, MCRYPT_MODE_ECB, red_compartir_grupo_define_encrypt_key2() );
   return trim($decrypttext);*/
   return red_crear_usuario_decrypt_text($value); 
}
function red_compartir_grupo_get_user_grupos_subdominio_get_contents_callback($is_print=1){
    $result=array();
    if(isset($_REQUEST['user_mail']) && !empty($_REQUEST['user_mail'])){
            $sub_grupos_array=array();
            $user_mail=base64_decode($_REQUEST['user_mail']);
            /*if(isset($_REQUEST['subdominio']) && !empty($_REQUEST['subdominio'])){
                $subdominio=base64_decode($_REQUEST['subdominio']);
                //if(hontza_is_servidor_red_alerta()){
                    //$sub_grupos_array=red_servidor_grupo_get_grupos_red_alerta($subdominio);
                    $sub_grupos_array=red_compartir_grupo_get_grupos_red_alerta($subdominio);
                //}
            }*/           
            $uid=red_get_user_uid_by_mail($user_mail);
            if(!empty($uid)){
                $result=my_get_og_grupo_list($uid,1,0,0);
            }
            $result=array_merge($sub_grupos_array,$result);
            if(!hontza_is_servidor_red_alerta()){
                $result=red_compartir_grupo_repasar_no_compartidos($result);
                $result=red_compartir_grupo_repasar_congelados($result);
            }
    }
    $output=base64_encode(serialize($result));        
    if($is_print){
        print $output;
        exit();
    }
    return $output;
}
function red_compartir_grupo_on_user_save($my_user){
    //intelsat-2015-redalerta-admin-user            
    //if(isset($my_user->uid) && !empty($my_user->uid) && $my_user->uid!=1){
    if(red_is_not_admin($my_user)){
    //    
        //if(hontza_is_sareko_user(0,$my_user)){
        if(hontza_is_sareko_user(1,$my_user)){
                $url='';
                $user_array=array();
                $user_array[0]=$my_user;
                $user_array=red_compartir_grupo_prepare_user_enviar_array($user_array);
                $user_enviar=serialize($user_array[0]);            
                $user_enviar=red_compartir_grupo_encrypt_text($user_enviar);
                $postdata=array();
                $postdata['user_enviar']=$user_enviar;
                //$postdata=http_build_query($postdata);
                //
            if(hontza_is_red_hoja()){    
                $url=red_compartir_define_redalerta_servidor_url().'/red_servidor/user_change';
                $content=red_compartir_grupo_postapi($url,$postdata);
            }//else if(hontza_is_servidor_red_alerta()){
                if(hontza_is_servidor_red_alerta()){
                    $subdominio_array=red_servidor_grupo_get_subdominio_array($my_user);   
                }else{
                    $subdominio_array=red_compartir_grupo_get_subdominio_array($my_user);            
                }            
                if(!empty($subdominio_array)){
                    foreach($subdominio_array as $i=>$subdominio){
                        if(red_compartir_grupo_is_apply_user_change($subdominio)){
                            $url='http://'.$subdominio.'/red_compartir/user_change';
                            $content=red_compartir_grupo_postapi($url,$postdata);
                        }
                    }
                }
            //}        
        }
    }
}
function red_compartir_grupo_postapi($url,$postdata,$is_borrarme_del_servidor=0)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query( $postdata ) );
    $data=curl_exec($curl);
    $result=unserialize(trim($data));
    /*if($is_borrarme_del_servidor){
        echo print_r($result,1);exit();
    }*/
    curl_close($curl);    
    if(isset($result['ok']) && !empty($result['ok']) && $result['ok']=='ok'){
        return 'ok';
    }
    return '';
}
function red_compartir_grupo_user_save($u_in,$is_edit=0,$uid_edit=0,$is_servidor_in=0){
    //AVISO::::se ha tomado como base sareratu_user_update de sareratu.user.inc
    $u=$u_in;
    $is_servidor=$is_servidor_in;
    if(hontza_is_servidor_red_alerta()){
        $is_servidor=1;
    }
    $roles=array();
    /*echo print_r($u,1);
    exit();*/
    if(isset($u->roles) && !empty($u->roles)){
        $roles=$u->roles;
    }
    if($is_edit){
        $result = db_query('REPLACE INTO {users} (uid,name, pass, mail, mode, sort, threshold, theme, signature, created, access, login, status, timezone, language, picture, init, data, signature_format) VALUES(%d,"%s", "%s", "%s", %d, %d, %d, "%s", "%s", %d, %d, %d, %d, "%s", "%s", "%s", "%s", "%s", %d)',$uid_edit,$u->name, $u->pass, $u->mail, $u->mode, $u->sort, $u->threshold, $u->theme, $u->signature, $u->created, $u->access, $u->login, $u->status, $u->timezone, $u->language, $u->picture, $u->init, $extra_data, $u->signature_format);
    }else{
        $result = db_query('INSERT INTO {users} (name, pass, mail, mode, sort, threshold, theme, signature, created, access, login, status, timezone, language, picture, init, data, signature_format) VALUES("%s", "%s", "%s", %d, %d, %d, "%s", "%s", %d, %d, %d, %d, "%s", "%s", "%s", "%s", "%s", %d)',$u->name, $u->pass, $u->mail, $u->mode, $u->sort, $u->threshold, $u->theme, $u->signature, $u->created, $u->access, $u->login, $u->status, $u->timezone, $u->language, $u->picture, $u->init, $extra_data, $u->signature_format);
    }
    $uid=red_get_user_uid_by_mail($u->mail);
    $u->uid=$uid;
    unset($u->pass);
    user_save($u);
    if($is_servidor){
        //intelsat-2015-redalerta-admin-user
        //if($u->uid!=1){
        if(red_is_not_admin($u)){
        //    
            if(!empty($roles)){
                foreach($roles as $rid=>$rol_name){
                    if(!red_compartir_grupo_is_rol_limitado($rid)){
                        db_query($sql=sprintf('REPLACE INTO {users_roles}(rid, uid) VALUES(%d,%d)',$rid,$u->uid));        
                        //print $sql.'<BR>';
                    }    
                }
            }        
        }
    }    
    //exit();
    if(isset($u->my_profile_values) && !empty($u->my_profile_values)){
        foreach($u->my_profile_values as $i=>$f){
            db_query('REPLACE INTO {profile_values}(fid, uid, value) VALUES(%d, %d, "%s")', $f->fid,$uid,$f->value);
        }    
    }
    //intelsat-2016
    red_compartir_grupo_save_user_profile_values($u,$u_in);
    return $uid;
}
function red_compartir_grupo_user_change_callback(){
    $result=array();
    $result['ok']='ok';
    if(isset($_POST['user_enviar']) && !empty($_POST['user_enviar'])){
        $user_enviar=unserialize(red_compartir_grupo_decrypt_text($_POST['user_enviar']));
        if(isset($user_enviar->mail) && !empty($user_enviar->mail)){
            
                $uid=red_get_user_uid_by_mail($user_enviar->mail);
                if(!empty($uid)){
                    $uid=red_compartir_grupo_user_save($user_enviar,1,$uid);
                }
                
        }        
    }
    print serialize($result);
    exit(); 
}
function red_compartir_grupo_get_grupos_otros_subdominios($current_sub){
    global $user;
    $result=array();
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/red_servidor_grupo_get_user_grupos_otros_subdominios_get_contents?user_mail='.base64_encode($user->mail).'&subdominio='.base64_encode($current_sub);
    //print $url;exit();
    $content=file_get_contents($url);
    //print $content;exit();
    $result=unserialize(base64_decode($content));
    //echo print_r($result,1);exit();
    if(!empty($result)){
        foreach($result as $key=>$row){
            //echo 'row===='.print_r($row,1);            
            $result[$key]=$row;
            $result[$key]->is_grupo_red_alerta=1;
            if(!isset($row->users_in_group)){
                $result[$key]->users_in_group=get_group_member_count($row);
            }
            if(!isset($row->subject_area)){
                $result[$key]->subject_area=hontza_grupos_get_subject_area_by_row($row);
            }
        }
    }    
    return $result;
}
function red_compartir_grupo_get_grupos_otros_subdominios_mis_grupos($subdominio){
    $rows=array();
    $otros_sub_grupos=red_compartir_grupo_get_grupos_otros_subdominios($subdominio);
    if(!empty($otros_sub_grupos)){
        return red_compartir_grupo_set_mis_grupos_row($otros_sub_grupos); 
    }
    return $rows;
}
function red_compartir_grupo_set_mis_grupos_row($grupos_red_alerta){
    $kont=0;
    $rows=array();
    if(!empty($grupos_red_alerta)){
        foreach($grupos_red_alerta as $i=>$row){
            $data=$row;
            $data->node_title=$row->title;
            $data->term_data_tid=$row->type_of_group_tid;
            hontza_grupos_set_mis_grupos_row($data,'',$kont,'',2,$rows);
            $kont++;                
        }
    }
    return $rows;    
}
function red_compartir_grupo_on_add_user_group($my_user,$grupo_nid){
     if(red_compartir_grupo_is_grupo_red_alerta($grupo_nid)){
         //if(hontza_is_sareko_user(0,$my_user)){
                $url='';
                $user_array=array();
                $user_array[0]=$my_user;               
                //$user_array=red_compartir_grupo_prepare_user_enviar_array($user_array);
                $node=node_load($grupo_nid);
                $grupo_enviar=red_compartir_grupo_prepare_grupo_enviar($node,$user_array);
                $postdata=array();
                $postdata['grupo_enviar']=$grupo_enviar;
                //
            if(hontza_is_red_hoja()){
                $url=red_compartir_define_redalerta_servidor_url().'/red_servidor/add_user_group';
                $content=red_compartir_grupo_postapi($url,$postdata);                
            }/*else if(hontza_is_servidor_red_alerta()){
                $subdominio_array=red_servidor_grupo_get_subdominio_array($my_user);
                if(!empty($subdominio_array)){
                    foreach($subdominio_array as $i=>$subdominio){
                        $url='http://'.$subdominio.'/red_compartir/user_change';
                        $content=red_compartir_grupo_postapi($url,$postdata);
                    }
                }
            }*/          
        //}
     }   
}
function red_compartir_grupo_is_grupo_red_alerta($grupo_nid=''){
    if(empty($grupo_nid)){
        $node=og_get_group_context();            
    }else{
        $node=node_load($grupo_nid);
    }
    if(isset($node->nid) && !empty($node->nid)){
        $grupo_array=red_compartir_grupo_get_red_compartir_grupo_array($node->vid, $node->nid,1);
        if(count($grupo_array)){
            return 1;
        }        
    }
    return 0;
}
function red_compartir_grupo_is_apply_user_change($subdominio){
    if(hontza_is_servidor_red_alerta()){
        return 1;
    }else{
        $current_sub=red_compartir_grupo_get_subdominio();
        if($current_sub!=$subdominio){
            return 1;
        }
        return 0;
    }
}
function red_compartir_grupo_get_subdominio_array($my_user){
    $result=array();
    $subdominio=red_compartir_grupo_get_subdominio();
    $url=red_compartir_define_redalerta_servidor_url();
    $url.='/red_servidor/red_servidor_grupo_get_subdominio_array_get_contents?user_mail='.base64_encode($my_user->mail);
    $content=file_get_contents($url);
    $decode=base64_decode($content);
    $result=unserialize($decode);
    return $result;
}
function red_compartir_grupo_get_pais_value($node){
    if(isset($node->field_group_country) && isset($node->field_group_country[0]) && isset($node->field_group_country[0]['value'])){
        return $node->field_group_country[0]['value'];
    }
    return '';
}
function red_compartir_grupo_get_temas_value($node){
    if(isset($node->field_group_topics_of_interest) && isset($node->field_group_topics_of_interest[0]) && isset($node->field_group_topics_of_interest[0]['value'])){
        return $node->field_group_topics_of_interest[0]['value'];
    }
    return '';
}
function red_compartir_grupo_get_region_value($node){
    if(isset($node->field_group_regions_of_interest) && isset($node->field_group_regions_of_interest[0]) && isset($node->field_group_regions_of_interest[0]['value'])){
        return $node->field_group_regions_of_interest[0]['value'];
    }
    return '';
}
function red_compartir_grupo_is_grupo_con_datos_rellenados($nid){
    $grupo=node_load($nid);
    if(isset($grupo->nid) && !empty($grupo->nid)){
        $texto=hontza_content_full_text($grupo);
        $texto=trim(strip_tags($texto));
        if(empty($texto)){
            return 0;
        }
        
        return 1;
    }
    return 0;
}
function red_compartir_grupo_no_compartir_grupo_hoja_callback(){
    drupal_set_title(t('Disconnect from Network'));
    return drupal_get_form('red_compartir_grupo_no_compartir_grupo_hoja_form'); 
}
function red_compartir_grupo_no_compartir_grupo_hoja_form(){
    $form=array();
    //intelsat-2016
    red_compartir_grupo_no_compartir_grupo_hoja_access_denied();
    $form['#action']=red_compartir_grupo_no_compartir_grupo_define_url_servidor();
    //simulando
    //unset($form['#action']);
    //print red_compartir_grupo_define_url_grupo_servidor();
    $form['#attributes']=array('target'=>'_blank');
    $nid=arg(2);
    $node=node_load($nid);
    $node->sareko_id=_SAREKO_ID;
    $grupo_enviar=red_compartir_grupo_prepare_no_compartir_grupo_enviar($node);
    $form['grupo_enviar']=array(
        '#type'=>'hidden',
        '#default_value'=>$grupo_enviar,
    );    
    $form['grupo_name']=array(
        '#value'=>'<p><b>'.$node->title.'</b></p>',
    );
    
    $form['share_btn']=array(
        '#type'=>'submit',
        '#default_value'=>t('Disconnect from Network'),
    );
    $destination='node/'.$nid;
    if(isset($_REQUEST['destination'])){
        $destination=$_REQUEST['destination'];
    }
    $form['cancelar_btn']=array(
        '#value'=>l(t('Return'),$destination),
    );    
    red_compartir_grupo_no_compartir_grupo_add_hoja_js();
    return $form;
}
function red_compartir_grupo_no_compartir_grupo_define_url_servidor(){
    $redalerta_servidor_url=red_compartir_define_redalerta_servidor_url();
    return url($redalerta_servidor_url.'/red_servidor/guardar_no_compartir_grupo_hoja',array('absolute'=>TRUE));    
}
function red_compartir_grupo_prepare_no_compartir_grupo_enviar($node,$user_array_in=''){
    $grupo_enviar=new stdClass();
    $grupo_enviar->nid=$node->nid;
    $grupo_enviar->vid=$node->vid;
    $grupo_enviar->grupo_title=$node->title;
    $grupo_enviar->sareko_id=$node->sareko_id;
    $grupo_enviar->subdominio=red_compartir_grupo_get_subdominio();
    $grupo_enviar->uid=$my_user->uid;
    //
    $result=base64_encode(serialize($grupo_enviar));
    $result=red_compartir_grupo_encrypt_text($result);
    $result=base64_encode($result);
    
    return $result;    
}
function red_compartir_grupo_no_compartir_grupo_add_hoja_js(){
 
    $js='$(document).ready(function(){
        $("#edit-share-btn").click(function(){
            call_red_no_compartir_grupo_hoja_ajax();
            //return false;
        });
        function call_red_no_compartir_grupo_hoja_ajax(){ 
            var d=new Date();
            var n=d.getTime();
            var grupo_enviar_val=$("#edit-grupo-enviar").val();
            jQuery.ajax({
				type: "POST",
				url: "'.url('red_compartir/red_compartir_grupo_guardar_en_local_no_compartir_grupo_hoja_enviado',array('absolute'=>TRUE)).'?my_time="+n,
				data: {grupo_enviar:grupo_enviar_val},
				dataType:"json",
				success: function(my_result){
                                  window.location.href="'.url('user-gestion/grupos/propios').'";
				}
			});          
        }          
    });';        
    drupal_add_js($js,'inline');
}
function red_compartir_grupo_get_icono_red_alerta($row_in,$is_mis_grupos,$is_grupo_local,&$icon_name){
    global $base_url;
    /*if($is_mis_grupos){
        echo print_r($row,1);
    }*/
    $title='';
    $icon_name='';
    //intelsat-2015
    if(empty($row_in)){
        $row=og_get_group_context();
    }else{
        $row=$row_in;
    }
    //
    //$path=$base_url.base_path().drupal_get_path('theme','buho').'/images/icons/';
    $path=$base_url.'/'.drupal_get_path('theme','buho').'/images/icons/';
    //intelsat-2015
    if($is_grupo_local){
        //AVISO::::solo se le llama desde el icono "Net Resources" del menú desplegable de arriba
        if(isset($row->nid)){
            if(!red_compartir_grupo_is_grupo_red_alerta($row->nid)){
                if(!hontza_is_servidor_red_alerta()){
                    $title=t('Local Not Connected Group');
                    //$icon_name='grupo_local_no_compartido';
                    //$icon_name='transparente';
                    $icon_name='grupo_local_no_conectado';
                }
            }
        }    
    }else if(red_compartir_grupo_is_grupo_de_otro_subdominio($row)){
        $title=t('Remote Connected Group');
        $icon_name='grupo_otro_subdominio';
    }else if(red_compartir_grupo_is_grupo_local_compartido_by_row($row)){
        $title=t('Local Connected Group');
        $icon_name='grupo_local_compartido';
    }else if(red_compartir_grupo_is_grupo_local_no_compartido_by_row($row)){
        if(hontza_is_servidor_red_alerta()){
            $title=t('Network Shared Group');
            $icon_name='grupo_del_servidor_red_alerta';
        }else{
            $title=t('Local Not Connected Group');
            //$icon_name='grupo_local_no_compartido';
            //$icon_name='transparente';
            $icon_name='grupo_local_no_conectado';
        }
    }else if(red_compartir_grupo_is_grupo_red_alerta_by_row($row)){
        $title=t('Network Shared Group');
        $icon_name='grupo_del_servidor_red_alerta';
    }           
    if(!empty($icon_name)){        
        $icon=$path.$icon_name.'.png';
        //
        if(empty($title)){
            if(isset($row->title)){
                $title=$row->title;
            }else if($row->node_title){
                $title=$row->node_title;
            }
        }
        return '<img class="icono_grupo_red_alerta" src="'.$icon.'" title="'.$title.'" alt="'.$title.'"/>';
    }
    return '';
}
function red_compartir_grupo_is_grupo_de_otro_subdominio($row){
    if(isset($row->is_grupo_subdominio) && !empty($row->is_grupo_subdominio)){
        return 1;
    }
    return 0;
}
function red_compartir_grupo_is_grupo_red_alerta_by_row($row){
    if(isset($row->is_grupo_red_alerta) && !empty($row->is_grupo_red_alerta)){
        return 1;
    }
    return 0;
}
function red_compartir_grupo_is_grupo_local_compartido_by_row($row){
    if(!red_compartir_grupo_is_grupo_red_alerta_by_row($row)){
        if(isset($row->is_grupo_local) && !empty($row->is_grupo_local)){
            if(red_compartir_grupo_is_grupo_red_alerta($row->nid)){
                return 1;
            }
        }
    }
    return 0;
}
function red_compartir_grupo_is_grupo_local_no_compartido_by_row($row){
    if(!red_compartir_grupo_is_grupo_red_alerta_by_row($row)){
        if(isset($row->is_grupo_local) && !empty($row->is_grupo_local)){
            if(!red_compartir_grupo_is_grupo_red_alerta($row->nid)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_compartir_grupo_guardar_en_local_no_compartir_grupo_hoja_enviado_callback(){
    $result=array();
    $result['ok']=1;
    if(isset($_POST['grupo_enviar']) && !empty($_POST['grupo_enviar'])){
        $grupo_enviar=$_POST['grupo_enviar'];
        $grupo_enviar=base64_decode($grupo_enviar);
        $grupo_enviar=red_compartir_grupo_decrypt_text($grupo_enviar);
        $grupo_enviar=unserialize(base64_decode($grupo_enviar));       
        red_compartir_grupo_save_red_no_compartir_grupo($grupo_enviar);       
    }
    drupal_set_message(t('<p>Group %grupo_title has been disconnected from Network</p>',array('%grupo_title'=>$grupo_enviar->grupo_title))); 
    print json_encode($result);
    exit();
}
function red_compartir_grupo_save_red_no_compartir_grupo($grupo_enviar){
    global $user;
    $row=red_compartir_grupo_get_red_compartir_grupo_row($grupo_enviar);
    if(isset($grupo_enviar->nid) && !empty($grupo_enviar->nid)){
        $nid=$grupo_enviar->nid;
        $vid=$grupo_enviar->vid;
        $uid=$user->uid;
        $fecha=time();
        $status=0;
        if(isset($row->id) && !empty($row->id)){
            //db_query($sql=sprintf('UPDATE {red_compartir_grupo} SET uid=%d,fecha=%d,status=%d WHERE nid=%d AND vid=%d',$uid,$fecha,$status,$nid,$vid));
            db_query($sql=sprintf('UPDATE {red_compartir_grupo} SET status=%d WHERE nid=%d AND vid=%d',$status,$nid,$vid));
        }
    }    
}
function red_compartir_grupo_repasar_no_compartidos($result_in){
    //return $result_in;
    $result=array();
    if(!empty($result_in)){
        foreach($result_in as $key=>$row){
            if(red_compartir_grupo_is_grupo_red_alerta($row->nid)){
                $result[$key]=$row;
            }
        }
    }
    return $result;
}
function red_compartir_grupo_is_grupo_red_alerta_access_denied(){
    boletin_report_no_group_selected_denied();
    //simulando
    if(hontza_is_sareko_id('LOKALA')){
        return 1;
    }
    if(!red_compartir_grupo_is_grupo_red_alerta()){
        drupal_access_denied();
        exit();
    }
}
function red_compartir_grupo_repasar_congelados($result_in){
    $result=$result_in;
    if(!empty($result)){
        foreach($result as $i=>$row){
           $result[$i]->is_grupo_en_subdominio_congelado=hontza_is_grupo_congelado($row);
        }
    }
    return $result;
}
function red_compartir_grupo_is_rol_limitado($rid){
    /****AVISO::::Lo normal (y altamente deseable) es que en la Red Hontza haya muchos usuarios
    El problema pueden ser los derechos, cualquier instalacion de Alerta o de Hontza4 va a meter usuarios a la Red Hontza / Red Alerta sin control 
    por nuestra parte. Lo que no interesa es que haya usuarios en la Red Hontza "descontrolados" que tengan rol de creador de grupo ... y menos aun de admin
    */
    //3: Administrador
    //4: Creador de Grupo
    //10:Developer
    $rid_array=array(3,4,10);
    if(in_array($rid,$rid_array)){
        return 1;
    }
    return 0;
}
//intelsat-2016
function red_compartir_grupo_save_user_profile_values($u,$u_in){
    $account = new stdClass();
    $account->uid = $u->uid;
    $edit=array();
    $profile_fields_array=red_compartir_grupo_get_profile_fields_array();
    if(!empty($profile_fields_array)){
        foreach($profile_fields_array as $i=>$row){
            $field=$row->name;
            if(isset($u->$field)){
                $edit[$field]=$u->$field;
            }
        }
    }
    if(!empty($edit)){
        //user_save($account,$edit);
        user_save($u,$edit);
    }
    red_compartir_grupo_users_facilitators_row_save($u,$u_in);
}
function red_compartir_grupo_get_profile_fields_array(){
    $result=array();
    $res=db_query('SELECT * FROM {profile_fields} WHERE 1');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
//intelsat-2016
function red_compartir_grupo_get_users_facilitators_enviar_row($uid){
    if(hontza_canal_rss_is_facilitador_activado()){
        if(db_table_exists('users_facilitators')){        
            $result=facilitador_get_users_facilitators_row($uid);
            return $result;
        }    
    }
    $my_result=new stdClass();
    return $my_result;
}
//intelsat-2016
function red_compartir_grupo_users_facilitators_row_save($u,$u_in){
    if(hontza_is_servidor_red_alerta()){
        if(hontza_canal_rss_is_facilitador_activado()){
            facilitador_servidor_central_users_facilitators_row_save($u,$u_in);
        }    
    }    
}
//intelsat-2016
function red_compartir_grupo_hoja_access_denied(){
    /*$is_grupo_publico=1;
    $grupo_nid=arg(2);
    $grupo_node=node_load($grupo_nid);
    if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
        $is_grupo_publico=0;
        $is_publico_colaborativo=1;
        $is_grupo_publico=estrategia_is_grupo_publico($grupo_node,'',$is_publico_colaborativo);
    }    
    if($is_grupo_publico){
        drupal_access_denied();
        exit();
    }*/
}
//intelsat-2016
function red_compartir_grupo_no_compartir_grupo_hoja_access_denied(){
    red_compartir_grupo_hoja_access_denied();
}