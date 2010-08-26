<?php
include_once('Auditeur_Framework_TestCase.php');

class session_variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'session_variables';
        $this->attendus = array('x');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>