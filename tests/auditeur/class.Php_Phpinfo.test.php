<?php



include_once('Auditeur_Framework_TestCase.php');

class Php_Phpinfo_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_Phpinfo()  {
        $this->expected = array( 'PHPinfo','phpINFO');
        $this->unexpected = array(/*'',*/);

        parent::generic_counted_test();
    }
}
?>