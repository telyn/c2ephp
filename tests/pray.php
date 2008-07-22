<?php
include(dirname(__FILE__).'/../pray/agent.php');
if($argv[1] != "") {
    $file = $argv[1];
} else {
    $file = 'rubber_ball.agents';
}
$ball = new Agent(file_get_contents($file));
if(!$ball->Parse()) {
    echo "Error!\n";
} else {
    print_r($ball->GetBlocks());
}

?>