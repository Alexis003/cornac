<?php 


include_once('Auditeur_Framework_TestCase.php');

class Classes_Constants_Test extends Auditeur_Framework_TestCase
{
    public function testClasses_Constants()  {
        $this->expected = array( 'z','y');
        $this->unexpected = array('x',);

        parent::generic_test();
    }
}
?>