<?php
function red_red_facilitadores_callback(){
   drupal_set_title(t('Shared facilitators')); 
   $output=''; 
   $filter_fields=red_red_facilitadores_filter_fields();
    
   $where=array();
   $where[]="1";
  
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=red_red_facilitadores_get_filter_value($f);
           if(!empty($v)){
                switch($f){
                    case 'nid':
                        $where[]="c.nid=".$v;                        
                        break;
                    case 'facilitador_title':
                        $where[]="n.title LIKE '%".$v."%'";
                        break;
                    case 'sareko_id':
                        $where[]="c.sareko_id='".$v."'";
                        break;
                    default:
                        break;
                }
           } 
       }
   }
   
   $where_time=red_red_facilitadores_get_where_fecha_time();
   
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
        if($order=='nid'){
            $field='c.nid';
        }else if($order=='id'){
            $field='c.id';
        }else if($order==t('Title')){
            $field='n.title';
        }else if($order==t('Subdomain')){
            $field='c.sareko_id';
        }else if($order==t('Created')){
            $field='c.fecha';
        } 
    }
    
   $sql="SELECT c.*,n.title as facilitador_title
   FROM {red_servidor_facilitador} c
   LEFT JOIN {node} n ON c.nid=n.nid
   WHERE ".implode(" AND ",$where)."
   ORDER BY ".$field." ".strtoupper($sort);
   //print $sql;
    $res=db_query($sql);
    //
    $headers=array();
    $headers[0]=t('Actions');
    $headers[1]=array('data'=>'id','field'=>'id');
    $headers[2]=array('data'=>'nid','field'=>'nid');
    $headers[3]=array('data'=>t('Title'),'field'=>'facilitador_title');
    $headers[4]=array('data'=>t('Subdomain'),'field'=>'sareko_id');
    $headers[5]=array('data'=>t('Created'),'field'=>'fecha');
    $rows=array();
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $node=node_load($r->nid);  
      if(isset($node->nid) && !empty($node->nid)){
        $rows[$kont]=array();
        $rows[$kont][0]=red_red_facilitadores_define_acciones($r);
        $rows[$kont][1]=$r->id;
        $rows[$kont][2]=$r->nid;
        $rows[$kont][3]=$node->title;
        $rows[$kont][4]=red_red_set_subdomain_name($r->sareko_id);
        $rows[$kont][5]=date('Y-m-d H:i',$r->fecha);
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
    //return red_red_facilitadores_header().drupal_get_form('hontza_gestion_usuarios_bulk_form',array($output));
    $output=red_red_pantallas_menu().red_red_facilitadores_header().$output;
    return $output;
}
function red_red_facilitadores_header(){
    $html=array();
    $html[]=red_red_facilitadores_filtro();
    return implode('',$html);
}
function red_red_facilitadores_filter_fields(){
    $filter_fields=array('nid','facilitador_title','sareko_id','fecha_inicio','fecha_fin');
    return $filter_fields;
}
function red_red_facilitadores_filtro(){
    my_add_buscar_js();
    return drupal_get_form('red_red_facilitadores_filtro_form');
}
function red_red_facilitadores_define_acciones($r){
    $html=array();
    $html[]=l(my_get_icono_action('viewmag', t('View')),'node/'.$r->nid,array('html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('target'=>'_blank','title'=>t('View'),'alt'=>t('View'))));    
    if(user_access('edit red_red source')){
        $html[]=l(my_get_icono_action('delete', t('Delete')),'red_red/facilitador/'.$r->id.'/delete'.$atzizkia,array('html'=>true,'query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Delete'),'alt'=>t('Delete'))));                
    }
    return implode('&nbsp;',$html); 
}
function red_red_facilitadores_get_where_fecha_time(){
    return hontza_get_usuarios_acceso_where_time('c.fecha','red_red_facilitadores','fecha_inicio','fecha_fin');
}
function red_red_facilitadores_filtro_form(){
    $form=array();
    $fs_title=t('Search');
    if(!red_red_facilitadores_is_filter_activated()){
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';
    }else{
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }
    //    
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['nid']=
        array('#type'=>'textfield',
        '#title'=>'nid',
        "#default_value"=>red_red_facilitadores_get_filter_value('nid'));    
    $form['file_buscar_fs']['facilitador_title']=
        array('#type'=>'textfield',
        '#title'=>t('Title'),
        "#default_value"=>red_red_facilitadores_get_filter_value('facilitador_title'));
    $form['file_buscar_fs']['sareko_id']=
        array('#type'=>'select',
        '#title'=>t('Sudomain'),
        '#options'=>red_red_define_subdomain_options(),    
        "#default_value"=>red_red_facilitadores_get_filter_value('sareko_id'));
    //
    $fecha_inicio=red_red_facilitadores_get_filter_value('fecha_inicio');
    $fecha_fin=red_red_facilitadores_get_filter_value('fecha_fin');
    $form['file_buscar_fs']['fecha_inicio']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('From'),
                        '#default_value'=>  $fecha_inicio);
    $form['file_buscar_fs']['fecha_fin']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('To'),
			'#default_value'=>$fecha_fin);    
    //
    $form['file_buscar_fs']['submit']=array('#type'=>'submit','#value'=>t('Search'),'#name'=>'buscar','#prefix'=>'<div id="red_red_facilitadores_filtro_botones">');
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar','#suffix'=>'</div>');
    return $form;
}
function red_red_facilitadores_get_filter_value($f){
    return hontza_get_gestion_usuarios_filter_value($f,'red_red_facilitadores');
}
function red_red_facilitadores_is_filter_activated(){
    $filter_fields=red_red_facilitadores_filter_fields();
    if(!empty($filter_fields)){
        foreach($filter_fields as $i=>$f){
            $v=red_red_facilitadores_get_filter_value($f);
            if(!empty($v)){
                return 1;
            }
        }    
    }
    return 0;
}
function red_red_facilitadores_filtro_form_submit(&$form,&$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION['red_red_facilitadores']['filter']) && !empty($_SESSION['red_red_facilitadores']['filter'])){
                unset($_SESSION['red_red_facilitadores']['filter']);
            }
        }else{
            $_SESSION['red_red_facilitadores']['filter']=array();
            $fields=red_red_facilitadores_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];
                    if(!empty($v)){
                        $_SESSION['red_red_facilitadores']['filter'][$f]=$v;
                    }
                }
            }
        }
    } 
}
function red_red_facilitador_delete_form(){
   $form=array();
   $id=arg(2);
   $node=red_red_facilitador_get_node($id);      
   $form['my_id']=array(
    '#type'=>'hidden',
    '#default_value' =>$id,
   );         
   //
    $borrar_msg.='';
    if(isset($node->nid) && !empty($node->nid)){
        $form['facilitador_nid']=array(
            '#type'=>'hidden',
            '#default_value' =>$node->nid,
        );
        $borrar_msg.='<b>'.$node->title.'</b><BR>';
    }
    $borrar_msg.=t('This action cannot be undone.').'<BR>';    
    $form['borrar_msg']=array(
        '#value'=>$borrar_msg,
    );

    $form['red_red_facilitador_delete_btn_submit']=array(
  '#name'=>'red_red_facilitador_delete_btn_submit',      
  '#type' => 'submit',
  '#value' => t('Delete'),
);
    
  
$my_destination='red_red/facilitadores';
    

$form['red_red_facilitador_delete_volver']=array(
  '#value' => l(t('Cancel'),$my_destination),  

);

    return $form;
}
function red_red_facilitador_get_node($id){
    $row=red_red_facilitador_get_row($id);
    if(isset($row->nid) && !empty($row->nid)){
        $node=node_load($row->nid);
        return $node;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_red_facilitador_get_row($id){
    $result=red_red_facilitador_get_array($id);
    if(count($result)>0){
        return $result[0];
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function red_red_facilitador_get_array($id=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($id)){
        $where[]='c.id='.$id;
    }
    $sql='SELECT * FROM {red_servidor_facilitador} c WHERE '.implode(' AND ',$where).' ORDER BY c.fecha DESC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function red_red_facilitador_delete_form_submit(&$form, &$form_state){
     if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        if(strcmp($name,'red_red_facilitador_delete_btn_submit')==0){
            $values=$form_state['values'];
            if(isset($values['my_id']) && !empty($values['my_id'])){
                red_red_facilitador_delete($values['my_id']);
            }
            //
            if(isset($values['facilitador_nid']) && !empty($values['facilitador_nid'])){
                node_delete($values['facilitador_nid']);
            }
        }
    }
    drupal_goto('red_red/facilitadores');
}
function red_red_facilitador_delete($id){
    db_query('DELETE FROM {red_servidor_facilitador} WHERE id=%d',$id);
}