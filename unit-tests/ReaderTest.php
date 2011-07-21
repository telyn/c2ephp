<?php
require_once(dirname(__FILE__) . '/AbstractTestCase.php');
require_once(dirname(__FILE__) . '/../support/FileReader.php');
require_once(dirname(__FILE__) . '/../support/StringReader.php');

class ReaderTest extends c2ephpAbstractTestCase {

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
