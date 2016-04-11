<?php
function visualizador_categorias_get_inicio_categorias_html(){
    $my_limit=4;
    $categorias_array=visualizador_categorias_get_categorias_array();
    $categorias_array=my_set_estrategia_pager($categorias_array, $my_limit);
    //$output = '<h3>'.t('Categories').'</h3>';
    $output ='';
  $num_rows = FALSE;
 foreach ($categorias_array as $i=>$row){
    $categoria_html=visualizador_categorias_get_inicio_categoria_html($row);
    $output .=$categoria_html;      
    $num_rows = TRUE;
  }
  
  if ($num_rows) {
    //$feed_url = url('rss.xml', array('absolute' => TRUE));
    //drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));
    $output .= '<div style="float:left;width:100%;padding-top:0px;"'.theme('pager', NULL,$my_limit).'</div>';
  }
  else {
     $output.= '<div id="first-time">' .t('There are no contents'). '</div>';  
  }    
  return $output;
}
function visualizador_categorias_get_categorias_array($tid_in=''){
    $result=array();
    $vid=visualizador_categorias_get_id_categoria();
    if(empty($tid_in)){
        $categorias=taxonomy_get_tree($vid);
    }else{
        $categorias=taxonomy_get_tree($vid,$tid_in);
    }
    $indefinida_tid=210161;
    if(!empty($categorias)){
        foreach($categorias as $i=>$term_row){
            if($term_row->tid!=$indefinida_tid){
                if(empty($tid_in)){
                    if(visualizador_categorias_is_principal($term_row)){
                        $result[]=$term_row;
                    }
                }else{
                    $result[]=$term_row;
                }
            }
        }
    }
    return $result;
}
function visualizador_categorias_get_inicio_categoria_html($term){
    $html=array();
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
    $term_name=hontza_canal_rss_categorias_unset_term_name_corchetes($term_name);      
    $description=visualizador_categorias_get_categoria_description($description);
    //$html[]='<div style="width:20%;float:left;">';
    $html[]='<div style="width:25%;float:left;">';
    $url=visualizador_categorias_get_categoria_url('publico/canales/categorias/'.$term->tid.'/ultimos');    
    $html[]=visualizador_categorias_get_inicio_categoria_img($url,$term->tid,$term_name);
    $html[]='<div id="node-'.$term->tid.'" class="node">';
    $html[]='<div id="flagtitulo">';
    $html[]='<div class="f-titulo">';
    //$html[]='<h5><b>'.l($term_name,$url,array('absolute'=>true,'attributes'=>array('target'=>'_blank'))).'</b></h5>';
    $html[]='<h5><b>'.l($term_name,$url,array('absolute'=>true)).'</b></h5>';
    $html[]='</div>';
    $html[]='</div>';
    $html[]='<div id="i-contenedor">';
    $html[]='<div class="item-teaser-texto">'.$description.'</div>';
    $html[]='</div>';
    $html[]='</div>';
    $html[]='</div>';                          
    return implode('',$html);      
}
function visualizador_categorias_get_categoria_description($description,$len=70,$is_cortar=1){
    $result=$description;
    $result=strip_tags($result);
    $result=html_entity_decode($result);
    if($is_cortar){
        if(strlen($result)>$len){
            $result=drupal_substr($result, 0, $len); 
            $result.='...';
            return $result;
        }
    }        
    return $result;
}
function visualizador_categorias_get_categoria_url($result_in){
    global $base_url;
    $result=$result_in;
    $my_grupo=og_get_group_context();        
    if(isset($my_grupo->nid) && !empty($my_grupo->nid)){

    }else{
        $grupo=visualizador_create_grupo_base_path();
        $result=$base_url.'/'.$grupo.'/'.$result;
    }
    return $result;
}
function visualizador_categorias_get_inicio_categoria_img($url,$tid,$title){
    global $base_url;
    $subdominio='insalerta.hontza.es';
    //intelsat-2016
    if(defined('_VISUALIZADOR_SUBDOMINIO')){
        $subdominio=_VISUALIZADOR_SUBDOMINIO;
    }
    $src=$base_url.'/sites/'.$subdominio.'/files/visualizador_categorias/';    
    if($tid==210162){
        $src.='desarrollo_territorial.jpg';
    }else if($tid==210164){    
        $src.='emprendimiento.jpg';
    }else if($tid==210165){    
        $src.='financiacion.jpg';
    }else if($tid==210163){    
        $src.='internacionalizacion.jpg';
    }else if($tid==210166){
        $src.='sectores.jpeg';
    }else{
        $src.='desarrollo_territorial.jpg';
    }
    //$width=100;
    //$height=100;
    //$height=119;
    //$width=170;
    $width=212;
    if(module_exists('taxonomy_image')){
        $url_image=taxonomy_image_get_url($tid);
        if(!empty($url_image)){
            $src=$url_image;
        }
    }
    //$img='<img src="'.$src.'" alt="'.$title.'" title="'.$title.'" width="'.$width.'" height="'.$height.'"/>';
    $img='<img src="'.$src.'" alt="'.$title.'" title="'.$title.'" width="'.$width.'"/>';
    //$result='<div style="height:100px;padding-bottom:10px;">'.l($img,$url,array('absolute'=>true,'html'=>true,'attributes'=>array('target'=>'_blank'))).'</div>';
    $result='<div>'.l($img,$url,array('absolute'=>true,'html'=>true,'attributes'=>array('target'=>'_blank'))).'</div>';    
    return $result;
}
function visualizador_categorias_is_principal($term_row){
    if(isset($term_row->parents) && isset($term_row->parents[0]) && !empty($term_row->parents[0])){
        return 0;
    }
    return 1;
}
function visualizador_categorias_get_id_categoria(){
    if(!visualizador_is_red_alerta()){
        $grupo_nid=visualizador_get_grupo_nid();
        $result=db_result(db_query("SELECT og.vid FROM {og_vocab} og WHERE  og.nid = '%s'",$grupo_nid));
        return $result;
    }
    return 14;
}