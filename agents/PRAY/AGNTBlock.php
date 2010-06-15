<?php
require_once(dirname(__FILE__).'/../../sprites/C16File.php');
require_once(dirname(__FILE__).'/../../sprites/S16File.php');
require_once(dirname(__FILE__).'/TagBlock.php');
class AGNTBlock extends TagBlock {
	public function AGNTBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_AGNT);
	}
	/// \brief Gets the agent's name.
	public function GetAgentName() {
		return $this->GetName();
	}
	/** \brief Gets the agent's description in the specified language
	 * If the description doesn't exist in that language, falls back on English.
	 * \param $localisation The two-letter language code (e.g. de, fr) to get. If not specified, english is used.
	 */ 
	public function GetAgentDescription($localisation='') {
		if($localisation == '') {
			return $this->GetTag('Agent Description');
		} else {
			$description = $this->GetTag('Agent Description-'.$localisation);
			if($description == '') {
				$description = $this->GetTag('Agent Description'); 
			}
			return $description;
		}
	}
	/** \brief Gets the agent's type
	 * I honestly have no idea what this is. For some reason always zero. O_o
	 */ 
	public function GetAgentType() {
		return $this->GetTag('Agent Type');
	}
	public function GetScriptCount() {
		return $this->GetTag('Script Count');
	}
	/** \brief Gets the specified script 
	 * Gets the first script, or if you specified which script, that one.
	 * \param $script Which script to get as an integer. The first script is script 1.
	 */
	public function GetScript($script=1) {
		if($script > 0 && $script <= $this->GetScriptCount()) {
			return $this->GetTag('Script '.$script);			
		}
		throw new Exception('Script doesn\'t exist!');
		
	}
	public function GetDependencyCount() {
		return $this->GetTag('Dependency Count');
	}
	public function GetDependency($dependency) {
		$file = $this->GetTag('Dependency '.$dependency);
		$category = $this->GetTag('Dependency Category '.$dependency);
		return new PrayDependency($category,$file);
	
	}
	public function GetDependencies() {
		$dependencies = array();
		for($i = 1; $i <= $this->GetDependencyCount(); $i++) {
			$dependencies[] = $this->GetDependency($i);
		}
		return $dependencies;
	}
	public function GetRemoveScript() {
		return $this->GetTag('Remove Script');
	}
	public function GetAgentAnimationFile() {
		return $this->GetTag('Agent Animation File');
	}
	public function GetAgentAnimationGallery() {
		return $this->GetTag('Agent Animation Gallery');
	}
	public function GetAgentAnimationFirstImage() {
		return $this->GetTag('Animation Sprite First Image');
	}
	public function GetAgentAnimationString() {
		return $this->GetTag('Agent Animation String');
	}
	/** \brief Gets the image used on the creator
	 * Since I have no desire to bring GIF files back to the internet
	 * this function will ONLY support single-frame animations.
	 * If you really, REALLY, want to make a GIF, it's totally possible so do it yourself.
	 * You can use this function as a basis. After all, that's what FOSS software is for.
	 * 
	 * This function tries hard to get the animation file.
	 */
	public function GetAgentAnimationAsSpriteFrame() {
		$animationFile = $this->GetAgentAnimationFile();
		if($animationFile == '') {
			$animationFile = $this->GetAgentAnimationGallery();
			if($animationFile == '') {
				throw new Exception('No animation file!');
			}
			$animationFile .= '.c16';
		}
		$animationFirstImage = $this->GetAgentAnimationFirstImage();
		$animationString = $this->GetAgentAnimationString();
		if($animationFirstImage == '') {
			$animationFirstImage = 0;
		}
		if($animationString == '') {
			$animationString = 0;
		}
		if(($position = strpos($animationString,' ')) !== false) {
			$animationString = substr($animationString,0,$position);
		}
		$prayfile = $this->GetPrayFile();
		if($prayfile == null) {
			throw new Exception('No PRAY file to get the icon from!');
		}
		$iconBlock = $prayfile->GetBlockByName($animationFile);
		if($iconBlock->GetType() != 'FILE') {
			throw new Exception('The block with the animation\'s filename is not a file block!');
		}
		$type = strtolower(substr($animationFile,-3));
		$icon = null;
		print $type;
		if($type == 'c16') {
			$icon = new C16File(new StringReader($iconBlock->GetData()));
		} else if($type == 's16') {
			$icon = new S16File(new StringReader($iconBlock->GetData()));
		}
		if($icon == null) {
			throw new Exception('For one reason or another, couldn\'t make a sprite file for the agent.');
		}
		return $icon->GetFrame($animationFirstImage+$animationString);
	}
}
?>
