<?php
function create_row_noticia_canal_publica_html($row,$is_mail=0){
  global $user;  
  $url_hasi='http://'.$_SERVER['HTTP_HOST'].base_path();
  //echo print_r($row,1);exit();
  $my_body='';
  //
  $is_denied=0;
  $link_title='';
  //
  if(strcmp($row->node_type,'noticias_portada')==0){
    $my_title=$row->node_title;
    $my_body=$row->node_data_field_noticias_portada_texto_field_noticias_portada_texto_value;
  }else if(strcmp($row->node_type,'rss_feed')==0){
    $my_title=$row->node_title;
    $my_body=$row->node_data_field_rss_descripcion_field_rss_descripcion_value;
  }else{
    $node=node_load($row->nid);
    $grupo_purl=hontza_get_grupo_purl_by_node($node);
    if(!empty($grupo_purl)){
        $grupo_purl.='/';
    }
    if(strcmp($row->node_type,'noticia')==0){
        $my_title=$row->node_title;
    }else{
        $my_title=$row->title;
    }
    //
    $my_body=strip_tags($node->body);
    $is_denied=1;
    //$link_title=l($my_title,'noticia_canal_home/'.$row->nid);
    $link_title='<a href="'.$url_hasi.$grupo_purl.'noticia_canal_home/'.$row->nid.'">'.$my_title.'</a>';    
  }
  if(!$is_denied){
      //$link_title=l($my_title,'node/'.$row->nid);
      $link_title='<a href="'.$url_hasi.'node/'.$row->nid.'">'.$my_title.'</a>';
  }
  //
  $user_image='';  
  //
  $html=array();
  if($is_mail){
      return date('d-m-Y',$row->node_created).':&nbsp;'.$link_title;
      $user_image=get_noticia_destacada_user_image($row);
      $html[]='<div class="noticia_destacada_mail">';
      $html[]='<div class="user_image_mail">';
      $html[]=$user_image;
      $html[]='</div>';
      
  }
  $html[]='<div class="views-row views-row-1 views-row-odd">';
  $html[]='<div class="views-field-created">';
  $html[]='<span class="field-content">'.date('d-m-Y',$row->node_created).'</span>';
  $html[]='</div>';
  $html[]='<div class="views-field-title">';
  $html[]='<span class="field-content">'.$link_title.'</span>';
  $html[]='</div>';
  $html[]='<div class="views-field-field-noticias-portada-texto-value">';
  $html[]='<div class="field-content">';
  //
  if(empty($my_body)){
    $html[]='<p>&nbsp;</p>';
  }else{
    $len=strlen($my_body);
    if($len>143){
      $html[]='<p>'.drupal_substr($my_body, 0, 143).' ...</p>';
    }else{
      $html[]='<p>'.$my_body.'</p>';
    }
  }
  $html[]='</div>';
  $html[]='</div>';
  $html[]='</div>';
  if($is_mail){      
      $html[]='</div>';
  }

  return implode('',$html);
}
function my_get_noticias_destacadas_canales_content($is_mail=0,$grupo='',$is_html=1){
   $html=array();
   $destacadas_list=get_noticias_destacadas_list();
   $item_list=get_noticias_destacadas_canales_list();
   $usuarios_destacadas_list=get_noticias_usuarios_publicas_list(0);
   $item_list=array_merge($destacadas_list,$item_list,$usuarios_destacadas_list);
   //echo print_r($item_list,1);
   $item_list=array_ordenatu($item_list,'node_created','desc',1);
   if(!$is_html){
       return $item_list;
   }
   //echo print_r($item_list,1);
   if(count($item_list)>0){
    if($is_mail){
        $my_limit=get_boletin_grupo_destacadas_limit($grupo);
    }
    $my_limit=variable_get('home_noticias_destacadas_num',100);
    //$my_limit=200;
    //print 'my_limit='.$my_limit.'<BR>';
    if($my_limit>0){
        $item_list=array_slice($item_list,0,$my_limit);
        //print 'num='.count($item_list).'<BR>';
        foreach($item_list as $i=>$row){        
            //$html[]=$row->title.'<BR>';
            $html[]=create_row_noticia_canal_publica_html($row,$is_mail);
        }
    }    
   }
   if($is_mail){
    return my_implode_destacadas_html($html);
   }else{
    return implode('',$html);
   }
}
function get_noticias_destacadas_list(){
    $result=array();
    //
    $where=array();
    $where[]="1";
    //$where[]="node.status <> 0";
    //$where[]="node.type='rss_feed'";
    //simulando
    $where[]="node.type='item'";    
    //intelsat-2015
    if(panel_admin_noticias_destacadas_is_gestion_noticias_destacadas()){
        $where=panel_admin_noticias_destacadas_add_where_filtro($where);
    }
    //
    $sql="SELECT node.nid AS nid,
    node.title AS node_title,
    node_data_field_rss_descripcion.field_rss_descripcion_value AS node_data_field_rss_descripcion_field_rss_descripcion_value,
    node_data_field_rss_descripcion.field_rss_descripcion_format AS node_data_field_rss_descripcion_field_rss_descripcion_format,
    node.type AS node_type, node.vid AS node_vid, node.created AS node_created,node.uid,node.status AS node_status,node.changed AS node_changed  
    FROM {node} node LEFT JOIN {content_type_rss_feed} node_data_field_rss_descripcion ON node.vid = node_data_field_rss_descripcion.vid 
    LEFT JOIN {og_ancestry} ON node.nid=og_ancestry.nid 
    LEFT JOIN {node_revisions} ON node.vid=node_revisions.vid  
    WHERE ".implode(" AND ",$where)." ORDER BY node_created DESC";
    //
    $res=db_query($sql);
    $result=array();
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function get_noticias_destacadas_canales_list(){
    $result=get_noticias_publicas_canales_list('noticia_destacada');
    return $result;
}
function create_publica_destacada_fieldset(&$form,$carpeta_dinamica_node){
    //echo print_r($form,1);exit();
    $is_my_submit=my_get_request_value('is_my_submit',1);
    $form['publica_destacada_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Public/Highlighted News'),
      '#attributes'=>array("id"=>'publica_destacada_fs'),
      '#weight'=>0,
      //'#prefix'=>'<div class="views-exposed-widget" id="div_publica_destacada_fs">',
      //'#suffix'=>'</div>',
    );
    //$is_carpeta_noticia_publica=get_carpeta_dinamica_field_boolean_value($carpeta_dinamica_node,'field_is_busqueda_publica');
    $is_carpeta_noticia_publica=my_get_request_value('is_carpeta_noticia_publica', 1);
    //print 'is_carpeta_noticia_publica='.$is_carpeta_noticia_publica.'<BR>';
    //
    $form['publica_destacada_fs']['is_carpeta_noticia_publica']=array(
      '#type'=>'checkbox',
      '#title'=>t('Public News'),
    );
    if($is_carpeta_noticia_publica && !$is_my_submit){
        $form['publica_destacada_fs']['is_carpeta_noticia_publica']['#attributes']=array('checked'=>'checked');
    }
    //
    //$is_carpeta_noticia_destacada=get_carpeta_dinamica_field_boolean_value($carpeta_dinamica_node,'field_is_busqueda_destacada');
    $is_carpeta_noticia_destacada=my_get_request_value('is_carpeta_noticia_destacada', 1);
    //print 'is_carpeta_noticia_destacada='.$is_carpeta_noticia_destacada.'<BR>';
    //
    $form['publica_destacada_fs']['is_carpeta_noticia_destacada']=array(
      '#type'=>'checkbox',
      '#title'=>t('Highlighted News'),
    );
    if($is_carpeta_noticia_destacada && !$is_my_submit){
        $form['publica_destacada_fs']['is_carpeta_noticia_destacada']['#attributes']=array('checked'=>'checked');
    }
    /*$form['publica_destacada_fs']['submit_publica_destacada']=array(
      '#type'=>'submit',
      '#value'=>t('Update Public/Featured'),
      '#name'=>'submit_publica_destacada',
    );*/
    $form['publica_destacada_fs']['is_my_submit']=array(
      '#type'=>'hidden',
      '#value'=>1,
      //'#name'=>'submit_publica_destacada',
    );
    my_advanced_search_news_js();
}
function save_carpeta_publica_destacada($sql){
    /*
    //echo print_r($_REQUEST,1);exit();
    $is_my_submit=my_get_request_value('is_my_submit');
    if(!empty($is_my_submit)){
        $is_carpeta_noticia_publica=my_get_request_value('is_carpeta_noticia_publica',1);
        $is_carpeta_noticia_destacada=my_get_request_value('is_carpeta_noticia_destacada',1);
        //print $is_carpeta_noticia_publica.'===='.$is_carpeta_noticia_destacada;exit();
        $res=db_query($sql);
        //$result=array();
        while($row=db_fetch_object($res)){
            //$result[]=$row;
            //print 'nid='.$row->nid;
            update_item_carpeta_publica_destacada($row->nid,$is_carpeta_noticia_publica,$is_carpeta_noticia_destacada);
        }
        if(is_show_borrar_carpeta_dinamica($my_query,$carpeta_dinamica_node)){
            update_carpeta_dinamica_publica_destacada($carpeta_dinamica_node->nid,$is_carpeta_noticia_publica,$is_carpeta_noticia_destacada);
        }


        //echo print_r($result,1);exit();
        //exit();
    }*/
}
function update_item_carpeta_publica_destacada($nid,$is_carpeta_noticia_publica,$is_carpeta_noticia_destacada){
    $sql="UPDATE content_type_item SET field_is_carpeta_noticia_publica_value=".$is_carpeta_noticia_publica.",field_is_carpeta_noticia_destaca_value=".$is_carpeta_noticia_destacada." WHERE nid=".$nid;
    db_query($sql);
}
function update_carpeta_dinamica_publica_destacada($nid,$is_carpeta_noticia_publica,$is_carpeta_noticia_destacada){
    $sql="UPDATE content_type_canal_busqueda SET field_is_busqueda_publica_value=".$is_carpeta_noticia_publica.",field_is_busqueda_destacada_value=".$is_carpeta_noticia_destacada." WHERE nid=".$nid;
    db_query($sql);
}
function get_carpeta_dinamica_field_boolean_value($carpeta_dinamica_node,$field){
    if(isset($carpeta_dinamica_node->nid) && !empty($carpeta_dinamica_node->nid)){
        //echo print_r($carpeta_dinamica_node,1);exit();
        $v='';
        //
        if(strcmp($field,'field_is_busqueda_publica')==0){
            $v=$carpeta_dinamica_node->field_is_busqueda_publica['0']['value'];
            if(empty($v)){
                $row_publica=get_content_type_canal_busqueda($carpeta_dinamica_node->nid,$carpeta_dinamica_node->vid);
                if(isset($row_publica->nid) && !empty($row_publica->nid)){
                    $v=$row_publica->field_is_busqueda_publica_value;
                }
            }
        }else if(strcmp($field,'field_is_busqueda_destacada')==0){
            $v=$carpeta_dinamica_node->field_is_busqueda_destacada['0']['value'];
            $row_publica=get_content_type_canal_busqueda($carpeta_dinamica_node->nid,$carpeta_dinamica_node->vid);
            if(isset($row_publica->nid) && !empty($row_publica->nid)){
                $v=$row_publica->field_is_busqueda_destacada_value;
            }
        }
        //
        if($v==1){
            return 1;
        }
    }
    return 0;
}
function get_content_type_canal_busqueda($nid,$vid){
    $where=array();
    $where[]="1";
    $where[]="nid=".$nid;
    $where[]="vid=".$vid;
    //
    $sql="SELECT * FROM content_type_canal_busqueda WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $result=array();
    $result=(object) $result;
    return $result;
}
function noticia_canal_home_callback(){
    $output='';
    $html=array();
    $nid=arg(1);
    hontza_og_access($nid);    
    if(!empty($nid)){
        //print $nid.'<BR>';
        $node=my_node_load($nid);
        $u=user_load($node->uid);
        //echo print_r($node,1);exit();
        drupal_set_title($node->title);
        //simulatzeko
        //$node->body='<p>prueba</p>';
        $username=$u->name;
        //print $node->uid.'<BR>';
        ///////////////////////////////////////////////
        $html[]='<div style="clear:both;" class="clearfix" id="content">';
        $html[]='<div class="node clearfix node-noticias-portada node-full published promoted not-sticky without-photo " id="node-'.$nid.'">';
        $html[]='<div class="meta">';
        //$html[]='<p>Submitted by '.$username.' on Thu, 2011-12-29 02:45</p>';
        $html[]='<p>Submitted by '.$username.' on '.format_date($node->created,'D, Y-m-d H:i').'</p>';
        $html[]='</div>';
        $html[]='<div class="content clearfix">';
        $html[]='<div class="field field-type-text field-field-noticias-portada-texto">';
        $html[]='<div class="field-items">';
        $html[]='<div class="field-item odd">';
        $html[]=$node->body;
        $html[]='</div>';
        $html[]='</div>';
        $html[]='</div>';
        $html[]='</div>';
        $html[]='</div>';
        $html[]='</div>';



        //////////////////////////////////////////////
    }
    $output=implode('',$html);
    return $output;
}
function my_node_load($nid){
    $where=array();
    $where[]="1";
    $where[]="n.nid=".$nid;
    $sql="SELECT n.*,nr.body FROM {node} n LEFT JOIN node_revisions nr ON n.nid=nr.nid WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function my_get_filemime_options(){
    $result=array();
    $result['']='';
    //
    /*$sql='SELECT DISTINCT(filemime) AS my_option FROM {files} WHERE 1 ORDER BY filemime ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        //echo print_r($row,1);exit();
        $result[$row->my_option]=$row->my_option;
    }*/
    $result["pdf"]="pdf";
    $result["image"]="image";
    $result["txt"]="txt";
    $result["excel"]="excel";    
    $result["powerpoint"]="powerpoint";
    $result["word"]="word";
    //echo print_r($result,1);
    return $result;
}
function destacar_item_callback(){
    if(is_show_destacar_link()){
        $nid=arg(1);
        //$node=node_load($nid);
        //echo print_r($node,1);exit();
        update_item_carpeta_destacada($nid,1);
        //return $nid;
        $html=array();
        $html[]=t('News added to Highlighted News');
        /*$destination=drupal_get_destination();
        $destination=str_replace('destination=','',$destination);
        $destination=urldecode($destination);
        $html[]=l(t('Return'),$destination,array('attributes' => array('class'=>'back_left')));*/
        $html[]=get_volver_link();
        return implode('<BR>',$html);
        //return implode('',$html);
    }else{
        drupal_access_denied();
        exit();
    }
}
function update_item_carpeta_destacada($nid,$is_carpeta_noticia_destacada){
    $sql="UPDATE content_type_item SET field_is_carpeta_noticia_destaca_value=".$is_carpeta_noticia_destacada." WHERE nid=".$nid;
    db_query($sql);
}
function get_destacar_link($node,$is_ajax=0){
    $row=my_get_content_type_item($node->nid,$node->vid);
    if(!empty($row) && isset($node->nid) && !empty($node->nid) && is_item_noticia_destacada($row)){
        $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Not Highlight'),'alt'=>t('Not Highlight'),'id'=>'id_destacar_'.$node->nid,'class'=>'a_no_destacar_ajax'));
        if($is_ajax){
            unset($params['query']);
        }
        $label='';
        //$label=t('Not Highlight');
        return l($label,'no_destacar_item/'.$node->nid,$params);            
    }
    //return l(t('Highlight'),'destacar_item/'.$node->nid,array('query'=>drupal_get_destination()));
    $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Highlight'),'alt'=>t('Highlight'),'id'=>'id_destacar_'.$node->nid,'class'=>'a_destacar_ajax'));    
    if($is_ajax){
        unset($params['query']);
    }
    $label='';
    //$label=t('Highlight');
    return l($label,'destacar_item/'.$node->nid,$params);
}
function no_destacar_item_callback(){
    if(is_show_destacar_link()){
        $nid=arg(1);

        update_item_carpeta_destacada($nid,0);

        $html=array();
        $html[]=t('News removed from Highlighted News');
        $html[]=get_volver_link();
        return implode('<BR>',$html);
        //return implode('',$html);
    }else{
        drupal_access_denied();
        exit();
    }
}
function is_item_noticia_destacada($content_type_item){
    if(!empty($content_type_item->field_is_carpeta_noticia_destaca_value)){
        return 1;
    }
    return 0;
}
function my_get_content_type_item($nid,$vid){
    $where=array();
    $where[]='c.nid='.$nid;
    $where[]='c.vid='.$vid;
    //
    $sql='SELECT c.* FROM {content_type_item} c WHERE '.implode(' AND ',$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function usuarios_estadisticas_callback(){
    return ideas_estadisticas_callback();
}
function my_get_usuarios_estadisticas_por_empresa_content(){
    global $user,$base_url;
    if(!hontza_grupo_shared_active_tabs_access(1)){
        return '';
    }
    //gemini-2014
    $my_grupo=og_get_group_context();
    if(isset($user->uid) && !empty($user->uid)){
        $html=array();
        $html[]='<div class="item-list">';
        $html[]='<ul class="views-summary">';
        //gemini-2014
        //$empresa_list=get_empresas_grupo_list();
        $empresa_list=hontza_get_empresas_grupo_list();
        if(count($empresa_list)>0){
            foreach($empresa_list as $i=>$e){
                //profile_values_profile_empresa_value
                $my_link=$e->profile_values_profile_empresa_value;
                //$my_link=l($my_link,'usuarios_estadisticas/'.$my_link);
                //$my_link=l($my_link,'usuarios_captacion_informacion/'.$my_link);
                if(isset($my_grupo->nid) && !empty($my_grupo->nid)){
                    $my_link=l($my_link,$base_url.'/'.$my_grupo->purl.'/usuarios_acceso/'.$my_link,array('absolute'=>TRUE));
                }else{    
                    $my_link=l($my_link,'usuarios_acceso/'.$my_link);
                }
                $html[]='<li>'.$my_link.' ('.$e->num_records.')</li>';
            }
        }
        $html[]='</ul>';
        $html[]='</div>';
        return implode('',$html);
    }    
    return '';
}
function get_empresas_grupo_list(){
    //AVISO::::ya no se utiliza se utiliza hontza_get_empresas_grupo_list
    $result=array();
    return $result;
    /*$result=array();
    //
    $where=array();
    $where[]="1";
    $my_grupo=og_get_group_context();
    if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
  	$where[]="(og_uid.nid = ".$my_grupo->nid.")";
    }
    $where[]="users.status <> 0";
    //
    $sql="SELECT profile_values_profile_empresa.value AS profile_values_profile_empresa_value
    , COUNT(users.uid) AS num_records
    FROM {users} users LEFT JOIN {og_uid} og_uid ON users.uid = og_uid.uid
    LEFT JOIN {profile_values} profile_values_profile_empresa ON users.uid = profile_values_profile_empresa.uid AND profile_values_profile_empresa.fid = '3'
    WHERE ".implode(" AND ",$where)."
    GROUP BY profile_values_profile_empresa_value ORDER BY profile_values_profile_empresa_value ASC";
    //print $sql;
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }

    return $result;*/
}
function is_usuarios_estadisticas($with_empresa=0,$konp='usuarios_estadisticas'){
    if(strcmp(arg(0),$konp)==0){
        if($with_empresa){
            if(strcmp(arg(1),'todos')==0){
                return 0;
            }
        }
        return 1;
    }
    return 0;
}
function set_leido_interesante_nuevo_comentario(&$a1){
    $nid = $a1['nid'];
    $node=node_load($nid);
    if(isset($node->nid) && !empty($node->nid)){
        if(in_array($node->type,array('item','noticia'))){
            $leido=get_leido_interesante($nid);
            if(!isset($leido->fcid) || empty($leido->fcid)){
                insert_leido_interesante($nid);
            }
        }
    }
}
function get_leido_interesante($nid){
    $where=array();
    $where[]="1";
    $where[]="fc.content_type='node'";
    $where[]="fc.content_id=".$nid;
    $where[]="f.name='leido_interesante'";
    $sql="SELECT fc.*
    FROM {flag_content} fc
    LEFT JOIN {flags} f ON fc.fid=f.fid
    WHERE ".implode(" AND ",$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function insert_leido_interesante($nid){
    global $user;
    //
    $flag_row=get_flag_row('leido_interesante');
    if(isset($flag_row->fid) && !empty($flag_row->fid)){
        $fid=$flag_row->fid;
        $sql="INSERT INTO flag_content(fid,content_type,content_id,uid,timestamp) VALUES(".$fid.",'node',".$nid.",".$user->uid.",".time().")";
        db_query($sql);
    }
}
function get_flag_row($name){
     $where=array();
    $where[]="1";
    $where[]="f.name='".$name."'";
    $sql="SELECT f.*
    FROM {flags} f 
    WHERE ".implode(" AND ",$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function my_show_breadcrumb($breadcrumb){
    if(hontza_is_user_anonimo()){
        return '';
    }
    if(strcmp(arg(0),'criterios_de_evaluacion')==0){
        return '';
    }
    $html=array();
    $padding_hasi=15;
    $my_padding='50';
    $html[]='<fieldset id="my_camino">';
    $html[]='<legend class="my_camino_legend">'.get_camino_title().'</legend>';
    $html[]='<div>';
    if(is_despliegue()){
        $estrategia_nid='';
        if(strcmp(arg(0),'despliegues')==0 || is_node_add()){
            $estrategia_nid=my_get_despliegue_estrategia_nid();
             if(!empty($estrategia_nid)){
                $my_value=get_estrategia_camino_html($estrategia_nid,$padding_hasi,$my_padding);
                if(empty($my_value)){
                   return '';
                }
                $html[]=$my_value;
             }
        }else{
            $node=my_get_node();
            $estrategia_nid=$node->estrategia_nid;
             if(!empty($estrategia_nid)){
                $html[]=get_estrategia_camino_html($estrategia_nid,$padding_hasi,$my_padding);
                $html[]=get_despliegue_camino_html($node->nid,$padding_hasi,$my_padding);
             }
        }
        //

        $html[]='</div>';
        $html[]='</fieldset>';
        print implode('',$html);
    }else if(is_decision()){
        $despliegue_nid='';
        $bakarra=0;
        if(strcmp(arg(0),'decisiones')==0 || is_node_add()){
            $despliegue_nid=my_get_decision_despliegue_nid();

        }else{
            $node=my_get_node();
            $despliegue_nid=$node->despliegue_nid;
            $bakarra=1;
        }
        if(!empty($despliegue_nid)){
            $despliegue=node_load($despliegue_nid);
            if(isset($despliegue->nid) && !empty($despliegue->nid)){
                $html[]=get_estrategia_camino_html($despliegue->estrategia_nid,$padding_hasi,$my_padding);
                $html[]=get_despliegue_camino_html($despliegue->nid,$padding_hasi,$my_padding);
                if($bakarra){
                    $html[]=get_decision_camino_html($node->nid,$padding_hasi,$my_padding);
                }
            }else{
                return '';
            }
        }
        $html[]='</div>';
        $html[]='</fieldset>';
        print implode('',$html);
    }else if(is_informacion()){
        $decision_nid='';
        $bakarra=0;
        if(strcmp(arg(0),'informaciones')==0 || is_node_add()){
            $decision_nid=my_get_informacion_decision_nid();            
        }else{
            $node=my_get_node();
            $decision_nid=$node->decision_nid;
            $bakarra=1;
        }
        //
        if(!empty($decision_nid)){
            $decision=node_load($decision_nid);
            if(isset($decision->nid) && !empty($decision->nid)){
                /*$html[]=get_estrategia_camino_html($despliegue->estrategia_nid,$padding_hasi,$my_padding);
                $html[]=get_despliegue_camino_html($despliegue->nid,$padding_hasi,$my_padding);
                if($bakarra){
                    $html[]=get_decision_camino_html($node->nid,$padding_hasi,$my_padding);
                }*/
                $despliegue=node_load($decision->despliegue_nid);
                if(isset($despliegue->nid) && !empty($despliegue->nid)){
                    $html[]=get_estrategia_camino_html($despliegue->estrategia_nid,$padding_hasi,$my_padding);
                    $html[]=get_despliegue_camino_html($despliegue->nid,$padding_hasi,$my_padding);
                    $html[]=get_decision_camino_html($decision_nid,$padding_hasi,$my_padding);
                    if($bakarra){
                         $html[]=get_link_camino_html($node->nid,$padding_hasi,$my_padding,3);
                    }
                }
            }else{
                return '';
            }
        }
        $html[]='</div>';
        $html[]='</fieldset>';
        print implode('',$html);
    }else if(is_oportunidad() || is_proyecto()){
        $my_array=array('oportunidad','proyecto');
        if(!empty($my_array)){
            foreach($my_array as $i=>$node_type){
                if(is_ficha_node($node_type)){
                    return '';
                }
            }
        }
        $my_value=my_show_breadcrumb_idea($padding_hasi,$my_padding);
        if(empty($my_value)){
            return '';
        }
        $html[]=$my_value;
        $html[]='</div>';
        $html[]='</fieldset>';
        print implode('',$html);
    }
    
    return 1;
}
function get_estrategia_camino_html($estrategia_nid,$padding_hasi,$my_padding){
    /*$html=array();
    $estrategia=node_load($estrategia_nid);
            if(isset($estrategia->nid) && !empty($estrategia->nid)){
                $html[]='<div style="padding-left:'.$padding_hasi.'px;">'.l($estrategia->title,'node/'.$estrategia->nid).'</div>';
            }
    return implode('',$html);*/
    return get_link_camino_html($estrategia_nid,$padding_hasi,$my_padding,0);
}
function get_despliegue_camino_html($despliegue_nid,$padding_hasi,$my_padding){
    /*$html=array();
    $padding=$padding_hasi+$my_padding*2;
    $node=node_load($despliegue_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $html[]='<div style="padding-left:'.$padding.'px;">'.l($node->title,'node/'.$node->nid).'</div>';
            }
    return implode('',$html);*/
    return get_link_camino_html($despliegue_nid,$padding_hasi,$my_padding,1);
}
function get_decision_camino_html($decision_nid,$padding_hasi,$my_padding){
    /*$html=array();
    $padding=$padding_hasi+$my_padding*3;
    $node=node_load($decision_nid);
            if(isset($node->nid) && !empty($node->nid)){
                $html[]='<div style="padding-left:'.$padding.'px;">'.l($node->title,'node/'.$node->nid).'</div>';
            }
    return implode('',$html);*/
    return get_link_camino_html($decision_nid,$padding_hasi,$my_padding,2);
}
function get_link_camino_html($nid,$padding_hasi,$my_padding,$bider){
    $html=array();
    $padding=$padding_hasi+$my_padding*$bider;
    $node=node_load($nid);
            if(isset($node->nid) && !empty($node->nid)){
                $title=estrategia_set_title_max_len($node->title);
                $html[]='<div style="padding-left:'.$padding.'px;">'.get_node_simbolo_img($nid).l($title,'node/'.$node->nid).'</div>';
            }
    return implode('',$html);
}
function my_show_breadcrumb_idea($padding_hasi,$my_padding){
    $html=array();
    if(is_oportunidad()){
        $idea_nid='';
        $bakarra=0;
        if(strcmp(arg(0),'oportunidades')==0 || is_node_add()){
            $idea_nid=my_get_oportunidad_idea_nid();

        }else{
            $node=my_get_node();
            $idea_nid=$node->idea_nid;
            $bakarra=1;
        }
        if(!empty($idea_nid)){
                $html[]=get_link_camino_html($idea_nid,$padding_hasi,$my_padding,0);
                if($bakarra){
                    $html[]=get_link_camino_html($node->nid,$padding_hasi,$my_padding,1);
                }

        }         
    }else if(is_proyecto()){
        $oportunidad_nid='';
        $bakarra=0;
        if(strcmp(arg(0),'proyectos')==0 || is_node_add()){
            $oportunidad_nid=my_get_proyecto_oportunidad_nid();

        }else{
            $node=my_get_node();
            $oportunidad_nid=$node->oportunidad_nid;
            $bakarra=1;
        }
        if(!empty($oportunidad_nid)){               
             $oportunidad=node_load($oportunidad_nid);
             $idea_nid=$oportunidad->idea_nid;
             if(!empty($idea_nid)){
                $html[]=get_link_camino_html($idea_nid,$padding_hasi,$my_padding,0);
                $html[]=get_link_camino_html($oportunidad_nid,$padding_hasi,$my_padding,1);
                if($bakarra){
                    $html[]=get_link_camino_html($node->nid,$padding_hasi,$my_padding,2);
                }
             }
        }
    }
    return implode('',$html);
}
function is_node_add($node_type=''){
    if(strcmp(arg(0),'node')==0 && strcmp(arg(1),'add')==0){
        if(empty($node_type)){
            return 1;
        }else if(strcmp(arg(2),$node_type)==0){
            return 1;
        }
    }
    return 0;
}
function publicar_noticia_usuario_callback(){
    $nid=arg(1);
    update_noticia_publicada($nid, 1);
    //return t('User News published');
    $html=array();
    $html[]=t('User News published');
    $html[]=get_volver_link();
    return implode('<BR>',$html);
}
function destacar_noticia_usuario_callback(){
    hontza_destacar_access_denied();
    $nid=arg(1);
    update_noticia_destacada($nid, 1);
    //return t('Noticia de usuario destacada');
    $html=array();
    $html[]=t('User News added to Highlighted News');
    $html[]=get_volver_link();
    return implode('<BR>',$html);
}
function update_noticia_destacada($nid, $v){
    $sql="UPDATE content_type_noticia SET field_is_noticia_usuario_destaca_value=".$v." WHERE nid=".$nid;
    db_query($sql);
}
function update_noticia_publicada($nid, $v){
    $sql="UPDATE content_type_noticia SET field_is_noticia_usuario_publica_value=".$v." WHERE nid=".$nid;
    db_query($sql);
}
function get_noticia_publicar_link($node){
    $row=my_get_content_type_noticia($node->nid,$node->vid);
    if(!empty($row) && isset($node->nid) && !empty($node->nid) && is_noticia_usuario_publicada($row)){
        return l(t('Not Public'),'no_publicar_noticia_usuario/'.$node->nid);
    }
    return l(t('Publish'),'publicar_noticia_usuario/'.$node->nid);
}
function get_noticia_destacar_link($node,$is_ajax=0){
    $row=my_get_content_type_noticia($node->nid,$node->vid);
    if(!empty($row) && isset($node->nid) && !empty($node->nid) && is_noticia_usuario_destacada($row)){
        $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Not Highlight'),'alt'=>t('Not Highlight'),'id'=>'id_destacar_noticia_usuario_'.$node->nid,'class'=>'a_no_destacar_noticia_usuario'));
        if($is_ajax){
            unset($params['query']);
        }
        //intelsat-2015
        $label='';
        //$label=t('Not Highlight');
        //
        return l($label,'no_destacar_noticia_usuario/'.$node->nid,$params);
    }
    $params=array('query'=>drupal_get_destination(),'attributes'=>array('title'=>t('Highlight'),'alt'=>t('Highlight'),'id'=>'id_destacar_noticia_usuario_'.$node->nid,'class'=>'a_destacar_noticia_usuario'));
    if($is_ajax){
        unset($params['query']);
    }
    $label='';
    //$label=t('Highlight');
    return l($label,'destacar_noticia_usuario/'.$node->nid,$params);
}
function my_get_content_type_noticia($nid,$vid){
    $where=array();
    $where[]='c.nid='.$nid;
    $where[]='c.vid='.$vid;
    //
    $sql='SELECT c.* FROM {content_type_noticia} c WHERE '.implode(' AND ',$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function is_noticia_usuario_destacada($content_type_noticia){
    if(!empty($content_type_noticia->field_is_noticia_usuario_destaca_value)){
        return 1;
    }
    return 0;
}
function is_noticia_usuario_publicada($content_type_noticia){
    if(!empty($content_type_noticia->field_is_noticia_usuario_publica_value)){
        return 1;
    }
    return 0;
}
function no_publicar_noticia_usuario_callback(){
    $nid=arg(1);
    update_noticia_publicada($nid, 0);
    //return t('User News unpublished');
    $html=array();
    $html[]=t('User News unpublished');
    $html[]=get_volver_link();
    return implode('<BR>',$html);
}
function no_destacar_noticia_usuario_callback(){
    hontza_destacar_access_denied();
    $nid=arg(1);
    update_noticia_destacada($nid, 0);
    //return t('Noticia de usuario no destacada');
    $html=array();
    $html[]='Noticia de usuario eliminada de Noticias Destacadas';
    $html[]=get_volver_link();
    return implode('<BR>',$html);
}
function get_node_simbolo_img($nid){
    $node=node_load($nid);
    //
    if(isset($node->nid) && !empty($node->nid)){
        //print 'type='.$node->type.'<BR>';
        if(strcmp($node->type,'idea')==0){
            return get_idea_bombilla_img();
        }else if(strcmp($node->type,'oportunidad')==0){
            return get_oportunidad_simbolo_img();
        }else if(strcmp($node->type,'proyecto')==0){
            return get_proyecto_simbolo_img();
        }else if(strcmp($node->type,'estrategia')==0){
            return get_estrategia_simbolo_img();
        }else if(strcmp($node->type,'despliegue')==0){
            return get_despliegue_simbolo_img();
        }else if(strcmp($node->type,'decision')==0){
            return get_decision_simbolo_img();
        }else if(strcmp($node->type,'informacion')==0){
            return get_informacion_simbolo_img();
        }
    }
    //
    return '';
}
function my_create_boton_volver($url){
    $html=array();
    $html[]='<div class="back_estrategia">';
    $html[]=l(t('Return'),$url,array('attributes'=>array('class'=>'back')));
    $html[]='</div>';
    return implode('',$html);
}
function my_set_notica_node_form_alter(&$form,&$form_state,$form_id){
   drupal_set_title(t('Create User News'));
   if(isset($form['#node']->field_cuando[0]['value']) && !empty($form['#node']->field_cuando[0]['value'])){
        $date_array=set_date_array($form['#node']->field_cuando[0]['value']);
   }else{
        //$date_array=set_date_array(time());
       $date_array=array();
   }
   //
    unset($form['field_cuando']);
    $form['field_cuando'] = array(
    '#type' => 'date',
    '#title' => t('When'),
    '#weight'=>30,
    '#default_value' => $date_array
  );
  //
  $form['title']['#title']=t('Title');
  //

   $form['field_quien'][0]['#title']=t('Who');
   $form['field_donde'][0]['#title']=t('Where');   
   $form['body_field']['body']['#title']=t('What');
   $form['field_fiabilidad']['#title']=t('Reliability');   
   //
   if(isset($form['taxonomy']['tags'][3])){
    $form['taxonomy']['tags'][3]['#title']=t('Tags');
   }
   //$form['field_enlace_noticia'][0]['#title']=t('Link');
   $form['field_enlace_noticia'][0]['#title']=t('Original news');
   //intelsat-2015
   $nid=hontza_get_nid_by_form($form);
   //$node=hontza_get_node_by_form($form);
   //$nid=$node->nid;
   $form['my_cat_']=create_categorias_tematicas_fieldset('',1,$nid,'noticia');
   //
   //intelsat-2015
   if(red_solr_inc_is_noticia_usuario_source_type_activado($form_id)){
       //red_solr_inc_field_item_source_tid_form_alter($form,$form_state, $form_id,$nid);
       red_despacho_node_add_source_type_form_field($form,$form_id,$nid);
   }
   $form['#content_extra_fields']['title']['weight']=-12;
   $form['field_quien']['#weight']=-11;
   $form['field_donde']['#weight']=-10;
   $form['field_cuando']['#weight']=-9;
   $form['#content_extra_fields']['body_field']['weight']=-8;
   $form['field_fiabilidad']['#weight']=-7;
   $form['taxonomy']['#weight']=-4;
   //
   unset($form['field_is_noticia_usuario_publica']);
   unset($form['field_is_noticia_usuario_destaca']);
   //gemini-2014
   red_noticia_node_form_alter($form, $form_state, $form_id);
   //
   if(hontza_canal_rss_is_visualizador_activado()){
       publico_vigilancia_set_noticia_node_form_alter($form, $form_id);
       $form['#validate'][] = 'publico_vigilancia_noticia_validate';
   }
   //intelsat-2015
   red_movil_vigilancia_notica_node_form_alter($form,$form_state,$form_id);
   //intelsat-2016
   hound_noticia_email_notica_node_form_alter($form,$form_state,$form_id);
   //intelsat-2015
   $form['title']['#maxlength']=1024;
 $form['#submit'][]='my_noticia_usuario_form_submit';
}
function my_noticia_usuario_form_submit(&$form, &$form_state) {        
    $cuando_array=$form_state['values']['field_cuando'];
    $form_state['values']['field_cuando']=array();
    $form_state['values']['field_cuando'][0]['value']=my_mktime($cuando_array);
}
function evaluar_doc_form(){
    $fid=arg(1);
    $row=get_evaluacion_doc($fid);
    $v=0;
    //
    if(isset($row->id) && !empty($row->id)){
        $v=$row->evaluacion;
    }
    //
    $form['evaluacion']=array(
  '#type' => 'select',
  '#title' => t('Rating'),
  '#default_value' => $v,
  '#options'=>my_get_evaluacion_options('','evaluar_doc'),
  '#required' => TRUE
);
    //
    $r=get_my_doc($fid);
    //
        if(isset($r->fid) && !empty($r->fid) ){
            //echo print_r($r,1);
            $u=user_load($r->uid);
            $form['usuario']['#value']=create_read_item_form(t('User'),'usuario',$u->name);
            $form['nombre']['#value']=create_read_item_form(t('Name'),'nombre',l($r->filename,hontza_get_url_file($r->filepath),array('attributes'=>array('target'=>'_blank','absolute'=>TRUE))));
            $my_node=get_file_node($r);
            $informacion_relacionada='';
            $tipo='';
            if(!empty($my_node) && isset($my_node->nid) && !empty($my_node->nid)){
                $informacion_relacionada=$my_node->title;
                $tipo=$my_node->type;
            }            
            $form['informacion_relacionada']['#value']=create_read_item_form(t('Information title'),'informacion_relacionada',$informacion_relacionada);
            //
            $comentario='';
            $c=_comment_load($r->cid);
            if(!empty($c) && isset($c->cid) && !empty($c->cid)){
                $comentario=l($c->subject,'node/'.$r->comment_nid,array('fragment'=>'comment-'.$r->cid));
            }
            $form['comentario']['#value']=create_read_item_form(t('Comment'),'comentario',$comentario);
            $form['tipo']['#value']=create_read_item_form(t('Type'),'tipo',$tipo);
            $form['fecha']['#value']=create_read_item_form(t('Date'),'fecha',date('d-m-Y H:i',$r->timestamp));
            //
        }
    //
    $form['evaluar_doc_submit']=array(
  '#type' => 'submit',
  '#value' => t('Save'),
);
  //$form['volver_btn']['#value']=l(t('Return'),'my_doc_list',array('attributes' => array('class'=>'back_left')));
  $form['volver_btn']['#value']=l(t('Cancel'),'my_doc_list');


    return $form;
}
function evaluar_doc_form_submit(&$form, &$form_state) {
    $fid=arg(1);
    //print $fid;exit();
    $row=get_evaluacion_doc($fid);
    //echo print_r($row,1);exit();
    $v=$form_state['values']['evaluacion'];
    if(isset($row->id) && !empty($row->id)){
        update_evaluacion_doc($row->id,$v);
    }else{
        insert_evaluacion_doc($fid,$v);
    }
    drupal_set_message(t('Assessed Document'));
    drupal_goto('my_doc_list');
}
function get_evaluacion_doc($fid=''){
    $result=array();
    $where=array();
    $where[]='1';
    if(!empty($fid)){
        $where[]='ed.fid='.$fid;
    }
    $sql='SELECT ed.* FROM {evaluacion_doc} ed WHERE '.implode(' AND ',$where).' ORDER BY id ASC';
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    //
    if(!empty($fid)){
        return $result[0];
    }
    //
    return $result;
}
function insert_evaluacion_doc($fid,$v){
    $sql='INSERT INTO evaluacion_doc(fid,evaluacion) VALUES('.$fid.','.$v.')';
    db_query($sql);
}
function update_evaluacion_doc($id,$v){
    $sql='UPDATE evaluacion_doc SET evaluacion='.$v.' WHERE id='.$id;
    db_query($sql);
}
function get_my_doc($fid){
    $headers=array();
    //AVISO:::: $is_manual_filemime y $filter son parametros out    
    $sql=get_my_doc_list_sql($fid,$headers,$is_manual_filemime,$filter);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=array();
    $my_result=(object) $my_result;
    return $my_result;
}
function create_read_item_form($label,$field,$v){
    $html=array();
    $html[]='<div id="edit-'.$field.'-wrapper" class="form-item">';
    $html[]='<label for="edit-'.$field.'">'.$label.':</label>';
    $html[]=$v;
    $html[]='</div>';
    return implode('',$html);
}
function is_estadisticas(){
    if(strcmp(arg(0),'estadisticas')==0){
        return 1;
    }
    return 0;
}
function my_help_estadisticas(){
    $param=arg(1);
    //
    if(strcmp(arg(0),'estadisticas')==0 && empty($param)){
        return help_popup_window(15319, 'help',my_get_help_link_object());
    }
    //
    return '';
}
function is_docs(){
    if(strcmp(arg(0),'my_doc_list')==0){
        return 1;
    }
    if(strcmp(arg(0),'evaluar_doc')==0){
        return 1;
    }
    return 0;
}
function my_help_docs(){
    $param=arg(1);
    //
    if(strcmp(arg(0),'my_doc_list')==0 && empty($param)){
        return help_popup_window(15320, 'help',my_get_help_link_object());
    }else if(strcmp(arg(0),'evaluar_doc')==0){
        return help_popup_window(15321, 'help',my_get_help_link_object());
    }
    //
    return '';
}
function my_help_area_trabajo(){
    $param=arg(1);
    //
    if(strcmp(arg(0),'area-trabajo')==0 && empty($param)){
        return help_popup_window(15324, 'help',my_get_help_link_object());
    }
    //
    return '';
}
function my_help_area_debate(){
    $param=arg(1);
    //
    if(strcmp(arg(0),'area-debate')==0 && empty($param)){
        return help_popup_window(15325, 'help',my_get_help_link_object());
    }
    //
    return '';
}
function my_help_usuarios_estadisticas(){
    return help_popup_window(15339, 'help',my_get_help_link_object());
}
function hontza_comment(&$a1, $op) {
  //if ($op == 'insert' || $op == 'update') {
  if ($op == 'insert') {
    //AVISO::::al insertar un nuevo comentario, si se quiere dar por validado directamente, descomentar esta funcion
    //set_leido_interesante_nuevo_comentario($a1);

    $nid = $a1['nid'];
    $node=node_load($nid);
    alerta_notif_save($node,'comment','insert',$a1['cid']);
    if(hontza_is_comment_enlace_nid_post()){
        hontza_comment_enlace_origen_save($a1['cid'],$_POST['enlace_nid']);        
    }
  }else if($op=='update'){
    $nid = $a1['nid'];
    $node=node_load($nid);
    alerta_notif_save($node,'comment','update',$a1['cid']);
  }

 // cache_clear_all_like(drupal_url(array('id' => $nid)));

}
function quitar_mi_perfil_notifications_and_messages($param){
    $result=$param;
    if(is_mi_perfil()){
        print '#############################################';
        print $result;exit();
        print '#############################################';
    }
    return $result;
}
function get_noticia_destacada_user($row){
  if(isset($row->uid) && !empty($row->uid)){
    $my_user=user_load($row->uid);
  }else{
    $uid='';
    if(isset($row->ct_canal_nid) && !empty($row->ct_canal_nid)){
        $my_node=node_load($row->ct_canal_nid);
        $uid=$my_node->uid;
    }
    if(isset($row->ct_canal_yql_nid) && !empty($row->ct_canal_yql_nid)){
        $my_node=node_load($row->ct_canal_yql_nid);
        $uid=$my_node->uid;
    }
    //
    if(!empty($uid)){
        $my_user=user_load($uid);
    }
  }
  if(isset($my_user) && isset($my_user->uid) && !empty($my_user->uid)){
      return $my_user;
  }
  //
  $my_result=array();
  $my_result=(object) $my_result;
  return $my_result;
}
function get_noticia_destacada_user_image($row){
  $my_user=get_noticia_destacada_user($row);
  //if(isset($my_user->uid) && !empty($my_user->uid)){
    $is_mail=1;
    return my_get_user_img_src('', $my_user->picture, $my_user->name, $my_user->uid, $is_mail);
  //}
  //
  return '';
}
function set_administrador_de_grupo_role(&$node){
    if(strcmp($node->type,'grupo')==0){
        //echo print_r($node,1);exit();
        $uid=$node->field_admin_grupo_uid[0]['value'];        
        if(!empty($uid) && !is_array($uid)){
            $role=get_role_by_name('Administrador de Grupo');
            if(isset($role->rid) && !empty($role->rid)){
                $rid=$role->rid;
                $my_user=user_load($uid);
                if(isset($my_user->uid) && !empty($my_user->uid)){
                   $role_keys=array_keys($my_user->roles);
                   if(!in_array($rid,$role_keys) && $uid!=1){
                       my_role_save($uid,$rid);
                   }
                   if(!empty($node->old_admin_uid) && $node->old_admin_uid!=$uid){
                       if(!is_beste_admin_grupo($node->nid,$node->old_admin_uid)){
                           my_role_delete($node->old_admin_uid,$rid);
                       }
                   }
                }
            }
        }
    }
}
function get_role_by_name($name,$rid=''){
    $where=array();
    $where[]="1";
    if(!empty($name)){
        $where[]="r.name='".$name."'";
    }
    if(!empty($rid)){
        $where[]="r.rid=".$rid;
    }
    $sql="SELECT * FROM {role} r WHERE ".implode(" AND ",$where);
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $result=array();
    $result=(object) $result;
    return $result;
}
function my_role_save($uid,$rid){
    $sql=sprintf('INSERT INTO {users_roles} (uid, rid) VALUES (%d, %d)', $uid, $rid);
    db_query($sql);
}
function my_role_delete($uid,$rid){
    $sql='DELETE FROM {users_roles} WHERE uid='.$uid.' AND rid='.$rid;
    db_query($sql);
}
function is_beste_admin_grupo($nid,$uid){
    $where=array();
    $where[]="1";
    $where[]="n.type='grupo'";
    $where[]="n.nid!=".$nid;
    $sql="SELECT n.* FROM {node} n WHERE ".implode(" AND ",$where);
    //
    $res=db_query($sql);
    while($row=db_fetch_object($res)){
        //print 'row_nid='.$row->nid.'<BR>';
        if(is_permiso_gestion_boletin_grupo($row->nid,0,$uid)){
            return 1;
        }
    }
    return 0;
}
function my_presave_grupo(&$node){
  //echo print_r($node,1);exit();  
  if(isset($node->field_admin_grupo_uid[0]) && isset($node->field_admin_grupo_uid[0]['value'])){
    /*if(empty($node->field_admin_grupo_uid[0]['value'])){
        //$node->field_admin_grupo_uid[0]['value']=1;
        unset($node->field_admin_grupo_uid);
    }*/
  }else{
    $v=$node->field_admin_grupo_uid[0];
    $node->field_admin_grupo_uid=array();
    $node->field_admin_grupo_uid[0]['value']=$v;
  }
  //   
}
function link_edit_canal($nid){
    global $user,$base_url;
    //intelsat-2015
    if(hontza_solr_search_is_usuario_lector()){
        return '';
    }
    //
    $label=t('Edit channel');
    $html=array();
    $html[]= '<span class="link_edit_canal">';
    $node=node_load($nid);
    $link='';
    $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/edit_canal.png';
    $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';        
    
    if(isset($node->nid) && !empty($node->nid)){
        $link=  l($img, 'node/'. $nid .'/edit',array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));
    }else{
        if($user->uid==1){
            $link=  l($img, 'borrar_items_canal_no_existe/'.$nid,array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));
        }else{
            return '';
        }
    }
    $html[]=$link;        
    $html[]= '</span>';
    return implode('',$html);
}
function link_ver_canal($nid){
    global $user,$base_url;
    $label=t('Channel file');
    $html=array();
    $html[]= '<span class="link_ver_canal">';
    $node=node_load($nid);
    $link='';
    $icon=$base_url.'/'.drupal_get_path('theme','buho').'/images/canal_view.png';    
    $img='<img class="icono_validar_pagina" src="'.$icon.'" title="'.$label.'" alt="'.$label.'"/>';        
    
    if(isset($node->nid) && !empty($node->nid)){
        $html[]=  l($img, 'node/'. $nid,array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));
     }else{
        if($user->uid==1){
            $html[]=  l($img, 'borrar_items_canal_no_existe/'.$nid,array('html'=>TRUE,'query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_validar_pagina')));
        }else{
            return '';
        }
    }
    
    $html[]= '</span>';
    return implode('',$html);
}
function my_noticia_node_form_alter(&$form,&$form_state,$form_id){
    echo print_r($form,1);exit();
}
function my_gestion_items_canal($data,$is_link=1){
    $node=node_load($data->nid);
    if(isset($node->nid) && !empty($node->nid)){
        $canal_nid=$node->field_item_canal_reference[0]['nid'];
        if(!empty($canal_nid)){
            $canal_node=node_load($canal_nid);
            if(isset($canal_node->nid) && !empty($canal_node->nid)){
                //return $canal_node->title;
                return l($canal_node->title,'node/'.$canal_node->nid,array('query'=>drupal_get_destination()));
            }
        }
    }
    return '';
}
function my_get_rows_gestion_items($vars_in){
    $vars=$vars_in;
    $sep='<th class="views-field views-field-phpcode">';
    $my_array=explode($sep,$vars['rows']);
    if(count($my_array)>1){
        $pos=strpos($my_array[1], '</th>');
        if($pos===FALSE){
            //
        }else{
            $my_sort='asc';
            $order=my_get_request_value('order');
            $sort=my_get_request_value('sort');
            if(strcmp($order,'canales')==0 && strcmp($sort,'asc')==0){
                $my_sort='desc';
            }
            $beste=substr($my_array[1],$pos);
            $img='';
            if(strcmp($order,'canales')==0){
                $img='<img width="13" height="13" title="'.t('sort '.$my_sort.'ending').'" alt="'.t('sort icon').'" src="/misc/arrow-'.$my_sort.'.png">';
            }
            $my_array[1]=l(t('Channels').$img,'panel_admin/items',array('html'=>true,'query'=>'order=canales&sort='.$my_sort)).$beste;
        }
    }
    $vars['rows']=implode($sep,$my_array);
    //intelsat-2015
    $vars['rows']=hontza_canal_rss_my_get_rows_gestion_items($vars);
    //
    return $vars['rows'];
}
function is_gestion_items(){
    if(strcmp(arg(0),'gestion')==0 && strcmp(arg(1),'items')==0){
        return 1;
    }
    return 0;
}
function create_gestion_items_order_array($my_list_in){
    $result=$my_list_in;
    $f='canal_name';
    if(count($result)>0){
        
        foreach($result as $i=>$row){
            $v=my_gestion_items_canal($row,0);
            $result[$i]->$f=$v;
        }
    }


	$info['field']=$f;
	$info['my_list']=$result;
	
	return $info;
}
function my_get_star_texto_options($my_type,$with_all=1){
    $result=array();
    switch($my_type){
        case 'calidad':
            $result['All']='<'.t('Any').'>';
            $result[20]='1='.t('Very low');
            $result[40]='2='.t('Low');
            $result[60]='3='.t('Normal');
            $result[80]='4='.t('High');
            $result[100]='5='.t('Very high');
            break;
        case 'exahustivo':
            $result['All']='<'.t('Any').'>';
            $result[20]='1='.t('Very low');
            $result[40]='2='.t('Low');
            $result[60]='3='.t('Normal');
            $result[80]='4='.t('Good');
            $result[100]='5='.t('Very good');
            break;
        case 'actualizacion':
            $result['All']='<'.t('Any').'>';
            $result[20]='1='.t('Very slow');
            $result[40]='2='.t('Slow');
            $result[60]='3='.t('Normal');
            $result[80]='4='.t('Quick');
            $result[100]='5='.t('Very fast');
            break;
    }
    if(!$with_all){
        $result=hontza_unset_all_option($result);
    }
    return $result;
}
function my_get_gestion_noticias_publicas_content(){
//gemini-2014
//if(hontza_is_sareko_id_red()){
//intelsat-2015
if(hontza_is_sareko_id_red() && !hontza_is_sareko_id('ROOT')){    
    return '';
}
//intelsat-2015
if(user_access('Ver my_noticias_publicas')){
$content =l(t('Add Public News'), 'node/add/noticias-portada') .'<br>'.
                             l(t('List of Public News'), 'gestion/my_noticias_publicas').'<br>'.
                             l(t('Add Highlighted News'), 'node/add/rss-feed') .'<br>'.
                             l(t('List of Highlighted News'), 'gestion/my_noticias_destacadas').'<br>'.
                             l(t('Settings'), 'admin/content/hontza/settings');
return $content;
}
return '';
}
function my_noticias_publicas_callback(){
    $output='';
    //
    $my_limit=variable_get('default_nodes_main', 20);
    //
    $headers=array();
    $headers[]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    $headers[]=t('News');
    $headers[]=t('Date');
    $headers[]=t('Published');
    $headers[]=t('Actions');
    //
    $item_list=my_get_noticias_publicas_canales_content(0);
    //
    //$output=create_div_view_header_my_noticias_publicas();
    //
    if(count($item_list)>0){
        $rows=array();
        foreach($item_list as $i=>$row){
            $rows[$kont]=array();
            $rows[$kont][0]='<input type="checkbox" id="txek_'.$row->nid.'" name="txek_nid['.$row->nid.']" class="bulk_txek" value="1">';
            $rows[$kont][1]=l($row->node_title,'node/'.$row->nid);
            //intelsat-2015
            //$rows[$kont][2]=date('H:i d-m-Y',$row->node_created);
            $rows[$kont][2]=date('H:i d-m-Y',$row->node_changed);            
            //
            $is_por_su_canal=get_is_por_su_canal($row);
            $rows[$kont][3]=set_publicado_value($row,$is_por_su_canal);
            $rows[$kont][4]=array('data'=>get_acciones_my_noticias_publicas($row,$is_por_su_canal),'style'=>'white-space:nowrap');
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no public news'). '</div>';
    }

    drupal_set_title(t('List of Public News'));
    return $output;
}
function get_acciones_my_noticias_publicas($r,$is_por_su_canal,$is_destacada=0){
    $html=array();
    $html[]=l(my_get_icono_action('edit',t('Edit')),'node/'.$r->nid.'/edit',array('query'=>drupal_get_destination(),'html'=>TRUE));
    $html[]=l(my_get_icono_action('delete',t('Delete')),'node/'.$r->nid.'/delete',array('query'=>drupal_get_destination(),'html'=>TRUE));
    //$html[]=panel_admin_items_define_accion_publish($r,drupal_get_destination());
    if($is_destacada){
        $html[]=get_accion_destacada($r,$is_por_su_canal);
    }else{
        $html[]=get_accion_portada($r,$is_por_su_canal);
    }
    //return add_div_actions(implode('&nbsp;',$html));
    return implode('&nbsp;',$html);
}
function create_div_view_header_my_noticias_publicas(){
    //intelsat-2015
    /*$html=array();
    $html[]='<div class="view-header">';
    //$html[]=l(t('Back to management panel'),'gestion',array('attributes'=>array('class'=>'back')));
    $html[]=l(t('Create public news'),'node/add/noticias-portada',array('attributes'=>array('class'=>'add')));
    $html[]='</div>';
    return implode('',$html);*/
    return '';
    //
}
function set_publicado_value($row,$is_por_su_canal,$is_destacada=0){
    if($is_por_su_canal){
        $result=t('Yes (Channel)');
        return $result;
    }
    //
    $result=t('No');
    if($is_destacada){
        if($row->node_type=='rss_feed'){
            return t('Yes');
        }else if($row->node_type=='noticia'){
            //$node=node_load($row->nid);
            if($row->field_is_noticia_usuario_destaca_value){
            //if($node->field_is_noticia_usuario_destaca[0]['value']){
                return t('Yes');
            }
       }else if($row->node_type=='item'){
            $node=node_load($row->nid);
           // echo print_r($node,1);
            if($node->field_is_carpeta_noticia_destaca[0]['value']){
                return t('Yes');
            }           
        }
    }else{
        if($row->node_type=='noticias_portada'){
            if($row->node_status){
                return t('Yes');
            }           
        }else{
            return t('Yes');
        }
    }
    return $result;
}
function get_accion_portada($row,$is_por_su_canal){
    //print $row->node_type.'<BR>';
    //echo print_r($row,1);
    if($row->node_type=='noticias_portada'){
        if($row->node_status){
            $result=l(t('Not Cover'),'my_set_no_portada',array('query'=>drupal_get_destination()));
        }else{
            $result=l(t('Cover'),'my_set_portada',array('query'=>drupal_get_destination()));
        }
    }else if($row->node_type=='noticia'){
        $node=node_load($row->nid);
        if($node->field_is_noticia_usuario_publica[0]['value']){
            $result=l(t('Not Cover'),'no_publicar_noticia_usuario/'.$node->nid,array('query'=>drupal_get_destination()));
        }else{
            $result=l(t('Cover'),'publicar_noticia_usuario/'.$node->nid,array('query'=>drupal_get_destination()));
        }
    }else if($row->node_type=='item'){
        $node=node_load($row->nid);
        if($is_por_su_canal){
            $canal_nid=$node->field_item_canal_reference[0]['nid'];      
            $result=l(t('Channel'),'node/'.$canal_nid.'/edit',array('query'=>drupal_get_destination()));
        }else{
            //echo print_r($node,1);exit();
            if($node->field_is_carpeta_noticia_publica[0]['value']){
                $result=l(t('Not Cover'),'no_publicar_item/'.$node->nid,array('query'=>drupal_get_destination()));
            }else{
                $result=l(t('Cover'),'publicar_item/'.$node->nid,array('query'=>drupal_get_destination()));
            }
        }
    }
    return $result;
}
function my_on_load_node(&$node){
    if($node->type=='noticia'){        
        $node->field_is_noticia_usuario_publica[0]['value']=zuzendu_is_noticia_usuario_publica($node);
        $node->field_is_noticia_usuario_destaca[0]['value']=zuzendu_is_noticia_usuario_destacada($node);
    }else if($node->type=='item'){
        $node->field_is_carpeta_noticia_publica[0]['value']=zuzendu_is_carpeta_noticia_publica($node);
        $node->field_is_carpeta_noticia_destaca[0]['value']=zuzendu_is_carpeta_noticia_destaca($node);
    }
    //echo print_r($node,1);
}
function zuzendu_is_noticia_usuario_publica(&$node){
    $c=my_get_content_type_noticia($node->nid, $node->vid);
    if(isset($c->nid) && !empty($c->nid)){
        return $c->field_is_noticia_usuario_publica_value;
    }
    return 0;
}
function publicar_item_callback(){
    $nid=arg(1);
    //$node=node_load($nid);
    //echo print_r($node,1);exit();
    update_item_carpeta_publica($nid,1);
    //return $nid;
    $html=array();
    $html[]=t('Converted to public news');
    $destination=drupal_get_destination();
    $destination=str_replace('destination=','',$destination);
    $destination=urldecode($destination);
    $html[]=l(t('Return'),$destination,array('attributes' => array('class'=>'back_left')));
    return implode('<BR>',$html);
    //return implode('',$html);
}
function update_item_carpeta_publica($nid,$is_carpeta_noticia_publica){
    $sql="UPDATE content_type_item SET field_is_carpeta_noticia_publica_value=".$is_carpeta_noticia_publica." WHERE nid=".$nid;
    db_query($sql);
}
function no_publicar_item_callback(){
    $nid=arg(1);

    update_item_carpeta_publica($nid,0);

    $html=array();
    $html[]=t('News removed from Public News');
    /*$destination=drupal_get_destination();
    my_drupal_get_destination();
    $destination=str_replace('destination=','',$destination);
    $destination=urldecode($destination);
    $html[]=l(t('Return'),$destination,array('attributes' => array('class'=>'back_left')));*/
    $html[]=get_volver_link();
    return implode('<BR>',$html);
    //return implode('',$html);
}
function zuzendu_is_carpeta_noticia_publica(&$node){
    $c=my_get_content_type_item($node->nid, $node->vid);
    if(isset($c->nid) && !empty($c->nid)){
        //echo print_r($c,1);exit();
        return $c->field_is_carpeta_noticia_publica_value;
    }
    return 0;
}
function get_is_por_su_canal($row,$is_destacada=0){
    if($row->node_type=='item'){
        $node=node_load($row->nid);
        $v=0;
        if($is_destacada){
            $v=$node->field_is_carpeta_noticia_destaca[0]['value'];
        }else{
            $v=$node->field_is_carpeta_noticia_publica[0]['value'];
        }
        if($v){
            return 0;
        }else{              
                $canal_nid=$node->field_item_canal_reference[0]['nid'];
                $canal_node=node_load($canal_nid);
                if(isset($canal_node->nid) && !empty($canal_node->nid)){
                    if($is_destacada){                        
                        if($canal_node->type=='canal_de_supercanal'){
                            if($canal_node->field_is_canal_noticia_destacada[0]['value']){
                                return 1;
                            }
                        }else if($canal_node->type=='canal_de_yql'){
                            if($canal_node->field_is_yql_noticia_destacada[0]['value']){
                                return 1;
                            }
                        }
                    }else{
                        //echo print_r($canal_node,1);exit();
                        if($node->type=='canal_de_supercanal'){
                            if($canal_node->field_is_canal_noticia_publica[0]['value']){
                                return 1;
                            }
                        }else if($node->type=='canal_de_yql'){
                            if($canal_node->field_is_yql_noticia_publica[0]['value']){
                                return 1;
                            }
                        }
                    }
                }
        }
    }
    return 0;
}
function my_noticias_destacadas_callback(){
    $output='';
    //
    $my_limit=variable_get('default_nodes_main', 20);
    //
    $headers=array();
    //intelsat-2015
    $headers[]='<input type="checkbox" id="my_select_all" name="my_select_all" class="my_select_all"/>';
    //
    $headers[]=t('News');
    $headers[]=t('Date');
    $headers[]=t('.Highlighted');
    $headers[]=t('Actions');
    //
    $item_list=my_get_noticias_destacadas_canales_content(0,'',0);
    //
    $output=create_div_view_header_my_noticias_destacadas();
    //
    if(count($item_list)>0){
        $rows=array();
        foreach($item_list as $i=>$row){
            $rows[$kont]=array();
            //intelsat-2015
            $rows[$kont][0]='<input type="checkbox" id="txek_'.$row->nid.'" name="txek_nid['.$row->nid.']" class="bulk_txek" value="1">';
            //
            $rows[$kont][1]=l($row->node_title,'node/'.$row->nid);
            $rows[$kont][2]=date('H:i d-m-Y',$row->node_changed);
            $is_por_su_canal=get_is_por_su_canal_destacada($row);
            //print $row->nid.'='.$is_por_su_canal.'<BR>';
            $rows[$kont][3]=set_destacada_value($row,$is_por_su_canal);
            $rows[$kont][4]=get_acciones_my_noticias_destacadas($row,$is_por_su_canal);
            $kont++;
        }
        $rows=my_set_estrategia_pager($rows,$my_limit);
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }else{
        $output.= '<div id="first-time">' .t('There are no top stories'). '</div>';
    }

    drupal_set_title(t('List of Highlighted News'));

    return $output;    
}
function create_div_view_header_my_noticias_destacadas(){
    //intelsat-2015
    /*
    $html=array();
    $html[]='<div class="view-header">';
    //$html[]=l(t('Back to management panel'),'gestion',array('attributes'=>array('class'=>'back')));
    $html[]=l(t('Create highlighted news'),'node/add/rss-feed',array('attributes'=>array('class'=>'add')));
    $html[]='</div>';
    return implode('',$html);*/
    return '';
    //
}
function get_is_por_su_canal_destacada($row){
    return get_is_por_su_canal($row,1);
}
function set_destacada_value($row,$is_por_su_canal){
    return set_publicado_value($row,$is_por_su_canal,1);
}
function get_acciones_my_noticias_destacadas($r,$is_por_su_canal){
    return get_acciones_my_noticias_publicas($r,$is_por_su_canal,1);
}
function get_accion_destacada($row,$is_por_su_canal){
    
    if($row->node_type=='noticia'){
        $node=node_load($row->nid);
        if($node->field_is_noticia_usuario_destaca[0]['value']){
            $result=l(my_get_icono_action('no-destacar',t('Not Highlight')),'no_destacar_noticia_usuario/'.$node->nid,array('query'=>drupal_get_destination(),'html'=>true));
        }else{
            $result=l(t('Highlight'),'destacar_noticia_usuario/'.$node->nid,array('query'=>drupal_get_destination()));
        }
    }else if($row->node_type=='item'){
        $node=node_load($row->nid);
        if($is_por_su_canal){
            $canal_nid=$node->field_item_canal_reference[0]['nid'];
            $result=l(t('Channel'),'node/'.$canal_nid.'/edit',array('query'=>drupal_get_destination()));
        }else{
            //echo print_r($node,1);exit();
            if($node->field_is_carpeta_noticia_destaca[0]['value']){
                $result=l(my_get_icono_action('no-destacar',t('Not Highlight')),'no_destacar_item/'.$node->nid,array('query'=>drupal_get_destination(),'html'=>true));
            }else{
                $result=l(t('Highlight'),'destacar_item/'.$node->nid,array('query'=>drupal_get_destination()));
            }
        }
    }
    return $result;
}
function zuzendu_is_noticia_usuario_destacada(&$node){
    $c=my_get_content_type_noticia($node->nid, $node->vid);
    if(isset($c->nid) && !empty($c->nid)){
        return $c->field_is_noticia_usuario_destaca_value;
    }
    return 0;
}
function zuzendu_is_carpeta_noticia_destaca(&$node){
    $c=my_get_content_type_item($node->nid, $node->vid);
    if(isset($c->nid) && !empty($c->nid)){
        //echo print_r($c,1);exit();
        return $c->field_is_carpeta_noticia_destaca_value;
    }
    return 0;
}
function my_noticias_publicas_form(){
    $form=array();    
    //
    $form['my_header']['#value']=create_div_view_header_my_noticias_publicas();
    //
    $form['my_bulk_operations_fs']=array(
      '#type'=>'fieldset',
      '#title'=>t('Bulk Actions'),
    );
    //
    /*$form['my_bulk_operations_fs']['my_delete_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Delete Node'),
      '#name'=>'my_delete_btn',
    );
    $form['my_bulk_operations_fs']['publish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Publish'),
      '#name'=>'publish_post_btn',
    );
    $form['my_bulk_operations_fs']['unpublish_post_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Unpublish'),
      '#name'=>'unpublish_post_btn',
    );*/
    panel_admin_items_add_bulk_operations_form_fields($form,1,'my_delete_btn');    
    //
    my_add_noticias_publicas_select_all_js();
    $form['my_table']['#value']=my_noticias_publicas_callback();
    //
    //$form['#submit'][]='my_noticias_publicas_form_submit';
    return $form;
}
function my_noticias_publicas_form_submit($form, &$form_state) {
    $button=$form_state['clicked_button'];
    $button_name=$button['#name'];
    if(in_array($button_name,array('my_delete_btn','publish_post_btn','unpublish_post_btn','delete_node_btn'))){
        if(isset($button['#post']['txek_nid']) && !empty($button['#post']['txek_nid'])){
            $nid_array=array_keys($button['#post']['txek_nid']);
            $_SESSION['my_bulk_publicas_nid_array']=$nid_array;
            if(in_array($button_name,array('my_delete_btn','delete_node_btn'))){
                drupal_goto('gestion/my_noticias_publicas_confirm_form');
            }else if(strcmp($button_name,'publish_post_btn')==0){
                drupal_goto('gestion/my_noticias_publicas_publish_post_confirm_form');
            }else if(strcmp($button_name,'unpublish_post_btn')==0){
                drupal_goto('gestion/my_noticias_publicas_unpublish_post_confirm_form');
            }
        }
    }
}
function create_content_confirm_delete_publicas($field='my_bulk_publicas_nid_array'){
    //intelsat-2015
    $nid_array=$_SESSION[$field];
    //
    $html=array();
    $html[]='<div class="item-list">';
    //intelsat-2015
    $html[]='<h3>'.hontza_canal_rss_get_selected_rows_message(count($nid_array)).':</h3>';
    //
    $html[]='<ul>';
    /*$html[]='<li class="first">Anonymous ataca al instituto de seguridad Stratfor</li>';
    $html[]='<li class="last">Cinco millones de internautas estudian idiomas con Busuu</li>';*/
    if(count($nid_array)>0){
        foreach($nid_array as $i=>$nid){
            $node=node_load($nid);
            $html[]='<li>'.$node->title.'</li>';
        }
    }
    $html[]='</ul>';
    $html[]='</div>';
    return implode('',$html);
}
function my_get_session_default_value($field,$glue=','){
    if(isset($_SESSION[$field])){
        if(!empty($glue)){
            return implode($glue,$_SESSION[$field]);
        }
        return $_SESSION[$field];
    }
    return '';
}
function my_noticias_publicas_confirm_form_submit(&$form, &$form_state) {
    call_bulk_confirm_form_submit($form,$form_state,'delete_node');
}
function my_noticias_publicas_confirm_form($my_type='delete_node'){
    $form=array();
    $esaldi='';
    if($my_type=='delete_node'){      
        $esaldi='Delete node';
    }else if($my_type=='publish_post'){
        $esaldi='Publish';
    }else if($my_type=='unpublish_post'){
        $esaldi='Unpublish';
    }
    drupal_set_title(t('Are you sure you want to perform '.$esaldi.' on selected rows?'));
    //
    $form['nid_array']=array(
      '#type'=>'hidden',
      '#default_value'=>my_get_session_default_value('my_bulk_publicas_nid_array'),
    );
    $form['edit-confirm']=array(
      '#type'=>'hidden',
      '#default_value'=>1,
      '#name'=>'confirm',
    );
    //
    $form['my_content']['#value']=create_content_confirm_delete_publicas();
    //
    $form['my_confirm_btn']=array(
      '#type'=>'submit',
      '#value'=>t('Confirm'),
      '#name'=>'my_confirm_btn',
    );
    $form['my_cancel']['#value']=l(t('Cancel'),'gestion/my_noticias_publicas');
    //
    if($my_type=='delete_node'){
        $form['#submit'][]='my_noticias_publicas_confirm_form_submit';
    }else if($my_type=='publish_post'){
        $form['#submit'][]='my_noticias_publicas_publish_post_confirm_form_submit';
    }else if($my_type=='unpublish_post'){
        $form['#submit'][]='my_noticias_publicas_unpublish_post_confirm_form_submit';
    }
    //
    return $form;
}
function call_bulk_confirm_form_submit(&$form, &$form_state,$my_type,$destination='gestion/my_noticias_publicas') {
    $values=$form_state['values'];
    $nid_array=explode(',',$values['nid_array']);
    if(count($nid_array)>0){
        foreach($nid_array as $i=>$nid){
            if(!empty($nid) && is_numeric($nid)){
                if($my_type=='delete_node'){
                    node_delete($nid);
                }else if($my_type=='publish_post'){
                    update_node_status($nid,1);
                }else if($my_type=='unpublish_post'){
                    update_node_status($nid,0);
                //intelsat-2015                    
                }else if($my_type=='activate_channel'){                    
                    gestion_canales_activar_canal($nid,1);
                }else if($my_type=='deactivate_channel'){                    
                    gestion_canales_activar_canal($nid,0);
                }
                //
            }
        }
    }
    //
    drupal_goto($destination);
}
function update_node_status($nid,$status){
    $sql='UPDATE node SET status='.$status.' WHERE nid='.$nid;
    db_query($sql);
}
function my_noticias_publicas_publish_post_confirm_form_submit(&$form,&$form_state){
    call_bulk_confirm_form_submit($form,$form_state,'publish_post');
}
function my_noticias_publicas_unpublish_post_confirm_form_submit(&$form,&$form_state){
    call_bulk_confirm_form_submit($form,$form_state,'unpublish_post');
}
function my_noticias_publicas_publish_post_confirm_form(){
    return my_noticias_publicas_confirm_form('publish_post');
}
function my_noticias_publicas_unpublish_post_confirm_form(){
    return my_noticias_publicas_confirm_form('unpublish_post');
}
function my_add_noticias_publicas_select_all_js(){
		$js='
			$(document).ready(function()
			{
			  $(".my_select_all").change(function(){
                            v=$(this).attr("checked");
                            my_set_beste_publicas_txek(v);
                          });
                          //
                           $(".bulk_txek").change(function(){
                                v=$(this).attr("checked");
                                if(v==false){
                                    $(".my_select_all").attr("checked",false);
                                }
                            });
                          //
                          function my_set_beste_publicas_txek(v){
                            $(".bulk_txek").each(function(){
                                $(this).attr("checked",v);
                            });
                          }
			});';

			drupal_add_js($js,'inline');
	
}
function get_volver_link(){
    $destination=drupal_get_destination();
    my_drupal_get_destination();
    $destination=str_replace('destination=','',$destination);
    $destination=urldecode($destination);
    return l(t('Return'),$destination,array('attributes' => array('class'=>'back_left')));
}
function my_advanced_search_news_js(){
		$js='
			$(document).ready(function()
			{
			  var a=$("#publica_destacada_fs").parent();
                          a.attr("id","id_div_publica_destacada_fs");
			});';

			drupal_add_js($js,'inline');

}
function is_show_destacar_link(){
    global $user;
    //intelsat-2015
    if(red_despacho_is_activado()){
        return 0;
    }
    //gemini-2014
    if(!hontza_is_sareko_id_red()){
    //    
        if(isset($user->uid) && !empty($user->uid) && $user->uid==1){
            return 1;
        }
    }else{
        //intelsat-2015
        $modo_estrategia=1;
        if(is_administrador_grupo($modo_estrategia)){
            return 1;
        }
    }
    //intelsat-2015
    if(hontza_canal_rss_is_visualizador_activado()){
        return 1;
    }
    return 0;
}