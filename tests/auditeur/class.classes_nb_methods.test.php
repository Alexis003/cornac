<?php
include_once('Auditeur_Framework_TestCase.php');

class classes_nb_methods_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'classes_nb_methods';
        $this->attendus = array('one_method_class::one_method_1',
'three_method_class::three_method_1',
'three_method_class::three_method_2',
'three_method_class::three_method_3',
'two_method_class::two_method_1',
'two_method_class::two_method_2',
);
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>