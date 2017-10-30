<?php
/**
This Example shows how to create a basic campaign via the MCAPI class.
**/
require_once 'inc/MCAPI.class.php';
require_once 'inc/config.inc.php'; //contains apikey

//intelsat-2016
$apikey="4dc50eae734b6bb1e09a8afa36314053-us13";

$api = new MCAPI($apikey);

$type = 'regular';
//intelsat2016
/*
$opts['list_id'] = 'f9ee6d8616';
$opts['subject'] = 'Test Newsletter Subject';
$opts['from_email'] = 'mailchimp@example.org'; 
$opts['from_name'] = 'ACME, Inc.';

$opts['tracking']=array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);

$opts['authenticate'] = true;
$opts['analytics'] = array('google'=>'my_google_analytics_key');
$opts['title'] = 'Test Newsletter Title';

$content = array('html'=>'some pretty html content *|UNSUB|* message', 
		  'text' => 'text text text *|UNSUB|*'
		);*/


$opts['list_id'] = '23c961e1b4';
$opts['subject'] = 'Creando campaing php';
$opts['from_email'] = 'bulegoa@netkam.com'; 
$opts['from_name'] = 'Bulegoa mailchimp';

//$opts['tracking']=array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);

$opts['authenticate'] = true;
//$opts['analytics'] = array('google'=>'my_google_analytics_key');
$opts['title'] = 'Creando campaing php title';

$content = array('html'=>'<b>Prueba</b>', 
		  'text' => 'Texto'
		);



/** OR we could use this:
$content = array('html_main'=>'some pretty html content',
		 'html_sidecolumn' => 'this goes in a side column',
		 'html_header' => 'this gets placed in the header',
		 'html_footer' => 'the footer with an *|UNSUB|* message', 
		 'text' => 'text content text content *|UNSUB|*'
		);
$opts['template_id'] = "1";
**/

$retval = $api->campaignCreate($type, $opts, $content);

if ($api->errorCode){
	echo "Unable to Create New Campaign!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
} else {
	echo "New Campaign ID:".$retval."\n";
}

?>
