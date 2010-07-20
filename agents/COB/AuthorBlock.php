<?php
require_once(dirname(__FILE__).'/COBBlock.php');

/// \brief Defines Author information about the COB
class COBAuthorBlock extends COBBlock {
	private $creationTime;
	private $version;
	private $revision;
	private $authorName;
	private $authorEmail;
	private $authorURL;
	private $authorComments;
	
	/** \brief Creates a new COBAuthorBlock
	 * \param $authorName the name of the author of this COB
	 * \param $authorEmail the author's email address
	 * \param $authorURL The author's website address
	 * \param $authorComments Any comments the author had about this COB
	 * \param $creationTime the time this COB was compiled
	 * \param $version The version number of this COB
	 * \param $revision the COB's revision number
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
	/// \brief Gets the name of the author
	public function GetAuthorName() {
		return $this->authorName;
	}
	/// \brief Gets the author's email address
	public function GetAuthorEmail() {
		return $this->authorEmail;
	}
	/// \brief Gets the author's web address
	public function GetAuthorURL() {
		return $this->authorURL;
	}
	/// \brief Gets comments from the author
	public function GetAuthorComments() {
		return $this->authorComments;
	}
	/// \brief Gets the time this COB was created
	public function GetCreationTime() {
		return $this->creationTime;
	}
	/// \brief Gets the COB's version number
	public function GetVersion() {
		return $this->version;
	}
	/// \brief Gets the COB's revision
	public function GetRevision() {
		return $this->revision;
	}
	/** Creates the COBAuthorBlock from an IReader
	 * \param $reader The IReader, currently at the position of the author block  
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