<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_FunctionsCalls_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_FunctionsCalls()  {
        $this->expected = array( 'userdefined', 'echo','strtolower');
        $this->unexpected = array();

        parent::generic_test();
    }
}
?>