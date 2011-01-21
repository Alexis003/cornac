<?php 


include_once('Auditeur_Framework_TestCase.php');

class Variables_AllCaps_Test extends Auditeur_Framework_TestCase
{
    public function testVariables_AllCaps()  {
        $this->expected = array( '$ALL_CAPS');
        $this->unexpected = array('$Some_Caps',);

        parent::generic_test();
    }
}
?>