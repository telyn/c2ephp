<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/SpriteFile.php');
require_once(dirname(__FILE__).'/C16Frame.php');

/** \brief Class representing a C16 sprite file. 
  * This documentation was written in the future, when the code had already been written
  * In other words, what is true according to what's in the docs is not what's actually true RIGHT NOW
  * I will remove this notice when I finish coding this class.
  */
class C16File extends SpriteFile
{
	private $encoding;
	/** \brief Creates a new C16File object.
	 * If $reader is null, creates an empty C16File ready to add sprites to.
	 * \param $reader The reader to read the sprites from. Can be null.
	 */
	public function C16File(IReader $reader=null)
	{
    if($reader != null) {
      parent::SpriteFile('C16');
  		$buffer = $reader->ReadInt(4);
  		if(($buffer & 1) == 1) {
  			$this->encoding = '565';
  		} else {
  			$this->encoding = '555';
  	  }
  			
    	if(($buffer & 2) == 0) { //buffer & 2 == 2 => RLE. buffer & 2 == 0 => non-RLE (same as s16 but not supported here because it's complex dude.
        throw new Exception('This file is probably a S16 masquerading as a C16!');
    	} else if($buffer > 3) {
    	  throw new Exception('File encoding not recognised. ('.$buffer.')');
      }
  			
  		$buffer = $reader->ReadInt(2);
  		if($buffer < 1)
  			throw new Exception('Sprite file appears to contain less than 1 frame.');
  		$frameCount = $buffer;
  		for($x=0; $x < $frameCount; $x++)
  		{
  			$this->AddFrame(new C16Frame($reader,$this->encoding));
  		}
  	}
	}
  /** \brief Sets the encoding for this file 
   * \param $encoding '565' or '555', anything else will be treated as '555'
   */
  public function SetEncoding($encoding) {
    $this->EnsureDecompiled();
    $this->encoding = $encoding;
  }
  /** \brief Compiles the file's data into a C16 binary string
   * \return A binary string containing the C16File's data.
   */
	public function Compile() {
    $data = ''; 
    $flags = 2; // 0b00 => 555 S16, 0b01 => 565 S16, 0b10 => 555 C16, 0b11 => 565 C16
    if($this->encoding == '565') {
      $flags = $flags | 1;
    }
    $data .= pack('V',$flags);
    $data .= pack('v',$this->GetFrameCount());
    $idata = '';
    $offset = 6+(8*$this->GetFrameCount());
    foreach($this->GetFrames() as $frame) {
      $offset += ($frame->GetHeight()-1)*4;
    }
    
    foreach($this->GetFrames() as $frame) {
      $data .= pack('V',$offset);
      $data .= pack('vv',$frame->GetWidth(),$frame->GetHeight());
      
      $framedata = $frame->Encode();
      $framebin = $framedata['data'];
      foreach($framedata['lineoffsets'] as $lineoffset) {
        $data .= pack('V',$lineoffset+$offset);
      }
      $offset += strlen($framebin); 
      $idata .= $framebin;
    }
    return $data . $idata;
  }
	
}
?>
