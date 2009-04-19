<?php
include(dirname(__FILE__).'/../pray/extract.php');
$agent = new PRAYFile(file_get_contents('testcreature.creature'));
$agent->Parse();
 
 ExtractCompleteAgent('testcreature.creature');
?>