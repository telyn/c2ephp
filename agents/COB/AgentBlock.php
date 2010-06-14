<?php
require_once(dirname(__FILE__).'/COBBlock.php');
require_once(dirname(__FILE__).'/../../sprites/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../../sprites/S16Frame.php');
require_once(dirname(__FILE__).'/../../sprites/SPRFrame.php');

/** \name Dependency Types
 * Types of dependencies that apply to COBs.
 * @{
 * \brief Sprite Dependency
**/
define('DEPENDENCY_SPRITE','sprite');
/// \brief Sound Dependency
define('DEPENDENCY_SOUND','sound');
///@}

/** \brief COB Agent Block for C1 and C2
 * For Creatures 1, this block contains all the useful data in a typical COB and will be the only block.
 * For Creatures 2, this block contains the scripts and metadata about the actual object.
 */
class COBAgentBlock extends COBBlock {
	
	private $agentName;
	private $agentDescription;
	
	private $lastUsageDate; //unix timestamp
	private $reuseInterval; // seconds IIRC
	private $quantityAvailable;
	private $expiryDate; //unix timestamp
	
	//'reserved' - never officially used by CL
	private $reserved1;
	private $reserved2;
	private $reserved3;
	
	private $dependencies = array();
	
	private $thumbnail; //ISpriteFrame ready to OutputPNG
	
	private $installScript;
	private $removeScript;
	private $eventScripts;
	
	/** \brief initialise a new COBAgentBlock
	 * Initialises a new COBAgentBlock with the given name and description. 
	 * As defaults can be made for everything else these is the only non-optional parts of a COB file in my opinion.
	 * Even then they could just be '' if you really felt like it. 
	 * \param $agentName The name of the agent (as displayed in the C2 injector)
	 * \param $agentDescription The description of the agent (as displayed in the C2 injector)
	 */
	public function COBAgentBlock($agentName,$agentDescription) {
		parent::COBBlock(COB_BLOCK_AGENT);
		$this->agentName = $agentName;
		$this->agentDescription = $agentDescription;
	}
	public function GetAgentName() {
		return $this->agentName;
	}
	public function GetAgentDescription() {
		return $this->agentDescription;
	}
	public function GetInstallScript() {
		return $this->installScript;
	}
	public function GetRemoveScript() {
		return $this->removeScript;
	}
	public function GetEventScripts() {
		return $this->eventScripts;
	}
	public function GetEventScript($whichScript) {
		return $this->eventScripts[$whichScript];
	}
	public function GetThumbnail() {
		return $this->thumbnail;
	}
	/** \brief Gets dependencies of the given type
	 * \param $type One of the DEPENDENCY_* constants.
	 */
	public function GetDependencies($type=null) {
		$dependenciesToReturn = array();
		foreach($this->dependencies as $dependency) {
			if($type == null || $type == $dependency->GetType()) {
				$dependenciesToReturn[] = $dependency;
			}
		}
		return $dependenciesToReturn;
	}
	/** \Gets the value of reserved1
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 */
	public function GetReserved1() {
		return $this->reserved1;
	}
	/** \Gets the value of reserved2
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 */
	public function GetReserved2() {
		return $this->reserved2;
	}
	/** \Gets the value of reserved3
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 */
	public function GetReserved3() {
		return $this->reserved3;
	}
	
	public function AddDependency(COBDependency $dependency) {
		if(!in_array($dependency->GetDependencyName(),$this->dependencies)) {
			$this->dependencies[] = $dependency;
		}
	}
	public function AddInstallScript($installScript) {
		$this->installScript = $installScript;
	}
	public function AddRemoveScript($removeScript) {
		$this->removeScript = $removeScript;
	}
	public function AddEventScript($eventScript) {
		$this->eventScripts[] = $eventScript;
	}
	public function AddLastUsageDate($time) {
		if($time > time()) {
			return false;
		} else {
			$this->lastUsageDate = $time;
		}
	}
	public function AddExpiryDate($time) {
		$this->expiryDate = $time;
	}
	public function AddQuantityAvailable($quantity) {
		$this->quantityAvailable = $quantity;
	}
	public function AddReuseInterval($interval) {
		$this->reuseInterval = $interval;
	}
	public function AddReserved($reserved1,$reserved2,$reserved3) {
		$this->reserved1 = $reserved1;
		$this->reserved2 = $reserved2;
		$this->reserved3 = $reserved3;
	}
	
	public function AddThumbnail(ISpriteFrame $frame) {
		$this->thumbnail = $frame;
	}
	public function AddC1RemoveScriptFromRCB(IReader $reader) {
		if($this->removeScript != '') {
			throw new Exception('Script already added!');
		}
		$rcb = new COB($reader);
		$ablocks = $rcb->GetBlocks(COB_BLOCK_AGENT);
		$this->removeScript = $ablocks[0]->GetInstallScript();
	}
	/** \brief Creates a new COBAgentBlock from an IReader.
	 * Reads from the current position of the IReader to fill out the data required by
	 * the COBAgentBlock, then creates one and adds all the fields to it.
	 * \param $reader The IReader, seeked to the beginning of the contents of the agent block
	 */
	public static function CreateFromReaderC2(IReader $reader) {
		$quantityAvailable = $reader->ReadInt(2);
		if($quantityAvailable == 0xffff) {
			$quantityAvailable = -1;
		}		
		$lastUsageDate = $reader->ReadInt(4);
		$reuseInterval = $reader->ReadInt(4);
		
		$expiryDay = $reader->ReadInt(1);
		$expiryMonth = $reader->ReadInt(1);
		$expiryYear = $reader->ReadInt(2);
		$expiryDate = mktime(0,0,0,$expiryMonth,$expiryDay,$expiryYear);
		
		$reserved = array($reader->ReadInt(4),$reader->ReadInt(4),$reader->ReadInt(4));
		
		$agentName = $reader->ReadCString();
		$agentDescription = $reader->ReadCString();
		
		$installScript = str_replace(',',"\n",$reader->ReadCString());
		$removeScript = str_replace(',',"\n",$reader->ReadCString());
		
		$numEventScripts = $reader->ReadInt(2);
		
		$eventScripts = array();
		
		for($i=0;$i<$numEventScripts;$i++) {
			$eventScripts[] = str_replace(',',"\n",$reader->ReadCString());
		}
		$numDependencies = $reader->ReadInt(2);
		$dependencies = array();
		
		for($i=0;$i<$numDependencies;$i++) {
			$type = ($reader->ReadInt(2) == 0)?DEPENDENCY_SPRITE:DEPENDENCY_SOUND;
			$name = $reader->ReadCString();
			$dependencies[] = new COBDependency($type,$name);
		}
		print_r($dependencies);
		$thumbWidth = $reader->ReadInt(2);
		$thumbHeight = $reader->ReadInt(2);
	
		$thumbnail = new S16Frame($reader,'565',$thumbWidth,$thumbHeight,$reader->GetPosition());
		$reader->Skip($thumbHeight*$thumbWidth*2);
		//parsing finished, onto making an AgentBlock.
		$agentBlock = new COBAgentBlock($agentName,$agentDescription);
		$agentBlock->AddQuantityAvailable($quantityAvailable);
		$agentBlock->AddReuseInterval($reuseInterval);
		$agentBlock->AddExpiryDate($expiryDate);
		$agentBlock->AddLastUsageDate($lastUsageDate);
		$agentBlock->AddReserved($reserved[0],$reserved[1],$reserved[2]);
		$agentBlock->AddInstallScript($installScript);
		$agentBlock->AddRemoveScript($removeScript);
		foreach($eventScripts as $eventScript) {
			$agentBlock->AddEventScript($eventScript);
		}
		foreach($dependencies as $dependency) {
			$agentBlock->AddDependency($dependency);
		}
		$agentBlock->AddThumbnail($thumbnail);
		return $agentBlock;
		
	}
	public static function CreateFromReaderC1(IReader $reader) {
		$quantityAvailable	= $reader->ReadInt(2);
		$expires_month = $reader->ReadInt(4);
		$expires_day = $reader->ReadInt(4);
		$expires_year = $reader->ReadInt(4);
		$expiryDate = mktime(0,0,0,$expires_month,$expires_day,$expires_year);
				
		$numObjectScripts = $reader->ReadInt(2);
		$numInstallScripts = $reader->ReadInt(2);
		$quantityUsed = $reader->ReadInt(4);
		$objectScripts = array();
		for($i=0;$i<$numObjectScripts;$i++) {
			$scriptsize = $reader->ReadInt(1);
			if($scriptsize == 255) {
				$scriptsize = $reader->ReadInt(2);
			}
			$objectScripts[$i] = $reader->Read($scriptsize);
		}
		$installScripts = array();
		for($i=0;$i<$numInstallScripts;$i++) {
			$scriptsize = $reader->ReadInt(1);
			if($scriptsize == 255) {
				$scriptsize = $reader->ReadInt(2);
			}
			$installScripts[$i] = $reader->Read($scriptsize);
		}
		$pictureWidth = $reader->ReadInt(4);
		$pictureHeight = $reader->ReadInt(4);
		$unknown = $reader->ReadInt(2);
		
		$sprframe = new SPRFrame($reader,$pictureWidth,$pictureHeight);
		$sprframe->Flip();
		$agentName = $reader->Read($reader->ReadInt(1));
		
		$agentBlock = new COBAgentBlock($agentName,'');
		$agentBlock->AddQuantityAvailable($quantityAvailable);
		$agentBlock->AddExpiryDate($expiryDate);
		$agentBlock->AddThumbnail($sprframe);
		foreach($objectScripts as $objectScript) {
			$agentBlock->AddEventScript($objectScript);
		}
		$agentBlock->AddInstallScript(implode("\n*c2ephp Install script seperator\n",$installScripts));
		return $agentBlock;
	}
}

class COBDependency {
	private $type;
	private $name;
	
	public function COBDependency($type,$name) {
		$this->type = $type;
		$this->name = $name;
	}
	public function GetDependencyType() {
		return $this->type;
	}
	public function GetDependencyName() {
		return $this->name;
	}
}
?>