<?php
chdir('..');
include('caos/highlight/highlight.php');
$cos = file_get_contents('tests/caos.cos');
echo HighlightCaos($cos);
?>