<?php
include_once('Auditeur_Framework_TestCase.php');

class dangerous_combinaisons_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('worm_post');
        $this->inattendus = array('worm_get','worm_request' );
        
        parent::generic_test();
    }
}

?>