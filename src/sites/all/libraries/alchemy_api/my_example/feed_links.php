<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract RSS / ATOM feed links from a web URL.
$result = $alchemyObj->URLGetFeedLinks("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract RSS / ATOM feed links from a HTML document.
$result = $alchemyObj->HTMLGetFeedLinks($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
