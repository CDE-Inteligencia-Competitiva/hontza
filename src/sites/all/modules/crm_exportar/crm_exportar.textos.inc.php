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
function crm_exportar_textos_get_array(){
  $result=array();
  $res=db_query('SELECT * FROM {crm_exportar_textos} WHERE 1 ORDER BY id ASC');
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