<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract a title from a web URL.
$result = $alchemyObj->URLGetTitle("http://www.techcrunch.com/", AlchemyAPI::JSON_OUTPUT_MODE);
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");

// Categorize a HTML document.
$result = $alchemyObj->HTMLGetCategory($htmlFile, "http://www.test.com/", AlchemyAPI::JSON_OUTPUT_MODE);
echo "$result<br/><br/>\n";

// Extract first link from an URL.
$result = $alchemyObj->URLGetConstraintQuery("http://microformats.org/wiki/hcard", "1st link", AlchemyAPI::JSON_OUTPUT_MODE);
echo "$result<br/><br/>\n";

// Extract RSS / ATOM feed links from a HTML document.
$result = $alchemyObj->HTMLGetFeedLinks($htmlFile, "http://www.test.com/", AlchemyAPI::JSON_OUTPUT_MODE);
echo "$result<br/><br/>\n";

// Extract topic keywords from a text string.
$result = $alchemyObj->TextGetRankedKeywords("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?", AlchemyAPI::JSON_OUTPUT_MODE);
echo "$result<br/><br/>\n";

?>
