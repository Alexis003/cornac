<?php
include_once('Auditeur_Framework_TestCase.php');

class tableaux_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'tableaux';
        $this->attendus = array('$a','$b','$d','$e','$f');
        $this->inattendus = array('$c','$g' );
        
        parent::generic_test();
    }
}

?>