<?php
/// @cond INTERNAL_DOCS

/// @brief C2 CAOS flow control (doif, else, loop, etc) dictionary
class C2CAOSFlowControls {
    /// @brief Returns an array of tokens.
    public static function GetTokens() {
        return array(
            'enum',
            'esee',
            'etch',
            'escn',
            'next',
            'nscn',
            'inst',
            'slow',
            'stop',
            'endm',
            'subr',
            'gsub',
            'retn',
            'reps',
            'repe',
            'loop',
            'untl',
            'ever',
            'doif',
            'else',
            'endi',
            'scrp',
            'iscr',
            'rscr'
        );
    }
}
/// @endcond
?>
