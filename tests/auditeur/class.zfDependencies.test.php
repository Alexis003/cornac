<?php 
include_once('Auditeur_Framework_TestCase.php');

class zfDependencies_Test extends Auditeur_Framework_TestCase
{
    public function testzfDependencies()  {
        $this->expected = array( 'Zend_Mail','Zend_Pdf');
        $this->unexpected = array('$x',);

        parent::generic_test();
    }
}
?>