<?php
include_once('Auditeur_Framework_TestCase.php');

class variables_unaffected_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$vu_unaffected',);
        $this->inattendus = array('$vu_affected');
        
        parent::generic_test();
    }
}

?>