<?php
function panel_admin_banners_callback(){
    $output='';    
    $headers=array();
    $headers[0]=array('field'=>'node_title','data'=>t('Title'));
    $headers[1]=array('field'=>'field_pisu_value','data'=>t('Order'));
    $headers[2]=t('Actions');    
    
    $where=array();
    $where[]='node.type="banner"';
    if(visualizador_is_red_alerta()){
        $where[]='node.language="es"';
    }else{
        //$where[]='node.language="en"';       
    }
    $my_limit=30;
    
    //$sort='desc';
    $sort='asc';
    //$field='node_created';
    $field='field_pisu_value';
    //$is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Title')){
            $field='node_title';
        }else if($order==t('Order')){
            //$is_numeric=1;
            $field='field_pisu_value';
        }
    }
    
    $sql='SELECT node.nid AS nid,
    node.title AS node_title,
    node.changed AS node_changed, 
    node.created AS node_created,
    content_type_banner.field_pisu_value,
    content_type_banner.field_banner_activado_value,
    node_revisions.body,
    node.tnid
    FROM {node} node 
    LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid 
    LEFT JOIN {content_type_banner} ON node.vid=content_type_banner.vid
    WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);        
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $rows[$kont]=array();
      //$rows[$kont][0]=$r->node_title;
      $node_title=panel_admin_banner_get_title_resumen($r->body);
      $rows[$kont][0]=$node_title;
      $rows[$kont]['node_title']=$node_title;
      $rows[$kont][1]=$r->field_pisu_value;
      $rows[$kont][2]=array('data'=>panel_admin_banners_define_acciones($r),'class'=>'td_nowrap');
      //intelsat-2016
      $rows[$kont]['nid']=$r->nid;
      $rows[$kont]['tnid']=$r->tnid;
      $kont++;
    }
    //
    //intelsat-2016
    $rows=panel_admin_banners_unset_translates($rows);
    if($order==t('Title')){
        $rows=array_ordenatu($rows,'node_title', $sort,0);        
    }
    //intelsat-2016
    //$unset_array=array('node_title');
    $unset_array=array('node_title','nid','tnid');
    $rows=hontza_unset_array($rows,$unset_array);
    
    $rows=my_set_estrategia_pager($rows, $my_limit);
   
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows,array('class'=>'table_panel_admin_items'));
        $output .= theme('pager', NULL, $my_limit);    
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    //drupal_set_title(t('Management of Banners'));
    drupal_set_title(t('List of Banners'));
    //
    return $output;
}
function panel_admin_banners_define_acciones($r){
    $html=array();
    $destination='destination=panel_admin/banners';
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>$destination,'html'=>true));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>$destination,'html'=>true));
    //$html[]=l(my_get_icono_action('viewmag',t('View')),'node/'.$r->nid,array('query'=>$destination,'html'=>true));
    $html[]=panel_admin_banners_define_accion_activado($r,$destination);
    return implode('&nbsp;',$html);
}
function panel_admin_banners_banner_node_form_alter(&$form,&$form_state,$form_id){
    $node=  hontza_get_node_by_form($form);
    if(!isset($form['nid']['#value']) || empty($form['nid']['#value'])){
        $form['options']['sticky']['#default_value']=1;
        $form['field_pisu']['#title']=t('Order');
    }
    $form['field_imagen'][0]['#title']=t('Image');
    $form['field_imagen'][0]['#description']=panel_admin_banners_get_field_imagen_description();
    if(!(isset($node->nid) && !empty($node->nid))){
        $form['title']['#default_value']='banner_'.time();
    }
    $form['title']['#prefix']='<div style="display:none">';
    $form['title']['#suffix']='</div>';
}
function panel_admin_banners_get_field_imagen_description(){
    //$result=t('Ideal size of banner: 360 x 250 pixels. If necessary it will converted to fit to this size.');
    $result=t('Ideal size of banner: 900 x 250 pixels. If necessary it will converted to fit to this size.');    
    return $result;
}
function panel_admin_banners_get_options_activado(){
    $result=array();
    $label=t('Active');
    $result[0]=$label;
    $result[1]=$label;
    return $result;
}
function panel_admin_banners_define_accion_activado($r,$destination){
    if(isset($r->field_banner_activado_value) && !empty($r->field_banner_activado_value)){
        $desactivar='unpublish_post';
        //$desactivar='no-destacar';
        return $html[]=l(my_get_icono_action($desactivar,t('Unpublish')),'panel_admin/banners/desactivar/'.$r->nid,array('query'=>$destination,'html'=>true));
    }
    $activar='publish_post';
    //$activar='destacar';
    return $html[]=l(my_get_icono_action($activar,t('Publish')),'panel_admin/banners/activar/'.$r->nid,array('query'=>$destination,'html'=>true));
}
function panel_admin_banners_activar_callback(){
    $nid=arg(3);
    panel_admin_banners_update_activado($nid,1);
    drupal_goto($_REQUEST['destination']);
}
function panel_admin_banners_desactivar_callback(){
    $nid=arg(3);
    panel_admin_banners_update_activado($nid,0);
    drupal_goto($_REQUEST['destination']);
}
function panel_admin_banners_update_activado($nid,$is_activado){
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        db_query('UPDATE {content_type_banner} SET field_banner_activado_value=%d WHERE nid=%d AND vid=%d',$is_activado,$node->nid,$node->vid);
        hontza_solr_clear_cache_content($nid);
    }
}
function panel_admin_banner_get_title_resumen($resumen,$max=70){
    $result=trim($resumen);
    //$result=utf8_encode($result);    
    $result=trim(strip_tags($result));
    if(strlen($result)>$max){
        $result=substr($result,0,$max).'...';
    }
    return $result;
}
//intelsat-2016
function panel_admin_banners_unset_translates($rows_in){
    $result=array();
    if(!empty($rows_in)){
        foreach($rows_in as $i=>$row){
            //print $row['nid'].'='.$row['tnid'].'<br>';
            if(empty($row['tnid']) || $row['nid']==$row['tnid']){
                $result[]=$row;
            }
        }
    }        
    return $result;        
}