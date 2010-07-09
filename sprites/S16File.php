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
	public function SetEncoding($encoding) {
	  $this->EnsureDecompiled();
	  $this->encoding = $encoding;
	}
	public function Compile() {
	  $data = ''; //S16 and C16 are actually the same format....C16 just has RLE. But they're different classes. not very DRY, I know.
    $flags = 0; // 0b00 => 555 S16, 0b01 => 565 S16, 0b10 => 555 C16, 0b11 => 565 C16
    if($this->encoding == '565') {
      $flags = $flags | 1;
    }
    $data .= pack('V',$flags);
    $data .= pack('v',$this->GetFrameCount());
    $idata = '';
    $offset = 6+(8*$this->GetFrameCount());
    foreach($this->GetFrames() as $frame) {
      $data .= pack('V',$offset);
      $data .= pack('vv',$frame->GetWidth(),$frame->GetHeight());
      
      $framebin = $frame->Encode();
      $offset += strlen($framebin); 
      $idata .= $framebin;
    }
    return $data . $idata;
	}
}
?>