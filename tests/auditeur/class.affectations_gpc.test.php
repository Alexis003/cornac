<?php
include_once('Auditeur_Framework_TestCase.php');

class affectations_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$_POST');
        $this->inattendus = array('$_GET');
        
        parent::generic_test();
    }
}

?>