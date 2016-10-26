<?php
require_once(dirname(__FILE__).'/../PRAY/GLSTBlock.php');

/**
 * @relates CreatureHistoryEvent
 * @name Event Numbers 
 * All creatures are either CONCEIVED, SPLICED, ENGINEERED, or
 * IAMCLONED.\n
 * Then CONCEIVED creatures are MUMLAIDMYEGG. \n
 * Then they are HATCHED except maybe ENGINEERED creatures. \n
 * Then they have a PHOTOTAKEN. \n
 * Then there's not a specific order of events that always happen
 * - they go on and live their own lives ;) 
 */
//@{
/** I was conceived by kisspopping or artificial insemination. \n
 * Value: 0 */
define('CREATUREHISTORY_EVENT_CONCEIVED', 0);
/** I was spliced from two other creatures \n
 * Value: 1 */
define('CREATUREHISTORY_EVENT_SPLICED', 1);
/** I was created by someone with a genetics kit \n
 * Value: 2 */
define('CREATUREHISTORY_EVENT_ENGINEERED', 2);
/** I hatched out of my egg. \n
 * Value: 3 */
define('CREATUREHISTORY_EVENT_HATCHED', 3);
/**
 * CreatureHistoryEvent::GetLifestage will tell you what lifestage I
 * am now. \n\n
 * Value: 4
 */
define('CREATUREHISTORY_EVENT_AGED', 4);
/** I was exported from this world \n
 * Value: 5 */
define('CREATUREHISTORY_EVENT_EXPORTED', 5);
/** I joined this world \n
 * Value: 6 */
define('CREATUREHISTORY_EVENT_IMPORTED', 6);
/** My journey through life ended. \n
 * Value: 7 */
define('CREATUREHISTORY_EVENT_DIED', 7);
/** I became pregnant. \n
 * Value: 8 */
define('CREATUREHISTORY_EVENT_BECAMEPREGNANT', 8);
/** I made someone else pregnant.  \n
 * Value: 9 */
define('CREATUREHISTORY_EVENT_IMPREGNATED', 9);
/** My child hatched from its egg! \n
 * Value: 10 */
define('CREATUREHISTORY_EVENT_CHILDBORN', 10);
/** My mum laid my egg. \n
 * Value: 11 */
define('CREATUREHISTORY_EVENT_MUMLAIDMYEGG', 11);
/** I laid an egg I was carrying. \n
 * Value: 12 */
define('CREATUREHISTORY_EVENT_LAIDEGG', 12);
/** A photo was taken of me. \n
 * Value: 13 */
define('CREATUREHISTORY_EVENT_PHOTOTAKEN', 13);
/**
 * I was made by cloning another creature \n
 * This happens when you export a creature then import it more than
 * once. \n
 * Value: 14
 */
define('CREATUREHISTORY_EVENT_IAMCLONED', 14); 
/** Another creature was made by cloning me. \n
 * This happens when you export a creature then import it more than
 * once. \n
 * Value: 15
 */
define('CREATUREHISTORY_EVENT_CLONEDME', 15);
/** I left this world through the warp \n
 * Value: 16 */
define('CREATUREHISTORY_EVENT_WARPEDOUT', 16);
/** I entered this world through the warp \n
 * Value: 17 */
define('CREATUREHISTORY_EVENT_WARPEDIN', 17);
//@}


/// @brief Class to represent events in a creature's life
class CreatureHistoryEvent {

    /// @cond INTERNAL_DOCS
    
    private $eventtype;
    private $worldtime;
    private $creatureage;
    private $timestamp;
    private $lifestage;
    private $moniker1;
    private $moniker2;
    private $usertext;
    private $photograph;
    private $worldname;
    private $worldUID;
    private $dockingstationuser; ///DS Only
    
    private $unknown1; ///DS Only
    private $unknown2; ///DS Only

    /// @endcond

    /// @brief Instantiates a new CreatureHistoryEvent.
    /**
     * 
     *
     * @see GetMoniker1(), GetMoniker2(), GetUserText,
     * GetPhotograph()
     * @param $eventtype The event number as defined by the
     * CREATUREHISTORY_EVENT_* constants.
     * @param $worldtime The world's age in ticks at the time of this
     * event.
     * @param $creatureage The age of the creature in ticks at the
     * time of this event
     * @param $timestamp The time of this event as a unix timestamp.
     * (number of seconds passed since 1st Jan, 1970)
     * @param $lifestage The lifestage this creature had achieved at
     * the time of this event.
     * @param $moniker1 The first moniker associated with this event.
     * @param $moniker2 The second moniker associated with this event.
     * @param $usertext The user text assosciated with this event.
     * @param $photograph The photograph associated with this event.
     * @param $worldname The name of the world the creature was inhabiting at the time of this event 
     * @param $worldUID The UID of the world the creature was inhabiting at the the time of this event
     */
    public function CreatureHistoryEvent($eventtype, $worldtime, $creatureage, $timestamp, $lifestage, $moniker1, $moniker2, $usertext, $photograph, $worldname, $worldUID) {
        $this->eventtype = $eventtype;
        $this->worldtime = $worldtime;
        $this->creatureage = $creatureage;
        $this->timestamp = $timestamp;
        $this->lifestage = $lifestage;
        $this->moniker1 = $moniker1;
        $this->moniker2 = $moniker2;
        $this->usertext = $usertext;
        $this->photograph = $photograph;
        $this->worldname = $worldname;
        $this->worldUID = $worldUID;
    }

    /// @brief Add DS-specific information to the CreatureHistoryEvent
    /**
     * @param $DSUserID The UID of the Docking Station user whose world the creature was in at the time of the event
     * @param $unknown1 I don't know! But it comes right after the DSUID in the GLST format.
     * @param $unknown2 I don't know! But it comes right after unknown1.
     */
    public function AddDSInfo($DSUserID, $unknown1, $unknown2) {
        $this->dockingstationuser = $DSUserID;
        $this->unknown1 = $unknown1;
        $this->unknown2 = $unknown2;
    }

    /// @brief Compiles the data into the correct format for the game
    /// specified.
    /**
     * This is called automatically by CreatureHistory, most users
     * should have no need to use this function themselves.
     * @param $format Which game to compile it for (a GLST_FORMAT_* constant)
     * @return string binary string containing GLST data ready to be put into a GLST history.
     */
    public function Compile($format) {
        $data = pack('VVVVV', $this->eventtype, $this->worldtime, $this->creatureage, $this->timestamp, $this->lifestage);
        $data .= pack('V', strlen($this->moniker1)).$this->moniker1;
        $data .= pack('V', strlen($this->moniker2)).$this->moniker2;
        $data .= pack('V', strlen($this->usertext)).$this->usertext;
        $data .= pack('V', strlen($this->photograph)).$this->photograph;
        $data .= pack('V', strlen($this->worldname)).$this->worldname;
        $data .= pack('V', strlen($this->worldUID)).$this->worldUID;
        if ($format == GLST_FORMAT_DS) {
            $data .= pack('V', strlen($this->dockingstationuser)).$this->dockingstationuser;
            $data .= pack('VV', $this->unknown1, $this->unknown2);
        }
        return $data;
    }

    /// @brief Accessor method for event type
    /**
     * @return The event type as a CREATUREHISTORY_EVENT_* constant.
     */
    public function GetEventType() {
        return $this->eventtype;
    }

    /// @brief Accessor method for world time
    /**
     * @return The age of the world, in ticks, when this event occurred.
     */
    public function GetWorldTime() {
        return $this->worldtime;
    }
    /// @brief Accessor method for creature age 
    /**
     * @return The age of the creature, in ticks, when this event happened
     */
    public function GetCreatureAge() {
        return $this->creatureage;
    }

    /// @brief Accessor method for timestamp 
    /**
     * @return The unix timestamp of the time at which this event occurred
     */
    public function GetTimestamp() {
        return $this->timestamp;
    }

    /// @brief Accessor method for life stage
    /**
     * @return The creature's life stage (an integer, 0-6 I think.
     * 0xFF means unborn.) \n
     * TODO: Make a set of constants for this.
     */
    public function GetLifeStage() {
        return $this->lifestage;
    }

    /// @brief Accessor method for moniker 1
    /**
     * Moniker 1 is the first moniker associated with this event.
     * In conception and splicing, it is one of the parent
     * creatures. \n
     * In cloning, it is the parent/child's moniker. Whichever is not
     * the current creature. \n
     * In laying an egg, it is the moniker of the egg laid. \n
     * In becoming pregnant, it's the creature that made this one
     * pregnant \n
     * In making another pregnant, it's the pregnant creature. \n
     * In a child being born, it's the other parent of the child
     * @return The first moniker associated with this event 
     */
    public function GetMoniker1() {
        return $this->moniker1;
    }

    /// @brief Accessor method for moniker 2
    /**
     * Moniker 2 is the second moniker associated with this event. \n
     * In conception and splicing, it is one of the conceiving
     * creatures. \n
     * In becoming pregnant, it's the child's moniker \n
     * In making another pregnant, it's the child's moniker \n
     * In a child being born, it's the child's moniker \n
     * @return The first moniker associated with this event 
     */
    public function GetMoniker2() {
        return $this->moniker2;
    }

    /// @brief Accessor method for user text
    /**
     * In theory user text can be used on any event without messing
     * it up (and it would be readable via CAOS) See
     * http://nornalbion.github.com/c2ephp/caos-guide.html#HIST%20FOTO
     * for more on reading history with CAOS. \n
     * In practice, this is only used by either the first event or
     * the hatched event (I forget which) and is used to mean the
     * text that the user enters to describe this creature in the
     * creature info dialog.
     * @return The user text associated with this event.
     */
    public function GetUserText() {
        return $this->usertext;
    }

    /// @brief Accessor method for photograph
    /**
     * Gets the name of the PHOT block containing the S16File
     * of the photograph for this event. \n
     * In theory this can be used on any event without messing
     * anything up, and would be readable via CAOS. See
     * http://nornalbion.github.com/c2ephp/caos-guide.html#HIST%20FOTO
     * for more on reading history with CAOS. \n
     * In pratice (i.e. in all GLST blocks I've seen) this is only
     * used on photo-taken events. \n
     * @return The identifier of the photograph (in the format
     * mymoniker-photonumber)
     */
    public function GetPhotograph() {
        return $this->photograph;
    }
    /// @brief Accessor method for world name
    /**
     * @return The name of the world this creature was in during this event
     */
    public function GetWorldName() {
        return $this->worldname;
    }

    /// @brief Accessor method for world name
    /**
     * @return The name of the world this creature was in during this event
     */
    public function GetWorldUID() {
        return $this->worldUID;
    }
}
?>
