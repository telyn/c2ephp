<?php
require_once(dirname(__FILE__).'/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
class S16Frame implements ISpriteFrame
{
	 private $offset;
	 private $width;
	 private $reader;
	 private $height;
	 private $encoding;
	 
	 public function S16Frame($reader,$encoding,$width=false,$height=false,$offset=false)
	 {
	   if($reader instanceof IReader) {
    	 	$this->reader = $reader;
    	 	$this->encoding = $encoding;
    	 	if($width === false || $height === false || $offset === false) {
    		 	$this->offset = $this->reader->ReadInt(4);
    		 	$this->width = $this->reader->ReadInt(2);
    	 		$this->height = $this->reader->ReadInt(2);
    	 	} else {
    	 		$this->width = $width;
    	 		$this->height = $width;
    	 		$this->offset = $offset;
    	 	}
     }/* else if(get_resource_type($reader) == 'gd') {
        $this->gdImage = $reader;
        $this->decoded = true;
        $this->width = imagesx($reader);
        $this->height = imagesy($reader);
        $this->encoding = '565';
     }*/
	 }
	 public function ToGDImage() {
	  $image = imagecreatetruecolor($this->width,
                    $this->height);
    $this->reader->Seek($this->offset);
    for($y = 0; $y < $this->height; $y++)
    {
      for($x = 0; $x < $this->width; $x++)
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
    return $image;
	 }
	 public function ToPNG()
	 {
	 	ob_start();
		imagepng($this->ToGDImage());
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	 }
}
?>
