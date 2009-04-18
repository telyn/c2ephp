<?php
require_once(dirname(__FILE__).'/IReader.php');
class StringReader implements IReader {
    private $position;
    private $string;
    public function StringReader($string) { // string can be a file pointer resource or a string.
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
    public function Seek($position) {
        $this->position = $position;
    }
    public function Skip($count) {
        $this->position += $characters;
    }
    public function ReadInt($characters) {
        return BytesToIntLilEnd($this->Read($characters));
    }
    public function GetPosition() {
        return $this->position;
    }
    public function GetSubString($start,$length = FALSE) {
        if($length == FALSE) {
            $length = strlen($this->string)-$start;
        }
        $str = substr($this->string,$start,$length);         
        return $str;
    }
}

function BytesToIntLilEnd($string) { //little endian
    if($string == "") {
        return false;
    }
    $length = strlen($string);
    $int = 0;
    for($i=0;$i<$length;$i++) {
        $int += ord($string{$i})<<($i*8);
    }
    return $int;
}
?>