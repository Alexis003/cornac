<?php
include_once('Auditeur_Framework_TestCase.php');

class php_classes_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('Exception','Phar','soapServer');
        $this->inattendus = array('user_defined_class' );
        
        parent::generic_test();
    }
}

?>