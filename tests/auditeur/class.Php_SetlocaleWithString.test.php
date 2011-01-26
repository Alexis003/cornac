<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_SetlocaleWithString_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_SetlocaleWithString()  {
        $this->expected = array( 'LC_ALL');
        $this->unexpected = array('LC_TIME',);

        parent::generic_test();
    }
}
?>