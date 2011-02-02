<?php 


class Classes_NsToPath extends modules {
	protected	$title = 'Namespace In Path';
	protected	$description = 'PHP 5.2 way of organizing files : namespace in the class name. A_B_C => A/B/C.php';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type='_classname_' AND
      T1.file NOT REGEXP concat(replace(code, '_', '/'),'.php')
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>