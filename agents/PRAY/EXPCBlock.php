<?php

require_once(dirname(__FILE__).'/TagBlock.php');
/// @brief Defines the class for EXPC (Creatures 3 exported creatures)
/**
 * @internal
 * Here's an example of the tags inside an EXPC block. \n
 * By the way, this is real data taken from a norn available for
 * download from Helen's site (http://www.creaturesvillage.com/helen/)
 * so don't blame the immature world name on me ;) \n
 * <pre>Array
 * (
 *    [Creature Age In Ticks] => 69524
 *    [Creature Life Stage] => 4
 *    [Exported At Real Time] => 998726076
 *    [Exported At World Time] => 203183
 *    [Gender] => 2
 *    [Genus] => 1
 *    [Pregnancy Status] => 0
 *    [Variant] => 4
 *    [Creature Name] => lilo
 *    [Exported From World Name] => stinky
 *    [Exported From World UID] => ship-pcnfc-evaq5-2zmqz-45yy2
 *    [Head Gallery] => A40a
 * )</pre>
 */

class EXPCBlock extends TagBlock {

    /// @brief Creates a new EXPCBlock
    /**
     * If $prayfile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param $prayfile The PRAYFile that this DFAM block belongs to.
     * @param $name The block's name.
     * @param $content The binary data of this block. May be null.
     * @param $flags The block's flags
     */
    public function EXPCBlock($prayfile,$name,$content,$flags) {
        parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_EXPC);
    }

    /// @brief Gets the age of the creature
    /**
     * @return The creature's age in ticks
     */
    public function GetAgeInTicks() {
        return $this->GetTag('Creature Age In Ticks');
    }

    /// @brief Gets the life stage of the creature
    /**
     * @return The life stage of the creature.
     */
    public function GetLifeStage() {
        return $this->GetTag('Creature Life Stage');
    }

    /// @brief Gets the time the creature was exported
    /**
     * @return The time the creature was exported as a UNIX timestamp. (Seconds since 00:00 1st Jan 1970)
     */
    public function GetExportUNIXTime() {
        return $this->GetTag('Exported At Real Time');
    }
    /// @brief Gets the world-time when the creature was exported
    /**
     * @return The age of the world, in ticks, when the creature was exported.
     */
    public function GetExportWorldTime() {
        return $this->GetTag('Exported At World Time');
    }
    /// @brief Gets the gender of the creature
    /*
     * 1 = male \n
     * 2 = female
     */
    public function GetGender() {
        return $this->GetTag('Gender');
    }

    /// @brief Gets the genus of the creature
    /**
     * I.e. whether it's a norn, grendel, ettin, or geat. \n
     * Most likely in that order. I think 1 is Norn.
     */
    public function GetGenus() {
        return $this->GetTag('Genus');
    }

    /// @brief Gets whether the creature is pregnant
    /**
     * 0 = not pregnant, 1 = pregnant.
     */
    public function GetPregnancyStatus() {
        return $this->GetTag('Pregnancy Status');
    }

    /// @brief Gets the variant (breed) of the creature
    /**
     * @return A single alphabetical character.
     * Unsure if it's upper or lowercase.
     */
    public function GetVariant() {
        return $this->GetTag('Variant');
    }

    /// @brief Gets the creature's name
    public function GetCreatureName() {
        return $this->GetTag('Creature Name');
    }

    /// @brief Gets the name of the world the creature was exported from.
    public function GetWorldName() {
        return $this->GetTag('Exported From World Name');
    }

    /// @brief Gets the UID of the world the creature was exported from.
    public function GetWorldUID() {
        return $this->GetTag('Exported From World UID');
    }

    /// @brief Gets the gallery for the creature's head sprites.
    /**
     * This does not include the file extension.
     */
    public function GetHeadGallery() {
        return $this->GetTag('Head Gallery');
    }
}
?>
