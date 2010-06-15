<?php
include_once('Auditeur_Framework_TestCase.php');

class _New_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = '_new';
        $this->attendus = array('$a','$asp','StdClass', 'classe_sans_parenthese');
        $this->inattendus = array('$x');
        
        parent::generic_test();
    }
}

?>