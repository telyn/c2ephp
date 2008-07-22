<?php
class s16_frame_header
{
	 var $offset;
	 var $width;
	 var $height;
	 function s16_frame_header($fp)
	 {
	 	$this->offset = ReadLittle($fp, 4);
	 	$buffer = ReadLittle($fp, 2);
	 	//if($buffer&4)
	 	//	throw new Exception("Invalid frame width; must be divisible by 4 - ".$buffer.".");
	 	$this->width = $buffer;
	 	$this->height = ReadLittle($fp, 2);
	 }
	 
	 function OutputPNG($fp, $encoding)
	 {
	 	header("Content-type: image/png");
		$image = imagecreatetruecolor($this->width,
									  $this->height);
		fseek($fp, $this->offset);
		for($y = 0; $y < $this->height; $y++)
		{
			for($x = 0; $x < $this->width; $x++)
			{
				$pixel = 0;
				$pixel += ord(fgetc($fp));
				$pixel += ord(fgetc($fp)) << 8;
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

function ReadLittle($fp, $count)
{
	$int = 0;
	for($x = 0; $x < $count; $x++)
	{
		$buffer = (ord(fgetc($fp)) << ($x * 8));
		if($buffer === false)
			throw new Exception("Read failure");
		//printf("(%d)", $buffer);
		$int += $buffer;
	}
	return $int; 
}
?>