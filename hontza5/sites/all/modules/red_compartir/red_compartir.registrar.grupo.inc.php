<?php
function red_compartir_registrar_grupo_hoja_registrar_form(){
    $form=array();
    $nid=arg(2);
    $node=node_load($nid);
    $form['grupo_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$nid,
    );
    $form['grupo_name']=array(
        '#value'=>'<p><b>'.$node->title.'</b></p>',
    );
    $form['share_btn']=array(
        '#type'=>'submit',
        '#default_value'=>t('Share'),
    );
    return $form;
}
function red_compartir_registrar_grupo_hoja_registrar_form_submit(&$form, &$form_state){
    $grupo_nid=$form_state['values']['grupo_nid'];
    $_REQUEST['destination']='';
    drupal_goto('red_compartir/compartir_grupo_hoja/'.$grupo_nid);
}
function red_compartir_registrar_grupo_add_form_fields($node,&$form){
    $registrar_grupo_name=$node->title;
    $form['registrar_grupo_name']=array(
        '#title'=>t('Group Name'),
        '#type'=>'textfield',        
        '#default_value'=>$registrar_grupo_name,
        //'#attributes'=>array('readonly'=>'readonly'),
    );
    $propietario=crear_usuario_get_grupo_propietario($node);
    $registrar_propietario_grupo_name=$propietario->name;
    $form['registrar_propietario_grupo_name']=array(
        '#title'=>t('Group Owner'),
        '#type'=>'textfield',        
        '#default_value'=>$registrar_propietario_grupo_name,
        '#attributes'=>array('readonly'=>'readonly'),
    );
    $registrar_propietario_uid_local=$propietario->uid;
    $form['registrar_propietario_grupo_uid_local']=array(
        '#type'=>'hidden',        
        '#default_value'=>$registrar_propietario_uid_local,
    );
    $registrar_propietario_email=$propietario->mail;
    $form['registrar_propietario_grupo_email']=array(
        '#title'=>t('Owner email'),
        '#type'=>'textfield',        
        '#default_value'=>$registrar_propietario_email,
        //'#attributes'=>array('readonly'=>'readonly'),
    );
    /*$idiomas_options=red_registrar_get_idiomas_options();
    $form['registrar_grupo_idiomas']=array('#type'=>'select',
        '#title'=>t('Languages'),
        '#options'=>$idiomas_options,
        '#multiple'=>TRUE,
        //'#required'=>TRUE,
    );*/
    $idioma_defecto_options=red_registrar_get_idioma_defecto_options();
    $registrar_grupo_idioma_defecto=$node->og_language;
    $form['registrar_grupo_idioma_defecto']=array('#type'=>'select',
        '#title'=>t('Main language'),
        '#options'=>$idioma_defecto_options,
        '#default_value'=>$registrar_grupo_idioma_defecto,    
    );
    $form['registrar_grupo_organizacion']=array('#type'=>'textfield',
        '#title'=>t('Organisation'),
        //'#required'=>TRUE),
    );    
    $form['registrar_grupo_pais']=array('#type'=>'select',
        '#title'=>t('Country'),
        '#options'=>red_registrar_pais_inc_get_pais_options(),
        //'#required'=>TRUE,
    );
    $form['registrar_grupo_sitio_web']=array('#type'=>'textfield',
        '#title'=>t('Website'),
        //'#required'=>TRUE,
    );
    $form['tags']=array('#type'=>'textfield',
        '#title'=>t('Tags'),
        '#description'=>t('A comma-separated list of geographic and thematic terms'),
        //'#required'=>TRUE,
    ); 
}
function red_compartir_registrar_grupo_get_registrar_grupo_enviar($grupo_enviar,$grupo_node){
    global $base_url,$base_root;
    $result=new stdClass();
    $my_user=user_load($grupo_enviar->uid);
    $result->local_nid=$grupo_enviar->nid;
    $result->local_vid=$grupo_enviar->vid;
    $result->local_uid=$grupo_enviar->uid;    
    $result->grupo_title=$grupo_enviar->grupo_title;
    $result->group_owner_username=$my_user->name;
    $result->group_owner_nombre=$my_user->profile_nombre;
    $result->group_owner_apellidos=$my_user->profile_apellidos;
    $result->group_owner_email=$my_user->mail;
    $result->group_main_language=$grupo_node->og_language;
    $result->organisation=$grupo_node->field_grupo_regis_organisation[0]['value'];
    $result->country=$grupo_node->field_grupo_regis_country[0]['value'];
    $result->website=$grupo_node->field_grupo_regis_website[0]['value'];
    $result->tags=$grupo_node->field_grupo_regis_tags[0]['value'];
    //intelsat-2016
    $result->tags_geograficos=$grupo_node->field_grupo_regis_tags_geo[0]['value'];
    $red_servidor_registrar_local_string=variable_get('red_servidor_registrar_local','');
    $red_registrar_empresa_post_string=variable_get('red_registrar_empresa_post','');
    $red_servidor_registrar_local_node=red_registrar_variable_get_red_servidor_registrar_local();        
    $red_registrar_empresa_post=red_registrar_variable_get_red_registrar_empresa_post();
    $result->red_servidor_registrar_local=$red_servidor_registrar_local_string;
    $result->red_registrar_empresa_post=$red_registrar_empresa_post_string;
    $result->red_servidor_registrar_nid=$red_servidor_registrar_local_node->nid;
    $result->red_servidor_registrar_vid=$red_servidor_registrar_local_node->vid;
    $result->group_short_name=$grupo_node->purl;
    $result->base_url=$base_url;
    $result->base_root=$base_root;
    //$result->remote_addr=$_SERVER['REMOTE_ADDR'];
    $result->uniq_id=variable_get('red_registrar_uniq_id','');
    return $result;
}