<?php
include_once('Auditeur_Framework_TestCase.php');

class _this_Test extends Auditeur_Framework_TestCase
{
    public function test_this()  { 
        $this->expected = array( '$this');
        $this->inexpected = array(/*'',*/);
        
//        parent::generic_test();
        parent::generic_counted_test();
    }
}
?>