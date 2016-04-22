<?php
function get_rss_gen($url){
	//$url = $_GET['q'];

	date_default_timezone_set('UTC');
	$today = date('l jS \of F Y h:i:s A');
	//$h2r = 'http://page2rss.com/api/page?url='.$url;
        $h2r = 'http://page2rss.com/api/page?url='.urlencode($url);
	$json = file_get_contents($h2r,0,null,null);
	$json_output = json_decode($json, true);

	if($json_output==null) {
		$_url = $url;
		$_json = file_get_contents($_url,0,null,null);
		$_json_output = json_decode($_json, true);

		for($i=0;$i<count($_json_output["data"]);$i++) {
			$head = $_json_output["data"][$i]["head"];
			$body = $_json_output["data"][$i]["body"];

			$st_ = explode("</script>", $head);
			$link1 = $st_[1];
			$link2 = str_replace("%2F", "/", $link1);
			$str = str_replace("%3A", ":", $link2);

			if($_json_output["data"][$i]["body"]["line"] != 0) {
				$_comment_url = $h2r . $_json_output["data"][$i]["body"]["line"];
				$_json_comm = file_get_contents($_comment_url,0,null,null);
				$_json_output_comm = json_decode($_json_comm, true);

				for($j=0;$j<count($_json_output_comm["data"]);$j++) {
					$tag1 = explode("<", $body);
					$tag2 = explode("/>", $body);
					$_json_output_comm = $tag1[1];
					$tag = $_json_output_comm["data"][$j]["<"];
					$tagEnd = $_json_output_comm["data"][$j]["/>"];
				}
			}
		}
		$json = file_get_contents($h2r,0,null,null);
		$json_output = json_decode($json, true);
	}

	$json = file_get_contents($h2r,0,null,null);
	$json_output = json_decode($json, true);

	$rssurl = $json_output["page"]["feed"];
	//header('Location: '.$rssurl);
	return $rssurl;
}
function rssgenparam($url1, $param1) {
//print 'url1='.$url1.'<BR>';
//print 'param1='.$param1.'<BR>';
    $url = str_replace("hontza",$param1,$url1);
    //gemini-2013
    $url = str_replace("HONTZA",$param1,$url);
    $url = str_replace("Hontza",$param1,$url);
    //
/*
//gemini-2013
if(is_super_admin()){
    print 'url='.$url.'<BR>';exit();
}
//
*/    
    
	date_default_timezone_set('UTC');
	$today = date('l jS \of F Y h:i:s A');
	//$h2r = 'http://page2rss.com/api/page?url='.$url;
        $h2r = 'http://page2rss.com/api/page?url='.urlencode($url);
        //print $h2r.'<BR>';
	$json = file_get_contents($h2r,0,null,null);
	$json_output = json_decode($json, true);

	if($json_output==null) {
		$_url = $url;
		$_json = file_get_contents($_url,0,null,null);
		$_json_output = json_decode($_json, true);

		for($i=0;$i<count($_json_output["data"]);$i++) {
			$head = $_json_output["data"][$i]["head"];
			$body = $_json_output["data"][$i]["body"];

			$st_ = explode("</script>", $head);
			$link1 = $st_[1];
			$link2 = str_replace("%2F", "/", $link1);
			$str = str_replace("%3A", ":", $link2);

			if($_json_output["data"][$i]["body"]["line"] != 0) {
				$_comment_url = $h2r . $_json_output["data"][$i]["body"]["line"];
				$_json_comm = file_get_contents($_comment_url,0,null,null);
				$_json_output_comm = json_decode($_json_comm, true);

				for($j=0;$j<count($_json_output_comm["data"]);$j++) {
					$tag1 = explode("<", $body);
					$tag2 = explode("/>", $body);
					$_json_output_comm = $tag1[1];
					$tag = $_json_output_comm["data"][$j]["<"];
					$tagEnd = $_json_output_comm["data"][$j]["/>"];
				}
			}
		}
		$json = file_get_contents($h2r,0,null,null);
		$json_output = json_decode($json, true);
	}

	$json = file_get_contents($h2r,0,null,null);
	$json_output = json_decode($json, true);

	$rssurl = $json_output["page"]["feed"];
        //print $rssurl;exit();
	//header('Location: '.$rssurl);
	return $rssurl;
}
?>
