<?php
require_once(dirname(__FILE__).'/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

class C16Frame implements ISpriteFrame 
{
	 private $offset;
	 private $width;
	 private $height;
	 private $reader;
	 private $encoding;
	 private $lineOffset;
	 
	 public function C16Frame(IReader &$reader,$encoding)
	 {
 	 	$this->reader = $reader;
 	 	$this->encoding = $encoding;
	 	$this->offset = $this->reader->ReadInt(4);
	 	$buffer = $this->reader->ReadInt(2);
	 	if($buffer < 1)
	 		throw new Exception('Frame claims zero width.');
	 	$this->width = $buffer;
	 	$this->height = $this->reader->ReadInt(2);
	 	for($x = 0; $x < ($this->height - 1); $x++)
	 	{
	 		$this->lineOffset[$x] = $this->reader->ReadInt(4);
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
			for($x = 0; $x < $this->width;)
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
			if($x == $this->width)
				$this->reader->Skip(2);
			}
		}
		imagepng($image);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	 }
}
?>
