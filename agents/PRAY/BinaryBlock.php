<?php
require_once(dirname(__FILE__).'/PrayBlock.php');

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