<?php
interface ISpriteFile {
	public function GetFrame($frame);
	//public function Compile();
	//public function AddFrame($frame);
	//public function DeleteFrame($frame);
	public function ToPNG($frame);
}
?>