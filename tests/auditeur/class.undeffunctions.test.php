<?php
include_once('Auditeur_Framework_TestCase.php');

class undeffunctions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('undefined_function');
        $this->inattendus = array('defined_function',);
        
        parent::generic_test();
    }
}

?>