<?php
function custom_menu_red_grupos_subdominios_callback(){
   custom_menu_red_grupos_subdominios();
   return date('Y-m-d H:i'); 
}
function  custom_menu_red_grupos_subdominios(){
   /*if(hontza_is_sareko_id('ROOT')){
       return;
   }*/
   /*//intelsat-2016
   if(defined('_IS_CUSTOM_MENU_RED_CACHE') && _IS_CUSTOM_MENU_RED_CACHE!=1){
       return;
   }*/
   if(custom_menu_red_is_activado()){
        custom_menu_red_delete_all_custom_menu_red();
        $users_array=hontza_solr_get_users();
        if(!empty($users_array)){
            foreach($users_array as $i=>$row){
                if(isset($row->uid) && !empty($row->uid)){
                    //intelsat-2016
                    if(is_user_demo_caducado($row->uid)){
                        continue;
                    }
                    custom_menu_red_save_custom_menu_red($row);
                }    
            }
        }
   }  
}
function custom_menu_red_is_activado(){
   if(db_table_exists('custom_menu_red')){
       if(hontza_is_sareko_id_red()){
        return 1;
       } 
   }
   return 0;
}
function custom_menu_red_save_custom_menu_red($user_row){
   global $user;
   //intelsat-2016
   if(is_user_demo_caducado($user_row->uid)){
       return;
   }
   $original_user = $user;
   $old_state = session_save_session();
   session_save_session(FALSE);
   $user = user_load($user_row->uid);
   $grupos_array=custom_menu_get_grupos_array($rows,1);
   $custom_menu_red_row=custom_menu_red_get_custom_menu_red_row($user_row->uid);
   $grupos_array_value=custom_menu_red_encode($grupos_array);
   $rows_value=custom_menu_red_encode($rows);
   $mis_grupos_form_value='';
   if(isset($user_row->uid) && !empty($user_row->uid)){
    $mis_grupos_form_value=custom_menu_red_encode(mis_grupos_form(0));
   }
   $changed=time();
   if(isset($custom_menu_red_row->id) && !empty($custom_menu_red_row->id)){
       db_query('UPDATE {custom_menu_red} SET uid=%d,grupos_array="%s",rows_value="%s",mis_grupos_form_value="%s",changed=%d WHERE id=%d',$user_row->uid,$grupos_array_value,$rows_value,$mis_grupos_form_value,$changed,$custom_menu_red_row->id);
   }else{
       db_query('INSERT INTO {custom_menu_red}(uid,grupos_array,rows_value,mis_grupos_form_value,changed) VALUES(%d,"%s","%s","%s",%d)',$user_row->uid,$grupos_array_value,$rows_value,$mis_grupos_form_value,$changed);
   }
   $user = $original_user;
   session_save_session($old_state);   
}
function custom_menu_red_get_custom_menu_red_row($uid){
   $res=db_query('SELECT * FROM {custom_menu_red} WHERE uid=%d',$uid);
   while($row=db_fetch_object($res)){
       return $row;
   }
   $my_result=new stdClass();
   return $my_result;
}
function custom_menu_red_get_max($max=14,$is_show=0){
   $result=$max;
   /*if($is_show){
       return $result;
   }*/
   if(custom_menu_red_is_activado()){
       $result=10000;
   }
   return $result;
}
function custom_menu_red_get_grupos_db_array(&$rows,$is_show=0){
    global $user;
    $rows=array();
    if(custom_menu_red_is_activado()){
        //if(custom_menu_red_is_db_request()){
            $custom_menu_red_row=custom_menu_red_get_custom_menu_red_row($user->uid);
            if(isset($custom_menu_red_row->id) && !empty($custom_menu_red_row->id)){
                $grupos_array=custom_menu_red_decode($custom_menu_red_row->grupos_array);
                if(!empty($grupos_array)){
                    /*if($is_show){
                        $max=custom_menu_red_get_max(14,$is_show);
                        $max=$max+1;
                        $grupos_array=array_slice($grupos_array,0,$max);
                    }*/
                    //echo print_r($grupos_array);
                    $rows=custom_menu_red_decode($custom_menu_red_row->rows_value);
                    //echo print_r($rows,1);exit();
                    return $grupos_array;
                }
            }
        //}    
    }
    $grupos_array=custom_menu_get_grupos_array($rows);
    return $grupos_array;
}
function custom_menu_red_encode($rows){
    $result=serialize($rows);
    //$result=base64_encode(serialize($rows));
    //$result=base64_encode(json_encode($rows));    
    return $result;        
}
function custom_menu_red_decode($rows){
    $result=unserialize($rows);
    //$result=unserialize(base64_decode($rows));            
    //$result=json_decode(base64_decode($rows));    
    return $result;        
}
function custom_menu_red_mis_grupos_form_db(){
    global $user;
    $form=array();
    $custom_menu_red_row=custom_menu_red_get_custom_menu_red_row($user->uid);
    if(isset($custom_menu_red_row->id) && !empty($custom_menu_red_row->id)){
        $form=custom_menu_red_decode($custom_menu_red_row->mis_grupos_form_value);
        $form=custom_menu_red_fix_mis_grupos_order_link($form);        
    }
    return $form;
}
function custom_menu_red_delete_all_custom_menu_red(){
    db_query('DELETE FROM {custom_menu_red} WHERE 1');
}
function custom_menu_red_is_red_grupos_subdominios_script(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='custom_menu'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='red_grupos_subdominios'){
            return 1;
        }
    }
    return 0;
}
function custom_menu_red_update_custom_menu_red_on_add_remove_users($accounts,$form,&$form_state,$og_my_users){
    global $user;
    if(custom_menu_red_is_activado()){
        $user_array=custom_menu_red_user_array_merge($accounts,$og_my_users);
        if(!empty($user_array)){
            foreach($user_array as $i=>$my_user){
                if($user->uid!=$my_user->uid){
                    custom_menu_red_rebuild_custom_menu_red_row($my_user);
                }    
            }
        }
    }
}
function custom_menu_red_fix_mis_grupos_order_link($form){
    $result=$form;
    $html=$result['grupos']['lista']['#value'];
    $sep='<thead>';
    $pos=strpos($html,$sep);
    if($pos===FALSE){
        return $result;
    }else{
        $pre=substr($html,0,$pos);
        $s=substr($html,$pos+strlen($sep));
        $sep_order='</thead>';
        $pos_order=strpos($s,$sep_order);
        if($pos_order===FALSE){
            return $result;
        }else{
            $s_order=substr($s,0,$pos_order);
            $html_order=custom_menu_red_fix_order_link($s_order);
            $table=substr($s,$pos_order);
            //$table=custom_menu_red_ordenar_mis_grupos($table);
            $html_fixed=$pre.$sep.$html_order.$table;
            $result['grupos']['lista']['#value']=$html_fixed;
        }
    }
    return $result;
}
function custom_menu_red_fix_order_link($s_order){
    $html=array();
    $sep='href="';
    $my_array=explode($sep,$s_order);
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if($i>0){
                $pos=strpos($value,'"');
                if($pos===FALSE){
                    $html[]=$value;
                    continue;
                }else{
                    $link=substr($value,0,$pos);
                    $sep_sort='?sort';
                    $pos_sort=strpos($link,$sep_sort);
                    if($pos_sort===FALSE){
                        $html[]=$value;
                        continue;
                    }else{
                        $s=substr($link,$pos_sort).'&is_db=0';
                        $html[]='mis-grupos'.$s.substr($value,$pos);
                    }
                }
            }else{
                $html[]=$value;
            }
        }
    }
    return implode($sep,$html);
}
function custom_menu_red_ordenar_mis_grupos($html_in){
    $html=array();
    $sep='target="_blank">';
    $my_array=explode($sep,$html_in);
    $result=array();
    $num=count($my_array);
    foreach($my_array as $i=>$value){
        if($i>0){
            $sep_a='</a>';
            $pos=strpos($value,$sep_a);
            if($pos===FALSE){
                return $html_in;
            }else{
                //$result[$i-1]['title']=substr($value,0,$pos);
                /*if($i==($num-1)){
                    $pos_tbody=strpos($value,'</tbody>');
                    if($pos_tbody===FALSE){
                        return $html_in;
                    }else{
                        $result[$i-1]['value']=substr($value,0,$pos_tbody);
                    }
                }else{
                    $result[$i-1]['value']=$value;
                }*/
                $result[]=substr($value,0,$pos);
            }            
        }else{
            $pre=$value;
        }            
    }
    echo print_r($result,1);
    exit();
    return implode($sep,$html);
}
function custom_menu_red_is_db_request(){
    if(isset($_REQUEST['is_db']) && empty($_REQUEST['is_db'])){
        return 0;
    }
    return 1;
}
function custom_menu_red_unset_grupos_locales_como_remotos($result_in){
    $result=array();
    if(!empty($result_in)){
        foreach($result_in as $i=>$row){
            if(custom_menu_red_is_grupo_local($row)){
                continue;
            }
            $result[]=$row;
        }
    }
    return $result;
}
function custom_menu_red_is_grupo_local($row){
    if(_SAREKO_ID=='ROOT'){
        if(isset($row->subdominio)){
            if($row->subdominio=='online.hontza.es'){
                return 1;
            }
        }    
    }    
    return 0;
}
function custom_menu_red_user_array_merge($accounts,$og_my_users){
    $result=$accounts;
    if(!empty($og_my_users)){
        foreach($og_my_users as $i=>$my_user){
            if(!custom_menu_red_in_user_array($my_user->uid,$result)){
                $result[]=$my_user;
            }
        }
    }    
    return $result;
}
function custom_menu_red_in_user_array($uid,$user_array){
    if(!empty($result)){
        foreach($result as $i=>$my_user){
            if($my_user->uid==$uid){
                return 1;
            }
        }
    }
    return 0;    
}
function custom_menu_red_delete_custom_menu_red_row($uid){
    db_query('DELETE FROM {custom_menu_red} WHERE uid=%d',$uid);
}
function custom_menu_red_rebuild_custom_menu_red_row($my_user){
    custom_menu_red_delete_custom_menu_red_row($my_user->uid);
    custom_menu_red_save_custom_menu_red($my_user);
}