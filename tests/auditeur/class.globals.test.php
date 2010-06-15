<?php
include_once('Auditeur_Framework_TestCase.php');

class globals_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'globals';
        $this->attendus = array('$global_du_main','$global_de_scope','$g1','$g2','$g3','$GLOBALS[\'hors_x\']','$GLOBALS','$GLOBALS[\'dans_x\']');
        $this->inattendus = array('x','$x','$local_de_scope');
        
        parent::generic_test();
    }
}

?>