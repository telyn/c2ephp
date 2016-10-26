<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/PrayBlock.php');
/// @brief Abstract class for easy de-archiving CreaturesArchives
/**
 * Used by CREABlock and GLSTBlock, this class is not for end-users.
 */
abstract class CreaturesArchiveBlock extends PrayBlock {
    /// @cond INTERNAL_DOCS 

    /// @brief Instantiates a new CreaturesArchiveBlock
    /**
     * @param $prayfile The PRAYFile this CreaturesArchive belongs to. May be null.
     * @param $name The name of this block
     * @param $content This block's binary data.
     * @param $flags the flags of this block
     * @param string $type This block's type as one of the PRAY_BLOCK_* constants
     */
    public function CreaturesArchiveBlock(&$prayfile, $name, $content, $flags, $type) {
        parent::PrayBlock($prayfile, $name, $content, $flags, $type);
        if ($prayfile instanceof PRAYFile) {
            if (!$this->DeArchive()) {
                throw new Exception('De-Archiving failed, block probably wasn\'t a CreaturesArchive type');
            }
        }
    }
    /// @endcond

    /// DeArchives this block
    private function DeArchive() {
        $content = $this->GetData();
        if ($content{0} == 'C') {
            $content = DeArchive($content);
            if ($content !== false) {
                $this->SetData($content);
                return true;
            }
//          echo 'Invalid CreaturesArchive';
            return false;
        }
        return true;
    }

}
?>
