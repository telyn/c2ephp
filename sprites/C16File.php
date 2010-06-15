<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/ISpriteFile.php');
require_once(dirname(__FILE__).'/C16Frame.php');
class C16File implements ISpriteFile
{
	private $encoding;
	private $frameCount;
	private $frames;
	private $reader;
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
	public function GetFrame($frame) {
		if($this->frameCount < ($frame+1))
			throw new Exception('OutputPNG - Frame out of bounds - '.$frame);
		return $this->frames[$frame];
	}
	public function OutputPNG($frame)
	{
		if($this->frameCount < ($frame+1))
			throw new Exception('OutputPNG - Frame out of bounds - '.$frame);
		return $this->frames[$frame]->OutputPNG($this->encoding);
	}
}
?>
