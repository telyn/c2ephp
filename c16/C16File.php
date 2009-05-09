<?php
require_once(dirname(__FILE__)."/C16Frame.php");
class C16File
{
	var $encoding;
	var $frame_count;
	var $frame_header;
	function C16File(IReader $fp)
	{
		$buffer = $fp->ReadInt(4);
		if($buffer == 3)
			$this->encoding = "565";
		else if($buffer == 2)
			$this->encoding = "555";
		else
			throw new Exception("File encoding not recognised. (".$buffer.")");
		$buffer = $fp->ReadInt(2);
		if($buffer < 1)
			throw new Exception("Sprite file appears to contain less than 1 frame.");
		$this->frame_count = $buffer;
		for($x=0; $x < $this->frame_count; $x++)
		{
			$this->frame_header[$x] = new C16Frame($fp);
		} 
	}
	
	function OutputPNG($frame, $fp)
	{
		if($this->frame_count < ($frame+1))
			throw new Exception("OutputPNG - Frame out of bounds - ".$frame);
		return $this->frame_header[$frame]->OutputPNG($fp, $this->encoding);
	}
}
?>
