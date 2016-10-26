<?php
require_once(dirname(__FILE__).'/PrayBlock.php');
require_once(dirname(__FILE__).'/../../sprites/S16File.php');
require_once(dirname(__FILE__).'/../../support/StringReader.php');

/// @brief Representation of a PHOT block
/**
* Used to store photos of creatures. \n
* For all properly exported creatures, PHOT blocks always have a
* corresponding CreatureHistoryEvent in the GLSTBlock. \n
* Support for creating your own PHOTBlocks is currently nonexistant.
*/
class PHOTBlock extends PrayBlock {

    /// @brief Instantiate a PHOTBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayfile The PRAYFile that this DSAG block belongs to.
     * @param $name The block's name.
     * @param $content The binary data of this block. May be null.
     * @param $flags The block's flags
     */
    public function PHOTBlock($prayfile, $name, $content, $flags) {
        parent::PrayBlock($prayfile, $name, $content, $flags, PRAY_BLOCK_PHOT);
    }

    /// @cond INTERNAL_DOCS

    protected function CompileBlockData() {
        return $this->GetData();
    }
    protected function DecompileBlockData() {
        throw new Exception('It\'s impossible to decompile a PHOT.');
    }

    /// @endcond

    /// @brief Returns the photo data as an s16 file. <b>Deprecated.</b>
    /**
     * @return S16File photo data as an S16File object.
     */
    public function GetS16File() {
        return new S16File(new StringReader($this->GetData()));
    }
    /// @brief Returns the photo data as a PNG.
    /**
     * @return The photo data as a binary string containing PHP data.
     */
    public function ToPNG() {
        return $this->GetS16File()->ToPNG(0);
    }
}
?>
