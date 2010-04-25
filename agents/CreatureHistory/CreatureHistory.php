<?php
require_once(dirname(__FILE__).'/CreatureHistoryEvent.php');
require_once(dirname(__FILE__).'/../PRAY/GLSTBlock.php');

/** \name Block Types
* Constants for the various PRAY block types
*/
//@{
/**\brief Value: 0 */
//Girls first! Boys suck :)
define('CREATUREHISTORY_GENDER_FEMALE',0);
/**\brief Value: 1 */
define('CREATUREHISTORY_GENDER_MALE',1);
//@}

/**
 * Represents a creature's complete history, as used in the C3 crypt as well as the creature's in-game info.
 * At present, this class is capable of decoding only. It is automatically created by GLSTBlock during decoding.
 */
class CreatureHistory {

	//TODO: Getter functions
	private $events;
	private $moniker;
	private $name;
	private $gender;
	private $genus;
	private $species;
	private $warpveteran;


	//TODO: What I don't know...
	private $unknown1;
	private $unknown2;
	private $unknown3; ///DS only
	private $unknown4; ///DS only
	
	/**
	 * Construct a CreatureHistory object with the given non-optional information.
	 * \param $moniker The moniker of the creature
	 * \param $name The creature's name
	 * \param $gender The creature's gender
	 * \param $genus The creature's genus (Grendel, ettin, norn, geat)
	 * \param $species The creature's species (unsure of purpose)  
	 */
	public function CreatureHistory($moniker,$name,$gender,$genus,$species) {
		$this->moniker = $moniker;
		$this->name = $name;
		$this->gender = $gender;
		$this->genus = $genus;
		$this->species = $species;
	}
	/**
	 * Compile the CreatureHistory to its CreaturesArchive representation, ready for archiving and putting in a GLST block. 
	 * \param $format GLST_FORMAT_* constant used to state whether the history should be compiled for C3 or DS compatibility.
	 */
	public function Compile($format) {
		
	}
	/**
	 * Try to work out which game this CreatureHistory is for (by working out whether any DS-specific variables are set)  
	 */
	public function GuessFormat() {
		return (isset($this->unknown3))?GLST_FORMAT_DS:GLST_FORMAT_C3;
	}
	public function AddEvent(CreatureHistoryEvent $event) {
		$this->events[] = $event;
	}
	/**
	 * Set variables that are currently unknown and used in C3 and DS.
	 * \param $unknown1 First unknown variable
	 * \param $unknown2 Second unknown variable
	 */
	public function SetC3Unknowns($unknown1,$unknown2) {
		$this->unknown1 = $unknown1;
		$this->unknown2 = $unknown2;
	}
	/**
	 * Set variables that are currently unknown, specific to DS
	 * \param $unknown1 First unknown variable (shared with C3)
	 * \param $unknown2 Second unknown variable (shared with C3)
	 * \param $unknown3 Third unknown variable (DS only)
	 * \param $unknown4 Forth unknown variable (DS only)
	 */
	public function SetDSUnknowns($unknown1,$unknown2,$unknown3,$unknown4) {
		$this->SetC3Unknowns($unknown1,$unknown2);
		$this->unknown3 = $unknown3;
		$this->unknown4 = $unknown4;
	}
	/*
	 * Sets whether or not the creature is a veteran of the warp (DS only)
	 * \param $warpveteran 
	 */
	public function SetWarpVeteran($warpveteran) {
		$this->warpveteran = $warpveteran;
	}
}

?>