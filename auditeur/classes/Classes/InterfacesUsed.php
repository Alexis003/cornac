<?php



class Classes_InterfacesUsed extends modules {
	protected	$title = 'Used Interfaces';
	protected	$description = 'List of used interfaces.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE type = '_implements_'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>