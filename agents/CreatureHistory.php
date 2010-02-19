<?php
require_once(dirname(__FILE__).'/../support/Archiver.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/PRAYFile.php');

class CreatureHistory {
	private $reader;
	private $history;
	private $prayFile;
	public function CreatureHistory(PRAYFile $prayFile) {
		$prayFile->Parse();
		$history = $prayFile->GetBlocks('GLSTBlock');
		if(sizeof($history) == 0) {
			throw new Exception('This pray file contains no GLST blocks');
		}
		$this->prayFile = $prayFile;
		$this->reader = new StringReader($history[0]['Content']);
	}
	public function Decode() {
		$firstchar = $this->reader->Read(1);
		if($firstchar == 'C') { //Still compressed
			$data = $this->reader->GetSubString(0);
			$data = DeArchive($data);
			if($data !== false) {
				$this->reader = new StringReader($data);
				return $this->Decode();
			}
			return false;
		} else if($firstchar == chr(0x27)) {
			//Good. Let's begin.
			$this->reader->Read(3);
			if($this->reader->ReadInt(4)!=1) {
				return false;
			}
			$this->history['information'] = array(
			'moniker'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'moniker2'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'name'				=> $this->reader->Read($this->reader->ReadInt(4)),
			'gender'			=> $this->reader->ReadInt(4),
			'genus'				=> $this->reader->ReadInt(4), //0 for norn, 1 for grendel, 2 for ettin
			'species'			=> $this->reader->ReadInt(4),
			'eventslength'		=> $this->reader->ReadInt(4)
			);
			
			if(!isset($this->history['information']['eventslength'])) {
				return false;
			}
			
			for($i=0;$i<$this->history['information']['eventslength'];$i++) {
				$this->DecodeEvent();
			}
			
			//reading the footer
			for($i=0;$i<4;$i++) {
				$this->history['information'] += array(
					'unknown1' => $this->reader->ReadInt(4),
					'unknown2' => $this->reader->ReadInt(4),
					'unknown3' => $this->reader->ReadInt(4),
					'warpveteran' => (($this->reader->ReadInt(4)==1)?1:0),
					'unknown4' => $this->reader->Read($this->reader->ReadInt(4))
				);
			}
			
			return $this->history;
		} else {
			return false;
		}
	}
	private function DecodeEvent() {
		$eventNumber = $this->reader->ReadInt(4);
		//echo 'Event '.$eventNumber."\n";
		if($eventNumber < 18) {
				$eventInfo = array(
			'eventnumber'		=> $eventNumber,
			'eventname'			=> $this->GetEventNameByNumber($eventNumber),
			'worldtime'			=> $this->reader->ReadInt(4),
			'creatureage'		=> $this->reader->ReadInt(4),
			'timestamp'			=> $this->reader->ReadInt(4),
			'lifestage'			=> $this->reader->ReadInt(4),
			'monikers'			=> array($this->reader->Read($this->reader->ReadInt(4)),$this->reader->Read($this->reader->ReadInt(4))),
			'usertext'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'photograph'		=> array('name' => $this->reader->Read($this->reader->ReadInt(4))),
			'worldname'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'worldUID'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'DSUser'			=> $this->reader->Read($this->reader->ReadInt(4)),
			'unknown1'			=> $this->reader->ReadInt(4),
			'unknown2'			=> $this->reader->ReadInt(4)
			);
			if($this->prayFile != null && $eventInfo['photograph']['name'] != '') {
				$eventInfo['photograph']['data'] = $this->prayFile->GetBlockByName($eventInfo['photograph']['name'].'.DSEX.photo');
				if($eventInfo['photograph']['data'] == '') {
					$eventInfo['photograph']['data'] = $this->prayFile->GetBlockByName($eventInfo['photograph']['name'].'.EXPC.photo');
				}
				$eventInfo['photograph']['data'] = $eventInfo['photograph']['data']['Content'];
				
			}
			$this->history['events'][] = $eventInfo;
			return true;
		}
		return false;
	}
	private function DecodeRegularEvent($eventNumber) {
		
	}
	public function GetEventNameByNumber($eventnumber) {
		switch($eventnumber) {
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
	public function GetGenderFromInteger($gender) {
		return ($gender==0)?'F':'M';
	}
}

?>
