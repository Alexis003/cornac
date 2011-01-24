<?php 


include_once('Auditeur_Framework_TestCase.php');

class Constants_Usage_Test extends Auditeur_Framework_TestCase
{
    public function testConstants_Usage()  {
        $this->expected = array( 'ACONSTANT','true','__FILE__','false');
        $this->unexpected = array('$x',);

        parent::generic_test();
    }
}
?>