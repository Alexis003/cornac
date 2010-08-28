<?php
include_once('Auditeur_Framework_TestCase.php');

class arobases_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(
        '@$x','@$a->prop','@new StdClass()','@substr(1, 1, 1)','@1',
        );
        $this->inattendus = array('$c','$y');
        
        parent::generic_test();
    }
}

?>

