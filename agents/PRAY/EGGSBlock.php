<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EGGSBlock extends TagBlock {
	public function EGGSBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EGGS);

	}
}
?>
