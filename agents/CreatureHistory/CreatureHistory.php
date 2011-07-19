<?php
require_once(dirname(__FILE__).'/CreatureHistoryEvent.php');
require_once(dirname(__FILE__).'/../PRAY/GLSTBlock.php');


/**
 * @relates CreatureHistory
 * @name Gender
 * CreatureHistory-specific gender constants
 */
///@{
/** Value: 1 */
define('CREATUREHISTORY_GENDER_MALE',1);

/** Value: 2 */
define('CREATUREHISTORY_GENDER_FEMALE',2);
///@}

/// @brief Class representing a creature's history.
/**
 * As used in the C3 crypt as well as the creature's in-game info.
 */
class CreatureHistory {

    /// @cond INTERNAL_DOCS

    private $events;
    private $moniker;
    private $name;
    private $gender;
    private $genus;
    private $species;
    private $warpveteran;


    //TODO: find out what unknowns are` 
    private $unknown1; 
    private $unknown2;
    private $unknown3; /// @brief DS only
    private $unknown4; /// @brief DS only

    /// @endcond

    /// @brief Construct a CreatureHistory object.
    /**
     * @param $moniker The moniker of the creature
     * @param $name The creature's name
     * @param $gender The creature's gender
     * @param $genus The creature's genus (Grendel, ettin, norn, geat)
     * @param $species The creature's species (unsure of purpose)  
     */
    public function CreatureHistory($moniker,$name,$gender,$genus,$species) {
        $this->moniker = $moniker;
        $this->name = $name;
        $this->gender = $gender;
        $this->genus = $genus;
        $this->species = $species;
    }

    /// @brief Compiles the CreatureHistory into CreaturesArchive data.
    /**
     * @param $format GLST_FORMAT_C3 or GLST_FORMAT_DS.
     * @return A binary string ready for archiving and putting in a GLST block. 
     */
    public function Compile($format=GLST_FORMAT_C3) {
        $data = '';
        if($format != GLST_FORMAT_C3 && $format != GLST_FORMAT_DS) {
            $format = $this->GuessFormat();
        }
        if($format == GLST_FORMAT_DS) {
            $data = pack('V',0x27);
        } else {
            $data = pack('V',0x0C);
        }
        $data .= pack('V',1);
        $data .= pack('V',32).$this->moniker;
        $data .= pack('V',32).$this->moniker; //yeah, twice. Dunno why, CL are bonkers.
        $data .= pack('V',strlen($this->name)).$this->name;
        $data .= pack('VVVV',$this->gender,$this->genus,$this->species,count($this->events));
        foreach($this->events as $event) {
            $data .= $event->Compile($format);
        }
        $data .= pack('V',$this->unknown1);
        $data .= pack('V',$this->unknown2);
        if($format == GLST_FORMAT_DS) {
            $data .= pack('V',$this->unknown3);
            $data .= pack('V',strlen($this->unknown4)).$this->unknown4;
        }
        return $data;
    }

    /// @brief Try to work out which game this CreatureHistory is for
    /**
     * This is done by working out whether any DS-specific variables
     * are set.
     * @return GLST_FORMAT_DS or GLST_FORMAT_C3.
     */
    public function GuessFormat() {
        return (isset($this->unknown3))?GLST_FORMAT_DS:GLST_FORMAT_C3;
    }

    /// @brief Adds an event to the end of a history.
    /**
     * @param $event A CreatureHistoryEvent to add to this
     * CreatureHistory object.
     */
    public function AddEvent(CreatureHistoryEvent $event) {
        $this->events[] = $event;
    }

    /// @brief Gets an event from the history
    /**
     * Simply gets the nth event that happened in this history
     * @param $n the event number to get
     * @return the $nth CreatureHistoryEvent 
     */
    public function GetEvent($n) {
        return $this->events[$n];
    }

    /// @brief Removes an event from history
    /**
     * Removes the nth event from this history
     * @param $n the event number to remove
     */
    public function RemoveEvent($n) {
       unset($this->events[$n]); 
    }

    /// @brief Counts the events in the history
    /**
     * @return How many events there currently are in this history
     */
    public function CountEvents() {
        return sizeof($this->events);
    }

    /// @brief Gets all events matching the given event type
    /**
     * @see agents/CreatureHistory/CreatureHistoryEvent.php Event Types
     * @param $type one of the Event Type constants.
     * @return an array of CreatureHistoryEvents.
     */
    public function GetEventsByType($type) {
        $matchingEvents = array();
        foreach($this->events as $event) {
            if($event->GetEventType() == $type) {
                $matchingEvents[] = $event;
            }
        }
        return $matchingEvents;
    }

    /// Gets all the events in this history
    /**
     * @return An array of CreatureHistoryEvents 
     */
    public function GetEvents() {
        return $this->events;
    } 

    /// @brief Gets the moniker of the creature this history is attached to.
    public function GetCreatureMoniker() {
        return $this->moniker;
    }

    /// @brief Gets the generation of the creature
    /**
     * I cannot guarantee that this function works. However, it does use the
     * same method as the Creatures 3 in-game creature information viewer,
     * so it should work on all creatures made in-game.
     * @return 0 for failure, the generation of the creature otherwise.
     */
    public function GetCreatureGenerationNumber() {
        if($pos = strpos('_',$this->moniker) == -1) {
            return 0;
        } else {
            $firstbit = substr($this->moniker,0,$pos);
            if(is_numeric($firstbit)) {
                return $firstbit+0;
            }
            return 0;
        }
    }

    /// @brief Gets the name of the creature this history is attached to.
    public function GetCreatureName() {
        return $this->name;
    }

    /// @brief Gets the gender of the creature this history is attached to.
    public function GetCreatureGender() {
        return $this->gender;
    }

    /// @brief Gets the genus of the creature this history is attached to.
    public function GetCreatureGenus() {
        return $this->genus;
    }

    /// @brief Gets the species of the creature this history is attached to.
    public function GetCreatureSpecies() {
        return $this->species;
    }

    /// @brief Gets whether the creature this history is attached to has been through the warp.
    public function GetCreatureIsWarpVeteran() {
        return $this->warpveteran;
    }

    /// @brief Set unknown variables.
    /**
     * Set variables that are currently unknown and used in C3 and DS
     * These variables COULD be mutations and crossovers during
     * conception, however in a creature that was not conceived, they
     * appear to be strange. \n
     * Honestly, I don't know yet because I haven't managed to work
     * it out.
     * @param $unknown1 First unknown variable
     * @param $unknown2 Second unknown variable
     */
    public function SetC3Unknowns($unknown1,$unknown2) {
        $this->unknown1 = $unknown1;
        $this->unknown2 = $unknown2;
    }

    /// @brief Set variables that are currently unknown, specific to
    /// DS
    /**
     * This calls SetC3Unknown
     * @param $unknown1 First unknown variable (shared with C3)
     * @param $unknown2 Second unknown variable (shared with C3)
     * @param $unknown3 Third unknown variable (DS only)
     * @param $unknown4 Forth unknown variable (DS only)
     */
    public function SetDSUnknowns($unknown1,$unknown2,$unknown3,$unknown4) {
        $this->SetC3Unknowns($unknown1,$unknown2);
        $this->unknown3 = $unknown3;
        $this->unknown4 = $unknown4;
    }

    /// @brief Sets whether or not the creature is a veteran of the warp (DS only)
    /**
     * @param $warpveteran A boolean (I think!)
     */
    public function SetWarpVeteran($warpveteran) {
        $this->warpveteran = $warpveteran;
    }
}

?>
