<?php
function despacho_boletin_report_word_get_current_content($current_content,$subject,$bulletin_text_nid,$br=''){
    global $base_url;
    /*if(hontza_is_sareko_id('ITI')){
      return $current_content;
    }*/
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
    //print $result;exit();
    if(red_informatica_despacho_boletin_report_is_resumen_activado()){
        $td_width=despacho_boletin_report_word_define_informatica_td_width();
        $td_style_width=$td_width."px";
        $td_width=" width='".$td_width."'";
        
        $result=str_replace('{td_width}',$td_width,$result);
        $result=str_replace('{td_style_width}',$td_style_width,$result);
    }else{
        $result=despacho_boletin_report_word_add_logo($result,$path);
        $result=str_replace('{despacho_boletin_report_titulo}',$subject,$result);    
    }
    $result=despacho_boletin_report_word_add_indice($result,$current_content,$br);
    $result=despacho_boletin_report_word_add_tipos_fuente($result,$current_content,$br);    
    
    return $result;
}
function despacho_boletin_report_word_add_indice($result_in,$current_content,$br=''){
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
                if(red_informatica_despacho_boletin_report_is_resumen_activado()){
                    $link=despacho_boletin_report_word_unset_style($link);
                    //$a_color='color:black !important;';                    
                    //$a_color='color:#000000 !important;font-family:Roboto !important;';
                    $a_color='color:#000000 !important;font-family:Roboto !important;font-size:12pt;';
                    $link='<a style="'.$a_color.'text-decoration:none;" href="#tipos_fuente_'.$link.$a_close;
                    /*$html[]="<p class=MsoListParagraphCxSpFirst style='margin-top:0cm;margin-right:0cm;
  margin-bottom:0cm;margin-left:8.75pt;margin-bottom:.0001pt;mso-add-space:
  auto;text-indent:-8.75pt;line-height:normal;mso-list:l1 level1 lfo2'><![if !supportLists]><span
  lang=ES-TRAD style='font-family:Symbol;mso-fareast-font-family:Symbol;
  mso-bidi-font-family:Symbol;color:#FF7100;'><span style='mso-list:Ignore;'>·<span
  style='font:7.0pt \"Times New Roman\"'>&nbsp;&nbsp; </span></span></span><![endif]><span
  lang=ES-TRAD style='font-family:Roboto'>".$link."<o:p></o:p></span></p>";*/
                   
                /*$html_indice[]='<li style="color:#FF7100;">
<p><font face="Roboto, serif"><span lang="es-ES">'.$link.'</span></font></p>
                </li>';*/
                //print $link;exit();
                /*$html_indice[]='<li style="color:#FF7100;">
<font face="Roboto, serif" style="'.$a_color.'"><span lang="es-ES" style="'.$a_color.'">'.$link.'</span></font>
                </li>';*/
                //intelsat-mailchimp-color
                $mailchimp_color_bola='#FF7100';
                if(isset($br->mailchimp_color_bola) && !empty($br->mailchimp_color_bola)){
                   $mailchimp_color_bola=$br->mailchimp_color_bola;
                }
                //intelsat-outlook
                /*
                $html_indice[]='<li style="color:'.$mailchimp_color_bola.';">
                <span lang="es-ES" style="'.$a_color.'">'.$link.'</span>
                </li>';*/
                $html_indice[]='<li style="color:'.$mailchimp_color_bola.';font-family:Roboto !important;font-size:12pt;">
                <span lang="es-ES" style="'.$a_color.'">'.$link.'</span>
                </li>';


                }else{
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
    }

    if(red_informatica_despacho_boletin_report_is_resumen_activado()){

        /*$html[]='<tr style="mso-yfti-irow:0">
            <td height="5" width="556" valign="top" style="border: none; padding: 0cm">
                <ul>'.implode('',$html_indice).'
                    </ul>
            </td>
        </tr>';*/
        //$td_indice_width='556';
        //$td_indice_width=" width='".despacho_boletin_report_word_define_informatica_td_width()."'";
        //$ul_style='';
        //$ul_style=' style="padding-left:18pt;padding-bottom:8pt;"';
        //$ul_style=' style="padding-left:0pt;padding-bottom:8pt;"';
        //$ul_style=' style="padding-left:8pt;padding-bottom:8pt;"';
        $td_width=despacho_boletin_report_word_define_informatica_td_width();
        //$td_style_width="427.8pt";
        $td_style_width=$td_width."px";
        $td_width=" width='".$td_width."'";
        //$ul_style=' style="padding-left:24px !important;padding-bottom:8px !important;"';
        //$ul_style=' style="padding-left:24px !important;padding-bottom:8px !important;margin-top:0px;"';
        //padding-inline-start para Gmail
        //$ul_style=' style="padding-left:24px !important;padding-bottom:8px !important;padding-inline-start:24px !important;"';
        /*En Outlook no le hace caso a padding-left:24px, aunque en Hontza se vea demasiado a la izquierda,
        en Outlook padding-left:0px queda bien, alineado a la izquierda*/
        //intelsat-outlook
        //$ul_style=' style="padding-left:0px !important;padding-bottom:8px !important;margin-top:0px;"';        
        //$ul_style=' style="padding-left:0px !important;padding-bottom:8px !important;margin-top:0px;padding-top:32pt;font-family:Roboto !important;"';
        $ul_style=' style="padding-left:0px !important;padding-bottom:8px !important;margin-top:0px;font-family:Roboto !important;font-size:12pt;"';
        $html[]='<tr style="mso-yfti-irow:0;">
            <td'.$td_width.'valign="top" style="width:'.$td_style_width.';border: none; padding:0cm;">
                <ul'.$ul_style.'>'.implode('',$html_indice).'
                    </ul>
            </td>
        </tr>';
    }    

    $my_replace=implode('',$html);
    $result=str_replace('{despacho_boletin_report_indice}',$my_replace,$result);
    return $result;
}
function despacho_boletin_report_word_add_tipos_fuente($result_in,$current_content,$br=''){
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
                $item_links_html=despacho_boletin_report_word_get_tipos_fuente_item_links_html(substr($value,$pos+strlen('</th>')),$br);

                $s=substr($value,0,$pos);
                $pos=strpos($s,'>');                
                if($pos===FALSE){
                    continue;
                }
                $link_tipos_fuente=substr($s,$pos+1);

                if(red_informatica_despacho_boletin_report_is_resumen_activado()){
                    /*$html[]="<tr style='mso-yfti-irow:4;height:14.2pt'>
                      <td width=713 valign=top style='width:427.8pt;padding:0cm 5.4pt 0cm 5.4pt;
                      height:14.2pt'>
                      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                      normal'><span lang=ES-TRAD><o:p>&nbsp;</o:p></span></p>
                      </td>
                     </tr>";*/
                     //$margin_bottom='margin-bottom:.0001pt';
                     //$margin_bottom=despacho_boletin_report_word_informatica_define_margin_bottom();
                     //print 'link_tipos_fuente='.$link_tipos_fuente;exit();
                     //$valign="top";
                     $valign="middle";
                     //$margin_bottom="margin-bottom:0cm;margin-bottom:.0001pt;";
                     //$margin_bottom="margin-bottom:0cm;margin-bottom:0pt;margin-bottom:0px;";                     
                     //$margin_bottom="margin-bottom:2px !important;font-family:Roboto !important;";
                     $margin_bottom="margin-bottom:0px !important;font-family:Roboto !important;";
                     $td_width=despacho_boletin_report_word_define_informatica_td_width();
                     //$td_style_width="427.8pt";
                     $td_style_width=$td_width."px";
                     $td_width=" width='".$td_width."'";

                     //intelsat-mailchimp-color
                     $mailchimp_color_fondo_tipo_documento="#FF8500";
                     if(isset($br->mailchimp_color_fondo_tipo_documento) && !empty($br->mailchimp_color_fondo_tipo_documento)){
                        $mailchimp_color_fondo_tipo_documento=$br->mailchimp_color_fondo_tipo_documento;
                     }
                     
                     $mailchimp_color_letra_tipo_documento="white";
                     if(isset($br->mailchimp_color_letra_tipo_documento) && !empty($br->mailchimp_color_letra_tipo_documento)){
                        $mailchimp_color_letra_tipo_documento=$br->mailchimp_color_letra_tipo_documento;
                     }

                     $mailchimp_info_tipo_documento=array();
                     $mailchimp_info_tipo_documento['mailchimp_color_fondo_tipo_documento']=$mailchimp_color_fondo_tipo_documento;
                     $mailchimp_info_tipo_documento['mailchimp_color_letra_tipo_documento']=$mailchimp_color_letra_tipo_documento;
                     despacho_boletin_report_word_get_mailchimp_info_tipo_documento_by_profundidad($link_tipos_fuente,$mailchimp_info_tipo_documento);    
                     $mailchimp_color_fondo_tipo_documento=$mailchimp_info_tipo_documento['mailchimp_color_fondo_tipo_documento'];
                     $mailchimp_color_letra_tipo_documento=$mailchimp_info_tipo_documento['mailchimp_color_letra_tipo_documento'];

                     //$parrafo_class='MsoNormal';
                     $parrafo_class='MsoNormal_tipos_fuente';
                     //$font_size='font-size:12.0pt;mso-bidi-font-size:11.0pt;';
                     $font_size='font-size:14.0pt !important;mso-bidi-font-size:14.0pt !important;';
                     $html[]="<tr style='mso-yfti-irow:5'>
                      <td".$td_width."valign=".$valign." style='width:".$td_style_width.";background:".$mailchimp_color_fondo_tipo_documento.";padding:
                      0cm 5.4pt 0cm 5.4pt'>
                      <p class=".$parrafo_class." style='".$margin_bottom.$font_size."text-align:
                      justify;line-height:normal'><span lang=ES-TRAD style='".$font_size."font-family:Roboto;color:".$mailchimp_color_letra_tipo_documento.";mso-themecolor:background1'>".$link_tipos_fuente."<o:p></o:p></span></p>
                      </td>
                    </tr>".$item_links_html;
                }else{    
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
    }    
    $my_replace=implode('',$html);
    $result=str_replace('{despacho_boletin_report_rows}',$my_replace,$result);
    return $result;
}
function despacho_boletin_report_word_get_tipos_fuente_item_links_html($result_in,$br=''){
    $result=$result_in;
    //intelsat
    $node='';
    $my_array=explode('<table class="mail_table_eskubi_aldea',$result);
    $sep='<td>';
    if(red_informatica_despacho_boletin_report_is_resumen_activado()){
         //$margin_bottom="margin-bottom:0cm;margin-bottom:.0001pt;";   
         //$margin_bottom="margin-bottom:0cm !important;margin-bottom:.0001pt !important;margin-bottom:0px !important;";
         $margin_bottom="margin-bottom:2px !important;font-family:Roboto !important;";
         $td_width=despacho_boletin_report_word_define_informatica_td_width();
         //$td_style_width="427.8pt";
         $td_style_width=$td_width."px";
         $td_width=" width='".$td_width."'";
         
         $empty_result="<tr style='mso-yfti-irow:2;height:14.2pt'>
      <td".$td_width."valign=top style='width:".$td_style_width.";padding:0cm 5.4pt 0cm 5.4pt;
      height:14.2pt'>
      <p class=MsoNormal align=center style='".$margin_bottom."
      text-align:center;line-height:normal'><span style='font-size:14.0pt;
      mso-bidi-font-size:11.0pt;font-family:Roboto !important;mso-ansi-language:ES;mso-fareast-language:
      ES;mso-no-proof:yes'><o:p>&nbsp;</o:p></span></p>
      </td>
     </tr>";
    }else{ 
        $empty_result="<tr style='mso-yfti-irow:5;height:5.65pt'>
      <td width=570 valign=top style='width:427.8pt;padding:0cm 5.4pt 0cm 5.4pt;
      height:5.65pt'>
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
      normal'><span lang=ES-TRAD><o:p>&nbsp;</o:p></span></p>
      </td>
     </tr>";
    } 
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
                        //intelsat
                        if(despacho_boletin_report_is_resumen_activado()){
                            $node=despacho_boletin_report_word_get_node_title_node($s);
                        }    
                        $title=strip_tags($s);
                        preg_match('/<a href="(.+)">/', $s, $match);
                        $href=$match[1];
                        if(!red_informatica_despacho_boletin_report_is_resumen_activado()){
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
                        }
                        $resumen='';
                        //intelsat
                        if(despacho_boletin_report_is_resumen_activado()){
                            $resumen.=despacho_boletin_report_word_get_source_title_url_html($node,$br);
                            $resumen.=despacho_boletin_report_word_get_resumen_html($fila_string,$pos);                                                    
                        }
                        if(red_informatica_despacho_boletin_report_is_resumen_activado()){
                            //$margin_bottom='margin-bottom:0cm;margin-bottom:.0001pt;';
                            $margin_bottom="margin-bottom:2px !important;font-family:Roboto !important;";
                            $td_width=despacho_boletin_report_word_define_informatica_td_width();
                             //$td_style_width="427.8pt";
                             $td_style_width=$td_width."px";
                             $td_width=" width='".$td_width."'";
                            //$font_size="font-size:10.0pt;mso-bidi-font-size:11.0pt;";
                            $font_size="font-size:12pt;mso-bidi-font-size:12pt;";
                            //intelsat-mailchimp-color
                            $mailchimp_color_titulo_noticia="#2E74B5";                            
                            if(isset($br->mailchimp_color_titulo_noticia) && !empty($br->mailchimp_color_titulo_noticia)){
                              $mailchimp_color_titulo_noticia=$br->mailchimp_color_titulo_noticia;
                            }

                            //intelsat-outlook
                            $padding_item_title="padding:0cm 5.4pt 0cm 5.4pt";
                            //$padding_item_title="padding:5.4pt 5.4pt 0cm 5.4pt";
                            //$padding_item_title="padding:2.7pt 5.4pt 0cm 5.4pt";

                            $link="<tr style='mso-yfti-irow:6'>
  <td".$td_width."valign=top style='width:".$td_style_width."pt;".$padding_item_title."'>
  <p class=MsoNormal style='".$margin_bottom."text-align:
  justify;line-height:normal'><span lang=ES-TRAD><a
  href=\"".$href."\" target=\"_blank\" style=\"text-decoration:none\"><span
  style='".$font_size."font-family:Roboto;
  color:".$mailchimp_color_titulo_noticia.";mso-themecolor:accent1;mso-themeshade:191;text-decoration:none;
  text-underline:none'>".$title."</span></span><span
  lang=ES-TRAD style='".$font_size."font-family:
  Roboto;color:".$mailchimp_color_titulo_noticia.";mso-themecolor:accent1;mso-themeshade:191'><o:p></o:p></span></p>
  </td>
 </tr>".$resumen;

                        }else{                      
                        $link="<tr style='mso-yfti-irow:9'>
  <td width=570 style='width:427.8pt;padding:".$padding.";vertical-align:middle;'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal;".$text_align."'><span lang=ES-TRAD>".$s.$resumen."</p>
  </td>
 </tr>";                  
                        }
                        $html[]=$link;
                    }        
                }
            }
        }
        if(!red_informatica_despacho_boletin_report_is_resumen_activado()){
            $html[]=$empty_result;
        }
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
        if(red_informatica_despacho_boletin_report_is_resumen_activado()){
            $name='informatica_boletin_report_resumen_word';    
        }
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
//intelsat
//function despacho_boletin_report_word_get_resumen_html($fila_string,$my_index){
function despacho_boletin_report_word_get_resumen_html($fila_string_in,$my_index){    
    $result='';
    //intelsat
    $fila_string=$fila_string_in;
    //print $fila_string;exit();    
    $s=substr($fila_string,$my_index);
    //intelsat
    $s=despacho_boletin_report_word_unset_despedida_html($s);
    //print $s;exit();    
    $sep='<td>';
    $pos=strpos($s,$sep);
    if($pos===FALSE){
        //return '';
        $result='';
    }else{
        $s=substr($s,$pos+strlen($sep));
        //print $s;exit();  
        $pos=strpos($s,'</td>');
        if($pos===FALSE){
            //return '';
            $result='';
        }else{
            //$result=$s=substr($s,0,$pos);
            $result=substr($s,0,$pos);
            //print $result;exit();
            //$result='<p>'.$result.'</p>';
            //return $result;
        }
    }

    if(red_informatica_despacho_boletin_report_is_resumen_activado()){
    //print $result;exit();    
    /*$result=ltrim($result,'<p>');
    $result=rtrim($result,'</p>');*/
    /*$result=ltrim($result,"<p>");
    $result=rtrim($result,"</p>");*/
      $result=despacho_boletin_report_word_trim_parrafo($result);

    //print $result;exit();    
    

    //$font_size_parrafo_empty="font-size:9.0pt;mso-bidi-font-size:11.0pt;";
    //$font_size_parrafo_empty="font-size:11pt;mso-bidi-font-size:11.0pt;";
    //$font_size_parrafo_empty="font-size:9pt;mso-bidi-font-size:9.0pt;";
    //$font_size_parrafo_empty="font-size:7pt;mso-bidi-font-size:7.0pt;";
    //$font_size_parrafo_empty="font-size:5pt;mso-bidi-font-size:5.0pt;";
    $font_size_parrafo_empty="font-size:3pt;mso-bidi-font-size:3.0pt;";
    //$margin_bottom_parrafo_empty="margin-bottom:0cm;margin-bottom:.0001pt;";
    //intelsat-outlook
    //$margin_bottom_parrafo_empty="margin-bottom:0cm;margin-bottom:0pt;";
    $margin_bottom_parrafo_empty="margin-bottom:0cm !important;margin-bottom:0pt !important;margin-bottom:0px !important;";
    $padding_bottom_parrafo_empty="padding-bottom:0cm !important;padding-bottom:0pt !important;padding-bottom:0px !important;";
    $parrafo_empty="";
    //$line_height='normal';
    //$line_height='5pt';
    $line_height='3pt';
    /*$parrafo_empty="<p class=MsoNormal_parrafo_empty style='".$margin_bottom_parrafo_empty.$font_size_parrafo_empty."text-align:
  justify;line-height:normal'><span lang=EN-US style='".$font_size_parrafo_empty."font-family:Roboto;mso-ansi-language:EN-US'><o:p>&nbsp;</o:p></span></p>";*/
    $parrafo_empty="<p class=MsoNormal_parrafo_empty style='".$margin_bottom_parrafo_empty.$font_size_parrafo_empty."text-align:
  justify;line-height:".$line_height.";'><span lang=EN-US style='".$margin_bottom_parrafo_empty.$font_size_parrafo_empty.$padding_bottom_parrafo_empty."font-family:Roboto;mso-ansi-language:EN-US;line-height:".$line_height."'><o:p>&nbsp;</o:p></span></p>";
    //intelsat-outlook  
    $padding="padding:0cm 5.4pt 0cm 5.4pt;";
    //$padding="padding:0cm 5.4pt 10.0pt 5.4pt;";
    //$padding="padding:0cm 5.4pt 20.0pt 5.4pt;";
    //$margin_bottom="margin-bottom:.0001pt";    
    //$margin_bottom="margin-bottom:3pt;";
    //$margin_bottom=despacho_boletin_report_word_informatica_define_margin_bottom();
    //$margin_bottom="margin-bottom:8px !important;font-family:Roboto !important;";
    //$margin_bottom="padding-bottom:8px !important;font-family:Roboto !important;";
    $margin_bottom="padding-bottom:0px !important;font-family:Roboto !important;";
    $td_width=despacho_boletin_report_word_define_informatica_td_width();
    //$td_style_width="427.8pt";
    $td_style_width=$td_width."px";
    $td_width=" width='".$td_width."'";
  
    //$font_size="font-size:9.0pt;mso-bidi-font-size:11.0pt;";
    $font_size="font-size:11pt;mso-bidi-font-size:11.0pt;";
    $result="<tr style='mso-yfti-irow:8'>
  <td".$td_width." valign=top style='width:".$td_style_width.";".$padding."'>
  <p class=MsoNormal style='".$margin_bottom."text-align:
  justify;line-height:normal'><span lang=ES-TRAD style='".$font_size."font-family:Roboto'>".$result."<o:p></o:p></span></p>".$parrafo_empty."
  </td>
 </tr>";
    }else{
        if(!empty($result)){
            $result='<p>'.$result.'</p>';
        }
    }
 
    return $result;
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
        if(red_informatica_despacho_boletin_report_is_resumen_activado()){
            $result='informatica_boletin_report_resumen_word.html';    
        }
    }
    return $result;
}
function despacho_boletin_report_word_set_node_title_with_nid($node_title,$node,$bg){
    $result=$node_title;
    //if(isset($bg->is_boletin_report) && !empty($bg->is_boletin_report) && $bg->is_boletin_report==1){
        $result.='<my_node_nid>'.$node->nid.'</my_node_nid>';
    //}
    return $result;
}
function despacho_boletin_report_word_get_node_title_node(&$value){
    if(despacho_boletin_report_is_resumen_activado()){
        $nid=despacho_boletin_report_word_get_node_title_nid($value);
        $value=despacho_boletin_report_word_unset_node_title_nid($value);
        if(!empty($nid)){
            $node=node_load($nid);
            return $node;
        }
    }
    $my_result=new stdClass();
    return $my_result;    
}
function despacho_boletin_report_word_get_node_title_nid($value){
    $nid='';
    /*$html_dom=DOMDocument::loadHTML($value.';');
    $html_nodes=$html_dom->getElementsByTagName('my_node_nid') ;
    if ($html_nodes->length>0) {
       foreach($html_nodes as $html_node){
            $nid=$html_node->nodeValue;
            return $nid;
       }
    }*/
    $nid=red_respacho_get_html_tag_value($value,'my_node_nid');
    //print $nid;exit(); 
    return $nid;
}
function despacho_boletin_report_word_get_source_title_url_html($node,$br=''){
    $html=array();
    $source_title='';
    if(isset($node->field_item_source_title) && isset($node->field_item_source_title[0])){
        if(isset($node->field_item_source_title[0]['value']) && !empty($node->field_item_source_title[0]['value'])){
            //$html[]='<p><b>'.t('Source title').'</b>: '.$node->field_item_source_title[0]['value'].'</p>';
            $source_title=$node->field_item_source_title[0]['value'];
        }    
    }
    $source_link='';
    if(isset($node->field_item_source_url) && isset($node->field_item_source_url[0])){
        if(isset($node->field_item_source_url[0]['value']) && !empty($node->field_item_source_url[0]['value'])){
            $source_url=red_despacho_add_http($node->field_item_source_url[0]['value']);
            /*$source_link=l($source_url,$source_url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
            $html[]='<p><b>'.t('Source Url').'</b>: '.$source_link.'</p>';*/
            if(empty($source_title)){
                $source_title=$source_url;
            }
            $source_link=l($source_title,$source_url,array('absolute'=>TRUE,'attributes'=>array('target'=>'_blank')));
        }    
    }

    if(!empty($source_title)){
            $margin_bottom="margin-bottom:2px !important;font-family:Roboto !important;";
            $td_width=despacho_boletin_report_word_define_informatica_td_width();
            //$td_style_width="427.8pt";
            $td_style_width=$td_width."px";
            $td_width=" width='".$td_width."'";
  
            //$font_size="font-size:9.0pt;mso-bidi-font-size:11.0pt;";
            $font_size="font-size:10pt;mso-bidi-font-size:11.0pt;";

            $mailchimp_color_titulo_fuente="#7F7F7F";
            if(isset($br->mailchimp_color_titulo_fuente) && !empty($br->mailchimp_color_titulo_fuente)){
              $mailchimp_color_titulo_fuente=$br->mailchimp_color_titulo_fuente;
            }

            //intelsat-outlook
            $padding_source_title="padding:0cm 5.4pt 0cm  5.4pt";
            //$padding_source_title="padding:0cm 5.4pt 10.0pt 5.4pt";


            $html[]="<tr style='mso-yfti-irow:7'>
          <td".$td_width." valign=top style='width:".$td_style_width.";".$padding_source_title."'>
          <p class=MsoNormal style='".$margin_bottom."text-align:
          justify;line-height:normal'><i style='mso-bidi-font-style:normal'><span
          lang=ES-TRAD style='".$font_size."font-family:
          Roboto;color:".$mailchimp_color_titulo_fuente.";mso-themecolor:text1;mso-themetint:128'>".$source_title."<o:p></o:p></span></i></p>
          </td>
         </tr>";
    }     

    return implode('',$html);
}
function despacho_boletin_report_word_unset_node_title_nid($value){
    $result=preg_replace('/<my_node_nid[^>]*>([\s\S]*?)<\/my_node_nid[^>]*>/','',$value);
    return $result;
}
function despacho_boletin_report_word_unset_despedida_html($value){
    $pos=strpos($value,'<table class="mail_table_despedida"');
    if($pos===FALSE){
        return $value;
    }else{
        $result=substr($value,0,$pos);
        return $result;
    }
}
function despacho_boletin_report_word_informatica_define_margin_bottom(){
    $margin_bottom="margin-bottom:8pt;";
    return $margin_bottom;
}
function despacho_boletin_report_word_unset_style($link){
    $result=$link;
    //$result=str_replace(' style="text-decoration:none;"','',$link);    
    $konp=' style="';
    $pos=strpos($link,$konp);
    if($pos===FALSE){
        return $link;
    }else{
        $ini=substr($link,0,$pos);
        $s=substr($link,$pos+strlen($konp));
        $konp2='"';
        $pos2=strpos($s,$konp2);
        if($pos2===FALSE){
            return $link;
        }else{
            $result=$ini.substr($s,$pos2+strlen($konp2));
            //print $result;exit();
        }
    }    
    return $result;
}
function despacho_boletin_report_word_define_informatica_td_width(){
    $result=713;
    //$result=1000;
    return $result;
}
function despacho_boletin_report_word_trim_parrafo($result_in){
  $result=$result_in;
  /*$pattern = "=^<p>(.*)</p>$=i";
  $result=preg_match($pattern,$result, $matches);*/
  $result=preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1',$result);
  return $result;
}
function despacho_boletin_report_word_get_mailchimp_info_tipo_documento_by_profundidad(&$link_tipos_fuente,&$mailchimp_info_tipo_documento){
  $konp='tipos_fuente_';
  $pos=strpos($link_tipos_fuente,$konp);
                if($pos===FALSE){
                    return 1;
                }else{
                    $s=substr($link_tipos_fuente,$pos+strlen($konp));
                    $pos_tid=strpos($s,'"');
                    if($pos_tid===FALSE){
                      return 1;
                    }else{
                      $tid=substr($s,0,$pos_tid);
                      $term=taxonomy_get_term($tid);
                      $pro=profundidad($term->tid);
                      if($pro>0){
                        $mailchimp_info_tipo_documento['mailchimp_color_fondo_tipo_documento']='#FFFFFF';
                        //$color_letra_tipo_documento='#555555';
                        $color_letra_tipo_documento='#444444';
                        $mailchimp_info_tipo_documento['mailchimp_color_letra_tipo_documento']=$color_letra_tipo_documento;                        
                        //$link_tipos_fuente='<i>'.$link_tipos_fuente.'</i>';
                        $link_tipos_fuente='<i style="mso-bidi-font-style:normal">'.$link_tipos_fuente.'</i>';
                      }
                    }  
                }
                //print $link_tipos_fuente;exit();    
  return 1;                  
}                    