<?php
include_once('Auditeur_Framework_TestCase.php');

class error_functions_Test extends Auditeur_Framework_TestCase
{
    public function testerror_functions()  { 
        $this->expected = array( 'debug_backtrace',
'debug_print_backtrace',
'error_get_last',
'error_log',
'error_reporting',
'restore_error_handler',
'restore_exception_handler',
'set_error_handler',
'set_exception_handler',
'trigger_error',
'user_error',);
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
    }
}
?>