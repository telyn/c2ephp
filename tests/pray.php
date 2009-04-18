<?php
include(dirname(__FILE__).'/../pray/agent.php');
if($argv[1] != "") {
    $file = $argv[1];
} else {
    $file = 'rubber_ball.agents';
}
$agent = new Agent(file_get_contents($file));
if(!$agent->Parse()) {
    echo "Error!\n";
} else {
	$testhandle = fopen('test.txt','wb');
	fwrite($testhandle,print_r($agent->GetBlocks(),true));
	fclose($testhandle);
}

?>