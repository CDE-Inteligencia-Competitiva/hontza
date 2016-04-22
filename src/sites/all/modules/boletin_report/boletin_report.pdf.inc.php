<?php
function boletin_report_pdf_callback(){
    $html=boletin_report_web_callback(1);
    /*$s='<P>&nbsp;</P>';
    $html=str_replace($s,'',$html);
    $s='<TD>&nbsp;</TD>';
    $html=str_replace($s,'',$html);
    $s='<tr style="VERTICAL-ALIGN: top">
<td>&nbsp;</td>
<td>&nbsp;</td></tr>';    
    $html=str_replace($s,'',$html);
*/
    //$html=boletin_report_pdf_repasar_html($html);
    //print $html;exit();
    boletin_report_pdf_create_pdf($html);
}
function boletin_report_pdf_create_pdf($html){
    //print $html;exit();    
    $pdf_title ='title';
    $send_filename=time().'.pdf';     
    $boletin_report_array_edit_id=arg(3);    
    $text_row='';
    if(!empty($boletin_report_array_edit_id)){
        //$text_row=boletin_report_get_edit_text_row('',$boletin_report_array_edit_id);
        $text_row=boletin_report_get_edit_text_row_by_id($boletin_report_array_edit_id);
        $node=node_load($text_row->nid);
        $pdf_title=$node->title;
    }    
     

   require_once('sites/all/libraries/wkpdf/WKPDF.php');
   try {
     // $wkhtmltopdf = new wkhtmltopdf(array('path' => APPLICATION_PATH . '/../public/uploads/'));
     $wkhtmltopdf = new wkhtmltopdf(array('path' => '/tmp/'));
     $wkhtmltopdf->setTitle($pdf_title);
     $wkhtmltopdf->setHtml($html);
     //$wkhtmltopdf->output(wkhtmltopdf::MODE_DOWNLOAD, $send_filename);
     $wkhtmltopdf->output(wkhtmltopdf::MODE_EMBEDDED, $send_filename);
   } catch (Exception $e) {
     echo $e->getMessage();
   }
   exit;
}
function boletin_report_pdf_is_pdf(){
    $is_pdf=0;
    $param=arg(2);
    if(!empty($param) && $param=='pdf'){
        $is_pdf=1;
    }
    return $is_pdf;
}
function boletin_report_pdf_div_callback(){
    return 'Funcion desactivada';
    $node_array=hontza_get_all_nodes(array('item'),'','',1);
    //$node_array=array_slice($node_array,0,10);
    if(!empty($node_array)){
        foreach($node_array as $i=>$row){
            $result[]=node_view($row).$row->body;
        }
    }
    $html=implode('',$result);
    $html=alerta_add_css($html);
    print $html;exit();
    boletin_report_pdf_create_pdf($html);
}
function boletin_report_pdf_node_callback(){
    $nid=arg(1);
    $node=node_load($nid);
    if(boletin_report_pdf_node_is_activado()){
        return boletin_report_pdf_node_table_html();
    }else{
        $content=utf8_decode($node->body);
        boletin_report_pdf_create_pdf($content);
    }    
}
function boletin_report_pdf_node_access(){
    if(boletin_report_is_permiso_editar_og_grupo()){
        return 1;
    }
    return 0;
}
function boletin_report_pdf_node_is_activado(){
    if(defined('_IS_BOLETIN_REPORT_PDF_NODE') && _IS_BOLETIN_REPORT_PDF_NODE==1){
        return 1;
    }
    return 0;
}
function boletin_report_insert_report_pdf($node){
    global $user;
    db_query('INSERT INTO {report_pdf}(nid,vid,created,texto,uid) VALUES(%d,%d,%d,"%s",%d)',$node->nid,$node->vid,time(),$node->body,$user->uid);
}
function boletin_report_pdf_node_table_html(){
    drupal_set_title(t('Pdf List'));
    $output='';
    $nid=arg(1);
    $node=node_load($nid);
    $output.=node_view($node);
    //
    $my_limit=100;    
    $sort='asc';
    $field='created';
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    //
    $headers=array();
    $headers[0]=array('data'=>t('Creator'),'field'=>'username');
    $headers[1]=array('data'=>t('Creation date'),'field'=>'created');
    $headers[2]=t('Actions');
    //
    $rows=array();
    $where=array();
    $where[]='1';
    $where[]='nid='.$nid;
    $sql='SELECT * FROM {report_pdf} WHERE '.implode(' AND ',$where).' ORDER BY '.$field.' '.$sort;
    $res=db_query($sql);
    $kont=0;
    while ($r = db_fetch_object($res)) {
      $my_user=user_load($r->uid); 
      $rows[$kont]=array();
      //
      $rows[$kont][0]=$my_user->name;
      $rows[$kont][1]='';
      if(!empty($r->created)){
        $rows[$kont][1]=date('Y-m-d H:i',$r->created);
      }
      $rows[$kont][2]=boletin_report_pdf_node_define_acciones($r);
      $kont++;
    }
    $new_rows=boletin_report_pdf_define_new_rows($nid);
    $rows=array_merge($new_rows,$rows);
    $rows=my_set_estrategia_pager($rows, $my_limit);

  $output .='<div style="float:left;width:100%;">';
       
  if (count($rows)>0) {
    $output .= theme('table',$headers,$rows);
    $output .= theme('pager', NULL, $my_limit);    
  }
  else {

    $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
  }
  
  $output .='</div>';
  
  return $output;
}
function boletin_report_pdf_inc_is_pdf_node(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='boletin_report'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2=='pdf_node'){
                return 1;
            }
        }
    }
    return 0;
}
function boletin_report_pdf_define_new_rows($nid){
    $new_rows=array();
    $new_rows[0][0]='';
    $new_rows[0][1]='';
    $icono=my_get_icono_action('add_left',t('Create new pdf')).'&nbsp;';
    $new_rows[0][2]=l($icono,'boletin_report/'.$nid.'/new_pdf_node',array('html'=>TRUE)).'&nbsp;';
    return $new_rows;
}
function boletin_report_pdf_node_define_acciones($r){
    $html=array();
    $html[]=l(my_get_icono_action('pdf', t('Pdf')),'boletin_report/'.$r->id.'/view_pdf_node',array('html'=>true,'attributes'=>array('target'=>'_blank')));
    $html[]=l(my_get_icono_action('delete', t('Delete')),'boletin_report/'.$r->id.'/delete_pdf_node',array('html'=>true));
    return implode('&nbsp;',$html);
}
function boletin_report_pdf_new_pdf_node_callback(){
    $nid=arg(1);
    $node=node_load($nid);
    if(boletin_report_pdf_node_is_activado()){
        boletin_report_insert_report_pdf($node);
    }
    drupal_goto('boletin_report/'.$nid.'/pdf_node');
}
function boletin_report_pdf_view_pdf_node_callback(){
    $id=arg(1);
    $report_pdf=boletin_report_get_report_pdf_row($id);
    $content=utf8_decode($report_pdf->texto);
    boletin_report_pdf_create_pdf($content);
}
function boletin_report_get_report_pdf_row($id){
    $report_pdf_array=boletin_report_get_report_pdf_array($id);
    if(count($report_pdf_array)>0){
        return $report_pdf_array[0];
    }
    //
    $report_pdf=new stdClass();
    return $report_pdf;
}
function boletin_report_get_report_pdf_array($id='',$nid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($id)){
        $where[]='id='.$id;
    }
    if(!empty($nid)){
        $where[]='nid='.$nid;
    }
    //
    $sql='SELECT * FROM {report_pdf} WHERE '.implode(' AND ',$where).' ORDER BY created DESC';
    $res=db_query($sql);
    while ($r = db_fetch_object($res)) {
        $result[]=$r;
    }
    return $result;
}
function boletin_report_pdf_node_is_with_pdf($node){
    if(boletin_report_pdf_node_is_activado()){
        if($node->type=='my_report'){
            if(boletin_report_pdf_node_exist_pdf($node)){
                return 1;
            }
        }
    }
    return 0;
}
function boletin_report_pdf_node_exist_pdf($node){
    $report_pdf_array=boletin_report_get_report_pdf_array('',$node->nid);
    if(count($report_pdf_array)>0){
        return 1;
    }
    return 0;
}
function boletin_report_pdf_get_pdf_node_view_link($node){
    global $base_url;
    $report_pdf_array=boletin_report_get_report_pdf_array('',$node->nid);
    if(count($report_pdf_array)>0){
        $id=$report_pdf_array[0]->id;
        $grupo_path='';
        if(isset($node->og_groups)){
            $group_nid_array=array_keys($node->og_groups);
            $my_grupo=node_load($group_nid_array[0]);
            if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                $grupo_path='/'.$my_grupo->purl;
            }
        }
        $url=$base_url.$grupo_path.'/boletin_report/'.$id.'/view_pdf_node';        
        $icono=my_get_icono_action('pdf',t('Pdf'),'boletin_report_pdf',array(),' style="float:left;"').'&nbsp;';
        //$title=$url;
        $title=$node->title;
        $result=$icono.l($title,$url,array('attributes'=>array('absolute'=>TRUE,'target'=>'_blank')));
        return $result;
    }
    return '';
}
function boletin_report_pdf_delete_pdf_node_callback(){
    return drupal_get_form('boletin_report_pdf_delete_pdf_node_form');
}
function boletin_report_pdf_delete_pdf_node_form(){
    $form=array();
    $id=arg(1);
    drupal_set_title(t('Are you sure you want to delete?'));
    $report_pdf_row=boletin_report_get_report_pdf_row($id);
    $form['report_pdf_id']=array(
      '#type'=>'hidden',
      '#default_value'=>$id,
    );
    $form['report_pdf_nid']=array(
      '#type'=>'hidden',
      '#default_value'=>$report_pdf_row->nid,
    );
    $form['delete_text']['#value']='<p>'.t('This action cannot be undone.').'</p>';
    $form['confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete'),
      '#name'=>'confirm_btn',
    );
    $form['cancel_btn']['#value']=l(t('Cancel'),'boletin_report/'.$report_pdf_row->nid.'/pdf_node');        
    return $form;
}
function boletin_report_pdf_delete_pdf_node_form_submit($form, &$form_state){
    if(isset($form_state['values']['report_pdf_id']) && !empty($form_state['values']['report_pdf_id'])){
        boletin_report_delete_report_pdf($form_state['values']['report_pdf_id']);
    }
    if(isset($form_state['values']['report_pdf_nid']) && !empty($form_state['values']['report_pdf_nid'])){
        drupal_goto('boletin_report/'.$form_state['values']['report_pdf_nid'].'/pdf_node');
    }
}
function boletin_report_delete_report_pdf($report_pdf_id){
    db_query('DELETE FROM {report_pdf} WHERE id=%d',$report_pdf_id);
}