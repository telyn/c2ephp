<?php

require_once(dirname(__FILE__).'/PrayBlock.php');
/** Class for a FILE block.
 * Used mainly in agent files, a FILE block can contain any type of data
 * These blocks are primarily used for sprite, body data and genetics files.
 * You can only tell which type of file it is by looking at the file's name
 **/
class FILEBlock extends PrayBlock {
  /** Creates a new FILEBlock
    * \param $prayfile The PRAYFile this FILEBlock belongs to. Can be null.
    * \param $name The name of this file block (also the file's name)
    * \param $content The binary data of this file block.
    * \param $flags The block's flags. See PrayBlock.
   **/
	public function FILEBlock($prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_FILE);
	}
	/** Compiles the block's data
	 * This function is for compatibility with PrayBlock's abstract class
	 * and allows FILEBlocks to be "compiled" and "decompiled"
	 **/
	protected function CompileBlockData() {
		return $this->GetData();
	}
	/** Decompiles the block's data
	 * In other blocks, this function would convert the binary data from the
	 * block into useful data. In FILEBlocks, this is impractical because
	 * the binary data could be in any c2e file format.
   **/
	protected function DecompileBlockData() {
		throw new Exception('It\'s impossible to decode a FILE.');
	}
	/** Gets the name of the file */
	public function GetFileName() {
	   return $this->GetName();
	}
	/** Gets the contents of the file */
	public function GetFileContents() {
	   return $this->GetData();
	}
}
?>
