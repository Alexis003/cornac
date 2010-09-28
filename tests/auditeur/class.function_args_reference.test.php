<?php 
include_once('Auditeur_Framework_TestCase.php');

class function_args_reference_Test extends Auditeur_Framework_TestCase
{
    public function testfunction_args_reference()  {
        $this->expected = array( 
'::function_one_reference',
'::function_two_references',
'::function_two_references_b',
'::function_two_references_c',
'x::method_one_reference',
'x::method_two_references',
'x::method_two_references_b',
'x::method_two_references_c',

        );
        $this->unexpeted = array(
'::function_no_reference',
'x::method_no_reference',
);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>