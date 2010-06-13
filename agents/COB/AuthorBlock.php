<?php
require_once(dirname(__FILE__).'/COBBlock.php');

class COBAuthorBlock extends COBBlock {
	private $creationTime;
	private $version;
	private $revision;
	private $authorName;
	private $authorEmail;
	private $authorURL;
	private $authorComments;
	
	public function COBAuthorBlock($authorName,$authorEmail,$authorURL,$authorComments,$creationTime,$version,$revision) {
		parent::COBBlock(COB_BLOCK_AUTHOR);
		$this->authorName = $authorName;
		$this->authorEmail = $authorEmail;
		$this->authorURL = $authorURL;
		$this->authorComments = $authorComments;
		$this->creationTime = $creationTime;
		$this->version = $version;
		$this->revision = $revision;
	}
	
	public function GetAuthorName() {
		return $this->authorName;
	}
	public function GetAuthorEmail() {
		return $this->authorEmail;
	}
	public function GetAuthorURL() {
		return $this->authorURL;
	}
	public function GetAuthorComments() {
		return $this->authorComments;
	}
	public function GetCreationTime() {
		return $this->creationTime;
	}
	public function GetVersion() {
		return $this->version;
	}
	public function GetRevision() {
		return $this->revision;
	}
	
	public static function CreateFromReader(IReader $reader) {
		$creationDay = $reader->ReadInt(1);
		$creationMonth = $reader->ReadInt(1);
		$creationYear = $reader->ReadInt(2);
		$timestamp = mktime(0,0,0,$creationMonth,$creationDay,$creationYear);
		$version = $reader->ReadInt(1);
		$revision = $reader->ReadInt(1);
		$authorName = $reader->ReadCString();
		$authorEmail = $reader->ReadCString();
		$authorURL = $reader->ReadCString();
		$authorComments = $reader->ReadCString();
		
		return $readerData;
	}
}
?>