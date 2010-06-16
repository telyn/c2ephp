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
	/// \brief Gets the number of scripts stored in this block
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
	/// \brief Get the number of files this agent depends on.
	public function GetDependencyCount() {
		return $this->GetTag('Dependency Count');
	}
	/** \brief Gets the dependency specified
	 * \param $dependency The number of the dependency to get. 1-based.
	 * \return A PrayDependency object describing the file the agent depends on
	 */
	
	public function GetDependency($dependency) {
		$file = $this->GetTag('Dependency '.$dependency);
		$category = $this->GetTag('Dependency Category '.$dependency);
		return new PrayDependency($category,$file);
	}
	/** \brief Gets all the files this agent depends on.
	 * \return An array of PrayDependency objects.
	 */
	public function GetDependencies() {
		$dependencies = array();
		for($i = 1; $i <= $this->GetDependencyCount(); $i++) {
			$dependencies[] = $this->GetDependency($i);
		}
		return $dependencies;
	}
	/** \brief Gets the script used to remove this agent.
	 * If not specified, most likely a removal script is included in the agent's scripts, however,
	 * if this isn't specified the game won't know how to remove the agent
	 */
	
	public function GetRemoveScript() {
		return $this->GetTag('Remove Script');
	}
	/// \brief Gets the file used for the animation of the agent on the C3 Creator/DS injector
	public function GetAgentAnimationFile() {
		return $this->GetTag('Agent Animation File');
	}
	/** \brief Gets the filename (excluding extension) used for the animation of the agent on the C3 Creator/DS injector
	 * For all agent files I've seen, it's functionally identical to substr(AGNTBlock::GetAgentAnimationFile(),0,-4)
	 * I have no idea why anyone would use this.
	 */
	public function GetAgentAnimationGallery() {
		return $this->GetTag('Agent Animation Gallery');
	}
	/** \brief Gets the number of the first image of the animation displayed on the C3 Creator/DS injector
	 * This is used as the basis for the animation string. For example, an AGNT block with 'Animation Sprite First Image' = 4
	 * and 'Agent Animation String' = 0 0 3 4
	 * Would show the same image as one with 'Animation Sprite First Image' = 0 and 'Agent Animation String' = 4 4 7 8
	 */
	public function GetAgentAnimationFirstImage() {
		return $this->GetTag('Animation Sprite First Image');
	}
	/** \brief Gets the animation displayed on the C3 creator/DS injector
	 * It's simply a space-delimited set of numbers. 
	 */
	public function GetAgentAnimationString() {
		return $this->GetTag('Agent Animation String');
	}
	/** \brief Gets the image used on the creator
	 * Since I have no desire to bring GIF files back to the internet
	 * this function will ONLY support single-frame animations.
	 * If you really, REALLY, want to make a GIF, it's totally possible so do it yourself.
	 * You can use this function as a basis. After all, that's what FOSS software is for.
	 * 
	 * This function tries hard to get the animation file. TODO: Make it try really hard, and then really really hard.
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
