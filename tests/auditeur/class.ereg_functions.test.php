<?php
include_once('Auditeur_Framework_TestCase.php');

class ereg_functions_Test extends Auditeur_Framework_TestCase
{
    public function testereg_functions()  { 
        $this->expected = array( 'ereg_replace',
'ereg',
'eregi_replace',
'eregi',
'split',
'spliti',
'sql_regcase',
);
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>