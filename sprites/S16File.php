<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
require_once(dirname(__FILE__).'/ISpriteFile.php');
require_once(dirname(__FILE__)."/S16Frame.php");
class S16File implements ISpriteFile
{
	private $encoding;
	private $frameCount;
	private $frames;
	private $reader;
	
	public function S16File(IReader $reader)
	{
		$this->reader = $reader;
		$buffer = $this->reader->ReadInt(4);
		if($buffer == 1) {
			$this->encoding = "565";
		} else if($buffer == 0) {
			$this->encoding = "555";
		} else {
			throw new Exception("File encoding not recognised. (".$buffer.'|'.$this->reader->GetPosition().')');
		}
		$this->frameCount = $this->reader->ReadInt(2);
		for($i=0; $i < $this->frameCount; $i++)
		{
			$this->frames[$i] = new S16Frame($this->reader,$this->encoding);
		}
	}
	public function GetFrame($frame) {
		if($this->frameCount < ($frame+1))
			throw new Exception('OutputPNG - Frame out of bounds - '.$frame);
		return $this->frames[$frame];
	}
	public function ToPNG($frame)
	{
		return $this->GetFrame($frame)->ToPNG();
	}
}
?>
