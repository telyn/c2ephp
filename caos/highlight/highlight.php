<?
include(dirname(__FILE__).'/C3/operators.php');
include(dirname(__FILE__).'/C3/flowcontrols.php');
include(dirname(__FILE__).'/C3/commands.php');
include(dirname(__FILE__).'/C3/variables.php');
include(dirname(__FILE__).'/C3/command_variables.php'); //could be a command, could be a variable, could be superman

function IndentCode($times) {
	$indentation = '';
    if($times>0)
    {
       $times *= 2; // to make the indent bigger. Ought to be personalisable.
       $indentation = '<span style="margin-left:'.$times.'em">';
    }
    return $indentation;
}
function TryToHighlightWord(&$word,$currentWord) {
	global $caosCommands;
    global $caosVariables;
    global $caosCommandVariables;
    global $caosOperators;
    global $caosFlowControls;
    
	$lcword = strtolower($word);
	if(in_array($lcword,$caosCommands)) {
		$word = '<span class="command">'.htmlentities($word).'</span>';
		return true;
	} else if(in_array($lcword,$caosVariables) || ereg("^(va|ov|mv)[0-9][0-9]$", $lcword)) {
		$word = '<span class="variable">'.htmlentities($word).'</span>';
		return true;
	} else if(in_array($lcword,$caosOperators)) {
		$word = '<span class="operator">'.htmlentities($word).'</span>';
		return true;
	} else if(in_array($lcword,$caosFlowControls)) {
		$word = '<span class="flowcontrol">'.htmlentities($word).'</span>';
		return true;
	} else if(in_array($lcword,$caosCommandVariables)) {
		if($currentWord == 0) {
			$word = '<span class="command">'.htmlentities($word).'</span>';
		} else {
			$word = '<span class="variable">'.htmlentities($word).'</span>';
		}
		return true;
	}
	return false;
}
function HighlightCaos($caos) {
    $caos = str_replace("\r","\n",$caos); //get rid of DUMB MAC&WINDOWS NEWLINES HURR
    while(strpos($caos,"\n\n") !== false)
    {
        $caos = str_replace("\n\n","\n",$caos);
    }
    $indent = 0;
    $lines = explode("\n",$caos);
    $returned='';
    foreach($lines as $line)
    {
        $line = str_replace("\t",' ',$line);
        while(strpos($caos,'  ') !== false)
        { // get rid of all tabs and convert to spaces
            $line = str_replace('  ',' ',$line);
        }
        $line = trim($line);
        $words = explode(' ',$line);
        $firstword = $words[0];
        if($firstword == 'scrp' || $firstword=='endm' || $firstword == 'rscr')
        {
            $indent = 0;
        }
        else if($firstword == 'retn' || $firstword == 'subr')
        {
            $indent = 1;
        }
        else if($firstword == 'elif' || $firstword == 'else' || $firstword == 'endi' || $firstword == 'retn' || $firstword == 'untl' || $firstword == 'next' || $firstword == 'ever' || $firstword == 'repe')
        {
            $indent--;
        }
        
        $inString = false;
        $inByteString = false; //byte strings are [] and mostly used with anim
        $lastWord = "";
        $newLine = IndentCode($indent).'';
        for($currentWord=0;$currentWord<sizeof($words);$currentWord++) {
        	$word = $words[$currentWord];
        	if($inString) {
                if($word{strlen($word)-1} == '"') {
                    $word .= '</span>'; //end the string
                    $inString=false;
                }
            } else if($inByteString) {
                if($word{strlen($word)-1} == ']') {
                    $word .= '</span>'; //end the string
                    $inByteString=false;
                }
            } else {
                if($lastWord == "gsub" || $lastWord == "subr") {
                    $word = '<span class="label">'.htmlentities($word).'</span>';
                } else if(!TryToHighlightWord($word,$currentWord)) {
                	//TryToHighlightWord modifies $word in place...
                	$twoword = $word.' '.$words[$currentWord+1];
                	if(TryToHighlightWord($twoword,$currentWord)) {
                		$word = $twoword;
	                	$currentWord++;
                	} else if($word{0} == '"') { //if it begins a string.
	                    $word = '<span class="string">'.$word;
    	                if($word{strlen($word)-1} == '"') {
        	            	$word .= '</span>'; //end the string
							$inString = false;
						} else {
							$inString = true;
						}
    	            } else if($word{0} == '[') {
        	            $word = '<span class="bytestring">'.$word;
            	        if($word{strlen($word)-1} == ']') {
                	        $word .= '</span>';
                    	    $inByteString = false;
	                    } else {
    	                    $inByteString = true;
        	            }
            	    } else if(is_numeric($word)) {
                	    $word = '<span class="number">'.htmlentities($word).'</span>';
                	} else if($word{0} == '*') {
                    	$word = '<span class="comment">';
	                    for($i=$currentWord;$i<sizeof($words);$i++)
    	                {
        	                if($i!=$currentWord)
            	            {
                	            $word.=' ';
                    	    }
                        	$word.=$words[$i];                  
	                    }
    	                $word .= '</span>';
        	            $newLine .= $word."<br />\n";
            	        break;
                	} else {
                    	$word = '<span class="error">'.htmlentities($word).'</span>';
                	}
                
	            }
            }
            
            if($currentWord == sizeof($words)-1) {
                if($indent > 0) {
                    $newLine .= $word."</span><br />\n";
                } else {
                    $newLine .= $word."<br />\n";
                }
            } else {
                $newLine .= $word.' ';
            }
            $lastWord = strtolower($words[$currentWord]);
        } //end foreach words
        
        if($firstword == 'scrp' || $firstword=='rscr') {
        	$newLine = "<br />\n".$newLine;
            $indent = 1;
        } else if($firstword == 'iscr') {
            $indent = 1;
        } else if($firstword=='doif' || $firstword == 'elif' || $firstword == 'else' || $firstword == 'inst' || $firstword == 'subr' || $firstword == 'loop' || $firstword == 'reps' || $firstword == 'etch' || $firstword == 'enum' || $firstword == 'esee' || $firstword == 'epas' || $firstword == 'econ') {
            $indent++;
        }
                
        $returned .= $newLine; 
    }
    return $returned;
}
?>
