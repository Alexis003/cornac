<?php
include_once('Auditeur_Framework_TestCase.php');

class multi_def_files_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'multi_def_files';
        $this->attendus = array('scope_class::__construct', 
                                '::scope_function',
                                '::global');
        $this->inattendus = array('scope_class::global',);
        
        parent::generic_test();
    }
}

?>