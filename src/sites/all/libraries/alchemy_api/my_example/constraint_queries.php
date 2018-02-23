<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract first link from an URL.
$result = $alchemyObj->URLGetConstraintQuery("http://microformats.org/wiki/hcard", "1st link");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract first link from a HTML.
$result = $alchemyObj->HTMLGetConstraintQuery($htmlFile, "http://www.test.com/", "1st link");
echo "$result<br/><br/>\n";


?>
