<?php

require_once(dirname(__FILE__).'/TagBlock.php');

/** \brief Block to enable some kind of compatibility with Amazing Virtual Sea Monkeys agents
 * This class will probably remain forever untested and unused.
 */
class LIVEBlock extends AGNTBlock {
	public function LIVEBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_LIVE);

	}
}
?>
