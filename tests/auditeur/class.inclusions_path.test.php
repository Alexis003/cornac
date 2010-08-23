<?php
include_once('Auditeur_Framework_TestCase.php');

class inclusions_path_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'inclusions_path';
        $this->attendus = array('inclusion.php',
                                '$this->inclusion()'
                                'PATH.$fichier'
                                );
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>