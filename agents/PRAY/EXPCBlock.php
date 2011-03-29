<?php

require_once(dirname(__FILE__).'/TagBlock.php');
/** Defines the class for EXPC (Creatures 3 exported creatures)
 */
class EXPCBlock extends TagBlock {
	/** Creates a new EXPCBlock
	 * \param $prayfile The prayfile this block is contained in. Can be null.
	 * \param $name The name of the block. Cannot be null.
	 * \param $content The binary data this block contains. Can be null.
	 * \param $flags The flags relating to this block. Should be zero or real flags.
	 */
	public function EXPCBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EXPC);
	}
	/** Gets the age of the creature
	 * return The creature's age in ticks
	 */
	public function GetAgeInTicks() {
		return $this->GetTag('Creature Age In Ticks');
	}
	/** Gets the life stage of the creature
	 * return The life stage of the creature.
	 */
	public function GetLifeStage() {
		return $this->GetTag('Creature Life Stage');
	}
	/** Gets the time the creature was exported
	 * return The time the creature was exported as a UNIX timestamp. (Seconds since 00:00 1st Jan 1970)
	 */
	public function GetExportUNIXTime() {
		return $this->GetTag('Exported At Real Time');
	}
	/** Gets the world-time when the creature was exported
	 * return The age of the world, in ticks, when the creature was exported.
	 */
	public function GetExportWorldTime() {
		return $this->GetTag('Exported At World Time');
	}
	/** Gets the gender of the creature
	 * 1 = male
	 * 2 = female
	 */
	public function GetGender() {
		return $this->GetTag('Gender');
	}
	/** Gets the genus of the creature
	 * I.e. whether it's a norn, grendel, ettin, or geat.
	 * Most likely in that order. I think 1 is Norn.
	 */
	public function GetGenus() {
		return $this->GetTag('Genus');
	}
	/** Gets whether the creature is pregnant
	 * 0 = not pregnant, 1 = pregnant.
	 */
	public function GetPregnancyStatus() {
		return $this->GetTag('Pregnancy Status');
	}
	/** Gets the variant (breed) of the creature
	 * 
	 */
	public function GetVariant() {
		return $this->GetTag('Variant');
	}
	/** Gets the creature's name */
	public function GetCreatureName() {
		return $this->GetTag('Creature Name');
	}
	/** Gets the name of the world the creature was exported from */
	public function GetWorldName() {
		return $this->GetTag('Exported From World Name');
	}
	/** Gets the UID of the world the creature was exported from */
	public function GetWorldUID() {
		return $this->GetTag('Exported From World UID');
	}
	/** Gets the gallery for the creature's head sprites.
	 * This does not include the file extension.
	 */
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
