<?php
require_once(dirname(__FILE__)."/c16_file_header.php");
class c16_file
{
	var $header;
	var $file;
	function c16_file($s16file)
	{
		if(!file_exists($s16file))
			throw new Exception("File does no exist: ".$s16file);
		if(!is_file($s16file))
			throw new Exception("Target is not a file.");
		if(!is_readable($s16file))
			throw new Exception("File exists, but is not readable.");
		
		$this->file = fopen($s16file, 'rb');
		
		if(!$this->file)
			throw new Exception("File failed to open.");
		
		$this->header = new c16_file_header($this->file);
	}
	
	function OutputPNG($frame)
	{
		$this->header->OutputPNG($frame, $this->file);
	}
}
?>