<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
class s16_frame_header
{
	 var $offset;
	 var $width;
	 var $height;
	 function s16_frame_header($fp)
	 {
	 	$this->offset = $fp->ReadInt(4);
	 	$buffer = $fp->ReadInt(2);
	 	$this->width = $buffer;
	 	$this->height = $fp->ReadInt(2);
	 }
	 
	 function OutputPNG($fp, $encoding)
	 {
	 	header("Content-type: image/png");
		$image = imagecreatetruecolor($this->width,
									  $this->height);
		$fp->Seek($this->offset);
		for($y = 0; $y < $this->height; $y++)
		{
			for($x = 0; $x < $this->width; $x++)
			{
				$pixel = $fp->ReadInt(2);
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
	 }
}
?>