<?php
include_once('Auditeur_Framework_TestCase.php');

class indenting_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array(
'_class,_function,ifthen,_foreach,ifthen,_switch,_case,_while,_dowhile',
'_class,_function,ifthen,_foreach,ifthen,_switch,_case,_while',
'_class,_function,ifthen,_foreach,ifthen,_switch,_case',
'_class,_function,ifthen,_foreach,ifthen,_switch',
'_class,_function,ifthen,_foreach,ifthen',
'_class,_function,ifthen,_foreach',
'_class,_function,ifthen',
'_class,_function',
'_class',
 );
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>