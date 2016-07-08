<?php
// $Id$

/**
 * @file
 * The guts of the theme :)
 */

require_once('theme-settings.php');

/**
 * Add Custom Generated CSS File
 * This file is generated each time the theme settings page is loaded.
 */
/*$custom_css = file_directory_path() .'/buho/custom.css';page
if (file_exists($custom_css)) {
  drupal_add_css($custom_css, 'theme', 'all', TRUE);
}*/

/**
 * Implementation of hook_theme().
 * This function provides a one-stop reference for all
 */
function buho_theme(&$existing, $type, $theme, $path) {
  return array(
    'breadcrumb' => array(
      'arguments' => array('breadcrumb' => array()),
      'file' => 'functions/theme-overrides.inc',
    ),
    'conditional_stylesheets' => array(
      'file' => 'functions/theme-custom.inc',
    ),
    'feed_icon' => array(
      'arguments' => array('url' => NULL, 'title' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'form_element' => array(
      'arguments' => array('element' => NULL, 'value' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'fieldset' => array(
      'arguments' => array('element' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'menu_local_tasks' => array(
      'arguments' => NULL,
      'file' => 'functions/theme-overrides.inc',
    ),
    'more_link' => array(
      'arguments' => array('url' => array(), 'title' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'pager' => array(
      'arguments' => array('tags' => array(), 'limit' => NULL, 'element' => NULL, 'parameters' => array(), 'quantity' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
   'status_messages' => array(
      'arguments' => array('display' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'status_report' => array(
      'arguments' => array('requirements' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'table' => array(
      'arguments' => array('header' => NULL, 'rows' => NULL, 'attributes' => array(), 'caption' => NULL),
      'file' => 'functions/theme-overrides.inc',
    ),
    'render_attributes' => array(
      'arguments' => array('attributes'),
      'file' => 'functions/theme-custom.inc',
    ),   
  );
}

/**
 * Implementation of hook_preprocess().
 *
 * @param $vars
 * @param $hook
 * @return Array
 */
function buho_preprocess(&$vars, $hook) {

  // Only add the admin.css file to administrative pages
  if (arg(0) == 'admin') {
    drupal_add_css(path_to_theme() .'/css/admin.css', 'theme', 'all', TRUE);
  }
 
 //gemini-2013 
 $is_show=1; 
  
 //gemini
 if($hook=='block'){
        //gemini-2013        
 	buho_set_canales_block_title($vars, $hook);        
        //my_help_block($vars,$hook);
        my_help_block($vars,$is_show,$hook);
        my_categorias_fuentes_block($vars, $hook);
        buho_set_canales_block_link($vars, $hook);
        //gemini-2014
        buho_set_og_area_trabajo_block_title_link($vars, $hook);
 }
 //
 /**
  * This function checks to see if a hook has a preprocess file associated with
  * it, and if so, loads it.
  */
  //gemini-2013
  if($is_show){
    if (is_file(drupal_get_path('theme', 'buho') .'/preprocess/preprocess-'. str_replace('_', '-', $hook) .'.inc')) {
      include('preprocess/preprocess-'. str_replace('_', '-', $hook) .'.inc');
    }
  }
}


/*Eliminar el node title en determinados tipos de contenidos*/


function buho_preprocess_page(&$vars) {

  /*itles are ignored by content type when they are not desired in the design.*/
  $vars['original_title'] = $vars['title'];
  if (!empty($vars['node']) && in_array($vars['node']->type, array('item', 'noticia'))) {
    $vars['title'] = '';
  }
 
  //gemini
  if (stripos($vars['title'],'icon_users.png')) {
    $vars['title'] = 'Users';
  }  
  //
 
  //intelsat-2015
  buho_set_title_preprocess_page($vars);
  
  //gemini
  //if(arg(0)=='admin' && arg(1)=='content' && arg(2)=='taxonomy' && arg(3)==1 && arg(4)==''){  	
  if(is_taxonomy_volver_link(0,$title) || is_url_frases_post_formulario()){
    //intelsat-2015
    if(hontza_canal_rss_is_show_volver_gestion()){  
        $vars['my_volver_link']='<div class="clearfix">';     
        $vars['my_volver_link'].=l(t('Back to management panel'),'gestion',array('attributes'=>array('class'=>'back')));
        $vars['my_volver_link'].='</div>';
    }
    //$vars['my_volver_link']=l(utf8_encode(t('Management panel')),'gestion',array('attributes'=>array('class'=>'back')));    
  }else if(is_ficha_node_post_formulario()){
    $vars['my_volver_link']=get_my_volver_link_post_formulario();
  }
  /*
  //intelsat-2015  
  }else if(is_ficha_node('servicio')){
    $vars['my_volver_link']=hontza_canal_rss_get_my_volver_link_servicio();
  }
  //
  */
  my_set_title($vars);
  //
}
//gemini
function is_taxonomy_volver_link($is_list_terms=0,&$title){
	$title='';
	//arg(3)==1 categoria de la fuente bakarrra da
	if(arg(0)=='admin' && arg(1)=='content' && arg(2)=='taxonomy'){
		 $vid=arg(3);
		 //print 'vid='.$vid.'<BR>';		 
		 //if(arg(3)==1){
		 //if(is_categoria_de_la_fuente($tid,$my_term)){
		 if(my_is_vocabulary($vid)){
			if(arg(4)==''){				
				return 1;
			}
			if(arg(4)=='add' && arg(5)=='term'){
				//$title='Añadir tipo de fuente';
                                $title=t('Add source type');
				return 1;
			}
		 }else if(arg(3)=='edit' && arg(4)=='term'){
		 	if(!$is_list_terms){		 		 
				$tid=arg(5);
				//print 'term_id='.$term_id.'<BR>';
				if(is_categoria_de_la_fuente($tid,$my_term)){
					return 1;
				}
			}	
		 }					
	}
		
	return 0;  	
}
//gemini
function my_help_block(&$vars,&$is_show, $hook){
	//print 'delta='.$vars['block']->delta.'<BR>';
        $is_show=1;
	if($hook=='block'){
                //print 'delta='.$vars['block']->delta.'<BR>';
		//print $vars['block']->subject.'<BR>';
		if(!isset($vars['block']->delta) || !isset($vars['block']->subject) || empty($vars['block']->subject)){			
			return '';
		}		
		if(strcmp($vars['block']->delta,'og_canales-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2934);			
			return '';
		}
		if(strcmp($vars['block']->delta,'0')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2935);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_canales_busqueda-block_1')==0){
                        $vars['block']->subject=$vars['block']->subject.help_popup_block(2936);
                        //unset($vars['block']);
                        //$is_show=0;                        
			return '';			
		}
		if(strcmp($vars['block']->delta,'14')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2937);
			return '';			
		}
		if(strcmp($vars['block']->delta,'27')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2938);
			return '';			
		}
		if(strcmp($vars['block']->delta,'12')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2939);
			return '';			
		}
		if(strcmp($vars['block']->delta,'8')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2940);
			return '';			
		}
		if(strcmp($vars['block']->delta,'4')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2941);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_categorias_fuentes-block_1')==0){
			//$vars['block']->subject=$vars['block']->subject.help_popup_block(2942,2);
                        //intelsat-2015
                        $icono=my_get_icono_action('tipos_de_fuentes',t('Type of Sources')).'&nbsp;';
			$vars['block']->subject=$icono.$vars['block']->subject.help_popup_block(2942,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'13')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2943);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_area_trabajo-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2944);
			return '';			
		}
		if(strcmp($vars['block']->delta,'11')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2945);
			return '';			
		}
		if(strcmp($vars['block']->delta,'1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2946);
			return '';			
		}
		if(in_array($vars['block']->delta,array('og_usuarios-block_1','41'))){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2947);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_canales_dash-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2949);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'ca8c74ef9b29fefa6b202cd5cbf47fbb')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2950);
			return '';			
		}
		if(strcmp($vars['block']->delta,'d640a64f46f55241f1a9ed29d2536ef2')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2951);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'273bd4ba9d1b68191046f2e902e83370')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2952);
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_home_faces-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2953);
			return '';			
		}
		if(strcmp($vars['block']->delta,'3')==0){
                        //intelsat-2015
                        $title=t('Tag Cloud');
                        $icono=my_get_icono_action('tag_left',$title,'tag_left').'&nbsp;';
                        $vars['block']->subject=$icono.$title;                        
                        //
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2954);			
			return '';			
		}
		if(strcmp($vars['block']->delta,'og_home_areadebate-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2955);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_home_areadetrabajo-block_1')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2956);
			return '';			
		}
		if(strcmp($vars['block']->delta,'21')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2957,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'25')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2958,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'16')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2959,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'20')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2960,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'7')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2961,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'17')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2962,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'18')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2963,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'22')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2964,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'30')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2965,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'15')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2966,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'19')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2967,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'24')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2968,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'23')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2969,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'31')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(2970,3);
			return '';			
		}
		if(strcmp($vars['block']->delta,'32')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(12994,3);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'og_area_debate_my_block-block_1')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(14115,3);
			return '';		
		}
		if(strcmp($vars['block']->delta,'33')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(14116,3);
			return '';			
		}		
		if(strcmp($vars['block']->delta,'34')==0){			
			$vars['block']->subject=$vars['block']->subject.help_popup_block(14117);
			return '';			
		}
                if(strcmp($vars['block']->delta,'37')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(15322);
			return '';
		}
                if(strcmp($vars['block']->delta,'38')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(15323);
			return '';
		}
                if(strcmp($vars['block']->delta,'42')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(15341);
			return '';
		}
                if(strcmp($vars['block']->delta,'43')==0){
			$vars['block']->subject=$vars['block']->subject.help_popup_block(20112);
			return '';
		}
                //
                my_default_help_block($vars, $hook);
	}
}
function buho_views_mini_pager($tags = array(), $limit = 10, $element = 0, $parameters = array(), $quantity = 9) {
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.


  $li_previous = theme('pager_previous', (isset($tags[1]) ? $tags[1] : '<'), $limit, $element, 1, $parameters);
  if (empty($li_previous)) {
    $li_previous = "&nbsp;";
  }

  $li_next = theme('pager_next', (isset($tags[3]) ? $tags[3] : '>'), $limit, $element, 1, $parameters);
  if (empty($li_next)) {
    $li_next = "&nbsp;";
  }
  
  
  //gemini
  $li_first = theme('pager_first', (isset($tags[0]) ? $tags[0] : '<<'), $limit, $element, $parameters);
  $li_last = theme('pager_last', (isset($tags[4]) ? $tags[4] : '>>'), $limit, $element, $parameters);
  //
  

  if ($pager_total[$element] > 1) {
  	//gemini
	if ($li_first) {
      $items[] = array(
        'class' => 'pager-first',
        'data' => $li_first,
      );
    }
	//
	
    $items[] = array(
      'class' => 'pager-previous',
      'data' => $li_previous,
    );

    $items[] = array(
      'class' => 'pager-current',
      'data' => t('@current of @max', array('@current' => $pager_current, '@max' => $pager_max)),
    );

    $items[] = array(
      'class' => 'pager-next',
      'data' => $li_next,
    );
	
	//gemini
	if ($li_last) {
      $items[] = array(
        'data' => $li_last,
      );
    }
	//
	
    return theme('item_list', $items, NULL, 'ul', array('class' => 'pager'));
  }
}
//gemini
function my_set_title(&$vars){
    global $language;
	$title='';
	//
        /*if(is_super_admin()){
        echo print_r($vars,1);
        }*/
        if(is_term_view_orig('Categoría Servicios')){
            $tid=arg(2);
            $vars['title']=get_term_extra_name($tid, '', $vars['title']);
        }else if(arg(0)=='categorias-fuentes'){
		$vars['title']=t('Source Type').': '.arg(1);
	}else if(is_taxonomy_volver_link(1,$title)){	
		if(empty($title)){
			$vars['title']=t('Types of Sources');
		}else{
			$vars['title']=t($title);
		}
	}else if(is_taxonomy_volver_link(0,$title)){
                $vars['title']=t('Edit source type');
	}else if(is_fuentes_pipes_todas()){
		$s='';
		if(isset($_REQUEST['tid']) && !empty($_REQUEST['tid'])){
			$tid=$_REQUEST['tid'];
			//$term=taxonomy_get_term($tid);
			$term=taxonomy_get_term_by_language($tid);
                        if(!empty($term)){
				$s=$term->name;
			}
		}	
		//	
		if(!empty($s)){
			$vars['title']=t('Source Type').': '.$s;
		}
	}else if(is_usuarios_todos()){            
            if(is_usuarios_estadisticas()){
                if(strcmp(arg(1),'todos')==0){
                    //$vars['title']=t('Usuarios de este grupo');
                    $vars['title']=t('List of Members');
                }else{
                    $vars['title']=t('List of Users').' '.arg(1);
                }
            }
	}else if(strcmp(arg(0),'canales')==0){
            $arg1=arg(1);
            if(!empty($arg1) && strcmp($arg1,'categorias')==0){
                $arg2=arg(2);
                $categoria_title='';
                if(!empty($arg2)){
                    $my_canal_categoria=taxonomy_get_term($arg2);
                    if(isset($my_canal_categoria->tid) && !empty($my_canal_categoria->tid)){
                        $categoria_title=' '.get_term_extra_name($my_canal_categoria->tid, $language->language,$my_canal_categoria->name);
                    }
                }
                $vars['title']=t('News in Category').': '.$categoria_title;
            }else{
                $vars['title']=str_replace('Canal: ','',$vars['title']);
                $vars['title']=str_replace('Channel: ','',$vars['title']);
            }
        //gemini-2013    
        }else if(is_og_users_faces()){
            $vars['title']=t('Faces');
            //intelsat-2015
            //$vars['head_title']=t('Faces').' | HONTZA';
            $my_site_name=variable_get('site_name', 'Drupal');
            if(red_is_subdominio_red_alerta()){
                $my_site_name='ALERTA';
            }
            $vars['head_title']=t('Faces').' | '.$my_site_name;
        }else if(hontza_is_carpeta_dinamica_selected($node_carpeta_dinamica)){
            //intelsat-2015
            if(isset($node_carpeta_dinamica->nid) && !empty($node_carpeta_dinamica->nid)){
                $vars['title']=hontza_solr_search_get_busqueda_title($node_carpeta_dinamica->title);
            }    
        }else if(hontza_is_node_view_type(array('estrategia','despliegue','decision','informacion','idea','oportunidad','proyecto'),$my_node_title)){
            $vars['title']=$my_node_title;
        }else if(hontza_is_canal_usuarios()){
            //gemini-2014
            $vars['title']=hontza_get_canal_usuarios_title();
        //intelsat-2015            
        }else if(panel_admin_is_gestion('ayuda_popup')){
            $vars['title']=t(panel_admin_set_gestion_ayuda_popup_title());
        }
        //
}
//gemini
function my_categorias_fuentes_block(&$vars, $hook){
	if($hook=='block'){
		if(strcmp($vars['block']->delta,'og_categorias_fuentes-block_1')==0){
			$result=$vars['block']->content;
			$my_array=explode('<ul class="views-summary">',$result);
			$bi_array=explode('</ul>',$my_array[1]);
			$li_list=my_get_categorias_fuentes_li_list();					
			$vars['block']->content=$my_array[0].'<ul class="views-summary">'.$li_list.'</ul>'.$bi_array[1];
		}
	}
}
//gemini
function my_get_categorias_fuentes_li_list(){

$my_grupo=og_get_group_context();
//
$where=array();

//$voc=my_vocabulary_load(utf8_encode('Categoría de la fuente'));
$voc=my_vocabulary_load('Categoría de la fuente');
//echo print_r($voc,1);
if(!empty($voc) && isset($voc->vid) && !empty($voc->vid)){
	$where[]="term_data.vid=".$voc->vid;
	//print "term_data.vid=".$voc->vid."<BR>";
}else{
	$where[]="term_data.vid in ('1')";
}
//
if(!empty($my_grupo) && isset($my_grupo->nid) && !empty($my_grupo->nid)){
	$where[]="(og_ancestry.group_nid = ".$my_grupo->nid.")"; 
}

$sql="SELECT term_data.name AS term_data_name,
   COUNT(node.nid) AS num_records,term_data.tid
 FROM node node 
 LEFT JOIN term_node term_node ON node.vid = term_node.vid
 LEFT JOIN term_data term_data ON term_node.tid = term_data.tid
 LEFT JOIN term_hierarchy term_hierarchy ON term_data.tid = term_hierarchy.tid
 LEFT JOIN term_data term_data_term_hierarchy ON term_hierarchy.parent = term_data_term_hierarchy.tid
 LEFT JOIN {og_ancestry} og_ancestry ON node.nid = og_ancestry.nid
 WHERE ".implode(" AND ",$where)."
 GROUP BY term_data_name
  ORDER BY term_data_name ASC";
  
//$sql=my_get_sql_fuentes_pipes_todas();  
  
	$result=db_query($sql);
	$my_result=array();
	while($row=db_fetch_object($result)){
		$my_result[]=$row;
	}
	$my_result;
	$li_list=array();  
  	if(count($my_result)>0){
		foreach($my_result as $i=>$row){
                    $term_data_name=$row->term_data_name;
                    $term_data_name=get_term_extra_name($row->tid, '', $term_data_name);
                    $term_lang=taxonomy_get_term_by_language($row->tid);
                    $li_list[]='<li>'.l($term_data_name,'fuentes-pipes/todas/'.$row->tid,array('attributes' => array('class' => 'a_tipos_de_fuentes','title'=>strip_tags($term_lang->description),'style'=>'font-weight:normal;'),'query'=>'tid='.$row->tid)).'('.$row->num_records.')</li>';                    
                }
	}
	return implode('',$li_list);
}
//gemini
function buho_menu_item_link($link) {
  if (empty($link['localized_options'])) {
    $link['localized_options'] = array();
  }
  //gemini
  //if(strcmp($link['link_path'],'usuarios/todos')==0){
	  /*if(arg(0)=='usuarios' && arg(1)=='todos'){	 
	  	if(!isset($link['localized_options']['attributes'])){
			$link['localized_options']['attributes']=array();
		}
		$link['localized_options']['attributes']['id']='id_a_usuarios_todos';
	  }*/
  
	  my_add_id_to_link($link);
  //}	  
  //
  return l($link['title'], $link['href'], $link['localized_options']);
}
function my_default_help_block(&$vars, $hook){
    if(strcmp($vars['block']->delta,'100')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15207);
	return '';
    }
    if(strcmp($vars['block']->delta,'200')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15208);
	return '';
    }
    if(strcmp($vars['block']->delta,'300')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15209);
	return '';
    }
    if(strcmp($vars['block']->delta,'400')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15210);
	return '';
    }
    if(strcmp($vars['block']->delta,'500')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15215);
	return '';
    }
    if(strcmp($vars['block']->delta,'600')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15216);
	return '';
    }
    if(strcmp($vars['block']->delta,'700')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15217);
	return '';
    }
    if(strcmp($vars['block']->delta,'800')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15272);
	return '';
    }
    if(strcmp($vars['block']->delta,'900')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15342);
	return '';
    }
    if(strcmp($vars['block']->delta,'1000')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15343);
	return '';
    }
    if(strcmp($vars['block']->delta,'1100')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15344);
	return '';
    }
    if(strcmp($vars['block']->delta,'1200')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15444);
	return '';
    }
    if(strcmp($vars['block']->delta,'1300')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(15719);
	return '';
    }
    //intelsat-2015
    if(strcmp($vars['block']->delta,'50')==0){
        $vars['block']->subject=$vars['block']->subject.help_popup_block(416019);
        return '';
    }
    if(hontza_canal_rss_is_visualizador_activado()){
        visualizador_help_block($vars,$hook);
    }                
}
function buho_alerta_time($element) {
  /*
  $output = '<div class="container-inline">'. $element['#children'] .'</div>';
  return theme('form_element', $element['#title'], $output, $element['#description'], $element['#id'], $element['#required'], form_get_error($element));
  */
  $output = '<div class="container-inline">'. $element['#children'] .'</div>';
  return theme('form_element', $element, $output);

}
//gemini
/*function buho_preprocess_node(&$vars) {
    $node=$vars['node'];
    print $node->type.'<BR>';exit();
}*/
//gemini
function buho_set_canales_block_link(&$vars, $hook){
	if($hook=='block'){
		if(strcmp($vars['block']->delta,'og_canales-block_1')==0){
                    $result=array();
                    /*$content_array=explode('href="',$vars['block']->content);
                    if(!empty($content_array)){
                        foreach($content_array as $i=>$v_orig){
                            $v=$v_orig;
                            $pos=strpos($v,'"');
                            $href=substr($v,0,$pos);
                            $my_array=explode("/",$href);
                            $num=count($my_array);
                            if($my_array[$num-2]=='canales'){
                                $canal_nid=trim($my_array[$num-1]);
                                if(!hontza_is_show_canales_pendientes_tab($canal_nid)){
                                    $value=str_replace('canales/'.$canal_nid,'canales/'.$canal_nid.'/validados',$v);
                                    $result[$i]=hontza_canales_add_popup_en_indices($value,$canal_nid);
                                }else{
                                    $result[$i]=hontza_canales_add_popup_en_indices($v,$canal_nid);
                                }
                            }else{
                                //gemini-2014
                                $value=str_replace('canales/canal-usuarios/','canal-usuarios/',$v);                                
                                $result[$i]=$value;
                            }
                        }
                    }
                    $vars['block']->content=implode('href="',$result);*/
                    $vars['block']->content=hontza_set_og_canales_block_link($vars['block']->content);
		}
	}
}
/*
function buho_hontza_grupos_datetime($element) {
  $output = '<div class="container-inline">'. $element['#children'] .'</div>';
  return theme('form_element', $element, $output);
}*/
//gemini-2013
function buho_set_canales_block_title(&$vars, $hook){
	if($hook=='block'){
		if(strcmp($vars['block']->delta,'og_canales-block_1')==0){
                    //$link_my_channels=t('My Channels');
                    $link_my_channels=t('Pending');
                    $label_all=t('All');
                    //$label_all=hontza_quitar_signo_t($label_all,'.');
                    $link_all=$label_all;
                    if(hontza_is_mis_canales_block()){ 
                        $link_all=l($label_all,'cambiar_consulta_canales_block',array('query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_channel_title')));
                    }else{
                        //$link_my_channels=l(t('My Channels'),'cambiar_consulta_canales_block',array('query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_channel_title')));
                        $link_my_channels=l(t('Pending'),'cambiar_consulta_canales_block',array('query'=>drupal_get_destination(),'attributes'=>array('class'=>'a_channel_title')));                    
                    }
                    $subject=$link_my_channels.'&nbsp;|&nbsp;'.$link_all;                                        
                    //intelsat-2015
                    $icono=my_get_icono_action('canal',t('Channels'),'canal').'&nbsp;';
                    //
                    $vars['block']->subject=$icono.$subject;                                                
		}
	}
}
//gemini-2013
function buho_date_select($element) {
  $output = '';
  $class = 'container-inline-date';
  // Add #date_float to allow date parts to float together on the same line. 
  if (empty($element['#date_float'])) {
    $my_array=hontza_define_vigilancia_filter_fields();
    if(in_array($element['#name'],$my_array)){  
      $class .= ' vigilancia_filter_field date-clear-block';
    }else{
      $class .= ' date-clear-block';    
    }
  }
  if (isset($element['#children'])) {
    $output = $element['#children'];
  }     
  return '<div class="'. $class .'">'. theme('form_element', $element, $output) .'</div>';
}
//gemini-2014
function buho_set_og_area_trabajo_block_title_link(&$vars, $hook){
    if($hook=='block'){
        if(strcmp($vars['block']->delta,'og_area_trabajo-block_1')==0){
            $subject='';
            $pos=strpos($vars['block']->subject,'<script type="text/javascript">');
            if(!($pos===FALSE)){
                $subject.=substr($vars['block']->subject,$pos);
            }
            //intelsat-2015
            $title=t('Collaboration');
            $icono=my_get_icono_action('trabajo_left',$title,'trabajo_left').'&nbsp;';
            //
            $subject.=$icono.l($title,'area-trabajo');
            $vars['block']->subject=$subject;                                                
        }
    }
}
//intelsat-2014
function buho_preprocess_search_results(&$variables) {
  //intelsat-2016
  //$my_limit=10;
  $my_limit=red_solr_inc_get_my_limit();  
  $variables['search_results'] = '';
  //intelsat-2016
  $my_results=red_solr_inc_get_my_results($variables['results']);
  //foreach ($variables['results'] as $i=>$result) {
  foreach ($my_results as $i=>$result) {  
    if($i>=$my_limit){
        break;
    }
    //intelsat-2014
    //$variables['search_results'] .= theme('search_result', $result, $variables['type']);
    $node=node_load($result['fields']['entity_id']);
    if(isset($node->nid) && !empty($node->nid)){
        $variables['search_results'].=node_view($node);
    }
    //
  }
  
  $variables['pager'] = theme('pager', NULL, $my_limit, 0);
  
  // Provide alternate search results template.
  $variables['template_files'][] = 'search-results-'. $variables['type'];
  //intelsat-2014
  $variables['my_num_found_solr']=$variables['response']->response->numFound;
  //intelsat-2015
  $variables['my_start_solr']=$variables['response']->response->start+1;
  $variables['my_end_solr']=$variables['response']->response->start+$my_limit;
  if($variables['my_num_found_solr']<$variables['my_end_solr']){
      $variables['my_end_solr']=$variables['my_num_found_solr'];
  }
  //
  $variables['solr_save_search_form']='';
  if((isset($_SESSION['solr_is_show_save_search']) && !empty($_SESSION['solr_is_show_save_search'])) || hontza_solr_is_resultados_pantalla()){
      unset($_SESSION['solr_is_show_save_search']);
      //$variables['solr_save_search_form']=drupal_get_form('hontza_solr_save_search_form');
      $variables['solr_my_buttons']=hontza_solr_define_search_result_buttons();
  }
  //
}
//intelsat-2014   
function buho_facetapi_deactivate_widget($variables = array()) {
  return  my_get_icono_action('delete_solr_filter', t('Delete filter'),'facetapi_active',$variables);
}
//intelsat-2014  
function buho_facetapi_title($variables) {
  $facet_title=drupal_strtolower($variables['title']);
  //intelsat-2015
  $facet_title=trim($facet_title);
  //
  if($facet_title=='canal'){
      //return t('Channel');
      $title=t('Filter by Channel');
  }else if($facet_title=='etiquetas'){
      //return t('Tags');
      $title=t('Filter by Tag');
  }else if($facet_title=='validador'){
      //return t('Validator');
      $title=t('Filter by Validator');
  }else if($facet_title=='source item type'){    
      //return t('Type');
      $title=t('Filter by Type');
  }else if($facet_title=='channel item category'){
      //return t('Category');
      $title=t('Filter by Category');
  }else if($facet_title=='validate status'){
      $title=t('Filter by Status');
  //intelsat-2015
  }else if($facet_title=='filename'){
      $title=t('Filter by Attachment');
  }else{
    $title=t('Filter by @title:', array('@title' => $facet_title));
  }
  $result=my_get_icono_action('filtrar_por',$title,'filtrar_por').'&nbsp;'.$title;
  return $result;
}
//intelsat-2014
function buho_apachesolr_search_suggestions($variables) {
  return '';  
  /*if(!user_access('root')){
      return '';
  }
  //
  $output = '<div class="spelling-suggestions">';
  $output .= '<dl class="form-item"><dt><strong>' . t('Did you mean') . '</strong></dt>';
  foreach ((array) $variables as $link) {
    $output .= '<dd>' . $link . '</dd>';
  }
  $output .= '</dl></div>';
  return $output;*/
}
//intelsat-2015
function buho_tagadelic_weighted($terms) {
  $output = '';
  foreach ($terms as $term) {
    //intelsat-2015
    $url='';  
    $options=array('attributes' => array('class' => "tagadelic level$term->weight", 'rel' => 'tag'));  
    if(hontza_canal_rss_is_publico_activado()){    
        $url=publico_term_path($term);  
    }else if(hontza_solr_is_solr_activado()){
        $url=hontza_solr_search_get_tag_filtrado_solr_url($term->tid,$url,$query);
        if(!empty($query)){
            $options['query']=$query;
        }
    }
    if(empty($url)){
      $url=taxonomy_term_path($term);  
    }
    $output .= l($term->name,$url,$options) ." \n";
    //
  }
  return $output;
}
//intelsat-2015
function buho_facetapi_link_inactive($variables){
  // Builds accessible markup.
  // @see http://drupal.org/node/1316580
  $accessible_vars = array(
    'text' => $variables['text'],
    'active' => FALSE,
  );
  $accessible_markup = theme('facetapi_accessible_markup', $accessible_vars);

  // Sanitizes the link text if necessary.
  $sanitize = empty($variables['options']['html']);
  $variables['text'] = ($sanitize) ? check_plain($variables['text']) : $variables['text'];

  // Adds count to link if one was passed.
  if (isset($variables['count'])) {
    $variables['text'] .= ' ' . theme('facetapi_count', $variables);
  }
  //intelsat-2015
  if(hontza_solr_search_is_profundidad_categoria_tematica($variables['text'])){
    $variables['text']=hontza_solr_search_get_text_by_profundidad_categoria_tematica($variables['text'],'',$profundidad);  
  }
  //
  // Resets link text, sets to options to HTML since we already sanitized the
  // link text and are providing additional markup for accessibility.
  $variables['text'] .= $accessible_markup;
  $variables['options']['html'] = TRUE;
  return theme_facetapi_link($variables);
}
//intelsat-2015
function buho_facetapi_link_active($variables) {

  // Sanitizes the link text if necessary.
  $sanitize = empty($variables['options']['html']);
  $link_text = ($sanitize) ? check_plain($variables['text']) : $variables['text'];

  // Theme function variables fro accessible markup.
  // @see http://drupal.org/node/1316580
  $accessible_vars = array(
    'text' => $variables['text'],
    'active' => TRUE,
  );

  // Builds link, passes through t() which gives us the ability to change the
  // position of the widget on a per-language basis.
  $replacements = array(
    //intelsat-2015  
    //'!facetapi_deactivate_widget' => theme('facetapi_deactivate_widget'),
    '!facetapi_deactivate_widget' => theme('facetapi_deactivate_widget',$variables),  
    '!facetapi_accessible_markup' => theme('facetapi_accessible_markup', $accessible_vars),
  );
  $variables['text'] = t('!facetapi_deactivate_widget !facetapi_accessible_markup', $replacements);
  $variables['options']['html'] = TRUE;
  //intelsat-2015
  if(hontza_solr_search_is_profundidad_categoria_tematica($link_text)){
    $link_text=hontza_solr_search_get_text_by_profundidad_categoria_tematica($link_text,'active',$profundidad);  
  }
  //
  return theme_facetapi_link($variables) . $link_text;
}
function buho_tagadelic_more($vid) {
  $url="tagadelic/chunk/$vid";  
  if(is_dashboard()){
    return hontza_inicio_view_all($url,t('View all Tags'));
  }else{
    if(hontza_canal_rss_is_publico_activado()){
        if(publico_is_pantalla_publico('vigilancia')){
            $url=publico_get_url_more_tags($vid);
        }    
    }  
    return "<div class='more-link'>". l(t('more tags'), $url) ."</div>";
  }  
}
//intelsat-2015
function buho_links($links_in, $attributes = array('class' => 'links')) {
  global $language;
  $output = '';
  //intelsat-2015
  $links=$links_in;
  $is_comentario_links=0;
  if(isset($links['is_comentario_links'])){
      $is_comentario_links=$links['is_comentario_links'];
      unset($links['is_comentario_links']);
  }
  //
  if (count($links) > 0) {
    //intelsat-2015
    if($is_comentario_links){  
        $output = '<ul' . drupal_attributes($attributes) . ' style="height:auto;>';
    }else{
        $output = '<ul' . drupal_attributes($attributes) . '>';  
    }
    //
    $num_links = count($links);
    $i = 1;
    //intelsat-2015
    if($is_comentario_links){
        $links=array_reverse($links,true);
        if(isset($links[0]) && empty($links[0])){
            unset($links[0]);
            $my_array=array();
            $my_array[0]='';
            $links=array_merge($my_array,$links);
        }
    }
    //
    foreach ($links as $key => $link) {
      /*
      //intelsat-2015  
      if($is_comentario_links){
          if(empty($link)){
              continue;
          }          
      }
      //
      */
      $class = $key;

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class .= ' first';
      }
      if ($i == $num_links) {
        $class .= ' last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page())) && (empty($link['language']) || $link['language']->language == $language->language)) {
        $class .= ' active';
      }
      //intelsat-2015
      $link_style='';
      if($is_comentario_links){
        $link_style=' style="float:left;padding:0px 5px;"';
      }
      //
      $output .= '<li' . drupal_attributes(array('class' => $class)) . ''.$link_style.'>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        if($is_comentario_links){
            $icono=buho_get_comentario_link_icono($key);
            $link['html']=true;
            $output .= l($icono, $link['href'], $link);
        }else{
            $output .= l($link['title'], $link['href'], $link);
        }    
      }
      else if (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>\n";
    }
    $output .= '</ul>';
  }

  return $output;
}
//intelsat-2015
function buho_get_comentario_link_icono($key){
  return red_movil_get_comentario_link_icono($key);
}
function buho_preprocess_node(&$variables) {
  $node = $variables['node'];
  if (module_exists('taxonomy')) {
    $variables['taxonomy'] = taxonomy_link('taxonomy terms', $node);
  }
  else {
    $variables['taxonomy'] = array();
  }

  if ($variables['teaser'] && $node->teaser) {
    $variables['content'] = $node->teaser;
  }
  elseif (isset($node->body)) {     
    $variables['content'] = $node->body;
  }
  else {
    $variables['content'] = '';
  }

  $variables['date']      = format_date($node->created);
  $variables['links']     = !empty($node->links) ? theme('links', $node->links, array('class' => 'links inline')) : '';
  $variables['name']      = theme('username', $node);
  $variables['node_url']  = url('node/'. $node->nid);
  $variables['terms']     = theme('links', $variables['taxonomy'], array('class' => 'links inline'));
  $variables['title']     = check_plain($node->title);

  // Flatten the node object's member fields.
  $variables = array_merge((array)$node, $variables);

  // Display info only on certain node types.
  if (theme_get_setting('toggle_node_info_'. $node->type)) {
    $variables['submitted'] = theme('node_submitted', $node);
    $variables['picture'] = theme_get_setting('toggle_node_user_picture') ? theme('user_picture', $node) : '';
  }
  else {
    $variables['submitted'] = '';
    $variables['picture'] = '';
  }
  /*$variables['page_publico']=0;
  if(hontza_canal_rss_is_publico_activado()){
    $variables['page_publico']=publico_vigilancia_get_page_node_view($variables['page']);
  }*/
  // Clean up name so there are no underscores.
  $variables['template_files'][] = 'node-'. $node->type;
}
//intelsat-2015
function buho_set_title_preprocess_page(&$vars){
    //if(hontza_is_vigilancia_pantalla('ultimas')){
    $is_title=0;
    if(hontza_is_vigilancia('ultimas')){
        $vars['title']=t('Monitoring - Latest News');
        $is_title=1;
    }else if(hontza_canal_rss_is_pantalla_banner_node($banner_node)){
        $vars['title']=panel_admin_banner_get_title_resumen($banner_node->body,70);
        $is_title=1;
    }
    if($is_title){
        $my_site_name=variable_get('site_name', 'Drupal');
        if(red_is_subdominio_red_alerta()){
            $my_site_name='ALERTA';
        }
        $vars['head_title']=$vars['title'].' | '.$my_site_name;
    }
    return $my_title;
}
//intelsat-2015
function buho_node_submitted($node) {
  if(in_array($node->type,array('grupo_inicio','grupos_ayuda','visualizador_proyecto'))){
      return '';
  }  
  return t('Submitted by !username on @datetime',
    array(
      '!username' => theme('username', $node),
      '@datetime' => format_date($node->created),
    ));
}
//intelsat-2015
function buho_textfield($element) {
  $size = empty($element['#size']) ? '' : ' size="'. $element['#size'] .'"';
  //intelsat-2015
  red_solr_inc_set_form_link_maxlength($element);  
  $maxlength = empty($element['#maxlength']) ? '' : ' maxlength="'. $element['#maxlength'] .'"';
  $class = array('form-text');
  $extra = '';
  $output = '';
  if ($element['#autocomplete_path'] && menu_valid_path(array('link_path' => $element['#autocomplete_path']))) {
    drupal_add_js('misc/autocomplete.js');
    $class[] = 'form-autocomplete';
    $extra =  '<input class="autocomplete" type="hidden" id="'. $element['#id'] .'-autocomplete" value="'. check_url(url($element['#autocomplete_path'], array('absolute' => TRUE))) .'" disabled="disabled" />';
  }
  _form_set_class($element, $class);

  if (isset($element['#field_prefix'])) {
    $output .= '<span class="field-prefix">'. $element['#field_prefix'] .'</span> ';
  }

  $output .= '<input type="text"'. $maxlength .' name="'. $element['#name'] .'" id="'. $element['#id'] .'"'. $size .' value="'. check_plain($element['#value']) .'"'. drupal_attributes($element['#attributes']) .' />';

  if (isset($element['#field_suffix'])) {
    $output .= ' <span class="field-suffix">'. $element['#field_suffix'] .'</span>';
  }

  return theme('form_element', $element, $output) . $extra;
}
//intelsat-2015
function buho_preprocess_user_profile(&$variables) {
  $variables['profile'] = array();
  // Sort sections by weight
  uasort($variables['account']->content, 'element_sort');
  // Provide keyed variables so themers can print each section independantly.
  foreach (element_children($variables['account']->content) as $key) {
    $variables['profile'][$key] = drupal_render($variables['account']->content[$key]);
  }
  // Collect all profiles to make it easier to print all items at once.
  $variables['user_profile'] = implode($variables['profile']);
}
function buho_select($element) {
  $element=red_solr_inc_get_select_options_label($element);
  $select = '';
  $size = $element['#size'] ? ' size="' . $element['#size'] . '"' : '';
  _form_set_class($element, array('form-select'));
  $multiple = $element['#multiple'];
  return theme('form_element', $element, '<select name="' . $element['#name'] . '' . ($multiple ? '[]' : '') . '"' . ($multiple ? ' multiple="multiple" ' : '') . drupal_attributes($element['#attributes']) . ' id="' . $element['#id'] . '" ' . $size . '>' . form_select_options($element) . '</select>');
}
//intelsat-2016
function buho_apachesolr_search_noresults() {
  $html=array();
  $html[]=t('<ul style="clear:both;">
<li>Check if your spelling is correct, or try removing filters.</li>
<li>Remove quotes around phrases to match each word individually: <em>"blue drop"</em> will match less than <em>blue drop</em>.</li>
<li>You can require or exclude terms using + and -: <em>big +blue drop</em> will require a match on <em>blue</em> while <em>big blue -drop</em> will exclude results that contain <em>drop</em>.</li>
</ul>');
  red_solr_inc_add_remaining_html($html);
  return implode('',$html);
}
function buho_admin_menu_icon() {
  global $base_url;    
  $favicon=(theme_get_setting('toggle_favicon') ? theme_get_setting('favicon') : base_path() .'misc/favicon.ico');
  $favicon=hontza_canal_rss_get_favicon_url('',$favicon);
  if(empty($favicon)){
    $favicon=$base_url.'/sites/default/files/buho_favicon.ico';
  }
  return '<img class="admin-menu-icon" src="'. $favicon .'" width="16" height="16" alt="'. t('Home') .'" />';
}