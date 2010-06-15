<?php
include_once('Auditeur_Framework_TestCase.php');

class Affectations_variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$a','$b','$c','$d','$e','$g','$j','$objet','$statique','$k','$l','$m',);
        $this->inattendus = array('$e','$h','$i','propriete');
        
        parent::generic_test();
    }
}

?>