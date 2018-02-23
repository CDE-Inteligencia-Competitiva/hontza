<?php
$chan = new DOMDocument(); 
$chan->load('http://administraciondeliberativa.blogspot.com/feeds/posts/default'); /* load channel */
$sheet = new DOMDocument(); 
$sheet->load('atom2rss.xsl'); /* use stylesheet from this page */
$processor = new XSLTProcessor();
$processor->registerPHPFunctions();
$processor->importStylesheet($sheet);
$result = $processor->transformToXML($chan);
print $result;
exit();