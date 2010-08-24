<?php
include_once('Auditeur_Framework_TestCase.php');

class concatenation_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$_GET','$_REQUEST',);
        $this->inattendus = array('$_POST');
        
        parent::generic_test();
    }
}

?>