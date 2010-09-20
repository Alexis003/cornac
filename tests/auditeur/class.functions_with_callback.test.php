<?php
include_once('Auditeur_Framework_TestCase.php');

class functions_with_callback_Test extends Auditeur_Framework_TestCase
{
    public function testfunctions_with_callback()  { 
        $this->expected = array( 'array_map','call_user_func','call_user_func_array','array_diff_uassoc','array_diff_ukey' );
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>