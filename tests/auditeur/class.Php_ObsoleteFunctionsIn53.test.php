<?php 


include_once('Auditeur_Framework_TestCase.php');

class Php_ObsoleteFunctionsIn53_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_ObsoleteFunctionsIn53()  {
        $this->expected = array( 
'call_user_method',
'call_user_method_array',
'define_syslog_variables',
'dl',
'ereg',
'ereg_replace',
'eregi',
'eregi_replace',
'set_magic_quotes_runtime',
'session_register',
'session_unregister',
'session_is_registered',
'set_socket_blocking',
'split',
'spliti',
'sql_regcase',
'mysql_db_query',
'mysql_escape_string',
);
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>