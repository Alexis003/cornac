<?php
include_once('Auditeur_Framework_TestCase.php');

class headers_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'headers';
        $this->attendus = array('header', 'setcookie','setrawcookie');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>