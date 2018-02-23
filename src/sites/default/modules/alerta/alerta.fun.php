<?php
function mis_boletines_grupo_callback(){
    //intelsat-2015
    alerta_inc_usuario_basico_access_denied();
    return boletin_grupo_table();
}
function set_categorias_legible($categorias_string){
    $my_array=explode(',',$categorias_string);
    if(count($my_array)>0){
        $result=array();
        foreach($my_array as $i=>$tid){
            $term=taxonomy_get_term($tid);
            if(isset($term->tid) && !empty($term->tid)){
                $result[]=$term->name;
            }
        }
        return implode(',',$result);
    }
    return '';
}
function set_usuarios_legible($usuarios_string){
    $my_array=explode(',',$usuarios_string);
    if(count($my_array)>0){
        $result=array();
        foreach($my_array as $i=>$uid){
            $u=user_load($uid);
            if(isset($u->uid) && !empty($u->uid)){
                $result[]=$u->name;
            }
        }
        return implode(',',$result);
    }
    return '';
}
function set_canales_legible($canales_string){
    $my_array=explode(',',$canales_string);
    if(count($my_array)>0){
        $result=array();
        foreach($my_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $result[]=$node->title;
            }
        }
        return implode(',',$result);
    }
    return '';
}
function is_in_hora($hora,$param,$alert_type){
    //AVISO::::si esta en la frecuencia sin tener en cuenta la hora
    if(is_my_web()){
        return 1;
    }


    //$last_cron=get_last_cron_run_completed();
    //if(isset($last_cron->wid) && !empty($last_cron->wid)){
        //$last_date=date('Y-m-d H:i:s',$last_cron->timestamp);
        $hora_array=explode(':',$hora);
        $h=0;
        $minute=0;
        if(count($hora_array)>1){
            $h=(int) $hora_array[0];
            $minute=(int) $hora_array[1];
        }
        $my_time=time();
        $mail_time=mktime($h,$minute,0);
        
        /*if($mail_time>$last_cron->timestamp && $mail_time<=$my_time){
            //print date('Y-m-d H:i:s',$mail_time).'<BR>';
            return 1;
        }*/
        if(!is_procesed_today($param,$alert_type) && $mail_time<=$my_time){
            //print date('Y-m-d H:i:s',$mail_time).'<BR>';
            return 1;
        }
    //}
    return 0;
}
function get_last_cron_run_completed(){
    $log_cron_list=get_log_cron_list('Cron run completed.');
    if(count($log_cron_list)>0){
        return $log_cron_list[0];
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    //
    return $my_result;
}
function get_log_cron_list($message){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="w.type='cron'";
    $where[]="w.message='".$message."'";
    $sql="SELECT w.* FROM {watchdog} w WHERE ".implode(" AND ",$where).' ORDER BY w.timestamp DESC';
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function is_procesed_today($param,$alert_type){
    if($param->send_method=='simple'){        
        return 0;
    }
    //
    if($alert_type=='alerta_user'){
        $alerta_user_today_list=get_alerta_user_today_list($param->id);
        if(count($alerta_user_today_list)>0){
            return 1;
        }
        return 0;
    }
    //
    if($alert_type=='boletin_grupo'){
        $boletin_grupo_array_today_list=get_boletin_grupo_array_today_list($param->id);
        if(count($boletin_grupo_array_today_list)>0){
            return 1;
        }
        return 0;
    }
    //
    return 0;
}
function get_alerta_user_today_list($alerta_user_id){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="t.send_method='mimemail'";
    $where[]="t.alerta_user_id=".$alerta_user_id;
    $where[]="t.fecha='".date('Y-m-d')." 00:00:00'";
    $sql="SELECT t.* FROM {alerta_user_today} t WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_boletin_grupo_array_today_list($boletin_grupo_array_id){
    $result=array();
    $where=array();
    $where[]="1";
    $where[]="t.send_method='mimemail'";
    $where[]="t.boletin_grupo_array_id=".$boletin_grupo_array_id;
    $where[]="t.fecha='".date('Y-m-d')." 00:00:00'";
    $sql="SELECT t.* FROM {boletin_grupo_array_today} t WHERE ".implode(" AND ",$where);
    //print $sql;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function save_alerta_user_today($param,$alert_type){
    if($alert_type=='alerta_user'){
        if(!is_procesed_today($param,$alert_type)){
            insert_alerta_user_today($param);
        }
    }
}
function save_boletin_grupo_array_today($param,$alert_type,$content=''){
    if($alert_type=='boletin_grupo'){
        if(!is_procesed_today($param,$alert_type)){
            //intelsat-2015
            //insert_boletin_grupo_array_today($param);
            insert_boletin_grupo_array_today($param,$content);
        }
    }
}
function insert_alerta_user_today($param){
    global $user;
    if($param->send_method=='mimemail'){
        $fecha=date("Y-m-d")." 00:00:00";
        $sql=sprintf("INSERT INTO alerta_user_today(alerta_user_id,send_method,fecha,execute_uid) VALUES(%d,'%s','%s',%d)",$param->id,$param->send_method,$fecha,$user->uid);
        db_query($sql);
    }
}
function insert_boletin_grupo_array_today($param,$content){
    global $user;
    if($param->send_method=='mimemail'){
        $fecha=date("Y-m-d")." 00:00:00";
        //intelsat-2015
        //se ha añadido texto y fecha_send
        $fecha_send=time();
        db_query("INSERT INTO boletin_grupo_array_today(boletin_grupo_array_id,send_method,fecha,execute_uid,texto,fecha_send) VALUES(%d,'%s','%s',%d,'%s',%d)",$param->id,$param->send_method,$fecha,$user->uid,$content,$fecha_send);
    }
}
function set_categoria_title_con_flechas($tid){
    $result=array();
    //if(isset($term->tid) && !empty($term->tid)){

        $parents=taxonomy_get_parents_all($tid);
        if(count($parents)>0){
            $parents=array_reverse($parents);
            foreach($parents as $i=>$p){
                $result[]=$p->name;
            }
        }
        //$result[]=$term->name;
    //}
    $fletxa='->';
    //$is_class=0;
    //$fletxa=get_menu_gezia_img($is_class);
    //print $fletxa;
    return implode($fletxa,$result);
}
function user_categoria_title_dentro_mail($param){
    //$s=strtoupper($param);
    $s=my_upper($param);
    $is_class=2;
    $fletxa=get_menu_gezia_img($is_class);
    $s=str_replace('->',$fletxa,$s);
    return $s;
}
function is_my_web(){
    $param=arg(0);
    //
    if(in_array($param,array('alerta_user','boletin_grupo'))){
        $zenb=arg(1);
        if(is_numeric($zenb)){
            $my_action=arg(2);
            if(strcmp($my_action,'my_web')==0){
                
                return 1;
            }
        }
    }
    return 0;
}
function get_content_by_my_message($content,$por_correo,$my_message,$is_boletin=0){
    //AVISO::::my_message empty significa que esta en hora y en frecuencia y se utiliza en save_aviso_procesado
    if(empty($content) && !$por_correo){
        $sep='<BR><BR>';
        if(empty($my_message)){
            if($is_boletin){
                return $sep.t('There are no records in the bulletin');
                
            }else{
                return $sep.t('There are no records in the custom alert');
            }

        }else{
            return $sep.$my_message;
        }
    }
    return $content;
}
function get_group_nid_array_by_anonimo($alerta_user_id,&$param_uid){
    $group_nid_array=array();
    $param_uid=0;
    $alerta_user_list=get_alerta_user_list($alerta_user_id);
        if(count($alerta_user_list)>0){
            $my_param=$alerta_user_list[0];
            $param_uid=$my_param->uid;
            $my_user=user_load($my_param->uid);            
            if(isset($my_user->og_groups) && !empty($my_user->og_groups)){
                $group_nid_array=array_keys($my_user->og_groups);
            }
        }
    //echo print_r($group_nid_array,1);
    return $group_nid_array;
}
//intelsat-2015
//function save_aviso_procesado($row,$por_correo,$my_message,$alert_type){
function save_aviso_procesado($row,$por_correo,$my_message,$alert_type,$content=''){
    //AVISO::::my_message empty significa que esta en hora y en frecuencia
    //print 'id='.$row->id.'='.$my_message.'<BR>';
    if(in_array($row->send_method,array('mail','mimemail')) && $por_correo && empty($my_message)){
        //print 'id(2)='.$row->id.'='.$my_message.'<BR>';
        save_alerta_user_today($row,$alert_type);
        save_boletin_grupo_array_today($row,$alert_type,$content);
    }
}
function get_boletin_introduccion(&$texto,$grupo,$subject='',$is_despedida=0,$uid_in='',$param_alerta=''){  
   $src='';
   $texto='';
   //gemini-2013
   if(empty($param_alerta)){
   //    
        if($is_despedida){
         $nid=get_grupo_boletin_despedida_nid($grupo->nid);
         //print 'nid(despedida)='.$nid;
        }else{
         $nid=get_grupo_boletin_introduccion_nid($grupo->nid);         
        }
        if(!empty($nid)){


            //print 'nid='.$nid;
            $node=node_load($nid);

            if(isset($node->nid) && !empty($node->nid)){
             $src=get_src_file_node($node);
             $texto=$node->body;
             return $node->body;
            }
        }
   }     
   //     
   if($is_despedida){
        return get_despedida_estandar_html($subject,$uid_in);
   }

   //AVISO::::alerta personalizadan boletín de grupo bezela bidali daiteke
   if(!empty($subject)){
        //intelsat-2016
        //$texto='<h3>'.$subject.'</h3>';
        $texto='<h3 class="title_boletin_report_automatico_editados">'.$subject.'</h3>';
   }

       //
       if(empty($src)){
        $src=alerta_get_introduccion_logo_by_subdominio(1);       
       }
       if(empty($texto)){
        $texto='<h3>'.t('Group bulletin').':'.$grupo->title.'</h3>';
       }
       //intelsat-2015
       $class_mail_table='mail_table';
            $html=array();
            $html[]='<table class="'.$class_mail_table.'" style="width:100%;border:0px;">';
            $html[]='<tr>';
            $html[]='<td>';            
            if(!empty($src)){
                //intelsat-2016
                //$html[]='<img src="'.$src.'"/>';
                $imagesize=alerta_inc_getimagesize($src);
                $html[]='<img src="'.$src.'"'.$imagesize.'/>';
            }
            $html[]='</td>';
            /*$html[]='</tr>';
            $html[]='<tr>';*/
            $html[]='<td>';
            $html[]=$texto;
            $html[]='</td>';
            $html[]='</tr>';
            $html[]='</table>';
           
            return implode('',$html);
       
   //}
   //return '';
}
function get_src_file_node($node){
   if(isset($node->files) && !empty($node->files)){
    $files=array_values($node->files);
    $src='http://'.$_SERVER['HTTP_HOST'].base_path().$files[0]->filepath;
    //print $src;exit();
    return $src;
   }
   return '';
}
function get_boletin_destacadas_html($grupo=''){
    $is_mail=1;
    $html=array();
    $content=my_get_noticias_destacadas_canales_content($is_mail,$grupo);
    if(!empty($content)){
     $html[]='<table class="mail_table_destacadas" style="width:100%;border:0px;">';
        $html[]='<tr><th colspan="2">'.t('HIGHLIGHTED NEWS').'</th>';
        /*foreach($my_list as $i=>$row){
            $node=node_load($row->nid);
            $my_user=user_load($row->uid);
            $is_mail=1;
            $img=my_get_user_img_src('',$my_user->picture,$my_user->name,$row->uid,$is_mail);
            //*/
            //$html[]='<tr style="vertical-align:top;">';
            /*$html[]='<td style="width:100px;">'.$img.'</td>';
            $html[]='<td>'.boletin_eskubi_aldea($row,$node,$current_type).'</td>';*/
            //$html[]='<td>'.$content.'</td>';
            //$html[]='</tr>';
            $html[]=$content;
        //}
        $html[]='</table>';
    }    
    return implode('',$html);
}
function add_introduccion_despedida_html($content_in,$grupo_in='',$uid='',$subject='',$param_alerta='',$is_boletin_report=0){
   
   $grupo=$grupo_in;
   $content=$content_in;
   if(!empty($content)){
        //intelsat-2015
        $alerta_fecha='';
        if(!red_despacho_is_activado()){
            $alerta_fecha=alerta_inc_get_fecha_para_dentro_del_correo();
        }
        if(empty($grupo)){            
            $grupo=get_user_grupo_del_boletin($uid);            
        }
        //
        //print 'subj='.$subject;
        $introduccion=get_boletin_introduccion($boletin_report_titulo_mail,$grupo,$subject,0,'',$param_alerta);        
        $despedida=get_boletin_despedida($grupo,$subject,$uid,$param_alerta);
        //
        //intelsat-2015
        //if(red_despacho_is_activado()){
        if(red_dashboard_is_despacho_no_dashboard()){
            $introduccion=red_despacho_get_boletin_introduccion($introduccion,$boletin_report_titulo_mail); 
            $despedida=red_despacho_boletin_report_get_boletin_despedida($despedida);
        }        
        $destacadas_html=get_boletin_destacadas_html($grupo);
        $apuntarse_link='';
        $esaldi='';
        if(empty($param_alerta)){
            if(!hontza_is_user_anonimo()){
                $apuntarse_link='http://'.$_SERVER['HTTP_HOST'].base_path().'boletin_grupo/'.$grupo->nid.'/my_no_recibir';
                $esaldi=t('Subscribe/Unsubscribe to the Group bulletin');
            }                        
        }else if(in_array($param_alerta->tipo,array('canal','categoria','usuario'))){
            //print $current_type.'<BR>';
            $atzizkia='';
            if(in_array($param_alerta->tipo,array('categoria','usuario'))){
                $atzizkia='_'.$param_alerta->tipo;
            }
            $apuntarse_link='http://'.$_SERVER['HTTP_HOST'].base_path().'alerta_user/'.$param_alerta->id.'/my_edit'.$atzizkia;
            $esaldi=t('Subscribe/Unsubscribe to the custom alert');

        }
        if(!hontza_is_user_anonimo()){
            $destacadas_html.='<table class="mail_table" style="width:100%;border:0px;"><tr><td colspan="2">'.'<a href="'.$apuntarse_link.'">'.$esaldi.'</a></td></tr></table>';
        }
        //return $content;
        if($is_boletin_report){
            $destacadas_html='';
        }
        $content=$introduccion.$alerta_fecha.$destacadas_html.$content.$despedida;
   }
    
    return $content;
}
function get_user_grupo_del_boletin($uid){
    $my_grupo_nid='';
    $my_user=user_load($uid);
    /*if(is_super_admin()){
        echo print_r($my_user,1);
        exit();
    }*/
    if(isset($my_user->og_groups) && !empty($my_user->og_groups)){
        foreach($my_user->og_groups as $grupo_nid=>$grupo_row){
            $my_list=get_content_type_boletin_grupo_introduccion_list($grupo_nid);
            if(count($my_list)>0){
                $my_grupo_nid=$grupo_nid;
                break;
            }            
        }
        //        
        if(!empty($my_grupo_nid)){
            $grupo=node_load($my_grupo_nid);
            return $grupo;
        }
        //
        foreach($my_user->og_groups as $grupo_nid=>$grupo_row){
            $my_list=get_content_type_boletin_grupo_despedida_list($grupo_nid);
            if(count($my_list)>0){
                $my_grupo_nid=$grupo_nid;
                break;
            }
        }
        if(empty($my_grupo_nid)){
            $my_grupo_nid=$grupo_nid;
        }
        //
        $grupo=node_load($my_grupo_nid);
        return $grupo;
        //
    }   
    //
    return '';
}
function get_boletin_despedida($grupo,$subject='',$uid='',$param_alerta=''){
    $is_despedida=1;
    return get_boletin_introduccion($boletin_report_titulo_mail,$grupo,$subject,$is_despedida,$uid,$param_alerta);
}
function get_grupo_by_tid($tid){
    $term=taxonomy_get_term($tid);
    if(isset($term->vid) && !empty($term->vid)){
        $vocab_list=my_get_og_vocab_list(array('vid'=>$term->vid));
        if(count($vocab_list)>0){
            $grupo_nid=$vocab_list[0]->nid;
            if(!empty($grupo_nid)){
                $grupo=node_load($grupo_nid);
                return $grupo;
            }
        }
    }
    return '';
}
function no_hay_novedades_html($current_type,$titulo_mail,$my_message,$param_alerta='',$term_name='',$is_categoria_titulo=0,$tipos_fuente_row=''){
    if(!empty($my_message)){
        return '';
    }
    $html=array();
        $html[]='<table class="mail_table" style="width:100%;border:0px;">';
        $th_class=red_despacho_boletin_report_get_th_categoria_title_class($tipos_fuente_row);
        $html[]='<tr><th colspan="2"'.$th_class.'>'.get_current_type_title($current_type,$titulo_mail,$param_alerta,$term_name,$tipos_fuente_row).'</th></tr>';
        if(!$is_categoria_titulo){
            $html[]='<tr style="vertical-align:top;">';
            //$html[]='<td style="width:100px;">'.$img.'</td>';
            $html[]='<td>'.t('There are no news').'</td>';
            $html[]='</tr>';
        }
        $html[]='</table>';
    
    return implode('',$html);
}
function get_despedida_estandar_html($subject,$uid_in){
    //
    $sareko_id=_SAREKO_ID;
    //intelsat-2016
    //if(!empty($sareko_id) && in_array($sareko_id,array('GESTION_CLAVES'))){
    if(red_is_claves_activado()){    
        return '';
    }
    $html=array();
        $html[]='<table class="mail_table_despedida" style="width:100%;border:0px;">';
        $html[]='<tr>';
        $html[]='<td style="text-align:center;"><b>'.$subject.'</b></td>';
        $html[]='</tr>';
        $html[]='<tr>';
        $url_mis_alertas='http://'.$_SERVER['HTTP_HOST'].base_path().'alerta_user/'.$uid_in.'/my_list';
        $html[]='<td style="text-align:right;"><a href="'.$url_mis_alertas.'">'.t('My Alerts').'</td>';
        $html[]='</tr>';
        $html[]='<tr><td>'.get_quienes_condiciciones_politica_privacidad().'</td></tr>';
        $html[]='<tr>';
        $url_powered='http://'.$_SERVER['HTTP_HOST'];
        $url_powered='http://www.hontza.es';
        $html[]='<tr><td style="text-align:center;"><a href="'.$url_powered.'">'.get_frase_powered('castellano').'</td></tr>';
        $html[]='<tr><td style="text-align:center;"><a href="'.$url_powered.'">'.get_frase_powered('ingles').'</td></tr>';
        $html[]='</tr>';
        $html[]='</table>';

    return implode('',$html);
}
function get_frase_powered($lang='castellano'){    
    $result='';
    switch($lang){
        case 'castellano':
            //$result='Impulsado por Hontza 3.0 - Plataforma Abierta de Inteligencia Competitiva';
            //$result='Hontza - Plataforma Abierta para Inteligencia Competitiva y Estratégica';
            $result='Hontza - Plataforma de Software Libre para Inteligencia Competitiva y Estratégica';
            break;
        case 'ingles':
            //$result='Powered by Hontza 3.0 - Open Code Platform for Competitive Intelligence';
            //$result='Hontza - Open Code Platform for Competitive and Strategic Intelligence';
            //$result='Hontza - Open Code Platform for Strategic and Competitive Intelligence';
            $result='Hontza - Free Software Platform for Strategic and Competitive Intelligence';
            break;
    }
    $url='http://www.hontza.es';
    $result='<a id="id_a_'.$lang.'" href="'.$url.'">'.$result.'</a>';
    return $result;
}
function get_quienes_condiciciones_politica_privacidad(){
    $html=array();
    //
    $url_quienes='http://www.hontza.es';
    $url_condiciones='http://www.hontza.es';
    $url_politica='http://www.hontza.es';
    $url_privacidad='http://www.hontza.es';
    //
    $html[]='<a href="'.$url_quienes.'">'.t('Who we are').'</a>';
    $html[]='<a href="'.$url_condiciones.'">'.t('Terms of Use');
    $html[]='<a href="'.$url_politica.'">'.t('Anti-Spam Policy');
    $html[]='<a href="'.$url_privacidad.'">'.t('Anti-Spam Policy');
    return implode('&nbsp;|&nbsp;',$html);
}
function create_is_recibir_txek($row){
    $default_value=1;
    if(isset($row->id) && !empty($row->id)){
        $default_value=$row->is_recibir;
    }
    $result=array(
        '#type'=>'checkbox',
        '#title'=>t('Enabled'),
        '#default_value'=>$default_value,
    );
    //
    return $result;
}
function get_value_alerta_personal_recibir($is_recibir){
    if($is_recibir){
        return t('Yes');
    }
    return t('No');
}
function my_upper($param){
    $result=strtoupper($param);
    $result=pasar_acentos_mayusculas($result);
    return $result;
}
function pasar_acentos_mayusculas($cadena){
    //return strtr($cadena,"áéíóúñ","ÁÉÍÓÚÑ");
    return strtr($cadena,"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
}
function is_add_ver_mas_resultados_tr($num_orig,$my_limite){
    //simulatzeko
    //return 1;
    //
    if($num_orig>$my_limite){
        return 1;
    }
    return 0;
}
function add_ver_mas_resultados_tr($param,$tid,$current_type,$grupo_nid){
    $href='';
    $hasi_url='http://'.$_SERVER['HTTP_HOST'].base_path();
    //
    if(is_boletin_grupo_current_type($current_type)){
        $href=$hasi_url.get_path_ver_mas_boletin_grupo($current_type,$grupo_nid);
    }else if($param->tipo=='canal'){
        //gemini-2013
        $grupo_name=get_grupo_name_by_node($param->canal_nid);
        if(!empty($grupo_name)){
            $grupo_name=$grupo_name.'/';
        }
        //
        $href=$hasi_url.$grupo_name.'canales/'.$param->canal_nid;                
    }else if(!empty($param) && $param->tipo=='categoria'){
        //gemini-2013
        $grupo_name=get_grupo_name_by_tid($tid);
        if(!empty($grupo_name)){
            $grupo_name=$grupo_name.'/';
        }
        //
        $href=$hasi_url.$grupo_name.'canales/categorias/'.$tid;        
    }else{
        return '';        
    }    
    $link='<a href="'.$href.'">'.t('See more results').'</a>';
    //print $link.'<BR>';
    //
    $html=array();
    $html[]='<tr style="vertical-align:top;">';
    $html[]='<td colspan="2">'.$link.'</td>';
    $html[]='</tr>';
    return implode('',$html);
}
function is_boletin_grupo_current_type($current_type){
    if(!empty($current_type)){
        $my_array=array('validado','noticia','comment','foros_wikis','idea_opor_proy');
        if(in_array($current_type,$my_array)){
            return 1;
        }
    }
    return 0;
}
function get_path_ver_mas_boletin_grupo($current_type,$grupo_nid){    
    $grupo=node_load($grupo_nid);
    if(isset($grupo->nid) && !empty($grupo->nid)){
        $path='';
        if(strcmp($current_type,'validado')==0){
            $path='/vigilancia/validados';
        }else if(strcmp($current_type,'noticia')==0){            
            $path='/canal-usuarios';
        }else if(strcmp($current_type,'comment')==0){
            $path='/vigilancia/lo-mas-comentado';
        }else if(strcmp($current_type,'foros_wikis')==0){
            $path='/area-debate';
        }else if(strcmp($current_type,'idea_opor_proy')==0){
            $path='/ideas';
        }else{
            return '';
        }
        return $grupo->purl.$path;
    }
    return '';
}
//intelsat-2015
//function alerta_link_notify($uid, $nid, $destination = NULL) {
function alerta_link_notify($uid, $nid, $destination = NULL,$is_alerta_usuario=0,$canal_usuario_uid=''){
  global $user,$base_url;
  $link='';
  //  
  $node=node_load($nid);
  if(isset($node->nid) && !empty($node->nid)){
     //
  }else{
     return ''; 
  }    
  //
  $options = array();
  $options['html']=TRUE;
  if ($destination){
    $options['query'] = array('destination' => $destination);    
  }
  if($is_alerta_usuario){
    $result=alerta_inc_get_alerta_usuario_array($uid,$canal_usuario_uid);  
  }else{
    $result=get_alerta_canal_list_by_uid($uid,$nid);
  }  
  if (count($result)<1) {
    //intelsat-2015
    //$label=t('Subscribe to this channel');
    $label=t('Add Alert');
    $html = '<span class="suscripcion">';
    //$icono_name='subscribe';
    $icono_name='alerta20';
    //$icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/'.$icono_name.'.png';
    $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/icons/'.$icono_name.'.png';
    $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';       
    $html .= l($img, 'alerta_user/add_alerta_canal/'.$nid, $options);
    $html .= '</span>';
    return $html;;
  }
  else {
    //intelsat-2015  
    //$label=t('Unsubscribe from this channel');  
    $label=t('Delete Alert');
    $id=$result[0]->id;
    $html = '<span class="desuscripcion">';
    //$icono_name='de-subscribe';
    $icono_name='alerta20';
    //$html .=  l(t('Des-suscribirse de este canal'), 'alerta_user/'.$id.'/my_edit', $options);
    //$icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/'.$icono_name.'.png';      
    $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/icons/'.$icono_name.'.png';
    $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';   
    $html .=  l($img, 'alerta_user/'.$nid.'/my_desuscribir', $options);
    $html .= '</span>';
    return $html;
  }
}
function get_alerta_canal_list_by_uid($uid,$nid){
    $where=array();
    $where[]="1";
    $where[]="au.tipo='canal'";
    $where[]="au.uid=".$uid;
    $todos_canal_nid_list=get_todos_canal_nid_list();
    //gemini-2014    
    $or_array=array();
    $or_array[]="(ac.canal_nid=".$nid.')';
    if(count($todos_canal_nid_list)>0){   
        $or_array[]="(au.is_todos_canales=1 AND ".$nid." IN(".implode(',',$todos_canal_nid_list)."))";
    }
    $canales_que_filtro=alerta_get_canales_que_filtro_nid_array('',$uid);
    if(count($canales_que_filtro)){
        $or_array[]="(au.is_canales_que_filtro=1 AND ".$nid." IN(".implode(',',$canales_que_filtro)."))";
    }
    $where[]='('.implode(' OR ',$or_array).')';
    //
    $sql="SELECT au.*
    FROM {alerta_user} au
    LEFT JOIN {alerta_user_canales} ac ON au.id=ac.alerta_user_id
    WHERE ".implode(" AND ",$where)."
    GROUP BY au.id
    ORDER BY au.id ASC";
    //
    //print $sql;
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_todos_canal_nid_list(){
    $result=array();
    $is_anonimo=0;
    $canal_list=get_canal_list($is_anonimo,$uid);
    if(count($canal_list)>0){
        foreach($canal_list as $i=>$c){
            $result[]=$c->nid;
        }
    }
    return $result;
}
function is_desde_pantalla_canales(){
    if(strcmp(arg(0),'alerta_user')==0 && strcmp(arg(1),'add_alerta_canal')==0 && is_numeric(arg(2))){
        return 1;
    }
    return 0;
}
function set_default_value_canales($canales){
    if(is_desde_pantalla_canales()){
        $result=array();
        $result[]=arg(2);
        //echo print_r($result,1);exit();
        return $result;
    }
    //echo print_r($canales,1);exit();
    return $canales;
}
function desuscribir_alerta_canal_form(){
    global $user;
    //
    $form['my_canal_id']=array(
            '#type'=>'hidden',
            '#default_value' =>arg(1),
        );
    //
    $form['borrar_msg']=array(
        '#value'=>t('This action cannot be undone.').'<BR>',
    );

    $form['my_table']['#value']=get_des_suscribir_table($user->uid,arg(1));

    $form['alerta_canal_btn_submit']=array(
  '#type' => 'submit',
  '#value' => t('Unsubscribe'),
);

$form['alerta_canal_volver']=array(
  //'#value' => l(t('Return'),$_REQUEST['destination'],array('attributes'=>array('id'=>'my_volver_alerta_user','class'=>'back_left'))),
  '#value' => l(t('Cancel'),$_REQUEST['destination']),


);

    return $form;
}
function desuscribir_alerta_canal_form_submit(&$form, &$form_state) {
    global $user;
    $uid=$user->uid;
    $canal_nid=$form_state['values']['my_canal_id'];
    $result=get_alerta_canal_list_by_uid($uid,$canal_nid);
    if(count($result)>0){
        foreach($result as $i=>$row){
            $user_canales_list=get_alerta_user_canal_nid_array($row->id,$row->is_todos_canales);
            if($row->is_todos_canales || $row->is_canales_que_filtro){
                if($row->is_todos_canales){
                    if(count($user_canales_list)>0){
                         /*if(count($user_canales_list)==1){
                             delete_alerta_user($row->id);
                         }else{*/
                             update_alerta_user_is_todos_canales($row->id,0);
                             my_delete_alerta_user_canales($row->id);
                             foreach($user_canales_list as $k=>$konp_nid){
                                 if($canal_nid!=$konp_nid){
                                     insert_alerta_user_canales($row->id, $konp_nid);
                                 }
                             }
                         //}
                     }
                }
                //
                if($row->is_canales_que_filtro){
                    $canales_que_filtro=alerta_get_canales_que_filtro_nid_array($row->id);
                    if(count($canales_que_filtro)>0){
                             alerta_update_is_canales_que_filtro($row->id,0);
                             my_delete_alerta_user_canales($row->id);
                             foreach($canales_que_filtro as $a=>$konp_nid){
                                 if($canal_nid!=$konp_nid){
                                     insert_alerta_user_canales($row->id, $konp_nid);
                                 }
                             }                         
                     }
                }
            }else{
                //echo print_r($user_canales_list,1);
                if(count($user_canales_list)>0){
                    if(count($user_canales_list)==1){                        
                        delete_alerta_user($row->id);
                    }else{
                        foreach($user_canales_list as $b=>$konp_nid){
                            if($canal_nid==$konp_nid){
                                my_delete_alerta_user_canales($row->id,$konp_nid);
                            }
                        }
                    }
                }
            }
        }
    }
    //exit();
}
function update_alerta_user_is_todos_canales($id,$is_todos_canales){
    $sql='UPDATE alerta_user SET is_todos_canales='.$is_todos_canales.' WHERE id='.$id;
    db_query($sql);
}
function get_des_suscribir_table($uid,$canal_nid){
    $result=get_alerta_canal_list_by_uid($uid,$canal_nid);
    $my_limit=100;
    $output='';
    if(count($result)>0){        
        $is_mis_alertas=0;
        $headers=get_alerta_user_headers($is_mis_alertas);
        $rows=array();
        $output='<b>'.t('To cancel the selected channel in the following alerts').':</b>';
        foreach($result as $i=>$r){
            $rows[$i]=get_alerta_user_table_row($r, $is_mis_alertas);
        }
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);


      if (count($rows)>0) {
        /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
        drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
      }
      else {

        $output.= '<div id="first-time">' .t('There are no custom alerts for the selected channel'). '</div>';
      }
      return $output;
}
function get_alerta_user_headers($is_mis_alertas=1){
    $headers=array();
    //$headers[0]=t('Name');
    $headers[0]=array('data'=>t('Name'),'field'=>'name');
    $headers[1]=t('Method');
    $headers[2]=t('Frequency');
    $headers[3]=t('Time');
    $headers[4]=t('Creation date');
    $headers[5]=t('Type');
    //$headers[6]=t('Creator');
    $headers[6]=t('Status');
    if($is_mis_alertas){
        $headers[7]=t('Actions');
    }
    unset($headers[1]);
    unset($headers[4]);
    $headers=array_values($headers);
    return $headers;
}
function get_alerta_user_table_row($r,$is_mis_alertas=1){
      $row=array();
      $row[0]=get_alerta_user_nombre($r);
      $row[1]=get_metodo_name($r->send_method);
      //intelsat-2015
      $row[2]=alerta_inc_get_frecuencia_label($r->frecuencia);
      $row[3]=$r->hora;
      $row[4]='';
      if(!empty($r->created)){
        $row[4]=date('Y-m-d',$r->created);
      }
      //gemini-2013
      //$row[5]=$r->tipo;
      $row[5]=set_t_alerta_tipo($r->tipo);
      //$row[6]=my_get_username($r->uid);
      //$row[7]=get_value_alerta_personal_recibir($r->is_recibir);
      $row[6]=alerta_get_img_alerta_personal_recibir($r->is_recibir);
      if($is_mis_alertas){
        $row[7]=get_acciones_alerta_user($r);
      }
      unset($row[1]);
      unset($row[4]);
      $row=array_values($row);
      return $row;
}
function is_alerta_user($type=''){
    if(strcmp(arg(0),'alerta_user')==0){
        return 1;        
    }
    if(strcmp(arg(0),'boletin_grupo')==0){
        return 1;
    }    
    if(is_crear_boletin_grupo('boletin-grupo-introduccion')){
        return 1;
    }
    if(is_crear_boletin_grupo('boletin-grupo-despedida')){
        return 1;
    }
    //gemini-2014
    if(strcmp(arg(0),'alerta')==0){
        return 1;
    }
    if(strcmp(arg(0),'boletin_report')==0){
        if(!is_area_trabajo()){
            return 1;
        }    
    }
    $node=my_get_node();
    if(isset($node->type) && !empty($node->type) && in_array($node->type,array('bulletin_text'))){
        return 1;
    }
    /*
    //intelsat-2016
    if(boletin_report_inc_is_noticia_boletines()){
        return 1;    
    }*/
    //
    return 0;
}
function my_help_alerta_user(){
     if(is_crear_boletin_grupo('boletin-grupo-introduccion')){
         return help_popup_window(17289, 'help',my_get_help_link_object());
     }
     if(is_crear_boletin_grupo('boletin-grupo-despedida')){
         return help_popup_window(17290, 'help',my_get_help_link_object());
     }
    if(strcmp(arg(0),'boletin_grupo')==0){
        if(strcmp(arg(2),'my_edit')==0){
            return help_popup_window(17287, 'help',my_get_help_link_object());
        }
        if(strcmp(arg(2),'my_web')==0){
            return help_popup_window(17288, 'help',my_get_help_link_object());
        }
        if(strcmp(arg(2),'my_no_recibir')==0){
            return help_popup_window(17291, 'help',my_get_help_link_object());
        }
         return help_popup_window(17286, 'help',my_get_help_link_object());
    }


    if(strcmp(arg(1),'add_alerta_canal')==0){
        return help_popup_window(17282, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(1),'add_alerta_categoria')==0){
        return help_popup_window(17283, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(1),'add_alerta_usuario')==0){
        return help_popup_window(17284, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(1),'mis_boletines_grupo')==0){
        return help_popup_window(17285, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(2),'my_list')==0){
        return help_popup_window(17281, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(2),'my_web')==0){
            return help_popup_window(17292, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(2),'my_edit')==0){
            return help_popup_window(17293, 'help',my_get_help_link_object());
    }
    if(strcmp(arg(2),'my_delete')==0){
            return help_popup_window(17294, 'help',my_get_help_link_object());
    }
    return '';
}
function is_crear_boletin_grupo($konp){
    if(is_node_add($konp)){
        return 1;
    }
    return 0;
}
function my_set_label_notifications_send_methods($options_in){
    $options=$options_in;
    if(isset($options['mimemail'])){
        $options['mimemail']=t('HTML Email');
    }
    if(isset($options['simple'])){
        $options['simple']=t('Website');
    }
    if(isset($options['mail'])){
        $options['mail']=t('Email');
    }
    return $options;
}
function is_invitado_de_mi_subgrupo($uid){
    global $user;
    //
    if($user->uid==$uid){
        return 1;
    }
    //
    $my_user=user_load($uid);
    if(!is_user_invitado()){
        if(is_user_invitado($my_user)){
            //print $my_user->name.'<BR>';
            $idea_nid_array=get_user_and_invitado_idea_nid_array($user->uid,$my_user->uid,$user->og_groups);
            if(count($idea_nid_array)>0){
                return 1;
            }
            //
            $oportunidad_nid_array=get_user_and_invitado_oportunidad_nid_array($user->uid,$my_user->uid,$user->og_groups);
            if(count($oportunidad_nid_array)>0){
                return 1;
            }
            //
            $proyecto_nid_array=get_user_and_invitado_proyecto_nid_array($user->uid,$my_user->uid,$user->og_groups);
            if(count($proyecto_nid_array)>0){
                return 1;
            }
            //
            return 0;
        }
        return 1;
    }else{
            $idea_nid_array=get_user_and_invitado_idea_nid_array($my_user->uid,$user->uid,$user->og_groups);
            if(count($idea_nid_array)>0){
                return 1;
            }
            //
            $oportunidad_nid_array=get_user_and_invitado_oportunidad_nid_array($my_user->uid,$user->uid,$user->og_groups);
            if(count($oportunidad_nid_array)>0){
                return 1;
            }
            //
            $proyecto_nid_array=get_user_and_invitado_proyecto_nid_array($my_user->uid,$user->uid,$user->og_groups);
            if(count($proyecto_nid_array)>0){
                return 1;
            }
            //
            return 0;
    }
    return 0;
}
function alerta_invitado_form(){
    $row=get_alerta_user_row(arg(1));
//echo print_r($row,1);
    if(isset($row->id) && !empty($row->id)){
        $form['my_id']=array(
            '#type'=>'hidden',
            '#default_value' =>$row->id,
        );
    }
    
$form['alerta_user_menu']=array();
$form['alerta_user_menu']['#value']=create_menu_alerta_user();    
    
$form['titulo']=array(
    '#type'=>'textfield',
    '#title'=>t('Title'),
    '#default_value'=>$row->titulo,
    '#required'=>TRUE,
);


//
$form['is_recibir']=create_is_recibir_txek($row);
//
$form=add_alerta_invitado_txek($form,$row);
//
$form['send_method'] = array(
        '#type' => 'select',
        '#title' => t('Select Sending Method'),
        '#options' => my_get_methods(),
        '#default_value' => $row->send_method,
        '#required' => TRUE,
         //intelsat-2015
        '#prefix'=>'<div style="display:none;">',
        '#suffix'=>'</div>',
        //
      );
$form['frecuencia'] = array(
        '#type' => 'select',
        '#title' => t('Frequency'),
        '#options' => get_frecuencia_options(),
        '#default_value' => $row->frecuencia,
        '#required' => TRUE
      );

$form['hora_array'] = array(
      '#type' => 'alerta_time',
        '#title' => t('Time'),
        '#default_value' => $row->hora,
        '#required' => TRUE
);

//$form=add_form_filtrar_fields($form,$row);

$form['my_limite']=array(
    '#type'=>'textfield',
    '#title'=>t('Limit'),
    '#default_value'=>set_alerta_user_my_limite($row->my_limite),
);

$form['limite_resumen']=alerta_limite_resumen_form_field($row);

$form['limite_resumen_comentario']=alerta_limite_resumen_comentario_form_field($row);

$form['alerta_categoria_btn_submit']=array(
  '#type' => 'submit',
  '#value' => t('Save'),
);


if(isset($row->id)){
    $form['delete_alerta_invitado_btn']=array(
      '#value' => '<input type="button" id="my_delete_alerta_btn" name="my_delete_alerta_btn" value="'.t('Delete').'">',
    );
}

add_js_alerta_delete_btn($row);

$form['alerta_invitado_volver']=array(
  //'#value' => l(t('Return'),$_REQUEST['destination'],array('attributes'=>array('id'=>'my_volver_alerta_user','class'=>'back_left'))),
  '#value' => l(t('Cancel'),$_REQUEST['destination']),

);




add_js_alerta_delete_btn($row);

return $form;
}
function add_alerta_invitado_txek($form_in,$row){
    $form=$form_in;
    //
    $form['respuesta_nid_list']=array(
    //'#title' => t('Associated Challenge'),
    '#value'=>get_respuesta_invitado_html($row),
 );
 return $form;
}
function get_respuesta_invitado_html($row){
    $html=array();
    $html[]='<div id="edit-alerta-respuesta-invitado-wrapper" class="form-item form-item-table">';
    $html[]='<label>'.t('Select answers').': </label>';
    $html[]=get_respuesta_invitado_table($row);
    $html[]='</div>';
    return implode('',$html);
}
function get_respuesta_invitado_table($param){
   global $user;
   $uid=$user->uid;
   //
   $arbol=array();
   $my_padding=50;
   $padding_hasi=0;
   $respuesta_list=get_respuesta_invitado_list();
   if(count($respuesta_list)>0){
       $kont=0;
       foreach($respuesta_list as $i=>$idea){
            //$my_img=get_idea_bombilla_img();
            $arbol[$kont][0]=get_link_camino_html($idea->idea_nid,$padding_hasi,$my_padding,0);
            $invitados_list=get_idea_invitados_list($idea->idea_nid, $uid);
            if(in_invitados_list($invitados_list,$uid)){
                $checked=is_txeked_alerta_invitados('idea',$idea->idea_nid,$param);
                $my_id='idea_'.$idea->idea_nid;
                $arbol[$kont][1]='<input type="checkbox" id="'.$my_id.'" name="idea['.$idea->idea_nid.']" value="1"'.$checked.'/>';
            }else{
                $arbol[$kont][1]='';
            }
            //
            $kont++;
            $oportunidad_list=get_idea_oportunidad_list($idea->idea_nid);
            if(count($oportunidad_list)>0){
                foreach($oportunidad_list as $k=>$oportunidad){
                    //print 'opor='.$oportunidad->nid.'<BR>';
                    $invitados_list=get_oportunidad_invitados_list($oportunidad->nid, $uid);
                    if(in_invitados_list($invitados_list,$uid)){
                        $arbol[$kont][0]=get_link_camino_html($oportunidad->nid,$padding_hasi,$my_padding,1);
                        $checked=is_txeked_alerta_invitados('oportunidad',$oportunidad->nid,$param);
                        $my_id='oportunidad_'.$oportunidad->nid;
                        $arbol[$kont][1]='<input type="checkbox" id="'.$my_id.'" name="oportunidad['.$oportunidad->nid.']" value="1"'.$checked.'/>';
                    }else{
                        if(in_oportunidad_proyecto_invitados_list($oportunidad->nid,$uid)){
                            $arbol[$kont][0]=get_link_camino_html($oportunidad->nid,$padding_hasi,$my_padding,1);
                            $arbol[$kont][1]='';
                        }else{
                            continue;
                        }
                    }
                    $kont++;
                    //
                    $proyecto_list=get_oportunidad_proyecto_list($oportunidad->nid);
                    if(count($proyecto_list)>0){
                        foreach($proyecto_list as $a=>$proyecto){
                            //print 'proy='.$proyecto->nid.'<BR>';
                            $invitados_list=get_proyecto_invitados_list($proyecto->nid, $uid);
                            if(in_invitados_list($invitados_list,$uid)){
                                $arbol[$kont][0]=get_link_camino_html($proyecto->nid,$padding_hasi,$my_padding,2);
                                $checked=is_txeked_alerta_invitados('proyecto',$proyecto->nid,$param);
                                //
                                $my_id='proyecto_'.$proyecto->nid;
                                $arbol[$kont][1]='<input type="checkbox" id="'.$my_id.'" name="proyecto['.$proyecto->nid.']" value="1"'.$checked.'/>';
                                $kont++;
                            }
                        }
                    }
                }
            }
       }
       //$arbol=array_reverse_child($arbol);
       $output=get_respuesta_invitado_theme_table($arbol);
       return $output;
   }
   
   return '';
}
function get_respuesta_invitado_list(){
    global $user;
    //
    $where[]="1";
    $where[]="n.type='idea'";
    //
    $or=array();
    $or[]="ii.uid=".$user->uid;
    $or[]="oi.uid=".$user->uid;
    $or[]="pi.uid=".$user->uid;
    $where[]="(".implode(" OR ",$or).")";
    //
    $sql="SELECT n.nid as idea_nid,ii.uid as idea_uid,oi.uid as oportunidad_uid,pi.uid as proyecto_uid 
    FROM {node} n
    LEFT JOIN {idea} i ON (n.nid=i.nid AND n.vid=i.vid)
    LEFT JOIN {oportunidad} o ON i.nid=o.idea_nid
    LEFT JOIN {proyecto} p ON o.nid=p.oportunidad_nid
    LEFT JOIN {idea_invitados} ii ON i.nid=ii.idea_nid
    LEFT JOIN {oportunidad_invitados} oi ON o.nid=oi.oportunidad_nid
    LEFT JOIN {proyecto_invitados} pi ON p.nid=pi.proyecto_nid
    WHERE ".implode(" AND ",$where)."
    GROUP BY n.nid";
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_respuesta_invitado_theme_table($arbol_in){
  $arbol=$arbol_in;
  //$is_reverse=0;
  $is_reverse=1;
  //
  if($is_reverse){
      $arbol=array_reverse_child($arbol);
  }
  //
  $my_limit=variable_get('default_nodes_main', 1000);
  $rows=my_set_estrategia_pager($arbol,$my_limit);
  $output='';
  //
  if(count($rows)>0){
        $respuesta_header=t('Ideas').get_menu_gezia_img(2).t('Opportunities').get_menu_gezia_img(2).t('Projects').get_menu_gezia_img(2);
        if($is_reverse){
            $headers=array('',$respuesta_header);
        }else{
            $headers=array($respuesta_header,'');
        }
        //$headers=array($respuesta_header);
        //
        $output .= theme('table',$headers,$rows);

	//print 'pager='.variable_get('default_nodes_main', 10).'<BR>';
	$output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output = '<div id="first-time">' .t('There are no proposals'). '</div>';
  }

  return $output;
}
function in_invitados_list($invitados_list,$uid){
    if(count($invitados_list)>0){
        foreach($invitados_list as $i=>$row){
            //print $row->uid.'='.$uid.'<BR>';
            if($row->uid==$uid){
                return 1;
            }
        }
    }
    return 0;
}
function in_oportunidad_proyecto_invitados_list($oportunidad_nid,$uid){
    $where=array();
    $where[]="1";
    $where[]="p.oportunidad_nid=".$oportunidad_nid;
    $where[]="pi.uid=".$uid;
    //
    $sql="SELECT p.nid
    FROM {proyecto} p
    LEFT JOIN {proyecto_invitados} pi ON p.nid=pi.proyecto_nid
    WHERE ".implode(" AND ",$where)."
    GROUP BY p.nid";
    //
    $result=array();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    if(count($result)>0){
        return 1;
    }
    return 0;
}
function array_reverse_child($arbol){
    $result=array();
    if(count($arbol)>0){
        foreach($arbol as $i=>$row){
            //echo print_r($row,1);
            $v=array_reverse($row);
            $result[]=$v;
            //$result[$i][0]=implode('',$v);
        }
    }
    return $result;
}
function alerta_invitado_form_submit(&$form, &$form_state) {
    save_alerta_user($form, $form_state,'invitado','');
}
function save_alerta_user_invitados_respuesta($my_id,$is_delete=0){
    /*$_POST['idea']
    $_POST['oportunidad'],1);
    $_POST['proyecto']*/
    $my_array=array('idea','oportunidad','proyecto');
    if(count($my_array)>0){
        foreach($my_array as $i=>$node_type){
            delete_alerta_user_invitados_respuesta($my_id,$node_type);
            if(!$is_delete){
                insert_alerta_user_invitados_respuesta($my_id,$node_type);
            }
        }
    }
}
function delete_alerta_user_invitados_respuesta($my_id,$node_type){
    $sql='DELETE FROM alerta_user_invitados_'.$node_type.' WHERE alerta_user_id='.$my_id;
    db_query($sql);
}
function insert_alerta_user_invitados_respuesta($my_id,$node_type){
    if(isset($_POST[$node_type]) && !empty($_POST[$node_type])){
        $my_array=$_POST[$node_type];
        foreach($my_array as $key=>$v){
            $sql='INSERT INTO alerta_user_invitados_'.$node_type.'(alerta_user_id,'.$node_type.'_nid) VALUES('.$my_id.','.$key.')';
            db_query($sql);
        }
    }
}
function call_delete_alerta_user_invitados_respuesta($id){
    $is_delete=1;
    save_alerta_user_invitados_respuesta($id,$is_delete);
}
function is_txeked_alerta_invitados($node_type,$nid,$param){
    if(isset($param->id) && !empty($param->id)){
        $respuesta_list=get_alerta_invitados_respuesta_array($node_type,$nid,$param->id);
        if(count($respuesta_list)>0){
            return ' checked="checked"';
        }
    }
    return '';
}
function get_alerta_invitados_respuesta_array($node_type,$nid='',$id=''){
    $result=array();
    //
    $where=array();
    $where[]='1';
    if(!empty($id)){
        $where[]='i.alerta_user_id='.$id;
    }
    if(!empty($nid)){
        $where[]='i.'.$node_type.'_nid='.$nid;
    }
    //
    $sql='SELECT i.*
    FROM {alerta_user_invitados_'.$node_type.'} i
    WHERE '.implode(' AND ',$where).'
    ORDER BY i.id ASC';
    //print $sql;exit();
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function set_invitados_legible($id,$sep,$with_img=1){
    $result=array();
    //
    $my_array=array('idea','oportunidad','proyecto');
    foreach($my_array as $i=>$node_type){
        $my_list=get_alerta_invitados_respuesta_array($node_type,'',$id);
        //echo print_r($my_list,1);
        if(count($my_list)>0){
            foreach($my_list as $i=>$r){
                $field=$node_type.'_nid';
                $node=node_load($r->$field);
                if(isset($node->nid) && !empty($node->nid)){
                    $img='';
                    if($with_img){
                        $img=get_image_node_type($node_type);
                    }
                    $result[]=$img.$node->title;
                }
            }
        }
    }
    //
    /*$sep=',';
    $sep='<BR>';*/
    return implode($sep,$result);
}
function get_image_node_type($node_type){
    $img='';
    if($node_type=='idea'){
        $img=get_idea_bombilla_img();
    }else if($node_type=='oportunidad'){
        $img=get_oportunidad_simbolo_img();
     }else if($node_type=='proyecto'){
        $img=get_proyecto_simbolo_img();
    }
    //
    return $img;
}
function call_alerta_invitado($row_in,$nombre_dia,$user_mail,$por_correo=1){
    $content='';
    $current_type='invitado';
    $row=$row_in;
        
    
    $nid_list=get_alerta_user_invitado_respuesta_nid_array($row->id);
    //echo print_r($usuarios,1);
    $my_message='';
    //echo print_r($nid_list,1);exit();
    //gemini-2013
    $hay_novedades=0;
    if(count($nid_list)>0){
        foreach($nid_list as $k=>$alerta_nid){
            $title='';
            $node=node_load($alerta_nid);
            if(isset($node->uid) && !empty($node->nid)){
                $title=': '.$node->title;
            }
            $titulo_mail=t('Guest customised alert').$title;
            $titulo_mail=str_replace(':','',$title);
            //
                $row->alerta_invitado_nid=$alerta_nid;
                $alerta_invitado_list=get_alerta_invitado_list_by_row($row,$nombre_dia,$my_message);
                if(count($alerta_invitado_list)>0){
                    $my_limite=get_alerta_my_limite($row->my_limite);
                    //$alerta_usuario_list=array_slice($alerta_usuario_list,0,$my_limite);
                    //gemini-2013
                    $content.=create_boletin_mail_html($alerta_invitado_list,$current_type,$hay_novedades_out,$my_limite,$titulo_mail,$row);
                    if($hay_novedades_out){
                        $hay_novedades=1;
                    }
                    
                    /*if($por_correo){
                        if(in_array($row->send_method,array('mail','mimemail'))){
                            my_send_mail($user_mail,$titulo_mail, $content,$row->send_method);
                        }
                    }*/
                }else{
                    //intelsat-2015
                    if(!red_despacho_is_activado()){
                        $content.=no_hay_novedades_html($current_type, $titulo_mail,$my_message,$row);
                    }    
                }
        }
    }
    $with_img=0;
    //$subject=t('Guest customised alert').': '.get_alerta_user_nombre($row,',',$with_img);
    $alerta_user_title=get_alerta_user_nombre($row,',',$with_img);
       if(!empty($alerta_user_title)){
           $subject=$alerta_user_title;
       }else{
           $subject=t('Guest customised alert');
       }
    $content=add_introduccion_despedida_html($content,'',$row->uid,$subject,$row);
    //intelsat-2015
    $content=red_despacho_boletin_report_get_current_content($content, $subject,'');

    if(!empty($content) && $por_correo){
        if(in_array($row->send_method,array('mail','mimemail'))){
            //gemini-2013
            if($hay_novedades){
                my_send_mail($user_mail,$subject, $content,$row->send_method,$row->is_recibir);
            }    

        }
    }else{
        $content=get_content_by_my_message($content,$por_correo,$my_message);
    }
    if(strcmp($row->tipo,'invitado')==0){
        save_aviso_procesado($row, $por_correo, $my_message, 'alerta_user');
    }
    return $content;
}
function get_alerta_user_invitado_respuesta_nid_array($id){
    $result=array();
    $my_array=array('idea','oportunidad','proyecto');
    foreach($my_array as $i=>$node_type){
        $my_list=get_alerta_invitados_respuesta_array($node_type,'',$id);
        //echo print_r($my_list,1);
        if(count($my_list)>0){
            foreach($my_list as $i=>$r){
                $field=$node_type.'_nid';
                $result[]=$r->$field;
            }
        }
    }
    return $result;
}
function get_alerta_invitado_list_by_row($param,$nombre_dia,&$my_message){
    $result=array();

    if(strcmp($param->tipo,'invitado')==0 && is_in_frecuencia($param->frecuencia,$nombre_dia,$param->hora,$param,'alerta_user',$my_message)){
        /*$canal=node_load($param->alerta_);
        $canal_nid_list=get_canal_node_nid_list($canal);
        //print $canal->title.'='.count($canal_nid_list).'<BR>';
        if(count($canal_nid_list)>0){*/
        //echo print_r($param,1);exit();
        $nid_array=get_alerta_invitado_nid_array($param->alerta_invitado_nid,$param->uid);
            //
            $where=array();
            $where[]="1";
            $where[]=get_where_intervalo($param->frecuencia);
            $where[]="an.nid IN (".implode(",",$nid_array).")";
            //
            $sql="SELECT an.*
            FROM {alerta_notif} an
            WHERE ".implode(" AND ",$where)."
            ORDER BY an.fecha ASC";
            //
            $res=db_query($sql);
            while($row=db_fetch_object($res)){
                $result[]=$row;
            }
            $result=repasar_filtrado($result,$param);
        }
        //print $canal->title.'='.$param->frecuencia.'='.count($result).'<BR>';
    //}

    return $result;
}
function get_alerta_invitado_nid_array($nid,$param_uid){
    $node=node_load($nid);
    $result=array();
    $result[0]=$nid;
    if(isset($node->nid) && !empty($node->nid)){
        if($node->type=='idea'){
            $oportunidad_list=get_idea_oportunidad_list($nid);
            if(count($oportunidad_list)>0){
                foreach($oportunidad_list as $i=>$oportunidad){
                    if(!in_array($oportunidad->nid,$result)){
                        //$num=count($result);
                        if(in_invitados_nid($oportunidad->nid,$param_uid)){
                            $result[]=$oportunidad->nid;
                        }
                    }
                    //
                    $result=get_proyecto_invitado_nid_array($result,$oportunidad->nid,$param_uid);
                }
            }
        }else if($node->type=='oportunidad'){
            $result=get_proyecto_invitado_nid_array($result,$nid);
        }
    }
    return $result;
}
function get_proyecto_invitado_nid_array($result_in,$oportunidad_nid,$param_uid){
    $result=$result_in;
    $proyecto_list=get_oportunidad_proyecto_list($oportunidad_nid);
    if(count($proyecto_list)>0){
        foreach($proyecto_list as $i=>$p){
             if(!in_array($p->nid,$result)){
                //$num=count($result);
                if(in_invitados_nid($p->nid,$param_uid)){
                    $result[]=$p->nid;
                }
             }
        }
    }
    return $result;
}
function in_invitados_nid($nid,$param_uid){
    //AVISO::::si dentro no es necesario que esté el invitado si en un padre/madre estándolo
    //return 1;
    //
    if(empty($param_uid)){
        return 0;
    }
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        $invitados_list=array();
        if($node->type=='idea'){
            $invitados_list=get_idea_invitados_list($nid,$param_uid);
        }else if($node->type=='oportunidad'){
            $invitados_list=get_oportunidad_invitados_list($nid,$param_uid);
        }else if($node->type=='proyecto'){
            $invitados_list=get_proyecto_invitados_list($nid,$param_uid);
        }       
        if(count($invitados_list)>0){
            return 1;
        }
    }
    return 0;
}
function get_invitado_respuesta_content($bg,&$my_message,&$is_no_hay,$my_user=''){
    $is_no_hay=0;
    if($bg->is_todos_idea_opor_proy){
        $idea_opor_proys=get_grupo_rows($bg,'idea_opor_proy');
        //print 'num='.count($idea_opor_proys).'<BR>';
        if(!empty($my_user) && isset($my_user->uid) && !empty($my_user->uid)){
            if(is_user_invitado($my_user)){
                $idea_opor_proys=prepare_estoy_invitado($idea_opor_proys,$my_user->uid);
                //print 'num2='.count($idea_opor_proys).'<BR>';
            }
        }
                    //gemini-2013
                    $content=create_boletin_mail_html($idea_opor_proys,'idea_opor_proy',$hay_novedades_out,$bg->limite_todos_idea_opor_proy, '', '', '',$bg->grupo_nid,$bg->limite_resumen,$bg);
                    
        
                    if(!empty($content)){
                        /*$html_body[]=$content;
                        $respuesta_content=$content;*/
                        return $content;
                    }else{
                        $no_hay=no_hay_novedades_html('idea_opor_proy', '',$my_message);
                        /*$html_body[]=$no_hay;
                        $respuesta_content=$no_hay;*/
                        //gemini-2013
                        $is_no_hay=1;
                        return $no_hay;

                    }
    }
    return '';
}
function prepare_estoy_invitado($my_list,$uid){
    $result=array();
    if(count($my_list)>0){
        foreach($my_list as $i=>$row){
            if(in_invitados_nid($row->nid,$uid)){
                $result[]=$row;
            }
        }
    }
    return $result;
}
function get_where_alerta_fecha($info){
    /*if(isset($info->frecuencia) && !empty($info->frecuencia)){
        $where=array();
        $konp=0;
        $now=time();
        //print $info->frecuencia.'<BR>';
        $dias=7;
        if($info->frecuencia=="diaria"){
            $dias=1;
            //$dias=7;
            //$dias=14;
        }else if(is_semanal($info->frecuencia)){
            $dias=7;
            //print 'dias='.$dias.'<BR>';
        }
        $konp=$now-($dias*24*60*60);
        //$konp=$now;        
        if($konp>0){            
            $where[]="an.fecha>=".$konp;
        }
        return "(".implode(" AND ",$where).")";
    }*/
    $where=get_where_intervalo($info->frecuencia);
    if(!empty($where)){
        return $where;
    }
    return "1";
}
function is_semanal($frecuencia){
    if(in_array($frecuencia,array("semanal_lunes","semanal_martes","semanal_miercoles","semanal_jueves","semanal_viernes","semanal_sabado","semanal_domingo"))){
        return 1;
    }
    return 0;
}
function is_mail_caducado($mail_to,$is_mensaje_despedida=0){
    if(!$is_mensaje_despedida){
        $user_list=get_user_list_by_mail($mail_to);
        if(count($user_list)>0){
            foreach($user_list as $i=>$u){
                if(is_user_demo_caducado($u->uid)){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function get_user_list_by_mail($mail_to){
    $result=array();
    if(!empty($mail_to)){
        $where=array();
        $where[]='1';
        $where[]='mail="'.$mail_to.'"';
        //
        $sql='SELECT u.* FROM {users} u WHERE '.implode(' AND ',$where);
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $result[]=$row;
        }
    }
    return $result;
}
function send_mensaje_despedida_usuario_demo_caducado(){
    $user_admin=user_load(1);
    $user_list=my_get_user_demo_caducado_list();
    if(count($user_list)>0){
        foreach($user_list as $i=>$u){
            if(!is_mensaje_despedida_enviado($u->uid)){
                $mail_to=$u->mail;
                //simulando
                //$mail_to='bulegoa@netkam.com';
                $info_body=get_subject_body_mensaje_despedida($u);
                $subject=$info_body['subject'];
                $body=$info_body['body'];
                $send_method='mimemail';
                $alert_type='';
                $is_mensaje_despedida=1;
                my_call_send_mail($mail_to,$subject,$body,$send_method,$alert_type,$is_mensaje_despedida);
                //al admin tambien se envia
                if(isset($user_admin->uid) && !empty($user_admin->uid)){
                    $mail_to_admin=$user_admin->mail;
                    //simulando
                    //$mail_to_admin='bulegoa@netkam.com';
                    my_call_send_mail($mail_to_admin,$subject,$body,$send_method,$alert_type,$is_mensaje_despedida);
                    //print 'mail_to_admin='.$mail_to_admin.'<BR>';
                }
                insert_mensaje_despedida_usuario_demo_caducado($u->uid);
            }
        }
    }
}
function is_mensaje_despedida_enviado($uid){
    if(empty($uid)){
        return 1;
    }
    $where=array();
    $where[]='1';
    $where[]='e.uid='.$uid;
    $sql='SELECT e.* FROM {mensaje_despedida_enviado} e WHERE '.implode(' AND ',$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return 1;
    }
    //
    return 0;
}
function insert_mensaje_despedida_usuario_demo_caducado($uid){
    $sql='INSERT INTO mensaje_despedida_enviado(uid) VALUES('.$uid.')';
    db_query($sql);
}
function get_subject_body_mensaje_despedida($u){
$result="";
$my_user=user_load($u->uid);
$nombre=$my_user->profile_nombre.' '.$my_user->profile_apellidos;
$code="en";
if(isset($my_user->language) && !empty($my_user->language)){
    $code=$my_user->language;
}
//
$subject=array();
$subject["en"]="End of trial period of Hontza Online";
$subject["es"]="Fin del periodo de prueba de Hontza Online";
//
$my_array=array();
$my_array["es"]="Estimado/a ".$nombre.",

Ha finalizado el plazo de 15 dias de prueba de Hontza Online, por lo que tu cuenta en Hontza ha quedado desactivada.

A partir de este momento  no recibirás más Boletines de grupo ni Alertas personalizadas procedentes de ".my_create_link("http://www.hontza.es")."

Esperamos que durante estos días hayas podido comprobar el funcionamiento de Hontza y que te haya parecido de interés.

Te recordamos que, si lo deseas, puedes contratar varios servicios de Hontza en ".my_create_link("http://www.hontza.es/es/node/7")."

También puedes descargar Hontza en ".my_create_link("http://www.hontza.es/es/node/6")."

Si deseas colaborar con Hontza, puedes hacerlo de muchas maneras: ".my_create_link("http://www.hontza.es/es/node/42")."

Si tienes cualquier duda, comentario o sugerencia puedes enviar un correo a ".my_create_link("hontza@hontza.es","mailto:hontza@hontza.es")."

Saludos
Equipo Hontza";
//--------------------------------
$my_array["en"]="Dear ".$nombre.",

It has finished the 15 days trial period of Hontza Online, so your Hontza account has been disabled.

From now on, you won't receive any more Group bulletins nor Customised alerts from ".my_create_link("http://www.hontza.es")."

We hope that during those days you have had the opportunity to test Hontza and that it be of your interest.

We would like to remind you that, if you like, you can ask for different Hontza Services at ".my_create_link("http://www.hontza.es/es/node/90")."

Also you can download Hontza using this form ".my_create_link("http://www.hontza.es/es/node/89")."

If you like to collaborate with Hontza, you can do it in many ways: ".my_create_link("http://www.hontza.es/es/node/91")."

If you have any doubt, comment or suggestion you can send a message to ".my_create_link("hontza@hontza.es","mailto:hontza@hontza.es")."

Best regards
Hontza Team";
//
$info_body=array();
$result=$my_array["en"];
$info_body["subject"]=$subject["en"];
if(isset($my_array[$code]) && !empty($my_array[$code])){
    $result=$my_array[$code];
    $info_body["subject"]=$subject[$code];
}
$info_body["body"]=nl2br($result);
//
return $info_body;
}
function my_create_link($text,$url_in=''){
    $url=$url_in;
    if(empty($url)){
        $url=$text;
    }
    return l($text,$url);
}
function add_js_alerta_delete_btn($r){
    $atzizkia='';
    if(in_array($r->tipo,array('categoria','usuario','invitado'))){
        $atzizkia='_'.$r->tipo;
    }
    //gemini-2013
    //$url_delete=url('alerta_user/'.$r->id.'/my_delete'.$atzizkia,array('query'=>drupal_get_destination()));
    $url_delete=url('alerta_user/'.$r->id.'/my_delete'.$atzizkia,array('query'=>'desde_edit='.arg(2).'&destination='.define_alerta_form_destination(1)));
    //print $url_delete.'<BR>';

   
		$js='var my_delete_url="'.$url_delete.'";
			$(document).ready(function()
			{
                            $("#my_delete_alerta_btn").click(function()
                            {
                                location.href=my_delete_url;
                            });
                        });';

			drupal_add_js($js,'inline');

}
function delete_alerta_invitado_form(){
    return delete_alerta_form();
}
function delete_alerta_invitado_form_submit(&$form, &$form_state) {
    delete_alerta_form_submit($form, $form_state);
}
//gemini-2013
function set_t_alerta_tipo($alerta_tipo){
    $my_array=array();
    $my_array['canal']='Channel';
    $my_array['categoria']='Category';
    $my_array['usuario']='User';
    //intelsat-2015
    $my_array['busqueda']='Search';
    //
    if(isset($my_array[$alerta_tipo])){
        return t($my_array[$alerta_tipo]);
    }
    return $alerta_tipo;
}
//gemini-2013
function define_alerta_form_destination($is_force=0){
    global $user;
    if(!$is_force && isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        return $_REQUEST['destination'];
    }else{
        return 'alerta_user/'.$user->uid.'/my_list';
    }
    return '';
}
//gemini-2013
function define_alerta_form_cancel_destination(){
    global $user;
    if(isset($_REQUEST['desde_edit']) && !empty($_REQUEST['desde_edit'])){
        return 'alerta_user/'.arg(1).'/'.$_REQUEST['desde_edit'];
    }else{
        return define_alerta_form_destination(1);
    }    
}
//gemini-2013
function alerta_order_by_name($rows){
    $sort='asc';
    $order='name';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    $my_array=$rows;
    if(!empty($my_array)){
        foreach($my_array as $i=>$r){
            $my_array[$i]['name']=strtolower($r[0]);
        }
        $my_array=array_ordenatu($my_array,$order,$sort,0);
        //
        $result=array();
        foreach($my_array as $k=>$row){
            $result[$k]=$row;
            if(isset($result[$k]['name'])){
                unset($result[$k]['name']);
            }
        }       
        return $result;
    }
    return $rows;
}
//gemini-2013
function alerta_is_order_name(){
    return 1;
}
//gemini-2013
function alerta_canal_form_validate(&$form, &$form_state) {
    $is_bg=0;
    alerta_limite_resumen_validate($is_bg,$form,$form_state);
    alerta_limite_resumen_comentario_validate($is_bg,$form,$form_state);
    //intelsat-2015
    alerta_inc_tipo_eventos_validate($is_bg,$form,$form_state);
}
//gemini-2013
function alerta_define_max_limite_resumen(){
    return 500;
}
//gemini-2013
function alerta_limite_resumen_validate($is_bg,&$form, &$form_state) {
    $field_title=t('Abstract Length');
    $field_name='limite_resumen';
    if($is_bg){
        $form_var=$form['limites_fs'][$field_name]; 
        $max=boletin_grupo_define_max_limite_resumen();
    }else{
        $form_var=$form[$field_name];        
        $max=alerta_define_max_limite_resumen();
    }    
    if(isset($form_state['values']) && !empty($form_state['values']) && isset($form_state['values']['limite_resumen']) && !empty($form_state['values']['limite_resumen'])){
        $limite_resumen=$form_state['values']['limite_resumen'];
        if(is_numeric($limite_resumen)){
            if(!$is_bg){
                if($limite_resumen<=$max){
                    //
                }else{                
                    form_error($form_var, $field_title.' '.t('must be less than').' '.$max);
                }
            }    
        }else{
            form_error($form_var, $field_title.' '.t('must be a numeric value'));
        }
    }
}
//gemini-2013
function alerta_limite_resumen_form_field($row){
 $limite_resumen=red_despacho_boletin_report_get_limite_resumen($row,1); 
 $result=array(
              '#type'=>'textfield',
              '#title'=>t('Abstract Length'),
              //'#default_value'=>get_alerta_my_limite($row->limite_resumen,alerta_define_max_limite_resumen()),
              '#default_value'=>$limite_resumen,
              '#required' => TRUE
            );
 return $result;
}
//gemini-2013
function alerta_categoria_form_validate(&$form, &$form_state) {
    $is_bg=0;
    alerta_limite_resumen_validate($is_bg,$form,$form_state);
    alerta_limite_resumen_comentario_validate($is_bg,$form,$form_state);
}
//gemini-2013
function alerta_usuario_form_validate(&$form, &$form_state) {
    $is_bg=0;
    alerta_limite_resumen_validate($is_bg,$form,$form_state);
    alerta_limite_resumen_comentario_validate($is_bg,$form,$form_state);
}
//gemini-2013
function alerta_invitado_form_validate(&$form, &$form_state) {
    $is_bg=0;
    alerta_limite_resumen_validate($is_bg,$form,$form_state);
    alerta_limite_resumen_comentario_validate($is_bg,$form,$form_state);
}
//gemini-2013
function alerta_get_comment_array($nid,$where_in=''){
    $result=array();
    $where=array();
    $where[]='c.nid='.$nid;
    if(!empty($where_in)){
        $where[]='('.$where_in.')';
    }
    $limite_comentarios_alerta=variable_get('limite_comentarios_alerta',10);
    if(empty($limite_comentarios_alerta)){
         $limite_comentarios_alerta=10;
    }    
    $sql='SELECT c.* FROM {comments} c WHERE '.implode(' AND ',$where).' ORDER BY c.timestamp ASC LIMIT 0,'.$limite_comentarios_alerta;
    
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
//gemini-2013
function alerta_node_comments_html($node,$bg='',$limite_resumen_comentario=0,$param_alerta=''){
    $html=array();
    $nid=$node->nid;
    $is_comment=1;
    if(empty($param_alerta)){
        if(empty($bg)){
            $comment_array=alerta_get_comment_array($nid);
        }else{
            $where_intervalo=get_where_intervalo($bg->frecuencia,$is_comment);
            $comment_array=alerta_get_comment_array($nid,$where_intervalo);
        }    
    }else{
        $where_intervalo=get_where_intervalo($param_alerta->frecuencia,$is_comment);
        $comment_array=alerta_get_comment_array($nid,$where_intervalo);
    }
    //
    if(!empty($comment_array)){
       $html[]='<tr>';
       $html[]='<td>';
       $html[]='<table class="mail_table" style="width:100%;border:0px;">';
       $html[]='<tr><th colspan="2"><b>'.t('Comments').'</b></th></tr>';
       if(user_access('root')){
           $html[]='<tr><td>'.comment_render($node,'',1,$bg,$param_alerta).'</td></tr>';
       }else{
        foreach($comment_array as $i=>$row){
            $my_user=user_load($row->uid);
            $is_mail=1;
            $img_style=' style="width:100px;"';
            $img=my_get_user_img_src('',$my_user->picture,$my_user->name,$row->uid,$is_mail,$bg);
            if(empty($img)){
                $img_style='';
            }
            //
            //if(empty($row->uid)){
            //$my_user_info=my_get_user_info($node);
            //$img=$my_user_info['img'];
            //}
            $max_resumen_comentario=0;
            if(empty($bg)){
                 $max_resumen_comentario=$limite_resumen_comentario;                
            }else{
                 if(isset($bg->limite_resumen_comentario) && !empty($bg->limite_resumen_comentario)){
                     $max_resumen_comentario=$bg->limite_resumen_comentario;               
                 }
            } 
            $comment_eskubi_aldea=alerta_comment_eskubi_aldea($row,$node,$max_resumen_comentario,$bg);
            $html[]='<tr style="vertical-align:top;">';
            //intelsat-2015
            //if(!red_despacho_is_activado()){
            if(!empty($img)){
                $html[]='<td'.$img_style.'>'.$img.'</td>';
            }
            $html[]='<td>'.$comment_eskubi_aldea.'</td>';
            $html[]='</tr>';
        }
       } 
       $html[]='</table>';
       $html[]='</td>';
       $html[]='</tr>';       
    }
    return implode('',$html);
}
function alerta_comment_eskubi_aldea($row_in,$node,$max_resumen_in=0,$bg=''){
    //gemini-2014
    $row=clone $row_in;
    $row->comment=alerta_fix_icon_src($row->comment);
    //
    $max_resumen=boletin_grupo_define_max_limite_resumen_comentario();
    if(!empty($max_resumen_in)){
        $max_resumen=$max_resumen_in;
    }
    $apply_limite_resumen_comentario=0;
    if(empty($bg)){
        $apply_limite_resumen_comentario=1;
    }else{
        if(isset($bg->apply_limite_resumen_comentario) && !empty($bg->apply_limite_resumen_comentario)){
            $apply_limite_resumen_comentario=$bg->apply_limite_resumen_comentario;
        }
    }
    if($apply_limite_resumen_comentario){
        //gemini-2014
        $len=strlen($row->comment);
        if($len>$max_resumen){
        //    
            $strip_tags=strip_tags($row->comment);
            $len=strlen($strip_tags);        
            if($len>$max_resumen){
                $comment=drupal_substr($strip_tags, 0, $max_resumen);        
                $comment.=' ...';
            }else{
                $comment=$row->comment;
            }
        }else{
            $comment=$row->comment;
        }
    }else{
        $comment=$row->comment;
    }
    $eskubi_aldea=$row->subject;
    //AVISO::::los comentarios sin título
    $eskubi_aldea='';
    //if(!empty($eskubi_aldea)){
        $html=array();
        $html[]='<table class="mail_table_eskubi_aldea" style="border:0px;">';
        $html[]='<tr><td>'.date('d-m-Y H:i',$row->timestamp).'</td></tr>';
        $html[]='<tr><td><b>'.$eskubi_aldea.'</b></td></tr>';
        $html[]='<tr><td>'.$comment.'</td></tr>';
        /*if(in_array($current_type,array('noticia','comment','foros_wikis','idea_opor_proy','user_canal','user_categoria','user_autor','invitado'))){
            $html[]='<tr><td><i>'.t($row->my_action).'</i></td></tr>';
            //$html[]='<tr><td>'.get_notif_actions_date_html($row->action_date_list).'</td></tr>';
            //$html[]='<tr><td>'.get_notif_actions_html($row->action_list).'</td></tr>';
        }*/
        $html[]='</table>';
        return implode('',$html);
    //}
    return '';
}
//gemini-2013
function alerta_limite_resumen_comentario_form_field($row){
 $limite_resumen_comentario=red_despacho_boletin_report_get_limite_resumen_comentario($row,1);    
 $result=array(
              '#type'=>'textfield',
              '#title'=>t('Comment Length'),
              //'#default_value'=>get_alerta_my_limite($row->limite_resumen_comentario,alerta_define_max_limite_resumen_comentario()),
              '#default_value'=>$limite_resumen_comentario,
              '#required' => TRUE
            );
 return $result;
}
//gemini-2013
function alerta_define_max_limite_resumen_comentario(){
    return 500;
}
//gemini-2013
function alerta_limite_resumen_comentario_validate($is_bg,&$form, &$form_state) {
    $field_title=t('Comment Length');
    $field_name='limite_resumen_comentario';
    if($is_bg){
        $form_var=$form['limites_fs'][$field_name];
        $max=boletin_grupo_define_max_limite_resumen_comentario();
    }else{
        $form_var=$form[$field_name];
        $max=alerta_define_max_limite_resumen_comentario();
    }
    //
    if(isset($form_state['values']) && !empty($form_state['values']) && isset($form_state['values']['limite_resumen_comentario']) && !empty($form_state['values']['limite_resumen_comentario'])){
        $limite_resumen_comentario=$form_state['values']['limite_resumen_comentario'];
        if(is_numeric($limite_resumen_comentario)){
            if(!$is_bg){
                if($limite_resumen_comentario<=$max){
                    //
                }else{
                    form_error($form_var, $field_title.' '.t('must be less than').' '.$max);

                }
            }
        }else{
            
                form_error($form_var, $field_title.' '.t('must be a numeric value'));
                
        }
    }
}
function alerta_get_introduccion_logo_by_subdominio($is_alerta_o_boletin=0,$logo_in=''){
    //intelsat-2015
    global $base_url;
    //intelsat-2016
    $sareko_id=strtolower(_SAREKO_ID);
    //if(_SAREKO_ID=='GESTION_CLAVES'){
    if(red_is_claves_activado()){
        //intelsat-2016
        $filename=strtolower(_SAREKO_ID).'-buho_logo.png';
        if($is_alerta_o_boletin){
            $filename='logo-boletin.png';
        }
        $src=$base_url.'/sites/'._SUBDOMINIO_SITES.'/files/'.$filename;   
    }else if(red_is_rojo()){   
        $src=$base_url.'/sites/'.$sareko_id.'.hontza.es/files/'.$sareko_id.'-buho_logo.png';
    //intelsat-2015
    }else if(red_is_theme_buho_settings_logo_path()){
        if(!empty($logo_in)){            
            $src=$logo_in;            
        }else if(defined('_IS_THEME_LOGO_SETTINGS') && _IS_THEME_LOGO_SETTINGS==1){            
            $theme_buho_settings=variable_get(_THEME_BUHO_SETTINGS,'');
            $src=$theme_buho_settings['logo_path'];
        }else{
            $current_theme=red_movil_get_current_theme();
            //$theme_buho_settings=variable_get('theme_buho_settings','');
            $theme_buho_settings=variable_get('theme_'.$current_theme.'_settings','');
            //intelsat
            $src='';
            if(isset($theme_buho_settings['logo_path'])){
                $src=$theme_buho_settings['logo_path'];
            }
        }
        //intelsat-2016
        if(!@file_exists($src) || red_crear_usuario_is_sites_default_files_buho_logo($src)){
            $src='http://'.$_SERVER['HTTP_HOST'].base_path().'sites/default/files/buho_logo.png'; 
        }else{
            $src=$base_url.'/system/files/'.basename($src);
        }
        //print $src.'<br>';               
    }else if(red_is_servidor_central() || red_funciones_is_sareko_id_alerta()){    
        //gemini-2014
        if(in_array($_SERVER['HTTP_HOST'],array('network.hontza.es'))){
            $src='http://'.$_SERVER['HTTP_HOST'].base_path().'sites/default/files/buho_logo.png';
        }else{
        //    
            $src='http://'.$_SERVER['HTTP_HOST'].base_path().'sites/'.strtolower(_SAREKO_ID).'.hontza.es/files/'.red_get_red_logo_prefijo().'-buho_logo.png';    
        }        
    }else{             
        //$src='http://'.$_SERVER['HTTP_HOST'].base_path().'sites/default/files/buho_logo.png';
        $src=$base_url.'/sites/default/files/buho_logo.png';
    }    
    return $src;
}             
function alerta_get_class_active($type){
    $result=0;
    if($type=='boletin_report'){
        $param0=arg(0);
        if(!empty($param0) && $param0=='boletin_report'){
            $result=1;
        }
    }else if($type=='mis_boletines_grupo'){
        $param0=arg(0);
        if(!empty($param0)){
            if($param0=='alerta_user'){
                $param1=arg(1);
                if(!empty($param1) && $param1=='mis_boletines_grupo'){
                    $result=1;
                }
            }else if($param0=='boletin_grupo'){
                $result=1;
            }else if(is_ficha_node('boletin-grupo-introduccion')){
                $result=1;
            }else if(is_ficha_node('boletin-grupo-despedida')){
                $result=1;
            }
        }
    }else if($type=='my_list'){
        $param0=arg(0);
        if(!empty($param0) && $param0=='alerta_user'){
            $param1=arg(1);
            if(!empty($param1) && is_numeric($param1)){
                $param2=arg(2);
                if(!empty($param2) && $param2=='my_list'){
                    $result=1;
                }
            }
        }
    }else if(in_array($type,array('add_alerta_canal','add_alerta_categoria','add_alerta_usuario','add_alerta_invitado'))){
        $param0=arg(0);
        if(!empty($param0) && $param0=='alerta_user'){
            $param1=arg(1);
            if(!empty($param1) && $param1==$type){
                $result=1;
            }        
        }    
    }else if(in_array($type,array('variables'))){
        $param0=arg(0);
        if(!empty($param0) && $param0=='alerta'){
            $param1=arg(1);
            if(!empty($param1) && $param1==$type){
                $result=1;
            }        
        } 
    }
    if(!empty($result)){
        return ' active';
    } 
    return '';
}
function alerta_resumen_comentario($comment_in,$node,$is_comentario_alerta,$content_in){         
    $comment=$comment_in;
    //gemini-2014
    $content=$content_in;
    $comment=alerta_fix_icon_src($comment);
    $content=alerta_fix_icon_src($content);
    //
        if($is_comentario_alerta){
            $apply_limite_resumen_comentario=$node->apply_limite_resumen_comentario;
            $max_resumen=2000;
            if(isset($node->max_resumen_comentario) && !empty($node->max_resumen_comentario)){
                $max_resumen=$node->max_resumen_comentario;
            }else if(isset($node->comentario_max_resumen) && !empty($node->comentario_max_resumen)){
                $max_resumen=$node->comentario_max_resumen;
            }
            //simulando
            //$apply_limite_resumen_comentario=1;
            //$max_resumen=3;
            //
            if($apply_limite_resumen_comentario){
                //gemini-2014
                $len=strlen($comment);
                if($len>$max_resumen){
                //    
                    $strip_tags=strip_tags($comment);
                    $len=strlen($strip_tags);        
                    if($len>$max_resumen){
                        $comment=drupal_substr($strip_tags, 0, $max_resumen);        
                        $comment.=' ...';
                    }
                }else{
                    return $content;
                }
            }else{                
                return $content;
            }
        }else{
            return $content;
        }
    return $comment;    
}