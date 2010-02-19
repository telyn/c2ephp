<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
class PHOTBlock extends PrayBlock {
	public function PHOTBlock(&$prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags);

	}
}
?>
