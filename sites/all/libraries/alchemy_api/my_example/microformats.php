<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract Microformats from a web URL.
$result = $alchemyObj->URLGetMicroformats("http://microformats.org/wiki/hcard");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/microformats.html");


// Extract Microformats from a HTML document.
$result = $alchemyObj->HTMLGetMicroformats($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
