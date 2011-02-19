<?php



class Cornac_Auditeur_Analyzer_Constants_HasLowerCase extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Constants with lower case in it';
	protected	$description = 'List constants names that have lower case letters : this is usually not the convention.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('Constants_Definitions');
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
FROM <report> T1
WHERE module = 'Constants_Definitions' AND
      BINARY UPPER(T1.element) != T1.element
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>