<?php
//take off the two unprintable characters after Creatures Evolution Engine - Archived information file. zLib 1.13 compressed.
//require_once(dirname(__FILE__).'/../agents/CreatureHistory.php');
require_once(dirname(__FILE__).'/../agents/PRAYFile.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/Archiver.php');

$agent = new PRAYFile(new FileReader($argv[1]));
$agent->Parse();

/*$blocks = $agent->GetBlocks('GLSTBlock');
$glst = $blocks[0]->GetData();
$glst = substr($glst,strpos($glst,chr(0x1A).chr(0x04))+2);
*/


?>
