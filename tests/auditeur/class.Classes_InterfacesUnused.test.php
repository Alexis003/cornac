<?php 


include_once('Auditeur_Framework_TestCase.php');

class Classes_InterfacesUnused_Test extends Auditeur_Framework_TestCase
{
    public function testClasses_InterfacesUnused()  {
        $this->expected = array( 'i_unused');
        $this->unexpected = array('i_used',);

        parent::generic_test();
    }
}
?>