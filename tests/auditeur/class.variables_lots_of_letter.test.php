<?php
include_once('Auditeur_Framework_TestCase.php');

class variables_lots_of_letter_Test extends Auditeur_Framework_TestCase
{
    public function testvariables_lots_of_letter()  { 
        $this->expected = array( '$this_is_a_another_long_variable_name (37 chars)','$this_is_a_very_long_variable_name (34 chars)');
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}

?>