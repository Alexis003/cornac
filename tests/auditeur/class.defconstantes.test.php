<?php
include_once('Auditeur_Framework_TestCase.php');

class defconstantes_Test extends Auditeur_Framework_TestCase
{
    public function testdefconstantes()  { 
        $this->expected = array( 'DEFINED_CONSTANTS',
                                  'DEFINED_CONSTANTS2',);
        $this->inexpected = array('true',
                                  '__METHOD__',
                                  'UNDEFINED_CONSTANTE',);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>