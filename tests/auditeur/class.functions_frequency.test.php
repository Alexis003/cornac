<?php
include_once('Auditeur_Framework_TestCase.php');

class functions_frequency_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'functions_frequency';
        $this->attendus = array('echo');
        $this->inattendus = array();
        
        parent::generic_counted_test();
    }
}

?>