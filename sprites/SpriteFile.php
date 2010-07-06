<?php
abstract class SpriteFile {
  private $frames = array();
  private $spritefiletype;
  
  public function SpriteFile($filetype) {
    $this->spritefiletype = $filetype;
  }
  
	public function GetFrame($frame) {
	  return $this->frames[$frame];
	}
	public function GetFrames() {
	  return $this->frames;
	}
	public abstract function Compile();
	
	public function AddFrame(SpriteFrame $frame, $position=false) {
	  if($position === false) {
	    $position = sizeof($this->frames);
	  } else if($position < 0) {
	    $position = sizeof($this->frames) - $position;
	  }
	  if($this->spritefiletype == substr(get_class($frame),0,3)) {
	    $this->frames[$position] = $frame;
	  } else {
	     $this->frames[$position] = $frame->ToSpriteFrame($this->spritefiletype);
	  }
	}
	
	public function ReplaceFrame(SpriteFrame $frame, $position) {
	  if($position === false) {
      $position = sizeof($this->frames);
    } else if($position < 0) {
      $position = sizeof($this->frames) - $position;
    }
    for($i = sizeof($this->frames); $i > $position; $i--) {
      $this->frames[$i] = $this->frames[$i-1];
    }
    $this->frames[$position] = $frame->ToSpriteFrame($this->spritefiletype);
	}
	public function GetFrameCount() {
	  return sizeof($this->frames);
	}
	public function DeleteFrame($frame) {
	  unset($this->frames[$frame]);
	}
	public function ToPNG($frame) {
	  return $this->frames[$frame]->ToPNG();
	}
}
?>