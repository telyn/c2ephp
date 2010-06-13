<?php
require_once(dirname(__FILE__).'/COBBlock.php');
require_once(dirname(__FILE__).'/../../sprites/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../../sprites/S16Frame.php');
require_once(dirname(__FILE__).'/../../sprites/SPRFrame.php');

define('DEPENDENCY_SPRITE','sprite');
define('DEPENDENCY_SOUND','sound');

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
	public function GetDependencies($type=null) {
		$dependenciesToReturn = array();
		foreach($this->dependencies as $dependency) {
			if($type == null || $type == $dependency->GetType()) {
				$dependenciesToReturn[] = $dependency;
			}
		}
		return $dependenciesToReturn;
	}
	public function GetReserved1() {
		return $this->reserved1;
	}
	public function GetReserved2() {
		return $this->reserved2;
	}
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