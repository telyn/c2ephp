<?php
class StringReader {
    private $position;
    private $string;
    public function StringReader($string) { // string can be a file pointer resource or a string.
        $this->string = $string;
    }
    public function Read($characters) {
        if($characters > 0) {
            if(is_resource($this->string)) {
                fseek($this->string,$this->position);
                $str = fgets($this->string,$characters+1);
            } else {
                if($this->position+$characters > strlen($this->string)) {
                    return "";
                }
                $str = substr($this->string,$this->position,$characters);
            }
            $this->position += $characters;
            return $str;
        }
        return "";
    }
    public function ReadInt($characters) {
        return BytesToIntLilEnd($this->Read($characters));
    }
    public function GetPosition() {
        return $this->position;
    }
    public function GetSubString($start=0,$length = FALSE) {
        if(is_string($this->string)) {
            if($length == FALSE) {
                $length = strlen($this->string)-$start;
            }
            $str = substr($this->string,$start,$length);         
        } else {
            fseek($this->string,$start);
            $str = fgets($this->string,$length);
        }
        return $str;
    }
}

function BytesToIntLilEnd($string) { //little endian
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