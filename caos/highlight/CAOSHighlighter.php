<?php

define('FORMAT_C1','C1');
define('FORMAT_C2','C2');
define('FORMAT_C3','C3');
define('FORMAT_DS','DS');

class CAOSHighlighter {
	private $caosCommands = array();
	private $caosVariables = array();
	private $caosCommandVariables = array();
	private $caosOperators = array();
	private $caosFlowControls = array();

	private $scriptFormat;
	private $scriptLines;
	private $highlightedLines;

	private $previousLineCode;
	private $currentLine;
	private $currentIndent;
	private $currentWord;

	public function CAOSHighlighter($format) {
		$this->format = $format;
		//load files...
		require_once(dirname(__FILE__).'/'.$format.'/CommandVariables.php');
		require_once(dirname(__FILE__).'/'.$format.'/Commands.php');
		require_once(dirname(__FILE__).'/'.$format.'/Variables.php');
		require_once(dirname(__FILE__).'/'.$format.'/FlowControls.php');
		require_once(dirname(__FILE__).'/'.$format.'/Operators.php');

		//put into arrays
		$this->caosCommandVariables = call_user_func(array($format.'CAOSCommandVariables','GetTokens'));
		$this->caosCommands = call_user_func(array($format.'CAOSCommands','GetTokens'));
		$this->caosVariables = call_user_func(array($format.'CAOSVariables','GetTokens'));
		$this->caosOperators = call_user_func(array($format.'CAOSOperators','GetTokens'));
		$this->caosFlowControls = call_user_func(array($format.'CAOSFlowControls','GetTokens'));
	}
	
	public function HighlightScript($script) {
		if(strpos("\r",$script) !== false) {
			$script = str_replace("\r","\n",$script); //get rid of mac and windows newlines.
			$script = str_replace("\n\n","\n",$script);
		}
		$script = str_replace("\t",' ',$script);
		$script = $this->SmartRemoveMultipleSpaces($script);
		$this->scriptLines = explode("\n",$script);
		$this->currentLine = 0;
		$this->highlightedLines = array();
		while($line = $this->HighlightNextLine()) {
			
			$this->highlightedLines[] = $line;
		}
		return implode($this->highlightedLines);
	}
	//TODO: Get rid of double spaces between CAOS commands but not in strings!
	private function SmartRemoveMultipleSpaces($text) {
		$newString = array();
		$inString = false;
		$inComment = false;
		for($i=0;$i<strlen($text);$i++) {
			$character = $text{$i};
			if($character == '"') {
				$inString = !$inString;
			} else if($character == '*') {
				$inComment = true;
			} else if($character == "\n" ) {
				$inComment = false;
			} else if(!$inString && !$inComment && $character == ' ') {
			
				while($i+2 < strlen($text) && $text{$i+1} == ' ') {
					$i++;
				}
			}
			$newString[] = $character;
		}
		return trim(implode('',$newString));
	}
	private function HighlightNextLine() {
		if(sizeof($this->scriptLines) <= $this->currentLine) {
			return false;
		}
		$line = $this->scriptLines[$this->currentLine];
		$line = $this->SmartRemoveMultipleSpaces($line);
		if(strlen($line) == 0 && $this->currentIndent > 0) {
			$highlightedLine = $this->CreateIndentForThisLine('')."\n";
			$this->currentLine++;
			return $highlightedLine;
		} else if(strlen($line) == 0) {
			return '';
		}
		$words = explode(' ',$line);
		
		$this->SetIndentForThisLine($words[0]);

		$inString = false;
		$inByteString = false;
		$highlightedLine = '';
		
		//if last line is a comment and this line starts with scrp set last line's indent to 0 (remove whitespace at front)
		if(in_array($words[0],array('scrp','rscr'))) {
			if(!empty($this->scritLines[$this->currentLine-1])) {
				if($this->scriptLines[$this->currentLine-1]{0} == '*') {
					$this->highlightedLines[$this->currentLine-1] = ltrim($this->highlightedLines[$this->currentLine-1]);
				}
			}
		} 
		
		for($currentWord=0;$currentWord<sizeof($words);$currentWord++) {
			
			$word = $words[$currentWord];
			$highlightedWord = '';
			if($inString) {
				if($word{strlen($word)-1} == '"') {
					$highlightedWord = htmlentities($word).'</span>'; //end the string
					$inString=false;
				} else {
					$highlightedWord = $word;
				}
			} else if($inByteString) {
				if($word{strlen($word)-1} == ']') {
					$highlightedWord = htmlentities($word).'</span>'; //end the string
					$inByteString=false;
				} else {
					$highlightedWord = $word;
				}
			} else {
				$highlightedWord = $this->TryToHighlightToken($word);
				//Highlight two-word block.
				if($highlightedWord == $word && $currentWord < sizeof($words)-1) {
					$wordPair = $word.' '.$words[$currentWord+1];
					$highlightedWord = $this->TryToHighlightToken($wordPair);
					if($highlightedWord != $wordPair) {
						$currentWord++;
					} else {
						$highlightedWord = $word;
					}
				}
				if($highlightedWord == $word) { //invalid caos command
					if($word{0} == '"') { //if it begins a string.
						$highlightedWord = '<span class="string">'.htmlentities($word);
						if($word{strlen($word)-1} == '"') {
							$highlightedWord .= '</span>'; //end the string
							$inString = false;
						} else {
							$inString = true;
						}
					} else if($word{0} == '[') { //begins a bytestring
						$highlightedWord = '<span class="bytestring">'.htmlentities($word);
						if($word{strlen($word)-1} == ']') {
							$word .= '</span>';
							$inByteString = false;
						} else {
							$inByteString = true;
						}
					} else if(is_numeric($word)) {
						$highlightedWord = '<span class="number">'.htmlentities($word).'</span>';
					} else if($word{0} == '*') { // because of SmartRemoveMultipleSpaces, prints exactly as written :)
						$highlightedWord = '<span class="comment">';
						for($i=$currentWord;$i<sizeof($words);$i++)
						{
							if($i!=$currentWord)
							{
								$highlightedWord.=' ';
							}
							$highlightedWord.= htmlentities($words[$i]);
						}
						$highlightedWord .= '</span>';
						$highlightedLine .= $highlightedWord;
						break;
					} else { //Well, I don't get it :)
						$highlightedWord = '<span class="error">'.htmlentities($word).'</span>';
					}
				}
					
			} // end else
			$highlightedLine .= $highlightedWord.' ';
		}
		$highlightedLine = $this->CreateIndentForThisLine($words[0]).$highlightedLine."\n".$this->SetIndentForNextLine($words[0]);
		$this->currentLine++;
		return $highlightedLine;
	}
	
	private function TryToHighlightToken($word) {
		$lcword = strtolower($word);
		if(in_array($lcword,$this->caosCommands)) {
			$word = '<span class="command">'.htmlentities($word).'</span>';
		} else if(in_array($lcword,$this->caosVariables) || preg_match("/^(va|ov|mv)[0-9]{2}$/", $lcword)) {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
		} else if(in_array($lcword,$this->caosOperators)) {
			$word = '<span class="operator">'.htmlentities($word).'</span>';
		} else if(in_array($lcword,$this->caosFlowControls)) {
			$word = '<span class="flowcontrol">'.htmlentities($word).'</span>';
		} else if(in_array($lcword,$this->caosCommandVariables)) {
			if($this->currentWord == 0) {
				$word = '<span class="command">'.htmlentities($word).'</span>';
			} else {
				$word = '<span class="variable">'.htmlentities($word).'</span>';
			}
		}
		return $word;
	}
	
	private function SetIndentForThisLine($firstword) {
		switch($firstword) {
			case 'scrp':
			case 'endm':
			case 'rscr':
				$this->currentIndent = 0;
				break;
			case 'retn':
			case 'subr':
				$this->currentIndent = 1;
				break;
			case 'elif':
			case 'else':
			case 'endi':
			case 'retn':
			case 'untl':
			case 'next':
			case 'ever':
			case 'repe';
				$this->currentIndent--;
			break;
		}
	}
	private function SetIndentForNextLine($firstword) {
		switch($firstword) {
			case 'scrp':
			case 'rscr':
			case 'iscr':
				$this->currentIndent = 0;
			case 'doif':
			case 'elif':
			case 'else':
			case 'inst':
			case 'subr':
			case 'loop':
			case 'reps':
			case 'etch':
			case 'enum':
			case 'esee':
			case 'epas':
			case 'econ':
				$this->currentIndent++;
				break;
			case 'endm':
				return "\n";
		}
	}
	
	private function CreateIndentForThisLine($firstword) {
		$indent = '';
		if(in_array($firstword,array('scrp','rscr'))) {
			if(!empty($this->previousLineCode)) {
				if($this->previousLineCode{0} != '*') {
					$indent = "\n";
				}
			}
		}
		for($i=0;$i<$this->currentIndent;$i++) {
			$indent .= "\t";
		}
		return $indent;
	}
}

?>