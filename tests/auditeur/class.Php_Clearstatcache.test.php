<?php


include_once('Auditeur_Framework_TestCase.php');

class Php_Clearstatcache_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_Clearstatcache()  {
        $this->expected = array( 'clearstatcache & realpath');
        $this->unexpected = array();

        parent::generic_test();
    }
}
?>