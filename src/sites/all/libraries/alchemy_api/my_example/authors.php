<?php

// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");
// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract concept tags from a web URL.
//$result = $alchemyObj->URLGetRankedConcepts("http://www.techcrunch.com/");
$result = $alchemyObj->URLGetAuthor("http://www.politico.com/blogs/media/2012/02/detroit-news-ed-upset-over-romney-edit-115247.html");
echo "$result<br/><br/>\n";

$result = $alchemyObj->HTMLGetAuthor($htmlFile,"http://www.test.com");
echo "$result<br/><br/>\n";
?>
