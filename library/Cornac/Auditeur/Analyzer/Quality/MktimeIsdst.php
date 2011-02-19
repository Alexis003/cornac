<?php



class Cornac_Auditeur_Analyzer_Quality_MktimeIsdst extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Mktime $is_dst usage';
	protected	$description = 'Search for use of $is_dst argument in mktime call.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2 
  ON T2.file = T1.file AND 
     T2.left = T1.right + 1 AND
     T2.type='arglist'
JOIN <tokens> T3
  ON T3.file = T1.file AND 
     T3.left BETWEEN T2.left AND T2.right AND
     T3.level = T2.level + 1
WHERE T1.code = 'mktime' AND 
      T1.type='_functionname_'
GROUP BY T1.id
HAVING COUNT(*) >= 7
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>