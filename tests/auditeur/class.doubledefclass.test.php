<?php
include_once('Auditeur_Framework_TestCase.php');

class doubledefclass_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'doubledefclass';
        $this->attendus = array('double_class');
        $this->inattendus = array('unique_class',);
        
        parent::generic_test();
    }
}

?>