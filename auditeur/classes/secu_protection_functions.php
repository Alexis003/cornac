<?php

class secu_protection_functions extends functioncalls {
	protected	$title = 'Fonctions de sécurité de PHP';
	protected	$description = 'Points d\'utilisation de quelques fonctions de sécurité classiques.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array('addcslashes', 'ctype_alpha', 'ctype_cntrl', 'ctype_digit', 'ctype_graph', 'ctype_lower', 'ctype_print', 'ctype_punct', 'ctype_space', 'ctype_upper', 'ctype_xdigit', 'filter_input', 'filter_var', 'html_entity_decode', 'htmlspecialchars', 'is_double', 'is_executable', 'is_file', 'is_int', 'is_link', 'is_writeable', 'md5_file', 'mysql_real_escape_string', 'mysqli_bind_param', 'pg_escape_bytea', 'rawurlencode', 'sha1_file', 'addslashes', 'checkdate', 'ctype_alnum', 'escapeshellarg', 'escapeshellcmd', 'filter_input_array', 'filter_var_array', 'getimagesize', 'hash_file', 'htmlentities', 'htmlspecialchars_decode', 'ip2long', 'is_bool', 'is_dir', 'is_numeric', 'is_readable', 'is_uploaded_file', 'mysql_escape_string', 'mysqli_bind_result', 'mysqli_real_escape_string', 'pdo->quote', 'pg_escape_string', 'preg_quote', 'quotemeta', 'realpath', 'sqlite_escape_string', 'strip_tags', 'strtotime', 'urlencode');
	    parent::analyse();
	}
}

?>