<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
/// @brief Block for defining a Creature's current state.
/**
 * The binary format of this block is completely un-understood.
 */
class CREABlock extends CreaturesArchiveBlock {
    /// @brief Instantiate a new CREABlock
    /**
     * If $prayfile is not null, all the data about this CREABlock
     * will be read from the PRAYFile.
     * @param $prayfile The PRAYFile associated with this CREA block.
     * It is allowed to be null.
     * @param $name The name of this block.
     * @param $content This block's content.
     * @param $flags Any flags this block may have
     */
    public function CREABlock($prayfile,$name,$content,$flags) {
        parent::CreaturesArchiveBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_CREA);
    }
    /// @cond INTERNAL_DOCS
    
    /// @see PrayBlock::CompileBlockData()
    // TODO: undocumented.
    protected function CompileBlockData() {
        return $this->GetData();
    }
    /// @see PrayBlock::DecompileBlockData()
    protected function DecompileBlockData() {
        throw new Exception('I don\'t know how to decompile CREA blocks!');
    }
    /// @endcond
}
?>
