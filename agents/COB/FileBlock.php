<?php
require_once(dirname(__FILE__).'/COBBlock.php');

class COBFileBlock extends COBBlock {
	private $fileType;
	private $fileName;
	
	private $reserved;
	private $contents;
	
	public function COBFileBlock($type,$name,$contents) {
		parent::COBBlock(COB_BLOCK_FILE);
		$this->fileType = $type;
		$this->fileName = $name;
		$this->contents = $contents;
	}
	public function AddReserved($reserved) {
		$this->reserved = $reserved;
	}
	public function GetName() {
		return $this->fileName;
	}
	public function GetType() {
		return $this->fileType;
	}
	public function GetContents() {
		return $this->contents;
	}
	public function GetReserved() {
		return $this->reserved;
	}
	public static function CreateFromReader(IReader $reader) {
		$type = ($reader->ReadInt(2)==0)?'sprite':'sound';
		$reserved = $reader->ReadInt(4);
		$size = $reader->ReadInt(4);
		$fileName = $reader->ReadCString();
		$contents = $reader->Read($size);
		$block = new COBFileBlock($type,$fileName,$contents);
		$block->AddReserved($reserved);
		return $block;
	}
}
?>