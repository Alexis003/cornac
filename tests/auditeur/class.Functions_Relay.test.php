<?php 


include_once('Auditeur_Framework_TestCase.php');

class Functions_Relay_Test extends Auditeur_Framework_TestCase
{
    public function testFunctions_Relay()  {
        $this->expected = array( 'this_is_a_relay2', 'this_is_a_relay');
        $this->unexpected = array('this_is_a_not_relay',);

        parent::generic_test();
    }
}
?>