<?php
require_once(dirname(__FILE__).'/PrayBlock.php');

/// @brief A block to allow simple, non-parsed binary content.
/**
 * This class is essentially un-necessary, but it's good for
 * debugging as you can use it to test PRAY compilation without
 * needing to test individual block types' compilation. \n
 * Additionally, it allows us to create blocks for types we don't
 * know of yet. \n
 * BinaryBlocks cannot be read from a PRAYFile.
 */
class BinaryBlock extends PrayBlock {
    /// @cond INTERNAL_DOCS
    
    private $binarydata;
    /// @endcond

    /// @brief Instantiate a BinaryBlock
    /**
     * @param $type The four-character code for the block.
     * @param $name The block's name.
     * @param $content The content of the block as a binary string.
     */
	public function BinaryBlock($type, $name, $content) {
		parent::PrayBlock(null, $name, '', 0, $type);
		$this->binarydata = $content;
    }
    /// @brief Compile the BinaryBlock
    /**
     * @return string compiled BinaryBlock as a binary string.
     */
	public function Compile() {
		return $this->EncodeBlockHeader(strlen($this->binarydata)).$this->binarydata;
	}
}

?>
