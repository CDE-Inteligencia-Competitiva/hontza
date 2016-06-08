<?php
function hound_enlazar_inc_is_activado(){
    if(defined('_IS_HOUND_ENLAZAR') && _IS_HOUND_ENLAZAR==1){
        return 1;
    }
    return 0;
}
function hound_enlazar_inc_hound_key_save($form_state){
    if(hound_enlazar_inc_is_activado()){
        if(isset($form_state['values']['hound_key'])){
            variable_set('hound_key',$form_state['values']['hound_key']);
        }
    }
}
function hound_enlazar_inc_add_create_edit_form_field(&$form){
    if(hound_enlazar_inc_is_activado()){
        $node=my_get_node();
        if(isset($node->nid) && !empty($node->nid)){
            $form['hound_editing_edit_btn']=array(
                '#type'=>'button',
                '#default_value'=>t('Edit Hound'),
            );
            hound_enlazar_inc_add_edit_js($node);
        }else{
            $form['fuentes']['hound1_create_btn']=array(
                '#type'=>'button',
                '#default_value'=>t('Create Hound'),
            );
            hound_enlazar_inc_add_create_js();
        }
    }
}
function hound_enlazar_inc_add_create_js(){
    $url=hound_enlazar_inc_get_create_url();
    $js='var hound_enlazar_url="'.$url.'";
        $(document).ready(function(){
        $("#edit-hound1-create-btn").click(function(){
            window.open(hound_enlazar_url);
            return false;
        });            
    });';        
    drupal_add_js($js,'inline');
}
function hound_enlazar_inc_get_base_url($is_http=1){
    $url=hound_enlazar_inc_define_hound_url();    
    return $url;
}
function hound_enlazar_inc_define_hound_url($is_http=1){
    $url='hound.hontza.es/blog/public';
    if($is_http){
        $url='http://'.$url;
    }
    return $url;
}
function hound_enlazar_inc_get_create_url(){
    $url=hound_enlazar_inc_define_hound_url();
    $url.='/channels/crearCanal/'.hound_enlazar_inc_get_key();
    return $url;
}
function hound_enlazar_inc_get_key(){
    $hound_key=variable_get('hound_key','');
    return $hound_key;
}
function hound_enlazar_inc_add_edit_js($canal){
    $url=hound_enlazar_inc_get_edit_url($canal);
    $js='var hound_enlazar_url="'.$url.'";
        $(document).ready(function(){
        $("#edit-hound-editing-edit-btn").click(function(){
            window.open(hound_enlazar_url);
            return false;
        });            
    });';        
    drupal_add_js($js,'inline');
}
function hound_enlazar_inc_get_edit_url($canal){
    $url=hound_enlazar_inc_define_hound_url();
    $hound_id=hound_enlazar_inc_get_hound_id($canal);
    $url.='/channels/editarCanal/'.$hound_id.'/'.hound_enlazar_inc_get_key();
    return $url;
}
function hound_enlazar_inc_delete_canal_hound($canal){
    hound_enlazar_inc_delete_canal_hound_servidor($canal);
    hound_delete_canal_hound_parametros($canal);    
}
function hound_enlazar_inc_delete_canal_hound_servidor($canal){
    if(hound_enlazar_inc_is_activado()){
        $url=hound_enlazar_inc_get_delete_url($canal);
        //print $url;exit();
        if(!empty($url)){
            file_get_contents($url);
        }    
    }    
}
function hound_enlazar_inc_get_delete_url($canal){
    $url=hound_enlazar_inc_define_hound_url();
    $hound_id=hound_enlazar_inc_get_hound_id($canal);
    if(empty($hound_id)){
        return '';
    }
    $url.='/channels/borrarCanal/'.$hound_id.'/'.hound_enlazar_inc_get_key();
    drupal_set_message($url);
    return $url;
}
function hound_enlazar_inc_get_hound_id($canal){
    $hound_id=hound_get_hound_id_by_nid('',$canal);
    $hound_id=urlencode($hound_id);
    return $hound_id; 
}
function hound_enlazar_inc_define_editing_parametros_html($canal_hound_row){
    $html=array();
    $content=hound_get_canal_hound_field($canal_hound_row,'content');
    $parametros=json_decode(base64_decode($content));
    //if(hound_enlazar_inc_is_activado()){
        //$html[]='<input type="hidden" name="content_json_edit" value="'.base64_encode($content).'"/>';
        $html[]='<input type="hidden" name="content_json_edit" value="'.$content.'"/>';
    //}
    if(!empty($parametros)){
        $html[]='<table>';
        $html[]='<tr>';
        $html[]='<th>'.t('Hound').'</th>';
        $html[]='<th>'.t('Parameter').'</th>';
        $html[]='<th>'.t('Value').'</th>';
        $html[]='</tr>';
        foreach($parametros as $key=>$hound_array){
            if(empty($hound_array)){
                if($canal_hound_row->is_empty){
                    $html[]='<tr><td><div style="padding:10px;display:none;">';
                }else{
                    $html[]='</tr><td><div style="padding:10px;">';
                } 
                $html[]='<label><b>'.$key.'</b></label>';                
                $html[]='</div></td>';
                $html[]='<td></td>';
                 $html[]='<td></td>';
                $html[]='<tr>';
            }else{
                foreach($hound_array as $i=>$row){
                    foreach($row as $name=>$value){
                        if($canal_hound_row->is_empty){
                            $html[]='<tr style="display:none;"><td><div style="padding:10px;display:none;">';
                        }else{
                            $html[]='<tr><td><div style="padding:10px;">';
                        }
                        $html[]='<label><b>'.$key.'</b></label></td>'; 
                        //$html[]='<label><b>'.hound_api_param_key_label($name).' ('.$key.')</b>:</label></td>';
                        $html[]='<td><label><b>'.hound_api_param_key_label($name).'</label></td>';
                        if($name=='hound_search'){
                            $html[]='<td><textarea id="hound1_f_'.$key.'_'.$name.'" name="hound1_f['.$key.']['.$name.']" rows="5" cols="60" readonly="readonly">'.$value.'</textarea></td>';
                        }else{
                            $html[]='<td><input type="text" id="hound1_f_'.$key.'_'.$name.'" name="hound1_f['.$key.']['.$name.']" value="'.$value.'" readonly="readonly"/></td>';
                        }
                        $html[]='</div></tr>';
                    }
                }    
            }    
        }
        $html[]='</table>';
    }
    return implode('',$html);
}
function hound_enlazar_inc_is_hound_add_canal_hound_parametros($is_edit=0){
    if($is_edit){    
        if(isset($_REQUEST['hound1_f'])){
            return 1;
        }
    }else{    
        if(isset($_REQUEST['hound1_f']) && !empty($_REQUEST['hound1_f'])){
            return 1;
        }
    }    
    if(hound_enlazar_inc_is_activado()){
        //if($is_edit){  
            if(isset($_REQUEST['content_json']) && !empty($_REQUEST['content_json'])){
                return 1;
            }
            if(isset($_REQUEST['content_json_edit']) && !empty($_REQUEST['content_json_edit'])){
                return 1;
            }
        //}    
    }
    return 0;
}
function hound_enlazar_inc_add_add_canal_hound_parametros(&$row){
    if(hound_enlazar_inc_is_activado()){
        if(isset($_REQUEST['content_json'])){
                $row->content=$_REQUEST['content_json'];
            }else if(isset($_REQUEST['content_json_edit'])){
                $row->content=$_REQUEST['content_json_edit'];
            }
    }
}      
function hound_enlazar_inc_editing_form_alter(&$form){
    $form['hound_id_editing']['#prefix']='<div style="display:none">';
    $form['hound_id_editing']['#suffix']='</div>';
}
function hound_enlazar_inc_get_canal_hound_parametros_row_hound_title(&$canal){
    if(isset($_REQUEST['hound_title_readonly']) && !empty($_REQUEST['hound_title_readonly'])){
        $canal->canal_hound_parametros->hound_title=$_REQUEST['hound_title_readonly'];
    }
}        
function hound_enlazar_inc_get_canal_rss_url($hound_id){
    if(hound_enlazar_inc_is_activado()){
        $url=hound_enlazar_inc_define_hound_url().'/channels/getRSS/'.$hound_id.'/'.hound_enlazar_inc_get_key().'?from=0';
        //print $url;exit();
    }else{
        $url=hound_base_url().'/index.php/channels/getRss/id/'.urlencode($hound_id).'/from/0';
    }
    return $url;
}
function hound_enlazar_inc_is_hound_rss_by_data($data,$yql_obj){
    if(!empty($data)){
        $url_values=array_values($data);
        $num=count($url_values);
        if($num==1){
            $url=$url_values[0];                
            if(hound_enlazar_inc_is_activado()){
                $find=hontza_define_hound_url();               
            }else{
                $find='/hound/houndRss.php';            
            }
            $pos=strpos($url,$find);
                if($pos===FALSE){
                    return 0;
                }else{
                    //return 1;
                    return hontza_is_hound_by_yql_obj($yql_obj);
                }
        }
    }
    return 0;
}
function hound_enlazar_inc_set_hound_url_from_time($url_in,$last_import_time){
    $url=$url_in;
    if(hound_enlazar_inc_is_activado()){
        $url=str_replace('from=','from='.$last_import_time,$url);
    }else{
        $pos=strpos($url_in,'/from/');
        if($pos===FALSE){
            return $url;
            /*$parse=parse_url($url);
            parse_str($parse['query'],$param_array);
            if(!empty($param_array)){
               $url=hontza_define_hound_url();
               $param_array['from']=$last_import_time; 
               $url.='?'.http_build_query($param_array,'','&');       
            }
            return $url;*/
        }else{
            $s=substr($url,0,$pos);
            $url=$s.'/from/'.$last_import_time;
        }
    }
    return $url;
}    