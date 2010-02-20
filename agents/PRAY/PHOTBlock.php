<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
require_once(dirname(__FILE__).'/../../sprites/S16File.php');
require_once(dirname(__FILE__).'/../../support/StringReader.php');
class PHOTBlock extends PrayBlock {
	public function PHOTBlock($prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_PHOT);
	}
	public function GetS16File() {
		return new S16File(new StringReader($this->GetData()));
	}
	public function OutputPNG() {
		return $this->GetS16File()->OutputPNG(0);
	}
}
?>
