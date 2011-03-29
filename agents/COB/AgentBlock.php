<?php

require_once(dirname(__FILE__).'/COBBlock.php');
require_once(dirname(__FILE__).'/../../sprites/SpriteFrame.php');
require_once(dirname(__FILE__).'/../../sprites/S16Frame.php');
require_once(dirname(__FILE__).'/../../sprites/SPRFrame.php');

/** Sprite dependency */
define('DEPENDENCY_SPRITE','sprite');
/** Sound dependency */
define('DEPENDENCY_SOUND','sound');

/** COB Agent Block for C1 and C2
 * For Creatures 1, this block contains all the useful data in a typical COB and will be the only block.
 * For Creatures 2, this block contains the scripts and metadata about the actual object.
 */
class COBAgentBlock extends COBBlock {
	
	private $agentName;
	private $agentDescription;
	
	private $lastUsageDate; //unix timestamp
	private $reuseInterval; // seconds
	private $quantityAvailable;
	private $expiryDate; //unix timestamp
	
	//'reserved' - never officially used by CL
	private $reserved1;
	private $reserved2;
	private $reserved3;
	
	private $dependencies = array();
	/** @var SpriteFrame */
	private $thumbnail;
	
	private $installScript;
	private $removeScript;
	private $eventScripts;
	
	/** initialise a new COBAgentBlock
	 * Initialises a new COBAgentBlock with the given name and description. 
	 * As defaults can be made for everything else these is the only non-optional parts of a COB file in my opinion.
	 * Even then they could just be '' if you really felt like it. 
	 * @param string $agentName The name of the agent (as displayed in the C2 injector)
	 * @param string $agentDescription The description of the agent (as displayed in the C2 injector)
	 */
	public function COBAgentBlock($agentName,$agentDescription) {
		parent::COBBlock(COB_BLOCK_AGENT);
		$this->agentName = $agentName;
		$this->agentDescription = $agentDescription;
	}
	/** Gets the agent's name 
	 * @return string
	 */
	public function GetAgentName() {
		return $this->agentName;
	}
	/** Gets the agent's description
	 * @return string
	 */
	public function GetAgentDescription() {
		return $this->agentDescription;
	}
	/** Gets the agent's install script
     * @return string
	 */
	public function GetInstallScript() {
		return $this->installScript;
	}
	/** Gets the agent's remove script
	 * @return string
	 */
	public function GetRemoveScript() {
		return $this->removeScript;
	}
	/** Gets the number of event scripts
	 * @return int
	 */
	public function GetEventScriptCount() {
		return sizeof($this->eventScripts);
	}
	/** Gets the agent's event scripts
	 * @return array
	 */
	public function GetEventScripts() {
		return $this->eventScripts;
	}
	/** Gets an event script
	 * Event scripts are not necessarily in any order.
	 * @param int $whichScript Which script to get.
	 * @return string
	 */
	public function GetEventScript($whichScript) {
		return $this->eventScripts[$whichScript];
	}
	/** Gets the thumbnail of this agent as would be shown in the Injector
	 * @return SpriteFrame
     */
	public function GetThumbnail() {
		return $this->thumbnail;
	}
	/** Gets dependencies of the given type
	 * @param int $type One of the DEPENDENCY_* constants.
	 * @return COBDependency
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
	/** Gets the value of reserved1
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved1() {
		return $this->reserved1;
	}
	/** Gets the value of reserved2
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved2() {
		return $this->reserved2;
	}
	/** Gets the value of reserved3
	 * Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved3() {
		return $this->reserved3;
	}
	/** Adds a dependency to this agent
	 * 
	 * @param COBDependency $dependency The COBDependency to add.
	 */
	public function SetDependency(COBDependency $dependency) {
		if(!in_array($dependency->GetDependencyName(),$this->dependencies)) {
			$this->dependencies[] = $dependency;
		}
	}
	/** Adds an install script
	 * @param string $installScript the text of the script to add
	 */
	public function SetInstallScript($installScript) {
		$this->installScript = $installScript;
	}
	/** Sets the remover script
	 * @param string $removeScript The text of the script to add
	 */
	public function SetRemoveScript($removeScript) {
		$this->removeScript = $removeScript;
	}
	/** Adds an event script
	 * @param string $eventScript The text of the script to add
	 */
	public function AddEventScript($eventScript) {
		$this->eventScripts[] = $eventScript;
	}
	/** Sets the date this agent was last injected
	 * @param int $time The date this agent was last injected as a UNIX timestamp
	 */
	public function SetLastUsageDate($time) {
		if($time > time()) {
			return false;
		} else {
			$this->lastUsageDate = $time;
		}
	}
	/** Sets the date this agent will expire
	 * @param int $time The date this agent will expire as a UNIX timestamp
	 */
	public function SetExpiryDate($time) {
		$this->expiryDate = $time;
	}
	/** Sets the quantity of the agent available
	 * @param int $quantity The quantity available
	 */
	public function SetQuantityAvailable($quantity) {
		$this->quantityAvailable = $quantity;
	}
	/** Sets the interval required between re-use.
	 * @param int $interval The interval in seconds, between re-use of this agent.
	 */
	public function SetReuseInterval($interval) {
		$this->reuseInterval = $interval;
	}
	/** Adds the reserved variables to this agent
	 * These variables have no meaning to Creatures 2 and don't appear in Creatures 1.
	 * @param int $reserved1 The first reserved variable
	 * @param int $reserved2 The second reserved variable
	 * @param int $reserved3 The third reserved variable
	 */
	public function SetReserved($reserved1,$reserved2,$reserved3) {
		$this->reserved1 = $reserved1;
		$this->reserved2 = $reserved2;
		$this->reserved3 = $reserved3;
	}
	/** Add the thumbnail to this agent.
	 * @param SpriteFrame $frame The thumbnail as a SpriteFrame 
	 */
	public function SetThumbnail(SpriteFrame $frame) {
		if($this->thumbnail != null) {
			throw new Exception('Thumbnail already added');
		}
		$this->thumbnail = $frame;
	}
	/**
	 * Adds a remover script by reading from an RCB file.
	 * @param IReader $reader A StringReader or FileReader for the RCB
	 */
	public function AddC1RemoveScriptFromRCB(IReader $reader) {
		if($this->removeScript != '') {
			throw new Exception('Script already added!');
		}
		$rcb = new COB($reader);
		$ablocks = $rcb->GetBlocks(COB_BLOCK_AGENT);
		$this->removeScript = $ablocks[0]->GetInstallScript();
	}
	/** @ignore 
     * Creates a new COBAgentBlock from an IReader.
	 * Reads from the current position of the IReader to fill out the data required by
	 * the COBAgentBlock, then creates one and adds all the fields to it.
	 * @param IReader $reader The IReader, seeked to the beginning of the contents of the agent block
	 * @return COBAgentBlock
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
	/** @ignore
	 * Creates a COBAgentBlock from an IReader
	 * Reads from the current position of the IReader to fill out the data required by
	 * the COBAgentBlock, then creates one and adds all the fields to it.
	 * @param $reader The IReader, seeked to the beginning of the contents of the agent block
	 * 
	 */
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
		$sprframe = null;
		if($pictureWidth > 0 || $pictureHeight > 0) {
  		$sprframe = new SPRFrame($reader,$pictureWidth,$pictureHeight);
      $sprframe->Flip(); 
    }
		
		$agentName = $reader->Read($reader->ReadInt(1));
		
		$agentBlock = new COBAgentBlock($agentName,'');
		$agentBlock->AddQuantityAvailable($quantityAvailable);
		$agentBlock->AddExpiryDate($expiryDate);
		if($sprframe != null) {
		  $agentBlock->AddThumbnail($sprframe);
		}
		foreach($objectScripts as $objectScript) {
			$agentBlock->AddEventScript($objectScript);
		}
		$agentBlock->AddInstallScript(implode("\n*c2ephp Install script seperator\n",$installScripts));
		return $agentBlock;
	}
}

/** defines a dependency which is used in a COB file */
class COBDependency {
	private $type;
	private $name;
	
	/** Creates a new COBDependency
	 * @param string $type The type of dependency ('sprite' or 'sound').
	 * @param string $name The name of the dependency (four characters, no file extension)
	 */
	public function COBDependency($type,$name) {
		$this->type = $type;
		$this->name = $name;
	}
	/** Gets the dependency type
     * @return string
     */
	public function GetDependencyType() {
		return $this->type;
	}
	/** Gets the name of the dependency
	 * @return string
	 */
	public function GetDependencyName() {
		return $this->name;
	}
}
?>
