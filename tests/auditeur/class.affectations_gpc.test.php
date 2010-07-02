<?php
include_once('Auditeur_Framework_TestCase.php');

class affectations_gpc_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = substr(basename(__FILE__), 6, -9);
        $this->attendus = array('$_GET[y]','$_POST[a]','$_GET','$_ENV[e]','$_SERVER[c][d]','$_SERVER[c]','$_FILES[f]','$HTTP_GET_VARS[g]','$HTTP_POST_VARS[h]','$_FILES');
        $this->inattendus = array('$x[$_GET[y]]', '$_GET[a]', '$_NORMAL[b]','$_env[e]','$z','$za','$zb','$zc','$zd','$ze','$_env[e]','$_NORMAL');
        
        parent::generic_test();
    }
}

?>