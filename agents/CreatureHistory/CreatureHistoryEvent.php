<?php
require_once(dirname(__FILE__).'/../PRAY/GLSTBlock.php');

/** \Brief Creature event numbers
 * all creatures are either CONCEIVED, SPLICED, ENGINEERED, or IAMCLONED
 * Then CONCEIVED creatures are MUMLAIDMYEGG
 * Then they are HATCHED except maybe ENGINEERED creatures.
 * Then they have a PHOTOTAKEN
 * Then they make their own way through life.
 */
//@{
/*\brief I was conceived the ol' fashioned way (kisspopping)
 * Value: 0*/
define('CREATUREHISTORY_EVENT_CONCEIVED',0);
/*\brief I was spliced from two other creatures
 * Value: 1*/
define('CREATUREHISTORY_EVENT_SPLICED',1);
/*\brief I was conceived by some geek in their bedroom (genetics kit)
 * Value: 2*/
define('CREATUREHISTORY_EVENT_ENGINEERED',2);
/*\brief I hatched out of my egg and began my journey through life!
 * Value: 3*/
define('CREATUREHISTORY_EVENT_HATCHED',3);
/*\brief I grew up a little! CreatureHistoryEvent::GetLifestage will tell you what lifestage I am now.
 * Value: 4*/
define('CREATUREHISTORY_EVENT_AGED',4);
/*\brief I left this world 
 * Value: 5*/
define('CREATUREHISTORY_EVENT_EXPORTED',5);
/*\brief I joined this world
 * Value: 6*/
define('CREATUREHISTORY_EVENT_IMPORTED',6);
/*\brief My journey through life ended. 
 * Value: 7*/
define('CREATUREHISTORY_EVENT_DIED',7);
/*\brief I became pregnant! (We kisspopped)
 * Value: 8*/
define('CREATUREHISTORY_EVENT_BECAMEPREGNANT',8);
/*\brief I made someone else pregnant! (We kisspopped) 
 * Value: 9*/
define('CREATUREHISTORY_EVENT_IMPREGNATED',9);
/*\brief My child began its journey through life! 
 * Value: 10*/
define('CREATUREHISTORY_EVENT_CHILDBORN',10);
/*\brief My mum laid my egg. Thanks, mum :D 
 * Value: 11*/
define('CREATUREHISTORY_EVENT_MUMLAIDMYEGG',11);
/*\brief I finally got that kid out of my stomach and onto the floor, where it belongs :P 
 * Value: 12*/
define('CREATUREHISTORY_EVENT_LAIDEGG',12);
/*\brief A photo was taken of me. 
 * Value: 13*/
define('CREATUREHISTORY_EVENT_PHOTOTAKEN',13);
/*\brief I was made by cloning another creature. This happens when you export a creature then import it multiple times. 
 * Value: 14*/
define('CREATUREHISTORY_EVENT_IAMCLONED',14); 
/*\brief Another creature was made by cloning me. This happens when you export a creature then import it multiple times. 
 * Value: 15*/
define('CREATUREHISTORY_EVENT_CLONEDME',15);
/*\brief I left this world via the internet :D 
 * Value: 16*/
define('CREATUREHISTORY_EVENT_WARPEDOUT',16);
/*\brief I entered this world via the internet! :D 
 * Value: 17*/
define('CREATUREHISTORY_EVENT_WARPEDIN',17);
//@}

/** \brief Class to represent events in a creature's life*/

class CreatureHistoryEvent {
	private $eventnumber;
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

	public function CreatureHistoryEvent($eventnumber,$worldtime,$creatureage,$timestamp,$lifestage,$moniker1,$moniker2,$usertext,$photograph,$worldname,$worldUID) {
		$this->eventnumber = $eventnumber;
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
	public function AddDSInfo($DSUserID,$unknown1,$unknown2) {
		$this->dockingstationuser = $DSUserID;
		$this->unknown1 = $unknown1;
		$this->unknown2 = $unknown2;
	}
	public function Compile($format) {
		$data = pack('VVVVV',$this->eventnumber,$this->worldtime,$this->creatureage,$this->timestamp,$this->lifestage);
		$data .= pack('V',strlen($this->moniker1)).$this->moniker1;
		$data .= pack('V',strlen($this->moniker2)).$this->moniker2;
		$data .= pack('V',strlen($this->usertext)).$this->usertext;
		$data .= pack('V',strlen($this->photograph)).$this->photograph;
		$data .= pack('V',strlen($this->worldname)).$this->worldname;
		$data .= pack('V',strlen($this->worldUID)).$this->worldUID;
		if($format == GLST_FORMAT_DS) {
			$data .= pack('V',strlen($this->dockingstationuser)).$this->dockingstationuser;
			$data .= pack('VV',$this->unknown1,$this->unknown2);
		}
	}
	public function GetEventNumber() {
		return $this->eventnumber;
	}
	public function GetWorldTime() {
		return $this->worldtime;
	}
	public function GetCreatureAge() {
		return $this->creatureage;
	}
	public function GetTimestamp() {
		return $this->timestamp;
	}
	public function GetLifeStage() {
		return $this->lifestage;
	}
	public function GetMoniker1() {
		return $this->moniker1;
	}
	public function GetMoniker2() {
		return $this->moniker2;
	}
	public function GetUserText() {
		return $this->usertext;
	}
	public function GetPhotograph() {
		return $this->photograph;
	}
	public function GetWorldName() {
		return $this->worldname;
	}
	public function GetWorldUID() {
		return $this->worldUID;
	}
}
?>
