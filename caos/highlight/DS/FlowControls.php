<?php
/** DS CAOS flow control (doif, else, loop, etc) dictionary */
class DSCAOSFlowControls {
	public static function GetTokens() {
		return array(
			'doif',
			'econ',
			'elif',
			'else',
			'enum',
			'endi',
			'endm',
			'epas',
			'esee',
			'etch',
			'ever',
			'goto',
			'gsub',
			'inst',
			'iscr',
			'loop',
			'next',
			'over', //wait until current agent anim is over...sounds like a flow control to me.
			'repe',
			'reps',
			'retn',
			'rscr',
			'scrp',
			'slow',
			'subr',
			'untl',
		);
	}
}
?>
