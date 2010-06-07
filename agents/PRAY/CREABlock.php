<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
class CREABlock extends CreaturesArchiveBlock {
	public function CREABlock($prayfile,$name,$content,$flags) {
		parent::CreaturesArchiveBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_CREA);
	}
	protected function CompileBlockData() {
		return $this->GetData();
	}
	protected function DecompileBlockData() {
		throw new Exception('I don\'t know how to decompile CREA blocks!');
	}
}
?>
