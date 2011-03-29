<?php
require_once(dirname(__FILE__).'/AGNTBlock.php');
require_once(dirname(__FILE__).'/TagBlock.php');

/** Docking Station agent description block
 * Defines everything about an agent for docking station
 */
class DSAGBlock extends AGNTBlock {
	/** Instantiates a new DSAGBlock
	 * \param $prayfile The PRAYFile this DSAGBlock is being read from. Can be null.
	 * \param $name This block's name.
	 * \param $content The binary contents of the block
	 * \param $flags The flags this block 
	 */
	public function DSAGBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DSAG);
	}
	/// Gets the label used on the web button.
	public function GetWebLabel() {
		return $this->GetTag('Web Label');
	}
	/// Gets the URL of the web site
	public function GetWebURL() {
		return $this->GetTag('Web URL');
	}
	/*
	 * "Web Icon" ""  
	"Web Icon Base" "" 
	"Web Icon Animation String" ""
	 */
	/// Gets the file used for the web button's icon
	public function GetWebIcon() {
		return $this->GetTag('Web Icon');
	}
	/// Gets the number of the sprite to use as the base for the web icon.
	public function GetWebIconBase() {
		return $this->GetTag('Web Icon Base');
	}
	/** Gets the animation string for the web icon.
	 * In theory, the web button would animate. In reality, DS actually only uses the first image.
	 */
	public function GetWebIconAnimationString() {
		return $this->GetTag('Web Icon Animation String');
	}
	/// Gets the web button image as an ISpriteFrame
	public function GetWebIconAsSpriteFrame() {
		$webIcon = $this->GetWebIcon();
		if($webIcon == '') {
			throw new Exception('No web icon!');
		}
		$webIconBase = $this->GetWebIconBase();
		$webIconAnimationString = $this->GetWebIconAnimationString();
		if($webIconBase == '') {
			$webIconBase = 0;
		}
		if($webIconAnimationString == '') {
			$webIconAnimationString = 0;
		}
		if(($position = strpos($webIconAnimationString,' ')) !== false) {
			$webIconAnimationString = substr($webIconAnimationString,0,$position);
		}
		$prayfile = $this->GetPrayFile();
		if($prayfile == null) {
			throw new Exception('No PRAY file to get the icon from!');
		}
		$iconBlock = $prayfile->GetBlockByName($webIcon);
		if($iconBlock->GetType() != 'FILE') {
			throw new Exception('The block with the web icon\'s filename is not a file block!');
		}
		$type = strtolower(substr($webIcon,-3));
		$icon = null;
		if($type == 'c16') {
			$icon = new C16File(new StringReader($iconBlock->GetData()));
		} else if($type == 's16') {
			$icon = new S16File(new StringReader($iconBlock->GetData()));
		}
		if($icon == null) {
			throw new Exception('For one reason or another, couldn\'t make a sprite file for the web icon.');
		}
		return $icon->GetFrame($webIconBase+$webIconAnimationString);
		
	}
	
}
?>
