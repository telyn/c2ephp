<?php
include('../cob/cob.php');
require_once('../support/FileReader.php');

$cob = new C1COB(new FileReader('carrotbeatle.cob'));
$data = $cob->GetData();
echo $data['picture']['spr'];
?>
