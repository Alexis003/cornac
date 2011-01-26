<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_SetlocaleWithString_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_SetlocaleWithString()  {
        $this->expected = array( '');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>