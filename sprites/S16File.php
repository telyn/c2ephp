<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
require_once(dirname(__FILE__)."/S16Frame.php");
class S16File
{
	private $encoding;
	private $frame_count;
	private $frame_header;
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
		$this->frame_count = $this->reader->ReadInt(2);
		for($i=0; $i < $this->frame_count; $i++)
		{
			$this->frame_header[$i] = new S16Frame($this->reader,$this->encoding);
		}
	}
	public function GetFrame($frame) {
		return $this->frame_header[$frame];
	}
	public function OutputPNG($frame)
	{
		if($this->frame_count < ($frame+1))
			throw new Exception("OutputPNG - Frame out of bounds - ".$frame);
		return $this->frame_header[$frame]->OutputPNG($this->encoding);
	}
}
?>
