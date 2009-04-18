<?php
include('../support/Archiver.php');
$h = fopen($argv[1].'.uncompressed','wb');
fwrite($h,DeArchive(file_get_contents($argv[1])));
fclose($h);
?>