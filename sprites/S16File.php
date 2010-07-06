<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
require_once(dirname(__FILE__).'/SpriteFile.php');
require_once(dirname(__FILE__)."/S16Frame.php");
class S16File extends SpriteFile
{
	private $encoding;
	private $frameCount;
	private $frames;
	private $reader;
	
	public function S16File(IReader $reader)
	{
	  parent::SpriteFile('S16');
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
			$this->AddFrame(new S16Frame($this->reader,$this->encoding));
		}
	}
	public function Compile() {
	  throw new Exception('Not yet implemented..');
	}
}
?>