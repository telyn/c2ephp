<?php
require_once(dirname(__FILE__).'/../agents/PRAYFile.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');

$agent = new PRAYFile(new FileReader($argv[1]));
$names = array();
foreach($agent->GetBlocks() as $block) {
	$blockname = $block->GetName().'.'.$block->GetType();
	while(in_array($blockname,$names)) {
		$blockname .= '-';
	}
	echo $blockname."\n";
	$fh = fopen($blockname,'w');
	fwrite($fh,$block->GetData());
	fclose($fh);
}

?>