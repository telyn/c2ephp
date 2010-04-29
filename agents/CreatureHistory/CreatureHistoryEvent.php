<?php
require_once(dirname(__FILE__).'/../PRAY/GLSTBlock.php');

/** \brief Class to represent events in a creature's life*/
/**
*
*/
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
	public function GetEventName() {
		switch($this->eventnumber) {
			case 0:
				return 'conceived';
			case 1:
				return 'spliced';
			case 2:
				return 'engineered';
			case 3:
				return 'hatched';
			case 4:
				return 'aged';
			case 5:
				return 'exported';
			case 6:
				return 'imported';
			case 7:
				return 'died';
			case 8:
				return 'got pregnant';
			case 9:
				return 'made other creature pregnant';
			case 10:
				return 'child hatched';
			case 11:
				return 'egg laid';
			case 12:
				return 'laid egg';
			case 13:
				return 'photo taken';
			case 14:
				return 'cloned from';
			case 15:
				return 'was cloned';
			case 16:
				return 'warped out';
			case 17:
				return 'warped in';
			default:
				return 'unknown';
				
		}
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
