<?php
require_once(dirname(__FILE__) . '/AbstractTestCase.php');
require_once(dirname(__FILE__) . '/../support/FileReader.php');
require_once(dirname(__FILE__) . '/../support/StringReader.php');

class ReaderTest extends c2ephpAbstractTestCase {

    /**
     * @dataProvider createPRAYFiles
     */
    public function testFileReader($prayFile, $info) {

        $fileReader = new FileReader($info['path']);

        $this->assertEquals($info['byte0'],$fileReader->Read(1));
        $this->assertEquals($info['byte1'],$fileReader->Read(1)); // tests position increments
        // now at position 2, skip 2 = position 4
        $fileReader->skip(2);
        $this->assertEquals($info['byte4'],$fileReader->Read(1)); // tests skip skips the correct amount
        $fileReader->seek(0);
        $this->assertEquals($info['byte0'],$fileReader->Read(1)); // tests seek works
        $this->assertEquals($info['substring'],$fileReader->GetSubString(3,8)); // tests getsubstring gets the right substring
        $this->assertEquals($info['byte1'],$fileReader->Read(1)); // tests that getsubstring leaves cursor where it found it

        $this->assertEquals($info['length'],strlen($fileReader->GetSubString(0)));
        $this->assertEquals($info['md5sum'],md5($fileReader->GetSubString(0)));

    }
    
    /**
     * @dataProvider generateStrings
     */
    public function testStringReader($string) {
        $stringReader = new StringReader($string);
        $this->assertEquals($string[0],$stringReader->Read(1));
        $this->assertEquals($string[1],$stringReader->Read(1));
        $stringReader->skip(2);
        $this->assertEquals($string[4],$stringReader->Read(1));
        $this->assertEquals(substr($string,3,8),$stringReader->GetSubString(3,8));
        $this->assertEquals($string[5],$stringReader->Read(1));

        $this->assertEquals($string,$stringReader->GetSubString(0));
    }
    
    public function generateStrings() {
        return array(
            array('striiing','blimp!!!!!!!'),
        );
    }
}
