<?php



include_once('Auditeur_Framework_TestCase.php');

class Quality_MktimeIsdst_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_MktimeIsdst()  {
        $this->expected = array( 'mktime');
        $this->unexpected = array(/*'',*/);

        parent::generic_counted_test();
    }
}
?>