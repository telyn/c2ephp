<?php
include(dirname(__FILE__).'/agent.php');

function ExtractCompleteAgent($agent) {
    $agent = new AgentFile(file_get_contents($agent));
    $agent->Parse();
    $blocks = $agent->GetBlocks();
    foreach($blocks as $block) {
        switch($block['Type']) {
            case 'FILE':
                $handle = fopen('./'.$block['Name'],'w');
                fwrite($handle,$block['Content']);
                fclose($handle);
                break;
                
            case 'AGNT':
            case 'DSAG':
                $handle = fopen($block['Name'].'.'.$block['Type'].'.pray.txt','w');
                fwrite($handle,'"en-GB"'."\r\n\r\n".'group '.$block['Type'].' "'.$block['Name']."\"\r\n");
                foreach($block['Tags'] as $name=>$value) {
                    
                    if($name == "Script 1") {
                        
                        $cosfile = $block['Name'].'.'.$block['Type'].'.cos';
                        $coshandle = fopen('./'.$cosfile,'w');
                        fwrite($coshandle,$value);
                        fclose($coshandle);
                        
                        fwrite($handle,'"Script 1" @ "'.$cosfile."\"\r\n");
                    } else {
                        if(is_int($value)) {
                            fwrite($handle,'"'.$name.'" '.$value."\r\n");
                        } else {
                            $value = str_replace("\r","",$value);
                            $value = str_replace("\n","",$value);
                            fwrite($handle,'"'.$name.'" "'.$value."\"\r\n");
                        }
                    }
                }
                fclose($handle);
                break;
        }
    }
    
}
function ExtractFileFromAgent($agent,$file) { //$file is a filename or a block id.
    if(is_int($file)) {
    }
}
?>