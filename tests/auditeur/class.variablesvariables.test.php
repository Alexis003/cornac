<?php
include_once('Auditeur_Framework_TestCase.php');

class variablesvariables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'variablesvariables';
        $this->attendus = array('$$c','$$$d','$$d','$$$e','$$e','$$or','$$or2','$curOr.$y');
        $this->inattendus = array('b','$x','$y','curOr');
        
        parent::generic_test();
    }
}

?>