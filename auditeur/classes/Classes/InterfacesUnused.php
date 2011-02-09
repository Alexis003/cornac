<?php



class Classes_InterfacesUnused extends modules {
	protected	$title = 'Unused Interfaces';
	protected	$description = 'List useless interfaces.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('Classes_InterfacesUsed','Classes_Interfaces');
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
FROM <report> T1
LEFT JOIN <report> T2
    ON T2.module = 'Classes_InterfacesUsed' AND
       T2.element = T1.element
WHERE T1.module = 'Classes_Interfaces' AND
      T2.id IS NULL
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>