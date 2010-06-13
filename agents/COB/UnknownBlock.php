<?php
require_once(dirname(__FILE__).'/COBBlock.php');
class COBUnknownBlock extends COBBlock {
	private $contents;
	
	public function COBUnknownBlock($type,$contents) {
		parent::COBBlock($type);
	}
}
?>