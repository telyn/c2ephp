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
	public function Compile() {
		$compiled = $this->EncodeBlockHeader();
		$ints = array();
		$strings = array();
		foreach($this->tags as $key=>$value) {
			if(is_int($value)) {
				$ints[$key] = $value;
			} else {
				$strings[$key] = $value;
			}
		}
		$compiled .= pack('V',sizeof($ints));
		foreach($ints as $key=>$value) {
			$compiled .= pack('V',strlen($key));
			$compiled .= $key;
			$compiled .= pack('V',$value);
		}
		$compiled .= pack('V',sizeof($strings));
		foreach($strings as $key=>$value) {
			$compiled .= pack('V',strlen($key));
			$compiled .= $key;
			$compiled .= pack('V',strlen($value));
			$compiled .= $value;
		}
	}
	public function TagBlock($prayfile,$name,$content,$flags,$type) {
		parent::PrayBlock($prayfile,$name,$content,$flags,$type);

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
