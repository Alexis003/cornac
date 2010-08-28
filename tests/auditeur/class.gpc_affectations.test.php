<?php
include_once('Auditeur_Framework_TestCase.php');

class gpc_affectations_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$_POST','$_GET');
        $this->inattendus = array('$_COOKIE' );
        
        parent::generic_test();
    }
}

?>