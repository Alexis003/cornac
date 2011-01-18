<?php 


include_once('Auditeur_Framework_TestCase.php');

class Drupal_Hook5_Test extends Auditeur_Framework_TestCase
{
    public function testDrupal_Hook5()  {
        $this->expected = array( 'hoook_access','hoook_block','hoook_comment', 'hoook_auth');
        $this->unexpected = array('hoook_user_presave','hoook_term_path',);

        parent::generic_test();
    }
}
?>
