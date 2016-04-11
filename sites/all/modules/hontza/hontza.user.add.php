<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function my_user_register_form_alter(&$form, &$form_state, $form_id){
    global $user;
    //print $form_id.'<BR>';
    $user_type='';
    if(isset($_GET['user_type']) && !empty($_GET['user_type'])){
        $user_type=$_GET['user_type'];
    }
    //intelsat-2015
    //se ha comentado esto
    /*if(empty($user_type)){
        if(hontza_is_sareko_id('ROOT')){
            if(isset($user->uid) && !empty($user->uid)){
                //
            }else{
                drupal_access_denied();
            }
        }    
    }*/
    //
    if(hontza_is_sin_lenguaje_user_register_url()){
        my_change_language('en');
    }else{
        my_change_language();
    }    
    //
    translate_user_register($form);
    hontza_translate_user_register($form);
    //intelsat-2015
    if(!red_crear_usuario_is_custom_css()){
        $form['account']['mail']['#description']=t("Please enter a valid email address. It won't be made public and it will be used only to validate the access or to send notifications. IMPORTANT: We only accept corporate email (exclude gmail, ymail, hotmail ... etc)");
    }else{
        $form['account']['mail']['#description']=t("Please enter a valid email address. It won't be made public and it will be used only to validate the access or to send notifications.");    
    }
    //
    switch($user_type){
        case 'probar_hontza_online':
            $form['#redirect'] = 'gracias_demo';
            unset_consultores_en_inteligencia_competitiva_fields($form,$user_type);
            break;
        case 'consultores_en_inteligencia_competitiva':
            $form['#redirect'] = 'gracias_consultores_en_inteligencia_competitiva';
            unset_seleccione_especialidades($form);
            $form['#attributes']['enctype']="multipart/form-data";
            $form['Servicios']['profile_servicios_documento']['#type']='file';
            break;
        case 'facilitadores':
            $form['#redirect'] = 'gracias_facilitador';
            unset_consultores_en_inteligencia_competitiva_fields($form,$user_type);
            create_fieldset_seleccione_especialidades($form);
            $form['#attributes']['enctype']="multipart/form-data";
            $form['Servicios']['profile_servicios_documento']['#type']='file';
            break;
        default:            
            my_unset_user_register_fields($form,$user_type);
            translate_user_create_form($form,$form_state,$form_id);
            break;
    }
    if(!empty($user_type)){
        $form['user_type'] = array(
		  '#type' => 'hidden',
		  '#value' => $user_type,
		);
        //        
    }
    //
    $responsable_value='hontza@hontza.es';
        $form['Empresa']['responsable'] = array(
          '#type' => 'hidden',
          //'#title' => t('Email of responsible person'),
          //'#maxlength' => 255,
          //'#description' =>  t('Email address of the person to be notified.'),
          //'#required' => 1,
          //'#weight'=>"-9",
          '#value'=>$responsable_value,
        );

  unset($form['Empresa']['profile_empresa_email_corporativo']);
  //intelsat-2015
  red_crear_usuario_user_register_form_alter($form,$form_state, $form_id);
}
function my_unset_user_register_fields(&$form,$user_type,$unset_array=array()){
    $fields=create_user_register_fields();
    foreach($fields as $name=>$fieldset_array){
        if(strcmp($user_type,$name)==0){
            //            
        }else{
            if(empty($unset_array) || in_array($name,$unset_array)){

                    foreach($fieldset_array as $fieldset_name=>$my_array){
                        //echo print_r($fields[$user_type],1);
                        if(empty($user_type) || !isset($fields[$user_type][$fieldset_name])){
                            //print 'fieldset_name='.$fieldset_name.'<BR>';
                            //echo print_r($my_array,1);
                            foreach($my_array as $i=>$field_name){
                               if(isset($form[$fieldset_name][$field_name])){
                                    unset($form[$fieldset_name][$field_name]);
                               }
                            }
                        }
                    }
                
            }
        }
    }
    //
    if(empty($user_type)){
        unset($form['Preguntas']);
        unset($form['Perfiles_web']);
        unset($form['Servicios']);
    }else if($user_type=='probar_hontza_online'){
        unset($form['Perfiles_web']);
        unset($form['Servicios']);
    }else if($user_type=='facilitadores'){
        //unset($form['Servicios']);
    }
    //
    if($user_type!='facilitadores'){
        unset_seleccione_especialidades($form);
    }
}
function create_user_register_fields(){
    $result=array();
    $result['probar_hontza_online']['Empresa']=array();
    $result['probar_hontza_online']['Empresa'][]='profile_empresa_cargo';
    $result['probar_hontza_online']['Empresa'][]='profile_empresa_ciudad';
    $result['probar_hontza_online']['Empresa'][]='profile_empresa_pais';
    $result['probar_hontza_online']['Empresa'][]='profile_empresa_sitio_web';
    $result['probar_hontza_online']['Empresa'][]='profile_empresa_email_corporativo';
    //
    $result['probar_hontza_online']['Preguntas']=array();
    $result['probar_hontza_online']['Preguntas'][]='profile_preguntas_como_has_conocido';
    $result['probar_hontza_online']['Preguntas'][]='profile_preguntas_como_piensas';
    //
    $result['consultores_en_inteligencia_competitiva']['Empresa'][]='profile_empresa_tamano';
    $result['consultores_en_inteligencia_competitiva']['Empresa'][]='profile_empresa_telefono';
    $result['consultores_en_inteligencia_competitiva']['Empresa'][]='profile_empresa_skype';
    //
    $result['consultores_en_inteligencia_competitiva']['Perfiles_web']=array();
    $result['consultores_en_inteligencia_competitiva']['Perfiles_web'][]='profile_perfil_en_twitter';
    $result['consultores_en_inteligencia_competitiva']['Perfiles_web'][]='profile_perfil_en_linkedln';
    $result['consultores_en_inteligencia_competitiva']['Perfiles_web'][]='profile_perfil_en_facebook';
    //
    $result['consultores_en_inteligencia_competitiva']['Servicios']=array();
    $result['consultores_en_inteligencia_competitiva']['Servicios'][]='profile_servicios_ofrece';
    $result['consultores_en_inteligencia_competitiva']['Servicios'][]='profile_servicios_documento';
    $result['consultores_en_inteligencia_competitiva']['Servicios'][]='profile_servicios_proveedores_vtic';
    $result['consultores_en_inteligencia_competitiva']['Servicios'][]='profile_servicios_detallar';
    //
    $result['facilitadores']['Perfiles_web']=array();
    $result['facilitadores']['Perfiles_web'][]='profile_perfil_en_twitter';
    $result['facilitadores']['Perfiles_web'][]='profile_perfil_en_linkedln';
    $result['facilitadores']['Perfiles_web'][]='profile_perfil_en_facebook';
    //
    $result['facilitadores']['Empresa']=array();
    $result['facilitadores']['Empresa'][]='profile_empresa_tamano';
    $result['facilitadores']['Empresa'][]='profile_empresa_telefono';
    $result['facilitadores']['Empresa'][]='profile_empresa_skype';
    //
    $result['facilitadores']['Consultoria_estrategica']=array();
    $result['facilitadores']['Consultoria_estrategica'][]='profile_consultoria_estrategica_ayuda';
    $result['facilitadores']['Consultoria_estrategica'][]='profile_consultoria_estrategica_despliegue_estrategico';
    $result['facilitadores']['Consultoria_estrategica'][]='profile_consultoria_estrategica_despliegue_fcv';
    //
    $result['facilitadores']['Consultoria_en_gestion_de_la_informacion']=array();
    $result['facilitadores']['Consultoria_en_gestion_de_la_informacion'][]='profile_consultoria_gestion_buscar_fuentes';
    $result['facilitadores']['Consultoria_en_gestion_de_la_informacion'][]='profile_consultoria_gestion_optimizacion_busquedas';
    //
    $result['facilitadores']['Optimizacion_tics']=array();
    $result['facilitadores']['Optimizacion_tics'][]='profile_optimizacion_tics_html_rss';
    $result['facilitadores']['Optimizacion_tics'][]='profile_optimizacion_tics_adaptacion';
    $result['facilitadores']['Optimizacion_tics'][]='profile_optimizacion_tics_crear_modulo';
    //
    $result['facilitadores']['Consultoria_en_innovacion']=array();
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_creatividad';
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_seleccion';
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_construccion';
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_evaluacion';
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_redaccion';
    $result['facilitadores']['Consultoria_en_innovacion'][]='profile_innovacion_busqueda';
    //
    $result['facilitadores']['Otro_servicio']=array();
    $result['facilitadores']['Otro_servicio'][]='profile_otro_servicio_a_detallar_por_el_usuario';
    //
    $result['facilitadores']['Servicios']=array();
    $result['facilitadores']['Servicios'][]='profile_servicios_ofrece';
    $result['facilitadores']['Servicios'][]='profile_servicios_documento';
    $result['facilitadores']['Servicios'][]='profile_servicios_proveedores_vtic';
    $result['facilitadores']['Servicios'][]='profile_servicios_detallar';
    //
    return $result;
}
function my_hontza_user($op, &$edit, &$account, $category = NULL) {
 $user_type='';
 $demo_group_nid=20074;
 $consultores_group_nid=20117;
 //
 if(isset($edit['user_type']) && !empty($edit['user_type'])){
    $user_type=$edit['user_type'];
 }
 $uid='';
 $my_user='';
 
 $uid=$edit['uid'];
 if(empty($uid)){
     if(isset($account->uid) && !empty($account->uid)){
         $uid=$account->uid;
     }
 }
 //
 if($op=='insert'){
    if(!empty($user_type)){
       
        if($user_type=='probar_hontza_online'){
            $rid=define_probar_hontza_online_rol_id();
            $edit['roles'][$rid]=1;
            //echo print_r($edit,1);exit();
            //$group=node_load($demo_group_nid);
            //$edit['og_groups'][$demo_group_nid]=$group;
            my_insert_user_grupo($uid,$demo_group_nid);
        }else if($user_type=='consultores_en_inteligencia_competitiva'){
            $rid=define_consultores_en_inteligencia_competitiva_rol_id();
            $edit['roles'][$rid]=1;
            my_insert_user_grupo($uid,$consultores_group_nid);
            file_save_servicios_documentos($uid,$edit);
        }if($user_type=='facilitadores'){            
            $rid=define_facilitadores_rol_id();
            $edit['roles'][$rid]=1;
            my_insert_user_grupo($uid,$consultores_group_nid);
            file_save_servicios_documentos($uid,$edit);
        }
    }
 }else if($op=='load'){
     //echo print_r($account->roles,1);
     is_user_demo_caducado();
 }else if($op=='update'){
    //echo print_r($account->roles,1);
    if(is_user_roles_demo($account->roles)){
        if(!$account->status){
            if($edit['status']){
                $edit['profile_usuario_activado_time']=time();
            }
        }
    }
    file_save_servicios_documentos($uid,$edit);
 }else if($op=='delete'){
     on_user_delete($account->uid,$account);
 }
}
function is_user_demo_caducado($user_id='',$is_return=0){
    global $user;
    //
    $param0=arg(0);
    if(empty($user_id)){
        $my_user=$user;
    }else{
        if(!empty($param0) && $param0!='user_demo_caducado'){
            $my_user=user_load($user_id);
        }    
    }
    //echo print_r($my_user,1);
    if(is_user_probar_hontza_online($user_id) && !in_user_demo_excepcion_list($user_id)){
        $created=$my_user->created;
        //print 'created ini='.$created.'<BR>';
        if(isset($my_user->profile_usuario_activado_time) && !empty($my_user->profile_usuario_activado_time)){
            $created=$my_user->profile_usuario_activado_time;           
        }
        //simulando
        /*if($my_user->name=='aitordemo'){
            $created=$my_user->created;
        }*/
        //
        $now=time();
        $value=$now-$created;
        //print 'profile_usuario_activado_time='.$user->profile_usuario_activado_time;        
        //AVISO::::15 dias
        $caducado=60*60*24*15;
        //simulando
        //$caducado=10;
        //
        //print $value.'===='.$caducado.'<BR>';
        if($value>=$caducado){
            //drupal_set_message(t('The demo user has expired'));
            //drupal_goto('logout');
            if(empty($user_id) && !$is_return){                
                if(!empty($param0) && !in_array($param0,array('user_demo_caducado','logout'))){
                    drupal_goto('user_demo_caducado');                    
                }
            }else{
                return 1;
            }
        }
    }
    return 0;
}
function is_user_probar_hontza_online($user_id=''){
    global $user;
    if(empty($user_id)){
        $my_user=$user;
    }else{
        $my_user=user_load($user_id);
    }
    /*if(isset($user->roles) && !empty($user->roles)){
        $roles=array_keys($user->roles);
        $rid=define_probar_hontza_online_rol_id();
        if(in_array($rid,$roles)){
            return 1;
        }
    }
    return 0;*/
    $roles=array();
    if(isset($my_user->roles) && !empty($my_user->roles)){
        $roles=$my_user->roles;
    }
    return is_user_roles_demo($roles);
}
function define_probar_hontza_online_rol_id(){
    return 7;
}
function user_demo_caducado_callback(){
    //menu_rebuild();
    return t('The demo user has expired');
}
function unset_consultores_en_inteligencia_competitiva_fields(&$form,$user_type){
    $unset_array=array('consultores_en_inteligencia_competitiva');
    my_unset_user_register_fields($form,$user_type,$unset_array);
}
function define_consultores_en_inteligencia_competitiva_rol_id(){
    return 8;
}
function define_facilitadores_rol_id(){
    return 9;
}
function create_fieldset_seleccione_especialidades(&$form){
    $form['selecciona_las_especialidades']=array(
                '#type'=>'fieldset',
                '#title'=>t('Select the specialties in which you want to collaborate'),
                '#weight'=>10,
            );
    //$my_array=array('Consultoria_estrategica');
    $my_array=get_seleccione_especialidades_fieldset_names();
    foreach($my_array as $i=>$name){
        if(isset($form[$name])){
            $a=$form[$name];
            unset($form[$name]);
            $form['selecciona_las_especialidades'][$name]=$a;
            //echo print_r($form,1);
        }
    }
    $form['Servicios']['#weight']=11;
            
}
function get_seleccione_especialidades_fieldset_names(){
    return array('Consultoria_estrategica','Consultoria_en_gestion_de_la_informacion','Optimizacion_tics','Consultoria_en_innovacion','Otro_servicio');
}
function unset_seleccione_especialidades(&$form){
    $my_array=get_seleccione_especialidades_fieldset_names();
    foreach($my_array as $i=>$name){
        if(isset($form[$name])){
            //$a=$form[$name];
            unset($form[$name]);
            //$form['selecciona_las_especialidades'][$name]=$a;
            //echo print_r($form,1);
        }
    }
}
function file_save_servicios_documentos($uid,&$edit){
        global $base_root;
            $dir=$_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/user_servicios_files/'.$uid.'/';
            if(hontza_is_private_download('',0,0)){
                //$dir='/home/'.trim(base_path(),'/').'_files/'.$uid.'/';
                //$dir='/home/'.hontza_trim_base_root($base_root).'_files/'.$uid.'/';
                if(red_is_hontza_home_dir()){
                    $dir='/home/'.hontza_trim_base_root($base_root).'_files/user_servicios_files/'.$uid.'/';
                }
            }
            if(!is_dir($dir)){
                @mkdir($dir);
            }
            $filename=$_FILES['files']['name']['profile_servicios_documento'];
            if(!empty($filename)){
                $edit['profile_servicios_documento']=$filename;
                file_save_upload('profile_servicios_documento', array(), $dir.$filename);
            }
}
function translate_user_register(&$form){
    $t_array=array();
    $t_array['Perfiles_web']='Perfiles web';
    $t_array['Consultoria_estrategica']='Consultoria estratégica';
    $t_array['Consultoria_en_gestion_de_la_informacion']='Consultoría en gestión de la información';
    $t_array['Optimizacion_tics']='Optimización TICS';
    $t_array['Consultoria_en_innovacion']='Consultoria en innovación';
    $t_array['Otro_servicio']='Otro servicio';
    //
    $fields=create_user_register_fields();
    foreach($fields as $user_type=>$my_array){
        foreach($my_array as $name=>$my_array2){
            if(isset($form[$name]) && isset($t_array[$name])){
                $form[$name]['#title']=t($t_array[$name]);
            }
        }
    }
}
function my_user_profile_servicios_form_alter(&$form,&$form_state,$form_id){
    if(arg(0)=='user'){
        $uid=arg(1);
        if(exist_servicios_documento_filename($uid)){
            $descargar_documento_html=l(t('Show uploaded document'),'download_servicios_documento/'.$uid, array('attributes' => array('target'=>'_blank')));
            if(is_numeric($uid) && arg(2)=='edit' && arg(3)=='Servicios'){
                $form['Servicios']['descargar_documento']=array(
                    '#value'=>$descargar_documento_html,
                    '#weight'=>0,
                );
            }
        }
    }
}
function download_servicios_documento_callback() {
  $uid=arg(1);
  $user=user_load($uid);
  $filename=$user->profile_servicios_documento;
  $user_dir='sites/default/files/user_servicios_files/'.$uid.'/';
  $src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path().$user_dir;
  if(!empty($filename)){  
    //print $src_hasi.$file->filepath;
  	if(file_exists($user_dir.$filename)){        
            header("Content-Disposition: attachment; filename=".$filename);
            header("Content-Type: application/force-download");
            header('Content-Description: File Transfer');
            readfile($src_hasi.$filename);
            exit;
        }
  }
  return t('Document not exist');
}
function gracias_demo_callback(){
    /*$s=variable_get('frase_post_probar_hontza_online','');
    if(!empty($s)){
        return t($s);
    }else{
        return t('Thanks demo');
    }*/
    $node_type='post_frase_probar_hontza_online';
    $node_list=get_frases_post_formulario_list($node_type);
    $node=find_node_frase($node_list, $node_type);
    if(isset($node->nid) && !empty($node->body)){
        return $node->body;
    }else{
        return t('Thanks demo');
    }
}
function my_get_post_formulario_content(){
    if(user_access('Ver frases_post_formulario')){
        $html=array();    
        $html[]=l(t('Welcome Messages'),'frases_post_formulario');
        return implode('<br>',$html);
    }
    return '';
}
function gracias_consultores_en_inteligencia_competitiva_callback(){
    /*$s=variable_get('frase_post_consultores_en_inteligencia_competitiva','');
    if(!empty($s)){
        return t($s);
    }else{
        return t('Thanks consultant');
    }*/
    $node_type='post_frase_consultores_en_inteli';
    $node_list=get_frases_post_formulario_list($node_type);
    //echo print_r($node_list,1);
    $node=find_node_frase($node_list, $node_type);
    if(isset($node->nid) && !empty($node->body)){
        return $node->body;
    }else{
        return t('Thanks consultant');
    }
}
function gracias_facilitador_callback(){
    /*$s=variable_get('frase_post_facilitador','');
    if(!empty($s)){
        return t($s);
    }else{
        return t('Thanks facilitator');
    }*/
    $node_type='post_frase_facilitadores';
    $node_list=get_frases_post_formulario_list($node_type);
    $node=find_node_frase($node_list, $node_type);
    if(isset($node->nid) && !empty($node->body)){
        return $node->body;
    }else{
        return t('Thanks facilitator');
    }
}
function my_insert_user_grupo($uid,$nid){
    $og_role=0;
    $is_active=1;
    $is_admin=0;
    $created=time();
    $changed=$created;
    $sql=sprintf("INSERT INTO {og_uid}(nid,og_role,is_active,is_admin,uid,created,changed) VALUES(%d,%d,%d,%d,%d,%d,%d)",$nid,$og_role,$is_active,$is_admin,$uid,$created,$changed);
    db_query($sql);
}
function frases_post_formulario_callback(){
  /*$where=array();
  //
  $where[]='n.promote = 1';
  $where[]='n.status = 1';
  $where[]='n.type IN("post_frase_probar_hontza_online","post_frase_consultores_en_inteligencia_competitiva","post_frase_facilitadores")';
  //
  $order_fields='n.sticky DESC, n.created DESC';
  //
  $sql='SELECT n.nid, n.sticky, n.created
  FROM {node} n
  WHERE '.implode(' AND ',$where).'
  GROUP BY n.nid '.$order_by;*/
//print $sql;
  $my_limit=variable_get('default_nodes_main', 10);

  //if(is_idea('mas_valoradas')){
   /* $result = db_query($sql);
    $rows=array();
    $output = '';
    $num_rows = FALSE;
    //$kont=0;
    while ($node = db_fetch_object($result)) {
      //print_r($node);
      $my_node=node_load($node->nid);
      
      $my_list[]=$my_node;
      $num_rows = TRUE;
    }*/

    $my_list=get_frases_post_formulario_list();

    $rows=create_frases_rows($my_list);


    $rows=my_set_estrategia_pager($rows,$my_limit);
    
 
  $num_rows=TRUE;

  if ($num_rows) {
    $headers=array(t('Title'),t('Action'));
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output.= '<div id="first-time">' .t('There are no Welcome Messages'). '</div>';
  }
  drupal_set_title(t('List of Welcome Messages'));

  return $output;
}
function create_frases_rows($my_list){
    $rows=array();
    /*$frases=array("post_frase_probar_hontza_online"=>t('Welcome Message: Try Hontza Online'),
    "post_frase_consultores_en_inteli"=>t('Welcome Message: Competitive Intelligence Consultants'),
    "post_frase_facilitadores"=>t('Welcome Message: Facilitators'));*/
    /*Mensaje de Bienvenida a Probar Hontza Online (Welcome Message to "Testo Hontza Online")
Mensaje de Bienvenida a Consultores en IC (Welcome Message to "IC Consultants")
Mensaje de Bienvenida a Facilitadores (Welcome Message to "Facilitators")*/
    
    $frases=get_frases();
    $frase_types=array_keys($frases);
    foreach($frase_types as $i=>$node_type){
        $type_name=str_replace("_","-",$node_type);
        $node=find_node_frase($my_list,$node_type);
        if(isset($node->nid)){
            
            $rows[$kont][0]=$node->title;
            $rows[$kont][1]=$html[]=l(my_get_icono_action('edit', t('Edit')),'node/'.$node->nid.'/edit',array('html'=>true,'query'=>drupal_get_destination()));
        }else{
            $rows[$kont][0]=t($frases[$node_type]);
            $rows[$kont][1]=$html[]=l(my_get_icono_action('add', t('Create')),'node/add/'.$type_name,array('html'=>true,'query'=>drupal_get_destination()));
        }
        $kont++;
    }
    return $rows;
}
function find_node_frase($my_list,$node_type){
    if(count($my_list)>0){
        foreach($my_list as $i=>$r){
            if($r->type==$node_type){
                return $r;
            }
        }
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function get_frases_post_formulario_list($type_in=''){
  $my_list=array();
  $where=array();
  //
  $where[]='n.promote = 1';
  $where[]='n.status = 1';
  if(empty($type_in)){
    $where[]='n.type IN("post_frase_probar_hontza_online","post_frase_consultores_en_inteli","post_frase_facilitadores")';
  }else{
    $where[]='n.type="'.$type_in.'"';
  }
  //
  $order_fields='n.sticky DESC, n.created DESC';
  //
  $sql='SELECT n.nid, n.sticky, n.created
  FROM {node} n
  WHERE '.implode(' AND ',$where).'
  GROUP BY n.nid '.$order_by;
//print $sql;
  

  //if(is_idea('mas_valoradas')){
   $result = db_query($sql);
    $rows=array();
    $output = '';
    $num_rows = FALSE;
    //$kont=0;
    while ($node = db_fetch_object($result)) {
      //print_r($node);
      //$my_node=node_load($node->nid);
      $my_node=hontza_node_load_by_lang($node->nid);  
        
      $my_list[]=$my_node;
      $num_rows = TRUE;
    }
    return $my_list;
}
function is_url_frases_post_formulario(){    
    if(arg(0)=='frases_post_formulario'){
        return 1;
    }
    return 0;
}
function is_ficha_node_post_formulario(){
    $frases=get_frases();
    $frase_types=array_keys($frases);
    foreach($frase_types as $i=>$type){
        //print $type.'<BR>';
        if(is_ficha_node($type)){
            return 1;
        }
    }
    return 0;
}
function get_frases(){
    $frases=array("post_frase_probar_hontza_online"=>t('"Try Hontza Online" Welcome message'),
    "post_frase_consultores_en_inteli"=>t('"CI Consultants" Welcome message'),
    "post_frase_facilitadores"=>t('"Facilitators" Welcome message'));
    return $frases;
}
function get_my_volver_link_post_formulario(){
    $my_volver_link='<div class="clearfix">';
    $my_volver_link.=l(t('Return'),'frases_post_formulario',array('attributes'=>array('class'=>'back')));
    $my_volver_link.='</div>';
    return $my_volver_link;
}
function my_help_frases_post_formulario(){
    return help_popup_window(20113, 'help',my_get_help_link_object());
}
function my_help_ficha_node_post_formulario(){
    if(is_ficha_node("post_frase_probar_hontza_online")){
        return help_popup_window(20114, 'help',my_get_help_link_object());
    }
    if(is_ficha_node("post_frase_consultores_en_inteli")){
        return help_popup_window(20115, 'help',my_get_help_link_object());
    }
    if(is_ficha_node("post_frase_facilitadores")){
        return help_popup_window(20116, 'help',my_get_help_link_object());
    }
}
function is_user_roles_demo($roles_in){
    //global $user;
    if(!empty($roles_in)){
        $roles=array_keys($roles_in);
        $rid=define_probar_hontza_online_rol_id();
        if(in_array($rid,$roles)){
            return 1;
        }
    }
    return 0;   
}
function on_user_delete($uid,$account){
    $dir=$_SERVER['DOCUMENT_ROOT'].base_path().'sites/default/files/user_servicios_files/'.$uid.'/';
            if(is_dir($dir)){
                $filename=$account->profile_servicios_documento;
                if(!empty($filename)){
                    unlink($dir.$filename);
                    //print $dir.$filename;exit();
                }
                //unlink($dir);
            }

}
function filtrar_nube_etiquetas_del_grupo($tags,$my_num_tags=0){
    $result=array();
    if(count($tags)>0){        
        foreach($tags as $i=>$t){
           if(is_term_del_grupo($t->tid,$t->vid)){
               $node_array=get_term_node_list($t->tid);
               if(count($node_array)>0){
                   /*if($t->name=="Alma"){
                    echo print_r($node_array,1);
                   }*/
                   $result[]=$t;
               }
           }
        }
    }
    if($my_num_tags>0){
        if(count($result)>0){
            $result=array_slice($result, 0, $my_num_tags);
        }
    }
    return $result;
}
function is_term_del_grupo($tid,$vid){
    /*if($vid!=3){
        return 1;
    }*/
    if(empty($tid)){
        return 0;
    }
    $my_grupo=og_get_group_context();
    //
    $where=array();
    $where[]="1";
    $where[]="tn.tid=".$tid;
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
  	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")";
    }else{
        return 0;
    }
    $sql="SELECT tn.* 
    FROM {term_node} tn LEFT JOIN {og_ancestry} og_ancestry ON tn.nid = og_ancestry.nid
    WHERE ".implode(" AND ",$where);
    $result=db_query($sql);
    
    while($row=db_fetch_array($result)){
        return 1;
    }

    return 0;
}
function define_my_num_tags(){
    return 1000;
}
function my_get_title_grupo_descripcion_block(){
    //return t('Group Overview');
    return t('Group Description');
}
function my_get_grupo_descripcion_content(){
    //simulando
    //listar_nube_etiquetas_vacias();
    //
    $delta=44;
    $help_block=help_popup_block(20139);
    $html.='<div class="block block-hontza block-odd region-odd clearfix " id="block-hontza-'.$delta.'">';
    //intelsat-2015
    $icono=my_get_icono_action('descripcion_grupo', t('Group'),'descripcion_grupo').'&nbsp;';
    //
    $html.='<h3 class="title">'.$icono.my_get_title_grupo_descripcion_block().$help_block.'</h3>';
    $html.='<div class="content">';
    $html.=get_grupo_descripcion_html();
    //gemini-2014
    $html.=hontza_get_gestores_del_grupo_html();
    //intelsat-2015
    $html.=hontza_inicio_view_all('mi-grupo',t('View Group'));
    $html.='</div>';
    $html.='</div>';   
    return $html;
}
function get_grupo_descripcion_html(){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){        
        $output=$my_grupo->og_description;
        $output=nl2br($output);
        return $output;
    }
    return '';
}
function is_show_descripcion_del_grupo(){
    if(is_dashboard()){
        return 1;
    }
    return 0;
}
function my_get_grupo_descripcion_region(){
  $style='';  
  /*if(!hontza_is_sareko_id('ROOT')){
      $style=' style="margin-bottom:15px;width:95%;"';    
  }*/
  //$style=' style="width:100%;"';
  $html=array();
  //$html[]='<div id="c_descripcion_del_grupo_con_calendario"'.$style.'><div class="page-region">';
  $html[]=my_get_grupo_descripcion_content();
  //$html[]="</div></div>";
  //$html[]=calendario_get_region();
  return implode("",$html);
}
function listar_nube_etiquetas_vacias(){
    $sql='SELECT * FROM term_data WHERE vid=3';
    $result=db_query($sql);
    $my_list=array();
    while($row=db_fetch_object($result)){
        $my_list[]=$row;
    }
    if(count($my_list)>0){
        foreach($my_list as $i=>$r){
            $node_array=get_term_node_list($r->tid);
            if(count($node_array)>0){
                //print $r->name.'<BR>';
            }else{
                print $r->name.'<BR>';
            }
        }
    }
    exit();
}
function get_term_node_list($tid){
    //$sql="SELECT tn.* FROM term_node tn LEFT JOIN node n ON tn.nid=n.nid WHERE tn.tid=".$tid;
    $sql="SELECT tn.* FROM node n LEFT JOIN term_node tn ON tn.vid=n.vid WHERE tn.tid=".$tid;
    $result=db_query($sql);
    $my_list=array();
    while($row=db_fetch_object($result)){
        $my_list[]=$row;
    }
    return $my_list;
}
function borrar_items_canal_no_existe_callback(){
    $canal_nid=arg(1);
    $canal=array();
    $canal=(object) $canal;
    $canal->nid=$canal_nid;
    $nid_list=get_canal_node_nid_list($canal);    
    if(count($nid_list)>0){
        $html=array();
        $html[]='<div><h3>';
        $html[]=t('Confirm that you want to delete these items?');
        $html[]='</h3></div>';
        $html[]='<div>';
        $html[]='<ul>';
        foreach($nid_list as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $html[]='<li>'.$node->title.'</li>';
            }
        }
        $html[]='</ul>';
        $html[]='</div>';
        $html[]='<div>';
        //$html[]='<span>'.l(t('Delete'),'confirm_borrar_items_canal_no_existe/'.$canal_nid).'</span>';
        $html[]='<input type="button" id="borrar_items_canal_no_existe_btn" name="borrar_items_canal_no_existe_btn" value="'.t('Delete').'"/>';
        $html[]='&nbsp';
        $html[]='<span>'.l(t('Cancel'),'canales/'.$canal_nid).'</span>';
        $html[]='</div>';
        $html[]='<div>';
        return implode('',$html);
    }
    return t('There are no items in this channel');
}
function  add_js_borrar_items_canal_no_existe(){
	if(is_borrar_items_canal_no_existe()){
            $canal_nid=arg(1);
		$js='
			var url_confirm_borrar_items_canal_no_existe="'.url('confirm_borrar_items_canal_no_existe/'.$canal_nid).'";
                        $(document).ready(function()
			{
			  $("#borrar_items_canal_no_existe_btn").click(function(){
                                location.href=url_confirm_borrar_items_canal_no_existe;
                          });
			});';

			drupal_add_js($js,'inline');
	}
}
function is_borrar_items_canal_no_existe(){
    if(arg(0)=='borrar_items_canal_no_existe'){
        return 1;
    }
    return 0;
}
function confirm_borrar_items_canal_no_existe_callback(){
    $canal_nid=arg(1);
    $canal=array();
    $canal=(object) $canal;
    $canal->nid=$canal_nid;
    $nid_list=get_canal_node_nid_list($canal);
    if(count($nid_list)>0){
        foreach($nid_list as $i=>$nid){
            node_delete($nid);
        }
    }
    drupal_goto('mensaje_items_borrados_canal_no_existe');
}
function mensaje_items_borrados_canal_no_existe_callback(){
    return t('Items from channel have been deleted');
}
function my_taxonomy_term_pre_execute(&$view){
    global $user,$language;
    $uid=$user->uid;
    //
    $tid=arg(2);
    $my_grupo=og_get_group_context();
    $grupo_nid='';
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $grupo_nid=$my_grupo->nid;
    }
    if(!empty($tid) && is_numeric($tid)){
        $term=taxonomy_get_term($tid);
        if(isset($term->vid) && !empty($term->vid) && $term->vid==4){
            $where=array();
            $where[]='(node.status <> 0 OR (node.uid = '.$uid.' AND '.$uid.' <> 0))';
            $where[]='node.vid IN ( SELECT tn.vid FROM {term_node} tn WHERE tn.tid = %d )';
            $where[]='node.language="'.$language->language.'"';            
            if(!empty($grupo_nid)){
                $where[]='og_ancestry.group_nid='.$grupo_nid;
            }
            //
            $sql='SELECT node.nid AS nid,
                node.sticky AS node_sticky,
                node.created AS node_created
                FROM {node} node
                LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
                WHERE '.implode(' AND ',$where).'
                ORDER BY node_sticky DESC, node_created DESC';
            //print $sql;
            $view->build_info['query']=$sql;
            $view->build_info['count_query']=$sql;
        }else{
            $is_administer_nodes=0;
            if(user_access('administer nodes')){
                $is_administer_nodes=1;
            }
            $where=array();
            $where[]='(node.status <> 0 OR (node.uid = '.$uid.' AND '.$uid.' <> 0) OR '.$is_administer_nodes.'=1)';
            $where[]='node.vid IN ( SELECT tn.vid FROM {term_node} tn WHERE tn.tid = %d )';
            if(!empty($grupo_nid)){
                $where[]='og_ancestry.group_nid='.$grupo_nid;
            }
            $sql='SELECT node.nid AS nid, node.sticky AS node_sticky, node.created AS node_created 
            FROM {node} node
            LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
            WHERE '.implode(' AND ',$where).'
            ORDER BY node_sticky DESC, node_created DESC';
            $view->build_info['query']=$sql;
            $view->build_info['count_query']=$sql;
        }
    }
}
function has_query_rows($sql){
    //$res=db
}
function save_grupo_fuentehtml(&$node){
    if($node->type=='fuentehtml'){
        $grupo_nid_array=array();
        $my_grupo=og_get_group_context();
        if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $grupo_nid_array[$my_grupo->nid]=$my_grupo->nid;
            /*if(!isset($node->og_initial_groups) || empty($node->og_initial_groups)){
                $node->og_initial_groups=array();
                $node->og_initial_groups=$grupo_nid_array;
            }*/
            if(!isset($node->og_groups) || empty($node->og_groups)){
                /*$node->og_groups=array();
                $node->og_groups=$grupo_nid_array;*/
                if(!exist_node_grupo($node->nid,$my_grupo->nid)){
                    $sql="INSERT INTO og_ancestry(nid,group_nid) VALUES(".$node->nid.",".$my_grupo->nid.")";
                    db_query($sql);
                }
            }
        }
        //echo print_r($node,1);exit();
    }/*else if($node->type=="supercanal"){
        echo print_r($node,1);exit();
    }*/
}
function exist_node_grupo($nid,$group_nid){
    $where=array();
    $where[]="nid=".$nid;
    $where[]="group_nid=".$group_nid;
    $sql="SELECT * FROM {og_ancestry} og_ancestry WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return 1;
    }
    return 0;
}
function my_user_consultoria_en_gestion_de_la_informacion_form_alter(&$form,&$form_state,$form_id){
    //echo print_r($form['Consultoria_en_gestion_de_la_informacion'],1);
    $my_array=array();
    $key='Consultoria_en_gestion_de_la_informacion';
    $my_array[$key]=array();
    //intelsat-2015
    //$my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    //
    $my_array[$key]['title']=t('Information Management Consultancy');
    $my_array[$key]['elements']=array();
    $my_array[$key]['elements']['profile_consultoria_gestion_buscar_fuentes']='Search Information Sources';
    $my_array[$key]['elements']['profile_consultoria_gestion_optimizacion_busquedas']='Search Optimisation';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function set_mi_perfil_secondary_titles($secondary){
    //$uid::::out parametroa da
    if(is_mi_perfil(0) || is_user_editing("-1", $uid)){
        $my_array=explode('</a>',$secondary);
        $num=count($my_array);
        if($num>0){
            for($i=0;$i<$num;$i++){
                $s=$my_array[$i];
                $pos=strpos($s,'">');
                if ($pos === false) {
                    //
                }else{
                    $title=substr($s,$pos+2);
                    $title=trim($title);
                    //intelsat-2015
                    $title_array=array('Datos personales','Perfiles_web','Consultoria_en_innovacion','Consultoria_estrategica','Optimizacion_tics','Servicios');
                    //intelsat-2016
                    if(!red_facilitador_user_access()){
                        $title_array[]='Consultoria_en_gestion_de_la_informacion';
                    }
                    if(in_array($title,$title_array)){
                        $my_array[$i]='';
                        continue;
                    }
                    //
                    $title=my_translate_profile_secondary_menu_title($title);
                    $my_array[$i]=substr($s,0,$pos+2).$title;
                    //gemini-2013
                    $my_array[$i]=set_mi_perfil_secondary_href($my_array[$i]);    
                }
            }
        }
        return implode('</a>',$my_array);
    }
    return $secondary;
}
function my_translate_profile_secondary_menu_title($s){
    $konp='';
    //print 's='.$s.'<BR>';
    if(strcmp($s,'Consultoria_en_gestion_de_la_informacion')==0){
        //$konp=t('Information Management Consultancy');
        //$konp=t('Facilitator');
        $konp=t('Expert');
    }else if(strcmp($s,'Consultoria_en_innovacion')==0){
        $konp=t('Innovation Consultancy');
    }else if(strcmp($s,'Consultoria_estrategica')==0){
        $konp=t('Strategic Consultancy');        
    }else if(strcmp($s,'Datos personales')==0){
        $konp=t('Personal data');
    }else if(strcmp($s,'Empresa')==0){
        $konp=t('Organisation');        
    }else if(strcmp($s,'Optimizacion_tics')==0){
        $konp=t('ICTs Optimisation');    
    }else if(strcmp($s,'Otro_servicio')==0){
        $konp=t('Other service');
    }else if(strcmp($s,'Perfiles_web')==0){
        $konp=t('Internet Profiles');
    }else if(strcmp($s,'Preguntas')==0){
        $konp=t('Questions');        
    }else if(strcmp($s,'Servicios')==0){
        $konp=t('Services');
    }
    //
    if(!empty($konp)){
        if(strcmp($konp,$s)==0){
            return $s;
        }else{
            return $konp;
        }
    }
    return $s;
}
function my_translate_user_profile_form($my_array,&$form,&$form_state,$form_id){
        foreach($my_array as $fieldset=>$row){
            if(isset($form[$fieldset])){
                $form[$fieldset]['#title']=$row['title'];
                if(isset($row['elements']) && !empty($row['elements'])){
                    foreach($row['elements'] as $profile_name=>$v){
                        if(isset($form[$fieldset][$profile_name])){
                            $form[$fieldset][$profile_name]['#title']=t($v);
                        }
                    }
                }
            }
        }
}
function my_user_consultoria_en_innovacion_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Consultoria_en_innovacion';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']=array();
    $my_array[$key]['elements']['profile_innovacion_creatividad']='Creativity and New ideas';
    $my_array[$key]['elements']['profile_innovacion_seleccion_de_ideas']='Idea Selection';
    $my_array[$key]['elements']['profile_innovacion_construccion']='Project building';
    $my_array[$key]['elements']['profile_innovacion_evaluacion']='Project evaluation';
    $my_array[$key]['elements']['profile_innovacion_redaccion']='Writing Projects';
    $my_array[$key]['elements']['profile_innovacion_busqueda']='Financial consultancy';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_consultoria_estrategica_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Consultoria_estrategica';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_consultoria_estrategica_ayuda']='Consultancy on Strategy definition';
    $my_array[$key]['elements']['profile_consultoria_estrategica_despliegue_estrategico']='Strategic Deployment';
    $my_array[$key]['elements']['profile_consultoria_estrategica_despliegue_fcv']='Critic Watching Factors Deployment';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_datos_personales_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Datos personales';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_nombre']='Name';
    $my_array[$key]['elements']['profile_apellidos']='Surname';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_empresa2_form_alter(&$form,&$form_state,$form_id,$solo_empresa=0){
    $my_array=array();
    $key='Empresa';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_empresa']='Organisation';
    if(!$solo_empresa){
        $my_array[$key]['elements']['profile_es_empresa_de_servicios']='Is a services company';
        $my_array[$key]['elements']['profile_codigo_empresa']='Company identifier';
        $my_array[$key]['elements']['profile_empresa_cargo']='Position';
        $my_array[$key]['elements']['profile_empresa_ciudad']='Town';
        $my_array[$key]['elements']['profile_empresa_pais']='Country';
        $my_array[$key]['elements']['profile_empresa_sitio_web']='Website';
        $my_array[$key]['elements']['profile_empresa_email_corporativo']='Corporate email';
        $my_array[$key]['elements']['profile_empresa_tamano']='Number of employees';
        $my_array[$key]['elements']['profile_empresa_telefono']='Phone';
        $my_array[$key]['elements']['profile_empresa_skype']='Skype';
        $my_array[$key]['elements']['profile_usuario_activado_time']='Active user time';
    }
    //intelsat-2015
    if(isset($form['Empresa']['profile_es_empresa_de_servicios'])){
        unset($form['Empresa']['profile_es_empresa_de_servicios']);
    }
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
    //intelsat-2016
    hontza_registrar_user_profile_empresa_form_alter($form,$form_state,$form_id);
}
function my_user_optimizacion_tics_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Optimizacion_tics';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_optimizacion_tics_html_rss']='HTML-RSS Conversion';
    $my_array[$key]['elements']['profile_optimizacion_tics_adaptacion']='Drupal and PHP Programming';
    $my_array[$key]['elements']['profile_optimizacion_tics_crear_modulo']='Create a new module';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_otro_servicio_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Otro_servicio';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_otro_servicio_a_detallar_por_el_usuario']='Details';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_perfiles_web_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Perfiles_web';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_perfil_en_twitter']='Twitter URL';
    $my_array[$key]['elements']['profile_perfil_en_linkedln']='Linkedln URL';
    $my_array[$key]['elements']['profile_perfil_en_facebook']='Facebook URL';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_preguntas_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Preguntas';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_preguntas_como_has_conocido']='How did you hear about Hontza?';
    $my_array[$key]['elements']['profile_preguntas_como_piensas']='How are you going to use Hontza?';
    //
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function my_user_servicios2_form_alter(&$form,&$form_state,$form_id){
    $my_array=array();
    $key='Servicios';
    //echo print_r($form[$key],1);
    $my_array[$key]=array();
    $my_array[$key]['title']=my_translate_profile_secondary_menu_title($key);
    $my_array[$key]['elements']['profile_servicios_ofrece']='Your Consultancy services';
    $my_array[$key]['elements']['profile_servicios_documento']='Attach document with your company services';
    $my_array[$key]['elements']['profile_servicios_proveedores_vtic']='Do your company have commercial relations with other CI software providers?';
    $my_array[$key]['elements']['profile_servicios_detallar']='Details';
    //$form['#attributes']['enctype']="multipart/form-data";
    $form[$key]['profile_servicios_documento']['#type']='file';
    //
    $form[$key]['profile_servicios_proveedores_vtic']['#options']=create_yes_no_options();
    my_translate_user_profile_form($my_array,$form,$form_state,$form_id);
}
function translate_user_create_form(&$form,&$form_state,$form_id){
    /*global $user;
    if(isset($user->uid) && !empty($user->uid)){*/
        my_user_empresa2_form_alter($form,$form_state,$form_id,1);
        my_user_datos_personales_form_alter($form,$form_state,$form_id);
    //}
    my_translate_account_information_form($form,$form_state,$form_id);
}
function my_translate_account_information_form(&$form,&$form_state,$form_id){
    hontza_translate_account_information($form);
    
    //
    /*$sql='SELECT * FROM locales_source s LEFT JOIN locales_target t ON s.lid=t.lid WHERE t.translation="El contenido de este campo se mantendrá privado y no se hará público."';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        echo print_r($row,1);exit();
    }*/
    $my_description=t('The content of this field is kept private and will not be shown publicly.');
    $form['Datos personales']['profile_nombre']['#description'] =  $my_description;
    $form['Datos personales']['profile_apellidos']['#description'] =  $my_description;
    $form['Empresa']['profile_empresa']['#description'] =  $my_description;
    $form['submit']['#value'] = t('Create new account');
}
function exist_servicios_documento_filename($uid){
  $filename='';
  $user=user_load($uid);
  if(isset($user->uid) && !empty($user->uid)){
      $filename=$user->profile_servicios_documento;
      //$filename='prueba.txt';
      $user_dir='sites/default/files/user_servicios_files/'.$uid.'/';
      //$src_hasi='http://'.$_SERVER['HTTP_HOST'].base_path().$user_dir;
      if(file_exists($user_dir.$filename)){
          return 1;
      }
  }
  return 0;
}
function create_yes_no_options(){
    $result=array ('0' => '--','No' => t('No'),'Si' => t('Yes'));
    return $result;
}
//gemini-2013
function set_mi_perfil_secondary_href($s){
    global $user;
    global $language;
    $bilatu='href="';
    $pos=strpos($s,$bilatu);
    if($pos===FALSE){
        return $s;
    }
    //
    $pos=$pos+strlen($bilatu);
    $href=substr($s,$pos);
    $pos=strpos($href,'"');
    if($pos===FALSE){
        return $s;
    }else{    
        $a=substr($href,0,$pos);
        //$uid::::out parametroa da
        if(is_user_editing('-1', $uid)){
            //
        }else{
            $uid=$user->uid;
        }
        //print 'uid===='.$uid.'<BR>';
        //                
        $konp='/hontza3/user/'.$uid.'/edit/groups';
        $konp_lang='/hontza3/'.$language->language.'/user/'.$uid.'/edit/groups';
        if($konp==$a || $konp_lang==$a){
            $result=l(t('Groups'),'user/'.$uid.'/edit/my_groups');
            $result=str_replace('</a>','',$result);
            return '<li>'.$result;
        }
    }
    return $s;
}
//gemini-2013
function user_my_groups_form(){
    $form=array();
    $uid=arg(1);
    $my_user=user_load($uid);
    if(isset($my_user->uid) && !empty($my_user->uid)){
        drupal_set_title($my_user->name);
    }
    $form['title_fieldset']=array('#type'=>'fieldset','#title'=>t('Groups'));
    
    $form['my_uid']=array('#type'=>'hidden','#value'=>$uid);
    //
    $options=get_usuario_grupos_options($uid);
   
     $form['title_fieldset']['groups_delete_txek'] = array(
    '#title' => t('Remove user to groups'),
    '#type' => 'checkboxes',
    '#options' => $options,
  );
    
    $form['borrar_btn']=array('#type'=>'submit','#name'=>'borrar_btn','#value'=>t('Delete'));
    $form['cancel_btn']=array('#value'=>l(t('Cancel'),'user/'.$uid.'/edit'));     
    //
    return $form;
}
function user_my_groups_form_submit($form, &$form_state) {
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $my_name=$form_state['clicked_button']['#name'];
        if($my_name=='borrar_btn'){
            $uid=$form_state['values']['my_uid'];
            if(isset($form_state['values']['groups_delete_txek']) && !empty($form_state['values']['groups_delete_txek'])){
                $txek_list=$form_state['values']['groups_delete_txek'];
                if(!empty($txek_list)){
                    foreach($txek_list as $grupo_nid=>$v){
                        if(!empty($v)){
                            my_user_group_unsubscribe($grupo_nid,$uid);
                        }
                    }
                }
            }
            drupal_goto('user/'.$uid.'/edit');    
        }                
    }   
}
function hontza_translate_user_register(&$form) {
    //AVISO::::Aquí no se le hace caso a $form_state y form_id, pero hay que pasarlos porque se pueden utilizar en otro form_alter
    $form_id='';
    hontza_translate_account_information($form);
    my_user_datos_personales_form_alter($form,$form_state,$form_id);
    $solo_empresa=0;
    my_user_empresa2_form_alter($form,$form_state,$form_id,$solo_empresa);
    my_user_preguntas_form_alter($form,$form_state,$form_id);
    my_user_perfiles_web_form_alter($form,$form_state,$form_id);
    my_user_servicios2_form_alter($form,$form_state,$form_id);
    //user_type=facilitadores
    my_user_consultoria_en_gestion_de_la_informacion_form_alter($form,$form_state,$form_id);
    my_user_consultoria_en_innovacion_form_alter($form,$form_state,$form_id);
    my_user_consultoria_estrategica_form_alter($form,$form_state,$form_id);
    my_user_optimizacion_tics_form_alter($form,$form_state,$form_id);
    my_user_otro_servicio_form_alter($form,$form_state,$form_id);
    //
    $form['submit']['#value'] = t('Create new account');
}
function hontza_translate_account_information(&$form){
    if(isset($form['account'])){
        $form['account']['#title'] = t('Account information');
        //
        $form['account']['name']['#title'] = t('Username');
        $form['account']['name']['#description']= t('Spaces are allowed; punctuation is not allowed except for periods, hyphens, and underscores.');
        //
        $form['account']['mail']['#title'] = t('E-mail address');
    }
}
function hontza_is_sin_lenguaje_user_register_url(){
    global $language;    
    $request_uri=request_uri();
    $pos=strpos($request_uri,'user/register');
    if($pos===FALSE){
        return 0;
    }
    $s=substr($request_uri,0,$pos);
    $s=str_replace(base_path(),'',$s);
    $s=trim($s,'/');
    if(empty($s)){
        return 1;
    }    
    return 0;
}
function hontza_get_gestores_del_grupo_html(){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $html=array();
        $html[]='<div style="padding-top:5px;">';
        $creator=hontza_get_username($my_grupo->uid);
        $html[]='<label><b>'.t('Creator').':</b></label>';
        $html[]='&nbsp;'.$creator;
        $html[]='</div>';
        $group_administrator_username='admin';
        if(isset($my_grupo->field_admin_grupo_uid[0]['value'])){
            $group_administrator_username=hontza_get_username($my_grupo->field_admin_grupo_uid[0]['value']);        
        }
        //        
        $html[]='<div>';    
        $html[]='<label><b>'.t('Editor in chief').':</b></label>';
        $html[]='&nbsp;'.$group_administrator_username;        
        $html[]='</div>';        
        return implode('',$html);
    }
    return '';
}