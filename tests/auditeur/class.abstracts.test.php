<?php 
include_once('Auditeur_Framework_TestCase.php');

class abstracts_Test extends Auditeur_Framework_TestCase
{
    public function testabstracts()  {
        $this->expected = array( 
'abstract_class::public_abstract_static_method',
'abstract_class::abstract_public_static_method',
'abstract_class::abstract_protected_static_method',
'abstract_class::protected_abstract_static_method',
'abstract_class::protected_static_abstract_method',
'abstract_class::public_static_abstract_method',
'abstract_class::abstract_protected_method',
'abstract_class::protected_abstract_method',
'abstract_class::abstract_public_method',
'abstract_class::public_abstract_method',
'abstract_class',
        );
        $this->unexpeted = array('real_method',);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>
    
