<?php
include_once('Auditeur_Framework_TestCase.php');

class array_duplication_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        // try moving to Auditeur_Framework_TestCase? 
        $this->name = str_replace('_Test', '', __CLASS__);

        $this->attendus = array('$a');
        $this->inattendus = array('$x');
        
        parent::generic_test();
    }
}

?>