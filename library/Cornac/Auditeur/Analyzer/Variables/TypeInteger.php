<?php



class Cornac_Auditeur_Analyzer_Variables_TypeInteger extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Variables holding integers';
	protected	$description = 'This is the special analyzer Variables_TypeInteger (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array("Variables_Names");
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
    FROM <tokens> T1
    WHERE type = 'variable'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>