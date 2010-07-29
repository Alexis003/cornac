<?php
include_once('Auditeur_Framework_TestCase.php');

class dangerous_combinaisons_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'dangerous_combinaisons';
        $this->attendus = array('worm');
        $this->inattendus = array( );
        
        parent::generic_test();
    }
}

?>