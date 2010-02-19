<?php
require_once(dirname(__FILE__).'/PrayBlock.php');
require_once(dirname(__FILE__).'/../../support/StringReader.php');
abstract class TagBlock extends PrayBlock {
	private $tags;
	public function GetTag($key) {
		foreach($this->tags as $tk => $tv) {
			if($key == $tk) {
				return $tv;
			}
		}
	}
	public function GetTags() {
		return $this->tags;
	}
	public function TagBlock(&$prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags);

        $blockReader = new StringReader($content);
        
        $numInts = $blockReader->ReadInt(4);
        for($i=0;$i<$numInts;$i++) {
            $nameLength = $blockReader->ReadInt(4);
            $name = $blockReader->Read($nameLength);
            $int = $blockReader->ReadInt(4);
            $this->tags[$name] = $int;
        }
        
        
        $numStrings = $blockReader->ReadInt(4);
        for($i=0;$i<$numStrings;$i++) {
            $nameLength = $blockReader->ReadInt(4);
            $name = $blockReader->Read($nameLength);
            $stringLength = $blockReader->ReadInt(4);
            $string = $blockReader->Read($stringLength);
            $this->tags[$name] = $string;
        }
    }
}
?>
