<?php
include_once('Auditeur_Framework_TestCase.php');

class properties_used_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'properties_used';
        $this->attendus = array('x->a','x->b','x->c','x->d','x->e','$autre->am', '$x->a');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>