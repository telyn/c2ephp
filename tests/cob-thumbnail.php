<?php
include('../agents/COB.php');
require_once('../support/FileReader.php');

$cob = new COB(new FileReader($argv[1]));

$blocks = $cob->GetBlocks(COB_BLOCK_AGENT);
print $blocks[0]->GetThumbnail()->ToPNG();
?>