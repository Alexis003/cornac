<?php 


class Variables_AllCaps extends modules {
	protected	$title = 'All Caps variables';
	protected	$description = 'List variables that are only made of upper case caracters.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE BINARY T1.code = UPPER(T1.code) AND
      T1.type = 'variable' AND
      T1.code NOT LIKE "$\_%"
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>