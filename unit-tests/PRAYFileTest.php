<?php
require_once(dirname(__FILE__) . '/AbstractTestCase.php');

class PRAYFileTest extends c2ephpAbstractTestCase {
    
    /**
      * @dataProvider createAgentFiles
      */
    public function testCreatePRAYFile(PRAYFile $prayfile) {
        $this->assertInstanceOf('PRAYFile',$prayfile);
        $this->assertNotNull($prayfile);
    }
    
    /**
      * @dataProvider createAgentFiles
      */
    public function testNumberOfBlocks(PRAYFile $prayfile,$fixtureInfo) {
        $this->assertNotNull($prayfile);
        $this->assertEquals($fixtureInfo['block count'],sizeof($prayfile->GetBlocks()));
    }

    /**
     * @dataProvider createAgentFiles
     */
    public function testFirstAgentDescription(PRAYFile $prayfile,$info) {
        $this->assertNotNull($prayfile);
        $blocks = $prayfile->GetBlocks(PRAY_BLOCK_AGNT);
        $this->assertEquals($info['first agent desc'],$blocks[0]->GetAgentDescription());
    }

    /**
     * @dataProvider createAgentFiles
     */ 
    public function testChangeAGNTAndRecompile(PRAYFile $prayfile,$info) {
        $this->assertNotNull($prayfile);
        $blocks = $prayfile->GetBlocks(PRAY_BLOCK_AGNT);
        $blocks[0]->SetTag('Agent Description','Testing');
        $this->assertEquals('Testing',$blocks[0]->GetAgentDescription());
        $data = $prayfile->Compile();
        $newfile = new PRAYFile(new StringReader($data));
        $this->assertEquals($info['block count'],sizeof($newfile->GetBlocks()));
        $newfile->GetBlocks('AGNT');
        $this->assertEquals('Testing',$blocks[0]->GetAgentDescription());
    }

    /**
     * @dataProvider createCreatureFiles
     */ 
    public function testChangeGLSTAndRecompile(PRAYFILE $prayfile,$info) {
        $this->assertNotNull($prayfile);
        $blocks = $prayfile->GetBlocks(PRAY_BLOCK_GLST);
        $this->assertEquals($info['events count'],$blocks[0]->GetHistory()->CountEvents());
        $blocks[0]->GetHistory()->RemoveEvent(0);
        $this->assertEquals($info['events count']-1,$blocks[0]->GetHistory()->CountEvents());
        
        $data = $prayfile->Compile();
        $newfile = new PRAYFile(new StringReader($data));
        $blocks = $newfile->GetBlocks(PRAY_BLOCK_GLST);
        $this->assertEquals($info['events count']-1,$blocks[0]->GetHistory()->CountEvents());

    }
}

?>

