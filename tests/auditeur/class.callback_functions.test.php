<?php
include_once('Auditeur_Framework_TestCase.php');

class callback_functions_Test extends Auditeur_Framework_TestCase
{
    public function testcallback_functions()  { 
        $this->expected = array( 'cb_1_1','cb_1_2','cb_1_3','cb_0_1','cb_0_2','cb_2_1' );
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>