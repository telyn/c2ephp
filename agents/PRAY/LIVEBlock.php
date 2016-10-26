<?php

require_once(dirname(__FILE__).'/TagBlock.php');

/// @brief Block to enable some kind of compatibility with
/// Amazing Virtual Sea Monkeys agents
/**
 * This class will probably remain forever untested and unused -
 * Amazing Virtual Sea Monkeys was not a popular game, I doubt 
 * many agents were created for it. 
 */
class LIVEBlock extends AGNTBlock {
    /// @brief Creates a new LIVEBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayfile The PRAYFile this LIVEBlock belongs to.
     * @param $name The name of this block
     * @param $content The binary data of this file block.
     * @param $flags The block's flags. See PrayBlock.
     */

    public function LIVEBlock($prayfile, $name, $content, $flags) {
        parent::TagBlock($prayfile, $name, $content, $flags, PRAY_BLOCK_LIVE);

    }
}
?>
