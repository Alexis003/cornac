<?php



include_once('Auditeur_Framework_TestCase.php');

class Php_FuncGetArgOutOfFunctionScope_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_FuncGetArgOutOfFunctionScope()  {
        $this->expected = array( 'func_get_args',
                                 'func_get_arg',
                                 'func_num_args');
        $this->unexpected = array(/*'',*/);

        parent::generic_counted_test();
    }
}
?>