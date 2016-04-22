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
$result = $alchemyObj->URLGetTitle("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract page text from a web URL (ignoring navigation links, ads, etc.).
$result = $alchemyObj->URLGetText("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract raw page text from a web URL (including navigation links, ads, etc.).
$result = $alchemyObj->URLGetRawText("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract a title from a HTML document.
$result = $alchemyObj->HTMLGetTitle($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


// Extract page text from a HTML document (ignoring navigation links, ads, etc.).
$result = $alchemyObj->HTMLGetText($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


// Extract raw page text from a HTML document (including navigation links, ads, etc.).
$result = $alchemyObj->HTMLGetRawText($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
