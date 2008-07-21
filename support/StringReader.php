<?php
class StringReader {
    private $position;
    private $string;
    
    public function StringReader($string) {
        $this->string = $string;
    }
    public function Read($characters) {
        if($characters > 0) {
            if($this->position+$characters > strlen($this->string)) {
                return "";
            }
            $str = substr($this->string,$this->position,$characters);
            $this->position += $characters;
            return $str;
        }
        return "";
    }
    public function ReadInt($characters) {
        return BytesToInt($this->Read($characters));
    }
    public function GetPosition() {
        return $this->position;
    }
    public function GetSubString($start=0,$length = FALSE) {
        if($length == FALSE) {
            $length = strlen($this->string)-$start;
        }
        return substr($this->string,$start,$length);
    }
}

function BytesToInt($string) {
        if($string == "") {
            return false;
        }
        $bytes = str_split($string);
        $length = strlen($string);
        $int = 0;
        for($i=0;$i<$length;$i++) {
            $int += ord($bytes[$i])<<($i*8);
        }
        return $int;
    }
?>