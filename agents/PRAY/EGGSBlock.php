<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EGGSBlock extends TagBlock {
	public function EGGSBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EGGS);

	}

	public function GetAgentType() {
		return $this->GetTag('Agent Type');
	}
	public function GetDependencyCount() {
		return $this->GetTag('Dependency Count');
	}
	public function GetEggAnimationString() {
		return $this->GetTag('Egg Animation String');
	}
	public function GetEggGalleryFemale() {
		return $this->GetTag('Egg Gallery female');
	}
	public function GetEggGalleryMale() {
		return $this->GetTag('Egg Gallery male');
	}
	public function GetEggGlyphFile1() {
		return $this->GetTag('Egg Glyph File');
	}
	public function GetEggGlyphFile2() {
		return $this->GetTag('Egg Glyph File 2');
	}
	public function GetGeneticsFile() {
		return $this->GetTag('Genetics File');
	}

}
?>
