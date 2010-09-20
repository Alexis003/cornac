<?php 
include_once('Auditeur_Framework_TestCase.php');

class handlers_Test extends Auditeur_Framework_TestCase
{
    public function testhandlers()  { 
        $this->expected = array( '');
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>