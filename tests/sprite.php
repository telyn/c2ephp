<?php
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../sprites/S16File.php');
require_once(dirname(__FILE__).'/../sprites/C16File.php');
require_once(dirname(__FILE__).'/../sprites/SPRFile.php');

$extension = substr(strtolower($argv[1]),-4);
$file;
switch($extension) {
  case '.s16':
    $file = new S16File(new FileReader($argv[1]));
    break;
  case '.c16':
    $file = new C16File(new FileReader($argv[1]));
    break;
  case '.spr':
    $file = new SPRFile(new FileReader($argv[1]));
    break;
}
print_r($file);
?>