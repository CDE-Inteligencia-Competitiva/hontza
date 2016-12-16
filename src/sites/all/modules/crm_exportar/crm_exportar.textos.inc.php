<?php
function crm_exportar_textos_importar_form(){
	$form = array();
  //simulando
  $form['my_msg']['#value']='Funcion desactivada';
  return $form;
  //
  $form['browser'] = array(
    '#type' => 'fieldset',
    '#title' => t('Browser Upload'),
    '#collapsible' => TRUE,
    '#description' => t("Upload a CSV file."),
  );
  $file_size ='';
  $form['browser']['upload_file'] = array(
    '#type' => 'file',
    '#title' => t('CSV File'),
    '#size' => 40,
    '#description' => t('Select the CSV file to be upload.').' '.$file_size,
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
                crm_exportar_textos_importar_csv($file_path,$form_state);        
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }        
}
function crm_exportar_textos_importar_csv($file_path,$form_state) {
    global $user;
    $lineas=estrategia_get_lineas_csv($file_path,"\t");
    $changed=time();          
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
          $name=trim($row[0]);
          $value=trim($row[1]);
          db_query('INSERT INTO {crm_exportar_textos}(name,value,changed,uid) VALUES("%s","%s",%d,%d)',$name,$value,$changed,$user->uid);
          print $name.'='.$value.'<br>';
        }
    }
}
function crm_exportar_textos_links_callback(){
  $html=array();
  if(crm_exportar_is_crm_exportar_texto()){
    if(db_table_exists('crm_exportar_textos')){
        $textos_array=crm_exportar_textos_get_array();
        if(!empty($textos_array)){
          //$html[]='<ul>';
          $html[]='<table>';
          $con_resultados=0;
          $sin_resultados=0;          
          foreach($textos_array as $i=>$row){
            $url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            //$html[]='<li>';
            $html[]='<tr>';
            $html[]='<td>';
            $html[]=l($row->name,$url,array('attributes'=>array('target'=>'_blank')));
            //$html[]='</li>';
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
          if(is_super_admin()){
            $resumen_html[]='<div>';          
            $resumen_html[]=l(t('Update results'),'crm_exportar/textos/exportar_noticias_kont');
            $resumen_html[]='</div>';
          }  
        }
    }  
  }
  $result=implode('',$resumen_html).implode('',$html);
  return $result;
}
function crm_exportar_textos_get_array($where_in='',$is_todas=0){
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
  }
  $res=db_query('SELECT * FROM {'.$table.'} WHERE '.implode(' AND ',$where).' ORDER BY id ASC');
  while($row=db_fetch_object($res)){
    $result[]=$row;
  }
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
  $is_kont=1;
  $changed=time();        
  if(crm_exportar_is_crm_exportar_texto()){
    if(db_table_exists('crm_exportar_textos')){
        $textos_array=crm_exportar_textos_get_array();
        if(!empty($textos_array)){
          //$html[]='<ul>';
          $html[]='<table>';          
          foreach($textos_array as $i=>$row){
            //$url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            $kont=crm_exportar_exportar_noticias($row->name,$is_kont);
            db_query('UPDATE {crm_exportar_textos} SET kont=%d,changed=%d,uid=%d WHERE id=%d',$kont,$changed,$user->uid,$row->id);
            //print $kont.'<br>';
          }
        }
    }
  }
  drupal_set_message('Resultados actualizados: '.date('Y-m-d H:i:s'));
  drupal_goto('crm_exportar/textos/links');            
}
function crm_exportar_textos_exportar_todas_noticias_callback(){
  crm_exportar_ip_access_denied();
  red_solr_inc_apachesolr_index_batch_index_remaining_callback();
  crm_exportar_textos_exportar_todas_noticias();  
  return '';
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
  $html[]=l(t('Clients'),'crm_exportar/textos/links',array('attributes'=>array('target'=>'_blank')));
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
      $output.="\t\t<title>". check_plain($tag_array[$i])."</title>\n";
      $output.="\t\t<id>".$row->id."</id>\n";
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
            $output.="\t\t<title>". check_plain($row->name)."</title>\n";
            $output.="\t\t<id>".$row->id."</id>\n";
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
function crm_exportar_textos_add_is_duplicate_news($form_state){
  if(crm_exportar_is_crm_exportar_texto()){
    if(isset($form_state['values']['is_duplicate_news']) && !empty($form_state['values']['is_duplicate_news'])){
      return '?is_duplicate_news=1';
    }
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
  $nid_array=array();
  $result=array();
  $nid_tag_array=array();
  $is_todas=0;
  if(crm_exportar_is_crm_exportar_texto()){
    $is_kont=0;
    $is_todas=1;
    $where=array();
    $where[]='kont>=0';    
    $textos_array=crm_exportar_textos_get_array($where,$is_todas);
        if(!empty($textos_array)){
          foreach($textos_array as $i=>$row){
            //$url='crm_exportar/exportar_noticias/'.urlencode($row->name).'/0/0';
            $my_result=crm_exportar_exportar_noticias($row->name,$is_kont,$is_todas);
            $result=$my_result['nid_array'];
            crm_exportar_textos_add_nid_tag_array($result,$row,$nid_tag_array);
            $nid_array=array_merge($nid_array,$result);                        
          }
        }
  }
  $channel=array();
  $is_post=0;
  $crm='Todas las noticias';
  $fecha_ini=0;
  $fecha_fin=0;
  if(isset($my_result['fecha_ini']) && !empty($my_result['fecha_ini'])){
    $fecha_ini=date('Y-m-d',strtotime($my_result['fecha_ini']));
  }
  if(isset($my_result['fecha_fin']) && !empty($my_result['fecha_fin'])){
    $fecha_fin=date('Y-m-d',strtotime($my_result['fecha_fin']));
  }
  $crm.=' : '.$fecha_ini.' / '.$fecha_fin;
  /*$my_kont_array=array_count_values($nid_array);
  echo print_r($my_kont_array,1);
  exit();*/
  $nid_duplicate_news_array=crm_exportar_textos_get_nid_duplicate_news_array($nid_array,$nid_tag_array);
  //if($is_automatic_tags){
    $automatic_result=array();
    $automatic_result['nid_tag_array']=$nid_tag_array;
    if(crm_exportar_is_crm_exportar_tag()){
      $automatic_result=(object) $automatic_result;
      crm_exportar_tags_automatic_save($automatic_result);
      red_solr_inc_apachesolr_index_batch_index_remaining_callback();  
    }
    //return $automatic_result;
  //}
  crm_exportar_node_feed($nid_array,$channel,$crm,$is_post,$is_todas,$nid_tag_array,$nid_duplicate_news_array);
  exit();
}              