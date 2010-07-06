<?php
require_once(dirname(__FILE__).'/COBBlock.php');
require_once(dirname(__FILE__).'/../../sprites/SpriteFrame.php');
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
	/// \brief Gets the agent's name
	public function GetAgentName() {
		return $this->agentName;
	}
	/// \brief Gets the agent's description
	public function GetAgentDescription() {
		return $this->agentDescription;
	}
	/// \brief Gets the agent's install script
	public function GetInstallScript() {
		return $this->installScript;
	}
	/// \brief Gets the agent's remove script
	public function GetRemoveScript() {
		return $this->removeScript;
	}
	/// \brief Gets the number of event scripts
	public function GetEventScriptCount() {
		return sizeof($this->eventScripts);
	}
	/// \brief Gets the agent's event scripts
	public function GetEventScripts() {
		return $this->eventScripts;
	}
	/** \brief Gets an event script
	 * Event scripts are not necessarily in any order.
	 * \param $whichScript Which script to get.
	 */
	public function GetEventScript($whichScript) {
		return $this->eventScripts[$whichScript];
	}
	/// \brief Gets the thumbnail of this agent as an ISpriteFrame
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
	/** \brief Adds a dependency to this agent
	 * 
	 * \param COBDependency $dependency The dependency to add.
	 */
	public function AddDependency(COBDependency $dependency) {
		if(!in_array($dependency->GetDependencyName(),$this->dependencies)) {
			$this->dependencies[] = $dependency;
		}
	}
	/** \brief Adds an install script
	 * \param $installScript the text of the script to add
	 */
	public function AddInstallScript($installScript) {
		$this->installScript = $installScript;
	}
	/** \brief Adds a remover script
	 * \param $removeScript The text of the script to add
	 */
	public function AddRemoveScript($removeScript) {
		$this->removeScript = $removeScript;
	}
	/** \brief Adds an event script
	 * \param $removeScript The text of the script to add
	 */
	public function AddEventScript($eventScript) {
		$this->eventScripts[] = $eventScript;
	}
	/** \brief Adds the date this agent was last injected
	 * \param $time The date this agent was last injected as a UNIX timestamp
	 */
	public function AddLastUsageDate($time) {
		if($time > time()) {
			return false;
		} else {
			$this->lastUsageDate = $time;
		}
	}
	/** \brief Adds the date this agent will expire
	 * \param $time The date this agent will expire as a UNIX timestamp
	 */
	public function AddExpiryDate($time) {
		$this->expiryDate = $time;
	}
	/** \brief Adds the quantity of the agent available
	 * \param $quantity The quantity available
	 */
	public function AddQuantityAvailable($quantity) {
		$this->quantityAvailable = $quantity;
	}
	/** \brief Adds the interval required between re-use.
	 * \param $interval The interval in seconds, between re-use of this agent.
	 */
	public function AddReuseInterval($interval) {
		$this->reuseInterval = $interval;
	}
	/** \brief Adds the reserved variables to this agent
	 * These variables have no meaning to Creatures 2 and don't appear in Creatures 1.
	 * \param $reserved1 The first reserved variable
	 * \param $reserved2 The second reserved variable
	 * \param $reserved3 The third reserved variable
	 */
	public function AddReserved($reserved1,$reserved2,$reserved3) {
		$this->reserved1 = $reserved1;
		$this->reserved2 = $reserved2;
		$this->reserved3 = $reserved3;
	}
	/** \brief Add the thumbnail to this agent.
	 * \param SpriteFrame $frame The thumbnail as a SpriteFrame 
	 */
	public function AddThumbnail(SpriteFrame $frame) {
		if($this->thumbnail != null) {
			throw new Exception('Thumbnail already added');
		}
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
	/** \brief Creates a COBAgentBlock from an IReader
	 * Reads from the current position of the IReader to fill out the data required by
	 * the COBAgentBlock, then creates one and adds all the fields to it.
	 * \param $reader The IReader, seeked to the beginning of the contents of the agent block
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

/// \brief defines a dependency as used in a COB file
class COBDependency {
	private $type;
	private $name;
	
	/** \brief Creates a new COBDependency
	 * \param $type The type of dependency.
	 * \param $name The name of the dependency.
	 */
	public function COBDependency($type,$name) {
		$this->type = $type;
		$this->name = $name;
	}
	/// \brief Gets the dependency type
	public function GetDependencyType() {
		return $this->type;
	}
	/// \brief Gets the name of the dependency
	public function GetDependencyName() {
		return $this->name;
	}
}
?>