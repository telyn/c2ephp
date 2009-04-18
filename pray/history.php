<?php
require_once(dirname(__FILE__).'/../support/Archiver.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');

class CreatureHistory {
	private $reader;
	private $history;
	
	public function CreatureHistory(IReader $reader) {
		$this->reader = $reader;
	}
	public function Decode() {
		$firstchar = $this->reader->Read(1);
		if($firstchar == 'C') { //Still compressed
			$data = $this->reader->GetSubString(0);
			$data = DeArchive($data);
			if($data !== false) {
				$this->reader = new StringReader($data);
				$this->Decode();
			}
		} else if($firstchar == chr(0x27)) {
			//Good. Let's begin.
			$this->reader->Read(3);
			while($this->DecodeEvent());
			//check for creature age on last
			$length = sizeof($this->history);
			$lastitem = $this->history[$length-1];
			$onebeforelast = $this->history[$length-2];
			if($lastitem['creatureage'] < $onebeforelast['creatureage'] || $lastitem['lifestage'] < $onebeforelast['lifestage']){
				unset($this->history[$length-1]);
			}
			return $this->history;
		} else {
			die(' D:');
			return false;
		}
	}
	private function DecodeEvent() {
		$eventNumber = $this->reader->ReadInt(4);
		//echo 'Event '.$eventNumber."\n";
		if($eventNumber == 0) {
			//file has probably ended.
			//echo 'Ended?';
			return false;
		} else if($eventNumber == 1) {
			$this->DecodeSplicedEvent();
			return true;
		} else if($eventNumber < 18) {
			$this->DecodeRegularEvent($eventNumber);
			return true;
		}
	}
	private function DecodeRegularEvent($eventNumber) {
		$eventInfo = array(
		'eventnumber'		=> $eventNumber,
		'eventname'			=> $this->GetEventNameByNumber($eventNumber),
		'worldtime'			=> $this->reader->ReadInt(4),
		'creatureage'		=> $this->reader->ReadInt(4),
		'timestamp'			=> $this->reader->ReadInt(4),
		'lifestage'			=> $this->reader->ReadInt(4),
		'eventspecific'		=> array($this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4))),
		'worldname'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'worldUID'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'DSUser'			=> $this->reader->Read($this->reader->ReadInt(4))
		);
		$this->reader->Read(8);
		$this->history[] = $eventInfo;
	}
	private function DecodeSplicedEvent() {
		$eventInfo = array(
		'eventnumber'		=> 1,
		'eventname'			=> $this->GetEventNameByNumber(1),
		'moniker'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'moniker2'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'name'				=> $this->reader->Read($this->reader->ReadInt(4)),
		'gender'			=> $this->reader->ReadInt(4),
		'unknown1'			=> $this->reader->ReadInt(4),
		'species'			=> $this->reader->ReadInt(4),
		'unknown2'			=> $this->reader->ReadInt(4),
		'unknown3'			=> $this->reader->ReadInt(4),
		'worldtime'			=> $this->reader->ReadInt(4),
		'creatureage'		=> $this->reader->ReadInt(4),
		'timestamp'			=> $this->reader->ReadInt(4),
		'lifestage'			=> $this->reader->ReadInt(4),
		'eventspecific'		=> array($this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4))),
		'worldname'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'worldUID'			=> $this->reader->Read($this->reader->ReadInt(4)),
		'DSUser'			=> $this->reader->Read($this->reader->ReadInt(4))
		);
		$this->reader->Read(8);
		$this->history[] = $eventInfo;
	}
	public function GetEventNameByNumber($eventnumber) {
		switch($eventnumber) {
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
	public function GetGenderFromInteger($gender) {
		return ($gender==0)?'F':'M';
	}
}

?>