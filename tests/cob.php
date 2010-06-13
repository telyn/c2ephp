<?php
include('../agents/COB.php');
require_once('../support/FileReader.php');

$cob = new COB(new FileReader($argv[1]));
$rcbname = substr($argv[1],0,-3).'rcb';
if(file_exists($rcbname)) {
	$rcbreader = new FileReader($rcbname);
	$blocks = $cob->GetBlocks(COB_BLOCK_AGENT);
	print_r($blocks);
	$blocks[0]->AddC1RemoveScriptFromRCB($rcbreader);
}
print_r($cob);
?>
