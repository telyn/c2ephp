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
     $data = '';
     $lineOffsets = array();
     for($y = 0; $y < $this->GetHeight(); $y++) {
       $wasblack = 0;
       $runlength = 0;
       if($y > 0) {
         $lineOffsets[] = strlen($data);
       }
       $colourRunData = '';
       for($x = 0; $x < $this->GetWidth(); $x++) {
         
         $pixel = $this->GetPixel($x,$y);
         if($pixel['red'] > 255 || $pixel['green'] > 255 || $pixel['blue'] > 255) {
           throw new Exception('Pixel colour out of range.');
         }
         $newpixel = 0;
         if($this->encoding == '555') {
             $newpixel = (($pixel['red'] << 7) & 0xF800) | (($pixel['green'] << 2) & 0x03E0) | (($pixel['blue'] >> 3) & 0x001F);
         } else {
             $newpixel = (($pixel['red'] << 8) & 0xF800) | (($pixel['green'] << 3) & 0x07E0) | (($pixel['blue'] >> 3) & 0x001F);
         }
         
         
         // if isblack !== wasblack
         if(($newpixel == 0) !== $wasblack || $runlength > 32766) {
           //end the run if this isn't the first run
           if($wasblack !== 0) {
           
             //output data.
             print "Ending run \n";
             $run = $runlength << 1;
             if($wasblack) {
               $data .= pack('v',$run);
                
             } else {
               $run = $run | 1;
               $data .= pack('v',$run);
               $data .= $colourRunData;
               $colourRunData = '';
             }
           }
           //start a new run
           if($newpixel == 0) {
             print "Starting new black run \n";
             $wasblack = true;
             $colourRunData = '';
           } else {
             print "Starting new colour run \n";
             $wasblack = false;
             $colourRunData = pack('v',$newpixel);
           }
           $runlength = 1;
           
         } else {
           if(!$wasblack) {
             $colourRunData .= pack('v',$newpixel);
           }
           $runlength++;
         }
         
         if($x == ($this->GetWidth()-1)) {
           //end run and output data.
           $run = $runlength << 1;
           if($wasblack) {
             $data .= pack('v',$run);
              
           } else {
             $run = $run | 1;
             $data .= pack('v',$run);
             $data .= $colourRunData;
             $colourRunData = '';
           }
         }
       }
       //line terminating zero tag.
       $data .= pack('xx');
     }
     //image terminating zero tag
     $data .= pack('xx');
     return array('lineoffsets' => $lineOffsets, 'data' => $data);
   }
}
?>
