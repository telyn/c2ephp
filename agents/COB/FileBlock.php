<?php
require_once(dirname(__FILE__).'/COBBlock.php');

/// @brief Defines the bock to represent a file block in a COB file.
class COBFileBlock extends COBBlock {

    /// @cond INTERNAL_DOCS
    //
	private $fileType;
	private $fileName;
	
	private $reserved;
    private $contents;

    /// @endcond

	/// @brief Constructs a new COBFileBlock
    /**
     * @param string $type The file type
	 * @param $name The file name (including extension)
	 * @param $contents The contents of the file
	 */
	public function COBFileBlock($type, $name, $contents) {
		parent::COBBlock(COB_BLOCK_FILE);
		$this->fileType = $type;
		$this->fileName = $name;
		$this->contents = $contents;
    }

	/// @brief Add the reserved data associated with this file block
    /**
     * @param $reserved The reserved data
	 */
	public function AddReserved($reserved) {
		$this->reserved = $reserved;
    }

    /// @brief Get the name of the file
	public function GetName() {
		return $this->fileName;
    }

    /// @brief Get the file's type
    /**
     * @return 'sprite' or 'sound' - i.e. one of the
     * COB_DEPENDENCY_* constants in COBAgentBlock
     */
    public function GetFileType() {
        return $this->fileType;
    }

    /// @brief Get the contents of the file.
    public function GetContents() {
        return $this->contents;
    }

    /// @brief Get the reserved data
    /**
     * Reserved data was never officially used.
     * @return A 4-byte integer.
     */
    public function GetReserved() {
        return $this->reserved;
    }

    /// @cond INTERNAL_DOCS

    /// @brief Creates a new COBFileBlock from an IReader
    /**
     * @param $reader The reader the data's coming from
     */
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
    /// @endcond
}
?>
