<?php 
include_once('Auditeur_Framework_TestCase.php');

class zfController_Test extends Auditeur_Framework_TestCase
{
    public function testzfController()  {
        $this->expected = array( 'Zend_My_Controller->realAction',
                                 'Zend_My_Second_Controller->anotherrealAction',
                                 );
        $this->unexpeted = array('X->notarealAction',);

        parent::generic_test();
    }
}
?>