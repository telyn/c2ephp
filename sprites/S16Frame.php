<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
class S16Frame
{
	 private $offset;
	 private $width;
	 private $reader;
	 private $height;
	 public function S16Frame(IReader &$reader,$width=false,$height=false,$offset=false)
	 {
	 	$this->reader = $reader;
	 	if($width === false || $height === false || $offset === false) {
		 	$this->offset = $this->reader->ReadInt(4);
		 	$buffer = $this->reader->ReadInt(2);
		 	$this->width = $buffer;
	 		$this->height = $this->reader->ReadInt(2);
	 	} else {
	 		$this->width = $width;
	 		$this->height = $width;
	 		$this->offset = $offset;
	 	}
	 }
	 
	 public function OutputPNG($encoding)
	 {
	 	ob_start();
		$image = imagecreatetruecolor($this->width,
									  $this->height);
		$this->reader->Seek($this->offset);
		for($y = 0; $y < $this->height; $y++)
		{
			for($x = 0; $x < $this->width; $x++)
			{
				$pixel = $this->reader->ReadInt(2);
				if($encoding == "565")
				{
					$red   = ($pixel & 0xF800) >> 8;
					$green = ($pixel & 0x07E0) >> 3;
					$blue  = ($pixel & 0x001F) << 3;
				}
				else if($encoding == "555")
				{
					$red   = ($pixel & 0x7C00) >> 7;
					$green = ($pixel & 0x03E0) >> 2;
					$blue  = ($pixel & 0x001F) << 3;
				}
				$colour = imagecolorallocate($image, $red, $green, $blue);
				imagesetpixel($image, $x, $y, $colour);
			}
		}
		imagepng($image);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	 }
}
?>
