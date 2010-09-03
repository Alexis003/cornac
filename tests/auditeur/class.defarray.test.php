<?php
include_once('Auditeur_Framework_TestCase.php');

class defarray_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(   
        '0 elements', 
        '1 elements',
        '2 elements',
        '2 elements',
        '2 elements',
        '2 elements',
        '5 elements',
        );
        $this->inattendus = array();
        
        parent::generic_counted_test();
    }
}

?>