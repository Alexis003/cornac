<?php
include_once('Auditeur_Framework_TestCase.php');

class zfClasses_Test extends Auditeur_Framework_TestCase
{
    public function testzfClasses()  { 
        $this->expected = array( 'Zend_View',);
        $this->inexpected = array('Zend_View_Not_Existing');
        
        parent::generic_test();
    }
}
?>