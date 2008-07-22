<?php
include(dirname(__FILE__).'/../support/StringReader.php');

class Agent {
    private $reader;
    private $blocks=array();
    
    
    function Agent($str) {
        $this->reader = new StringReader($str);
        $this->blocks = array();
    }
    
    
    function Parse() {
        if($this->ParseHeader()) {
            while($this->ParseBlockHeader()) {
            }
            return TRUE;
        } else {
            echo "Failed at block header: NOT A PRAY FILE";
            return FALSE;
        }
    }
    
    function GetBlocks($type=FALSE) { //gets all blocks or one type of block
        if(!$type) {
            return $this->blocks;
        } else {
            $retblocks = array();
            foreach($this->blocks as $block) {
                if($block['Type'] == $type) {
                    $retblocks[] = $block;
                }
            }
            return $retblocks;
        }
    }
    
    function ParseHeader() {
        if($this->reader->Read(4) == "PRAY") {
            return true;
        } else {
            return false;
        }
    }

    function ParseBlockHeader() {
        $blockid = $this->reader->Read(4);
        if($blockid=="") {
            return false;
        }
        $name = trim($this->reader->Read(128));
        if($name=="") {
            return false;
        }
        $length = $this->reader->ReadInt(4);
        if($length===false) {
            return false;
        }
        $fulllength = $this->reader->ReadInt(4); //full means uncompressed
        if($fulllength===false) {
            return false;
        }
        $flags = $this->reader->ReadInt(4);
        if($flags===false) {
            return false;
        }
        $compression = false;
        if($flags & 1 == 1) {
            $compression=true;
        }
        $this->blocks[] = array('Type'=>$blockid,'Name'=>$name,'Length'=>$length,'FullLength'=>$fulllength,'Compression'=>(int)$compression,'Start'=>$this->reader->GetPosition());
        $this->reader->Read($length);
        
        foreach($this->blocks as $blockid=>$blockArray) {
            switch($blockArray['Type']) {
                case 'AGNT':
                case 'DSAG':
                case 'LIVE':
                case 'EGG':
                case 'DFAM':
                case 'SFAM':
                case 'EXPC':
                case 'DSEX':
                    $this->ParseTagBlock($blockid);
                    break;
                default:
                    $content = $this->reader->GetSubString($this->blocks[$blockid]['Start'],$this->blocks[$blockid]['Length']);
                    if($this->blocks[$blockid]['Compression']) {
                        $content = gzuncompress($content);
                    }
                    $this->blocks[$blockid]['Content'] = $content;
            }
        }
        return true;
    }
    
    function ParseTagBlock($blockid) {
        $block = $this->blocks[$blockid];
        $blockBinary = $this->reader->GetSubString($block['Start'],$block['Length']);
        if($block['Compression']) {
            $blockBinary = gzuncompress($blockBinary);
        }
        $blockReader = new StringReader($blockBinary);
        
        
        $numInts = $blockReader->ReadInt(4);
        for($i=0;$i<$numInts;$i++) {
            $nameLength = $blockReader->ReadInt(4);
            $name = $blockReader->Read($nameLength);
            $int = $blockReader->ReadInt(4);
            $block['Tags'][$name] = $int;
        }
        
        
        $numStrings = $blockReader->ReadInt(4);
        for($i=0;$i<$numStrings;$i++) {
            $nameLength = $blockReader->ReadInt(4);
            $name = $blockReader->Read($nameLength);
            $stringLength = $blockReader->ReadInt(4);
            $string = $blockReader->Read($stringLength);
            $block['Tags'][$name] = $string;
        }       
        $this->blocks[$blockid] = $block;
    }
}

?>