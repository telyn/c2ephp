<?php
define('COB_BLOCK_AGENT','agnt');
define('COB_BLOCK_FILE','file');
define('COB_BLOCK_AUTHOR','auth');

/** The base COB block class */
abstract class COBBlock {
	private $type;
	/** @ignore 
	 * Instantiates a new COBBlock
	 * This function must be called from all COBBlock parents
	 * @param $type What type of COBBlock it is. Must be a 4-character string.
	 */
	public function COBBlock($type) {
		if(strlen($type) != 4) {
			throw new Exception('Invalid COB block type: '.$type);
		}
		$this->type = $type;
	}
	/** Gets the type of this COB block
	 * @return string One of the COB_BLOCK_* defines.
	 */
	public function GetType() {
		return $this->type;
	}
  /** Compiles this COB Block and returns COB file as a binary string.
   * @return string
   */
  public abstract function Compile();
}
?>
