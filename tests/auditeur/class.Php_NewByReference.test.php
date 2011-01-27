<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_NewByReference_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_NewByReference()  {
        $this->expected = array( 'new StdClass()');
        $this->unexpected = array('new x()',);

        parent::generic_test();
    }
}
?>