<?php
require_once(dirname(__FILE__).'/COBBlock.php');

/** Defines Author information about the COB */
class COBAuthorBlock extends COBBlock {
	private $creationTime;
	private $version;
	private $revision;
	private $authorName;
	private $authorEmail;
	private $authorURL;
	private $authorComments;
	
	/** Creates a new COBAuthorBlock
	 * @param string $authorName the name of the author of this COB
	 * @param string $authorEmail the author's email address
	 * @param string $authorURL The author's website address
	 * @param string $authorComments Any comments the author had about this COB
	 * @param int $creationTime the time this COB was compiled as a UNIX timestamp
	 * @param int $version The version number of this COB
	 * @param int $revision the COB's revision number
	 */
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
	/** Gets the name of the author
     * @return string */
	public function GetAuthorName() {
		return $this->authorName;
	}
	/** Gets the author's email address
     * @return string */
	public function GetAuthorEmail() {
		return $this->authorEmail;
	}
	/** Gets the author's web address
	 * @return string */
	public function GetAuthorURL() {
		return $this->authorURL;
	}
	/** Gets comments from the author
	 * @return string */
	public function GetAuthorComments() {
		return $this->authorComments;
	}
	/* Gets the time this COB was created
	 * @return int UNIX timestamp */
	public function GetCreationTime() {
		return $this->creationTime;
	}
	/** Gets the COB's version number
	 * @return int */
	public function GetVersion() {
		return $this->version;
	}
	/** Gets the COB's revision number
	 * @return int */
	public function GetRevision() {
		return $this->revision;
	}
	/** @ignore
	 * Creates the COBAuthorBlock from an IReader.
     * Ordinary programmers will not need to deal with this
	 * @param $reader The IReader, currently at the position of the author block  
	 * @return COBAuthorBlock
	 */
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
