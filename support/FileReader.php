<?php
require_once(dirname(__FILE__).'/IReader.php');
class FileReader implements IReader
{
	private $fp;
    public function FileReader($file)
    {
    	if(!file_exists($file))
			throw new Exception("File does not exist: ".$file);
		if(!is_file($file))
			throw new Exception("Target is not a file.");
		if(!is_readable($file))
			throw new Exception("File exists, but is not readable.");
		
		$this->fp = fopen($file, 'rb');
	}
	
    public function Read($count)
    {
		if($count > 0) {
			return fread($this->fp, $count);
		}
		return '';
	}
	
	public function ReadInt($count)
	{
		$int = 0;
		for($x = 0; $x < $count; $x++)
		{
			$buffer = (ord(fgetc($this->fp)) << ($x * 8));
			if($buffer === false)
				throw new Exception("Read failure");
			$int += $buffer;
		}
		return $int;
	}
	
	public function GetPosition()
	{
		return ftell($this->fp);
	}
	
	public function GetSubString($start,$length = FALSE)
	{
		fseek($this->fp,$start);
		if($length === false) {
			$data = '';
			while($data.=fread($this->fp,4096));
			return $data;
		}
		return fread($this->fp,$length);
	}
	
	public function Seek($position)
	{
		fseek($this->fp, $position);
	}
	
	public function Skip($count)
	{
		fseek($this->fp, $count, SEEK_CUR);
	}
}
?>
