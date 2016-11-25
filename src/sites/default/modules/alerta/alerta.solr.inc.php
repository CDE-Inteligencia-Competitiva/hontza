<?php
function alerta_solr_simular_alerta_solr_callback(){
        $canal_busqueda_array=hontza_get_all_nodes(array('canal_busqueda'));
        if(!empty($canal_busqueda_array)){
           foreach($canal_busqueda_array as $i=>$canal_busqueda){ 
               $nid_array=alerta_solr_get_canal_busqueda_nid_array($canal_busqueda);
               print 'num='.count($nid_array).'<BR>';
           }         
        }
    return date('Y-m-d H:i'); 
}
function alerta_solr_get_keys($path){
    $find='my_solr/my_search/';
    $pos=strpos($path,$find);
    if($pos===FALSE){
        return '';
    }
    $result=substr($path,$pos+strlen($find));
    return $result;
}
function alerta_solr_get_busqueda_nid_array($query){
    $result=array();
    $res=db_query($query);
    while($row=db_fetch_object($res)){
        $result[]=$row->nid;
    }
    return $result;
}
function alerta_solr_get_grupo_nid_by_node($node){
    if(isset($node->og_groups) && !empty($node->og_groups)){
        $values=array_values($node->og_groups);
        if(isset($values[0]) && !empty($values[0])){
            return $values[0]; 
        }
    }    
    return '';
}
function alerta_solr_get_solr_filters_active($facet,$my_solr_conditions){
    $result=array();        
    //if($facet['name']=='im_og_gid'){
        $conditions=$my_solr_conditions;
        //echo print_r($conditions,1);exit();
        if(!empty($conditions)){
            foreach($conditions as $key=>$value_array){
                if(!empty($value_array) && is_array($value_array)){
                    foreach($value_array as $i=>$value){
                        $find=$facet['name'].':';
                        $pos=strpos($value,$find);
                        if($pos===FALSE){
                            continue;
                        }else{
                            $indice=substr($value,$pos+strlen($find));
                            $result[$indice]=array();
                            $result[$indice]['field_alias']=$facet['name'];
                            $result[$indice]['value']=$indice;
                            $result[$indice]['pos']=$i;
                        }
                    }
                }
            }
        }        
    //}
    return $result;
}
function alerta_solr_get_solr_busqueda_nid_array($results){
    $result=array();
    if(!empty($results)){
        foreach($results as $i=>$row){
            $result[]=$row['node']->entity_id;
        }
    }
    return $result;
}
function alerta_solr_alerta_busqueda_access(){
    return TRUE;
    /*if(is_super_admin()){
        return TRUE;
    }
    return FALSE;*/
}
function alerta_solr_busqueda_form(){
    return alerta_canal_form();
}
function alerta_solr_busqueda_form_submit(&$form, &$form_state){
    alerta_canal_form_submit($form,$form_state);
}
function alerta_solr_get_canal_busqueda_nid_array($canal_busqueda,$value_in=''){
    $result=array();
    if(empty($value_in)){
        $value=$canal_busqueda->field_canal_busqueda_busqueda[0]['value'];
    }else{
        $value=$value_in;
    }    
           if(hontza_solr_is_solr_activado() && hontza_solr_is_busqueda_solr($value)){            
               if($value=='my_solr/my_search?f=Array'){
                   return $result;
               }
               $url_info=parse_url($value);
               $keys=alerta_solr_get_keys($url_info['path']);
               parse_str($url_info['query'],$conditions);
               //if(!empty($keys)){
                $results=array();
                $search_page = apachesolr_search_page_load('my_search');
                /*$s='keys='.$keys.'<br>';
                $s.=print_r($conditions,1);
                $s.='search_page='.print_r($search_page,1).'<br>';*/
                $results=apachesolr_search_search_results($keys,$conditions,$search_page,1);
                $nid_array=alerta_solr_get_solr_busqueda_nid_array($results);
                $num=count($nid_array);
                /*$s.='nid_array='.$num.'<br>';
                $s.='########################<br>';                
                drupal_set_message($s);*/                
                $result=$nid_array;
                /*if(module_exists('crm_exportar')){
                    print count($result).'<br>';
                }*/
               //} 
           }else{
               $view='';
               $url_info=parse_url($value);
               parse_str($url_info['query'],$query_array);
               $my_request=$query_array;
               $grupo_nid=alerta_solr_get_grupo_nid_by_node($canal_busqueda);
               my_carpeta_dinamica_pre_execute($view,$my_request,$grupo_nid,1);
               $nid_array=alerta_solr_get_busqueda_nid_array($view->build_info['query']);
               //print 'nid_array(slow)='.count($nid_array).'<br>';
               //exit();
               $result=$nid_array;
           }
           return $result;
}
function alerta_solr_resultado_busqueda_callbak(){
    $output='';
    //intelsat-2015
    alerta_solr_resultado_busqueda_access_denied();
    $my_limit=9000;
    $rows=array();
    $row=get_alerta_user_row(arg(1));
    $headers=array();
    $headers[0]=t('Title');
    $headers[1]='';
    //intelsat-2015
    //$canales=get_alerta_user_canal_nid_array($row->id,$row->is_todos_canales,$row->is_canales_que_filtro);
    $canales=get_alerta_user_canal_nid_array($row->id,$row->is_todos_canales,$row->is_canales_que_filtro,$row->tipo);
    if(!empty($canales)){
        foreach($canales as $i=>$canal_nid){
            $canal_busqueda=node_load($canal_nid);
            if(isset($canal_busqueda->nid) && !empty($canal_busqueda->nid)){
                $r=array();
                $r[0]=$canal_busqueda->title;
                $r[1]=alerta_solr_define_resultado_busqueda_acciones($canal_busqueda->nid,$row);
                $rows[]=$r;
            }    
        }
    }
    if (count($rows)>0) {
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }else {
        $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    drupal_set_title(t('Searches'));
    return $output;
}
function alerta_solr_is_busqueda_by_row($row){
    if(isset($row->id) && !empty($row->id)){
        if($row->tipo=='busqueda'){
            return 1;
        }
        return 0;
    }else{
        if(alerta_solr_is_alerta_user('add_alerta_busqueda')){
            return 1;
        }
    }
    return 0;
}
function alerta_solr_define_resultado_busqueda_acciones($canal_busqueda_nid,$row){
    $html=array();
    $html[]=l(my_get_icono_action('viewmag', t('Results')),'alerta_user/'.$canal_busqueda_nid.'/resultado_canal_busqueda',array('html'=>true,'attributes'=>array('target'=>'_blank'),'query'=>drupal_get_destination()));
    return add_div_actions(implode('&nbsp;',$html));
}
function alerta_solr_resultado_canal_busqueda_callbak(){
    $output='';
    //intelsat-2015
    alerta_solr_resultado_busqueda_access_denied();
    $canal_busqueda_nid=arg(1);
    $canal_busqueda=node_load($canal_busqueda_nid);
    $num_rows=FALSE;
    $my_limit=10;
    $rows=array();
    $kont=0;
    if(isset($canal_busqueda->nid) && !empty($canal_busqueda->nid)){
        drupal_set_title(t('Results').': '.$canal_busqueda->title);
        $nid_array=alerta_solr_get_canal_busqueda_nid_array($canal_busqueda);
        if(!empty($nid_array)){
            foreach($nid_array as $i=>$nid){
                $node=node_load($nid);
                if(isset($node->nid) && !empty($node->nid)){
                    $rows[$kont]=new stdClass();
                    $rows[$kont]->view= node_view($node, 1);
                    $kont++;
                    $num_rows=TRUE;
                }
            }
            $output.='<b>'.t('Results').':</b>&nbsp;'.count($rows).'<BR>';
            $rows=my_set_estrategia_pager($rows,$my_limit);            
            $output.=set_array_view_html($rows);
        }
    }
    if ($num_rows) {
    /*$feed_url = url('estrategia_rss.xml', array('absolute' => TRUE));
    drupal_add_feed($feed_url, variable_get('site_name', 'Drupal') . ' ' . t('RSS'));*/
    /*
	$headers=array('nide','title');
	$output .= theme('table',$headers,$rows);
	*/	
	$output .= theme('pager', NULL, $my_limit);
    }
    else {

      $output = '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    return $output;
}
function alerta_solr_alerta_resultadobusqueda_access(){
    if(is_super_admin()){
        return TRUE;
    }
    return FALSE;
}
function alerta_solr_inc_set_conditions_solrsort($conditions,$is_alerta){
    $result=$conditions;
    if($is_alerta){
        if(isset($result['apachesolr_search_sort']) && !empty($result['apachesolr_search_sort'])){
            return $result;
        }
        $result['apachesolr_search_sort']='ds_created desc';
    }    
    //
    return $result;
}
function alerta_solr_is_alerta_user($konp=''){
    $param0=arg(0);
    if($param0=='alerta_user'){
        if(empty($konp)){
            return 1;
        }else{
            $param1=arg(1);
            if($param1==$konp){
                return 1;
            }
        }
    }
    return 0;
}
//intelsat-2015
function alerta_solr_inc_is_busqueda_rss_activado(){
    if(module_exists('busqueda_rss')){
        return 1;
    }
    return 0;
}    
//intelsat-2015
function alerta_solr_inc_get_busqueda_rss_solr_link($canal_busqueda_nid){
    $result='';
    if(!hontza_canal_rss_is_publico_exportar_rss_enviar_mail_desactivado($canal_busqueda_nid)){
        $result=l(my_get_icono_action('publico_solr_results_rss', t('Generate RSS')),'busqueda_rss/custom_exportar_busqueda_rss/'.$canal_busqueda_nid,array('html'=>TRUE,'attributes'=>array('target'=>'_blank','style'=>'padding-left:10px;')));    
    }
    return $result;    
}
//intelsat-2015
function alerta_solr_inc_is_busqueda_rss_custom_cron(){
        $param0=arg(0);
        if(!empty($param0) && $param0=='busqueda_rss'){
            $param1=arg(1);
            if(!empty($param1) && $param1=='custom_cron'){
                return 1;
            }
        }
    return 0;
}
//intelsat-2015
function alerta_solr_is_apachesolr_access_apachesolr_query_alter(){
    //drupal_set_message('alerta solr');
    if(alerta_solr_inc_is_busqueda_rss_custom_cron()){
        return 1;
    }
    if(alerta_solr_is_simular_alerta_solr()){
        return 1;
    }
    if(alerta_solr_is_cron()){
        return 1;
    }
    //intelsat-2016
    if(hontza_crm_inc_is_crm_exportar_noticias()){
        return 1;
    }
    return 0;
}
//intelsat-2015
function alerta_solr_is_simular_alerta_solr(){
    $param0=arg(0);
    if(!empty($param0) && $param0=='alerta'){
        $param1=arg(1);
        if(!empty($param1) && $param1=='simular_alerta_solr'){
            return 1;
        }
    }
    return 0;
}
//intelsat-2015
function alerta_solr_is_cron(){
   $filename = basename(request_uri());
   if($filename=='cron.php'){
       return 1;
   }
   return 0;
}
//intelsat-2015
function alerta_solr_is_show_resultado_busqueda(){
   if(is_super_admin()){
       return 1;       
   } 
   return 0;
}
//intelsat-2015
function alerta_solr_resultado_busqueda_access_denied(){
   if(!alerta_solr_is_show_resultado_busqueda()){
       drupal_access_denied();
       exit();
   } 
}