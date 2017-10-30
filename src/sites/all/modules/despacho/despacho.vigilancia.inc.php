<?php
function despacho_vigilancia_get_source_types_content(){
    $html=array();
    $label='';
    //$source_type_array=taxonomy_get_tree(1);
    //$source_type_array=despacho_vigilancia_order_source_type_array($source_type_array);
    $source_type_array=taxonomy_get_custom_tree(1);
    if(!empty($source_type_array)){
        foreach($source_type_array as $i=>$term){
            if(despacho_vigilancia_is_source_type_noticias($term->tid)){
                $term_name=$term->name;
                $description='';
                $term_lang=taxonomy_get_term_by_language($term->tid);
                  $description=$term->description;
                  if(isset($term_lang->name) && !empty($term_lang->name)){                      
                      $term_name=$term_lang->name;
                  }
                  if(isset($term_lang->description) && !empty($term_lang->description)){
                      $description=$term_lang->description;
                  }
                $style=' style="list-style-image: none;list-style-type: none;"';
                $my_selected_tipos_fuente=red_despacho_get_selected_tipos_fuente();   
                if($term->tid==$my_selected_tipos_fuente){
                    $link=red_despacho_get_term_link_delete_filter($term_name);
                }else{
                    $link=l($term_name,'despacho/canales/tipos_fuente/'.$term->tid);
                }
                $html[]='<li class="nivel'.$term->depth.'"'.$style.'>'.$link.'</li>';
            }    
        }
    }
    return implode('',$html);
}
function despacho_vigilancia_order_source_type_array($source_type_array){
    $result=red_despacho_vigilancia_order_term_array($source_type_array);
    return $result;
}
function despacho_vigilancia_is_source_type_noticias($tid){
    $item_list=despacho_vigilancia_get_source_type_tid_item_list($tid);
    if(count($item_list)>0){
        return 1;
    }
    return 0;
}
function despacho_vigilancia_get_source_type_tid_item_list($tid){
    $result=hontza_canal_rss_get_content_field_item_source_tid_nid_array($tid,1);
    $canal_nid_list=despacho_get_source_type_canal_nid_list($tid);
    if(count($canal_nid_list)>0){
        foreach($canal_nid_list as $i=>$canal_nid){
            $nid_list=get_canal_nid_list($canal_nid);
            $result=array_merge($result,$nid_list);
        }
    }
    return $result;
}
function despacho_get_source_type_canal_nid_list($tid){
    $where=array();
    $where[]='1';
    $where[]='content_field_canal_source_type.field_canal_source_type_value='.$tid;
    if($with_group){
        $my_grupo=og_get_group_context();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]='og_ancestry.group_nid='.$my_grupo->nid;
        }
        $where[]='NOT node.nid IS NULL';
    }
    $sql='SELECT content_field_canal_source_type.* 
    FROM {content_field_canal_source_type} 
    LEFT JOIN {og_ancestry} ON content_field_canal_source_type.nid=og_ancestry.nid 
    LEFT JOIN {node} ON content_field_canal_source_type.vid=node.vid 
    WHERE '.implode(' AND ',$where).' 
    GROUP BY content_field_canal_source_type.nid';     
    //print $sql;exit();
    $res=db_query($sql);    
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
    
    
}
function despacho_vigilancia_canales_tipos_fuente_callback(){
    global $language;
    $output='';
    $tid=arg(2);
    $num_rows=false;
    $term_name='';
    $arg_type=arg(3);
    //intelsat-2015
    $is_headers=1;
    //if(hontza_canal_rss_is_publico_activado()){
        $info=despacho_vigilancia_get_canales_categorias_params($tid,$arg_type);
        $tid=$info['tid'];
        $arg_type=$info['arg_type'];
        //print $arg_type.'<br>';
        //$is_headers=$info['is_headers'];
    //}
    if($is_headers){
        $output.=hontza_canales_por_categorias_menu('canales_tipos_fuente',$tid,$arg_type);
        /*$output.='<div class="view-header">';
        $output.=link_validar_canal('',1);
        $output.='</div>';*/
        $output.=hontza_define_vigilancia_form_filter();
    }    
    if($arg_type=='bookmarks'){
        $my_grupo=og_get_group_context();
        if(!(isset($my_grupo->nid) && !empty($my_grupo->nid))){
            return '';
        }
        $bookmark_form_ini=hontza_solr_funciones_get_bookmark_ini(0);
        $output.=$bookmark_form_ini;
    }
    //            
    if(!empty($tid) && is_numeric($tid)){
        $term=taxonomy_get_term($tid);        
        if(isset($term->tid) && !empty($term->tid)){
            $term_name=$term->name;
        }
        //
        $my_limit=red_despacho_get_nodes_limit(20);
        //intelsat-2015
        $item_list=despacho_vigilancia_get_source_type_tid_item_list($tid);
        $item_list=hontza_canales_por_categorias($item_list,$arg_type);
        $item_list=array_unique($item_list,SORT_NUMERIC);
        //$item_list=despacho_vigilancia_get_destacadas_item($item_list);
        $my_list=array();
        $kont=0;
        $num=count($item_list);
        $max=100;
        if($num>0){
            /*if($num>$max){
                $item_list=array_slice($item_list,0,$max);                
            }*/
            foreach($item_list as $i=>$nid){
                    /*$my_node=node_load($nid);
                    $my_list[$kont]=new stdClass();
                    $my_list[$kont]->view= node_view($my_node, 1);                
                    $my_list[$kont]->created=$my_node->created;
                    */
                    $my_list[$kont]=new stdClass();
                    $my_list[$kont]->nid=$nid;
                    $my_list[$kont]->view= '';
                    $created=hontza_get_node_created($nid);
                    $my_list[$kont]->created=$created;                
                $kont++;
                $num_rows=true;
            }
        }
        if(!empty($my_list)){
            if(empty($arg_type) || in_array($arg_type,array('pendientes','ultimas','validados','rechazados'))){
                $my_list=array_ordenatu($my_list,'created','desc',1);
            }
            $my_list=my_set_estrategia_pager($my_list,$my_limit);
            foreach($my_list as $z=>$row_page){
                $my_node=node_load($row_page->nid);
                $my_list[$z]->view=node_view($my_node, 1);                
            }
            $output.=set_array_view_html($my_list);
        }
    }

      if ($num_rows) {
        /*$feed_url = url('idea_rss.xml', array('absolute' => TRUE));
        drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
        $output .= theme('pager', NULL, $my_limit);
        
      }
      else {

        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
        
      }

      if(!empty($term_name)){
        $term_name=get_term_extra_name($tid, $language->language,$term_name);
        $my_title=t('News in category').': '.$term_name;
        if(hontza_canal_rss_is_tipo_fuente($tid)){            
            $my_title=hontza_canal_rss_get_noticias_tipo_fuente_title($term_name);
        }  
      }else{
        $my_title=t('News in category');  
      }

      drupal_set_title($my_title);

    //
    return $output;
}
function despacho_vigilancia_canales_tipos_fuente_access(){
    if(user_access('access content')){
        return TRUE;
    }
    return FALSE;
}
function despacho_vigilancia_get_canales_categorias_params($tid,$arg_type){
    $info=array();
    $info['tid']=$tid;
    $info['arg_type']=$arg_type;
    $info['is_headers']=1;
    //if(publico_is_pantalla_publico('vigilancia')){
        $info['tid']=arg(3);
        $info['arg_type']=arg(4);
        if(empty($info['arg_type'])){
            $info['arg_type']='ultimas';
        }
        $info['is_headers']=0;
    //}
    return $info;
}
function despacho_vigilancia_is_vigilancia(){
    if(despacho_vigilancia_is_canales_tipos_fuente_pantalla()){
        return 1;
    }
    if(despacho_vigilancia_is_categorizar()){
        return 1;
    }
    return 0;
}
function despacho_vigilancia_get_canales_por_categorias_url_tab($tid,$type,$url,$type_tab){
    $result=$url;
    if(despacho_vigilancia_is_canales_tipos_fuente($type)){
        return despacho_vigilancia_create_canales_tipos_fuente_url($tid,$type_tab);
    }
    return $result;
}
function despacho_vigilancia_create_canales_tipos_fuente_url($tid,$type_tab){
    return 'despacho/canales/tipos_fuente/'.$tid.'/'.$type_tab;
}
function despacho_vigilancia_is_canales_tipos_fuente($type){
    if($type=='canales_tipos_fuente'){
        return 1;
    }
    return 0;
}
function despacho_vigilancia_is_canales_tipos_fuente_pantalla(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='despacho'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='canales'){
            $param2=arg(2);
            if(!empty($param2) && $param2=='tipos_fuente'){
                $param3=arg(3);
                if(!empty($param3) && is_numeric($param3)){
                    return 1;
                }
            }
        }
    }
    return 0;
}
function despacho_vigilancia_get_selected_tipos_fuente(){
    if(despacho_vigilancia_is_canales_tipos_fuente_pantalla()){        
        $tid=arg(3);
        return $tid;
    }
    return '';
}
function despacho_vigilancia_get_categorizar_link($node){
    $label='';
    $title=t('Categorize');
    $attributes=array('title'=>$title,'alt'=>$title);
    $destination=drupal_get_destination();
    $url='despacho/'.$node->nid.'/categorizar';
    $attributes['id']=$node->nid.'_categorizar_link';
    $attributes['target']='_blank';
    $is_ficha_completa=red_despacho_is_ficha_completa_js();
    despacho_vigilancia_add_categorizar_window_open_js($node->nid,$url.'/popup'.$is_ficha_completa);
    return l($label,$url,array('query'=>$destination,'attributes'=>$attributes));
}
function despacho_vigilancia_categorizar_form(){
    $form=array();
    $nid=arg(1);
    $node=node_load($nid);
    $form['my_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$nid,
    );
    $form['node_title']=array(
        '#value'=>'<h2>'.l($node->title,'node/'.$node->nid).'</h2>'
    );
    hontza_solr_search_add_categorias_form_field($form,$id_categoria,$node);
    $is_popup=despacho_vigilancia_is_categorizar_noticia_pantalla('popup');
    if($is_popup){
       $form['cat']['#attributes']['class']='fieldset_categorizar_popup'; 
    }
    $form['id_categoria']=array(
        '#type'=>'hidden',
        '#default_value'=>$id_categoria,
    );
    /*$form['add_categories_btn']=array(
        '#type'=>'submit',
        '#name'=>'add_categories_btn',
        '#default_value'=>t('Add categories'),
    );*/    
    $form['replace_categories_btn']=array(
        '#type'=>'submit',
        '#name'=>'replace_categories_btn',
        '#default_value'=>t('Replace categories'),
    );
    $url_return='vigilancia/validados';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_return=$_REQUEST['destination'];
    }
    //$label=t('Return');
    $label=t('Close');
    $link_return=l($label,$url_return,array('attributes'=>array('id'=>'edit-id-close-window-categorizar')));
    if($is_popup){
        despacho_vigilancia_categorizar_add_close_window_js();
        /*$form['return_btn']=array(
            '#value'=>$link_return,
        );*/   
        $form['id_close_window_categorizar']=array(
            '#type'=>'button',
            '#value'=>$label,            
        );
    }else{
        $form['return_btn']=array(
            '#value'=>$link_return,
        );        
    }
    return $form;
}  
function despacho_vigilancia_categorizar_access(){
    /*if(user_access('access content')){
        return TRUE;
    }*/
    $nid=arg(1);
    $node=node_load($nid);
    if(_community_tags_tab_access($node)){
        return TRUE;
    }
    return FALSE;
}
function despacho_vigilancia_is_categorizar($konp='categorizar'){
    $param0=arg(0);
    if(!empty($param0) && $param0=='despacho'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2==$konp){
                return 1;
            }
        }
    }
    return 0;
}
function despacho_vigilancia_add_category_checked($node,$contenido,&$form){
    //if(hontza_solr_search_tid_in_field_item_canal_category_tid($node,$contenido->tid)){
    if(despacho_vigilancia_in_node_category($node,$contenido->tid)){
        $form['cat'][$contenido->tid]['#attributes']['checked']='checked';        
    }
}
function despacho_vigilancia_on_node_save($op,&$node){
    if(in_array($node->type,array('item','noticia'))){
        /*$seleccionado_boletin=red_solr_inc_get_seleccionado_boletin($node,1);
        if(!$seleccionado_boletin)
        {
            $categorias_tematicas=red_despacho_vigilancia_get_node_categorias_tematicas_tid_array($node);
            if(!empty($categorias_tematicas)){
                despacho_vigilancia_validar_categorizar_node($node,'on_node_save');
            }    
        }*/
        /*if($node->type=='item'){
            despacho_vigilancia_item_post_url_save($op,$node);
        }*/
    }
}
function despacho_vigilancia_validar_categorizar_node($node,$type='on_node_save'){
    if($type=='on_node_save'){
        /*$validate_status=red_despacho_get_validate_status($node);
        if($validate_status!=3){
            boletin_report_insert_yes($node->nid);
        }
        if($validate_status!=2 && $validate_status!=3){
            hontza_validar_con_accion($node->nid);
        }*/
    }else{
        $validate_status=red_despacho_get_validate_status($node);
        boletin_report_insert_yes($node->nid);
        if($validate_status!=2){
            hontza_validar_con_accion($node->nid);
        }
    }    
}
function despacho_vigilancia_categorizar_form_submit($form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if(isset($form_state['clicked_button']['#name']) && !empty($form_state['clicked_button']['#name'])){
            $node_id_array=array();
            $nid=$form_state['values']['my_nid'];
            $node_id_array[]=$nid;
            if($form_state['clicked_button']['#name']=='replace_categories_btn'){
                red_despacho_modificar_taxonomia_save($form_state,$node_array,$node_id_array);
                $node=node_load($nid);
                despacho_vigilancia_validar_categorizar_node($node,'modificar_taxonomia');
                if(despacho_vigilancia_is_categorizar_noticia_pantalla('popup')){
                    $is_ficha_completa=despacho_vigilancia_get_drupal_goto_is_ficha_completa();
                    drupal_goto('despacho/'.$nid.'/on_categorizar/popup'.$is_ficha_completa);
                }
            }
        }
    }
}
function despacho_vigilancia_add_categorizar_window_open_js($nid,$url_in,$my_id='mywindow_despacho_vigilancia_categorizar',$type='categorizar'){
    $url=url($url_in);
    $info_size=despacho_vigilancia_get_categorizar_popup_info_size();
    $width=$info_size['width'];
    $height=$info_size['height'];    
    $js='$(document).ready(function()
   {            
            on_despacho_vigilancia_categorizar_link_click();
            function on_despacho_vigilancia_categorizar_link_click(){
                $("#'.$nid.'_'.$type.'_link").click(function(){
                    HontzaPopupCenter_despacho_vigilancia_categorizar("'.$url.'","'.$my_id.'",'.$width.','.$height.');
                    return false;
                });
            }
            function HontzaPopupCenter_despacho_vigilancia_categorizar(url, title, w, h) {
                // Fixes dual-screen position                         Most browsers      Firefox
                var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
                var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

                width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                var top = ((height / 2) - (h / 2)) + dualScreenTop;
                var newWindow = window.open(url, title, "scrollbars=yes, width=" + w + ", height=" + h + ", top=" + top + ", left=" + left);

                // Puts focus on the newWindow
                if (window.focus) {
                    newWindow.focus();
                }
            }
   });';
   drupal_add_js($js,'inline');
}
function despacho_vigilancia_is_categorizar_noticia_pantalla($type){
   if(despacho_vigilancia_is_categorizar()){
       $param=arg(3);
       if(!empty($param) && $param==$type){
           return 1;
       }
   }
   return 0;
}
function despacho_vigilancia_get_wrapper_style(){
   if(despacho_vigilancia_is_categorizar_noticia_pantalla('popup') || despacho_vigilancia_is_reclasificar_tipo_fuente_noticia_pantalla('popup')){
       $info_size=despacho_vigilancia_get_categorizar_popup_info_size();
       $width=$info_size['width']-25;
       return ' style="width:'.$width.'px;"';
   }
   return '';
}
function despacho_vigilancia_get_categorizar_popup_info_size(){
   $result=array();
   $result['width']=450;
   $result['height']=450;
   return $result;
}
function despacho_vigilancia_on_categorizar_popup_callback(){
   despacho_vigilancia_on_categorizar_popup();
}
function despacho_vigilancia_is_on_categorizar_noticia_pantalla($type){
    if(despacho_vigilancia_is_on_categorizar()){
       $param=arg(3);
       if(!empty($param) && $param==$type){
           return 1;
       }
   }
   return 0; 
}
function despacho_vigilancia_is_on_categorizar(){
   if(despacho_vigilancia_is_categorizar('on_categorizar')){
       return 1;
   }
   return 0;
}
function despacho_vigilancia_categorizar_add_close_window_js(){
    $js='$(document).ready(function()
     {
        $("#edit-id-close-window-categorizar").click(function()
        {
            self.close();
            return false;
        });         
     });';
    drupal_add_js($js,'inline');
}
function despacho_vigilancia_is_show_user_image(){
    return 0;
}
function despacho_vigilancia_unset_hound_id_item_title(&$node,$canal_nid){
    if(hontza_is_hound_canal($canal_nid,'')){
        $result=$node->title;
        $result=ltrim($result);
        //Quitar hound id que tiene en el title entre corchetes        
        $result=preg_replace('/^\[[^\]]*\]/','',$result);
        $result=ltrim($result);
        $node->title=$result;
    }
}
function despacho_vigilancia_in_node_category($node,$contenido_tid){
    if($node->type=='item'){
        return hontza_solr_search_tid_in_field_item_canal_category_tid($node,$contenido_tid);
    }else{        
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            $result=array_keys($node->taxonomy);
            if(in_array($contenido_tid,$result)){
                return 1;
            }
        }    
    }
    return 0;
}
function despacho_vigilancia_get_reclasificar_tipo_fuente_link($node){
    $label='';
    $title=t('Types of Sources');
    $attributes=array('title'=>$title,'alt'=>$title);
    $destination=drupal_get_destination();
    $url='despacho/'.$node->nid.'/reclasificar_tipo_fuente';
    $attributes['id']=$node->nid.'_reclasificar_tipo_fuente_link';
    $attributes['target']='_blank';
    $is_ficha_completa=red_despacho_is_ficha_completa_js();
    despacho_vigilancia_add_categorizar_window_open_js($node->nid,$url.'/popup'.$is_ficha_completa,'mywindow_despacho_vigilancia_reclasificar_tipo_fuente','reclasificar_tipo_fuente');
    return l($label,$url,array('query'=>$destination,'attributes'=>$attributes));
}
function despacho_vigilancia_reclasificar_tipo_fuente_form(){
    $form=array();
    drupal_set_title(t('Types of Sources'));        
    $nid=arg(1);
    $node=node_load($nid);
    $form['my_nid']=array(
        '#type'=>'hidden',
        '#default_value'=>$nid,
    );
    $form['node_title']=array(
        '#value'=>'<h2>'.l($node->title,'node/'.$node->nid).'</h2>'
    );    
    if(hontza_solr_is_solr_activado() || hontza_canal_rss_is_visualizador_activado()){
        hontza_solr_search_add_source_type_form_field($form,0,0,0,$node);        
    }     
    $is_popup=despacho_vigilancia_is_reclasificar_tipo_fuente_noticia_pantalla('popup');
    if($is_popup){
       $form['cat']['#attributes']['class']='fieldset_categorizar_popup'; 
    }
    $form['id_categoria']=array(
        '#type'=>'hidden',
        '#default_value'=>1,
    );
    $form['replace_btn']=array(
        '#type'=>'submit',
        '#name'=>'replace_btn',
        '#default_value'=>t('Replace'),
    );
    $url_return='vigilancia/validados';
    if(isset($_REQUEST['destination']) && !empty($_REQUEST['destination'])){
        $url_return=$_REQUEST['destination'];
    }
    //$label=t('Return');
    $label=t('Close');
    $link_return=l($label,$url_return,array('attributes'=>array('id'=>'edit-id-close-window-reclasificar-tipo-fuente')));
    if($is_popup){
        despacho_vigilancia_reclasificar_tipo_fuente_add_close_window_js();
        $form['id_close_window_reclasificar_tipo_fuente']=array(
            '#type'=>'button',
            '#value'=>$label,            
        );
    }else{
        $form['return_btn']=array(
            '#value'=>$link_return,
        );        
    }
    return $form;
}
function despacho_vigilancia_is_reclasificar_tipo_fuente_noticia_pantalla($type){
   if(despacho_vigilancia_is_reclasificar_tipo_fuente()){ 
       $param=arg(3);
       if(!empty($param) && $param==$type){
           return 1;
       }
   }
   return 0;
}
function despacho_vigilancia_is_reclasificar_tipo_fuente(){
   return despacho_vigilancia_is_categorizar('reclasificar_tipo_fuente');   
}
function despacho_vigilancia_is_on_reclasificar_tipo_fuente_noticia_pantalla($type){
   if(despacho_vigilancia_is_on_reclasificar_tipo_fuente()){
       $param=arg(3);
       if(!empty($param) && $param==$type){
           return 1;
       }
   }
   return 0;  
}
function despacho_vigilancia_is_on_reclasificar_tipo_fuente(){
   return despacho_vigilancia_is_categorizar('on_reclasificar_tipo_fuente');   
}
function despacho_vigilancia_reclasificar_tipo_fuente_add_close_window_js(){
    $js='$(document).ready(function()
     {
        $("#edit-id-close-window-reclasificar-tipo-fuente").click(function()
        {
            self.close();
            return false;
        });         
     });';
    drupal_add_js($js,'inline');
}
function despacho_vigilancia_reclasificar_tipo_fuente_form_submit($form, &$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if(isset($form_state['clicked_button']['#name']) && !empty($form_state['clicked_button']['#name'])){
            $node_id_array=array();
            $nid=$form_state['values']['my_nid'];
            $node_id_array[]=$nid;
            if($form_state['clicked_button']['#name']=='replace_btn'){
                red_despacho_reclasificar_tipo_fuente_save($form_state,$node_id_array);
                /*$node=node_load($nid);
                despacho_vigilancia_validar_categorizar_node($node,'modificar_taxonomia');*/
                if(despacho_vigilancia_is_reclasificar_tipo_fuente_noticia_pantalla('popup')){
                    $is_ficha_completa=despacho_vigilancia_get_drupal_goto_is_ficha_completa();
                    drupal_goto('despacho/'.$nid.'/on_reclasificar_tipo_fuente/popup'.$is_ficha_completa);
                }               
            }
        }
    }
}
function despacho_vigilancia_on_reclasificar_tipo_fuente_popup_callback(){
    despacho_vigilancia_on_categorizar_popup('reclasificar_tipo_fuente');
}
function despacho_vigilancia_on_categorizar_popup($type=''){
   $nid=arg(1);
   if($type=='reclasificar_tipo_fuente'){
    $js=red_despacho_add_item_source_tid_replace_popup_js($nid);
   }else{
    $js=red_despacho_add_item_canal_category_tid_replace_popup_js($nid);   
   }
   print '<html><head><title>Close</title></head><body onload="'.$js.'self.close();"></body></html>';
   exit();
}
function red_despacho_add_item_source_tid_replace_popup_js($nid,$type='source_tid'){
   if($type=='source_tid'){
    $ul_id=red_despacho_get_item_source_tid_ul_id($nid);
   }else{
    $ul_id=red_despacho_get_item_canal_category_tid_ul_id($nid);
   }   
   $js="";
   $js.=red_despacho_remove_ul_js($ul_id);
   $node=node_load($nid);
   $is_ficha_completa=red_despacho_is_ficha_completa_by_arg();
   $is_js=1;
   if($type=='source_tid'){
    $content=hontza_solr_search_get_item_source_types($node,$is_ficha_completa,$is_js);  
   }else{
    if($node->type=='item'){   
        $content=hontza_solr_funciones_get_item_categorias_tematicas($node,$is_ficha_completa,$is_js);
    }else if($node->type=='noticia'){
        $content=hontza_solr_search_get_noticia_categorias_tematicas($node,$is_ficha_completa,$is_js);
    }    
   }
   $content=str_replace('"','\'',$content);
   $content=addslashes($content);
   //$content=json_encode($content);
   $js.="ul_id.innerHTML='".$content."';";   
   return $js;
}
function red_despacho_is_ficha_completa_js(){
    $is_ficha_completa=red_despacho_is_ficha_completa();
    if($is_ficha_completa){
       $is_ficha_completa='/is_ficha_completa'; 
    }else{
       $is_ficha_completa=''; 
    }
    return $is_ficha_completa;
}    
function red_despacho_is_ficha_completa_by_arg(){
    $result=arg(4);
    if(!empty($result) && $result=='is_ficha_completa'){
        return 1;
    }
    return 0;
}
function red_despacho_add_item_canal_category_tid_replace_popup_js($nid){
    return red_despacho_add_item_source_tid_replace_popup_js($nid,'canal_category_tid');
}
function red_despacho_remove_ul_js($ul_id){
   $js="";
   $js.="var li_array=new Array();";
   $js.="var ul_id=window.opener.document.getElementById('".$ul_id."');";
   $js.="for (i = 0; i < ul_id.childNodes.length; i++) {";
   $js.="li_array[i] = ul_id.childNodes[i];";
   //$js.="ul_id.removeChild(li);";
   $js.="};";
   $js.="for (i = 0; i < li_array.length; i++) {";
   $js.="ul_id.removeChild(li_array[i]);";
   $js.="};";
   return $js;
}
function despacho_vigilancia_get_drupal_goto_is_ficha_completa(){
   $is_ficha_completa=arg(4);
   if(!empty($is_ficha_completa)){
     $is_ficha_completa='/'.$is_ficha_completa;
   }
   return $is_ficha_completa;                  
}
function despacho_vigilancia_validar_categorizar_node_array($node_array){
    if(!empty($node_array)){
        foreach($node_array as $i=>$node){
            despacho_vigilancia_validar_categorizar_node($node,'modificar_taxonomia');
        }
    }
}