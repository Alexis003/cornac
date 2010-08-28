<?php
include_once('Auditeur_Framework_TestCase.php');

class block_of_call_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->attendus = array('block4(0)
block4(1)
block4(2)
block4(3)',
'block5(0)
block5(1)
block5(2)
block5(3)
block5(4)',
'block3()
block3(1)
block3(2)'
                                );
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>