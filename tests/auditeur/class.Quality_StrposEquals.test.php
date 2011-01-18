<?php 


include_once('Auditeur_Framework_TestCase.php');

class Quality_StrposEquals_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_StrposEquals()  {
        $this->expected = array( '');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>