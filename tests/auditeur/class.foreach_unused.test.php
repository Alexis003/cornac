<?php
include_once('Auditeur_Framework_TestCase.php');

class foreach_unused_Test extends Auditeur_Framework_TestCase
{
    public function testforeach_unused()  { 
        $this->expected = array( '$k_variable','$v_variable','$k_reference','$v_reference');
        $this->inexpected = array('$K_variable','$V_variable','$K_reference','$V_reference');
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>