<?php

require_once(dirname(__FILE__).'/TagBlock.php');

/// @brief Creatures 3 starter family description block
/**
 * The tags in a SFAM block are the same as those in an
 * EXPC block so we inherit from EXPC to keep things DRY.
 *
 */
class SFAMBlock extends EXPCBlock {

	/// @brief Instantiates a new SFAMBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
	 * @param PRAYFile $prayfile The PRAYFile that this DFAM block belongs to.
	 * @param $name The block's name.
	 * @param $content The binary data of this block. May be null.
	 * @param $flags The block's flags
	 */
	public function SFAMBlock($prayfile, $name, $content, $flags) {
		parent::TagBlock($prayfile, $name, $content, $flags, PRAY_BLOCK_SFAM);

	}
}
?>
