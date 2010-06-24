<?php
include_once('Auditeur_Framework_TestCase.php');

class functions_undefined_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'functions_undefined';
        $this->attendus = array('used_but_undefined_in_functions_undefined');
        $this->inattendus = array('defined_and_used_in_functions_undefined');
        
        parent::generic_test();
    }
}

?>