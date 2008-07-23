<?php
require_once(dirname(__FILE__)."/../support/FileReader.php");
class c16_frame_header
{
	 var $offset;
	 var $width;
	 var $height;
	 var $line_offset;
	 function c16_frame_header($fp)
	 {
	 	$this->offset = $fp->ReadInt(4);
	 	$buffer = $fp->ReadInt(2);
	 	if($buffer < 1)
	 		throw new Exception("Frame claims zero width.");
	 	$this->width = $buffer;
	 	$this->height = $fp->ReadInt(2);
	 	for($x = 0; $x < ($this->height - 1); $x++)
	 	{
	 		$this->line_offset[$x] = $fp->ReadInt(4);
	 	}
	 }
	 
	 function OutputPNG($fp, $encoding)
	 {
	 	header("Content-type: image/png");
		$image = imagecreatetruecolor($this->width,
									  $this->height);
		$fp->Seek($this->offset);
		for($y = 0; $y < $this->height; $y++)
		{
			for($x = 0; $x < $this->width;)
			{
				$run = $fp->ReadInt(2);
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
			if($x == $this->width)
				$fp->Skip(2);
			}
		}
		imagepng($image);
	 }
}
?>