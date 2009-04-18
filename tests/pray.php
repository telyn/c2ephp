<?php
include(dirname(__FILE__).'/../pray/agent.php');
include(dirname(__FILE__).'/../support/FileReader.php');
if($argv[1] != "") {
    $file = $argv[1];
} else {
    $file = 'rubber_ball.agents';
}
$agent = new Agent(new FileReader($file));
if(!$agent->Parse()) {
    echo "Error!\n";
} else {
	$testhandle = fopen('test.txt','wb');
	fwrite($testhandle,print_r($agent->GetBlocks(),true));
	fclose($testhandle);
}

?>