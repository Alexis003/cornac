<?php
include_once('Auditeur_Framework_TestCase.php');

class variables_one_letter_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'variables_one_letter';
        $this->attendus = array('$a','$b','$B', '$C');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>