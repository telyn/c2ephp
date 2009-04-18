<?php
//take off the two unprintable characters after Creatures Evolution Engine - Archived information file. zLib 1.13 compressed.
require_once(dirname(__FILE__).'/../pray/extract.php');
require_once(dirname(__FILE__).'/../pray/history.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/Archiver.php');

$agent = new Agent(new FileReader($argv[1]));
$agent->Parse();

$blocks = $agent->GetBlocks('GLST');

$glst = $blocks[0]['Content'];
$glst = substr($glst,strpos($glst,chr(0x1A).chr(0x04))+2);


$data = gzuncompress($glst);
$h = fopen($argv[1].'.glst','wb');
fwrite($h,$data);
fclose($h);
$reader = new StringReader($data);
$history = new CreatureHistory($reader);
print_r($history->Decode());


?>