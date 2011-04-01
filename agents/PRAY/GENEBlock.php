<?php

require_once(dirname(__FILE__).'/PrayBlock.php');

/// @brief The class for a GENE block 
/**
  * The contents are identical to that of a .gen file\n
  * This class doesn't have any useful functions yet - I don't
  * actually know how to decode genetics at the moment.
 */
class GENEBlock extends PrayBlock {

    /// @brief Creates a new GENEBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param $prayfile The PRAYFile object this block belongs to. Can be null.
     * @param $name The block's name. This is a moniker, possibly ending in .gen. TODO: Does it end in gen?
     * @param $content The block's binary data. Used when constructing from a PrayFile
     * @param $flags The block's flags, which apply to the binary data as-is.
     */
    public function GENEBlock($prayfile,$name,$content,$flags) {
        parent::PrayBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_GENE);

    }

    /// @cond INTERNAL_DOCS

    /** Compiles the block's data
     * Does nothing really, just pipes the original binary data along
     * TODO: Make code for creation of GENE blocks.
     */
    protected function CompileBlockData() {
        return $this->GetData();
    }
    /** Decompiles the block's data
     * Called automatically when necessary.
     * TODO: This currently does nothing as I don't know how to decomile a gene file.
     */
    protected function DecompileBlockData() {
        throw new Exception('I don\'t know how to decompile a GENE.');
    }

    /// @endcond
}
?>
