<?php
include_once('Auditeur_Framework_TestCase.php');

class functions_unused_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'functions_unused';
        $this->attendus = array('defined_but_not_used');
        $this->inattendus = array('defined_and_used','eval','unset','__autoload');
        
        parent::generic_test();
    }
}

?>