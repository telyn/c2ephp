<?php

require_once(dirname(__FILE__).'/../../support/StringReader.php');
require_once(dirname(__FILE__).'/../../support/Archiver.php');
require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/../CreatureHistory/CreatureHistory.php');

define('GLST_FORMAT_UNKNOWN',0);
define('GLST_FORMAT_C3',1);
define('GLST_FORMAT_DS',2);
class GLSTBlock extends CreaturesArchiveBlock {
	private $history;
	private $format = GLST_FORMAT_UNKNOWN;
	public function GLSTBlock($object,$name,$content,$flags) {
		parent::CreaturesArchiveBlock($object,$name,$content,$flags,PRAY_BLOCK_GLST);
		if($object instanceof PRAYFile) {
			$this->Decode();
		} else if($object instanceof CreatureHistory) {
			$this->history = $object;
		} else {
			die('Couldn\'t create a GLST block. :(');
		}
	}
	public function Compile($format=GLST_FORMAT_UNKNOWN) {
		//if you don't know
		if($format == GLST_FORMAT_UNKNOWN) {
			//and I don't know
			if($this->format == GLST_FORMAT_UNKNOWN) {
				//ask $prayfile if it exists. (look for DSEX, otherwise C3)
				if($this->prayfile != null) {
					//prayfile should know
					if(sizeof($this->prayfile->GetBlocks(PRAY_BLOCK_DSEX)) > 0) {
						$format = GLST_FORMAT_DS;
					} else {
						$format = GLST_FORMAT_C3;
					}
				} else {
					//history will know. (Though it could be wrong)
					$format = $this->history->GuessFormat();
				}				
			} else {
				$format = $this->format;
			}
			$compiled = $this->history->Compile($format);
			return $this->EncodeBlockHeader(strlen($compiled)) . $compiled;
		}
	}
	public function GetHistory() {
		return $this->history;
	}
	public function GetPHOTBlockName($event) {
		if($this->format == GLST_FORMAT_DS) {
			return $event->GetPhotograph().'.DSEX.photo';
		} else {
			return $event->GetPhotograph().'.photo';
		}
	}
	private function Decode() {	
		$reader = new StringReader($this->GetData());
		$firstchar = $reader->Read(1);
		if($firstchar == chr(0x27)) {
			//ds
			$this->format = GLST_FORMAT_DS;
		} else if($firstchar == chr(0x0C)) {
			//c3
			$this->format = GLST_FORMAT_C3;
		} else {
			print 'Unknown format!';
			return false;
		}
		//Good. Let's begin.
		//bunch of bytes I don't get. (always seemed to be null, I think)
		//Actually, the first four bytes including $firstchar are probably one integer used to identify the game used.
		//seems like the way CL rolls with c2e.
		$reader->Skip(3); // 3 nulls.
		print('At '.$reader->GetPosition()."\n");
		if($reader->ReadInt(4)!=1) { //not :O always 1. Don't know why.
			print('I guess I was wrong about it always being 1.');
			return false;
		}
		$moniker		= $reader->Read($reader->ReadInt(4));
		$reader->Skip($reader->ReadInt(4)); //second moniker is always identical and never necessary.
		$name			= $reader->Read($reader->ReadInt(4));
		$gender			= $reader->ReadInt(4);
		$genus			= $reader->ReadInt(4); //0 for norn, 1 for grendel, 2 for ettin
		$species		= $reader->ReadInt(4);
		$eventslength	= $reader->ReadInt(4);
		$this->history = new CreatureHistory($moniker,$name,$gender,$genus,$species);

		if(!isset($eventslength)) {
			return false;
		}
		for($i=0;$i<$eventslength;$i++) {
			$this->DecodeEvent($reader);
		}
		
		//reading the footer
		$unknown1 = $reader->ReadInt(4);
		$unknown2 = $reader->ReadInt(4);
		if($this->format == GLST_FORMAT_DS) {
			$unknown3 = $reader->ReadInt(4);
			$warpveteran = (($reader->ReadInt(4)==1)?1:0);
			$unknown4 = $reader->Read($reader->ReadInt(4));
			$this->history->SetDSUnknowns($unknown1,$unknown2,$unknown3,$unknown4);
			$this->history->SetWarpVeteran($warpveteran);
		} else {
			$this->history->SetC3Unknowns($unknown1,$unknown2);
		}

		return $this->history;
	}
	private function DecodeEvent($reader) {
		$eventNumber = $reader->ReadInt(4);
        //echo 'Event '.$eventNumber."\n";
        if($eventNumber < 18) {
				$eventnumber	= $eventNumber;
				$worldtime		= $reader->ReadInt(4);
				$creatureage	= $reader->ReadInt(4);
				$timestamp		= $reader->ReadInt(4);
				$lifestage		= $reader->ReadInt(4);
				$moniker		= $reader->Read($reader->ReadInt(4));
				$moniker2		= $reader->Read($reader->ReadInt(4));
				$usertext		= $reader->Read($reader->ReadInt(4));
				$photograph		= $reader->Read($reader->ReadInt(4));
				$worldname		= $reader->Read($reader->ReadInt(4));
				$worldUID		= $reader->Read($reader->ReadInt(4));
                $event = new CreatureHistoryEvent($eventnumber,$worldtime,$creatureage,
							$timestamp,$lifestage,$moniker,$moniker2,$usertext,$photograph,
							$worldname,$worldUID);
                if($this->format == GLST_FORMAT_DS) {
					$DSUser		= $reader->Read($reader->ReadInt(4));
                    $unknown1	= $reader->ReadInt(4);
					$unknown2	= $reader->ReadInt(4);
					$event->AddDSInfo($DSUser,$unknown1,$unknown2);
                }
				$this->history->AddEvent($event);
                return true;
        }
        return false;		
	}
}
?>