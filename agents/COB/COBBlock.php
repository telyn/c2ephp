<?php
define('COB_BLOCK_AGENT','agnt');
define('COB_BLOCK_FILE','file');
define('COB_BLOCK_AUTHOR','auth');

abstract class COBBlock {
	private $type;
	public function COBBlock($type) {
		$this->type = $type;
	}
	public function GetType() {
		return $this->type;
	}
}
?>