<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
//include "../module/AlchemyAPI_CURL.php";


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract a ranked list of named entities from a web URL.
$result = $alchemyObj->URLGetRankedNamedEntities("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract a ranked list of named entities from a text string.
$result = $alchemyObj->TextGetRankedNamedEntities("Hello my name is Bob.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract a ranked list of named entities from a HTML document.
$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
