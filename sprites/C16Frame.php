<?php
require_once(dirname(__FILE__).'/SpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

class C16Frame extends SpriteFrame
{
	 private $encoding;
	 
	 private $lineOffset = array();
	 
	 private $reader;
	 private $offset;
	 
	 public function C16Frame($reader,$encoding)
	 {
  	 if($reader instanceof IReader) {
     	 $this->reader = $reader;
     	 $this->encoding = $encoding;
    	 $this->offset = $this->reader->ReadInt(4);
    	 
    	 $width = $this->reader->ReadInt(2);
    	 $height = $this->reader->ReadInt(2);
    	 
    	 parent::SpriteFrame($width,$height);
    	 
    	 for($x = 0; $x < ($height - 1); $x++)
    	 {
    	   $this->lineOffset[$x] = $this->reader->ReadInt(4);
    	 }
     } else if(is_resource($reader)) {
        if(get_resource_type($reader) == 'gd') {
          $this->encoding = '565';
          parent::SpriteFrame(imagesx($reader),imagesy($reader),true);
          $this->gdImage = $reader;
        }
     }
	 }
	 public function SetEncoding($encoding) {
	   $this->EnsureDecoded();
	   $this->encoding = $encoding;
	 }
	 protected function Decode()
	 {	  
		$image = imagecreatetruecolor($this->GetWidth(),
									  $this->GetHeight());
		$this->reader->Seek($this->offset);
		for($y = 0; $y < $this->GetHeight(); $y++)
		{
			for($x = 0; $x < $this->GetWidth();)
			{
				$run = $this->reader->ReadInt(2);
				if(($run & 0x0001) > 0)
					$run_type = "colour";
				else
					$run_type = "black";
				$run_length = ($run & 0x7FFF) >> 1;
				if($run_type == "black")
				{
					$z = $x + $run_length;
					for(;$x < $z; $x++)
					{
						imagesetpixel($image, $x, $y, imagecolorallocate($image, 0, 0, 0));
					}
				}
				else //colour run
				{
					$z = $x + $run_length;
					for(;$x < $z; $x++)
					{
						$pixel = $this->reader->ReadInt(2);
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
			if($x == $this->GetWidth())
				$this->reader->Skip(2);
			}
		}
		$this->gdImage = $image;
		return $image;
	 }
   public function Encode() {
     throw new Exception('Not yet implemented');
   }
}
?>
