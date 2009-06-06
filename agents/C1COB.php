<?php
require_once(dirname(__FILE__).'/ICOB.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');

class C1COB implements ICOB {
	private $data;
	private $reader;
	public function C1COB(IReader $reader) {
		$this->LoadCOB($reader);
	}
	public function LoadCOB(IReader $reader) {
		$this->reader = $reader;
		$this->data = array();
		$this->data['game'] = 'Creatures';
		$this->data['version'] 				= $this->reader->ReadInt(2);
		$this->data['quantityAvailable']	= $this->reader->ReadInt(2);
		$expires_month = $this->reader->ReadInt(4);
		$expires_day = $this->reader->ReadInt(4);
		$expires_year = $this->reader->ReadInt(4);
		$this->data['expires'] = array(
			'day' 		=> $expires_day,
			'month' 	=> $expires_month,
			'year'		=> $expires_year,
			'timestamp' => mktime(0,0,0,$expires_month,$expires_day,$expires_year)
		);
		
		$objectscripts = $this->reader->ReadInt(2);
		$installscripts = $this->reader->ReadInt(2);
		$this->data['quantityUsed'] = $this->reader->ReadInt(4);
		$this->data['objectScripts'] = array();
		for($i=0;$i<$objectscripts;$i++) {
			$scriptsize = $this->reader->ReadInt(1);
			if($scriptsize == 255) {
				$scriptsize = $this->reader->ReadInt(2);
			}
			$this->data['objectScripts'][$i] = $this->reader->Read($scriptsize);
		}
		$this->data['installScripts'] = array();
		for($i=0;$i<$installscripts;$i++) {
			$scriptsize = $this->reader->ReadInt(1);
			if($scriptsize == 255) {
				$scriptsize = $this->reader->ReadInt(2);
			}
			$this->data['installScripts'][$i] = $this->reader->Read($scriptsize);
		}
		$this->data['picture']['width'] = $this->reader->ReadInt(4);
		$this->data['picture']['height'] = $this->reader->ReadInt(4);
		$this->data['picture']['unknown'] = $this->reader->ReadInt(2);
		
		$sprframe = new SPRFrame($this->reader,$this->data['picture']['width'],$this->data['picture']['height']);
		$sprframe->Flip();
		$this->data['picture']['spr'] = $sprframe->OutputPNG();
		$this->data['name'] = $this->reader->Read($this->reader->ReadInt(1));
	}
	
	public function GetData() {
		return $this->data;
	}		
}
?>
