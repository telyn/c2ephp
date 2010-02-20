<?php
require_once(dirname(__FILE__).'/AGNTBlock.php');
require_once(dirname(__FILE__).'/CREABlock.php');
require_once(dirname(__FILE__).'/DFAMBlock.php');
require_once(dirname(__FILE__).'/DSAGBlock.php');
require_once(dirname(__FILE__).'/DSEXBlock.php');
require_once(dirname(__FILE__).'/EGGBlock.php');
require_once(dirname(__FILE__).'/EXPCBlock.php');
require_once(dirname(__FILE__).'/FILEBlock.php');
require_once(dirname(__FILE__).'/GENEBlock.php');
require_once(dirname(__FILE__).'/GLSTBlock.php');
require_once(dirname(__FILE__).'/LIVEBlock.php');
require_once(dirname(__FILE__).'/PHOTBlock.php');
require_once(dirname(__FILE__).'/SFAMBlock.php');


define('PRAY_BLOCK_AGNT','AGNT');
define('PRAY_BLOCK_CREA','CREA');
define('PRAY_BLOCK_DFAM','DFAM');
define('PRAY_BLOCK_DSAG','DSAG');
define('PRAY_BLOCK_DSEX','DSEX');
define('PRAY_BLOCK_EGG' ,'EGG' );
define('PRAY_BLOCK_EXPC','EXPC');
define('PRAY_BLOCK_FILE','FILE');
define('PRAY_BLOCK_GENE','GENE');
define('PRAY_BLOCK_GLST','GLST');
define('PRAY_BLOCK_LIVE','LIVE');
define('PRAY_BLOCK_PHOT','PHOT');
define('PRAY_BLOCK_SFAM','SFAM');

function MakePrayBlock($blocktype,PRAYFile &$prayfile,$name,$content,$flags) {
	switch($blocktype) {
		//agents
		case 'AGNT':
			return new AGNTBlock($prayfile,$name,$content,$flags);
		case 'DSAG':
			return new DSAGBlock($prayfile,$name,$content,$flags);
		case 'LIVE':
			return new LIVEBlock($prayfile,$name,$content,$flags); //sea monkeys agent files.

		//egg
		case 'EGG':
			return new EGGBlock($prayfile,$name,$content,$flags);

		//starter families
		case 'DFAM':
			return new DFAMBlock($prayfile,$name,$content,$flags);
		case 'SFAM':
			return new SFAMBlock($prayfile,$name,$content,$flags);
		
		//exported creatures
		case 'EXPC':
			return new EXPCBlock($prayfile,$name,$content,$flags);
		case 'DSEX':
			return new DSEXBlock($prayfile,$name,$content,$flags);

		case 'CREA':
			return new CREABlock($prayfile,$name,$content,$flags);

		//creature photos
		case 'PHOT':
			return new PHOTBlock($prayfile,$name,$content,$flags);

		//creature history
		case 'GLST':
			return new GLSTBlock($prayfile,$name,$content,$flags);

		//creature genetics
		case 'GENE':
			return new GENEBlock($prayfile,$name,$content,$flags);
	
		//files
		case 'FILE':
			return new FILEBlock($prayfile,$name,$content,$flags);

	}
}
define('PRAY_FLAG_ZLIB_COMPRESSED',1);

abstract class PrayBlock {
	private $prayfile;
	private $content;
	private $name;
	private $type;	

	public function PrayBlock(&$prayfile,$name,$content,$flags,$type) {
		$this->prayfile = $prayfile;
		$this->name = $name;
		$this->content = $content;
		$this->flags = $flags;
		$this->type = $type;
	}
	private function EncodeBlockHeader() {
		$compiled = $this->GetType();
		$compiled .= substr($this->GetName(),0,128);
		$len = 128 - strlen($this->GetName());
		for($i=0;$i<$len;$i++) {
			$compiled .= "\0";
		}
	}
	public function GetName() {
		return $this->name;
	}
	public function GetData() { //uncompressed if compressed.
		return $this->content;
	}
	public function GetType() {
		return $this->type;
	}
	public function GetFlags() {
		return $this->flags;
	}
	public function IsFlagSet($flag) {
		return ($this->flags & $flag == $flag);
	}
	public function GetLength() {
		return strlen($this->content);
	}
	public function GetPrayFile() {
		return $this->prayfile;
	}
	protected function SetData($content) {
		$this->content = $content;
	}
	public function Compile() {
		$compiled  = $this->EncodeBlockHeader();
		$compiled .= $this->GetData();
	}
}
?>
