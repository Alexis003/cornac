<?php
include_once('Auditeur_Framework_TestCase.php');

class arglist_disc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'arglist_disc';
        $this->attendus = array('arglist_def_2(3 args)', );
        $this->inattendus = array('arglist_def_ok(1 args)','arglist_def_ok_2(1 args)');
        
        parent::generic_test();
    }
}

?>