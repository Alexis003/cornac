<?php
include_once('Auditeur_Framework_TestCase.php');

class literals_reused_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'literals_reused';
        $this->attendus = array('b_reused','c_reused_thrice');
        $this->inattendus = array('$d','a_not_reused','$a');
        
        parent::generic_test();
    }
}

?>