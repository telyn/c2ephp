<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
class FILEBlock extends PrayBlock {
	public function FILEBlock($prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_FILE);

	}
}
?>
