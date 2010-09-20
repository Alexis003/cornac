<?php
include_once('Auditeur_Framework_TestCase.php');

class keyval_Test extends Auditeur_Framework_TestCase
{
    public function testkeyval()  { 
        $this->expected = array('$b',
                                '$c',
                                '$f',
                                '$h',
                                '$k',
                                '$l',
                                );
        $this->inexpected = array();
        
        parent::generic_test();
    }
}
?>