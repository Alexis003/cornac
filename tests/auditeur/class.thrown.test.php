<?php
include_once('Auditeur_Framework_TestCase.php');

class thrown_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('thrown',
                                );
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>