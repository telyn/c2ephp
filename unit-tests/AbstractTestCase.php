<?php
require_once(dirname(__FILE__).'/../agents/PRAYFile.php');
require_once(dirname(__FILE__).'/../support/FileReader.php');

abstract class c2ephpAbstractTestCase extends PHPUnit_Framework_TestCase {
    /// @brief Agent Files fixture
    /**
     * Creates PRAYFiles with matching facts about them. \n
     * Use @dataProvider createPrayFiles. \n
     * function prototype: someTest($prayFile, $prayFileFacts); \n
     * Current facts: 'block count'
     * 
     */
    public function createAgentFiles() {
        $files = array(
            array(
                'block count' => 6,
                'length' => 13689,
                'md5sum' => 'b96b67dc056be5899b796ccd7c96dd4c',
                'path' => dirname(__FILE__).'/fixtures/rubber_ball.agents',
                'byte0' => 'P',
                'byte1' => 'R',
                'byte4' => 'D',
                'substring' => 'YDSAGHi-',
                'first agent desc' => 'This rubber ball is very bouncy! It should keep your creatures happy for a while, although chasing after it can be tiring.'
            ),
        );

        $prayfiles = array();
        foreach ($files as $info) {

            $prayfiles[] = array(new PRAYFile(new FileReader($info['path'])), $info);
        }
        return $prayfiles;
    }

    public function createCreatureFiles() {
        $files = array(
            array(
                'path' => dirname(__FILE__).'/fixtures/lilo.creature',
                'events count' => 17
            ),
        );


        $prayfiles = array();
        foreach ($files as $info) {

            $prayfiles[] = array(new PRAYFile(new FileReader($info['path'])), $info);
        }
        return $prayfiles;

    }
}
?>
