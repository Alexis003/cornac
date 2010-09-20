<?php
include_once('Auditeur_Framework_TestCase.php');

class regex_Test extends Auditeur_Framework_TestCase
{
    public function testregex()  { 
        $this->expected = array( '/regex/','/regex2/is','/regex3/m',);
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>