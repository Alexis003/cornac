<?php
include_once('Auditeur_Framework_TestCase.php');

class proprietes_publiques_Test extends Auditeur_Framework_TestCase
{
    public function testproprietes_publiques()  { 
        $this->expected = array( 'x::$public_prop',
                                 'x::$static_public_prop',
                                 'x::$public_static_prop',
                                 );
        $this->inexpected = array('$protected_prop',
                                  '$private_prop',
                                  '$static_protected_prop',
                                  '$static_private_prop');
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>