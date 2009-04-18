<?php
//take off the two unprintable characters after Creatures Evolution Engine - Archived information file. zLib 1.13 compressed.
include(dirname(__FILE__).'/../pray/extract.php');
$agent = new Agent(file_get_contents($argv[1]));
$agent->Parse();
$blocks = $agent->GetBlocks('GLST');

$glst = $blocks[0]['Content'];
$glst = substr($glst,strpos($glst,chr(0x1A).chr(0x04))+2);
$data = gzuncompress($glst);
$h = fopen($argv[1].'.glst','wb');
fwrite($h,$data);
fclose($h);
?>