<?php



include_once('Auditeur_Framework_TestCase.php');

class Classes_InterfacesUsed_Test extends Auditeur_Framework_TestCase
{
    public function testClasses_InterfacesUsed()  {
        $this->expected = array( 'i_used');
        $this->unexpected = array('i_unused',);

        parent::generic_test();
    }
}
?>