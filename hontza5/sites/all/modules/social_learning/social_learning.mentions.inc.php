<?php
function social_learning_mentions_api_mentions_callback(){
    $output='';
    $output.=social_learning_create_menu();
    $headers=array();
    $headers[]=array('data'=>t('Mention id'),'field'=>'mentions_id');    
    $headers[]=array('data'=>t('Profile id'),'field'=>'profiles_id');
    $headers[]=array('data'=>t('Resource id'),'field'=>'resource_id');
    //$headers[]=array('data'=>t('Resource'),'field'=>'resource');
    $headers[]=array('data'=>t('Mention'),'field'=>'mention');
    $headers[]=array('data'=>t('Card'),'field'=>'card');
    $headers[]='';
    $mentions_array=social_learning_mentions_get_mentions_array();
    $my_limit=20;
    //
    $sort='desc';
    $field='mentions_id';
    $is_numeric=1;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Mention id')){
            $field='mentions_id';
        }else if($order==t('Profile id')){
            $field='profiles_id';
        }else if($order==t('Resource id')){
            $field='resources_id';
        }else if($order==t('Resource')){
            $field='resource';
            $is_numeric=0;
        }else if($order==t('Mention')){
            $field='mention';
            $is_numeric=0;
        }else if($order==t('Card')){
            $field='card';
            $is_numeric=0;
        }          
    }
    //
    $rows=array();
    $num_rows = FALSE;
    if(!empty($mentions_array)){
        foreach($mentions_array as $i=>$r){
                $resources_id=social_learning_get_resource_id_by_url($r->resource);
                $mentions_id=social_learning_mentions_get_mention_id_by_url($r->mention_url);
                $profiles_id=social_learning_get_profile_id_by_url($r->profile);
                $row[0]=$mentions_id;                
                $row[1]=$profiles_id;
                $row[2]=l($resources_id,'social_learning/api/resources/'.$resources_id);        
                $row[3]=$r->mention;
                $row[4]=social_learning_collections_create_text_links($r->card);
                $row[5]=social_learning_mentions_api_mentions_define_acciones($row[0]);
                $row['resources_id']=$resources_id;
                $row['mentions_id']=$mentions_id;
                $row['profiles_id']=$profiles_id;
                $row['mention']=$r->mention;
                $row['card']=$r->card;
                $rows[]=$row;
                $num_rows = TRUE;    
        }
    }
    //
    $rows=array_ordenatu($rows,$field,$sort,$is_numeric);
    $rows=my_set_estrategia_pager($rows, $my_limit);
    //$rows=social_learning_topics_set_fields($rows);
    $rows=hontza_unset_array($rows,array('resources_id','mentions_id','profiles_id','resource','mention','card'));
    
    if ($num_rows) {
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    
    return $output;
}
function social_learning_mentions_get_mentions_array(){
    $url=hontza_social_define_url('api/mentions');
    $content=file_get_contents($url);
    $result=json_decode($content);
    return $result;
}
function social_learning_mentions_get_mention_id_by_url($mention){
    $url=hontza_social_define_url('api/mentions');
    $result=str_replace($url,'',$mention);
    $result=trim($result,'/');
    return $result;
}
function social_learning_mentions_api_mentions_define_acciones($id){
    $html=array();
    $html[]=l(t('Mention'),'social_learning/api/mentions/'.$id);
    return implode('&nbsp;',$html);
}
function social_learning_mentions_api_mentions_view_callback(){
    $html=array();
    $id=arg(3);
    $row=social_learning_mentions_get_mentions_row($id);
    //
    $style='style="float:left;clear:both;padding-bottom:5px;"';
    //
    $mention_id=social_learning_mentions_get_mention_id_by_url($row->mention_url);
    $profile_id=social_learning_get_profile_id_by_url($row->profile);
    $resource_id=social_learning_get_resource_id_by_url($row->resource);
    $html[]=social_learning_create_menu();
    $html[]='<div '.$style.'>';
    $html[]='<label style="float:left;">';
    $html[]='<b>'.t('Mention id').':&nbsp;</b>';
    $html[]='</label>';
    $html[]='<div style="float:left;">'.$mention_id.'</div>';
    $html[]='</div>';    
    //
    $html[]='<div '.$style.'>';
    $html[]='<label style="float:left;">';
    $html[]='<b>'.t('Profile id').':&nbsp;</b>';
    $html[]='</label>';
    $html[]='<div style="float:left;">'.$profile_id.'</div>';
    $html[]='</div>';
    //
    $html[]='<div '.$style.'>';
    $html[]='<label style="float:left;">';
    $html[]='<b>'.t('Resource id').':&nbsp;</b>';
    $html[]='</label>';
    $html[]='<div style="float:left;">'.l($resource_id,'social_learning/api/resources/'.$resource_id).'</div>';
    $html[]='</div>';
     //
    $html[]='<div '.$style.'>';
    $html[]='<label style="float:left;">';
    $html[]='<b>'.t('Mention').':&nbsp;</b>';
    $html[]='</label>';
    $html[]='<div style="float:left;">'.$row->mention.'</div>';
    $html[]='</div>'; 
     //
    $html[]='<div '.$style.'>';
    $html[]='<label style="float:left;">';
    $html[]='<b>'.t('Card').':&nbsp;</b>';
    $html[]='</label>';
    $html[]='<div style="float:left;">'.$row->card.'</div>';
    $html[]='</div>';
    //
    $html[]='<div '.$style.'>';
    $html[]=l(t('Return'),'social_learning/api/mentions');
    $html[]='</div>';
    return implode('',$html);
}
function social_learning_mentions_get_mentions_row($id){
     $url=hontza_social_define_url('api/mentions/'.$id);
     $content=file_get_contents($url);
     $result=json_decode($content);     
     return $result;
}
function social_learning_topics_set_fields($rows){
    $result=$rows;
    if(!empty($result)){
        foreach($result as $i=>$row){
            $resource_row=social_learning_get_resources_row($row['resources_id']);
            if(isset($resource_row->title) && !empty($resource_row->title)){                    
                $result[$i][2]=l($resource_row->title,'social_learning/api/resources/'.$row['resource_id']);
                $result[$i]['resource']=$resource_row->title;
            }            
        }
    }
    return $result;
}