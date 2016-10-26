<?php
require_once(dirname(__FILE__).'/IReader.php');
/// @brief Wrapper class to make reading files easier.
/**
 * Quite often you'll have to make a FileReader in order to use other
 * read from a file. \n
 * This is a simple process, simply pass in the filename to FileReader()
 */
class FileReader implements IReader {

    /// @cond INTERNAL_DOCS

    private $fp;

    /// @endcond
    
    /// @brief Instantiates a filereader for the given file, ready
    /// for reading.
    /**
     * @param string $filename A full path to the file you want to open.
     */
    public function FileReader($filename)
    {
        if (!file_exists($filename))
            throw new Exception("File does not exist: ".$filename);
        if (!is_file($filename))
            throw new Exception("Target is not a file.");
        if (!is_readable($filename))
            throw new Exception("File exists, but is not readable.");

        $this->fp = fopen($filename, 'rb');
    }

    /**
     * @param integer $count
     */
    public function Read($count)
    {
        if ($count > 0) {
            return fread($this->fp, $count);
        }
        return '';
    }

    /**
     * @param integer $count
     */
    public function ReadInt($count)
    {
        $int = 0;
        for ($x = 0; $x < $count; $x++)
        {
            $buffer = (ord(fgetc($this->fp)) << ($x*8));
            if ($buffer === false)
                throw new Exception("Read failure");
            $int += $buffer;
        }
        return $int;
    }

    public function GetPosition()
    {
        return ftell($this->fp);
    }

    public function GetSubString($start, $length = FALSE)
    {
        $oldpos = ftell($this->fp);
        fseek($this->fp, $start);
        $data = '';
        if ($length === false) {
            while ($newdata = $this->Read(4096)) {
                if (strlen($newdata) == 0) {
                    break;
                }
                $data .= $newdata;
            }
        } else {
            $data = fread($this->fp, $length);
        }
        fseek($this->fp, $oldpos);
        return $data;
    }
    public function ReadCString() {
        $string = '';
        while (($char = $this->Read(1)) !== false) {
            if (ord($char) == 0) {
                break;
            }
            $string .= $char;
        }
        return $string;
    }
    public function Seek($position)
    {
        fseek($this->fp, $position);
    }

    public function Skip($count)
    {
        fseek($this->fp, $count, SEEK_CUR);
    }
}
?>
