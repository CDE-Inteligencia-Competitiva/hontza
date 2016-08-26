<?php
function red_fuentes_pipes_todas_callback(){
    //intelsat-2016
    drupal_access_denied();
    exit();
    drupal_set_title(t('Sources'));
    //intelsat-2015
    hontza_canal_rss_usuario_basico_access_denied();
    $output=red_fuentes_pipes_todas_header().drupal_get_form('red_fuentes_pipes_todas_form');
    return $output;
}
function red_fuentes_pipes_todas_header(){
    $html=array();
    $html[]=red_compartir_navegar_menu();
    $html[]=red_fuentes_pipes_todas_filtro();
    return implode('',$html);
}
function red_fuentes_pipes_todas_filtro(){
    my_add_buscar_js();
    return drupal_get_form('red_fuentes_pipes_todas_filtro_form');
}
function red_fuentes_pipes_todas_form(){
    drupal_set_title(t('Share Sources'));
    boletin_report_no_group_selected_denied();
    
    $url_volver='red/fuentes-pipes/todas';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_volver=$_REQUEST['destination'];
    }
    
    $form=array();
        $fuente_list=red_fuentes_get_pipes_todas_list();
        //intelsat-2015
        $prefix='<div class="n-opciones-item">';
        $link_return=red_compartir_copiar_fuentes_servidor_volver_link(0,$url_volver);        
        $form_return_btn=array(
            '#value'=>$link_return,
            '#prefix'=>$prefix,                             
        );
        //
        if(!empty($fuente_list)){
             $form['upload_fuente_table']=array(
                 '#value'=>red_fuentes_get_pipes_todas_table_html($fuente_list),
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
function red_fuentes_get_pipes_todas_table_html($fuente_list_in){
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
            $field='fuente_region';
        }else if($order==t('Rating')){
            $is_numeric=1;
            $field='fuente_rating_suma';            
        }else if($order==t('User')){
            $field='username';            
        }else if($order==t('Status')){
            $field='fuente_status';            
        }             
    }
    //
    $my_limit=25;
    //
    $headers=array();
    $headers[0]='';
    $headers[1]=array('data'=>t('Description'),'field'=>'fuente_title');
    $headers[2]=array('data'=>t('Region'),'field'=>'fuente_region');
    $headers[3]=array('data'=>t('Rating'),'field'=>'fuente_rating_suma');
    $headers[4]=array('data'=>t('Status'),'field'=>'fuente_status');
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
                        $rows[$kont][0].='<input type="checkbox" id="upload_fuente_'.$r->nid.'" name="upload_fuente['.$r->nid.']" value="1"/>&nbsp;';
                    }
                    //intelsat-2015
                    //se ha comentado esto
                    //$rows[$kont][0].=l(my_get_icono_action('viewmag', t('View')),'node/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination()));
                    //
                    $rows[$kont][0].='</div>';
                    //intelsat-2015
                    //$fuente_description=red_fuente_get_description($node,1,1,'node/'.$r->nid);
                    $fuente_description=red_fuente_get_description($node,1,1,'red_compartir/red_compartir_view_fuente_local/'.$r->nid);
                    //
                    $rows[$kont][1]=$fuente_description;
                    $region=red_regiones_get_region_decode_value($r->field_fuente_region_value);                    
                    $rows[$kont][2]=$region;
                    $rows[$kont][3]=array('data'=>red_fuente_get_rating_en_filas($node),'style'=>'width:190px;');
                    $info_status=red_fuente_get_info_status($node);                    
                    $rows[$kont][4]=$info_status['label'];
                    $username=hontza_get_username($node->uid);
                    $rows[$kont][5]=l($username,'user/'.$node->uid);
                    $fuente_calidad=hontza_get_fuente_stars_value($node,'calidad',0);
                    $fuente_exhaustividad=hontza_get_fuente_stars_value($node,'exhaustividad',0);
                    $fuente_actualizacion=hontza_get_fuente_stars_value($node,'actualizacion',0);
                    $rows[$kont]['fuente_description']=$fuente_description;
                    $rows[$kont]['fuente_rating_suma']=$fuente_calidad+$fuente_exhaustividad+$fuente_actualizacion;
                    $rows[$kont]['fuente_region']=strtolower($region);
                    $rows[$kont]['fuente_status']=strtolower($info_status['label']);
                    $rows[$kont]['username']=strtolower($username);
                    $rows[$kont]['node']=$node;
                    $rows[$kont]['calidad']=$fuente_calidad;
                    $rows[$kont]['exhaustividad']=$fuente_exhaustividad;
                    $rows[$kont]['actualizacion']=$fuente_actualizacion;
                    $rows[$kont]['status']=$info_status['value'];
                    $kont++;
                }    
        }        
    }
    
    $filtro=array();
    if(isset($_SESSION['red_fuentes_pipes_todas']['filter'])){
        $filtro=$_SESSION['red_fuentes_pipes_todas']['filter'];
    }
    
    $rows=red_fuentes_pipes_todas_repasar_filtro($rows,$filtro);
    
    if(!empty($field)){
        $rows=array_ordenatu($rows,$field,$sort,$is_numeric);
    }
    
    
    $rows=hontza_unset_array($rows,array('fuente_description','fuente_rating_suma','fuente_region','fuente_status','username','node','calidad','exhaustividad','actualizacion','status'));
    
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function red_fuentes_get_pipes_todas_list($grupo_nid_in=''){
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='node.type in ("supercanal", "fuentedapper", "fuentehtml")';
    $where[]='node.status <> 0';
    $my_grupo=og_get_group_context();
    if(!empty($grupo_nid_in)){
        $where[]='og_ancestry.group_nid = '.$grupo_nid_in;
    }else if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='og_ancestry.group_nid = '.$my_grupo->nid;
    }else{
        return $result;
    }   
    //
    $filter_fields=red_fuentes_pipes_todas_filter_fields();
    if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=red_fuentes_pipes_todas_get_filter_value($f);
           if(!empty($v)){
                switch($f){
                    case 'fuente_title':
                        $where[]="node.title LIKE '%%".$v."%%'";
                        break;
                    case 'region':
                        $v=red_regiones_get_region_decode_value($v);                        
                        if(!empty($v)){
                            $where[]='content_field_fuente_region.field_fuente_region_value LIKE "%%#'.$v.'#%%"';
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
    $sql='SELECT DISTINCT(node.nid) AS nid,
    node.title AS node_title,
    node.language AS node_language,
    node.type AS node_type,
    term_data.name AS term_data_name,
    term_data.vid AS term_data_vid,
    term_data.tid AS term_data_tid,
    users.name AS users_name,
    node_data_field_supercanal_calidad.field_supercanal_calidad_rating AS node_data_field_supercanal_calidad_field_supercanal_calidad_rating,
    node_data_field_supercanal_calidad.field_supercanal_calidad_target AS node_data_field_supercanal_calidad_field_supercanal_calidad_target,
    node.vid AS node_vid,
    node_data_field_supercanal_calidad.field_supercanal_exhaustividad_rating AS node_data_field_supercanal_calidad_field_supercanal_exhaustividad_rating,
    node_data_field_supercanal_calidad.field_supercanal_exhaustividad_target AS node_data_field_supercanal_calidad_field_supercanal_exhaustividad_target,
    node_data_field_supercanal_calidad.field_supercanal_actualizacion_rating AS node_data_field_supercanal_calidad_field_supercanal_actualizacion_rating,
    node_data_field_supercanal_calidad.field_supercanal_actualizacion_target AS node_data_field_supercanal_calidad_field_supercanal_actualizacion_target,
    node.created AS node_created,
    content_field_fuente_region.field_fuente_region_value
    FROM {node} node
    LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN {term_node} term_node ON node.vid = term_node.vid
    LEFT JOIN {term_data} term_data ON term_node.tid = term_data.tid 
    INNER JOIN {users} users ON node.uid = users.uid 
    LEFT JOIN {content_type_supercanal} node_data_field_supercanal_calidad ON node.vid = node_data_field_supercanal_calidad.vid
    LEFT JOIN {content_field_fuente_region} content_field_fuente_region ON node.vid=content_field_fuente_region.vid
    WHERE '.implode(' AND ',$where).' 
    GROUP BY nid ORDER BY node_title '.$sort;
    //GROUP BY nid ORDER BY node_created DESC';
    //print $sql;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    
    return $result;        
}
function red_fuente_get_description($node,$is_with_body=0,$is_link=0,$url=''){
    $len=150;
    $resumen=red_fuente_get_resumen($node,$len,$is_with_body);
    $icono='';
    if(in_array($node->type,array('supercanal','fuentedapper'))){
        $icono=my_get_icono_action('fuente20',t('Source'));
    }else if(in_array($node->type,array('canal_de_supercanal','canal_de_yql'))){
        $icono=my_get_icono_action('canal20',t('Channel'));
    }
    if(!empty($icono)){
        $icono.='&nbsp;';
    }
    //intelsat-2015
    $title=$node->title;
    if($is_link){
        $title=l($title,$url,array('query'=>drupal_get_destination()));                    
    }
    $html[]='<b>'.$icono.$title.'</b>';
    //
    $html[]='<p><i>'.$resumen.'</i></p>';
    return implode('',$html);
}
function red_fuente_get_resumen($node,$len=150,$is_with_body=0){
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
function red_fuente_get_info_status($node){
    $info=array();
    $html=array();
    $value_array=array();
    if(red_fuente_is_descargados($node)){
        $html[]=t('...Downloaded');
        $value_array[]='downloaded';
    }
    if(red_fuente_is_uploaded($node)){
        $html[]=t('...Shared');
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
function red_fuente_is_uploaded($node){
    $red_compartir_fuente_array=red_fuente_get_red_compartir_fuente_array($node);
    if(count($red_compartir_fuente_array)>0){
        return 1;
    }
    return 0;
}
function red_fuente_get_red_compartir_fuente_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_fuente} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fuente_is_descargados($node){
    $red_compartir_fuente_array=red_fuente_get_red_compartir_fuente_descargados_array($node);
    if(count($red_compartir_fuente_array)>0){
        return 1;
    }
    return 0;
}
function red_fuente_get_red_compartir_fuente_descargados_array($node){
    $result=array();
    $res=db_query('SELECT * FROM {red_compartir_fuente_descargados} WHERE nid=%d AND vid=%d',$node->nid,$node->vid);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_fuente_get_rating_en_filas($node){
    $info_stars=array();
    $td_style='border-bottom:0px;border-right:0px;';
    $td_style_left=$td_style.'width:75px;';
    $html=array();
    $html[]='<table style="border:0px;>';
    $html[]='<tr style="width:100%;">';
    $html[]='<td style="'.$td_style_left.'">';
    $html[]='<b>'.t('Quality').':</b>';
    $html[]='</td>';
    $html[]='<td style="'.$td_style.'">';
    $html[]=hontza_get_fuente_stars_value($node,'calidad',1);
    $html[]='</td>';
    $html[]='</tr>';
    $html[]='<tr style="width:100%;">';
    $html[]='<td style="'.$td_style_left.'">';
    $html[]='<b>'.t('Coverage').':</b>';
    $html[]='</td>';
    $html[]='<td style="'.$td_style.'">';
    $html[]=hontza_get_fuente_stars_value($node,'exhaustividad',1);
    $html[]='</td>';
    $html[]='</tr>';
    $html[]='<tr style="width:100%;">';
    $html[]='<td style="'.$td_style_left.'">';
    $html[]='<b>'.t('Update').':</b>';
    $html[]='</td>';
    $html[]='<td style="'.$td_style.'">';
    $html[]=hontza_get_fuente_stars_value($node,'actualizacion',1);
    $html[]='</td>';
    $html[]='</tr>';
    $html[]='</table>';
    return implode('',$html);    
}
function red_fuentes_pipes_todas_filtro_form(){
    $form=array();
    $fs_title=t('Search');
    if(!red_fuentes_pipes_todas_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //    
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['fuente_title']=
        array('#type'=>'textfield',
        '#title'=>t('Title'),
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('fuente_title'));
    $form['file_buscar_fs']['fuente_description']=
        array('#type'=>'textfield',
        '#title'=>t('Description'),
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('fuente_description'));
    /*$form['file_buscar_fs']['region']=
        array('#type'=>'textfield',
        '#title'=>t('Region'),
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('region'));*/
    /*$form['file_buscar_fs']['region']=
        array('#type'=>'select',
        '#title'=>t('Region'),
        '#options'=>red_regiones_define_regiones_options(1),    
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('region'));*/
    //$form['file_buscar_fs']['region_fs']=red_regiones_define_fieldset('',red_fuentes_pipes_todas_get_filter_value('region'));    
    $form['file_buscar_fs']['calidad']=
        array('#type'=>'select',
        '#title'=>t('Quality'),
        '#options'=>my_get_star_texto_options('calidad',0),    
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('calidad'));
     $form['file_buscar_fs']['exhaustividad']=
        array('#type'=>'select',
        '#title'=>t('Coverage'),
        '#options'=>my_get_star_texto_options('exahustivo',0),    
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('exhaustividad'));
     $form['file_buscar_fs']['actualizacion']=
        array('#type'=>'select',
        '#title'=>t('Update'),
        '#options'=>my_get_star_texto_options('actualizacion',0),    
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('actualizacion'));     
     $form['file_buscar_fs']['status']=
        array('#type'=>'select',
        '#title'=>t('Status'),
        '#options'=>red_fuentes_pipes_todas_define_status_options(),    
        "#default_value"=>red_fuentes_pipes_todas_get_filter_value('status'));
     
     $form['file_buscar_fs']['username']= array(
	  '#title' => t('User'),
	  '#type' => 'textfield',
	  '#maxlength' => 60,
	  '#autocomplete_path' => 'user/autocomplete',
	  "#default_value"=>red_fuentes_pipes_todas_get_filter_value('username'));      
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div id="red_red_fuentes_filtro_botones">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form;
}
function red_fuentes_pipes_todas_get_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'red_fuentes_pipes_todas');
}
function red_fuentes_pipes_todas_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['red_fuentes_pipes_todas']['filter']) && !empty($_SESSION['red_fuentes_pipes_todas']['filter'])){
                unset($_SESSION['red_fuentes_pipes_todas']['filter']);
            }
        }else{
            $_SESSION['red_fuentes_pipes_todas']['filter']=array();
            $fields=red_fuentes_pipes_todas_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    if($f=='region'){
                        $v=red_regiones_get_region_post_value();                        
                    }else{
                        $v=$form_state['values'][$f];
                    }
                    if(!empty($v)){
                        $_SESSION['red_fuentes_pipes_todas']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function red_fuentes_pipes_todas_is_filter_activated(){
    $filter_fields=red_fuentes_pipes_todas_filter_fields();
    if(!empty($filter_fields)){
        foreach($filter_fields as $i=>$f){
            $v=red_fuentes_pipes_todas_get_filter_value($f);
            if(!empty($v)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_fuentes_pipes_todas_filter_fields(){
    $filter_fields=array('fuente_title','fuente_description','region','calidad','exhaustividad','actualizacion','status','username');
    return $filter_fields;
}
function red_fuentes_pipes_todas_repasar_filtro($result_in,$filtro){
    $result=array();
    $fields=array('fuente_description','status');
    $star_fields=array('calidad','exhaustividad','actualizacion');
    $fields=array_merge($fields,$star_fields);
    if(!empty($result_in)){
        $kont=0;
        //$filtro['fuente_description']='dos';
        foreach($result_in as $i=>$row){
            $ok=1;
            foreach($fields as $k=>$f){
                    if(isset($filtro[$f]) && !empty($filtro[$f])){
                        if(in_array($f,$star_fields)){
                            $v=hontza_get_fuente_stars_value($row['node'],$f,0);
                            //print $v.'====='.$filtro[$f].'<BR>';
                            if($v<$filtro[$f]){
                                $ok=0;
                                break;
                            }
                        }else if($f=='fuente_description'){
                            $v=$filtro[$f];
                            if(!red_fuentes_pipes_todas_is_description_like($v,$row['node'])){
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
function red_fuentes_pipes_todas_is_description_like($v,$node){
    $text='';
    if(hontza_node_has_body($node)){
        $text=$node->content['body']['#value'];
    }else if(isset($node->body) && !empty($node->body)){
        $text=$node->body;
    }else if(isset($node->teaser) && !empty($node->teaser)){
        $text=$node->teaser;
    }    
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
function red_fuentes_pipes_todas_define_status_options(){
    $result=array();
    $result[0]='';    
    $result['downloaded']=t('...Downloaded');
    $result['local']=t('Local');
    $result['uploaded']=t('Shared');
    return $result;
}
function red_fuentes_pipes_todas_form_submit(&$form, &$form_state){
    global $user;
    if(isset($_SESSION['upload_fuente_array'])){
        unset($_SESSION['upload_fuente_array']);
    }
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'my_submit')==0){
            if(isset($_POST['upload_fuente']) && !empty($_POST['upload_fuente'])){
                $upload_fuente_array=array_keys($_POST['upload_fuente']);
                $_SESSION['upload_fuente_array']['user_'.$user->uid]=$upload_fuente_array;
                drupal_goto('red_compartir/compartir_fuente_hoja_multiple');
            }
        }
    } 
}
function red_fuente_is_compartir_fuente_servidor($with_publica=0){
    $param0=arg(0);
    if(!empty($param0) && $param0=='red_compartir'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='red_compartir_view_fuente_servidor'){
            $param2=arg(2);
            if(!empty($param2)){
                return 1;
            }    
        }
    }else if(!empty($param0) && $param0=='red_publica'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='fuente'){
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
function red_fuente_set_og_fuentes_pipes($vars_in){
    $ret=array();
    $vars=$vars_in;
    $find='<th class="views-field views-field';
    $my_array=explode($find,$vars['rows']);
    $kont_array=array(3,8,9);
    $num=count($my_array);
    foreach($my_array as $i=>$v){
        if($i>0){
            if($i==($num-1)){
                $pos=strpos($v,'</tr>');
                //print $v;exit();
                $b=substr($v,$pos);
                $k=count($ret);
                $ret[$k-4]=$type;
                $ret[$k-5]=$creator;
            }else{
                if($i==3){
                    $type=$v;
                }else if($i==4){
                    $creator=$v;
                }
                if(!in_array($i,$kont_array)){
                    //$b=str_replace('">','" style="display:none;">',$v);
                    $ret[]=$v;
                }
            }    
        }else{
            $ret[]=$v;
        }
    }
    $vars['rows']=implode($find,$ret).$b;
    //
    $ret=array();
    $find='<td class="views-field views-field';
    $my_array=explode($find,$vars['rows']);
    $kont=0;
    $num=count($my_array);
    $c=0;
    foreach($my_array as $i=>$v){
        if($i>0){
            if($kont==10){
                $kont=0;
                $pos=strpos($v,'</tr>');
                $b=substr($v,$pos);
                $k=count($ret);
                $ret[$k-1].=$b;
                $ret[$k-4]=$type;
                $ret[$k-5]=red_fuente_set_creator_image($creator,$vars['view']->result[$c]->node_uid);
                //$ret[$k-6]=$creator;
                $ret[$k-6]=red_copiar_get_fuente_title_color($ret[$k-6],$vars['view']->result[$c]);
                $c=$c+1;
            }else{
                if($kont==3){
                    $type=$v;
                }else if($kont==4){
                    $creator=$v;
                }
                if(!in_array($kont,$kont_array)){
                    //$b=str_replace('">','" style="display:none;">',$v);
                    $ret[]=$v;
                }
            }    
        }else{
            $ret[]=$v;
        }
        $kont++;
        /*if($kont==10){
            $kont=0;
        }*/
    }
    $vars['rows']=implode($find,$ret);
    //
    return $vars['rows'];
}
function red_fuente_set_creator_image($creator_in,$uid_in){    
    $result=$creator_in;
    $find='">';    
    $pos=strpos($result,$find);
    if($pos===FALSE){
        return $result;
    }else{
        $k=$pos+strlen($find);
        $creator=substr($result,$k);
        $user_image=hontza_grupos_mi_grupo_get_user_img($uid_in,50);
        //$result=substr($result,0,$k).'<div>'.$user_image.'</div>';
        $result=substr($result,0,$k).$user_image;
        return $result;
    }
}                