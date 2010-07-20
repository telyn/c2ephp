<?php

require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/PRAY/PrayBlock.php');
/** \brief Class representing a file that uses the PRAY format
  * .creature, .family and .agents files all use this format.
  */
class PRAYFile {
    private $reader;
    private $blocks=array();
    private $parsed = false;
    /** \brief Creates a new PRAYFile
	  * \param $reader The IReader to read from. If null, means this is a user-generated PRAYFile.
	  */
    function PRAYFile($reader=null) {
		$this->blocks = array();
		if($reader instanceof IReader) {
        	$this->reader = $reader;
			$this->Parse();
		} else {
			$this->parsed = true; //make sure no one tries to parse it...
		}
    }
    
    /** \brief Reads the PRAYFile stored in the reader. Called automatically. */
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
    /** \brief Compiles the PRAYFile.
	  * \return A binary string containing the PRAYFile's contents.
	  */
	public function Compile() {
		$compiled = 'PRAY';
		foreach($this->blocks as $block) {
			$compiled .= $block->Compile();
		}
		return $compiled;
	}
	/** \brief Adds a block to this PRAYFile.
	  * \param $block The PrayBlock to add
	  */
	public function AddBlock(PrayBlock $block) {
		//TODO: Check block name is unique (in blocks of the same type).
		$this->blocks[] = $block;
	}
	/** \brief Gets the blocks of the specified type(s)
	  * If $type is a string, returns all blocks of that type.
	  * If $type is an array, returns all blocks the types in the array.
	  * \param $type The type(s) of blocks to return, as the PRAYBLOCK_TYPE_* constants.
	  */
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
	/** \brief Gets a block with the specified name */
    public function GetBlockByName($name) {
		foreach($this->blocks as $blockid => $block) {
			
			if($block->GetName() == $name) {
				return $block;
			}
		}
		return null;
	}
	/** \brief Checks that this PRAYFile begins with PRAY */
    private function ParseHeader() {
        if($this->reader->Read(4) == "PRAY") {
            return true;
        } else {
            return false;
        }
    }
	/** \brief Reads a block from the reader
	  * Reads a block, then creates it using the PrayBlock::MakePrayBlock method.
	  * Returns true if the block was created successfully, false otherwise.
	  */
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
		//if we make the content here, we don't have to write the same line or two ten or more times :)
		$content = $this->reader->Read($length);
		$this->blocks[] = PrayBlock::MakePrayBlock($blocktype,$this,$name,$content,$flags);
		
        return true;
    }
}

?>
