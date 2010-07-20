<?php

require_once(dirname(__FILE__).'/../../support/StringReader.php');
require_once(dirname(__FILE__).'/../../support/Archiver.php');
require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/../CreatureHistory/CreatureHistory.php');

define('GLST_FORMAT_UNKNOWN',0);
define('GLST_FORMAT_C3',1);
define('GLST_FORMAT_DS',2);
/** \brief PRAY Block for Creature History Data.
  * because of PHP being a single-inheritance language, we store the
  * history as a CreatureHistory variable rather than simply extending
  * CreatureHistory as well as CreaturesArchiveBlock.
  */
class GLSTBlock extends CreaturesArchiveBlock {
	private $history;
	private $format = GLST_FORMAT_UNKNOWN;
	/** \brief Creates a new GLSTBlock
    * \param $object The PRAYFile this FILEBlock belongs to, or the CreatureHistory object to store. *CANNOT* be null.
    * \param $name The name of this file block (also the file's name)
    * \param $content The binary data of this file block.
    * \param $flags The block's flags. See PrayBlock.
   **/
	public function GLSTBlock($object,$name,$content,$flags) {
		parent::CreaturesArchiveBlock($object,$name,$content,$flags,PRAY_BLOCK_GLST);
		if($object instanceof PRAYFile) {
			//Do nothing! Decoding is automated later now :)
		} else if($object instanceof CreatureHistory) {
			$this->history = $object;
		} else {
			throw new Exception('Couldn\'t create a GLST block. :(');
		}
	}

	/** \brief Tries hard to work out which game this GLSTBlock is from. */
	private function GuessFormat() {
		//if I don't know
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
			//cache so I don't need to ask again :)
			$this->format = $format;
		}
		return $format;
	}
	/** \brief Compiles the block into binary for PrayBlock */
	protected function CompileBlockData($format=GLST_FORMAT_UNKNOWN) {
		//if you don't know
		if($format == GLST_FORMAT_UNKNOWN) {
			$format = $this->GuessFormat();
		}			
		$compiled = Archive($this->history->Compile($format));
		return $compiled;
	}
	/** \brief Gets the CreatureHistory this block stores. */
	public function GetHistory() {
		$this->EnsureDecompiled();
		return $this->history;
	}
	/** \brief Gets the PHOTBlock name corresponding to the event given.
	  * Useful for getting a photo event's photo.
      * e.g. if $block is a GLSTBlock, $prayfile a PRAYFile, and $event
	  * a CreatureHistoryEvent for an event with a photo,
	  * one can use this function thusly:
	  * $photblock = $prayfile->GetBlockByName($block->GetPHOTBlockName($event))
	  * $photo = $photblock->GetS16File();
	  * $photo is now an S16File ready for use.
	*/
	public function GetPHOTBlockName($event) {
		$photoname = $event->GetPhotoGraph();
		if(empty($photoname)) {
			return null;
		}
		if($this->format == GLST_FORMAT_DS) {
			return $photoname.'.DSEX.photo';
		} else {
			return $photoname.'.photo';
		}
	}
	/* \brief Decompiles the GLST format into a CreatureHistory object, then stores it */
	protected function DecompileBlockData() {	
		$reader = new StringReader($this->GetData());
		$firstchar = $reader->Read(1);
		if($firstchar == chr(0x27)) { //apostrophe thing
			//ds
			$this->format = GLST_FORMAT_DS;
		} else if($firstchar == chr(0x0C)) { //control character
			//c3
			$this->format = GLST_FORMAT_C3;
		} else {
			print 'Unknown format!';
			return false;
		}
		//the first four bytes including $firstchar are probably one integer used to identify the game used.
		//We read the first one above and now we're skipping the next three.
		$reader->Skip(3); // 3 nulls.
		if($reader->ReadInt(4)!=1) { //Always 1, don't know why.
			throw new Exception('I guess I was wrong about it always being 1.');
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
	}
	/** \brief Decodes an event. Used by DecompileBlockData.
	  * Not for public consumption. Move along, citizen.
	*/
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
