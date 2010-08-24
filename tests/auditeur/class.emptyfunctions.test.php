<?php
include_once('Auditeur_Framework_TestCase.php');

class emptyfunctions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'emptyfunctions';
        $this->attendus = array('empty_function',
                                'comment_in_function',
                                'semi_colon_function',

                                
                                );
        $this->inattendus = array('real_function',
                                  'interface_empty_function',);
        
        parent::generic_test();
    }
}

?>