<?php

require_once(dirname(__FILE__).'/EXPCBlock.php');
require_once(dirname(__FILE__).'/TagBlock.php');
/// @brief Class for DSEX (DS Export) blocks
/**
 * DSEX and EXPC seem to contain identical tags, so this class
 * simple extends EXPC.
 */
class DSEXBlock extends EXPCBlock {
    /// @brief Instantiates a DSEXBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
	 * @param $prayfile The PRAYFile that this DFAM block belongs to.
	 * @param $name The block's name.
	 * @param $content The binary data of this block. May be null.
	 * @param $flags The block's flags
	 */
	public function DSEXBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DSEX);
	}
}
?>
