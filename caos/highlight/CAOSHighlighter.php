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
	private $scriptLines = array();
	private $scriptSubroutines = array();
	private $highlightedLines = array();

	private $previousLineCode;
	private $currentLine;
	private $currentIndent;
	private $currentWord;

	public function CAOSHighlighter($format) {
		$this->scriptFormat = $format;
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
		if(strpos($script,"\r") !== false) {
			$script = str_replace("\r\n","\n",$script); //get rid of mac and windows newlines.
			$script = str_replace("\r","\n",$script);
		}
		//remove tabs and spaces before newlines.
		$script = str_replace(" \n","\n",$script);
		$script = str_replace("\t",'',$script);
		$script = $this->SmartRemoveMultipleSpaces($script);
		$this->scriptLines = explode("\n",$script);
		
		//now that we have the lines, we can make the list of subroutines.
		$this->ScanForSubroutines();
		$this->currentLine = 0;
		$this->highlightedLines = array();
		while(($line = $this->HighlightNextLine()) !== false) {
			
			$this->highlightedLines[] = $line;
		}
		return implode($this->highlightedLines);
	}
	public function SmartRemoveMultipleSpaces($text) {
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
	private function ScanForSubroutines() {
		//expects $scriptLines to be filled out
		foreach($this->scriptLines as $line) {
			$words = explode(' ',strtolower($line));
			if($words[0] == 'subr') {
				$this->scriptSubroutines[] = $words[1];
			}
		}	
	}
	private function HighlightNextLine() {
		if(sizeof($this->scriptLines) <= $this->currentLine) {
			return false;
		}
		$line = $this->scriptLines[$this->currentLine];
		//$line = $this->SmartRemoveMultipleSpaces($line);
		if(strlen($line) == 0 && $this->currentIndent > 0) {
			$highlightedLine = $this->CreateIndentForThisLine('')."\n";
			$this->currentLine++;
			return $highlightedLine;
		} else if(strlen($line) == 0) {
			$this->currentLine++;
			return '';
		}
		$words = explode(' ',$line);
		
		$this->SetIndentForThisLine($words[0]);

		$inString = false;
		$inByteString = false;
		$highlightedLine = '';
		$firstToken = '';
		
		//if last line is a comment and this line starts with scrp set last line's indent to 0 (remove whitespace at front)
		if(in_array($words[0],array('scrp','rscr'))) {
			if(!empty($this->scriptLines[$this->currentLine-1])) {
				if($this->scriptLines[$this->currentLine-1]{0} == '*') {
					$this->highlightedLines[$this->currentLine-1] = ltrim($this->highlightedLines[$this->currentLine-1]);
				}
			}
		} 
		
		for($this->currentWord=0;$this->currentWord<sizeof($words);$this->currentWord++) {
			
			$word = $words[$this->currentWord];
			$highlightedWord = $word;
			if($inString) {
				if($word{strlen($word)-1} == '"') {
					$highlightedWord = htmlentities($word).'</span>'; //end the string
					$inString=false;
				} else {
					$highlightedLine .= $word;
					continue;					
				}
			} else if($inByteString) {
				if($word{strlen($word)-1} == ']') {
					$highlightedWord = htmlentities($word).'</span>'; //end the string
					$inByteString=false;
				} else {
					$highlightedLine .= $word.' ';
					continue;
				}
			} else if($firstToken != '') {
				//sort out unquoted strings
				if($this->currentWord == 1) {
					if($firstToken == 'subr') {
						if(strlen($word) == 4 || $this->scriptFormat != FORMAT_C2) {
							$highlightedWord = '<span class="label">'.$word.'</span>';
						} else {
							$highlightedWord = '<span class="error">'.$word.'</span>';
						}
					} else if($firstToken == 'gsub') {
						if(in_array($word,$this->scriptSubroutines)) {
							//C3/DS allow for any length of subroutine name, C2 and C1 probably only allow 4-character names.
							if(in_array($this->scriptFormat,array(FORMAT_C3,FORMAT_DS)) || strlen($word) == 4) {
								$highlightedWord = '<span class="label">'.$word.'</span>';
							} else {
								$highlightedWord = '<span class="error">'.$word.'</span>';
							}
						} else {
							$highlightedWord = '<span class="error">'.$word.'</span>';
						}
					}
					if($this->scriptFormat == FORMAT_C2) {
						if(in_array(strtolower($firstToken),array('tokn','snde','sndc','sndl','sndq','plbs'))) {
							if(strlen($word) == 4) {
								$highlightedWord = '<span class="string">'.$word.'</span>';
							} else {
								$highlightedWord = '<span class="error">'.$word.'</span>';
							}
						}
					}
				} else if($this->currentWord == 2) {
					if($this->scriptFormat == 'C2') {
						if(preg_match('/^new: (scen|simp|cbtn|comp|vhcl|lift|bkbd|cbub)$/i',$firstToken)) {
							if(strlen($word) == 4) {
								$highlightedWord = '<span class="string">'.$word.'</span>';
							} else {
								$highlightedWord = '<span class="error">'.$word.'</span>';
							}
						}
					}
				} else if($this->currentWord == sizeof($words)-1) {
					if($this->scriptFormat == 'C2') {
						if(strtolower($firstToken) == 'rmsc') {
							if(strlen($word) == 4) {
								$highlightedWord = '<span class="string">'.$word.'</span>';
							} else {
								$highlightedWord = '<span class="error">'.$word.'</span>';
							}
						}
					}
				}
			}
			if($highlightedWord == $word) {
				$highlightedWord = $this->TryToHighlightToken($word);
				if($this->currentWord == 0) {
					$firstToken = $word;
				}
				//Highlight two-word block.
				if($highlightedWord == $word && $this->currentWord < sizeof($words)-1) {
					$wordPair = $word.' '.$words[$this->currentWord+1];
					$highlightedWord = $this->TryToHighlightToken($wordPair);
					if($highlightedWord != $wordPair) {
						if($this->currentWord == 0) {
							$firstToken = $wordPair;
						}
						$this->currentWord++;
					} else {
						$highlightedWord = $word;
					}
				}
				if($highlightedWord == $word) { //invalid caos command
					if($word{0} == '"' && $this->scriptFormat != FORMAT_C2) { //if it begins a string. (C2 has no strings)
						$highlightedWord = '<span class="string">'.htmlentities($word);
						if($word{strlen($word)-1} == '"') {
							$highlightedWord .= '</span>'; //end the string
							$inString = false;
						} else {
							$inString = true;
						}
					} else if($word{0} == '[') { //begins a bytestring
						$highlightedWord = '<span class="bytestring">'.htmlentities($word);
						if($this->scriptFormat == 'C2') {
							//c2 bytestrings are part of the original term, on they're own they're wrong!
							$highlightedWord = '<span class="error">'.htmlentities($word);
						}
						if($word{strlen($word)-1} == ']') {
							$highlightedWord .= '</span>';
							$inByteString = false;
						} else {
							$inByteString = true;
						}
					} else if(is_numeric($word)) {
						$highlightedWord = '<span class="number">'.htmlentities($word).'</span>';
					} else if($word{0} == '*') { // because of SmartRemoveMultipleSpaces, prints exactly as written :)
						$highlightedWord = '<span class="comment">';
						for($i=$this->currentWord;$i<sizeof($words);$i++)
						{
							if($i!=$this->currentWord)
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
		$matches; //used for C2 anim command preg_match
		
		//first position commands + flow controls only
		//2nd position commands + command variables + variables only.
		if(in_array($lcword,$this->caosCommands)) {
			$word = '<span class="command">'.htmlentities($word).'</span>';
		} else if(in_array($lcword,$this->caosVariables)) {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
			//vaXX, ovXX
		} else if(in_array($this->scriptFormat,array('C2','C3','DS')) && preg_match("/^(va|ov)[0-9]{2}$/", $lcword)) {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
			//mvXX
		} else if(in_array($this->scriptFormat,array('C3','DS')) && preg_match('/^(mv)[0-9]{2}$/',$lcword)) {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
			//obvX
		} else if(in_array($this->scriptFormat,array('C1','C2')) && preg_match('/^(obv)[0-9]$/',$lcword)) {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
		} else if($this->scriptFormat == 'C2' && preg_match('/^([Aa][Nn][Ii][Mm]|[Pp][Rr][Ll][Dd])(\[[0-9]+R?\])$/',$word,$matches)) {
			$word = '<span class="variable">'.strtolower($matches[1]).'</span><span class="bytestring">'.$matches[2].'</span>';
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
	//These may well not apply to all versions of CAOS! I haven't thoroughly looked over the docs.
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
			case 'elif': //doesn't exist in c2, but we still format it to improve readability.
						// if someone has used elif in c2 code it will still be tagged as an error :)
			case 'else':
			case 'endi':
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