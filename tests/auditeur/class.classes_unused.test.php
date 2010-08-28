<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_unused_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('cuc','cum','cuk','cuo','cup');
        $this->inattendus = array('$x','$y','$z','cua','cub','cud','cue','cuf','cug','cuh','cui','cuj');
        
        parent::generic_test();
    }
}

?>