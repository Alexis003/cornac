<?php
include_once('Auditeur_Framework_TestCase.php');

class globals_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$global_de_scope','$g1','$g2','$g3','$GLOBALS[hors_x]','$GLOBALS[dans_x]');
        $this->inattendus = array('x','$x','$local_de_scope','$var_not_global','theclasse','$global_du_main_without_global','$GLOBALS');
        
        parent::generic_test();
    }
}

?>