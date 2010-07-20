<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/COB/AgentBlock.php');
require_once(dirname(__FILE__).'/COB/FileBlock.php');
require_once(dirname(__FILE__).'/COB/AuthorBlock.php');
require_once(dirname(__FILE__).'/COB/UnknownBlock.php');

///@{
/** \brief C1 format cob
  * C1
  */
define('COB_FORMAT_C1','C1');
/** \brief C2 format COB
  * C2
  */
define('COB_FORMAT_C2','C2');
///@}

/** \brief Class that interacts with COB files (c1 and c2 formats) */
class COB {
	private $format;
	private $blocks;
	
	/** \brief Creates a new COB file
    * If you want to create a COB file from scratch, simply don't
    * pass anything to the constructor.
    * Alternatively, if you know which kind of COB file you are
    * reading, or only want to deal with a specific kind of COB
    * file, you can call the LoadC1COB and LoadC2COB functions
    * after creating a blank cob file. E.g. ($reader is a IReader)
    * $cob = new COB;
    * $cob->LoadC1COB($reader);
    * This code will only parse C1 cobs.
	  * \param $reader The reader which contains the cob to read from. Can be null.
	  */
	public function COB(IReader $reader=null) {
		if($reader != null) {
			$this->LoadCOB($reader);
		}
	}
	/** \brief Loads the COB from the IReader.
	  * Used internally, this function is not for the general public!
	  * This function first identifies which type of COB is in the IReader
	  * Then decompresses if necessary, then calls LoadC1COB or LoadC2COB.
	  * \param $reader The reader to read from
	  */
	private function LoadCOB(IReader $reader) {
		if($reader->Read(4) == 'cob2') {
			$reader->Seek(0);
			$this->LoadC2COB($reader);
		} else {
			$string = $reader->GetSubString(0);
			$data = @gzuncompress($string);
			if($data===false) {
				$reader->Seek(0);
				$this->LoadC1COB($reader);
			} else {
				$this->LoadC2COB(new StringReader($data));
			}
		}
	}
	/** \brief Loads a C2 COB from the IReader given
	  * C2 COBs have multiple blocks, which are accessible via the
	  * COB::GetBlocks function.
	  * \param $reader The IReader to load from
	  */
	public function LoadC2COB(IReader $reader) {
		$this->format = COB_FORMAT_C2;
		if($reader->Read(4) == 'cob2') {
			while($block = $this->ReadBlock($reader)) {
				$this->blocks[] = $block;
			}
		} else {
			throw new Exception('Not a valid C2 COB file!');
		}
	}
	/** \brief Loads a C1 COB from the specified reader
	  * C1 COBs have just one block, which is a COBAgentBlock.
	  * This is accessible by calling COB::GetBlocks
	  * \param $reader the reader to load from
	  */
	public function LoadC1COB(IReader $reader) {
		$this->format = COB_FORMAT_C1;
		$version = $reader->ReadInt(2);
		if($version > 4) {
			throw new Exception('Invalid cob file.');
		} else {
			$this->blocks[] = COBAgentBlock::CreateFromReaderC1($reader);
		}
	}
	/** \brief Adds a COBBlock to this COB
	  * \param $block the block to add.
	  */
	public function AddBlock(COBBlock $block) {
		//TODO: Check that this block works for this COB type?
		$this->blocks[] = $block;
	}
	/** \brief Underlying block reader used by C2 COBs
	  * Reads a block from the specified reader, then instanitates
	  * a representative COBBlock, and returns it.
	  */
	private function ReadBlock($reader) {
		if(!($type = $reader->Read(4))) {
			print($type);
			return false;
		}
		$size = $reader->ReadInt(4);
		switch($type) {
			case 'agnt':
				//we read the entire thing so that if there are errors we can still go on with the other blocks.
				return COBAgentBlock::CreateFromReaderC2($reader);
				break;
			case 'auth':
				return COBAuthorBlock::CreateFromReader(new StringReader($reader->Read($size)));
				break;
			case 'file':
				return COBFileBlock::CreateFromReader(new StringReader($reader->Read($size)));
				break;
			default:
				//throw new Exception('Invalid block type: Probably a bug or an invalid COB file: '.$type);
				//simply ignore unknown block types, in case people add their own
				return new COBUnknownBlock($type,$reader->Read($size));
				break;
		}
	}
	/** \brief Accessor method to get blocks of the given type
	  * If $type is false, will return all blocks in this agent.
	  * In a C1 COB, there is only one block and it is of the agnt
	  * type.
	  * \param $type The type of block to get (agnt, auth, file). False by default.
	  * \return An array of COBBlocks.
	  */
	public function GetBlocks($type=false) {
		$blocks = array();
		foreach($this->blocks as $block) {
			if($type === false || $type == $block->GetType()) {
				$blocks[] = $block;
			}
		}
		return $blocks;
	}
}
?>