<?php
include_once('Auditeur_Framework_TestCase.php');

class arglist_def_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'arglist_def';
        $this->attendus = array('one_arg(1 args)',
                                'ten_args(10 args)',
                                'two_2_arg(1 args)', 
                                'two_2_arg(2 args)',
                                'two_arg(2 args)',
                                'four_2_arg(1 args)', 
                                'four_2_arg(2 args)',
                                'four_2_arg(3 args)',
                                'four_2_arg(4 args)'
);
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>