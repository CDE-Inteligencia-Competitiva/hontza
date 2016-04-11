<?php

set_time_limit(0);
echo("<br></br>Checking functions without supplying them parameters");
CheckNoParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Named Entity Parameters");
CheckNamedEntityParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Relation Parameters");
CheckRelationParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Categorization Parameters");
CheckCategorizationParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Keyword Parameters");
CheckKeywordsParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Concept Parameters");
CheckConceptsParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Language Parameters");
CheckLanguageParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Text Parameters");
CheckTextParams();
echo("<br></br>Succeeded");
echo("<br></br>Checking Parameter Types");
CheckParameterTypes();
echo("<br></br>Succeeded");
echo("<br></br>Checking JSON with Parameters");
CheckJSON();
echo("<br></br>Succeeded");

echo("Testing Complete");


function CheckNoParams() {

	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");


	// Extract a ranked list of named entities from a web URL.
	
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->TextGetRankedNamedEntities("Hello my name is Bob.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetCategory("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->TextGetCategory("Latest on the War in Iraq.");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetCategory($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetConstraintQuery("http://microformats.org/wiki/hcard", "1st link");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetConstraintQuery($htmlFile, "http://www.test.com/", "1st link");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetFeedLinks("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetFeedLinks($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetRankedKeywords("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->TextGetRankedKeywords("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetRankedKeywords($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetRankedConcepts("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->TextGetRankedConcepts("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetRankedConcepts($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetLanguage("http://www.techcrunch.fr/");
	CheckForOKStatus($result);

	$result = $alchemyObj->TextGetLanguage("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetLanguage($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetMicroformats("http://microformats.org/wiki/hcard");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetMicroformats($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetTitle("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetText("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->URLGetRawText("http://www.techcrunch.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetTitle($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetText($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);

	$result = $alchemyObj->HTMLGetRawText($htmlFile, "http://www.test.com/");
	CheckForOKStatus($result);
}

function CheckNamedEntityParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
	$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	//Checking Quotation Param
	$namedEntityParams = new AlchemyAPI_NamedEntityParams();
	$namedEntityParams->setQuotations(0);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$quotations = ($doc->xpath("//quotation"));
	assert(count($quotations) == 0);

	$namedEntityParams->setQuotations(1);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$quotations = ($doc->xpath("//quotation"));
	assert(count($quotations) != 0);

	//Checking LinkedData Param
	$namedEntityParams = new AlchemyAPI_NamedEntityParams();
	$namedEntityParams->setLinkedData(0);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//dbpedia"));
	assert(count($matches) == 0);

	$namedEntityParams->setLinkedData(1);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//dbpedia"));
	assert(count($matches) != 0);

	//Checking Disambiguated Param
	$namedEntityParams = new AlchemyAPI_NamedEntityParams();
	$namedEntityParams->setDisambiguate(0);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0); 

	$namedEntityParams->setDisambiguate(1);
	$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) != 0);

	$namedEntityParams->setDisambiguate(0);
	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $namedEntityParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0);

	$namedEntityParams->setDisambiguate(1);
	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $namedEntityParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) != 0);

	//Checking XPath Param
	$namedEntityParams = new AlchemyAPI_NamedEntityParams();
	$namedEntityParams->setSourceText("xpath");
	$namedEntityParams->setXPath("//a");
	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $namedEntityParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//entity"));
	assert(count($matches) != 0);
	
	//Checking Custom Param
	$namedEntityParams->setCustomParameters("disambiguate", "0");
	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $namedEntityParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0);

	$namedEntityParams->setCustomParameters("disambiguate", "1");
	$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $namedEntityParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) != 0);
	
	try {
		$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2_nolinks, "http://www.test.com/", "xml", $namedEntityParams);
		//should return an error.  If there is no error, xpath found a link when there are none.
		assert(1==0);
	}
	catch(Exception $e) {
		
	}

}

function CheckRelationParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
	$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	//Checking Entities Param
	$relationParams = new AlchemyAPI_RelationParams();
	$relationParams->setEntities(0);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//entity"));
	assert(count($matches) == 0);

	$relationParams->setEntities(1);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//entity"));
	assert(count($matches) != 0);

	//Checking Sentiment Param
	$relationParams = new AlchemyAPI_RelationParams();
	$relationParams->setSentiment(0);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//sentiment"));
	assert(count($matches) == 0);

	$relationParams->setSentiment(1);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//sentiment"));
	assert(count($matches) != 0);

	//Checking LinkedData Param
	$relationParams = new AlchemyAPI_RelationParams();
	$relationParams->setEntities(1);
	$relationParams->setLinkedData(0);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//dbpedia"));
	assert(count($matches) == 0);

	$relationParams->setEntities(1);
	$relationParams->setLinkedData(1);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//dbpedia"));
	assert(count($matches) != 0);

	//Checking Disambiguated Param
	$relationParams = new AlchemyAPI_RelationParams();
	$relationParams->setEntities(1);
	$relationParams->setDisambiguate(0);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0); 

	$relationParams->setEntities(1);
	$relationParams->setDisambiguate(1);
	$result = $alchemyObj->URLGetRelations("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) != 0);

	$relationParams->setDisambiguate(0);
	$result = $alchemyObj->HTMLGetRelations($htmlFile2, "http://www.test.com/", "xml", $relationParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0);

	$relationParams->setDisambiguate(1);
	$result = $alchemyObj->HTMLGetRelations($htmlFile2, "http://www.test.com/", "xml", $relationParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) != 0);

	//Checking XPath Param
	$relationParams = new AlchemyAPI_RelationParams();
	$relationParams->setSourceText("xpath");
	$relationParams->setXPath("//a");
	$result = $alchemyObj->HTMLGetRelations($htmlFile2, "http://www.test.com/", "xml", $relationParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//relation"));
	assert(count($matches) != 0);
	
	//Checking Custom Param
	$relationParams->setCustomParameters("disambiguate", "0");
	$result = $alchemyObj->HTMLGetRelations($htmlFile2, "http://www.test.com/", "xml", $relationParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//disambiguated"));
	assert(count($matches) == 0);

	$relationParams->setCustomParameters("entities", "1");
	$result = $alchemyObj->HTMLGetRelations($htmlFile2, "http://www.test.com/", "xml", $relationParams);
	CheckForOKStatus($result);
	$doc = simplexml_load_string($result);
	$matches = ($doc->xpath("//entity"));
	assert(count($matches) != 0);
	
	try {
		$result = $alchemyObj->HTMLGetRelations($htmlFile2_nolinks, "http://www.test.com/", "xml", $relationParams);
		//should return an error.  If there is no error, xpath found a link when there are none.
		assert(1==0);
	}
	catch(Exception $e) {
		
	}

}

function CheckCategorizationParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
		$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$categoryParams = new AlchemyAPI_CategoryParams();
	$result = $alchemyObj->HTMLGetCategory($htmlFile2_nolinks, "http://www.test.com/", "xml", $categoryParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//category"));
	assert(count($matches) != 0); 
	
	/*try {
		$categoryParams->setSourceText("xpath");
		$categoryParams->setXPath("//a[@src = 'abc']");
		$result = $alchemyObj->HTMLGetCategory($htmlFile2_nolinks, "http://www.test.com/", "xml", $categoryParams);
		$doc = simplexml_load_string($result);
		CheckForOKStatus($result);
		$matches = ($doc->xpath("//category"));
		assert(count($matches) == 0);
	}
	catch(Exception $e) {
		//should not return an error, just no category
		assert(1==0);
	}*/
}

function CheckConceptsParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
		$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$conceptsParams = new AlchemyAPI_ConceptParams();
	$result = $alchemyObj->HTMLGetRankedConcepts($htmlFile2_nolinks, "http://www.test.com/", "xml", $conceptsParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//concept"));
	assert(count($matches) != 0); 
	
	try {
		$conceptsParams->setSourceText("xpath");
		$conceptsParams->setXPath("//a");
		$result = $alchemyObj->HTMLGetRankedConcepts($htmlFile2_nolinks, "http://www.test.com/", "xml", $conceptsParams);
		//should return an error.  If there is no error, xpath found a link when there are none.
		assert(1==0);
	}
	catch(Exception $e) {
	
	}
}

function CheckKeywordsParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
		$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$keywordsParams = new AlchemyAPI_KeywordParams();
	$result = $alchemyObj->HTMLGetRankedKeywords($htmlFile2_nolinks, "http://www.test.com/", "xml", $keywordsParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//keyword"));
	assert(count($matches) != 0); 
	
	try {
		$keywordsParams->setSourceText("xpath");
		$keywordsParams->setXPath("//a");
		$result = $alchemyObj->HTMLGetRankedKeywords($htmlFile2_nolinks, "http://www.test.com/", "xml", $keywordsParams);
		//should return an error.  If there is no error, xpath found a link when there are none.
		assert(1==0);
	}
	catch(Exception $e) {
	
	}
}

function CheckLanguageParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
		$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$languageParams = new AlchemyAPI_LanguageParams();
	$result = $alchemyObj->HTMLGetLanguage($htmlFile2_nolinks, "http://www.test.com/", "xml", $languageParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//language"));
	assert(count($matches) != 0); 
	
	try {
		$languageParams->setSourceText("xpath");
		$languageParams->setXPath("//a");
		$result = $alchemyObj->HTMLGetLanguage($htmlFile2_nolinks, "http://www.test.com/", "xml", $languageParams);
		//should return an error.  If there is no error, xpath found a link when there are none.
		assert(1==0);
	}
	catch(Exception $e) {
	
	}
}

function CheckTextParams() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
		$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$textParams = new AlchemyAPI_TextParams();
	$result = $alchemyObj->HTMLGetText($htmlFile2, "http://www.test.com/", "xml", $textParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);
	$matches = ($doc->xpath("//a"));
	assert(count($matches) == 0); 
	
	$textParams = new AlchemyAPI_TextParams();
	$textParams->setExtractLinks(1);
	$result = $alchemyObj->HTMLGetText($htmlFile2, "http://www.test.com/", "xml", $textParams);
	$doc = simplexml_load_string($result);
	CheckForOKStatus($result);

	
}

function CheckJSON() {

	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
	$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$textParams = new AlchemyAPI_TextParams();
	$result = $alchemyObj->HTMLGetText($htmlFile2, "http://www.test.com/", "json", $textParams);
	$obj = json_decode($result);
	assert($obj->{'status'} == "OK");
}

function CheckParameterTypes() {
	$alchemyObj = new AlchemyAPI();
	$alchemyObj->loadAPIKey("api_key.txt");

	$htmlFile = file_get_contents("../example/data/example.html");
	$htmlFile2 = file_get_contents("../example/data/example2.html");
	$htmlFile2_nolinks = file_get_contents("../example/data/example2_nolinks.html");
	
	$namedEntityParams = new AlchemyAPI_NamedEntityParams();
	$keywordParams = new AlchemyAPI_KeywordParams();
	
	try{
		$result = $alchemyObj->URLGetRankedNamedEntities("http://www.cnn.com/2010/HEALTH/06/03/gulf.fishermans.wife/index.html?hpt=C2", "xml", $keywordParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	
	try{
		$result = $alchemyObj->HTMLGetRankedNamedEntities($htmlFile2, "http://www.test.com/", "xml", $keywordParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->TextGetRankedNamedEntities("Hello my name is Bob.  I am speaking to you at this very moment.  Are you listening to me, Bob?","xml",$keywordParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
		$result = $alchemyObj->URLGetCategory("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}	
	try{
		$result = $alchemyObj->TextGetCategory("Latest on the War in Iraq.","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}	
	try{
		$result = $alchemyObj->HTMLGetCategory($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->URLGetConstraintQuery("http://microformats.org/wiki/hcard", "1st link","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->HTMLGetConstraintQuery($htmlFile, "http://www.test.com/", "1st link","xml",$namedEntityParams);	
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->URLGetFeedLinks("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->HTMLGetFeedLinks($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->URLGetRankedKeywords("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->TextGetRankedKeywords("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	try{
		$result = $alchemyObj->HTMLGetRankedKeywords($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
		$result = $alchemyObj->URLGetLanguage("http://www.techcrunch.fr/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
			$result = $alchemyObj->TextGetLanguage("Hello my name is Bob Jones.  I am speaking to you at this very moment.  Are you listening to me, Bob?","xml",$namedEntityParams);

		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
		$result = $alchemyObj->HTMLGetLanguage($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
		$result = $alchemyObj->URLGetMicroformats("http://microformats.org/wiki/hcard","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
		try{
		$result = $alchemyObj->HTMLGetMicroformats($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
			try{
		$result = $alchemyObj->URLGetTitle("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
			try{
		$result = $alchemyObj->URLGetText("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
			try{
		$result = $alchemyObj->URLGetRawText("http://www.techcrunch.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
			try{
		$result = $alchemyObj->HTMLGetTitle($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
			try{
		$result = $alchemyObj->HTMLGetText($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
				try{
		$result = $alchemyObj->HTMLGetRawText($htmlFile, "http://www.test.com/","xml",$namedEntityParams);
		
		//should throw an exception for mismatched parameters
		assert(1 == 0);
	}
	catch(Exception $e) {
	}
	


}




function CheckForOKStatus($result) {
	$doc = simplexml_load_string($result);
	$status = ($doc->xpath("/results/status"));
	assert("OK" == $status[0]);
}
?>
