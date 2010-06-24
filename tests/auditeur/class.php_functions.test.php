<?php
include_once('Auditeur_Framework_TestCase.php');

class php_functions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'php_functions';
        $this->attendus = array('xdebug_get_stack_depth','sqlite_open','echo');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>