<?php



include_once('Auditeur_Framework_TestCase.php');

class Constants_HasLowerCase_Test extends Auditeur_Framework_TestCase
{
    public function testConstants_HasLowerCase()  {
        $this->expected = array( 'HasSomeLower');
        $this->unexpected = array('ALL_CAPS', 'OK23');

        parent::generic_test();
    }
}
?>