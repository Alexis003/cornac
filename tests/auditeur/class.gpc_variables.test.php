<?php
include_once('Auditeur_Framework_TestCase.php');

class gpc_variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('a','z','y','x');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>