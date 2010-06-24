<?php
require_once(dirname(__FILE__).'/PrayBlock.php');
require_once(dirname(__FILE__).'/../../support/StringReader.php');
/** \brief Base block class for working with tag-type blocks
  * This includes (but may not be limited to agents (AGNTBlock/DSAGBlock),
  * creatures (EXPCBlock/DSEXBlock) and starter families (SFAMBlock,DFAMBlock)
  * This contains the majority of the meat of the tag block functions, including
  * compilation and decompilation and maintaining a name->value tag array.
  */
abstract class TagBlock extends PrayBlock {
	private $tags;
	/** \brief Creates a new TagBlock
	  * This should be called by all subclasses from their constructors.
  	  * \param $prayfile The prayfile this block is contained in, or for TagBlocks being created from scratch, the initial tags array. Can be null.
	  * \param $name The name of the block. Cannot be null.
	  * \param $content The binary data this block contains. Can be null.
	  * \param $flags The flags relating to this block. Should be zero or real flags.
	  */
	public function TagBlock($prayfile,$name,$content,$flags,$type) {
		parent::PrayBlock($prayfile,$name,$content,$flags,$type);
		if($prayfile instanceof PRAYFile) {
		} else if(is_array($prayfile)) {
			$this->tags = $prayfile;
		}
    }
    /** \brief Gets the tag with the given name
	  * Returns the tag's value as a string, or
	  * nothing if the tag doesn't exist.
	  */
	public function GetTag($key) {
		$this->EnsureDecompiled();
		foreach($this->tags as $tk => $tv) {
			if($key == $tk) {
				return $tv;
			}
		}
	}
	/** \brief Gets all the tags from this block */
	public function GetTags() {
		$this->EnsureDecompiled();
		return $this->tags;
	}
	/** \brief Compiles the block. Called by PrayBlock. Not by you. Ever. */
	protected function CompileBlockData() {
		$compiled = '';
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
		return $compiled;
	}
	/** \brief Compiles the block. Called by EnsureDecompiled. */
    protected function DecompileBlockData() {
    //use GetData because it decompresses if necessary.
        $blockReader = new StringReader($this->GetData());
        
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
