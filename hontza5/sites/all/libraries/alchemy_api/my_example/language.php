<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Detect the language for a web URL.
$result = $alchemyObj->URLGetLanguage("http://www.techcrunch.fr/");
echo "$result<br/><br/>\n";


// Detect the language for a text string. (requires at least 100 characters text)
$result = $alchemyObj->TextGetLanguage("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Detect the language for a HTML document.
$result = $alchemyObj->HTMLGetLanguage($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


?>
