<?php



class Cornac_Auditeur_Analyzer_Variables_StrangeChars extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Variables with strange chars';
	protected	$description = 'List variables whose name contains caracters that are no letter, figure or _ (and $). This is usually a bad idea';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type='variable' AND
      T1.code REGEXP '[^a-z\$_0-9]'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>