<?php
include_once('Auditeur_Framework_TestCase.php');

class dieexit_Test extends Auditeur_Framework_TestCase
{
    public function testdieexit()  { 
        $this->expected = array( 'die','exit','EXIT');
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>