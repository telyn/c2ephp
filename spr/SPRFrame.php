<?php
//require_once(dirname(__FILE__).'/../support/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');


class SPRFrame {
	private $width;
	private $height;
	private $offset;
	private $data;
	public static $sprToRGB;
	public function SPRFrame(IReader $reader,$width,$height,$offset=0) {
		$this->reader = $reader;
		$this->width = $width;
		$this->height = $height;
		$this->offset = $this->reader->GetPosition();
		$this->data = new StringReader($this->reader->Read($width*$height));
	}
	public function Flip() {
		$tempData = '';
		for($i=$this->height-1;$i>-1;$i--) {
			$tempData .= $this->data->GetSubString($this->width*$i,$this->width);
		}
		$this->data = new StringReader($tempData);

	}
	public function OutputPNG() {
		ob_start();
		$image = imagecreatetruecolor($this->width, $this->height);
		$this->data->Seek(0);
		$i=0;
		for($y = 0; $y < $this->height; $y++)
		{
			for($x = 0; $x < $this->width; $x++)
			{
				$colour = $this->NextToRGB();
				imagesetpixel($image,$x,$y,imagecolorallocate($image,$colour['r'],$colour['g'],$colour['b']));
				$i++;
			}
		}
		imagepng($image);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}
	private function NextToRGB() {
		$colour = $this->data->ReadInt(1);
		return self::$sprToRGB[$colour];
	}
	
}
if(empty(SPRFrame::$sprToRGB)) {
	$paletteReader = new FileReader(dirname(__FILE__).'/palette.dta');
	for($i = 0;$i<256;$i++) {
		SPRFrame::$sprToRGB[$i] = array('b'=>$paletteReader->ReadInt(1)*4,'g'=>$paletteReader->ReadInt(1)*4,'r'=>$paletteReader->ReadInt(1)*4);
	}
	unset($paletteReader);
}
?>
