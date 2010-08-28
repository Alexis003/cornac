<?php
include_once('Auditeur_Framework_TestCase.php');

class Affectations_variables_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$a','$b',
                                '$c','$d',
                                '$e','$g',
                                '$j','$objet',
                                '$statique','$k',
                                '$l','$m',
                                '$fe_key',
                                '$fe_value','$fe_value2');
        $this->inattendus = array('$e','$h','$i',
                                  'propriete','$fe_array',
                                  '$fe_array2','$fe_array3',
                                  '$fe_key3',);
        parent::generic_test();
    }
}

?>