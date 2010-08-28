<?php
include_once('Auditeur_Framework_TestCase.php');

class concatenation_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$_GET','$_REQUEST',);
        $this->inattendus = array('$_POST');
        
        parent::generic_test();
    }
}

?>