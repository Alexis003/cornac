<?php 


include_once('Auditeur_Framework_TestCase.php');

class Quality_StrposEquals_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_StrposEquals()  {
        $this->expected = array( 'strpos($c, $d) == 0',
                                 '0 == strpos($g, $h)',
                                 'strpos($a, $b)',
                                 'strpos($aw, $bw)',
);
        $this->unexpected = array('strpos($e, $f) === 0',);

        parent::generic_test();
    }
}
?>
