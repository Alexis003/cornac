<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_nb_methods_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'classes_nb_methods';
        $this->attendus = array('a','b','c');
        $this->inattendus = array('d');
        
        parent::generic_test();
    }
}

?>