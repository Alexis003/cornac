<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_ObsoleteModulesIn53_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_ObsoleteModulesIn53()  {
        $this->expected = array( 'dbase','fbsql','fdf','ming','msql','ncurses','sybase','mhash');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>