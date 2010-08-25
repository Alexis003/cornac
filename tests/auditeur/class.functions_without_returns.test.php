<?php
include_once('Auditeur_Framework_TestCase.php');

class functions_without_returns_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'functions_without_returns';
        $this->attendus = array('::function_without_return','fwr_x::method_without_return','fwr_x::static_method_without_return');
        $this->inattendus = array('function_with_return','method_with_return','static_method_with_return',
                                  '::function_with_return','::method_with_return','::static_method_with_return',
                                  'fwr_x::function_with_return','fwr_x::method_with_return','fwr_x::static_method_with_return'
        );
        
        parent::generic_test();
    }
}

?>