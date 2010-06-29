<?php
include_once('Auditeur_Framework_TestCase.php');

class inclusions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'inclusions';
        $this->attendus = array('include',
                                'include_once',
                                'require',
                                'require_once',
                                
                                );
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>