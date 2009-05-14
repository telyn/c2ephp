<?php
require_once(dirname(__FILE__).'/SPRFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

class SPRFile {
	private $reader;
	private $frameCount;
	private $frames;
	public function SPRFile(IReader	$reader) {
		$this->reader = $reader;
		$this->frameCount = $this->reader->ReadInt(2);
		$frameData = array();
		for($i=0;$i<$this->frameCount;$i++) {
			$this->reader->ReadInt(4); // skipping offset since I'll read all of the images in one lump.
			$frameData[$i]['width'] = $this->reader->ReadInt(2);
			$frameData[$i]['height'] = $this->reader->ReadInt(2);
		}
		for($i=0;$i<$this->frameCount;$i++) {
			$this->frames[$i] = new SPRFrame($this->reader,$frameData[$i]['width'],$frameData[$i]['height']);
		}
	}
	public function OutputPNG($frame)
	{
		if($this->frameCount < ($frame+1))
			throw new Exception('OutputPNG - Frame out of bounds - '.$frame);
		return $this->frames[$frame]->OutputPNG($this->reader, $this->encoding);
	}
}
?>
