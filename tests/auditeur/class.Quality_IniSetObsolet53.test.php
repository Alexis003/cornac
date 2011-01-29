<?php 


include_once('Auditeur_Framework_TestCase.php');

class Quality_IniSetObsolet53_Test extends Auditeur_Framework_TestCase
{
    public function testQuality_IniSetObsolet53()  {
        $this->expected = array( 'define_syslog_variables',
                  'register_globals',
                  'register_long_arrays',
                  'safe_mode',
                  'magic_quotes_gpc',
                  'magic_quotes_runtime',
                  'magic_quotes_sybase');
        $this->unexpected = array('',);

        parent::generic_test();
    }
}
?>