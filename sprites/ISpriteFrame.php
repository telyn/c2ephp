<?php
interface ISpriteFrame {
  /*public function GetPixel($x,$y);
  public function SetPixel($x,$y,$r,$g,$b);*/
	public function ToPNG();
	public function ToGDImage();
	//public function Compile();
	//At the moment not all ISpriteFrames support these functions.
}
?>