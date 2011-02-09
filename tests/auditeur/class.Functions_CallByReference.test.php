<?php



include_once('Auditeur_Framework_TestCase.php');

class Functions_CallByReference_Test extends Auditeur_Framework_TestCase
{
    public function testFunctions_CallByReference()  {
        $this->expected = array( 'userland_function',
                                 'strtolower');
        $this->unexpected = array('dont_spot',);

        parent::generic_test();
    }
}
?>