<?php

require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');
require_once(dirname(__FILE__).'/../support/IReader.php');

class C2COB implements ICOB {
	private $reader;
	private $data;
	public function C2COB(IReader $reader) {
		$this->LoadCOB($reader);
	}
	public function LoadCOB(IReader $reader,$uncompressed=false) {
		$this->reader = $reader;
		if(!$this->reader->Read(4) == 'cob2') {
			if(!$uncompressed) {
				$data = gzuncompress($this->reader->GetSubString(0));
				$this->LoadCOB($reader,true);
			} else {
				throw new Exception('Not a valid COB file');
				return;
			}
		}
		$this->data['game'] = 'Creatures 2';
		while($block = $this->ReadBlock()) {
			$this->data['blocks'][] = $block;
		}
	}
	private function ReadBlock() {
		if(!$type = $this->reader->Read(4)) {
			return false;
		}
		$size = $this->reader->ReadInt(4);
		switch($type) {
			case 'agnt':
				return $this->ReadAgentBlock(new StringReader($this->reader->Read($size)));
				break;
			case 'auth':
				return $this->ReadAuthorBlock(new StringReader($this->reader->Read($size)));
				break;
			case 'file':
				return $this->ReadFileBlock(new StringReader($this->reader->Read($size)));
				break;
			default:
				throw new Exception('Invalid block type: Probably a bug or an invalid COB file');
				break;
		}
	}
	private function ReadAgentBlock($block) {
		$blockData = array('type' => 'agent');
		
		
		$blockData['quantityAvailable'] = $block->ReadInt(2);
		if($blockData['quantityAvailable'] == 0xffff) {
			$blockData['quantityAvailable'] = 'Infinite';
		}
		$blockData['lastUsage'] = $block->ReadInt(4);
		$blockData['reuseInterval'] = $block->ReadInt(4);
		$expiryDay = $block->ReadInt(1);
		$expiryMonth = $block->ReadInt(1);
		$expiryYear = $block->ReadInt(2);
		$blockData['expires'] = array(
			'day' => $expiresDay,
			'month' => $expiresMonth,
			'year' => $expiresYear,
			'timestamp' => mktime(0,0,0,$expiresMonth,$expiresDay,$expiresYear)
		);
		$blockData['reserved1'] = $block->ReadInt(4);
		$blockData['reserved2'] = $block->ReadInt(4);
		$blockData['reserved3'] = $block->ReadInt(4);
		$blockData['agentName'] = $this->ReadString($block);
		$blockData['description'] = $this->ReadString($block);
		$blockData['installScript'] = $this->ReadString($block);
		$blockData['removeScript'] = $this->ReadString($block);
		$blockData['eventScripts'] = array();
		$eventScripts = $block->ReadInt(2);
		for($i=0;$i<$eventScripts;$i++) {
			$blockData['eventScripts'][] = $this->ReadString($block);
		}
		$dependencies = $block->ReadInt(2);
		for($i=0;$i<$dependencies;$i++) {
			$type = ($block->ReadInt(2) == 0)?'sprite':'sound';
			$name = $this->ReadString($block);
			$blockData['dependencies'][] = array('type'=>$type,'name'=>$name);
		}
		$thumbWidth = $block->ReadInt(2);
		$thumbHeight = $block->ReadInt(2);
		$thumb = new S16Frame(new StringReader($block->Read($thumbWidth*$thumbHeight*2)),$thumbWidth,$thumbHeight,0);
		$blockData['thumbnail'] = array('width'=>$thumbWidth,'height'=>$thumbHeight,'png'=>$thumb->OutputPNG('565'));
		return $blockData;
	}
	private function ReadFileBlock($block) {
		$blockData = array('type' => 'file');
		$blockData['type'] = ($block->ReadInt(2)==0)?'sprite':'sound';
		$blockData['reserved'] = $block->ReadInt(4);
		$size = $block->ReadInt(4);
		$blockData['fileName'] = $this->ReadString($block);
		$blockData['contents'] = $block->Read($size);
		
		return $blockData;
	}
	private function ReadAuthorBlock($block) {
		$blockData = array('type' => 'author');
		$creationDay = $block->ReadInt(1);
		$creationMonth = $block->ReadInt(1);
		$creationYear = $block->ReadInt(2);
		$blockData['creation'] = array(
			'day' => $creationDay,
			'month' => $creationMonth,
			'year' => $creationYear,
			'timestamp' => mktime(0,0,0,$creationMonth,$creationDay,$creationYear)
		);
		$blockData['version'] = $block->ReadInt(1);
		$blockData['revision'] = $block->ReadInt(1);
		$blockData['authorName'] = $this->ReadString($block);
		$blockData['authorEmail'] = $this->ReadString($block);
		$blockData['authorURL'] = $this->ReadString($block);
		$blockData['authorComments'] = $this->ReadString($block);
		return $blockData;
	}
	private function ReadString(IReader $reader) {
		$string = '';
		while(($char = $reader->Read(1)) !== false) {
			$string.=$char;
			if($char == "\0") {
				break;
			}			
		}
		return $string;
	}
	public function GetData($type=false) {
		if(!$type) {
            return $this->data;
        } else {
			if(is_string($type)) {
				$type = array($type);
			}
            $retblocks = array();
            foreach($this->data as $block) {
                if(in_array($block['type'],$type)) {
                    $retblocks[] = $block;
                }
            }
            return $retblocks;
        }
	}
}
?>
