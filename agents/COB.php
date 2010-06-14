<?php

require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/COB/AgentBlock.php');
require_once(dirname(__FILE__).'/COB/FileBlock.php');
require_once(dirname(__FILE__).'/COB/AuthorBlock.php');
require_once(dirname(__FILE__).'/COB/UnknownBlock.php');

define('COB_FORMAT_C1','C1');
define('COB_FORMAT_C2','C2');

class COB {
	private $reader;
	private $format;
	private $blocks;
	
	public function COB(IReader $reader=null) {
		if($reader != null) {
			$this->LoadCOB($reader);
		}
	}
	public function LoadCOB(IReader $reader) {
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
	
	public function LoadC1COB(IReader $reader) {
		$this->format = COB_FORMAT_C1;
		$version = $reader->ReadInt(2);
		if($version > 4) {
			throw new Exception('Invalid cob file.');
		} else {
			$this->blocks[] = COBAgentBlock::CreateFromReaderC1($reader);
		}
	}
	
	public function AddBlock(COBBlock $block) {
		$this->blocks[] = $block;
	}
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
