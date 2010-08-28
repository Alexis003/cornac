<?php
include_once('Auditeur_Framework_TestCase.php');

class unused_properties_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$public_defined_inited_unused',
                                '$protected_defined_inited_unused',
                                '$private_defined_inited_unused',
                                '$var_defined_inited_unused',
                                );
        $this->inattendus = array('$public_defined_inited',
                                  '$protected_defined_inited',
                                  '$private_defined_inited',
                                  '$var_defined_inited');
        
        parent::generic_test();
    }
}

?>