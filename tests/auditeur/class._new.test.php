<?php
include_once('Auditeur_Framework_TestCase.php');

class _New_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$a','$asp','StdClass', 'classe_sans_parenthese','z');
        $this->inattendus = array('$x','$z');
        
        parent::generic_test();
    }
}

?>