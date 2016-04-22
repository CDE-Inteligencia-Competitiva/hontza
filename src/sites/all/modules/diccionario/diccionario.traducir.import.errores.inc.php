<?php
function diccionario_traducir_import_errores_callback(){
    return drupal_get_form('diccionario_traducir_import_errores_form');
}
function diccionario_traducir_import_errores_form(){
    $form=array();
    $form['browser'] = array(
    '#type' => 'fieldset',
    '#title' => t('Browser Upload'),
    '#collapsible' => TRUE,
    '#description' => t("Upload a CSV file."),
    );
    //$file_size = t('Maximum file size: !size MB.', array('!size' => file_upload_max_size()));
    $file_size ='';
    $form['browser']['upload_file'] = array(
      '#type' => 'file',
      '#title' => t('CSV File'),
      '#size' => 40,
      '#description' => t('Select the CSV file to be upload.').' '.$file_size,
    );
    $form['migrate_btn']=array(
        '#type'=>'submit',
        '#name'=>'migrate_btn',
        '#default_value'=>t('Import'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'red_migracion/gestion'),
    );
    $form['#attributes']['enctype'] = "multipart/form-data";    
    return $form;
}
function diccionario_traducir_import_errores_form_submit($form, &$form_state) {
    if(isset($_FILES) && !empty($_FILES)){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            if(isset($_FILES['files']['type']) && !empty($_FILES['files']['type']) && $_FILES['files']['type']['upload_file']=='text/csv'){
                $file_path='/tmp/'.$_FILES['files']['name']['upload_file'];
                move_uploaded_file($_FILES['files']['tmp_name']['upload_file'],$file_path);
                diccionario_traducir_import_errores_import_csv($file_path);
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }        
}
function diccionario_traducir_import_errores_import_csv($file_path){
    $lineas=diccionario_traducir_import_errores_get_lineas_csv($file_path);
    //diccionario_traducir_import_errores_chequear_existe_lid($lineas);
    diccionario_traducir_import_errores_chequear_existe_drupal_locales_source($lineas);
    //diccionario_traducir_import_errores_update_locales_source_by_lineas($lineas);
}
function diccionario_traducir_import_errores_get_lineas_csv($file_path){
    $lineas=array();
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $length=10000;
        $campos=array();
        $i=0;
        $lineas=array();
        //while (($data = fgetcsv($handle,$length, ";")) !== FALSE) {
        while (($data = fgetcsv($handle,$length, "\t")) !== FALSE) { 
            if($i<1){
                $campos=$data;
            }else{
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $lineas[$i-1][$campos[$c]]=$data[$c];
                }    
            }
            $i++;
        }
        fclose($handle);
    }else{
        drupal_set_message(t('fopen csv error'),'error');
    }    
    return $lineas;    
}
function diccionario_traducir_import_errores_chequear_existe_lid($lineas){
    $kont=0;
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $konp=strtolower($row['x']);
            if($konp=='x'){
                $locales_source_row=diccionario_portugues_get_locales_source_row($row['lid']);
                if(isset($locales_source_row->lid) && !empty($locales_source_row->lid)){
                    print $row['en'].'===='.$locales_source_row->source.'<BR>'; 
                    $kont++;
                }
            }    
        }
    }
    print 'kont===='.$kont;exit();
}
function diccionario_traducir_import_errores_chequear_existe_drupal_locales_source($lineas){
    $kont=0;
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $konp=strtolower($row['x']);
            if($konp=='x'){
                $locales_source_row=diccionario_portugues_get_locales_source_row($row['lid'],'locales_source_20140424');
                if(isset($locales_source_row->lid) && !empty($locales_source_row->lid)){
                    db_set_active('drupal_locales');
                    $drupal_locales_source_row=diccionario_drupal_locales_get_source_row($locales_source_row->source);
                    db_set_active();
                    if(isset($drupal_locales_source_row->lid)){
                        print $drupal_locales_source_row->source.'===='.$row['en'].'<BR>';
                        $kont++;
                    }
                }    
            }    
        }
    }
    print 'kont===='.$kont;exit();
}
function diccionario_traducir_import_errores_update_locales_source_by_lineas($lineas){
    $kont=0;
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $konp=strtolower($row['x']);
            if($konp=='x'){
                $locales_source_row=diccionario_portugues_get_locales_source_row($row['lid']);
                if(isset($locales_source_row->lid) && !empty($locales_source_row->lid)){
                    //print $row['en'].'===='.$locales_source_row->source.'<BR>'; 
                    $sql=sprintf('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$row['en'],$locales_source_row->lid);
                    //print $sql.'<BR>';
                    //db_query('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$row['en'],$locales_source_row->lid);
                    $kont++;
                }
            }    
        }
    }
    print 'kont(update)='.$kont;exit();
}