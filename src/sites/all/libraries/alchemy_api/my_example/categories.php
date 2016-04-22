<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Categorize a web URL.
$result = $alchemyObj->URLGetCategory("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");

// Categorize some text.
$result = $alchemyObj->TextGetCategory("Latest on the War in Iraq.");
echo "$result<br/><br/>\n";



// Categorize a HTML document.
$result = $alchemyObj->HTMLGetCategory($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
