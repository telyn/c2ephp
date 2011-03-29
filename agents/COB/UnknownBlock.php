<?php
require_once(dirname(__FILE__).'/COBBlock.php');

/// Simple class to allow for extra block types.
class COBUnknownBlock extends COBBlock {
	private $contents;

	/** Creates a new COBUnknown block with the given type and contents
	 * \param $type The four-character type of the block
	 * \param $contents The contents of the block
	 */
	public function COBUnknownBlock($type,$contents) {
		parent::COBBlock($type);
	}
	/// Gets the block's contents
	public function GetContents() {
		return $this->contents;
	}
}
?>