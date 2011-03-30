<?php

require_once(dirname(__FILE__).'/COBBlock.php');
require_once(dirname(__FILE__).'/../../sprites/SpriteFrame.php');
require_once(dirname(__FILE__).'/../../sprites/S16Frame.php');
require_once(dirname(__FILE__).'/../../sprites/SPRFrame.php');

/** @name Dependency Types
 * The two types of dependency available to C1/C2 COBs
 */
//@{
/// @brief Sprite dependency - 'sprite'
define('DEPENDENCY_SPRITE','sprite');
/// @brief Sound dependency - 'sound'
define('DEPENDENCY_SOUND','sound');
//@}


/// @brief COB Agent Block for C1 and C2
/**
 * For Creatures 1, this block contains all the useful data in a typical COB and will be the only block.\n
 * For Creatures 2, this block contains the scripts and metadata about the actual object.
 */
class COBAgentBlock extends COBBlock {
	/// @cond INTERNAL_DOCS
    
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
	private $thumbnail; // SpriteFrame
	
	private $installScript;
	private $removeScript;
	private $eventScripts;
	/// @endcond

	/// @brief Initialises a new COBAgentBlock with the given name and description. 
    /** As defaults can be made for everything else these are the only non-optional
     * parts of a COB file in my opinion. Even then they could just be '' if you
     * really felt like it. 
	 * @param $agentName The name of the agent (as displayed in the C2 injector)
	 * @param $agentDescription The description of the agent (as displayed in the C2 injector)
	 */
	public function COBAgentBlock($agentName,$agentDescription) {
		parent::COBBlock(COB_BLOCK_AGENT);
		$this->agentName = $agentName;
		$this->agentDescription = $agentDescription;
	}
	/// @brief Gets the agent's name 
	/** @return string
	 */
	public function GetAgentName() {
		return $this->agentName;
	}
	/// @brief Gets the agent's description
	/** @return string
	 */
	public function GetAgentDescription() {
		return $this->agentDescription;
	}
	/// @brief Gets the agent's install script
    /** @return string
	 */
	public function GetInstallScript() {
		return $this->installScript;
	}
	/// @brief Gets the agent's remove script
	/** @return string
	 */
	public function GetRemoveScript() {
		return $this->removeScript;
	}
	/// @brief Gets the number of event scripts
	/** @return int
	 */
	public function GetEventScriptCount() {
		return sizeof($this->eventScripts);
	}
	/// @brief Gets the agent's event scripts
	/** @return array of strings, each string is an event script
	 */
	public function GetEventScripts() {
		return $this->eventScripts;
	}
	/// @brief Gets an event script
    /** Event scripts are not necessarily in any order, so you have to work out what each script is for yourself.
	 * @param $whichScript Which script to get.
	 * @return A string containing the event script. Each line is seperated by a comma I think.
	 */
	public function GetEventScript($whichScript) {
		return $this->eventScripts[$whichScript];
	}
	/// @brief Gets the thumbnail of this agent as would be shown in the Injector
	/** @return A SpriteFrame of the thumbnail
     */
	public function GetThumbnail() {
		return $this->thumbnail;
	}
    /// @brief Gets dependencies of the given type
    /** If type is null, will get all dependencies.
     * @see agents/COB/AgentBlock.php Dependency Types 
	 * @param $type One of the Dependency Types constants.
	 * @return An array of COBDependency objects
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
	/// @brief Gets the value of reserved1
	/** Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved1() {
		return $this->reserved1;
	}
	/// @brief Gets the value of reserved2
	/** Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved2() {
		return $this->reserved2;
	}
	/// @brief Gets the value of reserved3
	/** Reserved values weren't ever officially used by CL,
	 * but someone might find them useful for something else.
	 * @return int
	 */
	public function GetReserved3() {
		return $this->reserved3;
	}
	/// @brief Adds a dependency to this agent
	/** 
	 * @param $dependency The COBDependency to add.
	 */
	public function AddDependency(COBDependency $dependency) { 
		if(!in_array($dependency->GetDependencyName(),$this->dependencies)) {
			$this->dependencies[] = $dependency;
		}
	}
	/// @brief Sets the install script
    /**
     * @param $installScript the text of the script to add
	 */
	public function SetInstallScript($installScript) {
		$this->installScript = $installScript;
	}
	/// @brief Sets the remover script
    /**
     * @param $removeScript The text of the script to add
	 */
	public function SetRemoveScript($removeScript) {
		$this->removeScript = $removeScript;
	}
	/// @brief Adds an event script
    /** 
     * @param $eventScript The text of the script to add
	 */
	public function AddEventScript($eventScript) {
		$this->eventScripts[] = $eventScript;
	}
	/// @brief Sets the date this agent was last injected
    /** 
     * @param $time The date this agent was last injected as a UNIX timestamp
	 */
	public function SetLastUsageDate($time) {
		if($time > time()) {
			return false;
		} else {
			$this->lastUsageDate = $time;
		}
	}
	/// @brief Sets the date this agent will expire
    /**
     * @param $time The date this agent will expire as a UNIX timestamp
	 */
	public function SetExpiryDate($time) {
		$this->expiryDate = $time;
	}
	/// @brief Sets the quantity of the agent available
    /**
     * @param $quantity The quantity available, an integer. 0xFF means infinite.
	 */
	public function SetQuantityAvailable($quantity) {
		$this->quantityAvailable = $quantity;
	}
	/// @brief Sets the interval required between re-use.
	/** @param $interval The interval in seconds, between re-use of this agent.
	 */
	public function SetReuseInterval($interval) {
		$this->reuseInterval = $interval;
	}
	/// @brief Adds the reserved variables to this agent
    /**
     * These variables have no meaning to Creatures 2 and don't appear in Creatures 1.
     * They're all integers.
	 * @param $reserved1 The first reserved variable
	 * @param $reserved2 The second reserved variable
	 * @param $reserved3 The third reserved variable
	 */
	public function SetReserved($reserved1,$reserved2,$reserved3) {
		$this->reserved1 = $reserved1;
		$this->reserved2 = $reserved2;
		$this->reserved3 = $reserved3;
	}
	/// @brief Add the thumbnail to this agent.
	/** @param $frame The thumbnail as a SpriteFrame 
	 */
	public function SetThumbnail(SpriteFrame $frame) {
		if($this->thumbnail != null) {
			throw new Exception('Thumbnail already added');
		}
		$this->thumbnail = $frame;
	}
    /// @cond INTERNAL_DOCS
    /**
	 * @brief Adds a remover script by reading from an RCB file.
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

	/// @brief Creates a new COBAgentBlock from an IReader.
	/** Reads from the current position of the IReader to fill out the data required by
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
	/// @brief Creates a COBAgentBlock from an IReader
	/** Reads from the current position of the IReader to fill out the data required by
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
    /// @endcond
}

/// @brief defines a dependency which is used in a COB file */
class COBDependency {
    /// @cond INTERNAL_DOCS
    
	private $type;
    private $name;

    /// @endcond
	
	/// @brief Creates a new COBDependency
	/** @param $type The type of dependency ('sprite' or 'sound').
	 * @param $name The name of the dependency (four characters, no file extension)
	 */
	public function COBDependency($type,$name) {
		$this->type = $type;
		$this->name = $name;
	}
	/// @brief Gets the dependency type
    /** @return string
     */
	public function GetDependencyType() {
		return $this->type;
	}
	/// @brief Gets the name of the dependency
	/** @return string
	 */
	public function GetDependencyName() {
		return $this->name;
	}
}
?>
