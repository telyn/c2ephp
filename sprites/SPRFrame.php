<?php
require_once(dirname(__FILE__).'/SpriteFrame.php');
require_once(dirname(__FILE__).'/../support/IReader.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');
require_once(dirname(__FILE__).'/../support/StringReader.php');


/// @brief Class for a single frame stored in SPR format.
class SPRFrame extends SpriteFrame {

    /// @cond INTERNAL_DOCS
    
    private $offset;
    private $reader;
    public static $sprToRGB;

    /// @endcond

    /// @brief Instantiate a new SPRFrame
    /**
     * If you're creating your own SPRFrame.
     * @see http://php.net/image
     * @param $reader Either an IReader or a GD Image resource
     * @param $width Ignored when creating an SPRFrame from a GD image.
     * @param $height Ignored when creating an SPRFrame from a GD image.
     * @param $offset How far through the IReader the SPRFrame is. May not ever be used.
     */
    public function SPRFrame($reader, $width = 0, $height = 0, $offset = false) {
        if ($reader instanceof IReader) {
            $this->reader = $reader;
            parent::SpriteFrame($width, $height);
            if ($offset !== false) {
                $this->offset = $offset;
            } else {
                $this->offset = $this->reader->GetPosition();
            }

            //initialise palette if necessary.
            if (empty(self::$sprToRGB)) {
                $paletteReader = new FileReader(dirname(__FILE__).'/palette.dta');
                for ($i = 0; $i < 256; $i++) {
                    self::$sprToRGB[$i] = array('r'=>$paletteReader->ReadInt(1)*4, 'g'=>$paletteReader->ReadInt(1)*4, 'b'=>$paletteReader->ReadInt(1)*4);
                }
                unset($paletteReader);
            }
        } else if (get_resource_type($reader) == 'gd') {
            parent::SpriteFrame(imagesx($reader), imagesy($reader), true);
            $this->gdImage = $reader;
        } else {
            throw new Exception('$reader was not an IReader or a gd image.');
        }
    }


    /// @brief Flips the image on the y-axis.
    /**
     * This is really for automated use by C1 COBAgentBlocks, but
     * feel free to use it yourself, it's not going anywhere.
     */
    public function Flip() {
        if ($this->HasBeenDecoded()) {
            throw new Exception('Too late!');
            return;
        }
        $tempData = '';
        for ($i = ($this->GetHeight()-1); $i > -1; $i--) {
            $tempData .= $this->reader->GetSubString($this->offset+($this->GetWidth())*$i, ($this->GetWidth()));
        }
        $this->reader = new StringReader($tempData);
        $this->offset = 0;

    }


    /// @cond INTERNAL_DOCS

    /// @brief Decodes the SPRFrame into a gd image.
    protected function Decode() {    
        $image = imagecreatetruecolor($this->GetWidth(), $this->GetHeight());
        $this->reader->Seek($this->offset);
        for ($y = 0; $y < $this->GetHeight(); $y++)
        {
            for ($x = 0; $x < $this->GetWidth(); $x++)
            {
                $colour = self::$sprToRGB[$this->reader->ReadInt(1)];
                imagesetpixel($image, $x, $y, imagecolorallocate($image, $colour['r'], $colour['g'], $colour['b']));
            }
        }
        $this->gdImage = $image;
    }

    /// @endcond


    /// @brief Encodes the SPRFrame.
    /**
     * Called automatically by SPRFile::Compile() \n
     * Generally end-users won't want a single frame of SPR data,
     * so add it to an SPRFile and call SPRFile::Compile().
     * @return string binary string of SPR data.
     */
    public function Encode() {
        $data = '';
        for ($y = 0; $y < $this->GetHeight(); $y++) {
            for ($x = 0; $x < $this->GetWidth(); $x++) {
                $color = $this->GetPixel($x, $y);
                $data .= pack('C', $this->RGBToSPR($color['red'], $color['green'], $color['blue']));
            }
        }
        return $data;
    }

    /// @cond INTERNAL_DOCS

    /// @brief Chooses the nearest colour in the SPR pallette.
    /**
     * Runs in O(n) time.
     */

    private function RGBToSPR($r, $g, $b) {
        //start out with the maximum distance.
        $minDistance = ($r ^ 2)+($g ^ 2)+($b ^ 2);
        $minKey = 0;
        foreach (self::$sprToRGB as $key => $colour) {
            $distance = pow(($r-$colour['r']), 2)+pow(($g-$colour['g']), 2)+pow(($b-$colour['b']), 2);
            if ($distance == 0) {
                return $key;
            } else if ($distance < $minDistance) {
                $minKey = $key;
                $minDistance = $distance;
            }
        }
        return $key;
    }
    /// @endcond
}
?>
