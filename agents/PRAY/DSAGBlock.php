<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class DSAGBlock extends TagBlock {
	public function DSAGBlock(&$prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DSAG);
	}
}
?>
