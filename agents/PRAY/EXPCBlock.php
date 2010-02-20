<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EXPCBlock extends TagBlock {
	public function EXPCBlock(&$prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EXPC);

	}
}
?>
