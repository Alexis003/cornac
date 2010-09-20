<?php
include_once('Auditeur_Framework_TestCase.php');

class unused_args_Test extends Auditeur_Framework_TestCase
{
    public function testunused_args()  { 
        $this->expected = array( 'function x($a, $b, $d, $e = 1, $f = 2)');
        $this->inexpected = array();
        
        parent::generic_test();
    }
}
?>