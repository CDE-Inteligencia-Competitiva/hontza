<?php
function red_canales_todas_callback(){
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();    
    $output=red_canales_todas_header().drupal_get_form('red_canales_todas_form');
    return $output;
}
function red_canales_todas_header(){
    $html=array();
    $html[]=red_compartir_navegar_menu();
    $html[]=red_canales_todas_filtro();
    return implode('',$html);
}
function red_canales_todas_filtro(){
    my_add_buscar_js();
    return drupal_get_form('red_canales_todas_filtro_form');
}
function red_canales_todas_form(){
    drupal_set_title(t('Share Channels'));
    boletin_report_no_group_selected_denied();
    
    $url_volver='red/fuentes-pipes/todas';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_volver=$_REQUEST['destination'];
    }
    $form=array();
    $canal_list=red_canales_get_todas_list();
    //intelsat-2015
    $prefix='<div class="n-opciones-item">';
    $link_return=red_compartir_copiar_fuentes_servidor_volver_link(0,$url_volver);        
    $form_return_btn=array(
        '#value'=>$link_return,
        '#prefix'=>$prefix,                             
    );
    //            
        if(!empty($canal_list)){
             $form['upload_canal_table']=array(
                 '#value'=>red_canales_get_todas_table_html($canal_list),
             );
             if(hontza_is_mostrar_recursos_compartidos_del_servidor_red()){
                //intelsat-2015
                //$form['my_submit']=array('#type'=>'submit','#name'=>'my_submit','#value'=>t('Share'));
                $form['return_btn']=$form_return_btn;
                $form['my_submit']=array(
                    //intelsat-2015
                    //'#type'=>'submit',
                    '#type'=>'image_button',
                    '#name'=>'my_submit',
                    '#value'=>t('Share'),
                    //'#prefix'=>'<div style="float:left;">',                 
                    '#suffix'=>'</div>',
                    '#src'=>red_compartir_fuente_descargar_path('compartir_fuente'),
                    );
                // 
             }   
        }else{
            $form['my_msg']=array(
                '#value'=>'<p>'.t('There are no contents').'</p>',
            );
            //intelsat-2015
            $form['return_btn']=$form_return_btn;
            $form['return_btn']['#suffix']='</div>';
            //
        }       
    return $form;
}
function red_canales_get_todas_list(){
    global $user;
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='node.type in ("canal_de_supercanal", "canal_de_yql", "canal_busqueda")';
    if(!is_super_admin()){
        $where[]='users.uid='.$user->uid;
    }
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid = '.$my_grupo->nid;
    }else{
        return $result;
    }   
    //
    $filter_fields=red_canales_todas_filter_fields();
    if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=red_canales_todas_get_filter_value($f);
           if(!empty($v)){
                switch($f){
                    case 'canal_title':
                        $where[]="node.title LIKE '%%".$v."%%'";
                        break;
                    case 'region':                        
                        $v=red_regiones_get_region_decode_value($v);                        
                        if(!empty($v)){
                            $where[]='content_field_canal_region.field_canal_region_value LIKE "%%#'.$v.'#%%"';
                        }  
                        break;
                    case 'username':
                        $where[]="users.name='".$v."'";
                        break;
                    default:
                        break;
                }
           } 
       }
   }
    //    
    $sort='ASC';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }   
    //    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title, 
    node.language AS node_language, 
    node.type AS node_type,
    node_data_field_fuente_canal.field_fuente_canal_value AS node_data_field_fuente_canal_field_fuente_canal_value, 
    node.vid AS node_vid, 
    og_ancestry.nid AS og_ancestry_nid, 
    node.created AS node_created, 
    node.status AS node_status, 
    node.uid AS node_uid, 
    node_revisions.format AS node_revisions_format, 
    node.changed AS node_changed,
    content_field_canal_region.field_canal_region_value
    FROM {node} node 
    INNER JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
    LEFT JOIN {content_field_fuente_canal} node_data_field_fuente_canal ON node.vid = node_data_field_fuente_canal.vid 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid
    LEFT JOIN {content_field_canal_region} content_field_canal_region ON node.vid=content_field_canal_region.vid
    WHERE '.implode(' AND ',$where).'
    ORDER BY node_title '.$sort;
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }    
    return $result;        
}
function red_canales_todas_filter_fields(){
    $filter_fields=array('canal_title','canal_description','region','canal_rating','status','username');
    return $filter_fields;
}
function red_canales_get_todas_table_html($fuente_list_in){
    $fuente_list=$fuente_list_in;
    $sort='';
    $is_numeric=0;
    $field='';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }    
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Region')){
            $field='canal_region';
        }else if($order==t('Rating')){
            $is_numeric=1;
            $field='canal_rating';            
        }else if($order==t('User')){
            $field='username';            
        }else if($order==t('Status')){
            $field='canal_status';            
        }             
    }
    //
    $my_limit=25;
    //
    $headers=array();
    $headers[0]='';
    $headers[1]=array('data'=>t('Description'),'field'=>'canal_title');
    $headers[2]=array('data'=>t('Region'),'field'=>'canal_region');
    $headers[3]=array('data'=>t('Rating'),'field'=>'canal_rating');
    $headers[4]=array('data'=>t('Status'),'field'=>'canal_status');
    $headers[5]=array('data'=>t('User'),'field'=>'username');
    $is_mostrar=hontza_is_mostrar_recursos_compartidos_del_servidor_red();
    //                
    if(!empty($fuente_list)){
        foreach($fuente_list as $i=>$r){
                $node=node_load($r->nid);
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=array();
                    $rows[$kont][0]='<div style="white-space:nowrap;">';
                    if($is_mostrar){
                        $rows[$kont][0].='<input type="checkbox" id="upload_canal_'.$r->nid.'" name="upload_canal['.$r->nid.']" value="1"/>&nbsp;';
                    }
                    //intelsat-2015
                    //se ha coemtado esto
                    //$rows[$kont][0].=l(my_get_icono_action('viewmag', t('View')),'node/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination()));
                    //
                    $rows[$kont][0].='</div>';
                    //intelsat-2015
                    //$canal_description=red_canal_get_description($node,1,1,'node/'.$r->nid);
                    $canal_description=red_canal_get_description($node,1,1,'red_compartir/red_compartir_view_canal_local/'.$r->nid);
                    //
                    $rows[$kont][1]=$canal_description;
                    $region='';
                    if($r->field_canal_region_value){
                        $region=  red_regiones_get_region_decode_value($r->field_canal_region_value);
                    }
                    $rows[$kont][2]=$region;
                    if(!isset($node->votingapi_cache_row)){
                        $node->votingapi_cache_row=hontza_get_avg_rating($r->nid);
                    }                    
                    $rows[$kont][3]=red_canal_get_avg_rating_by_node($node);
                    $info_status=red_canal_get_info_status($node);                    
                    $rows[$kont][4]=$info_status['label'];
                    $username=hontza_get_username($node->uid);
                    $rows[$kont][5]=l($username,'user/'.$node->uid);
                    $rows[$kont]['canal_description']=$canal_description;
                    $rows[$kont]['canal_rating']=hontza_get_node_puntuacion_media_para_txt($r->nid,1);
                    $rows[$kont]['canal_region']=strtolower($region);
                    $rows[$kont]['canal_status']=strtolower($info_status['label']);
                    $rows[$kont]['username']=strtolower($username);
                    $rows[$kont]['node']=$node;
                    $rows[$kont]['status']=$info_status['value'];
                    $kont++;
                }    
        }        
    }
    
    $filtro=array();
    if(isset($_SESSION['red_canales_todas']['filter'])){
        $filtro=$_SESSION['red_canales_todas']['filter'];
    }
    
    $rows=red_canales_todas_repasar_filtro($rows,$filtro);
    
    if(!empty($field)){
        $rows=array_ordenatu($rows,$field,$sort,$is_numeric);
    }
    
    
    $rows=hontza_unset_array($rows,array('canal_description','canal_rating','canal_region','canal_status','username','node','status'));
    
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function red_canal_get_description($node,$is_with_body=0,$is_link=0,$url=''){
    $len=150;
    $resumen=red_canal_get_resumen($node,$len,$is_with_body,$is_link,$url);
    //intelsat-2015
    $title=$node->title;
    if($is_link){
        $title=l($title,$url,array('query'=>drupal_get_destination()));
    }
    $html[]='<b>'.my_get_icono_action('canal20',t('Channel')).'&nbsp;'.$title.'</b>';
    //
    $html[]='<p><i>'.$resumen.'</i></p>';
    return implode('',$html);
}
function red_canal_get_resumen($node,$len=150,$is_with_body=0,$is_link=0,$url=''){
        //$value=$node->body;
        $value=hontza_content_full_text($node,1);
        if(empty($value) && $is_with_body){
            $value=$node->body;
        }
        $result=strip_tags($value);
        if(strlen($result)>$len){
            $result=drupal_substr($result, 0, $len); 
            $result.='...';
            return $result;
        }
    return $result;
}
function red_canal_get_avg_rating_by_node($node,$is_value=0){   
    if(isset($node->votingapi_cache_row)){
        if(isset($node->votingapi_cache_row->value)){
            if($is_value){
                return $node->votingapi_cache_row->value;
            }else{
                return red_canal_create_stars_view($node->votingapi_cache_row->value);
            }    
        }
    }
    if($is_value){
        return 0;
    }else{
        return '';
    }
}
function red_canal_create_stars_view($value){
    $v=(int) ($value/20);
    return my_create_stars_view($v);
}
function red_canal_get_info_status($node){
    $info=array();
    $html=array();
    $value_array=array();
    if(red_canal_is_descargados($node)){
        $html[]=t('..Downloaded');
        $value_array[]='downloaded';
    }
    if(red_canal_is_uploaded($node)){
        $html[]=t('..Shared');
        $value_array[]='uploaded';
    }
    if(empty($html)){
        $html[]=t('Local');
        $value_array[]='local';
    }    
    $info['label']=implode('<BR>',$html);
    $info['value']=$value_array;
    return $info;
}
function red_canal_is_descargados($node){
    $red_compartir_canal_array=red_canal_get_red_compartir_canal_descargados_array($node);
    if(count($red_compartir_canal_array)>0){
        return 1;
    }
    return 0;
}
function red_canal_get_red_compartir_canal_descargados_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_canal_descargados} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_canal_is_uploaded($node){
    $red_compartir_canal_array=red_canal_get_red_compartir_canal_array($node);
    if(count($red_compartir_canal_array)>0){
        return 1;
    }
    return 0;
}
function red_canal_get_red_compartir_canal_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_canal} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_canales_todas_filtro_form(){
    $form=array();
    $fs_title=t('Search');
    if(!red_canales_todas_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //    
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['canal_title']=
        array('#type'=>'textfield',
        '#title'=>t('Title'),
        "#default_value"=>red_canales_todas_get_filter_value('canal_title'));
    $form['file_buscar_fs']['canal_description']=
        array('#type'=>'textfield',
        '#title'=>t('Description'),
        "#default_value"=>red_canales_todas_get_filter_value('canal_description'));
    /*$form['file_buscar_fs']['region']=
        array('#type'=>'textfield',
        '#title'=>t('Region'),
        "#default_value"=>red_canales_todas_get_filter_value('region'));
    */
    /*$form['file_buscar_fs']['region']=
        array('#type'=>'select',
        '#title'=>t('Region'),
        '#options'=>red_regiones_define_regiones_options(1),    
        "#default_value"=>red_canales_todas_get_filter_value('region'));*/
    //$form['file_buscar_fs']['region_fs']=red_regiones_define_fieldset('', red_canales_todas_get_filter_value('region'));
    
    $form['file_buscar_fs']['canal_rating']=
        array('#type'=>'select',
        '#title'=>t('Rating'),
        '#options'=>red_canales_define_average_options(),      
        "#default_value"=>red_canales_todas_get_filter_value('canal_rating'));
     $form['file_buscar_fs']['status']=
        array('#type'=>'select',
        '#title'=>t('Status'),
        '#options'=>red_canales_todas_define_status_options(),    
        "#default_value"=>red_canales_todas_get_filter_value('status'));
     
     $form['file_buscar_fs']['username']= array(
	  '#title' => t('User'),
	  '#type' => 'textfield',
	  '#maxlength' => 60,
	  '#autocomplete_path' => 'user/autocomplete',
	  "#default_value"=>red_canales_todas_get_filter_value('username'));      
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div id="red_red_fuentes_filtro_botones">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form;
}
function red_canales_todas_is_filter_activated(){
    $filter_fields=red_canales_todas_filter_fields();
    if(!empty($filter_fields)){
        foreach($filter_fields as $i=>$f){
            //$v=red_compartir_fuentes_copy_get_filter_value($f);
            $v=red_canales_todas_get_filter_value($f);
            if(!empty($v)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_canales_todas_get_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'red_canales_todas');
}
function red_canales_todas_define_status_options(){
    return red_fuentes_pipes_todas_define_status_options();
}
function red_canales_todas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['red_canales_todas']['filter']) && !empty($_SESSION['red_canales_todas']['filter'])){
                unset($_SESSION['red_canales_todas']['filter']);
            }
        }else{
            $_SESSION['red_canales_todas']['filter']=array();
            $fields=red_canales_todas_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if($f=='region'){
                        $v=red_regiones_get_region_post_value();                        
                    }else{
                        $v=$form_state['values'][$f];
                    }
                    if(!empty($v)){
                        $_SESSION['red_canales_todas']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function red_canales_define_average_options(){
    $result=array();
    $result[0]='';
    $result[20]='1';
    $result[40]='2';
    $result[60]='3';
    $result[80]='4';
    $result[100]='5';
    return $result;
}
function red_canales_todas_repasar_filtro($result_in,$filtro){
    $result=array();
    $fields=array('canal_description','canal_rating','status');
    if(!empty($result_in)){
        $kont=0;
        foreach($result_in as $i=>$row){
            $ok=1;
            foreach($fields as $k=>$f){
                if(isset($filtro[$f]) && !empty($filtro[$f])){
                    if($f=='canal_rating'){    
                        $v=0;
                        if(isset($row['node']->votingapi_cache_row) && isset($row['node']->votingapi_cache_row->value)){
                            $v=$row['node']->votingapi_cache_row->value;
                            if(empty($v)){
                                $v=0;
                            }
                            if($v<$filtro[$f]){
                                $ok=0;
                                break;
                            }
                        }else{
                            $ok=0;
                            break;
                        }                   
                    }else if($f=='canal_description'){
                            $v=$filtro[$f];
                            if(!red_canales_todas_is_description_like($v,$row['node'])){
                                $ok=0;
                                break;
                            }
                    }else if($f=='status'){
                            $v=$filtro[$f];
                            if(!in_array($v,$row['status'])){
                                $ok=0;
                                break;
                            }
                    }               
                }
            }
            if($ok){
                $result[$kont]=$row;
                $kont++;
            }
        }    
    }
    return $result;
}
function red_canales_todas_is_description_like($v,$node){
    return red_fuentes_pipes_todas_is_description_like($v,$node);
}
function red_canales_todas_form_submit(&$form, &$form_state){
    global $user;
    if(isset($_SESSION['upload_canal_array'])){
        unset($_SESSION['upload_canal_array']);
    }
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'my_submit')==0){
            if(isset($_POST['upload_canal']) && !empty($_POST['upload_canal'])){
                $upload_canal_array=array_keys($_POST['upload_canal']);
                $_SESSION['upload_canal_array']['user_'.$user->uid]=$upload_canal_array;
                drupal_goto('red_compartir/compartir_canal_hoja_multiple');
            }
        }
    } 
}
function red_canal_is_compartir_canal_servidor($with_publica=0){
    $param0=arg(0);
    if(!empty($param0) && $param0=='red_compartir'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='red_compartir_view_canal_servidor'){
            $param2=arg(2);
            if(!empty($param2)){            
                return 1;
            }    
        }
     }else if(!empty($param0) && $param0=='red_publica'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='canal'){
            $param2=arg(2);
            if(!empty($param2) && is_numeric($param2)){
               $param3=arg(3);
                if(!empty($param3) && $param3=='view'){
                    return 1;
                } 
            }    
        }
    }
    return 0;
}
//intelsat-2015
function red_canal_is_actions_canal_access($node){
    global $user;
    if(is_super_admin()){
        return 1;
    }
    //if(red_crear_usuario_is_rol_administrador_creador_grupo()){
        if($user->uid==$node->uid){
            return 1;
        }
    //}
    return 0;
}
//intelsat-2015
function red_canal_feeds_access($action, $param){
    global $user;
    if(red_canal_is_delete_items($param)){
        if($user->uid==$param->uid){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function red_canal_is_delete_items($param){
    if(in_array($param->type,array('canal_de_supercanal','canal_de_yql'))){
        $param0=arg(0);
        if(!empty($param0) && $param0=='node'){
            $param1=arg(1);
            if(!empty($param1) && is_numeric($param1)){
                $param2=arg(2);
                if(!empty($param2) && $param2=='delete-items'){
                    return 1;
                }
            }
        }
    }
    return 0;
}
//intelsat-2016
function red_canal_is_canal_opencalais_activado(){
    if(defined('_IS_CANAL_OPENCALAIS') && _IS_CANAL_OPENCALAIS==1){
        return 1;
    }
    return 0;
}
//intelsat-2016
function red_canal_get_url_by_len($url,$is_len){
    $result=$url;
    if($is_len){
        $max=250;
        if(strlen($result)>$max){
            $result=substr($result,0,$max).'...';
        }    
    }
    return $result;
}
//intelsat-2106
function red_canal_get_porcentajes_where_fecha($where_in,$is_porcentajes){
    $result=$where_in;
    if($is_porcentajes){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $field_delete_rejected_news_time=$my_grupo->field_delete_rejected_news_time[0]['value'];
            $field_delete_unread_news_time=$my_grupo->field_delete_unread_news_time[0]['value'];
            $meses=$field_delete_unread_news_time;
            if($field_delete_rejected_news_time<$field_delete_unread_news_time){
                $meses=$field_delete_rejected_news_time;
            }
            $fecha=date('Y-m-d H:i:s');
            $konp_time=strtotime($fecha." -".$meses." month" );
            //print date('Y-m-d H:i:s',$konp_time);
            $result[]='n.created>='.$konp_time;
        }
    }    
    return $result;
}
function red_canal_add_is_canal_opencalais_description(&$form,$field='is_canal_opencalais'){
    red_canal_add_apply_alchemy_description($form,$field,'opencalais');    
}
function red_canal_add_apply_alchemy_description(&$form,$field='apply_alchemy',$type='alchemy'){
    $api_key='';
    $field_key='';
    if($type=='alchemy'){
        $field_key='field_alchemy_key';
        $description='To activate please fill the Alchemy Api Key in the group settings page';
    }else if($type=='opencalais'){
        $field_key='field_opencalais_key';
        $description='To activate please fill the OpenCalais Api Key in the group settings page';
    }
    if(!empty($field_key)){
        $api_key=get_grupo_alchemy_api_key('',$field_key);
    }
    if(empty($api_key)){
        $form[$field]['#disabled']=TRUE;
        //$form[$field]['#attributes']=array('style'=>'outline:1px solid red;');
        $form[$field]['#description']=t($description);
        $form[$field]['#prefix']='<div class="hontza_checkbox_disabled">';
        $form[$field]['#suffix']='</div>';
    }
}
//intelsat-2016
function red_canal_delete_canal_hound($canal){
    if(module_exists('hound')){
        hound_enlazar_inc_delete_canal_hound($canal);
    }
}
//intelsat-2016
function red_canal_is_noticias_validadas(){
    $noticias_validadas_array=red_canal_get_noticias_validadas_array();
    if(count($noticias_validadas_array)>0){
        return 1;
    }
    return 0;
}
//intelsat-2016
function red_canal_get_noticias_validadas_array(){
 $result=array();
 $where=array();
 $where[]="1";
 $where[]="node.type IN ('item','noticia')";
 $my_grupo=og_get_group_context(); 
 if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og.group_nid = ".$my_grupo->nid.")"; 
 } 
 $where[]="fc.fid = 2";
 $where[]="NOT node.nid IS NULL";
 $sql="SELECT node.* 
 FROM {node} node 
 LEFT JOIN {flag_content} fc ON node.nid=fc.content_id
 LEFT JOIN {og_ancestry} og ON node.nid = og.nid
 WHERE ".implode(" AND ",$where).' GROUP BY fc.content_id';
 $res = db_query($sql);
 while ($item=db_fetch_object($res)) {
    $result[]=$item;
 }
 return $result;
}
//intelsat-2016
function red_canal_array_reverse_item_array($item_array_in,&$batch){
  $item_array=$item_array_in;
  if(!empty($item_array)){
      $item_array=array_reverse($item_array);
      foreach($item_array as $i=>$rss_item){
          $batch->addItem($rss_item);
      }
  }
}
function red_canal_get_activated_channel_options(){
    $result=array();
    $label=t('Activated channel');
    $result[0]=$label;
    $result[1]=$label;
    return $result;
}