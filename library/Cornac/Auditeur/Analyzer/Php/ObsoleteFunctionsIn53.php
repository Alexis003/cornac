<?php



class Cornac_Auditeur_Analyzer_Php_ObsoleteFunctionsIn53 extends Cornac_Auditeur_Analyzer_Functioncalls {
	protected	$title = 'PHP functions obsolete in 5.3';
	protected	$description = 'List of PHP functions obsolete in 5.3';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
	    $this->functions = array(
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
	    parent::analyse();
	}
}

?>