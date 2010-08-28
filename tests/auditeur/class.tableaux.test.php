<?php
include_once('Auditeur_Framework_TestCase.php');

class tableaux_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$a[1]','$b[2]','$b[2][3]','$e[$f[1]]','$f[1]','$d[$e[$f[1]]]');
        $this->inattendus = array('$c','$g' );
        
        parent::generic_test();
    }
}

?>