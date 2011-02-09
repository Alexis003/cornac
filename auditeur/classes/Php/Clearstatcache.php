<?php



class Php_Clearstatcache extends modules {
	protected	$title = 'Clearstatcache';
	protected	$description = 'Usage of Clearstatcache with realpath (migration issue in 5.3)';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, 'clearstatcache & realpath', T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type = '_functionname_' AND
      T1.code IN ('clearstatcache','realpath')
GROUP BY T1.file
HAVING COUNT(DISTINCT code) = 2
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>