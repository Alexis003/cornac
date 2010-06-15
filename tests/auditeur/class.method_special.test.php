<?php
include_once('Auditeur_Framework_TestCase.php');

class Method_special_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'Method_special';
        $this->attendus = array('a->__toString','a->__construct','a->a','a->__destruct','a->__clone','a->__set','a->__get','a->__call','a->__callStatic','a->__unset','a->__isset','a->__wakeup','a->__set_state','a->__sleep','a->__invoke','__autoload');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>