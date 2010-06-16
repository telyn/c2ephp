<?php

require_once(dirname(__FILE__).'/TagBlock.php');
class EXPCBlock extends TagBlock {
	public function EXPCBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EXPC);
	}
	public function GetAgeInTicks() {
		return $this->GetTag('Creature Age In Ticks');
	}
	public function GetLifeStage() {
		return $this->GetTag('Creature Life Stage');
	}
	public function GetExportUNIXTime() {
		return $this->GetTag('Exported At Real Time');
	}
	public function GetExportWorldTime() {
		return $this->GetTag('Exported At World Time');
	}
	public function GetGender() {
		return $this->GetTag('Gender');
	}
	public function GetGenus() {
		return $this->GetTag('Genus');
	}
	public function GetPregnancyStatus() {
		return $this->GetTag('Pregnancy Status');
	}
	public function GetVariant() {
		return $this->GetTag('Variant');
	}
	public function GetCreatureName() {
		return $this->GetTag('Creature Name');
	}
	public function GetWorldName() {
		return $this->GetTag('Exported From World Name');
	}
	public function GetWorldUID() {
		return $this->GetTag('Exported From World UID');
	}
	public function GetHeadGallery() {
		return $this->GetTag('Head Gallery');
	}
}
/*Array
(
    [Creature Age In Ticks] => 69524
    [Creature Life Stage] => 4
    [Exported At Real Time] => 998726076
    [Exported At World Time] => 203183
    [Gender] => 2
    [Genus] => 1
    [Pregnancy Status] => 0
    [Variant] => 4
    [Creature Name] => lilo
    [Exported From World Name] => stinky
    [Exported From World UID] => ship-pcnfc-evaq5-2zmqz-45yy2
    [Head Gallery] => A40a
)*/
?>
