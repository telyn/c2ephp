<?php
require_once(dirname(__FILE__).'/../support/StringReader.php');
$contents = file_get_contents(dirname(__FILE__).'/rubber_ball.agents');
$reader1 = new StringReader($contents);
$f = fopen(dirname(__FILE__).'/rubber_ball.agents','rb');

$reader2 = new StringReader($f);

echo "'".$reader1->GetSubString(20,10)."'\n";
echo "'".$reader2->GetSubString(20,10)."'\n";
echo "'".$reader1->Read(8)."'\n";
echo "'".$reader2->Read(8)."'\n";
?>