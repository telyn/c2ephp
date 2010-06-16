<?php

require_once(dirname(__FILE__).'/TagBlock.php');

/// \brief Creatures 3 starter family description block
class SFAMBlock extends TagBlock {
	/** \brief Instantiates a new SFAMBlock
	 * \param $prayfile The PRAYFile that this SFAM block belongs to.
	 * \param $name The block's name.
	 * \param $content The binary data of this block. May be null.
	 * \param $flags The block's flags
	 */
	public function SFAMBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_SFAM);

	}
}
?>
