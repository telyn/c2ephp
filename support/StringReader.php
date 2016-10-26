<?php
require_once(dirname(__FILE__).'/IReader.php');
/// @brief Class to read from strings in the same way as files
/**
 * This is a convenience class to allow strings to be
 * treated in the same way as files. You won't <i>generally</i>
 * have to create these yourself, but if you want to, go ahead.
 */
class StringReader implements IReader {

    /// @cond INTERNAL_DOCS
    
    private $position;
    private $string;
    /// @endcond

    /// @brief Creates a new StringReader for the given string.
    /**
     * Initialises the position to 0.
     */
    public function StringReader($string) {
        $this->string = $string;
        $this->position = 0;
    }
    public function Read($characters) {
        if ($characters > 0) {
            if ($this->position+$characters > strlen($this->string)) {
                return false;
            }
            $str = substr($this->string, $this->position, $characters);

            $this->position += $characters;
            return $str;
        }
        return "";
    }
    public function ReadCString() {
        $string = '';
        while (($char = $this->Read(1)) !== false) {
            $string .= $char;
            if ($char == "\0") {
                break;
            }           
        }
        return substr($string, 0, -1);
    }
    public function Seek($position) {
        $this->position = $position;
    }
    public function Skip($count) {
        $this->position += $count;
    }

    /**
     * @param integer $characters
     */
    public function ReadInt($characters) {
        return BytesToIntLilEnd($this->Read($characters));
    }
    public function GetPosition() {
        return $this->position;
    }

    /**
     * @param integer $start
     */
    public function GetSubString($start, $length = FALSE) {
        if ($length == FALSE) {
            $length = strlen($this->string)-$start;
        }
        $str = substr($this->string, $start, $length);         
        return $str;
    }
}

/**
 * @param false|string $string
 */
function BytesToIntLilEnd($string) { //little endian
    if ($string == "") {
        return false;
    }
    $length = strlen($string);
    $int = 0;
    for ($i = 0; $i < $length; $i++) {
        $int += ord($string{$i}) << ($i*8);
    }
    return $int;
}
?>
