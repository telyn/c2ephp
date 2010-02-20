<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/PrayBlock.php');
abstract class CreaturesArchiveBlock extends PrayBlock {
	public function CreaturesArchiveBlock(&$prayfile,$name,$content,$flags,$type) {
		parent::PrayBlock($prayfile,$name,$content,$flags,$type);
		$this->Decompress();
	}

	public function Compile() {
		$compiled  = $this->EncodeBlockHeader();
		$compiled .= Archive($this->GetData());
	}

	private function Decompress() {
			$content = $this->GetData();
			if($content{0} == 'C') {
				$content = DeArchive($content);
				if($content !== false) {
					$this->SetData($content);
					return true;
				}
				echo 'Invalid CreaturesArchive';
				return false;
			}
			print('Not decompressing.');
	}

}
?>
