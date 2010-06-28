<?php
/** \file
*	Contains the PHOTBlock class.
*	Used by the MakePrayBlock function and the GLSTBlock class.
*/

require_once(dirname(__FILE__).'/PrayBlock.php');
require_once(dirname(__FILE__).'/../../sprites/S16File.php');
require_once(dirname(__FILE__).'/../../support/StringReader.php');

/** Representation of a PHOT block
* Used to store photos of creatures.
* PHOT blocks always have a correspending GLSTEvent.
*/
class PHOTBlock extends PrayBlock {
	public function PHOTBlock($prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_PHOT);
	}
	protected function CompileBlockData() {
		return $this->GetData();
	}
	protected function DecompileBlockData() {
		throw new Exception('It\'s impossible to decompile a PHOT.');
	}
	/** Returns the photo data as an s16 file.
	*	\return The photo data as an S16File object.
	*/
	public function GetS16File() {
		return new S16File(new StringReader($this->GetData()));
	}
	/** Returns the photo data as a PNG.
	* 	\return The photo data as a binary string containing PHP data.
	*/
	public function ToPNG() {
		return $this->GetS16File()->ToPNG(0);
	}
}
?>
