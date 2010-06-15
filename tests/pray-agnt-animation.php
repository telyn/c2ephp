<?php
include(dirname(__FILE__).'/../agents/PRAYFile.php');

if($argv[1] != "") {
    $file = $argv[1];
} else {
    $file = 'rubber_ball.agents';
}
$agent = new PRAYFile(new FileReader($file));
$block = $agent->GetBlocks('AGNT');
print_r($block[0]->GetAgentAnimationAsSpriteFrame());

?>