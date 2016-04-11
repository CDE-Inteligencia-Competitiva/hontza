<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract concept tags from a web URL.
$result = $alchemyObj->URLGetRankedConcepts("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract concept tags from a text string.
$result = $alchemyObj->TextGetRankedConcepts("This thing has a steering wheel, tires, and an engine.  Do you know what it is?");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract concept tags from a HTML document.
$result = $alchemyObj->HTMLGetRankedConcepts($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
