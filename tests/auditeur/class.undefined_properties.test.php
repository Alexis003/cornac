<?php
include_once('Auditeur_Framework_TestCase.php');

class undefined_properties_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'undefined_properties';
        $this->attendus = array('undefined');
        $this->inattendus = array('public_defined_inited','protected_defined_inited','private_defined_inited','var_defined_inited');
        
        parent::generic_test();
    }
}

?>