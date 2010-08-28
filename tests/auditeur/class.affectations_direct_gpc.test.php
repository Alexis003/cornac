<?php
include_once('Auditeur_Framework_TestCase.php');

class affectations_direct_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('$_COOKIE',
'$_GET',
'$_POST',
'$_REQUEST',
'$HTTP_GET_VARS',
'$HTTP_POST_VARS',
'$_SESSION', 
'$HTTP_COOKIE_VARS',);
        $this->inattendus = array('$i','$j','$k','$a','$b','$c','$d','$e','$g','$h');
        
        parent::generic_test();
    }
}

?>

