<?php
include(dirname(__FILE__).'/../support/StringReader.php');

class AgentFile {
    private $reader;
    private $blocks=array();
    
    
    function AgentFile($str) {
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
    
    function GetBlocks() {
        return $this->blocks;
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
        /*switch($blockid) {
            case 'FILE':
            break;
                       
        }*/
        foreach($this->blocks as $blockid=>$blockarr) {
            switch($blockarr['Type']) {
                case 'AGNT':
                case 'DSAG':
                case 'EGG':
                    $this->ParseTagBlock($blockid);
                    break;
            }
        }
        return true;
    }
    
    function ParseTagBlock($blockid) {
        $block = $this->blocks[$blockid];
        $blockbinary = $this->reader->GetSubString($block['Start'],$block['Length']);
        if($block['Compression']) {
            $blockbinary = gzuncompress($blockbinary);
        }
        $blockreader = new StringReader($blockbinary);
        
        
        $numints = $blockreader->ReadInt(4);
        for($i=0;$i<$numints;$i++) {
            $namelen = $blockreader->ReadInt(4);
            $name = $blockreader->Read($namelen);
            $int = $blockreader->ReadInt(4);
            $block['Tags'][$name] = $int;
        }
        
        
        $numstrings = $blockreader->ReadInt(4);
        for($i=0;$i<$numstrings;$i++) {
            $namelen = $blockreader->ReadInt(4);
            $name = $blockreader->Read($namelen);
            $strlen = $blockreader->ReadInt(4);
            $string = $blockreader->Read($strlen);
            $block['Tags'][$name] = $string;
        }       
        $this->blocks[$blockid] = $block;
    }
    
}

?>