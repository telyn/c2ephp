<?php

require_once(dirname(__FILE__).'/EXPCBlock.php');
require_once(dirname(__FILE__).'/TagBlock.php');

class DSEXBlock extends EXPCBlock {
	public function DSEXBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DSEX);
	}
}
?>
