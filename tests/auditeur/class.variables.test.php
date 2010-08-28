<?php
include_once('Auditeur_Framework_TestCase.php');

class Variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$a','$b','$c','$d','$e','$y', '$or', '$x', '$$or', '$$or2', '$or2');
        $this->inattendus = array('$z','$');
        
        parent::generic_test();
    }
}

?>