<?php



class Cornac_Auditeur_Analyzer_Php_ReservedWords53 extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Php 5.3 ReservedWords';
	protected	$description = 'Spot usage of goto and namespace in PHP code for classes and labels.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type IN ('_functionname_','_classname_','_goto_') AND
      T1.code IN ('goto','namespace')
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>