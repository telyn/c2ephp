<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class DFAMBlock extends TagBlock {
	public function DFAMBlock(&$prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DFAM);

	}
}
?>
