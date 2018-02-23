<?php
function crm_exportar_textos_importar_form(){
	$form = array();

  drupal_set_title(t('Upload CSV'));

  //simulando
  /*$form['my_msg']['#value']='Funcion desactivada';
  return $form;*/
  //
  crm_exportar_categorias_access_denied();
  $options=panel_admin_crm_exportar_clientes_listas_get_options();
  /*echo print_r($options);
  exit();*/
  //simulando
  //$options=array();    
  if(empty($options)){
    $error_msg='<p>'.t('Before executing this command you have to create at least one list').'</p>';
    $error_msg.='<p>'.l(t('Create List'),'panel_admin/crm_exportar/listas/create').'</p>';
    $form['error_msg']['#value']=$error_msg;
    return $form;
  }
  $form['browser'] = array(
    '#type' => 'fieldset',
    //'#title' => t('Browser Upload'),
    '#title' =>t('Select CSV file'),
    '#collapsible' => TRUE,
    //'#description' => t("Upload a CSV file."),
  );
  $file_size ='';
  $form['browser']['upload_file'] = array(
    '#type' => 'file',
    '#title' => t('CSV File'),
    '#size' => 40,
    '#description' => t('Select the CSV file to be upload.').' '.$file_size,
  );

  $form['select_list_fieldset']=array(
    '#type'=>'fieldset',
    '#title'=>t('Select List'),
  );

  /*$form['select_list_fieldset']['is_add']=array(
    '#type'=>'checkbox',
    '#title'=>'<b>'.'Add new searches'.'</b>',
    '#attributes'=>array('checked'=>'checked'),
  );*/

  $is_add_options=crm_exportar_textos_define_is_add_options();
  
  $form['select_list_fieldset']['is_add']=array(
    '#type'=>'select',
    '#title'=>t('Select Option'),
    '#options'=>$is_add_options,
    '#default_value'=>1,
  );

  $form['select_list_fieldset']['crm_exportar_listas_id']=array(
      '#type'=>'select',
      '#title'=>t('List'),
      '#options'=>$options,
      //'#default_value'=>$crm_exportar_listas_row->id,
      //'#required'=>TRUE,
  );

  

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Upload CSV File'),
  );

  $form['#attributes']['enctype'] = "multipart/form-data";
  
  return $form;
}
function crm_exportar_textos_importar_form_submit($form, &$form_state) {
    if(isset($_FILES) && !empty($_FILES)){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            if(isset($_FILES['files']['type']) && !empty($_FILES['files']['type']) && ($_FILES['files']['type']['upload_file']=='text/csv' ||estrategia_importar_is_csv_by_name($_FILES['files']['name']['upload_file']))){
                $file_path='/tmp/'.$_FILES['files']['name']['upload_file'];
                move_uploaded_file($_FILES['files']['tmp_name']['upload_file'],$file_path);
                
                if(isset($form_state['values']['crm_exportar_listas_id']) && !empty($form_state['values']['crm_exportar_listas_id'])){
                  $crm_exportar_listas_id=$form_state['values']['crm_exportar_listas_id'];
                  $is_add=0;
                  if(isset($form_state['values']['is_add']) && !empty($form_state['values']['is_add'])){
                    $is_add=$form_state['values']['is_add'];
                  }
                  crm_exportar_textos_importar_csv($file_path,$form_state,$crm_exportar_listas_id,$is_add);
                }                         
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }        
}
function crm_exportar_textos_importar_csv($file_path,$form_state,$crm_exportar_listas_id,$is_add=1) {
    global $user;
    $lineas=estrategia_get_lineas_csv($file_path,"",1);
    $lineas=crm_exportar_textos_get_lineas_formato_estandar_csv($lineas);
    $lineas=crm_exportar_textos_get_decode_csv($file_path,$lineas);
    /*echo print_r($lineas,1);
    exit();*/    
    $changed=time();          
    if(!empty($lineas)){
        $key='panel_admin_crm_exportar_clientes_filtro';
        if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
            unset($_SESSION[$key]['filter']);
        }
        $_SESSION[$key]['filter']=array();
        $_SESSION[$key]['filter']['crm_exportar_listas_id']=$crm_exportar_listas_id;
        
        //db_query('DELETE FROM {crm_exportar_textos} WHERE 1');
        if(!$is_add){
          panel_admin_crm_exportar_listas_delete_crm_exportar_textos_listas_csv($crm_exportar_listas_id);
        }        
        //exit();
        foreach($lineas as $i=>$row){
          /*$name=trim($row[0]);
          $value=trim($row[1]);*/
          $name=trim($row[4]);
          $columna1=trim($row[5]);
          $account_number=trim($row[6]);
          $value=trim($row[7]);
          $booleano=trim($row[8]);
          db_query('INSERT INTO {crm_exportar_textos}(name,value,changed,uid,columna1,account_number,booleano) VALUES("%s","%s",%d,%d,"%s","%s","%s")',$name,$value,$changed,$user->uid,$columna1,$account_number,$booleano);
          //drupal_set_message($name.'='.$value);
          $crm_exportar_textos_id=db_last_insert_id('crm_exportar_textos','id');
          panel_admin_crm_exportar_listas_crm_exportar_textos_listas_save($crm_exportar_textos_id,$crm_exportar_listas_id);
        }
    }
    drupal_goto('panel_admin/crm_exportar/clientes');        
}
function crm_exportar_textos_links_callback(){
  $html=array();
  drupal_set_title(t('List of Searches'));
  crm_exportar_categorias_access_denied();
  $html[]='Desactivado';
  $result=implode('',$html);
  return $result;

  if(crm_exportar_is_crm_exportar_texto()){
    if(db_table_exists('crm_exportar_textos')){
        crm_exportar_textos_exportar_noticias_kont();
        $textos_array=crm_exportar_textos_get_array();
        if(!empty($textos_array)){
          //$html[]='<ul>';
          $html[]='<table>';
          $con_resultados=0;
          $sin_resultados=0;
          $html[]='<tr>';
          $html[]='<th>';
          $html[]=t('Tag');
          $html[]='</th>';
          $html[]='<th>';
          $html[]=t('Boolean');
          $html[]='</th>';
          $html[]='<th>';
          $html[]=t('Results');
          $html[]='</th>';
          $html[]='</tr>';          
          foreach($textos_array as $i=>$row){
            $url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            //$html[]='<li>';
            $html[]='<tr>';
            $html[]='<td>';
            //$html[]=l($row->name,$url,array('attributes'=>array('target'=>'_blank')));
            $html[]=$row->value;
            //$html[]='</li>';
            $html[]='</td>';
            $html[]='<td>';
            $html[]=$row->booleano;            
            $html[]='</td>';
            $html[]='<td>';
            $html[]=$row->kont;            
            $html[]='</td>';
            $html[]='</tr>';
            if($row->kont>0){
              $con_resultados=$con_resultados+1;
            }else{
              $sin_resultados=$sin_resultados+1;
            }
          }
          //$html[]='</ul>';
          $html[]='</table>';
          $resumen_html[]='<div>';
          $resumen_html[]='<label>';
          $resumen_html[]='<b>';          
          $resumen_html[]='Con resultados:';
          $resumen_html[]='</b>';
          $resumen_html[]='</label>';
          $resumen_html[]='&nbsp;';
          $resumen_html[]=$con_resultados;
          $resumen_html[]='</div>';
          $resumen_html[]='<div>';
          $resumen_html[]='<label>';
          $resumen_html[]='<b>';          
          $resumen_html[]='Sin resultados:';
          $resumen_html[]='</b>';
          $resumen_html[]='</label>';
          $resumen_html[]='&nbsp;';
          $resumen_html[]=$sin_resultados;
          $resumen_html[]='</div>';
          /*if(is_super_admin()){
            $resumen_html[]='<div>';          
            $resumen_html[]=l(t('Update results'),'crm_exportar/textos/exportar_noticias_kont');
            $resumen_html[]='</div>';
          }*/  
        }
    }  
  }
  $result=implode('',$resumen_html).implode('',$html);
  return $result;
}
//intelsat-2017-is-active
//function crm_exportar_textos_get_array($where_in='',$is_todas=0,$id=''){
function crm_exportar_textos_get_array($where_in='',$is_todas=0,$id='',$is_crm_activar_cliente=0){  
  $result=array();
  $where=array();
  if(empty($where_in)){
    $where[]='1';
  }else{
    $where=$where_in;
  }
  $table='crm_exportar_textos';
  if($is_todas){
    $table='backup_crm_exportar_textos';
    if(crm_exportar_is_crm_exportar_tag()){
      $table='crm_exportar_textos';
    }
  }
  if(!empty($id)){
    $where[]=$table.'.id='.$id;
  }
  
  //intelsat-2017-is-active
  if($is_crm_activar_cliente){
    if(crm_exportar_is_crm_activar_cliente()){
      $where[]='crm_exportar_textos.is_active=1';
    }
  }
  

  if(crm_exportar_textos_is_crm_exportar_listas($crm_exportar_listas_id)){
   if(empty($id)){ 
    $where[]='crm_exportar_textos_listas.crm_exportar_listas_id='.$crm_exportar_listas_id;
   }
   $sql='SELECT crm_exportar_textos.* 
    FROM {crm_exportar_textos} 
    LEFT JOIN {crm_exportar_textos_listas} ON crm_exportar_textos.id=crm_exportar_textos_listas.crm_exportar_textos_id 
    WHERE '.implode(' AND ',$where).'
    GROUP BY crm_exportar_textos.id 
    ORDER BY crm_exportar_textos.id ASC';
    //print 'sql='.$sql;exit();
    $res=db_query($sql);
  }else{  
    $res=db_query('SELECT * FROM {'.$table.'} WHERE '.implode(' AND ',$where).' ORDER BY id ASC');
  }
  while($row=db_fetch_object($res)){
    $result[]=$row;
  }
  /*if(user_access('root')){
    print count($result);exit();
    echo print_r($result,1);
    exit();
  }*/  
  return $result;
}
function crm_exportar_textos_autocomplete_callback($string){
  $matches = array();
  $result = db_query_range("SELECT name FROM {crm_exportar_textos} WHERE LOWER(name) LIKE LOWER('%s%')", $string, 0, 10);
  while ($data = db_fetch_object($result)) {
    $matches[$data->name] = check_plain($data->name);
  }
  print drupal_to_js($matches);
  exit();
}
function crm_exportar_textos_exportar_noticias_kont_callback(){
  global $user;
  if(!is_super_admin()){
    drupal_access_denied();
    exit();
  }
  drupal_set_title(t('Update results'));
  crm_exportar_textos_exportar_noticias_kont();
  drupal_set_message('Resultados actualizados: '.date('Y-m-d H:i:s'));
  drupal_goto('crm_exportar/textos/links');            
}
function crm_exportar_textos_exportar_todas_noticias_callback(){
  $output='';
  crm_exportar_ip_access_denied();
  red_solr_inc_apachesolr_index_batch_index_remaining_callback();
  $output=crm_exportar_textos_exportar_todas_noticias();
  drupal_set_title(t('Tagging & Export XML'));
  return $output;
}
function crm_exportar_textos_add_nid_tag_array($result,$row,&$nid_tag_array){
  if(!empty($result)){
    foreach($result as $i=>$nid){
      $my_row=new stdClass();
      $my_row->nid=$nid;
      $my_row->row=$row;
      $nid_tag_array[]=$my_row;
    }
  }
}
function crm_exportar_textos_get_nids_by_crm_all($nids,$crm,&$node_array,&$crm_id_array,&$tag_array,$is_todas=0,$nid_tag_array=array()){
  $result=array();  
          $node_array=array();
          if(!empty($nids)){
              foreach($nids as $i=>$nid){
                 $node=new stdClass();
                  $node=node_load($nid);
                   $my_row=$nid_tag_array[$i];                  
                                        $result[]=$nid;
                                        $crm_id_array[]=crm_exportar_create_crm_id($node->nid,'');
                                        $node_array[]=$node;
                                        $tag_array[]=$my_row->row->name;                                 
              }
          }
  return $result;        
}
function crm_exportar_textos_get_teaser($description,$len=300){
   $result=strip_tags($description);
   $result=substr($result, 0,$len);
   $index=strrpos($result, " ");
   $result=substr($result, 0,$index); 
   //$result.="...";
   return $result;
}
function crm_exportar_textos_get_links(){
  $html=array();
  if(!hontza_is_user_anonimo()){
    if(!crm_exportar_is_crm_exportar_categorias()){
      $html[]=l(t('List of Searches'),'crm_exportar/textos/links',array('attributes'=>array('target'=>'_blank')));
    }
  }
  //$html[]=l(t('Automatic tag'),'crm_exportar/tags/automatic',array('attributes'=>array('target'=>'_blank')));
  return implode('&nbsp;|&nbsp;',$html);
}
function crm_exportar_textos_get_tags($i,$tag_array,$nid_tag_array,$nid,$nid_duplicate_news_array){
  $output="";
  if(crm_exportar_is_crm_exportar_texto()){
    $row=$nid_tag_array[$i]->row;
    $output.="<tags>\n";
    if(isset($_REQUEST['is_duplicate_news']) && !empty($_REQUEST['is_duplicate_news'])){
      $output.="\t<tag>\n";
      //$output.="\t\t<title>". check_plain($tag_array[$i])."</title>\n";
      $output.="\t\t<title>". check_plain($row->value)."</title>\n";      
      $output.="\t\t<id>".$row->account_number."</id>\n";
      $output.="\t</tag>\n";    
    }else{
      /*foreach($nid_tag_array as $i=>$my_row){
        echo print_r($my_row,1);
        exit();
      }*/
      if(isset($nid_duplicate_news_array[$nid]) && isset($nid_duplicate_news_array[$nid]['nid_tag_array'])){
        if(!empty($nid_duplicate_news_array[$nid]['nid_tag_array'])){
          foreach($nid_duplicate_news_array[$nid]['nid_tag_array'] as $i=>$my_row){
            $row=$my_row->row;
            $output.="\t<tag>\n";
            $output.="\t\t<title>". check_plain($row->value)."</title>\n";
            $output.="\t\t<id>".$row->account_number."</id>\n";
            $output.="\t</tag>\n"; 
          }
        }  
      }
    }  
    $output.="</tags>\n";
  }else{
    $output .= ' <tag>'. check_plain($tag_array[$i]) ."</tag>\n";        
  }  
  return $output;
}
function crm_exportar_textos_add_url_fields($form_state){
  $result='';
  if(crm_exportar_is_crm_exportar_texto()){
    if(isset($form_state['values']['is_duplicate_news']) && !empty($form_state['values']['is_duplicate_news'])){
      $result='?is_duplicate_news=1';
    }
    if(isset($form_state['values']['is_tag']) && !empty($form_state['values']['is_tag'])){
      if(!empty($result)){
          $result.='&is_tag=1';
      }else{
          $result='?is_tag=1';
      }
    }else{
      if(!empty($result)){
          $result.='&is_tag=0';
      }else{
          $result='?is_tag=0';
      }  
    }
    if(isset($form_state['values']['is_export_xml']) && !empty($form_state['values']['is_export_xml'])){
      if(!empty($result)){
          $result.='&is_export_xml=1';
      }else{
          $result='?is_export_xml=1';
      }
    }else{
      if(!empty($result)){
          $result.='&is_export_xml=0';
      }else{
          $result='?is_export_xml=0';
      }  
    }
   if(isset($form_state['values']['grupo_nid']) && !empty($form_state['values']['grupo_nid'])){
       $grupo_nid=crm_exportar_textos_create_grupo_nid_parameter($form_state['values']['grupo_nid']);
       if(!empty($result)){
          $result.='&grupo_nid='.$grupo_nid;
        }else{
            $result='?grupo_nid=.'.$grupo_nid;
        }
    }
    if(isset($form_state['values']['crm_exportar_listas_id']) && !empty($form_state['values']['crm_exportar_listas_id'])){
       $crm_exportar_listas_id=$form_state['values']['crm_exportar_listas_id'];
       if(!empty($result)){
          $result.='&crm_exportar_listas_id='.$crm_exportar_listas_id;
        }else{
            $result='?crm_exportar_listas_id=.'.$crm_exportar_listas_id;
        }
        $result.=crm_exportar_textos_add_url_type_fields($crm_exportar_listas_id,$result);
    }
    return $result;
  }
  return '';
}
function crm_exportar_textos_is_nid_duplicate_news($nid,&$nid_duplicate_news_array,$i,$nid_tag_array){
  if(crm_exportar_is_crm_exportar_texto()){
    if(isset($_REQUEST['is_duplicate_news']) && !empty($_REQUEST['is_duplicate_news'])){
      return 1;
    }
    $values=array_keys($nid_duplicate_news_array);
    if(in_array($nid,$values)){
      $nid_duplicate_news_array[$nid]['nid_tag_array'][]=$nid_tag_array[$i];
      return 0;
    }else{
      $nid_duplicate_news_array[$nid]=array();
      $nid_duplicate_news_array[$nid]['nid_tag_array'][]=$nid_tag_array[$i];      
      return 1;
    }
  }
  return 1;
}
function crm_exportar_textos_get_nid_duplicate_news_array($nid_array,$nid_tag_array){
  $nid_duplicate_news_array=array();
  foreach ($nid_array as $i=>$nid) {
    if(crm_exportar_is_crm_exportar_texto()){
      if(!crm_exportar_textos_is_nid_duplicate_news($nid,$nid_duplicate_news_array,$i,$nid_tag_array)){
        continue;
      }
    }
  }
  return $nid_duplicate_news_array;     
}
function crm_exportar_textos_exportar_todas_noticias($is_automatic_tags=0){
  $html=array();  
  $nid_array=array();
  $result=array();
  $nid_tag_array=array();
  $is_todas=0;
  $my_result='';
  if(crm_exportar_is_crm_exportar_texto()){
    if(!crm_exportar_textos_is_option_selected()){
        return 'Please select at least one option';
    }
    $is_kont=0;
    $is_todas=1;
    $where=array();
    //$where[]='kont>=0';
    //intelsat-2017-is-active    
    //$textos_array=crm_exportar_textos_get_array($where,$is_todas);
    $is_crm_activar_cliente=crm_exportar_is_crm_activar_cliente();
    $textos_array=crm_exportar_textos_get_array($where,$is_todas,'',$is_crm_activar_cliente);
    //print count($textos_array);exit();
    /*echo print_r($textos_array,1);
    exit();*/    
        if(!empty($textos_array)){
          foreach($textos_array as $i=>$row){
            //simulando
            /*if(empty($row->booleano)){
              continue;
            }*/
            //$url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            $my_result=crm_exportar_exportar_noticias($row->name,$is_kont,$is_todas,$row);
            $result=$my_result['nid_array'];
            crm_exportar_textos_add_nid_tag_array($result,$row,$nid_tag_array);
            $nid_array=array_merge($nid_array,$result);                        
          }
        }
  }
  $channel=array();
  $is_post=0;
  $rss_title='';
  if(crm_exportar_is_crm_fecha_validacion_activado()){
    $crm=crm_exportar_textos_get_rss_title($my_result);
    $rss_title=$crm;

  }else{
    $crm=t('All news');
    $fecha_ini=0;
    $fecha_fin=0;
    if(isset($my_result['fecha_ini']) && !empty($my_result['fecha_ini'])){
      $fecha_ini=date('Y-m-d',strtotime($my_result['fecha_ini']));
    }
    if(isset($my_result['fecha_fin']) && !empty($my_result['fecha_fin'])){
      $fecha_fin=date('Y-m-d',strtotime($my_result['fecha_fin']));
    }
    if(!empty($fecha_ini) || !empty($fecha_fin)){
      $crm.=' : '.$fecha_ini.' / '.$fecha_fin;
    }  
  }

  /*$my_kont_array=array_count_values($nid_array);
  echo print_r($my_kont_array,1);
  exit();*/
  $nid_duplicate_news_array=crm_exportar_textos_get_nid_duplicate_news_array($nid_array,$nid_tag_array);
  //if($is_automatic_tags){
    $automatic_result=array();
    $automatic_result['nid_tag_array']=$nid_tag_array;
    if(crm_exportar_is_crm_exportar_tag()){
      if(isset($_REQUEST['is_tag']) && !empty($_REQUEST['is_tag'])){
        $html[]=t('Tagging has been done');  
        $automatic_result=(object) $automatic_result;
        crm_exportar_tags_automatic_save($automatic_result);
        //red_solr_inc_apachesolr_index_batch_index_remaining_callback();
      }  
    }
    //return $automatic_result;
  //}
  if(isset($_REQUEST['is_export_xml']) && !empty($_REQUEST['is_export_xml'])){
    //echo print_r($my_result,1);exit();

    //print count($nid_array);exit();

    crm_exportar_node_feed($nid_array,$channel,$crm,$is_post,$is_todas,$nid_tag_array,$nid_duplicate_news_array,$rss_title);
    exit();
  }
  return implode('',$html);
}
function crm_exportar_textos_get_row($id){
  $where='';
  $is_todas=1;
  $crm_exportar_textos_array=crm_exportar_textos_get_array($where,$is_todas,$id);
  if(count($crm_exportar_textos_array)>0){
    return $crm_exportar_textos_array[0];
  }
  $my_result=new stdClass();
  return $my_result;
}
function crm_exportar_tags_get_term_data_node_kont($value){
  $term_name='CRM:'.$value;
  $vid=hontza_crm_inc_get_tags_vid();
  $term_row=crm_exportar_tags_taxonomy_get_term_by_name_vid_row($term_name,$vid);
  $nid_array=array();
  $nid_array=panel_admin_crm_exportar_get_term_node_nid_array($term_row->tid,$nid_array);
  $result=count($nid_array);
  return $result;
}
function crm_exportar_textos_exportar_noticias_kont(){
  $is_kont=1;
  $changed=time();
  $is_todas=1;
  if(crm_exportar_is_crm_exportar_texto()){
    if(db_table_exists('crm_exportar_textos')){
        //$textos_array=crm_exportar_textos_get_array();
        $textos_array=crm_exportar_textos_get_array('',$is_todas);
        if(!empty($textos_array)){
          //$html[]='<ul>';
          $html[]='<table>';          
          foreach($textos_array as $i=>$row){
            //$url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            //$kont=crm_exportar_exportar_noticias($row->name,$is_kont);
            $kont=crm_exportar_tags_get_term_data_node_kont($row->value);
            db_query('UPDATE {crm_exportar_textos} SET kont=%d,changed=%d,uid=%d WHERE id=%d',$kont,$changed,$user->uid,$row->id);
            //print $kont.'<br>';
          }
        }
    }
  }
}
function crm_exportar_textos_is_option_selected(){
    $my_array=array('is_tag','is_export_xml');
    if(!empty($my_array)){
        foreach($my_array as $i=>$field){
            if(isset($_REQUEST[$field]) && !empty($_REQUEST[$field])){
                return 1;
            }
        }    
    }    
    return 0;
}
function crm_exportar_textos_crear_url_add_js(){
$js='
   $(document).ready(function()
   {
    $("#edit-is-export-xml").change(function(){
        var is_selected=$(this).attr("checked");
        var edit_is_duplicate_news=$("#edit-is-duplicate-news").parent();
        var edit_is_time=$("#edit-is-time").parent();
        if(is_selected){
            edit_is_duplicate_news.css("display","block");
            edit_is_time.css("display","block");
        }else{
            edit_is_duplicate_news.css("display","none");
            edit_is_time.css("display","none");
        }
    });
   });';
    drupal_add_js($js,'inline');
}
function crm_exportar_textos_get_grupo_options(){
    $result=array();
    $grupo_array=crm_exportar_textos_get_usuario_grupo_array();
    if(!empty($grupo_array)){
        foreach($grupo_array as $i=>$row){
            $result[$row->nid]=$row->title;
        }
    }
    return $result;
}
function crm_exportar_textos_get_usuario_grupo_array($is_con_usuario=1,$is_node_load=0){
    global $user;
    $result=array();
    $where=array();
    $where[]='1';
    $where[]='node.type="grupo"';
    //if(!is_super_admin()){
      if($is_con_usuario){
        $where[]='og_uid.uid='.$user->uid;
      }
    //}
    $sql='SELECT node.nid,node.title 
    FROM {node} node
    LEFT JOIN {og_uid} og_uid ON node.nid=og_uid.nid
    WHERE '.implode(' AND ',$where).'
    GROUP BY node.nid';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
      if($is_node_load){
        $result[]=node_load($row->nid);
      }else{  
        $result[]=$row;
      }  
    }
    return $result;
}
function crm_exportar_textos_create_grupo_nid_parameter($grupo_nid_array){
    $result='';
    if(!empty($grupo_nid_array)){
        $result=array_keys($grupo_nid_array);
        $result=implode(',',$result);
    }
    return $result;
}
function crm_exportar_textos_get_grupo_nid_default(){
    $grupo_nid=143311;
        //if(hontza_is_sareko_id('TAGS')){
        //  $grupo_nid=140432;
        //}
        if(hontza_is_sareko_id('TAGS_OTROALERTA')){
          $grupo_nid=140432;
        }
    return $grupo_nid;    
}
function crm_exportar_textos_is_nid_duplicate_news_feed($nid,&$nid_feed_array){
  if(crm_exportar_is_crm_exportar_texto()){
    if(isset($_REQUEST['is_duplicate_news']) && !empty($_REQUEST['is_duplicate_news'])){
      return 1;
    }
    if(in_array($nid,$nid_feed_array)){
      return 0;
    }else{
      $nid_feed_array[]=$nid;      
      return 1;
    }
  }
  return 1;
}
function crm_exportar_textos_get_lineas_formato_estandar_csv($lineas){
  $result=array();
  if(isset($lineas[0]) && !empty($lineas[0])){
    if(count($lineas[0])==4){
      foreach($lineas as $i=>$row){
        $result[]=crm_exportar_textos_create_formato_estandar_csv_row($row);
      }
      return $result;
    }
  }
  return $lineas;
}
function crm_exportar_textos_create_formato_estandar_csv_row($row){
  $result=array();
  $num=8;
  for($i=0;$i<=$num;$i++){
    $result[$i]='';
  }
  $result[4]=$row[1];
  $result[6]=$row[0];
  $result[7]=$row[2];
  $result[8]=$row[3];
  return $result;        
}
function crm_exportar_textos_get_crm_exportar_listas_id_default(){
  $result='';
  $options=panel_admin_crm_exportar_clientes_listas_get_options();
  if(!empty($options)){
    $values=array_keys($options);
    if(count($values)>0){
      $result=$values[0];
    }
  }
  return $result;              
}
function crm_exportar_textos_is_crm_exportar_listas(&$crm_exportar_listas_id){
  $crm_exportar_listas_id='';
  if(isset($_REQUEST['crm_exportar_listas_id']) && !empty($_REQUEST['crm_exportar_listas_id'])){
    $crm_exportar_listas_id=$_REQUEST['crm_exportar_listas_id'];
    return 1;
  }
  if(db_table_exists('crm_exportar_listas')){
    $crm_exportar_listas_id=panel_admin_crm_exportar_listas_get_default_crm_exportar_listas_id();
    if(!empty($crm_exportar_listas_id)){
      //print 'crm_exportar_listas_id='.$crm_exportar_listas_id;exit();
      return 1;
    }
    $crm_exportar_listas_id=1;
    return 1; 
  }
  return 0;
}
function crm_exportar_textos_get_nid_array_by_fecha_validacion($backup_fecha_ini,$backup_fecha_fin,$nid_array){
  $result=array();
  //print $backup_fecha_ini;exit();
  if(!empty($nid_array)){
    foreach($nid_array as $i=>$nid){
      if(crm_exportar_textos_in_fecha_validacion($backup_fecha_ini,$backup_fecha_fin,$nid)){
        $result[]=$nid;
      }
    }
  }
  return $result;
}
function crm_exportar_textos_in_fecha_validacion($backup_fecha_ini,$backup_fecha_fin,$nid){
  $where=array();
  if(!empty($backup_fecha_ini) || !empty($backup_fecha_fin)){
    if(!empty($backup_fecha_ini)){
      $where[]='flag_content.timestamp>='.strtotime($backup_fecha_ini);
    }
    if(!empty($backup_fecha_fin)){
      $time=strtotime($backup_fecha_fin)+3600*24;
      $fecha_fin=date('Y-m-d 00:00:00',$time);
      $where[]='flag_content.timestamp<'.strtotime($fecha_fin);
    }
    /*echo print_r($where,1);
    exit();*/
    $where[]='node.type IN("item","noticia")';
    $where[]='flag_content.fid=2';
    $where[]='node.nid='.$nid;
    $sql='SELECT node.nid 
    FROM {node} node
    LEFT JOIN {flag_content} flag_content ON node.nid=flag_content.content_id
    WHERE '.implode(' AND ',$where).'
    GROUP BY node.nid';
    $res=db_query($sql);
    //print $sql.'<br>';exit();
    while($row=db_fetch_object($res)){
      return 1;
    }
    return 0;
  }
  return 1;
}
function crm_exportar_textos_get_rss_title($my_result_in){
    $my_result=$my_result_in;
    if(isset($my_result['backup_fecha_ini']) && !empty($my_result['backup_fecha_ini'])){
      $my_result['fecha_ini']=$my_result['backup_fecha_ini'];
    }  
    if(isset($my_result['backup_fecha_fin']) && !empty($my_result['backup_fecha_fin'])){
      $my_result['fecha_fin']=$my_result['backup_fecha_fin'];
    }
    $crm=t('All news');
    $fecha_ini=0;
    $fecha_fin=0;
    if(isset($my_result['fecha_ini']) && !empty($my_result['fecha_ini'])){
      $fecha_ini=date('Y-m-d',strtotime($my_result['fecha_ini']));
    }
    if(isset($my_result['fecha_fin']) && !empty($my_result['fecha_fin'])){
      $fecha_fin=date('Y-m-d',strtotime($my_result['fecha_fin']));
    }
    if(!empty($fecha_ini) || !empty($fecha_fin)){
      $crm.=' : '.$fecha_ini.' / '.$fecha_fin;
    }
    return $crm;
}
function crm_exportar_textos_get_validation_time($node){
    $flag_content_array=crm_exportar_textos_get_flag_content_array($node->nid);
    if(!empty($flag_content_array)){
      /*echo  print_r($flag_content_array[0],1);
      exit();*/
      return $flag_content_array[0]->timestamp;
    }
    return $node->created;
}
function crm_exportar_textos_get_flag_content_array($nid){
    $result=array();
    $where=array();
    $where[]='node.type IN("item","noticia")';
    $where[]='flag_content.fid=2';
    $where[]='node.nid='.$nid;
    $sql='SELECT node.nid,flag_content.* 
    FROM {node} node
    LEFT JOIN {flag_content} flag_content ON node.nid=flag_content.content_id
    WHERE '.implode(' AND ',$where).'
    GROUP BY node.nid
    ORDER BY flag_content.timestamp DESC';
    $res=db_query($sql);
    //print $sql.'<br>';exit();
    while($row=db_fetch_object($res)){
      $result[]=$row;
    }
    return $result;
}
function crm_exportar_textos_add_fecha_fieldset(&$form){
    if(crm_exportar_is_crm_fecha_validacion_activado()){
      //$form['file_buscar_fs']['fecha_inicio']['#title']=t('Validate Date From');
      //$form['file_buscar_fs']['fecha_fin']['#title']=t('Validate Date To');
      $fecha_inicio_form_field=$form['file_buscar_fs']['fecha_inicio'];
      $fecha_fin_form_field=$form['file_buscar_fs']['fecha_fin'];
      unset($form['file_buscar_fs']['fecha_inicio']);
      unset($form['file_buscar_fs']['fecha_fin']);

      $is_fieldset=1;
      $my_help_icon=crm_exportar_my_help_automatic_tagging(500738,$is_fieldset);
      
      $form['file_buscar_fs']['fecha_validacion_fs']=array(
        '#type'=>'fieldset',
        '#title'=>t('Validation date').$my_help_icon,
      );
      $form['file_buscar_fs']['fecha_validacion_fs']['fecha_inicio']=$fecha_inicio_form_field;
      $form['file_buscar_fs']['fecha_validacion_fs']['fecha_fin']=$fecha_fin_form_field;
      //intelsat-2017
      //$form['file_buscar_fs']['fecha_validacion_fs']['#prefix']='<div style="clear:both;width:40%;">';
      $form['file_buscar_fs']['fecha_validacion_fs']['#prefix']='<div style="clear:both;width:100%;">';
      $form['file_buscar_fs']['fecha_validacion_fs']['#suffix']='</div>';
    }  
}
function crm_exportar_textos_get_decode_csv($file_path,$lineas){
  $result=$lineas;
  //$is_utf8=estrategia_importar_is_utf8($file_path);
  //if($is_utf8){
    if(!empty($lineas)){
      foreach($lineas as $i=>$row){
        if(!empty($row)){
          foreach($row as $kont=>$value){
              if(!empty($value) && !is_numeric($value)){
                $encoding=mb_detect_encoding($value);
                //if(empty($encoding)){
                if($encoding!='ASCII'){
                  //print $value.'<br>';
                  //print $encoding.'<br>';
                  if(!crm_exportar_textos_is_utf8($value)){
                    $result[$i][$kont]=utf8_encode($value);
                  }
                  //print $result[$i][$kont].'<br>';
                }              
              }
          }        
        }
      }
    }
  //}
  //exit();  
  return $result;
}
function crm_exportar_textos_str_to_utf8 ($str) {
    $decoded = utf8_decode($str);
    if (mb_detect_encoding($decoded , 'UTF-8', true) === false)
        return $str;
    return $decoded;
}
function crm_exportar_textos_is_utf8($string)
{
    if (is_array($string))
    {
        $enc = implode('', $string);
        return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }
    else
    {
        return (utf8_encode(utf8_decode($string)) == $string);
    }   
}
function crm_exportar_textos_add_url_type_fields($crm_exportar_listas_id,$result_in){
  if(crm_exportar_is_crm_list_type_activado()){
    if(!empty($crm_exportar_listas_id)){
      $crm_exportar_listas_row=panel_admin_crm_exportar_listas_get_crm_exportar_listas_row($crm_exportar_listas_id);
      if(isset($crm_exportar_listas_row->crm_exportar_listas_types_id) && !empty(isset($crm_exportar_listas_row->crm_exportar_listas_types_id))){
        $row=new stdClass();
        $row->crm_exportar_listas_types_id=$crm_exportar_listas_row->crm_exportar_listas_types_id;
        $type=panel_admin_crm_exportar_types_get_type_name($row);
        if(!empty($result_in)){
          return '&type='.$type;
        }else{
          return '?type='.$type;
        }
      }  
    }
  }
  return '';
}
function crm_exportar_textos_define_is_add_options(){
  $result=array();
  $result[1]=t('Add');
  $result[0]=t('Replace');
  return $result;
}