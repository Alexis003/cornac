<?php
include_once('Auditeur_Framework_TestCase.php');

class doubledeffunctions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('double');
        $this->inattendus = array('single',);
        
        parent::generic_test();
    }
}

?>