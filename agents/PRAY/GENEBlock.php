<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
class GENEBlock extends PrayBlock {
	public function GENEBlock(&$prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags);

	}
}
?>
