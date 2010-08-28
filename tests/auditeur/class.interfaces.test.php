<?php
include_once('Auditeur_Framework_TestCase.php');

class interfaces_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('interface_a');
        $this->inattendus = array('interface_a','ArrayObject','x');
        
        parent::generic_test();
    }
}

?>