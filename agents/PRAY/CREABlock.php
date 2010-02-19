<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
class CREABlock extends PrayBlock {
	public function CREABlock(&$prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags);

	}
}
?>
