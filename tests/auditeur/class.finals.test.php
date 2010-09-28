<?php 
include_once('Auditeur_Framework_TestCase.php');

class finals_Test extends Auditeur_Framework_TestCase
{
    public function testfinals()  {
        $this->expected = array( 
'final_class::public_final_static_method',
'final_class::final_public_static_method',
'final_class::final_protected_static_method',
'final_class::protected_final_static_method',
'final_class::protected_static_final_method',
'final_class::public_static_final_method',
'final_class::final_protected_method',
'final_class::protected_final_method',
'final_class::final_public_method',
'final_class::public_final_method',
'final_class',
        );
        $this->unexpected = array('real_method',);

        parent::generic_test();
    }
}
?>