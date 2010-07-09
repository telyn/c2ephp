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
	  * \param IReader $reader The reader to read the sprites from. Will be able to be null
	  */
	public function C16File(IReader $reader=null)
	{
    if($reader != null) {
      parent::SpriteFile('C16');
  		$buffer = $reader->ReadInt(4);
  		if($buffer == 3)
  			$this->encoding = '565';
  		else if($buffer == 2)
  			$this->encoding = '555';
  		else
  			throw new Exception('File encoding not recognised. ('.$buffer.')');
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
	public function Compile() {
	  $data = ''; //S16 and C16 are actually the same format....C16 just has RLE
	  $flags = 2; // 0b00 => 555 S16, 0b01 => 565 S16, 0b10 => 555 C16, 0b11 => 565 C16
	  if($this->encoding == '565') {
	    $flags = $flags | 1;
	  }
	  $data .= pack('V',$flags);
	  $data .= pack('v',$this->GetFrameCount());
	  foreach($this->GetFrames() as $frame) {
	    $data .= $frame->Encode();
	  }
	  return $data;
	}
	
}
?>
