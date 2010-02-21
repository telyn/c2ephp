<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/PrayBlock.php');
abstract class CreaturesArchiveBlock extends PrayBlock {
	public function CreaturesArchiveBlock(&$prayfile,$name,$content,$flags,$type) {
		parent::PrayBlock($prayfile,$name,$content,$flags,$type);
		if($prayfile instanceof PRAYFile) {
			$this->Decompress();
		}
	}

	private function Decompress() {
			$content = $this->GetData(true);
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
