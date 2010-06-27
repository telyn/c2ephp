<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/ISpriteFile.php');
require_once(dirname(__FILE__).'/C16Frame.php');

/** \brief Class representing a C16 sprite file. 
  * This documentation was written in the future, when the code had already been written
  * In other words, what is true according to what's in the docs is not what's actually true RIGHT NOW
  * I will remove this notice when I finish coding this class.
  */
class C16File implements ISpriteFile
{
	private $encoding;
	private $frameCount;
	private $frames;
	private $reader;
	/** \brief Creates a new C16File object.
	  * If $reader is null, creates an empty C16File ready to add sprites to.
	  * \param IReader $reader The reader to read the sprites from. Will be able to be null
	  */
	public function C16File(IReader $reader)
	{
		$this->reader = $reader;
		$buffer = $this->reader->ReadInt(4);
		if($buffer == 3)
			$this->encoding = '565';
		else if($buffer == 2)
			$this->encoding = '555';
		else
			throw new Exception('File encoding not recognised. ('.$buffer.')');
		$buffer = $this->reader->ReadInt(2);
		if($buffer < 1)
			throw new Exception('Sprite file appears to contain less than 1 frame.');
		$this->frameCount = $buffer;
		for($x=0; $x < $this->frameCount; $x++)
		{
			$this->frames[$x] = new C16Frame($this->reader,$this->encoding);
		} 
	}
	/** \brief Gets the specified C16Frame
	  * \param $frame The index of the frame to get.
	  * \return A C16Frame
	  */
	public function GetFrame($frame) {
		if($this->frameCount < ($frame+1))
			throw new Exception('GetFrame - Frame out of bounds - '.$frame);
		return $this->frames[$frame];
	}
	/** \brief Makes a PNG of the specified frame
	  * This function is syntactic sugar for GetFrame($frame)->OutputPNG();
	  * \param $frame The frame to output.
	  * \return A PNG as a binary string.
	  */
	public function OutputPNG($frame)
	{
		$this->GetFrame($frame)->OutputPNG($this->encoding);
	}
}
?>
