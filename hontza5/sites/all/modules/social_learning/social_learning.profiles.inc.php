<?php
function social_learning_profiles_api_profiles_callback(){
    $output='';
    $output.=social_learning_create_menu();
    $headers=array();
    $headers[]=array('data'=>t('Social Network'),'field'=>'social_network');
    $headers[]=array('data'=>t('Username'),'field'=>'username');
    $headers[]=array('data'=>t('Url'),'field'=>'url');
    //$headers[]='';
    $profiles_array=social_learning_profiles_get_profiles_array();
    $my_limit=20;
    //
    $sort='asc';
    $field='social_network';
    $is_numeric=0;
    if(isset($_REQUEST['sort']) && !empty($_REQUEST['sort'])){
        $sort=$_REQUEST['sort'];
    }
    if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
        $order=$_REQUEST['order'];
        if($order==t('Social Network')){
            $field='social_network';
            $is_numeric=0;
        }else if($order==t('Username')){
            $field='username';
            $is_numeric=0;
        }else if($order==t('Url')){
            $field='url';
            $is_numeric=0;
        }     
    }
    //
    $rows=array();
    $num_rows = FALSE;
    if(!empty($profiles_array)){
        foreach($profiles_array as $i=>$r){
                $row[0]=$r->social_network;
                $row[1]=$r->username;        
                //$row[2]=l($r->url,$r->url,array('absolute'=>TRUE));
                $row[2]=$r->url;
                $row['social_network']=$r->social_network;
                $row['username']=$r->username;        
                $row['url']=$r->url;
                $rows[]=$row;
                $num_rows = TRUE;    
        }
    }
    
    $rows=array_ordenatu($rows,$field,$sort,$is_numeric);    
    $rows=my_set_estrategia_pager($rows, $my_limit);
    $rows=hontza_unset_array($rows,array('social_network','username','url'));
    
    if ($num_rows) {
        $output .= theme('table',$headers,$rows);
        $output .= theme('pager', NULL, $my_limit);
    }
    else {
      $output.= '<div id="first-time">' .t('There are no contents'). '</div>';
    }
    
    return $output;
}
function social_learning_profiles_get_profiles_array(){
    $url=hontza_social_define_url('api/profiles');
    $content=file_get_contents($url);
    $result=json_decode($content);
    return $result;
}