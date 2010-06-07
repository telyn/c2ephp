<?php

require_once(dirname(__FILE__).'/CreaturesArchiveBlock.php');
require_once(dirname(__FILE__).'/PrayBlock.php');
abstract class CreaturesArchiveBlock extends PrayBlock {
	public function CreaturesArchiveBlock(&$prayfile,$name,$content,$flags,$type) {
		parent::PrayBlock($prayfile,$name,$content,$flags,$type);
		if($prayfile instanceof PRAYFile) {
			if(!$this->DeArchive()) {
				throw new Exception('De-Archiving failed, block probably wasn\'t a CreaturesArchive type');
			}
		}
	}
	private function DeArchive() {
		$content = $this->GetData();
		if($content{0} == 'C') {
			$content = DeArchive($content);
			if($content !== false) {
				$this->SetData($content);
				return true;
			}
//			echo 'Invalid CreaturesArchive';
			return false;
		}
		return true;
	}

}
?>
