<?php
include_once('Auditeur_Framework_TestCase.php');

class arglist_call_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(    'call_one_arg(1 args)',
    'call_ten_args(10 args)',
    'call_two_2_arg(2 args)',
    'call_two_3_arg(2 args)',
    'call_two_arg(2 args)',
);
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>