<?php
require_once(dirname(__FILE__).'/COBBlock.php');

/// @brief Simple class to allow for extra block types.
class COBUnknownBlock extends COBBlock {

    /// @cond INTERNAL_DOCS
    
    private $contents;

    /// @endcond

    /// @brief Creates a new COBUnknown block with the given type and contents
    /**
	 * @param $type The four-character type of the block
	 * @param $contents The contents of the block
	 */
	public function COBUnknownBlock($type,$contents) {
		parent::COBBlock($type);
    }

	/// @brief Gets the block's contents
	public function GetContents() {
		return $this->contents;
	}
}
?>
