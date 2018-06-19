<?php

//$url1=file_get_contents("http://192.169.1.17/hontza/google_sheet/croneako.json");
$url1=file_get_contents("http://92.243.9.240/hontza3/google_sheet/croneako.json");

$cronerako=json_decode($url1,true);
$a=count($cronerako);
for($x=0; $x<$a; $x++){
	$sheet_id=$cronerako[$x+1][0];
	$num_hoja=$cronerako[$x+1][1];

	$id=$sheet_id;
	$orria=$num_hoja;

	$url = 'https://spreadsheets.google.com/feeds/list/'.$id.'/'.$orria.'/public/values?alt=json';

	$file= file_get_contents($url);
	$json = json_decode($file);
	$rows = $json->{'feed'}->{'entry'};
	$json_sortzeko=array();
	$json_fields=array();
	$i='0';

	foreach($rows as $row) {
	$json_sortzeko[$i]['title'] =$row->{'gsx$title'}->{'$t'};
	$json_sortzeko[$i]['link']=$row->{'gsx$link'}->{'$t'};
	$json_sortzeko[$i]['description']=$row->{'gsx$description'}->{'$t'};
	$json_sortzeko[$i]['url_json']=$url;
	$i++;
	}


	$fitxero='google_sheet/id_'.$id.'_'.$orria.'.json';
    $bidaltzeko=json_encode($json_sortzeko);
    file_put_contents($fitxero, $bidaltzeko);



}

?>