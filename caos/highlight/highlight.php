<?
include("caos/highlight/operators.php");
include("caos/highlight/flowcontrols.php");
include("caos/highlight/commands.php");
include("caos/highlight/variables.php");
include("caos/highlight/command_variables.php"); //could be a command, could be a variable, could be superman
function IndentCode($times) 
    {
    if($times>0)
    {
       $times *= 2; // to make the indent bigger. Ought to be personalisable.
       $indentation = '<span style="margin-left:'.$times.'em">';
    }
    return $indentation;
}
function HighlightCaos($caos)
{
    global $caosCommands;
    global $caosVariables;
    global $caosCommandVariables;
    global $caosOperators;
    global $caosFlowControls;
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
        $line = str_replace("\t"," ",$line);
        while(strpos($caos,"  ") !== false)
        { // get rid of all tabs and convert to spaces
            $line = str_replace("  "," ",$line);
        }
        $line = trim($line);
        $words = explode(" ",$line);
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
        $currentWord = 0;
        $lastWord = "";
        $newLine = IndentCode($indent).'';
        foreach($words as $word)
        {
            if($inString == false && $inByteString == false)
            {
                $lcword = strtolower($word);
                
                if($lastWord == "gsub" || $lastWord == "subr") {
                    $word = '<span class="label">'.$word.'</span>';
                } else
                if(in_array($lcword,$caosCommands))
                {
                    $word = '<span class="command">'.$word.'</span>';
                }
                else if(in_array($lcword,$caosVariables) || ereg("(va|ov|mv)[0-9][0-9]", $lcword))
                {
                    $word = '<span class="variable">'.$word.'</span>';
                }
                else if(in_array($lcword,$caosOperators))
                {
                    $word = '<span class="operator">'.$word.'</span>';
                }
                else if(in_array($lcword,$caosFlowControls))
                {
                    $word = '<span class="flowcontrol">'.$word.'</span>';
                }
                else if(in_array($lcword,$caosCommandVariables))
                {
                    if($currentWord == 0)
                    {
                        $word = '<span class="command">'.$word.'</span>';
                    }
                    else
                    {
                        $word = '<span class="variable">'.$word.'</span>';
                    }
                }
                else if($word{0} == '"')
                { //if it begins a string.
                    $word = '<span class="string">'.$word;
                    if($word{strlen($word)-1} == '"')
                    {
                    	$word .= '</span>'; //end the string
						$inString = false;
					}
					else
					{
						$inString = true;
					}
                }
                else if($word{0} == '[')
                {
                    $word = '<span class="bytestring">'.$word;
                    if($word{strlen($word)-1} == ']') {
                        $word .= '</span>';
                        $inByteString = false;
                    } else {
                        $inByteString = true;
                    }
                }
                else if(is_numeric($word))
                {
                    $word = '<span class="number">'.$word.'</span>';
                }
                else if($word{0} == '*')
                {
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
                }
                else
                {
                    $word = '<span class="error">'.$word.'</span>';
                }
                
            } 
            else if($inString)
            {
                if($word{strlen($word)-1} == '"')
                {
                    $word .= '</span>'; //end the string
                    $inString=false;
                }
            }
            else if($inByteString)
            {
                if($word{strlen($word)-1} == ']')
                {
                    $word .= '</span>'; //end the string
                    $inByteString=false;
                }
            }
            
            if($currentWord == sizeof($words)-1)
            {
                if($indent > 0)
                {
                    $newLine .= $word."</span><br />\n";
                }
                else
                {
                    $newLine .= $word."<br />\n";
                }
            }
            else
            {
                $newLine .= $word.' ';
            }
            $lastWord = $lcword;
            $currentWord++;
        } //end foreach words
        
        if($firstword == 'scrp' || $firstword=='rscr') 
        {
            $newLine = "<br />\n".$newLine;
            $indent = 1;
        }
        else if($firstword == 'iscr') 
        {
            $indent = 1;
        }
        else if($firstword=='doif' || $firstword == 'elif' || $firstword == 'else' || $firstword == 'inst' || $firstword == 'subr' || $firstword == 'loop' || $firstword == 'reps' || $firstword == 'etch' || $firstword == 'enum' || $firstword == 'esee' || $firstword == 'epas' || $firstword == 'econ') 
        {
            $indent++;
        }
                
        $returned .= $newLine; 
    }
    return $returned;
}
?>