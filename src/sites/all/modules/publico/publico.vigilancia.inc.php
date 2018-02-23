<?php
function publico_vigilancia_view_callback(){
    $nid=arg(2);
    $node=node_load($nid);
    publico_vigilancia_view_access($node);
    $output=node_view($node,FALSE,1);
    $comments=comment_render($node);
    $output.=$comments;
    //intelsat-2015
    drupal_set_title($node->title);
    return $output;
}
function publico_vigilancia_callback($search='',$is_search=0,$is_advanced_search=0,$is_etiquetas=0){
    global $language;
    $tid='';
    if($is_etiquetas){
        $tid=arg(3);
    }
    $canal_node='';
    $canal_nid='';
    if(publico_vigilancia_is_canales_destacados()){
        $canal_nid=publico_vigilancia_get_canal_nid('canales_destacados');
        $canal_node=node_load($canal_nid);
    }
    if(!$is_search){
        if(!$is_etiquetas){
            $sql=publico_vigilancia_create_sql();
        }else{
            $sql=publico_vigilancia_etiquetas_create_sql();
        }    
    }else{
        if(!$is_advanced_search){
            $sql=publico_vigilancia_create_busqueda_sql($search);
        }else{
            $sql=publico_vigilancia_create_busqueda_avanzada_sql($search);
        }    
    }
    //$my_limit=20;
    $my_limit=10;
    $res=db_query($sql);
    $output='';
    $result=array();
    //$output=hontza_vigilancia_menu();
  $num_rows = FALSE;
 while ($row=db_fetch_object($res)){
    $node_view=node_view(node_load($row->nid), 1);
    $result[]=$node_view;      
    $num_rows = TRUE;
  }
  
  $result=my_set_estrategia_pager($result, $my_limit);    
    
  if(!empty($result)){
      foreach($result as $i=>$value){
          $output.=$value;
      }
  }
  
  if ($num_rows) {
    //$feed_url = url('rss.xml', array('absolute' => TRUE));
    //drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));
    $output .= theme('pager', NULL,$my_limit);
  }
  else {
     $output.= '<div id="first-time">' .t('There are no contents'). '</div>';  
  }        
     if(!$is_search){
         if(!$is_etiquetas){
            if(hontza_is_vigilancia('destacados')){
                drupal_set_title(t('Monitoring - Highlighted'));
            }else if(publico_vigilancia_is_canales_destacados()){
                if(isset($canal_node->title) && !empty($canal_node->title)){
                    drupal_set_title($canal_node->title);
                }    
            }else{ 
                drupal_set_title(t('Latest news'));
            }    
         }else{
            $term=taxonomy_get_term($tid);
            $term_name='';
            if(isset($term->tid) && !empty($term->tid)){
                $term_name=$term->name;
            }
            $term_name=get_term_extra_name($tid, $language->language,$term_name);
            $my_title=t('News in Tag').': '.$term_name;
            drupal_set_title($my_title);
         }   
     }else{
        drupal_set_title(t('Search results')); 
     }   
    return $output;    
}
function publico_vigilancia_create_sql(){
    $view='';
    $type=publico_vigilancia_get_type();
    if($type=='validados'){
        hontza_og_vigilancia_validados_pre_execute($view);
    }else if($type=='lo-mas-valorado'){
        my_og_vigilancia_lo_mas_valorado($view);
    }else if($type=='lo-mas-comentado'){
        my_og_vigilancia_mascomentadas($view);
    }else{    
        //my_vigilancia_ultimas_pre_execute($view);
        $canal_nid=publico_vigilancia_get_canal_nid($type);
        $sql=publico_vigilancia_get_ultimos_sql('',0,$canal_nid);
        return $sql;
    }
    return $view->build_info['query'];
}
function publico_vigilancia_is_vigilancia_left($is_block=0){
    /*if(publico_is_pantalla_publico('vigilancia')){
        return 1;
    }*/
    return 0;
}
function publico_vigilancia_get_hontza_vigilancia_menu_arg_type(){
    $result=arg(1);
    if(publico_is_pantalla_publico('vigilancia')){
        $result=arg(2);
    }
    return $result;
}
function publico_vigilancia_get_selected_active($selected){
    $result=$selected;
    if(publico_vigilancia_is_vigilancia_ultimos()){
        $result='publico/vigilancia/ultimos';        
    }    
    return $result;
}
function publico_vigilancia_get_sidebar_right($right){
    $result=$right;
    if(publico_vigilancia_is_vigilancia_ultimos()){
        $html=array();
        $html[]=publico_vigilancia_get_busqueda_simple_content_html();
        $html[]=publico_vigilancia_get_categoria_content_html();
        $html[]=publico_vigilancia_get_source_type_content_html();
        $html[]=publico_vigilancia_get_user_news_content_html();
        $html[]=red_funciones_get_nube_de_etiquetas_inicio(1);
        $result=implode('',$html);
    }
    return $result;
}
function publico_vigilancia_get_div_main_style(){
    $result='';
    //if(publico_is_pantalla_publico('vigilancia')){
    if(publico_vigilancia_is_vigilancia_ultimos()){
        $result=' style="float:left;width:70%;border-right: 1px solid #CCCCCC;"';
    }
    return $result;
}
function publico_vigilancia_get_div_sidebar_right_style(){
    $result='';
    if(publico_is_pantalla_publico('vigilancia')){
        $result=' style="float:left;width:25%;"';
    }
    return $result;
}
function publico_vigilancia_get_categoria_content_html(){
    $html=array();
    $html[]='<div class="block block-hontza block-even region-even clearfix " id="block-hontza-0">';
    $icono=my_get_icono_action('categories',t('Categories'));
    $html[]='<h3 class="title">'.$icono.'&nbsp;'.t('Categories').'</h3>';
    $html[]='<div class="content">';
    $info=hontza_block('view',0,array(),1);
    $html[]=$info['content'];
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);
}
function publico_vigilancia_get_url_categoria($contenido_tid,$url_cat){
    $result=$url_cat;
    if(publico_is_pantalla_publico('vigilancia')){
        $result='publico/canales/categorias/'. $contenido_tid.'/ultimos';           
    }
    return $result;
}
function publico_vigilancia_canales_categorias_callback(){
    return canales_my_categorias_callback();
}
function publico_vigilancia_get_canales_categorias_params($tid,$arg_type){
    $info=array();
    $info['tid']=$tid;
    $info['arg_type']=$arg_type;
    $info['is_headers']=1;
    if(publico_is_pantalla_publico('vigilancia')){
        $info['tid']=arg(3);
        $info['arg_type']=arg(4);
        $info['is_headers']=0;
    }
    return $info;
}
function publico_vigilancia_get_categorias_info($url_cat_bakup,$url_cat,$img){
    $info=array();
    $info['url_cat']=$url_cat;
    $info['img']=$img;
    if(publico_is_pantalla_publico('vigilancia')){
        $info['url_cat']=$url_cat_bakup;
        $info['img']='';
    }
    return $info;
}
function publico_vigilancia_get_source_type_content_html(){
    $html=array();
    $html[]='<div class="block block-hontza block-even region-even clearfix " id="block-hontza-source-type">';
    $label=t('Types of Sources');
    $icono=my_get_icono_action('tipos_de_fuentes',$label);
    $html[]='<h3 class="title">'.$icono.'&nbsp;'.$label.'</h3>';
    $html[]='<div class="content">';
    $content=publico_vigilancia_get_source_type_content();
    $html[]=$content;
    $html[]='</div>';
    $html[]='</div>';
    return implode('',$html);
}
function publico_vigilancia_get_source_type_content(){
    $html=array();
    $label='';
    $source_type_array=taxonomy_get_tree(1);
    $source_type_array=publico_vigilancia_order_source_type_array($source_type_array);
    if(!empty($source_type_array)){
        foreach($source_type_array as $i=>$term){
            if(publico_vigilancia_is_source_type_noticias($term->tid)){
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
                $html[]='<li class="nivel0"'.$style.'>'.l($term_name,'publico/canales/tipos_fuente/'.$term->tid).'</li>';
            }    
        }
    }
    return implode('',$html);
}
function publico_vigilancia_canales_tipos_fuente_callback(){
    global $language;
    $output='';
    $tid=arg(2);    
    $num_rows=false;
    $term_name='';
    $arg_type=arg(3);
    //intelsat-2015
    $is_headers=1;
    if(hontza_canal_rss_is_publico_activado()){
        $info=publico_vigilancia_get_canales_categorias_params($tid,$arg_type);
        $tid=$info['tid'];
        $arg_type=$info['arg_type'];
        $is_headers=$info['is_headers'];
    }
    if($is_headers){
        $output.=hontza_canales_por_categorias_menu();
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
        $my_limit=20;
        //intelsat-2015
        $item_list=publico_vigilancia_get_source_type_tid_item_list($tid);
        $item_list=hontza_canales_por_categorias($item_list);
        $item_list=publico_vigilancia_get_destacadas_item($item_list);
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
function publico_vigilancia_get_is_tipos_fuente(){
    if(publico_is_pantalla_publico('vigilancia')){
        $param=arg(2);
        if(!empty($param) && $param=='tipos_fuente'){
            return 1;
        }
    }
    return 0;
}
function publico_vigilancia_get_source_type_tid_item_list($tid){
    $result=hontza_canal_rss_get_content_field_item_source_tid_nid_array($tid);
    $canal_nid_list=publico_get_source_type_canal_nid_list($tid);
    if(count($canal_nid_list)>0){
        foreach($canal_nid_list as $i=>$canal_nid){
            $nid_list=get_canal_nid_list($canal_nid);
            $result=array_merge($result,$nid_list);
        }
    }
    return $result;
}
function publico_get_source_type_canal_nid_list($tid){
    $result=array();
    $res=db_query($sql=sprintf('SELECT * FROM {content_field_canal_source_type} WHERE field_canal_source_type_value=%d',$tid));
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
function publico_vigilancia_get_page_node_view($page){
    $result=$page;
    if(publico_is_vigilancia_node_view()){
        $result=1;
    }
    return $result;
}
function publico_is_vigilancia_node_view(){
    if(publico_is_pantalla_publico('vigilancia')){
        $nid=arg(2);
        if(!empty($nid) && is_numeric($nid)){
            $param=arg(3);
            if(!empty($param) && $param=='view'){
                return 1;
            }
        }
    }
    return 0;
}
function publico_vigilancia_is_source_type_noticias($tid){
    $item_list=publico_vigilancia_get_source_type_tid_item_list($tid);
    if(count($item_list)>0){
        return 1;
    }
    return 0;
}
function publico_vigilancia_busqueda_callback(){
    $search=$_REQUEST['search'];
    return publico_vigilancia_callback($search,1);
}
function publico_vigilancia_create_busqueda_sql($search){
    $field='node.created';
    $sort='DESC';
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where_grupo='og_ancestry.group_nid='.$my_grupo->nid;
    }
    $where[]='node.type IN("item","noticia")';
    $where[]='(node.title LIKE "%%'.$search.'%%" OR node_revisions.body LIKE "%%'.$search.'%%")';
    $where[]='(content_type_item.field_is_carpeta_noticia_destaca_value=1 OR content_type_noticia.field_is_noticia_usuario_destaca_value=1)';
    $sql='SELECT node.*
    FROM {node} node
    LEFT JOIN {og_ancestry} ON og_ancestry.nid=node.nid
    LEFT JOIN {node_revisions} node_revisions ON node_revisions.vid=node.vid 
    LEFT JOIN {content_type_item} ON node.vid=content_type_item.vid
    LEFT JOIN {content_type_noticia} ON node.vid=content_type_noticia.vid		
    WHERE '.implode(' AND ',$where).'
    ORDER BY '.$field.' '.$sort;
    return $sql;
}
function publico_vigilancia_get_user_news_content_html(){
    $html=array();
    $html[]='<div class="block block-hontza block-even region-even clearfix " id="block-hontza-add-user-news">';
    //$icono=my_get_icono_action('categories',t('Categories'));
    $icono='';
    $icono.='&nbsp;';
    $html[]='<h3 class="title">'.$icono.t('User news').'</h3>';
    $html[]='<div class="content">';
    //$node_add_noticia='publico/vigilancia/add_user_news';
    /*$node_add_noticia='node/add/noticia';
    $html[]=red_funciones_get_create_link(t('Create User News'),$node_add_noticia,array('attributes'=>array('class'=>'a_create_user_news'),'query'=>'destination=publico/vigilancia/ultimos'));
    */
    $node_add_noticia='publico/validar_email/node_add_noticia';    
    $html[]=red_funciones_get_create_link(t('Create User News'),$node_add_noticia,array('attributes'=>array('class'=>'a_create_user_news')));    
    $html[]='</div>';    
    $html[]='</div>';
    return implode('',$html);
}
function publico_vigilancia_noticia_validate($form, &$form_state){
    if(isset($form_state['values']['field_noticia_mail'][0]['value']) && !empty($form_state['values']['field_noticia_mail'][0]['value'])){
        $email=$form_state['values']['field_noticia_mail'][0]['value'];
        if(!publico_existe_register($email,$register_row)){
            form_set_error('edit-field-noticia-mail-0-value',t("Your email is not registered, you can't create news"));
        }
    }
}
function publico_vigilancia_is_node_add_noticia(){
    if(hontza_is_user_anonimo()){
        if(is_node_add('noticia')){
            return 1;
        }
    }
    return 0;
}
function publico_vigilancia_is_vigilancia_ultimos(){
    if(publico_is_pantalla_publico('vigilancia') || publico_vigilancia_is_node_add_noticia() || publico_vigilancia_is_publico_register_validar_email()){
        return 1;
    }
    return 0;
}
function publico_vigilancia_set_noticia_node_form_alter(&$form,$form_id){
    if(in_array($form_id,array('noticia_node_form'))){
        if(hontza_is_user_anonimo()){
            $form['field_noticia_mail']['#weight']=-1000;
            $form['field_noticia_mail'][0]['#title']=t('Your email');
            if(isset($_SESSION['publico_register_email_validado']) && !empty($_SESSION['publico_register_email_validado'])){
                $form['field_noticia_mail'][0]['#default_value']['value']=$_SESSION['publico_register_email_validado'];                
            }
            $form['body_field']['body']['#title']=t('Text');
            publico_unset_form_fields($form,$form_id);
            $form['#redirect']='publico/vigilancia/ultimos';       
        }else{
            $unset_array=array('field_noticia_mail','captcha');
            if(!empty($unset_array)){    
                foreach($unset_array as $i=>$unset_field){
                    if(isset($form[$unset_field])){
                        unset($form[$unset_field]);
                    }
                }
            }
        }         
    }
}
function publico_vigilancia_is_publico_register_validar_email($param='node_add_noticia'){
    if(publico_is_pantalla_publico('validar_email')){
        $validar_email_type=arg(2);
        if($validar_email_type==$param){
            return 1;
        }
    }
    return 0;
}
function publico_vigilancia_get_ultimos_sql($etiqueta_tid='',$is_etiquetas=0,$canal_nid=''){
    $sql='';
	 $my_grupo=og_get_group_context();
         $where=array();
         if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
            $where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")"; 
         }
             $where[]="node.status <> 0";
             $where[]="node.type in ('item', 'noticia')";
             $where[]="(content_type_item.field_is_carpeta_noticia_destaca_value=1 OR content_type_noticia.field_is_noticia_usuario_destaca_value=1)";
             if($is_etiquetas){
                 if(empty($etiqueta_tid)){
                    $tid=arg(3);
                 }else{
                    $tid=$etiqueta_tid; 
                 }
                 $where[]="term_node.tid=".$tid;
             }
             if(!empty($canal_nid)){
                 $where[]='content_type_item.field_item_canal_reference_nid='.$canal_nid;
             }
	 	$sql="SELECT DISTINCT(node.nid) AS nid, node.created AS node_created 
		FROM {node} node
		LEFT JOIN flag_content flag_content_node ON node.nid = flag_content_node.content_id 
		LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid 
                LEFT JOIN {content_type_item} ON node.vid=content_type_item.vid
                LEFT JOIN {content_type_noticia} ON node.vid=content_type_noticia.vid
		LEFT JOIN {term_node} ON term_node.vid=node.vid 
                WHERE ".implode(" AND ",$where)." 
		GROUP BY nid ORDER BY node_created DESC";
                //print $sql;
         return $sql;
}
function publico_vigilancia_get_destacadas_item($item_list){
    $destacadas_item=$item_list;
    if(publico_is_pantalla_publico('vigilancia')){    
        $destacadas_item=array();
        if(!empty($item_list)){
            foreach($item_list as $i=>$item_nid){
                $node=node_load($item_nid);
                if(publico_vigilancia_is_node_destacado($node)){
                    $destacadas_item[]=$item_nid;
                }
            }
        }
    }    
    return $destacadas_item;
}
function publico_vigilancia_is_node_destacado($node){
    return red_is_node_destacado($node);
}
function publico_vigilancia_view_access($node,$is_result=0){
    $result=1;
    if(!publico_vigilancia_is_node_destacado($node)){
        if(!$is_result){
            //drupal_access_denied();
            drupal_goto('publico/vigilancia/ultimos');
            exit();
        }else{
            $result=0;
        }
    }
    if($is_result){
        return $result;
    }    
}
function publico_vigilancia_add_item_tags_comments(&$item,$elemento){
    if(isset($elemento->tags)){
        $tags=(array) $elemento->tags;
        $item['tags']=base64_encode(json_encode($tags));
    }
    if(isset($elemento->comments)){
        $comments=publico_vigilancia_prepare_comments_rss($elemento->comments);
        //$comments=$elemento->comments;
        //$comments=(array) $comments;
        $item['comments']=base64_encode(json_encode($comments));
    }
}
function publico_vigilancia_save_item_tags_comments($node,$item){
    publico_vigilancia_save_item_tags($node,$item);
    publico_vigilancia_save_item_comments($node,$item);
    //hontza_solr_search_clear_cache_content($node);
}
function publico_vigilancia_save_item_tags($node,$item){
    $tid=0;            
    $vid=3;    
    if(isset($item['tags']) && !empty($item['tags'])){
        $tags=json_decode(base64_decode($item['tags']));
        if(isset($tags->tag) && !empty($tags->tag)){
            $tid=0;
            if(is_array($tags->tag)){
                foreach($tags->tag as $i=>$tag){
                    $tid=publico_vigilancia_save_taxonomy_term($vid,$tag);
                    if(!empty($tid)){
                        publico_vigilancia_save_term_node($node->vid,$node->nid,$tid);
                    }  
                }
            }else{
                if(!empty($tags->tag)){
                    $tid=publico_vigilancia_save_taxonomy_term($vid,$tags->tag);
                    if(!empty($tid)){
                        publico_vigilancia_save_term_node($node->vid,$node->nid,$tid);
                    }  
                }
            }              
        }    
    }
}
function publico_vigilancia_insert_taxonomy_term($vid,$tag){
    $tid=0;
    $description='';
    $weight=0;
    db_query('INSERT INTO {term_data}(vid,name,description,weight) VALUES(%d,"%s","%s",%d)',$vid,$tag,$description,$weight);
    $term=publico_vigilancia_taxonomy_get_term_by_name_vid($tag, $vid);
    if(isset($term->tid) && !empty($term->tid)){
        $tid=$term->tid;
    }
    return $tid;
}
function publico_vigilancia_taxonomy_get_term_by_name_vid($tag, $vid){
    $term_array=taxonomy_get_term_by_name_vid($tag, $vid);
    if(isset($term_array[0])){
        return $term_array[0];
    }
    $my_result=new stdClass();
    return $my_result;
}
function publico_vigilancia_save_taxonomy_term($vid,$tag){
    $term=publico_vigilancia_taxonomy_get_term_by_name_vid($tag, $vid);
    $tid=0;
    if(isset($term->tid) && !empty($term->tid)){
        $tid=$term->tid;                        
    }else{
        $tid=publico_vigilancia_insert_taxonomy_term($vid,$tag);
    }
    return $tid;
}
function publico_vigilancia_save_term_node($vid,$nid,$tid){
    db_query($sql=sprintf('INSERT INTO {term_node}(nid,vid,tid) VALUES(%d,%d,%d)',$nid,$vid,$tid));
}
function publico_vigilancia_save_item_comments($node,$item){
    global $user;
     if(isset($item['comments']) && !empty($item['comments'])){
        $comments=json_decode(base64_decode($item['comments']));
        //echo print_r($item,1);exit();
            
        if(isset($comments->comment) && !empty($comments->comment)){
            $kont=0;
            foreach($comments->comment as $i=>$comment){
                $time=time();
                db_query('INSERT INTO {comments}(nid,uid,subject,comment,timestamp,name) VALUES(%d,%d,"%s","%s",%d,"%s")',$node->nid,$user->uid,$comment->subject,$comment->text,$time,$user->name);
                $kont++;
            }
            if($kont>0){
                db_query('DELETE FROM {node_comment_statistics} WHERE nid=%d',$node->nid);
                db_query('INSERT INTO {node_comment_statistics}(nid,last_comment_timestamp,last_comment_name,last_comment_uid,comment_count) VALUES(%d,%d,"%s",%d,%d)',$node->nid,$time,$user->name,$user->uid,$kont);
            }                    
        }
     }   
}
function publico_vigilancia_prepare_comments_rss($comments_in){
    $comments=new stdClass();
    if(isset($comments_in->comment) && !empty($comments_in->comment)){
        $comments->comment=array();
        $kont=0;
        foreach($comments_in->comment as $row){
            $comments->comment[$kont]=new stdClass();
            $comments->comment[$kont]->subject=(string) $row->subject;
            $comments->comment[$kont]->text=(string) $row->text;
            $comments->comment[$kont]->timestamp=(string) $row->timestamp;
            $comments->comment[$kont]->name=(string) $row->name;      
            $kont++;
        }
    }
    return $comments;
}
function publico_vigilancia_send_callback(){
    $nid=arg(3);
    $node=node_load($nid);
    publico_vigilancia_view_access($node);
    return red_send_form_html($node);
}
function publico_vigilancia_is_send(){
    $result=0;
    if(publico_is_pantalla_publico('vigilancia','send')){
        $result=1;
    }
    return $result;
}
function publico_vigilancia_get_busqueda_simple_content_html(){
    $html=array();
    $html[]='<div class="block block-hontza block-even region-even clearfix " id="block-hontza-simple-search">';
    //$icono=my_get_icono_action('categories',t('Categories'));
    $icono='';
    $icono.='&nbsp;';
    $html[]='<h3 class="title">'.$icono.t('Simple Search').'</h3>';
    $html[]='<div class="content">';
    $html[]=my_get_busqueda_simple_content(1);    
    $html[]='</div>';    
    $html[]='</div>';
    return implode('',$html);
}
function publico_vigilancia_busqueda_avanzada_callback(){
    $output=drupal_get_form('publico_vigilancia_busqueda_avanzada_form');
    $is_search=1;
    $is_advanced_search=1;
    $output.=publico_vigilancia_callback('',$is_search,$is_advanced_search);
    return $output;
}
function publico_vigilancia_busqueda_avanzada_form(){
    $form=array();
    
    $fs_title=t('Search');
    $key='publico_busqueda_avanzada_filtro';
    if(publico_vigilancia_busqueda_avanzada_is_filter_activated()){
        $fs_title=t('Filter Activated');
        $class='file_buscar_fs_vigilancia_class fs_search_activated';
    }else{
        $fs_title=t('Filter');
        $class='file_buscar_fs_vigilancia_class';        
    }    
    //           
    $form=array();
    $form['file_buscar_fs']=array('#type'=>'fieldset','#title'=>$fs_title,'#attributes'=>array('id'=>'file_buscar_fs','class'=>$class));    
    
    $prefix_left='<div style="float:left;width:100%;"><div style="float:left;width:48%;">';
    $suffix_left='</div>';
    $prefix_right='<div style="float:left;width:48%;padding-left:10px;">';
    $suffix_right='</div></div>';
    $fecha_prefix_left='<div style="float:left;width:100%;padding-bottom:20px;"><div style="float:left;width:48%;">';
    $form['file_buscar_fs']['search']=array(
        '#title'=>t('In any field'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
        //'#attributes'=>array('style'=>'float:left;width:50%;'),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('search',$key),
    );
    $form['file_buscar_fs']['title']=array(
        '#title'=>t('In the title'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
        //'#attributes'=>array('style'=>'float:left;width:50%;'),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('title',$key),
    );
    $form['file_buscar_fs']['body']=array(
        '#title'=>t('In the description'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
        //'#attributes'=>array('style'=>'float:left;width:50%;'),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('body',$key),
    );
    $form['file_buscar_fs']['no_title']=array(
        '#title'=>t('Not in the title'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
        //'#attributes'=>array('style'=>'float:left;width:50%;'),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('no_title',$key),
    );
    $form['file_buscar_fs']['no_body']=array(
        '#title'=>t('Not in the description'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
        //'#attributes'=>array('style'=>'float:left;width:50%;'),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('no_body',$key),
    );
    $form['file_buscar_fs']['tipo']=array(
        '#title'=>t('Type'),
        '#type'=>'select',
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
        '#options'=>my_get_tipo_options(),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('tipo',$key),
    );
    $form['file_buscar_fs']['categoria_canal']=array(
        '#title'=>t('Category'),
        '#type'=>'select',
        '#prefix'=>$prefix_left,
        '#suffix'=>$suffix_left,
        '#options'=>my_get_categorias_canal(),
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('categoria_canal',$key),
    );
    $form['file_buscar_fs']['etiquetas']=array(
        '#title'=>t('Tag'),
        '#type'=>'textfield',
        '#prefix'=>$prefix_right,
        '#suffix'=>$suffix_right,
        '#default_value'=>publico_vigilancia_busqueda_avanzada_get_filter_value('etiquetas',$key),
        '#autocomplete_path' => 'publico/vigilancia/is_busqueda/taxonomy/autocomplete',        
    );
    
    $fecha_inicio=publico_vigilancia_busqueda_avanzada_get_filter_value('fecha_inicio',$key);    
    $fecha_fin=publico_vigilancia_busqueda_avanzada_get_filter_value('fecha_fin',$key);
      
    $form['file_buscar_fs']['fecha_inicio']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('From'),
			'#default_value'=>  $fecha_inicio,
                        //'#prefix'=>'<div style="float:left;width:100%;"><div style="float:left;width:50%;">',
                        //'#suffix'=>'</div>');
                        '#prefix'=>$fecha_prefix_left,
                        '#suffix'=>$suffix_left);
    $form['file_buscar_fs']['fecha_fin']=array(
			'#type' => 'date_select',
			'#date_format' => 'Y-m-d',
			'#date_label_position' => 'within',
			'#title'=>t('To'),
			'#default_value'=>$fecha_fin,
                        /*'#prefix'=>'<div style="float:left;width:50%;">',
                        '#suffix'=>'</div>');*/
                        '#prefix'=>$prefix_right,
                        '#suffix'=>$suffix_right);    
    $form['file_buscar_fs']['search_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Search'),
        '#name'=>'search_btn',
    );
    $form['file_buscar_fs']['reset']=array('#type'=>'submit','#value'=>t('Clean'),'#name'=>'limpiar');
    my_add_buscar_js();
    return $form;
}
function publico_vigilancia_busqueda_avanzada_form_submit($form,&$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        $name=$form_state['clicked_button']['#name'];
        $key='publico_busqueda_avanzada_filtro';
        if(strcmp($name,'limpiar')==0){
            if(isset($_SESSION[$key]['filter']) && !empty($_SESSION[$key]['filter'])){
                unset($_SESSION[$key]['filter']);
            }
        }else{
            $_SESSION[$key]['filter']=array();
            $fields=publico_vigilancia_busqueda_avanzada_define_filter_fields();
            if(count($fields)>0){
                foreach($fields as $i=>$f){
                    $v=$form_state['values'][$f];
                    if(!empty($v)){
                        $_SESSION[$key]['filter'][$f]=$v;
                    }
                }
            }
        }        
    }
}
function publico_vigilancia_busqueda_avanzada_define_filter_fields(){
    $result=array('search','title','body','no_title','no_body','tipo','categoria_canal','etiquetas','fecha_inicio','fecha_fin');    
    return $result;
}
function publico_vigilancia_busqueda_avanzada_is_filter_activated(){
    $fields=publico_vigilancia_busqueda_avanzada_define_filter_fields();
    if(count($fields)>0){
        foreach($fields as $i=>$f){
            if(isset($_SESSION['publico_busqueda_avanzada_filtro']['filter'][$f]) && !empty($_SESSION['publico_busqueda_avanzada_filtro']['filter'][$f])){
                return 1;
            }
        }
    }
    return 0;
}
function publico_vigilancia_busqueda_avanzada_get_filter_value($field,$key){
    return hontza_get_gestion_usuarios_filter_value($field,$key);
}
function publico_vigilancia_create_busqueda_avanzada_sql(){
    $field='node.created';
    $sort='DESC';
    $where=array();
    $where[]='1';
    $my_grupo=og_get_group_context();
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
        $where_grupo='og_ancestry.group_nid='.$my_grupo->nid;
    }
    $where[]='node.type IN("item","noticia")';
    
    $filter_fields=publico_vigilancia_busqueda_avanzada_define_filter_fields();
   
   if(!empty($filter_fields)){
       foreach($filter_fields as $k=>$f){
           $v=publico_vigilancia_busqueda_avanzada_get_filter_value($f,'publico_busqueda_avanzada_filtro');
           if(!empty($v)){
                switch($f){
                    case 'search':
                        $where[]='(node.title LIKE "%%'.$v.'%%" OR node_revisions.body LIKE "%%'.$v.'%%")';    
                        break;
                    case 'title':
                        $where[]='(node.title LIKE "%%'.$v.'%%")';    
                        break;
                    case 'body':
                        $where[]='(node_revisions.body LIKE "%%'.$v.'%%")';    
                        break;
                    case 'no_title':
                        $where[]='(NOT node.title LIKE "%%'.$v.'%%")';    
                        break;
                    case 'no_body':
                        $where[]='(NOT node_revisions.body LIKE "%%'.$v.'%%")';    
                        break;
                    case 'tipo':
                        $where[]='(content_field_canal_source_type.field_canal_source_type_value='.$v.' OR content_field_item_source_tid.field_item_source_tid_value='.$v.')';
                        break;
                    case 'categoria_canal':
                        $where[]='(term_node_categoria_canal.tid='.$v.' OR content_field_item_canal_category_tid.field_item_canal_category_tid_value='.$v.')';
                        break;
                    case 'etiquetas':
                        $where[]='(term_data_etiquetas.vid=3)';
                        $where[]=get_where_etiquetas($v);	
                        break;
                }
           } 
       }
   }
   
   $where[]=hontza_get_usuarios_acceso_where_time('node.changed','publico_busqueda_avanzada_filtro','fecha_inicio','fecha_fin');
        
    $where[]='(content_type_item.field_is_carpeta_noticia_destaca_value=1 OR content_type_noticia.field_is_noticia_usuario_destaca_value=1)';
    $sql='SELECT node.*
    FROM {node} node
    LEFT JOIN {og_ancestry} ON og_ancestry.nid=node.nid
    LEFT JOIN {node_revisions} node_revisions ON node_revisions.vid=node.vid 
    LEFT JOIN {content_type_item} ON node.vid=content_type_item.vid
    LEFT JOIN {content_type_noticia} ON node.vid=content_type_noticia.vid 
    LEFT JOIN {content_type_canal_de_yql} ON content_type_canal_de_yql.nid=content_type_item.field_item_canal_reference_nid 
    LEFT JOIN {content_field_canal_source_type} ON content_field_canal_source_type.vid=content_type_canal_de_yql.vid 
    LEFT JOIN {content_field_item_source_tid} ON content_field_item_source_tid.vid=node.vid 
    LEFT JOIN {term_node} term_node_categoria_canal ON content_type_canal_de_yql.nid=term_node_categoria_canal.nid 
    LEFT JOIN {content_field_item_canal_category_tid} ON content_field_item_canal_category_tid.vid=node.vid 
    LEFT JOIN {term_node} term_node_etiquetas ON node.nid=term_node_etiquetas.nid
    LEFT JOIN {term_data} term_data_etiquetas ON term_node_etiquetas.tid=term_data_etiquetas.tid
    WHERE '.implode(' AND ',$where).' 
    GROUP BY node.nid     
    ORDER BY '.$field.' '.$sort;
    return $sql;
}
function publico_vigilancia_is_busqueda_taxonomy_autocomplete_callback(){
    require_once('sites/all/modules/taxonomy/taxonomy.pages.inc');
    $string=arg(5);
    return taxonomy_autocomplete('is_busqueda',$string);
}
function publico_vigilancia_canales_tags_callback(){
    return publico_vigilancia_callback('',0,0,1);
}
function publico_vigilancia_etiquetas_create_sql($tid=''){
    return publico_vigilancia_get_ultimos_sql($tid,1);
}
function publico_vigilancia_filtrar_etiquetas_noticias_destacadas($tags){
    $result=$tags;
    if(!empty($result)){
        $result=array();
        foreach($tags as $i=>$r){
            if(hontza_is_user_anonimo()){
                if(publico_vigilancia_is_etiqueta_noticias_destacadas($r->tid)){
                    $result[]=$r;
                }
            }else{
                $result[]=$r;
            }    
        }        
    }
    return $result;
}
function publico_vigilancia_is_etiqueta_noticias_destacadas($tid){
    $result=0;
    $sql=publico_vigilancia_etiquetas_create_sql($tid);
    $res=db_query($sql);
    while($r=db_fetch_object($res)){
        $result=1;
        return $result;
    }
    return $result;
}
function publico_vigilancia_is_view(){
    $result=0;
    if(publico_is_pantalla_publico('vigilancia')){
        $param2=arg(2);
        if(!empty($param2) && is_numeric($param2)){
            $param3=arg(3);
            if(!empty($param3) && $param3=='view'){
                $result=1;
            }
        }
    }
    return $result;
}
function publico_vigilancia_publico_tagadelic_chunk_callback(){
    $vid=array();
    $vid[]=arg(2);
    return tagadelic_page_chunk($vid);
}
function publico_vigilancia_get_categoria_rss_link($tid){
    $result='';
    if(publico_is_pantalla_publico('vigilancia')){
        $icono='publico_solr_results_rss';
        $url='publico/canales/categorias/'.$tid.'/categoria_exportar_rss';
        $result=l(my_get_icono_action($icono, t('RSS'),'left_rss'),$url,array('html'=>TRUE,'attributes'=>array('target'=>'_blank')));    
    }
    return $result;
}
function publico_vigilancia_canales_categorias_categoria_exportar_rss_callback(){
    $item_array=canales_my_categorias_callback(1);
    $nid_array=publico_vigilancia_canales_categorias_categoria_exportar_rss_get_nid_array($item_array);
    busqueda_rss_node_feed($nid_array,array(),'',1,'publico_vigilancia_canales_categorias_categoria_exportar_rss');
    exit();
}
function publico_vigilancia_canales_categorias_categoria_exportar_rss_get_nid_array($item_array){
    $result=array();
    if(!empty($item_array)){
        foreach($item_array as $i=>$r){
            $result[]=$r->nid;
        }
    }        
    return $result;    
}
function publico_vigilancia_rss_general_callback(){
    $nid_array=publico_vigilancia_get_nid_array_rss_general();
    busqueda_rss_node_feed($nid_array,array(),'',1,'rss_general');
    exit();
}
function publico_vigilancia_get_nid_array_rss_general(){
    $result=array();
    $sql=publico_vigilancia_get_ultimos_sql();
    $res=db_query($sql);
    while($r=db_fetch_object($res)){
        $result[]=$r->nid;
    }
    return $result;
}
function publico_vigilancia_on_insert_noticia_de_usuario_send_message_admin(&$node){
    global $base_url,$language;
    $username_news='';
    $r=publico_register_get_register_row($publico_register_email_validado);
    if(isset($r->id) && !empty($r->id)){
        $username_news=$r->name;
    }
    $subject=t('News from user "!username_news"',array('!username_news'=>$username_news));
    $br='<br><br>';
    $user=user_load(1);
    $mail_to=$user->mail;
    $message=t('Hi').' '.$user->name.','.$br;
    $grupo=visualizador_create_grupo_base_path();
    $result=$result_in;
    $langcode='';
    if($language->language!='en'){
         $langcode='/'.$language->language;
    }
    $url=$base_url.$langcode.'/'.$grupo.'/node/'.$node->nid;
    $url=l($node->title,$url,array('absolute'=>true));
    $message.=t('The user "!username_news" has created this news: !url',array('!username_news'=>$username_news,'!url'=>$url)).$br;
    red_copiar_send_mail($mail_to,$subject,$message,'mimemail','');
}
function publico_vigilancia_get_categorias_menu(){
    //return '';
    $html=visualizador_custom_menu_create_menu_despegable();
    $html.=publico_vigilancia_add_js_remove_menu_sf_js_enabled();
    return $html;
}
function publico_vigilancia_set_menutop_publico_vigilancia_sf_js_enabled($menutop_in){
    $menutop=str_replace('menu sf-js-enabled','menu sf-js-enabled nav',$menutop_in);
    //print $menutop;exit();
    return $menutop;
}
function publico_vigilancia_add_js_remove_menu_sf_js_enabled(){
   $js='<script type="text/javascript">
   $(document).ready(function()
   {
    var bakup_id_ul="";
    //$("#id_a_latest_news").click(function()
    $("#id_a_latest_news").mouseover(function(event)
    {
        event.stopPropagation();
        var li=$(this).parent();
        var position=li.position();
        $("#ul_a").css("display","block");
        //if($.browser.mozilla) {
        //if ($.browser.mozilla || navigator.userAgent.search("Firefox") >= 0) {
        var my_user_agent=navigator.userAgent;
        //my_user_agent="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0";
        if (my_user_agent.search("Firefox") >= 0) {
            //alert(my_user_agent);
            my_version=Math.floor($.browser.version);
            if (my_version<=29) {
                $("#ul_a").css("left",position.left);
            }    
        }
        //return false;
    });
    $("#id_a_latest_news").click(function(){
        var url=$(this).attr("href");
        window.location.href=url;
    });
    //$("a.a_subcategorias").click(function()
    $("a.a_subcategorias").mouseover(function()
    {
        if(bakup_id_ul.length>0){
            $("#"+bakup_id_ul).css("display","none");
        }
        var position=$(this).position();
        var li=$(this).parent();
        var width=li.width();
        var id=li.attr("id");
        var id_ul=id.replace("id_","ul_");
        $("#"+id_ul).css("display","block");
        bakup_id_ul=id_ul;
        $("#"+id_ul).css("left",position.left+width);
        $("#"+id_ul).css("top",position.top);
        //return false;
    });
    $("a.a_subcategorias_no").mouseover(function()
    {
        if(bakup_id_ul.length>0){
            $("#"+bakup_id_ul).css("display","none");
        }
    }); 
    $(document).click(function() { 
        $("#ul_a").css("display","none");
        $("#ul_a ul").css("display","none");
    });
    /*$("#ul_a a").mouseover(function()
    {
        //var my_class=$(this).attr("class");
        //if(my_class!="a_subcategorias"){
            //$("#ul_a ul").css("display","none");
        if(bakup_id_ul.length>0){
            $("#"+bakup_id_ul).css("display","none");
        }    
        //}
    });*/
    $("#ul_a").mouseover(function(event){
        event.stopPropagation();
    });
    $(document).mouseover(function() {
        $("#ul_a").css("display","none");
        $("#ul_a ul").css("display","none");
    });
   });
   </script>';
   
   //drupal_add_js($js,'inline');
   return $js;
}
function publico_vigilancia_order_source_type_array($source_type_array){
    $result=array_ordenatu($source_type_array,'name','asc',0);
    return $result;
}
function publico_vigilancia_get_type(){
    $type=arg(2);
    if(publico_vigilancia_is_canales_destacados()){
        $type='canales_destacados';
    }
    return $type;
}
function publico_vigilancia_is_canales_destacados(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='canales'){
        $param1=arg(1);
        if(!empty($param1) && is_numeric($param1)){
            $param2=arg(2);
            if(!empty($param2) && $param2=='destacados'){
                return 1;
            }
        }
    }
    return 0;
}
function publico_vigilancia_get_canal_nid($type){
    if($type=='canales_destacados'){
        $canal_nid=arg(1);
        return $canal_nid;
    }
    return '';
}
function publico_vigilancia_on_noticia_de_usuario_save($op,&$node){
    /*if(hontza_is_user_anonimo()){
        drupal_goto('publico/vigilancia/ultimos');        
    }*/
}
function publico_vigilancia_get_info_username_noticia($node){
    $result=t('Anonymous');
    if(isset($node->field_noticia_mail[0]['value']) && !empty($node->field_noticia_mail[0]['value'])){
        $register_row=publico_register_get_register_row($node->field_noticia_mail[0]['value']);
        //if(isset($register_row->id) && !empty($register_row->id)){
        if(isset($register_row->name) && !empty($register_row->name)){
           $result=$register_row->name;
        }else{        
            $result=$node->field_noticia_mail[0]['value'];
        }
        $max=25;
        if(strlen($result)>$max){
            $result=substr($result,0,$max).'...';
        }
    }
    return $result;
}