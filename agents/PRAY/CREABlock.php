<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
/** \brief Block for defining a Creature's current state.
 * The binary format of this block is completely un-understood.
 */
class CREABlock extends CreaturesArchiveBlock {
	/** \brief Instantiate a new CREABlock
	 * \param $prayfile The PRAYFile associated with this CREA block. It is allowed to be null.
	 * \param $name The name of this block.
	 * \param $content This block's content.
	 * \param $flags Any flags this block may have
	 */
	public function CREABlock($prayfile,$name,$content,$flags) {
		parent::CreaturesArchiveBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_CREA);
	}
	protected function CompileBlockData() {
		return $this->GetData();
	}
	protected function DecompileBlockData() {
		throw new Exception('I don\'t know how to decompile CREA blocks!');
	}
}
?>
