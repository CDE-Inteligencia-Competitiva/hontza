<?php
function boletin_report_historico_todos_callback(){
    boletin_report_no_group_selected_denied();
    $output=create_menu_alerta_user();
    $output.=boletin_report_historico_todos_table_todos();
    return $output;
}
function boletin_report_historico_todos_create_historico_table_headers_todos(){
    $headers=array();
    $headers[0]=array('data'=>t('Serie'),'field'=>'titulo');
    $headers[1]=array('data'=>t('Bulletin'),'field'=>'title');
    $headers[2]=array('data'=>t('Date'),'field'=>'fecha_sended');
    $headers[3]=array('data'=>t('Edition'),'field'=>'is_edit');
    $headers[4]=t('Actions');
    return $headers;
}    
function boletin_report_historico_todos_table_todos(){
    $my_limit=20;
    $key='boletin_report_historico_todos_filtro';
    $headers=boletin_report_historico_todos_create_historico_table_headers_todos();
    $rows=array();
    $kont=0;
    $boletin_report_array_id=arg(1);
    //
    $where=array();
    $where[]='1';
    //$where[]='e.boletin_report_array_id='.$boletin_report_array_id;
    $where[]='e.fecha_sended!="0000-00-00 00:00:00"';
    $where[]='NOT br.id IS NULL';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){     
        $where[]='br.grupo_nid='.$my_grupo->nid;
    }
    $filter_fields=boletin_report_historico_todos_filter_fields();
    if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=boletin_report_historico_todos_get_filter_value('texto',$key);
           if(!empty($v)){
                switch($f){
                    case 'texto':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';
                        break;
                    default:
                        break;
                }
           } 
       }
   }
    
    //$sort='asc';
    //$field='titulo';
    $sort='desc';
    $field='fecha_sended';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    $is_numeric=0;
    $is_date_of_last_bulletin=0;
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Serie')){
            $field='titulo';
        }else if($order==t('Bulletin')){
            $field='node_title';
        }else if($order==t('Date')){
            $field='fecha_sended';
        }else if($order==t('Edition')){
            $is_numeric=0;
            $field='is_edit';
        }        
    }
    
    $sql='SELECT e.*,br.titulo,br.activado,node.title AS node_title   
    FROM {boletin_report_array_edit} e
    LEFT JOIN {boletin_report_array} br ON e.boletin_report_array_id=br.id 
    LEFT JOIN {node} node ON node.vid=e.vid 
    LEFT JOIN {node_revisions} node_revisions ON node_revisions.vid=node.vid 
    WHERE '.implode(' AND ',$where).'
    ORDER BY '.$field.' '.$sort;        
    
    $res=db_query($sql);
    
    while ($r = db_fetch_object($res)) {
      $node=node_load($r->nid);
      //$row=array();
      $icono_activado=get_icono_activado_by_row($r);
      $rows[$kont][0]=$icono_activado.'&nbsp;'.$r->titulo;      
      //$rows[$kont][1]=$node->title;
      $rows[$kont][1]=$r->node_title;
      //$rows[$kont][1]=boletin_report_set_sending_value_string($r->is_sended);
      $rows[$kont][2]=$r->fecha_sended;
      $edit_label=boletin_report_set_edition_type_value_string($r->is_edit,1);
      $rows[$kont][3]=$edit_label;
      $rows[$kont][4]=array('data'=>boletin_report_get_acciones_historico($r),'class'=>'td_nowrap');
      $rows[$kont]['edit_label']=$edit_label;
      //$row[$kont]=$row;
      $kont++;
    }

    if($is_date_of_last_bulletin){
        $rows=array_ordenatu($rows,'edit_label',strtolower($sort),$is_numeric);
    }
    $rows=hontza_unset_array($rows,array('edit_label'));        
    $rows=my_set_estrategia_pager($rows, $my_limit);

  if (count($rows)>0) {
    /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  $output=boletin_report_historico_todos_define_filtro().$output;
  return $output;
}
function boletin_report_historico_todos_is_filter_activated(){
    $fields=boletin_report_historico_todos_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['boletin_report_historico_todos_filtro']['filter'][$f]) && !empty($_SESSION['boletin_report_historico_todos_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function boletin_report_historico_todos_define_filtro(){
    my_add_buscar_js();
    return drupal_get_form('boletin_report_historico_todos_filtro_form');
}
function boletin_report_historico_todos_filtro_form(){
    $fs_title=t('Search');
    $key='boletin_report_historico_todos_filtro';
    if(boletin_report_historico_todos_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));
    $form['file_buscar_fs']['texto']=array(
        '#type'=>'textfield',
        '#title'=>t('Text'),
        '#default_value'=>boletin_report_historico_todos_get_filter_value('texto',$key),
    );
    $form['file_buscar_fs']['my_submit']=array(
        '#type'=>'submit',
        '#value'=>t('Apply'),
    );
    $form['file_buscar_fs']['limpiar']=array(
        '#type'=>'submit',
        '#name'=>'limpiar',
        '#value'=>t('Reset'),
    );
    return $form;
}
function boletin_report_historico_todos_filter_fields(){
    $result=array('texto');
    return $result;
}
function boletin_report_historico_todos_filtro_form_submit(&$form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];        
        $key='boletin_report_historico_todos_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=boletin_report_historico_todos_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];
                    if(!empty($v)){
                        $_SESSION[$key]['filter'][$f]=$v;
                    }
                }
            }            
        }
    } 
}
function boletin_report_historico_todos_get_filter_value($field,$key){
    return hontza_get_gestion_usuarios_filter_value($field,$key);
}