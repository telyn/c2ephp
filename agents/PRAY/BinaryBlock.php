<?php
require_once(dirname(__FILE__).'/PrayBlock.php');

/** A block to allow simple, non-parsed binary content.
 * This class is essentially un-necessary, but it's good for debugging
 * as you can use it to test PRAY compilation without needing to test
 * individual block types' compilation.
 * Additionally, it allows us to create blocks for types we don't know of yet.
 */
class BinaryBlock extends PrayBlock {
	private $binarydata;
	
	public function BinaryBlock($type,$name,$content) {
		parent::PrayBlock(null,$name,'',0,$type);
		$this->binarydata = $content;
	}
	public function Compile() {
		return $this->EncodeBlockHeader(strlen($this->binarydata)).$this->binarydata;
	}
}

?>