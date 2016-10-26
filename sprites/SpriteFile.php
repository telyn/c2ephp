<?php

/// @brief Superclass for all SpriteFile types
abstract class SpriteFile {

    /// @cond INTERNAL_DOCS
    
    private $frames = array();
    private $spritefiletype;
    /// @endcond

    /// @cond INTERNAL_DOCS
  
    /**
     * @param string $filetype
     */
    public function SpriteFile($filetype) {
    $this->spritefiletype = $filetype;
    }
    /// @endcond

    /// @brief Gets a SpriteFrame from the SpriteFile.
    /**
     * @param $frame The 0-based index of the frame to get.
     * @return A SpriteFrame
     */
    public function GetFrame($frame) {
        return $this->frames[$frame];
    }
    /// @brief Gets the entire frame array.
    /**
     * @return An array of SpriteFrames
     */
    public function GetFrames() {
        return $this->frames;
    }

    /// @brief Compiles the SpriteFile into a binary string
    /**
     * @return A binary string containing the SpriteFile's data and frames.
     */
    public abstract function Compile();

    /// @brief Adds a SpriteFrame to the SpriteFile
    /**
     * If necessary, this function converts the SpriteFrame to the
     * correct format.
     * At the moment, this can only add a SpriteFrame to the end of the
     * SpriteFile. TODO: I aim to fix this by the CCSF 2011.
     * @internal
     * This process uses some magic which require all types of
     * SpriteFile and SpriteFrame to use 3-character identifiers.
     * This means that if you want to make your own sprite formats
     * you'll need to override this function and provide all our magic
     * plus your own.
     * @endinternal
     * @param $frame A SpriteFrame
     * @param $position Where to put the frame. Currently un-used.
     */
    public function AddFrame(SpriteFrame $frame, $position = false) {
        /*
      if($position === false) {
          $position = sizeof($this->frames);
      } else if($position < 0) {
          $position = sizeof($this->frames) - $position;
      }
      */
        if ($this->spritefiletype == substr(get_class($frame), 0, 3)) {
            //$this->frames[$position] = $frame;
            $this->frames[] = $frame;
        } else {
            //$this->frames[$position] = $frame->ToSpriteFrame($this->spritefiletype);
            $this->frames[] = $frame->ToSpriteFrame($this->spritefiletype);
        }
    }
    /// @brief Replaces a frame in the SpriteFile
    /**
     * Replaces the frame in the given position
     * Uses the same magic as AddFrame
     * @param $frame A SpriteFrame of any type.
     * @param $position Which frame to replace. If negative, counts
     * backwards from the end of the frames array.
     */
    public function ReplaceFrame(SpriteFrame $frame, $position) {
        if ($position < 0) {
            $position = sizeof($this->frames)-$position;
        }
        $this->frames[$position] = $frame->ToSpriteFrame($this->spritefiletype);
    }
    /// @brief Gets the number of frames currently stored in this SpriteFile.
    /**
     * @return The number of frames
     */
    public function GetFrameCount() {
        return sizeof($this->frames);
    }
    /// @brief Deletes the frame in the given position.
    /**
     * @param $frame The 0-based index of the frame to delete.
     */
    public function DeleteFrame($frame) {
        unset($this->frames[$frame]);
    }
    /// @brief Converts the given frame to PNG. <strong>Deprecated.</strong>
    /**
     * May be removed in a future release.
     * Use GetFrame($frame)->ToPNG() instead.
     * @param integer $frame The 0-based index of the frame to delete.
     * @return A binary string containing a PNG.
     */
    public function ToPNG($frame) {
        return $this->frames[$frame]->ToPNG();
    }
}
?>
