<?php



class Cornac_Auditeur_Analyzer_Commands_SqlConcatenation extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'SQL concatenations';
	protected	$description = 'Spot concatenations that are building SQL queries.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('Commands_Sql');
	}


	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, TC.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.left BETWEEN T1.left AND T1.right AND
       T2.type = 'literals'
JOIN <report> TR
    ON T2.id = TR.token_id AND
       TR.module = 'Commands_Sql'
JOIN <tokens_cache> TC
    ON T1.id = TC.id
WHERE T1.type = 'concatenation'
SQL;

        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>