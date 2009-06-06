<?php
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/../sprites/SPRFrame.php');
require_once(dirname(__FILE__).'/../sprites/S16Frame.php');
require_once(dirname(__FILE__).'/ICOB.php');
require_once(dirname(__FILE__).'/C1COB.php');
require_once(dirname(__FILE__).'/C2COB.php');

class COB implements ICOB {
	public $cob;
	public function COB(IReader $reader) {
		echo "New cob\n";
		$this->LoadCOB($reader);
	}
	public function LoadCOB(IReader $reader) {
		if($reader->Read(4) == 'cob2') {
			$reader->Seek(0);
			$this->cob = new C2COB($reader);
			echo "COB2!\n";
		} else {
			echo "Compressed or COB1\n";
			$uncompressed = @gzuncompress($reader->GetSubString(0));
			if($uncompressed !== false) {
				$this->LoadCOB(new StringReader($uncompressed));
			} else {
				$this->cob = new C1COB($reader);
			}
		}
	}
	public function GetData() {
		$this->cob->GetData();
	}
}



?>
