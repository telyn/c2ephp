<?php
define('COB_BLOCK_AGENT','agnt');
define('COB_BLOCK_FILE','file');
define('COB_BLOCK_AUTHOR','auth');

/// \brief The base COB block class
abstract class COBBlock {
	private $type;
	/** \brief Instantiates a new COBBlock
	 * This function must be called from all COBBlock parents
	 * \param $type What type of COBBlock it is. Must be a 4-character string.
	 */
	public function COBBlock($type) {
		if(strlen($type) != 4) {
			throw new Exception('Invalid COB block type: '.$type);
		}
		$this->type = $type;
	}
	/// \brief Gets the type of this COB block
	public function GetType() {
		return $this->type;
	}
}
?>