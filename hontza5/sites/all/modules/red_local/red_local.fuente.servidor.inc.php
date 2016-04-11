<?php
function red_local_fuentes_servidor_callback(){
   drupal_set_title(t('Sources downloaded from Server')); 
   $output=''; 
   $filter_fields=red_local_fuentes_servidor_filter_fields();
    
   $where=array();
   $where[]="1";
   //
   /*$group_nid_array=red_local_get_user_group_nid_array();
   if(!empty($group_nid_array)){
       $where[]='f.group_nid IN('.implode(',',$group_nid_array).')';
   }else{*/
   $my_grupo=og_get_group_context();
   if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where[]='f.group_nid='.$my_grupo->nid;
   }else{
        return t('No group selected');  
   }
   //
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=red_local_fuentes_servidor_get_filter_value($f);
           if(!empty($v)){
                switch($f){
                    case 'fuente_title':
                        $where[]="n.title LIKE '%%".$v."%%'";
                        break;
                    default:
                        break;
                }
           } 
       }
   }
   
   $where_time=red_local_fuentes_servidor_get_where_fecha_time();
   
   if(!empty($where_time)){
       $where[]=$where_time;
   }
   
   $my_limit=40;
    //simulando
    //$my_limit=4000;
        
    $sort='desc';
    $field='id';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //$is_numeric=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='n.title';
        }else if($order==t('Shared')){
            $field='f.fecha';
        } 
    }
    
   $sql="SELECT f.*,n.title as fuente_title
   FROM {node} n
   LEFT JOIN {red_compartir_fuente_descargados} f  ON n.nid=f.nid 
   WHERE ".implode(" AND ",$where)."
   ORDER BY ".$field." ".strtoupper($sort);
   
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]='';
    $headers[1]=t('Title');
    $headers[2]=t('Origin');
    $headers[3]=t('Type');
    //$headers[4]=t('Subdomain');
    $headers[4]=t('Quality');
    $headers[5]=t('Coverage');
    $headers[6]=t('Update');
    /*$headers[8]='A';
    $headers[9]='O';
    $headers[10]='F';*/
    $headers[7]=t('User');
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $node=node_load($r->nid);  
      if(isset($node->nid) && !empty($node->nid)){
        $rows[$kont]=array();
        $rows[$kont][0]=red_local_fuentes_servidor_define_acciones($r);
        $rows[$kont][1]=$node->title;
        $rows[$kont][2]=red_compartir_fuente_get_origin($node->type);
        $rows[$kont][3]=red_compartir_get_fuente_type($node);
        //$rows[$kont][4]=red_local_set_subdomain_name($r->servidor_sareko_id);
        $rows[$kont][4]=hontza_get_fuente_stars_value($node,'calidad',1);
        $rows[$kont][5]=hontza_get_fuente_stars_value($node,'exhaustividad',1);
        $rows[$kont][6]=hontza_get_fuente_stars_value($node,'actualizacion',1);
        /*$rows[$kont][8]=my_get_source_view_api_icon($node,'alchemy');
        $rows[$kont][9]=my_get_source_view_api_icon($node,'opencalais');
        $rows[$kont][10]=my_get_source_view_api_icon($node,'full_text_rss');*/
        $rows[$kont][7]=hontza_get_username($r->uid);
        $kont++;        
      }
    }
    //
    $rows=my_set_estrategia_pager($rows, $my_limit);
    
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_gestion_usuarios'));
        $output .= theme('pager', NULL, $my_limit);    
    }
    else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //return red_local_fuentes_servidor_header().drupal_get_form('hontza_gestion_usuarios_bulk_form',array($output));
    $output=red_local_pantallas_menu().red_local_fuentes_servidor_header().$output;
    return $output;
}
function red_local_fuentes_servidor_filter_fields(){
    $result=array();
    return $result;
}
function red_local_fuentes_servidor_get_where_fecha_time(){
    return '';
}
function red_local_fuentes_servidor_header(){
    $html=array();
    $html[]=red_local_fuentes_servidor_filtro();
    return implode('',$html);
}
function red_local_fuentes_servidor_filtro(){
    return '';
}
function red_local_fuentes_servidor_define_acciones($r){
    $html=array();
    $html[]=l(my_get_icono_action('viewmag', t('View')),'node/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('target'=>'_blank','title'=>t('View'),'alt'=>t('View'))));    
    /*if(user_access('edit red_local source')){
        $html[]=l(my_get_icono_action('delete', t('Delete')),'red_red/fuente/'.$r->id.'/delete'.$atzizkia,array('html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Delete'),'alt'=>t('Delete'))));                
    }*/
    return implode('&nbsp;',$html);    
}