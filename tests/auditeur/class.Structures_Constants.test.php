<?php 


include_once('Auditeur_Framework_TestCase.php');

class Structures_Constants_Test extends Auditeur_Framework_TestCase
{
    public function testStructures_Constants()  {
        $this->expected = array( '');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>