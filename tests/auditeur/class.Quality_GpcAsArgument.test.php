<?php 


include_once('Auditeur_Framework_TestCase.php');

class Quality_GpcAsArgument_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_GpcAsArgument()  {
        $this->expected = array( 'print_r($_GET)',
                                 '$x($_POST)',
                                 'user_land_function($_REQUEST)');
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>