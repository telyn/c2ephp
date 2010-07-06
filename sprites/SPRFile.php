<?php
require_once(dirname(__FILE__).'/SPRFrame.php');
require_once(dirname(__FILE__).'/SpriteFile.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

//TODO: THIS CLASS WAS IMPLEMENTED REALLY POORLY :(
class SPRFile extends SpriteFile {
		
	public function SPRFile(IReader	 $reader) {
		parent::SpriteFile('SPR');
		$frameCount = $reader->ReadInt(2);
		
		for($i=0;$i<$frameCount;$i++) {
			$offset = $reader->ReadInt(4); // skipping offset since I'll read all of the images in one lump.
			$width = $reader->ReadInt(2);
			$height = $reader->ReadInt(2);
			$this->AddFrame(new SPRFrame($reader,$width,$height,$offset));
		}
	}
	public function Compile() {
	  throw new Exception('Not implemented yet');
	}
}
?>
