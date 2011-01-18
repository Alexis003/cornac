<?php 


include_once('Auditeur_Framework_TestCase.php');

class Quality_ConstructNameOfClass_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_ConstructNameOfClass()  {
        $this->expected = array( 'constructor_both::__construct', 
                                 'constructor_both::constructor_both',
                                 'constructor_php4_only::constructor_php4_only',
                                 'constructor_php5_only::__construct',
                                 );
        $this->unexpected = array('constructor_none');

        parent::generic_test();
    }
}
?>