<?php 


include_once('Auditeur_Framework_TestCase.php');

class Drupal_Hook6_Test extends Auditeur_Framework_TestCase
{
    public function testDrupal_Hook6()  {
        $this->expected = array( 'hoook_comment','hoook_term_path',  );
        $this->unexpected = array('hoook_auth','hoook_user_presave');

        parent::generic_test();
    }
}
?>