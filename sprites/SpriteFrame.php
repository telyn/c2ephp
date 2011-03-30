<?php
/// @brief Class representing a single frame of a sprite.
abstract class SpriteFrame {

    /// @cond INTERNAL_DOCS
    
    private $decoded;
    protected $gdImage;
    private $width;
    private $height;

    /// @endcond

    /// @cond INTERNAL_DOCS
    
    /// @brief Initialises a SpriteFrame
    /**
     * Width and height must both be non-zero.
     * @see C16Frame::C16Frame
     */
    public function SpriteFrame($width,$height,$decoded=false) {
        if($width == 0) {
            throw new Exception('Zero width');
        } else if($height == 0) {
            throw new Exception('Zero height');
        }
        $this->width = $width;
        $this->height = $height;
        $this->decoded = $decoded;
    }

    protected function HasBeenDecoded() {
        return $this->decoded;  
    }

    /// @endcond

    /// @brief Gets the GD Image resource for this sprite frame.
    /// <strong> Deprecated</strong>
    /**
     *  @return a GD image resource. See http://php.net/image 
     */
    public function GetGDImage() {
        $this->EnsureDecoded();
        return $this->gdImage;
    }

    /// @brief Gets the width of the frame in pixels
    public function GetWidth() {
        return $this->width;
    }
    /// @brief Gets the height of the frame in pixels
    public function GetHeight() {
        return $this->height;
    }

    /// @brief Gets the colour of the pixel at the given position.
    /**
     * Returns an array like the following:
     * <pre> Array
     * ( 
     *    [red] => 226
     *    [green] => 222
     *    [blue] => 252
     *    [alpha] => 0 
     * )</pre>
     * @see http://php.net/imagecolorsforindex
     * @param $x The x-coordinate of the pixel to get.
     * @param $y The y-coordinate of the pixel to get.
     * @return An associative array containing the keys 'red','green','blue','alpha'.
     */
    public function GetPixel($x,$y) {
        $this->EnsureDecoded();
        $colori = imagecolorat($this->gdImage, $x, $y);
        $color = imagecolorsforindex($this->gdImage, $colori);
        return $color;
    }

    /// @brief Sets the colour of a pixel.
    /**
     * @param $x The x-coordinate of the pixel to change.
     * @param $y The y-coordinate of the pixel to change.
     * @param $r The red component of the pixel. 0-255.
     * @param $g The green component of the pixel. 0-255.
     * @param $b The blue component of the pixel. 0-255.
     */
    public function SetPixel($x,$y,$r,$g,$b) {
        $this->EnsureDecoded();
        imagesetpixel($this->gdImage, $x, $y, imagecolorallocate($this->gdImage, $r, $g, $b));
    }

    /// @cond INTERNAL_DOCS
    
    /// @brief Ensures that the SpriteFrame has been decoded
    /**
     * This causes $gdImage to point to a usable GD Image resource
     * if it does't already.
     */
    protected function EnsureDecoded() {
        if(!$this->decoded)
            $this->Decode();

        $this->decoded = true;
    }

    /// @endcond

    /// @cond INTERNAL_DOCS

    /// @brief Decodes the SpriteFrame and creates gdImage
    protected abstract function Decode();

    /// @brief Encodes the SpriteFrame and returns a binary string.
    public abstract function Encode();
    /// @endcond

    /// @brief Converts this SpriteFrame into one of another type.
    /**
     * This is called internally by SpriteFile, and is not really
     * for public use. A way of converting I'd approve of more is to
     * create a SpriteFile of the right type and then call AddFrame. 
     * @see SpriteFile AddFrame. 
     * @internal
     * If you create your own SpriteFrame and SpriteFile formats, and
     * they use names longer than 3 characters, you will need to 
     * override this function in your class to provide extra magic.
     * @endinternal
     * @param $type The type of SpriteFrame to convert this to.
     */
    public function ToSpriteFrame($type) {
        $this->EnsureDecoded();
        if(substr(get_class($this),0,3) == $type && substr(get_class($this),3) == 'Frame') {
            return $this;
        }
        switch($type) {
        case 'C16':
            return new C16Frame($this->GetGDImage());
        case 'S16':
            return new S16Frame($this->GetGDImage());
        case 'SPR':
            return new SPRFrame($this->GetGDImage());
        default:
            throw new Exception('Invalid sprite type '.$type.'.');
        }
    }

    /// @brief Converts this SpriteFrame into a PNG.
    /**
     * @return A binary string in PNG format, ready for output! :)
     */
    public function ToPNG() {
        $this->EnsureDecoded();
        ob_start();
        imagepng($this->GetGDImage());
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
}
?>
