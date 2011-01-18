<?php 


include_once('Auditeur_Framework_TestCase.php');

class Drupal_Hook7_Test extends Auditeur_Framework_TestCase
{
    public function testDrupal_Hook7()  {
        $this->expected = array( 'hoook_forms', 'hoook_help', 'hoook_info', 'hoook_info_alter', 'hoook_alter_info', 'drupal_other_hook_info');
        $this->unexpected = array('hoook_non_drupal_suffix' );

        parent::generic_test();
    }
}
?>