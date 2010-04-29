<?php

require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/PRAY/PrayBlock.php');

class PRAYFile {
    private $reader;
    private $blocks=array();
    private $parsed = false;
    
    function PRAYFile($reader=null) {
		$this->blocks = array();
		if($reader instanceof IReader) {
        	$this->reader = $reader;
			$this->Parse();
		} else {
			$this->parsed = true; //make sure no one tries to parse it...
		}
    }
    
    
    private function Parse() {
		if(!$this->parsed) {
			if($this->ParseHeader()) {
				while($this->ParseBlockHeader()) {
				}
				$this->parsed = true;
				//print_r($this->blocks);
				return $this->blocks;
			} else {
				echo "Failed at block header: NOT A PRAY FILE";
				return FALSE;
			}
		}
		
		return $this->blocks;
    }
    
	public function Compile() {
		$compiled = 'PRAY';
		foreach($this->blocks as $block) {
			$compiled .= $block->Compile();
		}
		return $compiled;
	}
	public function AddBlock(PrayBlock $block) {
		$this->blocks[] = $block;
	}
    public function GetBlocks($type=FALSE) { //gets all blocks or one type of block
        if(!$type) {
            return $this->blocks;
        } else {
			if(is_string($type)) {
				$type = array($type);
			}
            $retblocks = array();
            foreach($this->blocks as $block) {
                if(in_array($block->GetType(),$type)) {
                    $retblocks[] = $block;
                }
            }
            return $retblocks;
        }
    }
    public function GetBlockByName($name) {
		foreach($this->blocks as $blockid => $block) {
			
			if($block->GetName() == $name) {
				return $block;
			}
		}
		return null;
	}

    private function ParseHeader() {
        if($this->reader->Read(4) == "PRAY") {
            return true;
        } else {
            return false;
        }
    }

    private function ParseBlockHeader() {
		$blocktype = $this->reader->Read(4);
        if($blocktype=="") {
			return false;
        }
        $name = trim($this->reader->Read(128));
        if($name=="") {
            return false;
        }
        $length = $this->reader->ReadInt(4);
        if($length===false) {
            return false;
        }
        $fulllength = $this->reader->ReadInt(4); //full means uncompressed
        if($fulllength===false) {
            return false;
        }
        $flags = $this->reader->ReadInt(4);
        if($flags===false) {
            return false;
        }
        $compression = false;
        if($flags & 1 == 1) {
		    $compression=true;
        }
		//if we make the content here, we don't have to write the same line or two ten or more times :)
		$content = $this->reader->Read($length);
		$this->blocks[] = PrayBlock::MakePrayBlock($blocktype,$this,$name,$content,$flags);
		
        return true;
    }
}

?>
