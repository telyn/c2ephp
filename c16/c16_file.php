<?php
require_once(dirname(__FILE__)."/c16_file_header.php");
class c16_file
{
	var $header;
	var $file;
	function c16_file(IReader $c16file)
	{
		$this->file = $c16file;
		
		$this->header = new c16_file_header($this->file);
	}
	
	function OutputPNG($frame)
	{
		return $this->header->OutputPNG($frame, $this->file);
	}
}
?>