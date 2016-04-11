<?php
// $Id: HontzafeedParser.inc,v 1.1 2010/07/16 21:35:08 karlosgliberal Exp $

/**
 * @file
 * First module on hontza distribution
 */

/**
 * A parser for the yql 
 */
class HontzafeedsParser extends FeedsParser {
 
 /**
 * Implementation of FeedsParser::parse().
 */
 public function parse(FeedsImportBatch $batch, FeedsSource $source) { 
  //gemini
  $my_row=my_get_row_import_html($source);
  $is_import_html=0;
  if(isset($my_row->id) && !empty($my_row->id)){
    $is_import_html=1;
  }
   
  //intelsat-2015
  $config=$source->getConfig();  
  $url=$config['FeedsHTTPFetcher']['source'];  
  //    
    //intelsat-2014
    if(hontza_social_is_activado()){
        /*$config=$source->getConfig();  
        $url=$config['FeedsHTTPFetcher']['source'];*/
        if(hontza_social_is_json_url($url)){
            hontza_social_add_json_items($url,$source,$batch);
            return;
        }
    }else{
        //intelsat-2016
        $json_fields='';            
        if(hontza_canal_json_is_canal_json($source,$json_fields)){
            canal_json_FeedsSyndicationParser($batch,$source,$json_fields);
            return;
        }else if(hontza_canal_rss_is_kimonolabs_json($batch->getRaw())){
            kimonolabs_FeedsSyndicationParser($batch,$source);
            return;
        }
    }
    //
  
  //intelsat-2015  
  //$xml = new SimpleXMLElement($batch->getRaw());
  $rss_content=$batch->getRaw();
  if(!red_is_xml($rss_content)){
      return '';
  }
  $xml = new SimpleXMLElement($rss_content);
    
  /*if(user_access('root')){
      print $batch->getRaw();
      exit();
  }*/
  
  //gemini-2014
  $is_atom=0;
  if(hontza_is_atom_by_xml($xml)){
    $is_atom=1;  
    /*$config=$source->getConfig();  
    $url=$config['FeedsHTTPFetcher']['source'];*/
    $atom_string=hontza_atom2rrs($url);
    $xml = new SimpleXMLElement($atom_string);    
  }
  //
 //gemini-2013
  if(isset($xml->results->rss)){     
    $sets = $xml->results->rss;
    $all = sizeof($sets);
    for ($i=0; $i<$all; $i++) {
      $r = $sets[$i];
        foreach ($r->channel->item as $elemento) {
          //intelsat-2015
          if(hontza_solr_search_is_canal_correo($source,'',$url)){              
              $elemento->guid=hontza_solr_search_create_guid($elemento);
          }
          //
          //gemini-2013
          $elemento=hontza_fix_hontzafeeds_elemento($elemento);
          //gemini-2014
          if(hontza_is_item_duplicado($elemento,$source,'',$url)){       
            continue;
          }
          //gemini-2014
          if(is_filtrar_numero_palabras($elemento->title,$is_import_html,$my_row,$source)){
          //            
              //gemini-2013
              //AVISO:::: se ha añadido string
              $item=array();
              $item['title'] = (string) $elemento->title;
              $item['description'] =  (string) $elemento->description;            
              $item['link'] =  (string) $elemento->link;
              $item['guid'] =  (string) $elemento->guid;
              //$item['field_uniq']=hontza_get_elemento_uniq($elemento);
              //gemini            
              //$item['description'] =apply_alchemy_api($item);
              //
              //intelsat-2016                                                  
              //$batch->addItem($item);
              $item_array[]=$item;
          }
        }
      }   
  }else{
    $sets = $xml->channel->item;    
    $all=0;
    if(isset($xml->channel) && isset($xml->channel->item)){
        $all=$sets->count();
    }
    
    $all=hontza_hound_limit_all($all);
    for ($i=0; $i<$all; $i++) {
      $elemento = $sets[$i];
      //intelsat-2015
      $is_canal_correo=0;
      if(hontza_solr_search_is_canal_correo($source,'',$url)){
        $elemento->guid=hontza_solr_search_create_guid($elemento);
        $is_canal_correo=1;
      }
      //          
      if($is_atom){
          $link=hontza_get_elemento_link_by_atom($elemento);
          unset($elemento->link);
          $elemento->link=$link;
      }
        //foreach ($r as $elemento) {
          //gemini-2013
          //$elemento=hontza_fix_hontzafeeds_elemento($elemento);
          //gemini-2014
          if(hontza_is_item_duplicado($elemento,$source,'',$url)){
              continue;
          }
          //gemini          
          if(is_filtrar_numero_palabras($elemento->title,$is_import_html,$my_row,$source)){
          //                          
              //gemini-2013
              //AVISO:::: se ha añadido string
              $item=array();
              $item['title'] = (string) $elemento->title;
              $item['description'] =  (string) $elemento->description;            
              $item['link'] =  (string) $elemento->link;
              //$item['guid'] =  (string) $elemento->guid;
              if($is_canal_correo){
                $item['guid'] =  (string) $elemento->guid;  
              }else{
                $item['guid'] =  (string) $elemento->link;
              }  
              //$item['field_uniq']=hontza_get_elemento_uniq($elemento);
              //gemini            
              //$item['description'] =apply_alchemy_api($item);
              //
              //intelsat-2015
              if(hontza_canal_rss_is_visualizador_activado()){
                  publico_vigilancia_add_item_tags_comments($item,$elemento);
              }
              //intelsat-2016                                                  
              //$batch->addItem($item);
              $item_array[]=$item;
          }
        //}
    }
  }
  if(!empty($item_array)){
      $item_array=array_reverse($item_array);
      foreach($item_array as $i=>$rss_item){
          $batch->addItem($rss_item);
      }
  }
 }
           

  /**
   * Implementation of FeedsParser::getMappingSources().
   */
   
  public function getMappingSources() {
    return array(
      'title' => array(
        'name' => t('title'),
        'description' => t('title'),
      ),
       'description' => array(
        'name' => t('description'),
        'description' => 'Esto es una descripcion',
      ),
       'link' => array(
        'name' => t('link'),
        'descripcion' => 'Link del item',
      ),
       'guid' => array(
        'name' => t('guid'),
        'descripcion' => 'guid del item',
      ),
    );
  }
}
//gemini
function filter($item, $wordlimit) {
//AVISO::::por si queremos utilizar el filtro de Alex Eguia
        $pieces = explode("<h2>", $item);
	$newscount = count($pieces)-1;
	$array = array();

	for($i=0;$i<$newscount;$i++) {
		$neww = $pieces[$i+1];
		$pieces2 = explode("</h2>", $neww);
		$finalnew = $pieces2[0];
		$newstitle = strip_tags($finalnew);

		if(substr_count($newstitle, ' ') >= $wordlimit-1) {
			$array[$newstitle] = $finalnew;
		}
	}

	var_dump($array);
	return $array;
}
//gemini
function my_get_row_import_html($source){
   //simulando
   //unset($_SESSION['yql_my_id']);
   //
   //set_session_yql_my_id($source);
   $yql_my_id=get_yql_my_id($source);
   //
   //if(isset($_SESSION['yql_my_id']) && !empty($_SESSION['yql_my_id'])){
   if(!empty($yql_my_id)){
            //$row=get_import_html_row($_SESSION['yql_my_id']);
            $row=get_import_html_row($yql_my_id);
            //unset($_SESSION['yql_my_id']);
            if(isset($row->id) && !empty($row->id)){
                return $row;
            }        
   }
   $my_result=array();
   $my_result=(object) $my_result;
   return $my_result;
}
//gemini
//function set_session_yql_my_id($source){
function get_yql_my_id($source){
   //if(isset($_SESSION['yql_my_id']) && !empty($_SESSION['yql_my_id'])){
    //OK
   //}else{
   //print $source->feed_nid;exit();
       $node=node_load($source->feed_nid);
       if(isset($node->nid) && !empty($node->nid)){
            if($node->type=='canal_de_yql'){
                if(isset($node->field_import_html_source_id[0]['value']) && !empty($node->field_import_html_source_id[0]['value'])){
                    $import_html_source_id=$node->field_import_html_source_id[0]['value'];
                    return $import_html_source_id;
                }
            }
        }
   //}
   return '';
}