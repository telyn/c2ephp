<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class LIVEBlock extends TagBlock {
	public function LIVEBlock(&$prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags);

	}
}
?>
