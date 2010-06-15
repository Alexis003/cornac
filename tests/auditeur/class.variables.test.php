<?php
include_once('Auditeur_Framework_TestCase.php');

class Variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'variables';
        $this->attendus = array('$a','$b','$c','$d','$e');
        $this->inattendus = array('$z');
        
        parent::generic_test();
    }
}

?>