<?php
/* 0 = Main directory 
1 = Sounds directory 
2 = Images directory 
3 = Genetics Directory 
4 = Body Data Directory (ATT files) 
5 = Overlay Directory 
6 = Backgrounds Directory 
7 = Catalogue Directory 
8 = Bootstrap Directory (Denied) 
9 = Worlds Directory (Denied) 
10 = Creatures Directory 
11 = Pray Files Directory (Denied) */
define('PRAY_DEPENDENCY_SOUND',1);
define('PRAY_DEPENDENCY_IMAGE',2);
define('PRAY_DEPENDENCY_GENE',3);
define('PRAY_DEPENDENCY_BODYDATA',4);
define('PRAY_DEPENDENCY_OVERLAY',5);
define('PRAY_DEPENDENCY_BACKGROUND',6);
define('PRAY_DEPENDENCY_CATALOGUE',7);
define('PRAY_DEPENDENCY_CREATURE',10);

class PrayDependency {
	private $category;
	private $filename;
	public function PrayDependency($category,$filename) {
		$this->category = $category;
		$this->filename = $filename;
	}
	public function GetCategory() {
		return $this->category;
	}
	public function GetFileName() {
		return $this->filename;
	}
}
?>