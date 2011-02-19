<?php



class Cornac_Auditeur_Analyzer_Structures_CallTimePassByReference extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Call-time pass-by-reference';
	protected	$description = 'Spot all function/method call that force pass by reference at call time.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T3.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.left BETWEEN T1.left AND T1.right AND
       T2.level = T1.level + 1 AND
       T2.type = 'reference'
JOIN <tokens_tags> TT
    ON TT.token_sub_id = T1.id
JOIN <tokens> T3
    ON TT.token_id = T3.id AND
       T3.file = T1.file AND
       T3.type = 'functioncall'
WHERE T1.type = 'arglist'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>