<?php
function red_facilitadores_todas_callback(){
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();    
    $output=red_facilitadores_todas_header().drupal_get_form('red_facilitadores_todas_form');
    return $output;
}
function red_facilitadores_todas_header(){
    $html=array();
    $html[]=red_compartir_navegar_menu();
    $html[]=red_facilitadores_todas_filtro();
    return implode('',$html);
}
function red_facilitadores_todas_form(){
    drupal_set_title(t('Share Facilitators'));
    boletin_report_no_group_selected_denied();
    
    $url_volver='red/fuentes-pipes/todas';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_volver=$_REQUEST['destination'];
    }
    
    $form=array();
    $facilitador_list=red_facilitadores_get_todas_list();
    //intelsat-2015
    $prefix='<div class="n-opciones-item">';
    $link_return=red_compartir_copiar_fuentes_servidor_volver_link(0,$url_volver);        
    $form_return_btn=array(
        '#value'=>$link_return,
        '#prefix'=>$prefix,                             
    );
    //        
        if(empty($facilitador_list)){
            $facilitador_list=red_facilitadores_get_todas_list('en');
        }
        if(!empty($facilitador_list)){
             $form['upload_facilitador_table']=array(
                 '#value'=>red_facilitadores_get_todas_table_html($facilitador_list),
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
function red_facilitadores_get_todas_list($my_lang=''){
    global $language;
    $result=array();
    $where[]='(node.type in ("servicio"))';
    if(empty($my_lang)){
        $where[]='(node.language in ("'.$language->language.'"))';
    }else{
        $where[]='(node.language in ("'.$my_lang.'"))';
    }
    //
    $filter_fields=red_facilitadores_todas_filter_fields();
    if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=red_facilitadores_todas_get_filter_value($f);
           if(!empty($v)){
                switch($f){
                    case 'facilitador_title':
                        $where[]="node.title LIKE '%%".$v."%%'";
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
    node_data_field_logo_servicios.field_logo_servicios_fid AS node_data_field_logo_servicios_field_logo_servicios_fid,
    node_data_field_logo_servicios.field_logo_servicios_list AS node_data_field_logo_servicios_field_logo_servicios_list,
    node_data_field_logo_servicios.field_logo_servicios_data AS node_data_field_logo_servicios_field_logo_servicios_data,
    node.type AS node_type,
    node.vid AS node_vid,
    node_data_field_logo_servicios.field_descripcion_servicios_value AS node_data_field_logo_servicios_field_descripcion_servicios_value,
    node_data_field_logo_servicios.field_descripcion_servicios_format AS node_data_field_logo_servicios_field_descripcion_servicios_format,
    node.created AS fecha
    FROM {node} node 
    LEFT JOIN {content_type_servicio} node_data_field_logo_servicios ON node.vid = node_data_field_logo_servicios.vid
    LEFT JOIN {users} users ON node.uid=users.uid 
    WHERE '.implode(' AND ',$where).'
    ORDER BY node_title '.$sort;
    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }    
    
    return $result;
}
function red_facilitadores_get_todas_table_html($facilitador_list){    
    $sort='';
    $is_numeric=0;
    $field='';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }    
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Description')){
            $field='facilitador_description';        
        }else if($order==t('User')){
            $field='username';            
        }else if($order==t('Status')){
            $field='facilitador_status';            
        }else if($order==t('Created')){
            $field='fecha';
            $is_numeric=1;
        }              
    }
    //
    $my_limit=25;
    $headers=array();
    $headers[0]='';
    $headers[1]=array('data'=>t('Title'),'field'=>'node_title');
    $headers[2]=array('data'=>t('Description'),'field'=>'facilitador_description');
    $headers[3]=t('Categories');
    $headers[4]=array('data'=>t('Created'),'field'=>'fecha');
    $headers[5]=array('data'=>t('Status'),'field'=>'facilitador_status');
    $headers[6]=array('data'=>t('User'),'field'=>'username');
    //
    $is_mostrar=hontza_is_mostrar_recursos_compartidos_del_servidor_red();
    if(!empty($facilitador_list)){
        foreach($facilitador_list as $i=>$r){
                $node=node_load($r->nid);
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=array();
                    //
                    $rows[$kont][0]='<div style="white-space:nowrap;">';
                    if($is_mostrar){
                        $rows[$kont][0].='<input type="checkbox" id="upload_facilitador_'.$r->nid.'" name="upload_facilitador['.$r->nid.']" value="1"/>&nbsp;';
                    }
                    //intelsat-2015
                    //$rows[$kont][0].=l(my_get_icono_action('viewmag', t('View')),'node/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination()));
                    //
                    $rows[$kont][0].='</div>';
                    //intelsat-2015                   
                    $rows[$kont][1]=l($node->title,'red_compartir/red_compartir_view_facilitador_local/'.$r->nid,array('query'=>drupal_get_destination()));
                    //
                    $facilitador_description=$node->field_descripcion_servicios[0]['value'];
                    $rows[$kont][2]=$facilitador_description;
                    $rows[$kont][3]=red_compartir_facilitador_copy_set_categories($node);
                    $rows[$kont][4]=date('Y-m-d',$r->fecha);
                    $info_status=red_facilitador_get_info_status($node);
                    $rows[$kont][5]=$info_status['label'];
                    $username=hontza_get_username($node->uid);
                    $rows[$kont][6]=l($username,'user/'.$node->uid,array('query'=>drupal_get_destination()));
                    $rows[$kont]['facilitador_description']=strip_tags($facilitador_description);
                    $rows[$kont]['facilitador_status']=strtolower($info_status['label']);
                    $rows[$kont]['username']=strtolower($username);
                    $rows[$kont]['username']=strtolower($username);
                    $rows[$kont]['fecha']=$r->fecha;
                    $kont++;
                }
        }        
    }
    
    $filtro=array();
    if(isset($_SESSION['red_facilitadores_todas']['filter'])){
        $filtro=$_SESSION['red_facilitadores_todas']['filter'];
    }
    
    $rows=red_facilitadores_todas_repasar_filtro($rows,$filtro);
    
    if(!empty($field)){
        $rows=array_ordenatu($rows,$field,$sort,$is_numeric);
    }
    
    
    $rows=hontza_unset_array($rows,array('facilitador_description','facilitador_status','username','node','fecha'));
    
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));           
    }
    else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function red_facilitadores_todas_form_submit(&$form, &$form_state){
    global $user;
    if(isset($_SESSION['upload_facilitador_array'])){
        unset($_SESSION['upload_facilitador_array']);
    }
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'my_submit')==0){
            if(isset($_POST['upload_facilitador']) && !empty($_POST['upload_facilitador'])){
                $upload_facilitador_array=array_keys($_POST['upload_facilitador']);
                $_SESSION['upload_facilitador_array']['user_'.$user->uid]=$upload_facilitador_array;
                drupal_goto('red_compartir/compartir_facilitador_hoja_multiple');
            }
        }
    } 
}
function red_facilitador_set_default_language(&$form,&$form_state,$form_id){
    $form['language']['#default_value']='en';
    //$form['language']['#options']=red_unset_empty_key($form['language']['#options']);
}
function red_facilitador_get_info_status($node){
    $info=array();
    $html=array();
    $value_array=array();
    if(red_facilitador_is_descargados($node)){
        $html[]=t('..Downloaded');
        $value_array[]='downloaded';
    }
    if(red_facilitador_is_uploaded($node)){
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
function red_facilitador_is_descargados($node){
    $red_compartir_facilitador_array=red_facilitador_get_red_compartir_facilitador_descargados_array($node);
    if(count($red_compartir_facilitador_array)>0){
        return 1;
    }
    return 0;
}
function red_facilitador_get_red_compartir_facilitador_descargados_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_facilitador_descargados} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_facilitador_is_uploaded($node){
    $red_compartir_facilitador_array=red_facilitador_get_red_compartir_facilitador_array($node);
    if(count($red_compartir_facilitador_array)>0){
        return 1;
    }
    return 0;
}
function red_facilitador_get_red_compartir_facilitador_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_facilitador} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_facilitadores_todas_filtro(){
    my_add_buscar_js();
    return drupal_get_form('red_facilitadores_todas_filtro_form');
}
function red_facilitadores_todas_filtro_form(){
    $form=array();
    $fs_title=t('Search');
    if(!red_facilitadores_todas_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //    
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['facilitador_title']=
        array('#type'=>'textfield',
        '#title'=>t('Title'),
        "#default_value"=>red_facilitadores_todas_get_filter_value('facilitador_title'));
    $form['file_buscar_fs']['facilitador_description']=
        array('#type'=>'textfield',
        '#title'=>t('Description'),
        "#default_value"=>red_facilitadores_todas_get_filter_value('facilitador_description'));
    
     $form['file_buscar_fs']['facilitador_status']=
        array('#type'=>'select',
        '#title'=>t('Status'),
        '#options'=>red_facilitadores_todas_define_status_options(),    
        "#default_value"=>red_facilitadores_todas_get_filter_value('facilitador_status'));
     
     $form['file_buscar_fs']['username']= array(
	  '#title' => t('User'),
	  '#type' => 'textfield',
	  '#maxlength' => 60,
	  '#autocomplete_path' => 'user/autocomplete',
	  "#default_value"=>red_facilitadores_todas_get_filter_value('username'));      
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div id="red_red_fuentes_filtro_botones">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form;
}
function red_facilitadores_todas_is_filter_activated(){
    $filter_fields=red_facilitadores_todas_filter_fields();
    if(!empty($filter_fields)){
        foreach($filter_fields as $i=>$f){
            //$v=red_compartir_fuentes_copy_get_filter_value($f);
            $v=red_facilitadores_todas_get_filter_value($f);
            if(!empty($v)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_facilitadores_todas_filter_fields(){
    $filter_fields=array('facilitador_title','facilitador_description','facilitador_status','username');
    return $filter_fields;
}
function red_facilitadores_todas_get_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'red_facilitadores_todas');
}
function red_facilitadores_todas_define_status_options(){
    return red_fuentes_pipes_todas_define_status_options();
}
function red_facilitadores_todas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['red_facilitadores_todas']['filter']) && !empty($_SESSION['red_facilitadores_todas']['filter'])){
                unset($_SESSION['red_facilitadores_todas']['filter']);
            }
        }else{
            $_SESSION['red_facilitadores_todas']['filter']=array();
            $fields=red_facilitadores_todas_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];                    
                    if(!empty($v)){
                        $_SESSION['red_facilitadores_todas']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function red_facilitadores_todas_repasar_filtro($result_in,$filtro){
    $result=array();
    $fields=array('facilitador_description','facilitador_status');
    if(!empty($result_in)){
        $kont=0;
        foreach($result_in as $i=>$row){
            $ok=1;
            foreach($fields as $k=>$f){
                if(isset($filtro[$f]) && !empty($filtro[$f])){
                    if($f=='facilitador_description'){
                            $v=$filtro[$f];
                            if(!red_facilitadores_todas_is_description_like($v,$row['facilitador_description'])){
                                $ok=0;
                                break;
                            }
                    }else if($f=='facilitador_status'){
                            $v=$filtro[$f];
                            if($v!=$row['facilitador_status']){
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
function red_facilitadores_todas_is_description_like($v,$text_in){
    $text=$text_in;
    //
    if(!empty($text)){
        $text=strip_tags($text);
        $pos=strpos($text,$v);
        if($pos===FALSE){
            return 0;
        }else{
            return 1;
        }
    }
    return 0;
}
//intelsat-2016
function red_facilitador_acces_denied(){
    if(!red_facilitador_user_access()){
        drupal_access_denied();
        exit();
    }
}
//intelsat-2016
function red_facilitador_user_access(){
    if(hontza_is_red_hoja()){
        return 1;
    }
    if(hontza_is_servidor_red_alerta()){
        return 1;
    }
    return 0;
}
//intelsat-2016
function red_facilitador_is_grupo_colaborativo_publico($nid){
    $grupo_node=node_load($nid);
    if(isset($grupo_node->nid) && !empty($grupo_node->nid)){
        if(isset($grupo_node->taxonomy) && !empty($grupo_node->taxonomy)){
            foreach($grupo_node->taxonomy as $tid=>$term){
                if($term->vid==6){
                    if($tid==28){
                        return 0;
                    }
                }
            }
        }
    }
    return 1;
}
//intelsat-2016
function red_facilitador_user_profile_form_alter(&$form,&$form_state,$form_id){
    if(hontza_canal_rss_is_facilitador_activado()){
        facilitador_user_profile_form_alter($form,$form_state,$form_id);
    }
}
//intelsat-2016
function red_facilitator_get_servicios_experto_vid(){
    $result=28;
    return $result;
}
//intelsat-2016
function red_facilitador_unset_traduccion($result_in){
    $result=ltrim($result_in,'[');
    $result=rtrim($result,']');
    return $result;
}