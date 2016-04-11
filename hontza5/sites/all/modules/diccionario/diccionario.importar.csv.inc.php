<?php
function diccionario_importar_csv_form(){
    $form=array();
    $form['funcion_desactivada']=array(
        '#value'=>'Funcion desactivada',
    );
    return $form;
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
        '#default_value'=>t('Migrate'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'red_migracion/gestion'),
    );
    $form['#attributes']['enctype'] = "multipart/form-data";
    return $form;
}
function diccionario_importar_csv_form_submit($form, &$form_state) {
    if(isset($_FILES) && !empty($_FILES)){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            if(isset($_FILES['files']['type']) && !empty($_FILES['files']['type']) && $_FILES['files']['type']['upload_file']=='text/csv'){
                $file_path='/tmp/'.$_FILES['files']['name']['upload_file'];
                move_uploaded_file($_FILES['files']['tmp_name']['upload_file'],$file_path);
                $html=diccionario_importar_csv($file_path);
                $_SESSION['importar_csv_html']=$html;
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }
    drupal_goto('diccionario/importar_csv_print');
}
function diccionario_importar_csv($file_path){
    $lineas=diccionario_importar_csv_get_lineas($file_path);
    //$html=diccionario_importar_csv_print_lineas($lineas);
    //$html=diccionario_importar_csv_print_no_existe_locales_source($lineas);
    //$html=diccionario_importar_csv_print_source_diferente_locales_source($lineas);    
    //$html=diccionario_importar_csv_update_locales_target($lineas);
    return $html;
}
function diccionario_importar_csv_get_lineas($file_path){
    $lineas=array();
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $length=10000;
        $campos=array();
        $i=0;
        $lineas=array();
        //while (($data = fgetcsv($handle,$length, ";")) !== FALSE) {
        while (($data = fgetcsv($handle,$length, "\t")) !== FALSE) { 
            if($i>0){
                $lineas[]=$data;
            }
            $i++;
        }
        fclose($handle);
    }else{
        drupal_set_message(t('fopen csv error'),'error');
    }    
    return $lineas;
}
function diccionario_importar_csv_print_lineas($lineas){
    $html=array();
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            foreach($row as $f=>$v){
                $html[]=$f.'='.$v.'<BR>';
            }
            $html[]='############'.$i.'############<BR>';
        }
    }
    return implode('',$html);
}
function diccionario_importar_csv_print_callback(){
    if(isset($_SESSION['importar_csv_html'])){
        return $_SESSION['importar_csv_html'];
    }
    return '';
}
function diccionario_importar_csv_print_no_existe_locales_source($lineas){
    $result=array();
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $lid=trim($row[1]);
            $locales_source_row=diccionario_portugues_get_locales_source_row($lid);
            if(isset($locales_source_row->lid)){
                //echo print_r($locales_source_row,1);
                //exit();
            }else{
                $result[]=$row;
            }    
        }
    }
    return diccionario_importar_csv_print_lineas($result);
}
function diccionario_importar_csv_print_source_diferente_locales_source($lineas){
    $result=array();
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $lid=trim($row[1]);
            $source=trim($row[2]);
            $locales_source_row=diccionario_portugues_get_locales_source_row($lid);
            $type=trim($row[6]);
            /*if($type=='no-traducir'){
                continue;
            }*/
            if(isset($locales_source_row->lid)){    
                //$konp=trim($locales_source_row->source);
                $konp=$locales_source_row->source;
                if($konp==$source){
                    //echo print_r($locales_source_row,1);
                    //exit();
                }else{
                    $r=$row;
                    $r[]=$konp;
                    $result[]=$r;
                    //diccionario_importar_csv_update_source_by_lid($lid,$source);
                }
            }    
        }
    }
    return diccionario_importar_csv_print_lineas($result);
}
function diccionario_importar_csv_update_source_by_lid($lid,$source){
    //$sql=sprintf('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$source,$lid);
    db_query('UPDATE {locales_source} SET source="%s" WHERE lid=%d',$source,$lid);
}
function diccionario_importar_csv_update_locales_target($lineas){
    $result=array();
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $lid=trim($row[1]);
            $source=trim($row[2]);
            $type=trim($row[6]);
            /*if(!empty($type) && in_array($type,array('no-traducir'))){
                continue;
            }*/
            if(in_array($source,array('Number of Parámetros'))){
                continue;
            }
            /*$locales_source_row=diccionario_portugues_get_locales_source_row($lid);
            if(isset($locales_source_row->lid)){    
                //$konp=trim($locales_source_row->source);
                $konp=$locales_source_row->source;
                if($konp==$source){
                    //echo print_r($locales_source_row,1);
                    //exit();
                }else{
                    $r=$row;
                    $r[]=$konp;
                    $result[]=$r;
                    //diccionario_importar_csv_update_source_by_lid($lid,$source);
                }
            }*/
            $es=trim($row[3]);
            if(!empty($es)){
                diccionario_save_locales_target_row($lid,$es,'es');
            }
            $fr=trim($row[4]);
            if(!empty($fr)){
                diccionario_save_locales_target_row($lid,$fr,'fr');
            }
            $pt_pt=trim($row[5]);
            if(!empty($pt_pt)){
                diccionario_save_locales_target_row($lid,$pt_pt,'pt-pt');
            }
            //diccionario_get_locales_target_row($lid,$locale_target_row->language);
        }
    }
    return date('Y-m-d H:i');
}
function diccionario_save_locales_target_row($lid,$translation,$lang){
    $row=diccionario_get_locales_target_row($lid,$lang);
    if(isset($row->lid) && !empty($row->lid)){
        //$sql=sprintf('UPDATE {locales_target} SET translation="%s" WHERE lid=%d AND language="%s" AND plural=%d',$locale_target_row->translation,$sub_locale_row->lid,$locale_target_row->language,$locale_target_row->plural);
        $plural=0;
        db_query('UPDATE {locales_target} SET translation="%s" WHERE lid=%d AND language="%s" AND plural=%d',$translation,$lid,$lang,$plural);
                            
    }else{
        $row_es=diccionario_get_locales_target_row($lid,'es');
        if(isset($row_es->lid) && !empty($row_es->lid)){
                $language=$lang;
                $plid=$row_es->plid;
                $plural=$row_es->plural;
                $l10n_status=$row_es->l10n_status;
                $i18n_status=$row_es->i18n_status;
                db_query('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$lid,$translation,$language,$plid,$plural,$l10n_status,$i18n_status);
        }else{
            if($lid!=110){
                $language=$lang;
                $plid=0;
                $plural=0;
                $l10n_status=1;
                $i18n_status=0;
                db_query('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$lid,$translation,$language,$plid,$plural,$l10n_status,$i18n_status);                
            }
        }
    }
}
function diccionario_undo_traducciones_permisos_callback(){
    return 'Funcion desactivada';
$lid_array=array(
13741,
4961,
4990,
5004,
5015,
5022,
4977,
4991,
5005,
5016,
5023,
4978,
4963,
16421,
4960,
4968,
4976,
4989,
5014,
5021,
5003);
if(!empty($lid_array)){
    foreach($lid_array as $i=>$lid){
        $row=diccionario_portugues_get_locales_source_row($lid);
        $bakup_row=diccionario_portugues_get_locales_source_row($lid,'locales_source_20140610');
        diccionario_importar_csv_update_source_by_lid($lid,$bakup_row->source);
        print $row->source.'<BR>';
        print $bakup_row->source.'<BR>';
        print '############################################################<BR>';
    }
}
}
function diccionario_importar_csv_print_permisos_callback(){
    return 'Funcion desactivada';
    return drupal_get_form('diccionario_importar_csv_print_permisos_form');
}
function diccionario_importar_csv_get_lineas_permisos($file_path){
    $result=array();
    $lineas=file($file_path);
    if(!empty($lineas)){
            foreach($lineas as $i=>$v){
                $value=trim($v);
                if(!empty($value)){
                    $result[]=$value;
                }
            }
        
    }
    return $result;
}
function diccionario_importar_csv_print_permisos_form(){
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
        '#default_value'=>t('Migrate'),
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Cancel'),'red_migracion/gestion'),
    );
    $form['#attributes']['enctype'] = "multipart/form-data";
    return $form;
}
function diccionario_importar_csv_print_permisos_form_submit($form, &$form_state) {
    if(isset($_FILES) && !empty($_FILES)){
        if(isset($_FILES['files']) && !empty($_FILES['files'])){
            if(isset($_FILES['files']['type']) && !empty($_FILES['files']['type']) && $_FILES['files']['type']['upload_file']=='text/csv'){
                $file_path='/tmp/'.$_FILES['files']['name']['upload_file'];
                move_uploaded_file($_FILES['files']['tmp_name']['upload_file'],$file_path);
                $html=diccionario_importar_csv_print_permisos($file_path);
                $_SESSION['importar_csv_html']=$html;
            }else{
                drupal_set_message(t('The file not is a csv'),'error');
            }
        }
    }
    drupal_goto('diccionario/importar_csv_print');
}
function diccionario_importar_csv_print_permisos($file_path){
    global $base_url;
    $result=array();
    $url_txt=$base_url.'/sites/all/modules/diccionario/permisoak.txt';
    $permisos=diccionario_importar_csv_get_lineas_permisos($url_txt);
    $lineas=diccionario_importar_csv_get_lineas($file_path);
    if(!empty($lineas)){
        foreach($lineas as $i=>$row){
            $key=trim($row[2]);
            if(in_array($key,$permisos)){
                $result[]=$key;
            }
        }
    }
    print implode('<BR>',$result);
    exit();
}        