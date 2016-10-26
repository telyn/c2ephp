<?php

require_once(dirname(__FILE__).'/TagBlock.php');
require_once(dirname(__FILE__).'/DSEXBlock.php');

/// @brief Docking Station starter family description block
/**
 * The fields in a DFAM block are identical to those in a DSEX block
 * so this class simply extends DSEXBlock.
 */
class DFAMBlock extends DSEXBlock {
    /// @brief Instantiates a new DFAMBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
	 * @param PRAYFile $prayfile The PRAYFile that this DFAM block belongs to.
	 * @param $name The block's name.
	 * @param $content The binary data of this block. May be null.
	 * @param $flags The block's flags
	 */
	public function DFAMBlock($prayfile, $name, $content, $flags) {
		parent::TagBlock($prayfile, $name, $content, $flags, PRAY_BLOCK_DFAM);

	}
}
?>
