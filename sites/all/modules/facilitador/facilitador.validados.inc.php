<?php
function facilitador_validados_facilitadores_publicados_callback(){
    $headers=array();
    $headers[0]=t('Expert');
    $headers[1]=t('Services');
    //$headers[2]=t('Actions');
    //        
    $where=array();
    $where[]='1';
    $where[]='users_facilitators.validate=2';
    $sql='SELECT users_facilitators.*
    FROM {users}
    LEFT JOIN {users_facilitators} ON users_facilitators.uid=users.uid
    WHERE '.implode(' AND ',$where).'
    GROUP BY users.uid     
    ORDER BY users_facilitators.id DESC';
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    $faktore=50;
    $my_limit=20;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      $user_image=hontza_grupos_mi_grupo_get_user_img($r->uid,$faktore);
      $my_user=user_load($r->uid);
      $user_link='';
      if(isset($my_user->uid) && !empty($my_user->uid)){
        $name=facilitador_get_name($my_user);  
        $user_url='user/'.$r->uid;
        $query=  drupal_get_destination();
        if(hontza_is_user_anonimo()){
            $user_url='red_publica/user/'.$r->uid.'/view';            
        }
        $user_link=l($name,$user_url,array('query'=>$query,'attributes'=>array('target'=>'_blank'),'html'=>true));
      }
      $user_image=$user_link.'<div>'.$user_image.'</div>';
      $rows[$kont][0]=$user_image;
      //$rows[$kont][1]=facilitador_get_services_html($my_user);
      $rows[$kont][1]=facilitador_get_servicios_experto_html($r->servicios_experto);
      //$rows[$kont][2]=array('data'=>facilitador_define_facilitadores_acciones($r),'style'=>'white-space:nowrap;');
      $kont++;
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_facilitador_facilitadores'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //
    return $output;
}