<?php
include(dirname(__FILE__).'/../agents/PRAYFile.php');

if($argv[1] != "") {
    $file = $argv[1];
} else {
    $file = 'rubber_ball.agents';
}
$agent = new PRAYFile(new FileReader($file));
print_r($agent->GetBlocks());

?>
