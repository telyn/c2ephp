<?php
require_once(dirname(__FILE__).'/../agents/PRAYFile.php');
require_once(dirname(__FILE__).'/../agents/PRAY/BinaryBlock.php');


$prayfile = new PRAYFile();
for($i=1;$i<sizeof($argv);$i++) {
	$type = substr($argv[$i],-4);
	$name = substr($argv[$i],0,-5);
	echo $name.'.'.$type."\n";
	$content = file_get_contents($argv[$i]);
	$prayfile->AddBlock(new BinaryBlock($type,$name,$content));
}
$fh = fopen('output.pray','w');
fwrite($fh,$prayfile->Compile());
fclose($fh);
?>