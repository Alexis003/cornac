<?php 
include_once('Auditeur_Framework_TestCase.php');

class php_functions_name_conflict_Test extends Auditeur_Framework_TestCase
{
    public function testphp_functions_name_conflict()  { 
        $this->expected = array( '');
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>