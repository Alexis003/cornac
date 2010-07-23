<?php
include_once('Auditeur_Framework_TestCase.php');

class php_modules_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'php_modules';
        $this->attendus = array('simplexml','sqlite','standard','phar','soap');
        $this->inattendus = array('xdebug' );
        
        parent::generic_test();
    }
}

?>