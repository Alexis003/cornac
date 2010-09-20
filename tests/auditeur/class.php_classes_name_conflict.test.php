<?php 
include_once('Auditeur_Framework_TestCase.php');

class php_classes_name_conflict_Test extends Auditeur_Framework_TestCase
{
    public function testphp_classes_name_conflict()  { 
        $this->expected = array( 'variant');
        $this->unexpeted = array('stdCLass',);
        
        parent::generic_test();
    }
}
?>