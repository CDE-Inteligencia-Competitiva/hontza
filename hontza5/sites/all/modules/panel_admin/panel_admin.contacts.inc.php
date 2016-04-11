<?php
function panel_admin_contacts_custom_access(){
    if(hontza_canal_rss_is_visualizador_activado()){    
        if(panel_admin_admin_access){
            return true;
        }
    }
    return false;
}
function panel_admin_contacts_callback(){
    $output='';
    
    $headers=array();
    $headers[0]=t('Name');
    $headers[1]=t('Mail');
    $headers[2]=t('Actions');
    
    
    $my_limit=30;
    $sql=panel_admin_contacts_create_contact_array_sql();
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $rows[$kont][0]=$r->node_title;
      //$rows[$kont][1]=$r->field_contacto_correo_email;
      $rows[$kont][1]=$r->contacto_correo;
      $rows[$kont][2]=panel_admin_contacts_define_acciones($r);
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('List of Contacts'));
    //
    return $output;
}
function panel_admin_contacts_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/contacts';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}
function panel_admin_contacts_visualizador_contacto_node_form_alter(&$form,&$form_state,$form_id){
    if(isset($form['title'])){
        $form['title']['#title']=t('Name');
    }
    panel_admin_contacts_unset_visualizador_contacto_node_form_fields($form,$form_state,$form_id);
    $node=hontza_get_node_by_form($form);
    $form['contacto_correo']=array(
        '#type'=>'textfield',
        '#title'=>t('Mail'),
        '#required'=>true,
        '#default_value'=>panel_admin_contacts_get_value_visualizador_contacto_correo($node),
    );
    $form['contacto_url']=array(
        '#type'=>'textfield',
        '#title'=>t('Url'),
        '#required'=>true,
        '#default_value'=>panel_admin_contacts_get_value_visualizador_contacto_url($node),
    );
}
function panel_admin_contacts_unset_visualizador_contacto_node_form_fields(&$form,&$form_state,$form_id){
    if($form_id=='visualizador_contacto_node_form'){
        $unset_array=array('comment_settings','path','author','options','menu','revision_information','body_field');
        if(!empty($unset_array)){    
            foreach($unset_array as $i=>$unset_field){
                if(isset($form[$unset_field])){
                    //unset($form[$unset_field]);
                    $form[$unset_field]['#prefix']='<div style="display:none">';
                    $form[$unset_field]['#suffix']='</div>';
                }
            }
        }
        if(isset($form['buttons']['preview'])){
            unset($form['buttons']['preview']);
        }
    }
}
function panel_admin_contacts_create_contact_array_sql(){
    $where=array();
    $where[]='node.status=1';
    $where[]='node.type="visualizador_contacto"';
                
    /*$sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.created AS node_created, 
    content_type_visualizador_contacto.field_contacto_correo_email
    FROM {node} node
    LEFT JOIN {content_type_visualizador_contacto} ON content_type_visualizador_contacto.vid=node.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created ASC';*/
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.created AS node_created,
    visualizador_contactos.contacto_correo 
    FROM {node} node 
    LEFT JOIN {visualizador_contactos} visualizador_contactos ON visualizador_contactos.vid=node.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created ASC';
    return $sql;
} 
function panel_admin_contacts_get_contacts_array(){
    $sql=panel_admin_contacts_create_contact_array_sql();
    $result=array();
    $res=db_query($sql);
    while($r=db_fetch_object($res)){
        $node=node_load($r->nid);
        if(isset($node->nid) && !empty($node->nid)){
            $result[]=$node;
        }
    }
    return $result;
}
function panel_admin_contacts_get_logo($r){
    $result='';
    if(isset($r->files) && !empty($r->files)){
        foreach($r->files as $i=>$f){
            $result=hontza_get_url_file($f->filepath);    
        }
    }
    if(!empty($result)){
        $result='<img src="'.$result.'" alt="'.$r->title.'" title="'.$r->title.'" height="35">';
    }else{
        $result='<div class="div_logo_vacio">&nbsp;</div>';
    }
    return $result;
}
function panel_admin_contacts_on_visualizador_contacto_save(&$node, $op){
    if(hontza_canal_rss_is_visualizador_activado()){
         panel_admin_contacts_contacto_correo_save($node);
    }
}
function panel_admin_contacts_contacto_correo_save(&$node){
    $r=panel_admin_contacts_get_contacto_correo_row($node->vid,$node->nid);
    if(isset($r->id) && !empty($r->id)){
        db_query('UPDATE {visualizador_contactos} SET contacto_correo="%s",contacto_url="%s" WHERE id=%d',$node->contacto_correo,$node->contacto_url,$r->id);
    }else{
        db_query('INSERT INTO {visualizador_contactos}(vid,nid,contacto_correo,contacto_url) VALUES(%d,%d,"%s","%s")',$node->vid,$node->nid,$node->contacto_correo,$node->contacto_url);
    }
}
function panel_admin_contacts_get_contacto_correo_row($vid,$nid){
    $res=db_query('SELECT * FROM {visualizador_contactos} WHERE nid=%d AND vid=%d',$nid,$vid);
    while($r=db_fetch_object($res)){
        return $r;
    }
    $my_result=new stdClass();
    return $my_result;
}
function panel_admin_contacts_get_value_visualizador_contacto_correo($node){
    $result='';
    if(isset($node->nid) && !empty($node->nid)){
        $r=panel_admin_contacts_get_contacto_correo_row($node->vid,$node->nid);
        if(isset($r->contacto_correo) && !empty($r->contacto_correo)){
            $result=$r->contacto_correo;
        }
    }
    return $result;
}
function panel_admin_contacts_get_value_visualizador_contacto_url($node){
    $result='';
    if(isset($node->nid) && !empty($node->nid)){
        $r=panel_admin_contacts_get_contacto_correo_row($node->vid,$node->nid);
        if(isset($r->contacto_url) && !empty($r->contacto_url)){
            $result=$r->contacto_url;
        }
    }
    return $result;
}
function panel_admin_contacts_edit_title_callback(){
    $result='';
    if(hontza_canal_rss_is_publico_activado()){
        return drupal_get_form('panel_admin_contacts_edit_title_form');
    }
    return $result;
}
function panel_admin_contacts_edit_title_form(){
    $form=array();
    $form['text']=array(
        '#type'=>'textfield',
        '#default_value'=>variable_get('contact_title_text','Contact to the team'),
        '#title'=>t('Contact title'),
        '#required'=>true,
    );
    $form['save_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save'),
    );
    $url_cancel='panel_admin';
    $form['cancel_btn']['#value']=l(t('Cancel'),$url_cancel);
    return $form;
}
function panel_admin_contacts_edit_title_form_submit($form,&$form_state){
   drupal_set_message(t('Title saved'));
   if(isset($form_state['values']['text']) && !empty($form_state['values']['text'])){
       variable_set('contact_title_text',$form_state['values']['text']);
   }
}