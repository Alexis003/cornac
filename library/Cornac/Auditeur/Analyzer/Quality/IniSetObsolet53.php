<?php



class Cornac_Auditeur_Analyzer_Quality_IniSetObsolet53 extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'PHP directive obsolete in 5.3';
	protected	$description = 'PHP directive obsolete in 5.3';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, T2.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.left = T1.right + 2 AND
       T2.file = T1.file
WHERE T1.type = '_functionname_' AND
      T1.code IN ( 'ini_set', 'ini_get' ) AND
      T2.code IN ('define_syslog_variables',
                  'register_globals',
                  'register_long_arrays',
                  'safe_mode',
                  'magic_quotes_gpc',
                  'magic_quotes_runtime',
                  'magic_quotes_sybase')
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>