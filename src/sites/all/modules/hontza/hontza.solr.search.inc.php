<?php
function hontza_solr_search_menu_items($items_in){
    $items=$items_in;
    $en_grupo_array=array('tag_bookmark_multiple_mode','fivestar_bookmark_multiple_mode','discuss_bookmark_multiple_mode','collaborate_bookmark_multiple_mode',
    'idea_bookmark_multiple_mode','destacar_bookmark_multiple_mode','report_bookmark_multiple_mode','backup_channel_bookmark_multiple_mode',
    'mark_bookmark_multiple_mode','unmark_bookmark_multiple_mode','modificar_taxonomia_bookmark_multiple_mode','validar_bookmark_multiple_mode',
    'rechazar_bookmark_multiple_mode','delete_bookmark_multiple_mode','unselect_report_bookmark_multiple_mode');
    foreach($en_grupo_array as $i=>$v){
        $items['hontza_solr/'.$v]=array(
        'title'=>t('Bulk Actions'),
        'page callback' => 'hontza_solr_search_multiple_mode_callback',
        'access callback' => 'hontza_solr_my_access',    
        );
    }
    $items['my_close']=array(
        'title'=>t('Closed'),
        'page callback' => 'hontza_solr_search_my_close_callback',
        'access callback' => 'hontza_solr_my_access',    
    );
    $items['hontza_solr/simular_actualizar_items']=array(
        'title'=>t('News'),
        'page callback' => 'hontza_solr_search_simular_actualizar_items_callback',
        'access arguments' => array('root'),  
    );
    $items['canal-usuarios/%/bookmarks']=array(
        'title'=>t('News'),
        'page callback' => 'hontza_solr_search_canal_usuarios_bookmarks_callback',
        'access callback' => 'hontza_solr_my_access',
    );
    $items['canales_rss/%']=array(
        'title'=>t('RSS'),
        'page callback' => 'hontza_solr_search_canales_rss_callback',
        'access arguments' => array('access content'),  
    );
    $items['hontza_solr/indexar']=array(
        'title'=>t('Index'),
        'page callback' => 'hontza_canal_rss_solr_indexar_callback',
        'access callback' => TRUE,  
    );
    $items['canales_rss_canal_usuarios/%']=array(
        'title'=>t('RSS'),
        'page callback' => 'hontza_solr_search_canales_rss_callback',
        'access arguments' => array('access content'),  
    );
    return $items;
}    
function hontza_solr_search_get_result_node_id_array(){
    $result=array();
    if(isset($_SESSION['bookmarks_rows']) && !empty($_SESSION['bookmarks_rows'])){
        foreach($_SESSION['bookmarks_rows'] as $i=>$row){
            $result[]=$row->nid;
        }
    }
    return $result;
}
function hontza_solr_search_unset_bookmarks_rows(){
    if(isset($_SESSION['bookmarks_rows']) && !empty($_SESSION['bookmarks_rows'])){
        unset($_SESSION['bookmarks_rows']);
    }
}
function hontza_solr_search_get_checked($nid){
    //if(isset($_SESSION['selected_node_id_array']) && !empty($_SESSION['selected_node_id_array'])){
    if(isset($_REQUEST['selected_node_id_array']) && !empty($_REQUEST['selected_node_id_array'])){
        $selected_node_id_array=explode(',',$_REQUEST['selected_node_id_array']);
        if((isset($selected_node_id_array[0]) && $selected_node_id_array[0]=='is_all_selected') || in_array($nid,$selected_node_id_array)){
            $result=' checked="checked"';
            $nid_array=array();
            $nid_array[]=$nid;
            $selected_node_id_array=array_diff($selected_node_id_array,$nid_array);
            //$_SESSION['selected_node_id_array']=implode(',',$selected_node_id_array);
            return $result;
        }    
    }
    return '';
}
function hontza_solr_search_unset_selected_node_id_array(){
    if(isset($_SESSION['selected_node_id_array'])){
        unset($_SESSION['selected_node_id_array']);
    }
}
function hontza_solr_search_get_popup_center_js(){
            $result='function HontzaPopupCenter_solr(url, title, w, h) {
                // Fixes dual-screen position                         Most browsers      Firefox
                var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
                var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

                width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                var top = ((height / 2) - (h / 2)) + dualScreenTop;
                var newWindow = window.open(url, title, "scrollbars=yes,location=no,statusbar=no,menubar=no,resizable=0,modal=yes,width=" + w + ", height=" + h + ", top=" + top + ", left=" + left);
                // Puts focus on the newWindow
                if (window.focus) {
                    newWindow.focus();
                }
            }';
            return $result;
}
function hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid){
    $result='f[0]=im_og_gid:'.$my_grupo_nid;
    if(isset($_REQUEST['solrsort']) && !empty($_REQUEST['solrsort'])){
        $result.='&solrsort='.$_REQUEST['solrsort'];
    }else{
        $result.='&solrsort=ds_created desc';
    }
    return $result;
}
function hontza_solr_search_get_bookmark_close_button($mode){
    if(in_array($mode,array('tag','fivestar'))){
        hontza_solr_search_add_close_window_js();
        return l(t('Close'),'my_close',array('attributes'=>array('id'=>'id_close_window')));
    }
    return '';
}
function hontza_solr_search_add_close_window_js(){
    $js='$(document).ready(function()
     {
        $("#id_close_window").click(function()
        {
            window.opener.location.reload(false);
            self.close();
        });         
     });';
    drupal_add_js($js,'inline');
}
function hontza_solr_search_set_icono_home_areadebate_block($vars_in){
    $vars=$vars_in;
    $sep='<div class="views-field-title">';
    $result=explode($sep,$vars['rows']);
    if(!empty($result)){
        foreach($result as $i=>$v){
            if($i>0){            
                $result[$i]=my_get_icono_action('debate_left',t('Discussion')).$v;
            }    
        }
        $vars['rows']=implode($sep,$result);        
    }
    return $vars['rows'];
}
function hontza_solr_search_get_tag_popup_link(){
    $html=array();    
    $html[]=l(my_get_icono_action('tag', t('Tag'),''),'hontza_solr/tag_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_tag_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));
    return implode('',$html);
}
function hontza_solr_search_add_tag_popup_js(){
 $my_grupo=og_get_group_context();
 $my_grupo_nid='';
 if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
     $my_grupo_nid=$my_grupo->nid;
 }
   $bookmark_type='default';
   $canal_nid='';
   $tid='';
   if(hontza_is_canales('bookmarks')){
       $bookmark_type='canales';
       $canal_nid=arg(1);
   }else if(hontza_is_canales_categorias('bookmarks')){
       $bookmark_type='categorias';
       $tid=arg(2);
   }else if(hontza_is_canal_usuarios('bookmarks')){
       $bookmark_type='canal-usuarios';
   }
   $is_solr=0;
   $destination='';
   if(hontza_solr_is_resultados_pantalla()){
       $is_solr=1;
       $destination=hontza_solr_search_set_my_destination();
   }
 $select_msg=t('Please Select at least one item');
 //intelsat-2016
 $my_base_path=hontza_canal_rss_get_base_path_help_popup();
 //   
 $js="$(document).ready(function()
     {
        var is_click=0;
        function get_node_id_array(){    
            var node_id_array='';
            var my_array=new Array();
            var my_id='';
            var is_all_selected=$('#select_bookmark_all_txek').attr('checked');
            if(is_all_selected){
                node_id_array='is_all_selected';
            }else{
                $('.node_bookmark_txek_class').each(function() {
                    var is_txeked=$(this).attr('checked');
                    if(is_txeked){    
                        my_id=$(this).attr('id');
                        my_id=my_id.replace('node_','');
                        my_id=my_id.replace('_bookmark_txek','');
                        my_array.push(my_id);
                    }            
                });        
                node_id_array=my_array.join(',');
            }
            //if(node_id_array.length==0){
            //    alert('".$select_msg."');
            //}
            return node_id_array;
        }
        $('.jqm-trigger-tag_bookmark_multiple_mode').click(function(){
            $('#extag_bookmark_multiple_mode').jqm({ajax: '".$my_base_path."help_popup.php?mode=tag&nid=tag_bookmark_multiple_mode&w=500&h=400&my_grupo_nid=".$my_grupo_nid."&bookmark_type=".$bookmark_type."&canal_nid=".$canal_nid."&tid=".$tid."&is_solr=".$is_solr.$destination."&node_id_array='+get_node_id_array(), trigger: 'a.jqm-trigger-tag_bookmark_multiple_mode',modal:true, toTop: true, overlay: 0});
            if(is_click==0){
                is_click=1;
                $('.jqm-trigger-tag_bookmark_multiple_mode').click();
            }
            return false; 
        });        
     });";
     drupal_add_js($js,'inline');
}
function hontza_solr_search_get_tag_popup_html(){
    //global $base_url;
    $node_id_array=$_REQUEST['node_id_array'];
    $node_id_array=explode(',',$node_id_array);
    $node=hontza_solr_search_bookmark_temporal_node_save($node_id_array);   
    $html=community_tags_node_view($node);
    return $html;
}
function hontza_solr_search_bookmark_temporal_node_save($node_id_array){
         $node=new stdClass();
         $node->type='bookmark_temporal';
         $node->title='bookmark_multiple_'.time();
         $node->status=1;
         $node->field_node_id_array[0]['value']=serialize($node_id_array);
         node_save($node);
         $_SESSION['bookmark_nid_temporal_array'][]=$node->nid;
         return $node;
}
function hontza_solr_search_is_popup(){
    if(isset($_REQUEST['nid']) && !empty($_REQUEST['nid']) && in_array($_REQUEST['nid'],array('tag_bookmark_multiple_mode'))){
        return 1;
    }
    return 0;
}
function hontza_solr_search_modificar_taxonomia_access(){
    if(is_super_admin()){
        return 1;
    }        
    //intelsat-2016
    /*if(is_administrador_grupo(1)){
        return 1;
    }*/
    if(red_crear_usuario_is_rol_administrador_creador_grupo()){
        return 1;
    }
    return 0;
}
function hontza_solr_search_get_modificar_taxonomia_link($style){
    $html=array();    
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('modificar_taxonomia', t('Categorize'),''),'hontza_solr/modificar_taxonomia_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_modificar_taxonomia_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';
    return implode('',$html);
}
function hontza_solr_search_modificar_taxonomia_titles_html($node_id_array,$url_return,&$is_return,&$title,$is_solr=0){
    $html=array();
    if(!hontza_solr_search_modificar_taxonomia_access()){
        drupal_access_denied();
        exit();
    }
    $is_return=0;
    $title=t('Categorize');
    //$html[]=hontza_solr_funciones_get_selected_node_titles($node_id_array);
    $html[]=hontza_solr_search_modificar_taxonomia_html($node_id_array,$url_return,$is_solr);
    return implode('',$html);
}
function hontza_solr_search_modificar_taxonomia_html($node_id_array,$url_return,$is_solr){
    return drupal_get_form('hontza_solr_search_modificar_taxonomia_form',$node_id_array,$url_return,$is_solr);
}
function hontza_solr_search_modificar_taxonomia_form(&$form_state,$node_id_array,$url_return_in,$is_solr){
    $form=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $my_destination='';
    if(isset($_REQUEST['my_destination']) && !empty($_REQUEST['my_destination'])){
        $my_destination=$_REQUEST['my_destination'];
    }else{
        $my_destination=$url_return_in;
    }
    $form['node_id_array']=array(
        '#type'=>'hidden',
        '#default_value'=>implode(',',$node_id_array),
    );
    $form['url_return']=array(
        '#type'=>'hidden',
        '#default_value'=>$url_return,
    );
    $form['is_solr']=array(
        '#type'=>'hidden',
        '#default_value'=>$is_solr,
    );
    $form['my_destination']=array(
        '#type'=>'hidden',
        '#default_value'=>$my_destination,
    );    
    hontza_solr_search_add_categorias_form_field($form,$id_categoria);    
    $form['id_categoria']=array(
        '#type'=>'hidden',
        '#default_value'=>$id_categoria,
    );    
    $form['add_categories_btn']=array(
        '#type'=>'submit',
        '#name'=>'add_categories_btn',
        '#default_value'=>t('Add categories'),
    );    
    $form['replace_categories_btn']=array(
        '#type'=>'submit',
        '#name'=>'replace_categories_btn',
        '#default_value'=>t('Replace categories'),
    );    
    //if($is_solr){
        $url_info=parse_url($url_return_in);
        $link_return=l(t('Return'),$url_info['path'],array('query'=>$url_info['query']));
    /*}else{
        $link_return=l(t('Return'),$url_return);
    }*/
    $form['return_btn']=array(
        '#value'=>$link_return,
    );
    return $form;
}
function hontza_solr_search_get_bookmark_mode_js(){
    return 'function hontza_solr_funciones_get_bookmark_mode(my_id){
        if(my_id=="id_backup_channel_bookmark_multiple_mode"){
            return "backup_channel";
        }else if(my_id=="id_collaborate_bookmark_multiple_mode"){
            return "area_trabajo";
        }else if(my_id=="id_discuss_bookmark_multiple_mode"){
            return "debate";
        }else if(my_id=="id_idea_bookmark_multiple_mode"){
            return "idea";
        }else if(my_id=="id_fivestar_bookmark_multiple_mode"){
            return "fivestar";
        }else if(my_id=="id_tag_bookmark_multiple_mode"){
            return "tag";
        }else if(my_id=="id_destacar_bookmark_multiple_mode"){
            return "destacar";
        }else if(my_id=="id_report_bookmark_multiple_mode"){
            return "report";
        }else if(my_id=="id_unmark_bookmark_multiple_mode"){
            return "unmark";
        }else if(my_id=="id_delete_bookmark_multiple_mode"){
            return "delete";
        }else if(my_id=="id_mark_bookmark_multiple_mode"){
            return "mark";
        }else if(my_id=="id_modificar_taxonomia_bookmark_multiple_mode"){
            return "modificar_taxonomia";
        }else if(my_id=="id_validar_bookmark_multiple_mode"){
            return "validar";
        }else if(my_id=="id_rechazar_bookmark_multiple_mode"){
            return "rechazar";
        }else if(my_id=="id_reclasificar_tipo_fuente_bookmark_multiple_mode"){
            return "reclasificar_tipo_fuente";
        }else if(my_id=="id_unselect_report_bookmark_multiple_mode"){
            return "unselect_report";
        } 
        return "";
    }';
}
function hontza_solr_search_modificar_taxonomia_form_submit($form,&$form_state){
    $node_id_array=$form_state['values']['node_id_array'];
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg_string($node_id_array);
    $node_id_array=explode(',',$node_id_array);
    $url_return=$form_state['values']['url_return'];
    $is_solr=$form_state['values']['is_solr'];
    if($is_solr){
        $url_return=base64_decode($url_return);
    }
    //intelsat-2015
    /*$id_categoria=$form_state['values']['id_categoria'];
    //
    $tid_array=hontza_solr_search_get_form_state_values_tid_array($form_state['values'],$id_categoria);
    $value_array=hontza_solr_search_set_value_array($tid_array);
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                if($node->type=='item'){
                    if(hontza_solr_is_solr_activado()){
                        //intelsat-2015
                        if(!(isset($node->field_item_canal_category_tid))){
                            $node->field_item_canal_category_tid=array();
                        }
                        //
                            if(hontza_solr_search_is_replace_categorias($form_state,'replace_categories_btn')){
                                hontza_solr_search_modificar_item_categorias($node,$tid_array);
                            }else if(hontza_solr_search_is_replace_categorias($form_state,'add_categories_btn')){
                                hontza_solr_search_add_item_categorias($node,$tid_array); 
                            }
                            //intelsat-2015
                            hontza_canal_rss_solr_clear_node_index($node,$nid);
                            //                                                
                    }                    
                }else if($node->type=='noticia'){
                    if(hontza_solr_search_is_replace_categorias($form_state,'replace_categories_btn')){
                        hontza_solr_search_modificar_noticia_usuario_categorias($node,$tid_array);
                    }else if(hontza_solr_search_is_replace_categorias($form_state,'add_categories_btn')){
                        hontza_solr_search_add_noticia_usuario_categorias($node,$tid_array); 
                    }
                }
            }
        }
    }*/
    red_despacho_modificar_taxonomia_save($form_state,$node_array,$node_id_array);
    //
    //intelsat-2016
    red_despacho_vigilancia_validar_categorizar_node_array($node_array);
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);        
    hontza_solr_funciones_redirect();
}
//intelsat-2015
//function hontza_solr_search_add_categorias_form_field(&$form,&$id_categoria,$node_in=''){
//intelsat-2016
//function hontza_solr_search_add_categorias_form_field(&$form,&$id_categoria,$node_in=''){
function hontza_solr_search_add_categorias_form_field(&$form,&$id_categoria,$node_in='',$form_state=''){
    //intelsat-2015
    $node=$node_in;
    if(og_get_group_context()){
    //Obtener el ID del grupo en el que se esta
    $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",og_get_group_context()->nid));    
    //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
    $categorias=taxonomy_get_tree($id_categoria);
        
	//gemini	
	//simulatzeko
	//$categorias=array();
	//if(empty($categorias)){
	if(!my_hay_categorias(1,$categorias)){	 
	 //unset($form['buttons']['next']);
  	 //form_set_error('categorias', t('There are no categories'));
        }
	    	
    //$salida = array();
    $form['cat'] = array(
      '#type' => 'fieldset',
      '#title' => t('Categories'),
      //'#description' => '<u>'.t('Please select at least ONE category').'</u>',
	  //gemini
	  '#attributes'=>array('id'=>'my_categorias_yql'),
	  //
    );
    //intelsat-2016
    $indefinida_tid=my_get_term_data_tid('Categoria indefinida');
    foreach ($categorias as $id => $contenido){
      //gemini-2014
      $pro=profundidad($contenido->tid);
      //$pro=variable_get('profundidad_valor',0);
      $form['cat'][$contenido->tid] = array(
        //gemini-2014
        //AVISO::::se ha quitado required  
        //'#required' => TRUE,
        '#type' => 'checkbox',
        '#prefix' => '<div class=taxo'.$pro.'>',
        '#suffix' => '</div>', 
        '#title' => $contenido->name,
      );
      //intelsat-2015
      red_despacho_add_category_checked($node, $contenido, $form);
      //intelsat-2016
      red_copiar_add_undefined_category_checked($contenido, $form,$indefinida_tid,$form_state);
    }
  }    
}
function hontza_solr_search_get_form_state_values_tid_array($values_in,$id_categoria,$type=''){
    $result=array();
    $values=$values_in;
    if(!empty($values)){
        if(!empty($type)){
            $values=$values[$type];
        }
        foreach($values as $tid=>$v){
            if(is_numeric($tid) && !empty($v)){
                $term=taxonomy_get_term($tid);
                if(isset($term->tid) && !empty($term->tid) && $term->vid==$id_categoria){
                    $result[]=$tid;
                }    
            }
        }
    }
    return $result;
}
function hontza_solr_search_set_value_array($tid_array){
    $result=array();
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            $result[]['value']=$tid;
        }
    }
    return $result;
}
function hontza_solr_search_modificar_item_categorias($node,$tid_array){
    hontza_solr_reset_item_canal_category_tid($node);
    if(!empty($tid_array)){
        $delta=0;
        foreach($tid_array as $k=>$tid){
            if($delta>0 || ($delta==0 && !hontza_canal_rss_solr_existe_content_field_item_canal_category_tid_row($node))){
                $res=db_query('INSERT INTO {content_field_item_canal_category_tid}(field_item_canal_category_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
            }else{
                $res=db_query('UPDATE {content_field_item_canal_category_tid} SET field_item_canal_category_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$tid,$node->nid,$node->vid);
            }    
            $delta++;
        }
    }
    /*
    //intelsat-2015
    $updated=0;
    */
    hontza_solr_set_item_solr_updated($node,$updated);
    hontza_solr_search_clear_cache_content($node);
}
function hontza_solr_search_clear_cache_content($node){
    $sql='DELETE FROM {cache_content} where cid = "content:'.$node->nid.':'.$node->vid.'"';
    db_query($sql);
}
function hontza_solr_search_set_my_destination($is_solr=0){
    global $base_root;    
    if($is_solr){
        $result=hontza_solr_get_busqueda().'&solrsort=ds_created desc';
        if(isset($_REQUEST['is_select_bookmark_all']) && !empty($_REQUEST['is_select_bookmark_all'])){
            $result.='&is_select_bookmark_all=1';
        }
        if(isset($_REQUEST['is_my_all_results']) && !empty($_REQUEST['is_my_all_results'])){
            $result.='&is_my_all_results=1';
        }
    }else{
        $result=$base_root.request_uri();        
    }        
    $result='&my_destination='.base64_encode($result);
    return $result;
}
function hontza_solr_search_is_replace_categorias($form_state,$name){
    if(isset($form_state['clicked_button']['#name']) && $form_state['clicked_button']['#name']==$name){
        return 1;
    }
    return 0;
}
function hontza_solr_search_add_item_categorias($node,$tid_array){
    $content_field_item_canal_category_tid_array=hontza_solr_get_content_field_item_canal_category_tid($node->nid,$node->vid,1);
    $delta=hontza_solr_search_get_next_delta($content_field_item_canal_category_tid_array);
    //
    if(!empty($tid_array)){
        foreach($tid_array as $k=>$tid){
            if(!hontza_solr_search_in_content_field_item_canal_category_tid_array($tid,$content_field_item_canal_category_tid_array)){
                $res=db_query('INSERT INTO {content_field_item_canal_category_tid}(field_item_canal_category_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
                $delta++;
            }                
        }
    }
    /*
    //intelsat-2015
    $updated=0;
    */
    hontza_solr_set_item_solr_updated($node,$updated);
    hontza_solr_search_clear_cache_content($node);
}
function hontza_solr_search_get_next_delta($my_array){
    $num=count($my_array);
    if($num>0){
        $row=$my_array[$num-1];
        if(isset($row->delta)){
            $result=$row->delta;
            if(empty($result)){
                $result=0;
            }
            $result=$result+1;
            return $result;
        }
    }
    return 0;
}
function hontza_solr_search_in_content_field_item_canal_category_tid_array($tid,$content_field_item_canal_category_tid_array){
    if(!empty($content_field_item_canal_category_tid_array)){
        foreach($content_field_item_canal_category_tid_array as $i=>$row){
            if($row->field_item_canal_category_tid_value==$tid){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_solr_search_get_validar_link($style){
    $html=array();    
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('validar_checkbox', t('Validate'),''),'hontza_solr/validar_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_validar_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';
    return implode('',$html);
}
function hontza_solr_search_get_rechazar_link($style){
    $html=array();    
    $html[]='<div style="'.$style.'">';
    $html[]=l(my_get_icono_action('rechazar_checkbox', t('Reject'),''),'hontza_solr/rechazar_bookmark_multiple_mode',array('html'=>TRUE,'attributes'=>array('id'=>'id_rechazar_bookmark_multiple_mode','class'=>'a_class_bookmark_multiple_mode')));    
    $html[]='</div>';
    return implode('',$html);    
}
function hontza_solr_search_validar_titles_html($node_id_array,$url_return,&$is_return,&$title,$is_solr=0){
    $html=array();
    $is_return=0;
    $title=t('Validate');
    //$html[]=hontza_solr_funciones_get_selected_node_titles($node_id_array);
    $html[]=hontza_solr_search_validar_html($node_id_array,$url_return,$is_solr);
    return implode('',$html);
}
function hontza_solr_search_validar_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);
    drupal_goto('hontza_solr/validar_multiple/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);   
}
function hontza_solr_search_validar_multiple_callback(){
    global $user;
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg(explode(',',arg(2)));  
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
            $row=new stdClass();
            $row->nid=$nid;
            hontza_delete_flag_content($row);
            $flag_result = flag('flag','leido_interesante',$row->nid);
        }    
    }
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);
    hontza_solr_funciones_redirect();
}
function hontza_solr_search_rechazar_titles_html($node_id_array,$url_return,&$is_return,&$title,$is_solr=0){
    $html=array();
    $is_return=0;
    $title=t('Reject');
    //$html[]=hontza_solr_funciones_get_selected_node_titles($node_id_array);
    $html[]=hontza_solr_search_rechazar_html($node_id_array,$url_return,$is_solr);
    return implode('',$html);
}
function hontza_solr_search_rechazar_html($node_id_array,$url_return_in,$is_solr=0){
    $html=array();
    $url_return=$url_return_in;
    if($is_solr){
        $url_return=base64_encode($url_return_in);
    }
    $node_id_array_string=hontza_solr_funciones_get_node_id_string_by_all_selected($node_id_array);            
    drupal_goto('hontza_solr/rechazar_multiple/'.$node_id_array_string,'my_destination='.$url_return.'&is_solr='.$is_solr);   
}
function hontza_solr_search_rechazar_multiple_callback(){
    global $user;
    $node_id_array=hontza_solr_funciones_get_node_id_array_by_arg(explode(',',arg(2))); 
    if(!empty($node_id_array)){
        foreach($node_id_array as $i=>$nid){
           $row=new stdClass();
           $row->nid=$nid;
           hontza_delete_flag_content($row);
           $flag_result = flag('flag','leido_no_interesante',$row->nid);
        }    
    }
    //intelsat-2015
    $num=count($node_id_array);
    red_set_bulk_command_executed_message($num);
    hontza_solr_funciones_redirect();
}
function hontza_solr_search_multiple_mode_callback(){
    return hontza_solr_search_accion_en_grupo_message();
}
function hontza_solr_search_accion_en_grupo_message(){
    return 'Esta acción no se puede ejecutar en otra pestaña, hay que ejecutar en la misma página';
}
function hontza_solr_search_get_otro_tab_navegador_js(){
    hontza_solr_search_get_js_variables($bookmark_type,$canal_nid,$tid,$is_solr,$destination,$current_path_all_selected,$current_path,$select_msg);
    $result='$(".node_bookmark_txek_class").click(function() {
                var is_txeked=$(this).attr("checked");
                modificar_en_grupo_link();                           
            });';
    $result.='
            function modificar_en_grupo_link(){
                var mode_array=new Array("backup_channel","collaborate","discuss","idea","fivestar","tag","destacar","report","unmark","delete","mark","modificar_taxonomia","validar","rechazar","reclasificar_tipo_fuente");
                for(var i in mode_array){
                    modificar_en_grupo_open_link(mode_array[i]);
                }
            }
            function modificar_en_grupo_open_link(s){
                var mode=modificar_en_grupo_get_mode(s);
                var my_url=$("#id_"+s+"_bookmark_multiple_mode").attr("href");
                var my_array=new Array();
                var my_id="";
                var is_all_selected=$("#select_bookmark_all_txek").attr("checked");
                if(is_all_selected){
                    node_id_array="is_all_selected";
                }else{
                    $(".node_bookmark_txek_class").each(function() {
                        var is_txeked=$(this).attr("checked");
                        if(is_txeked){    
                            my_id=$(this).attr("id");
                            my_id=my_id.replace("node_","");
                            my_id=my_id.replace("_bookmark_txek","");
                            my_array.push(my_id);
                        }            
                    });        
                    node_id_array=my_array.join(",");
                }
                my_url="'.url('hontza_solr/bookmark_multiple_mode').'?mode="+mode+"&node_id_array="+node_id_array+"&bookmark_type='.$bookmark_type.'&canal_nid='.$canal_nid.'&tid='.$tid.'&is_solr='.$is_solr.$destination.'";
                //alert(my_url);    
                $("#id_"+s+"_bookmark_multiple_mode").attr("href",my_url);
            }
            function modificar_en_grupo_get_mode(s){
                var my_id="id_"+s+"_bookmark_multiple_mode";
                return hontza_solr_funciones_get_bookmark_mode(my_id);
            }';            
    return $result;        
}
function hontza_solr_search_get_js_variables(&$bookmark_type,&$canal_nid,&$tid,&$is_solr,&$destination,&$current_path_all_selected,&$current_path,&$select_msg){   
   $bookmark_type='default';
   $canal_nid='';
   $tid='';
   if(hontza_is_canales('bookmarks')){
       $bookmark_type='canales';
       $canal_nid=arg(1);
   }else if(hontza_is_canales_categorias('bookmarks')){
       $bookmark_type='categorias';
       $tid=arg(2);
   }else if(hontza_is_canal_usuarios('bookmarks')){
       $bookmark_type='canal-usuarios';
       //$my_uid=arg(1);
   }
   $is_solr=0;
   $destination='';
   if(hontza_solr_is_resultados_pantalla()){
       $is_solr=1;
       //$destination='&'.drupal_get_destination();
       $destination=hontza_solr_search_set_my_destination(1);
   }else{
       $destination=hontza_solr_search_set_my_destination();
   }
   $current_path_all_selected=hontza_solr_funciones_get_current_path();
   $current_path=hontza_solr_funciones_get_current_path(0);
   $select_msg=t('Please Select at least one item');
}
function hontza_solr_search_my_close_callback(){
    return t('Closed');
}
function hontza_solr_search_get_temp_selected_node_id_array(){
    if(isset($_REQUEST['url_return']) && !empty($_REQUEST['url_return'])){
        $url_info=parse_url($_REQUEST['url_return']);
        parse_str($url_info['query'],$my_array);
        if(isset($my_array['selected_node_id_array']) && !empty($my_array['selected_node_id_array']) && $my_array['selected_node_id_array']=='is_all_selected'){
            return $my_array['selected_node_id_array'];
        }
    }
    if(isset($_SESSION['selected_node_id_array']) && !empty($_SESSION['selected_node_id_array'])){        
        return $_SESSION['selected_node_id_array'];
    }
    return '';
}
function hontza_solr_search_add_url_return_selected_node_id_array($url){
    $selected_node_id_array=hontza_solr_search_get_temp_selected_node_id_array();
    $url_info=parse_url($url);
    if(empty($url_info['query'])){
        $url.='?';
    }else{
        $url.='&';
    }
    $url.='selected_node_id_array='.$selected_node_id_array;
    return $url;
}
function hontza_solr_search_in_url_base64_decode($url){
    $url_info=parse_url($url);
    if(isset($url_info['path']) && !empty($url_info['path'])){
        if(in_array($url_info['path'],array('vigilancia/bookmarks'))){
            return 1;
        }
        if(hontza_solr_search_is_vigilancia_canales_by_url($url_info['path'])){
            return 1;
        }
        if(hontza_solr_search_is_vigilancia_canales_my_categorias_by_url($url_info['path'])){
            return 1;
        }
        if(hontza_solr_search_is_canal_usuarios($url_info['path'])){
            return 1;
        }
    }
    return 0;
}
function hontza_solr_search_add_url_query_selected_node_id_array($query_in,$selected_node_id_array){
    $result='';
    $query=$query_in;
    /*parse_str($query,$my_array);
    if(isset($my_array['selected_node_id_array'])){
        return $query;
    }*/
    $query=hontza_solr_search_unset_query_selected_node_id_array('',$query);
    $result=$query;
    if(!empty($selected_node_id_array)){
        if(!empty($result)){
            $result.='&';            
        }/*else{
            $result.='?';
        }*/
        $result.='selected_node_id_array='.$selected_node_id_array;
        return $result;
    }
    return $query;
}
function hontza_solr_search_unset_query_selected_node_id_array($url,$query_in='',$field='selected_node_id_array',$url_info_query=''){
    if(empty($url)){
        $query=$query_in;
        $result=$query;
    }else{
        if(empty($url_info_query)){
            $url_info=parse_url($url);
            $query=$url_info['query'];
        }else{
            $query=$url_info_query;            
        }
        $result=$url;        
    }    
    parse_str($query,$my_array);
    if(isset($my_array[$field]) && !empty($my_array[$field])){
        $find=$field.'='.$my_array[$field];
        $result=str_replace('&'.$find,'',$result);
        $result=str_replace($find,'',$result);
        return $result;
    }
    return $result;
}
function hontza_solr_search_prepare_redirect_url($url){
    global $base_path,$language;    
    $result='';
    if(!empty($base_path) && $base_path!='/'){
        $result=str_replace($base_path,'',$url);        
    }else{
        $result=$url;
        $result=ltrim($result,'/');        
    }
    if($language->language!='en'){
        /*$find=$language->language.'/';
        $result=ltrim($result,$find);*/
        $my_array=explode('/',$result);
        if(isset($my_array[0]) && !empty($my_array[0])){
            if($my_array[0]==$language->language){
                unset($my_array[0]);
                $result=implode('/',$my_array);                
            }
        }
    }
    return $result;
}
function hontza_solr_search_add_question($url){
    $result=rtrim($url,'?');
    return $result.'?';
}
function hontza_solr_search_unset_get_not_solr_variables($get){
    $result=$get;
    $my_array=array('canal_busqueda_nid','selected_node_id_array');
    foreach($my_array as $i=>$f){    
        if(isset($result[$f])){
            unset($result[$f]);
        }
    }    
    return $result;
}
function hontza_solr_search_add_solrsort($url){
    $result='';
    $url_info=parse_url($url);
    if(empty($url_info['query'])){
        return $url;
    }else{
        $result=$url;
        parse_str($url_info['query'],$my_array);
        if(!isset($my_array['solrsort'])){
            $result.='&solrsort=ds_created desc';
        }
        return $result;
    }    
}
function hontza_solr_search_is_vigilancia_canales_by_url($url){
    $my_array=explode('/',$url);
    if(isset($my_array[0]) && !empty($my_array[0]) && $my_array[0]=='canales'){
        if(isset($my_array[1]) && !empty($my_array[1]) && is_numeric($my_array[1])){
            if(isset($my_array[2]) && !empty($my_array[2]) && $my_array[2]=='bookmarks'){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_solr_search_is_vigilancia_canales_my_categorias_by_url($url){
    $my_array=explode('/',$url);
    if(isset($my_array[0]) && !empty($my_array[0]) && $my_array[0]=='canales'){
        if(isset($my_array[1]) && !empty($my_array[1]) && $my_array[1]=='my_categorias'){
            if(isset($my_array[2]) && !empty($my_array[2]) && is_numeric($my_array[2])){
                if(isset($my_array[3]) && !empty($my_array[3]) && $my_array[3]=='bookmarks'){
                    return 1;
                }
            }            
        }
    }
    return 0;
}
function hontza_solr_search_get_categorias_grupo(){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",$my_grupo->nid));
        //Funcion del modulo taxonomy que dado un el id de una categoria devuelve todos los terminos de la misma
        $categorias=taxonomy_get_tree($id_categoria);
        return $categorias;
    }
    return $result;
}
function hontza_solr_search_get_item_categorias_tematicas($node,$is_ficha_completa,$is_js=0){
    $html=array();
    $popup_array=array();
    $categorias=array();
    //$categorias=hontza_solr_search_get_categorias_grupo();
    $tid_array=array();
    $is_see_more=0;
    $max=2;
    //intelsat-2015
    $result_tid_array=array();
    if(!empty($categorias)){
        $tid_array=hontza_solr_search_get_tid_array_by_categorias($categorias);        
    }else{
        $tid_array=hontza_solr_search_get_tid_array_by_field_item_canal_category_tid($node);        
    }
    if(!empty($tid_array)){
        $kont=0;
        foreach($tid_array as $i=>$tid){
            if(!$is_ficha_completa){
                if($kont>=$max){
                    $is_see_more=1;                                        
                }
            }
            if(hontza_solr_search_tid_in_field_item_canal_category_tid($node,$tid)){
                $term_name=taxonomy_get_term_name_by_idioma($tid);
                if(!empty($term_name)){
                    //$pro=profundidad($tid);
                    $popup_array[]=$term_name;
                    $kont++;
                    if(!$is_see_more){
                        //intelsat-2015
                        hontza_canal_rss_get_item_categoria_tematica($tid,$html,$term_name);    
                    }
                    //intelsat-2015
                    $result_tid_array[]=$tid;
                }
            }    
        }
    }
    /*
    //intelsat-2015
    if(hontza_canal_rss_is_visualizador_activado()){
        publico_get_item_canal_categorias($kont,$popup_array,$html,$node,$is_ficha_completa,$result_tid_array);
        if(count($popup_array)>$max){
            $is_see_more=1;
        }
        if(publico_vigilancia_is_view()){
            $is_see_more=0;
        }
    }*/
    $sep=red_despacho_get_popup_character($is_js);
    $popup=implode($sep,$popup_array);
    if($is_see_more && !empty($popup)){
	$node_url='node/'.$node->nid;
	if(hontza_canal_rss_is_visualizador_activado()){
	  $node_url=publico_get_node_url('node/'.$node->nid,$node);
        }
        $html[]='<li>'.l(t('See more'),$node_url,array('attributes'=>array('title'=>$popup))).'</li>';                    
    }
    return implode('',$html);
}
function hontza_solr_search_get_tid_array_by_categorias($categorias){
    $result=array();
    if(!empty($categorias)){
        foreach($categorias as $i=>$row){
            $result[]=$row->tid;
        }
    }
    return $result;
}
function hontza_solr_search_get_tid_array_by_field_item_canal_category_tid($node){
    $result=array();
    if(isset($node->field_item_canal_category_tid) && !empty($node->field_item_canal_category_tid)){
        foreach($node->field_item_canal_category_tid as $i=>$row){
            $result[]=$row['value'];
        }
    }
    return $result;
}
function hontza_solr_search_tid_in_field_item_canal_category_tid($node,$tid){
    if(isset($node->field_item_canal_category_tid) && !empty($node->field_item_canal_category_tid)){
        foreach($node->field_item_canal_category_tid as $i=>$row){
            if(isset($row['value']) && !empty($row['value']) && $row['value']==$tid){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_solr_search_get_item_source_types($node,$is_ficha_completa,$is_js=0){
    $html=array();
    $popup_array=array();
    //$source_types=hontza_define_clasificaciones_fuente();
    $source_types=array();
    $tid_array=array();
    $is_see_more=0;
    if(!empty($source_types)){
        $tid_array=array_keys($source_types);       
    }else{
        $tid_array=hontza_solr_search_get_tid_array_by_field_item_source_tid($node); 
    }
    if(!empty($tid_array)){
        $max=2;
        $kont=0;
        foreach($tid_array as $i=>$tid){
            if(!$is_ficha_completa){
                if($kont>=$max){
                    $is_see_more=1;                                        
                }
            }
            if(hontza_solr_search_tid_in_field_item_source_tid($node,$tid)){
                $term_name=taxonomy_get_term_name_by_idioma($tid);
                if(!empty($term_name)){
                    //$pro=profundidad($tid);
                    $popup_array[]=$term_name;
                    $kont++;
                    if(!$is_see_more){
                        $url='taxonomy/term/'.$tid;
                        $url=hontza_solr_search_get_source_type_filtrado_solr_url($tid,$url,0,$query);
                        if(red_movil_is_activado()){
                            $html[]='<li><b>'.$term_name.'</b></li>';
                        }else{
                            if(empty($query)){
                                $html[]='<li>'.l($term_name,$url).'</li>';
                            }else{
                                $html[]='<li>'.l($term_name,$url,array('query'=>$query)).'</li>';
                            } 
                        }    
                    }                                       
                }
            }    
        }
    }
    $sep=red_despacho_get_popup_character($is_js);
    $popup=implode($sep,$popup_array);
    if($is_see_more && !empty($popup)){
        //$popup='';
        $html[]='<li>'.l(t('See more'),'node/'.$node->nid,array('attributes'=>array('title'=>$popup))).'</li>';                    
    }
    return implode('',$html);
}
function hontza_solr_search_get_tid_array_by_field_item_source_tid($node){
    $result=array();
    if(isset($node->field_item_source_tid) && !empty($node->field_item_source_tid)){
        foreach($node->field_item_source_tid as $i=>$row){
            $result[]=$row['value'];
        }
    }
    return $result;
}
function hontza_solr_search_tid_in_field_item_source_tid($node,$tid){
    if(isset($node->field_item_source_tid) && !empty($node->field_item_source_tid)){
        foreach($node->field_item_source_tid as $i=>$row){
            if(isset($row['value']) && !empty($row['value']) && $row['value']==$tid){
                return 1;
            }
        }
    }
    return 0;
}
function hontza_solr_search_on_item_checkbox_click_js($is_solr){
    $result='var is_solr_temp='.$is_solr.';
            $(".node_bookmark_txek_class").click(function() {
                var is_txeked=$(this).attr("checked");
                if(!is_txeked){    
                    if(is_solr_temp){
                        $("#id_label_selected_news").css("display","none");
                    }
                    $("#select_bookmark_all_txek").attr("checked",false);
                }
            });';
    return $result;
}
//intelsat-2016
//function hontza_solr_search_add_source_type_form_field(&$form,$is_import_rss=0,$is_node_form=0){
function hontza_solr_search_add_source_type_form_field(&$form,$is_import_rss=0,$is_node_form=0,$is_required=1,$node='',$form_state=''){
  //intelsat-2015
  //$taxo=hontza_define_clasificaciones_fuente();
  $taxo=red_despacho_get_source_type_options();  
  //$taxonomia_title=t('Assign one or more classifications');
  //$error_message= t('There are no classifications');
  //if($is_import_rss){
        //$taxonomia_title=t('Assign one or more Source Type');
        //$taxonomia_title=t('Source Type');
        $taxonomia_title='';
        $error_message= t('There are no Source Types');
  //}  
  //gemini
  //
  //simulatzeko
  //$taxo=array();
  if(!$is_node_form){      
    if(empty($taxo)){
      unset($form['buttons']['next']);  
          form_set_error('asignar_clasif',$error_message);
    }
  }
  //if(!my_hay_categorias()){
  //unset($form['buttons']['next']);
  //form_set_error('categorias', t('There are no categories. You have to create at least one category before continuing.'));
  //}
  //
  //intelsat-2016
  $title=t('Source Types');
  $form['taxonomia_fs']=array(
      '#type'=>'fieldset',
      '#title'=>'<span class="fieldset-title-required" title="This field is required.">*</span>'.$title,
      '#attributes'=>array('id'=>'id_taxonomia_fs'),
  );
  //intelsat-2016
  if(!$is_required){
      $form['taxonomia_fs']['#title']=$title;
  }
  $form['taxonomia_fs']['taxonomia'] = array(
    '#title' => $taxonomia_title,
    '#type' => 'checkboxes',
    '#multiple' => TRUE,
    //'#required' => TRUE,
    '#options' => $taxo,
  );
  //intelsat-2015
  red_despacho_set_tree_source_type_form_field($form,$taxo,$node,$form_state);
}
function hontza_solr_search_simular_actualizar_items_callback(){
    return 'Funcion desactivada';
    /*$feed_nid=216688;
    $node=node_load($feed_nid);
    //echo print_r($node,1);
    $importer_id = feeds_get_importer_id($node->type);
    //feeds_batch_set(t('Importing'), 'import',$importer_id,$feed_nid);
    feeds_batch('import',$importer_id, $feed_nid,$context);*/
    //hontza_solr_actualizar_items();
    //hontza_solr_search_actualizar();
    return date('Y-m-d H:i:s');    
}
function hontza_solr_search_actualizar_item_source_tid_by_canal_de_yql($canal,$node){
    if(hontza_solr_is_canal_source_type_updated($canal)){
        hontza_solr_save_item($node,$canal,1,0);
    }else{
        if(isset($canal->field_canal_source_type) && !empty($canal->field_canal_source_type)){
            $delta=0;
            foreach($canal->field_canal_source_type as $i=>$row){
                if(isset($row['value']) && !empty($row['value'])){
                    $tid=$row['value'];
                    if($delta>0){
                        $res=db_query('INSERT INTO {content_field_item_source_tid}(field_item_source_tid_value,nid,vid,delta) VALUES(%d,%d,%d,%d)',$tid,$node->nid,$node->vid,$delta);                                        
                    }else{
                        $res=db_query('UPDATE {content_field_item_source_tid} SET field_item_source_tid_value=%d WHERE nid=%d AND vid=%d AND delta=0',$tid,$node->nid,$node->vid);
                    }    
                    $delta++;
                }    
            }                                
        }
    }
}
function hontza_solr_search_is_simple_import_rss(){
    if(hontza_solr_is_solr_activado()){
        return 0;
    }
    //intelsat-2015
    if(hontza_canal_rss_is_visualizador_activado()){
        return 0;
    }
    return 1;
}
function hontza_solr_search_taxonomia_validate(&$form,&$form_state){
    if(hontza_solr_search_is_empty_form_field_taxonomia($form_state)){
        if(isset($form['taxonomia_fs']['taxonomia'])){
            $field_title=t('Source Types');    
            form_error($form['taxonomia_fs']['taxonomia'],t('!name field is required.', array('!name' => $field_title)));
        }
    }    
}
function hontza_solr_search_is_empty_form_field_taxonomia($form_state){
    if(isset($form_state['values']['taxonomia']) && !empty($form_state['values']['taxonomia'])){
        foreach($form_state['values']['taxonomia'] as $tid=>$value){
            if(!empty($value)){
                return 0;
            }
        }        
    }
    return 1;
}
//intelsat-2015
//function hontza_solr_search_fivestar_botonera($node,$is_enabled=0,$node_c_d_w=''){
//intelsat-2016
//function hontza_solr_search_fivestar_botonera($node,$is_enabled=0,$node_c_d_w='',$with_label=1){
function hontza_solr_search_fivestar_botonera($node,$is_enabled=0,$node_c_d_w='',$with_label=1,$is_estrategia=0){    
    $html=array();
    if($is_enabled){
        if(!hontza_is_fivestar_enabled($node)){
            return '';
        }
        if(hontza_solr_search_is_usuario_lector()){
           return ''; 
        }
    }
    //if(empty($node_c_d_w)){
        $html[]='<div class="item-fivestar" style="clear:none;">';
    /*}else{
        $html[]='<div class="item-fivestar">';
    }*/
    //$style='float:right;';
    //if(!empty($node_c_d_w)){
    //intelsat-2016
    $float='right';
    $margin_left='';
    if($is_estrategia){
        $float='left';
        $margin_left='margin-left:-5px;';    
    }    
        $style='float:'.$float.';padding-top:2px;'.$margin_left;
    //}
    $html[]='<div style="'.$style.'">';
    $content=$node->content['fivestar_widget']['#value'];
    if($node->type=='idea'){
        if($node->es_mi_idea){
            $content=$node->my_stars;
        }                            
    }else if($node->type=='oportunidad'){
        if($node->es_mi_oportunidad){
            $content=$node->my_stars;
        } 
    }else{
        //intelsat-2016
        //if(hontza_solr_search_is_usuario_lector()){
        if(hontza_solr_search_is_usuario_lector() || $is_estrategia){
            //return '';
            $content=traducir_average(fivestar_static('node', $node->nid, NULL, $node->type));
            //intelsat-2016
            if($is_estrategia){
                $content=str_replace(t('Rating').':','',$content);
            }
        }    
    }
    $content=hontza_solr_search_traducir_average($content);
    //intelsat-2015
    $style='font-size:10px;';
    if(!$with_label){
        $style.='display:none';
    }
    $content=str_replace('<label for="edit-vote','<label  style="'.$style.'" for="edit-vote>',$content);
    $content=str_replace('<div class="description">','<div class="description" style="display:none;">',$content);
    $html[]=$content;		    
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);
}
function hontza_solr_search_get_source_type_filtrado_solr_url($tid,$url,$is_categoria_tematica,&$query){
    $query='';        
    if(hontza_solr_is_solr_activado()){
        $result='my_solr/my_search';
        $my_grupo=og_get_group_context();
        $my_array=array();
        if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $i=count($my_array);
            $my_array[]='f['.$i.']=im_og_gid:'.$my_grupo->nid;
            $i=count($my_array);
            if(!empty($is_categoria_tematica) && $is_categoria_tematica==1){
                if(!empty($tid)){
                    $i=count($my_array);
                    $my_array[]='f['.$i.']=itm_field_item_canal_category_ti:'.$tid;
                }
            }else{
                if(empty($is_categoria_tematica)){
                    if(!empty($tid)){
                        $i=count($my_array);
                        $my_array[]='f['.$i.']=itm_field_item_source_tid:'.$tid;
                    }
                }else if($is_categoria_tematica==2){
                    if(!empty($tid)){
                        $i=count($my_array);
                        $my_array[]='f['.$i.']=im_taxonomy_vid_3:'.$tid;
                    }
                }
                $my_selected_categoria=red_funciones_get_filtro_por_categoria();
                if(!empty($my_selected_categoria)){
                    $i=count($my_array);
                    $my_array[]='f['.$i.']=itm_field_item_canal_category_ti:'.$my_selected_categoria;
                }
            }    
            $selected_canal_nid=red_funciones_get_filtro_por_canal();
            if(!empty($selected_canal_nid)){
                $i=count($my_array);
                $my_array[]='f['.$i.']=im_field_item_canal_reference:'.$selected_canal_nid;
            }
            $query=implode('&',$my_array);
            if(!empty($query)){
                $query.='&solrsort=ds_created desc';
            }
            return $result;
        }
    }
    return $url;
}
function hontza_solr_search_get_categorias_tematicas_filtrado_solr_url($tid,$url,&$query){
    $is_categoria_tematica=1;
    return hontza_solr_search_get_source_type_filtrado_solr_url($tid,$url,$is_categoria_tematica,$query);
}
function hontza_solr_search_get_resumen_etiqueta($node_taxonomy,$is_ficha_completa,$node){
    //intelsat-2015
    global $base_url;
    $html=array();
    $popup_array=array();
    $is_see_more=0;
    if(!empty($node_taxonomy)){
        $max=4;
        $kont=0;
        foreach($node_taxonomy as $my_tid=>$term){
            if(hontza_in_etiquetas_vocabulary($term)){
                $tid=$term->tid;
                if(!$is_ficha_completa){
                    if($kont>=$max){
                        $is_see_more=1;                                        
                    }
                }
                    $popup_array[]=$term->name;
                    $kont++;
                    if(!$is_see_more){
                        $url='taxonomy/term/'.$tid;
                        $url=hontza_solr_search_get_tag_filtrado_solr_url($tid,$url,$query);
                        //intelsat-2015
                        $url=$base_url.'/'.$url;                        
                        if(red_movil_is_activado()){
                            $link='<b>'.$term->name.'</b>';
                        }else{    
                            if(empty($query)){
                                $link=l($term->name,$url);
                            }else{                            
                                $link=l($term->name,$url,array('query'=>$query));
                            }
                        }    
                        if($is_ficha_completa){
                            $html[]=$link;
                        }else{
                            $html[]='<li>'.$link.'</li>';
                        }    
                    }
            }        
        }
    }
    $popup=implode("\n",$popup_array);
    if($is_see_more && !empty($popup)){
        $html[]='<li>'.l(t('See more'),'node/'.$node->nid,array('attributes'=>array('title'=>$popup))).'</li>';                    
    }
    if($is_ficha_completa){
        $content=implode(' ',$html);    
        return $content;
    }else{
        $content=implode('',$html);    
    }
    //
    $html=array();
    $html[]='<div style="margin-top:0px;float:left;" class="terms terms-inline">';
    $html[]='<ul>';
    if(!empty($content)){
        $html[]=$content;
    }
    $html[]='</ul>';
    $html[]='</div>';
    return implode('',$html);
}
function hontza_solr_search_get_tag_filtrado_solr_url($tid,$url,&$query){
    $is_categoria_tematica=2;
    return hontza_solr_search_get_source_type_filtrado_solr_url($tid,$url,$is_categoria_tematica,$query);
}
function hontza_solr_search_traducir_average($v){
    global $language;
    $result=$v;
    $value=t('Average');
    $result=str_replace('Average',$value,$result);    
    if($language->language=='es'){       
        $result=str_replace('Puntuación media','Media',$result);
    }else{
        $max=strlen('Batezbestekoa ');
        $len=strlen($value);
        if($len>=$max){
            $result=str_replace($value.':','',$result);
        }            
    }    
    return $result;
}
function hontza_solr_search_define_is_canal_correo_options(){
    $result=array();
    $label=hontza_solr_search_define_mail_channel_label();
    $result[0]=$label;
    $result[1]=$label;
    return $result;
}
function hontza_solr_search_define_mail_channel_label(){
    return 'Mail Channel';
}
function hontza_solr_search_canal_de_yql_form_alter(&$form,&$form_state, $form_id){
    if(!is_super_admin()){
        if(isset($form['field_is_canal_correo']) && !empty($form['field_is_canal_correo'])){
            unset($form['field_is_canal_correo']);
        }
    }
}
function hontza_solr_search_is_canal_correo($source='',$canal_in='',$url=''){
    return hontza_canal_rss_is_canal_correo($source,$canal_in,$url);
}
function hontza_solr_search_create_guid($item) {
   $result=(array) $item;
   $result['title']=(string) $result['title'];
   $result['description']=(string) $result['description'];
   $result=md5($result['title'].$result['description']);
   return $result;
}
function hontza_solr_search_set_feed_node_item_guid($source,$node,$item){
       $config=$source->getConfig();  
       $url=$config['FeedsHTTPFetcher']['source'];
       $node->feeds_node_item->guid=$item['guid'];        
}
function hontza_solr_search_actualizar(){
    hontza_solr_search_actualizar_canales_correo();
}
function hontza_solr_search_actualizar_canales_correo(){
    if(db_column_exists('content_type_canal_de_yql','field_is_canal_correo_value')){
        $canal_correo_array=hontza_solr_search_get_canal_correo_array();
        if(!empty($canal_correo_array)){
           foreach($canal_correo_array as $i=>$row){
               $canal=node_load($row->nid);
               if(isset($canal->nid) && !empty($canal->nid)){
                   hontza_solr_search_actualizar_canal($canal);
               }
           }
        }
    }
}
function hontza_solr_search_get_canal_correo_array(){
    $result=array();
    $sql='SELECT * FROM {content_type_canal_de_yql} WHERE field_is_canal_correo_value=1';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function hontza_solr_search_actualizar_canal($canal,$nid=''){
    if(isset($canal->nid) && !empty($canal->nid)){
        $node=$canal;
    }else{
        $node=node_load($nid);    
    }
    $feed_nid=$node->nid;
    $importer_id = feeds_get_importer_id($node->type);
    feeds_batch('import',$importer_id, $feed_nid,$context);
}
function hontza_solr_search_is_canal_correo_by_rss($rss){
    $pos=strpos($rss,'hound2.hontza.es/hound/index.php/mailPages/getRss/id');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_solr_search_existingItemId($source,$value){
   $my_grupo=hontza_get_grupo_by_feed_nid($source);
   if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
       $nid = db_result(db_query("SELECT feeds_node_item.nid FROM {feeds_node_item} feeds_node_item LEFT JOIN {og_ancestry} og_ancestry ON feeds_node_item.feed_nid=og_ancestry.nid WHERE og_ancestry.group_nid = %d AND id = '%s' AND guid = '%s'",$my_grupo->nid, $source->id, $value));       
       return $nid;   
   }
   return 0;
}
function hontza_solr_search_get_canal_usuarios_bookmarks_sql($uid,$where_in,$order_by){
    global $user;
    $where=$where_in;
    $where[]='NOT hontza_bookmark.nid IS NULL';
    $where[]='hontza_bookmark.uid='.$user->uid;
    $sql='SELECT node.nid AS nid, node.created AS node_created 
    FROM {node} node LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
    LEFT JOIN {hontza_bookmark} hontza_bookmark ON node.nid=hontza_bookmark.nid 
    WHERE '.implode(' AND ',$where).'
    GROUP BY nid '.$order_by;
    return $sql;
}
function hontza_solr_search_is_canal_usuarios_bookmarks_by_destination(&$my_destination,&$pos,$url_in=''){
    if(empty($url_in)){
        $my_destination=$_REQUEST['destination'];
        if(empty($my_destination)){
            $my_destination=$_REQUEST['my_destination'];
        }
        $my_destination=base64_decode($my_destination);
    }else{
        $my_destination=$url_in;
    }
    $pos=strpos($my_destination,'canal-usuarios');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_solr_search_define_url_return($url_return){
    if(hontza_solr_search_is_canal_usuarios_bookmarks_by_destination($my_destination,$pos)){
        $s=substr($my_destination,$pos);
        $find='/bookmarks';
        $pos_end=strpos($s,$find);
        if($pos_end===FALSE){
            return $url_return;
        }else{
            $result=substr($s,0,$pos_end+strlen($find));
            return $result;
        }
    }
    return $url_return;
}
function hontza_solr_search_is_canal_usuarios($url){
    if(hontza_solr_search_is_canal_usuarios_bookmarks_by_destination($my_destination,$pos,$url)){
        return 1;
    }
    return 0;
}
function hontza_solr_search_canal_usuarios_bookmarks_callback(){
    drupal_set_title(t('Bookmarked'));
    hontza_solr_funciones_unset_my_results_solr();
    $html=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $html[]=hontza_define_canal_usuarios_menu();
        $html[]=hontza_solr_search_canal_usuarios_bookmarks_html();
    }
    return implode('',$html);
}
function hontza_solr_search_canal_usuarios_bookmarks_html($bookmark_type=''){
    global $user;
    hontza_solr_funciones_delete_bookmark_node_temporal();
    $my_limit=20;
    $rows=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $uid=arg(1);
        $sql=hontza_get_canal_usuarios_sql($uid);
        //        
        $res=db_query($sql);
        while($row=db_fetch_object($res)){
            $node=node_load($row->nid);
            if(isset($node->nid) && !empty($node->nid)){  
                $rows[]=$node;
                $num_rows=TRUE;
            }
        }
    }
    $_SESSION['bookmarks_rows']=$rows;
    
    if(hontza_solr_funciones_is_select_bookmark_all()){
        $rows=array_slice($rows,0,$my_limit);      
    }
        
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //
        if(!empty($rows)){
            foreach($rows as $i=>$node){
                $output.=node_view($node,TRUE);
            }
        }    
    //
    if ($num_rows) {
        $bookmark_form_ini=hontza_solr_funciones_get_bookmark_ini(0);
        $output=$bookmark_form_ini.$output;
        //$output.='</form>';
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function hontza_solr_search_get_noticia_categorias_tematicas($node,$is_ficha_completa=0,$is_js=0){
    global $base_url;
    $html=array();
    $popup_array=array();
    //$categorias=hontza_solr_search_get_categorias_grupo();
    $categorias=array();
    $tid_array=array();
    $is_see_more=0;
    if(!empty($categorias)){
        //$tid_array=hontza_solr_search_get_tid_array_by_categorias($categorias);        
    }else{
        $tid_array=hontza_solr_search_get_tid_array_by_node_taxonomy($node);        
    }
    if(!empty($tid_array)){
        $max=2;
        $kont=0;
        foreach($tid_array as $i=>$tid){
            if(!$is_ficha_completa){
                if($kont>=$max){
                    $is_see_more=1;                                        
                }
            }
            if(hontza_solr_search_tid_in_node_taxonomy($node,$tid)){
                $term_name=taxonomy_get_term_name_by_idioma($tid);
                if(!empty($term_name)){
                    //$pro=profundidad($tid);
                    $popup_array[]=$term_name;
                    $kont++;
                    if(!$is_see_more){
                        $url='canales/my_categorias/'.$tid.'/ultimas';
                        if(hontza_canal_rss_is_visualizador_activado()){
                            $grupo=visualizador_create_grupo_base_path();
                            $url=$base_url.'/'.$grupo.'/publico/canales/categorias/'.$tid.'/ultimas';
                        }
                        $url=hontza_solr_search_get_categorias_tematicas_filtrado_solr_url($tid,$url,$query);
                        if(red_movil_is_activado()){
                            $html[]='<li><b>'.$term_name.'</b></li>';
                        }else{
                            if(empty($query)){
                                $html[]='<li>'.l($term_name,$url).'</li>';
                            }else{
                                $html[]='<li>'.l($term_name,$url,array('query'=>$query)).'</li>';
                            }
                        }    
                    }                                       
                }
            }    
        }
    }
    $sep=red_despacho_get_popup_character($is_js);
    $popup=implode($sep,$popup_array);
    if($is_see_more && !empty($popup)){
        $html[]='<li>'.l(t('See more'),'node/'.$node->nid,array('attributes'=>array('title'=>$popup))).'</li>';                    
    }
    return implode('',$html);
}
function hontza_solr_search_get_tid_array_by_node_taxonomy($node){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",$my_grupo->nid));
        if(!empty($id_categoria)){
            if(isset($node->taxonomy) && !empty($node->taxonomy)){
                foreach($node->taxonomy as $tid=>$term){
                    if($term->vid==$id_categoria){
                        $result[]=$term->tid;
                    }
                }
            }    
        }
    }
    return $result;
}
function hontza_solr_search_tid_in_node_taxonomy($node,$tid){
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",$my_grupo->nid));
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            foreach($node->taxonomy as $my_tid=>$term){
                if($term->vid==$id_categoria){
                    if($term->tid==$tid){
                        return 1;
                    }
                }
            }
        }
    }
    return 0;
}
function hontza_solr_search_get_noticia_categorias_tematicas_html($node,$is_ficha_completa=0){
    $html=array();
    $ul_id=red_despacho_get_item_canal_category_tid_ul_id($node->nid);
    $html[]='<ul id="'.$ul_id.'">';
    $content=hontza_solr_search_get_noticia_categorias_tematicas($node,$is_ficha_completa);
    if(!empty($content)){
        $html[]=$content;
    }
    $html[]='</ul>';
    return implode('',$html);
}
function hontza_solr_search_modificar_noticia_usuario_categorias(&$node,$tid_array){
    $taxonomy=hontza_solr_search_get_taxonomy_sin_categorias_tematicas($node);
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            $term=taxonomy_get_term($tid);
            if(isset($term->tid) && !empty($term->tid)){
                $taxonomy[$term->tid]=$term;
            }    
        }
    }
    $node->taxonomy=$taxonomy;
    node_save($node);
}
function hontza_solr_search_get_taxonomy_sin_categorias_tematicas($node){
    $result=array();
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $id_categoria = db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid=%s",$my_grupo->nid));
        $taxonomy=array();
        if(isset($node->taxonomy) && !empty($node->taxonomy)){
            foreach($node->taxonomy as $tid=>$term){
                if($term->vid!=$id_categoria){
                    $result[]=$term;
                }
            }
        }
    }
    return $result;
}
function hontza_solr_search_add_noticia_usuario_categorias(&$node,$tid_array){
    $taxonomy=$node->taxonomy;
    if(!empty($tid_array)){
        foreach($tid_array as $i=>$tid){
            if(!hontza_solr_search_tid_in_node_taxonomy($node,$tid)){
                $term=taxonomy_get_term($tid);
                if(isset($term->tid) && !empty($term->tid)){
                    $taxonomy[$term->tid]=$term;
                }
            }
        }
    }
    $node->taxonomy=$taxonomy;
    node_save($node);
}
function hontza_solr_search_unset_all_variables($url){
    $result=str_replace('&is_select_bookmark_all=1','',$url);
    $result=str_replace('&is_my_all_results=1','',$result);
    $result=hontza_solr_search_unset_request_variable($result,'is_select_bookmark_all');
    $result=hontza_solr_search_unset_request_variable($result,'is_my_all_results');
    return $result;
}
function hontza_solr_search_unset_request_variable($url,$s){
    $pos=strpos($url,'?'.$s);
    if($pos===FALSE){
        return $url;
    }
    $pos_end=strpos($url,$s.'=1&');
    if($pos_end===FALSE){
        $result=str_replace('?'.$s.'=1','',$url);
        return $result;
    }
    $result=str_replace($s.'=1&','',$url);
    return $result;
}
function hontza_solr_search_is_bookmark_activado_admin(){
    if(is_super_admin()){
        //intelsat-2016
        if(defined('_IS_BOOKMARK_ADMIN') && _IS_BOOKMARK_ADMIN==1){
            return 1;
        }
    }    
    return 0;
}
function hontza_solr_search_add_url_query_my_var($query_in,$mode=''){
    if($mode=='delete'){
        $result='';
        $query=$query_in;
        $query=hontza_solr_search_unset_query_my_var('',$query);
        $result=$query;
            if(!empty($result)){
                $result.='&';            
            }
            $result.='my_var='.time();
            return $result;        
    }
    return $query_in;
}
function hontza_solr_search_unset_query_my_var($url,$query_in=''){
    return hontza_solr_search_unset_query_selected_node_id_array($url,$query_in,'my_var');
}
function hontza_solr_search_is_solr_activado_admin(){
    return 0;
}
function hontza_solr_search_set_og_canales_busqueda_my_block($vars_in){
    $vars=$vars_in;
    if(hontza_solr_is_solr_activado() || hontza_is_sareko_id_red(1,1)){
        //$find='<span class="field-content">';
        $find='<div class="views-field-nothing">';
        $result=explode($find,$vars['rows']);
        if(!empty($result)){
            foreach($result as $i=>$value){
                if($i>0){
                    //intelsat-2015
                    //if(!hontza_canal_rss_is_publico_exportar_rss_enviar_mail($vars['view']->result[$i-1]->nid)){
                    $link_rss=hontza_solr_search_get_left_link_rss($vars['view']->result[$i-1],0,1);
                    /*}else{
                        $link_rss=hontza_canal_rss_get_canal_rss_link('',$vars['view']->result[$i-1]->nid);
                    }*/
                    $result[$i]=$link_rss.$find.$value;
                }
            }
            //$vars['rows']=implode($find,$result);
            $vars['rows']=implode('',$result);
        }
    }    
    return $vars['rows'];
}
//intelsat-2015
//function hontza_solr_search_get_left_link_rss($row){
function hontza_solr_search_get_left_link_rss($row,$is_drupal_goto=0,$is_block=0){    
    $result='';
    //$is_popup=0;
    $icono='solr_results_rss';
    //intelsat-2016
    //$destination=red_copiar_get_popup_destination('?my_current_');
    //$destination='';
    if(isset($row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value) && !empty($row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value)){
        $url=$row->node_data_field_canal_busqueda_busqueda_field_canal_busqueda_busqueda_value;
        $url_info=parse_url($url);
        if(isset($url_info['query']) && !empty($url_info['query'])){
            if(hontza_solr_is_busqueda_solr($url)){
                parse_str($url_info['query'],$query_array);
                if(!isset($query_array['is_my_all_results'])){
                    $url_info['query'].='&is_my_all_results=1';
                }                
                //intelsat-2015
                if($is_drupal_goto){
                    $url_info['query'].='&red_exportar_rss_canal_nid='.$row->nid;
                    return $url_info;
                }
                if(hontza_canal_rss_is_publico_exportar_rss_enviar_mail($row->nid)){
                    $icono='publico_solr_results_rss';
                    $url='busqueda_rss/exportar_busqueda_rss/'.$row->nid;
                    //$url='busqueda_rss/exportar_busqueda_rss/'.$row->nid.$destination;
                    $url_info=parse_url($url);                    
                }        
            }else{
                if($url_info['path']=='busqueda'){
                    $url_info['path']='busqueda_rss';
                    if(!hontza_solr_is_solr_activado()){
                        //$is_popup=1;
                        //$url_info['query'].='&is_popup=1';
                        $url_info['path']='busqueda_rss/exportar_busqueda_rss/'.$row->nid;
                        //$url_info['path']='busqueda_rss/exportar_busqueda_rss/'.$row->nid.$destination;
                        $icono='publico_solr_results_rss';
                    }
                }        
            }
        }
        /*if($is_popup){
            hontza_canal_rss_add_red_exportar_rss_enviar_mail_js($row->nid);
            $result=l(my_get_icono_action($icono, t('RSS'),'left_rss'),$url_info['path'],array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('class'=>'jqm-trigger-red_exportar_rss_enviar_mail_'.$row->nid)));
            $result.='<div id="exred_exportar_rss_enviar_mail_'.$canal_nid.'" class="jqmWindow jqmID2000"></div>';
        }else{*/
        if(hontza_canal_rss_is_publico_exportar_rss_enviar_mail_desactivado($row->nid)){
            $icono='export_rss_off';
            //$title=t('Export RSS OFF');
            //$title=t('Export search');
            $title=t('Export OFF');
            $result=my_get_icono_action($icono,$title,'left_rss');
        }else{
            //$title=t('RSS');
            $title=t('Export search');
            $result=l(my_get_icono_action($icono,$title,'left_rss'),$url_info['path'],array('html'=>TRUE,'query'=>$url_info['query'],'attributes'=>array('target'=>'_blank')));
        }
        //}                    
    }
    //intelsat-2015
    if($is_drupal_goto){
        return '';
    }
    return $result;
}
function hontza_solr_search_get_nombre_busqueda_repetido_mensaje(){
    return t('The Search name already exists');
}
function hontza_solr_search_get_solr_pantalla_title($title){
    if(hontza_solr_search_is_busqueda_guardada_pantalla()){
        $canal_busqueda_nid=$_REQUEST['canal_busqueda_nid'];
        $canal_busqueda_node=node_load($canal_busqueda_nid);
        if(isset($canal_busqueda_node->nid) && !empty($canal_busqueda_node->nid)){
            $result=$canal_busqueda_node->title;
            return hontza_solr_search_get_busqueda_title($result);
        }    
    }
    return $title;
}
function hontza_solr_search_is_busqueda_guardada_pantalla(){
    if(hontza_solr_is_resultados_pantalla()){
        if(isset($_REQUEST['canal_busqueda_nid']) && !empty($_REQUEST['canal_busqueda_nid'])){
            return 1;
        }
    }
    return 0;
}
function hontza_solr_search_unset_facetapi_not_solr_variables(&$result){
    if(!empty($result)){
       foreach($result as $key=>$row){
           $result[$key]['#query']=hontza_solr_search_unset_get_not_solr_variables($row['#query']);
       } 
    }    
}
function hontza_solr_search_set_facetapi_by_orden_categorias($my_array,$my_tree){
    $result=array();    
    if(!empty($my_tree) && !empty($my_array)){
        foreach($my_tree as $i=>$row){
            $profundidad=profundidad($row->tid);
            if(isset($my_array[$row->tid])){
                $result[$row->tid]=$my_array[$row->tid];               
            }else{
                $result[$row->tid]=hontza_solr_search_create_categoria_tematica_facetapi_row($row);
            }
            //$result[$row->tid]['#html']=true;
            $result[$row->tid]['#value']=hontza_solr_search_set_profundidad($profundidad,$result[$row->tid]['#value']);
        }
        return $result;
    }
    return $my_array;
}
function hontza_solr_search_get_busqueda_title($title){
    return t('#Search').': '.$title;
}
function hontza_solr_search_create_categoria_tematica_facetapi_row($term){
    $url=hontza_solr_get_busqueda();
    $url_info=parse_url($url);
    if(!hontza_solr_is_busqueda_solr($url_info['path'])){
        $url_info['path']='my_solr/my_search';
    }
    $query=hontza_solr_search_add_categoria_tematica_query_array($url_info['query'],$term->tid);
    $result=array(
    '#value'=> $term->name,
    '#path' => $url_info['path'],
    '#html' => '',
    '#indexed_value' => $term->tid,
    '#count' => 0,
    '#active' => 0,
    '#item_parents' =>array(),
    '#item_children' =>array(),
    '#query'=>$query);        
return $result;
}
function hontza_solr_search_set_profundidad($profundidad,$value){
    $result='-profundidad:'.$profundidad.'-'.$value;
    return $result;
}
function hontza_solr_search_is_profundidad_categoria_tematica($s){
    $pos=strpos($s,'-profundidad:');
    if($pos===FALSE){
        return 0;
    }
    return 1;
}
function hontza_solr_search_get_text_by_profundidad_categoria_tematica($s,$link_type,&$result_profundidad){
    $style='';
    /*if($link_type=='active'){
        $style=' style="float:left;"';
    }*/
    $result_profundidad=0;
    $pos=strpos($s,':');
    if($pos===FALSE){
        return $s;
    }else{
        $profundidad_string=substr($s,$pos+1);
        $pos2=strpos($profundidad_string,'-');
        if($pos2===FALSE){
            return $s;
        }else{
            $profundidad=substr($profundidad_string,0,$pos2);
            $profundidad=(int) $profundidad;
            $result=substr($profundidad_string,$pos2+1);
            if($profundidad>0){
                $result_profundidad=$profundidad;
                $profundidad=15*$profundidad;
                $padding='padding:5px 0px 5px '.$profundidad.'px';
                $result='<div style="'.$padding.'">'.$result.'</div>';
            }else{
                $result='<div'.$style.'>'.$result.'</div>';
            }
            return $result;
        }
    }
}
function hontza_solr_search_add_categoria_tematica_query_array($query,$tid){
    parse_str($query,$query_array);
    if(!isset($query_array['f'])){
        $query_array['f']=array();
    }
    $field='itm_field_item_canal_category_ti';
    /*if(hontza_solr_search_in_f_query_array($field,$query_array,$my_index)){
        $s=$query_array['f'][$my_index];
        $my_array=explode(':',$s);
        if(count($my_array)>=2){
            $my_array[1]=$tid;
        }
        $query_array['f'][$my_index]=implode(':',$my_array);
    }else{*/
        $query_array['f'][]=$field.':'.$tid;
    //}
    return $query_array;
}
function hontza_solr_search_in_f_query_array($field,$query_array,&$index){
    if(!empty($query_array)){
        foreach($query_array['f'] as $i=>$value){
            $pos=strpos($value,$field);
            if($pos===FALSE){
                continue;
            }else{
                $index=$i;
                return 1;
            }
        }
    }
    return 0;
}
function hontza_solr_search_set_og_canales_rss_link($vars_in){
    $vars=$vars_in;
    $find='<div class="views-summary views-summary-unformatted">';
        $result=explode($find,$vars['rows']);
        if(!empty($result)){
            foreach($result as $i=>$value){
                if($i>0){
                    $link_rss=hontza_solr_search_get_left_link_rss_by_href($value);
                    $result[$i]=$link_rss.$value;
                }
            }
            $vars['rows']=implode($find,$result);            
        }
    return $vars['rows'];
}
function hontza_solr_search_get_left_link_rss_by_href($value){
    $result='';
    $find='href="';
    $pos=strpos($value,$find);
    if($pos===FALSE){
        return '';
    }else{
        $s=substr($value,$pos+strlen($find));
        $pos2=strpos($s,'">');
        if($pos2===FALSE){
            return '';
        }else{
            $url=substr($s,0,$pos2);
            //intelsat-2015
            $orig_url=$url;
            $result=hontza_canal_rss_get_canal_rss_link($url);
            $result.=hontza_canal_rss_add_user_img_canal($url,$orig_url);
        }
    }
    return $result;
}
function hontza_solr_search_canales_rss_callback(){
    return hontza_canal_rss_canales_rss();    
}
function hontza_solr_search_lector_repase_access(){
    $result=1;
    if(hontza_solr_search_is_usuario_lector()){    
        if(is_node_add() || hontza_is_node_edit() || hontza_solr_search_is_node_delete() || hontza_solr_search_is_node_tag() || hontza_is_comment_reply()){
            $result=0;
        }
        if(hontza_solr_search_is_node_enlazar_debate() || hontza_solr_search_is_node_enlazar_trabajo() || hontza_solr_search_is_node_enlazar_idea()){
            $result=0;
        }
        if(!$result){
            drupal_access_denied();
            exit();
        }
    }    
}
function hontza_solr_search_is_node_delete(){
    return hontza_is_node_edit('delete');
}
function hontza_solr_search_is_usuario_lector(){
    global $user;
    if(isset($user->roles) && !empty($user->roles)){
       $values=array_values($user->roles);
       if(count($values)==2){
          //intelsat-2015
          if(in_array('Lector',$values)){
              return 1;
          } 
       }
    }
    //intelsat-2015
    if(hontza_canal_rss_is_publico_usuario_lector()){
        return 1;
    }
    if(hontza_canal_rss_is_usuario_basico()){
        return 1;
    }
    return 0;
}
function hontza_solr_search_is_node_tag(){
    return hontza_is_node_edit('tag');
}
function hontza_solr_search_is_node_enlazar_debate(){
    return hontza_is_node_edit('enlazar_debate');
}
function hontza_solr_search_is_node_enlazar_trabajo(){
    return hontza_is_node_edit('enlazar_wiki');
}
function hontza_solr_search_is_node_enlazar_idea(){
    return hontza_is_node_edit('enlazar_idea');
}
function hontza_solr_search_is_usuario_lector_access_denied(){
    if(hontza_solr_search_is_usuario_lector()){ 
        drupal_access_denied();
        exit();
    }
}
function hontza_solr_search_grupo_left_title(){
    $title=t('Group');
    $icono=my_get_icono_action('descripcion_grupo', $title,'descripcion_grupo').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
function hontza_solr_search_users_left_title(){
    $title=t('Users');
    $icono=my_get_icono_action('user', $title,'users_left').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
function hontza_solr_search_add_source_left_title(){
    $title=t('Add Source');
    $icono=my_get_icono_action('fuente', $title,'add_source_left').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
function hontza_solr_search_busqueda_simple_left_title(){
    $title=t('Simple Search');
    $icono=my_get_icono_action('busqueda_simple', $title,'busqueda_simple_left').'&nbsp;';
    $result=$icono.$title;
    return $result;
}                
function hontza_solr_search_add_channel_left_title(){
    $title=t('Add Channel');
    $icono=my_get_icono_action('canal',$title,'canal').'&nbsp;';
    $result=$icono.$title;
    return $result;
}             
function hontza_solr_search_categories_left_title(){
    $title=t('Categories');
    $icono=my_get_icono_action('categories',$title,'categories').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
function hontza_solr_search_active_filters_left_title(){
    $title=t('My Filter');
    $icono=my_get_icono_action('filtrar_por',$title,'filtrar_por').'&nbsp;';
    $result=$icono.$title;
    return $result;
}
function hontza_solr_search_reports_area_left_title(){
    $title=t('Reports Area');
    $result=l($title,'boletin_report/report_view_list');
    $icono=my_get_icono_action('boletin_left',$title,'boletin_left').'&nbsp;';
    $result=$icono.$result;
    return $result;
}
function hontza_solr_search_get_busqueda_avanzada_title_links(){
    global $base_url;
    $html=array();
    $my_grupo=og_get_group_context();
    $my_grupo_nid='';
    $url_simple_search='vigilancia/validados';
    $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
    $url_advanced_search='hontza_solr/busqueda_avanzada_solr?='.$query_busqueda_avanzada_solr;
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $my_grupo_nid=$my_grupo->nid;
        $query_busqueda_avanzada_solr=hontza_solr_search_get_query_busqueda_avanzada_solr($my_grupo_nid);
        $url_advanced_search='hontza_solr/busqueda_avanzada_solr?='.$query_busqueda_avanzada_solr;    
        $url_simple_search=$base_url.'/'.$my_grupo->purl.'/vigilancia/validados';
        $url_advanced_search=$base_url.'/'.$my_grupo->purl.'/hontza_solr/busqueda_avanzada_solr?'.$query_busqueda_avanzada_solr;
    }
    //
    $html[]='<input id="solr_title_simple_search_btn" type="button" value="'.t('Simple Search').'"/>';
    //
    $html[]='<input id="solr_title_advanced_search_btn" type="button" value="'.t('Advanced Search').'"/>';    
    $html[]=hontza_solr_search_add_busqueda_avanzada_title_links_js($url_simple_search,$url_advanced_search);
    return implode('&nbsp;',$html);
}
function hontza_solr_search_add_busqueda_avanzada_title_links_js($url_simple_search,$url_advanced_search){
    $result='<script>$(document).ready(function()
     {
        $("#solr_title_simple_search_btn").click(function()
        {
            location.href="'.$url_simple_search.'";
        });
         $("#solr_title_advanced_search_btn").click(function()
        {
            location.href="'.$url_advanced_search.'";
        });  
     });</script>';
    return $result;
}        