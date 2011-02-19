<?php



class Cornac_Auditeur_Analyzer_Php_SpecialHandlers extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Php special handlers';
	protected	$description = 'Spot usage of PHP special handlers, such as session handler, error handler, etc.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('Functions_Php');
	}

	public function analyse() {
        $this->clean_report();

        $in = Cornac_Auditeur_Analyzer::getPHPHandlers();
        $this->in = '"'.join('", "', $in).'"';

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
FROM <report> T1
WHERE T1.module = 'Functions_Php' AND
      T1.element IN ({$this->in})
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>