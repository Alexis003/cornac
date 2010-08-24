<?php
include_once('Auditeur_Framework_TestCase.php');

class upload_functions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('move_uploaded_file','is_uploaded_file','rename',);
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>