<?php
require_once(dirname(__FILE__).'/SPRFrame.php');
require_once(dirname(__FILE__).'/SpriteFile.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

/// @brief Class for files in C1's SPR format.
/**
 * Creating new SPR files is currently unsupported. \n
 * TODO: Allow creating SPR files.
*/ 
class SPRFile extends SpriteFile {

    /// @brief Instantiates a new SPRFile
    /**
     * Reads in the IReader and creates SPRFrames as required.
     * @param $reader An IReader to read from.
     */
    public function SPRFile(IReader $reader) {
        parent::SpriteFile('SPR');
        $frameCount = $reader->ReadInt(2);
		
        for($i=0;$i<$frameCount;$i++) {
            $offset = $reader->ReadInt(4);
            $width = $reader->ReadInt(2);
            $height = $reader->ReadInt(2);
            $this->AddFrame(new SPRFrame($reader,$width,$height,$offset));
        }
    }

    /// @brief Compiles the SPR file into a binary string
    public function Compile() {
        $data = pack('v',$this->GetFrameCount());
        $offset = 2+(8*$this->GetFrameCount());
        foreach($this->GetFrames() as $frame) {
        $data .= pack('V',$offset);
        $data .= pack('vv',$frame->GetWidth(),$frame->GetHeight());
        $offset += $frame->GetWidth()*$frame->GetHeight();
        }
        foreach($this->GetFrames() as $frame) {
        $data .= $frame->Encode();
        }
        return $data;
    }
}
?>
