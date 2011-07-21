<?php
include '../c2ephp.php';

$history = new CreatureHistory('063-wind-gvqyh-ul36x-6hf64-rdnl2','DonkeyBreath',CREATUREHISTORY_GENDER_FEMALE,1,3,0,0);
$event = new CreatureHistoryEvent(0,100,0,time(),0xFFFFFFFF,'063-wind-gvqyh-ul36x-6hf64-rdnl2','063-wind-gvqyh-ul36x-6hf64-rdnl2','','','Donkeys','063-wind-gvqyh-ul36x-6hf64-rdnl2');
$history->AddEvent($event);

$prayfile = new PRAYFile();
$prayfile->AddBlock(new GLSTBlock($history,'DonkeyBreath!!','',PRAY_FLAG_ZLIB_COMPRESSED));
print $prayfile->Compile();

?>
