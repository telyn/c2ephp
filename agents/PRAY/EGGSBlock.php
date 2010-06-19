<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EGGSBlock extends TagBlock {
	public function EGGSBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EGGS);

	}
	/**
	 * Gets the agent's type. This method is identical to that in EXPCBlock.
	 * The type is 0 for EGGSBlocks.
	 */
	public function GetAgentType() {
		return $this->GetTag('Agent Type');
	}
	
	public function GetDependencyCount() {
		return $this->GetTag('Dependency Count');
	}
	/** \brief Gets the animation string used for the egg.
	 * It seems to be a single pose. 
	 */
	public function GetEggAnimationString() {
		return $this->GetTag('Egg Animation String');
	}
	/** \brief Gets the gallery file for the female egg.
	 * At least for bruin and bengal norns, this is the
	 * same as EggFile2 with the extension removed.
	 */
	public function GetEggGalleryFemale() {
		return $this->GetTag('Egg Gallery female');
	}
	/** \brief Gets the gallery file for the male egg.
	 * At least for bruin and bengal norns, this is the
	 * same as EggFile1 with the extension removed.
	 */
	public function GetEggGalleryMale() {
		return $this->GetTag('Egg Gallery male');
	}
	/**\brief Gets the glyph filename for the male eggs.
	 * This includes the file extension.
	 */
	public function GetEggGlyphFile1() {
		return $this->GetTag('Egg Glyph File');
	}
	/** \brief Gets the glyph filename for the female eggs.
	 * This includes the file extension.
	 */
	public function GetEggGlyphFile2() {
		return $this->GetTag('Egg Glyph File 2');
	}
	/** \brief Gets the genetics file for the eggs.
	 * Doesn't include the .gen file extension.
	 */
	public function GetGeneticsFile() {
		return $this->GetTag('Genetics File');
	}

}
?>
