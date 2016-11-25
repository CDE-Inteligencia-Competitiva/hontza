<?php
function despacho_boletin_report_word_get_current_content($current_content,$subject,$bulletin_text_nid){
    global $base_url;
    if(!empty($bulletin_text_nid)){
        return $current_content;
    }
    //$path=$base_url.'/sites/all/modules/despacho/despacho_boletin_report_word/';
    $path=despacho_boletin_report_word_get_path();
    //intelsat-2016
    //$filename=$path.'despacho_boletin_report_word.html';
    $filename=despacho_boletin_report_word_get_filename();
    $filename=$path.$filename;
    $result=file_get_contents($filename);
    $result=utf8_encode($result);
    $result=despacho_boletin_report_word_add_logo($result,$path);
    $result=str_replace('{despacho_boletin_report_titulo}',$subject,$result);    
    $result=despacho_boletin_report_word_add_indice($result,$current_content);
    $result=despacho_boletin_report_word_add_tipos_fuente($result,$current_content);
    return $result;
}
function despacho_boletin_report_word_add_indice($result_in,$current_content){
    $result=$result_in;
    $sep='indice_table"';
    $pos=strpos($current_content,$sep);
    if($pos===FALSE){
        $result=str_replace('{despacho_boletin_report_indice}','',$result);
        return $result;
    }
    $s=substr($current_content,$pos+strlen($sep));
    $sep='</table>';
    $pos=strpos($s,$sep);
    if($pos===FALSE){
        $result=str_replace('{despacho_boletin_report_indice}','',$result);
        return $result;
    }
    $s=substr($s,0,$pos);
    $sep='<a href="#tipos_fuente_';
    $my_array=explode($sep,$s);
    $html=array();
    $a_close='</a>';
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if($i>0){                
                $pos=strpos($value,$a_close);
                if($pos===FALSE){
                    continue;
                }
                $link=substr($value,0,$pos);
                //$link='<a style="color:#000000;" href="#tipos_fuente_'.$link.$a_close;                
                $link='<a style="color:#006699;text-decoration:none;" href="#tipos_fuente_'.$link.$a_close;                
                $html[]="<p class=MsoListParagraphCxSpFirst style='margin-top:0cm;margin-right:0cm;
              margin-bottom:0cm;margin-left:18.0pt;margin-bottom:.0001pt;mso-add-space:
              auto;text-indent:-18.0pt;line-height:normal;mso-list:l0 level1 lfo2'><![if !supportLists]><span
              lang=ES-TRAD style='font-family:Symbol;mso-fareast-font-family:Symbol;
              mso-bidi-font-family:Symbol'><span style='mso-list:Ignore'>·<span
              style='font:7.0pt \"Times New Roman\"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </span></span></span><![endif]><span lang=ES-TRAD><b>".$link."</b></span></p>";
            }
        }    
    }
    $my_replace=implode('',$html);
    $result=str_replace('{despacho_boletin_report_indice}',$my_replace,$result);
    return $result;
}
function despacho_boletin_report_word_add_tipos_fuente($result_in,$current_content){
    $result=$result_in;
    //$sep='<a name="tipos_fuente_';
    $sep='<th ';
    $my_array=explode($sep,$current_content);
    $html=array();
    $a_close='</a>';
    if(!empty($my_array)){
        foreach($my_array as $i=>$value){
            if($i>0){                
                $pos=strpos($value,'</th>');                
                if($pos===FALSE){
                    continue;
                }
                $item_links_html=despacho_boletin_report_word_get_tipos_fuente_item_links_html(substr($value,$pos+strlen('</th>')));                                
                $s=substr($value,0,$pos);
                $pos=strpos($s,'>');                
                if($pos===FALSE){
                    continue;
                }
                $link_tipos_fuente=substr($s,$pos+1);
                //$background_field='background';
                $background_field='background-color';
                $background_tipos_fuente='black';
                $background_tipos_fuente=despacho_boletin_report_word_get_background_tipos_fuente($link_tipos_fuente);
                $bgcolor='';
                $bgcolor='bgcolor="'.$background_tipos_fuente.'" ';
                //$theme_color="mso-background-themecolor:text1;";
                $theme_color="";
                $html[]="<tr style='mso-yfti-irow:4'>
                <td ".$bgcolor."width=570 valign=top style='width:427.8pt;".$background_field.":".$background_tipos_fuente.";".$theme_color."padding:0cm 5.4pt 0cm 5.4pt'>
                <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                normal'><b style='mso-bidi-font-weight:normal'><span lang=ES-TRAD
                style='color:white;mso-themecolor:background1'>".$link_tipos_fuente."<o:p></o:p></span></b></p>
                </td>
               </tr>".$item_links_html;                                
            }
        }    
    }    
    $my_replace=implode('',$html);
    $result=str_replace('{despacho_boletin_report_rows}',$my_replace,$result);
    return $result;
}
function despacho_boletin_report_word_get_tipos_fuente_item_links_html($result_in){
    $result=$result_in;
    $my_array=explode('<table class="mail_table_eskubi_aldea',$result);
    $sep='<td>';
    $empty_result="<tr style='mso-yfti-irow:5;height:5.65pt'>
  <td width=570 valign=top style='width:427.8pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:5.65pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span lang=ES-TRAD><o:p>&nbsp;</o:p></span></p>
  </td>
 </tr>";
    if(count($my_array)>1){
        foreach($my_array as $i=>$value){
            if($i>0){
                $pos=strpos($value,$sep);
                if($pos===FALSE){
                    continue;
                }else{
                    $s=substr($value,$pos+strlen($sep));
                    $pos=strpos($s,'</td>');
                    if($pos===FALSE){
                        continue;
                    }else{
                        $fila_string=$s;
                        $s=substr($s,0,$pos);
                        //$s=str_replace('<b>','',$s);
                        //$s=str_replace('</b>','',$s);
                        $title=strip_tags($s);
                        preg_match('/<a href="(.+)">/', $s, $match);
                        $href=$match[1];
                        $s="<a style=\"text-decoration:none;\" href=\"".$href."\" target=\"_blank\"><b style=\"mso-bidi-font-weight: normal\"><span style='color:#006699;text-decoration:none;text-underline:none;'>".$title."</span></b></a>";
                        //$padding="0cm 5.4pt 0cm 5.4pt";
                        $padding="5.4pt 5.4pt 5.4pt 5.4pt";
                        //$valign="top";
                        $valign="middle";
                        /*$link="<tr style='mso-yfti-irow:9'>
  <td width=570 valign=".$valign." style='width:427.8pt;padding:".$padding."'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span lang=ES-TRAD>".$s."</p>
  </td>
 </tr>";*/                
                        //$text_align="";
                        $text_align="text-align:justify";
                        //$text_align="text-align:right";
                        $resumen='';
                        //$resumen=despacho_boletin_report_word_get_resumen_html($fila_string,$pos);                        
                        $link="<tr style='mso-yfti-irow:9'>
  <td width=570 style='width:427.8pt;padding:".$padding.";vertical-align:middle;'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal;".$text_align."'><span lang=ES-TRAD>".$s.$resumen."</p>
  </td>
 </tr>";                  
                        $html[]=$link;
                    }        
                }
            }
        }
        $html[]=$empty_result;
        return implode('',$html);
    }
    return $empty_result;
}
function despacho_boletin_report_word_add_logo($result_in,$path,$is_content=0){
    if($is_content){
        $filename=$path.'image001.png';
    }else{
        //$filename=$path.'despacho_boletin_report_logo.png';
        //$filename=$path.'despacho_boletin_report_logo.jpg';
        if(red_informatica_is_informatica_activado()){
            $filename=$path.'Boletin_informatica_word_archivos/image001.png';
        }else{    
            $filename=$path.'Boletín_ICB_word_archivos/image001.png';
        }
    }        
    $content=file_get_contents($filename);
    if($is_content){
        return $content;
    }
    $content=base64_encode($content);
    //$content='data:image/png;base64,'.$content;
    $content='data:image/jpeg;base64,'.$content;
    $content=$filename;
    $result=str_replace('{despacho_boletin_report_logo}',$content,$result_in);    
    return $result;
}
function despacho_boletin_report_word_add_logo_attachment(&$message){
    $path=despacho_boletin_report_word_get_path();
    $content=despacho_boletin_report_word_add_logo($result_in,$path,1);
    $attachments=array(
     'filecontent' => $content,
     //'filepath'=>$path.'image001.png',   
     //'filename' => 'image001',
     'filename' => $path.'image001.png',
     'filemime' => 'image/png',
   );
    //$message['params']['attachments'][] =$attachments;
    //$message['attachments'][] =$attachments;
    $message['attachments']=$attachments;
    //$message['params']['attachments']=$attachments;
    //$message['headers']['Content-Type'] = 'multipart/mixed; text/html';
    //$message['headers']['Content-Type'] = 'text/html; charset=UTF-8; format=flowed';
}
function despacho_boletin_report_word_get_path(){
    global $base_url;
    $name='despacho_boletin_report_word';
    if(red_informatica_is_informatica_activado()){
        $name='informatica_boletin_report_word';
    }
    $result=$base_url.'/sites/all/modules/despacho/'.$name.'/';
    return $result;
}
function despacho_boletin_report_word_is_my_add_css(){
    if(boletin_report_inc_is_boletin_report_my_web()){
        return 0;
    }
    return 1;
}
function despacho_boletin_report_word_is_custom_content(){
    if(is_node_add('bulletin-text')){
        return 1;
    }
    if(boletin_report_inc_is_previsualizacion_boletin()){
        return 1;
    }
    if(boletin_report_inc_is_boletin_report_my_web('my_web',1)){
        return 1;            
    }
    if(boletin_report_in_is_launch()){
        return 1;
    }
    return 0;
}
function despacho_boletin_report_word_is_add_introduccion_despedida_html($bulletin_text_nid,$is_edit_content){
    if(!boletin_report_is_content_editado($bulletin_text_nid,$is_edit_content)){
        return 1;
    }
    return 0;
}
function despacho_boletin_report_word_get_forward_attributes($result_in){
    $result=$result_in;
    $result=despacho_boletin_report_word_get_target_blank($result);
    return $result;
}
function despacho_boletin_report_word_get_boletin_report_forward_content_view_web($content){
    $html=array();
    despacho_boletin_report_word_add_boletin_report_forward_content_view_web_js($content);            
    $html[]='<iframe id="id_despacho_boletin_report_forward_iframe" src="about:blank" width="100%" height="100%" style="padding-top:10px;" onload="despacho_boletin_report_forward_resize_iframe()">'.$content.'</iframe>';
    return implode('',$html);
}
function despacho_boletin_report_word_add_boletin_report_forward_content_view_web_js($content_in){
   $content=$content_in;
   $content=str_replace(array("\n","\r"),"",$content);
   $js='';
   /*$js.='$(document).ready(function()
   {
    $("#id_despacho_boletin_report_forward_iframe").load(function() {
        $(this).contents().find("html").html("'.addslashes($content).'");
        $(this).height($("body").height()+10);
    }); 
    
   });';*/
   $js.='function despacho_boletin_report_forward_resize_iframe(){
        $("#id_despacho_boletin_report_forward_iframe").contents().find("html").html("'.addslashes($content).'");
        $("#id_despacho_boletin_report_forward_iframe").height($("body").height()+10);    
    }';
   /*        //alert($("body").height());
        //$(this).css("height",$("body").height());*/
   drupal_add_js($js,'inline');
}
function despacho_boletin_report_word_get_download_content($content,$is_download){
    if($is_download){
        $result=utf8_decode($content);
        return $result;
    }
    return $content;
}
function despacho_boletin_report_get_options_view_next_group_bulletin($result_in){
    $result=$result_in;
    $result['attributes']=despacho_boletin_report_word_get_target_blank($result);
    return $result;
}
function despacho_boletin_report_word_get_target_blank($result_in){
    $result=$result_in;
    $result['target']='_blank';
    return $result;
}
function despacho_boletin_report_word_is_boletin_grupo_no_styles(){
    if(is_my_web()){
        return 1;
    }
    return 0;
}
function despacho_boletin_report_word_fix_pdf($content){
    $result=$content;
    $result=str_replace('charset=windows-1252','charset=UTF-8',$result);
    return $result;
}
function despacho_boletin_report_word_get_get_options_view_alerta($result_in){
    $result=$result_in;
    $result['attributes']=despacho_boletin_report_word_get_target_blank($result);
    return $result;
}
function despacho_boletin_report_word_access_denied(){
    drupal_access_denied();
    exit();
}
function despacho_boletin_report_word_unset_headers($headers){
    $result=array();    
    if(!empty($headers)){    
        foreach($headers as $i=>$value){
            if($value!=t('Edition')){
                $result[]=$value;
            }
        }
    }
    $result=array_values($result);
    return $result;
}
function despacho_boletin_report_word_set_edition_type_value_string($is_edit,$is_archive){
    return boletin_report_set_edition_type_value_string(0,$is_archive);
}
function despacho_boletin_report_word_get_resumen_html($fila_string,$my_index){    
    $s=substr($fila_string,$my_index);
    $sep='<td>';
    $pos=strpos($s,$sep);
    if($pos===FALSE){
        return '';
    }else{
        $s=substr($s,$pos+strlen($sep));
        $pos=strpos($s,'</td>');
        if($pos===FALSE){
            return '';
        }else{
            $result=$s=substr($s,0,$pos);
            $result='<p>'.$result.'</p>';
            return $result;
        }
    }
    return '';
}
function despacho_boletin_report_word_replace_title_boletin_report_automatico_editados($content,$titulo_boletin){
    //$result=$content;
    $needle="11.0pt;line-height:105%;color:white;mso-themecolor:background1'>";
    /*$pos=strpos($content,$needle);
    if($pos===FALSE){
        return $result;
    }else{
        $s=substr($result,0,$pos);
        $my_string=substr($result,$pos+strlen($needle));*/
        $my_needle='<o:p>';
        /*$pos=strpos($my_string,$my_needle);
        if($pos===FALSE){
            return $result;
        }else{
            $my_string=substr($my_string,$pos+strlen($my_needle));
            $result=$s.$needle.$titulo_boletin.$my_needle.$my_string;
        }    
    }    
    return $result;*/
    return boletin_report_inc_replace_title_boletin_report_automatico_editados($content,$titulo_boletin,$needle,$my_needle);
}
function despacho_boletin_report_word_get_background_tipos_fuente(&$link_tipos_fuente){
    //http://apple.stackexchange.com/questions/96056/outlook-2011-category-colors-windows-color-palette
    $result='black';
    $konp_array=array('<i name="tipos_fuente_','<span name="tipos_fuente_');
    if(!empty($konp_array)){
        foreach($konp_array as $i=>$konp){
            $pos=strpos($link_tipos_fuente,$konp);
            if($pos===FALSE){
                continue;
            }else{
                $s=substr($link_tipos_fuente,$pos+strlen($konp));
                $pos=strpos($s,'"');
                if($pos===FALSE){
                    continue;
                }else{
                    $tid=substr($s,0,$pos);
                    $term=taxonomy_get_term($tid);
                    $pro=profundidad($term->tid);
                    if($pro==1){
                        //$result='#505050';
                        $result='#3C3C3C';
                        //$result='DimGrey';
                        //$result='#6f6f6f';
                        $link_tipos_fuente=strip_tags($link_tipos_fuente);
                    }else if($pro>=2){
                        //$result='#8C8C8C';
                        $result='#969696';
                        //$result='LightGrey';
                        //$result='#bfbfbf';
                        $link_tipos_fuente='<i>'.strip_tags($link_tipos_fuente).'</i>';
                    }
                }               
            }
        }
    }    
    return $result;
}
function despacho_boletin_report_word_get_filename(){
    $result='despacho_boletin_report_word.html';
    if(red_informatica_is_informatica_activado()){
        $result='informatica_boletin_report_word.html';
    }
    return $result;
}