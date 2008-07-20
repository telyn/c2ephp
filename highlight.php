<?
//doif elif else endi scrp endm inst? slow?
$caosCommands = array("lol");
$caosVariables = array("hah");
$caosCommandVariables = array("pfft"); //could be a command, could be a variable, could be superman
function HighlightCaos($caos)
{
    global $caosCommands;
    global $caosVariables;
    global $caosCommandVariables;
    $caos = strtolower($caos);
    $caos = str_replace("\r","\n",$caos); //get rid of DUMB MAC&WINDOWS NEWLINES HURR
    while(strpos($caos,"\n\n") !== false)
    {
        $caos = str_replace("\n\n","\n",$caos);
    }
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
        
        $inString = false;
        $currentWord = 0;
        $newLine = '';
        foreach($words as $word)
        {
            if(!$inString)
            { 
                if(in_array($word,$caosCommands))
                {
                    $word = '<span class="command">'.$word.'</span>';
                }
                else if(in_array($word,$caosVariables)) {
                    $word = '<span class="variable">'.$word.'</span>';
                }
                else if(in_array($word,$caosCommandVariables))
                {
                    if($currentWord == 0)
                    {
                        $word = '<span class="command">'.$word.'</span>';
                    } else
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
						$inString=false;
					}
					else
					{
						$inString=true;
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
            } else
            {
                if($word{strlen($word)-1} == '"')
                {
                    $word .= '</span>'; //end the string
                    $inString=false;
                }
            }
            if($currentWord == sizeof($words)-1)
            {
                $newLine .= $word."<br />\n";
            }
            else
            {
                $newLine .= $word.' ';
            }
            $currentWord++;
        }
        $returned .= $newLine; 
    }
    return $returned;
}
?>