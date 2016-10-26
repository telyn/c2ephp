<?php

require_once(dirname(__FILE__).'/TagBlock.php');

/// @brief Class for egg description block which is used to provide
/// eggs for Muco and the C3 egg layer.
class EGGSBlock extends TagBlock {


    /// @brief Instantiate a new EGGSBlock
    /**
     * Makes a new EGGSBlock. \n
     * If $prayfile is not null, all the data about this AGNTBlock
     * will be read from the PRAYFile.
     * @param PRAYFile $prayfile The PRAYFile associated with this AGNT block.
     * It is allowed to be null.
     * @param $name The name of this block.
     * @param $content This block's content.
     * @param $flags Any flags this block may have. I think this is a
     * single byte. Check http://www.creatureswiki.net/wiki/PRAY
     */
        public function EGGSBlock($prayfile,$name,$content,$flags) {
        parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EGGS);

    }

        /// @brief Gets the agent's type.
        /**
         * This method is identical to that in EXPCBlock.
         * The type seems to always be 0 for EGGSBlocks.
         */
    public function GetAgentType() {
        return $this->GetTag('Agent Type');
    }

    /// @brief Gets the dependency count
    /**
     * The number of sprites, etc that the eggs depend on.
     */ 
    public function GetDependencyCount() {
        return $this->GetTag('Dependency Count');
    }

    /// @brief Gets the animation string used for the egg.
    /**
     * It seems to always be a single pose. 
     */
    public function GetEggAnimationString() {
        return $this->GetTag('Egg Animation String');
    }

    /// @brief Gets the gallery file for the female egg.
    /**
     * At least for bruin and bengal norns, this is the
     * same as GetGylphFile2() with the extension removed.
     */
    public function GetEggGalleryFemale() {
        return $this->GetTag('Egg Gallery female');
    }

    /// @brief Gets the gallery file for the male egg.
    /**
     * At least for bruin and bengal norns, this is the
     * same as GetGlyphFile1() with the extension removed.
     */
    public function GetEggGalleryMale() {
        return $this->GetTag('Egg Gallery male');
    }

    /// @brief Gets the glyph filename for the male eggs.
    /**
     * This includes the file extension.
     */
    public function GetEggGlyphFile1() {
        return $this->GetTag('Egg Glyph File');
    }

    /// @brief Gets the glyph filename for the female eggs.
    /**
     * This includes the file extension.
     */
    public function GetEggGlyphFile2() {
        return $this->GetTag('Egg Glyph File 2');
    }

    /// @brief Gets the genetics file for the eggs.
    /**
     * Doesn't include the .gen file extension.
     */
    public function GetGeneticsFile() {
        return $this->GetTag('Genetics File');
    }

}
?>
