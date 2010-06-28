<?php
require_once(dirname(__FILE__).'/ISpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');


class SPRFrame implements ISpriteFrame {
	private $width;
	private $height;
	private $offset;
	private $reader;
	public static $sprToRGB;
	
	public function SPRFrame($reader,$width,$height) {
	  if($reader instanceof IReader) {
  		$this->reader = $reader;
  		$this->width = $width;
  		$this->height = $height;
  		$this->offset = $this->reader->GetPosition();
  		
  		//initialise palette if necessary.
  		if(empty(SPRFrame::$sprToRGB)) {
  			$paletteReader = new FileReader(dirname(__FILE__).'/palette.dta');
  			for($i = 0;$i<256;$i++) {
  				SPRFrame::$sprToRGB[$i] = array('b'=>$paletteReader->ReadInt(1)*4,'g'=>$paletteReader->ReadInt(1)*4,'r'=>$paletteReader->ReadInt(1)*4);
  			}
  			unset($paletteReader);
  		}
    }
	}
	//Sometimes you need to flip an image. trufax. Agent thumbnails I think.
	public function Flip() {
	  if($this->decoded) { throw new Exception('You\'re too late to flip the image, it\'s already been decoded!'); }
		$tempData = '';
		for($i=$this->height-1;$i>-1;$i--) {
			$tempData .= $reader->GetSubString($this->offset+$this->width*$i,$this->width);
		}
		$this->reader = new StringReader($tempData);
		$this->offset = 0;

	}
  public function ToGDImage() {
    if($this->decoded) { return $this->gdImage; }
	   
    $image = imagecreatetruecolor($this->width, $this->height);
    $this->reader->Seek($this->offset);
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
    return $image;
	}
	public function ToPNG() {
		ob_start();
		imagepng($this->ToGDImage());
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}
	private function NextToRGB() {
		$colour = $this->reader->ReadInt(1);
		return self::$sprToRGB[$colour];
	}
	
}
?>
