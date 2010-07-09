<?php
require_once(dirname(__FILE__).'/SpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');


class SPRFrame extends SpriteFrame {
	private $offset;
	private $reader;
	public static $sprToRGB;
	
	public function SPRFrame($reader,$width=0,$height=0,$offset=false) {
	  if($reader instanceof IReader) {
  		$this->reader = $reader;
  		parent::SpriteFrame($width,$height);
  		if($offset !== false) {
  		  $this->offset = $offset;
  		} else {
  		  $this->offset = $this->reader->GetPosition();
  		}

  		//initialise palette if necessary.
  		if(empty(self::$sprToRGB)) {
  			$paletteReader = new FileReader(dirname(__FILE__).'/palette.dta');
  			for($i = 0;$i<256;$i++) {
  				self::$sprToRGB[$i] = array('r'=>$paletteReader->ReadInt(1)*4,'g'=>$paletteReader->ReadInt(1)*4,'b'=>$paletteReader->ReadInt(1)*4);
  			}
  			unset($paletteReader);
  		}
    } else if(get_resource_type($reader) == 'gd') {
      parent::SpriteFrame(imagesx($reader),imagesy($reader),true);
      $this->gdImage = $reader;
    } else {
      throw new Exception('$reader was not an IReader or a gd image.');
    }
	}
	//Sometimes you need to flip an image. trufax. Agent thumbnails I think.
	public function Flip() {
	  if($this->HasBeenDecoded()) {
	    throw new Exception('Too late!');
	    return;
	  }
		$tempData = '';
		for($i=($this->GetHeight()-1);$i>-1;$i--) {
			$tempData .= $this->reader->GetSubString($this->offset+($this->GetWidth())*$i,($this->GetWidth()));
		}
		$this->reader = new StringReader($tempData);
		$this->offset = 0;

	}
  protected function Decode() {	   
    $image = imagecreatetruecolor($this->GetWidth(), $this->GetHeight());
    $this->reader->Seek($this->offset);
    for($y = 0; $y < $this->GetHeight(); $y++)
    {
      for($x = 0; $x < $this->GetWidth(); $x++)
      {
        $colour = self::$sprToRGB[$this->reader->ReadInt(1)];
        imagesetpixel($image,$x,$y,imagecolorallocate($image,$colour['r'],$colour['g'],$colour['b']));
      }
    }
    $this->gdImage = $image;
	}
	public function Encode() {
	  $data = '';
	  for($y = 0; $y < $this->GetHeight(); $y++ ) {
  	  for($x = 0; $x < $this->GetWidth(); $x++ ) {
  	    $color = $this->GetPixel($x, $y);
  	    $data .= pack('C',$this->RGBToSPR($color['red'], $color['green'], $color['blue']));
      }
	  }
	  return $data;
	}
	private function RGBToSPR($r,$g,$b) {
	  //start out with the maximum distance.
	  $minDistance = ($r^2) + ($g^2) + ($b^2);
	  $minKey = 0;
	  foreach(self::$sprToRGB as $key => $colour) {
	     $distance = pow(($r-$colour['r']),2) +  pow(($g-$colour['g']),2) +  pow(($b-$colour['b']),2);
	     if($distance == 0) {
	       return $key;
	     } else if ($distance < $minDistance) {
         $minKey = $key;
         $minDistance = $distance;
       }
	  }
	  return $key;
	}
}
?>