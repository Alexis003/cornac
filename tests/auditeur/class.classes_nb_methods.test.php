<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_nb_methods_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(
'one_method_class',
'two_method_class',
'two_method_class',
'three_method_class',
'three_method_class',
'three_method_class',

);
        $this->inattendus = array();
        
        parent::generic_counted_test();
    }
}

?>