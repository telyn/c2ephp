<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EGGBlock extends TagBlock {
	public function EGGBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EGG);

	}
}
?>
