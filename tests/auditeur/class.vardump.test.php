<?php
include_once('Auditeur_Framework_TestCase.php');

class vardump_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('var_dump','print_r');
        $this->inattendus = array();

        parent::generic_test();
    }
}

?>