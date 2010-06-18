<?php
include_once('Auditeur_Framework_TestCase.php');

class properties_defined_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'properties_defined';
        $this->attendus = array('x->$a','x->$b','x->$c','x->$d','x->$e','x->$f','x->$g','x->$h','x->$i','x->$j','x->$k','x->$l','x->$m','x->$n','x->$o','x->$p','x->$q','x->$r','x->$s','x->$es','x->$fs','x->$gs','x->$hs','x->$is','x->$se','x->$sf','x->$sg','x->$sh','x->$si');
        $this->inattendus = array('$arg','$local','x');
        
        parent::generic_test();
    }
}

?>