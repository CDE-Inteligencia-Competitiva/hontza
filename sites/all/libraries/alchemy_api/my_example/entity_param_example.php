<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
//include "../module/AlchemyAPI_CURL.php";


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");

// Create a named entity API parameters object	
$namedEntityParams = new AlchemyAPI_NamedEntityParams();

// Turn off quotations extraction
$namedEntityParams->setQuotations(0);

// Turn off entity disambiguation
$namedEntityParams->setDisambiguate(0);

// Turn on sentiment analysis
$namedEntityParams->setSentiment(1);

// Extract a ranked list of named entities from a web URL, using the previously created parameters object.
$result = $alchemyObj->URLGetRankedNamedEntities("http://www.techcrunch.com/", "xml", $namedEntityParams);
echo "$result<br/><br/>\n";


// Extract a ranked list of named entities from a text string, using the previously created parameters object.
$result = $alchemyObj->TextGetRankedNamedEntities("Hello my name is Bob.  I am speaking to you at this very moment.  Are you listening to me, Bob?", "xml", $namedEntityParams);
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract a ranked list of named entities from a HTML document, using the previously created parameters object.
$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile, "http://www.test.com/", "xml", $namedEntityParams);
echo "$result<br/><br/>\n";


?>
