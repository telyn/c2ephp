<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
class FILEBlock extends PrayBlock {
	public function FILEBlock($prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_FILE);
	}
	protected function CompileBlockData() {
		return $this->GetData();
	}
	protected function DecompileBlockData() {
		throw new Exception('It\'s impossible to decode a FILE.');
	}
}
?>
