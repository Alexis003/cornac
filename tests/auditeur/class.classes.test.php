<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('a','b','c');
        $this->inattendus = array('d');
        
        parent::generic_test();
    }
}

?>