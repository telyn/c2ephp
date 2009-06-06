<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
interface ICOB {
	public function LoadCOB(IReader $reader);
	public function GetData();

}
?>
