<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_undefined_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('z','$w','$request[3]');
        $this->inattendus = array('a','$x','$y','$z','$v','StdClass',);
        
        parent::generic_test();
    }
}

?>