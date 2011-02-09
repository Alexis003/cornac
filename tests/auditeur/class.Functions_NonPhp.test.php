<?php



include_once('Auditeur_Framework_TestCase.php');

class Functions_NonPhp_Test extends Auditeur_Framework_TestCase
{
    public function testFunctions_NonPhp()  {
        $this->expected = array( 'userdefined');
        $this->unexpected = array('echo','strtolower');

        parent::generic_test();
    }
}
?>