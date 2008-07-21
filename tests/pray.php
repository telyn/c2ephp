<?php
include(dirname(__FILE__).'/../pray/agent.php');

$ball = new AgentFile(file_get_contents('rubber_ball.agents'));
if(!$ball->Parse()) {
    echo "Error!\n";
} else {
    print_r($ball->GetBlocks());
}
?>