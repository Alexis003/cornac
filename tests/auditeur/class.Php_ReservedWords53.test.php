<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_ReservedWords53_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_ReservedWords53()  {
        $this->markTestSkipped('Won\'t must work with PHP 5.2 (max)');
        
        $this->expected = array( 'goto','namespace');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>