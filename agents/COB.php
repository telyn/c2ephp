<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/COB/AgentBlock.php');
require_once(dirname(__FILE__).'/COB/FileBlock.php');
require_once(dirname(__FILE__).'/COB/AuthorBlock.php');
require_once(dirname(__FILE__).'/COB/UnknownBlock.php');

///@{
/**
 * @name C1 format cob
 * Value: C1
 */
define('COB_FORMAT_C1', 'C1');
/**
 * @name C2 format COB
 * Value: C2
 */
define('COB_FORMAT_C2', 'C2');
///@}

/// @brief Class that interacts with COB files (c1 and c2 formats)
class COB {

    /// @cond INTERNAL_DOCS

    private $format;
    private $blocks;

    /// @endcond

    /// @brief Creates a new COB file
    /**
     * If you want to create a COB file from scratch, simply don't
     * pass anything to the constructor. \n\n
     * Alternatively, if you know which kind of COB file you are
     * reading, or only want to deal with a specific kind of COB
     * file, you can call the LoadC1COB and LoadC2COB functions
     * after creating a blank cob file. E.g. ($reader is a IReader) \n\n
     * $cob = new COB; \n
     * $cob->LoadC1COB($reader); \n
     * This code will only parse C1 cobs.
     * @param $reader The reader which contains the cob to read from. Can be null.
     */
    public function COB(IReader $reader = null) {
        if ($reader != null) {
            $this->LoadCOB($reader);
        }
    }

    /// @cond INTERNAL_DOCS

    /// @brief Loads the COB from the IReader.
    /**
     * Used internally, this function is not for the general public! \n
     * This function first identifies which type of COB is in the IReader
     * Then decompresses if necessary, then calls LoadC1COB or LoadC2COB.
     * @param $reader The reader to read from
     */
    private function LoadCOB(IReader $reader) {
        if ($reader->Read(4) == 'cob2') {
            $reader->Seek(0);
            $this->LoadC2COB($reader);
        } else {
            $string = $reader->GetSubString(0);
            $data = @gzuncompress($string);
            if ($data === false) {
                $reader->Seek(0);
                $this->LoadC1COB($reader);
            } else {
                $this->LoadC2COB(new StringReader($data));
            }
        }
    }

    /// @endcond

    /// @brief Loads a C2 COB from the IReader given
    /**
     * C2 COBs have multiple blocks, which are accessible via the
     * COB::GetBlocks function.
     * @param $reader The IReader to load from
     */
    public function LoadC2COB(IReader $reader) {
        $this->format = COB_FORMAT_C2;
        if ($reader->Read(4) == 'cob2') {
            while ($block = $this->ReadBlock($reader)) {
                $this->blocks[] = $block;
            }
        } else {
            throw new Exception('Not a valid C2 COB file!');
        }
    }

    /// @brief Loads a C1 COB from the specified reader
    /** 
     * C1 COBs have just one block, which is a COBAgentBlock.
     * This is accessible by calling COB::GetBlocks
     * @param $reader the reader to load from
     */
    public function LoadC1COB(IReader $reader) {
        $this->format = COB_FORMAT_C1;
        $version = $reader->ReadInt(2);
        if ($version > 4) {
            throw new Exception('Invalid cob file.');
        } else {
            $this->blocks[] = COBAgentBlock::CreateFromReaderC1($reader);
        }
    }
    /// @brief Adds a COBBlock to this COB
    /**
     * @param $block the block to add.
     */
    public function AddBlock(COBBlock $block) {
        //TODO: Check that this block works for this COB type?
        $this->blocks[] = $block;
    }

    /// @cond INTERNAL_DOCS

    /// @brief Underlying block reader used by C2 COBs
    /**
     * Reads a block from the specified reader, then instanitates
     * a representative COBBlock, and returns it.
     */
    private function ReadBlock(IReader $reader) {
        if (!($type = $reader->Read(4))) {
            return false;
        }
        $size = $reader->ReadInt(4);
        switch ($type) {
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
            return new COBUnknownBlock($type, $reader->Read($size));
            break;
        }
    }

    /// @endcond
    
    /// @brief Accessor method to get blocks of the given type
    /**
     * If $type is false, will return all blocks in this agent. \n
     * In a C1 COB, there is only one block and it is of the agnt
     * type.
     * @param $type The type of block to get (agnt, auth, file). False by default.
     * @return An array of COBBlocks.
     */
    public function GetBlocks($type = false) {
        $blocks = array();
        foreach ($this->blocks as $block) {
            if ($type === false || $type == $block->GetType()) {
                $blocks[] = $block;
            }
        }
        return $blocks;
    }

    /// @brief Compiles the COB in the given format
    /**
     *  @param $format The format of the COB. If null, assumed it's a creatures 2 COB
     *  @return A binary string containing the COB.
     */
    public function Compile($format = null) {
        if ($format == null) {
            $format = $this->GetType();
        }
        if ($format != FORMAT_C1) {
            $format = FORMAT_C2;
        }
        switch ($format) {
        case FORMAT_C1:
            $this->CompileC1();
        case FORMAT_C2:
            $this->CompileC2();
        default:
            throw new Exception('Non-understood COB format - sorry!');
        }
    }

    /// @brief Compiles to a C1 COB. <b>Unimplemented</b>
    // TODO: implement this.
    public function CompileC1() {
        throw new Exception('C1 COB Compilation not yet ready.');
    }
    /// @brief Compiles a C2 COB. <b>May not actually work.</b>
    // TODO: Check accuracy
    public function CompileC2() {
        $data = 'cob2'; 
        foreach ($this->blocks as $block) {
            $data .= $block->Compile();
        }    
    }
}
?>
