<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
require_once(dirname(__FILE__)."/S16Frame.php");
class S16File
{
	private $encoding;
	private $frame_count;
	private $frame_header;
	private $fp;
	function S16File(IReader $fp)
	{
		$this->fp = $fp;
		$buffer = $this->fp->ReadInt(4);
		if($buffer == 1)
			$this->encoding = "565";
		else if($buffer == 0)
			$this->encoding = "555";
		else
			throw new Exception("File encoding not recognised. (".$buffer.'|'.$this->fp->GetPosition().')');
		$buffer = $this->fp->ReadInt(2);
		$this->frame_count = $buffer;
		for($x=0; $x < $this->frame_count; $x++)
		{
			$this->frame_header[$x] = new S16Frame($this->fp);
		}
	}
	
	function OutputPNG($frame)
	{
		if($this->frame_count < ($frame+1))
			throw new Exception("OutputPNG - Frame out of bounds - ".$frame);
		return $this->frame_header[$frame]->OutputPNG($this->encoding);
	}
}
?>
