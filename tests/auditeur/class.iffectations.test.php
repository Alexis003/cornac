<?php
include_once('Auditeur_Framework_TestCase.php');

class iffectations_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$x = 1',
                                '$z = 3',);
        $this->inattendus = array('$y = 2',
                                  '$b = 6',
                                  '$a = 5',);
        
        parent::generic_test();
    }
}

?>