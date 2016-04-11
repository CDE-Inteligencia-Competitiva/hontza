<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
//include "../module/AlchemyAPI_CURL.php";


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();

// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract a ranked list of relations from a web URL.
$result = $alchemyObj->URLGetRelations("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract a ranked list of relations from a text string.
$result = $alchemyObj->TextGetRelations("Hello my name is Bob.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract a ranked list of relations from a HTML document.
$result = $alchemyObj->HTMLGetRelations($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";



$relationParams = new AlchemyAPI_RelationParams();

// Turn off quotations extraction
$relationParams->SetSentiment(1);
$relationParams->SetEntities(1);
$relationParams->SetDisambiguate(1);
$relationParams->SetSentimentExcludeEntities(1);
$result = $alchemyObj->TextGetRelations("Madonna enjoys tasty Pepsi.  I love her style.", "xml", $relationParams);
echo "$result<br/><br/>\n";

$relationParams->SetRequireEntities(1);
$result = $alchemyObj->TextGetRelations("Madonna enjoys tasty Pepsi.  I love her style.", "xml", $relationParams);
echo "$result<br/><br/>\n";


?>
