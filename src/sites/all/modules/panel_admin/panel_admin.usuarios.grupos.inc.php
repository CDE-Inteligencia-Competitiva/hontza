<?php
function panel_admin_usuarios_grupos_callback(){
    $output='';
    
    $headers=array();
    //$headers[0]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[0]=t('Owner');
    $headers[1]=t('Editor in chief');
    $headers[2]=t('Group');
    $headers[3]=t('Creation Date');
    $headers[4]=t('Actions');
    
    $my_limit=25;
    
    
    $where=array();
    $where[]='1';
    $where[]='node.type in ("grupo")';

    
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.language AS node_language,
    node.created AS node_created,
    node.uid AS node_uid,
    node_data_field_tematica.field_admin_grupo_uid_value AS group_administrator_uid 
    FROM {node} node
    LEFT JOIN {content_type_grupo} node_data_field_tematica ON node.vid = node_data_field_tematica.vid 
    WHERE '.implode(' AND ',$where).' ORDER BY node_created DESC'; 
    
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    $faktore=50;    
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      //$rows[$kont][0]='<input type="checkbox" id="txek_'.$r->nid.'" name="txek_nid['.$r->nid.']" class="bulk_txek" value="1">';            
      $usuario='';
      $my_user=user_load($r->node_uid);
      if(isset($my_user->uid) && !empty($my_user->uid)){
        $usuario=$my_user->profile_nombre.' '.$my_user->profile_apellidos;
      }  
      $user_link=l($usuario,'hontza_grupos/'.$r->node_uid.'/user_view',array('query'=>drupal_get_destination()));            
      $user_image=hontza_grupos_mi_grupo_get_user_img($r->node_uid,$faktore);
      $user_image=$user_link.'<div>'.$user_image.'</div>';            
      $rows[$kont][0]=$user_image;
      $group_administrator_username=hontza_get_username($r->group_administrator_uid);
      $editor=user_load($r->group_administrator_uid);
      if(isset($editor->uid) && !empty($editor->uid)){
        $editor_usuario=$editor->profile_nombre.' '.$editor->profile_apellidos;
      }
      $editor_link=l($editor_usuario,'hontza_grupos/'.$r->group_administrator_uid.'/user_view',array('query'=>drupal_get_destination()));          
      $group_administrator_user_image=$editor_link.'<div>'.hontza_grupos_mi_grupo_get_user_img($r->group_administrator_uid,$faktore).'</div>';
      $rows[$kont][1]=$group_administrator_user_image;
      $grupo_title=$r->node_title;
      $max=custom_menu_define_grupo_title_max_len();
      $len=strlen($grupo_title);
      if($len>$max){
        $grupo_title=substr($grupo_title,0,$max)."...";
      }
      $rows[$kont][2]=$grupo_title;
      $rows[$kont][3]=date('Y-m-d H:i',$r->node_created);
      $rows[$kont][4]=panel_admin_usuarios_grupos_define_acciones($r);            
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_usuarios_grupos'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Add users to groups'));
    //
    return $output;
}
function panel_admin_usuarios_grupos_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/usuarios-grupos';
    $html[]=l(my_get_icono_action('edit_add',t('Add users')),'og/users/'.$r->nid.'/add_user',array('query'=>$destination,'html'=>true));
    return implode('&nbsp;',$html);
}