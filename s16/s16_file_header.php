<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
require_once(dirname(__FILE__)."/s16_frame_header.php");
class s16_file_header
{
	var $encoding;
	var $frame_count;
	var $frame_header;
	function s16_file_header(IReader $fp)
	{
		$buffer = $fp->ReadInt(4);
		if($buffer == 1)
			$this->encoding = "565";
		else if($buffer == 0)
			$this->encoding = "555";
		else
			throw new Exception("File encoding not recognised. (".$buffer.")");
		$buffer = $fp->ReadInt(2);
		$this->frame_count = $buffer;
		for($x=0; $x < $this->frame_count; $x++)
		{
			$this->frame_header[$x] = new s16_frame_header($fp);
		}
	}
	
	function OutputPNG($frame, IReader $fp)
	{
		if($this->frame_count < ($frame+1))
			throw new Exception("OutputPNG - Frame out of bounds - ".$frame);
		$this->frame_header[$frame]->OutputPNG($fp, $this->encoding);
	}
}
?>