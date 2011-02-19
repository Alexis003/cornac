<?php



class Cornac_Auditeur_Analyzer_Php_ObsoleteModulesIn53 extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Obslete modules in PHP 5.3';
	protected	$description = 'Obslete modules in PHP 5.3';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('Php_Modules');
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
FROM <report> T1
WHERE element IN ('dbase','fbsql','fdf','ming','msql','ncurses','sybase','mhash')
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>