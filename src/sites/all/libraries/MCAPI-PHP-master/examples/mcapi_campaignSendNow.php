<?php
/**
This Example shows how to immediately send a Campaign via the MCAPI class.
**/
require_once 'inc/MCAPI.class.php';
//require_once 'inc/config.inc.php'; //contains apikey

//intelsat-2016
$apikey="4dc50eae734b6bb1e09a8afa36314053-us13";
$campaignId="e49eeecb76";
$campaignId="d6204db039"; 

$api = new MCAPI($apikey);

$retval = $api->campaignSendNow($campaignId);

if ($api->errorCode){
	echo "Unable to Send Campaign!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
} else {
	echo "Campaign Sent!\n";
}

?>
