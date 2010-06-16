<?php
include_once('Auditeur_Framework_TestCase.php');

class Functions_undefined_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'functions_undefined';
        $this->attendus = array('used_but_undefined');
        $this->inattendus = array('defined_and_used');
        
        parent::generic_test();
    }
}

?>