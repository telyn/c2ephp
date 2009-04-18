<?php
function DeArchive($data) {
	if(is_string($data)) {
		if(substr($data,0,55) == "Creatures Evolution Engine - Archived information file.") {
			$data = substr($data,strpos($data,chr(0x1A).chr(0x04))+2);
			$data = gzuncompress($data);
			return $data;
		}
		die(':(');
		return false;
	} else if(is_resource($data)) {
		return false; //coming soon
	}	
}
function Archive($data,$filehandle=null) {
	if(is_resource($filehandle)) {
		return false;
	}
	$data = gzcompress($data);
	$data = "Creatures Evolution Engine - Archived information file. zLib 1.13 compressed.".chr(0x1A).chr(0x04).$data;
	return $data;
	
}
?>