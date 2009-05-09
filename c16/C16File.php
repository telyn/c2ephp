<?php
require_once(dirname(__FILE__)."/C16Frame.php");
class C16File
{
	private $encoding;
	private $frame_count;
	private $frame_header;
	private $fp;
	public function C16File(IReader $fp)
	{
		$this->fp = $fp;
		$buffer = $this->fp->ReadInt(4);
		if($buffer == 3)
			$this->encoding = "565";
		else if($buffer == 2)
			$this->encoding = "555";
		else
			throw new Exception("File encoding not recognised. (".$buffer.")");
		$buffer = $this->fp->ReadInt(2);
		if($buffer < 1)
			throw new Exception("Sprite file appears to contain less than 1 frame.");
		$this->frame_count = $buffer;
		for($x=0; $x < $this->frame_count; $x++)
		{
			$this->frame_header[$x] = new C16Frame($this->fp);
		} 
	}
	
	public function OutputPNG($frame)
	{
		if($this->frame_count < ($frame+1))
			throw new Exception("OutputPNG - Frame out of bounds - ".$frame);
		return $this->frame_header[$frame]->OutputPNG($this->fp, $this->encoding);
	}
}
?>
