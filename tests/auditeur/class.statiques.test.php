<?php
include_once('Auditeur_Framework_TestCase.php');

class statiques_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('a->$propriete','parent::methode3()','self::methode2()','a::method(2)','a::constante');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>