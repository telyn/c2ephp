<?php
require_once(dirname(__FILE__).'/SpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
class S16Frame extends SpriteFrame
{
	 private $offset;
	 private $reader;
	 private $decoded;
	 private $encoding;
	 
	 public function S16Frame($reader,$encoding='565',$width=false,$height=false,$offset=false)
	 {
	   if($reader instanceof IReader) {
    	 	$this->reader = $reader;
    	 	$this->encoding = $encoding;
    	 	if($width === false || $height === false || $offset === false) {
    		 	$this->offset = $this->reader->ReadInt(4);
    		 	parent::SpriteFrame($this->reader->ReadInt(2), $this->reader->ReadInt(2));
    	 	} else {
    	 		parent::SpriteFrame($width,$height);
    	 		$this->offset = $offset;
    	 	}
     } else if(get_resource_type($reader) == 'gd') {
        $this->gdImage = $reader;
        $this->decoded = true;
        $this->encoding = $encoding;
        parent::SpriteFrame(imagesx($reader), imagesy($reader));
        
     }
	 }
	 public function Encode() {
	   throw new Exception('Not implemented yet');
	 }
	 protected function Decode() {
	  if($this->decoded) { return $this->gdImage; }
	  
	  $image = imagecreatetruecolor($this->GetWidth(),
                    $this->GetHeight());
    $this->reader->Seek($this->offset);
    for($y = 0; $y < $this->GetHeight(); $y++)
    {
      for($x = 0; $x < $this->GetWidth(); $x++)
      {
        $pixel = $this->reader->ReadInt(2);
        $red = 0; $green = 0; $blue = 0;
        if($this->encoding == "565")
        {
          $red   = ($pixel & 0xF800) >> 8;
          $green = ($pixel & 0x07E0) >> 3;
          $blue  = ($pixel & 0x001F) << 3;
        }
        else if($this->encoding == "555")
        {
          $red   = ($pixel & 0x7C00) >> 7;
          $green = ($pixel & 0x03E0) >> 2;
          $blue  = ($pixel & 0x001F) << 3;
        }
        $colour = imagecolorallocate($image, $red, $green, $blue);
        imagesetpixel($image, $x, $y, $colour);
      }
    }
    $this->gdImage = $image;
    return $image;
	 }
}
?>