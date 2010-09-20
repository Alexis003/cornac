<?php
include_once('Auditeur_Framework_TestCase.php');

class references_Test extends Auditeur_Framework_TestCase
{
    public function testreferences()  { 
        $this->expected = array( '$a','$d','$f');
        $this->inexpected = array('$b','$c','$e');
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>