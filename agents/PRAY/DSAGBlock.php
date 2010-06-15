<?php
require_once(dirname(__FILE__).'/AGNTBlock.php');
require_once(dirname(__FILE__).'/TagBlock.php');
class DSAGBlock extends AGNTBlock {
	public function DSAGBlock($prayfile,$name,$content,$flags) {
		parent::TagBlock($prayfile,$name,$content,$flags,PRAY_BLOCK_DSAG);
	}
	public function GetWebLabel() {
		return $this->GetTag('Web Label');
	}
	public function GetWebURL() {
		return $this->GetTag('Web URL');
	}
	/*
	 * "Web Icon" ""  
	"Web Icon Base" "" 
	"Web Icon Animation String" ""
	 */
	public function GetWebIcon() {
		return $this->GetTag('Web Icon');
	}
	public function GetWebIconBase() {
		return $this->GetTag('Web Icon Base');
	}
	public function GetWebIconAnimationString() {
		return $this->GetTag('Web Icon Animation String');
	}
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
