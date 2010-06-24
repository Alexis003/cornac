<?php
include_once('Auditeur_Framework_TestCase.php');

class php_modules_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'php_modules';
        $this->attendus = array('xdebug','sqlite');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>