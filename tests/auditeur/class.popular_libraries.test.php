<?php
include_once('Auditeur_Framework_TestCase.php');

class popular_libraries_Test extends Auditeur_Framework_TestCase
{
    public function testpopular_libraries()  { 
        $this->expected = array( 'fpdf','tcpdf');
        $this->inexpected = array('Third_Party_Lib',);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>