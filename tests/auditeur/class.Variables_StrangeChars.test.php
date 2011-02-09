<?php



include_once('Auditeur_Framework_TestCase.php');

class Variables_StrangeChars_Test extends Auditeur_Framework_TestCase
{
    public function testVariables_StrangeChars()  {
        $this->expected = array( '$xéx', '$我');
        $this->unexpected = array('$X','$normal');

        parent::generic_test();
    }
}
?>