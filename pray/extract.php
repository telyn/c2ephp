<?php
require_once(dirname(__FILE__).'/agent.php');
 
//this function is pretty dirty, all things considered.
 
function ExtractCompleteAgent($agent) {
    $agent = new Agent(file_get_contents($agent));
    $agent->Parse();
    $blocks = $agent->GetBlocks();
    $fileBlocks = $agent->GetBlocks('FILE');
    foreach($blocks as $block) {
        switch($block['Type']) {
            case 'FILE':
			case 'PHOT':
			case 'GENE':
			case 'GLST':
			case 'CREA':
                $handle = fopen('./'.$block['Name'],'wb');
                fwrite($handle,$block['Content']);
                fclose($handle);
                break;
                
            case 'AGNT':
            case 'DSAG':
            case 'EGG': //after looking I don't think it needs any special treatment
                $handle = fopen($block['Name'].'.'.$block['Type'].'.pray.txt','w');
                fwrite($handle,'"en-GB"'."\r\n\r\n".'group '.$block['Type'].' "'.$block['Name']."\"\r\n");
                foreach($block['Tags'] as $name=>$value) {
                    
                    if($name == "Script 1") { //since apparently there should only be one script ever (according to CDN)
                        
                        $cosFile = $block['Name'].'.'.$block['Type'].'.cos';
                        $cosHandle = fopen('./'.$cosFile,'w');
                        fwrite($cosHandle,$value);
                        fclose($cosHandle);
                        
                        fwrite($handle,'"Script 1" @ "'.$cosFile."\"\r\n"); //special handling because the script directive is DUMB
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
                foreach($fileBlocks as $fileBlock) {
                    fwrite($handle,'inline FILE "'.$fileBlock['Name'].'" "'.$fileBlock['Name']."\"\r\n");
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