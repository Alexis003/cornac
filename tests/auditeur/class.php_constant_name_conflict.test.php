<?php 
include_once('Auditeur_Framework_TestCase.php');

class php_constant_name_conflict_Test extends Auditeur_Framework_TestCase
{
    public function testphp_constant_name_conflict()  { 
        $this->expected = array( 'FBSQL_ASSOC');
        $this->inexpected = array('true');
        
        parent::generic_test();
    }
}
?>