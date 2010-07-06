<?php
abstract class SpriteFrame {
  private $decoded;
  protected $gdImage;
  private $width;
  private $height;
  
  public function SpriteFrame($width,$height,$decoded=false) {
    if($width == 0) {
      throw new Exception('Zero width');
    } else if($height == 0) {
      throw new Exception('Zero height');
    }
    $this->width = $width;
    $this->height = $height;
    $this->decoded = $decoded;
  }
  
  protected function HasBeenDecoded() {
    return $this->decoded;  
  }
  
  public function GetGDImage() {
    $this->EnsureDecoded();
    return $this->gdImage;
  }
  public function GetWidth() {
    return $this->width;
  }
  public function GetHeight() {
    return $this->height;
  }
  public function GetPixel($x,$y) {
    $this->EnsureDecoded();
    $color = imagecolorat($this->gdImage, $x, $y);
    return imagecolorsforindex($this->gdImage, $color);
  }
  
  public function SetPixel($x,$y,$r,$g,$b) {
    $this->EnsureDecoded();
    imagesetpixel($this->gdImage, $x, $y, imagecolorallocate($this->gdImage, $r, $g, $b));
  }
  
  protected function EnsureDecoded() {
    if(!$this->decoded)
      $this->Decode();
  }
  
	protected abstract function Decode();
	public abstract function Encode();
	
	public function ToSpriteFrame($type) {
	  $this->EnsureDecoded();
	  if(substr(get_class($this),0,3) == $type && substr(get_class($this),3) == 'Frame') {
	    return $this;
	  }
	  switch($type) {
	    case 'C16':
	      return new C16Frame($this->GetGDImage());
	    case 'S16':
	      return new S16Frame($this->GetGDImage());
	    case 'SPR':
	      return new SPRFrame($this->GetGDImage());
	    default:
	      throw new Exception('Invalid sprite type '.$type.'.');
	  }
	}
	public function ToPNG() {
	  $this->EnsureDecoded();
    ob_start();
    imagepng($this->GetGDImage());
    $data = ob_get_contents();
    ob_end_clean();
    return $data;
  }
}
?>