<?php
include_once('Auditeur_Framework_TestCase.php');

class fluid_interface_Test extends Auditeur_Framework_TestCase
{
    public function testfluid_interface()  { 
        $this->expected = array( '$that->is->a->fluid');
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>