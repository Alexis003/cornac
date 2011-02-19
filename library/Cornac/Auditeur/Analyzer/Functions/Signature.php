<?php



class Cornac_Auditeur_Analyzer_Functions_Signature extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Function signatures';
	protected	$description = 'Sport argument list that are actually part of the function definition, not a list of arguments called.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT T2.id 
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.left = T1.left + 5
WHERE T2.type = 'arglist' AND
      T1.type = '_function'
SQL;
        $this->exec_query_attributes($this->name, $query);

        return true;
	}
}

?>