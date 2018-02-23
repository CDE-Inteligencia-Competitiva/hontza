<?php


// Load the AlchemyAPI module code.
include "../module/AlchemyAPI.php";
// Or load the AlchemyAPI PHP+CURL module.
/*include "../module/AlchemyAPI_CURL.php";*/


// Create an AlchemyAPI object.
$alchemyObj = new AlchemyAPI();


// Load the API key from disk.
$alchemyObj->loadAPIKey("api_key.txt");


// Extract sentiment from a web URL.
$result = $alchemyObj->URLGetTextSentiment("http://www.techcrunch.com/");
echo "$result<br/><br/>\n";


// Extract sentiment from a text string.
$result = $alchemyObj->TextGetTextSentiment("It's wonderful when the sun is shining and ABBA is playing.");
echo "$result<br/><br/>\n";


// Load a HTML document to analyze.
$htmlFile = file_get_contents("data/example.html");


// Extract sentiment from a HTML document.
$result = $alchemyObj->HTMLGetTextSentiment($htmlFile, "http://www.test.com/");
echo "$result<br/><br/>\n";


// Enable entity-level sentiment.
$namedEntityParams = new AlchemyAPI_NamedEntityParams();
$namedEntityParams->setSentiment(1);

// Extract entities with entity-level sentiment.
$result = $alchemyObj->TextGetRankedNamedEntities("Wyle E. Coyote is slow.", "xml", $namedEntityParams);
echo "$result<br/><br/>\n";


// Enable keyword-level sentiment.
$keywordParams = new AlchemyAPI_KeywordParams();
$keywordParams->setSentiment(1);

// Extract keywords with keyword-level sentiment.
$result = $alchemyObj->TextGetRankedKeywords("Wyle E. Coyote is slow.", "xml", $keywordParams);
echo "$result<br/><br/>\n";

// Enable Targeted Sentiment
$targetedSentimentParams = new AlchemyAPI_TargetedSentimentParams();
$targetedSentimentParams->setShowSourceText(1);

$result = $alchemyObj->TextGetTargetedSentiment("This car is terrible.", "car", "xml", $targetedSentimentParams);
echo "$result<br/><br/>\n";

$result = $alchemyObj->URLGetTargetedSentiment("http://techcrunch.com/2012/03/01/keen-on-anand-rajaraman-how-walmart-wants-to-leapfrog-over-amazon-tctv/", "Walmart", "xml", $targetedSentimentParams);
echo "This:$result<br/><br/>\n";

$result = $alchemyObj->HTMLGetTargetedSentiment($htmlFile, "http://www.test.com/", "WujWuj", "xml", $targetedSentimentParams);
echo "$result<br/><br/>\n";

?>
