<?php
include_once('Auditeur_Framework_TestCase.php');

class literals_reused_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('b_reused','c_reused_thrice','1');
        $this->inattendus = array('$d','a_not_reused','$a');
        
        parent::generic_test();
    }
}

?>